<template>
    <ButtonItem
        text="Фильтровать"
        :class="['parametric-button__filter-button', modify]"
        :filtersCount="filtersCount"
        icon-before="IconFilter"
        icon-modify="icon_stroke_color icon_color_white"
        @click="showPanel"
        ref="openFilterButton"
    />
</template>

<script>
import ButtonItem from '../ButtonItem/ButtonItem';
import Observer from '@/common/scripts/observer';

const observer = new Observer();

export default {
    name: 'OpenFilter',

    components: {
        ButtonItem
    },

    props: ['modify', 'filtersCount'],

    methods: {
        showPanel() {
            observer.publish('showPanel', {
                target   : this.$refs.openFilterButton.$el,
                component: 'FiltersComponent'
            });
        }
    }
};
</script>
