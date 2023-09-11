/**
 * @author Kelnik
 *
 * Validation - Проверка инпутов на валидность
 */

import type {
    IValidateInputResult,
    IValidation,
    IValidationMessages,
    IValidationMessagesR,
    IValidationOptions,
    IValidationOptionsR
} from './types';
import type {ICustomSelect} from '@/components/form/custom-select/types';
import InputMask from 'inputmask';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';

const observer: IObserver = new Observer();
const FORM_INPUTS_SELECTOR = 'input:not([type="submit"]):not([type="hidden"]):not([type="button"]), textarea';
const OPTIONS_DEFAULT = {
    formSuccess     : 'validate-form-success',
    formError       : 'validate-form-error',
    inputSuccess    : 'validate-input-success',
    inputError      : 'validate-input-error',
    inputKey        : 'validate-input-key',
    phoneMask       : '+7 (999) 999 99-99',
    emailReg        : /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, // eslint-disable-line
    numberReg       : /^\d+$/,
    lettersReg      : /^[a-zA-Zа-яёА-ЯЁ]+(\s[a-zA-Zа-яёА-ЯЁ]+)*$/i,
    urlReg          : /^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=.]+$/, // eslint-disable-line
    keyDelay        : 500,
    separatorPrice  : ' ',
    separatorDecimal: ',',
    decimalDigits   : 1
};
const MESSAGES_DEFAULT = {
    required: 'Поле обязательно для заполнения',
    min     : 'Мин $ символа',
    max     : 'Длина строки не больше $',
    email   : 'Введите эл. почту',
    number  : 'Только числа',
    letters : 'Только буквы',
    url     : 'Не корректный url',
    tel     : 'Не корректный номер телефона',
    regexp  : 'Не соответствует формату',
    mask    : 'Заполните поле полностью'
};

class Validation implements IValidation {
    private options: IValidationOptionsR;

    private messages: IValidationMessagesR;

    private forms: Element[] = [];
    private inputs: Element[] = [];

    constructor(options: IValidationOptions = {}, messages: IValidationMessages = {}) {
        this.options = Object.assign(OPTIONS_DEFAULT, options);
        this.messages = Object.assign(MESSAGES_DEFAULT, messages);

        this.update();
    }

    public update(): void {
        this._initElements();
        this._filterInputsIntoForm();
        this._initInputMask();

        this._bindEvents();
        this._subscribes();
    }

    /**
     * При ошибках в валидации
     * @param {Node} content - Контейнер или сама форма которую нужно обновить
     */
    public refresh(content: HTMLElement): void {
        if (!content) {
            return;
        }

        let forms = [...content.querySelectorAll('form[data-init]')];

        if (!forms.length && content.tagName === 'form') {
            forms = [content];
        }

        forms.forEach((form: Element) => {
            form.removeAttribute('data-init');

            [...form.querySelectorAll(FORM_INPUTS_SELECTOR)].forEach((input: Element) => {
                input.removeAttribute('data-init');
            });
        });

        if (forms.length) {
            this.update();
        }
    }

    private _initElements(): void {
        this.forms = [...document.querySelectorAll('form[data-validate]')];
        this.inputs = [...document.querySelectorAll('input[data-validate], textarea[data-validate]')];
    }

    /**
     * Фильтруем все инпуты которые будут валидироваться в форме
     */
    private _filterInputsIntoForm(): void {
        this.inputs = this.inputs.filter((input: Element) => {
            return !input.closest('form');
        });
    }

    /**
     * Добавляем маски для телефонов и кастомные (через аттрибут)
     */
    private _initInputMask(): void {
        const optionsMask = {showMaskOnHover: false};
        const phone = (input: Element): void => {
            new InputMask(this.options.phoneMask, optionsMask).mask(input as HTMLElement);
        };
        const customMask = (input: Element): void => {
            const mask = input.getAttribute('data-validate-mask');

            if (mask) {
                new InputMask(mask, optionsMask).mask(input as HTMLElement);
            }
        };

        const giveMasks = (input: HTMLInputElement): void => {
            if (input.hasAttribute('data-validate-mask')) {
                customMask(input);
            } else if (input.type === 'tel' || input.hasAttribute('data-validate-tel')) {
                phone(input);
            }
        };

        this.inputs.forEach((input: Element): void => {
            giveMasks(input as HTMLInputElement);
        });

        this.forms.forEach((form: Element): void => {
            const inputs = form.querySelectorAll(FORM_INPUTS_SELECTOR);

            inputs.forEach((input: Element) => {
                giveMasks(input as HTMLInputElement);
            });
        });
    }

