export default class extends window.Controller {
    static targets = [
        "source",
        "upload"
    ];

    connect() {
        let fileUrl = this.data.get('url') ? this.data.get('url') : this.data.get(`value`);

        if (fileUrl) {
            this.element.querySelector('.file-preview').href = fileUrl;
            this.element.querySelector('.file-preview').innerText = this.data.get('origname') || '';
            return;
        }

        this.element.querySelector('.file-preview').classList.add('none');
        this.element.querySelector('.picture-remove').classList.add('none');
    }

    /**
     * Event for uploading image
     *
     * @param event
     */
    upload(event) {
        if (!event.target.files[0]) {
            return;
        }

        let maxFileSize = this.data.get('max-file-size');
        if (event.target.files[0].size / 1024 / 1024 > maxFileSize) {
            this.alert('Validation error', `The download file is too large. Max size: ${maxFileSize} MB`);
            event.target.value = null;
            return;
        }

        let reader = new FileReader();
        reader.readAsDataURL(event.target.files[0]);

        reader.onloadend = () => {
            const formData = new FormData();

            formData.append('file', event.target.files[0]);
            formData.append('storage', this.data.get('storage'));
            formData.append('group', this.data.get('groups'));

            const element = this.element;
            window.axios.post(this.prefix('/systems/files'), formData)
                .then((response) => {
                    const fileInfo = response.data.url;
                    const targetValue = this.data.get('target');

                    element.querySelector('.file-preview').href = fileInfo;
                    element.querySelector('.file-preview').innerText = response.data.original_name || '';
                    element.querySelector('.file-preview').classList.remove('none');
                    element.querySelector('.picture-remove').classList.remove('none');
                    element.querySelector('.picture-path').value = response.data[targetValue];

                    const modal = element.querySelector('.modal');

                    if (modal) {
                        window.Bootstrap.Modal.getOrCreateInstance(modal)?.hide();
                    }
                })
                .catch((error) => {
                    this.alert('Validation error', 'File upload error');
                    console.warn(error);
                });
        };
    }

    clear() {
        this.element.querySelector('.picture-path').value = '';
        this.element.querySelector('.file-preview').classList.add('none');
        this.element.querySelector('.picture-remove').classList.add('none');
    }
}
