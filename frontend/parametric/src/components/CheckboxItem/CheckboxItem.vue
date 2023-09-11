<template>
    <div :class="['checkbox', modify]">
        <input
            type="checkbox"
            :id="name"
            :name="name"
            :checked="checked"
            :disabled="disabled"
            :value="value"
            class="checkbox__input"
        >
        <label class="checkbox__label" :for="name">
            <span class="checkbox__text">{{ title }}</span>
            <span v-if="statusFilter" class="checkbox__toggle"></span>
        </label>
    </div>
</template>

<script>
export default {
    name: 'CheckboxItem',

    props: ['modify', 'name', 'title', 'value', 'checked', 'disabled', 'statusFilter']
};
</script>

<style lang="scss">
@import 'CheckboxItem';
</style>

