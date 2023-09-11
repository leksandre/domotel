export interface INavigationGroupsElement extends HTMLElement {
    isGroup: boolean;
    show: number | boolean;
    groupStart: number | boolean;
    groupEnd: number | boolean;
    navigation: Element | null;
    isVisible: boolean;
    isGroupActive: boolean;
}

export interface INavigationGroupsOptions {
    navigation: HTMLElement;
    navigationSelector?: string;
    navigationModify?: string;
}

export interface INavigationGroups {
    init(): void;
}
