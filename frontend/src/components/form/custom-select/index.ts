import type {ICustomSelect, ICustomSelectCheckOptions, ICustomSelectOptions} from './types';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';
import {Utils} from '@/common/scripts/utils';

const observer: IObserver = new Observer();
const SUMMARY_PREFIX: [string, string, string] = ['Выбран', 'Выбрано', 'Выбрано'];
const CLASS_OPEN = 'is-open';
const CLASS_CHECKED = 'is-checked';
const CLASS_DISABLED = 'is-disabled';

enum ECustomSelectSelectors {
    CHECK = '.j-custom-select__checkbox',
    GROUP = '.j-custom-select__group',
    GROUP_CHECK = '.j-custom-select__group-checkbox',
    GROUP_LABEL = '.j-custom-select__group-label',
}

class CustomSelect implements ICustomSelect {
    public readonly element: HTMLElement;
    private options: ICustomSelectOptions;

    private isOpen: boolean = false;

    private isSingleSelect: boolean = false;

    private isMobile: boolean = Utils.isMobile();

    private placeholder: string;

    private prefix: string;
    private suffix: string;
    private valueType: string;
    // ELEMENTS
    private input: HTMLElement;
    private content: HTMLElement;
    private value: HTMLElement;
    private checks: Element[];
    private groups: Element[];
    private groupChecks: Element[];
    private groupLabels: Element[];
    private itemsWrap: HTMLElement;

    constructor(options: ICustomSelectOptions) {
        this.options = options;
        this.element = options.element;

        if (!this.element) {
            console.error(`Element with selector "${options.element}" not found!`);

            return;
        }

        this._clickOutside = this._clickOutside.bind(this);
    }

    public init(): void {
        if (!this.element) {
            return;
        }

        this._toggleFix = this._toggleFix.bind(this);
        this._initElements();
        this._getDataset();

        if (this.element.classList.contains(CLASS_OPEN)) {
            this.open();
        }

        if (this.element.classList.contains('j-custom-select_single_select')) {
            this.isSingleSelect = true;
        }

        this._bindEvents();
        this._changeValue();
    }

    /**
     * Открыть выпадающий список
     */
    public open(): void {
        document.addEventListener('click', this._clickOutside);
        this.isOpen = true;
        this.element.classList.add(CLASS_OPEN);

        if (!this.isMobile) {
            this.content.style.height = `${this.content.scrollHeight}px`;
        }

        this._fixBody();
    }

    /**
     * Закрыть выпадающий список
     */
    public close(): void {
        document.removeEventListener('click', this._clickOutside);
        this.isOpen = false;

        if (!this.isMobile) {
            this.content.style.height = '0px';
            setTimeout(() => {
                this.content.style.height = '';
            }, 300);
        }

        this.element.classList.remove(CLASS_OPEN);
        this._unfixBody();
    }

    /**
     * Обновляет селект снаружи
     * @param {boolean} reset - true - полный сброс
     */
    public update(reset: boolean = false): void {
        this._updateGroups(reset);
        this._changeValue();
    }

    /**
     * Возвращает список чекбоксов в формате объекта
     * @return {{checked: boolean, disabled: boolean, value: string}[]} - чекнут ли, задисейблен ли, значение
     */
    public getOptions(): ICustomSelectCheckOptions[] {
        return this.checks.map((check: Element) => {
            return {
                value   : (check as HTMLInputElement).value,
                checked : Boolean((check as HTMLInputElement).checked),
                disabled: Boolean((check as HTMLInputElement).disabled)
            };
        });
    }

