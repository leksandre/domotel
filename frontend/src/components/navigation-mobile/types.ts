export interface IMobileNavigationMenuItem {
    menu: HTMLElement;
}

export type IMobileNavigationMenu = Record<number, IMobileNavigationMenuItem>;

export interface IMobileNavigationOptions {
    element: Element;
}

export interface IMobileNavigation {
    init(): void;
}
