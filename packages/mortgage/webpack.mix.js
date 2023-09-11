const mix = require('laravel-mix');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');

mix.setPublicPath('../../public/vendor/kelnik-mortgage')
    .js('resources/js/app.js', 'js/app.js')
    .sourceMaps()
    .version()
    .webpackConfig({
        plugins: [new CleanWebpackPlugin({
            cleanOnceBeforeBuildPatterns: ['js/*']
        })]
    })
    .extract(['axios', 'lodash']);
