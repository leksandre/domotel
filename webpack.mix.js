const mix = require('laravel-mix');
const ESLintPlugin = require('eslint-webpack-plugin');
const StylelintPlugin = require('stylelint-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const webpack = require('webpack');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const path = require('path');
const prod = process.env.NODE_ENV === 'production';

mix.setPublicPath('public')
    .ts('frontend/src/common/scripts/app.ts', 'js/common/app.js')
    .sass('frontend/src/common/styles/styles.scss', 'css/common/styles.css')
    .sass('frontend/src/pages/pdf/pdf.scss', 'css/pdf/pdf.css')
    .sourceMaps()
    .version()
    .webpackConfig({
        resolve: {
            extensions: ['.ts', '.tsx', '.js'],
            alias     : {
                '@': path.resolve(__dirname, './frontend/src')
            }
        },
        module: {
            rules: [{
                test   : /\.tsx?$/,
                loader : 'ts-loader',
                exclude: /node_modules/
            }, {
                test: /\.twig$/,
                use : {
                    loader: 'twig-loader'
                }
            }, {
                test: /\.s[ac]ss$/i,
                use : [{
                    loader: 'sass-loader'
                }]
            }]
        },
        // eslint-disable-next-line array-bracket-newline
        plugins: [
            new ESLintPlugin({
                files     : 'frontend/src/',
                extensions: ['js', 'ts'],
                fix       : true
            }), new StylelintPlugin({
                files     : 'frontend/src/',
                extensions: ['scss']
            }), new TerserPlugin(), new CleanWebpackPlugin({
                cleanOnceBeforeBuildPatterns: ['js/chunks/*']
            })],
        output: {
            publicPath   : '/',
            chunkFilename: prod ? 'js/chunks/[name].[contenthash].js' : 'js/chunks/[name].js'
        },
        optimization: {
            splitChunks: {
                chunks: 'all'
            }
        }
    });
