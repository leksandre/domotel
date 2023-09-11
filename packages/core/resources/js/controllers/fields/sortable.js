function _defineProperty(obj, key, value) {
    if (key in obj) {
        Object.defineProperty(obj, key, {value,
            enumerable  : true,
            configurable: true,
            writable    : true});
    } else {
        obj[key] = value;
    }

    return obj;
}

import Sortable from 'sortablejs';

export default class _class extends window.Controller {
    initialize() {
        this.end = this.end.bind(this);
    }

    connect() {
        this.sortable = new Sortable(this.element, {...this.defaultOptions,
            ...this.options});
    }

    disconnect() {
        this.sortable.destroy();
        this.sortable = null;
    }

    end({item, newIndex}) {
        const apiUrl = this.data.get('url') || null;

        if (!apiUrl) {
            return;
        }

        const data = new FormData();

        this.sortable.el.children.forEach((child) => {
            return data.append('elements[]', child.dataset.sort);
        });
        data.append('_method', 'PATCH');

        axios.post(apiUrl, data)
            .then((resp) => {
                const res = resp.data;

                if (typeof res.messages !== 'undefined') {
                    this.alert(res.messages.shift(), '', 'info');
                }
            })
            .catch((error) => {
                this.alert('Request error', error);
            });
    }

    get options() {
        return {
            animation: this.animationValue || this.defaultOptions.animation || 150,
            handle   : this.handleValue || this.defaultOptions.handle || null,
            onEnd    : this.end
        };
    }

    get defaultOptions() {
        return {};
    }
}

_defineProperty(_class, 'values', {
    resourceName: String,
    paramName   : String,
    animation   : Number,
    handle      : String
});
