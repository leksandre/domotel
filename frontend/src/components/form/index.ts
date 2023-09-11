import type {IForm, IFormOptions} from './types';
import axios from 'axios';
import type {AxiosResponse} from 'axios';
import CustomSelect from './custom-select';
import type {IObserver} from '@/common/scripts/types/observer';
import type {IPopup} from '@/components/popup/types';
import Observer from '@/common/scripts/observer';
import {Utils} from '@/common/scripts/utils';

const CLASS_READY = 'is-ready';
const CLASS_SHOW = 'is-active';
const CLASS_ERROR = 'is-error';
const CLASS_DISABLED = 'is-disabled';
const observer: IObserver = new Observer();

class Form implements IForm {
    private wrapper: HTMLElement;
    private popupInstance: IPopup | null;
    private form: HTMLFormElement | null;
    private formId: string = '';
    private isValidation: boolean;
    private successMessage: boolean;
    private action: string;
    private messageBot: HTMLInputElement | null;
    private selects: Element[];
    private submit: HTMLElement | null;
    private submitText: HTMLSpanElement | null;
    private submitSendText: string = '';
    private submitSuccessText: string;
    private submitErrorText: string;
    private successMessageWrap: Element | null;
    private errorMessageWrap: Element | null;
    private formData: FormData;

    public init(options: IFormOptions): void {
        this.wrapper = options.target;
        this.popupInstance = options.popupInstance || null;
        this.form = options.target.querySelector('form');
        if (!this.form) {
            const selector = [...this.wrapper.classList].find((item: string) => {
                return new RegExp(/^j-/g).test(item);
            });

            throw new Error(`Не найден элемент формы в блоке '.${selector}'`);
        }
        this.action = this.form.getAttribute('action') || '';
        if (!this.action) {
            throw new Error('Не указан action формы');
        }
        this.isValidation = 'validate' in this.form.dataset || false;
        this.successMessage = options.successMessage;
        this.messageBot = this.form.querySelector('[name="message_bot"]');
        this.selects = [...this.form.querySelectorAll('.j-form__select')];

        this._getElements();
        this._subscribes();
        this._bindEvents();
        this._setHiddenUrl();
        this._setHiddenInputs();
    }

    /**
     * Метод получает дом элементы.
     * @private
     */
    private _getElements(): void {
        this.formId = this.form!.getAttribute('id') || '';
        this.submit = this.form!.querySelector('.j-form-submit');

        if (!this.submit) {
            throw new Error(`Нет элемента кнопки отправки формы в элементе ${this.form}`);
        }

        this.submitText = this.submit.querySelector('span');
        this.submitSendText = this.submit.dataset['sendText'] || '';
        this.submitSuccessText = this.submit.dataset['successText'] || 'Отправлено';
        this.submitErrorText = this.submit.dataset['errorText'] || 'Повторить отправку';

        if (this.successMessage) {
            this.successMessageWrap = this.wrapper.querySelector('.j-form-success');
            this.errorMessageWrap = this.wrapper.querySelector('.j-form-error');
        }
    }

    private _subscribes(): void {
        if (this.isValidation) {
            observer.subscribe('formIsValid', (form: Element) => {
                if (form === this.form) {
                    this._send();
                }
            });
        }
    }

    /**
     * Метод навешивает обработчики событий.
     * @private
     */
    private _bindEvents(): void {
        if (!this.isValidation) {
            if (this.submit) {
                this.submit.addEventListener('click', (event: MouseEvent) => {
                    event.preventDefault();
                    this._send();
                });
            }
        }

        this.selects.forEach((element: Element) => {
            const select = new CustomSelect({
                element: element as HTMLElement
            });

            select.init();
        });
    }

