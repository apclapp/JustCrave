var gulp = require('gulp');
var bro = require('gulp-bro');
var reactify = require('reactify');
var uglifyify = require('uglifyify');
var rename = require('gulp-rename');

gulp.task('build', function() {
    return gulp.src('./web/main.jsx')
        .pipe(bro({
            transform: [reactify, uglifyify]
        }))
        .pipe(rename("bundle.js"))
        .pipe(gulp.dest('./public'));
});

gulp.task('debugbuild', function() {
    return gulp.src('./web/main.jsx')
        .pipe(bro({
            transform: [reactify]
        }))
        .pipe(rename("bundle.js"))
        .pipe(gulp.dest('./public'));
});

gulp.task('default', ['debugbuild'], function() {
    gulp.watch('web/**/*', ['debugbuild']);
});