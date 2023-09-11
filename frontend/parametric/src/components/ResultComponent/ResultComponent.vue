<template>
    <div ref="result"
         class="parametric-result">
        <OpenFilter
            v-if="isMobile"
            modify="parametric-button_full-width_mobile"
            :filtersCount="filtersCount"
            ref="openFilter"
        />
        <ControlsComponent
            v-if="premises.length"
            :premises="premises"
            @updateAnimation="_updateAnimation"
        />
        <div class="parametric-result__content">
            <div v-if="visiblePremisesList.length"
                 class="parametric-result__result">
                <ResultList
                    :premises="visiblePremisesList"
                    @updateAnimation="_updateAnimation"
                />
            </div>
            <div v-if="!premises.length"
                 class="parametric-result__no-result">
                <EmptyResult
                    :estatePlural="estatePlural"
                />
            </div>
        </div>
        <div class="pagination__wrapper"
             v-if="pagination && pagination?.pages">
            <button v-if="(paginationType === 'both' || paginationType === 'next') && page !== pagination.pages"
                    class="button"
                    @click="_showMore(page + 1, 'next')">
                Показать ещё
            </button>
            <Pagination v-if="(paginationType === 'both' || paginationType === 'page')"
                        :form="filterFormElement"
                        @change="_showMore"/>
        </div>
        <More
            v-else-if="hiddenPremisesCount > 0"
            :hiddenPremisesCount="hiddenPremisesCount"
            :visiblePremisesCount="visiblePremisesCount"
            :estatePlural="estatePlural"
        />

        <OpenFilter v-if="isMobile && premises.length"
                    :class="['parametric-button_scroll_button',
                    {'is-active': buttonUpIsVisible},
                    {'is-relative': buttonUpIsStatic},
                    {'is-fixed': buttonUpIsFixed}]"
                    :filtersCount="filtersCount"
        />
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ControlsComponent from '../ControlsComponent/ControlsComponent';
import EmptyResult from '../EmptyResult/EmptyResult';
import More from '../MoreComponent/MoreComponent';
import OpenFilter from '../OpenFilter/OpenFilter';
import Pagination from '../PaginationComponent/PaginationComponent';
import {RESIZE_EVENTS} from '@/common/scripts/constants';
import ResultList from '../ResultList/ResultList';
import ScrollAnimation from '@/components/scrollAnimation';
import {Utils} from '@/common/scripts/utils';

