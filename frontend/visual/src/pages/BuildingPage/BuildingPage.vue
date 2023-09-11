<template>
    <div :class="['visual__wrapper', {'is-show': isReady}]">
        <header class="visual__header">
            <BreadcrumbsComponent :floor-step="floorStep"/>
            <CompassComponent/>
        </header>
        <div class="visual__canvas">
            <CanvasComponent
                :floor-step="floorStep"
                :window-width="windowWidth"
                :window-height="windowHeight"
                ref="canvas"
            />
        </div>
        <footer class="visual__footer">
            <FilterComponent :key="filterKey"/>
        </footer>
        <RotateComponent/>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import BreadcrumbsComponent from '../../components/BreadcrumbsComponent/BreadcrumbsComponent';
import CanvasComponent from '../../components/CanvasComponent/CanvasComponent';
import CompassComponent from '../../components/CompassComponent/CompassComponent';
import FilterComponent from '../../components/FilterComponent/FilterComponent';
import RotateComponent from '../../components/RotateComponent/RotateComponent';
import store from '../../store';

export default {
    name: 'BuildingPage',

    components: {
        BreadcrumbsComponent,
        CanvasComponent,
        CompassComponent,
        FilterComponent,
        RotateComponent
    },

    props: {
        floorStep   : Boolean,
        windowWidth : Number,
        windowHeight: Number
    },

    data() {
        return {
            filterKey: 1
        };
    },

    computed: {
        ...mapGetters({
            isReady: 'GET_IS_READY'
        })
    },

    /**
     * @param {Object} to - параметр
     * @param {Object} from - параметр
     * @param {Object} next - параметр
     * Отправляет данные на сервер.
     */
    beforeRouteEnter(to, from, next) {
        const get = window.location.search;
        const urlParams = new URLSearchParams(get);

        const body = {
            step      : to.meta.step,
            iframeView: Boolean(urlParams.get('iframe')),
            ...to.params.buildingId && {
                buildingId: to.params.buildingId
            },
            ...to.params.sectionId && {
                sectionId: to.params.sectionId
            },
            ...to.params
        };

        store.dispatch('send', {
            body,
            query: to.query
        }).then(() => {
            next((vm) => {
                vm.filterKey++;
            });
        });
    },

    methods: {
        ...mapActions(['send'])
    }
};
</script>