    /**
     * Возвращает список неотмеченных чекбоксов в выбранных группах
     * @return {object} data - объект с чекбоксами в формате name: value
     */
    public getGroupOptions(): Record<string, string | string[]> {
        const data: Record<string, string | string[]> = {};

        this.groups.forEach((group: Element) => {
            const groupLabel = group.querySelector(`${ECustomSelectSelectors.GROUP_LABEL}.${CLASS_CHECKED}`);

            if (this._isGroupEmpty(group as HTMLElement) && groupLabel) {
                Array.from(group.querySelectorAll(`${ECustomSelectSelectors.CHECK}:not(:disabled)`))
                    .forEach((check: Element) => {
                        const name: string = (check as HTMLInputElement).getAttribute('name') || '';
                        const value: string = (check as HTMLInputElement).getAttribute('value') || '';

                        if (name && name in data) {
                            let prop = data[name];

                            if (prop) {
                                if (Array.isArray(prop)) {
                                    prop.push(value);
                                } else {
                                    prop = [prop, value];
                                }
                            }
                        } else {
                            data[name] = value;
                        }
                    });
            }
        });

        return data;
    }

    /**
     * Инит элементов
     * @private
     */
    private _initElements(): void {
        this.input = this.element.querySelector('.j-custom-select__input') as HTMLElement;
        this.content = this.element.querySelector('.j-custom-select__content') as HTMLElement;
        this.value = this.element.querySelector('.j-custom-select__value') as HTMLElement;
        this.checks = [...this.element.querySelectorAll(ECustomSelectSelectors.CHECK)];
        this.groups = [...this.element.querySelectorAll(ECustomSelectSelectors.GROUP)];
        this.groupChecks = [...this.element.querySelectorAll(ECustomSelectSelectors.GROUP_CHECK)];
        this.groupLabels = [...this.element.querySelectorAll(ECustomSelectSelectors.GROUP_LABEL)];
        this.itemsWrap = this.element.querySelector('.j-custom-select__items') as HTMLElement;
    }

    /**
     * Берет данные из data-атрибутов
     * @private
     */
    private _getDataset(): void {
        // eslint-disable-next-line @typescript-eslint/typedef
        const {placeholder, prefix, suffix, valueType} = this.element.dataset;

        this.placeholder = placeholder || '';
        this.prefix = prefix || '';
        this.suffix = suffix || '';
        this.valueType = valueType || '';
    }

    /**
     * Вешает обработчики событий
     * @private
     */
    private _bindEvents(): void {
        const events = ['resize', 'orientationchange'];

        events.forEach((event: string) => {
            window.addEventListener(event, this._onResize.bind(this));
        });

        this.input.addEventListener('click', () => {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        });

        // Чек опшенов
        this.checks.forEach((check: Element) => {
            (check as HTMLElement).addEventListener('change', () => {
                if (this.isSingleSelect) {
                    this._emptyChecks(check as HTMLElement);
                    this._changeValue();
                    this.close();
                    observer.publish('custom-select:close', this);

                    if (this.options.selectCallback) {
                        this.options.selectCallback(this.value);
                    }
                } else {
                    this._checkInsideGroup(check as HTMLElement);
                    this._changeValue();
                }
            });
        });

        // Чек заголовков группы
        this.groupChecks.forEach((check: Element) => {
            (check as HTMLElement).addEventListener('change', () => {
                this._checkGroup(check as HTMLElement);
                this._changeValue();
            });
        });

        // Клик по псевдочеку группы
        this.groupLabels.forEach((label: Element) => {
            (label as HTMLElement).addEventListener('click', () => {
                label.classList.toggle(CLASS_CHECKED);
                this._checkGroup(label as HTMLElement);
                this._changeValue();

                if (this.options.onGroupClick) {
                    this.options.onGroupClick();
                }
            });
        });
    }

    private _onResize(): void {
        this.isMobile = Utils.isMobile();
    }

    private _fixBody(): void {
        if (!this.isMobile) {
            return;
        }

        Utils.bodyStatic();
        Utils.bodyFixed(this.itemsWrap);
        this._bindResize();
    }

    private _unfixBody(): void {
        if (!this.isMobile) {
            return;
        }

        Utils.bodyStatic();
        this._unbindResize();
    }

    /**
     * Вешает обработчики на изменение размеров окна и изменение ориентации устройства
     * @private
     */
    private _bindResize(): void {
        window.addEventListener('resize', this._toggleFix);
        window.addEventListener('orientationchange', this._toggleFix);
    }

