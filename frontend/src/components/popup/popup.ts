/**
 * @version 2.0
 * @author Kelnik Studios {http://kelnik.ru}
 * @link https://gitbook.kelnik.ru/main/front-end/components/popup.html documentation
 */

/**
 * DEPENDENCIES
 */
import type {IAbstractPopup,
    IAjaxPopupOptions,
    IAlbumData,
    ICallbackRequestOnline,
    ICallbackRequestProgress,
    IGallerySlides,
    IPopupData,
    IPopupDataAjax,
    IPopupDataGallery,
    IPopupDataOnline,
    IPopupDataProgress,
    IPopupDataStatic,
    IPopupIframeDataPlanoplan,
    IPopupOptions,
    IStaticPopupOptions} from './types';
import type {ICallback, ICallbackRequest} from '@/common/scripts/types/utils';
import {initPlanoplanWidget, insertPremisesData} from '@/common/scripts/common';
import {InternetConnection, Utils} from '@/common/scripts/utils';
import basicTemplate from './templates/basic-popup.twig';
import {ERestMethod} from '@/common/scripts/types/utils';
import galleryTemplate from './templates/gallery-popup.twig';
import Hammer from 'hammerjs';
import type {IObserver} from '@/common/scripts/types/observer';
import type {IOnlineCameraData} from './types';
import type {IPopup} from './types';
import type {IPopupIframeData} from './types';
import Observer from '@/common/scripts/observer';
import onlineTemplate from './templates/online-popup.twig';
import planoplanTemplate from './templates/planoplan-popup.twig';
import progressContentTemplate from './templates/progress-content.twig';
import progressTemplate from './templates/progress-popup.twig';
import TouchZoom from '../touch-zoom/touch-zoom';

const observer: IObserver = new Observer();
const emptyFunction = (): unknown => {
    return null;
};

abstract class AbstractPopup implements IAbstractPopup {
    public content: HTMLElement | null = null;
    public currentTarget: HTMLElement;
    protected closeButtonAriaLabel: string;
    protected readonly openStateClass: string;
    protected currentState: boolean = false;
    protected openButtons: HTMLElement[];

    /* Определяется в методе _setTemplates() */
    protected template: ((param: unknown) => string) | null = null;

    /* Определяется в методе _getElements() */
    protected popup: HTMLElement | null = null;
    private closeButtons: HTMLElement[] = [];
    private readonly onCreate: () => unknown;
    private readonly onClose: () => unknown;

    /* Определяется в методе _setData() */
    protected abstract data: IPopupData | null;

    protected constructor(options: Partial<IPopupOptions> = {}) {
        this.openStateClass = options.openStateClass || 'popup_state_open';
        this.closeButtonAriaLabel = options.closeButtonAriaLabel || 'Закрыть всплывающее окно';
        this.openButtons = options.openButtons || [];
        this.onCreate = options.onCreate || emptyFunction;
        this.onClose = options.onClose || emptyFunction;

        this._bindMethodsContext();
    }

    /**
     * Входная точка проекта
     * @public
     */
    public init(): void {
        this._bindOpenEvents();
    }

    /**
     * Отображение попапа на экране
     * @public
     */
    public showPopup(): void {
        if (!this.popup) {
            return;
        }

        this.popup.classList.add(this.openStateClass);

        Utils.bodyFixed(this.popup);
    }

    /**
     * Открытие попапа
     * @public
     */
    public displayPopup(): void {
        this.showPopup();

        observer.publish('popup:open', this);

        this.currentState = true;
    }

    /**
     * Сокрытие попапа с дисплея
     * @public
     */
    public hidePopup(): void {
        if (!this.popup) {
            return;
        }

        this.popup.classList.remove(this.openStateClass);

        Utils.bodyStatic();
    }

    /**
     * Удаление попапа из DOM-дерева
     * @public
     */
    public removePopup(): void {
        if (!this.popup) {
            return;
        }

        this.hidePopup();
        Utils.removeElement(this.popup);

        observer.publish('popup:close', this);

        this.currentState = false;

        document.removeEventListener('keyup', this._closeByKeyboard);
        document.removeEventListener('click', this._closeByMouse);

        this.onClose();
    }

    /**
     * Перезапуск попапа
     * @public
     */
    public reOpen(): void {
        this.removePopup();
        this._open();
    }

    /**
     * Генерация попапа
     * @protected
     */
    protected _createPopup(): void {
        this._setData();
        this._setTemplates();
        this._setPopupOnPage();
        this._getElements();
        this._bindCloseEvents();

        this.onCreate();
    }

