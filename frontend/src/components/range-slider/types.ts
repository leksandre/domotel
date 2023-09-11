export interface IRangeSliderOuterOptions {
    type: string | null;
    min: number;
    max: number;
    from: string | number | null;
    to: string | number | null;
    step: string | null;
    min_interval: string | null;
    digit?: boolean;
    disable: boolean;
    validate: string;
}

export interface IRangeCSliderOptions {
    wrapper: HTMLElement;
    outerOptions?: IRangeSliderOuterOptions | Record<string, string | undefined>;
}

export interface IRangeSliderOptions {
    type: string | null;
    min: number;
    max: number;
    from: number | null;
    to: number | null;
    step: number | null;
    min_interval: number | null;
    digit: boolean;
    disable: boolean;
    validate: string | undefined;
    hide_from_to: boolean;
    hide_min_max: boolean;
    prettify_enabled: boolean;
    onStart(data: IRangeSliderData): void;
    onChange(data: IRangeSliderData): void;
    onFinish(data: IRangeSliderData): void;
    onUpdate(data: IRangeSliderData): void;
}

export interface IRangeSliderData {
    from: number;
    from_percent: number;
    from_pretty: string;
    input: HTMLInputElement;
    max: number;
    min: number;
    rangeSlider?: IRangeSlider;
    slider: HTMLElement;
    to: number;
    to_percent: number;
    to_pretty: string;
}

export interface IIonRangeSliderInstance {
    old_from: number;
    update(options?: Partial<IIonRSUpdateOptions>): void;
    destroy(): void;
    init(): void;
    reset(): void;
}

export interface IIonRSUpdateOptions {
    from: string | number | undefined | null;
    to: string | number | undefined | null;
    max: string | number | undefined | null;
    min: string | number | undefined | null;
}

export interface IRangeSlider {
    input: HTMLInputElement;
    wrapper: HTMLElement;
    slider: HTMLElement;
    oldFrom: number;
    ionRangeSliderInstance?: IIonRangeSliderInstance;
    options: IRangeSliderOptions;
    init(): void;
}

