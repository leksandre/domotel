export interface IHeaderOptions {
    headerWrap: HTMLElement;
    targetScroll?: HTMLElement[];
}

export interface IHeader {
    init(options: IHeaderOptions): void;
}
