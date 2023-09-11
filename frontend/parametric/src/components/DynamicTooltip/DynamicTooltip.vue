<template>
    <transition name="fade">
        <div :class="['tooltip', `tooltip_theme_${direction}`,
        {'tooltip_action_hover': type === 'premisesOptions'},
        {'is-single': this.isSingleOption}]">
            <div class="tooltip__wrapper">

                <template v-if="type === 'premisesOptions'">
                    <ul class="list list_theme_regular list_theme_details">
                        <li v-for="(item, index) in items"
                            :key="index.id"
                            class="list__item">
                            {{ item.title }}
                        </li>
                    </ul>
                </template>

                <template v-if="type === 'sort'">
                    <ul class="list list_theme_column list_theme_regular list_theme_sort">
                        <template v-for="(item, key) in items">
                            <li v-for="(type, order) in item"
                                :key="type"
                                :class="['list__item', {'is-active': isCurrentType(type)}]">
                                <SortItem
                                    modify="parametric-button_theme_tooltip"
                                    :disabled="false"
                                    :text="type"
                                    :type="key"
                                    :order="order"
                                />
                                <span v-if="isCurrentType(type)">
                                    <ArrowCheckIcon
                                        class-name="icon_stroke_color icon_color_dark"
                                    />
                                </span>
                            </li>
                        </template>
                    </ul>
                </template>

            </div>
        </div>
    </transition>
</template>

<script>
import ArrowCheckIcon from '../Icons/ArrowCheckIcon';
import SortItem from '../SortComponent/SortItem';

export default {
    name      : 'DynamicTooltip',
    components: {
        ArrowCheckIcon,
        SortItem
    },

    props: ['id', 'items', 'type', 'currentType', 'direction', 'visible'],

    computed: {
        isSingleOption() {
            return this.type === 'premisesOptions' && this.items.length < 2;
        }
    },

    methods: {
        isCurrentType(type) {
            return type === this.currentType;
        }
    }
};
</script>

<style lang="scss">
@import "DynamicTooltip";
</style>
