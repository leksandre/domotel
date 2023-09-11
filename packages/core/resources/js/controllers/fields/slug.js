export default class extends window.Controller {
    static get targets() {
        return ['name'];
    }

    constructor(props) {
        super(props);
        this.inputElement = '';
        this.source = '';
        this.sourceId = 0;
        this.method = '';
        this.sourceText = '';
        this.slug = '';
        this.state = false;
        this.timerHandler = false;
        this.action = 'transliterate';
        this.prevRequest = false;
        this.additionalFields = [];
    }

    connect() {
        this.init();
    }

    init() {
        this.source = this.data.get('source');
        this.sourceId = this.data.get('sourceId');
        this.method = this.data.get('method');
        this.additionalFields = this.data.get('additionalFields');
        this.inputElement = this.element.querySelector('input');

        if (!this.additionalFields) {
            this.additionalFields = [];

            return;
        }

        this.additionalFields = atob(this.additionalFields).split('|');
    }

    onChange() {
        const sourceElement = document.getElementById(this.source);

        if (!sourceElement) {
            return;
        }

        this.sourceText = sourceElement.value;
        this.action = 'transliterate';
        this.transliterateAndCheck();
    }

    onKeyUp() {
        const that = this;

        clearInterval(this.timerHandler);
        this.timerHandler = setTimeout(() => {
            that.slug = that.inputElement.value;
            that.action = 'check';
            that.transliterateAndCheck();
        },
        700);
    }

    transliterateAndCheck() {
        if (this.prevRequest) {
            this.prevRequest.cancel('Cancel previous request');
        }

        this.prevRequest = axios.CancelToken.source();

        const data = new FormData();

        data.append('action', this.action);
        data.append('source', this.sourceText);
        data.append('sourceId', this.sourceId);
        data.append('slug', this.slug);

        this.addFieldValues(data);

        axios
            .post(this.method,
                data,
                {cancelToken: this.prevRequest.token})
            .catch((thrown) => {
            })
            .then((response) => {
                this.prevRequest = false;
                if (response) {
                    this.slug = response.data.slug || '';
                    this.state = response.data.state || false;
                }
                this.replace();
                this.changeState();
            });
    }

    addFieldValues(data) {
        if (!this.additionalFields) {
            return;
        }

        this.additionalFields.forEach((item) => {
            const val = document.getElementsByName(item);

            if (val.length) {
                data.append(item, val.item(0).value);
            }
        });
    }

    replace() {
        if (this.action === 'transliterate') {
            this.inputElement.value = this.slug;
        }
    }

    changeState() {
        const errorClass = 'is-invalid';
        const successClass = 'is-valid';

        this.inputElement.classList.remove(errorClass);
        this.inputElement.classList.remove(successClass);
        this.inputElement.classList.add(this.state ? successClass : errorClass);
    }
}
