<template>
    <transition name="fade">
        <div v-if="visible"
             :style="[{'top': `${y}px`}, {'left': `${x}px`}]"
             :class="[
                 'visual-tooltip',
                 {'visual-tooltip_theme_floor': themeFloor},
                 {'visual-tooltip_theme_floor-switcher': this.fromFloorSwitcher},
                 {'visual-tooltip_width_auto': tooltip.disabled},
                 `is-${direction}`
             ]"
             ref="wrapper">

            <template v-if="!fromFloorSwitcher">
                <div class="visual-tooltip__header">
                    <template v-if="dataStep.step !== 'floor'">
                        <div v-if="tooltip.title"
                             class="visual-tooltip__title">
                            {{ tooltip.title }}
                        </div>
                        <div v-if="tooltip.deadline"
                             class="visual-tooltip__deadline">
                            {{ tooltip.deadline }}
                        </div>
                    </template>

                    <template v-else>
                        <div v-if="tooltip.title"
                             class="visual-tooltip__title">
                            {{ tooltip.title }}
                        </div>
                        <div v-if="tooltip.area"
                             class="visual-tooltip__title">
                            {{ formatArea(tooltip.area) }}&nbsp;м²
                        </div>
                    </template>
                </div>

                <template v-if="dataStep.step !== 'floor'">
                    <ul v-if="tooltip.params && tooltip.params.length"
                        class="visual-tooltip__params">
                        <li v-for="(item, index) in tooltip.params"
                            :key="index"
                            class="visual-tooltip__param">
                            <div v-if="item.title" class="visual-tooltip__param-flats">
                                {{ item.title }}
                            </div>
                            <div v-if="item.amount >= 0" class="visual-tooltip__param-amount">
                                {{ item.amount }}
                            </div>
                            <div v-if="item.area" class="visual-tooltip__value">
                                {{ formatArea(item.area) }}&nbsp;м²
                            </div>

                            <template v-if="hidePrice.status">
                                <div class="visual-tooltip__value">
                                    {{ hidePrice.text }}
                                </div>
                            </template>

                            <template v-else>
                                <div v-if="item.price"
                                     class="visual-tooltip__value">
                                    от&nbsp;{{ convertPriceRank(item.price) }}&nbsp;млн
                                </div>
                            </template>
                        </li>
                    </ul>
                </template>

                <template v-else>
                    <template v-if="hidePrice.status">
                        <div class="visual-tooltip__price">
                            {{ hidePrice.text }}
                        </div>
                    </template>

                    <template v-else-if="!tooltip.showPrice">
                        <div class="visual-tooltip__price">
                            {{ tooltip.state.additionalTitle || tooltip.state.title }}
                        </div>
                    </template>

                    <template v-else>
                        <div class="visual-tooltip__prices">
                            <div v-if="tooltip.price"
                                 :class="['visual-tooltip__price',
                                 {'action-price': tooltip.actionPrice}]">
                                {{ convertPriceDigit(tooltip.price) }}&nbsp;₽
                            </div>
                            <div v-if="tooltip.actionPrice"
                                 class="action-price__action-wrapper">
                                <div class="action-price__action">
                            <span class="action-price__action-value">
                                -{{ convertPriceDigit(tooltip.actionPrice.value) }}&nbsp;₽
                            </span>
                                </div>
                                <div class="action-price__basic-price">
                                    {{ convertPriceDigit(tooltip.actionPrice.base) }}&nbsp;₽
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
            </template>

            <template v-else>
                <div v-if="tooltip.amount > 1">
                    {{ tooltip.amount }} {{ estateType(tooltip.amount) }} в продаже
                </div>
                <div v-else>
                    Нет {{ estateType(0) }} в продаже
                </div>
            </template>
        </div>
    </transition>
</template>

<script>
import {mapGetters} from 'vuex';
import {Utils} from '@/common/scripts/utils';

