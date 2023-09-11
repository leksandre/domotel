export interface ICustomSelectOptions {
    element: HTMLElement;
    isOpen?: boolean;
    selectCallback?(...args: any[]): void;
    onGroupClick?(...args: any[]): void;
}

export interface ICustomSelectCheckOptions {
    checked: boolean;
    disabled: boolean;
    value: string;
}

export interface ICustomSelect {
    element: HTMLElement;
    init(): void;
}