    /**
     * Получение необходимого дата-атрибута с кнопок открытия попапа
     * @protected
     * @param {string} attribute - атрибут, по которому происходит поиск
     * @return {String} - значение дата-атрибута
     */
    protected _getDataAttrValue(attribute: string): string {
        const attributesArray = [...new Set(this.openButtons.map((x: HTMLElement) => {
            return x.getAttribute(attribute);
        }))];

        if (attributesArray.length !== 1 || !attributesArray[0]) {
            throw new Error(`Expect one ${attribute}. You set: ${attributesArray[0] ? attributesArray : 0}`);
        }

        return attributesArray[0];
    }


    /**
     * Получение нод DOM-дерева из попапа
     * @protected
     */
    protected _getElements(): void {
        this.popup = document.querySelector('.j-popup__body');
        this.content = document.querySelector('.j-popup__content');

        const closeBtn = [...this.popup?.querySelectorAll('.j-popup__close') || []].map((item: Element) => {
            return item as HTMLElement;
        });
        const overLay = this.popup?.querySelector('.j-popup__overlay') as HTMLElement;

        this.closeButtons = [...closeBtn, overLay];
    }

    /**
     * Привязка методов к событиям открытия попапа
     * @protected
     */
    protected _bindOpenEvents(): void {
        this.openButtons.forEach((button: HTMLElement, index: number) => {
            button.addEventListener('click', (event: MouseEvent) => {
                event.preventDefault();

                if (this.currentState) {
                    return;
                }

                this.currentTarget = button;

                this._open();
                this._onClick(index);
            });
        });
    }

    /**
     * Привязка методов к событиям закрытия попапа
     * @protected
     */
    protected _bindCloseEvents(): void {
        this._closeByMouse();
        document.addEventListener('keyup', this._closeByKeyboard);
    }

    /**
     * Установка шаблона попапа
     * @protected
     */
    protected _setTemplates(): void {
        this.template = basicTemplate;
    }

    /**
     * Привязывает контекст для методов класса
     * @private
     */
    private _bindMethodsContext(): void {
        this._closeByKeyboard = this._closeByKeyboard.bind(this);
        this.removePopup = this.removePopup.bind(this);
    }

    /**
     * Вставка ноды попапа в DOM-дерево
     * @private
     */
    private _setPopupOnPage(): void {
        if (this.template === null) {
            return;
        }

        document.body.insertAdjacentHTML('afterbegin', this.template(this.data));
    }

    /**
     * Привязка методов к событиям закрытия попапа ко клику мышью
     * @private
     */
    private _closeByMouse(): void {
        this.closeButtons.forEach((closeBtn: HTMLElement) => {
            closeBtn.addEventListener('click', this.removePopup);
        });
    }

    /**
     * Привязка методов к событиям закрытия попапа по вводу с клавиатуры
     * @private
     * @param {number} key - числовой код нажатой клавиши
     */
    private _closeByKeyboard(key: KeyboardEvent): void {
        if (key.code === 'Escape') {
            this.removePopup();
        }
    }

    /**
     * Callback для события открытия
     * @protected abstract
     */
    protected abstract _open(): void

    /**
     * Callback для события по клику
     * @protected abstract
     */
    protected abstract _onClick(param: unknown): void

    /**
     * Установка данных в попапе
     * @protected abstract
     */
    protected abstract _setData(): void
}

export class StaticPopup extends AbstractPopup implements IPopup {
    protected readonly modify: string;
    protected override data: IPopupDataStatic | null = null;
    private readonly closeStateClass: string;
    private readonly removeDelay: number;

    /**
     * @extends
     * @param {Partial<IStaticPopupOptions>} options - параметры инициализации
     */
    constructor(options: Partial<IStaticPopupOptions>) {
        super(options);

        this.closeStateClass = options.closeStateClass || 'popup_state_close';
        this.closeButtonAriaLabel = 'Закрыть';
        this.modify = options.modify || '';
        this.removeDelay = options.removeDelay || 0;
    }

    /**
     * @override
     */
    public override removePopup(): void {
        if (!this.popup) {
            return;
        }

        if (this.removeDelay) {
            this.popup.classList.remove(this.openStateClass);
            this.popup.classList.add(this.closeStateClass);

            setTimeout(() => {
                super.removePopup();
            }, this.removeDelay);
        } else {
            super.removePopup();
        }
    }

    /**
     * @override
     */
    protected override _open(): void {
        this._createPopup();
        this.displayPopup();
    }

    /**
     * @override
     */
    protected override _onClick(): void {}

