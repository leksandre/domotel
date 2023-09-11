module.exports = {
    root     : true,
    extends  : ['../../.stylelintrc'],
    overrides: [{
        files       : ['*.scss', '**/*.scss'],
        customSyntax: 'postcss-scss'
    }]
};
