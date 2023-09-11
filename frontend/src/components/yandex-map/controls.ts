import type {IYandexMapControls, IYMapsSettings} from './types';
import fullscreenTemplate from './custom-fullscreen/fullscreen-template.twig';
import type ymaps from 'yandex-maps';
import zoomTemplate from './custom-zoom/zoom-template.twig';

class Controls implements IYandexMapControls {
    private map: ymaps.Map;
    private settings: IYMapsSettings;
    private ymaps: typeof ymaps;

    public init(map: ymaps.Map, settings: IYMapsSettings, ymap: typeof ymaps): void {
        this.map = map;
        this.settings = settings;
        this.ymaps = ymap;

        this._initZoom();
        this._initScrollZoom();
        this._addFullScreenControl();
    }

    /**
     * Добавляет зум на карту.
     * @private
     */
    private _initZoom(): void {
        if (!this.settings.zoomControl) {
            return;
        }

        if (this.settings.customZoomControl) {
            this._initCustomZoomControl();
        } else {
            this.map.controls.add('zoomControl', {
                // @ts-ignore
                zoomStep: this.settings.zoomStep || 1
            });
        }
    }

    /**
     * Разрешает или запрещает сколл страницы при фокусе на карте.
     * @private
     */
    private _initScrollZoom(): void {
        const method = this.settings.zoomScroll ? 'enable' : 'disable';

        this.map.behaviors[method]('scrollZoom');
    }

    /**
     * Добавляет кнопку открытия карты на полный экран
     * @private
     */
    private _addFullScreenControl(): void {
        if (!this.settings.fullScreenControl) {
            return;
        }

        if (this.settings.customFullScreenControl) {
            this._initCustomFullscreenControl();
        } else {
            this.map.controls.add('fullscreenControl');
        }
    }

    /**
     * Инициализация кастомных контролов
     * @private
     */
    private _initCustomZoomControl(): void {
        const height: number = this.map.container.getSize()[1] || 0;
        const mapId: string = this.map.container.getParentElement().id;
        const zoomStep: number = this.settings.zoomStep || 1;

        const ZoomLayout = this.ymaps.templateLayoutFactory.createClass(zoomTemplate({mapId}), {
            // Переопределяем методы макета, чтобы выполнять дополнительные действия
            // при построении и очистке макета.
            build() {
                // Вызываем родительский метод build.
                // @ts-ignore
                ZoomLayout.superclass.build.call(this);

                // Начинаем слушать клики на кнопках макета.
                document.querySelector(`#${mapId}-zoom-in`)!.addEventListener('click', () => {
                    this.zoomIn();
                });
                document.querySelector(`#${mapId}-zoom-out`)!.addEventListener('click', () => {
                    this.zoomOut();
                });
            },

            clear() {
                // Вызываем родительский метод clear.
                // @ts-ignore
                ZoomLayout.superclass.clear.call(this);
            },

            zoomIn() {
                // @ts-ignore
                const map = this.getData().control.getMap();

                map.setZoom(map.getZoom() + zoomStep, {
                    checkZoomRange: true
                });
            },

            zoomOut() {
                // @ts-ignore
                const map = this.getData().control.getMap();

                map.setZoom(map.getZoom() - zoomStep, {
                    checkZoomRange: true
                });
            }
        });

        const zoomControl = new this.ymaps.control.ZoomControl({
            options: {
                // @ts-ignore
                layout  : ZoomLayout,
                position: {
                    top  : height / 2,
                    right: 16
                },
                zoomStep
            }
        });


        this.map.controls.add(zoomControl);
    }

    private _initCustomFullscreenControl(): void {
        const FullscreenLayout = this.ymaps.templateLayoutFactory.createClass(fullscreenTemplate());

        const fullscreenControl = new this.ymaps.control.FullscreenControl({
            options: {
                layout  : FullscreenLayout,
                position: {
                    top  : undefined,
                    right: 0
                }
            }
        });

        this.map.controls.add(fullscreenControl);
    }
}

export default Controls;
