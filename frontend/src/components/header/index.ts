import type {IHeader, IHeaderOptions} from './types';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';
import {Utils} from '@/common/scripts/utils';

const observer: IObserver = new Observer();
const CLASS_FIXED: string = 'is-fixed';
const CLASS_TRANSPARENT: string = 'header_theme_transparent';
const CLASS_NO_SHADOW: string = 'header_theme_no-shadow';
const CLASS_FULL_SCREEN_NAV: string = 'header_theme_fullscreen';
const DOCUMENT_ROOT: HTMLElement = document.documentElement;

class Header implements IHeader {
    private header: HTMLElement;
    private targetScroll: HTMLElement[] | undefined;
    private currentTargetScrollVersion: string = '';
    private heightHeader: number;
    private scrollPosition: number = 0;
    private sections: Element[] = [];
    private isOpenFilter: boolean = false;
    private noShadowHeader: boolean;
    private section: HTMLElement[] = [...document.querySelectorAll('.j-anchor-section')] as HTMLElement[];
    private scrollByStart: boolean;
    private prevScrollPosition: number;

    public init(options: IHeaderOptions): void {
        this.header = options.headerWrap;
        this.targetScroll = options.targetScroll;
        this.heightHeader = this.header.offsetHeight;
        this.noShadowHeader = this.header.classList.contains('j-header-alternative');

        this._initState();
        this._subscribes();
        this._bindEvents();
    }

    /**
     * Обрабатывает события.
     */
    private _bindEvents(): void {
        window.addEventListener('scroll', Utils.debounce(() => {
            if (!this.isOpenFilter) {
                this._changeState();
                this._changeStateAnchor();
            }
        }, 10));

        window.addEventListener('resize', Utils.debounce(() => {
            if (!this.isOpenFilter) {
                this._setHeight();
                this._changeState();
                this._changeStateAnchor();
            }
        }, 10));
    }

    /**
     * Метод содержит в себе колбэки на события других модулей.
     * @private
     */
    private _subscribes(): void {
        observer.subscribe('navigation:ready', () => {
            this._setHeight();
        });

        observer.subscribe('scrollBy:start', () => {
            this.scrollByStart = true;
        });

        observer.subscribe('scrollBy:finish', () => {
            this.scrollByStart = false;
        });
    }

    /**
     * Ставит изначальное состояние
     * @private
     */
    private _initState(): void {
        if (!this.noShadowHeader) {
            this._changeState();
        }

        this._checkFullScreenNavigation();
        this._setHeight();
    }

    /**
     * Проверяет, используется ли версия полноэкранного меню для всех устройств
     * @private
     */
    private _checkFullScreenNavigation(): void {
        const fullScreenNavi = this.header.querySelector('.j-navigation-fullscreen__menu');

        if (fullScreenNavi && !this.header.classList.contains(CLASS_FULL_SCREEN_NAV)) {
            this.header.classList.add(CLASS_FULL_SCREEN_NAV);
        }
    }

    /**
     * Ставит высоту шапки в CSS-переменную
     * @private
     */
    private _setHeight(): void {
        DOCUMENT_ROOT.style.setProperty('--header-height', `${this.header.clientHeight}px`);
    }

    /**
     * Изменяет состояние хэдера
     * @private
     */
    private _changeState(): void {
        this.scrollPosition = window.scrollY;

        if (this._isScrollInTarget()) {
            this.header.classList.remove(CLASS_FIXED);
            this.currentTargetScrollVersion !== '3' && this.header.classList.add(CLASS_TRANSPARENT);
        } else {
            this.header.classList.add(CLASS_FIXED);
            this.header.classList.remove(CLASS_TRANSPARENT);
        }

        this.prevScrollPosition = this.scrollPosition;

        if (this.scrollPosition < 100 && this.noShadowHeader) {
            this.header.classList.add(CLASS_NO_SHADOW);
        } else {
            this.header.classList.remove(CLASS_NO_SHADOW);
        }
    }

    /**
     * Проверяет скролл вниз
     * @returns {boolean} - true - скролл вниз
     */
    private _isScrollDown(): boolean {
        const stepScrollBottom = 1;

        // если скролл больше высоты хэдера, и разница между текущим и предыдущим значением скролла больше константы
        return this.scrollPosition > this.heightHeader &&
            (this.scrollPosition - this.prevScrollPosition) > stepScrollBottom;
    }

    /**
     * Проверяет, является ли текущий сколл в рамках элемента-таргета
     * @returns {boolean} - true - скролл есть
     */
    private _isScrollInTarget(): boolean {
        if (!this.targetScroll?.length) {
            return false;
        }

        return this.targetScroll.some((element: HTMLElement) => {
            const isTarget = element.getBoundingClientRect().top <= 0 &&
                element.getBoundingClientRect().top + element.getBoundingClientRect().height >= 0;

            if (isTarget) {
                this.currentTargetScrollVersion = element.dataset['version'] || '';
            }

            return isTarget;
        });
    }


    /**
     * Меняет состояние якорей при скролле по странице.
     * @private
     */
    private _changeStateAnchor(): void {
        if (!this.section.length) {
            return;
        }

        this.section.forEach((item: HTMLElement) => {
            // если блок в 1/3 экрана, то состояние изменится (меньше не делать, иначе будут проблемы с мелкими секциями)
            const safeZone = window.innerHeight / 3;
            const sectionTop = item.getBoundingClientRect().top + pageYOffset - safeZone;

            if (this.scrollPosition > sectionTop) {
                if (!this.sections.includes(item)) {
                    this.sections.push(item);
                }
            }

            if (this.scrollPosition < sectionTop) {
                if (this.sections.includes(item)) {
                    this.sections.splice(this.sections.length - 1, 1);
                }
            }

            // @ts-ignore
            const sectionId = this.sections.length ? `#${this.sections[this.sections.length - 1].id}` : false;

            if (!this.scrollByStart && sectionId) {
                observer.publish('anchorChangeSection', sectionId);
            }
        });
    }
}

export default Header;
