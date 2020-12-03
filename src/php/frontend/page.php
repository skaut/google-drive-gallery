<?php
/**
 * Contains all the functions used to handle the "page" AJAX endpoint.
 *
 * The "page" AJAX enpoint gets called each time the user needs to fetch items for a gallery.
 *
 * @package skaut-google-drive-gallery
 */

namespace Sgdg\Frontend\Page;

/**
 * Registers the "page" AJAX endpoint
 */
function register() {
	add_action( 'wp_ajax_page', '\\Sgdg\\Frontend\\Page\\handle_ajax' );
	add_action( 'wp_ajax_nopriv_page', '\\Sgdg\\Frontend\\Page\\handle_ajax' );
}

/**
 * Handles errors for the "page" AJAX endpoint.
 *
 * This function is a wrapper around `handle_ajax_body` that handles all the possible errors that can occur and sends them back as error messages.
 */
function handle_ajax() {
	try {
		ajax_handler_body();
	} catch ( \Sgdg\Exceptions\Exception $e ) {
		wp_send_json( array( 'error' => $e->getMessage() ) );
	} catch ( \Exception $_ ) {
		wp_send_json( array( 'error' => esc_html__( 'Unknown error.', 'skaut-google-drive-gallery' ) ) );
	}
}

/**
 * Actually handles the "gallery" AJAX endpoint.
 *
 * Returns a list of directories and a list of images.
 *
 * @see get_page()
 */
function ajax_handler_body() {
	list( $parent_id, $options, $path_verification ) = get_context();
	$pagination_helper                               = ( new \Sgdg\Frontend\Pagination_Helper() )->withOptions( $options, false );

	$page_promise = get_page( $parent_id, $pagination_helper, $options )->then(
		static function( $page ) {
			wp_send_json( $page );
		}
	);
	\Sgdg\API_Client::execute( array( $path_verification, $page_promise ) );
}

/**
 * Returns common variables used by different parts of the codebase
 *
 * @throws \Sgdg\Exceptions\Gallery_Expired_Exception The gallery has expired.
 *
 * @return array An array of the form {
 *     @type string The root directory of the gallery.
 *     @type \Sgdg\Frontend\Options_Proxy The configuration of the gallery.
 *     @type \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise rejecting if the path is invalid.
 * }
 */
function get_context() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $_GET['hash'] ) ) {
		throw new \Sgdg\Exceptions\Gallery_Expired_Exception();
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$transient = get_transient( 'sgdg_hash_' . sanitize_text_field( wp_unslash( $_GET['hash'] ) ) );

	if ( false === $transient ) {
		throw new \Sgdg\Exceptions\Gallery_Expired_Exception();
	}

	$path    = array( $transient['root'] );
	$options = new \Sgdg\Frontend\Options_Proxy( $transient['overriden'] );

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['path'] ) && '' !== $_GET['path'] ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$path = array_merge( $path, explode( '/', sanitize_text_field( wp_unslash( $_GET['path'] ) ) ) );
	}

	return array(
		end( $path ),
		$options,
		verify_path( $path ),
	);
}

/**
 * Checks that a path is a valid path on Google Drive.
 *
 * @param array $path A list of directory IDs.
 *
 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface|null A promise that resolves if the path is valid
 */
function verify_path( array $path ) {
	if ( count( $path ) === 1 ) {
		return null;
	}
	return \Sgdg\API_Client::check_directory_in_directory( $path[1], $path[0] )->then(
		static function() use ( $path ) {
			array_shift( $path );
			return verify_path( $path );
		},
		static function( $exception ) {
			if ( $exception instanceof \Sgdg\Exceptions\Directory_Not_Found_Exception ) {
				$exception = new \Sgdg\Exceptions\Path_Not_Found_Exception();
			}
			return new \Sgdg\Vendor\GuzzleHttp\Promise\RejectedPromise( $exception );
		}
	);
}

/**
 * Return one page worth of items
 *
 * Lists one page of items - first directories and then images, up until the number of items per page is reached.
 *
 * @param string                           $parent_id A directory to list items of.
 * @param \Sgdg\Frontend\Pagination_Helper $pagination_helper An initialized pagination helper.
 * @param \Sgdg\Frontend\Options_Proxy     $options The configuration of the gallery.
 *
 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to the page return value.
 */
