const path = require('path');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const StylelintPlugin = require('stylelint-webpack-plugin');
const webpack = require('webpack');
const Config = require('webpack-chain');

// eslint-disable-next-line
const config = new Config();

module.exports = {
    outputDir: '../../public/parametric/',
    publicPath: process.env.NODE_ENV === 'production' ? '/parametric/' : '/',

    pages: {
        main: {
            entry   : 'src/main.ts',
            template: 'src/index.twig',
            filename: 'index.html'
        }
    },

    devServer: {
        host       : 'localhost',
        port       : 3000,
        static     : {
            directory: path.join(__dirname, '../../public')
        },
        proxy      : {
            '/tests': {
                target: 'http://localhost/',
                secure: false
            },
            '/images': {
                target: 'http://localhost/',
                secure: false
            },
            '/webicons': {
                target: 'http://localhost/',
                secure: false
            }
        }
    },

    chainWebpack: (config) => {
        if (process.env.NODE_ENV === 'production') {
            config
                .plugin('CleanWebpackPlugin')
                .use(CleanWebpackPlugin, [{
                    cleanOnceBeforeBuildPatterns: ['js', 'css', 'img']
                }]);
        }

        config.module
            .rule('twig')
            .test(/\.twig$/)
            .use('twig-loader')
            .loader('twig-loader')
            .end();

        config.module
            .rule('ts')
            .test(/\.tsx?$/)
            .use('ts-loader')
            .loader('ts-loader')
            .tap((options) => {
                Object.assign(options || {}, {
                    appendTsSuffixTo: [/\.vue$/],
                    transpileOnly   : true
                });

                return options;
            })
            .end();
    },

    configureWebpack: {
        resolve: {
            extensions: ['.ts', '.vue', '.js'],
            alias: {
                '@': path.resolve(__dirname, '../src/')
            }
        },
        plugins: [
            new StylelintPlugin({
                files: ['src/components/']
            })
        ]
    },

    css: {
        loaderOptions         : {
            css: {
                modules: {
                    auto: () => false
                }
            },
            sass: {
                additionalData: '@import "src/styles/base.scss";'
            }
        }
    },

    pluginOptions      : {},
    lintOnSave         : 'error',
    productionSourceMap: false,
    runtimeCompiler    : true,
    parallel           : false
};
