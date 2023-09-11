import {EYandexMapPanelState} from './types';
import type {IYandexMapMarkerData} from './types';
import type {IYandexMapPanel} from './types';
import {Utils} from '@/common/scripts/utils';
import type ymaps from 'yandex-maps';

class Panel implements IYandexMapPanel {
    private state: EYandexMapPanelState = EYandexMapPanelState.close;
    private defDelay: number = 300;
    private id: number;
    private domTarget: HTMLElement | null;
    private marker: HTMLElement;
    private wrapper: HTMLElement;
    private panel: HTMLElement;
    private panelHeight: number;
    private activeMarker: HTMLElement;
    private template: (data: IYandexMapMarkerData) => string;
    private data: IYandexMapMarkerData;

    /**
     * Открывает панель при клике на маркер.
     * @param {object} target - геообъект по которому произошло событие.
     * @public§
     */
    public open(target: ymaps.Placemark): void {
        // @ts-ignore
        this.data = target.options.getNative('data');
        // @ts-ignore
        this.template = target.options.getNative('template');
        // @ts-ignore
        // eslint-disable-next-line
        this.domTarget = target.getOverlaySync().getLayoutSync().getElement();
        this.marker = this.domTarget!.classList.contains('j-yandex-map-marker') ?
            this.domTarget as HTMLElement :
            this.domTarget!.querySelector('.j-yandex-map-marker') as HTMLElement;
        this.wrapper = this.marker.closest('.j-yandex-map-base') as HTMLElement;

        if (this.id === Number(this.data.id)) {
            // Если клик произошел потому же маркеру, то надо просто закрыть панельку.
            this._close();
        } else if (this.id !== Number(this.data.id) && this.state !== 'close') {
            // Если клик произошел по новому маркеру, но при этом панелька уже открыта, то нужно закрыть и открыть
            // новую с задержкой
            this._close();
            this._createTemplate('repeat');
        } else {
            this._createTemplate();
        }

        this._bindMapClickListener();
    }

    private _bindMapClickListener(): void {
        window.addEventListener('bindMapClick', () => {
            this._close();
        });
    }

    /**
     * Создает шаблон панели.
     * @param {string} repeat - если клик произошел при уже открытой панельки, то это параметр показывает,
     * что нужно показать панельку с задержкой
     * @private
     */
    private _createTemplate(repeat: string = ''): void {
        const noDelay = 0;
        const delay = repeat ? this.defDelay : noDelay;

        this._setStateMarker();

        setTimeout(() => {
            Utils.insetContent(this.wrapper, this.template(this.data));

            this.id = Number(this.data.id);

            this._getElements();
            this._showPanel();
        }, delay);
    }

    /**
     * Метод получает элементы панельки
     * @private
     */
    private _getElements(): void {
        this.panel = this.wrapper.querySelector('.j-yandex-map-balloon') as HTMLElement;
        this.panelHeight = this.panel.offsetHeight;
    }

    /**
     * Метод показывает только заголовок.
     * @private
     */
    private _showPanel(): void {
        this.panel.style.transform = `translateY(${-this.panelHeight}px)`;
        this.state = EYandexMapPanelState.open;
        this.wrapper.classList.add('is-active');
    }

    /**
     * Метод скрывает всю панельку.
     * @private
     */
    private _hideAll(): void {
        this.panel.style.transform = 'translateY(0px)';
        this.state = EYandexMapPanelState.close;
        this.wrapper.classList.remove('is-active');
    }

    /**
     * Метод удаляет панельку со страницы.
     * @private
     */
    private _close(): void {
        this._removeStateMarker();
        this._hideAll();
        this.id = -1;

        setTimeout(() => {
            Utils.removeElement(this.panel);
        }, this.defDelay);
    }

    /**
     * Устанавливает активное состояние для маркера при клике на нем.
     * @private
     */
    private _setStateMarker(): void {
        if (this.marker) {
            this.marker.classList.add('is-active');
        }
    }

    /**
     * Метод удаляет активное состояние у маркера.
     * @private
     */
    private _removeStateMarker(): void {
        if (!this.marker) {
            return;
        }
        // Удаляем класс через querySelector, т.к при клике на другой маркер при активном текущем удаляется текущий,
        // а не предыдущий
        this.activeMarker = this.wrapper.querySelector('.j-yandex-map-marker.is-active') as HTMLElement;

        if (this.activeMarker) {
            this.activeMarker.classList.remove('is-active');
        }

        this.marker.classList.remove('is-active');
    }
}

export default Panel;