    /**
     * @override
     */
    protected override _setData(): void {
        const contentLink = this._getDataAttrValue('data-href');

        this.data = {
            buttonAriaLabel: this.closeButtonAriaLabel,
            modify         : this.modify,
            content        : document.getElementById(contentLink)?.innerHTML,
            targetModify   : this.currentTarget?.dataset?.['modify'] ?? false
        };
    }
}

export class StaticPopupWithHash extends StaticPopup {
    private popupHeader: HTMLElement | null = null;

    /**
     * @extends
     */
    override _getElements(): void {
        super._getElements();

        this.popupHeader = document.querySelector('.j-popup__header') as HTMLElement || null;
    }

    /**
     * @extends
     */
    override _bindOpenEvents(): void {
        super._bindOpenEvents();

        this._parseUrl();
    }

    /**
     * @extends
     */
    override _bindCloseEvents(): void {
        super._bindCloseEvents();

        this._closeBySwipe();
    }

    /**
     * @extends
     */
    override displayPopup(): void {
        super.displayPopup();

        this._setHash();
        window.isSPWHOpen = true;
    }

    /**
     * @extends
     */
    override removePopup(): void {
        super.removePopup();

        this._clearHash();
        window.isSPWHOpen = false;
    }

    /**
     * Скрывает попап по свайпу на хэдер попапа
     * @private
     */
    private _closeBySwipe(): void {
        if (!this.popupHeader) {
            return;
        }

        const touchEvent = new Hammer(this.popupHeader);

        touchEvent.get('swipe').set({direction: Hammer.DIRECTION_ALL});
        touchEvent.on('swipedown', this.removePopup);
    }

    /**
     * Открывает попап по хэшу в омнибоксе
     * @private
     */
    private _parseUrl(): void {
        const contentLink = this._getDataAttrValue('data-href');

        if (this.currentState || window.location.hash !== `#${contentLink}` || window.isSPWHOpen) {
            return;
        }

        this._createPopup();
        this.displayPopup();
    }

    /**
     * Устанавливает хэш попапа в омнибокс браузера
     * @private
     */
    private _setHash(): void {
        const contentLink = this._getDataAttrValue('data-href');

        window.history.pushState(null, '', `#${contentLink}`);
    }

    /**
     * Очищает омнибокс браузера от хэша попапа
     * @private
     */
    private _clearHash(): void {
        if (window.history.pushState) {
            window.history.pushState(null, '', window.location.pathname + window.location.search);

            return;
        }

        window.location.hash = '';
    }
}

export class AjaxPopup extends AbstractPopup {
    protected override data: IPopupDataAjax | null;

    /* Определяется в методе _sendDataRequest() */
    protected contentInfo: unknown | null | undefined = null;
    protected serverConnection: InternetConnection;

    /**
     * @extends
     * @param {Partial<IAjaxPopupOptions>} options - параметры инициализации
     */
    constructor(options: Partial<IAjaxPopupOptions>) {
        super(options);

        this._setData = this._setData.bind(this);
        this.serverConnection = new InternetConnection(this._sendDataRequest.bind(this));
    }

    /**
     * @override
     */
    override _open(): void {
        this.serverConnection.connect();
    }

    /**
     * @override
     */
    override _setData(): void {
        this.data = {
            buttonAriaLabel: this.closeButtonAriaLabel,
            contentInfo    : this.contentInfo
        };
    }

    /**
     * @override
     */
    override _onClick(): void {}

    /**
     * Отправляет запрос на сервер и вызывает создание попапа с данными из ответа сервера
     * @protected
     */
    protected _sendDataRequest(): void {
        const that = this;
        const contentLink = this._getDataAttrValue('data-ajax');
        const callback: ICallback = {
            success(req: ICallbackRequest) {
                that.contentInfo = req.data.content;
            },
            error(err: number) {
                console.error(`Server error with status ${err}`);
            },
            complete() {
                that.contentInfo = that.contentInfo || that.serverConnection.getErrorMessage();

                that._createPopup();
                that.displayPopup();

                that.contentInfo = null;
            }
        };

        Utils.send(null, contentLink, callback, this._getQueryType());
    }

    /**
     * Проверка на наличие интернета
     * @protected
     * @return {String} Метод запроса из атрибута или POST, если атрибут пуст.
     */
    protected _getQueryType(): ERestMethod {
        const attributesArray = [...new Set(this.openButtons.map((x: HTMLElement) => {
            return x.getAttribute('data-query');
        }))];

        if (attributesArray.length > 1) {
            throw new Error(`Expect maximum one data-query. You set: ${attributesArray}`);
        }

        return attributesArray[0] as ERestMethod || ERestMethod.POST;
    }
}

