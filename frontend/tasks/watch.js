/**
 * DEPENDENCIES
 */
const reload = require('./server/server').reload;
const gulp = require('gulp');
const config = require('./config');
const pages = require('./pages');
const lint = require('./lint');

/**
 * WATCH
 */
const watch = () => {
    gulp.watch(config.pages.watch, gulp.series(pages, reload));
    gulp.watch(config.mixBuild.output).on('change', reload);
    gulp.watch(config.styles.input, gulp.series(lint));
};

watch.displayName = 'Watch';
watch.description = 'Watch dev files';

module.exports = watch;
