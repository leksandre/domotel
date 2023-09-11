<template>
    <transition name="transform">
        <div v-if="visible" :class="['panel', {'panel_theme_floor': dataStep.step === 'floor'}]"
             ref="panel">
            <div class="panel__header"
                 v-touch:swipe.bottom="closePanel">
                <ButtonItem
                    :class="['visual-button__close']"
                    icon-after="CloseIcon"
                    icon-modify="visual-button__close-icon"
                    @click="closePanel"
                />
            </div>

            <template>
                <div class="panel__body">
                    <div class="panel__content">

                        <!-- Вывод данных с масок -->
                        <template v-if="!insertComponent">
                            <div v-if="panel.render"
                                 class="panel__render-wrapper">
                                <div class="panel__render"
                                     @click="toNextStep(panel)">
                                    <img class="panel__render-image" :src="panel.render" alt="corpus">
                                </div>
                            </div>

                            <div class="panel__info">
                                <div class="panel__info-header">
                                    <template v-if="dataStep.step !== 'floor'">
                                        <div v-if="panel.title"
                                             class="panel__title"
                                             @click="toNextStep(panel)">
                                            {{ panel.title }}
                                        </div>
                                        <div v-if="panel.deadline"
                                             class="panel__deadline">
                                            {{ panel.deadline }}
                                        </div>
                                    </template>

                                    <template v-else>
                                        <div v-if="panel.title"
                                             class="panel__title"
                                             @click="toNextStep(panel)">
                                            {{ panel.title }}
                                        </div>
                                        <div v-if="panel.area"
                                             class="panel__title">
                                            {{ formatArea(panel.area) }}&nbsp;м²
                                        </div>
                                    </template>
                                </div>

                                <template v-if="dataStep.step !== 'floor'">
                                    <ul class="panel__params">
                                        <li v-for="(item, index) in panel.params"
                                            :key="index"
                                            class="panel__param">
                                            <div v-if="item.title" class="panel__param-flats">
                                                {{ item.title }}
                                            </div>
                                            <div v-if="item.amount >= 0" class="panel__param-amount">
                                                {{ item.amount }}
                                            </div>

                                            <div v-if="item.area" class="panel__value">
                                                {{ formatArea(panel.area) }}&nbsp;м²
                                            </div>

                                            <template v-if="hidePrice.status">
                                                <div class="panel__value">
                                                    {{ hidePrice.text }}
                                                </div>
                                            </template>

                                            <template v-else>
                                                <div class="panel__value">
                                                    от&nbsp;{{ convertPriceRank(item.price) }}&nbsp;млн
                                                </div>
                                            </template>
                                        </li>
                                    </ul>
                                </template>

                                <template v-else-if="panel.block || panel.section || panel.floor">
                                    <div class="panel__params">
                                        <div v-if="panel.block"
                                             class="panel__param">
                                            Корпус {{ panel.block }}
                                        </div>
                                        <div v-if="panel.section"
                                             class="panel__param">
                                            Секция {{ panel.section }}
                                        </div>
                                        <div v-if="panel.floor"
                                             class="panel__param">
                                            <span v-if="panel.floor.value">Этаж&nbsp;{{ panel.floor.value }}</span>
                                            <span v-if="panel.floor.total">&nbsp;из&nbsp;{{ panel.floor.total }}</span>
                                        </div>
                                    </div>

                                    <div class="panel__prices">
                                        <template v-if="hidePrice.status">
                                            <div class="panel__price">
                                                {{ hidePrice.text }}
                                            </div>
                                        </template>

                                        <template v-else-if="!panel.showPrice">
                                            <div class="panel__price">
                                                {{ panel.state.additionalTitle || panel.state.title }}
                                            </div>
                                        </template>

                                        <template v-else>
                                            <div v-if="panel.price"
                                                 :class="['panel__price',
                                                     {'action-price': panel.actionPrice}]">
                                                {{ convertPriceDigit(panel.price) }}&nbsp;₽
                                            </div>

                                            <div v-if="panel.actionPrice"
                                                 class="action-price__action-wrapper">
                                                <div class="action-price__action">
                                                <span class="action-price__action-value">
                                                    -{{ convertPriceDigit(panel.actionPrice.value) }}&nbsp;₽
                                                </span>
                                                </div>
                                                <div class="action-price__basic-price">
                                                    {{ convertPriceDigit(panel.actionPrice.base) }}&nbsp;₽
                                                </div>
                                            </div>

                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Вставка компонента -->
                        <template v-else>
                            <keep-alive>
                                <component :is="component"/>
                            </keep-alive>
                        </template>

                    </div>
                </div>

                <div class="panel__footer">
                    <!-- Вывод данных с масок -->
                    <template v-if="!insertComponent">
                        <template v-if="panel.link && panel.linkTitle && !panel.viewType">
                            <ButtonItem
                                :class="['visual-button__button visual-button_width_block']"
                                :text="panel.linkTitle"
                                @click="toNextStep(panel)"
                            />
                        </template>
                        <template v-if="panel.viewType === 'card' && panel.linkTitle">
                            <ButtonItem
                                :class="['visual-button__button visual-button_width_block']"
                                :text="panel.linkTitle"
                                @click="toNextStep(panel)"
                            />
                        </template>
                        <template v-if="panel.viewType === 'popup' && panel.linkTitle">
                            <ButtonItem
                                :class="['visual-button__button visual-button_width_block',
                                    {'j-popup-callback': !iframeView}]"
                                :data-no-hash="isPopup(panel.link)"
                                :data-href="isPopup(panel.link) && !iframeView &&  popupOptions.id"
                                :text="panel.linkTitle"
                                @click="openPopup(panel)"
                            />
                        </template>
                    </template>

                    <!-- Вставка компонента -->
                    <template v-else>
                        <ButtonItem
                            :class="['visual-button__button visual-button_width_block']"
                            :text="resultButtonText"
                            @click="closePanel"
                        />
                    </template>
                </div>
            </template>
        </div>
    </transition>