    /**
     * Снимает ресайз-обработчики
     * @private
     */
    private _unbindResize(): void {
        window.removeEventListener('resize', this._toggleFix);
        window.removeEventListener('orientationchange', this._toggleFix);
    }

    private _toggleFix(): void {
        if (!this.isMobile && this.isOpen) {
            this._unfixBody();
        }
    }

    /**
     * Ловит клик снаружи элемента
     * @param {object} event - объект события клика
     * @private
     */
    private _clickOutside(event: Event): void {
        if (!event?.target) {
            return;
        }

        if (!Utils.closest(event.target as HTMLElement, '.j-custom-select')) {
            this.close();
        }
    }

    /**
     * Клик по чекбоксу группы
     * @param {Element} groupCheck - элемент чекбокса
     * @private
     */
    private _checkGroup(groupCheck: HTMLElement): void {
        const checked = (groupCheck as HTMLInputElement).checked ||
            (groupCheck.classList.contains(CLASS_CHECKED) && !groupCheck.classList.contains(CLASS_DISABLED));
        const group = groupCheck.closest(ECustomSelectSelectors.GROUP);

        this.checks.forEach((check: Element) => {
            if ((check as HTMLElement).closest(ECustomSelectSelectors.GROUP) === group) {
                (check as HTMLInputElement).checked = checked;
            }
        });
    }

    /**
     * Если ли у группы внутри что-то чекнутое
     * @param {Element} group - группа
     * @return {boolean} - true - внутри ничего не отмечено
     * @private
     */
    private _isGroupEmpty(group: HTMLElement): boolean {
        return !Array.from(group.querySelectorAll(`${ECustomSelectSelectors.CHECK}:checked`)).length;
    }


    /**
     * Оставляет чекнутым только тот чекбокс, который выбираем
     * @param {Element} check - элемент чекбокса
     * @private
     */
    private _emptyChecks(check: HTMLElement): void {
        const checkedArr = [...this.element.querySelectorAll(`${ECustomSelectSelectors.CHECK}:checked`)];

        if (checkedArr.length > 1) {
            checkedArr.filter((item: Element) => {
                // eslint-disable-next-line no-return-assign
                return (item as HTMLInputElement).checked = item === check;
            });
        }
    }

    /**
     * Обрабатывает группу вложенного чекбокса
     * @param {Element} check - элемент чекбокса
     * @private
     */
    private _checkInsideGroup(check: HTMLElement): void {
        const group = check.closest(ECustomSelectSelectors.GROUP);

        if (!group) {
            return;
        }

        const isGroupChecked = !this._isGroupEmpty(group as HTMLElement);
        const label = group.querySelector(ECustomSelectSelectors.GROUP_LABEL);

        if (label) {
            label.classList.toggle(CLASS_CHECKED, isGroupChecked);

            return;
        }

        (group.querySelector(ECustomSelectSelectors.GROUP_CHECK) as HTMLInputElement).checked = isGroupChecked;
    }

    /**
     * Обновляет лейблы групп в зависимости от того, что происходит с чекбоксами у них внутри
     * @param {boolean} reset - true - сбросить все
     * @private
     */
    private _updateGroups(reset: boolean): void {
        this.groups.forEach((group: Element) => {
            const groupLabel = group.querySelector(ECustomSelectSelectors.GROUP_LABEL);

            if (!groupLabel) {
                return;
            }

            groupLabel.classList.remove(CLASS_DISABLED);

            if (reset) {
                groupLabel.classList.remove(CLASS_CHECKED);

                return;
            }


            if (!this._isGroupEmpty(group as HTMLElement)) {
                groupLabel.classList.add(CLASS_CHECKED);

                return;
            }

            const len = Array.from(group.querySelectorAll(ECustomSelectSelectors.CHECK)).length;
            const disabledLen = Array.from(group.querySelectorAll(`${ECustomSelectSelectors.CHECK}:disabled`)).length;

            if (len === disabledLen) {
                groupLabel.classList.add(CLASS_DISABLED);
                groupLabel.classList.remove(CLASS_CHECKED);
            }
        });
    }

