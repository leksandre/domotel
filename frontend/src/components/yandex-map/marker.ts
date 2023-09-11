/**
 * @version 2.0
 * @author Kelnik Studios {http://kelnik.ru}
 */
import type {IYandexMapBalloonData, IYandexMapMarker, IYandexMapMarkerData, IYandexMapPanel} from './types';
import aboutBalloonTemplate from './balloon/about.twig';
import aboutMarkerTemplate from './marker/about.twig';
import balloonTemplate from './balloon/default.twig';
import {EBreakpoint} from '@/common/scripts/constants';
import mapUtils from './utils';
import markerTemplate from './marker/default.twig';
import Panel from './panel';
import type ymaps from 'yandex-maps';

const panel: IYandexMapPanel = new Panel();

class Marker implements IYandexMapMarker {
    // Объект яндекс карт
    private ymaps: typeof ymaps = window.ymaps;
    private hideIconOnBalloonOpen: boolean;
    private timerCloseBalloon: ReturnType<typeof setTimeout> | null;

    private markerTemplate: (data: IYandexMapMarkerData) => string;
    private balloon: IYandexMapMarkerData;
    private balloonTemplate: (data: IYandexMapBalloonData) => string;
    private _balloonSize: [number, number];
    private _balloonIconHeight: number;

    /**
     * Создает HTML маркер
     * @param {object} element - элемент массива маркеров
     * @returns {function} функции инициализации маркера на карте параметрического поиска
     * @public
     */
    public createHtmlMarker(element: IYandexMapMarkerData): ymaps.Placemark {
        this._setTemplateMarker(element);
        this._setTemplateBalloon(element);
        this.balloon = element;

        const CustomLayoutClass = this._createMarkerFromTemplate(element);
        const zIndex = element.zIndex;
        const isBalloon = element.balloon;
        const balloonLayout = mapUtils.needBalloonPanel() || !isBalloon ?
            false :
            this._createBalloonContentLayout();
        const cursor = isBalloon || element.modifyClass === 'office' ? 'pointer' : 'default';

        const marker = new this.ymaps.Placemark(element.coords, {}, {
            type                  : 'Placemark',
            // @ts-ignore
            balloonLayout,
            iconLayout            : CustomLayoutClass,
            hideIconOnBalloonOpen : this.hideIconOnBalloonOpen,
            // текущий шаблон для balloon'а
            template              : this.balloonTemplate,
            // все данные от бэкенда
            data                  : element,
            // z-index для маркера
            zIndex,
            panel,
            cursor,
            isBalloon,
            balloonPanelMaxMapArea: 0
        });

        this._bindEventsMarker(marker);

        // Для табов, если есть такой флаг то изначально маркер будет скрыт
        if (element.isHidden) {
            // @ts-ignore
            marker.options.set('visible', false);
        }

        return marker;
    }

    /**
     * Навешивает обработчки события клик по карте и геобъектам для закрытия balloon'ов.
     * @param {object} map - Экзмепляр созданной карты.
     * @public
     */
    public closeBalloonOnClickMap(map: ymaps.Map): void {
        if (window.innerWidth < EBreakpoint.DESKTOP) {
            map.geoObjects.events.add('click', () => {
                if (map.balloon.isOpen()) {
                    map.balloon.close();
                }
            });

            map.events.add('click', () => {
                if (map.balloon.isOpen()) {
                    map.balloon.close();
                }
            });
        }
    }

