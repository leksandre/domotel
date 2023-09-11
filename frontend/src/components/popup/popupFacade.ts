/**
 * @version 2.0
 * @author Kelnik Studios {http://kelnik.ru}
 * @link https://gitbook.kelnik.ru/main/front-end/components/popup.html#facade documentation
 */

/**
 * DEPENDENCIES
 */
import {
    AjaxPopup,
    GalleryPopup,
    OnlinePopup,
    ProgressPopup,
    StaticPopup,
    StaticPopupWithHash
} from './popup';
import type {IPopupFacade,
    IPopupFacadeOptions,
    IPopupsGroupsName,
    IPopupsGroupsObject
} from './types';
import {initVideoTabs} from '@/common/scripts/common';

export default class PopupFacade implements IPopupFacade {
    private openButtons: HTMLElement[];

    private readonly popupsGroups: Record<IPopupsGroupsName, IPopupsGroupsObject>;

    /* eslint-disable max-lines-per-function */
    constructor(options: IPopupFacadeOptions) {
        this.openButtons = options.openButtons;
        this.popupsGroups = {
            href: {
                Construct: StaticPopupWithHash,
                popups   : {},
                options  : {
                    modify     : options.modify,
                    removeDelay: 300
                }
            },
            hrefNoHash: {
                Construct: StaticPopup,
                popups   : {},
                options  : {
                    removeDelay: options.removeDelay
                }
            },
            ajax: {
                Construct: AjaxPopup,
                popups   : {},
                options  : {
                    removeDelay: options.removeDelay
                }
            },
            galleryPopup: {
                Construct: GalleryPopup,
                popups   : {},
                options  : {
                    modify     : 'popup_theme_white',
                    removeDelay: 300
                }
            },
            progressPopup: {
                Construct: ProgressPopup,
                popups   : {},
                options  : {
                    modify     : 'popup_theme_white',
                    removeDelay: 300
                }
            },
            callback: {
                Construct: StaticPopupWithHash,
                popups   : {},
                options  : {
                    removeDelay         : options.removeDelay,
                    closeButtonAriaLabel: options.closeButtonAriaLabel,
                    modify              : options.modify
                }
            },
            online: {
                Construct: OnlinePopup,
                popups   : {},
                options  : {
                    modify     : 'popup_theme_white',
                    removeDelay: 300,
                    onCreate   : () => {
                        initVideoTabs('.j-popup__content');
                    }
                }
            }
        };

        this._checkOpenButtonsExistence();
    }
    /* eslint-enable max-lines-per-function */

    /**
     * Входная точка проекта
     * @public
     */
    public init(): void {
        this._groupPopupsByType();
        this._initPopups();
    }

    /**
     * Проверка на передачу наличие входных данных
     * @private
     */
    private _checkOpenButtonsExistence(): void {
        if (!this.openButtons.length) {
            throw new Error('There is no PopupFacade open buttons in constructor');
        }
    }

    /**
     * Группировка кнопок по названиям ссылок внутри групп
     * @private
     * @param {HTMLElement} button - кнопка для открытия попапа
     * @param {IPopupsGroupsName} group - название группы, к которой относится попап
     * @param {string} link - ссылка на контент попапа (data-attribute)
     */
    private _addToGroup(button: HTMLElement, group: IPopupsGroupsName, link: string): void {
        const linkValue = button.dataset[link];

        if (!linkValue) {
            console.error(`Неверно задан параметр ${link} для ${group}`);
        }

        if (linkValue && !this.popupsGroups[group]?.popups[linkValue]) {
            this.popupsGroups[group].popups[linkValue] = [];
        }
        const popups = linkValue && this.popupsGroups[group].popups[linkValue];

        if (popups) {
            popups.push(button);
        }
    }

    /**
     * Группировка кнопок по data-атрибутам
     * @private
     */
    private _groupPopupsByType(): void {
        this.openButtons.forEach((button: HTMLElement) => {
            if (button.hasAttribute('data-callback')) {
                this._addToGroup(button, 'callback', 'href');
            } else if (button.hasAttribute('data-href') && button.hasAttribute('data-no-hash')) {
                this._addToGroup(button, 'hrefNoHash', 'href');
            } else if (button.hasAttribute('data-href')) {
                this._addToGroup(button, 'href', 'href');
            } else if (button.hasAttribute('data-ajax-online')) {
                this._addToGroup(button, 'online', 'ajax');
            } else if (button.hasAttribute('data-progress')) {
                this._addToGroup(button, 'progressPopup', 'ajax');
            } else if (button.hasAttribute('data-ajax')) {
                this._addToGroup(button, 'ajax', 'ajax');
            } else if (button.hasAttribute('data-gallery')) {
                this._addToGroup(button, 'galleryPopup', 'slider');
            }
        });
    }

    /**
     * Инициализация нужных попапов в зависимости от групп и ссылок кнопок
     * @private
     */
    private _initPopups(): void {
        for (const group in this.popupsGroups) {
            if (Object.prototype.hasOwnProperty.call(this.popupsGroups, group)) {
                const groupName = group as IPopupsGroupsName;

                for (const popup in this.popupsGroups[groupName].popups) {
                    if (Object.prototype.hasOwnProperty.call(this.popupsGroups[groupName].popups, popup)) {
                        Object.assign(this.popupsGroups[group as IPopupsGroupsName].options, {
                            openButtons: this.popupsGroups[group as IPopupsGroupsName].popups[popup]
                        });

                        new this.popupsGroups[groupName].Construct(this.popupsGroups[groupName].options).init();
                    }
                }
            }
        }
    }
}
