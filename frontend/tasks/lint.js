/**
 * DEPENDENCIES
 */
const config = require('./config');
const gulp = require('gulp');
const gulpStylelint = require('gulp-stylelint');
const notify = require('gulp-notify');
const sass = require('gulp-sass')(require('sass'));

/**
 * STYLES
 * @returns {*} - task
 */
const lint = () => {
    return gulp.src(config.styles.linterBase)
        .pipe(gulpStylelint({
            failAfterError: true,
            reporters     : [{
                formatter: 'string',
                console  : true
            }]
        }))
        .pipe(sass().on('error', (error) => {
            return notify().write(error);
        }));
};

lint.displayName = 'Style linter base';
lint.description = 'Lint base styles';

module.exports = lint;
