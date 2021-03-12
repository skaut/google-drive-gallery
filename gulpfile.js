/* eslint-env node */

const gulp = require( 'gulp' );

const cleanCSS = require( 'gulp-clean-css' );
const concat = require( 'gulp-concat' );
const inject = require( 'gulp-inject-string' );
const merge = require( 'merge-stream' );
const rename = require( 'gulp-rename' );
const replace = require( 'gulp-replace' );
const shell = require( 'gulp-shell' );
const terser = require( 'gulp-terser' );
const ts = require( 'gulp-typescript' );

gulp.task( 'build:css:admin', function () {
	return gulp
		.src( [ 'src/css/admin/*.css' ] )
		.pipe( cleanCSS( { compatibility: 'ie8' } ) )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'dist/admin/css/' ) );
} );

gulp.task( 'build:css:frontend', function () {
	return gulp
		.src( [ 'src/css/frontend/*.css' ] )
		.pipe( cleanCSS( { compatibility: 'ie8' } ) )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'dist/frontend/css/' ) );
} );

gulp.task(
	'build:css',
	gulp.parallel( 'build:css:admin', 'build:css:frontend' )
);

gulp.task( 'build:deps:composer:apiclient', function () {
	return merge(
		gulp
			.src(
				[
					'vendor/google/apiclient/src/AccessToken/Revoke.php',
					'vendor/google/apiclient/src/AccessToken/Verify.php',
					'vendor/google/apiclient/src/AuthHandler/AuthHandlerFactory.php',
					'vendor/google/apiclient/src/AuthHandler/Guzzle5AuthHandler.php',
					'vendor/google/apiclient/src/AuthHandler/Guzzle6AuthHandler.php',
					'vendor/google/apiclient/src/AuthHandler/Guzzle7AuthHandler.php',
					'vendor/google/apiclient/src/Client.php',
					'vendor/google/apiclient/src/Collection.php',
					'vendor/google/apiclient/src/Exception.php',
					'vendor/google/apiclient/src/Http/Batch.php',
					'vendor/google/apiclient/src/Http/MediaFileUpload.php',
					'vendor/google/apiclient/src/Http/REST.php',
					'vendor/google/apiclient/src/Model.php',
					'vendor/google/apiclient/src/Service.php',
					'vendor/google/apiclient/src/Service/Exception.php',
					'vendor/google/apiclient/src/Service/Resource.php',
					'vendor/google/apiclient/src/Task/Exception.php',
					'vendor/google/apiclient/src/Task/Composer.php',
					'vendor/google/apiclient/src/Task/Retryable.php',
					'vendor/google/apiclient/src/Task/Runner.php',
					'vendor/google/apiclient/src/Utils/*',
					'!**/autoload.php',
					'!**/README*',
				],
				{ base: 'vendor/' }
			)
			.pipe(
				replace(
					/\nnamespace Google/,
					'\nnamespace Sgdg\\Vendor\\Google'
				)
			)
			.pipe(
				replace(
					/\nuse BadMethodCallException/g,
					'\nuse \\BadMethodCallException'
				)
			)
			.pipe(
				replace( /\nuse DomainException/g, '\nuse \\DomainException' )
			)
			.pipe(
				replace(
					/\nuse InvalidArgumentException/g,
					'\nuse \\InvalidArgumentException'
				)
			)
			.pipe(
				replace( /\nuse LogicException/g, '\nuse \\LogicException' )
			)
			.pipe( replace( /\nuse Exception/g, '\nuse \\Exception' ) )
			.pipe( replace( /\nuse TypeError/g, '\nuse \\TypeError' ) )
			.pipe(
				replace( /\nuse ReflectionObject/g, '\nuse \\ReflectionObject' )
			)
			.pipe(
				replace(
					/\nuse ReflectionProperty/g,
					'\nuse \\ReflectionProperty'
				)
			)
			.pipe( replace( /\nuse stdClass/g, '\nuse \\stdClass' ) )
			.pipe( replace( /\nuse ([^\\])/g, '\nuse Sgdg\\Vendor\\$1' ) )
			.pipe(
				replace(
					/defined\('\\?GuzzleHttp/g,
					"defined('\\Sgdg\\Vendor\\GuzzleHttp"
				)
			)
			.pipe(
				replace(
					'class_exists($this->$keyType)',
					"class_exists('\\\\Sgdg\\\\Vendor\\\\' . $this->$keyType)"
				)
			)
			.pipe(
				replace(
					'return $this->$keyType;',
					"return '\\\\Sgdg\\\\Vendor\\\\' . $this->$keyType;"
				)
			)
			.pipe(
				replace(
					'public function call($name, $arguments, $expectedClass = null)\n  {',
					"public function call($name, $arguments, $expectedClass = null)\n  {\n    $expectedClass = '\\\\Sgdg\\\\Vendor\\\\' . $expectedClass;"
				)
			),
		gulp
			.src( [ 'vendor/google/apiclient/src/aliases.php' ], {
				base: 'vendor/',
			} )
			.pipe(
				replace(
					"class_exists('Google_Client'",
					"class_exists('\\\\Sgdg\\\\Vendor\\\\Google_Client'"
				)
			)
			.pipe(
				replace(
					/\n {4}'Google\\\\([^']*)' => 'Google_([^']*)',/g,
					"\n    'Sgdg\\\\Vendor\\\\Google\\\\$1' => 'Sgdg\\\\Vendor\\\\Google_$2',"
				)
			)
			.pipe(
				replace(
					/\nclass Google_Task_Composer extends \\Google\\Task\\Composer\n{\n}/g,
					'\n//class Google_Task_Composer extends \\Google\\Task\\Composer\n//{\n//}'
				)
			)
			.pipe(
				replace(
					/\nclass Google_([^ ]*) extends \\Google\\([^ ]*)/g,
					'\nclass Google_$1 extends \\Sgdg\\Vendor\\Google\\$2'
				)
			)
	).pipe( gulp.dest( 'dist/bundled/vendor/' ) );
} );

gulp.task( 'build:deps:composer:apiclient-services', function () {
	return gulp
		.src(
			[
				'vendor/google/apiclient-services/src/Google/Service/Drive.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/Drive.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/DriveList.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/DriveFileShortcutDetails.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/DriveFileImageMediaMetadata.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/DriveFileVideoMediaMetadata.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/DriveFile.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/FileList.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/Resource/*',
			],
			{ base: 'vendor/' }
		)
		.pipe( replace( /^<\?php/, '<?php\nnamespace Sgdg\\Vendor;' ) )
		.pipe( gulp.dest( 'dist/bundled/vendor/' ) );
} );

gulp.task( 'build:deps:composer:licenses', function () {
	return gulp
		.src(
			[
				'vendor/google/apiclient-services/LICENSE',
				'vendor/google/apiclient/LICENSE',
				'vendor/google/auth/LICENSE',
				'vendor/guzzlehttp/guzzle/LICENSE',
				'vendor/guzzlehttp/promises/LICENSE',
				'vendor/guzzlehttp/psr7/LICENSE',
				'vendor/monolog/monolog/LICENSE',
				'vendor/psr/cache/LICENSE.txt',
				'vendor/psr/http-message/LICENSE',
				'vendor/psr/log/LICENSE',
				'vendor/symfony/polyfill-intl-idn/LICENSE',
				'vendor/symfony/polyfill-mbstring/LICENSE',
			],
			{ base: 'vendor/' }
		)
		.pipe( gulp.dest( 'dist/bundled/vendor/' ) );
} );

gulp.task( 'build:deps:composer:other', function () {
	return gulp
		.src(
			[
				'vendor/google/auth/src/Cache/Item.php',
				'vendor/google/auth/src/Cache/MemoryCacheItemPool.php',
				'vendor/google/auth/src/Credentials/UserRefreshCredentials.php',
				'vendor/google/auth/src/CacheTrait.php',
				'vendor/google/auth/src/FetchAuthTokenInterface.php',
				'vendor/google/auth/src/GetQuotaProjectInterface.php',
				'vendor/google/auth/src/SignBlobInterface.php',
				'vendor/google/auth/src/ProjectIdProviderInterface.php',
				'vendor/google/auth/src/UpdateMetadataInterface.php',
				'vendor/google/auth/src/FetchAuthTokenCache.php',
				'vendor/google/auth/src/CredentialsLoader.php',
				'vendor/google/auth/src/HttpHandler/HttpHandlerFactory.php',
				'vendor/google/auth/src/Middleware/AuthTokenMiddleware.php',
				'vendor/google/auth/src/Middleware/ScopedAccessTokenMiddleware.php',

				'vendor/google/auth/src/HttpHandler/Guzzle6HttpHandler.php',
				'vendor/google/auth/src/OAuth2.php',
				'vendor/guzzlehttp/guzzle/src/Client.php',
				'vendor/guzzlehttp/guzzle/src/ClientInterface.php',
				'vendor/guzzlehttp/guzzle/src/Cookie/CookieJarInterface.php',
				'vendor/guzzlehttp/guzzle/src/Cookie/CookieJar.php',
				'vendor/guzzlehttp/guzzle/src/Cookie/SetCookie.php',
				'vendor/guzzlehttp/guzzle/src/Exception/GuzzleException.php',
				'vendor/guzzlehttp/guzzle/src/Exception/TransferException.php',
				'vendor/guzzlehttp/guzzle/src/Exception/RequestException.php',
				'vendor/guzzlehttp/guzzle/src/Exception/BadResponseException.php',
				'vendor/guzzlehttp/guzzle/src/Exception/ClientException.php',
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
				'vendor/guzzlehttp/guzzle/src/Utils.php',
				'vendor/guzzlehttp/guzzle/src/functions.php',
				'vendor/guzzlehttp/promises/src/Create.php',
				'vendor/guzzlehttp/promises/src/Each.php',
				'vendor/guzzlehttp/promises/src/EachPromise.php',
				'vendor/guzzlehttp/promises/src/Is.php',
				'vendor/guzzlehttp/promises/src/Promise.php',
				'vendor/guzzlehttp/promises/src/PromiseInterface.php',
				'vendor/guzzlehttp/promises/src/PromisorInterface.php',
				'vendor/guzzlehttp/promises/src/FulfilledPromise.php',
				'vendor/guzzlehttp/promises/src/RejectedPromise.php',
				'vendor/guzzlehttp/promises/src/RejectionException.php',
				'vendor/guzzlehttp/promises/src/TaskQueue.php',
				'vendor/guzzlehttp/promises/src/TaskQueueInterface.php',
				'vendor/guzzlehttp/promises/src/Utils.php',
				'vendor/guzzlehttp/promises/src/functions.php',
				'vendor/guzzlehttp/psr7/src/MessageTrait.php',
				'vendor/guzzlehttp/psr7/src/Query.php',
				'vendor/guzzlehttp/psr7/src/Request.php',
				'vendor/guzzlehttp/psr7/src/Response.php',
				'vendor/guzzlehttp/psr7/src/Stream.php',
				'vendor/guzzlehttp/psr7/src/Uri.php',
				'vendor/guzzlehttp/psr7/src/UriResolver.php',
				'vendor/guzzlehttp/psr7/src/Utils.php',
				'vendor/guzzlehttp/psr7/src/functions.php',
				'vendor/monolog/monolog/src/Monolog/Handler/AbstractHandler.php',
				'vendor/monolog/monolog/src/Monolog/Handler/AbstractProcessingHandler.php',
				'vendor/monolog/monolog/src/Monolog/Handler/HandlerInterface.php',
				'vendor/monolog/monolog/src/Monolog/Handler/StreamHandler.php',
				'vendor/monolog/monolog/src/Monolog/Logger.php',
				'vendor/monolog/monolog/src/Monolog/ResettableInterface.php',
				'vendor/monolog/monolog/src/Monolog/Utils.php',
				'vendor/psr/cache/src/CacheItemInterface.php',
				'vendor/psr/cache/src/CacheItemPoolInterface.php',
				'vendor/psr/http-message/src/MessageInterface.php',
				'vendor/psr/http-message/src/RequestInterface.php',
				'vendor/psr/http-message/src/ResponseInterface.php',
				'vendor/psr/http-message/src/StreamInterface.php',
				'vendor/psr/http-message/src/UriInterface.php',
				'vendor/psr/log/Psr/Log/LoggerInterface.php',
				'vendor/symfony/polyfill-intl-idn/bootstrap.php',
				'vendor/symfony/polyfill-intl-idn/Idn.php',
				'vendor/symfony/polyfill-mbstring/bootstrap.php',
				'vendor/symfony/polyfill-mbstring/Mbstring.php',
			],
			{ base: 'vendor/' }
		)
		.pipe( replace( /\nnamespace /g, '\nnamespace Sgdg\\Vendor\\' ) )
		.pipe( replace( /\nuse /g, '\nuse Sgdg\\Vendor\\' ) )
		.pipe( replace( ' \\GuzzleHttp', ' \\Sgdg\\Vendor\\GuzzleHttp' ) )
		.pipe(
			replace(
				/defined\('GuzzleHttp/g,
				"defined('\\Sgdg\\Vendor\\GuzzleHttp"
			)
		)
		.pipe( gulp.dest( 'dist/bundled/vendor/' ) );
} );

gulp.task(
	'build:deps:composer',
	gulp.parallel(
		'build:deps:composer:apiclient',
		'build:deps:composer:apiclient-services',
		'build:deps:composer:licenses',
		'build:deps:composer:other'
	)
);

gulp.task( 'build:deps:npm:imagelightbox', function () {
	return gulp
		.src( 'node_modules/imagelightbox/dist/imagelightbox.min.*' )
		.pipe( gulp.dest( 'dist/bundled/' ) );
} );

gulp.task( 'build:deps:npm:imagesloaded', function () {
	return gulp
		.src( 'node_modules/imagesloaded/imagesloaded.pkgd.min.js' )
		.pipe( gulp.dest( 'dist/bundled/' ) );
} );

gulp.task(
	'build:deps:npm:justified-layout',
	gulp.series(
		shell.task( [ 'npm install' ], {
			cwd: 'node_modules/justified-layout',
		} ),
		function () {
			return gulp
				.src(
					'node_modules/justified-layout/dist/justified-layout.min.*'
				)
				.pipe( gulp.dest( 'dist/bundled/' ) );
		}
	)
);

gulp.task(
	'build:deps:npm',
	gulp.parallel(
		'build:deps:npm:imagelightbox',
		'build:deps:npm:imagesloaded',
		'build:deps:npm:justified-layout'
	)
);

gulp.task(
	'build:deps',
	gulp.parallel( 'build:deps:composer', 'build:deps:npm' )
);

gulp.task( 'build:js:admin', function () {
	function bundle( name, sources ) {
		const tsProject = ts.createProject( 'tsconfig.json' );
		return gulp
			.src( sources.concat( [ 'src/d.ts/*.d.ts' ] ) )
			.pipe( tsProject() )
			.js.pipe( concat( name + '.min.js' ) )
			.pipe( terser( { ie8: true } ) )
			.pipe( gulp.dest( 'dist/admin/js/' ) );
	}

	return merge(
		bundle( 'root_selection', [
			'src/ts/isError.ts',
			'src/ts/admin/root_selection.ts',
		] ),
		bundle( 'tinymce', [ 'src/ts/isError.ts', 'src/ts/admin/tinymce.ts' ] )
	);
} );

gulp.task( 'build:js:frontend', function () {
	function bundle( name, sources, jQuery = false ) {
		const tsProject = ts.createProject( 'tsconfig.json' );
		let ret = gulp
			.src( sources.concat( [ 'src/d.ts/*.d.ts' ] ) )
			.pipe( tsProject() )
			.js.pipe( concat( name + '.min.js' ) );
		if ( jQuery ) {
			ret = ret
				.pipe(
					inject.prepend(
						'jQuery( document ).ready( function( $ ) {\n'
					)
				)
				.pipe( inject.append( '} );\n' ) );
		}
		return ret
			.pipe( terser( { ie8: true } ) )
			.pipe( gulp.dest( 'dist/frontend/js/' ) );
	}

	return merge(
		bundle(
			'block',
			[
				'src/ts/isError.ts',
				'src/ts/frontend/block/SgdgEditorComponent.ts',
				'src/ts/frontend/block/SgdgBlockIconComponent.ts',
				'src/ts/frontend/block.ts',
				'src/ts/frontend/block/SgdgSettingsComponent.ts',
				'src/ts/frontend/block/SgdgBooleanSettingsComponent.ts',
				'src/ts/frontend/block/SgdgIntegerSettingsComponent.ts',
				'src/ts/frontend/block/SgdgOrderingSettingsComponent.ts',
				'src/ts/frontend/block/SgdgSettingsOverrideComponent.ts',
				'src/ts/frontend/interfaces/Attributes.ts',
			],
			true
		),
		bundle(
			'shortcode',
			[
				'src/ts/isError.ts',
				'src/ts/frontend/shortcode/QueryParameter.ts',
				'src/ts/frontend/shortcode/Shortcode.ts',
				'src/ts/frontend/shortcode/ShortcodeRegistry.ts',
				'src/ts/frontend/shortcode.ts',
			],
			true
		)
	);
} );

gulp.task( 'build:js', gulp.parallel( 'build:js:admin', 'build:js:frontend' ) );

gulp.task( 'build:php:admin', function () {
	return gulp
		.src( [ 'src/php/admin/**/*.php' ] )
		.pipe( gulp.dest( 'dist/admin/' ) );
} );

gulp.task( 'build:php:base', function () {
	return gulp.src( [ 'src/php/*.php' ] ).pipe( gulp.dest( 'dist/' ) );
} );

gulp.task( 'build:php:bundled', function () {
	return gulp
		.src( [ 'src/php/bundled/*.php' ] )
		.pipe( gulp.dest( 'dist/bundled/' ) );
} );

gulp.task( 'build:php:exceptions', function () {
	return gulp
		.src( [ 'src/php/exceptions/**/*.php' ] )
		.pipe( gulp.dest( 'dist/exceptions/' ) );
} );

gulp.task( 'build:php:frontend', function () {
	return gulp
		.src( [ 'src/php/frontend/**/*.php' ] )
		.pipe( gulp.dest( 'dist/frontend/' ) );
} );

gulp.task(
	'build:php',
	gulp.parallel(
		'build:php:admin',
		'build:php:base',
		'build:php:bundled',
		'build:php:exceptions',
		'build:php:frontend'
	)
);

gulp.task( 'build:png', function () {
	return gulp
		.src( [ 'src/png/icon.png' ] )
		.pipe( gulp.dest( 'dist/admin/' ) );
} );

gulp.task( 'build:txt', function () {
	return gulp.src( [ 'src/txt/*.txt' ] ).pipe( gulp.dest( 'dist/' ) );
} );

gulp.task(
	'build',
	gulp.parallel(
		'build:css',
		'build:deps',
		'build:js',
		'build:php',
		'build:png',
		'build:txt'
	)
);
