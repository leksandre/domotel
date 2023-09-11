export default class extends window.Controller {

    translates = {};

    static get targets() {
        return [
            'complex',
            'building',
            'section',
            'floor',
            'type',
            'plan',
            'searchPlan',
            'floorPlan',
            'button',
            'result'
        ];
    }

    connect() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.translates = JSON.parse(this.buttonTarget.dataset.translates ?? '{}');
    }

    bindEvents() {
        this.complexTarget.addEventListener('change', () => {
            const that = this;

            this.clearSelect(this.buildingTarget);
            this.clearSelect(this.sectionTarget);
            this.clearSelect(this.floorTarget);
            this.loadData(
                this.complexTarget.dataset.action,
                {
                    cid: this.complexTarget.value
                },
                (res) => {
                    that.fillSelect(that.buildingTarget, res.buildings ?? {});
                }
            );
        });

        this.buildingTarget.addEventListener('change', () => {
            const that = this;

            this.clearSelect(this.sectionTarget);
            this.clearSelect(this.floorTarget);
            this.loadData(
                this.buildingTarget.dataset.action,
                {
                    bid: this.buildingTarget.value
                },
                (res) => {
                    that.fillSelect(that.sectionTarget, res.sections ?? {});
                    that.fillSelect(that.floorTarget, res.floors ?? {});
                }
            );
        });

        this.sectionTarget.addEventListener('change', (el) => {
            if (el.target.value === '0') {
                this.buildingTarget.dispatchEvent(new Event('change'));

                return;
            }

            const that = this;

            this.clearSelect(this.floorTarget);
            this.loadData(
                this.sectionTarget.dataset.action,
                {
                    sid: this.sectionTarget.value
                },
                (res) => {
                    that.fillSelect(that.floorTarget, res.floors ?? {});
                }
            );
        });
    }

    has(obj, key) {
        return Object.prototype.hasOwnProperty.call(obj, key);
    }

    getFloorValues() {
        return this.getSelectionValues(this.floorTarget);
    }

    getSelectionValues(select) {
        const res = [];

        Array.from(select.options).forEach((el) => {
            if (el.selected) {
                res.push(el.value);
            }
        });

        return res;
    }

    getFiles() {
        const files = {
            plan      : [],
            searchPlan: [],
            floorPlan : []
        };

        for (const key in files) {
            if (Object.hasOwnProperty.call(files, key)) {
                files[key] = Array.from(this[`${key}Target`].files);
            }
        }

        return files;
    }

    floorSelected() {
        return this.getFloorValues().length;
    }

    fileAdded() {
        let cnt = 0;
        const files = this.getFiles();

        for (const key in files) {
            if (this.has(files, key)) {
                cnt = cnt + files[key].length;
            }
        }

        return cnt > 0;
    }

    clearSelect(select) {
        Array.from(select.querySelectorAll('option')).forEach((el) => {
            if (el.value !== '0') {
                el.remove();
            }
        });
    }

    disableSelect(select) {
        select.attr('disabled', 'disabled');
    }

    enableSelect(select) {
        select.removeAttribute('disabled');
    }

    disableButton() {
        this.buttonTarget.disabled = 'disabled';
    }

    enableButton() {
        this.buttonTarget.disabled = false;
    }

    clearFiles() {
        Array.from(this.buttonTarget.closest('form').querySelectorAll('input[type=file]')).forEach((el) => {
            el.value = '';
        });
    }

    fillSelect(select, variants) {
        this.clearSelect(select);
        variants.forEach((el) => {
            select.append(new Option(el.title, el.id));
        });
        this.enableSelect(select);
    }

    showTable() {
        this.resultTarget.classList.remove('d-none');
    }

    hideTable() {
        this.resultTarget.classList.add('d-none');
    }

    clearTable() {
        Array.from(this.resultTarget.querySelectorAll('tbody>tr')).forEach((el) => el.remove());
    }

    loadData(action, data, callback) {
        window.axios.post(action, data)
            .then((res)=> {
                if (!res.request.status) {
                    if (res.request.errors.lenght) {
                        alert(res.request.errors.join('\n'));
                    }
                    return false;
                }
                callback(res.data.data);
            })
            .catch((error) => {
                console.log(error);
                this.alert(this.translates.requestError, error);
            });
    }

    async submit() {
        if (!this.floorSelected()) {
            this.alert(this.translates.error, this.translates.floorError, 'danger');

            return false;
        }

        if (!this.fileAdded()) {
            this.alert(this.translates.error, this.translates.fileError, 'danger');

            return false;
        }

        const files = this.getFiles();
        const floors = this.getFloorValues();
        const type = this.typeTarget.closest('div').querySelector('input:checked').value;

        this.disableButton();
        this.hideTable();
        this.clearTable();

        for (const key in files) {
            if (!this.has(files, key) || !files[key].length) {
                continue;
            }

            for (const fileKey in files[key]) {
                const file = files[key][fileKey];
                const form = new FormData();

                form.append('type', type);
                form.append('field', key);
                form.append('section', this.sectionTarget.value);
                floors.forEach((floorId) => form.append('floors[]', floorId));
                form.append('file', file);

                const cells = [`<td>${this.translates[key]}</td>`, `<td>${file.name}</td>`];

                await window.axios({
                    method : 'post',
                    url    : this.buttonTarget.dataset.formAction,
                    data   : form,
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                    .then((res) => {
                        if (!res.data.success) {
                            cells.push(`<td>${res.data.messages.join('<br/>')}</td>`);

                            return;
                        }
                        if (typeof res.data.data.updated !== 'undefined') {
                            cells.push(`<td>${res.data.data.updated}</td>`);

                            return;
                        }
                        cells.push('<td>-</td>');
                    })
                    .catch((error) => {
                        cells.push(`<td>${this.translates.requestError}: ${error.message}</td>`);
                        this.alert(this.translates.requestError, error.message);
                    })
                    .finally(() => {
                        const tbody = document.createElement('tbody');

                        tbody.innerHTML = `<tr>${cells.join('')}</tr>`;
                        this.resultTarget.querySelector('table').append(tbody);
                        this.showTable();
                    });
            }
        }

        this.clearFiles();
        this.enableButton();

        return false;
    }
}