</template>

<script>
import ButtonItem from '../ButtonItem/ButtonItem';
import FilterForm from '../FilterComponent/FilterForm';
import FloorSwitcherComponent from '../FloorSwitcherComponent/FloorSwitcherComponent';
import {mapGetters} from 'vuex';
import {Utils} from '@/common/scripts/utils';

export default {
    name: 'PanelComponent',

    components: {
        ButtonItem,
        FilterForm,
        FloorSwitcherComponent
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
            dataStep    : 'GET_DATA_STEP',
            hidePrice   : 'GET_HIDDEN_PRICE',
            popupOptions: 'GET_IS_POPUP',
            estatePlural: 'GET_ESTATE_PLURAL',
            elements    : 'GET_ELEMENTS',
            iframeView  : 'GET_IS_IFRAME',
            isMobile    : 'GET_IS_MOBILE'
        }),

        resultButtonText() {
            return this.elements.length ?
                `Показать ${this.estateType}` :
                `${this.estateType} не найдено`;
        },

        estateType() {
            return Utils.pluralWord(this.elements.length ? 3 : 0, this.estatePlural);
        }
    },

    watch: {
        dataStep() {
            if (!this.insertComponent) {
                this.closePanel();
            }
        },

        isMobile() {
            this.closePanel();
        }
    },

    mounted() {
        this._bindEvents();
        this._subscribes();
    },

    methods: {
        _bindEvents() {
            document.addEventListener('click', this._onClickOutside);
        },

        _subscribes() {
            this.$root.$on('mouseOverPanel', (data) => {
                this._setData(data);
                this._insert();
                this._initPopup(data.link);
            });

            this.$root.$on('showPanel', (data) => {
                this._setComponent(data);
                this._insert();
            });

            this.$root.$on('mouseLeavePanel', () => {
                this._remove();
            });
        },

        _insert() {
            this.visible = true;
        },

        _remove() {
            this.visible = false;
            this.panel = null;
        },

        _setData(data) {
            const targetId = Number(data.id);

            const currentData = data.elements.find((element) => {
                const id = Number(element.id);

                return id === targetId ? element : false;
            });

            this.panel = {
                type        : data.type,
                link        : data.link,
                isSimpleLink: currentData.isSimpleLink,
                viewType    : currentData.viewType,
                render      : currentData.render,
                target      : data.event.target,
                showPrice   : data.showPrice || true,
                state       : data.state || null,
                ...currentData.tooltip
            };
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

        formatArea(value) {
            return value.toString().replace('.', ',');
        },

        convertPriceRank(price) {
            if (Number(price)) {
                return Utils.convertToDigit(Utils.convertToRank(price, 1000000).toFixed(1));
            }

            return price;
        },

        convertPriceDigit(price) {
            if (Number(price)) {
                return Utils.convertToDigit(price);
            }

            return price;
        },

        closePanel() {
            this.insertComponent = false;
            this.openFilterTarget = null;
            this._remove();
            this.$root.$emit('closePanel');
        },

        toNextStep(element) {
            this.$root.$emit('clickNextPanel', element);
        },

        _initPopup() {
            setTimeout(() => {
                this.$root.$emit('initPopup');
            }, 0);
        },

        openPopup(data) {
            this.$root.$emit('openPopup', data);
        },

        isPopup(link) {
            return !link?.length && Boolean(this.popupOptions);
        }
    }
};
</script>

<style lang="scss">
@import "PanelComponent";
</style>
