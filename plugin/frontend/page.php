<?php
namespace Sgdg\Frontend\Page;

function register() {
	add_action( 'wp_ajax_page', '\\Sgdg\\Frontend\\Page\\handle_ajax' );
	add_action( 'wp_ajax_nopriv_page', '\\Sgdg\\Frontend\\Page\\handle_ajax' );
}

function handle_ajax() {
	try {
		ajax_handler_body();
	} catch ( \Sgdg\Vendor\Google_Service_Exception $e ) {
		if ( 'userRateLimitExceeded' === $e->getErrors()[0]['reason'] ) {
			wp_send_json( [ 'error' => esc_html__( 'The maximum number of requests has been exceeded. Please try again in a minute.', 'skaut-google-drive-gallery' ) ] );
		} else {
			wp_send_json( [ 'error' => $e->getErrors()[0]['message'] ] );
		}
	} catch ( \Exception $e ) {
		wp_send_json( [ 'error' => $e->getMessage() ] );
	}
}

function ajax_handler_body() {
	list( $client, $dir, $options ) = get_context();

	$remaining = $options->get( 'page_size' );
	// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
	$skip = $remaining * ( max( 1, (int) $_GET['page'] ) - 1 );

	wp_send_json( get_page( $client, $dir, $skip, $remaining, $options ) );
}

function get_context() {
	$client = \Sgdg\Frontend\GoogleAPILib\get_drive_client();

	// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
	$transient = get_transient( 'sgdg_hash_' . $_GET['hash'] );

	if ( false === $transient ) {
		throw new \Exception( esc_html__( 'The gallery has expired.', 'skaut-google-drive-gallery' ) );
	}

	$dir     = $transient['root'];
	$options = new \Sgdg\Frontend\Options_Proxy( $transient['overriden'] );

	// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
	if ( isset( $_GET['path'] ) && '' !== $_GET['path'] ) {
		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		$dir = apply_path( $client, $dir, explode( '/', $_GET['path'] ) );
	}

	return [ $client, $dir, $options ];
}

