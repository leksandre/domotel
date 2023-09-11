import MenuTree from '../components/Menu/MenuTree.vue';
import Vue from 'vue';

export default class extends window.Controller {
    connect() {
        this.init();
    }

    init() {
        const el = this.element;
        const allowAttr = ['data-translates', 'data-content', 'data-route-list', 'data-route-url', 'data-modal'];
        const elAttributes = {};

        el.attributes.forEach((attr) => {
            if (allowAttr.includes(attr.name)) {
                const paramName = attr.name.replace(/data-/g, '');

                elAttributes[paramName] = attr.nodeValue;
            }
        });

        elAttributes['modal'] = `screen-modal-${elAttributes['modal'] || ''}`;

        this.tree = new Vue({
            el,
            render: (h) => {
                return h(MenuTree, {
                    attrs: elAttributes
                });
            }
        });
    }
}
