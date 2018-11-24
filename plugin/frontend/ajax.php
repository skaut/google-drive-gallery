<?php
namespace Sgdg\Frontend\Ajax;

function register() {
	add_action( 'wp_ajax_list_dir', '\\Sgdg\\Frontend\\Ajax\\handle_ajax' );
	add_action( 'wp_ajax_nopriv_list_dir', '\\Sgdg\\Frontend\\Ajax\\handle_ajax' );
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
	$client = \Sgdg\Frontend\GoogleAPILib\get_drive_client();

	// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
	$transient = get_transient( 'sgdg_hash_' . $_GET['hash'] );
	$dir       = $transient['root'];

	if ( false === $dir ) {
		throw new \Exception( esc_html__( 'The gallery has expired.', 'skaut-google-drive-gallery' ) );
	}

	$options = new \Sgdg\Frontend\Options_Proxy( $transient['overriden'] );

	$ret = [];
	// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
	if ( isset( $_GET['path'] ) && '' !== $_GET['path'] ) {

		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		$path        = explode( '/', $_GET['path'] );
		$ret['path'] = path_names( $client, $path );
		$dir         = apply_path( $client, $dir, $path );
	}
	$ret['directories'] = directories( $client, $dir, $options );
	$ret['images']      = images( $client, $dir, $options );
	wp_send_json( $ret );
}

function path_names( $client, array $path, array $used_path = [] ) {
	$client->getClient()->setUseBatch( true );
	$batch = $client->createBatch();
	foreach ( $path as $segment ) {
		$request = $client->files->get(
			$segment,
			[
				'supportsTeamDrives' => true,
				'fields'             => 'name',
			]
		);
		$batch->add( $request, $segment );
	}
	$responses = $batch->execute();
	$client->getClient()->setUseBatch( false );
	$ret = [];
	foreach ( $path as $segment ) {
		$response = $responses[ 'response-' . $segment ];
		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		$ret[] = [
			'id'   => $segment,
			'name' => $response->getName(),
		];
	}
	return $ret;
}

function apply_path( $client, $root, array $path ) {
	$page_token = null;
	do {
		$params   = [
			'q'                     => '"' . $root . '" in parents and mimeType = "application/vnd.google-apps.folder" and trashed = false',
			'supportsTeamDrives'    => true,
			'includeTeamDriveItems' => true,
			'pageToken'             => $page_token,
			'pageSize'              => 1000,
			'fields'                => 'nextPageToken, files(id)',
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

function directories( $client, $dir, $options ) {
	$ids   = [];
	$names = [];

	$page_token = null;
	do {
		$params   = [
			'q'                     => '"' . $dir . '" in parents and mimeType = "application/vnd.google-apps.folder" and trashed = false',
			'supportsTeamDrives'    => true,
			'includeTeamDriveItems' => true,
			'orderBy'               => $options->get( 'dir_ordering' ),
			'pageToken'             => $page_token,
			'pageSize'              => 1000,
			'fields'                => 'nextPageToken, files(id, name)',
		];
		$response = $client->files->listFiles( $params );
		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		foreach ( $response->getFiles() as $file ) {
			$ids[]   = $file->getId();
			$names[] = $file->getName();
		}
		$page_token = $response->getNextPageToken();
	} while ( null !== $page_token );

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
	return $ret;
}

function dir_images_requests( $client, $batch, $dirs, $options ) {
	$params = [
		'supportsTeamDrives'    => true,
		'includeTeamDriveItems' => true,
		'orderBy'               => $options->get( 'image_ordering' ),
		'pageSize'              => 1,
		'fields'                => 'files(imageMediaMetadata(width, height), thumbnailLink)',
	];

	foreach ( $dirs as $dir ) {
		$params['q'] = '"' . $dir . '" in parents and mimeType contains "image/" and trashed = false';
		$request     = $client->files->listFiles( $params );
		$batch->add( $request, 'img-' . $dir );
	}
}

function dir_counts_requests( $client, $batch, $dirs ) {
	$params = [
		'supportsTeamDrives'    => true,
		'includeTeamDriveItems' => true,
		'pageSize'              => 1000,
		'fields'                => 'files(id)',
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

function images( $client, $dir, $options ) {
	$ret        = [];
	$page_token = null;
	do {
		$params = [
			'q'                     => '"' . $dir . '" in parents and mimeType contains "image/" and trashed = false',
			'supportsTeamDrives'    => true,
			'includeTeamDriveItems' => true,
			'pageToken'             => $page_token,
			'pageSize'              => 1000,
		];
		if ( $options->get_by( 'image_ordering' ) === 'time' ) {
			$params['fields'] = 'nextPageToken, files(id, thumbnailLink, createdTime, imageMediaMetadata(time))';
		} else {
			$params['orderBy'] = $options->get( 'image_ordering' );
			$params['fields']  = 'nextPageToken, files(id, thumbnailLink)';
		}
		$response = $client->files->listFiles( $params );
		if ( $response instanceof \Sgdg\Vendor\Google_Service_Exception ) {
			throw $response;
		}
		foreach ( $response->getFiles() as $file ) {
			$val = [
				'id'        => $file->getId(),
				'image'     => substr( $file->getThumbnailLink(), 0, -3 ) . $options->get( 'preview_size' ),
				'thumbnail' => substr( $file->getThumbnailLink(), 0, -4 ) . 'h' . floor( 1.25 * $options->get( 'grid_height' ) ),
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
	return $ret;
}