function get_page( $parent_id, $pagination_helper, $options ) {
	$page = array(
		'directories' => directories( $parent_id, $pagination_helper, $options ),
	);
	return \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all( $page )->then(
		static function( $page ) use ( $parent_id, $pagination_helper, $options ) {
			if ( $pagination_helper->should_continue() ) {
				$page['images'] = images( $parent_id, $pagination_helper, $options );
			}
			return \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all( $page );
		}
	)->then(
		static function( $page ) use ( $parent_id, $pagination_helper, $options ) {
			if ( $pagination_helper->should_continue() ) {
				$page['videos'] = videos( $parent_id, $pagination_helper, $options );
			}
			return \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all( $page );
		}
	)->then(
		static function( $page ) use ( $pagination_helper ) {
			$page['more'] = $pagination_helper->has_more();
			return $page;
		}
	);
}

/**
 * Returns a list of subdirectories in a directory.
 *
 * @param string                           $parent_id A directory to list items of.
 * @param \Sgdg\Frontend\Pagination_Helper $pagination_helper An initialized pagination helper.
 * @param \Sgdg\Frontend\Options_Proxy     $options The configuration of the gallery.
 *
 * @return \Sgdg\Vendor\GuzzleHttp\Promise\Promise A promise resolving to a list of directories in the format `['id' =>, 'id', 'name' => 'name', 'thumbnail' => 'thumbnail', 'dircount' => 1, 'imagecount' => 1]`.
 */
function directories( $parent_id, $pagination_helper, $options ) {
	return ( \Sgdg\API_Client::list_directories( $parent_id, new \Sgdg\Frontend\API_Fields( array( 'id', 'name' ) ), $options->get( 'dir_ordering' ), $pagination_helper )->then(
		static function( $files ) use ( &$options ) {
			$files = array_map(
				static function( $file ) use ( &$options ) {
					if ( '' !== $options->get( 'dir_prefix' ) ) {
						$pos          = mb_strpos( $file['name'], $options->get( 'dir_prefix' ) );
						$file['name'] = mb_substr( $file['name'], false !== $pos ? $pos + 1 : 0 );
					}
					return $file;
				},
				$files
			);
			$ids   = array_column( $files, 'id' );

			return \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all( array( $files, dir_images( $ids, $options ), dir_counts( $ids ) ) );
		}
	)->then(
		static function( $list ) use ( &$options ) {
			list( $files, $images, $counts ) = $list;
			$count                           = count( $files );
			for ( $i = 0; $i < $count; $i++ ) {
				$files[ $i ]['thumbnail'] = $images[ $i ];
				if ( 'true' === $options->get( 'dir_counts' ) ) {
					$files[ $i ] = array_merge( $files[ $i ], $counts[ $i ] );
				}
				if ( 0 === $counts[ $i ]['dircount'] + $counts[ $i ]['imagecount'] + $counts[ $i ]['videocount'] ) {
					unset( $files[ $i ] );
				}
			}
			return $files;
		}
	) );
}

/**
 * Creates API requests for directory thumbnails
 *
 * Takes a batch and adds to it a request for the first image in each directory.
 *
 * @param array                        $dirs A list of directory IDs.
 * @param \Sgdg\Frontend\Options_Proxy $options The configuration of the gallery.
 *
 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to a list of directory images
 */
function dir_images( $dirs, $options ) {
	return \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all(
		array_map(
			static function( $directory ) use ( &$options ) {
				return \Sgdg\API_Client::list_images(
					$directory,
					new \Sgdg\Frontend\API_Fields(
						array(
							'imageMediaMetadata' => array( 'width', 'height' ),
							'thumbnailLink',
						)
					),
					$options->get( 'image_ordering' ),
					( new \Sgdg\Frontend\Pagination_Helper() )->withValues( 0, 1 )
				)->then(
					static function( $images ) use ( &$options ) {
						if ( count( $images ) === 0 ) {
							return false;
						}
						return substr( $images[0]['thumbnailLink'], 0, -4 ) . ( $images[0]['imageMediaMetadata']['width'] > $images[0]['imageMediaMetadata']['height'] ? 'h' : 'w' ) . floor( 1.25 * $options->get( 'grid_height' ) );
					}
				);
			},
			$dirs
		)
	);
}

