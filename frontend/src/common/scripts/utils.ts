/**
 * @version 2.0
 * @author Kelnik Studios {http://kelnik.ru}
 * @link https://kelnik.gitbooks.io/kelnik-documentation/content/front-end/components/utils.html documentation
 */

/**
 * DEPENDENCIES
 */
import 'url-search-params-polyfill';
import {clearAllBodyScrollLocks, disableBodyScroll} from 'body-scroll-lock';
import {DEFAULT_MAP_KEY, EBreakpoint} from './constants';
import type {FixedLengthArray, ICallback, ICallbackRequest} from './types/utils';
import {ERestMethod} from './types/utils';

const defaultCb: ICallback = {
    success: (req: ICallbackRequest) => {
        console.warn(`This is the default handler for callbacks. Use custom instead. Req: ${req}`);
    },
    error: (err: number) => {
        throw new Error(`This is the default handler for callbacks. Use custom instead. Error number: ${err}`);
    }
};

class Utils {
    static isTouchDevice(): boolean {
        return (
            Boolean(typeof window !== 'undefined' &&
                ('ontouchstart' in window ||
                    (window.DocumentTouch &&
                        typeof document !== 'undefined' &&
                        document instanceof window.DocumentTouch))) ||
            Boolean(typeof navigator !== 'undefined' &&
                (navigator.maxTouchPoints || navigator.maxTouchPoints))
        );
    }

    /**
     * Метод полностью очищает весь html элемент.
     * @param {HTMLElement} element - DOM-элемент, который необходимо очистить.
     */
    static clearHtml(element: HTMLElement): void {
        element.innerHTML = '';
    }

    /**
     * Метод вставляет содержимое в блок.
     * @param {HTMLElement} element - элемент в который нужно вставить.
     * @param {Node/string} content - вставляемый контент.
     */
    static insetContent(element: HTMLElement, content: string | Node): void {
        if (typeof content === 'string') {
            element.insertAdjacentHTML('beforeend', content);
        } else if (typeof content === 'object') {
            element.appendChild(content);
        }
    }

    /**
     * Метод полностью удаляет элемент из DOM-дерева.
     * @param {HTMLElement} element - элемент, который необходимо удалить.
     */
    static removeElement(element: HTMLElement): void {
        // node.remove() не работает в IE11
        element.parentNode?.removeChild(element);
    }

    /**
     * Метод показывает элемент.
     * @param {HTMLElement} element - элемент, который необходимо показать.
     */
    static show(element: HTMLElement): void {
        element.style.display = 'block';
    }

    /**
     * Метод скрывает элемент.
     * @param {HTMLElement} element - элемент, который необходимо скрыть.
     */
    static hide(element: HTMLElement | null): void {
        if (!element) {
            return;
        }
        element.style.display = 'none';
    }

    /**
     * Узнает index элемента в родительской элемент
     * Аналог jquery.index()
     * @param {Element} element - искомый элемент
     * @return {number} - порядковый номер (индекс) в родительском элементе
     */
    static getElementIndex(element: Element): number {
        if (element.parentNode) {
            return Array.from(element.parentNode.children).indexOf(element);
        }

        return -1;
    }

    /**
     * Узнаёт, находится ли элемент во вьюпорте
     * @param {HTMLElement} element - искомый элемент
     * @param {number} offset - дополнительный отступ
     * @return {boolean} - возвращает true, если элемент виден на экране, и false наоборот
     */
    static isInViewport(element: HTMLElement, offset: number = 0): boolean {
        const rect: DOMRect = element.getBoundingClientRect();
        const top: number = rect.top + offset;
        const left: number = rect.left + offset;
        const windowHeight: number = window.innerHeight || document.documentElement.clientHeight;
        const windowWidth: number = window.innerWidth || document.documentElement.clientWidth;
        const belowViewport = 0;

        const verticalInView: boolean = (top <= windowHeight) && ((top + rect.height) >= belowViewport);
        const horizontalInView: boolean = (left <= windowWidth) && ((left + rect.width) >= belowViewport);

        return verticalInView && horizontalInView;
    }

    /**
     * Определяет ширину экрана
     * @returns {number} Значение ширины экрана
     */
    static getWindowWidth(): number {
        return window.innerWidth || document.documentElement.clientWidth;
    }

    /**
     * Определяет высоту экрана
     * @returns {number} Значение высоты экрана
     */
    static getWindowHeight(): number {
        return window.innerHeight || document.documentElement.clientHeight;
    }

    /**
     * Определяет ширину экрана
     * @returns {number} Значение ширины экрана
     */
    static getWindowOuterWidth(): number {
        return window.outerWidth;
    }

    /**
     * Определяет ширину экрана
     * @returns {number} Значение ширины экрана
     */
    static getScreenWidth(): number {
        return window.screen.width;
    }

    static getBodyWidth(): number {
        return document.body.getBoundingClientRect().width;
    }

