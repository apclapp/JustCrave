var gulp = require('gulp');
var sass = require('gulp-sass');
var bro = require('gulp-bro');
var reactify = require('reactify');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var fs = require('fs');
var mysql      = require('mysql');
var util = require('util');
var Promise = require('bluebird');
var exec = Promise.promisify(require('child_process').exec);

var dbConfig = {
  host     : 'localhost',
  user     : 'root',
  password : 'root',
  database : 'justcrave',
  multipleStatements: true
};

var connection = mysql.createConnection(dbConfig);
var query = Promise.promisify(connection.query, {context: connection});


gulp.task('build', function() {
    return gulp.src('./web/main.jsx')
        .pipe(bro({
            transform: [reactify]
        }))
        .pipe(uglify())
        .pipe(rename("bundle.js"))
        .pipe(gulp.dest('./public'));
});

gulp.task('sass', function () {
  return gulp.src('./scss/**/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./public/css'));
});

gulp.task('debugbuild', function() {
    return gulp.src('./web/main.jsx')
        .pipe(bro({
            transform: [reactify]
        }))
        .pipe(rename("bundle.js"))
        .pipe(gulp.dest('./public'));
});

gulp.task('default', ['debugbuild', 'sass'], function() {
    gulp.watch('web/**/*', ['debugbuild']);
    gulp.watch('scss/**/*.scss', ['sass']);
});


gulp.task('updatedb', function(callback) {
    var patchnames = fs.readdirSync('./sql/patches');
    var total = patchnames.length;

    var promise = query('SELECT * FROM patches;').then(function(rows) {
        patchnames = patchnames.filter(function(patchname) {
            for(var i in rows) {
                if(rows[i].patchname == patchname) return false;
            }
            return true;
        });
    });

    promise.then(function() {
        console.log(util.format('\n    %s/%s patches already installed.\n', total-patchnames.length, total));

        for(var i in patchnames){
            promise = promise.then(function(patchname) {
                return function(){
                    console.log(util.format('    Running patch %s...', patchname));
                    return exec('mysql -uroot -proot justcrave < ./sql/patches/' + patchname)
                        .then(query('INSERT INTO patches VALUES (\'' + patchname + '\');'));
                }
            }(patchnames[i]));
        }

        promise.finally(function() {
            
            console.log(util.format('    Complete.\n'));

            connection.end();
            callback();
        });
    });
});