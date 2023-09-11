import 'url-search-params-polyfill';
import type {IMortgageCalculator, IMortgageUtils} from './types';
import type {IRangeSlider, IRangeSliderData} from '@/components/range-slider/types';
import type {IObserver} from '@/common/scripts/types/observer';
import MortgageUtils from './utils';
import Observer from '@/common/scripts/observer';
import RangeSlider from '@/components/range-slider';
import {Utils} from '@/common/scripts/utils';

const CLASS_ERROR = 'is-error';
const CLASS_DISABLED = 'is-disabled';
const CLASS_HIDDEN = 'is-hidden';
const observer: IObserver = new Observer();
const U: IMortgageUtils = new MortgageUtils();

class MortgageCalculator implements IMortgageCalculator {
    private wrapper: HTMLElement;
    private minMonthlyPayment: string = '0';
    private rangeSliders: IRangeSlider[] = [];
    private sliders: Element[];
    private resultHeading: HTMLElement;
    private programResult: HTMLElement;
    private programNoResult: HTMLElement;
    private programs: Element[];
    private mortgageProgramsCount: HTMLElement;
    private mortgageAmount: HTMLElement;
    private mortgagePercent: HTMLElement;
    private resetFilters: HTMLElement | null;
    private flatPrice: number | null = null;
    private queryData: Record<string, string> | false;

    private priceSlider: IRangeSlider | undefined;
    private firstPaymentSlider: IRangeSlider | undefined;
    private limitationSlider: IRangeSlider | undefined;

    constructor(wrapper: HTMLElement) {
        this.wrapper = wrapper;

        this._setInitialElements();
    }

    /**
     * Записываем в инпуты значения слайдеров.
     * Добавляем разрядные пробелы и постфикс для срока кредита
     * @param {number} value - значение слайдера
     * @param {Object} data - параметры слайдера
     */
    static setInput(value: number, data: IRangeSliderData): void {
        data.input.value = value.toLocaleString('ru-Ru');
    }

    /**
     * Рассчитываем ежемесячный платёж по формуле:
     * @param {number} rate - ставка
     * @return {number} - ежемесячный платёж
     * A = K * S,
     * где A - ежемесячный аннуитетный платёж,
     * K - коэффициент аннуитета,
     * S - сумма кредита.
     * K = (i * (1 + n)^n) / (((1 + i)^n) - 1)
     * i - месячная процентная ставка по кредиту (годовая/12)
     * n - количество периодов, в течение которых выплачивается кредит
     */
    static calcMonthlyPay(rate: number): number | '—' {
        const i = rate / 12 / 100;
        const n = U.getAccessStorage('limitation[min]') * 12;
        const kNumerator = i * Math.pow(1 + i, n);
        const kDenominator = Math.pow(1 + i, n) - 1;
        const _K = kNumerator / kDenominator;
        const _S = U.getAccessStorage('creditAmount');
        const _A = _K * _S;

        return _A < 0 ? '—' : _A;
    }

    /**
     * Метод инициализирует модуль
     */
    public init(): void {
        this._subscribes();
        this._initCalcSliders();
        this._bindEvents();
        this._setMinimalPayment();
        this._setShareParam(false);
    }

    /**
     * Обновление значений экземпляра слайдера и запись в хранилище сессии при необходимости
     * @param {Object} data - данные слайдеров
     * @param {'set' | undefined} flag - флаг, показывающий, нужно ли записывать данные слайдеров в хранилище сессии
     */
    public updateSlidersValues(data: IRangeSliderData, flag?: 'set'): void {
        this._updateData(data, flag);
        MortgageCalculator.setInput(Number(data.from), data);
        this._setCredit();
    }

    /**
     * Функция обновления значений ипотечной таблицы, карточек и количества предложений
     */
    public update(): void {
        this._setPaymentOnTable();
        this._disableItems();
        this._setOrdersCount();
        this._checkEmptyResult();
        this._setOrder();
        this._setMinimalPayment();
    }

