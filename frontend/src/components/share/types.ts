export interface IShareOptions {
    target: HTMLElement;
}

export interface IShare {
    init(options: IShareOptions): void;
}
