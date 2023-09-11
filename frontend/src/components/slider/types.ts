export type IResponsive = Record<number, number>;

export interface ICaptionData {
    width: string;
    items: (string | undefined)[];
    hasItems: boolean;
}

export interface ICaptionSize {
    width: number;
    fullWidth: number;
    minTranslate: number;
    maxTranslate: number;
}

export interface ISlidesData {
    width: number;
    fullWidth: number;
    minTranslate: number;
    maxTranslate: number;
}

export interface ISliderElements {
    wrap: string;
    width: string;
    items: string[];
    modify: string | false;
    innerModify: string | boolean;
    innerData: string | boolean;
}

export enum EElementPosition {
    slide = 'slide',
    allSlider = 'allSlider',
    caption = 'caption',
}

export interface ISliderOptions {
    slider: HTMLElement;
    slideCount?: number;
    arrowPosition?: EElementPosition;
    counterPosition?: EElementPosition;
    dotsPosition?: EElementPosition;
    isCaption?: boolean;
    slidesWrap?: string;
    captionWrap?: string;
    widgetWrap?: string;
    slideVisible?: number;
    modify?: string;
    slidesModify?: string;
    innerModify?: string;
    innerData?: string;
    dots?: boolean;
    duration?: string;
    responsive?: IResponsive;
    noSwipe?: boolean;
    autoplay?: boolean;
    arrow?: boolean;
    counter?: boolean;
    autoplaySpeed?: number;
    stopOnClick?: boolean;
    infinityLoop?: boolean;
}

export interface ISlider {
    slider: HTMLElement;
    init(options: ISliderOptions): void;
}
