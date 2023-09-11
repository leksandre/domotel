import type {INavigationGroups, INavigationGroupsElement, INavigationGroupsOptions} from './types';
import {Utils} from '@/common/scripts/utils';

const CLASS_VISIBLE = 'is-show';
const CLASS_GROUP = 'is-group';
const CLASS_ACTIVE = 'is-active';
const CLASS_OPEN = 'is-open';

class NavigationGroup implements INavigationGroups {
    private navigation: HTMLElement;
    private readonly navigationSelector: string;
    private readonly navigationModify: string;
    private navigationItems: Element[];
    private windowWidth: number = Utils.getWindowWidth();

    constructor(options: INavigationGroupsOptions) {
        this.navigation = options.navigation;
        this.navigationSelector = options.navigationSelector || '.j-group-navigation__navigation';
        this.navigationModify = options.navigationModify || 'navigation_theme_group';
        this.navigationItems = [...this.navigation.querySelectorAll('.j-group-navigation__item')];
    }

    public init(): void {
        this._setNavigationItems();
        this._setState();
        this._bindEvents();
        this._setActive();
    }

    private _setNavigationItems(): void {
        this.navigationItems.forEach((item: Element) => {
            (item as INavigationGroupsElement).isGroup = Boolean((item as HTMLElement).dataset['group']) || false;
            (item as INavigationGroupsElement).show = Number((item as HTMLElement).dataset['show']) || false;
            (item as INavigationGroupsElement).groupStart = Number((item as HTMLElement).dataset['groupStart']) ||
                false;
            (item as INavigationGroupsElement).groupEnd = Number((item as HTMLElement).dataset['groupEnd']) || false;
            (item as INavigationGroupsElement).navigation = item.querySelector(this.navigationSelector) || null;
        });
    }

    /**
     * Устанавливает группировку и активность для каждого элемента
     * @private
     */
    private _setState(): void {
        this.navigationItems.forEach((element: Element) => {
            const item = element as INavigationGroupsElement;

            item.classList.remove(CLASS_OPEN);
            item.isVisible = item.show ? this.windowWidth >= item.show : false;

            item.isVisible ?
                item.classList.add(CLASS_VISIBLE) :
                item.classList.remove(CLASS_VISIBLE);

            if (item.isGroup) {
                item.isGroupActive =
                    (item.groupEnd ? this.windowWidth < item.groupEnd : true) &&
                    (item.groupStart ? this.windowWidth > item.groupStart : true);

                if (item.isGroupActive) {
                    item.classList.add(CLASS_GROUP);

                    if (this.navigationModify) {
                        (item.navigation as HTMLElement).classList.add(this.navigationModify);
                    }
                } else {
                    item.classList.remove(CLASS_GROUP);

                    if (this.navigationModify) {
                        (item.navigation as HTMLElement).classList.remove(this.navigationModify);
                    }
                }
            }
        });
    }

    /**
     * Проставляет класс активности при готовности
     * @private
     */
    private _setActive(): void {
        this.navigation.classList.add(CLASS_ACTIVE);
    }

    /**
     * Устанавливает события: при изменении экрана, навешивает обработчик по клику
     * @private
     */
    private _bindEvents(): void {
        this._bindActionsEvents();
        this._windowSizeEvents();
    }

    /**
     * Обработчик клика.
     * Добавляет клик если элемент нужно группировать и если группировка активна (брейки)
     * @private
     */
    private _bindActionsEvents(): void {
        this.navigationItems.forEach((element: Element) => {
            const item = element as INavigationGroupsElement;

            if (item.isGroup && item.isGroupActive) {
                item.addEventListener('mouseover', () => {
                    this._stateHandler(item);
                });

                item.addEventListener('mouseout', () => {
                    this._stateHandler(item, false);
                });
            }
        });
    }

    /**
     * Обработчик при изменении экрана
     * @private
     */
    private _windowSizeEvents(): void {
        const events = ['resize', 'orientationchange'];

        events.forEach((event: string) => {
            window.addEventListener(event, () => {
                this.windowWidth = Utils.getWindowWidth();
                this._setState();
                this._bindActionsEvents();
            });
        });
    }

    /**
     * Обработчик состояния меню
     * @param {Object} item - активный элемент меню
     * @param {boolean} show - флаг активности
     * @private
     */
    private _stateHandler(item: INavigationGroupsElement, show: boolean = true): void {
        if (!item.isGroupActive) {
            return;
        }

        const method = show ? 'add' : 'remove';
        const direction = this._checkDirection(item);

        item.classList[method](direction, CLASS_OPEN);
    }

    private _checkDirection(item: INavigationGroupsElement): string {
        return (item.navigation as HTMLElement).getBoundingClientRect().left +
            (item.navigation as HTMLElement).offsetWidth + 50 < this.windowWidth ?
            'is-right' :
            'is-left';
    }
}

export default NavigationGroup;
