export default {
    mounted() {
        this.modal = document.getElementById(this.$attrs.modal);
        this.modalForm = this.modal?.querySelector('form') || null;
    },

    destroyed() {
        if (!this.modalForm) {
            return;
        }

        this.modalForm.removeEventListener('submit', (event) => {
            return this.submitModalForm(event);
        });
    },

    methods: {
        bindModalEvents() {
            if (!this.modalForm) {
                return;
            }

            this.modalForm.addEventListener('submit', (event) => {
                return this.submitModalForm(event);
            });
            const that = this;
            const select = this.modalForm.querySelector('*[name="item[page_id]"]');

            if (!select || !Object.prototype.hasOwnProperty.call(select, 'tomselect')) {
                return;
            }

            select.tomselect.on('change', (val) => {
                const componentSelect = that.modalForm.querySelector('*[name="item[page_component_id]"]');

                if (!componentSelect) {
                    return;
                }

                that.rebuildPageComponentSelect(val || 0, 0, componentSelect);
            });
        },

        openModal() {
            window.Bootstrap.Modal.getOrCreateInstance(this.modal).show();
        },

        closeModal() {
            window.Bootstrap.Modal.getOrCreateInstance(this.modal).hide();
        },

        fillModal(item) {
            if (!this.modalForm) {
                return;
            }

            for (const key in this.modelParams) {
                if (Object.prototype.hasOwnProperty.call(this.modelParams, key)) {
                    const val = this.modelParams[key];

                    if (val?.ignore === true || val?.ignoreOnModal === true) {
                        continue;
                    }

                    const inp = this.modalForm.querySelector(`*[name="item[${key}]"]`);

                    if (typeof inp === 'undefined') {
                        continue;
                    }

                    const value = item[val.name] || '';

                    inp.value = value;

                    if (val?.isSelect) {
                        fillSelect(inp, value, val, key, item, this);
                        continue;
                    }

                    if (val?.isPicture && typeof window.application !== 'undefined') {
                        fillPicture(inp, value, val, key, item);
                    }
                }
            }

            this.modalForm.querySelector('input[name="item[active]"]').checked = item.active;
            this.modalForm.querySelector('input[name="item[marked]"]').checked = item.marked;

            function fillPicture(input, value, field, key, item) {
                const picture = window.application
                    .getControllerForElementAndIdentifier(input.closest('div[data-controller="picture"]'),
                        'picture');

                if (typeof picture === 'undefined') {
                    console.error('Picture controller not found');

                    return;
                }

                picture.clear();
                picture.element.dataset.pictureValue = value;
                picture.element.dataset.pictureUrl = field.ref ? item[field.ref] || '' : '';
                input.setAttribute('url', picture.element.dataset.pictureUrl);
                input.value = value;

                picture.connect();

                if (value) {
                    picture.element.querySelector('.picture-preview').classList.remove('none');
                    picture.element.querySelector('.picture-remove').classList.remove('none');
                }
            }

            function fillSelect(input, value, field, key, item, that) {
                if (key === 'page_id') {
                    input.value = value;
                    input.dispatchEvent(new Event('change'));

                    return;
                }

                that.rebuildPageComponentSelect(Number(item.page_id || 0), Number(item.page_component_id), input);
            }
        },

        rebuildPageComponentSelect(pageId, pageComponentId, input) {
            let defaultValueNode = null;

            while (input.firstChild) {
                if (input.firstChild.value === '0') {
                    defaultValueNode = input.firstChild;
                }
                input.removeChild(input.firstChild);
            }
            input.tomselect.clear();
            input.tomselect.clearOptions();
            input.tomselect.sync();

            this.loadData(this.$attrs['route-list'],
                {params: {pageId}},
                (resp) => {
                    let val = null;

                    if (defaultValueNode) {
                        input.append(defaultValueNode);
                    }

                    for (const key in resp.data) {
                        if (Object.prototype.hasOwnProperty.call(resp.data, key)) {
                            const selected = Number(resp.data[key].id) === pageComponentId;

                            input.append(new Option(resp.data[key].title,
                                resp.data[key].id,
                                false,
                                selected));

                            if (selected) {
                                val = Number(resp.data[key].id);
                            }

                            // input.dispatchEvent(new Event('change'));
                        }
                    }

                    if (!val && defaultValueNode) {
                        input.firstChild.selected = true;
                    }

                    input.tomselect.clear();
                    input.tomselect.clearCache();
                    input.tomselect.sync();
                });
        },

        async submitModalForm(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            const itemId = Number(formData.get('item[id]') || 0);

            if (!itemId) {
                return;
            }

            const node = this._findNode(itemId, this.data || {children: this.content});

            if (typeof node === 'object') {
                const data = {};

                for (const key in this.modelParams) {
                    if (Object.prototype.hasOwnProperty.call(this.modelParams, key)) {
                        const val = this.modelParams[key];

                        if (val?.ignore === true) {
                            continue;
                        }

                        const value = formData.get(`item[${key}]`) || '';

                        data[val.name] = val.type === 'int' ? Number(value) : value;
                    }
                }

                if (this.modalForm) {
                    data.active = this.modalForm.querySelector('input[name="item[active]"]')?.checked ?? false;
                    data.marked = this.modalForm.querySelector('input[name="item[marked]"]')?.checked ?? false;
                    data.icon_path = data.icon_image ?
                        this.modalForm.querySelector('.picture-preview')?.src ?? '' :
                        '';
                }
                data.url = data.link || '';

                if (data.page_id || data.page_component_id) {
                    await this.loadData(this.$attrs['route-url'],
                        {
                            params: {
                                pageId: data.page_id,
                                compId: data.page_component_id
                            }
                        },
                        (resp) => {
                            data.url = resp.data?.url || data.link || '';
                        });
                }

                this.fillNode(node, data);
            }

            this.closeModal();
        },

        fillNode(node, values) {
            Object.assign(node, values);
        }
    }
};