    /**
     * Выводит список выбранного
     * @private
     */
    private _changeValue(): void {
        const text = this.valueType === 'summary' ? this._getSummary() : this._collectText();

        this._insertTextValue(text);
    }

    /**
     * Возвращает строку-список выбранного в формате "Выбрано Х элементов"
     * НЕ ДОРАБОТАНО ДЛЯ ГРУПП ЧЕКБОКСОВ !!!
     * @return {string} - итоговая строка
     * @private
     */
    private _getSummary(): string {
        const len = Array.from(this.element.querySelectorAll(`${ECustomSelectSelectors.CHECK}:checked`)).length;

        if (!len) {
            return this.placeholder;
        }

        const suffix = this.suffix ?
            Utils.pluralWord(len, this.suffix?.split('|') as [string, string, string]) :
            '';

        return `${Utils.pluralWord(len, SUMMARY_PREFIX)} ${len} ${suffix}`;
    }

    /**
     * Собирает список выбранного из названий (через запятую)
     * @return {string} - итоговая строка
     * @private
     */
    private _collectText(): string {
        let finalText = '';

        if (this.groups.length) {
            finalText = this._collectGroupText();
        } else {
            for (let i = 0; i < this.checks.length; i++) {
                const check = this.checks[i];

                if ((check as HTMLInputElement).checked && (check as HTMLInputElement).value === 'reset') {
                    finalText = '';

                    return this.placeholder;
                }

                finalText = this._getItemsText(check as HTMLElement, finalText);
            }
        }

        finalText = finalText.substr(2);

        if (!finalText) {
            return this.placeholder;
        }

        return `${this.prefix ? `${this.prefix} ` : ``}${finalText}`;
    }

    /**
     * Собирает список выбранного по группе
     * @return {string} - итоговая строка
     * @private
     */
    private _collectGroupText(): string {
        let finalText = '';

        this.groups.forEach((group: Element) => {
            const elem = group.querySelector(ECustomSelectSelectors.GROUP_LABEL) ||
                group.querySelector(ECustomSelectSelectors.GROUP_CHECK);

            if (!(elem as HTMLElement).classList.contains(CLASS_CHECKED) && !(elem as HTMLInputElement).checked) {
                return;
            }

            const checkedList = Array.from(group.querySelectorAll(`${ECustomSelectSelectors.CHECK}:checked`));
            const targetList = checkedList.length > 0 ?
                checkedList :
                Array.from(group.querySelectorAll(ECustomSelectSelectors.CHECK));

            targetList.forEach((check: Element) => {
                finalText = this._getItemsText(check as HTMLElement, finalText, checkedList.length === 0);
            });
        });

        return finalText;
    }

    /**
     * Получает текст чекбокса для вывода в строку результатов
     * @param {Element} item - элемент чекбокса
     * @param {string} str - строка, к которой нужно приклеить текст чекбокса
     * @param {boolean} forceGet - true - учитывать чекбокс даже если он не чекнут
     * @return {string|*} - итоговая строка
     * @private
     */
    private _getItemsText(item: HTMLElement, str: string, forceGet: boolean = false): string {
        if (!(item as HTMLInputElement).checked && !forceGet) {
            return str;
        }

        const dataText = item.dataset['text'];
        let itemText = `, ${dataText}`;

        if (!this.suffix) {
            return str + itemText;
        }

        let finalText = str;

        // Первый символ отмеченного - цифра
        // Подходит для комнат/этажей
        if (!isNaN(Number(dataText?.slice(0, 1)))) {
            finalText = str.endsWith(this.suffix) ? str.slice(0, str.length - this.suffix.length) : str;
            itemText = itemText + this.suffix;
        }

        return `${finalText}${itemText}`;
    }

    /**
     * Подставляет текстовое значение в инпут с результатами выбора
     * @param {string} text - текст, который нужно вставить
     * @private
     */
    private _insertTextValue(text: string): void {
        Utils.clearHtml(this.value);
        Utils.insetContent(this.value, text);

        this.value.innerText === this.placeholder ?
            this.element.classList.remove('is-selected') :
            this.element.classList.add('is-selected');
    }
}

export default CustomSelect;
