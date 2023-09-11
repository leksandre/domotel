import type {ITabData, ITabs, ITabsOptions} from './types';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';

const observer: IObserver = new Observer();
const CLASS_ACTIVE = 'is-active';

class Tabs implements ITabs {
    /**
     * Обертка табов
     * нужна на случай множественных табов на странице
     * по дефолту - document
     * @type {HTMLElement | null}
     */
    private elem: HTMLElement | Document;

    /**
     * Надо ли открывать таб по хэшу
     * @type {boolean}
     */
    private hash: boolean = false;

    /**
     * Идентификатор таба, который нужно показать первым
     * если нет хэша, первым будет показан этот таб
     * если его нет, будет показан первый таб или таб с активным классом
     * @type {string | null}
     */
    private initialTab: string | null;

    /**
     * Родительская нода табов
     * @type {Element | null}
     */
    private tabsParent: Element | null;

    /**
     * Массив табов
     * @type {Array}
     */
    private tabs: HTMLElement[];

    /**
     * Родительская нода контента
     * @type {Node}
     */
    private contentsParent: Element | null;

    /**
     * Массив блоков контента
     * @type {HTMLElement[]}
     */
    private contents: HTMLElement[];

    /**
     * Атрибуты активного таба
     * @type {
     *     id: string;
     *     tab: HTMLElement;
     * }
     */
    private dataTab: ITabData;

    private onChangeCallback: ((param?: ITabData) => unknown) | false;
    private tabItem: string;
    private contentItem: string;

    public init(options: ITabsOptions): void {
        this._setOptions(options);
        this._setInitialState();
        this._bindContext();
        this._bindEvents();
    }

    /**
     * Применяет параметры
     * @private
     * @param {ITabsOptions} options - параметры
     */
    private _setOptions(options: ITabsOptions): void {
        this.elem = options.elem || document;
        this.hash = Boolean(options.hash);
        this.initialTab = options.initialTab || null;
        this.onChangeCallback = options?.onChangeCallback || false;
        this.tabItem = options.tabItem ?? '.j-tabs__item';
        this.contentItem = options.contentItem || '.j-tabs-content__item';

        this.tabsParent = this.elem.querySelector(options.tabsParent || '.j-tabs') || null;
        this.tabs = this.tabsParent ? Array.from(this.tabsParent.querySelectorAll(this.tabItem)) : [];

        this.contentsParent = this.elem.querySelector(options.contentsParent || '.j-tabs-content') || null;
        this.contents = this.contentsParent ? Array.from(this.contentsParent.querySelectorAll(this.contentItem)) : [];
    }

    /**
     * Ставит начальное состояние
     * @private
     */
    private _setInitialState(): void {
        let tab = this.tabs.find((item: Element) => {
            return item.classList.contains(CLASS_ACTIVE);
        }) || this.tabs[0];

        if (this.hash) {
            const hash = this._getHash();

            tab = hash ?
                this.tabs.find((item: Element) => {
                    return (item as HTMLElement).dataset['tab'] === hash;
                }) || tab :
                tab;
        } else if (this.initialTab) {
            tab = this.tabs.find((item: Element) => {
                return (item as HTMLElement).dataset['tab'] === this.initialTab;
            }) || tab;
        }

        this.toggleTab(tab as HTMLElement);
    }

    /**
     * Привязывает контекст
     * @private
     */
    private _bindContext(): void {
        this._onTabs = this._onTabs.bind(this);
    }

    /**
     * Привязывает события
     * @private
     */
    private _bindEvents(): void {
        this.tabsParent?.addEventListener('click', this._onTabs);
    }

    /**
     * Обрабатывает событие клика на таб
     * @private
     * @param {Object} event - объект события
     */
    private _onTabs(event: Event): void {
        event.preventDefault();

        const target = event.target as HTMLElement;
        const tab = target?.closest(this.tabItem);

        if (!tab) {
            return;
        }

        this.toggleTab(tab);
    }

    /**
     * Получает значение хэша
     * @private
     * @returns {string} - значение хэша
     */
    private _getHash(): string {
        return window.location.hash.replace(/^#/u, '');
    }

    /**
     * Переключает таб
     * @private
     * @param {Node} tab - нода активного таба
     */
    private toggleTab(tab: Element): void {
        this._setData(tab as HTMLElement);
        this._changeState();

        if (this.onChangeCallback) {
            this.onChangeCallback(this.dataTab);
        }
    }

    /**
     * Записывает атрибуты активного таба
     * @private
     * @param {HTMLElement} tab - нода активного таба
     */
    private _setData(tab: HTMLElement): void {
        if (!tab) {
            return;
        }

        this.dataTab = {
            tab,
            id: tab.dataset['tab'] || ''
        };
    }

    /**
     * Переключает классы всем задействованным элементам
     * @private
     */
    private _changeState(): void {
        const id = this.dataTab.id;

        this.tabs.forEach((elem: HTMLElement) => {
            elem.classList.toggle(CLASS_ACTIVE, elem.dataset['tab'] === id);
        });
        this.contents.forEach((elem: HTMLElement) => {
            elem.classList.toggle(CLASS_ACTIVE, elem.dataset['tab'] === id);

            if ((elem as HTMLElement).dataset['tab'] === id) {
                observer.publish('tabChange', elem);
            }
        });
    }
}

export default Tabs;
