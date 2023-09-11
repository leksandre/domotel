import type {
    AjaxPopup,
    GalleryPopup,
    OnlinePopup,
    ProgressPopup,
    StaticPopup,
    StaticPopupWithHash
} from './popup';
import type {ICallbackRequest} from '@/common/scripts/types/utils';

export interface IPopupOptions {
    openStateClass: string;
    closeButtonAriaLabel: string;
    openButtons: HTMLElement[];
    onCreate(): unknown;
    onClose(): unknown;
}

export interface IStaticPopupOptions extends IPopupOptions {
    closeStateClass: string;
    modify: string;
    removeDelay: number;
}

export interface IAjaxPopupOptions extends IPopupOptions {
    modify?: string;
}

export interface IPopupData {
    buttonAriaLabel: string;
}

export interface IPopupDataStatic extends IPopupData {
    modify: string;
    content?: string;
    targetModify?: string | false;
}

export interface IPopupDataGallery extends IPopupDataStatic {
    items: IGallerySlides[];
}

export interface IPopupDataAjax extends IPopupData {
    contentInfo?: unknown | null;
}

export interface IPopupDataProgress extends IPopupDataAjax {
    modify: string;
    progress: IAlbumData | false;
    prev: IAlbumData | false;
    next: IAlbumData | false;
}

export interface IPopupDataOnline extends IPopupData {
    modify: string;
    tabs?: IOnlineCameraData[];
}

export interface IGallerySlides {
    src: string | null;
    alt: string | null;
    caption: string | null;
}

export interface IAlbumData {
    id: number | string;
    title: string;
    comment?: string;
    description?: string;
    videos?: {
        thumb: string;
        url: string;
    }[];
    images: string[];
}

export interface IOnlineCameraData {
    id?: number | string;
    title?: string;
    description?: string;
    video?: {
        thumb: string;
        url: string;
    };
}

export interface ICallbackRequestOnline extends ICallbackRequest {
    data: {
        content?: unknown;
        cameras?: IOnlineCameraData[];
    };
}

export interface ICallbackRequestProgress extends ICallbackRequest {
    data: {
        content?: unknown;
        albums: IAlbumData[];
    };
}

export interface IPopupFacadeOptions {
    openButtons: HTMLElement[];
    modify?: string;
    removeDelay?: number;
    closeButtonAriaLabel?: string;
}

export type IPopupsGroupsName = 'href' | 'hrefNoHash' | 'ajax' | 'galleryPopup' |
'progressPopup' | 'callback' | 'online';

export type TPopupsGroupConstruct = typeof StaticPopupWithHash | typeof StaticPopup | typeof AjaxPopup |
    typeof GalleryPopup | typeof ProgressPopup | typeof OnlinePopup;

export interface IPopupsGroupsObject {
    Construct: TPopupsGroupConstruct;
    popups: Record<any, HTMLElement[]>;
    options: {
        modify?: string;
        removeDelay?: number;
        closeButtonAriaLabel?: string;
        onCreate?(): unknown;
    };
}

export interface IAbstractPopup {
    init(): void;
    showPopup(): void;
    hidePopup(): void;
    displayPopup(): void;
    removePopup(): void;
    reOpen(): void;
}

export interface IPopupIframeData {
    title: string | null;
    area: string | null;
    block: string | null;
    section: string | null;
    floor: string | null;
    hidePrice: string | null;
    price: string | null;
    description: string;
    actionPrice: {
        value: string;
        base: string;
    } | null;
}

export interface IPopupIframeDataPlanoplan {
    uid: string;
}

export interface IPopup extends IAbstractPopup {
    content: HTMLElement | null;
    currentTarget: HTMLElement;
}

export interface IPopupFacade {
    init(): void;
}
