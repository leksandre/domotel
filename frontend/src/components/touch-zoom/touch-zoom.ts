import type {ITZOuterOptions, ITZTransform} from '@/components/touch-zoom/types';
import Hammer from 'hammerjs';

const CLASS_ACTIVE = 'is-active';

class TouchZoom {
    private readonly scalingItem: HTMLElement;
    private startX: number;
    private startY: number;
    private startScale: number;
    private transform: ITZTransform;
    private options: ITZOuterOptions;
    private absTranslateX: number;
    private absTranslateY: number;
    // @ts-ignore
    private ham: Hammer;

    constructor(scalingItem: HTMLElement) {
        this.scalingItem = scalingItem;
        this._setOptions();
        this._initHammer();
    }

    private _getOuterOptions(): ITZOuterOptions {
        const minScale = Number(this.scalingItem.dataset['minscale']) || 1;
        const maxScale = Number(this.scalingItem.dataset['maxscale']) || 3;

        return {
            minScale,
            maxScale
        };
    }

    /**
     * Установка параметров (мин. и макс. масштабирование) и прочих переменных
     * @private
     */
    private _setOptions(): void {
        // стартовая позиция translate и scale
        this.startX = 0;
        this.startY = 0;
        this.startScale = 1;

        // стартовая позиция transform
        this._resetTransform();

        this.options = this._getOuterOptions();
    }

    /**
     * Инициализация хаммера
     * @private
     */
    private _initHammer(): void {
        this.ham = new Hammer(this.scalingItem, {
            domEvents: true
        });

        // для масштабирования
        this.scalingItem.addEventListener('touchstart', (event: TouchEvent) => {
            if (event.touches.length >= 2) {
                this.ham.get('pinch').set({enable: true});
            }
        });

        this.scalingItem.addEventListener('touchend', (event: TouchEvent) => {
            if (event.touches.length < 2) {
                this.ham.get('pinch').set({enable: false});
            }
        });

        // для движения в разные направления
        this.ham.get('pan').set({direction: Hammer.DIRECTION_HORIZONTAL});

        this._bindTouchEvents();
    }

    /**
     * Привязываем тач-события
     * @private
     */
    private _bindTouchEvents(): void {
        this.ham.on('pinchstart pinchmove pinchend', this._onPinch.bind(this));
        this.ham.on('pinchend', this._onPinchEnd.bind(this));
        this.ham.on('panstart panmove', this._onPan.bind(this));
        this.ham.on('panend', this.onPanEnd.bind(this));
    }

    /**
     * Событие при движении в разные направления
     * @private
     * @param {event} event - объект события
     */
    // eslint-disable-next-line no-undef
    private _onPan(event: HammerInput): void {
        const currentScale = this.transform.scale;

        // запрет на движение, когда картинка достигла минимального значения скейла
        if (currentScale <= this.options.minScale) {
            return;
        }

        // расчет стартового текущего положения
        if (event.type === 'panstart') {
            this.startX = this.transform.translate.x ? this.transform.translate.x : 0;
            this.startY = this.transform.translate.y ? this.transform.translate.y : 0;
        }

        const deltaCalcX = this.startX + event.deltaX;
        const deltaCalcY = this.startY + event.deltaY;

        const deltaCalc = this._getUpdatedTranslate([deltaCalcX, deltaCalcY]);

        this.transform.translate = {
            x: deltaCalc[0],
            y: deltaCalc[1]
        };

        this._updateElementTransform();
    }

    /**
     * Окончание движения в разные направления при котором картинка возвращается в изначальное положение,
     * если scale становится минимальным
     * @private
     */
    private onPanEnd(): void {
        if (this.transform.scale > this.options.minScale) {
            return;
        }

        this._resetTransform();
        this._updateElementTransform();
    }

