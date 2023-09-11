/**
 * @version 2.0
 * @author Kelnik Studios {http://kelnik.ru}
 * @link https://kelnik.gitbooks.io/kelnik-documentation/content/front-end/components/пока нет documentation
 */

/**
 * DEPENDENCIES
 */
import 'url-search-params-polyfill';
import type {IYandexMapMarker,
    IYandexMapMarkerData,
    IYandexMapPolygonData,
    IYMapsOuterSettings,
    IYMapsSettings} from './types';
import axios from 'axios';
import Cluster from './cluster';
import Controls from './controls';
import {EBreakpoint} from '@/common/scripts/constants';
import type {IObserver} from '@/common/scripts/types/observer';
import type {IYandexMap} from './types';
import mapUtils from './utils';
import Marker from './marker';
import Observer from '@/common/scripts/observer';
import overlayTmpl from './overlay/template.twig';
import {RESIZE_EVENTS} from '@/common/scripts/constants';
import {Utils} from '@/common/scripts/utils';
import type ymaps from 'yandex-maps';

const observer: IObserver = new Observer();
// Класс DOM-элемента в который будет вставлена карта
const CLASS_BASE = '.j-yandex-map-base';

class YandexMap implements IYandexMap {
    private readonly ymaps: typeof ymaps;
    private geoObjectCollection: ymaps.GeoObjectCollection;
    private geoObjectCollectionArray: ymaps.IGeoObject[];
    // URL по которому будет выполнен ajax запрос за данными
    private url: string | false;
    private id: string;
    private mapWrapper: HTMLElement;
    private outerOptions: IYMapsOuterSettings;
    private content: HTMLElement;
    private json: string | false;
    private body: string | FormData;
    // Яндексовый объект карты
    private map: ymaps.Map | null = null;
    private cluster: any;
    private overlay: HTMLElement | null;
    private isOverlayHide: boolean = false;
    // Все настройки карты
    private settings: IYMapsSettings = {
        minZoom: 10,
        maxZoom: 18,
        zoom   : 12,
        markers: []
    };

    constructor(ymap: typeof ymaps) {
        /**
         * Экземпляр загруженных яндекс карт.
         */
        this.ymaps = ymap;

        /**
         * Экземпляр конструктора геообъектов
         */
        this.geoObjectCollection = new this.ymaps.GeoObjectCollection();
    }

    /**
     * Метод принимает настройки из вне и запускает процесс инициализации карт
     * @param {object} outerOptions - опции из скрипта
     */
    public init(outerOptions: IYMapsOuterSettings): void {
        this.outerOptions = outerOptions;
        this.mapWrapper = outerOptions.wrapper;
        this.content = this.mapWrapper.querySelector(CLASS_BASE) as HTMLElement;
        if (!this.content) {
            throw new Error('Не найден элемент j-yandex-map-base для карты');
        }
        this.id = this.content.id;
        this.url = this.mapWrapper.dataset['ajax'] || false;
        this.json = this.mapWrapper.dataset['json'] || false;

        this._runMap();
    }

    /**
     * Метод содержит в себе колбэки на события других модулей
     */
    private _subscribes(): void {
        this._showGeoObject = this._showGeoObject.bind(this);
        this._hideGeoObject = this._hideGeoObject.bind(this);

        observer.subscribe('filterMapItem:add', (markerType: string) => {
            this._toggleGeoObjects(markerType, this._showGeoObject);
        });

        observer.subscribe('filterMapItem:remove', (markerType: string) => {
            this._toggleGeoObjects(markerType, this._hideGeoObject);
        });
    }

    /**
     * Основной метод, который запускает выполнение всех остальных методов. При ошибке кладет весь остальной процесс.
     * @private
     */
    private async _runMap(): Promise<void> {
        await this._setSettings();
        await this._initYandexMap();
        this._setDeviceSettings();
        this._initMarker();
        this._initPolygons();
        this._initControls();
        this._initCluster();
        this._subscribes();
        this._bindEvents();
        this._showMap();
    }

    private _bindEvents(): void {
        RESIZE_EVENTS.forEach((event: string): void => {
            window.addEventListener(event, Utils.debounce(this._resize.bind(this), 100));
        });
    }

    private _resize(): void {
        this._setDeviceSettings();
    }

    /**
     * Метод собирает все настройки в единый объект. Если нет ответа от сервера, карта все равно будет работать
     * с дефолтными настройками
     * @private
     */
    private async _setSettings(): Promise<void> {
        this._connectSettings(this._setDefaultSettings());
        this._connectSettings(this._setScriptSettings());
        this._preBody();

        if (this.url) {
            try {
                const response = await axios.post(this.url, this.body);

                this._connectSettings(response.data.data);
            } catch (err) {
                console.error(`Не удалось получить данные от сервера, карта работает с дефолтными настройками ${err}`);
            }

            return;
        }

        if (this.json) {
            const decode = JSON.parse(atob(this.json));

            this._connectSettings(decode.data);
        }
    }

