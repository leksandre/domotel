const sourceBase = 'frontend/src';
const buildBase = 'public';

module.exports = {
    server  : {watch: `${buildBase}`},
    mixBuild: {
        output: [`${buildBase}/js/common/app.js`,
            `${buildBase}/js/chunks/*.js`,
            `${buildBase}/css/common/styles.css`,
            `${buildBase}/css/pdf/pdf.css`]
    },
    proxy: [{
        url   : '/tests',
        target: 'http://localhost'
    }],
    styles: {
        base      : `${sourceBase}/common/styles/styles.scss`,
        linterBase: [`${sourceBase}/**/*.scss`, `${sourceBase}/**/**/*.scss`, `${sourceBase}/**/**/**/*.scss`],
        input     : `${sourceBase}/**/*.scss`,
        output    : `${buildBase}/css/common`
    },
    pages: {
        input: `${sourceBase}/pages/**/*.twig`,
        watch:
            [`${sourceBase}/pages/**/*.twig`, `${sourceBase}/components/**/*.twig`, `${sourceBase}/sections/**/*.twig`],
        output: `${buildBase}/pages`
    },
    images: {
        input:
        // eslint-disable-next-line max-len
            [`${sourceBase}/components/**/*.{jpg,png,jpeg,webp}`, `${sourceBase}/sections/**/*.{jpg,png,jpeg,webp}`, `${sourceBase}/pages/**/*.{jpg,png,jpeg,webp}`],
        output: `${buildBase}/images`
    },
    svg: {
        input : [`${sourceBase}/components/**/*.svg`, `${sourceBase}/sections/**/*.svg`],
        output: `${buildBase}/webicons`
    },
    // eslint-disable-next-line no-process-env
    NODE_ENV    : process.env.NODE_ENV || 'development',
    // eslint-disable-next-line no-invalid-this
    isProduction: this.NODE_ENV === 'production'
};
