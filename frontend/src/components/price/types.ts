export interface IPriceOptions {
    element: HTMLElement;
    payment?: string;
    href?: string;
}

export interface IPrice {
    init(): void;
}