    private _bindEvents(): void {
        this.forms.forEach((form: Element) => {
            if (!this._verifyDoubleInit(form as HTMLElement)) {
                return;
            }

            const inputs = [...form.querySelectorAll(FORM_INPUTS_SELECTOR)];
            const typeCheck = (form as HTMLElement).dataset['validateCheck'];
            const inputKeys = inputs.filter((input: Element) => {
                return (input as HTMLElement).dataset['validateKeyOnly'] ||
                    (input as HTMLElement).dataset['validatePrice'];
            });

            if (typeCheck === 'key') {
                this._inputsEvents(inputs);
            } else if (inputKeys.length) {
                this._inputsEvents(inputKeys);
            }

            this._replaceDefaultValid(inputs);

            (form as HTMLElement).addEventListener('submit', (event: SubmitEvent) => {
                event.preventDefault();

                let valid = true;
                const selects = [...form.querySelectorAll('.j-custom-select:not([data-no-validate])')];

                inputs.forEach((input: Element) => {
                    const validate = this._validateInput(input as HTMLElement);
                    const name = (input as HTMLElement).dataset['validateName'];
                    const error = document.querySelector(`[data-validate-error="${name}"]`);

                    if (validate.result) {
                        this._inputSuccess(input as HTMLElement, error as HTMLElement);
                    } else {
                        valid = false;
                        this._inputError(input as HTMLElement, error as HTMLElement, validate);
                    }
                });

                if (selects.length) {
                    selects.forEach((select: Element) => {
                        const result = this._validateSelect(select as HTMLElement);

                        if (!result) {
                            valid = false;
                        }
                    });
                }

                if (valid) {
                    this._formSuccess(form as HTMLElement);
                } else {
                    this._formError(form as HTMLElement);
                }
            });
        });

        this._replaceDefaultValid(this.inputs);
        this._inputsEvents(this.inputs);
    }

    private _subscribes(): void {
        observer.subscribe('custom-select:close', (select: ICustomSelect) => {
            this._validateSelect(select.element);
        });
    }

    private _validateSelect(select: HTMLElement): boolean {
        const placeholder = select.dataset['placeholder'];
        const selected = (select.querySelector('.j-custom-select__value') as HTMLElement).innerText;
        const validate = this._validateInput(select);
        const name = select.dataset['validateName'];
        const error = document.querySelector(`[data-validate-error="${name}"]`);
        let result = true;

        if (placeholder === selected) {
            this._inputError(select, error as HTMLElement, validate);

            result = false;
        } else {
            this._inputSuccess(select, error as HTMLElement);

            result = true;
        }

        return result;
    }

    /**
     * Бинд событий для инпутов
     * @param {Array} inputs - Элементы инпутов
     */
    // eslint-disable-next-line max-lines-per-function
    private _inputsEvents(inputs: Element[]): void {
        inputs.forEach((input: Element) => {
            if (!this._verifyDoubleInit(input as HTMLElement)) {
                return;
            }

            const name = (input as HTMLElement).dataset['validateName'];
            const error = document.querySelector(`[data-validate-error="${name}"]`);
            const keyOnly = (input as HTMLElement).dataset['validateKeyOnly'];
            const isPrice = input.hasAttribute('data-validate-price');
            const isDecimal = input.hasAttribute('data-validate-decimal');
            let timerKeyup: ReturnType<typeof setTimeout> | null = null;
            const validateInput = (): void => {
                const validate = this._validateInput(input as HTMLElement);

                if ((input as HTMLInputElement).value.length) {
                    validate.result ?
                        this._inputSuccess(input as HTMLInputElement, error as HTMLElement) :
                        this._inputError(input as HTMLInputElement, error as HTMLElement, validate);
                } else {
                    const validated = this._checkValidateClass(input as HTMLInputElement);

                    if (validated) {
                        this._setValidationClass(input as HTMLInputElement, validate);
                    }
                }
            };

            if (keyOnly) {
                this._onlyKeyEvents(input, keyOnly);
            }

            if (isPrice) {
                this._formatPrice(input as HTMLInputElement);
            } else if (isDecimal) {
                this._formatDecimal(input as HTMLInputElement);
            }

            input.addEventListener('focusout', () => {
                timerKeyup = this._inputsAddKeyClass(input as HTMLInputElement, timerKeyup);

                setTimeout(() => {
                    validateInput();
                }, 0);
            });
        });
    }

