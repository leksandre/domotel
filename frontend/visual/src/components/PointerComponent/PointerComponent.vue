<template>
    <g class="visual-canvas__pointers">
        <template v-for="(pointer, index) in pointers">
            <foreignObject
                :key="index"
                :x="pointer.left"
                :y="pointer.top"
                :id="pointer.id"
                width="1"
                height="1"
                requiredExtensions="http://www.w3.org/1999/xhtml"
                :class="['visual-pointer',
                    `visual-pointer_theme_${pointer.type}`,
                    {'is-disabled': pointer.disabled},
                    {'is-booked': pointer.booked},
                    {'is-hovered': pointer.id === activeElement}]"
                @mouseover="mouseOverHandler($event, pointer)"
                @mousemove="mouseMoveHandler($event, pointer)"
                @mouseleave="mouseLeaveHandler(pointer)"
                @click="clickHandler(pointer)">

                <div xmlns="http://www.w3.org/1999/xhtml"
                     class="visual-pointer__item">
                    <div class="visual-pointer__info">
                        <FlatBookedIcon v-if="floorStep && pointer.disabled"
                            class-name="visual-pointer__booked-icon"
                        />
                        <template v-else-if="pointer.text">
                            {{ pointer.text }}
                        </template>
                    </div>
                </div>
            </foreignObject>
        </template>
    </g>
</template>

<script>
import FlatBookedIcon from '../Icons/FlatBookedIcon';
import {mapGetters} from 'vuex';

export default {
    name: 'PointerComponent',

    components: {
        FlatBookedIcon
    },

    props: {
        elements : Array,
        floorStep: Boolean,
        pointers : Array
    },

    data() {
        return {
            activeElement: null
        };
    },

    computed: {
        ...mapGetters({
            isMobile: 'GET_IS_MOBILE',
            isTouch : 'GET_IS_TOUCH'
        })
    },

    mounted() {
        this._subscribes();
    },

    methods: {
        _subscribes() {
            this.$root.$on('pointerHover', (id) => {
                id ? this._pointerHoverHandler(id) : this._pointerHoverHandler(id);
            });
        },

        _pointerHoverHandler(id = null) {
            this.activeElement = Number(id);
        },

        _mouseLeaveEvents() {
            this.activeElement = null;
            // Убираем подсветку маски этажа
            this.$root.$emit('maskHover');
            // Скрываем тултип
            this.$root.$emit('mouseLeaveTooltip');
        },

        mouseOverHandler(event, element) {
            if (typeof element.isShowTooltip !== 'undefined' && !element.isShowTooltip) {
                return;
            }

            this.activeElement = Number(element.id);
            const observerAction = this.isMobile && this.isTouch ? 'mouseOverPanel' : 'mouseOverTooltip';

            // Включаем подсветку маски этажа
            this.$root.$emit('maskHover', element.id);
            this.$root.$emit(observerAction, {
                id      : element.id,
                link    : element.link,
                event,
                elements: this.elements
            });
        },

        mouseMoveHandler(event, element) {
            if (typeof element.isShowTooltip !== 'undefined' && !element.isShowTooltip) {
                return;
            }

            if (!(this.isMobile && this.isTouch)) {
                this.$root.$emit('mouseMoveTooltip', event);
            }
        },

        mouseLeaveHandler(element) {
            if (typeof element.isShowTooltip !== 'undefined' && !element.isShowTooltip) {
                return;
            }

            if (!(this.isMobile && this.isTouch)) {
                this._mouseLeaveEvents();
            }
        },

        clickHandler(item) {
            this.$root.$emit('clickOnPointer', item);
        }
    }
};
</script>

<style lang="scss">
    @import "PointerComponent";
</style>
