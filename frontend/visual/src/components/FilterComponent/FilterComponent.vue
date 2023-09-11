<template>
    <div v-if="roomsFilter"
         class="visual-filter">

        <ButtonItem v-if="isTablet"
                    :class="['visual-filter__button']"
                    icon-after="FilterIcon"
                    ref="filterButton"
                    @click="showPanel"
        />

        <template v-if="!isTablet">
            <FilterForm/>
        </template>
    </div>
</template>

<script>
import ButtonItem from '../ButtonItem/ButtonItem';
import FilterForm from './FilterForm';
import {mapGetters} from 'vuex';

export default {
    name: 'FilterComponent',

    components: {
        ButtonItem,
        FilterForm
    },

    computed: {
        ...mapGetters({
            dataStep   : 'GET_DATA_STEP',
            isTablet   : 'GET_IS_TABLET',
            roomsFilter: 'GET_ROOMS_FILTER'
        })
    },

    methods: {
        showPanel() {
            this.$root.$emit('showPanel', {
                target   : this.$refs.filterButton.$el,
                component: 'FilterForm'
            });
        }
    }
};
</script>

<style lang="scss">
@import "FilterComponent";
</style>
