/**
 * DEPENDENCIES
 */
const gulp = require('gulp');
const config = require('./config');
const rename = require('gulp-rename');
const imagemin = require('gulp-imagemin');

const images = () => {
    return gulp.src(config.images.input)
        .pipe(imagemin())
        .pipe(rename((path) => {
            // для винды вот такой вид path.dirname.split('\\')[0]
            path.dirname = path.dirname.split('/')[0];
        }))
        .pipe(gulp.dest(config.images.output));
};

images.displayName = 'compress images';
images.description = 'Minify images';

module.exports = images;
