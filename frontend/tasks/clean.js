/**
 * DEPENDENCIES
 */
const config = require('./config');
const del = require('del');

/**
 * CLEAN
 */
const clean = () => {
    return del([config.styles.output]);
};

const cleanImages = () => {
    return del([config.images.output]);
};

clean.displayName = 'Clean';
clean.description = 'Clean page files';

module.exports = {
    clean,
    cleanImages
};