export class GalleryPopup extends StaticPopup {
    protected override data: IPopupDataGallery | null = null;
    private gallery: HTMLElement | null;

    /**
     * @extends
     * @param {Partial<IStaticPopupOptions>} options - параметры инициализации
     */
    constructor(options: Partial<IStaticPopupOptions>) {
        super(options);

        this.closeButtonAriaLabel = 'Закрыть';
    }

    /**
     * @override
     */
    override _setTemplates(): void {
        this.template = galleryTemplate;
    }

    /**
     * @override
     */
    override _setData(): void {
        this.data = {
            buttonAriaLabel: this.closeButtonAriaLabel,
            modify         : this.modify,
            items          : this._collectSlidesData()
        };
    }

    /**
     * @override
     */
    override _onClick(): void {
        if (!this.popup) {
            return;
        }

        this.gallery = this.popup.querySelector('.j-popup-gallery');
        this._initPinch();
    }

    /**
     * Собирает данные о слайдах
     * @private
     * @return {IGallerySlides[]} - массив данных для вставки в попап
     */
    private _collectSlidesData(): IGallerySlides[] {
        return this.openButtons.reduce((resultArray: IGallerySlides[], item: HTMLElement) => {
            if (!item.closest('.cloned-slide')) {
                resultArray.push({
                    src    : item.getAttribute('data-src'),
                    alt    : item.getAttribute('data-alt'),
                    caption: item.getAttribute('data-caption')
                });
            }

            return resultArray;
        }, []);
    }

    /**
     * Инициализирует зум для изображений
     * @private
     */
    private _initPinch(): void {
        if (!this.gallery) {
            return;
        }

        const images = [...this.gallery.querySelectorAll('img')];

        images.forEach((item: HTMLImageElement) => {
            return new TouchZoom(item);
        });
    }
}

export class ProgressPopup extends AjaxPopup {
    protected override data: IPopupDataProgress | null = null;
    protected override contentInfo: { albums: IAlbumData[] } | string | null = null;
    private readonly modify: string;
    private currentItemId: string | undefined;
    private currentItemIndex: number;
    private templateContent: (prop?: unknown) => string = progressContentTemplate;
    private navButtons: HTMLElement[];

    /**
     * @extends
     * @param {Partial<IAjaxPopupOptions>} options - параметры инициализации
     */
    constructor(options: Partial<IAjaxPopupOptions>) {
        super(options);

        this.modify = options.modify || '';
    }

    /**
     * @override
     */
    override _sendDataRequest(): void {
        const that = this;
        const contentLink = this._getDataAttrValue('data-ajax');
        const callback = {
            success(req: ICallbackRequestProgress) {
                that.contentInfo = req.data;
            },
            error(err: number) {
                console.error(`Server error with status ${err}`);
            },
            complete() {
                that.contentInfo = that.contentInfo || that.serverConnection.getErrorMessage();

                that._createPopup();
                that.displayPopup();
            }
        };

        Utils.send(null, contentLink, callback, this._getQueryType());
    }

    /**
     * @override
     */
    override _setTemplates(): void {
        this.template = progressTemplate;
    }

    /**
     * @override
     */
    override _setData(): void {
        const albumData = typeof this.contentInfo === 'string' || this.contentInfo === null ?
            [] :
            this.contentInfo.albums;

        this.currentItemIndex = albumData.findIndex((item: IAlbumData) => {
            return Number(item.id) === Number(this.currentItemId);
        });
        this.data = {
            buttonAriaLabel: this.closeButtonAriaLabel,
            modify         : this.modify,
            progress       : albumData[this.currentItemIndex] || false,
            prev           : albumData[this.currentItemIndex - 1] || false,
            next           : albumData[this.currentItemIndex + 1] || false
        };
    }

    /**
     * @override
     */
    override _onClick(): void {
        this.currentItemId = this.currentTarget.dataset['id'];
    }

    /**
     * @override
     */
    override _createPopup(): void {
        super._createPopup();
        this._initChange();
    }

    /**
     * Инициализирует события для кнопок смены контента
     * @private
     */
    private _initChange(): void {
        if (!this.content) {
            return;
        }

        this.navButtons = Array.from(this.content.querySelectorAll('.j-progress-nav'));

        this.navButtons.forEach((navButton: HTMLElement) => {
            navButton.addEventListener('click', this._changeContent.bind(this));
        });
    }

