/**
 * @version 2.0
 * @author Kelnik Studios {http://kelnik.ru}
 * @link https://kelnik.gitbooks.io/kelnik-documentation/front-end/components/slider.html
 */

/**
 * DEPENDENCIES
 */
import type {ICaptionData, ICaptionSize, ISlider, ISliderElements, ISliderOptions, ISlidesData} from './types';
import arrowTemplate from './templates/arrows-tmpl.twig';
import captionTemplate from './templates/caption-tmpl.twig';
import counterTemplate from './templates/counter-tmpl.twig';
import dotsTemplate from './templates/dots-tmpl.twig';
import {EElementPosition} from './types';
import Hammer from 'hammerjs';
import type {IDynamicImport} from '@/common/scripts/types/utils';
import InitPlanoplan from '../planoplan';
import type {IObserver} from '@/common/scripts/types/observer';
import type {IResponsive} from './types';
import Observer from '@/common/scripts/observer';
import slideTemplate from './templates/slides-tmpl.twig';
import {Utils} from '@/common/scripts/utils';

const observer: IObserver = new Observer();
// Класс активного элемента. Задаётся активной точке, показанному в данный момент слайду
const CLASS_ACTIVE = 'is-active';
// Класс неактивного элемента. Задаётся стрелкам, в случае если на нее нельзя нажать (последний слайд)
const CLASS_DISABLED = 'is-disabled';
// Класс скрытого элемента. Задаётся стрелкам, в случае если слайдов меньше, чем возможно отобразить.
const CLASS_HIDDEN = 'is-hidden';
const CLASS_DOTS_WRAP = '.j-slider-dots';

class Slider implements ISlider {
    /**
     * Нода слайдера
     */
    public slider: HTMLElement;
    private slidesWrap: string;
    private captionWrap: string;
    private widgetWrap: string;

    /**
     * Номер активного слайда - индекс. При загрузке страницы это первый слайд.
     * @type {number}
     */
    private activeSlide: number = 0;
    private isArrow: boolean;
    private arrowLeft: HTMLElement;
    private arrowRight: HTMLElement;
    private isDots: boolean;
    private isCounter: boolean;
    private noSwipe: boolean;
    private isAutoplay: boolean;
    private autoplaySpeed: number;
    private stopOnClick: boolean;
    private infinityLoop: boolean = false;
    private isCaption: boolean;
    private slideCount: number;
    private arrowPosition: EElementPosition;
    private counterPosition: EElementPosition;
    private dotsPosition: EElementPosition;
    private planoplanElements: HTMLElement[] = [];
    private slideVisible: number;
    private slidesItems: HTMLElement[];
    private slidesModify: string | boolean;
    private innerModify: string | boolean;
    private duration: string;
    private modify: string | false;
    private responsive: IResponsive | false;
    private innerData: string | boolean;
    private slidesHtml: string;
    private arrowHtml: string;
    private captionHtml: string;
    private counterHtml: string;
    private dotsHtml: string;
    private currentBreakpoint: number;
    private captionData: ICaptionSize;
    private slidesData: ISlidesData;
    private breakpointArray: number[];

