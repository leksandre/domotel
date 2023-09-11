class IonRangeSlider {
    constructor(element, initOptions= {}) {
        this.input = element;

        // check if base element is input   
        if (this.input.nodeName !== "INPUT") {
            console && console.warn && console.warn("Base element should be <input>!", input);
        }

        this.options = initOptions;
        this.calc_count = 0;
        this.update_tm = 0;
        this.old_from = 0;
        this.old_to = 0;
        this.old_min_interval = null;
        this.raf_id = null;
        this.dragging = false;
        this.forceRedraw = false;
        this.noDiapason = false;
        this.has_tab_index = true;
        this.is_key = false;
        this.is_update = false;
        this.is_start = true;
        this.is_finish = false;
        this.is_active = false;
        this.is_resize = false;
        this.is_click = false;

        this.target = "base";

        // cache for links to all DOM elements
        this.cache = {
            win: window,
            body: document.body,
            input: this.input,
            cont: null,
            rs: null,
            min: null,
            max: null,
            from: null,
            to: null,
            single: null,
            bar: null,
            line: null,
            s_single: null,
            s_from: null,
            s_to: null,
            shad_single: null,
            shad_from: null,
            shad_to: null,
            edge: null,
            grid: null,
            grid_labels: []
        };

        // storage for measure variables
        this.coords = {
            // left
            x_gap: 0,
            x_pointer: 0,

            // width
            w_rs: 0,
            w_rs_old: 0,
            w_handle: 0,

            // percents
            p_gap: 0,
            p_gap_left: 0,
            p_gap_right: 0,
            p_step: 0,
            p_pointer: 0,
            p_handle: 0,
            p_single_fake: 0,
            p_single_real: 0,
            p_from_fake: 0,
            p_from_real: 0,
            p_to_fake: 0,
            p_to_real: 0,
            p_bar_x: 0,
            p_bar_w: 0,

            // grid
            grid_gap: 0,
            big_num: 0,
            big: [],
            big_w: [],
            big_p: [],
            big_x: []
        };

        // storage for labels measure variables
        this.labels = {
            // width
            w_min: 0,
            w_max: 0,
            w_from: 0,
            w_to: 0,
            w_single: 0,

            // percents
            p_min: 0,
            p_max: 0,
            p_from_fake: 0,
            p_from_left: 0,
            p_to_fake: 0,
            p_to_left: 0,
            p_single_fake: 0,
            p_single_left: 0
        };

        // default config
        this.config = {
            skin: "flat",
            type: "single",

            min: 10,
            max: 100,
            from: null,
            to: null,
            step: 1,

            min_interval: 0,
            max_interval: 0,
            drag_interval: false,

            values: [],
            p_values: [],

            from_fixed: false,
            from_min: null,
            from_max: null,
            from_shadow: false,

            to_fixed: false,
            to_min: null,
            to_max: null,
            to_shadow: false,

            prettify_enabled: true,
            prettify_separator: " ",
            prettify: null,

            force_edges: false,

            keyboard: true,

            grid: false,
            grid_margin: true,
            grid_num: 4,
            grid_snap: false,

            hide_min_max: false,
            hide_from_to: false,

            prefix: "",
            postfix: "",
            max_postfix: "",
            decorate_both: true,
            values_separator: " — ",

            input_values_separator: ";",

            disable: false,
            block: false,

            extra_classes: "",

            scope: null,
            onStart: null,
            onChange: null,
            onFinish: null,
            onUpdate: null
        };

        // config from data-attributes extends js config
        this.config_from_data = {
            skin: this.input.dataset.skin,
            type: this.input.dataset.type,

            min: this.input.dataset.min,
            max: this.input.dataset.max,
            from: this.input.dataset.from,
            to: this.input.dataset.to,
            step: this.input.dataset.step,

            min_interval: this.input.dataset.minInterval,
            max_interval: this.input.dataset.maxInterval,
            drag_interval: this.input.dataset.dragInterval,

            values: this.input.dataset.values,

            from_fixed: this.input.dataset.fromFixed,
            from_min: this.input.dataset.fromMin,
            from_max: this.input.dataset.fromMax,
            from_shadow: this.input.dataset.fromShadow,

            to_fixed: this.input.dataset.toFixed,
            to_min: this.input.dataset.toMin,
            to_max: this.input.dataset.toMax,
            to_shadow: this.input.dataset.toShadow,

            prettify_enabled: this.input.dataset.prettifyEnabled,
            prettify_separator: this.input.dataset.prettifySeparator,

            force_edges: this.input.dataset.forceEdges,

            keyboard: this.input.dataset.keyboard,

            grid: this.input.dataset.grid,
            grid_margin: this.input.dataset.gridMargin,
            grid_num: this.input.dataset.gridNum,
            grid_snap: this.input.dataset.gridSnap,

            hide_min_max: this.input.dataset.hideMinMax,
            hide_from_to: this.input.dataset.hideFromTo,

            prefix: this.input.dataset.prefix,
            postfix: this.input.dataset.postfix,
            max_postfix: this.input.dataset.maxPostfix,
            decorate_both: this.input.dataset.decorateBoth,
            values_separator: this.input.dataset.valuesSeparator,

            input_values_separator: this.input.dataset.inputValuesSeparator,

            disable: this.input.dataset.disable,
            block: this.input.dataset.block,

            extra_classes: this.input.dataset.extraClasses,
        };

        this.config_from_data.values = this.config_from_data.values && this.config_from_data.values.split(",");

        for (let prop in this.config_from_data) {
            if (this.config_from_data.hasOwnProperty(prop)) {
                if (this.config_from_data[prop] === undefined || this.config_from_data[prop] === "") {
                    delete this.config_from_data[prop];
                }
            }
        }


        // input value extends default config
        this.val = this.input.value;
        if (this.val !== undefined && this.val !== "") {
            this.val = this.val.split(this.config_from_data.input_values_separator || this.options.input_values_separator || ";");

            if (this.val[0] && this.val[0] == +this.val[0]) {
                this.val[0] = +this.val[0];
            }
            if (this.val[1] && this.val[1] == +this.val[1]) {
                this.val[1] = +this.val[1];
            }

            if (this.options && this.options.values && this.options.values.length) {
                this.config.from = val[0] && this.options.values.indexOf(val[0]);
                this.config.to = val[1] && this.options.values.indexOf(val[1]);
            } else {
                this.config.from = this.val[0] && +this.val[0];
                this.config.to = this.val[1] && +this.val[1];
            }
        }

        // js config extends default config
        Object.assign(this.config, this.options);

        // data config extends config
        Object.assign(this.config, this.config_from_data);
        this.options = this.config;

        this.update_check = {};

        // default result object, returned to callbacks
        this.result = {
            input: this.cache.input,
            slider: null,

            min: this.options.min,
            max: this.options.max,

            from: this.options.from,
            from_percent: 0,
            from_value: null,

            to: this.options.to,
            to_percent: 0,
            to_value: null
        };

        // HTML Templates
        this.baseHtml =
            '<span class="irs">' +
            '<span class="irs-line" tabindex="0"></span>' +
            '<span class="irs-min">0</span><span class="irs-max">1</span>' +
            '<span class="irs-from">0</span><span class="irs-to">0</span><span class="irs-single">0</span>' +
            '</span>' +
            '<span class="irs-grid">1</span>';

        this.singleHtml =
            '<span class="irs-bar irs-bar--single"></span>' +
            '<span class="irs-shadow shadow-single"></span>' +
            '<span class="irs-handle single"><i></i><i></i><i></i></span>';

        this.doubleHtml =
            '<span class="irs-bar"></span>' +
            '<span class="irs-shadow shadow-from"></span>' +
            '<span class="irs-shadow shadow-to"></span>' +
            '<span class="irs-handle from"><i></i><i></i><i></i></span>' +
            '<span class="irs-handle to"><i></i><i></i><i></i></span>';

        this.disableHtml = '<span class="irs-disable-mask"></span>';
    }

    /**
     * Инициализация
     * @param {boolean} isUpdate - обновление или первый старт 
     */
    init(isUpdate) {
        this.noDiapason = false;
        this.coords.p_step = this._convertToPercent(this.options.step, true);

        this.target = "base";

        this._toggleInput();
        this._append();
        this._setMinMax();

        if (isUpdate) {
            this.forceRedraw = true;
            this._calc(true);

            // callbacks called
            this._callOnUpdate();
        } else {
            this.forceRedraw = true;
            this._calc(true);

            // callbacks called
            this._callOnStart();
        }

        this._updateScene();
    };

    // Public methods
    update(newOptions) {
        console.log(123, 'update', newOptions)
        if (!this.input) {
            return;
        }

        this.is_update = true;

        this.options.from = this.result.from;
        this.options.to = this.result.to;
        this.update_check.from = this.result.from;
        this.update_check.to = this.result.to;

        this.options =  Object.assign(this.options, this.newOptions);
        this._validate();
        this._updateResult(newOptions);

        this._toggleInput();
        this._remove();
        this.init(true);
    };

    reset() {
        if (!this.input) {
            return;
        }

        this._updateResult();
        this.update();
    };

    destroy() {
        if (!this.input) {
            return;
        }

        this._toggleInput();
        this.input.removeAttribute("readonly");

        this._remove();
        this.input = null;
        this.options = null;
    }

    /**
     * Appends slider template to a DOM
     */
    _append () {
        const containerHtml = '<span class="irs irs--' + this.options.skin + ' ' + this.options.extra_classes + '"></span>';
        
        this.cache.input.insertAdjacentHTML('beforebegin', containerHtml);
        this.cache.input.setAttribute("readonly", "true");
        this.cache.cont = this.cache.input.previousElementSibling;
        this.result.slider = this.cache.cont;

        this.cache.cont.innerHTML = this.baseHtml;
        this.cache.rs = this.cache.cont.querySelector(".irs");
        this.cache.min = this.cache.cont.querySelector(".irs-min");
        this.cache.max = this.cache.cont.querySelector(".irs-max");
        this.cache.from = this.cache.cont.querySelector(".irs-from");
        this.cache.to = this.cache.cont.querySelector(".irs-to");
        this.cache.single = this.cache.cont.querySelector(".irs-single");
        this.cache.line = this.cache.cont.querySelector(".irs-line");
        this.cache.grid = this.cache.cont.querySelector(".irs-grid");

        if (this.options.type === "single") {
            this.cache.cont.insertAdjacentHTML('beforeend', this.singleHtml);
            this.cache.bar = this.cache.cont.querySelector(".irs-bar");
            this.cache.edge = this.cache.cont.querySelector(".irs-bar--single");
            this.cache.s_single = this.cache.cont.querySelector(".single");
            this.cache.from.style.visibility = "hidden";
            this.cache.to.style.visibility = "hidden";
            this.cache.shad_single = this.cache.cont.querySelector(".shadow-single");
        } else {
            this.cache.cont.insertAdjacentHTML('beforeend', this.doubleHtml);
            this.cache.bar = this.cache.cont.querySelector(".irs-bar");
            this.cache.s_from = this.cache.cont.querySelector(".from");
            this.cache.s_to = this.cache.cont.querySelector(".to");
            this.cache.shad_from = this.cache.cont.querySelector(".shadow-from");
            this.cache.shad_to = this.cache.cont.querySelector(".shadow-to");

            this._setTopHandler();
        }

        if (this.options.hide_from_to) {
            this.cache.from.style.display = "none";
            this.cache.to.style.display = "none";
            this.cache.single.style.display = "none";
        }

        this._appendGrid();

        if (this.options.disable) {
            this._appendDisableMask();
            this.cache.input.disabled = true;
        } else {
            this.cache.input.disabled = false;
            this._removeDisableMask();
            this._bindEvents();
        }

        // block only if not disabled
        if (!this.options.disable) {
            if (this.options.block) {
                this._appendDisableMask();
            } else {
                this._removeDisableMask();
            }
        }

        if (this.options.drag_interval) {
            this.cache.bar.style.cursor = "ew-resize";
        }
    };

    /**
     * bind all slider events
     */
    _bindEvents() {
        if (this.noDiapason) {
            return;
        }

        this.cache.body.addEventListener('touchmove', this._pointerMove.bind(this));
        this.cache.body.addEventListener('mousemove', this._pointerMove.bind(this));

        this.cache.win.addEventListener('touchend', this._pointerUp.bind(this));
        this.cache.win.addEventListener('mouseup', this._pointerUp.bind(this));

        this.cache.line.addEventListener('touchstart', this._pointerClick.bind(this, 'click'), {passive: true});
        this.cache.line.addEventListener('mousedown', this._pointerClick.bind(this, 'click'));
        this.cache.line.addEventListener('focus', this._pointerFocus.bind(this));

        if (this.options.drag_interval && options.type === "double") {
            this.cache.bar.addEventListener('touchstart', this._pointerDown.bind(this, 'both'), {passive: true});
            this.cache.bar.addEventListener('mousedown', this._pointerDown.bind(this, 'both'));
        } else {
            this.cache.bar.addEventListener('touchstart', this._pointerClick.bind(this, 'click'), {passive: true});
            this.cache.bar.addEventListener('mousedown', this._pointerClick.bind(this, 'click'));
        }

        if (this.options.type === "single") {
            this.cache.single.addEventListener('touchstart', this._pointerDown.bind(this, 'single'), {passive: true});
            this.cache.s_single.addEventListener('touchstart', this._pointerDown.bind(this, 'single'), {passive: true});
            this.cache.shad_single.addEventListener('touchstart', this._pointerClick.bind(this, 'click'), {passive: true});

            this.cache.single.addEventListener('mousedown', this._pointerDown.bind(this, 'single'));
            this.cache.s_single.addEventListener('mousedown', this._pointerDown.bind(this, 'single'));
            this.cache.edge.addEventListener('mousedown', this._pointerClick.bind(this, 'click'));
            this.cache.shad_single.addEventListener('touchstart', this._pointerClick.bind(this, 'click'), {passive: true});
        } else {
            this.cache.single.addEventListener('touchstart', this._pointerDown.bind(this, null), {passive: true});
            this.cache.single.addEventListener('mousedown', this._pointerDown.bind(this, null));

            this.cache.from.addEventListener('touchstart', this._pointerDown.bind(this, 'from'), {passive: true});
            this.cache.s_from.addEventListener('touchstart', this._pointerDown.bind(this, 'from'), {passive: true});
            this.cache.to.addEventListener('touchstart', this._pointerDown.bind(this, 'to'), {passive: true});
            this.cache.s_to.addEventListener('touchstart', this._pointerDown.bind(this, 'to'), {passive: true});
            this.cache.shad_from.addEventListener('touchstart', this._pointerClick.bind(this, 'click'), {passive: true});
            this.cache.shad_to.addEventListener('touchstart', this._pointerClick.bind(this, 'click'), {passive: true});

            this.cache.from.addEventListener('mousedown', this._pointerDown.bind(this, 'from'));
            this.cache.s_from.addEventListener('mousedown', this._pointerDown.bind(this, 'from'));
            this.cache.to.addEventListener('mousedown', this._pointerDown.bind(this, 'to'));
            this.cache.s_to.addEventListener('mousedown', this._pointerDown.bind(this, 'to'));
            this.cache.shad_from.addEventListener('mousedown', this._pointerClick.bind(this, 'click'));
            this.cache.shad_to.addEventListener('mousedown', this._pointerClick.bind(this, 'click'));
        }

        if (this.options.keyboard) {
            this.cache.line.addEventListener('keydown', this._key.bind(this, 'keyboard'));
        }
    }

    /**
     * Determine which handler has a priority (works only for double slider type)
     */
    _setTopHandler() {
        const min = this.options.min;
        const max = this.options.max;
        const from = this.options.from;
        const to = this.options.to;

        if (from > min && to === max) {
            this.cache.s_from.classList.add("type_last");
        } else if (to < max) {
            this.cache.s_to.classList.add("type_last");
        }
    }

    /**
     * Determine which handles was clicked last and which handler should have hover effect
     *
     * @param target {String}
     */
    _changeLevel(target) {
        console.log(target)
        switch (target) {
            case "single":
                this.coords.p_gap = this._toFixed(this.coords.p_pointer - this.coords.p_single_fake);
                this.cache.s_single.classList.add("state_hover");
                break;
            case "from":
                console.log(this.cache)
                this.coords.p_gap = this._toFixed(this.coords.p_pointer - this.coords.p_from_fake);
                this.cache.s_from.classList.add("state_hover", "type_last");
                this.cache.s_to.classList.remove("type_last");
                break;
            case "to":
                this.coords.p_gap = this._toFixed(this.coords.p_pointer - this.coords.p_to_fake);
                this.cache.s_to.classList.add("state_hover", "type_last");
                this.cache.s_from.classList.remove("type_last");
                break;
            case "both":
                this.coords.p_gap_left = this._toFixed(this.coords.p_pointer - this.coords.p_from_fake);
                this.coords.p_gap_right = this._toFixed(this.coords.p_to_fake - this.coords.p_pointer);
                this.cache.s_to.classList.remove("type_last");
                this.cache.s_from.classList.remove("type_last");
                break;
            default:
                break;
        }
    }

    /**
     * Then slider is disabled -> append extra layer with opacity
     */
    _appendDisableMask() {
        this.cache.cont.insertAdjacentHTML('beforeend', this.disableHtml);
        this.cache.cont.classList.add("irs-disabled");
    };

    /**
     * Then slider is not disabled -> remove disable mask
     */
    _removeDisableMask() {
        this.cache.cont.classList.remove(".irs-disable-mask");
        this.cache.cont.classList.remove("irs-disabled");
    }

    /**
     * Remove slider instance and unbind all events
     */
    _remove() {
        this.cache.cont.remove();
        this.cache.cont = null;

        this.cache.win.removeEventListener("keydown", this._key.bind(this, 'keyboard'));
        this.cache.body.removeEventListener("touchmove", this._pointerMove.bind(this));
        this.cache.body.removeEventListener("mousemove", this._pointerMove.bind(this));
        this.cache.win.removeEventListener("touchend", this._pointerUp.bind(this));
        this.cache.win.removeEventListener("mouseup", this._pointerUp.bind(this));

        this.cache.grid_labels = [];
        this.coords.big = [];
        this.coords.big_w = [];
        this.coords.big_p = [];
        this.coords.big_x = [];

        cancelAnimationFrame(this.raf_id);
    }

    /**
     * Focus with tabIndex
     *
     * @param e {Object} event object
     */
    _pointerFocus(e) {
        if (!this.target) {
            let x, $handle;

            if (this.options.type === "single") {
                $handle = this.cache.single;
            } else {
                $handle = this.cache.from;
            }

            x = $handle.getBoundingClientRect().left;
            x += ($handle.getBoundingClientRect().width / 2) - 1;
            
            this._pointerClick("single", {preventDefault: function () {}, pageX: x});
        } else {
            this.cache.line.focus();
        }
    }

    /**
     * Mousemove or touchmove (only for handlers)
     *
     * @param e {Object} event object
     */
    _pointerMove(e) {
        if (!this.dragging) {
            return;
        }

        const x = e.pageX || e.originalEvent.touches && e.originalEvent.touches[0].pageX; // TODO
        this.coords.x_pointer = x - this.coords.x_gap;
        this._calc();
    }

    /**
     * Mouseup or touchend
     * only for handlers
     *
     * @param e {Object} event object
     */
    _pointerUp(e) {
        if (this.is_active) {
            this.is_active = false;
        } else {
            return;
        }

        const hoverState = this.cache.cont.querySelector(".state_hover");

        if (hoverState) {
            hoverState.classList.remove("state_hover");
        }

        this.forceRedraw = true;

        this._updateScene();
        this._restoreOriginalMinInterval();
        // callbacks call
        if (this.cache.cont.contains(e.target) || this.dragging) {
            this._callOnFinish();
        }

        this.dragging = false;
    };

    /**
     * Mousedown or touchstart
     * only for handlers
     *
     * @param destination {String|null}
     * @param e {Object} event object
     */
    _pointerDown(destination, e) {
        e.preventDefault();
        const x = e.pageX || e.originalEvent.touches && e.originalEvent.touches[0].pageX; // TODO
        if (e.button === 2) {
            return;
        }

        if (destination === "both") {
            this._setTempMinInterval();
        }

        if (!destination) {
            destination = this.target || "from";
        }

        this.target = destination;

        this.is_active = true;
        this.dragging = true;

        this.coords.x_gap = this.cache.rs.getBoundingClientRect().left;
        this.coords.x_pointer = x - this.coords.x_gap;

        this._calcPointerPercent();
        this._changeLevel(destination);

        this.cache.line.dispatchEvent(new Event("focus"));

        this._updateScene();
    };

    /**
     * Mousedown or touchstart
     * for other slider elements, like diapason line
     *
     * @param destination {String}
     * @param e {Object} event object
     */
    _pointerClick(destination, e) {
        e.preventDefault();
        const x = e.pageX || e.originalEvent.touches && e.originalEvent.touches[0].pageX; // TODO
        if (e.button === 2) {
            return;
        }

        this.target = destination;

        this.is_click = true;
        this.coords.x_gap = this.cache.rs.getBoundingClientRect().left;
        this.coords.x_pointer = +(x - this.coords.x_gap).toFixed();

        this.forceRedraw = true;
        this._calc();

        this.cache.line.dispatchEvent(new Event("focus"));
    };

    /**
     * Keyboard controls for focused slider
     *
     * @param destination {String}
     * @param e {Object} event object
     * @returns {boolean|undefined}
     */
    _key(destination, e) {
        if (e.altKey || e.ctrlKey || e.shiftKey || e.metaKey) {
            return;
        }

        switch (e.which) {
            case 83: // W
            case 65: // A
            case 40: // DOWN
            case 37: // LEFT
                e.preventDefault();
                this._moveByKey(false);
                break;

            case 87: // S
            case 68: // D
            case 38: // UP
            case 39: // RIGHT
                e.preventDefault();
                this._moveByKey(true);
                break;
        }
    };

    /**
     * Move by key
     *
     * @param right {boolean} direction to move
     */
    _moveByKey(right) {
        let p = this.coords.p_pointer;
        const p_step = this.options.step / ((this.options.max - this.options.min) / 100);

        right ? p += p_step : p -= p_step;

        this.coords.x_pointer = this._toFixed(this.coords.w_rs / 100 * p);
        this.is_key = true;
        this._calc();
    };

    /**
     * Set visibility and content
     * of Min and Max labels
     */
    _setMinMax() {
        if (!this.options) {
            return;
        }

        if (this.options.hide_min_max) {
            this.cache.min.style.display = "none";
            this.cache.max.style.display = "none";
            return;
        }

        if (this.options.values.length) {
            this.cache.min.innerHTML = this._decorate(this.options.p_values[options.min]);
            this.cache.max.innerHTML = this._decorate(this.options.p_values[options.max]);
        } else {
            const min_pretty = this._prettifyNum(this.options.min);
            const max_pretty = this._prettifyNum(this.options.max);

            this.result.min_pretty = min_pretty;
            this.result.max_pretty = max_pretty;

            this.cache.min.innerHTML = this._decorate(min_pretty, this.options.min);
            this.cache.max.innerHTML = this._decorate(max_pretty, this.options.max);
        }

        this.labels.w_min = this.cache.min.offsetWidth;
        this.labels.w_max = this.cache.max.offsetWidth;
    };

    /**
     * Then dragging interval, prevent interval collapsing
     * using min_interval option
     */
    _setTempMinInterval() {
        const interval = this.result.to - this.result.from;

        if (this.old_min_interval === null) {
            this.old_min_interval = this.options.min_interval;
        }

        this.options.min_interval = interval;
    };

    _restoreOriginalMinInterval() {
        if (this.old_min_interval !== null) {
            this.options.min_interval = this.old_min_interval;
            this.old_min_interval = null;
        }
    };

    // Calculations

    /**
     * All calculations and measures start here
     *
     * @param update {boolean=}
     */
    _calc(update) {
        if (!this.options) {
            return;
        }

        this.calc_count++;

        if (this.calc_count === 10 || update) {
            this.calc_count = 0;
            this.coords.w_rs = this.cache.rs.offsetWidth;

            this._calcHandlePercent();
        }

        if (!this.coords.w_rs) {
            return;
        }

        this._calcPointerPercent();
        let handle_x = this._getHandleX();


        if (this.target === "both") {
            this.coords.p_gap = 0;
            handle_x = this._getHandleX();
        }

        if (this.target === "click") {
            this.coords.p_gap = this.coords.p_handle / 2;
            handle_x = this._getHandleX();

            if (this.options.drag_interval) {
                this.target = "both_one";
            } else {
                this.target = this._chooseHandle(handle_x);
            }
        }

        switch (this.target) {
            case "base":
                const w = (this.options.max - this.options.min) / 100,
                    f = (this.result.from - this.options.min) / w,
                    t = (this.result.to - this.options.min) / w;

                this.coords.p_single_real = this._toFixed(f);
                this.coords.p_from_real = this._toFixed(f);
                this.coords.p_to_real = this._toFixed(t);

                this.coords.p_single_real = this._checkDiapason(this.coords.p_single_real, this.options.from_min, this.options.from_max);
                this.coords.p_from_real = this._checkDiapason(this.coords.p_from_real, this.options.from_min, this.options.from_max);
                this.coords.p_to_real = this._checkDiapason(this.coords.p_to_real, this.options.to_min, this.options.to_max);

                this.coords.p_single_fake = this._convertToFakePercent(this.coords.p_single_real);
                this.coords.p_from_fake = this._convertToFakePercent(this.coords.p_from_real);
                this.coords.p_to_fake = this._convertToFakePercent(this.coords.p_to_real);

                this.target = null;

                break;

            case "single":
                if (this.options.from_fixed) {
                    break;
                }

                this.coords.p_single_real = this._convertToRealPercent(handle_x);
                this.coords.p_single_real = this._calcWithStep(this.coords.p_single_real);
                this.coords.p_single_real = this._checkDiapason(this.coords.p_single_real, this.options.from_min, this.options.from_max);

                this.coords.p_single_fake = this._convertToFakePercent(this.coords.p_single_real);

                break;

            case "from":
                if (this.options.from_fixed) {
                    break;
                }

                this.coords.p_from_real = this._convertToRealPercent(handle_x);
                this.coords.p_from_real = this._calcWithStep(this.coords.p_from_real);
                if (this.coords.p_from_real > this.coords.p_to_real) {
                    this.coords.p_from_real = this.coords.p_to_real;
                }
                this.coords.p_from_real = this._checkDiapason(this.coords.p_from_real, this.options.from_min, this.options.from_max);
                this.coords.p_from_real = this._checkMinInterval(this.coords.p_from_real, this.coords.p_to_real, "from");
                this.coords.p_from_real = this._checkMaxInterval(this.coords.p_from_real, this.coords.p_to_real, "from");

                this.coords.p_from_fake = this._convertToFakePercent(this.coords.p_from_real);

                break;

            case "to":
                if (this.options.to_fixed) {
                    break;
                }

                this.coords.p_to_real = this._convertToRealPercent(handle_x);
                this.coords.p_to_real = this._calcWithStep(this.coords.p_to_real);
                if (this.coords.p_to_real < this.coords.p_from_real) {
                    this.coords.p_to_real = this.coords.p_from_real;
                }
                this.coords.p_to_real = this._checkDiapason(this.coords.p_to_real, this.options.to_min, this.options.to_max);
                this.coords.p_to_real = this._checkMinInterval(this.coords.p_to_real, this.coords.p_from_real, "to");
                this.coords.p_to_real = this._checkMaxInterval(this.coords.p_to_real, this.coords.p_from_real, "to");

                this.coords.p_to_fake = this._convertToFakePercent(this.coords.p_to_real);

                break;

            case "both":
                if (this.options.from_fixed || this.options.to_fixed) {
                    break;
                }

                handle_x = this._toFixed(handle_x + (this.coords.p_handle * 0.001));

                this.coords.p_from_real = this._convertToRealPercent(handle_x) - this.coords.p_gap_left;
                this.coords.p_from_real = this._calcWithStep(this.coords.p_from_real);
                this.coords.p_from_real = this._checkDiapason(this.coords.p_from_real, this.options.from_min, this.options.from_max);
                this.coords.p_from_real = this._checkMinInterval(this.coords.p_from_real, this.coords.p_to_real, "from");
                this.coords.p_from_fake = this._convertToFakePercent(this.coords.p_from_real);

                this.coords.p_to_real = this._convertToRealPercent(handle_x) + this.coords.p_gap_right;
                this.coords.p_to_real = this._calcWithStep(this.coords.p_to_real);
                this.coords.p_to_real = this._checkDiapason(this.coords.p_to_real, this.options.to_min, this.options.to_max);
                this.coords.p_to_real = this._checkMinInterval(this.coords.p_to_real, this.coords.p_from_real, "to");
                this.coords.p_to_fake = this._convertToFakePercent(this.coords.p_to_real);

                break;

            case "both_one":
                if (this.options.from_fixed || this.options.to_fixed) {
                    break;
                }

                const real_x = this._convertToRealPercent(handle_x),
                    from = this.result.from_percent,
                    to = this.result.to_percent,
                    full = to - from,
                    half = full / 2;
                let new_from = real_x - half,
                    new_to = real_x + half;

                if (new_from < 0) {
                    new_from = 0;
                    new_to = new_from + full;
                }

                if (new_to > 100) {
                    new_to = 100;
                    new_from = new_to - full;
                }

                this.coords.p_from_real = this._calcWithStep(new_from);
                this.coords.p_from_real = this._checkDiapason(this.coords.p_from_real, this.options.from_min, this.options.from_max);
                this.coords.p_from_fake = this._convertToFakePercent(this.coords.p_from_real);

                this.coords.p_to_real = this._calcWithStep(new_to);
                this.coords.p_to_real = this._checkDiapason(this.coords.p_to_real, this.options.to_min, this.options.to_max);
                this.coords.p_to_fake = this._convertToFakePercent(this.coords.p_to_real);

                break;
        }

        if (this.options.type === "single") {
            this.coords.p_bar_x = (this.coords.p_handle / 2);
            this.coords.p_bar_w = this.coords.p_single_fake;

            this.result.from_percent = this.coords.p_single_real;
            this.result.from = this._convertToValue(this.coords.p_single_real);
            this.result.from_pretty = this._prettifyNum(this.result.from);

            if (this.options.values.length) {
                this.result.from_value = this.options.values[this.result.from];
            }
        } else {
            this.coords.p_bar_x = this._toFixed(this.coords.p_from_fake + (this.coords.p_handle / 2));
            this.coords.p_bar_w = this._toFixed(this.coords.p_to_fake - this.coords.p_from_fake);
            this.result.from_percent = this.coords.p_from_real;
            this.result.from = this._convertToValue(this.coords.p_from_real);
            this.result.from_pretty = this._prettifyNum(this.result.from);
            this.result.to_percent = this.coords.p_to_real;
            this.result.to = this._convertToValue(this.coords.p_to_real);
            this.result.to_pretty = this._prettifyNum(this.result.to);

            if (this.options.values.length) {
                this.result.from_value = this.options.values[this.result.from];
                this.result.to_value = this.options.values[this.result.to];
            }
        }

        this._calcMinMax();
        this._calcLabels();
    };

    /**
     * calculates pointer X in percent
     */
    _calcPointerPercent() {
        if (!this.coords.w_rs) {
            this.coords.p_pointer = 0;
            return;
        }

        if (this.coords.x_pointer < 0 || isNaN(this.coords.x_pointer)) {
            this.coords.x_pointer = 0;
        } else if (this.coords.x_pointer > this.coords.w_rs) {
            this.coords.x_pointer = this.coords.w_rs;
        }

        this.coords.p_pointer = this._toFixed(this.coords.x_pointer / this.coords.w_rs * 100);
    };

     // TODO refactor next 2 functions
    _convertToRealPercent(fake) {
        const full = 100 - this.coords.p_handle;
        return fake / full * 100;
    };

    _convertToFakePercent(real) {
        const full = 100 - this.coords.p_handle;
        return real / 100 * full;
    };

    _getHandleX() {
        const max = 100 - this.coords.p_handle;
        let x = this._toFixed(this.coords.p_pointer - this.coords.p_gap);

        if (x < 0) {
            x = 0;
        } else if (x > max) {
            x = max;
        }

        return x;
    };

    _calcHandlePercent() {
        if (this.options.type === "single") {
            this.coords.w_handle = this.cache.s_single.offsetWidth;
        } else {
            this.coords.w_handle = this.cache.s_from.offsetWidth;
        }

        this.coords.p_handle = this._toFixed(this.coords.w_handle / this.coords.w_rs * 100);
    };

    /**
     * Find closest handle to pointer click
     *
     * @param real_x {Number}
     * @returns {String}
     */
    _chooseHandle(real_x) {
        if (this.options.type === "single") {
            return "single";
        } else {
            const m_point = this.coords.p_from_real + ((this.coords.p_to_real - this.coords.p_from_real) / 2);
            if (real_x >= m_point) {
                return this.options.to_fixed ? "from" : "to";
            } else {
                return this.options.from_fixed ? "to" : "from";
            }
        }
    };

    /**
     * Measure Min and Max labels width in percent
     */
    _calcMinMax() {
        if (!this.coords.w_rs) {
            return;
        }

        this.labels.p_min = this.labels.w_min / this.coords.w_rs * 100;
        this.labels.p_max = this.labels.w_max / this.coords.w_rs * 100;
    };

    /**
     * Measure labels width and X in percent
     */
    _calcLabels() {
        if (!this.coords.w_rs || this.options.hide_from_to) {
            return;
        }

        if (this.options.type === "single") {
            this.labels.w_single = this.cache.single.offsetWidth;
            this.labels.p_single_fake = this.labels.w_single / this.coords.w_rs * 100;
            this.labels.p_single_left = this.coords.p_single_fake + (this.coords.p_handle / 2) - (this.labels.p_single_fake / 2);
            this.labels.p_single_left = this._checkEdges(this.labels.p_single_left, this.labels.p_single_fake);

        } else {
            this.labels.w_from = this.cache.from.offsetWidth;
            this.labels.p_from_fake = this.labels.w_from / this.coords.w_rs * 100;
            this.labels.p_from_left = this.coords.p_from_fake + (this.coords.p_handle / 2) - (this.labels.p_from_fake / 2);
            this.labels.p_from_left = this._toFixed(this.labels.p_from_left);
            this.labels.p_from_left = this._checkEdges(this.labels.p_from_left, this.labels.p_from_fake);

            this.labels.w_to = this.cache.to.offsetWidth;
            this.labels.p_to_fake = this.labels.w_to / this.coords.w_rs * 100;
            this.labels.p_to_left = this.coords.p_to_fake + (this.coords.p_handle / 2) - (this.labels.p_to_fake / 2);
            this.labels.p_to_left = this._toFixed(this.labels.p_to_left);
            this.labels.p_to_left = this._checkEdges(this.labels.p_to_left, this.labels.p_to_fake);

            this.labels.w_single = this.cache.single.offsetWidth;
            this.labels.p_single_fake = this.labels.w_single / this.coords.w_rs * 100;
            this.labels.p_single_left = ((this.labels.p_from_left + this.labels.p_to_left + this.labels.p_to_fake) / 2) - (this.labels.p_single_fake / 2);
            this.labels.p_single_left = this._toFixed(this.labels.p_single_left);
            this.labels.p_single_left = this._checkEdges(this.labels.p_single_left, this.labels.p_single_fake);

        }
    };


     // Drawings

    /**
     * Main function called in request animation frame
     * to update everything
     */
    _updateScene() {
        console.log(12, this.raf_id);
        if (this.raf_id) {
            cancelAnimationFrame(this.raf_id);
            this.raf_id = null;
        }

        clearTimeout(this.update_tm);
        this.update_tm = null;

        if (!this.options) {
            return;
        }

        this._drawHandles();

        if (this.is_active) {
            this.raf_id = requestAnimationFrame(this._updateScene);
        } else {
            this.update_tm = setTimeout(this._updateScene, 300);
        }
    };

    /**
     * Draw handles
     */
    _drawHandles() {
        this.coords.w_rs = this.cache.rs.offsetWidth;
        if (!this.coords.w_rs) {
            return;
        }

        if (this.coords.w_rs !== this.coords.w_rs_old) {
            this.target = "base";
            this.is_resize = true;
        }

        if (this.coords.w_rs !== this.coords.w_rs_old || this.forceRedraw) {
            this._setMinMax();
            this._calc(true);
            this._drawLabels();

            if (this.options.grid) {
                this._calcGridMargin();
                this._calcGridLabels();
            }

            this.forceRedraw = true;
            this.coords.w_rs_old = this.coords.w_rs;
            this._drawShadow();
        }

        if (!this.coords.w_rs) {
            return;
        }

        if (!this.dragging && !this.forceRedraw && !this.is_key) {
            return;
        }

        if (this.old_from !== this.result.from || this.old_to !== this.result.to || this.forceRedraw || this.is_key) {
            this._drawLabels();

            this.cache.bar.style.left = this.coords.p_bar_x + "%";
            this.cache.bar.style.width = this.coords.p_bar_w + "%";

            if (this.options.type === "single") {
                this.cache.bar.style.left = "0";
                this.cache.bar.style.width = this.coords.p_bar_w + this.coords.p_bar_x + "%";

                this.cache.s_single.style.left = this.coords.p_single_fake + "%";

                this.cache.single.style.left = this.labels.p_single_left + "%";
            } else {
                this.cache.s_from.style.left = this.coords.p_from_fake + "%";
                this.cache.s_to.style.left = this.coords.p_to_fake + "%";

                if (this.old_from !== this.result.from || this.forceRedraw) {
                    this.cache.from.style.left = this.labels.p_from_left + "%";
                }
                if (this.old_to !== this.result.to || this.forceRedraw) {
                    this.cache.to.style.left = this.labels.p_to_left + "%";
                }

                this.cache.single.style.left = this.labels.p_single_left + "%";
            }

            this._writeToInput();

            if ((this.old_from !== this.result.from || this.old_to !== this.result.to) && !this.is_start) {
                this.cache.input.dispatchEvent(new Event("change"));
                this.cache.input.dispatchEvent(new Event("input"));
            }

            this.old_from = this.result.from;
            this.old_to = this.result.to;

            // callbacks call
            if (!this.is_resize && !this.is_update && !this.is_start && !this.is_finish) {
                this._callOnChange();
            }
            if (this.is_key || this.is_click) {
                this.is_key = false;
                this.is_click = false;
                this._callOnFinish();
            }

            this.is_update = false;
            this.is_resize = false;
            this.is_finish = false;
        }

        this.is_start = false;
        this.is_key = false;
        this.is_click = false;
        this.forceRedraw = false;
    };


     /**
     * Draw labels
     * measure labels collisions
     * collapse close labels
     */
    _drawLabels() {
        if (!this.options) {
            return;
        }

        const values_num = this.options.values.length,
            p_values = this.options.p_values;
        let text_single,
            text_from,
            text_to,
            from_pretty,
            to_pretty;

        if (this.options.hide_from_to) {
            return;
        }

        if (this.options.type === "single") {

            if (values_num) {
                text_single = this._decorate(p_values[this.result.from]);
                this.cache.single.innerHTML = text_single;
            } else {
                from_pretty = this._prettifyNum(this.result.from);

                text_single = this._decorate(from_pretty, this.result.from);
                this.cache.single.innerHTML = text_single;
            }

            this._calcLabels();

            if (this.labels.p_single_left < this.labels.p_min + 1) {
                this.cache.min.style.visibility = "hidden";
            } else {
                this.cache.min.style.visibility = "visible";
            }

            if (this.labels.p_single_left + this.labels.p_single_fake > 100 - this.labels.p_max - 1) {
                this.cache.max.style.visibility = "hidden";
            } else {
                this.cache.max.style.visibility = "visible";
            }

        } else {
            if (values_num) {

                if (this.options.decorate_both) {
                    text_single = this._decorate(p_values[this.result.from]);
                    text_single += this.options.values_separator;
                    text_single += this._decorate(p_values[this.result.to]);
                } else {
                    text_single = this._decorate(p_values[this.result.from] + this.options.values_separator + p_values[this.result.to]);
                }
                text_from = this._decorate(p_values[this.result.from]);
                text_to = this._decorate(p_values[this.result.to]);
                this.cache.single.innerHTML = text_single;
                this.cache.from.innerHTML = text_from;
                this.cache.to.innerHTML = text_to;

            } else {
                from_pretty = this._prettifyNum(this.result.from);
                to_pretty = this._prettifyNum(this.result.to);

                if (this.options.decorate_both) {
                    text_single = this._decorate(from_pretty, this.result.from);
                    text_single += this.options.values_separator;
                    text_single += this._decorate(to_pretty, this.result.to);
                } else {
                    text_single = this._decorate(from_pretty + this.options.values_separator + to_pretty, this.result.to);
                }
                text_from = this._decorate(from_pretty, this.result.from);
                text_to = this._decorate(to_pretty, this.result.to);

                this.cache.single.innerHTML = text_single;
                this.cache.from.innerHTML = text_from;
                this.cache.to.innerHTML = text_to;

            }

            this._calcLabels();

            const min = Math.min(this.labels.p_single_left, this.labels.p_from_left),
                single_left = this.labels.p_single_left + this.labels.p_single_fake,
                to_left = this.labels.p_to_left + this.labels.p_to_fake;
            let max = Math.max(single_left, to_left);

            if (this.labels.p_from_left + this.labels.p_from_fake >= this.labels.p_to_left) {
                this.cache.from.style.visibility = "hidden";
                this.cache.to.style.visibility = "hidden";
                this.cache.single.style.visibility = "visible";

                if (this.result.from === this.result.to) {
                    if (this.target === "from") {
                        this.cache.from.style.visibility = "visible";
                    } else if (this.target === "to") {
                        this.cache.to.style.visibility = "visible";
                    } else if (!this.target) {
                        this.cache.from.style.visibility = "visible";
                    }
                    this.cache.single.style.visibility = "hidden";
                    max = to_left;
                } else {
                    this.cache.from.style.visibility = "hidden";
                    this.cache.to.style.visibility = "hidden";
                    this.cache.single.style.visibility = "visible";
                    max = Math.max(single_left, to_left);
                }
            } else {
                this.cache.from.style.visibility = "visible";
                this.cache.to.style.visibility = "visible";
                this.cache.single.style.visibility = "hidden";
            }

            min < this.labels.p_min + 1 ? this.cache.min.style.visibility = "hidden" : this.cache.min.style.visibility = "visible";
            max > 100 - this.labels.p_max - 1 ? this.cache.max.style.visibility = "hidden" : this.cache.max.style.visibility = "visible";
        }
    };

     /**
     * Draw shadow intervals
     */
    _drawShadow() {
        const o = this.options,
            c = this.cache,

            is_from_min = typeof o.from_min === "number" && !isNaN(o.from_min),
            is_from_max = typeof o.from_max === "number" && !isNaN(o.from_max),
            is_to_min = typeof o.to_min === "number" && !isNaN(o.to_min),
            is_to_max = typeof o.to_max === "number" && !isNaN(o.to_max);

        let from_min,
            from_max,
            to_min,
            to_max;

        if (o.type === "single") {
            if (o.from_shadow && (is_from_min || is_from_max)) {
                from_min = this._convertToPercent(is_from_min ? o.from_min : o.min);
                from_max = this._convertToPercent(is_from_max ? o.from_max : o.max) - from_min;
                from_min = this._toFixed(from_min - (this.coords.p_handle / 100 * from_min));
                from_max = this._toFixed(from_max - (this.coords.p_handle / 100 * from_max));
                from_min = from_min + (this.coords.p_handle / 2);

                c.shad_single.style.display = "block";
                c.shad_single.style.left = from_min + "%";
                c.shad_single.style.width = from_max + "%";
            } else {
                c.shad_single.style.display = "none";
            }
        } else {
            if (o.from_shadow && (is_from_min || is_from_max)) {
                from_min = this._convertToPercent(is_from_min ? o.from_min : o.min);
                from_max = this._convertToPercent(is_from_max ? o.from_max : o.max) - from_min;
                from_min = this._toFixed(from_min - (this.coords.p_handle / 100 * from_min));
                from_max = this._toFixed(from_max - (this.coords.p_handle / 100 * from_max));
                from_min = from_min + (this.coords.p_handle / 2);

                c.shad_from.style.display = "block";
                c.shad_from.style.left = from_min + "%";
                c.shad_from.style.width = from_max + "%";
            } else {
                c.shad_from.style.display = "none";
            }

            if (o.to_shadow && (is_to_min || is_to_max)) {
                to_min = this._convertToPercent(is_to_min ? o.to_min : o.min);
                to_max = this._convertToPercent(is_to_max ? o.to_max : o.max) - to_min;
                to_min = this._toFixed(to_min - (this.coords.p_handle / 100 * to_min));
                to_max = this._toFixed(to_max - (this.coords.p_handle / 100 * to_max));
                to_min = to_min + (this.coords.p_handle / 2);

                c.shad_to.style.display = "block";
                c.shad_to.style.left = to_min + "%";
                c.shad_to.style.width = to_max + "%";
            } else {
                c.shad_to.style.display = "none";
            }
        }
    };

     /**
     * Write values to input element
     */
    _writeToInput() {
        if (this.options.type === "single") {
            if (this.options.values.length) {
                this.cache.input.setAttribute("value", this.result.from_value);
            } else {
                this.cache.input.setAttribute("value", this.result.from);
            }
            this.cache.input.dataset.from = this.result.from;
        } else {
            if (this.options.values.length) {
                this.cache.input.setAttribute("value", this.result.from_value + this.options.input_values_separator + this.result.to_value);
            } else {
                this.cache.input.setAttribute("value", this.result.from + this.options.input_values_separator + this.result.to);
            }
            this.cache.input.dataset.from = this.result.from;
            this.cache.input.dataset.to = this.result.to;
        }
    };

    // Callbacks

    _callOnStart() {
        this._writeToInput();

        if (this.options.onStart && typeof this.options.onStart === "function") {
            if (this.options.scope) {
                this.options.onStart.call(this.options.scope, this.result);
            } else {
                this.options.onStart(this.result);
            }
        }
    };

    _callOnChange() {
        this._writeToInput();

        if (this.options.onChange && typeof this.options.onChange === "function") {
            if (this.options.scope) {
                this.options.onChange.call(this.options.scope, this.result);
            } else {
                this.options.onChange(this.result);
            }
        }
    };
    
    _callOnFinish() {
        this._writeToInput();

        if (this.options.onFinish && typeof this.options.onFinish === "function") {
            if (this.options.scope) {
                this.options.onFinish.call(this.options.scope, this.result);
            } else {
                this.options.onFinish(this.result);
            }
        }
    };

    _callOnUpdate() {
        this._writeToInput();

        if (this.options.onUpdate && typeof this.options.onUpdate === "function") {
            if (this.options.scope) {
                this.options.onUpdate.call(this.options.scope, this.result);
            } else {
                this.options.onUpdate(this.result);
            }
        }
    };

    // Service methods

    _toggleInput() {
        this.cache.input.classList.toggle("irs-hidden-input");

        if (this.has_tab_index) {
            this.cache.input.setAttribute("tabindex", "-1");
        } else {
            this.cache.input.removeAttribute("tabindex");
        }

        this.has_tab_index = !this.has_tab_index;
    };

    /**
     * Convert real value to percent
     *
     * @param value {Number} X in real
     * @param no_min {boolean=} don't use min value
     * @returns {Number} X in percent
     */
    _convertToPercent(value, no_min) {
        let diapason = this.options.max - this.options.min,
            one_percent = diapason / 100,
            val, percent;

        if (!diapason) {
            this.noDiapason = true;
            return 0;
        }

        if (no_min) {
            val = value;
        } else {
            val = value - this.options.min;
        }

        percent = val / one_percent;

        return this._toFixed(percent);
    };

    /**
     * Convert percent to real values
     *
     * @param percent {Number} X in percent
     * @returns {Number} X in real
     */
    _convertToValue(percent) {
        let min = this.options.min,
            max = this.options.max,
            min_decimals = min.toString().split(".")[1],
            max_decimals = max.toString().split(".")[1],
            min_length, max_length,
            avg_decimals = 0,
            abs = 0;

        if (percent === 0) {
            return this.options.min;
        }
        if (percent === 100) {
            return this.options.max;
        }


        if (min_decimals) {
            min_length = min_decimals.length;
            avg_decimals = min_length;
        }
        if (max_decimals) {
            max_length = max_decimals.length;
            avg_decimals = max_length;
        }
        if (min_length && max_length) {
            avg_decimals = (min_length >= max_length) ? min_length : max_length;
        }

        if (min < 0) {
            abs = Math.abs(min);
            min = +(min + abs).toFixed(avg_decimals);
            max = +(max + abs).toFixed(avg_decimals);
        }

        let number = ((max - min) / 100 * percent) + min,
            string = this.options.step.toString().split(".")[1],
            result;

        if (string) {
            number = +number.toFixed(string.length);
        } else {
            number = number / this.options.step;
            number = number * this.options.step;

            number = +number.toFixed(0);
        }

        if (abs) {
            number -= abs;
        }

        string ? result = +number.toFixed(string.length) : result = this._toFixed(number);

        if (result < this.options.min) {
            result = this.options.min;
        } else if (result > this.options.max) {
            result = this.options.max;
        }

        return result;
    };


    /**
     * Round percent value with step
     *
     * @param percent {Number}
     * @returns percent {Number} rounded
     */
    _calcWithStep(percent) {
        let rounded = Math.round(percent / this.coords.p_step) * this.coords.p_step;

        if (rounded > 100) {
            rounded = 100;
        }
        if (percent === 100) {
            rounded = 100;
        }

        return this._toFixed(rounded);
    };

    _checkMinInterval(p_current, p_next, type) {
        let o = this.options, current, next;

        if (!o.min_interval) {
            return p_current;
        }

        current = this._convertToValue(p_current);
        next = this._convertToValue(p_next);

        if (type === "from") {
            if (next - current < o.min_interval) {
                current = next - o.min_interval;
            }
        } else {
            if (current - next < o.min_interval) {
                current = next + o.min_interval;
            }
        }

        return this._convertToPercent(current);
    };

    _checkMaxInterval(p_current, p_next, type) {
        let o = this.options, current, next;

        if (!o.max_interval) {
            return p_current;
        }

        current = this._convertToValue(p_current);
        next = this._convertToValue(p_next);

        if (type === "from") {
            if (next - current > o.max_interval) {
                current = next - o.max_interval;
            }
        } else {
            if (current - next > o.max_interval) {
                current = next + o.max_interval;
            }
        }

        return this._convertToPercent(current);
    };

    _checkDiapason(p_num, min, max) {
        let num = this._convertToValue(p_num);

        typeof min !== 'number' ? min = this.options.min : min;
        typeof max !== 'number' ? max = this.options.max : max;

        num < min ? num = min : num;
        num > max ? num = max : num;

        return this._convertToPercent(num);
    };

    _toFixed(num) {
        num = num.toFixed(20);
        return +num;
    };

    _prettifyNum(num) {
        if (!this.options.prettify_enabled) {
            return num;
        }

        if (this.options.prettify && typeof this.options.prettify === "function") {
            return this.options.prettify(num);
        } else {
            return this._prettify(num);
        }
    };

    _prettify(num) {
        return num.toString().replace(/(\d{1,3}(?=(?:\d\d\d)+(?!\d)))/g, "$1" + this.options.prettify_separator);
    };

    _checkEdges(left, width) {
        if (!this.options.force_edges) return this._toFixed(left);

        left < 0 ? left = 0 : left > 100 - width ? left = 100 - width : left;

        return this._toFixed(left);
    };

    _validate() {
        let o = this.options,
            r = this.result,
            v = o.values,
            vl = v.length,
            value,
            i;

        if (typeof o.min === "string") o.min = +o.min;
        if (typeof o.max === "string") o.max = +o.max;
        if (typeof o.from === "string") o.from = +o.from;
        if (typeof o.to === "string") o.to = +o.to;
        if (typeof o.step === "string") o.step = +o.step;

        if (typeof o.from_min === "string") o.from_min = +o.from_min;
        if (typeof o.from_max === "string") o.from_max = +o.from_max;
        if (typeof o.to_min === "string") o.to_min = +o.to_min;
        if (typeof o.to_max === "string") o.to_max = +o.to_max;

        if (typeof o.grid_num === "string") o.grid_num = +o.grid_num;

        if (o.max < o.min) {
            o.max = o.min;
        }

        if (vl) {
            o.p_values = [];
            o.min = 0;
            o.max = vl - 1;
            o.step = 1;
            o.grid_num = o.max;
            o.grid_snap = true;

            for (i = 0; i < vl; i++) {
                value = +v[i];

                if (!isNaN(value)) {
                    v[i] = value;
                    value = this._prettifyNum(value);
                } else {
                    value = v[i];
                }

                o.p_values.push(value);
            }
        }

        if (typeof o.from !== "number" || isNaN(o.from)) o.from = o.min;
        if (typeof o.to !== "number" || isNaN(o.to)) o.to = o.max;

        if (o.type === "single") {
            if (o.from < o.min) o.from = o.min;
            if (o.from > o.max) o.from = o.max;
        } else {
            if (o.from < o.min) o.from = o.min;
            if (o.from > o.max) o.from = o.max;

            if (o.to < o.min) o.to = o.min;
            if (o.to > o.max) o.to = o.max;

            if (this.update_check.from) {

                if (this.update_check.from !== o.from) {
                    if (o.from > o.to) o.from = o.to;
                }
                if (this.update_check.to !== o.to) {
                    if (o.to < o.from) o.to = o.from;
                }

            }

            if (o.from > o.to) o.from = o.to;
            if (o.to < o.from) o.to = o.from;

        }

        if (typeof o.step !== "number" || isNaN(o.step) || !o.step || o.step < 0) {
            o.step = 1;
        }

        if (typeof o.from_min === "number" && o.from < o.from_min) {
            o.from = o.from_min;
        }

        if (typeof o.from_max === "number" && o.from > o.from_max) {
            o.from = o.from_max;
        }

        if (typeof o.to_min === "number" && o.to < o.to_min) {
            o.to = o.to_min;
        }

        if (typeof o.to_max === "number" && o.from > o.to_max) {
            o.to = o.to_max;
        }

        if (r) {
            if (r.min !== o.min) r.min = o.min;
            if (r.max !== o.max) r.max = o.max;
            if (r.from < r.min || r.from > r.max) r.from = o.from;
            if (r.to < r.min || r.to > r.max) r.to = o.to;
        }

        if (typeof o.min_interval !== "number" || isNaN(o.min_interval) || !o.min_interval || o.min_interval < 0) {
            o.min_interval = 0;
        }

        if (typeof o.max_interval !== "number" || isNaN(o.max_interval) || !o.max_interval || o.max_interval < 0) {
            o.max_interval = 0;
        }

        if (o.min_interval && o.min_interval > o.max - o.min) {
            o.min_interval = o.max - o.min;
        }

        if (o.max_interval && o.max_interval > o.max - o.min) {
            o.max_interval = o.max - o.min;
        }
    };

    _decorate(num, original) {
        let decorated = "",
            o = this.options;

        if (o.prefix) {
            decorated += o.prefix;
        }

        decorated += num;

        if (o.max_postfix) {
            if (o.values.length && num === o.p_values[o.max]) {
                decorated += o.max_postfix;
                if (o.postfix) {
                    decorated += " ";
                }
            } else if (original === o.max) {
                decorated += o.max_postfix;
                if (o.postfix) {
                    decorated += " ";
                }
            }
        }

        if (o.postfix) {
            decorated += o.postfix;
        }

        return decorated;
    };

    _updateFrom() {
        this.result.from = this.options.from;
        this.result.from_percent = this._convertToPercent(this.result.from);
        this.result.from_pretty = this._prettifyNum(this.result.from);
        
        if (this.options.values) {
            this.result.from_value = this.options.values[this.result.from];
        }
    };

    _updateTo() {
        this.result.to = this.options.to;
        this.result.to_percent = this._convertToPercent(this.result.to);
        this.result.to_pretty = this._prettifyNum(this.result.to);
        
        if (this.options.values) {
            this.result.to_value = this.options.values[this.result.to];
        }
    };

    _updateResult() {
        this.result.min = this.options.min;
        this.result.max = this.options.max;
        this._updateFrom();
        this._updateTo();
    };

     // Grid

    _appendGrid() {
        if (!this.options.grid) {
            return;
        }

        let o = this.options,
            i, z,

            total = o.max - o.min,
            big_num = o.grid_num,
            big_p = 0,
            big_w = 0,

            small_max = 4,
            local_small_max,
            small_p,
            small_w = 0,

            result,
            html = '';


        this._calcGridMargin();

        if (o.grid_snap) {
            big_num = total / o.step;
        }

        if (big_num > 50) big_num = 50;
        big_p = this._toFixed(100 / big_num);

        if (big_num > 4) {
            small_max = 3;
        }
        if (big_num > 7) {
            small_max = 2;
        }
        if (big_num > 14) {
            small_max = 1;
        }
        if (big_num > 28) {
            small_max = 0;
        }

        for (i = 0; i < big_num + 1; i++) {
            local_small_max = small_max;

            big_w = this._toFixed(big_p * i);

            if (big_w > 100) {
                big_w = 100;
            }

            this.coords.big[i] = big_w;

            small_p = (big_w - (big_p * (i - 1))) / (local_small_max + 1);

            for (z = 1; z <= local_small_max; z++) {
                if (big_w === 0) {
                    break;
                }

                small_w = this._toFixed(big_w - (small_p * z));

                html += '<span class="irs-grid-pol small" style="left: ' + small_w + '%"></span>';
            }

            html += '<span class="irs-grid-pol" style="left: ' + big_w + '%"></span>';

            result = this._convertToValue(big_w);
            if (o.values.length) {
                result = o.p_values[result];
            } else {
                result = this._prettifyNum(result);
            }

            html += '<span class="irs-grid-text js-grid-text-' + i + '" style="left: ' + big_w + '%">' + result + '</span>';
        }

        this.coords.big_num = Math.ceil(big_num + 1);

        this.cache.cont.classList.add("irs-with-grid");
        this.cache.grid.innerHTML = html;
        this._cacheGridLabels();
    };

    _cacheGridLabels() {
        for (let i = 0; i < this.coords.big_num; i++) {
            this.cache.grid_labels.push(this.cache.grid.querySelector(".js-grid-text-" + i));
        }

        this._calcGridLabels();
    };

    _calcGridLabels() {
        const start = [], finish = [],
            num = this.coords.big_num;
        for (let i = 0; i < num; i++) {
            this.coords.big_w[i] = this.cache.grid_labels[i].offsetWidth;
            this.coords.big_p[i] = this._toFixed(this.coords.big_w[i] / this.coords.w_rs * 100);
            this.coords.big_x[i] = this._toFixed(this.coords.big_p[i] / 2);

            start[i] = this._toFixed(this.coords.big[i] - this.coords.big_x[i]);
            finish[i] = this._toFixed(start[i] + this.coords.big_p[i]);
        }

        if (this.options.force_edges) {
            if (start[0] < -this.coords.grid_gap) {
                start[0] = -this.coords.grid_gap;
                finish[0] = this._toFixed(start[0] + this.coords.big_p[0]);

                this.coords.big_x[0] = this.coords.grid_gap;
            }

            if (finish[num - 1] > 100 + this.coords.grid_gap) {
                finish[num - 1] = 100 + this.coords.grid_gap;
                start[num - 1] = this._toFixed(finish[num - 1] - this.coords.big_p[num - 1]);

                this.coords.big_x[num - 1] = this._toFixed(this.coords.big_p[num - 1] - this.coords.grid_gap);
            }
        }

        this._calcGridCollision(2, start, finish);
        this._calcGridCollision(4, start, finish);

        for (let i = 0; i < num; i++) {
            const label = this.cache.grid_labels[i];
            if (this.coords.big_x[i] !== Number.POSITIVE_INFINITY) {
                this.label.style.marginLeft = -this.coords.big_x[i] + "%";
            }
        }
    };

    // Collisions Calc Beta
    // TODO: Refactor then have plenty of time
    _calcGridCollision(step, start, finish) {
        const num = this.coords.big_num;

        for (let i = 0; i < num; i += step) {
            let next_i = i + (step / 2);
            if (next_i >= num) {
                break;
            }
            const label = this.cache.grid_labels[next_i];

            if (finish[i] <= start[next_i]) {
                label.style.visibility = "visible";
            } else {
                label.style.visibility = "hidden";
            }
        }
    };

    _calcGridMargin() {
        if (!this.options.grid_margin) {
            return;
        }

        this.coords.w_rs = this.cache.rs.offsetWidth;

        if (!this.coords.w_rs) {
            return;
        }

        if (this.options.type === "single") {
            this.coords.w_handle = this.cache.s_single.offsetWidth;
        } else {
            this.coords.w_handle = this.cache.s_from.offsetWidth;
        }

        this.coords.p_handle = this._toFixed(this.coords.w_handle / this.coords.w_rs * 100);
        this.coords.grid_gap = this._toFixed((this.coords.p_handle / 2) - 0.1);

        this.cache.grid.style.width = this._toFixed(100 - this.coords.p_handle) + "%";
        this.cache.grid.style.left = this.coords.grid_gap + "%";
    };
}

export default IonRangeSlider;

// const IonRangeSlider = function (element, initOptions= {}) {

    
   

//     return {
//         update: function(options) {
//             update(options);
//         },
//         reset : function() {
//             reset();
//         },
//         destroy : function() {
//             destroy();
//         },
//         init : function () {
//             validate();
//             init();

//             return this;
//         },
//     };
// };

// /**
//  * Simple init function
//  *
//  * @param element {string|Element}
//  * @param options {Object}
//  */
// export default function ionRangeSlider2(element, options = null) {
//     if (typeof element === 'string') {
//         element = document.querySelector(element);
//     }

//     const ionRangeSlider = new IonRangeSlider(element, options);
    

//     return ionRangeSlider.init();
// }
