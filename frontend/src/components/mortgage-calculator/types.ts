import type {IRangeSliderData} from '@/components/range-slider/types';

export interface IMortgageUtils {
    isExist(data: unknown): boolean;
    getAccessStorage(key: string): number;
    removeStorage(key: string): void;
    calc(action: string, a: number, b: number): number;
    calcString(action: string, a: number, b: number): string;
    decodePostfix(num: number): string;
    saveToStorage(key: string, value: number): void;
    calcCredit(): {amount: string, percent: string};
    checkInputData(inputElement: HTMLInputElement, data: IRangeSliderData, regexp: RegExp): void;
    onKeyDownEvent(event: KeyboardEvent,
        data: any,
        obj: IMortgageCalculator,
        input: HTMLInputElement,
        regexp: RegExp): void;
    preventNaNSymbolsEnter(event: KeyboardEvent): void;
    getQueryData(): Record<string, string> | false;
}

export interface IMortgageCalculator {
    init(): void;
    updateSlidersValues(data: IRangeSliderData, flag?: 'set'): void;
    update(): void;
}