    /**
     * Вешает события на ввод инпутам, ввод только допустимых символов
     * @param {Node} input - Элемент инпута
     * @param {string} keyOnly - Строка key'сов для ввода
     */
    private _onlyKeyEvents(input: Element, keyOnly: string): void {
        const keysRaw = keyOnly.split(',').map((key: string) => {
            if (Number(key)) {
                return Number(key);
            }

            return key.split('-').map((keyItem: string) => {
                return Number(keyItem);
            });
        });
        // key after filter
        const keys = keysRaw.filter((key: number | number[]) => {
            return Number(key) || (Array.isArray(key) && key.filter((k: number) => {
                return Boolean(k);
            }).length);
        });

        (input as HTMLElement).addEventListener('keypress', (event: KeyboardEvent) => {
            let stop = true;

            keys.forEach((key: number | number[]) => {
                if (Array.isArray(key)) {
                    if (typeof key[0] !== 'undefined' && event.charCode >= key[0] &&
                        typeof key[1] !== 'undefined' && event.charCode <= key[1]) {
                        stop = false;
                    }
                } else if (event.charCode === key) {
                    stop = false;
                }
            });

            stop && event.preventDefault();
        });

        input.addEventListener('paste', (event: Event) => {
            event.preventDefault();
        });
    }

    private _formatPrice(input: HTMLInputElement): void {
        const separator = input.dataset['validatePrice'] || this.options.separatorPrice;

        input.addEventListener('keypress', (event: KeyboardEvent) => {
            if (!(event.charCode >= 48 && event.charCode <= 57)) {
                event.preventDefault();
            }
        });

        input.addEventListener('input', () => {
            const val = input.value.replaceAll(separator, '');

            if (!val) {
                return;
            }

            input.value = this._toFormatPrice(val, separator);
        });

        input.addEventListener('paste', (event: ClipboardEvent) => {
            event.preventDefault();
        });
    }