    // eslint-disable-next-line complexity, max-statements
    public init(options: ISliderOptions): void {
        this.slider = options.slider;
        this.slidesWrap = options?.slidesWrap ?? '.j-slides';
        this.captionWrap = options?.captionWrap ?? '.j-caption';
        this.widgetWrap = options?.widgetWrap ?? '.j-slider__widget';
        this.slidesItems = Array.prototype.slice.call(this.slider.querySelector(this.slidesWrap)?.children);
        this.slideCount = options?.slideCount ?? 1;
        this.arrowPosition = options?.arrowPosition ?? EElementPosition.slide;
        this.counterPosition = options?.counterPosition ?? EElementPosition.caption;
        this.dotsPosition = options?.dotsPosition ?? EElementPosition.caption;
        this.isCaption = options?.isCaption ?? false;
        this.slideVisible = options?.slideVisible ?? 1;
        this.modify = options?.modify ?? false;
        this.slidesModify = options?.slidesModify ?? false;
        this.innerModify = options?.innerModify ?? false;
        this.innerData = options.innerData || false;
        this.isDots = (options.dots && this.slidesItems.length > 1) || false;
        this.duration = options.duration || '0.3s';
        this.responsive = options?.responsive ?? false;
        this.noSwipe = options?.noSwipe ?? false;
        this.isAutoplay = (options?.autoplay && this.slidesItems.length > this.slideCount) || false;
        this.isArrow = (options.arrow && this.slidesItems.length > this.slideCount) || false;
        this.isCounter = (options.counter && this.slidesItems.length > this.slideCount) || false;
        this.autoplaySpeed = options?.autoplaySpeed ?? 3000;
        this.stopOnClick = options.stopOnClick || false;
        this.infinityLoop = this.responsive ? false : options.infinityLoop || false;

        this._getBreakpoints();
        this._setCountSlides();
        this._createSlider();
        this._addSlider();
        this._initSliderPopup();
        this._getSliderParameters();
        this._bindEvents();
        this._disableArrow();
        this._initInfinityLoop();
        this._setActiveClass();
        this._ready();

        if (this.isAutoplay) {
            this._slideAutoplay();
        }
    }

    /* eslint-enable */

    /**
     * Создаём элементы галереи
     * @private
     */
    private _createSlider(): void {
        this.slider.style.opacity = `0`;

        this._createSlides();

        if (this.isCaption) {
            this._createCaption();
        }

        if (this.isDots) {
            this._createDots();
        }

        if (this.isArrow) {
            this._createArrows();
        }

        if (this.isCounter) {
            this._createCounter();
        }
    }

    /**
     * Добавляем элементы галереи на страницу
     * @private
     */
    private _addSlider(): void {
        Utils.clearHtml(this.slider);
        Utils.insetContent(this.slider, this.slidesHtml);

        if (this.isCaption) {
            Utils.insetContent(this.slider.lastElementChild as HTMLElement, this.captionHtml);
        }

        if (this.isDots) {
            this._addDots();
        }

        if (this.isArrow) {
            this._addArrows();
        }

        if (this.isCounter) {
            this._addCounter();
        }
    }

    private _initSliderPopup(): void {
        const sliderPopups = [...this.slider.querySelectorAll('.j-popup-slider')];

        if (sliderPopups.length) {
            // @ts-ignore
            import(/* webpackChunkName: "popup-facade" */ '../popup/popupFacade')
                .then(({default: PopupFacade}: IDynamicImport) => {
                    const options = {
                        ...{openButtons: sliderPopups}
                    };

                    new PopupFacade(options).init();
                });
        }
    }

    /**
     * Добавляем стрелки в дом
     * @private
     */
    private _addArrows(): void {
        let element = this.slider.firstElementChild as HTMLElement;

        switch (this.arrowPosition) {
            case EElementPosition.slide:
            default:
                break;
            case EElementPosition.allSlider:
                element = this.slider;
                break;
            case EElementPosition.caption:
                element = this.slider.lastElementChild as HTMLElement;
                break;
        }

        Utils.insetContent(element, this.arrowHtml);
        this.arrowLeft = this.slider.querySelector('.j-arrow-left') as HTMLElement;
        this.arrowRight = this.slider.querySelector('.j-arrow-right') as HTMLElement;
    }

    private _addDots(): void {
        let element = this.slider;

        switch (this.dotsPosition) {
            case EElementPosition.caption:
                element = this.slider.lastElementChild as HTMLElement;
                break;
            default:
                break;
        }

        Utils.insetContent(element, this.dotsHtml);
    }

    private _addCounter(): void {
        let element = this.slider;

        switch (this.counterPosition) {
            case EElementPosition.slide:
                element = this.slider.firstElementChild as HTMLElement;
                break;
            case EElementPosition.caption:
                element = this.slider.lastElementChild as HTMLElement;
                break;
            default:
                break;
        }

        Utils.insetContent(element, this.counterHtml);
    }

