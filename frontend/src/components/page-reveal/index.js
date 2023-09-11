import Observer from '../../common/scripts/observer';
import {Utils} from '../../common/scripts/utils';

const observer = new Observer();

class PageReveal {
    constructor() {
        /**
         * Целевой элемент
         */
        this.wrapper = null;

        /**
         * Идет ли отслеживание скролла
         * @type {boolean}
         */
        this.isBinding = false;

        /**
         * Css-класс, который добавляется появившемуся блоку
         * @type {string}
         */
        this.revealClass = 'is-revealed';

        /**
         * Отступ от края экрана - граница, которую должен пересечь элемент, чтобы появиться
         * @type {number}
         */
        this.viewportOffset = 100;

        this.pause = false;

        this.started = false;

        this._triggerReveal = this._triggerReveal.bind(this);
    }

    /**
     * Инициализация модуля
     * @param {object} options - параметры
     */
    init(options) {
        this.options = options;
        this.wrapper = options.wrapper;
        this.elements = Array.from(options.wrapper.querySelectorAll(options.elementsSelector || '.j-page-reveal'));

        this._subscribe();

        if (!options.hasTrigger) {
            this._start();
        }
    }

    /**
     * Подписка на события других модулей, если нужно запустить или остановить анимацию по триггеру
     * @private
     */
    _subscribe() {
        observer.subscribe('popup:open', () => {
            this.pause = true;
        });

        observer.subscribe('popup:close', () => {
            this.pause = false;

            if (this.started) {
                this._triggerReveal();
            }
        });
    }

    /**
     * Запускает отслеживание
     * @private
     */
    _start() {
        this.started = true;
        this._triggerReveal();

        if (!this.isBinding) {
            this._bindScroll();
        }

        this.isBinding = true;
    }

    /**
     * Останавливает отслеживание
     * @private
     */
    _stop() {
        this._resetReveal();

        if (this.isBinding) {
            this._unbindScroll();
        }

        this.isBinding = false;
    }

    /**
     * Вешает обработку скролла
     * @private
     */
    _bindScroll() {
        window.addEventListener('scroll', this._triggerReveal);
    }

    /**
     * Снимает обработку скролла
     * @private
     */
    _unbindScroll() {
        window.removeEventListener('scroll', this._triggerReveal);
    }

    /**
     * Запускает анимацию для каждого элемента
     * @private
     */
    _triggerReveal() {
        if (this.pause) {
            return;
        }

        this.elements.forEach((element) => {
            this._animateElement(element);
        });
    }

    /**
     * Сбрасывает анимацию со всех элементов
     * @private
     */
    _resetReveal() {
        this.elements.forEach((element) => {
            element.classList.remove(this.revealClass);
        });
    }

    /**
     * Анимирует конкретный элемент
     * @param {Element} element - элемент, которому нужно задать анимацию появления
     * @private
     */
    _animateElement(element) {
        if (Utils.isInViewport(element, this.viewportOffset)) {
            element.classList.add(this.revealClass);
        } else {
            element.classList.remove(this.revealClass);
        }
    }
}

export default PageReveal;
