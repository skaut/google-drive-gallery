<?php
/**
 * Contains the API_Facade class.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg;

/**
 * API call facade
 */
class API_Facade {
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
		\Sgdg\API_Client::preamble();
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
		return \Sgdg\API_Client::async_request(
			\Sgdg\API_Client::get_drive_client()->files->listFiles( $params ), // @phan-suppress-current-line PhanTypeMismatchArgument
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
		\Sgdg\API_Client::preamble();
		return \Sgdg\API_Client::async_request(
			\Sgdg\API_Client::get_drive_client()->drives->get( // @phan-suppress-current-line PhanTypeMismatchArgument
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
		\Sgdg\API_Client::preamble();
		/**
		 * `$transform` transforms the raw Google API response into the structured response this function returns.
		 *
		 * @throws \Sgdg\Exceptions\File_Not_Found_Exception The file/directory wasn't found.
		 */
		return \Sgdg\API_Client::async_request(
			\Sgdg\API_Client::get_drive_client()->files->get( // @phan-suppress-current-line PhanTypeMismatchArgument
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
		\Sgdg\API_Client::preamble();
		return \Sgdg\API_Client::async_request(
			\Sgdg\API_Client::get_drive_client()->files->get( // @phan-suppress-current-line PhanTypeMismatchArgument
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
		\Sgdg\API_Client::preamble();
		return \Sgdg\API_Client::async_paginated_request(
			static function( $page_token ) {
				return \Sgdg\API_Client::get_drive_client()->drives->listDrives(
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
		\Sgdg\API_Client::preamble();
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
		return \Sgdg\API_Client::async_paginated_request(
			static function( $page_token ) use ( $parent_id, $order_by, $pagination_helper, $mime_type_check, $fields ) {
				return \Sgdg\API_Client::get_drive_client()->files->listFiles(
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