    /**
     * Создаём основные большие слайды
     */
    private _createSlides(): void {
        this.slidesHtml = this._createElement(slideTemplate, this.slidesWrap, this.slideVisible);
    }

    /**
     * Создаём подписи к слайдеру
     * @private
     */
    private _createCaption(): void {
        const captionData = this._getCaptionData();

        if (captionData.hasItems) {
            // @ts-ignore
            this.captionHtml = captionTemplate(captionData);
        } else {
            this.isCaption = false;
            this.slider.classList.add('slider__caption_is_empty');
        }
    }

    /**
     * Создаём точки для слайдера
     * @private
     */
    private _createDots(): void {
        // @ts-ignore
        this.dotsHtml = dotsTemplate({items: this.slidesItems});
    }

    /**
     * Создаём счётчик слайдов
     * @private
     */
    private _createCounter(): void {
        // @ts-ignore
        this.counterHtml = counterTemplate({items: this.slidesItems});
    }

    /**
     * Создаём стрелки слайдера
     * @private
     */
    private _createArrows(): void {
        // @ts-ignore
        this.arrowHtml = arrowTemplate();
    }

    // @ts-ignore
    /**
     * Создаём элемент галереи - блок со слайдерами или блок с тумбочками
     * @param {object} template - twig шаблон
     * @param {string} wrapClass - класс обертки для тумбочек или слайдов
     * @param {number} items - количество элементов к показу (по умолчанию один слайд)
     * @returns {string} - разметка блока галереи
     * @private
     */
    private _createElement(template: any, wrapClass: string, items: number): string {
        return template(this._getElements(wrapClass, items));
    }

    /**
     * Получаем элементы галереи - для слайдов или тумбочек
     * @param {string} wrapClass - класс обертки
     * @param {number|string} items - количество элементов к показу (по умолчанию один слайд)
     * @returns {object} - объект
     * @private
     */
    private _getElements(wrapClass: string, items: number): ISliderElements {
        const substringNumber = 1;
        const elementsArray = [...(this.slider.querySelector(wrapClass) as HTMLElement)?.children] as HTMLElement[];
        const stringArray = elementsArray.map((element: HTMLElement) => {
            return element.outerHTML;
        });

        return {
            wrap       : wrapClass.substring(substringNumber),
            width      : `${this._getWidth(items)}%`,
            items      : stringArray,
            modify     : this.modify ? this.modify : false,
            innerModify: this.innerModify,
            innerData  : this.innerData
        };
    }

    /**
     * Вычисляем ширину тумбочки
     * @param {number} items - количество элементов к показу (по умолчанию один слайд)
     * @returns {number} - ширина тумбочки в процентах
     * @private
     */
    private _getWidth(items: number): number {
        const percent = 100;

        return percent / items;
    }

    private _getCaptionData(): ICaptionData {
        const slides = [].slice.call(this.slider.querySelector(this.slidesWrap)?.children);
        const widthItem = this._getWidth(this.slideVisible);
        const captionText = slides.map((item: HTMLElement) => {
            return item.dataset['caption'];
        });

        return {
            width   : `${widthItem}%`,
            items   : captionText,
            hasItems: captionText.join('').length > 0
        };
    }

    /**
     * Привязываем события
     * @private
     */
    private _bindEvents(): void {
        this._slideEvents();

        if (this.isDots) {
            this._dotsEvents();
        }

        if (this.isArrow) {
            this._arrowEvents();
        }

        document.addEventListener('keyup', (event: KeyboardEvent) => {
            const keyCodeLeft = 37;
            const keyCodeRight = 39;

            // eslint-disable-next-line max-len
            if (this.slider && (this.slider.classList.contains('is-full-size') || this.slider.classList.contains('slider_theme_progress'))) {
                if (event.keyCode === keyCodeLeft) {
                    this._onTurnSlide(-1);
                } else if (event.keyCode === keyCodeRight) {
                    this._onTurnSlide(1);
                }
            }
        });

        window.addEventListener('resize', this._resizeWindow.bind(this));
    }

