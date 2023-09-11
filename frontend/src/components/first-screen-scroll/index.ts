import type {IFirstScreenScroll, IFirstScreenScrollOptions} from './types';
import {Utils} from '@/common/scripts/utils';

const ANIMATION_DURATION = 600;

class FirstScreenScroll implements IFirstScreenScroll {
    readonly wrapperSelector: string;
    private element: HTMLElement;
    private wrapper: HTMLElement | null;
    private height: number;

    constructor(options: IFirstScreenScrollOptions) {
        this.element = options.element;
        this.wrapperSelector = options.wrapperSelector || '.j-main-screen';
    }

    public init(): void {
        this._setElements();
        this._setHeight();
        this._bindEvents();
    }

    private _setElements(): void {
        this.wrapper = document.querySelector(this.wrapperSelector) as HTMLElement;
    }

    private _setHeight(): void {
        if (!this.wrapper) {
            return;
        }

        this.height = this.wrapper.offsetHeight;
    }

    private _bindEvents(): void {
        const events = ['resize', 'orientationchange'];

        events.forEach((event: string) => {
            window.addEventListener(event, Utils.debounce(this._setHeight.bind(this), 100));
        });

        this.element.addEventListener('click', Utils.debounce(this._onClick.bind(this), 100));
    }

    private _onClick(): void {
        const y = this.height;
        const startY = window.pageYOffset;
        const diff = y - startY;
        let start = 0;

        const step = (timestamp: number): void => {
            if (!start) {
                start = timestamp;
            }

            const time = timestamp - start;
            const percent = Math.min(time / ANIMATION_DURATION, 1);

            window.scrollTo(0, startY + (diff * percent));

            if (time < ANIMATION_DURATION) {
                window.requestAnimationFrame(step.bind(this));
            }
        };

        window.requestAnimationFrame(step.bind(this));
    }
}

export default FirstScreenScroll;
