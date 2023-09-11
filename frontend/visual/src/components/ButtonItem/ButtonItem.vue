<template>
    <button :class="['visual-button', modify]"
            :type="type"
            @click="click">

        <component
            :is="iconBefore"
            :class-name="['visual-button__icon visual-button__icon_icon_before', iconModify]"/>

        <span class="visual-button__text" v-html="text"/>

        <component
            :is="iconAfter"
            :class-name="['visual-button__icon visual-button__icon_icon_after', iconModify]"/>

    </button>
</template>

<script>
import CloseIcon from '../Icons/CloseIcon';
import FilterIcon from '../Icons/FilterIcon';

export default {
    name: 'ButtonItem',

    components: {
        CloseIcon,
        FilterIcon
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
        modify: String
    },

    methods: {
        click() {
            this.$emit('click');
        }
    }
};
</script>

<style lang="scss">
@import 'ButtonItem';
</style>
