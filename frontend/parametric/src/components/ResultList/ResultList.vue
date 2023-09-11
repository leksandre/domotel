<template>
    <div :class="['result-list', `result-list_theme_${resultViewModifier}`,
        {'is-processing': isProcessing},
        {'is-sort-open': isSortOpen}
    ]">

        <component v-for="(item, index) in premises"
           :id="item.id"
           :key="index"
           :is="resultType(item)"
           :href="isCardResultType(item.viewType) && item.link"
           :target="isCardResultType(item.viewType) && '_blank'"
           class="result-list__item"
           @click.prevent="isCardResultType(item.viewType) && clickHandler($event, item)"
        >
            <div class="result-list__container">
                <!-- Планировка -->
                <ResultListPlan :rawItem="item"
                                :isRowResultView="isRowResultView" />
                <div class="result-list__content">
                    <!-- Общая инф-я -->
                    <div class="result-list__info">
                        <div class="result-list__general">
                            <!-- Название и площадь -->
                            <ul class="result-list__title list list_theme_accent list_theme_dot list_indent_tomato">
                                <li v-if="item.title" class="list__item">{{ item.title }}</li>
                                <li v-if="item.area" class="list__item">{{ formatArea(item.area) }}&nbsp;м²</li>
                            </ul>

                            <!-- Блок с подробностями -->
                            <ul class="list list_theme_medium list_theme_dot">
                                <li v-if="item.block" class="list__item">
                                    Корпус&nbsp;{{ item.block }}
                                </li>
                                <li v-if="item.section" class="list__item">
                                    Секция&nbsp;{{ item.section }}
                                </li>
                                <li v-if="item.floor" class="list__item">
                                    <span v-if="item.floor.value">Этаж&nbsp;{{ item.floor.value }}</span>
                                    <span v-if="item.floor.total">&nbsp;из&nbsp;{{ item.floor.total }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Блок с ценами -->
                        <template v-if="hidePrice.status">
                            <div class="result-list__prices">
                                <div class="result-list__price">
                                    {{ hidePrice.text }}
                                </div>
                            </div>
                        </template>

                        <template v-else>
                            <div class="result-list__prices">
                                <div v-if="item.price && showPrice(item)"
                                     :class="['result-list__price',
                                         {'action-price': item.actionPrice && showPrice(item)}]">
                                    {{ formatPrice(item.price) }} ₽
                                </div>
                                <div v-else class="result-list__price">
                                    {{ getStateText(item) }}
                                </div>

                                <div v-if="item.actionPrice && showPrice(item)" class="action-price__action-wrapper">
                                    <div class="action-price__action">
                                        <span class="action-price__action-value">
                                            -{{ formatPrice(item.actionPrice.value) }} ₽
                                        </span>
                                     </div>
                                    <div class="action-price__basic-price">
                                        {{ formatPrice(item.actionPrice.base) }} ₽
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>

                    <div class="result-list__bottom">
                        <!-- Срок сдачи и особенности -->
                        <div class="result-list__details">

                            <template v-if="item.delivery">
                                <ul class="list list_theme_regular list_theme_details-parametric">
                                    <li class="list__item">
                                        <span class="list__item-label">
                                            {{ item.delivery.label }}
                                        </span>&nbsp;
                                        {{ item.delivery.title }}
                                    </li>
                                </ul>
                            </template>

                            <!-- Группировка особенностей -->
                            <template v-if="item.options && item.options.length">
                                <ul v-if="noGroupingOptions(item.options, item)"
                                    class="list list_theme_regular list_theme_details-parametric">
                                    <li v-for="(option, index) in item.options"
                                        :key="index.id"
                                        class="list__item">
                                        {{ option.title }}
                                    </li>
                                </ul>


                                <div v-else class="list list_theme_regular list_theme_details-parametric">
                                    <div v-for="(option, index) in setStartOptions(item.options, item)"
                                        :key="index.id"
                                        class="list__item">
                                        {{ option.title }}
                                    </div>

                                    <div>
                                        <div class="list__tooltip-container"
                                            @mouseover="showTooltip(item.id)"
                                            @mouseout="hideTooltip"
                                        >
                                        <span class="list__item list__item_option_counter">
                                            +{{ setLastOptions(item.options, item).length }}
                                        </span>

                                            <DynamicTooltip
                                                :type="'premisesOptions'"
                                                :direction="'top'"
                                                :id="item.id"
                                                :items="setLastOptions(item.options, item)"
                                                ref="optionsTooltip"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="result-list__icons">
                            <div v-if="hasIcon(item)"
                                class="result-list__booked">
                                <img :src="item.state.icon" alt="icon">
                            </div>
                        </div>

                        <template v-if="isPopupResultType(item.viewType) && !hasIcon(item)">
                            <ButtonItem
                                :data-no-hash="isPopupResultType(item.viewType)"
                                :data-href="isPopupResultType(item.viewType) && popupOptions.id"
                                text="Забронировать"
                                class="result-list__button-booking j-popup-callback"
                                @click="clickHandler($event, item)"
                            />
                        </template>
                    </div>

                </div>
            </div>
        </component>
    </div>
