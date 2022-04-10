const { src, dest, watch, series } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const minify = require('gulp-clean-css');
const autoprefixer = require('gulp-autoprefixer');
const rename = require("gulp-rename");

/**
 * Compiling SASS to css
 */

function buildSytle() {
    return src('scss/**/*.scss')
    .pipe(sass())
    .pipe(autoprefixer({
        browsers: ['last 2 versions'],
        cascade: false
    }))
    .pipe(dest('css/unminified'))
}

// Minified CSS
function minifyCssfile(){
    return src('css/unminified/*.css')
    .pipe(minify())
    .pipe(autoprefixer({
        browsers: ['last 2 versions'],
        cascade: false
    }))
    .pipe(rename({suffix:'.min'}))
    .pipe(dest('css/minified'))
}

function watchTask(){
    watch('scss/**/*.scss', buildSytle)
}
exports.default = series(
    buildSytle,
    minifyCssfile,
    watchTask
)
