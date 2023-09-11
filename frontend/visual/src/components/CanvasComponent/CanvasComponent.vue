<template>
    <div ref="canvas"
         :class="['visual-canvas',
            {'visual-canvas_theme_floor': floorStep},
            modify]">
        <SvgPanZoom
            ref="panzoom"
            :zoomEnabled="true"
            :controlIconsEnabled="false"
            :mouseWheelZoomEnabled="false"
            :preventMouseEventsDefault="false"
            :dblClickZoomEnabled="false"
            :panEnabled="isPanEnabled"
            :fit="false"
            :center="false"
            :minZoom="minZoom"
            :maxZoom="maxZoom"
            :customEventsHandler="{haltEventListeners: haltListeners,
             init: _initCustomEvents,
             destroy: _destroyCustomEvents}"
            :beforePan="_beforePan"
            @svgpanzoom="_initPanZoom"
            style="width: 100%; height: 100%;">

            <svg width="100%" height="100%">
                <g>
                    <!-- start: Разметка плана визуального -->
                    <svg ref="render"
                         :viewBox="[`0 0 ${renderSize.width} ${renderSize.height}`]"
                         :preserveAspectRatio="floorStep ? 'xMidYMid' : 'xMidYMid slice'"
                         class="visual-canvas__svg">

                        <!-- Рендер -->
                        <image :href="render.link"
                               x="0" y="0" width="100%" height="100%">
                        </image>

                        <!-- Затемнение -->
                        <rect v-if="overlay && !floorStep"
                              class="visual-canvas__overlay"
                              :fill-opacity="overlay"
                              width="100%"
                              height="100%"
                              mask="url(#visual-canvas__overlay)">
                        </rect>

                        <!-- Маски -->
                        <path
                            v-for="element in elements"
                            :key="element.id"
                            :id="element.id"
                            :d="element.path"
                            :style="floorStep ? setPathStyle(element.maskColor) : false"
                            :data-no-hash="!isMobile && isPopup(element.viewType)"
                            :data-href="!isMobile && isPopup(element.viewType) && !iframeView && popupOptions.id"
                            :class="[
                                'visual-canvas__path',
                                {'is-disabled': element.disabled},
                                {'is-booked': element.booked},
                                {'is-hovered': Number(element.id) === activeElement},
                                {'j-popup-callback': !isMobile && isPopup(element.viewType) && !iframeView}
                            ]"
                            @mouseover="mouseOverHandler($event, element)"
                            @mousemove="mouseMoveHandler($event, element)"
                            @mouseleave="mouseLeaveHandler(element)"
                            @click="clickHandler(element)"
                        >
                        </path>

                        <!-- Заливка масок при затемнении -->
                        <defs v-if="overlay && !floorStep"
                              class="visual-canvas__mask">
                            <mask id="visual-canvas__overlay">
                                <rect fill="white" width="100%" height="100%"></rect>
                                <path v-for="(element, index) in elements" :key="index" :d=element.path></path>
                            </mask>
                        </defs>

                        <!-- Поинтеры элементов (маркеры) -->
                        <template v-if="pointers.length">
                            <PointerComponent
                                :pointers="pointers"
                                :elements="elements"
                                :floor-step="floorStep"
                            />
                        </template>

                        <!-- Поинтеры рендера -->
                        <template v-if="renderPointers.length">
                            <RenderPointerComponent
                                :render-pointers="renderPointers"
                            />
                        </template>

                        <!-- Кнопки 360 -->
                        <template v-if="panorama.length">
                            <PanoramaComponent :panorama="panorama"
                                                @open-planoplan="_openPlanoplan"/>
                        </template>
                    </svg>
                    <!-- end: Разметка плана визуального -->
                </g>
            </svg>
        </SvgPanZoom>

        <!-- start: контролы svgpanzoom -->
        <template v-if="zoom && floorStep">
            <ul class="visual-canvas__zoom-controls">
                <li class="visual-canvas__zoom-control"
                    @click="zoomIn">
                    <ZoomPlusIcon :class-name="['visual-canvas__zoom-icon',
                            {'is-disabled': zoomValue >= this.maxZoom}]"/>
                </li>
                <li class="visual-canvas__zoom-control"
                    @click="zoomOut">
                    <ZoomMinusIcon :class-name="['visual-canvas__zoom-icon',
                            {'is-disabled': zoomValue <= minZoom}]"/>
                </li>
            </ul>
        </template>
        <!-- /end: контролы svgpanzoom -->

        <!-- start: popup для планоплана -->
        <div v-if="isPlanoplanOpen"
             ref="planoplanPopup"
             class="visual-panorama__popup">
            <div class="visual-panorama__planoplan" id="planoplan"></div>
            <div class="visual-panorama__close">
                <button class="button-circle"
                        aria-label="Закрыть"
                        @click="_closePlanoplan">
                    <CloseIcon />
                </button>
            </div>
        </div>
        <!-- /end: popup для планоплана -->
    </div>