    /**
     * Вешаем события на сам слайд - свайп
     * @private
     */
    private _slideEvents(): void {
        const respSlideCount: number | false | undefined = typeof this.responsive === 'object' &&
            this.responsive[this.currentBreakpoint];

        if (this.noSwipe || this.slidesItems.length === 1 ||
            (respSlideCount && this.slidesItems.length <= respSlideCount)) {
            return;
        }

        // eslint-disable-next-line no-undef
        const touchEvent: HammerManager = new Hammer(this.slider);

        touchEvent.on('swiperight', this._onSwipe.bind(this));
        touchEvent.on('swipeleft', this._onSwipe.bind(this));
    }

    /**
     * Вешаем события на точки
     * @private
     */
    private _dotsEvents(): void {
        const dotsArray = Array.prototype.slice.call(this.slider.querySelector('.j-slider-dots')?.children);

        dotsArray.forEach((dot: HTMLElement) => {
            dot.addEventListener('click', this._onDots.bind(this));
        });
    }

    /**
     * Вешаем события на стрелки галереи
     * @private
     */
    private _arrowEvents(): void {
        const arrowsArray = Array.prototype.slice.call(this.slider.querySelector(`.j-slider-arrows`)?.children);

        arrowsArray.forEach((arrow: HTMLElement) => {
            arrow.addEventListener('click', this._onArrow.bind(this));
        });
    }

    /**
     * Отрабатываем тач события свайпа
     * @param {event} event - событие
     * @private
     */
    // eslint-disable-next-line no-undef
    private _onSwipe(event: TouchInput): void {
        const left = -1;
        const right = 1;
        const slideDirection = event.type === 'swiperight' ? left : right;

        this._onTurnSlide(slideDirection);
    }

    /**
     * Отрабатываем событие клика по стрелке
     * @param {event} event - событие
     * @private
     */
    private _onArrow(event: MouseEvent): void {
        const arrow = (event.target as HTMLElement)?.closest('.j-arrow');

        if (!arrow) {
            return;
        }
        const slideDirection = Utils.getElementIndex(arrow) ? 1 : -1;

        this._onTurnSlide(slideDirection);
    }

    /**
     * Переключение слайдера
     * @param {number} direction - в каком направлении переключать (1-next, -1-prev)
     */
    // eslint-disable-next-line max-statements
    private _onTurnSlide(direction: 1 | -1): void {
        this._getSlidesParam();

        if (this._checkTransform(direction)) {
            this._changeActive(direction);
            this._slideTo(this.duration);

            if (this.isCaption) {
                this._captionTo(this.duration);
            }
        } else if (this.infinityLoop) {
            this._changeActive(direction);
            this._slideTo(this.duration);

            if (this.isCaption) {
                this._captionTo(this.duration);
            }

            this._rewindSlider();
        }

        this._disableArrow();

        if (this.slider.classList.contains('is-full-size')) {
            const slides: HTMLElement[] = [].slice.call(this.slider.querySelector(this.slidesWrap)?.children);
            const slide = slides[this.activeSlide];

            if (!slide) {
                return;
            }
            const picture = slide.querySelector('picture');

            if (picture) {
                const sources = [].slice.call(picture.querySelectorAll('source'));

                sources.forEach((source: HTMLElement) => {
                    if (source.dataset['big']) {
                        source.setAttribute('srcset', source.dataset['big']);
                    }
                });
            }
        }
    }

    /**
     * Отрабатываем событие клика по точке
     * @param {event} event - событие
     * @private
     */
    private _onDots(event: MouseEvent): void {
        const dot = event.currentTarget;

        if (!dot) {
            return;
        }

        const dotIndex = Utils.getElementIndex(dot as HTMLElement);

        this._setActive(dotIndex);
        this._slideTo(this.duration);

        if (this.isCaption) {
            this._captionTo(this.duration);
        }
    }