    /**
     * Метод находит необходимые DOM-элементы для работы калькуляторы
     */
    private _setInitialElements(): void {
        this.sliders = [...this.wrapper.querySelectorAll('.j-range-slider')] || [];
        this.resultHeading = this.wrapper.querySelector('.j-mortgage-calculator__mortgage-heading') as HTMLElement;
        this.programResult = this.wrapper.querySelector('.j-mortgage-calculator__programs-result') as HTMLElement;
        this.programNoResult = this.wrapper.querySelector('.j-mortgage-calculator__programs-no-result') as HTMLElement;
        this.programs = [...this.wrapper.querySelectorAll('.j-mortgage-calculator__program')] || [];
        this.mortgageProgramsCount = this.wrapper.querySelector('.j-mortgage-calculator__orders-count') as HTMLElement;
        this.mortgageAmount = this.wrapper.querySelector('.j-mortgage-calculator__mortgage-amount') as HTMLElement;
        this.mortgagePercent = this.wrapper.querySelector('.j-mortgage-calculator__percent') as HTMLElement;
        this.resetFilters = this.wrapper.querySelector('.j-mortgage-calculator__reset') as HTMLElement || null;
    }

    /**
     * Инициализация слайдеров ипотечного калькулятора
     * @private
     */
    private _initCalcSliders(): void {
        this.sliders.forEach((slider: Element) => {
            this._initSlider(slider as HTMLElement);
        });

        this._setSlidersElements();
        this._correctFirstPayment();

        // Устанавливаем актуальную сумму квартиры при инициализации
        this.flatPrice = this.priceSlider?.oldFrom || null;

        this._setGetQueryValue();
    }

    private _initSlider(slider: HTMLElement): void {
        const rangeSlider: IRangeSlider = new RangeSlider({
            wrapper: slider
        });

        rangeSlider.init();

        this.rangeSliders.push(rangeSlider);
    }

    /**
     * Метод содержит в себе колбэки на событиях других модулей
     * @private
     */
    private _subscribes(): void {
        observer.subscribe('rangeSlider:start', (data: IRangeSliderData, rangeSlider: IRangeSlider) => {
            data.input = rangeSlider.input;
            data.rangeSlider = rangeSlider;
            this.updateSlidersValues(data);
            this._manualInput(data);
            this.update();
        });

        observer.subscribe('rangeSlider:change', (data: IRangeSliderData) => {
            this.updateSlidersValues(data, 'set');
            this.update();
        });

        observer.subscribe('rangeSlider:finish', () => {
            this._setCredit();
            this._setShareParam(true);
        });
    }

    private _bindEvents(): void {
        if (this.resetFilters) {
            this.resetFilters.addEventListener('click', () => {
                this._resetData();
            });
        }
    }

    private _resetData(): void {
        observer.publish('ionRangeSlider:reset');
        this.update();
        window.history.pushState(null, '', window.location.pathname);
    }

    /**
     * Считываем из хранилища или записываем в хранилище данные о текущем положении ползунков
     * @param {Object} data - все значения слайдера
     * @param {string} flag - флаг, указывающий, что данные нужно записать в хранилище
     */
    private _updateData(data: IRangeSliderData, flag: 'set' | undefined): void {
        const key = data.input.getAttribute('name');

        if (!key) {
            return;
        }

        const isDisabled = data.input.disabled;
        const getParam = Utils.getUrlParams(key);

        if (getParam && parseInt(getParam) && flag !== 'set' && !isDisabled) {
            data.from = parseInt(getParam);
        } else {
            data.from = parseInt(`${data.from}`);
        }
        U.saveToStorage(key, data.from);
    }

    /**
     * Выводим сумму и процент кредита
     * Если процент больше 100, выделяем его красным, а сумму кредита не выводим
     */
    private _setCredit(): void {
        const creditData = U.calcCredit();
        const method = parseInt(creditData.percent) >= 90 ? 'add' : 'remove';

        this.mortgageAmount.innerHTML = creditData.amount;
        this.mortgagePercent.innerHTML = creditData.percent;
        this.mortgagePercent.classList[method](CLASS_ERROR);
    }

