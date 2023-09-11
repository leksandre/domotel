import ColorPicker from '../../components/ColorPicker/ColorPicker.vue';
import Vue from 'vue';

export default class extends window.Controller {
    static get targets() {
        return ['name'];
    }

    connect() {
        this.init();
    }

    init() {
        const el = this.element;
        const allowAttr = ['title', 'label', 'name', 'data-color', 'data-default'];
        const elAttributes = {};

        el.attributes.forEach((attr) => {
            if (allowAttr.includes(attr.name)) {
                let paramName = attr.name.replace(/data-/g, '');

                if (paramName === 'default') {
                    paramName = 'colorDefault';
                }
                elAttributes[paramName] = attr.nodeValue;
            }
        });
        new Vue({
            el,
            render: (h) => {
                return h(ColorPicker, {
                    attrs: elAttributes
                });
            }
        });
    }
}
