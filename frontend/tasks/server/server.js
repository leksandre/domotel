/**
 * DEPENDENCIES
 */
const browserSync = require('browser-sync').create();
const config = require('../config');
const {createProxyMiddleware} = require('http-proxy-middleware');

/**
 * SERVER
 * @param {Function} done - callback
 */
const server = (done) => {
    const configMiddleware = config.proxy.map((proxy) => {
        return createProxyMiddleware(proxy.url, {
            target      : proxy.target,
            changeOrigin: true,
            logLevel    : 'debug'
        });
    });

    browserSync.init({
        server: {
            baseDir   : config.server.watch,
            middleware: configMiddleware
        },
        port  : 8080,
        open  : false,
        notify: false
    });
    done();
};

const reload = async() => {
    await browserSync.reload();
};

server.displayName = 'Server';
server.description = 'Create local server localhost:8080';

module.exports = {
    browserSync,
    reload,
    server
};