</template>

<script>
import ButtonItem from '../ButtonItem/ButtonItem';
import DynamicTooltip from '../DynamicTooltip/DynamicTooltip';
import {initCallbackPopup} from '@/common/scripts/common';
import {mapGetters} from 'vuex';
import Observer from '@/common/scripts/observer';
import popupContentTemplate from '@/components/form/form-parametric-content.twig';
import ResultListPlan from '../ResultListPlan/ResultListPlan';
import {Utils} from '@/common/scripts/utils';

const observer = new Observer();

export default {
    name: 'ResultList',

    components: {
        ButtonItem,
        DynamicTooltip,
        ResultListPlan
    },

    props: ['premises'],

    data() {
        return {
            firstOptions       : null,
            isTooltipOpen      : false,
            isSortOpen         : false,
            calcGroupOptions   : 1,
            tooltipVisibleClass: 'is-active',
            activePlan         : 0,
            imageHovered       : false
        };
    },

    computed: {
        ...mapGetters({
            isMobile    : 'GET_IS_MOBILE',
            isProcessing: 'GET_IS_PROCESSING',
            page        : 'GET_PAGE',
            resultView  : 'GET_RESULT_VIEW',
            groupOptions: 'GET_GROUP_OPTIONS',
            hidePrice   : 'GET_HIDDEN_PRICE',
            popupOptions: 'GET_IS_POPUP'
        }),

        resultViewModifier() {
            return this.resultView === 1 ?
                'row' :
                'card';
        },

        isRowResultView() {
            return this.resultView === 1;
        }
    },

    watch: {
        premises: {
            handler() {
                this.$emit('updateAnimation');
            },
            deep: true
        },

        isProcessing() {
            this.$emit('updateAnimation');
        },

        page() {
            this.$nextTick(() => {
                this.initPopup();
            });
        }
    },

    mounted() {
        this.subscribes();
        this.initPopup();
    },

    methods: {
        subscribes() {
            observer.subscribe('openSort', () => {
                this.disableCardOpen();
            });

            observer.subscribe('closeSort', () => {
                this.enableCardOpen();
            });
        },

        /**
         * Метод устанавливает вид результата выдачи:
         * если у помещения есть ссылка, то выводится карточка;
         * если нет ссылки, то выводим попап;
         * если нет ни ссылки ни попапа, выводим блок, ошибку в консоль и нет обработки событий
         * @param {Object} premises - данные помещения
         * @returns {String} вид блока (ссылка / блок)
         */
        resultType(premises) {
            let tag = 'div';

            switch (premises.viewType) {
                case 'card':
                    tag = 'a';
                    break;
                case 'popup':
                    break;
                default:
                    console.error(`Заполните карточку помещения для id=${premises.id} или создайте попап.`);

                    break;
            }

            return tag;
        },


        isCardResultType(type) {
            return type === 'card';
        },

        isPopupResultType(type) {
            return type === 'popup';
        },

        initPopup() {
            const popupButtons = Array.from(document.querySelectorAll('.j-popup-callback:not([data-init])'));

            if (popupButtons.length) {
                initCallbackPopup(popupButtons);

                popupButtons.forEach((button) => {
                    button.dataset.init = 'true';
                });
            }
        },

        clickHandler(event, itemData) {
            this.isCardResultType(itemData.viewType) ?
                this.openPremisesCard(event, itemData.link) :
                this.openPopup(itemData);
        },

        formatPrice(value) {
            const val = (value / 1).toFixed(0).replace('.', ',');

            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        },

        formatArea(value) {
            return value.toString().replace('.', ',');
        },

        hasIcon(item) {
            return this._hasState(item) && (item.state.icon || false);
        },

        isAvailable(item) {
            return this._hasState(item) && item.state.available;
        },

        showPrice(item) {
            return item.showPrice || false;
        },

        getStateText(item) {
            return this._hasState(item) ? item.state.additionalTitle || '' : '';
        },

        _hasState(item) {
            return Object.prototype.hasOwnProperty.call(item, 'state');
        },

        /**
         * Получаем длину отображаемых элементов преимуществ
         * @param {object} item - тип ссылки - попап или карточка
         * @returns {number} - возвращает число элементов для отображения
         */
        _getLength(item) {
            return item.viewType === 'popup' && !this.isMobile && !this.hasIcon(item) ? 1 : this.groupOptions;
        },

        noGroupingOptions(options, item) {
            return options.length <= this._getLength(item);
        },

        setStartOptions(options, item) {
            return options.slice(0, this._getLength(item));
        },

        setLastOptions(options, item) {
            return options.slice(this._getLength(item));
        },

        showTooltip(id) {
            // контейнер, в который вставится компонент
            this.tooltipContainer = this.$refs.optionsTooltip.filter((item) => {
                return Number(id) === Number(item.id);
            });
            // нода тултипа
            this.tooltipNode = this.tooltipContainer[0].$el;
            // обертка с количеством скрытых опций
            this.optionsCounter = this.tooltipContainer[0].$el.previousElementSibling;

            this.tooltipNode.classList.add(this.tooltipVisibleClass);
            this.optionsCounter.classList.add(this.tooltipVisibleClass);

            setTimeout(() => {
                this.isTooltipOpen = true;
            }, 10);
        },

        hideTooltip() {
            if (this.tooltipNode) {
                this.tooltipNode.classList.remove(this.tooltipVisibleClass);
                this.optionsCounter.classList.remove(this.tooltipVisibleClass);

                this.tooltipContainer = null;
                this.tooltipNode = null;

                setTimeout(() => {
                    this.isTooltipOpen = false;
                }, 0);
            }
        },

        disableCardOpen() {
            this.isSortOpen = true;
        },

        enableCardOpen() {
            setTimeout(() => {
                this.isSortOpen = false;
            }, 300);
        },

        openPremisesCard(event, link) {
            if (!link) {
                return;
            }

            if (this.isMobile) {
                if (!this.tooltipNode && !this.isTooltipOpen && !this.isSortOpen) {
                    window.open(link, '_blank');
                }
            } else if (!this.isSortOpen) {
                window.open(link, '_blank');
            }
        },

        openPopup(data) {
            observer.subscribe('popup:open', (popup) => {
                const currentForm = popup.popup.querySelector('.j-form');
                const container = currentForm.querySelector('.j-form__parametric-content');
                const fillField = currentForm.querySelector('.j-form__fill-field');

                if (fillField) {
                    fillField.value = `${data.description}`;
                }

                if (!container) {
                    return;
                }

                const popupData = {
                    title      : data.title ? data.title : null,
                    area       : data.area ? `${this.formatArea(data.area)}&nbsp;м²` : null,
                    block      : data.block ? `Корпус&nbsp;${data.block}` : null,
                    section    : data.section ? `Секция&nbsp;${data.section}` : null,
                    floor      : data.floor.value ? `Этаж&nbsp;${data.floor.value}` : null,
                    hidePrice  : this.hidePrice.status ? this.hidePrice.text : null,
                    price      : data.price && this.showPrice(data) ? `${this.formatPrice(data.price)}&nbsp;₽` : null,
                    actionPrice: data.actionPrice && this.showPrice(data) ?
                        {
                            value: `${this.formatPrice(data.actionPrice.value)}`,
                            base : `${this.formatPrice(data.actionPrice.base)}`
                        } :
                        null
                };

                Utils.clearHtml(container);
                Utils.insetContent(container, popupContentTemplate(popupData));
            });
        }
    }
};
</script>

<style lang="scss">
@import "ResultList";
</style>
