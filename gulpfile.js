var gulp = require('gulp');
var bro = require('gulp-bro');
var reactify = require('reactify');
var uglifyify = require('uglifyify');
var rename = require('gulp-rename');
var fs = require('fs');
var mysql      = require('mysql');
var localconfig = require('./config.json');
var util = require('util');
var gulpUtil = require('gulp-util');

var connection = mysql.createConnection({
  host     : 'localhost',
  user     : 'root',
  password : 'root',
  database : 'justcrave'
});

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



/* Database stuff */

gulp.task('db', ['db:checkversion'], function() {
    connection.end();
});

gulp.task('db++', ['db:incrementversion'], function() {
    connection.end();
});

gulp.task('db:connect', function(callback) {
    connection.connect();
    callback();
});

gulp.task('db:checkversion', ['db:connect'], function(callback) {
    console.log('\nGetting version info...\n');

    connection.query('SELECT * FROM version', function(err, rows, fields) {
        if (err) throw err;

        console.log(util.format('--\nDatabase version:  %s\nCode version:      %s\n\nUp to date:        %s\n',
            rows[0].dbversion,
            localconfig.dbversion,
            (rows[0].dbversion == localconfig.dbversion).toString().toUpperCase()
        ));

        if(rows[0].dbversion != localconfig.dbversion) {
            throw new gulpUtil.PluginError({
                plugin: 'db',
                message: 'database is out of sync with code.'
            });
        }

        callback();
    });
});

gulp.task('db:incrementversion', ['db:connect', 'db:checkversion'], function(callback) {

    console.log('\nIncrementing database version...\n');

    connection.query('UPDATE version SET dbversion = dbversion + 1', function(err, rows, fields) {
      if (err) throw err;
        console.log('success, incrementing code version...');
        localconfig.dbversion++;
        fs.writeFileSync('./config.json', JSON.stringify(localconfig));
        console.log(util.format('\nNew Versions\n--\nDatabase version:  %s\nCode version:      %s\n\n',
            localconfig.dbversion,
            localconfig.dbversion
        ));

        callback();
    });
});