    /**
     * Дисейблим первую или последнюю стрелку
     * @private
     */
    private _disableArrow(): void {
        if (!this.arrowLeft || !this.arrowRight || this.infinityLoop) {
            return;
        }

        const firstSlideIndex = 0;
        const lastSlideIndex = this.slidesItems.length - Math.floor(this.slideVisible);

        this.arrowLeft.classList.remove(CLASS_HIDDEN, CLASS_DISABLED);
        this.arrowRight.classList.remove(CLASS_HIDDEN, CLASS_DISABLED);

        if (this.slideVisible > this.slidesItems.length || this.slidesItems.length === 1) {
            this.arrowLeft.classList.add(CLASS_HIDDEN);
            this.arrowRight.classList.add(CLASS_HIDDEN);
        } else if (this.slidesItems.length === this.slideVisible) {
            this.arrowLeft.classList.add(CLASS_DISABLED);
            this.arrowRight.classList.add(CLASS_DISABLED);
        } else if (this.activeSlide === firstSlideIndex) {
            this.arrowLeft.classList.add(CLASS_DISABLED);
            this.arrowRight.classList.remove(CLASS_DISABLED);
        } else if (this.activeSlide === lastSlideIndex) {
            this.arrowRight.classList.add(CLASS_DISABLED);
        }
    }

    /**
     * Инициализация виджета planoplan
     * @private
     * @param {object} element - Объект планоплана требующий инициализации
     *
     */
    private _initPlanoplan(element: HTMLElement): void {
        const widget = element.querySelector(this.widgetWrap) as HTMLElement;
        const planoplan = new InitPlanoplan(widget);

        planoplan.init();
    }

    /**
     * Вешаем IntersectionObserver на слайдеры с планопланом
     */
    private _observePlanoplan(): void {
        if ('IntersectionObserver' in window) {
            const lazyPlanoplanObserver = new IntersectionObserver((entries: IntersectionObserverEntry[]) => {
                entries.forEach((entry: IntersectionObserverEntry) => {
                    if (entry.isIntersecting) {
                        this.planoplanElements.forEach((element: HTMLElement) => {
                            this._initPlanoplan(element);
                        });
                        lazyPlanoplanObserver.unobserve(this.slider);
                    }
                });
            });

            if (Utils.isInViewport(this.slider, window.innerHeight || document.documentElement.clientHeight)) {
                this.planoplanElements.forEach((element: HTMLElement) => {
                    this._initPlanoplan(element);
                });
            } else {
                lazyPlanoplanObserver.observe(this.slider);
            }
        } else {
            // Если нет поддержки IntersectionObserver - инициализируем сразу
            this.planoplanElements.forEach((element: HTMLElement) => {
                this._initPlanoplan(element);
            });
        }
    }

    /**
     * Проверка на возможность скроллить слайды
     * @param {number} direction - единица (следующий слайд) или минус единица (предыдущий слайд)
     * @returns {boolean} - true: можем скроллить галерею, false: уперлись в крайние значения первого или последнего слайда
     * @private
     */
    private _checkTransform(direction: 1 | -1): boolean {
        const zeroIndex = 0;
        const offset = this.infinityLoop ? this.slideVisible : 0;
        const slidesItemsFinal = this.infinityLoop ? 0 : Math.floor(this.slideVisible) - 1;
        const nextSlide = this.activeSlide + direction - offset;

        return nextSlide >= zeroIndex && nextSlide + slidesItemsFinal !== this.slidesItems.length;
    }

    /**
     * меняем номер активного слайда в большую или меньшую сторону на единицу
     * @param {number} direction - единица или минус единица.
     * @private
     */
    private _changeActive(direction: 1 | -1): void {
        this.activeSlide = this.activeSlide + direction;

        this._setActiveClass();

        if (this.isCounter) {
            this._setCurrentSlideNumber();
        }
    }

    /**
     * Меняем номер активного слайда на нужный
     * @param {number} index - индекс активного слайда
     * @private
     */
    private _setActive(index: number): void {
        this.activeSlide = index;

        this._setActiveClass();
    }