    /**
     * Метод запускает создание экземпляра яндекс карт. Здесь происходит непосредственная вставка карты на страницу.
     * При ошибке выбросит исключение и положит весь остальной процесс. Т.к при ошибке инита нет смысла выполнять
     * дальнейшие методы
     * @return {Promise<void>} обещание на инициализацию карт
     * @private
     */
    private async _initYandexMap(): Promise<void> {
        const insertMap = (): Promise<void> => {
            return new Promise((resolve: () => void): void => {
                this.map = new this.ymaps.Map(this.id, this.settings, {
                    suppressMapOpenBlock: true,
                    minZoom             : Math.min(this.settings.zoom, this.settings.minZoom),
                    maxZoom             : Math.max(this.settings.zoom, this.settings.maxZoom)
                });

                if (this.map) {
                    resolve();
                } else {
                    throw new Error();
                }
            });
        };

        try {
            return await insertMap();
        } catch (err) {
            console.error(`При инициализации Яндекс Карт произошла критическая ошибка ${err}`);
            throw new Error();
        }
    }

    /**
     * Склеивает настройки из одного источника в общую настройку всей карты.
     * @param {object} outerSettings - настройки из вне.
     * @private
     */
    private _connectSettings(outerSettings: any): void {
        if (typeof outerSettings === 'object') {
            Object.assign(this.settings, outerSettings);
        }
    }

    /**
     * Устанавливает настройки по умолчанию.
     * @return {{
     * center: number[],
     * zoomStep: number,
     * controls: Array,
     * zoomControl: boolean,
     * customZoomControl: boolean,
     * fullScreenControl: boolean,
     * customFullScreenControl: boolean,
     * height: number,
     * zoomScroll: boolean}} - Объект настройки карты
     * @private
     */
    private _setDefaultSettings(): IYMapsSettings {
        const latCenter = 59.939014;
        const lngCenter = 30.315545;
        const heightMap = this.content.offsetHeight;

        return {
            height                 : heightMap,
            center                 : [latCenter, lngCenter],
            zoom                   : 12,
            minZoom                : 10,
            maxZoom                : 18,
            zoomControl            : true,
            zoomStep               : 1,
            customZoomControl      : true,
            // setBounds при загрузке
            autoZoom               : true,
            // отступы при setBounds
            zoomMargin             : 250,
            zoomScroll             : false,
            fullScreenControl      : true,
            customFullScreenControl: true,
            controls               : [],
            markers                : []
        };
    }

    /**
     * Возвращает настройки из скрипта - app.js
     * @return {object} - данные указанные в вызове метода init в app.js
     * @private
     */
    private _setScriptSettings(): IYMapsOuterSettings {
        return this.outerOptions;
    }