    /**
     * Проверяет, мобилка или нет
     * @returns {boolean} - true - мобилка
     */
    static isMobile(): boolean {
        return Utils.getWindowWidth() < EBreakpoint.DESKTOP;
    }

    /**
     * Фиксирует страницу для запрета прокрутки
     * @param {Node} element - кроме данного элемента у всех остальных сбросится скролл
     */
    static bodyFixed(element: HTMLElement): void {
        disableBodyScroll(element, {
            reserveScrollBarGap: true
        });

        const padding = getComputedStyle(document.body).paddingRight;
        const header = document.querySelector('.header') as HTMLElement;

        if (header) {
            header.style.paddingRight = padding;
        }
    }

    /**
     * Снимаем фиксирование страницы
     */
    static bodyStatic(): void {
        clearAllBodyScrollLocks();

        const header = document.querySelector('.header') as HTMLElement;

        if (header) {
            header.style.paddingRight = '0';
        }
    }


    /**
     * Устанавливает гет-параметры
     * @param {string} url - адрес скрипта/API который будем подключать
     * @param {object} callback - функция обратного вызова, если нужна
     */
    static loadScript(url: string, callback?: () => any): void {
        const script = document.createElement('script');

        // eslint-disable-next-line consistent-return
        script.onload = () => {
            if (callback) {
                return callback();
            }
        };

        script.src = url;
        const head = document.getElementsByTagName('head')[0] as HTMLElement;

        if (head) {
            head.appendChild(script);
        }
    }

    /**
     * Троттлинг функции означает, что функция вызывается не более одного раза в указанный период времени
     * (например, раз в 10 секунд).
     * @param {function} fun - Функция выполнения
     * @param {number} time - Миллисекунды
     * @return {function} - Замыкание
     */
    static throttle(fun: (args: any) => any, time: number): (args: any) => any {
        let lastCall: number | null = null;

        return (args: any) => {
            const previousCall = lastCall;

            lastCall = Date.now();

            if (previousCall === null || (lastCall - previousCall) > time) {
                fun(args);
            }
        };
    }

    /**
     * Debouncing функции означает, что все вызовы будут игнорироваться до тех пор,
     * пока они не прекратятся на определённый период времени. Только после этого функция будет вызвана.
     * @param {function} fun - Функция выполнения
     * @param {number} time - Миллисекунды
     * @return {function} - Замыкание
     */
    static debounce(fun: (args: any) => any, time: number): (args: any) => any {
        let lastCall: number | null = null;
        let lastCallTimer: ReturnType<typeof setTimeout> | null = null;

        return (args: any) => {
            const previousCall = lastCall;

            lastCall = Date.now();

            if (previousCall && ((lastCall - previousCall) <= time) && lastCallTimer) {
                clearTimeout(lastCallTimer);
            }

            lastCallTimer = setTimeout(() => {
                fun(args);
            }, time);
        };
    }

    /**
     * Метод проверяет наличие интернета
     * @return {boolean} - При наличии результатом будет true, а при отсутствии false.
     */
    static checkInternetConnection(): boolean {
        return navigator.onLine;
    }

    /**
     * Метод отправляет ajax запрос на сервер.
     * @param {FormData | string} data - отправляемые данные.
     * @param {string} url - маршрут по которому нужно произвести запрос.
     * @param {Function} callback - функции обратного вызова, при успехе вызовет поле success, а при ошибке - error.
     * @param {String} method - метод отправки запроса
     */
    static send(data: FormData | string | null,
                url: string,
                callback: ICallback = defaultCb,
                method: ERestMethod = ERestMethod.POST): void {
        const xhr = new XMLHttpRequest();
        const statusSuccess = 200;

        xhr.open(method, url);

        if (!(data instanceof FormData)) {
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        }

        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.send(data);

        xhr.onload = function XHR() {
            if (xhr.status === statusSuccess) {
                const req = JSON.parse(this.responseText);

                if (callback.success) {
                    callback.success(req);
                }
            } else if (callback.error) {
                callback.error(xhr.status);
            }

            if (callback.complete) {
                callback.complete();
            }
        };

        xhr.onerror = function XHR() {
            if (callback.error) {
                callback.error(xhr.status);
            }

            if (callback.complete) {
                callback.complete();
            }
        };
    }

    /**
     * Генерация псевдослучайных чисел в заданном числовом интервале
     * @param {number} min - минимальное псевдослучайное число
     * @param {number} max - максимальное псевдослучайное число
     * @return {number} - возвращает псевдослучайное число в интервале [min, max]
     */
    static random(min: number, max: number): number {
        return Math.floor((Math.random() * (max - min)) + min);
    }

    /**
     * Конвертирует число или строку в строку с разрядами.
     * @param {string/number} value - число или строка.
     * @return {string} - преобразованная (или нет) строка.
     */
    static convertToDigit(value: string): string {
        return Number(value) ? Number(value).toLocaleString('ru-Ru') : value;
    }

    /**
     * Конвертирует целое число в дробное
     * @param {string|number} value - число или строка.
     * @param {number} denominator - целый делитель.
     * @return {number} - преобразованная строка.
     */
    static convertToRank(value: string | number, denominator: number): number {
        const result = Number(value);

        return result / denominator;
    }

