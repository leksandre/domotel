/**
 * @version 2.0
 * @author Kelnik Studios {http://kelnik.ru}
 * @link https://kelnik.gitbooks.io/kelnik-documentation/content/front-end/components/menu-trigger.html documentation
 */
import type {IButtonTrigger, IButtonTriggerOptions} from './types';
import {EBreakpoint} from '@/common/scripts/constants';
import {EButtonTriggerState} from './types';
import type {IObserver} from '@/common/scripts/types/observer';
import Observer from '@/common/scripts/observer';
import {Utils} from '@/common/scripts/utils';

const observer: IObserver = new Observer();
const CLASS_OPEN = 'is-open';

class ButtonTrigger implements IButtonTrigger {
    /**
     * Элемент, по которому кликают
     * @type {HTMLElement}
     */
    private button: HTMLElement;

    /**
     * Состояния кнопки
     * open - открывает
     * close - закрывает
     * @type {string}
     */
    private state: EButtonTriggerState;

    /**
     * Динамичность кнопки
     * Если динамичная, то состояние меняется при клике
     * если не динамичное, то состояние вообще не меняется
     * @type {boolean}
     */
    private dynamic: boolean = true;

    /**
     * Строка со значением события для медиатора для состояния закрытия
     * @type {string}
     */
    private eventOpen: string = '';

    /**
     * Строка со значением события для медиатора для состояния открытия
     * @type {string}
     */
    private eventClose: string = '';

    /**
     * Инициализирует компонент
     * @param {Object} options - объект настроек
     */
    public init(options: IButtonTriggerOptions): void {
        this.button = options.target;
        this.state = this.button.classList.contains(CLASS_OPEN) ? EButtonTriggerState.OPEN : EButtonTriggerState.CLOSE;
        this.dynamic = this.button.dataset['dynamic'] ? Boolean(JSON.parse(this.button.dataset['dynamic'])) : true;
        if (options.eventOpen) {
            this.eventOpen = options.eventOpen;
        }
        if (options.eventClose) {
            this.eventClose = options.eventClose;
        }

        this._subscribes();
        this._bindEvents();
    }

    public closeByResize(): void {
        if (Utils.getWindowWidth() < EBreakpoint.TABLET_PORTRAIT || this.state !== 'open') {
            return;
        }

        if (this.dynamic) {
            this._toggleClass();
        }
        this.state = EButtonTriggerState.CLOSE;
        this._stateClose();
    }

    /**
     * Метод содержит в себе колбэки на события других модулей.
     * @private
     */
    private _subscribes(): void {
        observer.subscribe('scrollBy:start', () => {
            this.state = EButtonTriggerState.CLOSE;
            this.button.classList.remove(CLASS_OPEN);
        });
    }

    /**
     * Навешивает слушателей на элементы
     * @private
     */
    private _bindEvents(): void {
        this.button.addEventListener('click', this._clickBind.bind(this));
    }

    /**
     * Обрабатывает клик
     * @private
     */
    private _clickBind(): void {
        if (this.dynamic) {
            this._toggleClass();
        }

        this._changeState();
    }

    /**
     * Изменяет состояние
     * с отрытого на закрытое и наоборот
     * @private
     */
    private _changeState(): void {
        if (this.state === EButtonTriggerState.OPEN) {
            this.state = EButtonTriggerState.CLOSE;
            this._stateClose();
        } else {
            this.state = EButtonTriggerState.OPEN;
            this._stateOpen();
        }
    }

    /**
     * Изменяет классы на элементе с открытого на закрытое и открытое
     * @private
     */
    private _toggleClass(): void {
        this.button.classList.toggle(CLASS_OPEN);
    }

    /**
     * Сообщает, что компонент открыт
     * @private
     */
    private _stateOpen(): void {
        if (this.eventOpen) {
            observer.publish(this.eventOpen, this);
        }
    }

    /**
     * Сообщает, что компонент закрыт
     * @private
     */
    private _stateClose(): void {
        if (this.eventClose) {
            observer.publish(this.eventClose, this);
        }
    }
}

export default ButtonTrigger;
