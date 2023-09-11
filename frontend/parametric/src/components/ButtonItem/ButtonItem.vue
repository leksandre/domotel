<template>
    <button :class="['parametric-button', modify]"
            :type="type"
            :disabled="disabled"
            @click="click">

        <component
            :is="iconBefore"
            :class-name="['parametric-button__icon parametric-button__icon_icon_before', iconModify]"/>

        <span class="parametric-button__text">{{ text }}</span>
        <span v-if="filtersCount > 0" class="parametric-button__counter">{{ filtersCount }}</span>

        <component
            :is="iconAfter"
            :class-name="['parametric-button__icon parametric-button__icon_icon_after', iconModify]"/>

    </button>
</template>

<script>
import IconArrowTop from '../Icons/TopArrowIcon';
import IconClose from '../Icons/CloseIcon';
import IconFilter from '../Icons/FilterIcon';
import IconPriceSort from '../Icons/PriceSort';

export default {
    name: 'ButtonItem',

    components: {
        IconArrowTop,
        IconClose,
        IconFilter,
        IconPriceSort
    },

    props: {
        iconAfter : String,
        iconBefore: String,
        iconModify: String,
        text      : String,
        type      : {
            type   : String,
            default: 'button'
        },
        modify      : String,
        filtersCount: Number,
        disabled    : Boolean
    },

    methods: {
        click() {
            this.$emit('click');
        }
    }
};
</script>

<style lang="scss">
    @import "ButtonItem";
</style>
