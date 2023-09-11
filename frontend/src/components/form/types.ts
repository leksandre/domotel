import type {IPopup} from '@/components/popup/types';

export interface IFormOptions {
    target: HTMLElement;
    popupInstance?: IPopup;
    successMessage: boolean;
}

export interface IForm {
    init(options: IFormOptions): void;
}