    /**
     * Событие при мультитач движении для масштабирования
     * @private
     * @param {HammerInput} event - объект события
     */
    // eslint-disable-next-line no-undef
    private _onPinch(event: HammerInput): void {
        if (event.type === 'pinchend') {
            if (this.transform.scale === this.options.minScale) {
                this.scalingItem.classList.remove(CLASS_ACTIVE);
            }
        }
        // расчет стартового текущего положения
        if (event.type === 'pinchstart') {
            this.scalingItem.classList.add(CLASS_ACTIVE);
            this.startScale = this.transform.scale ? this.transform.scale : 1;
        }

        this._setAbsTranslateValue();
        this.transform.scale = this._getUpdatedScale(this.startScale * event.scale);
        this._updateElementTransform();
    }

    /**
     * Окончание мультитач движения для масштабирования
     * (происходит изменение translate для того, чтобы значение translate было в рамках абс. значений)
     * @private
     */
    private _onPinchEnd(): void {
        const deltaCalcX = this.transform.translate.x;
        const deltaCalcY = this.transform.translate.y;
        const deltaCalc = this._getUpdatedTranslate([deltaCalcX, deltaCalcY]);

        this.transform.translate = {
            x: deltaCalc[0],
            y: deltaCalc[1]
        };
        this._updateElementTransform();
    }

    /**
     * Обновление css-свойства transform у элемента, который нужно масштабировать и двигать
     * @private
     */
    private _updateElementTransform(): void {
        this.scalingItem.style.transform = this.transform.scale === this.options.minScale ?
            `` :
            `translate3d(${this.transform.translate.x}px,
            ${this.transform.translate.y}px, 0) scale(${this.transform.scale},
            ${this.transform.scale}) rotate(0deg)`;
    }

    /**
     * Получение обновленных transform: translateX, translateY после проверки с фбс.значением
     * @param {array} deltaCalc - предварительные расчётные данные transform: translateX, translateY в виде массива
     * @return {array} - обновленные данные в виде массива
     */
    private _getUpdatedTranslate(deltaCalc: [number, number]): [number, number] {
        let [deltaCalcX, deltaCalcY] = deltaCalc;

        if (this.absTranslateX <= deltaCalcX) {
            deltaCalcX = this.absTranslateX;
        } else if (-this.absTranslateX >= deltaCalcX) {
            deltaCalcX = -this.absTranslateX;
        }

        if (this.absTranslateY <= deltaCalcY) {
            deltaCalcY = this.absTranslateY;
        } else if (-this.absTranslateY >= deltaCalcY) {
            deltaCalcY = -this.absTranslateY;
        }

        return [deltaCalcX, deltaCalcY];
    }

    /**
     * Установка абс. значений translate, которые ограничивают смещение
     * @private
     */
    private _setAbsTranslateValue(): void {
        const scalingWidth = this.scalingItem.offsetWidth;
        const scalingHeight = this.scalingItem.offsetHeight;
        const currentScalingItemWidth = scalingWidth * this.transform.scale;
        const currentScalingItemHeight = scalingHeight * this.transform.scale;

        this.absTranslateX = (currentScalingItemWidth - scalingWidth) / 2;
        this.absTranslateY = (currentScalingItemHeight - scalingHeight) / 2;
    }

    /**
     * Получение обновленных данных для св-ва transform: scale, с учетом проверки на мин. и макс
     * @private
     * @param {number} calcScale - предварительный расчет для св-ва transform: scale
     * @return {number} - обновленный расчет после проверки
     */
    private _getUpdatedScale(calcScale: number): number {
        let extreme = calcScale;

        if (calcScale < this.options.minScale) {
            extreme = this.options.minScale;
        } else if (calcScale > this.options.maxScale) {
            extreme = this.options.maxScale;
        }

        return extreme;
    }

    /**
     * Сброс св-ва transform до начального состояния
     * @private
     */
    private _resetTransform(): void {
        this.transform = {
            translate: {
                x: 0,
                y: 0
            },
            scale: 1
        };
    }
}

export default TouchZoom;
