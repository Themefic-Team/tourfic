// I don't feel like writing var everytime
var gulp = require("gulp"),
    sass = require("gulp-sass"),
    postcss = require("gulp-postcss"),
    autoprefixer = require("autoprefixer"),
    cssnano = require("cssnano"),
    sourcemaps = require("gulp-sourcemaps"),
    browsersync = require('browser-sync').create(),
    rename = require('gulp-rename');

// Scripts
//var jshint = require('gulp-jshint');
//var stylish = require('jshint-stylish');
//var concat = require('gulp-concat');
//var uglify = require('gulp-terser');
//var optimizejs = require('gulp-optimize-js');

// Put this after including our dependencies
var paths = {
    styles: {
        src: "src/scss/main.scss",
        dest: "assets/css",
    },
    scripts: {
      input: 'src/js/*',
      output: 'dist/js/',
    }
};

// Compile Styles
function style() {
    return (
      gulp.src(paths.styles.src)
      // Initialize sourcemaps before compilation starts
      .pipe(sourcemaps.init())
      .pipe(sass({ outputStyle: 'expanded' }))
      .on("error", sass.logError)
      .pipe(postcss([autoprefixer()]))
      .pipe(rename({ basename: "tourfic-styles" }))
      .pipe(gulp.dest(paths.styles.dest))
      .pipe(sass())
      .on("error", sass.logError)
      .pipe(postcss([autoprefixer(), cssnano()]))
      .pipe(rename({ basename: "tourfic-styles", suffix: '.min' }))
      .pipe(sourcemaps.write())
      .pipe(gulp.dest(paths.styles.dest))
    );
}

// Watching the file changes
function watch() {
  gulp.watch(
    ['src/scss/*.scss', 'src/scss/**/*.scss'],
    { events: 'all', ignoreInitial: false },
    gulp.series(style,function(done){
      browsersync.reload();
      done();
    })
  );

  gulp.watch(
    ['*.php', '**/*.php'],
    { events: 'all', ignoreInitial: false },
    gulp.series(style,function(done){
      browsersync.reload();
      done();
    })
  );
}


// Init BrowserSync.
function browserSync(done) {
  /*browsersync.init({
    proxy: 'http://localhost/isobar/', // Change this value to match your local URL.
    socket: {
      domain: 'localhost:3000'
    }
  });*/

  browsersync.init(null, {
    proxy : "http://localhost/wp/"
  });

  done();
}

exports.default = gulp.parallel(browserSync, watch); // $ gulp
exports.watch = watch;