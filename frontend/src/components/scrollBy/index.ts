import type {IScrollBy, IScrollByOptions} from './types';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';
import ScrollAnimation from '../scrollAnimation';

const observer: IObserver = new Observer();
const ANIMATION_SPEED = 300;

class ScrollBy implements IScrollBy {
    private options: IScrollByOptions = {
        anchors       : [],
        offset        : 0,
        animate       : true,
        speedAnimation: 1000,
        takeElements  : []
    };

    private offset: number;
    public init(options: Partial<IScrollByOptions>): void {
        this.options = Object.assign(this.options, options);
        this.offset = this.options.offset || 0;

        this._initAnchors();
        this._parseHashParams();
    }

    /**
     * Метод скроллит по клику на кнопку с указанными в data-атрибуте id элемента, к которому нужно проскролить
     * @param {object} element - дом-элемент с которого ловится клик
     * @public
     */
    public scrollToHref(element: HTMLElement): void {
        element.addEventListener('click', (event: MouseEvent) => {
            event.preventDefault();

            const id = element.dataset['scrollBy'] || element.getAttribute('href');

            if (!id) {
                return;
            }
            const target = document.querySelector(id);

            if (!target) {
                return;
            }

            this._setActiveClass(element);
            this._setOffsetTakeElements();
            this._scroll(target as HTMLElement);
            this._setGetParams(id);
        });
    }

    /**
     * Метод скроллит к DOM-элементу
     * @param {object} element - дом-элемент к которому необходимо проскролить
     * @public
     */
    public scrollToElement(element: HTMLElement): void {
        this._scroll(element);
    }

    /**
     * Метод скроллит к якорю из гет-параметра
     * @public
     */
    public scrollToGetAnchor(): void {
        this._parseGetParams();
    }

    /**
     * Метод скроллит наверх страницы
     * @public
     */
    public scrollToTop(): void {
        this._scroll();
    }

    /**
     * Метод инициализирует якоря из массива.
     * @private
     */
    private _initAnchors(): void {
        if (!this.options.anchors.length) {
            return;
        }

        this.options.anchors.forEach((item: Element) => {
            this.scrollToHref(item as HTMLElement);
        });
    }

    /**
     * Метод выполняет анимацию скролла
     * @param {object} element - дом-элемент к которому необходимо проскролить
     * @private
     */
    private _scroll(element?: HTMLElement): void {
        const x = 0;
        const y = element ? (element.getBoundingClientRect().top + scrollY) - this.offset : 0;

        observer.publish('scrollBy:start');

        if (this.options.animate) {
            const scrollAnimation = new ScrollAnimation({
                duration: this.options.speedAnimation,
                targetY : y
            });

            scrollAnimation.scroll();

            setTimeout(() => {
                observer.publish('scrollBy:finish');
            }, this.options.speedAnimation);
        }

        if (!this.options.animate) {
            window.scrollTo(x, y);
        }
    }

    /**
     * Метод парсит гет-параметры
     * @private
     */
    private _parseGetParams(): void {
        const get = window.location.search;

        if (get) {
            const url = new URLSearchParams(get);

            for (const item of url.entries()) {
                if (item[0] === 'anchor') {
                    const element = document.querySelector(`#${item[1]}`);

                    this._scroll(element as HTMLElement);
                }
            }
        }
    }

    /**
     * Метод парсит hash-параметр
     * @private
     */
    private _parseHashParams(): void {
        const hash = window.location.hash;
        const isScroll = Boolean(hash) && this.options.anchors.reduce((isSame: boolean, item: Element) => {
            return isSame || (item as HTMLLinkElement).href === hash;
        }, false);

        if (isScroll) {
            const element = document.querySelector(`${hash}`);

            if (element) {
                document.addEventListener('readystatechange', () => {
                    if (document.readyState === 'complete') {
                        setTimeout(() => {
                            this._setOffsetTakeElements();
                            this._scroll(element as HTMLElement);
                        }, ANIMATION_SPEED);
                    }
                });
            }
        }
    }

    /**
     * Устанавливает отступ в зависимости от высоты переданного элемента и дополнительной настройки
     * @private
     */
    private _setOffsetTakeElements(): void {
        if (this.options.takeElements.length) {
            let sum = 0;

            this.options.takeElements.forEach((item: Element) => {
                sum = sum + (item as HTMLElement).offsetHeight;
            });

            this.offset = -this.offset + sum;
        }
    }

    /**
     * Устанавливает активный класс для якоря
     * @param {object} element - дом-элемент по которому произошел клик
     * @private
     */
    private _setActiveClass(element: HTMLElement): void {
        if (!this.options.anchors.length) {
            return;
        }

        this.options.anchors.forEach((item: HTMLElement) => {
            item.classList.remove('is-active');
        });

        element.classList.add('is-active');
    }

    /**
     * Устанавливает id якоря как гет-параметр
     * @param {id} id - id якоря
     * @private
     */
    private _setGetParams(id: string): void {
        document.location.hash = id;
    }
}

export default ScrollBy;
