export interface INavigationBreaks {
    min: number;
    max: number;
}

export interface INavigationOptions {
    target: HTMLElement;
    wrapper: string;
    driveElements: string;
    foldingNavigation?: string;
    breaks?: INavigationBreaks;
    offset?: number;
}

export interface INavigation {
    init(options: INavigationOptions): void;
}
