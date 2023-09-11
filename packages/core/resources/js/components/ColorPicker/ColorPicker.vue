<template>
    <color-picker
        v-model="colour"
        :id="id"
        :name="name"
        :label="label"
        :value="colour"
        :color="colour"
        :color-default="colorDefault"
        picker="chrome" />
</template>

<script>
import ColorPickerTemplate from './ColorPicerTemplate.vue';

export default {
    name: 'ColorPickerItem',

    components: {
        'color-picker': ColorPickerTemplate
    },

    props: {
        id: String,
        name: String,
        color: String,
        label: String,
        colorDefault: {
            type: String,
            default: '#000000'
        }
    },

    data() {
        return {
            colour: '#000000'
        }
    },

    created() {
        this.colour = this.color;
    }
};
</script>