    /**
     * Метод для непосредственного взаимодействия с инпутами (без использования слайдеров)
     * @param {Object} data - все значения слайдера
     */
    private _manualInput(data: IRangeSliderData): void {
        this._chooseInputInteraction(data, 'focus');
        this._chooseInputInteraction(data, 'blur');
        this._chooseInputInteraction(data, 'keyup');
        this._chooseInputInteraction(data, 'keydown');
    }

    /**
     * Определение поведения инпутов и слайдеров при взаимодействии с инпутами
     * @param {Object} data - все значения слайдера
     * @param {string} interaction - тип взаимодействия с инпутом
     */
    private _chooseInputInteraction(data: IRangeSliderData, interaction: string): void {
        const onlyNumbersRegexp = /[^0-9.]/g;
        const that = this;

        /* eslint-disable */
        data.input.addEventListener(interaction, function eventListener(event: Event) {
            // @ts-ignore
            const input = this as HTMLInputElement;

            switch (interaction) {
                case 'focus':
                    input.value = '';
                    break;
                case 'blur':
                    if (input.value === '') {
                        MortgageCalculator.setInput(Number(data.from), data);
                    } else {
                        U.checkInputData(input, data, onlyNumbersRegexp);
                        that.updateSlidersValues(data, 'set');
                        data.rangeSlider?.ionRangeSliderInstance?.update();
                        that.update();
                    }
                    break;
                case 'keyup':
                    if (input.value) {
                        MortgageCalculator.setInput(Number(input.value.replace(onlyNumbersRegexp, '')), data);
                    }
                    break;
                case 'keydown':
                    U.preventNaNSymbolsEnter(event as KeyboardEvent);
                    U.onKeyDownEvent(event as KeyboardEvent, data, that, input, onlyNumbersRegexp);
                    break;
                default: console.error('Wrong type of interaction');
            }
        });
        /* eslint-enable */
    }

    /**
     * Высчитываем и устанавливаем ежемесячный платёж каждому банку
     */
    private _setPaymentOnTable(): void {
        this.programs.forEach((item: Element) => {
            this._calcPaymentForBank(item as HTMLElement);
        });
    }

    private _calcPaymentForBank(item: HTMLElement): void {
        const rate = Number((item.querySelector('.j-mortgage-calculator__rate') as HTMLElement)!.dataset['rate']);
        const paymentElement = item.querySelector('.j-mortgage-calculator__payment') as HTMLElement;

        if (!paymentElement) {
            return;
        }

        paymentElement.innerHTML =
            U.isExist(MortgageCalculator.calcMonthlyPay(rate)) ?
                // @ts-ignore
                U.calcString('monthlyPaymentPretty', MortgageCalculator.calcMonthlyPay(rate), null) :
                '—';

        paymentElement.dataset['payment'] =
            U.isExist(MortgageCalculator.calcMonthlyPay(rate)) ?
                // @ts-ignore
                `${parseInt(MortgageCalculator.calcMonthlyPay(rate).toFixed())}` :
                '—';
    }

    /**
     * Передаём список банков на проверку соответствия условиям
     * @private
     */
    private _disableItems(): void {
        this.programs.forEach((item: Element) => {
            this._setDisabled(item as HTMLElement);
        });
    }