    /**
     * Создает HTML маркер с определением размера маркера и оффсетами
     * @param {object} element - элемент массива маркеров
     * @return {object} - элемент маркера
     * @private
     */
    // eslint-disable-next-line max-lines-per-function
    private _createMarkerFromTemplate(element: IYandexMapMarkerData):
    ymaps.IClassConstructor<ymaps.layout.templateBased.Base> {
        return this.ymaps.templateLayoutFactory.createClass(this.markerTemplate(element), {
            build() {
                // @ts-ignore
                this.constructor.superclass.build.call(this);
                // @ts-ignore
                this._element = this.getParentElement().querySelector('.j-yandex-map-marker') as HTMLElement;
                // @ts-ignore
                this._options = this.getData().options;
                // @ts-ignore
                this._size = [this._element.offsetWidth, this._element.offsetHeight];
                // @ts-ignore
                this._offset = [-this._size[0] / 2, -this._size[1] / 2];

                const iconShape = {
                    type       : 'Rectangle',
                    // @ts-ignore
                    coordinates: [this._offset, [this._offset[0] + this._size[0], this._offset[1] + this._size[1]]]
                };

                // @ts-ignore
                this._options.set('shape', iconShape);
                // @ts-ignore
                this._applyElementOffset(this._offset);
                this._bindEvents();
            },

            _bindEvents() {
                // @ts-ignore
                if (!this.inited) {
                    // @ts-ignore
                    this.inited = true;
                    // @ts-ignore
                    this.innerWidth = window.innerWidth;

                    window.addEventListener('resize', () => {
                        // @ts-ignore
                        if (window.innerWidth !== this.innerWidth) {
                            // @ts-ignore
                            this.innerWidth = window.innerWidth;
                            // @ts-ignore
                            this.rebuild();
                        }
                    });
                }
            },

            // _calcElementOffset() {
            //     switch (element.modifyClass) {
            //         case 'complex':
            //         case 'infrastructure':
            //         default:
            //             return [-this._size[0] / 2, -this._size[1] / 2];
            //     }
            // },

            _applyElementOffset() {
                // @ts-ignore
                this._element.style.marginLeft = `${this._offset[0]}px`;
                // @ts-ignore
                this._element.style.marginTop = `${this._offset[1]}px`;
            }
        });
    }

    /**
     * Устанавливает шаблон для маркера исходя из модификатора.
     * @param {object} element - данные для маркера от бэкенда.
     * @private
     */
    private _setTemplateMarker(element: IYandexMapMarkerData): void {
        switch (element.modifyClass) {
            case 'yandex-map-balloon_theme_routes':
                this.hideIconOnBalloonOpen = false;
                this.markerTemplate = aboutMarkerTemplate;
                break;
            default:
                this.hideIconOnBalloonOpen = false;
                this.markerTemplate = markerTemplate;
                break;
        }
    }

    /**
     * Устанавливает шаблон для balloon'а исходя из модификатора.
     * @param {object} element - данные для маркера
     */
    private _setTemplateBalloon(element: IYandexMapMarkerData): void {
        switch (element.modifyClass) {
            case 'yandex-map-balloon_theme_routes':
                this.balloonTemplate = aboutBalloonTemplate;
                break;
            default:
                this.balloonTemplate = balloonTemplate;
                break;
        }
    }

    /**
     * Создает шаблон balloon'а
     * @returns {object} - шаблон balloon'а для использования в методе инициализации маркеров.
     * @private
     */
    private _createBalloonContentLayout(): ymaps.IClassConstructor<ymaps.layout.templateBased.Base> {
        const that = this;
        // @ts-ignore
        const BalloonLayout = this.ymaps.templateLayoutFactory.createClass(this.balloonTemplate(this.balloon), {

            /**
             * Строит экземпляр макета на основе шаблона и добавляет его в родительский HTML-элемент.
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/layout.templateBased.Base.xml#build
             */
            build() {
                // @ts-ignore
                this.constructor.superclass.build.call(this);
                // @ts-ignore
                this.balloon = document.querySelector('.j-yandex-map-balloon') as HTMLElement;
                // @ts-ignore
                that._balloonOffset(this.balloon);
            },

            /**
             * Удаляет содержимое макета из DOM.
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/layout.templateBased.Base.xml#clear
             */
            clear() {
                // @ts-ignore
                this.constructor.superclass.clear.call(this);
            },

            /**
             * Закрывает balloon при клике на крестик, кидая событие "userclose" на макете.
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/IBalloonLayout.xml#event-userclose
             * @param {event} event - событие
             */
            onCloseClick(event: ymaps.Event) {
                event.preventDefault();
                // @ts-ignore
                this.events.fire('userclose');
            },

            /**
             * Метод будет вызван системой шаблонов АПИ при изменении размеров вложенного макета.
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/IBalloonLayout.xml#event-userclose
             */
            onSublayoutSizeChange() {
                // @ts-ignore
                BalloonLayout.superclass.onSublayoutSizeChange.apply(this, arguments);
                // @ts-ignore
                if (!this.balloon) {
                    return;
                }
                // @ts-ignore
                this.events.fire('shapechange');
            },

            /**
             * Используется для автопозиционирования (balloonAutoPan).
             * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/ILayout.xml#getClientBounds
             * @returns {object} - Координаты левого верхнего и правого нижнего углов шаблона относительно точки привязки.
             */
            // @ts-ignore
            getShape() {
                // @ts-ignore
                if (!this.balloon) {
                    // @ts-ignore
                    return BalloonLayout.superclass.getShape.call(this);
                }

                // @ts-ignore
                const balloonHeight = this.balloon.offsetHeight;
                // @ts-ignore
                const balloonWidth = this.balloon.offsetWidth;
                // @ts-ignore
                const positionTop = this.balloon.offsetTop;
                // @ts-ignore
                const positionLeft = this.balloon.offsetLeft;

                const bounds: number[][] =
                    [[positionLeft, positionTop], [positionLeft + balloonWidth, positionTop + balloonHeight]];

                return new window.ymaps.shape.Rectangle(new window.ymaps.geometry.pixel.Rectangle(bounds));
            }
        });

        return BalloonLayout;
    }

