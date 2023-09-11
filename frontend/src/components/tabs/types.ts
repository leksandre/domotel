export interface ITabsOptions {
    elem: HTMLElement;
    hash?: boolean;
    initialTab?: string;
    tabItem?: string;
    tabsParent?: string;
    contentItem?: string;
    contentsParent?: string;
    onChangeCallback?(param?: ITabData): unknown;
}

export interface ITabData {
    id: string;
    tab: HTMLElement;
}

export interface ITabs {
    init(options: ITabsOptions): void;
}
