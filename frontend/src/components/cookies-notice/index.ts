import Cookies from 'js-cookie';
import {ECookiesNoticeState} from './types';
import type {ICookiesNotice} from './types';

const DAY_MILLISECONDS = 24 * 60 * 60 * 1000;
const SHOW_DELAY = 1500;
const COOKIE_NAME = 'alert-cookie-accepted';

class CookiesNotice implements ICookiesNotice {
    private wrapper: HTMLElement;
    private timer: string | boolean;
    private date: number = new Date().getTime();
    private button: Element | null;

    /**
     * Метод инициализирует модуль.
     * @param {HTMLElement} wrapper - элемент куки плашки
     */
    public init(wrapper: HTMLElement): void {
        this.wrapper = wrapper;
        this.timer = this.wrapper.dataset['timer'] || false;
        this.button = this.wrapper.querySelector('.j-cookies-notice__button');

        this._bindEvents();
        this._check();
    }

    /**
     * Метод навешивает обработчики на элементы.
     * @private
     */
    private _bindEvents(): void {
        if (this.button) {
            this.button.addEventListener('click', this._close.bind(this));
        }
    }

    /**
     * Метод проверяет наличие куки.
     * @private
     */
    private _check(): void {
        // Дата, когда была записана кука
        const cookiesTime = Number(Cookies.get(COOKIE_NAME));
        // Сколько прошло времени
        const spentTime = this.date - cookiesTime;
        // Если стоит таймер
        const timerData = this.timer ? DAY_MILLISECONDS * Number(this.timer) : 0;
        // Когда истекает запись
        const dateOver = cookiesTime + timerData;

        if (!cookiesTime) {
            this._show();
        } else if ((spentTime > dateOver) && this.timer) {
            Cookies.remove(COOKIE_NAME);
            this._show();
        }
    }

    /**
     * Метод показывает уведомление.
     * @private
     */
    private _show(): void {
        // Добавляем доп класс, что-бы уведомление не прыгало при загрузке страницы
        this.wrapper.classList.add(ECookiesNoticeState.SHOW);

        setTimeout(() => {
            this.wrapper.classList.add(ECookiesNoticeState.OPEN);
        }, SHOW_DELAY);
    }

    /**
     * Метод закрывает уведомление и устанавливает куку.
     * @private
     */
    private _close(): void {
        Cookies.set(COOKIE_NAME, `${this.date}`);

        this.wrapper.classList.remove(ECookiesNoticeState.OPEN, ECookiesNoticeState.SHOW);
    }
}

export default CookiesNotice;
