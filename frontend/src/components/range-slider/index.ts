/* eslint-disable camelcase */
import type {
    IIonRangeSliderInstance,
    IIonRSUpdateOptions,
    IRangeCSliderOptions,
    IRangeSlider,
    IRangeSliderData,
    IRangeSliderOptions,
    IRangeSliderOuterOptions} from './types';
import type {IObserver} from '@/common/scripts/types/observer';
import IonRangeSlider from '../../../libs/vanilla-rangeslider-master/js/rangeslider';
import Observer from '@/common/scripts/observer';
import {Utils} from '@/common/scripts/utils';

const observer: IObserver = new Observer();

class RangeSlider implements IRangeSlider {
    public wrapper: HTMLElement;
    public slider: HTMLElement;
    public input: HTMLInputElement;
    public oldFrom: number = 0;
    public ionRangeSliderInstance: IIonRangeSliderInstance;
    public options: IRangeSliderOptions;
    private outerOptions: IRangeSliderOuterOptions | DOMStringMap | undefined;

    constructor(options: IRangeCSliderOptions) {
        this.wrapper = options.wrapper;

        this.slider = this.wrapper.querySelector('.j-range-slider__base') as HTMLElement;
        this.input = this.wrapper.querySelector('.j-range-slider__input') as HTMLInputElement;
        this.outerOptions = options.outerOptions ?? this.slider.dataset;
    }

    /**
     * Инициализация слайдера
     */
    public init(): void {
        this._setOptions();
        this._initRangeSlider();
        this._subscribes();
        this._bindEvents();
        this._initDigit();
    }

    /**
     * Установка параметров
     */
    private _setOptions(): void {
        this.options = {
            type            : this.outerOptions?.type || null,
            min             : Number(this.outerOptions?.min) || 0,
            max             : Number(this.outerOptions?.max) || 0,
            from            : Number(this.outerOptions?.from) || 0,
            to              : Number(this.outerOptions?.to) || 0,
            step            : Number(this.outerOptions?.step) || null,
            min_interval    : Number(this.outerOptions?.step) || null,
            digit           : Boolean(this.outerOptions?.digit) || false,
            disable         : Boolean(this.outerOptions?.disable) || false,
            validate        : this.outerOptions?.validate,
            hide_from_to    : true,
            hide_min_max    : true,
            prettify_enabled: true,
            onStart         : (data: IRangeSliderData) => {
                observer.publish('rangeSlider:start', data, this);
            },
            onChange: (data: IRangeSliderData) => {
                this._updateInputs(data);
                observer.publish('rangeSlider:change', data);
            },
            onFinish: (data: IRangeSliderData) => {
                observer.publish('rangeSlider:finish', data);
            },
            onUpdate: (data: IRangeSliderData) => {
                this._updateInputs(data);
                observer.publish('rangeSlider:change', data);
            }
        };
    }

    /**
     * Метод инициализирует плагин range-слайдера
     */
    private _initRangeSlider(): void {
        if (this.slider) {
            // @ts-ignore
            this.ionRangeSliderInstance = new IonRangeSlider(this.slider, this.options);

            this.ionRangeSliderInstance.update({
                max : this.outerOptions!.max,
                from: this.outerOptions!.from,
                to  : this.outerOptions!.to
            });
        }
    }

    private _subscribes(): void {
        observer.subscribe('ionRangeSlider:reset', () => {
            this.ionRangeSliderInstance.reset();
        });
    }

    /**
     * Навешиваем события
     */
    private _bindEvents(): void {
        if (!this.input) {
            return;
        }

        this.input.addEventListener('change', () => {
            this._onInputFromChange();
        });

        this.input.addEventListener('input', (event: Event) => {
            const target = event.target as HTMLInputElement;
            const value = target.value;

            this._validate(target);
            this._setDigit(target, value);
        });
    }

    /**
     * Изменение инпута "от"
     */
    private _onInputFromChange(): void {
        // конвертируем в число, т.к после добавления разрядов получаем строку
        let value = this._getNumber(this.input.value);
        const currentTo = Number(this.slider.dataset['to']);

        if (value < this.options.min) {
            value = this.options.min;
        } else if (value > currentTo) {
            value = this.options.max;
        }

        this.input.value = value.toString();

        // Конвертируем в число с разрядами
        this._setDigit(this.input, value);
        this._update({
            from: value
        });
    }

    /**
     * Выбирает метод валидации.
     * @param {Object} target - дом-элемент слайдера
     */
    private _validate(target: HTMLInputElement): void {
        const type = this.options.validate;

        if (!type) {
            return;
        }

        switch (type) {
            case 'number':
                this._validateNumber(target);
                break;
            case 'number_decimal':
                this._validateNumberDecimal(target);
                break;
            default:
                break;
        }
    }

    /**
     * Запускает валидацию только по числам
     * @param {Object} target - дом-элемент слайдера
     */
    private _validateNumber(target: HTMLInputElement): void {
        const regex = /\d*/;
        const result = regex.exec(target.value);

        if (result && typeof result[0] === 'string') {
            target.value = result[0];
        }
    }

    /**
     * Запускает валидацию только по целым и числам с плавающей точкой
     * @param {Object} target - дом-элемент слайдера
     */
    private _validateNumberDecimal(target: HTMLInputElement): void {
        const regex = /\d*\.?\d?/g;
        const result = regex.exec(target.value);

        if (result && typeof result[0] === 'string') {
            target.value = result[0];
        }
    }

    private _update(data: Partial<IIonRSUpdateOptions>): void {
        this.ionRangeSliderInstance.update(data);
    }

    /**
     * Обновление инпутов
     * @param {Object} data - данные рэйндж-слайдера
     */
    private _updateInputs(data: IRangeSliderData): void {
        this._setDigit(this.input, data.from);
        this.oldFrom = data.from;
    }

    /**
     * Метод инициализирует разряды для значений инпута
     */
    private _initDigit(): void {
        if (this.options.digit && this.input) {
            this._setDigit(this.input);
        }
    }

    /**
     * Метод устанавливает разряды для значений инпута
     * @param {Object} input - изменяемый инпут
     * @param {string} value - значение инпута, при перемещении ручек приходит от слайдера.
     */
    private _setDigit(input: HTMLInputElement, value: string | number = ''): void {
        const type = this.options.digit;

        if (!type) {
            return;
        }

        const data = value ? value : input.value;
        const number = this._getNumber(data.toString()).toString();

        input.value = Utils.convertToDigit(number);
    }

    /**
     * Конвертация из строки с разрядами в число
     * @param {string} str - значение с разрядом
     * @returns {number} - значение без разрядов
     */
    private _getNumber(str: string): number {
        if (!str) {
            return 0;
        }

        return Utils.convertToNumber(str);
    }
}

export default RangeSlider;