    /**
     * Метод конвертирует строку (можно с разрядами) в число
     * @param {string} str - необходимая строка.
     * @return {number} - конвертированное число.
     */
    static convertToNumber(str: string): number {
        return parseFloat(str
            .toString()
            .replace(/\s/g, ''));
    }

    /**
     * Выбирает окончание слова.
     * @param {number} n - количество
     * @param {Object} words - массив слов. Например, показать ещё ['квартиру', 'квартиры', 'квартир']
     * @return {string} - слово в правильном склонении
     */
    static pluralWord(n: number, words: FixedLengthArray<string, 3>): string | undefined {
        /* eslint-disable  */
        const $i = n % 10 === 1 && n % 100 !== 11 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2;
        /* eslint-enable*/

        return words[$i];
    }

    /**
     * Получает значение гет-параметра
     * @param {string} param - ключ для поиска в гет
     * @return {string | null} value - значение гет
     */
    static getUrlParams(param: string): string | null {
        const get: string = window.location.search;
        const url: URLSearchParams = new URLSearchParams(get);

        return url.get(param);
    }

    /**
     * Клик по элементу или мимо
     * @param {array} selectors - Строка или массив селектор(а)ов отслеживаемых элемент(а)ов
     * @param {function} outside - Callback если клик вне элемент(а)ов
     * @param {function} inside - Callback если клик по элемент(у)ам
     */
    static clickOutside(selectors: string[] | string,
                        // eslint-disable-next-line @typescript-eslint/no-empty-function
                        outside: () => any = function() {},
                        // eslint-disable-next-line @typescript-eslint/no-empty-function
                        inside: () => any = function() {}): void {
        if (!selectors?.length) {
            return;
        }

        const elements: Node[][] = [];

        if (typeof selectors == 'string') {
            elements.push(Array.from(document.querySelectorAll(selectors)));
        } else {
            selectors.forEach((selector: string) => {
                elements.push(Array.from(document.querySelectorAll(selector)));
            });
        }

        document.addEventListener('click', (event: MouseEvent): void => {
            let target = event.target as Node;

            do {
                try {
                    // eslint-disable-next-line no-loop-func
                    elements.forEach((element: Node[]) => {
                        if (element.indexOf(target as Node) !== -1) {
                            inside();

                            // eslint-disable-next-line no-throw-literal
                            throw false;
                        }
                    });
                } catch (error) {
                    return;
                }

                if (target) {
                    target = target.parentNode as Node;
                }
            } while (target);

            outside();
        });
    }

    /**
     * Проверяет, является ли сущность дом элементом
     * @param {*} elem - элемент для проверки
     * @returns {boolean} true - является
     */
    static isElement(elem: any): boolean {
        return Boolean(elem.tagName);
    }

    static closest(element: HTMLElement, parent: string): Element | null {
        if (!element || !parent) {
            return null;
        }

        return element.closest(parent);
    }

    static isWindows(): boolean {
        const platform = window.navigator?.userAgentData?.platform || window.navigator.platform;
        const windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'];

        return windowsPlatforms.indexOf(platform) !== -1;
    }

    static checkMap(callbackFunction: () => void): void {
        const ymaps = window.ymaps;
        const API_KEY = window?.yandexMapKey ?? DEFAULT_MAP_KEY;

        if (ymaps) {
            callbackFunction();
        } else {
            this.loadScript(`https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=${API_KEY}`, callbackFunction);
        }
    }
}

class Counter {
    stopValue: number;
    countValue: number;

    constructor(stopValue: number = 3) {
        this.stopValue = stopValue;
        this.countValue = this.stopValue;
    }

    tick(): boolean {
        if (!this.countValue) {
            this.countValue = this.stopValue;

            return true;
        }

        this.countValue--;

        return false;
    }
}

class InternetConnection {
    counter: Counter;
    callback: () => any;
    interval: number;

    constructor(callback: () => any = () => {
        return null;
    }, interval: number = 200) {
        this.counter = new Counter(3);

        this.callback = callback;
        this.interval = interval;

        this._bindMethodsContext();
    }

    connect(): void {
        if (!Utils.checkInternetConnection() && !this.counter.tick()) {
            this._reconnect(this.connect);

            return;
        }

        this.callback();
    }

    _bindMethodsContext(): void {
        this.connect = this.connect.bind(this);
    }

    /**
     * @Public
     * Возвращает контент с ошибками сети
     * @return {Object} - объект со списком контента для обработки ошибок
     */
    getErrorMessage(): string {
        return Utils.checkInternetConnection() ?
            '<h1>На сервере произошла ошибка. Повторите запрос позднее</h1>' :
            '<h1>Вы не подключены к интернету. Повторите запрос позднее</h1>';
    }

    _reconnect(callback: () => any): void {
        setTimeout(() => {
            callback();
        }, this.interval);
    }
}

export {Utils, InternetConnection};
