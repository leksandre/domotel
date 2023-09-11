<template>
    <div class="parametric-sort">
        <div class="parametric-sort__heading"
             @click="handleSorting"
             v-view="viewHandler"
             ref="sortHeading">
            {{ this.currentSortType }}

            <IconPriceSort
                class-name="icon_stroke_color icon_color_dark"
                @click="handleSorting"
            />
        </div>
        <DynamicTooltip
            type="sort"
            direction="bottom"
            :items="this.sortItems"
            :currentType="this.currentSortType"
            v-show="tooltipOpen"
            ref="sortTooltip"/>
    </div>
</template>

<script>
import checkView from 'vue-check-view';
import DynamicTooltip from '../DynamicTooltip/DynamicTooltip';
import IconPriceSort from '../Icons/PriceSort';
import {mapGetters} from 'vuex';
import Observer from '@/common/scripts/observer';
import Vue from 'vue';

Vue.use(checkView);

const observer = new Observer();

export default {
    name: 'SortComponent',

    components: {
        DynamicTooltip,
        IconPriceSort
    },

    data() {
        return {
            tooltipOpen: false
        };
    },

    computed: {
        ...mapGetters({
            sortItems  : 'GET_SORT_ITEMS',
            currentSort: 'GET_CURRENT_SORT'
        }),

        currentSortType() {
            for (const key in this.sortItems) {
                if (Object.prototype.hasOwnProperty.call(this.sortItems, key)) {
                    if (key === this.currentSort.type) {
                        return this.sortItems[key][this.currentSort.order];
                    }
                }
            }

            return true;
        }
    },

    mounted() {
        this.bindEvents();
    },

    methods: {
        bindEvents() {
            document.addEventListener('click', this.onClickOutside);
        },

        viewHandler(event) {
            if (this.tooltipOpen && event.type === 'exit') {
                this.hideSorting();
            }
        },

        handleSorting() {
            this.tooltipOpen = !this.tooltipOpen;

            return this.tooltipOpen ?
                observer.publish('openSort') :
                observer.publish('closeSort');
        },

        hideSorting() {
            if (this.tooltipOpen) {
                this.tooltipOpen = false;
                observer.publish('closeSort');
            }
        },

        onClickOutside(event) {
            const {sortTooltip, sortHeading} = this.$refs;

            if (this.tooltipOpen && !sortHeading.contains(event.target)) {
                if (!sortTooltip || !sortTooltip.$el.contains(event.target)) {
                    this.hideSorting();
                }
            }
        },

        beforeDestroy() {
            document.removeEventListener('click', this.onClickOutside);
        }
    }
};
</script>

<style lang="scss">
@import "SortComponent";
</style>
