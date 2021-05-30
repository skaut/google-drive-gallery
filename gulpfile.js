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

gulp.task( 'build:css:admin', () =>
	gulp
		.src( [ 'src/css/admin/*.css' ] )
		.pipe( cleanCSS( { compatibility: 'ie8' } ) )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'dist/admin/css/' ) )
);

gulp.task( 'build:css:frontend', () =>
	gulp
		.src( [ 'src/css/frontend/*.css' ] )
		.pipe( cleanCSS( { compatibility: 'ie8' } ) )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'dist/frontend/css/' ) )
);

gulp.task(
	'build:css',
	gulp.parallel( 'build:css:admin', 'build:css:frontend' )
);

gulp.task(
	'build:deps:composer:scoper',
	shell.task(
		'vendor/bin/php-scoper add-prefix --force --output-dir=dist/bundled/vendor'
	)
);

gulp.task(
	'build:deps:composer:autoloader',
	gulp.series(
		shell.task( 'composer dump-autoload --no-dev' ),
		() =>
			merge(
				gulp.src( [
					'vendor/composer/autoload_classmap.php',
					'vendor/composer/autoload_files.php',
					'vendor/composer/autoload_namespaces.php',
					'vendor/composer/autoload_psr4.php',
				] ),
				gulp
					.src( [ 'vendor/composer/autoload_static.php' ] )
					.pipe(
						replace(
							'namespace Composer\\Autoload;',
							'namespace Sgdg\\Vendor\\Composer\\Autoload;'
						)
					)
					.pipe(
						replace(
							/'(.*)\\\\' => \n/g,
							"'Sgdg\\\\Vendor\\\\$1\\\\' => \n"
						)
					)
			).pipe( gulp.dest( 'dist/bundled/vendor/composer/' ) ),
		shell.task( 'composer dump-autoload' )
	)
);

gulp.task(
	'build:deps:composer',
	gulp.series(
		'build:deps:composer:scoper',
		'build:deps:composer:autoloader'
	)
);

gulp.task( 'build:deps:npm:imagelightbox', () =>
	gulp
		.src( 'node_modules/imagelightbox/dist/imagelightbox.min.*' )
		.pipe( gulp.dest( 'dist/bundled/' ) )
);

gulp.task( 'build:deps:npm:imagesloaded', () =>
	gulp
		.src( 'node_modules/imagesloaded/imagesloaded.pkgd.min.js' )
		.pipe( gulp.dest( 'dist/bundled/' ) )
);

gulp.task(
	'build:deps:npm:justified-layout',
	gulp.series(
		shell.task( [ 'npm install' ], {
			cwd: 'node_modules/justified-layout',
		} ),
		() =>
			gulp
				.src(
					'node_modules/justified-layout/dist/justified-layout.min.*'
				)
				.pipe( gulp.dest( 'dist/bundled/' ) )
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

function jsBundle( name, type, sources ) {
	const tsProject = ts.createProject( 'tsconfig.json' );
	return gulp
		.src( sources.concat( [ 'src/d.ts/*.d.ts' ] ) )
		.pipe( tsProject() )
		.js.pipe( concat( name + '.min.js' ) )
		.pipe( inject.prepend( 'jQuery( document ).ready( function( $ ) {\n' ) )
		.pipe( inject.append( '} );\n' ) )
		.pipe( terser( { ie8: true } ) )
		.pipe( gulp.dest( 'dist/' + type + '/js/' ) );
}

gulp.task(
	'build:js',
	gulp.parallel(
		() =>
			jsBundle( 'root_selection', 'admin', [
				'src/ts/isError.ts',
				'src/ts/admin/root_selection.ts',
			] ),
		() =>
			jsBundle( 'tinymce', 'admin', [
				'src/ts/isError.ts',
				'src/ts/admin/tinymce.ts',
			] ),
		() =>
			jsBundle( 'block', 'frontend', [
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
			] ),
		() =>
			jsBundle( 'shortcode', 'frontend', [
				'src/ts/isError.ts',
				'src/ts/frontend/shortcode/QueryParameter.ts',
				'src/ts/frontend/shortcode/Shortcode.ts',
				'src/ts/frontend/shortcode/ShortcodeRegistry.ts',
				'src/ts/frontend/shortcode.ts',
			] )
	)
);

gulp.task( 'build:php:admin', () =>
	gulp.src( [ 'src/php/admin/**/*.php' ] ).pipe( gulp.dest( 'dist/admin/' ) )
);

gulp.task( 'build:php:base', () =>
	gulp.src( [ 'src/php/*.php' ] ).pipe( gulp.dest( 'dist/' ) )
);

gulp.task( 'build:php:bundled', () =>
	gulp.src( [ 'src/php/bundled/*.php' ] ).pipe( gulp.dest( 'dist/bundled/' ) )
);

gulp.task( 'build:php:exceptions', () =>
	gulp
		.src( [ 'src/php/exceptions/**/*.php' ] )
		.pipe( gulp.dest( 'dist/exceptions/' ) )
);

gulp.task( 'build:php:frontend', () =>
	gulp
		.src( [ 'src/php/frontend/**/*.php' ] )
		.pipe( gulp.dest( 'dist/frontend/' ) )
);

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

gulp.task( 'build:png', () =>
	gulp.src( [ 'src/png/icon.png' ] ).pipe( gulp.dest( 'dist/admin/' ) )
);

gulp.task( 'build:txt', () =>
	gulp.src( [ 'src/txt/*.txt' ] ).pipe( gulp.dest( 'dist/' ) )
);

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
