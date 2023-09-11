'use strict';

/**
 * DEPENDENCIES
 */
const gulp = require('gulp');
const clean = require('./frontend/tasks/clean').clean;
const cleanImages = require('./frontend/tasks/clean').cleanImages;
const images = require('./frontend/tasks/images');
const pages = require('./frontend/tasks/pages');
const server = require('./frontend/tasks/server/server').server;
const svg = require('./frontend/tasks/svg');
const watch = require('./frontend/tasks/watch');

/**
 * TASKS
 */
gulp.task('images', gulp.series(cleanImages, images));

gulp.task('icons', gulp.series(svg));

gulp.task('build', gulp.series(clean, pages));

gulp.task('dev', gulp.series(server, watch));
