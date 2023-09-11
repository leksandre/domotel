import EstateVisual from '../components/EstateVisual/EstateVisual.vue';
import Vue from 'vue';

export default class extends window.Controller {
    connect() {
        this.init();
    }

    init() {
        const el = this.element;
        const allowAttr = ['data-translates', 'data-url', 'data-storage-url', 'data-storage-disk', 'data-groups'];
        const elAttributes = {};

        el.attributes.forEach((attr) => {
            if (allowAttr.includes(attr.name)) {
                const paramName = attr.name.replace(/data-/g, '');

                elAttributes[paramName] = attr.nodeValue;
            }
        });

        elAttributes['stimulus'] = this;

        this.estateVisual = new Vue({
            el,
            render: (h) => {
                return h(EstateVisual, {
                    attrs: elAttributes
                });
            }
        });
    }
}