/**
 * Creates API requests for directory item counts
 *
 * Takes a batch and adds to it requests for the counts of subdirectories and images for each directory.
 *
 * @param array $dirs A list of directory IDs.
 *
 * @return \Sgdg\Vendor\GuzzleHttp\Promise\PromiseInterface A promise resolving to a list of subdirectory, image and video counts of format `['dircount' => 1, 'imagecount' => 1, 'videocount' => 1]` for each directory.
 */
function dir_counts( $dirs ) {
	return \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all(
		array_map(
			static function( $dir ) {
				return \Sgdg\Vendor\GuzzleHttp\Promise\Utils::all(
					array(
						\Sgdg\API_Client::list_directories(
							$dir,
							new \Sgdg\Frontend\API_Fields( array( 'createdTime' ) ),
							'name',
							new \Sgdg\Frontend\Single_Page_Pagination_Helper()
						),
						\Sgdg\API_Client::list_images(
							$dir,
							new \Sgdg\Frontend\API_Fields( array( 'createdTime' ) ),
							'name',
							new \Sgdg\Frontend\Single_Page_Pagination_Helper()
						),
						\Sgdg\API_Client::list_videos(
							$dir,
							new \Sgdg\Frontend\API_Fields( array( 'createdTime' ) ),
							'name',
							new \Sgdg\Frontend\Single_Page_Pagination_Helper()
						),
					)
				)->then(
					static function( $items ) {
						return array(
							'dircount'   => count( $items[0] ),
							'imagecount' => count( $items[1] ),
							'videocount' => count( $items[2] ),
						);
					}
				);
			},
			$dirs
		)
	);
}

/**
 * Returns a list of images in a directory
 *
 * @param string                           $parent_id A directory to list items of.
 * @param \Sgdg\Frontend\Pagination_Helper $pagination_helper An initialized pagination helper.
 * @param \Sgdg\Frontend\Options_Proxy     $options The configuration of the gallery.
 *
 * @return \Sgdg\Vendor\GuzzleHttp\Promise\Promise A promise resolving to a list of images in the format `['id' =>, 'id', 'description' => 'description', 'image' => 'image', 'thumbnail' => 'thumbnail']`.
 */
function images( $parent_id, $pagination_helper, $options ) {
	if ( $options->get_by( 'image_ordering' ) === 'time' ) {
		$order_by = 'name';
		$fields   = new \Sgdg\Frontend\API_Fields(
			array(
				'id',
				'thumbnailLink',
				'createdTime',
				'imageMediaMetadata' => array( 'time' ),
				'description',
			)
		);
	} else {
		$order_by = $options->get( 'image_ordering' );
		$fields   = new \Sgdg\Frontend\API_Fields( array( 'id', 'thumbnailLink', 'description' ) );
	}
	return \Sgdg\API_Client::list_images( $parent_id, $fields, $order_by, $pagination_helper )->then(
		static function( $images ) use ( &$options ) {
			$images = array_map(
				static function( $image ) use ( &$options ) {
					return image_preprocess( $image, $options );
				},
				$images
			);
			return images_order( $images, $options );
		}
	);
}

/**
 * Processes an image response.
 *
 * @param array                        $image An image.
 * @param \Sgdg\Frontend\Options_Proxy $options The configuration of the gallery.
 *
 * @return array {
 *     @type string      $id The ID of the image.
 *     @type string      $description The description (caption) of the image.
 *     @type string      $image A URL of the image to be displayed in the lightbox
 *     @type string      $thumbnail A URL of a thumbnail to be displayed in the image grid.
 *     @type string|null $timestamp A timestamp to order the images by. Optional.
 * }
 */
function image_preprocess( $image, $options ) {
	$ret = array(
		'id'          => $image['id'],
		'description' => array_key_exists( 'description', $image ) ? esc_attr( $image['description'] ) : '',
		'image'       => substr( $image['thumbnailLink'], 0, -3 ) . $options->get( 'preview_size' ),
		'thumbnail'   => substr( $image['thumbnailLink'], 0, -4 ) . 'h' . floor( 1.25 * $options->get( 'grid_height' ) ),
	);
	if ( $options->get_by( 'image_ordering' ) === 'time' ) {
		if ( array_key_exists( 'imageMediaMetadata', $image ) && array_key_exists( 'time', $image['imageMediaMetadata'] ) ) {
			$timestamp = \DateTime::createFromFormat( 'Y:m:d H:i:s', $image['imageMediaMetadata']['time'] );
		} else {
			$timestamp = \DateTime::createFromFormat( 'Y-m-d\TH:i:s.uP', $image['createdTime'] );
		}
		if ( false !== $timestamp ) {
			$ret['timestamp'] = $timestamp->format( 'U' );
		}
	}
	return $ret;
}

