/* eslint-env node */

export {};

const gulp = require( 'gulp' );

const shell = require( 'gulp-shell' );

// Unit tests

gulp.task( 'unit:phpunit', shell.task( [ 'vendor/bin/phpunit' ] ) );

gulp.task( 'unit', gulp.series( 'unit:phpunit' ) );
