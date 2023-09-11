import type {VNode} from 'vue';
import type Vue from 'vue';

declare global {
    namespace JSX {
        type Element = VNode
        type ElementClass = Vue
        type IntrinsicElements = Record<string, any>;
    }
}
