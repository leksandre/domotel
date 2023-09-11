const mix = require('laravel-mix');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');

mix.setPublicPath('../../public/vendor/kelnik-estate-import')
    .js('resources/js/app.js', 'js/app.js')
    .sass('resources/sass/app.scss', 'css/app.css')
    .sourceMaps()
    .version()
    .webpackConfig({
        plugins: [new CleanWebpackPlugin({
            cleanOnceBeforeBuildPatterns: ['css/*', 'js/*']
        })]
    })
    .extract(['axios', 'lodash']);