    private _setCurrentSlideNumber(): void {
        const currentSlide = this.slider.querySelector('.j-counter-current') as HTMLElement;
        const count = this.infinityLoop ? this.activeSlide + 1 - this.slideVisible : this.activeSlide + 1;

        if (count === 0) {
            currentSlide.innerHTML = `${this.slidesItems.length}`;
        } else if (count === this.slidesItems.length + 1) {
            currentSlide.innerHTML = '1';
        } else {
            currentSlide.innerHTML = `${count}`;
        }
    }

    /**
     * Добавляем классы активным элементам
     * @private
     */
    private _setActiveClass(): void {
        const slides = Array.prototype.slice.call(this.slider.querySelector(this.slidesWrap)?.children);

        slides.forEach((slide: HTMLElement) => {
            slide.classList.remove(CLASS_ACTIVE);
        });

        slides[this.activeSlide]?.classList.add(CLASS_ACTIVE);

        if (this.isDots) {
            const dots = Array.prototype.slice.call(this.slider.querySelector(CLASS_DOTS_WRAP)?.children);

            dots.forEach((dot: HTMLElement) => {
                dot.classList.remove(CLASS_ACTIVE);
            });

            dots[this.activeSlide].classList.add(CLASS_ACTIVE);
        }
    }

    /**
     * Вычисляем позицию для смещения слайдера
     * @returns {number} - число
     * @private
     */
    private _getSlidePosition(): number {
        return this.slidesData.width * this.activeSlide;
    }

    /**
     * Вычисляем позицию для смещения подписи
     * @returns {number} - число
     * @private
     */
    private _getCaptionPosition(): number {
        return this.captionData.width * this.activeSlide;
    }

    /**
     * Перелистывает к переданной позиции слайды.
     * @param {string} duration - время анимации
     * @private
     */
    private _slideTo(duration: string): void {
        const position = this._getSlidePosition();
        const slidesWrap = this.slider.querySelector(this.slidesWrap) as HTMLElement;

        slidesWrap.style.transform = `translate3d(-${position}px, 0, 0)`;
        slidesWrap.style.transitionDuration = duration;
    }

    /**
     * Перелистывает к переданной позиции подпись.
     * @param {string} duration - время анимации
     * @private
     */
    private _captionTo(duration: string): void {
        const position = this._getCaptionPosition();
        const captionWrap = this.slider.querySelector(this.captionWrap) as HTMLElement;

        captionWrap.style.transform = `translate3d(-${position}px, 0, 0)`;
        captionWrap.style.transitionDuration = duration;
    }

    /**
     * Получаем размеры слайдов и тумбочек.
     * @private
     */
    private _getSliderParameters(): void {
        this._getSlidesParam();

        if (this.isCaption) {
            this._getCaptionParams();
        }
    }

    /**
     * Получаем размеры слайдов и ограничения для прокрутки
     * @private
     */
    private _getSlidesParam(): void {
        const count = this.slideVisible;
        const slidesArray = Array.prototype.slice.call(this.slider.querySelector(this.slidesWrap)?.children);

        if (!slidesArray.length) {
            return;
        }

        const offsetWidth = Number(slidesArray[0].getBoundingClientRect().width.toFixed(4));

        this.slidesData = {
            width       : offsetWidth,
            fullWidth   : offsetWidth * slidesArray.length,
            minTranslate: 0,
            maxTranslate: (offsetWidth * slidesArray.length) - (offsetWidth * count)
        };
    }

    /**
     * Получаем размеры подписей и ограничения для прокрутки
     * @private
     */
    private _getCaptionParams(): void {
        const zero = 0;
        const captionArr = Array.prototype.slice.call(this.slider.querySelector(this.captionWrap)?.children);
        const count = this.slideVisible;

        if (!captionArr.length) {
            return;
        }

        this.captionData = {
            width       : captionArr[zero].offsetWidth,
            fullWidth   : captionArr[zero].offsetWidth * captionArr.length,
            minTranslate: 0,
            maxTranslate: (captionArr[zero].offsetWidth * captionArr.length) - (captionArr[zero].offsetWidth * count)
        };
    }

    /**
     * Показывает слайдер, когда все методы выполнятся.
     * @private
     */
    private _ready(): void {
        this._setIntegerWidth();
        this.slider.style.opacity = `1`;

        observer.publish('slider:ready', this);

        this._getPlanoplan();

        this._observePlanoplan();
    }

