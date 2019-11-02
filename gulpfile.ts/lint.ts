/* eslint-env node */

export {};

const gulp = require( 'gulp' );

const eslint = require( 'gulp-eslint' );
const shell = require( 'gulp-shell' );
const stylelint = require( 'gulp-stylelint' );

// Lints

gulp.task( 'lint:phpcs', shell.task( [ 'vendor/bin/phpcs' ] ) );

gulp.task( 'lint:phpmd', shell.task( [ 'vendor/bin/phpmd src,tests text phpmd.xml' ] ) );

gulp.task( 'lint:phan', shell.task( [ 'export PHAN_DISABLE_XDEBUG_WARN=1;vendor/bin/phan' ] ) );

gulp.task( 'lint:eslint', function() {
	return gulp.src( [ 'src/**/*.ts', 'gulpfile.js/*.js' ] )
		.pipe( eslint() )
		.pipe( eslint.format() )
		.pipe( eslint.failAfterError() );
} );

gulp.task( 'lint:stylelint', function() {
	return gulp.src( [ 'src/**/*.css' ] )
		.pipe( stylelint( {
			failAfterError: true,
			reporters: [
				{ formatter: 'string', console: true },
			],
		} ) );
} );

//gulp.task( 'lint', gulp.series( 'phpcs', 'phpmd', 'phan', 'eslint', 'stylelint' ) );
gulp.task( 'lint', gulp.series( 'lint:phpcs', 'lint:phpmd', 'lint:phan', 'lint:eslint' ) );
