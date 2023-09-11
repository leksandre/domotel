<template>
    <div :class="['visual',
        {'visual_frame_grid': gridFrame},
        {'visual_theme_floor': floorStep},
        {'is-overlay': isPanelOpen}]">
        <PanelComponent />
        <transition name="fade">
            <PinchzoomComponent v-if="isPinchVisible"/>
        </transition>
        <PreloaderComponent v-if="isProcessing"/>
        <RouterView
            :floor-step="floorStep"
            :window-width="windowWidth"
            :window-height="windowHeight"
        />
        <TooltipComponent/>
    </div>
</template>

<script lang="ts">
import {EBreakpoint, RESIZE_EVENTS} from '@/common/scripts/constants';
import {mapActions, mapGetters} from 'vuex';
import PanelComponent from './components/PanelComponent/PanelComponent.vue';
import PinchzoomComponent from './components/PinchzoomComponent/PinchzoomComponent.vue';
import PreloaderComponent from './components/PreloaderComponent/PreloaderComponent.vue';
import TooltipComponent from './components/TooltipComponent/TooltipComponent.vue';
import {Utils} from '@/common/scripts/utils';

export default {
    name: 'App',

    components: {
        PanelComponent,
        PinchzoomComponent,
        PreloaderComponent,
        TooltipComponent
    },

    data() {
        return {
            windowWidth : Utils.getBodyWidth(),
            windowHeight: Utils.getWindowHeight(),
            isPanelOpen : false
        };
    },

    computed: {
        ...mapGetters({
            isMobile    : 'GET_IS_MOBILE',
            isPinch     : 'GET_IS_PINCH',
            isReady     : 'GET_IS_READY',
            dataStep    : 'GET_DATA_STEP',
            isProcessing: 'GET_IS_PROCESSING',
            floorStep   : 'GET_FLOOR_STEP',
            gridFrame   : 'GET_GRID_FRAME'
        }),

        isPinchVisible(): boolean {
            return this.isPinch && this.isMobile;
        }
    },

    watch: {
        windowWidth() {
            this.setIsMobile(this.windowWidth < EBreakpoint.DESKTOP);
            this.setIsTablet(this.windowWidth < EBreakpoint.TABLET);
        }
    },

    created() {
        this.setIsMobile(this.windowWidth < EBreakpoint.DESKTOP);
        this.setIsTablet(this.windowWidth < EBreakpoint.TABLET);
        this.setStartSettings();
    },

    mounted() {
        this._bindEvents();
        this._subscribes();
    },

    beforeDestroy() {
        this._unbindEvents();
    },

    methods: {
        ...mapActions(['setStartSettings', 'setIsMobile', 'setIsTablet']),

        _bindEvents(): void {
            RESIZE_EVENTS.forEach((event: string) => {
                window.addEventListener(event, this._onResize.bind(this));
            });
        },

        _unbindEvents(): void {
            RESIZE_EVENTS.forEach((event: string) => {
                window.removeEventListener(event, this._onResize, false);
            });
        },

        _subscribes(): void {
            this.$root.$on('mouseOverPanel', () => {
                this.isPanelOpen = true;
            });

            this.$root.$on('showPanel', () => {
                this.isPanelOpen = true;
            });

            this.$root.$on('closePanel', () => {
                this.isPanelOpen = false;
            });
        },

        _onResize(): void {
            this.windowWidth = Utils.getBodyWidth();
            this.windowHeight = Utils.getWindowHeight();
        }
    }
};
</script>

<style lang="scss">
    @import "App";
</style>
