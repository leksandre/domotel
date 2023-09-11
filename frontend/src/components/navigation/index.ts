import type {INavigation, INavigationBreaks, INavigationOptions} from './types';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';
// @ts-ignore
import priorityNav from 'priority-nav';

const observer: IObserver = new Observer();

class Navigation implements INavigation {
    // Тот элемент - который должен схлопываться
    private navigationWrapper: HTMLElement;
    // Вся шапка
    private wrapper: HTMLElement;
    // Массив с элементами лого и блоком с телефоном
    private driveElements: Element[];
    private foldingNavigation: string | null;
    private breaks: INavigationBreaks;
    private offset: number;
    private isActive: boolean = false;

    public init(options: INavigationOptions): void {
        this.navigationWrapper = options.target;
        this.wrapper = this.navigationWrapper.closest(options.wrapper) as HTMLElement;
        this.driveElements = [...this.wrapper.querySelectorAll(options.driveElements)];
        this.foldingNavigation = options.foldingNavigation || null;
        this.breaks = options.breaks ?? {
            min: 320,
            max: 1920
        };
        this.offset = options.offset ?? 15;

        this._bindEvents();
        this._setSize();
        this._setState();
    }

    private _bindEvents(): void {
        const events = ['resize', 'orientationchange'];

        events.forEach((event: string) => {
            window.addEventListener(event, this._setSize.bind(this));
        });
    }

    private _setSize(): void {
        if (this.isActive && (this.breaks.min > window.innerWidth && window.innerWidth > this.breaks.max)) {
            return;
        }

        let navWidth = this.driveElements.reduce((startWidth: number, driveElement: Element) => {
            return startWidth - (driveElement as HTMLElement).offsetWidth;
        }, this.wrapper.offsetWidth);

        const navigationOffset = getComputedStyle(this.navigationWrapper);

        navWidth = navWidth - parseInt(navigationOffset.marginLeft) + parseInt(navigationOffset.marginRight);
        this.navigationWrapper.style.width = `${navWidth - this.offset}px`;

        if (this.foldingNavigation) {
            this._setFoldingNavigationIcon();
        }
    }

    private _setState(): void {
        this._setFoldingNavigation();
        this.navigationWrapper.classList.add('is-active');
        this.isActive = true;
        // 300 - время анимации
        setTimeout(() => {
            observer.publish('navigation:ready');
        }, 300);
    }

    private _setFoldingNavigation(): void {
        if (!this.foldingNavigation) {
            return;
        }

        priorityNav.init({
            mainNavWrapper            : '.j-folding-navigation',
            breakPoint                : 0,
            throttleDelay             : '0',
            navDropdownLabel          : '',
            navDropdownBreakpointLabel: ''
        });

        this._setFoldingNavigationIcon();
    }

    private _setFoldingNavigationIcon(): void {
        const selector = document.querySelector('.priority-nav__dropdown-toggle');

        const icon = `<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="19.5" cy="13.5" r="1.5"/>
                <circle cx="19.5" cy="19.5" r="1.5"/>
                <circle cx="19.5" cy="25.5" r="1.5"/>
            </svg>`;

        if (selector) {
            setTimeout(() => {
                selector.innerHTML = `${icon}`;
                this._setListener(selector as HTMLElement);
            }, 100);
        }
    }

    /**
     * Обрабатывает клики на раскрывающемся меню схлопывающегося меню
     * @param {object} selector - Элемент кнопки раскрытия доп списка
     */
    private _setListener(selector: HTMLElement): void {
        if (!selector) {
            return;
        }

        const dropdown = document.querySelector('.priority-nav__dropdown');

        if (!dropdown) {
            return;
        }
        const dropdownItems = [...dropdown.querySelectorAll('.navigation__item')];

        dropdownItems.forEach((item: Element) => {
            (item as HTMLElement).addEventListener('click', () => {
                dropdown.classList.remove('show');
                selector.classList.remove('is-open');
            });
        });
    }
}

export default Navigation;
