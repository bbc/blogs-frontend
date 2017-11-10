'use strict';

const gulp = require('gulp');
const sass = require('gulp-sass');
const { gulpSassError } = require('gulp-sass-error');
const del = require('del');
const autoprefixer = require('gulp-autoprefixer');

const staticPathSrc = 'resources/assets';
const staticPathDist = 'web/assets';
const sassMatch = '/sass/**/*.scss';

var throwError = true;

// ------

gulp.task('sass:clean', function() {
    return del([staticPathDist + '/css']);
});

gulp.task('sass', ['sass:clean'], function() {
    return gulp.src(staticPathSrc + sassMatch)
        .pipe(sass({
            outputStyle: 'compressed',
            precision: 8,
            includePaths: ['src', 'node_modules']
        }).on('error', gulpSassError(throwError)))
        .pipe(autoprefixer({
            browsers: ['last 3 versions'], cascade: false, remove: false
        }))
        .pipe(gulp.dest(staticPathDist + '/css/'));
});

/*
 * Entry tasks
 */
gulp.task('watch',function() {
    // When watching we don't want to throw an error, because then we have to
    // go and restart the watch task if we ever write invalid sass, which would
    // be really annoying.
    throwError = false;

    gulp.watch([staticPathSrc + sassMatch], ['sass']);
});

gulp.task('default', ['sass']);

// TODO use gulp-rev to create versioned files
gulp.task('distribution', ['default']);
