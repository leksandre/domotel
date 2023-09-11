import type {IPrice, IPriceOptions} from './types';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';
import {Utils} from '@/common/scripts/utils';

const observer: IObserver = new Observer();

class Price implements IPrice {
    // Элемент обертка для блока месячного платежа ипотеки
    private mortgagePriceWrapper: HTMLElement;
    // Сумма ежемесячного платежа
    private monthlyPayment: string | null;
    // ID ссылки на блок ипотеки
    private readonly href: string | null;
    // Поле со значением месячного платежа ипотеки
    private readonly mortgageMonthlyField: HTMLElement | null;
    // Ссылка на блок ипотеки
    private mortgageLink: HTMLLinkElement | null;
    constructor(options: IPriceOptions) {
        this.mortgagePriceWrapper = options.element;
        this.monthlyPayment = options.payment || null;
        this.href = options.href || null;
        this.mortgageMonthlyField = this.mortgagePriceWrapper
            .querySelector('.j-price__mortgage-monthly') as HTMLElement || null;
        this.mortgageLink = this.mortgagePriceWrapper
            .querySelector('.j-price__mortgage-link') as HTMLLinkElement || null;
    }

    public init(): void {
        this._setMonthlyPayment();
        this._setMortgageLink();
        this._subscribes();
    }

    /**
     * Отображаем блок с суммой минимального месячного платежа
     */
    private _setMonthlyPayment(): void {
        if (!this.mortgageMonthlyField || !this.monthlyPayment) {
            return;
        }

        this.mortgageMonthlyField.innerText = Utils.convertToDigit(this.monthlyPayment);

        this.mortgagePriceWrapper.classList.add('is-active');
    }

    private _setMortgageLink(): void {
        if (!this.mortgageLink || !this.href) {
            return;
        }

        this.mortgageLink.href = `#${this.href}`;
    }

    private _subscribes(): void {
        observer.subscribe('monthlyPayment:updated', (price: string) => {
            this.monthlyPayment = price;
            this._setMonthlyPayment();
        });

        observer.subscribe('mortgage:noResult', (state: boolean) => {
            this._setNoResult(state);
        });
    }

    private _setNoResult(state: boolean): void {
        const method = state ? 'remove' : 'add';

        this.mortgagePriceWrapper.classList[method]('is-empty');
    }
}

export default Price;