    private _toFormatPrice(value: string, separator: string = ' '): string {
        const val = parseFloat(value)
            .toFixed(0)
            .replace('.', ',');

        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, separator);
    }

    private _formatDecimal(input: HTMLInputElement): void {
        const separator: string = input.dataset['validateDecimal'] || this.options.separatorDecimal;
        const digits: number = Number(input.dataset['validateDecimalDigits']) || this.options.decimalDigits;

        new InputMask({
            regex          : `\\d+(\\${separator}\\d{${digits}})?`,
            placeholder    : '',
            showMaskOnHover: false
        }).mask(input);

        input.addEventListener('focusout', () => {
            const arr = input.value.split(separator);

            if (arr[1]) {
                input.value = `${arr[0]}${separator}${arr[1] + '0'.repeat(digits - arr[1].length)}`;
            } else if (arr[0]) {
                input.value = `${input.value}${separator}${'0'.repeat(digits)}`;
            }
        });
    }

    /**
     * Добавляет класс при вводе текста
     * @param {Node} input - Элемент инпута
     * @param {function} timer - Таймер перерыва ввода
     * @return {function} - Таймер перерыва ввода
     */
    private _inputsAddKeyClass(input: HTMLInputElement, timer: ReturnType<typeof setTimeout> | null):
    ReturnType<typeof setTimeout> {
        input.classList.add(this.options.inputKey);

        if (timer) {
            clearTimeout(timer);
        }

        return setTimeout(() => {
            input.classList.remove(this.options.inputKey);
        }, this.options.keyDelay);
    }

    /**
     * Проверяем на повторную инициализацию и добавляем атрибут data-init
     * @param {Node} element - элемент
     * @return {boolean} - True если элемент не инициализировался
     */
    private _verifyDoubleInit(element: HTMLElement): boolean {
        if (element.dataset['init']) {
            return false;
        }

        element.setAttribute('data-init', 'true');

        return true;
    }

    /**
     * Заменяет аттрибут required на data-validate-required (для отключения стандартной валидации)
     * @param {Array} inputs - элементы инпутов
     */
    private _replaceDefaultValid(inputs: Element[]): void {
        inputs.forEach((input: Element) => {
            if ((input as HTMLInputElement).required) {
                input.removeAttribute('required');
                input.setAttribute('data-validate-required', '');
            }

            if ((input as HTMLInputElement).type === 'tel') {
                (input as HTMLInputElement).type = 'text';
                input.setAttribute('data-validate-tel', '');
            }

            if ((input as HTMLInputElement).type === 'email') {
                (input as HTMLInputElement).type = 'text';
                input.setAttribute('data-validate-email', '');
            }

            if ((input as HTMLInputElement).type === 'number') {
                (input as HTMLInputElement).type = 'text';
                input.setAttribute('data-validate-number', '');
            }

            if ((input as HTMLInputElement).type === 'url') {
                (input as HTMLInputElement).type = 'text';
                input.setAttribute('data-validate-url', '');
            }
        });
    }

    /**
     * Проверка инпута на валидацию (по data-аттрибутам)
     * @param {Node} input - Инпут для валидации
     * @return {object} - Результат валидации
     */
    // eslint-disable-next-line max-lines-per-function,complexity,max-statements
    private _validateInput(input: HTMLElement): IValidateInputResult {
        if (input.hasAttribute('data-validate-required')) {
            if (!this._validateRequired(input as HTMLInputElement)) {
                const message = input.dataset['validateRequiredMsg'] || this.messages.required;

                return {
                    result: false,
                    error : 'required',
                    message
                };
            }
        }

        if (input.hasAttribute('data-validate-mask') ||
            input.hasAttribute('data-validate-tel') ||
            (input as HTMLInputElement).type === 'tel') {
            if (!this._validateMask(input as HTMLInputElement)) {
                const message = input.dataset['validateMaskMsg'] || this.messages.mask;

                return {
                    result: false,
                    error : 'mask',
                    message
                };
            }
        }

        if (input.hasAttribute('data-validate-regexp')) {
            if (!this._validateRegexp(input as HTMLInputElement)) {
                const message = input.dataset['validateRegexpMsg'] || this.messages.regexp;

                return {
                    result: false,
                    error : 'regexp',
                    message
                };
            }
        }

        if (input.hasAttribute('data-validate-email') || (input as HTMLInputElement).type === 'email') {
            if (!this._validateEmail(input as HTMLInputElement)) {
                const message = input.dataset['validateEmailMsg'] || this.messages.email;

                return {
                    result: false,
                    error : 'email',
                    message
                };
            }
        }

        if (input.hasAttribute('data-validate-number') || (input as HTMLInputElement).type === 'number') {
            if (!this._validateNumber(input as HTMLInputElement)) {
                const message = input.dataset['validateNumberMsg'] || this.messages.number;

                return {
                    result: false,
                    error : 'number',
                    message
                };
            }
        }

        if (input.hasAttribute('data-validate-letters')) {
            if (!this._validateLetters(input as HTMLInputElement)) {
                const message = input.dataset['validateLettersMsg'] || this.messages.letters;

                return {
                    result: false,
                    error : 'string',
                    message
                };
            }
        }

        if (input.hasAttribute('data-validate-url')) {
            if (!this._validateUrl(input as HTMLInputElement)) {
                const message = input.dataset['validateUrlMsg'] || this.messages.url;

                return {
                    result: false,
                    error : 'url',
                    message
                };
            }
        }

        if (input.hasAttribute('data-validate-min')) {
            if (!this._validateMin(input as HTMLInputElement)) {
                const message = input.dataset['validateMinMsg'] || this.messages.min;

                return {
                    result: false,
                    error : 'min',
                    message
                };
            }
        }

        if (input.hasAttribute('data-validate-max')) {
            if (!this._validateMax(input as HTMLInputElement)) {
                const message = input.dataset['validateMaxMsg'] || this.messages.max;

                return {
                    result: false,
                    error : 'max',
                    message
                };
            }
        }

        return {
            result: true
        };
    }

    private _checkIfEmptyNotRequired(input: HTMLInputElement): boolean {
        return !input.value && !input.dataset['validateRequired'];
    }

    private _validateRequired(input: HTMLInputElement): boolean {
        if (input.type === 'checkbox') {
            return input.checked;
        }

        if (input.type === 'radio') {
            const form = input.closest('form');
            const container = form ? form : document;

            return Boolean(container.querySelector(`input[name="${input.name}"]:checked`));
        }

        return Boolean(input.value);
    }

    private _validateMask(input: HTMLInputElement): boolean {
        if (this._checkIfEmptyNotRequired(input)) {
            return true;
        }

        return input.inputmask!.isComplete();
    }

    private _validateRegexp(input: HTMLInputElement): boolean {
        if (this._checkIfEmptyNotRequired(input)) {
            return true;
        }

        const attr = input.dataset['validateRegexp'] || '';
        const reArray = attr.split('/');

        if (attr[0] !== '/' || reArray.length !== 3) {
            throw new Error('Не верный формат regexp');
        }

        return new RegExp(String(reArray[1]), reArray[2]).test(input.value);
    }

    private _validateMin(input: HTMLInputElement): boolean {
        if (this._checkIfEmptyNotRequired(input)) {
            return true;
        }

        const min = input.dataset['validateMin'];

        if (!Number(min)) {
            console.error('Attribute "data-validate-min" is not number');

            return false;
        }

        return Number(input.value.length) >= Number(min);
    }

    private _validateMax(input: HTMLInputElement): boolean {
        const max = input.dataset['validateMax'];

        if (!Number(max)) {
            console.error('Attribute "data-validate-max" is not number');

            return false;
        }

        return Number(input.value.length) <= Number(max);
    }

    private _validateEmail(input: HTMLInputElement): boolean {
        if (this._checkIfEmptyNotRequired(input)) {
            return true;
        }

        return this.options.emailReg.test(input.value.toLowerCase());
    }

    private _validateNumber(input: HTMLInputElement): boolean {
        if (this._checkIfEmptyNotRequired(input)) {
            return true;
        }

        return this.options.numberReg.test(input.value);
    }

    private _validateLetters(input: HTMLInputElement): boolean {
        if (this._checkIfEmptyNotRequired(input)) {
            return true;
        }

        return this.options.lettersReg.test(input.value);
    }

    private _validateUrl(input: HTMLInputElement): boolean {
        if (this._checkIfEmptyNotRequired(input)) {
            return true;
        }

        return this.options.urlReg.test(input.value);
    }

    /**
     * Успешная валидация, убираем вывод ошибок
     * @param {Node} input - Элемент инпут
     * @param {Node} error - Элемент вывода ошибки
     */
    private _inputSuccess(input: HTMLElement, error: HTMLElement | null): void {
        if (error) {
            this._hideError(error);
        }

        this._setSuccessClassInput(input);
    }

    /**
     * Неудачная валидация, выводим ошибки
     * @param {HTMLElement} input - Элемент инпут
     * @param {HTMLElement | null} error - Элемент вывода ошибки
     * @param {IValidateInputResult} validate - Инфо об ошибки
     */
    private _inputError(input: HTMLElement, error: HTMLElement | null, validate: IValidateInputResult): void {
        if (error) {
            this._replaceTextError(input, error, validate);
            this._showError(error);
        } else {
            console.warn('Element for display error not found!');
        }

        this._setErrorClassInput(input as HTMLInputElement);
    }

    /**
     * Делает замену текста ошибки (подставляет значение с min, max)
     * @param {Node} input - Элемент инпут
     * @param {Node} error - Элемент вывода ошибки
     * @param {object} validate - Инфо об ошибки
     */
    private _replaceTextError(input: HTMLElement, error: HTMLElement, validate: IValidateInputResult): void {
        if (!validate.error?.length || !validate.message) {
            return;
        }

        const errorName = `${validate.error[0]?.toUpperCase()}${validate.error.slice(1)}`;
        const value = input.dataset[`validate${errorName}`];
        const message = value ? validate!.message.replace(/\$/gi, value) : validate.message;

        error.innerHTML = `${message}`;
    }

    private _hideError(error: HTMLElement): void {
        error.style.display = 'none';
    }

    private _showError(error: HTMLElement): void {
        error.style.display = 'block';
    }

    private _setSuccessClassInput(input: HTMLElement): void {
        input.classList.remove(this.options.inputError);
        input.classList.add(this.options.inputSuccess);

        input.setAttribute('validity', '');
    }

    private _setErrorClassInput(input: HTMLInputElement): void {
        input.classList.remove(this.options.inputSuccess);
        input.classList.add(this.options.inputError);

        input.removeAttribute('validity');
    }

    private _checkValidateClass(input: HTMLInputElement): boolean {
        return input.classList.contains(this.options.inputError) || input.classList.contains(this.options.inputSuccess);
    }

    private _setValidationClass(input: HTMLInputElement, validate: IValidateInputResult): void {
        const form = this.forms[0];

        if (form?.classList.contains(this.options.formError)) {
            input.classList.remove(this.options.inputSuccess);
            const name = input.dataset['validateName'];
            const error = document.querySelector(`[data-validate-error="${name}"]`);

            this._inputError(input, error as HTMLElement, validate);
        } else {
            input.classList.remove(this.options.inputSuccess);
            input.classList.remove(this.options.inputError);
        }
    }

    /**
     * При успешной валидации
     * @param {Node} form - элемент формы
     */
    private _formSuccess(form: HTMLElement): void {
        form.classList.remove(this.options.formError);
        form.classList.add(this.options.formSuccess);
        form.setAttribute('validity', '');
        observer.publish('formIsValid', form);
    }

    /**
     * При ошибках в валидации
     * @param {Node} form - элемент формы
     */
    private _formError(form: HTMLElement): void {
        form.classList.remove(this.options.formSuccess);
        form.classList.add(this.options.formError);
        form.removeAttribute('validity');
    }
}

export default Validation;
