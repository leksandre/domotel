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
            <template v-if="!isMobile">
                <FloorSwitcherComponent/>
            </template>
        </div>
        <footer class="visual__footer">
            <FilterComponent/>
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
import FloorSwitcherComponent from '../../components/FloorSwitcherComponent/FloorSwitcherComponent';
import RotateComponent from '../../components/RotateComponent/RotateComponent';
import store from '../../store';

export default {
    name: 'FloorPage',

    components: {
        BreadcrumbsComponent,
        CanvasComponent,
        CompassComponent,
        FilterComponent,
        FloorSwitcherComponent,
        RotateComponent
    },

    props: {
        floorStep   : Boolean,
        windowWidth : Number,
        windowHeight: Number
    },

    computed: {
        ...mapGetters({
            isMobile: 'GET_IS_MOBILE',
            isReady : 'GET_IS_READY'
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
            next();
        });
    },

    methods: {
        ...mapActions(['send'])
    }
};
</script>
