export enum ECookiesNoticeState {
    SHOW = 'is-show',
    OPEN = 'is-open'
}

export interface ICookiesNotice {
    init(wrapper: HTMLElement): void;
}