/**
 * Orders images.
 *
 * @param array                        $images A list of images in the format `['id' =>, 'id', 'description' => 'description', 'image' => 'image', 'thumbnail' => 'thumbnail', 'timestamp' => new \DateTime()]`.
 * @param \Sgdg\Frontend\Options_Proxy $options The configuration of the gallery.
 *
 * @return array An ordered list of images in the format `['id' =>, 'id', 'description' => 'description', 'image' => 'image', 'thumbnail' => 'thumbnail']`.
 */
function images_order( $images, $options ) {
	if ( $options->get_by( 'image_ordering' ) === 'time' ) {
		usort(
			$images,
			static function( $first, $second ) use ( $options ) {
				$asc = $first['timestamp'] - $second['timestamp'];
				return $options->get_order( 'image_ordering' ) === 'ascending' ? $asc : -$asc;
			}
		);
		array_walk(
			$images,
			static function( &$item ) {
				unset( $item['timestamp'] );
			}
		);
	}
	return $images;
}

/**
 * Returns a list of images in a directory
 *
 * @param string                           $parent_id A directory to list items of.
 * @param \Sgdg\Frontend\Pagination_Helper $pagination_helper An initialized pagination helper.
 * @param \Sgdg\Frontend\Options_Proxy     $options The configuration of the gallery.
 *
 * @return \Sgdg\Vendor\GuzzleHttp\Promise\Promise A promise resolving to a list of videos in the format `['id' =>, 'id', 'thumbnail' => 'thumbnail', 'mimeType' => 'mimeType', 'src' => 'src']`.
 */
function videos( $parent_id, $pagination_helper, $options ) {
	return \Sgdg\API_Client::list_videos(
		$parent_id,
		new \Sgdg\Frontend\API_Fields(
			array(
				'id',
				'mimeType',
				'webContentLink',
				'thumbnailLink',
				'videoMediaMetadata' => array( 'width', 'height' ),
			)
		),
		$options->get( 'image_ordering' ),
		$pagination_helper
	)->then(
		static function( $videos ) use ( &$options ) {
			return array_map(
				static function( $video ) use ( &$options ) {
					return array(
						'id'        => $video['id'],
						'thumbnail' => substr( $video['thumbnailLink'], 0, -4 ) . 'h' . floor( 1.25 * $options->get( 'grid_height' ) ),
						'mimeType'  => $video['mimeType'],
						'width'     => array_key_exists( 'videoMediaMetadata', $video ) ? $video['videoMediaMetadata']['width'] : '0',
						'height'    => array_key_exists( 'videoMediaMetadata', $video ) ? $video['videoMediaMetadata']['height'] : '0',
						'src'       => resolve_video_url( $video['webContentLink'] ),
					);
				},
				$videos
			);
		}
	);
}

/**
 * Resolves the correct URL for a video.
 *
 * Finds the correct URL so that a video would load in the browser.
 *
 * @param string $web_content_url The webContentLink returned by Google Drive API.
 *
 * @return string The resolved video URL.
 */
function resolve_video_url( $web_content_url ) {
	$http_client = new \Sgdg\Vendor\GuzzleHttp\Client();
	$url         = $web_content_url;
	$response    = $http_client->get( $url, array( 'allow_redirects' => false ) );

	if ( $response->hasHeader( 'Set-Cookie' ) && 0 === mb_strpos( $response->getHeader( 'Set-Cookie' )[0], 'download_warning' ) ) {
		// Handle virus scan warning.
		mb_ereg( '(download_warning[^=]*)=([^;]*).*Domain=([^;]*)', $response->getHeader( 'Set-Cookie' )[0], $regs );
		$name       = $regs[1];
		$confirm    = $regs[2];
		$domain     = $regs[3];
		$cookie_jar = \Sgdg\Vendor\GuzzleHttp\Cookie\CookieJar::fromArray( array( $name => $confirm ), $domain );

		$response = $http_client->head(
			$url . '&confirm=' . $confirm,
			array(
				'allow_redirects' => false,
				'cookies'         => $cookie_jar,
			)
		);
		$url      = $response->getHeader( 'Location' )[0];
	}
	return $url;
}
