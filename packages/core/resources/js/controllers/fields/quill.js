import Quill from 'quill';
import Typograf from 'typograf';

export default class extends window.Controller {
    /**
     *
     */
    connect() {
        const quill = Quill;
        const selector = this.element.querySelector('.quill').id;
        const input = this.element.querySelector('input');

        quill.register(quill.import('attributors/style/align'), true);
        quill.register(quill.import('attributors/style/size'), true);
        quill.register(quill.import('attributors/style/direction'), true);
        quill.register(quill.import('attributors/style/color'), true);
        quill.register(quill.import('attributors/style/background'), true);

        const options = {
            placeholder: input.placeholder,
            readOnly: input.readOnly,
            theme: 'snow',
            modules: {
                toolbar: {
                    container: this.containerToolbar(),
                    handlers: {
                        'kelnik-typograf': () => this.typograf(),
                        'kelnik-source': () => this.sourceHtml()
                    }
                },
            },
        };

        let icons = quill.import('ui/icons');
        icons['kelnik-typograf'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-font" viewBox="0 0 16 16">' +
            '<path d="M10.943 6H5.057L5 8h.5c.18-1.096.356-1.192 1.694-1.235l.293-.01v5.09c0 .47-.1.582-.898.655v.5H9.41v-.5c-.803-.073-.903-.184-.903-.654V6.755l.298.01c1.338.043 1.514.14 1.694 1.235h.5l-.057-2z"/>' +
            '<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>' +
            '</svg>';

        icons['kelnik-source'] = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-code" viewBox="0 0 16 16">' +
            '<path d="M5.854 4.854a.5.5 0 1 0-.708-.708l-3.5 3.5a.5.5 0 0 0 0 .708l3.5 3.5a.5.5 0 0 0 .708-.708L2.707 8l3.147-3.146zm4.292 0a.5.5 0 0 1 .708-.708l3.5 3.5a.5.5 0 0 1 0 .708l-3.5 3.5a.5.5 0 0 1-.708-.708L13.293 8l-3.147-3.146z"/>' +
            '</svg>';

        // Dispatch the event for customization and installation of plugins
        document.dispatchEvent(new CustomEvent('orchid:quill', {
            detail: {
                quill: quill,
                options: options
            }
        }));

        this.editor = new quill(`#${selector}`, options);

        // quill editor add image handler
        let isBase64Format = JSON.parse(this.data.get('base64'));
        if (! isBase64Format) {
            this.editor.getModule('toolbar').addHandler('image', () => {
                this.selectLocalImage();
            });
        }

        let value = JSON.parse(this.data.get("value"))

        // set value
        // editor.setText(input.value);
        this.editor.root.innerHTML = input.value = value;

        // save value
        this.editor.on('text-change', () => {
            input.value = this.editor.getText() ? this.editor.root.innerHTML : '';
            input.dispatchEvent(new Event('change'));
        });

        this.editor.getModule('toolbar').addHandler('color', (value) => {
            this.editor.format('color', this.customColor(value));
        });

        this.editor.getModule('toolbar').addHandler('background', (value) => {
            this.editor.format('background', this.customColor(value));
        });
    }

    /**
     * Show dialog for custom color
     *
     * @param value
     */
    customColor = (value) => {
        return value === 'custom-color'
            ? window.prompt('Enter Color Code (#c0ffee or rgba(255, 0, 0, 0.5))')
            : value;
    }

    colors() {
        return [
            '#000000', '#e60000', '#ff9900', '#ffff00', '#008a00', '#0066cc',
            '#9933ff', '#ffffff', '#facccc', '#ffebcc', '#ffffcc', '#cce8cc',
            '#cce0f5', '#ebd6ff', '#bbbbbb', '#f06666', '#ffc266', '#ffff66',
            '#66b966', '#66a3e0', '#c285ff', '#888888', '#a10000', '#b26b00',
            '#b2b200', '#006100', '#0047b2', '#6b24b2', '#444444', '#5c0000',
            '#663d00', '#666600', '#003700', '#002966', '#3d1466', 'custom-color'
        ];
    }

    containerToolbar() {
        const controlsGroup = {
            "text": [
                {size:['10px', false, '18px', '32px']},
                {header: [1, 2, 3, 4, 5, 6, false]},
                'bold', 'italic', 'underline', 'strike', 'clean'
            ],
            "quote": ['blockquote', 'code-block'],
            "color": [{color: this.colors()}, {background: this.colors()}],
            "format": [{list: 'ordered'}, {list: 'bullet'}, {align: []}],
            "media": ['link', 'image', 'video'],
            "kelnik": ['kelnik-typograf', 'kelnik-source']
        }

        return JSON.parse(this.data.get('toolbar')).map(tool => controlsGroup[tool]);
    }

    /**
     * Step1. select local image
     *
     */
    selectLocalImage() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.click();

        // Listen upload local image and save to server
        input.onchange = () => {
            const file = input.files[0];

            // file type is only image.
            if (/^image\//.test(file.type)) {
                this.saveToServer(file);

                return;
            }

            this.alert('Validation error', 'You could only upload images.', 'danger');
            console.warn('You could only upload images.');
        };
    }

    /**
     * Step2. save to server
     *
     * @param {File} file
     */
    saveToServer(file) {
        const formData = new FormData();
        formData.append('image', file);

        if (this.data.get('groups')) {
            formData.append('group', this.data.get('groups'));
        }

        axios
            .post(this.prefix('/systems/files'), formData)
            .then((response) => {
                this.insertToEditor(response.data.url);
            })
            .catch((error) => {
                this.alert('Validation error', 'Quill image upload failed');
                console.warn('quill image upload failed');
                console.warn(error);
            });
    }

    /**
     * Step3. insert image url to rich editor.
     *
     * @param {string} url
     */
    insertToEditor(url) {
        // push image url to rich editor.
        const range = this.editor.getSelection();
        this.editor.insertEmbed(range.index, 'image', url);
    }

    typograf() {
        if (typeof this.editor.typograf === 'undefined') {
            this.editor.typograf = new Typograf({
                locale: ['ru', 'en-US'],
                htmlEntity: {type: 'name'}
            });
        }

        this.editor.root.innerHTML = this.editor.typograf.execute(this.editor.root.innerHTML);
    }

    sourceHtml() {
        if (typeof this.editor.kelnikSourceCode === 'undefined') {
            let el = this.editor.container.nextElementSibling;
            while (el) {
                if (el.classList.contains('modal')) {
                    this.editor.kelnikSourceModal = el;
                    break;
                }
                el = el.nextElementSibling;
            }

            this.editor.kelnikSourceCode = this.editor.kelnikSourceModal.querySelector('.kelnik-quill-source');

            // Close buttons
            let buttons = this.editor.kelnikSourceModal.querySelectorAll('button[data-bs-dismiss="modal"]');
            if (buttons) {
                [...buttons].forEach((el) => el.addEventListener('click', () => close()));
            }

            // Save button
            let saveButton = this.editor.kelnikSourceModal.querySelector('.kelnik-quill-source-save');
            if (saveButton) {
                saveButton.addEventListener('click', () => save());
            }
        }

        let close = () => {
            this.editor.kelnikSourceModal.style.display = 'none';
        }

        let save = () => {
            const delta = this.editor.clipboard.convert(this.editor.kelnikSourceCode.value);
            this.editor.setContents(delta, 'user');
            close();
        }

        this.editor.kelnikSourceCode.value = this.editor.root.innerHTML;
        this.editor.kelnikSourceModal.style.display = 'block';
    }
}