    /**
     * Метод сдвигает balloon, чтобы "хвостик" указывал на точку привязки.
     * @see https://api.yandex.ru/maps/doc/jsapi/2.1/ref/reference/IBalloonLayout.xml#event-userclose
     * @param {object} balloon - DOM элемент balloon'а
     */
    private _balloonOffset(balloon: HTMLElement): void {
        this._balloonSize = [balloon.offsetWidth, balloon.offsetHeight];
        this._balloonIconHeight = this.balloon.icon.size.height;

        balloon.style.top = this._calcTopBalloonOffset();
        balloon.style.left = `${-(this._balloonSize[0] / 2)}px`;
    }

    private _calcTopBalloonOffset(): string {
        switch (this.balloon.modifyClass) {
            case 'object':
                return `${-(this._balloonSize[1] + (this._balloonIconHeight / 2))}px`;
            default:
                return `${-(this._balloonSize[1] + this._balloonIconHeight)}px`;
        }
    }

    /**
     * Привязываем события маркера
     * @param {Object} marker - текущий маркер
     * @private
     */
    private _bindEventsMarker(marker: ymaps.Placemark): void {
        if (window.innerWidth >= EBreakpoint.DESKTOP) {
            marker.events.add('mouseenter', this._onMouseenterMarker.bind(this));
            marker.events.add('mousemove', this._onMousemoveMarker.bind(this));
            marker.events.add('mouseleave', this._onMouseleaveMarker.bind(this));
        } else {
            marker.events.add('click', this._onMouseenterMarker.bind(this));
        }
    }

    /**
     * Событие при наведении мышки на маркер
     * @param {Object} event - объект события наведения мышки
     */
    private _onMouseenterMarker(event: ymaps.Event): void {
        const target = event.get('target');
        const balloon = target.balloon;

        if (mapUtils.needBalloonPanel() && target.options.get('isBalloon')) {
            panel.open(target);
        } else {
            this._bindEventsBalloon(balloon);
        }
    }

    /**
     * Событие при движении мышки над маркером
     * @param {Object} event - объект события движении мышки над маркером
     */
    private _onMousemoveMarker(event: ymaps.Event): void {
        const balloon = event.get('target').balloon;
        const isOpen = balloon.isOpen();

        if (!isOpen) {
            balloon.open();
        }
    }

    /**
     * Событие при убирании мышки с маркера
     * @param {Object} event - объект события убирания мышки с маркера
     */
    private _onMouseleaveMarker(event: ymaps.Event): void {
        const balloon = event.get('target').balloon;

        this.timerCloseBalloon = this._getSetTimeoutClosingBalloon(balloon);
    }

    /**
     * Привязываем события balloon'а
     * @param {Object} balloon - текущий balloon
     */
    private _bindEventsBalloon(balloon: ymaps.Balloon): void {
        balloon.events.add('mouseenter', this._onMouseenterBalloon.bind(this));
        balloon.events.add('mouseleave', this._onMouseleaveBalloon.bind(this));
    }

    /**
     * Событие при наведении мышки на balloon
     */
    private _onMouseenterBalloon(): void {
        if (this.timerCloseBalloon) {
            clearTimeout(this.timerCloseBalloon);
        }
        this.timerCloseBalloon = null;
    }

    /**
     * Событие, при отсутствии hover'а на balloon'е
     * @param {Object} event - объект события потери мышки с balloon
     * @private
     */
    private _onMouseleaveBalloon(event: ymaps.Event): void {
        const balloon = event.get('target').balloon;

        this._getSetTimeoutClosingBalloon(balloon);
    }

    /**
     * Получение id установленного таймаута закрытия
     * @param {Object} balloon - текущий balloon
     * @return {number} -
     */
    private _getSetTimeoutClosingBalloon(balloon: ymaps.Balloon): ReturnType<typeof setTimeout> {
        return setTimeout(() => {
            balloon.close();
        }, 100);
    }
}

export default Marker;
