<template>
    <div v-if="breadcrumbs.length"
         class="visual-breadcrumbs">
        <div class="visual-breadcrumbs__container">
            <router-link v-if="breadcrumbs.length > 1"
                         :to="{path: breadcrumbsBackLink, query: $route.query}"
                         class="visual-breadcrumbs__back">
                <ArrowIconLeft
                    class-name="visual-breadcrumbs__back-icon"
                />
            </router-link>

            <ul v-if="!isMobile" class="visual-breadcrumbs__list">
                <li v-for="(item, index) in breadcrumbsSteps"
                    :key="index"
                    class="visual-breadcrumbs__item">
                    <div class="visual-breadcrumbs__step">
                        {{item.name}}
                    </div>
                </li>
            </ul>

            <div v-if="isMobile && !floorStep" class="visual-breadcrumbs__step">
                {{ breadcrumbsCurrent.name }}
            </div>

            <template v-if="isMobile && floorStep && currentFloor">
                <div class="visual-button visual-breadcrumbs__floor-switcher"
                     ref="floorSwitcherBreadcrumbs"
                     @click="showPanel">
                    <div class="visual-filter__floor-switcher-button">
                        {{ `Этаж ${currentFloor.title}` }}
                    </div>
                    <div class="visual-breadcrumbs__floor-switcher-arrows">
                        <FloorChangeIcon />
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import ArrowIconLeft from '../Icons/ArrowIconLeft';
import FloorChangeIcon from '../Icons/FloorChangeIcon';
import {mapGetters} from 'vuex';

export default {
    name: 'BreadcrumbsComponent',

    components: {
        ArrowIconLeft,
        FloorChangeIcon
    },

    props: {
        floorStep: Boolean
    },

    data() {
        return {
            floors      : null,
            currentFloor: null
        };
    },

    computed: {
        ...mapGetters({
            breadcrumbs : 'GET_BREADCRUMBS',
            dataStep    : 'GET_DATA_STEP',
            floorsFilter: 'GET_FLOORS_FILTER',
            filtersCount: 'GET_FLOORS_FILTER_COUNT',
            isMobile    : 'GET_IS_MOBILE'
        }),

        breadcrumbsBackLink() {
            return this.breadcrumbs[this.breadcrumbs.length - 2].link;
        },

        breadcrumbsSteps() {
            return this.breadcrumbs.length > 1 ? this.breadcrumbs.slice(1) : this.breadcrumbs;
        },

        breadcrumbsCurrent() {
            return this.breadcrumbs[this.breadcrumbs.length - 1];
        }
    },

    watch: {
        filtersCount() {
            this._setFloors();
            this._setCurrentFloor();
        }
    },

    mounted() {
        this._setFloors();
        this._setCurrentFloor();
    },

    methods: {
        _setFloors() {
            this.floors = this.floorsFilter && this.floorsFilter.items ? Object.values(this.floorsFilter.items) : [];
        },

        _setCurrentFloor() {
            if (!this.floors) {
                return;
            }

            this.currentFloor = this.floors.find((item) => {
                return item.active;
            });
        },

        showPanel() {
            this.$root.$emit('showPanel', {
                target   : this.$refs.floorSwitcherBreadcrumbs,
                component: 'FloorSwitcherComponent'
            });
        }
    }
};
</script>

<style lang="scss">
@import 'BreadcrumbsComponent';
</style>