function apply_path( $client, $root, array $path ) {
	$page_token = null;
	do {
		$params   = [
			'q'                         => '"' . $root . '" in parents and mimeType = "application/vnd.google-apps.folder" and trashed = false',
			'supportsAllDrives'         => true,
			'includeItemsFromAllDrives' => true,
			'pageToken'                 => $page_token,
			'pageSize'                  => 1000,
			'fields'                    => 'nextPageToken, files(id)',
		];
		$response = $client->files->listFiles( $params );
		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		foreach ( $response->getFiles() as $file ) {
			if ( $file->getId() === $path[0] ) {
				if ( count( $path ) === 1 ) {
					return $file->getId();
				}
				array_shift( $path );
				return apply_path( $client, $file->getId(), $path );
			}
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );
	throw new \Exception( esc_html__( 'No such subdirectory found in this gallery.', 'skaut-google-drive-gallery' ) );
}

function get_page( $client, $dir, $skip, $remaining, $options ) {
	$ret = [ 'more' => false ];
	if ( 0 < $remaining ) {
		list( $ret['directories'], $skip, $remaining, $ret['more'] ) = directories( $client, $dir, $options, $skip, $remaining );
	}
	if ( 0 < $remaining ) {
		list( $ret['images'], $ret['more'] ) = images( $client, $dir, $options, $skip, $remaining );
	}
	return $ret;
}

function directories( $client, $dir, $options, $skip, $remaining ) {
	$ids   = [];
	$names = [];

	$page_token = null;
	do {
		$params   = [
			'q'                         => '"' . $dir . '" in parents and mimeType = "application/vnd.google-apps.folder" and trashed = false',
			'supportsAllDrives'         => true,
			'includeItemsFromAllDrives' => true,
			'orderBy'                   => $options->get( 'dir_ordering' ),
			'pageToken'                 => $page_token,
			'pageSize'                  => min( 1000, $skip + $remaining + 1 ),
			'fields'                    => 'nextPageToken, files(id, name)',
		];
		$response = $client->files->listFiles( $params );
		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		$more = false;
		foreach ( $response->getFiles() as $file ) {
			if ( 0 < $skip ) {
				$skip--;
				continue;
			}
			if ( 0 >= $remaining ) {
				$more = true;
				break;
			}
			$ids[] = $file->getId();
			$name  = $file->getName();
			if ( $options->get( 'dir_prefix' ) ) {
				$pos     = mb_strpos( $name, $options->get( 'dir_prefix' ) );
				$names[] = mb_substr( $name, false !== $pos ? $pos + 1 : 0 );
			} else {
				$names[] = $name;
			}
			$remaining--;
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token && ( 0 < $remaining || ! $more ) );

	$client->getClient()->setUseBatch( true );
	$batch = $client->createBatch();
	dir_images_requests( $client, $batch, $ids, $options );
	dir_counts_requests( $client, $batch, $ids );
	$responses = $batch->execute();
	$client->getClient()->setUseBatch( false );

	$dir_images = dir_images_responses( $responses, $ids, $options );
	$dir_counts = dir_counts_responses( $responses, $ids );

	$ret   = [];
	$count = count( $ids );
	for ( $i = 0; $i < $count; $i++ ) {
		$val = [
			'id'        => $ids[ $i ],
			'name'      => $names[ $i ],
			'thumbnail' => $dir_images[ $i ],
		];
		if ( 'true' === $options->get( 'dir_counts' ) ) {
			$val = array_merge( $val, $dir_counts[ $i ] );
		}
		if ( 0 < $dir_counts[ $i ]['dircount'] + $dir_counts[ $i ]['imagecount'] ) {
			$ret[] = $val;
		}
	}
	return [ $ret, $skip, $remaining, $more ];
}

function dir_images_requests( $client, $batch, $dirs, $options ) {
	$params = [
		'supportsAllDrives'         => true,
		'includeItemsFromAllDrives' => true,
		'orderBy'                   => $options->get( 'image_ordering' ),
		'pageSize'                  => 1,
		'fields'                    => 'files(imageMediaMetadata(width, height), thumbnailLink)',
	];

	foreach ( $dirs as $dir ) {
		$params['q'] = '"' . $dir . '" in parents and mimeType contains "image/" and trashed = false';
		$request     = $client->files->listFiles( $params );
		$batch->add( $request, 'img-' . $dir );
	}
}

function dir_counts_requests( $client, $batch, $dirs ) {
	$params = [
		'supportsAllDrives'         => true,
		'includeItemsFromAllDrives' => true,
		'pageSize'                  => 1000,
		'fields'                    => 'files(id)',
	];

	foreach ( $dirs as $dir ) {
		$params['q'] = '"' . $dir . '" in parents and mimeType contains "application/vnd.google-apps.folder" and trashed = false';
		$request     = $client->files->listFiles( $params );
		$batch->add( $request, 'dircount-' . $dir );
		$params['q'] = '"' . $dir . '" in parents and mimeType contains "image/" and trashed = false';
		$request     = $client->files->listFiles( $params );
		$batch->add( $request, 'imgcount-' . $dir );
	}
}

function dir_images_responses( $responses, $dirs, $options ) {
	$ret = [];
	foreach ( $dirs as $dir ) {
		$response = $responses[ 'response-img-' . $dir ];
		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		$images = $response->getFiles();
		if ( count( $images ) === 0 ) {
			$ret[] = false;
		} else {
			$ret[] = substr( $images[0]->getThumbnailLink(), 0, -4 ) . ( $images[0]->getImageMediaMetadata()->getWidth() > $images[0]->getImageMediaMetadata()->getHeight() ? 'h' : 'w' ) . floor( 1.25 * $options->get( 'grid_height' ) );

		}
	}
	return $ret;
}

function dir_counts_responses( $responses, $dirs ) {
	$ret = [];
	foreach ( $dirs as $dir ) {
		$dir_response = $responses[ 'response-dircount-' . $dir ];
		$img_response = $responses[ 'response-imgcount-' . $dir ];
		if ( $dir_response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $dir_response;
		}
		if ( $img_response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $img_response;
		}
		$ret[] = [
			'dircount'   => count( $dir_response->getFiles() ),
			'imagecount' => count( $img_response->getFiles() ),
		];
	}
	return $ret;
}

function images( $client, $dir, $options, $skip, $remaining ) {
	$ret        = [];
	$page_token = null;
	do {
		$params = [
			'q'                         => '"' . $dir . '" in parents and mimeType contains "image/" and trashed = false',
			'supportsAllDrives'         => true,
			'includeItemsFromAllDrives' => true,
			'pageToken'                 => $page_token,
			'pageSize'                  => 1000,
		];
		if ( $options->get_by( 'image_ordering' ) === 'time' ) {
			$params['fields'] = 'nextPageToken, files(id, thumbnailLink, createdTime, imageMediaMetadata(time), description)';
		} else {
			$params['orderBy'] = $options->get( 'image_ordering' );
			$params['fields']  = 'nextPageToken, files(id, thumbnailLink, description)';
		}
		$response = $client->files->listFiles( $params );
		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		foreach ( $response->getFiles() as $file ) {
			$description = $file->getDescription();
			$val         = [
				'id'          => $file->getId(),
				'description' => ( isset( $description ) ? esc_attr( $description ) : '' ),
				'image'       => substr( $file->getThumbnailLink(), 0, -3 ) . $options->get( 'preview_size' ),
				'thumbnail'   => substr( $file->getThumbnailLink(), 0, -4 ) . 'h' . floor( 1.25 * $options->get( 'grid_height' ) ),
			];
			if ( $options->get_by( 'image_ordering' ) === 'time' ) {
				if ( $file->getImageMediaMetadata() && $file->getImageMediaMetadata()->getTime() ) {
					$val['timestamp'] = \DateTime::createFromFormat( 'Y:m:d H:i:s', $file->getImageMediaMetadata()->getTime() )->format( 'U' );
				} else {
					$val['timestamp'] = \DateTime::createFromFormat( 'Y-m-d\TH:i:s.uP', $file->getCreatedTime() )->format( 'U' );
				}
			}
			$ret[] = $val;
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );
	if ( $options->get_by( 'image_ordering' ) === 'time' ) {
		usort(
			$ret,
			function( $a, $b ) use ( $options ) {
				$asc = $a['timestamp'] - $b['timestamp'];
				return $options->get_order( 'image_ordering' ) === 'ascending' ? $asc : -$asc;
			}
		);
		array_walk(
			$ret,
			function( &$item ) {
				unset( $item['timestamp'] );
			}
		);
	}
	$more = count( $ret ) > $skip + $remaining;
	return [ array_slice( $ret, $skip, $remaining ), $more ];
}
