<template>
    <div v-if="floors && floors.length > 1"
         class="visual-floors-switcher">
        <template v-if="isMobile">
            <div class="visual-floors-switcher__panel">
                <div class="visual-floors-switcher__panel-title">Этажи</div>
                <div class="visual-floors-switcher__panel-amount">{{ estateType }} в продаже</div>
            </div>
        </template>

        <div :class="['visual-floors-switcher__container',
                     {'is-short': !isMobile && floors.length < visibleItemsCount}]">
            <button v-if="!isMobile && floors.length > visibleItemsCount"
                    :class="['visual-floors-switcher__prev',
                            {'is-disabled': !floorSwitcher || floorSwitcher.isBeginning}]"
                    @click="_goPrev">
                <ArrowIconUp class-name="visual-floors-switcher__svg"/>
            </button>

            <Swiper direction="vertical"
                    :slides-per-view="floors.length >= visibleItemsCount ? visibleItemsCount : floors.length"
                    :slides-per-group="floors.length >= visibleItemsCount ? visibleItemsCount : floors.length"
                    :auto-height="true"
                    :space-between="0"
                    :height="wrapperHeight"
                    :style= "[wrapperHeight ? {'height': `${wrapperHeight}px`} : '']"
                    class="visual-floors-switcher__wrap"
                    @swiper="_initSwiper">
                <swiper-slide v-for="(floor, index) in floors"
                              :key="floor.id"
                              :class="['visual-floors-switcher__item',
                                  {'is-disabled':floor.disabled},
                                  {'is-active': currentFloorId === Number(floor.id)}]">
                    <button :ref="`floorSwitcherButton${index}`"
                            :class="['visual-floors-switcher__button',
                                    {'is-active': currentFloorId === Number(floor.id)}]"
                            @mouseover="_mouseOverHandler(floor.amount, $event)"
                            @mousemove="_mouseMoveHandler"
                            @mouseout="_mouseLeaveHandler"
                            @click="_setFloor(floor.id)">
                        <template v-if="isMobile">
                            <span>
                                {{ floor.title }}&nbsp;этаж
                            </span>
                            <span class="visual-floors-switcher__button-amount">
                                {{ floor.amount }}
                            </span>
                        </template>
                        <template v-else>
                            {{ floor.title }}
                        </template>
                    </button>
                </swiper-slide>
            </Swiper>

            <button v-if="!isMobile && floors.length > visibleItemsCount"
                    :class="['visual-floors-switcher__next', {'is-disabled': !floorSwitcher || floorSwitcher.isEnd }]"
                    @click="_goNext">
                <ArrowIconDown class-name="visual-floors-switcher__svg"/>
            </button>
        </div>

    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import {Swiper, SwiperSlide} from 'swiper-vue2';
import ArrowIconDown from '../Icons/ArrowIconDown';
import ArrowIconUp from '../Icons/ArrowIconUp';
import {RESIZE_EVENTS} from '@/common/scripts/constants';
import {Utils} from '@/common/scripts/utils';

export default {
    name: 'FloorSwitcherComponent',

    components: {
        ArrowIconDown,
        ArrowIconUp,
        Swiper,
        SwiperSlide
    },

    data() {
        return {
            floorSwitcher    : null,
            visibleItemsCount: 7,
            wrapperHeight    : 0,
            onResize         : null
        };
    },

    computed: {
        ...mapGetters({
            floorsFilter: 'GET_FLOORS_FILTER',
            estatePlural: 'GET_ESTATE_PLURAL',
            isMobile    : 'GET_IS_MOBILE'
        }),

        floors() {
            return this.floorsFilter.items ?
                this.floorsFilter && Object.values(this.floorsFilter.items).reverse() :
                false;
        },

        currentFloorId() {
            return Number(this.$route.params.floorId);
        },

        currentFloor() {
            return this.floors.find((item) => {
                return Number(item.id) === Number(this.currentFloorId);
            });
        },

        estateType() {
            return Utils.pluralWord(3, this.estatePlural);
        },

        currentFloorIndex() {
            const index = this.floors.indexOf(this.currentFloor);

            return index < 0 ? 0 : index;
        }
    },

    watch: {
        currentFloorIndex() {
            if (!this.floorSwitcher) {
                return;
            }

            this.floorSwitcher.slideTo(this.currentFloorIndex);
        },

        floors: {
            deep: true,
            handler() {
                this._calcWrapperSize();
            }
        }
    },

    mounted() {
        this.onResize = Utils.debounce(() => {
            this._calcWrapperSize();
        }, 100);

        RESIZE_EVENTS.forEach((event) => {
            window.addEventListener(event, this.onResize);
        });

        this._calcWrapperSize();
    },

    beforeDestroy() {
        RESIZE_EVENTS.forEach((event) => {
            window.removeEventListener(event, this.onResize, false);
        });
    },

    methods: {
        ...mapActions(['send']),

        _initSwiper(swiper) {
            this.floorSwitcher = swiper;
            this.floorSwitcher.slideTo(this.currentFloorIndex);
        },

        _goPrev() {
            this.floorSwitcher.slidePrev();
        },

        _goNext() {
            this.floorSwitcher.slideNext();
        },

        _calcWrapperSize() {
            const floorButton = this.$refs.floorSwitcherButton0 && this.$refs?.floorSwitcherButton0[0];

            if (!floorButton) {
                return;
            }

            const height = floorButton.offsetHeight;
            const count = this.floors.length > this.visibleItemsCount ? this.visibleItemsCount : this.floors.length;

            this.wrapperHeight = height * count;
        },

        _setFloor(floorId) {
            if (Number(floorId) === this.currentFloorId) {
                return;
            }

            const url = this.$route.path.replace(/floor[/]\d*/, `floor/${floorId}`);
            const body = {
                step: this.$route.meta.step,
                ...this.$route.params.buildingId && {
                    buildingId: this.$route.params.buildingId
                },
                ...this.$route.params.sectionId && {
                    sectionId: this.$route.params.sectionId
                },
                floorId,
                ...this.$dataset
            };

            this.$router.push({
                path : url,
                query: this.$route.query
            });

            this.send({body});
        },

        _mouseOverHandler(amount, event) {
            if (this.isMobile) {
                return;
            }

            this.$root.$emit('mouseOverFloorSwitcher', {
                amount,
                event,
                direction: 'left'
            });
        },

        _mouseMoveHandler(event) {
            if (!this.isMobile) {
                this.$root.$emit('mouseMoveTooltip', event);
            }
        },

        _mouseLeaveHandler() {
            if (this.isMobile) {
                return;
            }

            this.$root.$emit('mouseLeaveTooltip');
        }
    }
};
</script>

<style lang="scss">
@import "FloorSwitcherComponent";
</style>
