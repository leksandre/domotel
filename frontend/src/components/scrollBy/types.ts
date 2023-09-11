export interface IScrollByOptions {
    anchors: HTMLElement[];
    offset?: number;
    animate?: boolean;
    speedAnimation?: number;
    takeElements: Element[];
}

export interface IScrollBy {
    init(options: IScrollByOptions): void;
    scrollToHref(element: HTMLElement): void;
    scrollToElement(element: HTMLElement): void;
    scrollToGetAnchor(): void;
    scrollToTop(): void;
}