export default {
    name: 'ResultComponent',

    components: {
        ControlsComponent,
        EmptyResult,
        More,
        OpenFilter,
        Pagination,
        ResultList
    },

    props: ['isFilterOpen', 'estateHeading', 'estatePlural'],

    data() {
        return {
            buttonUpIsVisible: false,
            buttonUpIsStatic : false,
            buttonUpIsFixed  : false,
            body             : document.querySelector('body'),
            isAnimation      : false,
            animEvent        : null,
            scrollAnimation  : new ScrollAnimation({}),
            headerOffset     : 0
        };
    },

    computed: {
        ...mapGetters({
            isMobile      : 'GET_IS_MOBILE',
            filtersCount  : 'GET_FILTER_COUNT',
            page          : 'GET_PAGE',
            pagination    : 'GET_PAGINATION',
            paginationMode: 'GET_PAGINATION_MODE',
            premises      : 'GET_PREMISES',
            premisesToShow: 'GET_PREMISES_TO_SHOW'
        }),

        /**
         * Устанавливает количество квартир / помещений, показываемых при инициализации.
         * @returns {string|number} количество квартир / помещений
         */
        visiblePremisesCount() {
            if (this.pagination) {
                return this.pagination.limit;
            }

            return this.premisesToShow ? this.premisesToShow : this.premises.length;
        },

        /**
         * Устанавливает количество показываемых квартир / помещений с учётом страницы
         * @returns {number} количество квартир
         */
        visiblePremises() {
            return this.paginationMode === 'page' ?
                this.visiblePremisesCount :
                this.page * this.visiblePremisesCount;
        },

        /**
         * Устанавливает количество скрытых квартир / помещений.
         * Разница между общим количеством и уже показанным.
         * @returns {string|number} количество скрытых квартир / помещений
         */
        hiddenPremisesCount() {
            return this.premises.length - this.visiblePremises;
        },

        /**
         * Устанавливает массив отображаемых квартир / помещений
         * @returns {boolean|*[]} Массив квартир / помещений
         */
        visiblePremisesList() {
            if (!this.premises.length) {
                return false;
            }

            return this.premises.filter((item, index) => {
                return index < this.visiblePremises;
            });
        },

        filterFormElement() {
            return this.$parent?.$refs?.filter?.$refs.form ?? null;
        },

        /**
         * Тип пагинации
         * @return {'page'|'next'|null} - тип
         */
        paginationType() {
            return this.pagination?.type ?? null;
        },

        /**
         * Возвращает элемент результатов
         * @return {HTMLDivElement} - элемент
         */
        resultContentBlock() {
            return this.$refs['result'];
        }
    },

    watch: {
        visiblePremisesList: {
            handler() {
                this._updateAnimation();
            },
            deep: true
        }
    },

    mounted() {
        this._bindEvents();
        this._setHeaderOffset();
        this.isAnimation = this.body.classList.contains('j-animation');

        if (this.isAnimation) {
            this.animEvent = new Event('updateAnimation', {
                bubbles   : true,
                cancelable: true,
                composed  : false
            });

            this._updateAnimation();
        }
    },

    methods: {
        ...mapActions(['changePage', 'changePaginationMode', 'showPage']),

        _bindEvents() {
            RESIZE_EVENTS.forEach((event) => {
                window.addEventListener(event, Utils.debounce(() => {
                    this._setHeaderOffset();
                }, 10));
            });

            window.addEventListener('scroll', Utils.debounce(() => {
                this._showFilterOpenButton();
            }, 10));

            this.$root.$on('closePanel', (isScroll) => {
                if (isScroll && this.isMobile && this.resultContentBlock) {
                    const top = window.pageYOffset + this.resultContentBlock.getBoundingClientRect().top;

                    this.scrollAnimation.scroll(top, this.headerOffset);
                }
            });

            this.$root.$on('nextPage', () => {
                this.buttonUpIsStatic = false;
                this.buttonUpIsFixed = true;
                this.changePage();
            });

            this.$root.$on('scrollToStart', () => {
                this._scrollToStart();
            });
        },

        _setHeaderOffset() {
            this.headerOffset = parseFloat(document.documentElement.style
                .getPropertyValue('--header-height')) || 0;
        },

        _showFilterOpenButton() {
            if (!this.$refs.openFilter) {
                return;
            }

            const currentScroll = window.scrollY + window.innerHeight;
            const parametricHeight = this.$parent.$el.offsetHeight + 100;

            this.buttonUpIsVisible = !Utils.isInViewport(this.$refs.openFilter.$el);

            if (this.buttonUpIsVisible) {
                this.buttonUpIsStatic = false;
                this.buttonUpIsFixed = true;
            }

            if (this.buttonUpIsVisible && !(parametricHeight > currentScroll)) {
                this.buttonUpIsStatic = true;
                this.buttonUpIsFixed = false;
            }
        },

        _updateAnimation() {
            if (!this.isAnimation) {
                return;
            }

            this.$nextTick(() => {
                this.body.dispatchEvent(this.animEvent);
            });
        },

        _showMore(page, mode) {
            const body = {
                type: 'pagination',
                ...this.filterFormElement && {
                    form: this.filterFormElement
                },
                isGetParams: true,
                page
            };

            if (this.paginationMode !== mode) {
                this.changePaginationMode(mode);
            }
            this.showPage(body);
            if (mode === 'page') {
                this._scrollToStart();
            }
        },

        _scrollToStart() {
            this.$nextTick(() => {
                if (this.resultContentBlock?.getBoundingClientRect().top < this.headerOffset) {
                    window.scrollTo({
                        top: this.resultContentBlock.getBoundingClientRect().top +
                            window.pageYOffset - this.headerOffset,
                        left    : 0,
                        behavior: 'smooth'
                    });
                }
            });
        }
    }
};
</script>

<style lang="scss">
    @import "ResultComponent";
</style>
