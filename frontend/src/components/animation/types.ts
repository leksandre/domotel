import type {gsap} from 'gsap';
export interface IAnimationItem extends HTMLElement {
    timeline: gsap.core.Timeline | null;
}

export interface IAnimationRowSetting {
    breakpoint: number;
    itemCounts: number;
    name: string;
}

export interface IAnimationRowItem extends HTMLElement {
    timeline: gsap.core.Timeline | null;
    mm: gsap.MatchMedia | null;
}

export interface IAnimationRow extends HTMLElement {
    items: IAnimationRowItem[];
    settings: IAnimationRowSetting[];
    currentSetting: IAnimationRowSetting | null;
}

export interface IAnimationObject {
    animationBlock: Element;
    items: IAnimationItem[];
    rows: IAnimationRow[];
}

export interface IAnimationOptions {
    wrapper: HTMLElement;
    blockSelector?: string;
    rowSelector?: string;
}
