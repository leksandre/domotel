/**
 * DEPENDENCIES
 */
const gulp = require('gulp');
const config = require('./config');
const twig = require('gulp-twig');
const plumber = require('gulp-plumber');
const typograf = require('gulp-typograf');
const beautify = require('gulp-html-beautify');

/**
 * PAGES
 * @returns {*} - task
 */
const pages = () => {
    return gulp.src(config.pages.input)
        .pipe(plumber({
            errorHandler(err) {
                // eslint-disable-next-line no-console
                console.log(err);
                this.emit('end');
            }
        }))
        .pipe(twig())
        .pipe(beautify({indentSize: 4}))
        .pipe(typograf({
            locale     : ['ru'],
            disableRule: ['ru/nbsp/centuries', 'common/number/fraction']
        }))
        .pipe(gulp.dest(config.pages.output));
};

pages.displayName = 'Pages';
pages.description = 'Convert twig on html';

module.exports = pages;
