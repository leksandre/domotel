<template>
    <div :class="['checkbox', modify]">
        <input
            :id="name"
            :name="name"
            :checked="checked"
            :disabled="disabled"
            :value="value"
            type="checkbox"
            class="checkbox__input"
        >
        <label class="checkbox__label" :for="name">
            <span v-if="color" class="checkbox__legend" :style="{backgroundColor: color}"></span>
            <span v-if="isTablet" class="checkbox__text">{{ title.full }}</span>
            <span v-else class="checkbox__text">{{ title.short }}</span>
        </label>
    </div>
</template>


<script>
import {mapGetters} from 'vuex';

export default {
    name: 'CheckboxItem',

    props: ['modify', 'name', 'title', 'value', 'checked', 'color', 'disabled'],

    computed: {
        ...mapGetters({
            isTablet: 'GET_IS_TABLET'
        })
    }
};
</script>

<style lang="scss">
@import 'CheckboxItem.scss';
</style>
