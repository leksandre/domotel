export default class extends window.Controller {
    static get targets() {
        return [];
    }

    constructor(props) {
        super(props);

        this.modal = '';
        this.form = '';
    }

    connect() {
        this.modal = document.getElementById(`screen-modal-${this.data.get('modal')}`);
        this.form = this.modal?.querySelector('form');

        this.bindEvents();
    }

    bindEvents() {
        if (this.form) {
            this.form.addEventListener('submit', (event) => {
                return this.submitForm(event);
            });
        }
    }

    openModal(event) {
        event.preventDefault();
        this.fillModal(event.target.closest('a').dataset.key);

        window.Bootstrap.Modal.getOrCreateInstance(this.modal).show();
    }

    closeModal() {
        window.Bootstrap.Modal.getOrCreateInstance(this.modal).hide();
    }

    updateActive(event) {
        const isActive = event.target.checked || false;
        const inp = event.target.closest('tr').querySelector('input[data-field="active"]');

        if (typeof inp !== 'undefined') {
            inp.value = isActive ? 1 : 0;
        }
    }

    updateTitle(event) {
        const val = event.target.value;
        const inp = event.target.closest('tr').querySelector('input[data-field="title"]');

        if (typeof inp !== 'undefined') {
            inp.value = val;
        }
    }

    fillModal(key) {
        const data = this.getDataByIndex(key);

        data.active = parseInt(data.active);

        this.form.querySelector('input[name="variant[index]"]').value = key;
        this.form.querySelector('input[name="variant[title]"]').value = data.title;
        this.form.querySelector('input[name="variant[active]"]').checked = Boolean(data.active);
        this.form.querySelectorAll('input[name="variant[showBanks]"]')?.forEach((el) => {
            const label = el.closest('label');

            if (!label) {
                return;
            }

            [el, label].forEach((element) => {
                element.removeAttribute('checked');
                element.classList.remove('active');
            });

            el.checked = false;

            if (el.value !== data.showBanks) {
                return;
            }

            label.classList.add('active');
            label.setAttribute('checked', 'checked');
            label.dispatchEvent(new Event('change'));
            el.checked = true;
        });

        const textField = this.form.querySelector('input[name="variant[text]"]');
        const textController = textField.closest('div[data-controller="kelnik-quill"]');
        const quill = this.application.getControllerForElementAndIdentifier(textController, 'kelnik-quill');

        quill.editor.root.innerHTML = data.text;
    }

    fillMatrixRow(index, data) {
        const inp = this.element.querySelector(`input[data-key="${index}"][data-field="title"]`);

        if (typeof inp === 'undefined') {
            return;
        }

        const tr = inp.closest('tr');

        tr.querySelector('input[data-field="title_"]').value = data.title;
        tr.querySelector('input[data-field="active_"]').checked = Boolean(data.active);

        Object.keys(data).map((key) => {
            this.element.querySelector(`input[data-key="${index}"][data-field="${key}"]`).value = data[key];
        });
    }

    getDataByIndex(index) {
        const inp = this.element.querySelectorAll(`input[data-key="${index}"]`);
        const res = {
            title    : '',
            text     : '',
            active   : 0,
            showBanks: ''
        };

        if (!inp.length) {
            return res;
        }

        [...inp].forEach((el) => {
            if (typeof el.dataset.field !== 'string' || !Object.hasOwnProperty.call(res, el.dataset.field)) {
                return;
            }
            res[el.dataset.field] = el.value;
        });

        return res;
    }

    submitForm(event) {
        event.preventDefault();
        const formData = new FormData(event.target);

        this.fillMatrixRow(formData.get('variant[index]') || 1,
            {
                title    : formData.get('variant[title]') || '',
                text     : formData.get('variant[text]') || '',
                active   : Number(formData.get('variant[active]') !== null),
                showBanks: formData.get('variant[showBanks]')
            });
        this.closeModal();
    }
}
