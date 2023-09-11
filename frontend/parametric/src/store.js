import axios from 'axios';
import Observer from '@/common/scripts/observer';
import Vue from 'vue';
import Vue2TouchEvents from 'vue2-touch-events';
import Vuex from 'vuex';
const url = '/tests/parametric/parametric.php';
const defaultResultView = 1;
const observer = new Observer();

Vue.use(Vuex);
Vue.use(Vue2TouchEvents);

export default new Vuex.Store({
    /**
     * Глобальное хранилище для всего приложения.
     * Доступ только через геттеры, изменение через мутацию.
     */
    state: {
        settings: {
            estateHeading : '',
            estatePlural  : '',
            premisesToShow: '',
            groupOptions  : 0,
            sort          : {},
            view          : {
                type  : '',
                switch: true
            },
            hidePrice: {
                status: false,
                text  : ''
            },
            popup: {
                id: ''
            }
        },
        filters     : [],
        filtersCount: 0,
        premises    : [],
        page        : 1,
        formData    : null,
        isReady     : false,
        isProcessing: false,
        currentSort : {
            type : '',
            order: ''
        },
        pagination    : false,
        paginationMode: 'next',
        isMobile      : false
    },

    /**
     * Методы для получения данных из state.
     */
    getters: {
        /**
         * Метод отдает заголовок по типу выборщика (квартиры / коммерция).
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_ESTATE_HEADING(state) {
            return state.settings.estateHeading;
        },

        /**
         * Метод отдает массив заголовков по склонениям по типу выборщика (квартиры / коммерция).
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_ESTATE_PLURAL(state) {
            return state.settings.estatePlural;
        },

        /**
         * Метод отдает данные формы.
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_FORM_DATA(state) {
            return state.formData;
        },

        /**
         * Метод отдает состояние готовности.
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_IS_READY(state) {
            return state.isReady;
        },

        /**
         * Метод отдает состояние загрузки.
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_IS_PROCESSING(state) {
            return state.isProcessing;
        },

        /**
         * Метод отдает данные пагинации
         * @param {Object} state - текущий state.
         * @returns {Object | false} - текущее значение параметра.
         */
        GET_PAGINATION(state) {
            return state.pagination;
        },

        /**
         * Метод возвращает режим пагинации
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_PAGINATION_MODE(state) {
            return state.paginationMode;
        },

        /**
         * Метод отдает текущую страницу результата выдачи.
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_PAGE(state) {
            return state.page;
        },

        /**
         * Метод отдает количество показываемых квартир / помещений и подгружаемых за 1 загрузку
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_PREMISES_TO_SHOW(state) {
            return state.settings.premisesToShow;
        },

        /**
         * Метод отдает текущий вид результата выдачи.
         * @param {Object} state - текущий state.
         * @returns {number} - текущее значение параметра.
         */
        GET_RESULT_VIEW(state) {
            return state.settings.view.type;
        },

        /**
         * Метод отдает состояние отображения переключения вида.
         * @param {Object} state - текущий state.
         * @returns {Boolean} - текущее значение параметра.
         */
        GET_RESULT_SWITCH(state) {
            return state.settings.view.switch;
        },

        /**
         * Метод отдает количество особенностей для группировки или отсутствие группировки.
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_GROUP_OPTIONS(state) {
            return Number(state.settings.groupOptions);
        },

        /**
         * Метод отдает текущий массив квартир / помещений.
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_PREMISES(state) {
            return state.premises;
        },

        /**
         * Метод отдает текущие значения фильтров.
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_FILTERS(state) {
            return state.filters;
        },

        /**
         * Метод отдает текущие параметры сортировки.
         * @param {Object} state - текущий state.
         * @returns {string} - текущее значение параметра.
         */
        GET_SORT_ITEMS(state) {
            return state.settings.sort.items;
        },

        /**
         * Метод отдает текущую сортировку.
         * @param {Object} state - текущий state.
         * @returns {Object} - текущее значение параметра.
         */
        GET_CURRENT_SORT(state) {
            return state.currentSort;
        },

        /**
         * Метод отдает настройку скрытия цен.
         * @param {Object} state - текущий state.
         * @returns {Object} - текущее значение параметра.
         */
        GET_HIDDEN_PRICE(state) {
            return state.settings.hidePrice;
        },

        /**
         * Метод отдает количество выбранных фильтров.
         * @param {Object} state - текущий state.
         * @returns {Object} - текущее значение параметра.
         */
        GET_FILTER_COUNT(state) {
            return state.filtersCount;
        },

        GET_IS_POPUP(state) {
            return state.settings.popup;
        },

        GET_IS_MOBILE(state) {
            return state.isMobile;
        }
    },

    /**
     * События, которые введут за собой изменение state.
     * https://vuex.vuejs.org/ru/guide/actions.html
     */
    actions: {
        /**
         * Событие происходит при инициализации приложения.
         * @param {Object} context - текущий контекст.
         * @param {Object} body - тело запроса.
         */
        init({commit, dispatch, getters}, body) {
            commit('SET_IS_PROCESSING', true);
            commit('SET_ESTATE_HEADING');
            commit('SET_ESTATE_PLURAL');
            commit('SET_FORM_DATA', body);

            axios.post(Vue.prototype.$appOptions.url || url, getters.GET_FORM_DATA)
                .then((response) => {
                    const data = response.data.data;

                    commit('SET_PAGINATION', data);
                    commit('SET_INIT_PAGE');
                    dispatch('initPaginationMode');
                    commit('SET_HIDDEN_PRICE', data);
                    commit('SET_DEFAULT_RESULT_VIEW', data);
                    commit('SET_RESULT_SWITCH', data);
                    commit('SET_PREMISES_TO_SHOW', data);
                    commit('SET_GROUP_OPTIONS', data);
                    commit('SET_SORT_ITEMS', data);
                    commit('SET_CURRENT_SORT', data);
                    commit('SET_IS_POPUP', data);
                    commit('SET_PREMISES', data);
                    if (!getters.GET_PAGINATION) {
                        commit('SET_PREMISES_SORTED');
                    }
                    commit('SET_FILTERS', data);
                    commit('SET_IS_READY');
                    commit('SET_IS_PROCESSING', false);
                })
                .then(() => {
                    dispatch('countFilters', body);
                })
                .catch((error) => {
                    console.error(`При инициализации параметрического поиска произошла ошибка: ${error}`);
                });
        },

        /**
         * Событие происходит при смене фильтра.
         * Запускает подсчет выбранных фильтров, отправку запроса
         * @param {Object} context - текущий контекст.
         * @param {Object} body - тело запроса.
         */
        changeFiltersHandler({commit, dispatch}, body) {
            commit('SET_FILTER_COUNT_STARTER');

            dispatch('countFilters', body);
            dispatch('changeFilter', body);
        },

        /**
         * Событие происходит при смене фильтра.
         * Подсчитывается количество выбранных фильтров.
         * @param {Object} context - текущий контекст.
         * @param {Object} body - тело запроса.
         */
        countFilters(context, body) {
            const form = body.form ?? document.querySelector('form');

            if (!form) {
                return;
            }

            const formElements = [...form.getElementsByTagName('input')];

            formElements.forEach((element) => {
                switch (element.type) {
                    // проверка чекбоксов, если чекнутый, то добавляем +1
                    case 'checkbox': {
                        if (element.checked) {
                            context.commit('SET_FILTER_COUNT');
                        }
                        break;
                    }

                    // если не чекбокс, то это слайдер (скрытый инпут со значениями), других типов нет
                    default: {
                        if (element.dataset.counter) {
                            // находим значение левой и правой границы и проверяем на соответствие min / max
                            const sliderFitLeft =
                                Number(element.dataset.minValue) > Number(element.dataset.min);
                            const sliderFitRight =
                                Number(element.dataset.maxValue) < element.dataset.max;

                            // считаем слайдер как выбранный, если хотя бы один параметр отличается от min / max
                            // если оба значения === min и max, то фильтра нет, во всех остальных случаях - есть
                            if (sliderFitLeft || sliderFitRight) {
                                context.commit('SET_FILTER_COUNT');
                            }
                        }

                        break;
                    }
                }
            });
        },

        /**
         * Событие происходит при смене фильтра.
         * @param {Object} context - текущий контекст.
         * @param {Object} body - тело запроса.
         */
        changeFilter({commit, getters}, body) {
            commit('SET_IS_PROCESSING', true);
            commit('SET_PAGE', 1);
            commit('SET_FORM_DATA', body);
            commit('SET_GET_PARAMS');

            axios.post(Vue.prototype.$appOptions.url || url, getters.GET_FORM_DATA)
                .then((response) => {
                    const data = response.data.data;

                    commit('SET_PAGINATION', data);
                    commit('SET_PREMISES', data);
                    if (!getters.GET_PAGINATION) {
                        commit('SET_PREMISES_SORTED');
                    }
                    commit('SET_FILTERS', data);
                    commit('SET_IS_PROCESSING', false);

                    if (body.type === 'reset') {
                        commit('RESET_SLIDERS');
                        commit('SET_FILTER_COUNT_STARTER');
                    }
                })
                .catch((error) => {
                    console.error(`При запросе данных произошла ошибка: ${error}`);
                });
        },

        /**
         * Запрос для данных конкретной страницы
         * @param {Object} context - текущий контекст.
         * @param {Object} body - тело запроса.
         */
        showPage({commit, dispatch}, body) {
            commit('SET_IS_PROCESSING', true);
            commit('SET_PAGE', body.page);
            commit('SET_GET_PARAMS');
            commit('SET_FORM_DATA', body);
            dispatch('sendPaginationRequest');
        },

        /**
         * Событие происходит при смене вида результата выдачи.
         * @param {Object} context - текущий контекст.
         * @param {Object} body - тело запроса.
         */
        changeView({commit}, body) {
            commit('SET_RESULT_VIEW', body.type);
        },

        /**
         * Событие происходит при пагинации по клику на "Показать ещё".
         * @param {Object} context - текущий контекст.
         */
        changePage({commit}) {
            commit('SET_NEXT_PAGE');
            commit('SET_GET_PARAMS');
        },

        /**
         * Событие происходит при смене типа или направления сортировки.
         * @param {Object} context - текущий контекст.
         * @param {Object} sortData - данные сортировки.
         */
        changeSort({commit, dispatch, state}, sortData) {
            if (state.pagination) {
                dispatch('changePaginationSort', sortData);
            } else {
                commit('SET_GET_PARAMS', sortData);
                commit('SET_CURRENT_SORT');
                commit('SET_PREMISES_SORTED');
            }
        },

        /**
         * Действие происходит при смене сортировки в параметрическом с пагинацией
         * @param {Object} context - текущий контекст.
         * @param {Object} sortData - объект с данными сортировки
         */
        changePaginationSort({commit, dispatch}, sortData) {
            commit('SET_IS_PROCESSING', true);
            commit('SET_PAGE', 1);
            commit('SET_GET_PARAMS', sortData);
            commit('SET_CURRENT_SORT');
            commit('SET_FORM_DATA', {
                type : 'sort',
                page : 1,
                sort : sortData.type,
                order: sortData.order
            });
            dispatch('sendPaginationRequest');
        },

        /**
         * Действие отсылает запрос за данными при наличии пагинации при переключении пагинации/сортировки
         * @param {Function} commit - функция вызова мутаций
         * @param {Function} getters - функция получения геттеров состояния
         */
        sendPaginationRequest({commit, getters}) {
            axios.post(Vue.prototype.$appOptions.url || url, getters.GET_FORM_DATA)
                .then((response) => {
                    const data = response.data.data;
                    const setterPremises = getters.GET_PAGINATION_MODE === 'next' ?
                        'SET_ADD_PREMISES' :
                        'SET_PREMISES';

                    commit(setterPremises, data);
                    commit('SET_PAGINATION', data);
                    commit('SET_IS_PROCESSING', false);
                })
                .catch((error) => {
                    console.error(`При запросе данных произошла ошибка: ${error}`);
                });
        },

        /**
         * Событие происходит при сбросе фильтра.
         * @param {Object} context - текущий контекст.
         * @param {Object} body - тело запроса.
         */
        resetFilter({dispatch}, body) {
            dispatch('changeFilter', body);
        },

        initPaginationMode({commit, getters}) {
            if (getters.GET_PAGE !== 1 &&
                (getters.GET_PAGINATION?.type === 'both' || getters.GET_PAGINATION?.type === 'page')) {
                commit('SET_PAGINATION_MODE', 'page');
            }
        },

        changePaginationMode({commit}, mode) {
            commit('SET_PAGINATION_MODE', mode);
        },

        setIsMobile({commit}, value) {
            commit('SET_IS_MOBILE', value);
        }
    },

    /**
     * Методы для изменения state.
     */
    mutations: {
        /**
         * Мутация устанавливает начальный вид результата выдачи (при наличии свойства и значения, иначе по дефолту).
         * @param {Object} state - текущий state.
         * @param {Object} data - данные от сервера.
         * @param {number} type - вид списка (таблицей = 1 (row) - default, карточкой - 2 (card)).
         */
        SET_DEFAULT_RESULT_VIEW(state, data, type = defaultResultView) {
            // проверяет, существует ли свойство
            // eslint-disable-next-line no-param-reassign
            type = Object.prototype.hasOwnProperty.call(data.settings, 'view') &&
                // проверяет, существует ли значение у свойства
                Boolean(data.settings.view.type) ?

                // проверяет переданное значение
                Number(data.settings.view.type) === 1 || Number(data.settings.view.type) === 2 ?
                    // если 1 или 2, то устанавливает его
                    Number(data.settings.view.type) :
                    // иначе по дефолту
                    defaultResultView :

                // иначе если нет свойства (или значения), то по дефолту
                type;

            state.settings.view.type = type;
        },

        /**
         * Мутация устанавливает заголовок по типу выборщика (квартиры / коммерция).
         * @param {Object} state - текущий state.
         */
        SET_ESTATE_HEADING(state) {
            state.settings.estateHeading = this._vm.$appOptions.title.toLowerCase();
        },

        /**
         * Мутация устанавливает массив заголовков по склонениям по типу выборщика (квартиры / коммерция).
         * @param {Object} state - текущий state.
         */
        SET_ESTATE_PLURAL(state) {
            state.settings.estatePlural = this._vm.$appOptions.plural ?
                JSON.parse(atob(this._vm.$appOptions.plural)) :
                ['помещение', 'помещения', 'помещений'];
        },

        /**
         * Мутация устанавливает данные формы.
         * @param {Object} state - текущий state.
         * @param {Object} body - тело запроса.
         */
        SET_FORM_DATA(state, {additionalData = {}, isGetParams = false, form, type}) {
            state.formData = form ? new FormData(form) : new FormData();
            state.formData.append('type', type || false);

            // если это первая загрузка (при ней нет формы), и не сброс фильтров
            if ((!form && type !== 'reset') || isGetParams) {
                if (window.location.search) {
                    const searchParams = new URLSearchParams(window.location.search);

                    for (const value of searchParams.entries()) {
                        state.formData.append(value[0], value[1]);
                    }
                }
            }

            for (const key in additionalData) {
                if (Object.hasOwn(additionalData, key)) {
                    state.formData.append(key, additionalData[key]);
                }
            }
        },

        /**
         * Мутация устанавливает GET-параметры в url.
         * Включает текущую форм-дату, и ключи, такие как: сортировка, номер страницы.
         * @param {Object} state - текущий state.
         * @param {Object} body - тело запроса.
         */
        SET_GET_PARAMS(state, body) {
            const query = new URLSearchParams();

            for (const item of state.formData.entries()) {
                query.append(item[0], item[1]);
            }

            // удаляем тип и гет-параметры раннее полученные из форм-даты
            query.delete('sort');
            query.delete('order');
            query.delete('page');
            query.delete('type');

            query.append('sort', body && body.type ? body.type : state.currentSort.type);
            query.append('order', body && body.order ? body.order : state.currentSort.order);
            if (state.page !== 1) {
                query.append('page', state.page);
            }

            window.history.pushState(null, null, `?${query.toString()}`);
        },

        /**
         * Мутация устанавливает ключ готовности - когда будут полностью отрисованы фильтры с данными.
         * @param {Object} state - текущий state.
         */
        SET_IS_READY(state) {
            state.isReady = true;
        },

        /**
         * Мутация устанавливает состояние загрузки.
         * @param {Object} state - текущий state.
         * @param {Boolean} value - значение состояния "загрузка".
         */
        SET_IS_PROCESSING(state, value) {
            state.isProcessing = value;
        },

        /**
         * Метод устанавливает данные пагинации.
         * @param {Object} state - текущий state.
         * @param {Object} data - данные ответа сервера
         */
        SET_PAGINATION(state, data) {
            state.pagination = data?.pagination ?? false;
        },

        /**
         * Мутация устанавливает начальную страницу результата выдачи.
         * @param {Object} state - текущий state.
         */
        SET_INIT_PAGE(state) {
            const get = window.location.search;
            const urlParams = new URLSearchParams(get);
            const page = urlParams.get('page');

            state.page = Number(page ? page : 1);
        },

        /**
         * Мутация устанавливает страницу результата выдачи.
         * @param {Object} state - текущий state.
         * @param {Number} page - номер страницы.
         */
        SET_PAGE(state, page) {
            state.page = page;
        },

        /**
         * Мутация устанавливает следующую страницу результата выдачи.
         * @param {Object} state - текущий state.
         */
        SET_NEXT_PAGE(state) {
            state.page = state.page + 1;
        },

        /**
         * Мутация обнуляет количество выбранных фильтров.
         * Обнуляет каждый раз перед запуском счета и при сбросе фильтра.
         * @param {Object} state - текущий state.
         */
        SET_FILTER_COUNT_STARTER(state) {
            state.filtersCount = 0;
        },

        /**
         * Мутация добавляет выбранный фильтр.
         * @param {Object} state - текущий state.
         */
        SET_FILTER_COUNT(state) {
            state.filtersCount = state.filtersCount + 1;
        },

        /**
         * Мутация устанавливает скрытие цен.
         * @param {Object} state - текущий state.
         * @param {Object} data - данные от сервера.
         */
        SET_HIDDEN_PRICE(state, data) {
            state.settings.hidePrice = data.settings.hidePrice;
        },

        /**
         * Мутация устанавливает количество показываемых квартир / помещений и подгружаемых за 1 загрузку.
         * (см. ResultComponent, MoreComponent).
         * @param {Object} state - текущий state.
         * @param {Object} data - данные от сервера.
         */
        SET_PREMISES_TO_SHOW(state, data) {
            state.settings.premisesToShow =
                Object.hasOwnProperty.call(data.settings, 'premisesToShow') && Number(data.settings.premisesToShow);
        },

        /**
         * Мутация устанавливает группировку особенностей для скрытия или отсутствие группировки.
         * @param {Object} state - текущий state.
         * @param {Object} data - данные от сервера.
         * @param {Boolean} group - группировка особенностей.
         */
        SET_GROUP_OPTIONS(state, data, group = false) {
            // проверяет, существует ли свойство
            // eslint-disable-next-line no-param-reassign
            group = Object.prototype.hasOwnProperty.call(data.settings, 'groupOptions') &&
            // проверяет, существует ли значение у свойства
            Boolean(data.settings.groupOptions) ?
                Number(data.settings.groupOptions) :
                group;

            state.settings.groupOptions = group;
        },

        /**
         * Мутация устанавливает текущий вид результата выдачи.
         * @param {Object} state - текущий state.
         * @param {Object} type - вид списка
         */
        SET_RESULT_VIEW(state, type) {
            state.settings.view.type = type;
        },

        /**
         * Мутация устанавливает состояние отображения переключения вида.
         * @param {Object} state - текущий state.
         * @param {Object} data - данные от сервера.
         */
        SET_RESULT_SWITCH(state, data) {
            state.settings.view.switch = data.settings.view.switch;
        },

        /**
         * Мутация устанавливает текущий массив квартир / помещений.
         * @param {Object} state - текущий state.
         * @param {Object} data - данные от сервера.
         */
        SET_PREMISES(state, data) {
            state.premises = [];
            state.premises = Array.from(Object.values(data.premises));
        },

        /**
         * Мутация добавляет новые данные в текущий массив квартир / помещений.
         * @param {Object} state - текущий state.
         * @param {Object} data - данные от сервера.
         */
        SET_ADD_PREMISES(state, data) {
            state.premises = state.premises.concat(Array.from(Object.values(data.premises)));
        },

        /**
         * Мутация устанавливает отсортированный массив квартир / помещений.
         * @param {Object} state - текущий state.
         * @returns {Object} объект отсортированных квартир / помещений по типу и направлению сортировки.
         */
        SET_PREMISES_SORTED(state) {
            const sortType = state.currentSort.type;
            const sortOrder = state.currentSort.order;

            return state.premises.sort((a, b) => {
                const valA = a.state.available ?
                    a[sortType] && Math.abs(a[sortType].value ? a[sortType].value : a[sortType]) :
                    null;

                const valB = b.state.available ?
                    b[sortType] && Math.abs(b[sortType].value ? b[sortType].value : b[sortType]) :
                    null;

                if (valA === null) {
                    return 1;
                } else if (valB === null) {
                    return -1;
                }

                return sortOrder === 'asc' ? valA - valB : valB - valA;
            });
        },

        /**
         * Мутация вызывает сброс активности слайдера.
         */
        RESET_SLIDERS() {
            observer.publish('resetSliders');
        },

        /**
         * Мутация устанавливает данные от бека для фильтров.
         * @param {Object} state - текущий state.
         * @param {Object} data - данные от сервера.
         */
        SET_FILTERS(state, data) {
            state.filters = data.filters;
        },

        /**
         * Мутация устанавливает данные от бека для типов сортировки.
         * @param {Object} state - текущий state.
         * @param {Object} data - данные от сервера.
         */
        SET_SORT_ITEMS(state, data) {
            state.settings.sort = data.settings.sort;
        },

        /**
         * Мутация устанавливает текущую сортировку: по дефолту или из get-параметров.
         * @param {Object} state - текущий state.
         */
        SET_CURRENT_SORT(state) {
            const get = window.location.search;
            const urlParams = new URLSearchParams(get);
            const sortType = urlParams.get('sort');
            const orderType = urlParams.get('order');

            state.currentSort.type = sortType ?
                sortType :
                state.settings.sort.default.type;

            state.currentSort.order = orderType ?
                orderType :
                state.settings.sort.default.order;
        },

        SET_IS_POPUP(state, data) {
            state.settings.popup = data.settings.popup;
        },

        /**
         * Мутация устанавливает вид пагинации
         * @param {Object} state - текущее состояние
         * @param {String} mode - режим пагинации
         */
        SET_PAGINATION_MODE(state, mode) {
            state.paginationMode = mode;
        },

        SET_IS_MOBILE(state, value) {
            state.isMobile = value;
        }
    }
});
