import type {IAbAccordion, IAccordionOptions} from './types';
import {EAccordionState} from './types';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';
import ScrollAnimation from '../scrollAnimation';
import {Utils} from '@/common/scripts/utils';

const observer: IObserver = new Observer();
const CLASS_OPEN = 'is-open';
const HEIGHT_CLOSE = 0;

abstract class AbstractAccordion implements IAbAccordion {
    protected options: IAccordionOptions;

    /**
     * Состояние компонента
     * open - открыто
     * close - закрыто
     * @type {string}
     */
    protected isOpen: EAccordionState = EAccordionState.CLOSE;

    /**
     * Высота схлопываемого контента
     * зависит от содержимого
     * @type {number}
     */
    protected height: number;

    /**
     * Целевой элемент
     * @type {HTMLElement}
     */
    private element: Element;

    /**
     * Флаг задержки скрытия контентной части
     * @type {boolean}
     */
    private isCloseTimeout: boolean = false;

    /**
     * Элемент-переключатель
     * @type {Element|null}
     */
    private toggler: Element | null | undefined = null;

    /**
     * Элемент-содержимое
     * Используем его высоту
     * @type {Element|null}
     */
    private content: HTMLElement | undefined;

    /**
     * Элемент-обертка содержимого
     * Его высоту меняем
     * @type {Element|null}
     */
    private contentOuter: HTMLElement | null | undefined = null;

    /**
     * Элемент заголовка
     * @type {Element|null}
     */
    private title: HTMLElement;

    /**
     * Текст заголовка для переключения между состояниями (заголовок/скрыть)
     * @type {string}
     */
    private titleText: string = '';

    /**
     * Текст "скрыть"
     * @type {string}
     */
    private hiddenText: string = 'Скрыть';

    /**
     * Флаг, менять ли заголовок между (заголовок/скрыть)
     * @type {boolean}
     */
    private isChangeTitle: boolean = false;

    /**
     * Значение задержки перед скрытием контентной части
     * @type {number}
     */
    private timeout: number = 300;

    private blockElement: Element;

    constructor() {
        // Привязываем контекст
        this.close = this.close.bind(this);
        this.open = this.open.bind(this);
    }

    /**
     * Инициализирует модуль
     * @param {Object} options - параметры
     */
    public init(options: IAccordionOptions): void {
        this._setElements(options);
        this._setInitState();
        this._subscribe();
        this._bindEvents();
    }

    /**
     * Переинициирует начальное состояние
     */
    public reinit(): void {
        this._setInitState();
    }

    /**
     * Открывает контент
     */
    public open(): void {
        if (this.isChangeTitle && this.titleText) {
            Utils.clearHtml(this.title);
            Utils.insetContent(this.title, this.hiddenText);
        }

        this.element.classList.add(CLASS_OPEN);
        this.isOpen = EAccordionState.OPEN;
        this._setContentHeight(this.height);
        setTimeout(() => {
            observer.publish('accordion:open');
        }, this.timeout);
    }

    /**
     * Закрывает контент
     */
    public close(): void {
        if (this.isChangeTitle && this.titleText) {
            Utils.clearHtml(this.title);
            Utils.insetContent(this.title, this.titleText);
        }

        if (this.blockElement) {
            this._scrollToElement();
        }

        setTimeout(() => {
            this.element.classList.remove(CLASS_OPEN);
            this.isOpen = EAccordionState.CLOSE;
            this._setContentHeight(HEIGHT_CLOSE);
            observer.publish('accordion:close');
        }, this.isCloseTimeout ? this.timeout : 0);
    }

    /**
     * Ставит начальное состояние
     * @private
     */
    protected _setInitState(): void {
        this.height = this._getHeight();
        this.isOpen = this.element.classList.contains(CLASS_OPEN) ? EAccordionState.OPEN : EAccordionState.CLOSE;
        this.titleText = this.title ? (this.element as HTMLElement).dataset['initialTitle'] || '' : '';
        this.isChangeTitle = Boolean((this.element as HTMLElement).dataset['changeTitle']);
        this.hiddenText = (this.element as HTMLElement).dataset['alternateTitle'] || this.hiddenText;

        if (this.title) {
            this.title.innerHTML = this.titleText;
        }
    }