    /**
     * Метод отправляет данные на сервер.
     * @private
     */
    private _send(): void {
        if (this._isAllReady()) {
            return;
        }

        this._insertAntiSpam();
        this.formData = this.form ? new FormData(this.form) : new FormData();
        this._insertButtonText(this.submitSendText);

        axios.post(this.action, this.formData)
            .then((response: AxiosResponse) => {
                const status = response.data.success;

                if (status) {
                    this._sendIsSuccess();
                } else {
                    throw new Error('Ошибка на сервере');
                }
            })
            .catch((err: unknown) => {
                this._sendIsError();
                console.error(err);
            });
    }

    private _isAllReady(): boolean {
        return this.wrapper.classList.contains(CLASS_READY);
    }

    /**
     * Метод вызывается при успешной отправке запроса.
     * @private
     */
    private _sendIsSuccess(): void {
        this.errorMessageWrap?.classList.remove(CLASS_SHOW);
        this.submit?.classList.remove(CLASS_ERROR);
        this.submit?.classList.add(CLASS_DISABLED);

        this._showSuccessMessage();
        this._insertButtonText(this.submitSuccessText);
    }

    /**
     * Метод вызывается при ошибке отправки запроса.
     * @private
     */
    private _sendIsError(): void {
        this._insertButtonText(this.submitErrorText);
        this.submit?.classList.add(CLASS_ERROR);

        this._showErrorMessage();
    }

    /**
     * Показывает сообщение об успешной отправкой формы.
     * @private
     */
    private _showSuccessMessage(): void {
        this.wrapper.classList.add(CLASS_READY);
        this.successMessageWrap?.classList.add(CLASS_SHOW);
        Utils.hide(this.form);
    }

    /**
     * Показывает сообщение об ошибке
     * @private
     */
    private _showErrorMessage(): void {
        this.wrapper.classList.add(CLASS_READY);
        this.successMessageWrap?.classList.remove(CLASS_SHOW);
        this.errorMessageWrap?.classList.add(CLASS_SHOW);
        Utils.hide(this.form);
    }

    /**
     * Метод вставляет текст в кнопку сабмит.
     * @param {string} text - необходимый текст.
     * @private
     */
    private _insertButtonText(text: string): void {
        if (!this.submitText) {
            return;
        }

        Utils.clearHtml(this.submitText);
        Utils.insetContent(this.submitText, text);
    }

    /**
     * Метод вставляет скрытое поле с антиспамом
     */
    private _insertAntiSpam(): void {
        if (!this.messageBot) {
            return;
        }

        const minRandom = 1;
        const maxRandom = 10;

        this.messageBot.value = `nospam ${Utils.random(minRandom, maxRandom)}`;
    }

    /**
     * При необходимости устанавливает текущий url в скрытый инпут
     * @private
     */
    private _setHiddenUrl(): void {
        const input: HTMLInputElement | null |undefined = this.form?.querySelector('input[name=url]');

        if (input) {
            input.value = window.location.href;
        }
    }

    /**
     * Устанавливает данные в скрытый input, исходя из данных кнопки вызова попапа
     * @private
     */
    private _setHiddenInputs(): void {
        if (!this.popupInstance?.currentTarget) {
            return;
        }
        const inputsData = this.popupInstance.currentTarget.dataset['description'];

        if (!inputsData) {
            return;
        }

        const inputsArray = inputsData.split(',');

        for (const item of inputsArray) {
            const data: string[] = item.split(':');

            if (data.length < 2) {
                return;
            }

            const input: HTMLInputElement | null | undefined =
                this.form?.querySelector(`input[name="${data[0]}"]`) as HTMLInputElement;

            if (input) {
                input.value = data[1] || '';
            } else {
                // @ts-ignore
                this._createDescriptionInput(data);
            }
        }
    }

    /**
     * Создаем новый инпут и вставляем его в форму заявки с текстом из data-аттрибута
     * @param {Array} data - массив с именем и значением для создаваемого поля
     */
    private _createDescriptionInput(data: [string, string]): void {
        const newInput = document.createElement('input');

        newInput.setAttribute('type', 'hidden');
        newInput.setAttribute('name', data[0]);
        newInput.value = data[1];

        this.form?.prepend(newInput);
    }
}

export default Form;
