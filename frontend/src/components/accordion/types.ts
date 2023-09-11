export enum EAccordionState {
    OPEN = 'open',
    CLOSE = 'close',
}

export interface IAccordionOptions {
    element: Element;
    isCloseTimeout: boolean;
    selectors: IAccordionSelectors;
}

export interface IAccordionSelectors {
    toggler: string;
    content: string;
    contentOuter: string;
    title: string;
    blockElement?: string;
}

export interface IAbAccordion {
    init(options: IAccordionOptions): void;
    reinit(): void;
    open(): void;
    close(): void;
}