</template>

<script>
import {initCallbackPopup, insertPremisesData} from '@/common/scripts/common';
import {mapActions, mapGetters} from 'vuex';
import CloseIcon from '../Icons/CloseIcon.vue';
import Hammer from 'hammerjs';
import Observer from '@/common/scripts/observer';
import PanoramaComponent from '../PanoramaComponent/PanoramaComponent';
import PointerComponent from '../PointerComponent/PointerComponent';
import RenderPointerComponent from '../RenderPointerComponent/RenderPointerComponent';
import {RESIZE_EVENTS} from '@/common/scripts/constants';
import SvgPanZoom from 'vue-svg-pan-zoom';
import {Utils} from '@/common/scripts/utils';
import ZoomMinusIcon from '../Icons/ZoomMinusIcon';
import ZoomPlusIcon from '../Icons/ZoomPlusIcon';

const observer = new Observer();

export default {
    name: 'CanvasComponent',

    components: {
        CloseIcon,
        PanoramaComponent,
        PointerComponent,
        RenderPointerComponent,
        SvgPanZoom,
        ZoomPlusIcon,
        ZoomMinusIcon
    },

    props: {
        windowWidth : Number,
        windowHeight: Number,
        modify      : String,
        floorStep   : Boolean
    },

    data() {
        return {
            overlay        : this.$dataset.overlay || false,
            activeElement  : null,
            svgpanzoom     : null,
            zoomValue      : 1,
            maxZoom        : 2,
            minZoom        : 1,
            hammer         : null,
            haltListeners  : ['touchstart', 'touchend', 'touchmove', 'touchleave', 'touchcancel'],
            heightRatio    : null,
            uid            : null,
            isPlanoplanOpen: false
        };
    },

    computed: {
        ...mapGetters({
            iframeView    : 'GET_IS_IFRAME',
            isMobile      : 'GET_IS_MOBILE',
            isTouch       : 'GET_IS_TOUCH',
            isTouchTable  : 'GET_IS_TOUCH_TABLE',
            elements      : 'GET_ELEMENTS',
            dataStep      : 'GET_DATA_STEP',
            renderSize    : 'GET_CANVAS_SIZE',
            render        : 'GET_RENDER',
            panorama      : 'GET_PANORAMA',
            pointers      : 'GET_POINTERS',
            renderPointers: 'GET_RENDER_POINTERS',
            canvasShift   : 'GET_CANVAS_SHIFT',
            popupOptions  : 'GET_IS_POPUP',
            hidePrice     : 'GET_HIDDEN_PRICE',
            zoom          : 'GET_ZOOM',
            elementsArea  : 'GET_ELEMENTS_AREA'
        }),

        canvas() {
            return this.$refs.canvas || null;
        },

        panzoom() {
            return this.$refs.panzoom.$el || null;
        },

        isPanEnabled() {
            return this.floorStep;
        },

        /**
         * Координаты смещения при инициализации
         * @return {(number|number)[]|number[]} - координаты
         */
        deltaShiftMasksCoords() {
            if (!this.renderSize.width || !this.renderSize.height) {
                return [0, 0];
            }

            const halfWWidth = this.windowWidth / 2;
            const halfWHeight = this.windowHeight / 2;
            const deltaX = this.elementsArea.cx > halfWWidth ?
                this._roundingNum((this.elementsArea.cx * this.heightRatio) - halfWWidth) :
                0;
            const deltaY = this.elementsArea.cy > halfWHeight ?
                this._roundingNum((this.elementsArea.cy * this.heightRatio) - halfWHeight) :
                0;

            return [deltaX, deltaY];
        }
    },

    watch: {
        render: {
            handler() {
                this.$nextTick(() => {
                    this._changeScrollPosition();
                });
            },
            deep: true
        },

        canvasShift: {
            handler() {
                this._changeScrollPosition();
            },
            deep: true
        },

        isMobile() {
            this._setCanvasSize();
            this._changeScrollPosition();

            this.$nextTick(() => {
                if (!this.isMobile && Boolean(this.popupOptions)) {
                    this._initPopup();
                }
            });
        },

        isPanEnabled() {
            if (!this.svgpanzoom) {
                return;
            }

            if (this.isPanEnabled) {
                this.svgpanzoom.enablePan();
            } else {
                this.svgpanzoom.reset();
                this.svgpanzoom.disablePan();
            }
        },

        isPlanoplanOpen() {
            this.$nextTick(() => {
                this._initPlanoplan();
            });
        }
    },

    mounted() {
        this._subscribes();
        this._bindEvents();
        this._bindRootEvents();
        this._initPopup();
        this._setCanvasSize();
        this._changeScrollPosition();
    },

    beforeDestroy() {
        RESIZE_EVENTS.forEach((event) => {
            window.removeEventListener(event, this._setCanvasSize, false);
        });
        observer.unsubscribe('setMaskHover');
        observer.unsubscribe('removeMaskHover');
        observer.unsubscribe('clickOnPointer');
        observer.unsubscribe('clickNextPanel');
    },

    methods: {
        ...mapActions(['changeStateLoading']),

        /**
         * Округление до 2-х знаков
         * @param {number} num - исходное число
         * @return {number} - результат
         * @private
         */
        _roundingNum(num) {
            return parseFloat(num.toFixed(2));
        },

        _subscribes() {
            this.$root.$on('maskHover', (id) => {
                id ? this._maskHoverHandler(id) : this._maskHoverHandler();
            });

            this.$root.$on('clickOnPointer', (item) => {
                this.clickHandler(item);
            });

            this.$root.$on('closePanel', () => {
                this._mouseLeaveEvents();
            });

            this.$root.$on('clickNextPanel', (item) => {
                this._route(item);
            });
        },

        _bindEvents() {
            RESIZE_EVENTS.forEach((event) => {
                window.addEventListener(event, this._setCanvasSize.bind(this));
            });
        },

        _bindRootEvents() {
            this.$root.$on('initPopup', () => {
                this._initPopup();
            });

            this.$root.$on('openPopup', (data) => {
                this.$root.$emit('mouseLeavePanel');
                this.$root.$emit('closePanel');
                this._openPopup(data);
            });
        },

        _setCanvasSize() {
            this.heightRatio = this.renderSize.height < this.canvas.getBoundingClientRect().height ?
                this._roundingNum(this.canvas.getBoundingClientRect().height / this.renderSize.height) :
                1;

            Object.assign(this.panzoom.style, {
                width : this.isMobile && !this.floorStep ? `${this.renderSize.width * this.heightRatio}px` : '100',
                height: this.isMobile && !this.floorStep ? `${this.renderSize.height * this.heightRatio}px` : '100%'
            });
        },

        _changeScrollPosition() {
            if (this.isMobile) {
                if (this.canvasShift.horizontal || this.canvasShift.vertical) {
                    this.canvas.scrollLeft =
                        this.canvasShift.horizontal * (this.canvas.scrollWidth - this.canvas.clientWidth);

                    this.canvas.scrollTop =
                        this.canvasShift.vertical * (this.canvas.scrollHeight - this.canvas.clientHeight);
                } else {
                    this._setDefaultZoom();

                    if (!this.floorStep) {
                        this.canvas.scrollTo({
                            left    : this.deltaShiftMasksCoords[0],
                            top     : this.deltaShiftMasksCoords[1],
                            behavior: 'smooth'
                        });
                    }
                }
            }
        },

        _setDefaultZoom() {
            const panZoomSize = this.panzoom.getBoundingClientRect();

            if (this._roundingNum(panZoomSize.width / panZoomSize.height) <
                this._roundingNum(this.renderSize.width / this.renderSize.height)) {
                const wrapZoom = this._roundingNum(this.renderSize.width /
                    (this.elementsArea.x2 - this.elementsArea.x1));

                this.zoomValue = wrapZoom <= this.maxZoom ?
                    wrapZoom >= this.minZoom ?
                        wrapZoom :
                        this.minZoom :
                    this.maxZoom;
                this.svgpanzoom.zoom(this.zoomValue);

                if (this.floorStep) {
                    const x = (-(panZoomSize.width * (this.elementsArea.cx -
                            (this.renderSize.width / 2 / this.zoomValue))) /
                        this.renderSize.width * this.zoomValue) + 1;
                    const offsetY = (this.elementsArea.cy - (this.renderSize.height / 2)) / this.zoomValue;
                    const y = (-(panZoomSize.height * (this.elementsArea.cy - offsetY -
                            (this.renderSize.height / 2 / this.zoomValue))) /
                        this.renderSize.height * this.zoomValue) + 1;

                    this.svgpanzoom
                        .pan({x,
                            y});
                }
            }
        },

        _initPopup() {
            const popupButtons = Array.from(this.$root.$el.querySelectorAll('.j-popup-callback:not([data-init])'));

            if (popupButtons.length) {
                initCallbackPopup(popupButtons);

                popupButtons.forEach((button) => {
                    button.dataset.init = true;
                });
            }
        },

        isPopup(type) {
            return type === 'popup';
        },

        _maskHoverHandler(id = null) {
            this.activeElement = Number(id);
        },

        _mouseLeaveEvents() {
            this.activeElement = null;
            // Убираем подсветку поинтера
            this.$root.$emit('pointerHover');
            // Прячем тултип
            this.$root.$emit('mouseLeaveTooltip');
        },

        mouseOverHandler(event, element) {
            if (typeof element.isShowTooltip !== 'undefined' && !element.isShowTooltip) {
                return;
            }

            this.activeElement = Number(element.id);
            const observerAction = this.isMobile && this.isTouch ? 'mouseOverPanel' : 'mouseOverTooltip';

            this.$root.$emit('pointerHover', element.id);
            this.$root.$emit(observerAction, {
                id      : element.id,
                link    : element.link,
                event,
                elements: this.elements
            });
        },

        mouseMoveHandler(event, element) {
            if (typeof element.isShowTooltip !== 'undefined' && !element.isShowTooltip) {
                return;
            }

            if (!(this.isMobile && this.isTouch)) {
                this.$root.$emit('mouseMoveTooltip', event);
            }
        },

        mouseLeaveHandler(element) {
            if (typeof element.isShowTooltip !== 'undefined' && !element.isShowTooltip) {
                return;
            }

            if (!(this.isMobile && this.isTouch)) {
                this._mouseLeaveEvents();
            }
        },

        clickHandler(item) {
            if (item.disabled) {
                return;
            }

            if (!this.isMobile) {
                // Если устройство тач стол и элемент не в фокусе - ставим ему фокус и показываем тултип, иначе переходим по ссылке
                if (this.isTouchTable && !item.inFocus) {
                    this._unFocusAll();
                    item.inFocus = true;
                } else {
                    item.link ? this._route(item) : this._openPopup(item.tooltip);
                }
            } else if (item.link && item.isSimpleLink) {
                window.location = item.link;
            }
        },

        // Снимаем статус фокуса со всех элементов (для тач столов)
        _unFocusAll() {
            if (!this.elements) {
                return;
            }

            this.elements.forEach((element) => {
                element.inFocus = false;
            });
        },

        _route(element) {
            if (!element.link) {
                return;
            }

            // карточки квартир открываем в новой вкладке
            if (this.floorStep) {
                window.open(element.link, '_blank');
                // флаг, означающий переход по ссылке (сторонней или на страницу сайта)
            } else if (element.isSimpleLink) {
                window.location = element.link;
            } else {
                const query = this.$route.query;

                delete query.rotateId;
                this.$router.push({
                    path: element.link,
                    query
                });
            }
        },

        _openPopup(data) {
            const popupData = {
                title      : data?.title ?? null,
                area       : data.area ? `${this.formatArea(data.area)}&nbsp;м²` : null,
                block      : data.block ? `Корпус&nbsp;${data.block}` : null,
                section    : data.section ? `Секция&nbsp;${data.section}` : null,
                floor      : data.floor.value ? `Этаж&nbsp;${data.floor.value}` : null,
                hidePrice  : this.hidePrice.status ? this.hidePrice.text : null,
                price      : data.price && this.showPrice(data) ? `${this.formatPrice(data.price)}&nbsp;₽` : null,
                description: data?.description ?? '',
                actionPrice: data.actionPrice && this.showPrice(data) ?
                    {
                        value: `${this.formatPrice(data.actionPrice.value)}`,
                        base : `${this.formatPrice(data.actionPrice.base)}`
                    } :
                    null
            };

            if (this.iframeView) {
                window.parent.postMessage({
                    message: 'openPopup',
                    href   : this.popupOptions.id,
                    type   : 'callback',
                    popupData
                }, '*');

                return;
            }

            observer.subscribe('popup:open', (popup) => {
                insertPremisesData(popup.popup, popupData);
            });

            this.$root.$emit('mouseLeaveTooltip');
        },

        setPathStyle(color) {
            return {
                fill  : color,
                stroke: color
            };
        },

        _initPanZoom(svgpanzoom) {
            this.svgpanzoom = svgpanzoom;
        },

        _initCustomEvents(options) {
            if (!this.floorStep) {
                return;
            }

            const instance = options.instance;
            let pannedX = 0;
            let pannedY = 0;

            this.hammer = new Hammer(options.svgElement);
            this.hammer.get('pinch').set({enable: true});

            // Handle pan
            this.hammer.on('panstart panmove', (event) => {
                // On pan start reset panned variables
                if (event.type === 'panstart') {
                    pannedX = 0;
                    pannedY = 0;
                }

                // Pan only the difference
                instance.panBy({
                    x: event.deltaX - pannedX,
                    y: event.deltaY - pannedY
                });
                pannedX = event.deltaX;
                pannedY = event.deltaY;
            });

            // Handle pinch
            this.hammer.on('pinchstart pinchmove', (event) => {
                // On pinch start remember initial zoom
                if (event.type === 'pinchstart') {
                    this.zoomValue = this._roundingNum(instance.getZoom());
                    instance.zoomAtPoint(this.zoomValue * event.scale, {
                        x: event.center.x,
                        y: event.center.y
                    });
                }

                instance.zoomAtPoint(this.zoomValue * event.scale, {
                    x: event.center.x,
                    y: event.center.y
                });
            });

            this.hammer.on('pinchend', () => {
                this.zoomValue = this._roundingNum(instance.getZoom());
            });

            // Prevent moving the page on some devices when panning over SVG
            options.svgElement.addEventListener('touchmove', (event) => {
                event.preventDefault();
            });
        },

        _destroyCustomEvents() {
            if (this.hammer) {
                this.hammer.destroy();
            }
        },

        _beforePan(oldPan, newPan) {
            if (!this.svgpanzoom) {
                return false;
            }

            const sizes = this.svgpanzoom.getSizes();
            const gutterHeight = 100;
            const gutterWidth = 100;
            const leftLimit = -((sizes.viewBox.x + sizes.viewBox.width) * sizes.realZoom) + gutterWidth;
            const rightLimit = sizes.width - gutterWidth - (sizes.viewBox.x * sizes.realZoom);
            const topLimit = -((sizes.viewBox.y + sizes.viewBox.height) * sizes.realZoom) + gutterHeight;
            const bottomLimit = sizes.height - gutterHeight - (sizes.viewBox.y * sizes.realZoom);

            return {
                x: Math.max(leftLimit, Math.min(rightLimit, newPan.x)),
                y: Math.max(topLimit, Math.min(bottomLimit, newPan.y))
            };
        },

        zoomIn() {
            if (!this.svgpanzoom) {
                return;
            }

            if (!this.svgpanzoom.isPanEnabled() && this.floorStep) {
                this.svgpanzoom.enablePan();

                this.$nextTick(() => {
                    this.svgpanzoom.zoomIn();
                });
            }

            this.svgpanzoom.zoomIn();

            this.zoomValue = this._roundingNum(this.svgpanzoom.getZoom());
        },

        zoomOut() {
            if (!this.svgpanzoom) {
                return;
            }

            this.svgpanzoom.zoomOut();

            this.zoomValue = this._roundingNum(this.svgpanzoom.getZoom());
        },

        showPrice(item) {
            return item.showPrice || false;
        },

        formatPrice(value) {
            const val = (value / 1).toFixed(0).replace('.', ',');

            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        },

        formatArea(value) {
            return value.toString().replace('.', ',');
        },

        checkDisabled(symbol) {
            return symbol === 'plus' && this.zoomValue >= this.maxZoom ?
                'is-disabled' :
                symbol === 'minus' && this.zoomValue <= this.minZoom ? 'is-disabled' : '';
        },

        _openPlanoplan(uid) {
            if (this.iframeView) {
                window.parent.postMessage({
                    message  : 'openPopup',
                    type     : 'planoplan',
                    href     : 'planoplan',
                    popupData: {
                        uid
                    }
                }, '*');

                return;
            }
            this.changeStateLoading(true);
            this.uid = uid;
            this.isPlanoplanOpen = true;
        },

        _closePlanoplan() {
            this.isPlanoplanOpen = false;
            Utils.bodyStatic();
        },

        _initPlanoplan() {
            if (!this.isPlanoplanOpen) {
                return;
            }

            Utils.bodyFixed(this.$refs.planoplanPopup);

            const primaryColor = getComputedStyle(document.documentElement)
                .getPropertyValue('--color-brand-base') || null;

            const widget = window.Planoplan.init({
                width          : '100%',
                height         : '100%',
                uid            : this.uid,
                el             : 'planoplan',
                textColor      : '#000000',
                backgroundColor: '#ffffff',
                ...primaryColor && {
                    primaryColor
                }
            });

            widget.on('load', () => {
                this.changeStateLoading(false);
            });
        }
    }
};
</script>

<style lang="scss">
    @import "CanvasComponent";
</style>
