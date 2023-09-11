<template>
    <transition name="transform">
        <div v-if="visible" :class="['panel']"
             ref="panel">
            <div class="panel__header"
                 v-touch:swipe.bottom="closePanel">
                <ButtonItem
                    class="parametric-button__filter-close"
                    icon-after="IconClose"
                    icon-modify="icon_fill_color icon_color_dark"
                    @click="closePanel"
                />
            </div>

            <div ref="panelBody" class="panel__body">
                <div class="panel__content">
                    <!-- Вставка компонента -->
                        <keep-alive>
                            <component
                                :is="component"
                                :isPanelOpen="isPanelOpen"
                                :resultButtonText="resultButtonText"
                                :noResult="noResult">
                            </component>
                        </keep-alive>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
import ButtonItem from '../ButtonItem/ButtonItem';
import FiltersComponent from '../FiltersComponent/FiltersComponent';
import {mapGetters} from 'vuex';
import Observer from '@/common/scripts/observer';
import {Utils} from '@/common/scripts/utils';

const observer = new Observer();

export default {
    name: 'PanelComponent',

    components: {
        ButtonItem,
        FiltersComponent
    },

    props: {
        isPanelOpen     : Boolean,
        resultButtonText: String,
        noResult        : Boolean
    },

    data() {
        return {
            visible        : false,
            panel          : false,
            insertComponent: false,
            openPanelTarget: null,
            component      : null
        };
    },

    computed: {
        ...mapGetters({
            isMobile: 'GET_IS_MOBILE'
        })
    },

    watch: {
        dataStep() {
            if (!this.insertComponent) {
                this.closePanel();
            }
        },

        isMobile() {
            this.closePanel();
        },

        isPanelOpen() {
            this.$nextTick(() => {
                this.isPanelOpen ? Utils.bodyFixed(this.$refs.panelBody) : Utils.bodyStatic();
            });
        }
    },

    mounted() {
        this._bindEvents();
        this._subscribes();
    },

    beforeDestroy() {
        document.removeEventListener('click', this.onClickOutside);
    },

    methods: {
        _bindEvents() {
            document.addEventListener('click', this._onClickOutside);

            this.$root.$on('closePanel', () => {
                this.insertComponent = false;
                this._remove();
            });
        },

        _subscribes() {
            observer.subscribe('showPanel', (data) => {
                this._setComponent(data);
                this._insert();
            });
        },

        _insert() {
            this.visible = true;
        },

        _remove() {
            this.visible = false;
            this.panel = null;
        },

        _setComponent(data) {
            this.insertComponent = true;
            this.openPanelTarget = data.target;
            this.component = data.component;
        },

        _onClickOutside(event) {
            const {panel} = this.$refs;
            let target = null;

            if (this.panel) {
                target = this.panel.target.contains(event.target);
            } else if (this.openPanelTarget) {
                target = this.openPanelTarget.contains(event.target);
            }

            if (this.visible && !target) {
                if (!panel || !panel.contains(event.target)) {
                    this.closePanel();
                }
            }
        },

        closePanel() {
            this.insertComponent = false;
            this._remove();
            this.$root.$emit('closePanel', false);
        }
    }
};
</script>

<style lang="scss">
@import "PanelComponent";
</style>