    /**
     * Отключаем банк, если он не подходит по параметрам
     * @param {Object} item - DOM элемент программы
     * @private
     */
    private _setDisabled(item: HTMLElement): void {
        const monthlyPayment = item.querySelector('.j-mortgage-calculator__payment') as HTMLElement;
        const firstPaymentCell = item.querySelector('.j-mortgage-calculator__first-payment') as HTMLElement;
        const firstPayment = Number(firstPaymentCell.dataset['firstPayment']);
        const firstPaymentMax = Number(firstPaymentCell.dataset['firstPaymentMax']);
        const limitationCell = item.querySelector('.j-mortgage-calculator__limitation') as HTMLElement;
        const limitation = Number(limitationCell.dataset['limitation']);
        const limitationMin = Number(limitationCell.dataset['limitationMin']);

        if (U.getAccessStorage('creditPercent') < firstPayment ||
            U.getAccessStorage('creditPercent') > firstPaymentMax ||
            U.getAccessStorage('limitation[min]') > limitation ||
            U.getAccessStorage('limitation[min]') < limitationMin) {
            monthlyPayment.innerHTML = '—';
            item.classList.add(CLASS_DISABLED);
        } else {
            item.classList.remove(CLASS_DISABLED);
        }
    }

    /**
     * Устанавливает кол-во подходящих программ
     * @private
     */
    private _setOrdersCount(): void {
        const countFilteredPrograms = this._activePrograms().length;
        const AffordableWord = Utils.pluralWord(countFilteredPrograms, ['Подходит', 'Подходят', 'Подходят']);
        const countProgramWord = Utils.pluralWord(countFilteredPrograms, ['предложение', 'предложения', 'предложений']);

        this.mortgageProgramsCount.innerText = `${AffordableWord} ${countFilteredPrograms} ${countProgramWord}`;
    }

    /**
     * Выводит плашку об отсутствии результатов
     * @private
     */
    private _checkEmptyResult(): void {
        const noResult = Boolean(!this._activePrograms().length);
        const resultMethod = noResult ? 'add' : 'remove';
        const noResultMethod = noResult ? 'remove' : 'add';

        this.programResult.classList[resultMethod](CLASS_HIDDEN);
        this.resultHeading.classList[resultMethod](CLASS_HIDDEN);
        this.programNoResult.classList[noResultMethod](CLASS_HIDDEN);
        observer.publish('mortgage:noResult', !noResult);
    }

    /**
     * Передаём строки таблицы с банками на упорядочивание
     */
    private _setOrder(): void {
        this.programs.forEach((item: Element) => {
            this._setItemOrder(item as HTMLElement);
        });
    }

    /**
     * Устанавливаем свойство order для каждого банка, чтобы упорядочить элементы по цене
     * @param {HTMLElement} bankCard - DOM элемент каждого банка (строка в таблице или карточка)
     * @private
     */
    private _setItemOrder(bankCard: HTMLElement): void {
        const specialOrderCoefficient = 10000000;
        let payment = Number(bankCard.querySelector('.j-mortgage-calculator__payment')!.innerHTML
            .replace(/[^0-9]/g, ''));

        bankCard.setAttribute('data-payment', payment === 0 ? `${specialOrderCoefficient}` : `${payment}`);

        if (bankCard.dataset['sort'] && !bankCard.classList.contains(CLASS_DISABLED)) {
            payment = Number(bankCard.dataset['sort']);
        }

        bankCard.style.order = payment === 0 ? `${specialOrderCoefficient}` : `${payment}`;
    }

    private _setMinimalPayment(): void {
        // Получаем все элементы содержащие минимальные платежи
        const allMinPaymentElements: Element[] = [...this.wrapper.querySelectorAll('.j-mortgage-calculator__payment')];
        // Массив минимальных платежей
        const allMinPaymentsData: number[] = [];

        allMinPaymentElements.forEach((item: Element) => {
            const element = item as HTMLElement;

            element.dataset['payment'] && allMinPaymentsData.push(Number(element.dataset['payment']));
        });

        const sortedMinPaymentsArray: number[] = allMinPaymentsData.sort((a: number, b: number) => {
            return a - b;
        });

        this.minMonthlyPayment = sortedMinPaymentsArray.length ?
            `${sortedMinPaymentsArray[0]}` :
            '0';

        this.wrapper.dataset['minMonthly'] = this.minMonthlyPayment;

        observer.publish('monthlyPayment:updated', this.minMonthlyPayment);
    }

