module.exports = {
    root: true,

    env: {
        node: true
    },

    extends: ['plugin:vue/essential', 'eslint:recommended', '../../.eslintrc', '@vue/typescript'],

    parserOptions: {
        parser: '@typescript-eslint/parser'
    },

    rules: {
        'no-console'           : process.env.NODE_ENV === 'production' ? 'error' : 0,
        'no-debugger'          : process.env.NODE_ENV === 'production' ? 'error' : 'warn',
        'vue/no-mutating-props': 'off'
    }
};