    /**
     * Установка высоты элементу-обертке
     * @param {number} height - высота
     * @private
     */
    protected _setContentHeight(height: number): void {
        if (!this.contentOuter) {
            throw new Error(`Необходимо указать элемент 'contentOuter': ${this.options.selectors.contentOuter}`);
        }

        this.contentOuter.style.height = height ? `${height}px` : `${height}`;
    }

    /**
     * Подписывается на события других модулей
     * ATTENTION! Абстрактный метод
     * @private
     */
    protected _subscribe(): void {
        throw new Error('Метод _subscribe должен быть переопределен');
    }

    /**
     * Определяет элементы из параметров
     * @param {Object} options - параметры
     * @private
     */
    private _setElements(options: IAccordionOptions): void {
        this.options = options;
        const {selectors}: IAccordionOptions = this.options;

        this.element = options.element;
        this.isCloseTimeout = options.isCloseTimeout;
        this.toggler = options.element.querySelector(selectors.toggler);
        this.content = options.element.querySelector(selectors.content) as HTMLElement;
        this.contentOuter = options.element.querySelector(selectors.contentOuter) as HTMLElement;
        this.title = options.element.querySelector(selectors.title) as HTMLElement;
        if (selectors.blockElement) {
            this.blockElement = options.element.closest(selectors.blockElement) as HTMLElement;
        }
    }

    /**
     * Возвращает высоту контента
     * @returns {number} - высота контента
     * @private
     */
    private _getHeight(): number {
        if (!this.content) {
            throw new Error(`Необходимо указать элемент 'content': ${this.options.selectors.content}`);
        }

        return this.content.offsetHeight;
    }

    /**
     * Навешивает обработчики событий
     * @private
     */
    private _bindEvents(): void {
        if (this.toggler) {
            this.toggler.addEventListener('click', this._onTogglerClick.bind(this));
        }

        const events = ['resize', 'orientationchange'];

        events.forEach((event: string) => {
            window.addEventListener(event, this._onResize.bind(this));
        });
    }

    /**
     * Событие, которое происходит при нажатии на переключатель
     * @private
     */
    private _onTogglerClick(): void {
        const method = this.isOpen === EAccordionState.OPEN ? this.close : this.open;

        this.height = this._getHeight();

        method();
    }

    /**
     * Событие, которое происходит при изменении размера/ориентации окна
     * @private
     */
    private _onResize(): void {
        if (this.isOpen) {
            this._update();
        }
    }

    /**
     * Обновляет высоту
     */
    private _update(): void {
        this.height = this._getHeight();
        this._setContentHeight(this.isOpen === EAccordionState.OPEN ? this.height : HEIGHT_CLOSE);
    }

    /**
     * Скролл к началу блока
     */
    private _scrollToElement(): void {
        if (!this.blockElement) {
            return;
        }

        const targetY =
            this.blockElement.getBoundingClientRect().top + pageYOffset -
            ((this.blockElement as HTMLElement).offsetTop * 2);

        const scrollAnimation = new ScrollAnimation({
            targetY
        });

        scrollAnimation.scroll();
    }
}

export default class Accordion extends AbstractAccordion {
    /**
     * Подписывается на события других модулей
     * @private
     */
    override _subscribe(): void {
        observer.subscribe('closeAccordion', this.close);
        observer.subscribe('openAccordion', this.open);
    }

    /**
     * Ставит начальное состояние
     * @private
     */
    override _setInitState(): void {
        super._setInitState();
        this._setContentHeight(this.isOpen === EAccordionState.OPEN ? this.height : HEIGHT_CLOSE);
    }
}
