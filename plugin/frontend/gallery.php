<?php
namespace Sgdg\Frontend\Gallery;

function register() {
	add_action( 'wp_ajax_gallery', '\\Sgdg\\Frontend\\Gallery\\handle_ajax' );
	add_action( 'wp_ajax_nopriv_gallery', '\\Sgdg\\Frontend\\Gallery\\handle_ajax' );
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
	list( $client, $dir, $options ) = \Sgdg\Frontend\Page\get_context();

	$ret = [];
	// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
	if ( isset( $_GET['path'] ) && '' !== $_GET['path'] ) {
		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		$ret['path'] = path_names( $client, explode( '/', $_GET['path'] ), $options );
	}
		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
	$remaining = $options->get( 'page_size' ) * max( 1, (int) $_GET['page'] );
	$ret       = array_merge( $ret, \Sgdg\Frontend\Page\get_page( $client, $dir, 0, $remaining, $options ) );
	wp_send_json( $ret );
}

function path_names( $client, array $path, $options ) {
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
		$name = $response->getName();
		$pos  = false;
		if ( $options->get( 'dir_prefix' ) ) {
			$pos = mb_strpos( $name, $options->get( 'dir_prefix' ) );
		}
		$ret[] = [
			'id'   => $segment,
			'name' => mb_substr( $name, false !== $pos ? $pos + 1 : 0 ),
		];
	}
	return $ret;
}
