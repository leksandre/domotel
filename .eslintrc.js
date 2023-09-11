module.exports = {
    root: true,
    env : {
        node: true
    },
    extends      : ['./.eslintrc'],
    parserOptions: {
        parser: '@babel/eslint-parser'
    },
    rules: {
        // eslint-disable-next-line no-process-env
        'no-console' : process.env.NODE_ENV === 'production' ? 'error' : 'warn',
        // eslint-disable-next-line no-process-env
        'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'warn'
    }
};
