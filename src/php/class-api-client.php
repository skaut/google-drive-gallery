<?php
/**
 * Contains the API_Client class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg;

use \Sgdg\Vendor\GuzzleHttp\Promise\Promise;

/**
 * API client
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class API_Client {
	/**
	 * Google API client
	 *
	 * @var \Sgdg\Vendor\Google\Client $raw_client
	 */
	private static $raw_client;

	/**
	 * Google Drive API client
	 *
	 * @var \Sgdg\Vendor\Google_Service_Drive $raw_client
	 */
	private static $drive_client;

	/**
	 * The current Google API batch
	 *
	 * @var \Sgdg\Vendor\Google\Http\Batch|null $current_batch
	 */
	private static $current_batch = null;

	/**
	 * The currently pending API requests as a list of callbacks.
	 *
	 * @var callable[] $pending_requests
	 */
	private static $pending_requests;

	/**
	 * Returns a fully set-up Google client.
	 *
	 * @return \Sgdg\Vendor\Google\Client
	 */
	public static function get_raw_client() {
		if ( ! isset( self::$raw_client ) ) {
			self::$raw_client = new \Sgdg\Vendor\Google\Client();
			self::$raw_client->setAuthConfig(
				array(
					'client_id'     => \Sgdg\Options::$client_id->get(),
					'client_secret' => \Sgdg\Options::$client_secret->get(),
					'redirect_uris' => array( esc_url_raw( admin_url( 'admin.php?page=sgdg_basic&action=oauth_redirect' ) ) ),
				)
			);
			self::$raw_client->setAccessType( 'offline' );
			self::$raw_client->setApprovalPrompt( 'force' );
			self::$raw_client->addScope( \Sgdg\Vendor\Google_Service_Drive::DRIVE_READONLY );
		}
		return self::$raw_client;
	}

	/**
	 * Returns a fully set-up Google Drive API client.
	 *
	 * @throws \Sgdg\Exceptions\Plugin_Not_Authorized_Exception Not authorized.
	 *
	 * @return \Sgdg\Vendor\Google_Service_Drive
	 */
	public static function get_drive_client() {
		if ( ! isset( self::$drive_client ) ) {
			$raw_client   = self::get_raw_client();
			$access_token = get_option( 'sgdg_access_token', false );
			if ( false === $access_token ) {
				throw new \Sgdg\Exceptions\Plugin_Not_Authorized_Exception();
			}
			$raw_client->setAccessToken( $access_token );

			if ( $raw_client->isAccessTokenExpired() ) {
				$raw_client->fetchAccessTokenWithRefreshToken( $raw_client->getRefreshToken() );
				$new_access_token    = $raw_client->getAccessToken();
				$merged_access_token = array_merge( $access_token, $new_access_token );
				update_option( 'sgdg_access_token', $merged_access_token );
			}
			// @phan-suppress-next-line PhanTypeMismatchArgument
			self::$drive_client = new \Sgdg\Vendor\Google_Service_Drive( $raw_client );
		}
		return self::$drive_client;
	}

	/**
	 * Sets up request batching.
	 */
	private static function preamble() {
		if ( ! is_null( self::$current_batch ) ) {
			return;
		}
		// @phan-suppress-next-line PhanUndeclaredMethod
		self::get_drive_client()->getClient()->setUseBatch( true );
		// @phan-suppress-next-line PhanUndeclaredMethod
		self::$current_batch    = self::get_drive_client()->createBatch();
		self::$pending_requests = array();
	}

	/**
	 * Registers a request to be executed later.
	 *
	 * @param \Sgdg\Vendor\GuzzleHttp\Psr7\Request $request The Google API request.
	 * @param callable                             $transform A function to be executed when the request completes, in the format `function( $response ): $output` where `$response` is the Google API response. The function should do any transformations on the output data necessary.
	 * @param callable|null                        $rejection_handler A function to be executed when the request fails, in the format `function( $exception ): $output` where `$exception` is the exception in question and `$output` should be a RejectedPromise.
	 *
	 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise that will be resolved in `$callback`.
	 */
	private static function async_request( $request, $transform, $rejection_handler = null ) {
		$key = wp_rand( 0, 0 );
		// @phan-suppress-next-line PhanPossiblyNonClassMethodCall
		self::$current_batch->add( $request, $key );
		$promise                                      = new Promise();
		self::$pending_requests[ 'response-' . $key ] = static function( $response ) use ( $transform, $promise ) {
			try {
				self::check_response( $response );
				$promise->resolve( $transform( $response ) );
			} catch ( \Sgdg\Exceptions\Exception $e ) {
				$promise->reject( $e );
			}
		};
		return $promise->then( null, $rejection_handler );
	}

	/**
	 * Registers a paginated request to be executed later.
	 *
	 * @param callable                                        $request A function which makes the Google API request. In the format `function( $page_token )` where `$page_token` is the pagination token to use.
	 * @param callable                                        $transform A function to be executed when the request completes, in the format `function( $response ): $output` where `$response` is the Google API response. The function should do any transformations on the output data necessary.
	 * @param callable|null                                   $rejection_handler A function to be executed when the request fails, in the format `function( $exception ): $output` where `$exception` is the exception in question and `$output` should be a RejectedPromise.
	 * @param \Sgdg\Frontend\Pagination_Helper_Interface|null $pagination_helper An initialized pagination helper.
	 *
	 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise that will be resolved in `$callback`.
	 */
	private static function async_paginated_request( $request, $transform, $rejection_handler = null, $pagination_helper = null ) {
		if ( is_null( $pagination_helper ) ) {
			$pagination_helper = new \Sgdg\Frontend\Infinite_Pagination_Helper();
		}
		$page    = static function( $page_token, $promise, $previous_output ) use ( $request, $transform, $pagination_helper, &$page ) {
			$key = wp_rand( 0, 0 );
			// @phan-suppress-next-line PhanPossiblyNonClassMethodCall
			self::$current_batch->add( $request( $page_token ), $key );
			self::$pending_requests[ 'response-' . $key ] = static function( $response ) use ( $promise, $previous_output, $transform, $pagination_helper, &$page ) {
				try {
					self::check_response( $response );
					$new_page_token = $response->getNextPageToken();
					$output         = $transform( $response );
					$output         = array_merge( $previous_output, $output );
					if ( null === $new_page_token || ! $pagination_helper->should_continue() ) {
						$promise->resolve( $output );
						return;
					}
					$page( $new_page_token, $promise, $output );
				} catch ( \Sgdg\Exceptions\Exception $e ) {
					$promise->reject( $e );
				}
			};
		};
		$promise = new Promise();
		$page( null, $promise, array() );
		return $promise->then( null, $rejection_handler );
	}

	/**
	 * Executes all requests and resolves all promises.
	 *
	 * @param array $promises The promises to resolve and throw exceptions if they reject.
	 *
	 * @return array A list of results from the promises. Is in the same format as the parameter `$promises`, i.e. if an associative array of promises is passed, an associative array of results will be returned.
	 */
	public static function execute( $promises = array() ) {
		if ( is_null( self::$current_batch ) ) {
			\Sgdg\Vendor\GuzzleHttp\Promise\Utils::queue()->run();
			return \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all( $promises )->wait();
		}
		$batch = self::$current_batch;
		// @phan-suppress-next-line PhanUndeclaredMethod
		self::$current_batch = self::get_drive_client()->createBatch();
		// @phan-suppress-next-line PhanPossiblyNonClassMethodCall
		$responses = $batch->execute();
		foreach ( $responses as $key => $response ) {
			call_user_func( self::$pending_requests[ $key ], $response );
			unset( self::$pending_requests[ $key ] );
		}
		\Sgdg\Vendor\GuzzleHttp\Promise\Utils::queue()->run();
		if ( count( self::$pending_requests ) > 0 ) {
			self::execute();
		}
		self::$current_batch = null;
		// @phan-suppress-next-line PhanUndeclaredMethod
		self::get_drive_client()->getClient()->setUseBatch( false );
		return \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all( $promises )->wait();
	}

	/**
	 * Checks the API response and throws an exception if there was a problem.
	 *
	 * @param \ArrayAccess|\Countable|\Iterator|\Sgdg\Vendor\Google\Collection|\Sgdg\Vendor\Google\Model|\Sgdg\Vendor\Google_Service_Drive_FileList|\Traversable|iterable $response The API response.
	 *
	 * @throws \Sgdg\Exceptions\API_Rate_Limit_Exception Rate limit exceeded.
	 * @throws \Sgdg\Exceptions\Not_Found_Exception The requested resource couldn't be found.
	 * @throws \Sgdg\Exceptions\API_Exception A wrapped API exception.
	 */
	private static function check_response( $response ) {
		if ( ! ( $response instanceof \Sgdg\Vendor\Google\Service\Exception ) ) {
			return;
		}
		if ( in_array( 'userRateLimitExceeded', array_column( $response->getErrors(), 'reason' ), true ) ) {
			throw new \Sgdg\Exceptions\API_Rate_Limit_Exception( $response );
		}
		if ( in_array( 'notFound', array_column( $response->getErrors(), 'reason' ), true ) ) {
			throw new \Sgdg\Exceptions\Not_Found_Exception();
		}
		throw new \Sgdg\Exceptions\API_Exception( $response );
	}

	/**
	 * Searches for a directory ID by its parent and its name
	 *
	 * @param string $parent_id The ID of the directory to search in.
	 * @param string $name The name of the directory.
	 *
	 * @throws \Sgdg\Exceptions\API_Exception|\Sgdg\Exceptions\API_Rate_Limit_Exception A problem with the API.
	 *
	 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to the ID of the directory.
	 */
	public static function get_directory_id( $parent_id, $name ) {
		self::preamble();
		$params = array(
			'q'                         => '"' . $parent_id . '" in parents and name = "' . str_replace( '"', '\\"', $name ) . '" and (mimeType = "application/vnd.google-apps.folder" or (mimeType = "application/vnd.google-apps.shortcut" and shortcutDetails.targetMimeType = "application/vnd.google-apps.folder")) and trashed = false',
			'supportsAllDrives'         => true,
			'includeItemsFromAllDrives' => true,
			'pageSize'                  => 2,
			'fields'                    => 'files(id, name, mimeType, shortcutDetails(targetId))',
		);
		/**
		 * `$transform` transforms the raw Google API response into the structured response this function returns.
		 *
		 * @throws \Sgdg\Exceptions\Directory_Not_Found_Exception The directory wasn't found.
		 */
		return self::async_request(
			self::get_drive_client()->files->listFiles( $params ), // @phan-suppress-current-line PhanTypeMismatchArgument
			static function( $response ) use ( $name ) {
				if ( 1 !== count( $response->getFiles() ) ) {
					throw new \Sgdg\Exceptions\Directory_Not_Found_Exception( $name );
				}
				$file = $response->getFiles()[0];
				return $file->getMimeType() === 'application/vnd.google-apps.shortcut' ? $file->getShortcutDetails()->getTargetId() : $file->getId();
			}
		);
	}

	/**
	 * Searches for a drive name by its ID
	 *
	 * @param string $id The of the drive.
	 *
	 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to the name of the drive.
	 *
	 * @SuppressWarnings(PHPMD.ShortVariable)
	 */
	public static function get_drive_name( $id ) {
		self::preamble();
		return self::async_request(
			self::get_drive_client()->drives->get( // @phan-suppress-current-line PhanTypeMismatchArgument
				$id,
				array(
					'fields' => 'name',
				)
			),
			static function( $response ) {
				return $response->getName();
			},
			static function( $exception ) {
				if ( $exception instanceof \Sgdg\Exceptions\Not_Found_Exception ) {
					$exception = new \Sgdg\Exceptions\Drive_Not_Found_Exception();
				}
				return new \Sgdg\Vendor\GuzzleHttp\Promise\RejectedPromise( $exception );
			}
		);
	}

	/**
	 * Searches for a file/directory name by its ID
	 *
	 * @param string $id The ID of the file/directory.
	 *
	 * @throws \Sgdg\Exceptions\API_Exception|\Sgdg\Exceptions\API_Rate_Limit_Exception A problem with the API.
	 *
	 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to the name of the directory.
	 *
	 * @SuppressWarnings(PHPMD.ShortVariable)
	 */
	public static function get_file_name( $id ) {
		self::preamble();
		/**
		 * `$transform` transforms the raw Google API response into the structured response this function returns.
		 *
		 * @throws \Sgdg\Exceptions\File_Not_Found_Exception The file/directory wasn't found.
		 */
		return self::async_request(
			self::get_drive_client()->files->get( // @phan-suppress-current-line PhanTypeMismatchArgument
				$id,
				array(
					'supportsAllDrives' => true,
					'fields'            => 'name, trashed',
				)
			),
			static function( $response ) {
				if ( $response->getTrashed() ) {
					throw new \Sgdg\Exceptions\File_Not_Found_Exception();
				}
				return $response->getName();
			},
			static function( $exception ) {
				if ( $exception instanceof \Sgdg\Exceptions\Not_Found_Exception ) {
					$exception = new \Sgdg\Exceptions\File_Not_Found_Exception();
				}
				return new \Sgdg\Vendor\GuzzleHttp\Promise\RejectedPromise( $exception );
			}
		);
	}

	/**
	 * Checks whether an ID points to a valid directory inside another directory
	 *
	 * @param string $id The ID of the directory.
	 * @param string $parent The ID of the parent directory.
	 *
	 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving if the directory is valid.
	 *
	 * @SuppressWarnings(PHPMD.ShortVariable)
	 */
	public static function check_directory_in_directory( $id, $parent ) {
		self::preamble();
		return self::async_request(
			self::get_drive_client()->files->get( // @phan-suppress-current-line PhanTypeMismatchArgument
				$id,
				array(
					'supportsAllDrives' => true,
					'fields'            => 'trashed, parents, mimeType, shortcutDetails(targetId)',
				)
			),
			/**
			 * `$transform` transforms the raw Google API response into the structured response this function returns.
			 *
			 * @throws \Sgdg\Exceptions\Directory_Not_Found_Exception The directory wasn't found.
			 */
			static function( $response ) use ( $parent ) {
				if ( $response->getTrashed() ) {
					throw new \Sgdg\Exceptions\Directory_Not_Found_Exception();
				}
				if (
					$response->getMimeType() !== 'application/vnd.google-apps.folder' &&
					(
						$response->getMimeType() !== 'application/vnd.google-apps.shortcut' ||
						$response->getShortcutDetails()->getTargetMimeType() !== 'application/vnd.google-apps.folder'
					)
				) {
					throw new \Sgdg\Exceptions\Directory_Not_Found_Exception();
				}
				if ( ! in_array( $parent, $response->getParents(), true ) ) {
					throw new \Sgdg\Exceptions\Directory_Not_Found_Exception();
				}
			},
			static function( $exception ) {
				if ( $exception instanceof \Sgdg\Exceptions\Not_Found_Exception ) {
					$exception = new \Sgdg\Exceptions\Directory_Not_Found_Exception();
				}
				return new \Sgdg\Vendor\GuzzleHttp\Promise\RejectedPromise( $exception );
			}
		);
	}

	/**
	 * Lists all drives.
	 *
	 * @throws \Sgdg\Exceptions\API_Exception|\Sgdg\Exceptions\API_Rate_Limit_Exception A problem with the API.
	 *
	 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to a list of drives in the format `[ 'id' => '', 'name' => '' ]`.
	 */
	public static function list_drives() {
		self::preamble();
		return self::async_paginated_request(
			static function( $page_token ) {
				return self::get_drive_client()->drives->listDrives(
					array(
						'pageToken' => $page_token,
						'pageSize'  => 100,
						'fields'    => 'nextPageToken, drives(id, name)',
					)
				);
			},
			static function( $response ) {
				return array_map(
					static function( $drive ) {
						return array(
							'name' => $drive->getName(),
							'id'   => $drive->getId(),
						);
					},
					$response->getDrives()
				);
			}
		);
	}

	/**
	 * Lists all files of a given type inside a given directory.
	 *
	 * @param string                                          $parent_id The ID of the directory to list the files in.
	 * @param \Sgdg\Frontend\API_Fields                       $fields The fields to list.
	 * @param string                                          $order_by Sets the ordering of the results. Valid options are `createdTime`, `folder`, `modifiedByMeTime`, `modifiedTime`, `name`, `name_natural`, `quotaBytesUsed`, `recency`, `sharedWithMeTime`, `starred`, and `viewedByMeTime`.
	 * @param \Sgdg\Frontend\Pagination_Helper_Interface|null $pagination_helper An initialized pagination helper.
	 * @param string                                          $mime_type_prefix The mimeType prefix to filter the files for.
	 *
	 * @throws \Sgdg\Exceptions\Unsupported_Value_Exception A field that is not supported was passed in `$fields`.
	 *
	 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to a list of files in the format `[ 'id' => '', 'name' => '' ]`- the fields of each file are given by the parameter `$fields`.
	 */
	private static function list_files( $parent_id, $fields, $order_by, $pagination_helper, $mime_type_prefix ) {
		if ( is_null( $pagination_helper ) ) {
			$pagination_helper = new \Sgdg\Frontend\Infinite_Pagination_Helper();
		}
		self::preamble();
		if ( ! $fields->check(
			array(
				'id',
				'name',
				'mimeType',
				'createdTime',
				'imageMediaMetadata' => array( 'width', 'height', 'time' ),
				'videoMediaMetadata' => array( 'width', 'height' ),
				'webContentLink',
				'thumbnailLink',
				'description',
			)
		) ) {
			throw new \Sgdg\Exceptions\Unsupported_Value_Exception( $fields, 'list_files' );
		}
		if ( $fields->check( array( 'id', 'name' ) ) ) {
			$mime_type_check = '(mimeType contains "' . $mime_type_prefix . '" or (mimeType contains "application/vnd.google-apps.shortcut" and shortcutDetails.targetMimeType contains "' . $mime_type_prefix . '"))';
		} else {
			$mime_type_check = 'mimeType contains "' . $mime_type_prefix . '"';
		}
		return self::async_paginated_request(
			static function( $page_token ) use ( $parent_id, $order_by, $pagination_helper, $mime_type_check, $fields ) {
				return self::get_drive_client()->files->listFiles(
					array(
						'q'                         => '"' . $parent_id . '" in parents and ' . $mime_type_check . ' and trashed = false',
						'supportsAllDrives'         => true,
						'includeItemsFromAllDrives' => true,
						'orderBy'                   => $order_by,
						'pageToken'                 => $page_token,
						'pageSize'                  => $pagination_helper->next_list_size( 1000 ),
						'fields'                    => 'nextPageToken, files(' . $fields->format() . ')',
					)
				);
			},
			static function( $response ) use ( $fields, $pagination_helper ) {
				$dirs = array();
				$pagination_helper->iterate(
					$response->getFiles(),
					static function( $file ) use ( $fields, &$dirs ) {
						$dirs[] = $fields->parse_response( $file );
					}
				);
				return $dirs;
			},
			null,
			$pagination_helper
		);
	}

	/**
	 * Lists all directories inside a given directory.
	 *
	 * @param string                                          $parent_id The ID of the directory to list directories in.
	 * @param \Sgdg\Frontend\API_Fields                       $fields The fields to list.
	 * @param string                                          $order_by Sets the ordering of the results. Valid options are `createdTime`, `folder`, `modifiedByMeTime`, `modifiedTime`, `name`, `name_natural`, `quotaBytesUsed`, `recency`, `sharedWithMeTime`, `starred`, and `viewedByMeTime`. Default `name`.
	 * @param \Sgdg\Frontend\Pagination_Helper_Interface|null $pagination_helper An initialized pagination helper. Optional.
	 *
	 * @throws \Sgdg\Exceptions\Unsupported_Value_Exception                            A field that is not supported was passed in `$fields`.
	 * @throws \Sgdg\Exceptions\API_Exception|\Sgdg\Exceptions\API_Rate_Limit_Exception A problem with the API.
	 *
	 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to a list of directories in the format `[ 'id' => '', 'name' => '' ]`- the fields of each directory are givent by the parameter `$fields`.
	 */
	public static function list_directories( $parent_id, $fields, $order_by = 'name', $pagination_helper = null ) {
		return self::list_files( $parent_id, $fields, $order_by, $pagination_helper, 'application/vnd.google-apps.folder' );
	}

	/**
	 * Lists all images inside a given directory.
	 *
	 * @param string                                          $parent_id The ID of the directory to list directories in.
	 * @param \Sgdg\Frontend\API_Fields                       $fields The fields to list.
	 * @param string                                          $order_by Sets the ordering of the results. Valid options are `createdTime`, `folder`, `modifiedByMeTime`, `modifiedTime`, `name`, `name_natural`, `quotaBytesUsed`, `recency`, `sharedWithMeTime`, `starred`, and `viewedByMeTime`. Default `name`.
	 * @param \Sgdg\Frontend\Pagination_Helper_Interface|null $pagination_helper An initialized pagination helper. Optional.
	 *
	 * @throws \Sgdg\Exceptions\Unsupported_Value_Exception                            A field that is not supported was passed in `$fields`.
	 * @throws \Sgdg\Exceptions\API_Exception|\Sgdg\Exceptions\API_Rate_Limit_Exception A problem with the API.
	 *
	 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to a list of images in the format `[ 'id' => '', 'name' => '' ]`- the fields of each directory are givent by the parameter `$fields`.
	 */
	public static function list_images( $parent_id, $fields, $order_by = 'name', $pagination_helper = null ) {
		return self::list_files( $parent_id, $fields, $order_by, $pagination_helper, 'image/' );
	}

	/**
	 * Lists all videos inside a given directory.
	 *
	 * @param string                                          $parent_id The ID of the directory to list directories in.
	 * @param \Sgdg\Frontend\API_Fields                       $fields The fields to list.
	 * @param string                                          $order_by Sets the ordering of the results. Valid options are `createdTime`, `folder`, `modifiedByMeTime`, `modifiedTime`, `name`, `name_natural`, `quotaBytesUsed`, `recency`, `sharedWithMeTime`, `starred`, and `viewedByMeTime`. Default `name`.
	 * @param \Sgdg\Frontend\Pagination_Helper_Interface|null $pagination_helper An initialized pagination helper. Optional.
	 *
	 * @throws \Sgdg\Exceptions\Unsupported_Value_Exception                            A field that is not supported was passed in `$fields`.
	 * @throws \Sgdg\Exceptions\API_Exception|\Sgdg\Exceptions\API_Rate_Limit_Exception A problem with the API.
	 *
	 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to a list of images in the format `[ 'id' => '', 'name' => '' ]`- the fields of each directory are givent by the parameter `$fields`.
	 */
	public static function list_videos( $parent_id, $fields, $order_by = 'name', $pagination_helper = null ) {
		return self::list_files( $parent_id, $fields, $order_by, $pagination_helper, 'video/' );
	}
}
