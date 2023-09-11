import axios from 'axios';

export default {
    mounted() {
        if (this.$attrs.translates) {
            try {
                this.translates = Object.assign(this.translates, JSON.parse(atob(this.$attrs.translates)));
            } catch (error) {
                console.error(error);
            }
        }

        this.defaultAxiosConfig = {
            headers: {
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf_token"]')?.content || ''
            }
        };
    },

    methods: {
        getInputName(node, name) {
            return `items[${node.id}][${name}]`;
        },

        alert(title, message, type = 'warning') {
            const toastWrapper = document.querySelector('[data-controller="toast"]');
            const toastController = this.$attrs.stimulus.application
                .getControllerForElementAndIdentifier(toastWrapper, 'toast');

            if (toastController) {
                toastController.alert(title, message, type);

                return;
            }

            alert(title + '\n' + message);
        },

        async loadData(url, config, callback) {
            await axios.get(url, {
                ...this.defaultModel,
                ...config
            })
                .then((resp) => {
                    return callback(resp);
                })
                .catch((err) => {
                    return console.log('Error: ', err);
                });
        }
    }
};
