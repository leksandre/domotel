/**
 * DEPENDENCIES
 */
const gulp = require('gulp');
const config = require('./config');
const svgo = require('gulp-svgo');
const rename = require('gulp-rename');

const svg = () => {
    return gulp.src(config.svg.input)
        .pipe(svgo({
            plugins: [{
                removeStyleElement: true
            }]
        }))
        .pipe(rename((path) => {
            // для винды вот такой вид path.dirname.split('\\')[0]
            path.dirname = path.dirname.split('/')[0];
        }))
        .pipe(gulp.dest(config.svg.output));
};

svg.displayName = 'Svg sprite';
svg.description = 'Create svg sprite';

module.exports = svg;
