const mix = require('laravel-mix');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');

mix.setPublicPath('../../public/vendor/kelnik-page')
    .js('resources/js/app.js', 'js/app.js')
    .sass('resources/sass/app.scss', 'css/app.css')
    .sourceMaps()
    .version()
    .webpackConfig({
        module: {
            rules: [{
                test: /\.s[ac]ss$/i,
                use : [{
                    loader : 'sass-loader',
                    options: {
                        additionalData: `@import "../../frontend/src/common/styles/mixins";`
                    }
                }]
            }]
        },
        plugins: [new CleanWebpackPlugin({
            cleanOnceBeforeBuildPatterns: ['css/*', 'js/*']
        })]
    })
    .extract(['axios', 'lodash']);
