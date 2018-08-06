var gulp = require( 'gulp' );
var npmcheck = require( 'gulp-npm-check' );
var composer = require( 'gulp-composer' );
var shell = require( 'gulp-shell' );
var es = require( 'event-stream' );
var replace = require( 'gulp-replace' );
var eslint = require( 'gulp-eslint' );
var stylelint = require( 'gulp-stylelint' );

gulp.task( 'composer-check-updates', function( done ) {
		composer( 'show -l', {'self-install': false, 'async': false});
		done();
	});

gulp.task( 'npm-check-updates', function( done ) {
		npmcheck({'skipUnused': true}, done );
	});

gulp.task( 'composer-do-update', function( done ) {
		composer( 'update ', {'self-install': false, 'async': false});
		done();
	});

gulp.task( 'composer-copy-apiclient-services', function() {
		return gulp.src([
				'vendor/google/apiclient-services/src/Google/Service/Drive.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/DriveFileImageMediaMetadata.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/DriveFile.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/FileList.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/Resource/*',
				'vendor/google/apiclient-services/src/Google/Service/Drive/TeamDrive.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/TeamDriveList.php'
			], {base: 'vendor/'})
			.pipe( replace( /^<\?php/, '<?php\nnamespace Sgdg\\Vendor;' ) )
			.pipe( replace( /\nuse /g, '\nuse Sgdg\\Vendor\\' ) )
			.pipe( gulp.dest( 'plugin/bundled/vendor/' ) );
	});

