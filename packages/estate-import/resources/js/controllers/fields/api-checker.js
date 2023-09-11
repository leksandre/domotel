export default class extends window.Controller {
    form = null;


    static get targets() {
        return ['button'];
    }

    connect() {
        this.bindEvents();
    }

    bindEvents() {
        this.form = this.buttonTarget.closest('form');

        this.buttonTarget.addEventListener('click', (event) => {
            event.preventDefault();

            const route = this.buttonTarget.dataset.route || null;

            if (!route) {
                this.alert('Route required');

                return false;
            }

            window.axios.post(this.buttonTarget.dataset.route, new FormData(this.form))
                .then((res) => {
                    this.alert(res.data.header, res.data.text, res.data.res === true ? 'info' : 'warning');
                })
                .catch((error) => {
                    this.alert('Request error', error.message, 'error');
                });

            return false;
        });
    }

    disableButton() {
        this.buttonTarget.disabled = 'disabled';
    }

    enableButton() {
        this.buttonTarget.disabled = false;
    }

    alert(title, message, type = 'warning') {
        const toastWrapper= document.querySelector('[data-controller="toast"]');
        const toastController = this.application.getControllerForElementAndIdentifier(toastWrapper, 'toast');

        if (toastController) {
            toastController.alert(title, message, type);

            return;
        }

        alert(title + '\n' + message);
    }

    submit() {
        this.disableButton();
        this.enableButton();

        return false;
    }
}