    /**
     * Устанавливает целочисленную ширину для галереи, чтобы избежать полос по бокам слайдера
     * @private
     */
    private _setIntegerWidth(): void {
        if (this.responsive) {
            return;
        }

        this.slider.style.width = `${this.slidesData.width * this.slideVisible}px`;
    }

    /**
     * Удаляет фиксированную ширину галереи
     * @private
     */
    private _removeIntegerWidth(): void {
        this.slider.style.width = '';
    }

    /**
     * Пересчитываем значения размеров элементов слайдера и смещаем слайды без анимации
     * @private
     */
    private _resizeWindow(): void {
        if (this.responsive) {
            this._setCountSlides();
            this._redrawSlides();
            this._disableArrow();
        }

        this._removeIntegerWidth();
        this._getSliderParameters();
        this._setIntegerWidth();
        this._slideTo('0s');

        if (this.isCaption) {
            this._captionTo('0s');
        }
    }

    private _redrawSlides(): void {
        const slidesArray = Array.prototype.slice.call(this.slider.querySelector(this.slidesWrap)?.children);
        const width = this._getElements(this.slidesWrap, this.slideVisible).width;

        slidesArray.forEach((slide: HTMLElement) => {
            if (slide) {
                slide.style.minWidth = width;
                slide.style.flexBasis = width;
            }
        });
    }

    /**
     * Задаем количество показанных слайдов в случаем адаптивного слайдера
     * @private
     */
    private _setCountSlides(): void {
        if (!this.responsive) {
            return;
        }

        if (this._getCurrentBreakpoint() !== this.currentBreakpoint) {
            this.currentBreakpoint = this._getCurrentBreakpoint();
            const key = this.currentBreakpoint;

            // @ts-ignore
            this.slideVisible = this.responsive[key];
        }
    }

    /**
     * Задаем массив брейкпоинтов из настроек слайдера
     * @private
     */
    private _getBreakpoints(): void {
        if (!this.responsive) {
            return;
        }

        const breakpointArray: number[] = [];

        for (const key in this.responsive) {
            if (Object.prototype.hasOwnProperty.call(this.responsive, key)) {
                breakpointArray.push(parseInt(key));
            }
        }

        this.breakpointArray = breakpointArray;
    }

    /**
     * Собирает элементы (слайды) с planoplan в массив элементов
     * @private
     */
    private _getPlanoplan(): void {
        this.slidesItems.forEach((item: HTMLElement) => {
            if (item.querySelector(this.widgetWrap)) {
                this.planoplanElements.push(item);
            }
        });
    }

    /**
     * Возвращаем брейкпоинт из указанных в настройках слайдера, соответствующий текущей ширине экрана
     * @return {number} breakpoint - текущий брейкпоинт
     * @private
     */
    private _getCurrentBreakpoint(): number {
        let breakpoint = 0;

        this.breakpointArray.forEach((point: number) => {
            if (window.matchMedia(`(min-width: ${point}px)`).matches) {
                breakpoint = point;
            }
        });

        return breakpoint;
    }

    /**
     * Запускаем автопроигрывание слайдов
     * @private
     */
    private _slideAutoplay(): void {
        const slideDirection = 1;
        let auto: number = 0;

        const startAutoplay = (): void => {
            auto = window.setInterval(() => {
                if (this._checkTransform(slideDirection)) {
                    this._changeActive(slideDirection);
                    this._slideTo(this.duration);

                    if (this.isCaption) {
                        this._captionTo(this.duration);
                    }
                } else if (this.infinityLoop) {
                    this._changeActive(slideDirection);
                    this._slideTo(this.duration);

                    if (this.isCaption) {
                        this._captionTo(this.duration);
                    }

                    this._rewindSlider();
                }
            }, this.autoplaySpeed);
        };

        startAutoplay();

        if (this.stopOnClick) {
            this.slider.addEventListener('touchstart', () => {
                if (auto) {
                    clearInterval(auto);
                }
                startAutoplay();
            });
        }
    }

