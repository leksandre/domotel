export default class extends window.Controller {
    static get targets() {
        return [];
    }

    constructor(props) {
        super(props);

        this.modal = '';
        this.form = '';
        this.service = '';
        this.apiKey = '';
        this.center = '';
        this.inputPrefix = '';
        this.translates = {
            error  : 'Error',
            success: 'Success',
            empty  : 'Empty list',
            added  : 'Added rows'
        };
    }

    connect() {
        this.modal = document.getElementById(this.data.get('modal'));
        this.form = this.modal.querySelector('form');
        this.service = this.data.get('service');
        this.apiKey = this.data.get('apiKey');
        this.center = this.data.get('center');
        this.inputPrefix = this.data.get('prefix');

        let translates = this.form.dataset.translates;

        if (typeof translates == 'string' && translates.length) {
            translates = JSON.parse(atob(translates));

            if (typeof translates === 'object') {
                this.translates = translates;
            }
        }

        this.bindEvents();
    }

    bindEvents() {
        this.element.addEventListener('click', (event) => {
            return event.preventDefault();
        });
        this.form.addEventListener('submit', (event) => {
            return this.submitForm(event);
        });
    }

    openModal() {
        window.Bootstrap.Modal.getOrCreateInstance(this.modal).show();
    }

    closeModal() {
        window.Bootstrap.Modal.getOrCreateInstance(this.modal).hide();
    }

    submitForm(event) {
        event.preventDefault();
        const services = {
            yandex: 'serviceYandex'
        };

        if (!this.service || !Object.hasOwnProperty.call(services, this.service)) {
            this.alert(this.translates.error, `Service ${this.service} unavailable`);

            return;
        }

        const formData = new FormData(event.target);
        const tmpTypes = formData.getAll('types[]');
        const types = {};

        tmpTypes.forEach((val) => {
            const newVal = atob(val).split('|');

            if (newVal.length < 2) {
                return;
            }

            types[newVal[0]] = decodeURIComponent(newVal[1]);
        });

        formData.delete('types[]');
        const serviceMethod = services[this.service];
        const geocoder = this;

        this[serviceMethod](formData, types).then((markers) => {
            if (typeof markers !== 'object' || !Object.keys(markers).length) {
                this.restoreSubmitButton(event.submitter);
                geocoder.closeModal();
                geocoder.alert(geocoder.translates.error, geocoder.translates.empty);

                return;
            }

            geocoder.addMarkersToMatrix(markers);
            this.restoreSubmitButton(event.submitter);
            geocoder.closeModal();
        });
    }

    restoreSubmitButton(btn)
    {
        btn.classList.remove('cursor-wait', 'btn-loading')
        btn.disabled = false;
        btn.querySelector('span.spinner-loading')?.remove();
    }

    addMarkersToMatrix(typesOfMarkers) {
        if (!Object.keys(typesOfMarkers).length) {
            return;
        }

        const fillRow = function(tr, marker) {
            for (const name in marker) {
                if (!Object.hasOwnProperty.call(marker, name)) {
                    continue;
                }

                const input = tr.querySelector(`*[name$="${name}]"]`);

                if (input) {
                    input.value = name === 'coords' ? marker[name].join(',') : marker[name];
                }
            }
        };

        let totalAdded = 0;

        for (const type in typesOfMarkers) {
            if (!Object.hasOwnProperty.call(typesOfMarkers, type)) {
                continue;
            }

            const matrix = document.querySelector(`table[data-controller=matrix][data-marker-type=${type}]`);

            if (!matrix) {
                continue;
            }

            let codes = matrix.querySelectorAll(`input[name$="code]"]`);

            codes = [...codes].map((el) => {
                return el.value.trim();
            }).filter((el) => {
                return el.length;
            });

            const matrixController = this.application.getControllerForElementAndIdentifier(matrix, 'matrix');
            const markers = typesOfMarkers[type];

            markers.forEach((el) => {
                if (codes.includes(el.code)) {
                    return;
                }

                const newRow = matrixController.addRow(new Event('geocoder:addMatrixRow'));

                if (newRow !== false) {
                    return;
                }

                let lastRow = matrix.querySelectorAll('tbody tr:not(.add-row)');

                lastRow = [...lastRow].pop();

                fillRow(lastRow, el);
                totalAdded++;
            });
        }

        this.alert(this.translates.success, `${this.translates.added}: ${totalAdded}`, 'info');
    }

    async serviceYandex(formData, types) {
        const baseUrl = 'https://search-maps.yandex.ru/v1/';
        const rspnDefault = 0;
        const kmToGradForLat = 0.0164;
        const kmToGradForLng = 0.0091;

        let center = this.center.split(',');

        center = `${center.pop()},${center.pop()}`;

        const spn1 = (parseFloat(formData.get('spn[0]')) * kmToGradForLat).toFixed(4);
        const spn2 = (parseFloat(formData.get('spn[1]')) * kmToGradForLng).toFixed(4);

        const params = [`apikey=${this.apiKey}`, 'lang=ru_RU', `ll=${center}`, `spn=${spn1},${spn2}`, `rspn=${formData.get('rspn') || rspnDefault}`, `results=${formData.get('results')}`];

        const uri = `${baseUrl}?${params.join('&')}`;
        const res = {};

        for (const k in types) {
            if (!Object.hasOwnProperty.call(types, k)) {
                continue;
            }
            const title = types[k];

            // eslint-disable-next-line no-undef,no-await-in-loop
            await axios.get(`${uri}&text=${title}`).then((resp) => {
                const data = resp.data;
                const hasResults = data.properties.ResponseMetaData.SearchResponse.found || 0;

                if (!hasResults) {
                    return false;
                }

                data.features.forEach((el) => {
                    const elObj = {
                        code       : el.properties.CompanyMetaData.id || null,
                        coords     : [el.geometry.coordinates[1] || 0, el.geometry.coordinates[0] || 0],
                        title      : el.properties.name || el.properties.CompanyMetaData.name || null,
                        description: el.properties.description || el.properties.CompanyMetaData.address || null
                    };

                    if (!elObj.code) {
                        return;
                    }

                    if (typeof res[k] === 'undefined') {
                        res[k] = [];
                    }

                    res[k].push(elObj);
                });
            });
        }

        return res;
    }
}