    private _setDeviceSettings(): void {
        if (!this.map) {
            return;
        }

        let method: string = '';
        const settingsDraggable: boolean = this.settings.disableMobileDrag ?? true;
        const isMobile = {
            Android: () => {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: () => {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: () => {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: () => {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: () => {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: () => {
                /* eslint-disable new-cap */
                return isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() ||
                    isMobile.Windows();
                /* eslint-enable new-cap */
            }
        };

        if (isMobile.any() && settingsDraggable) {
            method = 'disable';
        } else {
            method = 'enable';
        }

        this.map.behaviors[method]('drag');
        this._initOverlay();
    }

    /**
     * Вставляет и инициализирует overlay для мобильных устройств
     * @private
     */
    private _initOverlay(): void {
        if (!this.map || this.overlay || this.isOverlayHide ||
            !Utils.isTouchDevice() || window.innerWidth >= EBreakpoint.TABLET_WIDE) {
            return;
        }

        const isDraggable: boolean = this.map.behaviors.isEnabled('drag');

        Utils.insetContent(this.mapWrapper, overlayTmpl({isDraggable}));
        this.overlay = this.mapWrapper.querySelector('.j-map__overlay') as HTMLElement;
        this._removeOverlay = this._removeOverlay.bind(this);
        this.mapWrapper.addEventListener('touchstart', this._removeOverlay);
    }

    private _removeOverlay(): void {
        if (!this.overlay) {
            return;
        }

        this.overlay.classList.add('is-hidden');
        setTimeout(() => {
            // @ts-ignore
            this.overlay.remove();
            this.overlay = null;
            this.isOverlayHide = true;
        }, 300);
        this.mapWrapper.removeEventListener('touchstart', this._removeOverlay);
    }

    /**
     * Инициализация маркера на визуальном выборщике.
     */
    private _initMarker(): void {
        this.settings.markers.forEach((item: IYandexMapMarkerData) => {
            const marker: IYandexMapMarker = new Marker();

            this.geoObjectCollection.add(marker.createHtmlMarker(item));
            if (this.map) {
                marker.closeBalloonOnClickMap(this.map);
            }
        });

        const generalTypeMarkers = this.settings.markers.filter((item: IYandexMapMarkerData) => {
            return item.type === 'all';
        });

        this.geoObjectCollectionArray = this.geoObjectCollection.toArray().map((x: ymaps.IGeoObject) => {
            return x;
        });
        this.map!.geoObjects.add(this.geoObjectCollection);

        if (generalTypeMarkers.length > 1 && this.settings.autoZoom) {
            const geoCollectionBounds = this.geoObjectCollection.getBounds();

            if (geoCollectionBounds !== null) {
                this.map!.setBounds(geoCollectionBounds, {
                    // @ts-ignore
                    zoomMargin: this.settings.zoomMargin
                });
            }
        }

        this._bindMapClick();
    }

    /**
     * Инициализация фоновых изображений на карте
     * @private
     */
    private _initPolygons(): void {
        if (!this.settings.polygons?.length) {
            return;
        }

        this.settings.polygons.forEach((polyData: IYandexMapPolygonData): void => {
            const polygon: ymaps.GeoObject = new this.ymaps.GeoObject({
                geometry: {
                    type       : 'Rectangle',
                    // @ts-ignore
                    coordinates: polyData.coords
                }
            }, {
                stroke       : false,
                fillImageHref: polyData.image,
                fillMethod   : 'stretch',
                cursor       : 'default'
            });

            this.map!.geoObjects.add(polygon);
        });
    }

    /**
     * Закрытие панели карты
     * @private
     */
    private _bindMapClick(): void {
        const bindMapClick = new CustomEvent('bindMapClick');

        window.dispatchEvent(bindMapClick);

        if (this.map) {
            this.map.events.add('click', () => {
                // Брейкпоинт, после которого маркеры открываются не по клику, а по ховеру
                if (window.innerWidth < EBreakpoint.DESKTOP) {
                    window.dispatchEvent(bindMapClick);
                }
            });
        }
    }

    /**
     * Метод устанавливает кастомные контроллы и прочие элементы управления.
     * @private
     */
    private _initControls(): void {
        const controls = new Controls();

        if (this.map) {
            controls.init(this.map, this.settings, this.ymaps);
        }
    }

    /**
     * Метод инициализирует кластеры
     * @private
     */
    private _initCluster(): void {
        if (this.outerOptions.cluster) {
            this.cluster = new Cluster();

            this.cluster.init(this.geoObjectCollection);
        }
    }

    /**
     * Метод скрывает прелоадер и показывает карту по завершению выполнения всех методов.
     * @private
     */
    private _showMap(): void {
        this.content.style.opacity = '1';
    }

    /**
     * Подготавливает тело ajax-запроса, в данном случае это гет-параметры, если они есть.
     * @private
     */
    private _preBody(): void {
        const get = window.location.search;

        if (!get) {
            this.body = '';

            return;
        }

        this.body = new FormData();

        const url = new URLSearchParams(get);
        const key = 0;
        const value = 1;

        for (const item of url.entries()) {
            this.body.append(item[key], item[value]);
        }
    }

    /**
     * Отображает геообъекты на карте и в кластерах
     * @param {ymaps.GeoObject} geoObject - тип маркера
     * @private
     */
    private _showGeoObject(geoObject: ymaps.IGeoObject): void {
        this.cluster.addGeoObjectToCluster(geoObject);
    }

    /**
     * Скрывает геообъекты на карте и в кластерах
     * @param {String} geoObject - объект карты
     * @private
     */
    private _hideGeoObject(geoObject: ymaps.IGeoObject): void {
        this.cluster.removeGeoObjectToCluster(geoObject);
    }

    /**
     * Обёртка для переключения геообъектов по типу
     * @param {string} itemType - тип геообъекта
     * @param {Function} callback - коллбэк, который будет применён к геообъекту
     * @private
     */
    private _toggleGeoObjects(itemType: string, callback: (gObject: ymaps.IGeoObject) => void): void {
        this.geoObjectCollectionArray.forEach((geoObject: ymaps.IGeoObject): void => {
            // @ts-ignore
            if (geoObject.options.getNative('data')?.type !== itemType) {
                return;
            }

            callback(geoObject);
        });
    }

    /**
     * Открывает баллун по клику на кнопку.
     * @param {string} id - дата атрибут из какого-либо объекта
     * @param {string} type - параметр в геообъекте по которому будет производиться поиск
     * @private
     */
    private _openBalloon(id: string, type: string): void {
        const idButton = Number(id);

        this.geoObjectCollection.each((item: ymaps.IGeoObject): void => {
            const placemark = item as ymaps.Placemark;
            const idMarker = Number(item.options.getNative(type));

            if (idButton === idMarker) {
                const panel = placemark.options.getNative('panel');
                const coords = placemark.geometry?.getCoordinates();

                // @ts-ignore
                mapUtils.needBalloonPanel() ? panel.open(item) : placemark.balloon.open();

                if (coords) {
                    this._setCenterMap(coords);
                }
            }
        });
    }

    /**
     * Центрирует карту относительно координат
     * @param {number[]} coords - координаты центра.
     * @private
     */
    private _setCenterMap(coords: number[]): void {
        if (!this.map) {
            return;
        }

        if (mapUtils.isMobile()) {
            this.map.setCenter(coords, this.settings.zoom);
        }
    }
}

export default YandexMap;
