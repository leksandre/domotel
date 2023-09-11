declare module 'yall-js';

declare module '*.twig' {
    import type {Template} from 'twig';

    const template: Template;
    export = template;
}
