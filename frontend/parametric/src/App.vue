<template>
    <div>
        <Preloader v-if="!isReady"/>
        <Parametric
            :isReady="isReady"
            :isPanelOpen="isPanelOpen"
            :estateHeading="estateHeading"
            :estatePlural="estatePlural"
        />
    </div>
</template>

<script>
import {EBreakpoint, RESIZE_EVENTS} from '@/common/scripts/constants';
import {mapActions, mapGetters} from 'vuex';
import Observer from '@/common/scripts/observer';
import Parametric from './components/ParametricComponent/ParametricComponent';
import Preloader from './components/PreloaderComponent/PreloaderComponent';
import {Utils} from '@/common/scripts/utils';

const observer = new Observer();

export default {
    name: 'App',

    data: () => {
        return {
            windowWidth: Utils.getWindowWidth(),
            isPanelOpen: false
        };
    },

    components: {
        Parametric,
        Preloader
    },

    computed: {
        ...mapGetters({
            estateHeading: 'GET_ESTATE_HEADING',
            estatePlural : 'GET_ESTATE_PLURAL',
            isReady      : 'GET_IS_READY',
            isProcessing : 'GET_IS_PROCESSING'
        })
    },

    watch: {
        windowWidth() {
            this.setIsMobile(this.windowWidth < EBreakpoint.DESKTOP);
        }
    },

    created() {
        this.setIsMobile(this.windowWidth < EBreakpoint.DESKTOP);
    },

    mounted() {
        const body = {
            type: 'init'
        };

        this.init(body);
        this._bindEvents();
        this._subscribes();
    },

    beforeDestroy() {
        this._unbindEvents();
    },

    methods: {
        ...mapActions(['init', 'setIsMobile']),

        _bindEvents() {
            RESIZE_EVENTS.forEach((event) => {
                window.addEventListener(event, this._onResize.bind(this));
            });

            this.$root.$on('closePanel', () => {
                this.isPanelOpen = false;
            });
        },

        _unbindEvents() {
            RESIZE_EVENTS.forEach((event) => {
                window.removeEventListener(event, this._onResize, false);
            });
        },

        _subscribes() {
            observer.subscribe('showPanel', () => {
                this.isPanelOpen = true;
            });
        },

        _onResize() {
            this.windowWidth = Utils.getWindowWidth();
        }
    }
};
</script>