    private _initInfinityLoop(): void {
        if (!this.infinityLoop) {
            return;
        }

        this._cloneSlides();

        if (this.isCaption) {
            this._cloneCaptions();
        }

        if (this.isDots) {
            this._cloneDots();
        }

        this._setActive(this.slideVisible);
        this._slideTo(`0s`);

        if (this.isCaption) {
            this._captionTo(`0s`);
        }
    }

    private _rewindSlider(): void {
        if (!this.infinityLoop) {
            return;
        }

        const newActiveSlide = this.activeSlide === 0 ?
            this.slidesItems.length - 1 + this.slideVisible :
            this.slideVisible;

        if (this.isArrow) {
            this._preventArrowsClick();
        }

        setTimeout(() => {
            this.activeSlide = newActiveSlide;
            this._setActive(newActiveSlide);
            this._slideTo(`0s`);

            if (this.isCaption) {
                this._captionTo(`0s`);
            }

            if (this.isArrow) {
                this._allowArrowsClick();
            }
        }, 300);
    }

    private _cloneSlides(): void {
        if (!this.infinityLoop) {
            return;
        }

        const slidesArray = Array.prototype.slice.call(this.slider.querySelector(this.slidesWrap)?.children);

        for (let i = 0; i < this.slideVisible; i++) {
            const firstSlides = slidesArray[i];
            const lastSlides = slidesArray[slidesArray.length - 1 - i];
            const beforeElem = firstSlides.cloneNode(true);
            const afterElem = lastSlides.cloneNode(true);
            const container = this.slider.querySelector(this.slidesWrap);

            afterElem.classList.add('cloned-slide');
            beforeElem.classList.add('cloned-slide');

            if (container) {
                container.appendChild(beforeElem);
                container.insertBefore(afterElem, container.children[0] as Node);
            }
        }
    }

    private _cloneCaptions(): void {
        if (!this.infinityLoop) {
            return;
        }

        const captionsArray = Array.prototype.slice.call(this.slider.querySelector(this.captionWrap)?.children);

        for (let i = 0; i < this.slideVisible; i++) {
            const firstCaptions = captionsArray[i];
            const lastCaptions = captionsArray[captionsArray.length - 1 - i];
            const beforeElem = firstCaptions.cloneNode(true);
            const afterElem = lastCaptions.cloneNode(true);
            const container = this.slider.querySelector(this.captionWrap);

            afterElem.classList.add('cloned-slide');
            beforeElem.classList.add('cloned-slide');

            if (container) {
                container.appendChild(beforeElem);
                container.insertBefore(afterElem, container.children[0] as Node);
            }
        }
    }

    private _cloneDots(): void {
        if (!this.infinityLoop) {
            return;
        }

        const dotsArray = Array.prototype.slice.call(this.slider.querySelector(CLASS_DOTS_WRAP)?.children);

        for (let i = 0; i < this.slideVisible; i++) {
            const firstCaptions = dotsArray[i];
            const lastCaptions = dotsArray[dotsArray.length - 1 - i];
            const beforeElem = firstCaptions.cloneNode(true);
            const afterElem = lastCaptions.cloneNode(true);
            const container = this.slider.querySelector(CLASS_DOTS_WRAP);

            afterElem.classList.add('cloned-slide');
            beforeElem.classList.add('cloned-slide');

            if (container) {
                container.appendChild(beforeElem);
                container.insertBefore(afterElem, container.children[0] as Node);
            }
        }
    }

    private _preventArrowsClick(): void {
        this.arrowLeft.style.pointerEvents = 'none';
        this.arrowRight.style.pointerEvents = 'none';
        this.arrowLeft.style['cursor'] = 'pointer';
        this.arrowRight.style['cursor'] = 'pointer';
    }

    private _allowArrowsClick(): void {
        this.arrowLeft.style.pointerEvents = '';
        this.arrowRight.style.pointerEvents = '';
        this.arrowLeft.style.cursor = '';
        this.arrowRight.style.cursor = '';
    }
}

export default Slider;
