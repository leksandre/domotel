<template>
    <div class="parametric-filter__wrapper">
        <div :class="['parametric-filter',
        {'parametric-filter_mobile_filter': isPanelOpen}]"
             ref="parametricFilter">
            <form :class="['parametric-filter__form']"
                  ref="form"
                  @change="changeFilters"
                  @reset.prevent="resetFilters"
            >
                <div v-if="filters.length" class="parametric-filter__content">

                    <div class="parametric-filter__container"
                         v-for="(filter, index) in filters"
                         :key="index">
                        <div class="parametric-filter__label">{{ filter.label }}</div>

                        <div class="parametric-filter__filter">
                            <template v-if="isCheckboxFilter(filter.type)">
                                <CheckboxItem v-for="(item, index) in filter.items"
                                              :key="index"
                                              :modify="setFilterModify(filter.view)"
                                              :name="`${filter.category}[${item.id}]`"
                                              :value="`${item.id}`"
                                              :title="item.title"
                                              :checked="item.checked"
                                              :disabled="item.disabled"
                                />
                            </template>

                            <template v-if="isSliderFilter(filter.type)">
                                <RangeSlider
                                    :slider="filter"
                                    :sliderType="filter.category"
                                    ref="slider"
                                    @change="changeFilters"
                                />
                            </template>

                            <template v-if="isToggleFilter(filter.type)">
                                <CheckboxItem
                                    :modify="setFilterModify(filter.view)"
                                    :statusFilter="true"
                                    :name="`${filter.category}`"
                                    :value="1"
                                    :title="filter.title"
                                    :checked="filter.checked"
                                    :disabled="filter.disabled"
                                />
                            </template>
                        </div>

                    </div>

                </div>
                <div class="parametric-filter__footer">
                    <div class="parametric-filter__reset">
                        <ButtonItem
                            type="reset"
                            text="Сбросить параметры"
                            class="parametric-filter__reset-button"
                        />
                    </div>
                </div>
            </form>
        </div>

        <div v-if="isMobile && isPanelOpen"
             class="parametric__filter-footer">
            <div class="parametric-button__wrapper">
                <ButtonItem
                    :text="resultButtonText"
                    :disabled="noResult"
                    modify="parametric-button__flats-button"
                    @click="closePanel"
                />
            </div>
        </div>

        <div v-if="!isMobile"
             class="parametric-button__wrapper">
            <ButtonItem
                text="Фильтровать"
                :class="['parametric-button__filter-button', 'parametric-button_scroll_button',
                    {'is-active': buttonUpIsVisible},
                    {'is-absolute': buttonUpIsStatic},
                    {'is-fixed': buttonUpIsFixed}]"
                icon-after="IconArrowTop"
                icon-modify="icon_fill_color icon_color_white"
                @click="scrollToFilter"
            />
        </div>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import ButtonItem from '../ButtonItem/ButtonItem';
import CheckboxItem from '../CheckboxItem/CheckboxItem';
import Observer from '@/common/scripts/observer';
import RangeSlider from '../RangeSlider/RangeSlider';
import ScrollAnimation from '@/components/scrollAnimation';
import {Utils} from '@/common/scripts/utils';

const observer = new Observer();

export default {
    name: 'FiltersComponent',

    components: {
        ButtonItem,
        CheckboxItem,
        RangeSlider
    },

    props: ['isPanelOpen', 'resultButtonText', 'noResult'],

    data() {
        return {
            buttonUpIsVisible: false,
            buttonUpIsStatic : false,
            buttonUpIsFixed  : false
        };
    },

    computed: {
        ...mapGetters({
            isMobile : 'GET_IS_MOBILE',
            isReady  : 'GET_IS_READY',
            filters  : 'GET_FILTERS',
            hidePrice: 'GET_HIDDEN_PRICE'
        })
    },

    mounted() {
        this._subscribes();
        this._bindEvents();
    },

    methods: {
        ...mapActions(['changeFiltersHandler', 'resetFilter']),

        _bindEvents() {
            window.addEventListener('scroll', Utils.debounce(() => {
                this.showFilterScrollButton();
            }, 10));

            this.$root.$on('nextPage', () => {
                this.buttonUpIsStatic = false;
                this.buttonUpIsFixed = true;
            });
        },

        _subscribes() {
            observer.subscribe('resetSliders', () => {
                this.resetSliders();
            });
        },

        isCheckboxFilter(type) {
            return type === 'checkbox';
        },

        isSliderFilter(type) {
            return type === 'slider';
        },

        isToggleFilter(type) {
            return type === 'toggle';
        },

        setFilterModify(category) {
            switch (category) {
                case 'block':
                    return 'checkbox_theme_block';
                case 'toggle':
                    return 'checkbox_theme_toggle';
                default:
                    return 'checkbox_theme_button';
            }
        },

        changeFilters() {
            const body = {
                type: 'filter',
                form: this.$refs.form
            };

            this.changeFiltersHandler(body);
        },

        resetFilters() {
            this.resetFilter({
                type: 'reset'
            });
            this.$root.$emit('scrollToStart');
        },

        resetSliders() {
            if (this.$refs.slider && this.$refs.slider.length) {
                this.$refs.slider.forEach((slider) => {
                    slider.reset();
                });
            }
        },

        showFilterScrollButton() {
            if (!this.$refs.parametricFilter) {
                return;
            }

            const currentScroll = window.scrollY + window.innerHeight;
            const parametricHeight = this.$parent.$el.offsetHeight + 100;

            this.buttonUpIsVisible = !Utils.isInViewport(this.$refs.parametricFilter);

            if (this.buttonUpIsVisible) {
                this.buttonUpIsStatic = false;
                this.buttonUpIsFixed = true;
            }

            if (this.buttonUpIsVisible && !(parametricHeight > currentScroll)) {
                this.buttonUpIsStatic = true;
                this.buttonUpIsFixed = false;
            }
        },

        scrollToFilter() {
            const scrollAnimation = new ScrollAnimation({});

            scrollAnimation.scroll();
        },

        closePanel() {
            this.$root.$emit('closePanel', true);
        }
    }
};
</script>

<style lang="scss">
@import "FiltersComponent";
</style>
