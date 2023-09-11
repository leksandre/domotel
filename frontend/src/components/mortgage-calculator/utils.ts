import type {IMortgageCalculator, IMortgageUtils} from './types';
import type {IRangeSliderData} from '../range-slider/types';

class MortgageUtils implements IMortgageUtils {
    /**
     * Проверяем примитив или объект на существование
     * @param {*} data - примитив или объект
     * @return {boolean} - определён ли параметр
     */
    public isExist(data: unknown): boolean {
        return data !== null && !isNaN(Number(data)) && typeof data !== 'undefined';
    }

    public saveToStorage(key: string, value: number): void {
        window.sessionStorage.setItem(key, value!.toString());
    }

    public getAccessStorage(key: string): number {
        return Number(window.sessionStorage.getItem(key));
    }

    public removeStorage(key: string): void {
        window.sessionStorage.removeItem(key);
    }

    /**
     * Совершаем нужные вычисления для операндов
     * @param {String} action - флаг, определяющий, какое действие необходимо совершить над операндами
     * @param {number} a - операнд
     * @param {number} b - операнд
     * @return {number|string} - результат операций
     */
    public calc(action: string, a: number, b: number): number {
        switch (action) {
            case 'creditAmount':
                return a - b;
            case 'creditPercent':
                return parseInt(((b / a) * 100).toFixed());
            default:
                return -Infinity;
        }
    }

    public calcString(action: string, a: number, b: number): string {
        switch (action) {
            case 'creditAmountPretty':
                return `${(a - b).toLocaleString('ru-Ru')}&nbsp;₽`;
            case 'creditPercentPretty':
                return `${((b / a) * 100).toFixed()}%`;
            case 'monthlyPaymentPretty':
                return `${parseInt(a.toFixed()).toLocaleString('ru-Ru')}&nbsp;₽/мес.`;
            default:
                return `Неверный параметр action: ${action}`;
        }
    }

    /**
     * Высчитываем сумму и процент кредита
     * Если процент больше 100, выделяем его красным, а сумму кредита не выводим
     * @return {object} - сумма и процент кредита
     */
    public calcCredit(): {amount: string, percent: string} {
        const price: number = this.getAccessStorage('price[min]');
        const firstPay: number = this.getAccessStorage('first-pay[min]');
        const percent: string = this.calcString('creditPercentPretty', price, firstPay);
        const amount: string = this.calc('creditAmount', price, firstPay) >= 0 ?
            this.calcString('creditAmountPretty', price, firstPay) :
            '—';

        this.saveToStorage('creditAmount', this.calc('creditAmount', price, firstPay));
        this.saveToStorage('creditPercent', this.calc('creditPercent', price, firstPay));

        return {
            amount,
            percent
        };
    }

    /**
     * Выбираем необходимое склонение постфикса
     * @param {number} num - число, определяющее склонение
     * @return {string} - склонение
     */
    public decodePostfix(num: number): string {
        const postfixGod = [1, 21, 31];

        return postfixGod.includes(num) ? 'года' : 'лет';
    }

    /**
     * Проверка на наличие значений в инпуте
     * @param {object} inputElement - элемент Инпута
     * @param {object} data - все значения слайдера
     * @param {String} regexp - регулярное выражение, исключающее всё, кроме цифр и точки
     */
    public checkInputData(inputElement: HTMLInputElement, data: IRangeSliderData, regexp: RegExp): void {
        const newDataFrom = Number(inputElement.value.replace(regexp, ''));

        data.from = newDataFrom < data.min ?
            data.min :
            newDataFrom > data.max ? data.max : newDataFrom;
    }

    /**
     * Функция ограничения ввода символов с клавиатуры
     * @param {Event} event - событие
     */
    public preventNaNSymbolsEnter(event: KeyboardEvent): void {
        const functionalKeys = [8, 9, 13, 17, 18, 35, 36, 37, 39, 46];

        if (!functionalKeys.includes(event.which) &&
            (event.which > 57 || event.which < 48) &&
            (event.which > 105 || event.which < 96) &&
            (event.which > 123 || event.which < 112)) {
            event.preventDefault();
        }
    }

    /**
     * Обработчик нажатий клавиш Tab, Enter, Escape
     * Tab, Enter проверяют корректность заполнения инпута, обновляют слайдеры и значения банков
     * @param {KeyboardEvent} event - событие
     * @param {object} data - все значения слайдера
     * @param {object} obj - экземпляр HTB для текущего ползунка
     * @param {object} input - активный DOM элемент input
     * @param {String} regexp - регулярное выражение, исключающее всё, кроме цифр и точки
     */
    // eslint-disable-next-line max-params
    public onKeyDownEvent(event: KeyboardEvent,
                          data: IRangeSliderData,
                          obj: IMortgageCalculator,
                          input: HTMLInputElement,
                          regexp: RegExp): void {
        switch (event.which) {
            case 13:
                this.checkInputData(input, data, regexp);
                obj.updateSlidersValues(data, 'set');
                obj.update();
                input.blur();
                break;
            case 27:
                input.blur();
                break;
            default:
                break;
        }
    }

    public getQueryData(): Record<string, string> | false {
        if (!window.location.search.length) {
            return false;
        }

        const queryLine = window.location.search.replace('?', '')
            .split('&')
            .reduce((acc: Record<string, string>, value: string) => {
                const param = value.split('=');

                acc[decodeURIComponent(String(param[0]))] = decodeURIComponent(String(param[1]));

                return acc;
            },
            {});

        return queryLine;
    }
}

export default MortgageUtils;
