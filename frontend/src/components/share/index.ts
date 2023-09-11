import type {IShare, IShareOptions} from './types';
import {Utils} from '@/common/scripts/utils';

const CLASS_OPEN = 'is-open';
const CLASS_HIDDEN = 'is-hidden';
const DELAY = 300;

class Share implements IShare {
    private target: HTMLElement;
    // Общий контейнер
    private shareContainer: HTMLElement;
    // Блок с вариантами "поделиться"
    private shareContent: HTMLElement;
    // Вариант отправки по email
    private email: HTMLElement;
    private formContainer: HTMLElement;
    private stepBack: HTMLElement;

    public init(options: IShareOptions): void {
        this._setOptions(options);
        this._getElements();
        this._bindEvents();
    }

    private _setOptions(options: IShareOptions): void {
        this.target = options.target;
    }

    private _getElements(): void {
        this.shareContainer = this.target.querySelector('.j-share__container') as HTMLElement;
        this.shareContent = this.shareContainer.querySelector('.j-share__content') as HTMLElement;
        this.email = this.shareContent.querySelector('.j-share__email') as HTMLElement;

        if (this.email) {
            this.formContainer = this.shareContainer.querySelector('.j-share__form') as HTMLElement;
            this.stepBack = this.shareContainer.querySelector('.j-share__form-back') as HTMLElement;
        }
    }

    private _bindEvents(): void {
        if (this.target) {
            this.target.addEventListener('click', this._open.bind(this));
        }

        if (this.email) {
            this.email.addEventListener('click', this._emailForm.bind(this));
            this.stepBack.addEventListener('click', this._stepBack.bind(this));
        }
    }

    private _open(event: MouseEvent): void {
        if (!event.target) {
            return;
        }

        if ((event.target as HTMLElement).closest('.j-share__button')) {
            this.target.classList.toggle(CLASS_OPEN);
        }

        if (!this.target.classList.contains(CLASS_OPEN)) {
            this._setInitialContent();
        }

        Utils.clickOutside(['.j-share'], this._clickedOutside.bind(this));
    }

    private _emailForm(event: MouseEvent): void {
        if (!event.target) {
            return;
        }

        this.shareContent.classList.add(CLASS_HIDDEN);
        this.shareContent.classList.remove(CLASS_OPEN);

        this.formContainer.classList.add(CLASS_OPEN);
        this.formContainer.classList.remove(CLASS_HIDDEN);
    }

    private _setInitialContent(): void {
        setTimeout(() => {
            this.formContainer.classList.remove(CLASS_OPEN);
            this.shareContent.classList.remove(CLASS_HIDDEN);
        }, DELAY);
    }

    private _stepBack(): void {
        this.shareContent.classList.remove(CLASS_HIDDEN);
        this.shareContent.classList.add(CLASS_OPEN);

        this.formContainer.classList.remove(CLASS_OPEN);
        this.formContainer.classList.add(CLASS_HIDDEN);
    }

    private _clickedOutside(): void {
        this.target.classList.remove(CLASS_OPEN);
    }
}

export default Share;