    /**
     * Смена контента
     * @private
     * @param {MouseEvent} event - клик
     */
    private _changeContent(event: MouseEvent): void {
        if (!this.content) {
            return;
        }

        this.currentItemId = (event.currentTarget as HTMLElement)?.dataset['id'];
        this._setData();
        Utils.clearHtml(this.content);
        Utils.insetContent(this.content, this.templateContent(this.data));
        this.popup?.scroll({
            top     : 0,
            behavior: 'smooth'
        });
        this._initChange();
    }
}

export class OnlinePopup extends AjaxPopup {
    protected override data: IPopupDataOnline | null = null;
    protected override contentInfo: { cameras?: IOnlineCameraData[] } | string | null = null;
    private readonly modify: string;

    /**
     * @extends
     * @param {Partial<IAjaxPopupOptions>} options - параметры инициализации
     */
    constructor(options: Partial<IAjaxPopupOptions>) {
        super(options);

        this.modify = options.modify || '';
    }

    /**
     * @override
     */
    override _sendDataRequest(): void {
        const that = this;
        const contentLink = this._getDataAttrValue('data-ajax');
        const callback = {
            success(req: ICallbackRequestOnline) {
                that.contentInfo = req.data;
            },
            error(err: number) {
                console.error(`Server error with status ${err}`);
            },
            complete() {
                that.contentInfo = that.contentInfo || that.serverConnection.getErrorMessage();

                that._createPopup();
                that.displayPopup();
            }
        };

        Utils.send(null, contentLink, callback, this._getQueryType());
    }

    /**
     * @override
     */
    override _setTemplates(): void {
        this.template = onlineTemplate;
    }

    /**
     * @override
     */
    override _setData(): void {
        const tabsData = typeof this.contentInfo === 'string' || this.contentInfo === null ?
            [] :
            this.contentInfo.cameras || [];

        this.data = {
            buttonAriaLabel: this.closeButtonAriaLabel,
            modify         : this.modify,
            tabs           : tabsData
        };
    }
}

export class PostMessagePopup extends StaticPopup {
    private iframeData: {
        href: string;
        popupData: IPopupIframeData | IPopupIframeDataPlanoplan;
        type: string;
    };

    /**
     * @override
     */
    override _setData(): void {
        this.data = {
            buttonAriaLabel: this.closeButtonAriaLabel,
            modify         : this.modify,
            content        : document.getElementById(this.iframeData.href)?.innerHTML
        };
    }

    /**
     * @override
     */
    override _open(): void {
        this._createPopup();
        this._appendData();
        this.displayPopup();
    }

    /**
     * @override
     */
    override _bindOpenEvents(): void {
        window.addEventListener('message', (event: MessageEvent) => {
            if (event.origin !== window.origin) {
                return;
            }

            if (event.data?.message === 'openPopup' && event.data?.href) {
                this.iframeData = event.data;

                if (this.currentState) {
                    return;
                }

                this._open();
                this._openInitialisation();
            }
        });
    }

    /**
     * Установка шаблона post message попапа
     * @protected
     */
    override _setTemplates(): void {
        switch (this.iframeData.type) {
            case 'planoplan':
                this.template = planoplanTemplate;
                break;
            default:
                this.template = basicTemplate;
                break;
        }
    }

    private _openInitialisation(): void {
        switch (this.iframeData.type) {
            case 'planoplan':
                if (!window.Planoplan) {
                    this._initPlanoplanScript();
                    break;
                }
                this._initPlanoplanWidget();
                break;
            case 'callback':
                this._appendData();
                break;
            default:
                break;
        }
    }

    /**
     * Подключает скрипт виджета Planoplan'а
     * @private
     */
    private _initPlanoplanScript(): void {
        if (window.Planoplan) {
            return;
        }

        const planoplanScript = document.createElement('script');

        planoplanScript.setAttribute('src', 'https://widget.planoplan.com/etc/multiwidget/release/v4/static/js/main.js?v=2');

        document.body.appendChild(planoplanScript);

        planoplanScript.onload = () => {
            this._initPlanoplanWidget();
        };
    }

    /**
     * Вызывает функцию инициализации виджета Planoplan'а
     * @private
     */
    private _initPlanoplanWidget(): void {
        if (!this.popup) {
            return;
        }

        if ('uid' in this.iframeData.popupData) {
            initPlanoplanWidget(this.iframeData.popupData.uid);
        }
    }

    /**
     * Вставляет данные для скрытых полей
     * @private
     */
    private _appendData(): void {
        if (!this.popup) {
            return;
        }

        if ('title' in this.iframeData.popupData) {
            insertPremisesData(this.popup, this.iframeData.popupData);
        }
    }
}
