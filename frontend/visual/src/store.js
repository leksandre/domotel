import axios from 'axios';
import loadHandler from './helpers/loadHandler';
import SVGPathCommander from 'svg-path-commander';
import {Utils} from '@/common/scripts/utils';
import Vue from 'vue';
import Vue2TouchEvents from 'vue2-touch-events';
import Vuex from 'vuex';
const url = '/tests/visual/visual.php';

Vue.use(Vuex);
Vue.use(Vue2TouchEvents);

export default new Vuex.Store({
    /**
     * Глобальное хранилище для всего приложения
     * Доступ только через геттеры, изменение через мутацию.
     */
    state: {
        settings: {
            estatePlural: [],
            hidePrice   : {
                status: false,
                text  : ''
            },
            popup: {
                id: ''
            },
            zoom: true
        },
        data: {
            render      : '',
            elements    : '',
            breadcrumbs : '',
            elementsArea: null
        },
        compass: {
            degree: 0
        },
        canvasSize: {
            width : 0,
            height: 0
        },
        canvasShift: {
            horizontal: 0,
            vertical  : 0
        },
        perspective   : null,
        rotateId      : null,
        filters       : [],
        roomsFilter   : {},
        floorsFilter  : {},
        filtersCount  : 0,
        pointers      : [],
        renderPointers: [],
        dataStep      : {},
        form          : '',
        formData      : '',
        isProcessing  : false,
        isReady       : false,
        isPinch       : true,
        floorStep     : false,
        iframeView    : false,
        gridFrame     : false,
        isMobile      : false,
        isTablet      : false,
        isTouch       : Utils.isTouchDevice(),
        isTouchTable  : false,
        panorama      : []
    },

    /**
     * Методы для получения данных из стейта.
     */
    getters: {
        /**
         * @param {Object} state - данные визуального
         * @return {string} - путь к изображению-подложке
         * @constructor
         */
        GET_RENDER(state) {
            return state.data.render;
        },

        GET_ELEMENTS(state) {
            return state.data.elements;
        },

        /**
         * @param {Object} state - данные визуального
         * @return {Object|null} - координаты области расположения масок
         * @constructor
         */
        GET_ELEMENTS_AREA(state) {
            return state.data.elementsArea;
        },

        GET_CANVAS_SIZE(state) {
            return state.canvasSize;
        },

        GET_CANVAS_SHIFT(state) {
            return state.canvasShift;
        },

        /**
         * Возвращает объект с данными о ракурсах
         * @param {object} state - объект состояния данных
         * @returns {null|{plan: string, points: [{id: (number|string), top: number, left: number, deg: number}]}} - объект с данными о ракурсах
         */
        GET_PERSPECTIVE(state) {
            return state.perspective;
        },

        GET_ROTATE_ID(state) {
            return state.rotateId;
        },

        GET_FILTERS(state) {
            return state.filters;
        },

        GET_ROOMS_FILTER(state) {
            return state.roomsFilter;
        },

        GET_FLOORS_FILTER(state) {
            return state.floorsFilter;
        },

        GET_FLOORS_FILTER_COUNT(state) {
            return state.filtersCount;
        },

        GET_POINTERS(state) {
            return state.pointers;
        },

        GET_RENDER_POINTERS(state) {
            return state.renderPointers;
        },

        GET_BREADCRUMBS(state) {
            return state.data.breadcrumbs;
        },

        GET_DATA_STEP(state) {
            return state.dataStep;
        },

        GET_COMPASS(state) {
            return state.compass;
        },

        GET_PANORAMA(state) {
            return state.panorama;
        },

        GET_FORM_DATA(state) {
            return state.formData;
        },

        GET_FORM(state) {
            return state.form;
        },

        GET_IS_PROCESSING(state) {
            return state.isProcessing;
        },

        GET_IS_READY(state) {
            return state.isReady;
        },

        GET_FLOOR_STEP(state) {
            return state.floorStep;
        },

        GET_HIDDEN_PRICE(state) {
            return state.settings.hidePrice;
        },

        /**
         * Метод отдает массив заголовков по склонениям по типу выборщика (квартиры / коммерция).
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_ESTATE_PLURAL(state) {
            return state.settings.estatePlural;
        },

        GET_IS_PINCH(state) {
            return state.isPinch;
        },

        GET_IS_POPUP(state) {
            return state.settings.popup;
        },

        GET_IS_IFRAME(state) {
            return state.iframeView;
        },

        GET_GRID_FRAME(state) {
            return state.gridFrame;
        },

        GET_ZOOM(state) {
            return state.settings.zoom;
        },

        /**
         * Возвращает параметр определяющий тип - тач устройство или нет
         * @param {object} state - объект состояния данных
         * @returns {object} возвращает параметр определяющий тип - тач устройство или нет
         */
        GET_IS_TOUCH_TABLE(state) {
            return state.isTouchTable;
        },

        GET_IS_MOBILE(state) {
            return state.isMobile;
        },

        GET_IS_TABLET(state) {
            return state.isTablet;
        },

        GET_IS_TOUCH(state) {
            return state.isTouch;
        }
    },

    /**
     * События, которые введут за собой изменение стейта.
     * https://vuex.vuejs.org/ru/guide/actions.html
     */
    actions: {
        send({commit, dispatch}, {body, query = {}}) {
            commit('SET_IS_PROCESSING', true);
            commit('SET_GRID_FRAME');
            commit('SET_DATA_STEP', body);
            commit('SET_FORM_DATA', {
                type: 'init',
                query
            });

            return new Promise((resolve) => {
                axios.post(Vue.prototype.$dataset.url || url, this.getters.GET_FORM_DATA)
                    .then((response) => {
                        const status = response.data.success;
                        const errors = response.data.messages;

                        if (!status) {
                            throw new Error(errors.length ? errors : 'Ошибка на сервере');
                        }

                        if (errors && errors.length) {
                            errors.forEach((error) => {
                                console.error(error);
                            });
                        }

                        const data = response.data.data;
                        const render = data.render.link;

                        loadHandler(render)
                            .then((size) => {
                                dispatch('setInfo', {
                                    data,
                                    size
                                }).then(() => {
                                    resolve();
                                });
                            });
                    })
                    .catch((error) => {
                        console.error(`При получении данных произошла ошибка ${error}`);
                        throw new Error();
                    });
            });
        },

        setInfo({commit}, {data, size}) {
            commit('SET_CANVAS_SIZE', size);
            commit('SET_HIDDEN_PRICE', data);
            commit('SET_ZOOM', data);
            commit('SET_IS_POPUP', data);
            commit('SET_DATA', data);
            commit('SET_CANVAS_SHIFT', data.render.shift);
            commit('SET_ELEMENTS_AREA', data);
            commit('SET_PERSPECTIVE', data);
            commit('SET_ROTATE_ID', data);
            commit('SET_POINTERS', data);
            commit('SET_FILTERS', data);
            commit('SET_RENDER_POINTERS', data);
            commit('SET_COMPASS', data.compass);
            commit('SET_PANORAMA_POINTERS', data);
            commit('SET_FLOOR_STEP');
            commit('SET_IS_PROCESSING', false);
            commit('SET_IS_READY');
        },

        changeFilter({commit, getters}, {form, query = {}}) {
            commit('SET_FORM', form);
            commit('SET_FORM_DATA', {
                type: 'filter',
                query
            });
            commit('SET_IS_PROCESSING', true);

            axios.post(Vue.prototype.$dataset.url || url, getters.GET_FORM_DATA)
                .then((response) => {
                    const data = response.data.data;

                    commit('SET_ELEMENTS', data);
                    commit('SET_POINTERS', data);
                    commit('SET_FILTERS', data);
                    commit('SET_IS_PROCESSING', false);
                })
                .catch((error) => {
                    console.error(`При фильтрации произошла ошибка= ${error}`);
                });
        },

        changeStateLoading({commit}, state) {
            commit('SET_IS_PROCESSING', state);
        },

        changeStatePinch({commit}, state) {
            commit('SET_IS_PINCH', state);
        },

        /**
         * Устанавливает стартовые настройки, которые не будут меняться на протяжении всей работы приложения
         * @param {function} commit - - метод запуска мутации
         */
        setStartSettings({commit}) {
            commit('SET_IS_IFRAME');
            commit('SET_ESTATE_PLURAL');
            commit('SET_IS_TOUCH_TABLE');
        },

        changeRotate({commit, dispatch, getters}, query) {
            commit('SET_IS_PROCESSING', true);
            commit('SET_FORM_DATA', {
                type: 'changeRotate',
                query
            });

            return new Promise((resolve) => {
                axios.post(Vue.prototype.$dataset.url || url, getters.GET_FORM_DATA)
                    .then((response) => {
                        const status = response.data.success;
                        const errors = response.data.messages;

                        if (!status) {
                            throw new Error(errors.length ? errors : 'Ошибка на сервере');
                        }

                        if (errors && errors.length) {
                            errors.forEach((error) => {
                                console.error(error);
                            });
                        }

                        const data = response.data.data;
                        const render = data.render.link;

                        loadHandler(render)
                            .then((size) => {
                                dispatch('setInfo', {
                                    data,
                                    size
                                }).then(() => {
                                    resolve();
                                });
                            });
                    })
                    .catch((error) => {
                        console.error(`При получении данных произошла ошибка ${error}`);
                        throw new Error();
                    });
            });
        },

        setIsMobile({commit}, value) {
            commit('SET_IS_MOBILE', value);
        },

        setIsTablet({commit}, value) {
            commit('SET_IS_TABLET', value);
        }
    },

    /**
     * Методы для изменения стейта.
     */
    mutations: {
        SET_CANVAS_SIZE(state, data) {
            state.canvasSize = data;
        },

        SET_DATA(state, data) {
            state.data = data;
        },

        SET_CANVAS_SHIFT(state, shift) {
            if (!shift) {
                state.canvasShift = {
                    horizontal: 0,
                    vertical  : 0
                };

                return;
            }

            const horizontalShift = shift.horizontal ? Number(shift.horizontal) : 0;
            const verticalShift = shift.vertical ? Number(shift.vertical) : 0;

            state.canvasShift.horizontal = horizontalShift >= 0 && horizontalShift <= 100 ? horizontalShift / 100 : 0;
            state.canvasShift.vertical = verticalShift >= 0 && verticalShift <= 100 ? verticalShift / 100 : 0;
        },

        SET_PERSPECTIVE(state, data) {
            state.perspective = data?.perspective || null;
        },

        SET_ROTATE_ID(state, data) {
            state.rotateId = data?.perspective?.points?.find((item) => {
                return item.active;
            }).id || null;
        },

        SET_ELEMENTS(state, data) {
            state.data.elements = data.elements;
        },

        SET_POINTERS(state) {
            const elements = state.data.elements;
            const pointers = [];

            elements.forEach((element) => {
                if (element.pointer) {
                    const pointerData = {
                        ...element.pointer,
                        id           : Number(element.id),
                        link         : element.link,
                        disabled     : element.disabled,
                        booked       : element.booked,
                        isShowTooltip: element.isShowTooltip,
                        isSimpleLink : element.isSimpleLink
                    };

                    pointers.push(pointerData);
                }
            });

            state.pointers = pointers;
        },

        SET_RENDER_POINTERS(state, data) {
            state.renderPointers = data?.pointers?.length ?
                data.pointers.filter((item) => {
                    return item.type !== 'panorama';
                }) :
                [];
        },

        SET_FILTERS(state, data) {
            state.filters = data.filters || [];

            this.commit('STATE_ROOMS_FILTER');
            this.commit('SET_FLOORS_FILTER');
            this.commit('SET_FLOORS_FILTER_COUNT');
        },

        STATE_ROOMS_FILTER(state) {
            state.filters.filter((filter) => {
                return filter.category === 'room' && Object.assign(state.roomsFilter, filter);
            });
        },

        SET_FLOORS_FILTER(state) {
            const floorsFilter = state.filters.find((filter) => {
                return filter.category === 'floor' && filter;
            }) || {};

            if (floorsFilter && floorsFilter.items) {
                floorsFilter.items.sort((a, b) => {
                    return a?.priority > b?.priority ? 1 : -1;
                });
            }
            state.floorsFilter = floorsFilter;
        },

        SET_FLOORS_FILTER_COUNT(state) {
            state.filtersCount = state.filtersCount + 1;
        },

        SET_DATA_STEP(state, data) {
            state.dataStep = data;
        },

        SET_COMPASS(state, data) {
            state.compass = data;
        },

        SET_PANORAMA_POINTERS(state, data) {
            state.panorama = data?.pointers?.length ?
                data?.pointers?.filter((item) => {
                    return item.type === 'panorama';
                }) :
                [];
        },

        SET_FORM(state, form) {
            state.form = form;
        },

        SET_FORM_DATA(state, {type, query = {}}) {
            const form = this.getters.GET_FORM;
            const dataStep = this.getters.GET_DATA_STEP;

            state.formData = form ? new FormData(form) : new FormData();

            for (const item in dataStep) {
                if (Object.prototype.hasOwnProperty.call(dataStep, item)) {
                    if (!state.formData.has(item)) {
                        state.formData.append(item, dataStep[item]);
                    }
                }
            }

            /**
             * query - данные из строки поиска (get-параметры)
             */
            for (const item in query) {
                if (Object.prototype.hasOwnProperty.call(query, item)) {
                    if (!state.formData.has(item)) {
                        state.formData.append(item, query[item]);
                    }
                }
            }

            state.formData.append('type', type);
        },

        SET_IS_PROCESSING(state, value) {
            state.isProcessing = value;
        },

        SET_IS_PINCH(state, value) {
            state.isPinch = value;
        },

        /**
         * Мутация устанавливает вид через iframe, сли в get-араметрах есть iframe = 1/true
         * @param {object} state - текущий стейт
         */
        SET_IS_IFRAME(state) {
            const get = window.location.search;
            const href = window.location.href;
            const urlParams = new URLSearchParams(get);
            const hrefReq = (/frame/).test(href);

            state.iframeView = Boolean(urlParams.get('iframe')) || hrefReq;
        },

        SET_GRID_FRAME(state) {
            state.gridFrame = Vue.prototype.$dataset.iframeTemplate === 'narrow';
        },

        SET_IS_READY(state) {
            state.isReady = true;
        },

        SET_FLOOR_STEP(state) {
            state.floorStep = state.dataStep.step === 'floor';
        },

        SET_HIDDEN_PRICE(state, data) {
            state.settings.hidePrice = data.settings.hidePrice;
        },

        SET_IS_POPUP(state, data) {
            state.settings.popup = data.settings.popup;
        },

        /**
         * Мутация устанавливает массив заголовков по склонениям по типу выборщика (квартиры / коммерция).
         * @param {Object} state - текущий state.
         */
        SET_ESTATE_PLURAL(state) {
            state.settings.estatePlural = JSON.parse(atob(this._vm.$dataset.plural));
        },

        SET_ZOOM(state, data) {
            state.settings.zoom = data.settings.zoom ?? true;
        },

        /**
         * Мутация устанавливает значение крайних точек области расположения масок
         * @param {Object} state - объект состояния данных
         * @constructor
         */
        SET_ELEMENTS_AREA(state) {
            let x1 = state.canvasSize.width;
            let y1 = state.canvasSize.height;
            let x2 = 0;
            let y2 = 0;

            const masks = state.data.elements.filter((element) => {
                return !element.disabled && element.path;
            });

            masks.forEach((element) => {
                const values = SVGPathCommander.getPathBBox(element.path);

                // Для шага этажа высчитываем общую область активных масок
                if (state.dataStep.step === 'floor') {
                    if (values.x < x1) {
                        x1 = values.x;
                    }
                    if (values.y < y1) {
                        y1 = values.y;
                    }
                    if (values.x2 > x2) {
                        x2 = values.x2;
                    }
                    if (values.y2 > y2) {
                        y2 = values.y2;
                    }
                // Для остальных шагов ищем первую маску по x оси
                } else if (values.x < x1) {
                    x1 = values.x;
                    y1 = values.y;
                    x2 = values.x2;
                    y2 = values.y2;
                }
            });

            state.data.elementsArea = {
                x1,
                y1,
                x2,
                y2,
                cx: (x2 + x1) / 2,
                cy: (y2 + y1) / 2
            };
        },

        /**
         * Устанавливает параметр определяющий тач стол или нет
         * @param {object} state - объект состояния данных
         */
        SET_IS_TOUCH_TABLE(state) {
            state.isTouchTable = document.body.dataset.touch && document.body.dataset.touch === 'true';
        },

        SET_IS_MOBILE(state, value) {
            state.isMobile = value;
        },

        SET_IS_TABLET(state, value) {
            state.isTablet = value;
        }
    }
});
