import type {IDetailsAccordion} from './types';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';
import {Utils} from '@/common/scripts/utils';

const observer: IObserver = new Observer();

class DetailsAccordion implements IDetailsAccordion {
    // The <details> element
    private element: HTMLDetailsElement;
    // The <summary> element
    private summary: HTMLElement | null;
    // The content element
    private content: HTMLElement | null;
    // The animation object (so we can cancel it if needed)
    private animation: Animation | null;
    // if the element is closing
    private isClosing: boolean = false;
    // Store if the element is expanding
    private isExpanding: boolean = false;
    private desktopOpen: boolean;

    public init(element: HTMLDetailsElement): void {
        this._setOptions(element);
        this._bindEvents();

        // Set the open attribute based on the parameter
        this.element.dataset['open'] = `${this.element.hasAttribute('open')}`;
    }

    private _setOptions(element: HTMLDetailsElement): void {
        this.element = element;
        this.summary = this.element.querySelector('summary');
        this.content = this.element.querySelector('.j-details__content');
        this.desktopOpen = this.element.dataset['desktopOpen'] ?
            Boolean(JSON.parse(this.element.dataset['desktopOpen'])) :
            false;
    }

    private _bindEvents(): void {
        if (this.summary) {
            this.summary.addEventListener('click', this._onClick.bind(this));
        }

        if (this.desktopOpen) {
            // Раскрываем при ините все списки
            this._onResize();

            const events = ['resize', 'orientationchange'];

            events.forEach((event: string) => {
                window.addEventListener(event, this._onResize.bind(this));
            });
        }
    }

    private _onClick(event: MouseEvent): void {
        event.preventDefault();
        this.element.style.overflow = 'hidden';
        // Если есть параметр раскрытия на десктопе и устройство больше мобильного, то не обрабатываем клик на раскрытие
        if (this.desktopOpen && !Utils.isMobile()) {
            return;
        }

        if (this.isClosing || !this.element.open) {
            // Check if the element is being closed or is already closed
            this._open();
            // Set the open attribute based on the parameter
            this.element.dataset['open'] = 'true';
        } else if (this.isExpanding || this.element.open) {
            // Check if the element is being opened or is already open
            this._shrink();
            // Set the open attribute based on the parameter
            this.element.dataset['open'] = 'false';
        }
    }

    private _open(): void {
        // Apply a fixed height on the element
        this.element.style.height = `${this.element.offsetHeight}px`;
        // Force the [open] attribute on the details element
        this.element.open = true;
        // Wait for the next frame to call the expand function
        window.requestAnimationFrame(this._expand.bind(this));
    }

    private _shrink(): void {
        // Set the element as "being closed"
        this.isClosing = true;

        // Store the current height of the element
        const startHeight = `${this.element.offsetHeight}px`;
        // Calculate the height of the summary
        const endHeight = this.summary ? `${this.summary.offsetHeight}px` : '0';

        // If there is already an animation running
        if (this.animation) {
            // Cancel the current animation
            this.animation.cancel();
        }

        // Start a WAAPI animation
        this.animation = this.element.animate({
            // Set the keyframes from the startHeight to endHeight
            height: [startHeight, endHeight]
        }, {
            duration: 300,
            easing  : 'ease-out'
        });

        // When the animation is complete, call onAnimationFinish()
        this.animation.onfinish = () => {
            this._onAnimationFinish(false);
        };

        // If the animation is cancelled, isClosing variable is set to false
        this.animation.oncancel = () => {
            this.isClosing = false;
        };
    }

    private _expand(): void {
        // Set the element as "being expanding"
        this.isExpanding = true;
        // Get the current fixed height of the element
        const startHeight = `${this.element.offsetHeight}px`;
        // Calculate the open height of the element (summary height + content height)
        const endHeight = `${this.summary!.offsetHeight + this.content!.offsetHeight}px`;

        // If there is already an animation running
        if (this.animation) {
            // Cancel the current animation
            this.animation.cancel();
        }

        // Start a WAAPI animation
        this.animation = this.element.animate({
            // Set the keyframes from the startHeight to endHeight
            height: [startHeight, endHeight]
        }, {
            duration: 300,
            easing  : 'ease-out'
        });

        // When the animation is complete, call onAnimationFinish()
        this.animation.onfinish = () => {
            this._onAnimationFinish(true);
        };

        // If the animation is cancelled, isExpanding variable is set to false
        this.animation.oncancel = () => {
            this.isExpanding = false;
        };
    }

    private _onAnimationFinish(open: boolean): void {
        // Set the open attribute based on the parameter
        this.element.open = open;
        // Clear the stored animation
        this.animation = null;
        // Reset isClosing & isExpanding
        this.isClosing = false;
        this.isExpanding = false;
        // Remove the overflow hidden and the fixed height
        this.element.style.height = '';
        this.element.style.overflow = '';
        observer.publish(`details:${open ? 'open' : 'close'}`);
    }

    private _onResize(): void {
        if (Utils.isMobile()) {
            // Check if the element is being opened or is already open
            this._shrink();
            // Set the open attribute based on the parameter
            this.element.dataset['open'] = 'false';
        } else if (this.isClosing || !this.element.open) {
            // Check if the element is being closed or is already closed
            this._open();
            // Set the open attribute based on the parameter
            this.element.dataset['open'] = 'true';
        }
    }
}

export default DetailsAccordion;
