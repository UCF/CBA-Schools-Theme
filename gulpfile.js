var gulp = require('gulp'),
    sass = require('gulp-sass'),
    minifyCss = require('gulp-minify-css'),
    bless = require('gulp-bless'),
    notify = require('gulp-notify'),
    bower = require('gulp-bower'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    jshint = require('gulp-jshint'),
    jshintStylish = require('jshint-stylish'),
    scsslint = require('gulp-scss-lint'),
    vinylPaths = require('vinyl-paths'),
    browserSync = require('browser-sync').create(),
    reload = browserSync.reload;

var config = {
  sassPath: './static/scss',
  cssPath: './static/css',
  jsPath: './static/js',
  fontPath: './static/fonts',
  phpPath: './',
  bowerDir: './static/bower_components',
  sync: false,
};


// Run Bower
gulp.task('bower', function() {
  return bower()
    .pipe(gulp.dest(config.bowerDir))
    .on('end', function() {

      // Add Glyphicons to fonts dir
      gulp.src(config.bowerDir + '/bootstrap-sass-official/assets/fonts/*/*')
        .pipe(gulp.dest(config.fontPath));

      gulp.src(config.bowerDir + '/font-awesome/fonts/*/*')
        .pipe(gulp.dest(config.fontPath));

    });
});


// Process .scss files in /static/scss/
gulp.task('css', function() {
  return gulp.src(config.sassPath + '/*.scss')
    .pipe(scsslint())
    .pipe(sass().on('error', sass.logError))
    .pipe(minifyCss({compatibility: 'ie8'}))
    .pipe(rename('style.min.css'))
    .pipe(bless())
    .pipe(gulp.dest(config.cssPath))
    .pipe(browserSync.stream());
});

// Lint, concat and uglify js files.
gulp.task('js', function() {

  // Run jshint on all js files in jsPath (except already minified files.)
  return gulp.src([config.jsPath + '/*.js', '!' + config.jsPath + '/*.min.js'])
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'))
    .pipe(jshint.reporter('fail'))
    .on('end', function() {

      // Combine and uglify js files to create script.min.js.
      var minified = [
        config.bowerDir + '/bootstrap-sass-official/assets/javascripts/bootstrap.js',
        config.bowerDir + '/jquery.dotdotdot/src/js/jquery.dotdotdot.js',
        config.bowerDir + '/jquery-placeholder/jquery.placeholder.js',
        config.jsPath + '/webcom-base.js',
        config.jsPath + '/generic-base.js',
        config.jsPath + '/script.js'
      ];

      gulp.src(minified)
        .pipe(concat('script.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest(config.jsPath));

    });
});


// Rerun tasks when files change
gulp.task('watch', function() {
  if (config.sync) {
    browserSync.init({
        proxy: {
          target: "localhost/devos"
        }
    });
  }
  gulp.watch(config.jsPath + '/*.js', ['js']).on('change', reload);
  gulp.watch(config.phpPath + '/*.php').on('change', reload);
  gulp.watch(config.phpPath + '/*.php');

  gulp.watch(config.sassPath + '/*.scss', ['css']);
  gulp.watch(config.jsPath + '/*.js', ['js']);
});


// Default task
gulp.task('default', ['bower', 'css', 'js']);