    private _setShareParam(setHash: boolean): void {
        const query = new URLSearchParams();
        const hash = window.location.hash;

        query.append('price[min]', `${U.getAccessStorage('price[min]')}`);
        query.append('first-pay[min]', `${U.getAccessStorage('first-pay[min]')}`);
        query.append('limitation[min]', `${U.getAccessStorage('limitation[min]')}`);

        if (setHash) {
            window.history.pushState(null, '', `?${query.toString()}${hash}`);
        }
    }

    private _activePrograms(): Element[] {
        return this.programs.filter((program: Element) => {
            return !program.classList.contains('is-disabled');
        });
    }

    /**
     * Устанавливаем значения из гет параметров если они есть
     */
    private _setGetQueryValue(): void {
        // получаем данные гет параметра
        this.queryData = U.getQueryData();

        if (!this.queryData) {
            return;
        }

        this.rangeSliders.forEach((rangeSlider: IRangeSlider) => {
            const sliderInput = rangeSlider.input;
            const name = sliderInput?.name;
            const queryValue = this.queryData[name];

            if (queryValue && Number(queryValue) > rangeSlider.options.min) {
                if (name === 'first-pay[min]') {
                    // Если при изменении первого платежа он становится больше
                    // 90% суммы квартиры, то ставим 90% от суммы квартиры,
                    // иначе сумму выбранного первого платежа
                    rangeSlider.oldFrom = this.flatPrice && queryValue > this.flatPrice * 0.9 ?
                        this.flatPrice * 0.9 :
                        queryValue;

                    sliderInput.value = Utils.convertToDigit(`${rangeSlider.oldFrom}`);

                    this._updateFirstPaymentInstance(rangeSlider);
                } else {
                    sliderInput.value = Utils.convertToDigit(queryValue);
                }
            }

            if (name === 'price[min]' || rangeSlider.slider.id === 'price') {
                this._setCredit();
            }
        });
    }

    private _setSlidersElements(): void {
        if (!this.rangeSliders.length) {
            return;
        }

        this.priceSlider = this.rangeSliders.find((rangeSlider: IRangeSlider) => {
            return rangeSlider.slider.id === 'price';
        });

        this.firstPaymentSlider = this.rangeSliders.find((rangeSlider: IRangeSlider) => {
            return rangeSlider.slider.id === 'first-pay';
        });

        this.limitationSlider = this.rangeSliders.find((rangeSlider: IRangeSlider) => {
            return rangeSlider.slider.id === 'limitation';
        });
    }

    private _correctFirstPayment(): void {
        if (!this.priceSlider?.oldFrom) {
            return;
        }

        this.flatPrice = Number(this.priceSlider.oldFrom);
        this._updateFirstPayment(this.flatPrice);
        this._setCredit();
        this.update();
    }

    /**
     * Обновляем первый платеж при изменении цены квартиры
     * @param {number} from - максимальное значение для рэйндж-слайдера первого взноса
     */
    private _updateFirstPayment(from: number): void {
        // Это для исключения первого инициализации всех слайдеров, когда ещё не все
        // слайдеры активированы
        if (!this.firstPaymentSlider) {
            return;
        }

        // Если текущее значение первого платежа в процентном выражении
        // составляет больше 90% от новой суммы квартиры, устанавливаем 90% от
        // суммы квартиры в качестве первого платежа, иначе оставляем ту же
        // сумму первого платежа, что и была выбрана пользователем ранее.
        this.firstPaymentSlider.oldFrom = parseInt(U.calcCredit().percent) >= 90 ?
            Math.floor(from * 0.9) :
            this.firstPaymentSlider.oldFrom;

        this._updateFirstPaymentInstance(this.firstPaymentSlider);
    }

    private _updateFirstPaymentInstance(slider: IRangeSlider): void {
        slider.ionRangeSliderInstance?.update({
            max : Number(this.flatPrice),
            from: Number(slider.oldFrom),
            to  : Number(this.flatPrice)
        });
    }
}

export default MortgageCalculator;