gulp.task( 'composer-copy-apiclient', function() {
		return es.merge(
			gulp.src([
					'vendor/google/apiclient/src/Google/AccessToken/Revoke.php',
					'vendor/google/apiclient/src/Google/AuthHandler/AuthHandlerFactory.php',
					'vendor/google/apiclient/src/Google/AuthHandler/Guzzle6AuthHandler.php',
					'vendor/google/apiclient/src/Google/Client.php',
					'vendor/google/apiclient/src/Google/Collection.php',
					'vendor/google/apiclient/src/Google/Exception.php',
					'vendor/google/apiclient/src/Google/Http/Batch.php',
					'vendor/google/apiclient/src/Google/Http/REST.php',
					'vendor/google/apiclient/src/Google/Service.php',
					'vendor/google/apiclient/src/Google/Service/Exception.php',
					'vendor/google/apiclient/src/Google/Task/Runner.php',
					'vendor/google/apiclient/src/Google/Utils/*',
					'!**/autoload.php',
					'!**/README*'
				], {base: 'vendor/'})
				.pipe( replace( /^<\?php/, '<?php\nnamespace Sgdg\\Vendor;' ) )
				.pipe( replace( /\nuse /g, '\nuse Sgdg\\Vendor\\' ) )
				.pipe( replace( /class_exists\('(?!\\)/g, 'class_exists(\'\\\\Sgdg\\\\Vendor\\\\' ) )
				.pipe( replace( / Iterator/g, ' \\Iterator' ) )
				.pipe( replace( / Countable/g, ' \\Countable' ) )
				.pipe( replace( / Exception/g, ' \\Exception' ) ),
			gulp.src([
					'vendor/google/apiclient/src/Google/Model.php'
				], {base: 'vendor/'})
				.pipe( replace( /^<\?php/, '<?php\nnamespace Sgdg\\Vendor;' ) )
				.pipe( replace( / ArrayAccess/g, ' \\ArrayAccess' ) )
				.pipe( replace( 'class_exists($this->$keyType)', 'class_exists(\'\\\\Sgdg\\\\Vendor\\\\\' . $this->$keyType)' ) )
				.pipe( replace( 'return $this->$keyType;', 'return \'\\\\Sgdg\\\\Vendor\\\\\' . $this->$keyType;' ) ),
			gulp.src([
					'vendor/google/apiclient/src/Google/Service/Resource.php'
				], {base: 'vendor/'})
				.pipe( replace( /^<\?php/, '<?php\nnamespace Sgdg\\Vendor;' ) )
				.pipe( replace( /\nuse /g, '\nuse Sgdg\\Vendor\\' ) )
				.pipe( replace( 'public function call($name, $arguments, $expectedClass = null)\n  {', 'public function call($name, $arguments, $expectedClass = null)\n  {\n    $expectedClass = \'\\\\Sgdg\\\\Vendor\\\\\' . $expectedClass;' ) )
		)
			.pipe( gulp.dest( 'plugin/bundled/vendor/' ) );
	});

gulp.task( 'composer-copy-other', function() {
		return gulp.src([
				'vendor/google/auth/src/Cache/Item.php',
				'vendor/google/auth/src/Cache/MemoryCacheItemPool.php',
				'vendor/google/auth/src/CacheTrait.php',
				'vendor/google/auth/src/FetchAuthTokenInterface.php',
				'vendor/google/auth/src/HttpHandler/Guzzle6HttpHandler.php',
				'vendor/google/auth/src/HttpHandler/HttpHandlerFactory.php',
				'vendor/google/auth/src/Middleware/ScopedAccessTokenMiddleware.php',
				'vendor/google/auth/src/OAuth2.php',
				'vendor/guzzlehttp/guzzle/src/Client.php',
				'vendor/guzzlehttp/guzzle/src/ClientInterface.php',
				'vendor/guzzlehttp/guzzle/src/Handler/CurlFactory.php',
				'vendor/guzzlehttp/guzzle/src/Handler/CurlFactoryInterface.php',
				'vendor/guzzlehttp/guzzle/src/Handler/CurlHandler.php',
				'vendor/guzzlehttp/guzzle/src/Handler/CurlMultiHandler.php',
				'vendor/guzzlehttp/guzzle/src/Handler/EasyHandle.php',
				'vendor/guzzlehttp/guzzle/src/Handler/Proxy.php',
				'vendor/guzzlehttp/guzzle/src/Handler/StreamHandler.php',
				'vendor/guzzlehttp/guzzle/src/HandlerStack.php',
				'vendor/guzzlehttp/guzzle/src/Middleware.php',
				'vendor/guzzlehttp/guzzle/src/PrepareBodyMiddleware.php',
				'vendor/guzzlehttp/guzzle/src/RedirectMiddleware.php',
				'vendor/guzzlehttp/guzzle/src/RequestOptions.php',
				'vendor/guzzlehttp/guzzle/src/functions.php',
				'vendor/guzzlehttp/promises/src/FulfilledPromise.php',
				'vendor/guzzlehttp/promises/src/Promise.php',
				'vendor/guzzlehttp/promises/src/PromiseInterface.php',
				'vendor/guzzlehttp/promises/src/TaskQueue.php',
				'vendor/guzzlehttp/promises/src/TaskQueueInterface.php',
				'vendor/guzzlehttp/promises/src/functions.php',
				'vendor/guzzlehttp/psr7/src/MessageTrait.php',
				'vendor/guzzlehttp/psr7/src/Request.php',
				'vendor/guzzlehttp/psr7/src/Response.php',
				'vendor/guzzlehttp/psr7/src/Stream.php',
				'vendor/guzzlehttp/psr7/src/Uri.php',
				'vendor/guzzlehttp/psr7/src/UriResolver.php',
				'vendor/guzzlehttp/psr7/src/functions.php',
				'vendor/monolog/monolog/src/Monolog/Handler/AbstractHandler.php',
				'vendor/monolog/monolog/src/Monolog/Handler/AbstractProcessingHandler.php',
				'vendor/monolog/monolog/src/Monolog/Handler/HandlerInterface.php',
				'vendor/monolog/monolog/src/Monolog/Handler/StreamHandler.php',
				'vendor/monolog/monolog/src/Monolog/Logger.php',
				'vendor/psr/cache/src/CacheItemInterface.php',
				'vendor/psr/cache/src/CacheItemPoolInterface.php',
				'vendor/psr/http-message/src/MessageInterface.php',
				'vendor/psr/http-message/src/RequestInterface.php',
				'vendor/psr/http-message/src/ResponseInterface.php',
				'vendor/psr/http-message/src/StreamInterface.php',
				'vendor/psr/http-message/src/UriInterface.php',
				'vendor/psr/log/Psr/Log/LoggerInterface.php'
			], {base: 'vendor/'})
			.pipe( replace( /\nnamespace /g, '\nnamespace Sgdg\\Vendor\\' ) )
			.pipe( replace( /\nuse /g, '\nuse Sgdg\\Vendor\\' ) )
			.pipe( replace( ' \\GuzzleHttp', ' \\Sgdg\\Vendor\\GuzzleHttp' ) )
			.pipe( gulp.dest( 'plugin/bundled/vendor/' ) );
	});

gulp.task( 'composer-copy-licenses', function() {
		return gulp.src([
				'vendor/google/apiclient-services/LICENSE',
				'vendor/google/apiclient/LICENSE',
				'vendor/google/auth/LICENSE',
				'vendor/guzzlehttp/guzzle/LICENSE',
				'vendor/guzzlehttp/promises/LICENSE',
				'vendor/guzzlehttp/psr7/LICENSE',
				'vendor/monolog/monolog/LICENSE',
				'vendor/psr/cache/LICENSE.txt',
				'vendor/psr/http-message/LICENSE',
				'vendor/psr/log/LICENSE'
			], {base: 'vendor/'})
			.pipe( gulp.dest( 'plugin/bundled/vendor/' ) );
	});

gulp.task( 'composer-copy', gulp.parallel( 'composer-copy-apiclient-services', 'composer-copy-apiclient', 'composer-copy-other', 'composer-copy-licenses' ) );

function copyImagelightbox() {
	return gulp.src( 'node_modules/imagelightbox/dist/imagelightbox.min.*' )
		.pipe( gulp.dest( 'plugin/bundled/' ) );
}

function copyImagesloaded() {
	return gulp.src( 'node_modules/imagesloaded/imagesloaded.pkgd.min.js' )
		.pipe( gulp.dest( 'plugin/bundled/' ) );
}

function copyJustifiedLayout() {
	return gulp.src( 'node_modules/justified-layout/dist/justified-layout.min.js' )
		.pipe( gulp.dest( 'plugin/bundled/' ) );
}

gulp.task( 'composer-update', gulp.series( 'composer-do-update', 'composer-copy' ) );

gulp.task( 'npm-update', gulp.series( shell.task([ 'npm install', 'npm update' ]), gulp.parallel( copyImagelightbox, copyImagesloaded, copyJustifiedLayout ) ) );

gulp.task( 'phpcs', shell.task([ 'vendor/squizlabs/php_codesniffer/bin/phpcs' ]) );

gulp.task( 'eslint', function() {
		return gulp.src([ '**/*.js', '!node_modules/**', '!vendor/**', '!plugin/bundled/**' ])
			.pipe( eslint() )
			.pipe( eslint.format() )
			.pipe( eslint.failAfterError() );
	});

gulp.task( 'stylelint', function() {
		return gulp.src([ 'plugin/**/*.css', '!plugin/bundled/**' ])
			.pipe( stylelint({
				failAfterError: true,
				reporters: [
					{formatter: 'string', console: true}
				]
			}) );
	});

gulp.task( 'default', gulp.series( 'phpcs', 'eslint', 'stylelint', 'composer-check-updates', 'npm-check-updates' ) );
