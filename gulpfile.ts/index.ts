/* eslint-env node */

import './build';
import './lint';
import './unit';

const gulp = require( 'gulp' );

// Default command

gulp.task( 'default', gulp.series( 'build', 'unit', 'lint' ) );