export default {
    name: 'TooltipComponent',

    data() {
        return {
            visible          : false,
            tooltip          : false,
            direction        : false,
            fromFloorSwitcher: false,
            rootOffset       : 0,
            x                : 0,
            y                : 0
        };
    },

    computed: {
        ...mapGetters({
            dataStep    : 'GET_DATA_STEP',
            hidePrice   : 'GET_HIDDEN_PRICE',
            estatePlural: 'GET_ESTATE_PLURAL'
        }),

        themeFloor() {
            return this.dataStep.step === 'floor' && !this.fromFloorSwitcher;
        }
    },

    watch: {
        tooltip() {
            this.$nextTick(() => {
                this.wrapper = this.$refs.wrapper;

                this._checkBorders();
                this._setX();
                this._setY();
            });
        }
    },

    mounted() {
        this._getElementOffset();
        this._subscribes();
    },

    methods: {
        _subscribes() {
            this.$root.$on('mouseOverTooltip', (data) => {
                this._insert(data);
            });

            this.$root.$on('mouseOverFloorSwitcher', (data) => {
                this.fromFloorSwitcher = true;
                this._insert(data);
            });

            this.$root.$on('mouseMoveTooltip', (event) => {
                this._change(event);
            });

            this.$root.$on('mouseLeaveTooltip', () => {
                this._remove();
            });
        },

        _insert(data) {
            this.direction = data.direction || 'over';
            this.offset = 40;

            this._getMouseCoords(data.event);
            this._setData(data);

            this.visible = true;
        },

        _change(event) {
            this._getMouseCoords(event);
            this._checkBorders();
            this._setX();
            this._setY();
        },

        _remove() {
            this.fromFloorSwitcher = false;
            this.visible = false;
        },

        /**
         * Получаем оффсет блока
         */
        _getElementOffset() {
            this.rootOffset = this.$root?.$el?.offsetTop;
        },

        _getMouseCoords(event) {
            this._getElementOffset();

            this.mouseCoords = {
                X: event.pageX,
                Y: event.pageY
            };
        },

        _setData(data) {
            let currentData = {};

            if (!this.fromFloorSwitcher) {
                const targetId = Number(data.id);

                currentData = data.elements.find((element) => {
                    const id = Number(element.id);

                    return id === targetId ? element : false;
                });
            }

            this.tooltip = {
                type     : data.type || null,
                modify   : data.modify || null,
                amount   : data.amount || null,
                disabled : currentData.disabled || null,
                showPrice: data.showPrice || true,
                state    : data.state || null,
                ...currentData.tooltip || null
            };
        },

        _checkBorders() {
            const halfWidth = 2;

            let mouseCoord = 0;
            let tooltipCoord = 0;

            // Не влезает по верхнему краю.
            const top = () => {
                mouseCoord = this.mouseCoords.Y - this.offset;
                tooltipCoord = this.wrapper.offsetHeight + this.offset;

                return mouseCoord < tooltipCoord;
            };

            // Не влезает по правому краю.
            const right = () => {
                mouseCoord = window.innerWidth - this.mouseCoords.X;
                tooltipCoord = (this.wrapper.offsetWidth / halfWidth) + this.offset;

                return mouseCoord < tooltipCoord;
            };

            // Не влезает по левому краю.
            const left = () => {
                mouseCoord = this.mouseCoords.X + this.offset;
                tooltipCoord = (this.wrapper.offsetWidth / halfWidth) - this.offset;

                return mouseCoord < tooltipCoord;
            };

            if (top()) {
                this.direction = 'under';
            }

            if (right()) {
                this.direction = 'left';
            }

            if (left()) {
                this.direction = 'right';
            }
        },

        _setX() {
            const halfWidth = 2;

            switch (this.direction) {
                case 'over':
                case 'under':
                    this.x = this.mouseCoords.X - (this.wrapper.offsetWidth / halfWidth);
                    break;
                case 'left':
                    this.x = this.mouseCoords.X - this.wrapper.offsetWidth - this.offset;
                    break;
                case 'right':
                    this.x = this.mouseCoords.X + this.offset;
                    break;
                default:
                    this.x = this.mouseCoords.X - (this.wrapper.offsetWidth / halfWidth);
                    break;
            }
        },

        _setY() {
            const halfHeight = 2;

            switch (this.direction) {
                case 'over':
                    this.y = this.mouseCoords.Y - this.wrapper.offsetHeight - this.offset - this.rootOffset;
                    break;
                case 'under':
                    this.y = this.mouseCoords.Y + this.offset - this.rootOffset;
                    break;
                case 'left':
                case 'right':
                    this.y = this.mouseCoords.Y - (this.wrapper.offsetHeight / halfHeight) - this.rootOffset;
                    break;
                default:
                    this.y = this.mouseCoords.Y - this.wrapper.offsetHeight - this.offset;
                    break;
            }
        },

        formatArea(value) {
            return value.toString().replace('.', ',');
        },

        convertPriceRank(price) {
            if (Number(price)) {
                return Utils.convertToDigit(Utils.convertToRank(price, 1000000).toFixed(1));
            }

            return price;
        },

        convertPriceDigit(price) {
            if (Number(price)) {
                return Utils.convertToDigit(price);
            }

            return price;
        },

        estateType(count) {
            return Utils.pluralWord(count, this.estatePlural);
        }
    }
};
</script>

<style lang="scss">
@import 'TooltipComponent';
</style>
