(self.webpackChunkmulti_kelnik_2_0=self.webpackChunkmulti_kelnik_2_0||[]).push([[898,309],{4993:function(e,t,i){"use strict";var s=i(281);function r(e){return r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},r(e)}function n(e,t){for(var i=0;i<t.length;i++){var s=t[i];s.enumerable=s.enumerable||!1,s.configurable=!0,"value"in s&&(s.writable=!0),Object.defineProperty(e,a(s.key),s)}}function a(e){var t=function(e,t){if("object"!==r(e)||null===e)return e;var i=e[Symbol.toPrimitive];if(void 0!==i){var s=i.call(e,t||"default");if("object"!==r(s))return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"===r(t)?t:String(t)}var o=function(){function e(t){var i,s,r;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),i=this,r=void 0,(s=a(s="element"))in i?Object.defineProperty(i,s,{value:r,enumerable:!0,configurable:!0,writable:!0}):i[s]=r,this.element=t}var t,i,r;return t=e,(i=[{key:"init",value:function(){window.Planoplan?this._load():s.c.loadScript("https://widget.planoplan.com/etc/multiwidget/release/static/js/main.js",this._load.bind(this))}},{key:"_load",value:function(){var e=this.element.dataset.planoplan,t=this.element.id;if(!e)throw new Error("#".concat(t," должен содержать атрибут 'data-planoplan' с данными виджета"));var i=JSON.parse(atob(e));window.Planoplan.init({el:t,uid:i.uid,primaryColor:i.primaryColor,secondaryColor:i.secondaryColor,textColor:i.textColor,backgroundColor:i.backgroundColor})}}])&&n(t.prototype,i),r&&n(t,r),Object.defineProperty(t,"prototype",{writable:!1}),e}();t.Z=o},3788:function(e,t,i){"use strict";i.r(t);var s=i(7368),r=i.n(s),n=i(7138),a=i.n(n),o=i(6633),l=i.n(o),d=i(7221),c=i.n(d),p=i(3007),u=i(840),h=i.n(u),y=i(4993),v=i(6163),f=i(1888),w=i.n(f),g=i(281);function b(e){return b="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},b(e)}function m(e,t){var i=Object.keys(e);if(Object.getOwnPropertySymbols){var s=Object.getOwnPropertySymbols(e);t&&(s=s.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),i.push.apply(i,s)}return i}function _(e){return function(e){if(Array.isArray(e))return k(e)}(e)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(e)||function(e,t){if(!e)return;if("string"==typeof e)return k(e,t);var i=Object.prototype.toString.call(e).slice(8,-1);"Object"===i&&e.constructor&&(i=e.constructor.name);if("Map"===i||"Set"===i)return Array.from(e);if("Arguments"===i||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(i))return k(e,t)}(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function k(e,t){(null==t||t>e.length)&&(t=e.length);for(var i=0,s=new Array(t);i<t;i++)s[i]=e[i];return s}function C(e,t){for(var i=0;i<t.length;i++){var s=t[i];s.enumerable=s.enumerable||!1,s.configurable=!0,"value"in s&&(s.writable=!0),Object.defineProperty(e,S(s.key),s)}}function T(e,t,i){return(t=S(t))in e?Object.defineProperty(e,t,{value:i,enumerable:!0,configurable:!0,writable:!0}):e[t]=i,e}function S(e){var t=function(e,t){if("object"!==b(e)||null===e)return e;var i=e[Symbol.toPrimitive];if(void 0!==i){var s=i.call(e,t||"default");if("object"!==b(s))return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"===b(t)?t:String(t)}var x=new v.Z,L="is-active",A="is-disabled",j="is-hidden",E=".j-slider-dots",P=function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),T(this,"slider",void 0),T(this,"slidesWrap",void 0),T(this,"captionWrap",void 0),T(this,"widgetWrap",void 0),T(this,"activeSlide",0),T(this,"isArrow",void 0),T(this,"arrowLeft",void 0),T(this,"arrowRight",void 0),T(this,"isDots",void 0),T(this,"isCounter",void 0),T(this,"noSwipe",void 0),T(this,"isAutoplay",void 0),T(this,"autoplaySpeed",void 0),T(this,"stopOnClick",void 0),T(this,"infinityLoop",!1),T(this,"isCaption",void 0),T(this,"slideCount",void 0),T(this,"arrowPosition",void 0),T(this,"counterPosition",void 0),T(this,"dotsPosition",void 0),T(this,"planoplanElements",[]),T(this,"slideVisible",void 0),T(this,"slidesItems",void 0),T(this,"slidesModify",void 0),T(this,"innerModify",void 0),T(this,"duration",void 0),T(this,"modify",void 0),T(this,"responsive",void 0),T(this,"innerData",void 0),T(this,"slidesHtml",void 0),T(this,"arrowHtml",void 0),T(this,"captionHtml",void 0),T(this,"counterHtml",void 0),T(this,"dotsHtml",void 0),T(this,"currentBreakpoint",void 0),T(this,"captionData",void 0),T(this,"slidesData",void 0),T(this,"breakpointArray",void 0)}var t,s,n;return t=e,s=[{key:"init",value:function(e){var t,i,s,r,n,a,o,l,d,c,u,h,y,v,f,w;this.slider=e.slider,this.slidesWrap=null!==(t=null==e?void 0:e.slidesWrap)&&void 0!==t?t:".j-slides",this.captionWrap=null!==(i=null==e?void 0:e.captionWrap)&&void 0!==i?i:".j-caption",this.widgetWrap=null!==(s=null==e?void 0:e.widgetWrap)&&void 0!==s?s:".j-slider__widget",this.slidesItems=Array.prototype.slice.call(null===(r=this.slider.querySelector(this.slidesWrap))||void 0===r?void 0:r.children),this.slideCount=null!==(n=null==e?void 0:e.slideCount)&&void 0!==n?n:1,this.arrowPosition=null!==(a=null==e?void 0:e.arrowPosition)&&void 0!==a?a:p.Q.slide,this.counterPosition=null!==(o=null==e?void 0:e.counterPosition)&&void 0!==o?o:p.Q.caption,this.dotsPosition=null!==(l=null==e?void 0:e.dotsPosition)&&void 0!==l?l:p.Q.caption,this.isCaption=null!==(d=null==e?void 0:e.isCaption)&&void 0!==d&&d,this.slideVisible=null!==(c=null==e?void 0:e.slideVisible)&&void 0!==c?c:1,this.modify=null!==(u=null==e?void 0:e.modify)&&void 0!==u&&u,this.slidesModify=null!==(h=null==e?void 0:e.slidesModify)&&void 0!==h&&h,this.innerModify=null!==(y=null==e?void 0:e.innerModify)&&void 0!==y&&y,this.innerData=e.innerData||!1,this.isDots=e.dots&&this.slidesItems.length>1||!1,this.duration=e.duration||"0.3s",this.responsive=null!==(v=null==e?void 0:e.responsive)&&void 0!==v&&v,this.noSwipe=null!==(f=null==e?void 0:e.noSwipe)&&void 0!==f&&f,this.isAutoplay=(null==e?void 0:e.autoplay)&&this.slidesItems.length>this.slideCount||!1,this.isArrow=e.arrow&&this.slidesItems.length>this.slideCount||!1,this.isCounter=e.counter&&this.slidesItems.length>this.slideCount||!1,this.autoplaySpeed=null!==(w=null==e?void 0:e.autoplaySpeed)&&void 0!==w?w:3e3,this.stopOnClick=e.stopOnClick||!1,this.infinityLoop=!this.responsive&&(e.infinityLoop||!1),this._getBreakpoints(),this._setCountSlides(),this._createSlider(),this._addSlider(),this._initSliderPopup(),this._getSliderParameters(),this._bindEvents(),this._disableArrow(),this._initInfinityLoop(),this._setActiveClass(),this._ready(),this.isAutoplay&&this._slideAutoplay()}},{key:"_createSlider",value:function(){this.slider.style.opacity="0",this._createSlides(),this.isCaption&&this._createCaption(),this.isDots&&this._createDots(),this.isArrow&&this._createArrows(),this.isCounter&&this._createCounter()}},{key:"_addSlider",value:function(){g.c.clearHtml(this.slider),g.c.insetContent(this.slider,this.slidesHtml),this.isCaption&&g.c.insetContent(this.slider.lastElementChild,this.captionHtml),this.isDots&&this._addDots(),this.isArrow&&this._addArrows(),this.isCounter&&this._addCounter()}},{key:"_initSliderPopup",value:function(){var e=_(this.slider.querySelectorAll(".j-popup-slider"));e.length&&i.e(407).then(i.bind(i,7516)).then((function(t){var i=t.default,s=function(e){for(var t=1;t<arguments.length;t++){var i=null!=arguments[t]?arguments[t]:{};t%2?m(Object(i),!0).forEach((function(t){T(e,t,i[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(i)):m(Object(i)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(i,t))}))}return e}({},{openButtons:e});new i(s).init()}))}},{key:"_addArrows",value:function(){var e=this.slider.firstElementChild;switch(this.arrowPosition){case p.Q.slide:default:break;case p.Q.allSlider:e=this.slider;break;case p.Q.caption:e=this.slider.lastElementChild}g.c.insetContent(e,this.arrowHtml),this.arrowLeft=this.slider.querySelector(".j-arrow-left"),this.arrowRight=this.slider.querySelector(".j-arrow-right")}},{key:"_addDots",value:function(){var e=this.slider;this.dotsPosition===p.Q.caption&&(e=this.slider.lastElementChild),g.c.insetContent(e,this.dotsHtml)}},{key:"_addCounter",value:function(){var e=this.slider;switch(this.counterPosition){case p.Q.slide:e=this.slider.firstElementChild;break;case p.Q.caption:e=this.slider.lastElementChild}g.c.insetContent(e,this.counterHtml)}},{key:"_createSlides",value:function(){this.slidesHtml=this._createElement(w(),this.slidesWrap,this.slideVisible)}},{key:"_createCaption",value:function(){var e=this._getCaptionData();e.hasItems?this.captionHtml=a()(e):(this.isCaption=!1,this.slider.classList.add("slider__caption_is_empty"))}},{key:"_createDots",value:function(){this.dotsHtml=c()({items:this.slidesItems})}},{key:"_createCounter",value:function(){this.counterHtml=l()({items:this.slidesItems})}},{key:"_createArrows",value:function(){this.arrowHtml=r()()}},{key:"_createElement",value:function(e,t,i){return e(this._getElements(t,i))}},{key:"_getElements",value:function(e,t){var i,s=_(null===(i=this.slider.querySelector(e))||void 0===i?void 0:i.children).map((function(e){return e.outerHTML}));return{wrap:e.substring(1),width:"".concat(this._getWidth(t),"%"),items:s,modify:!!this.modify&&this.modify,innerModify:this.innerModify,innerData:this.innerData}}},{key:"_getWidth",value:function(e){return 100/e}},{key:"_getCaptionData",value:function(){var e,t=[].slice.call(null===(e=this.slider.querySelector(this.slidesWrap))||void 0===e?void 0:e.children),i=this._getWidth(this.slideVisible),s=t.map((function(e){return e.dataset.caption}));return{width:"".concat(i,"%"),items:s,hasItems:s.join("").length>0}}},{key:"_bindEvents",value:function(){var e=this;this._slideEvents(),this.isDots&&this._dotsEvents(),this.isArrow&&this._arrowEvents(),document.addEventListener("keyup",(function(t){e.slider&&(e.slider.classList.contains("is-full-size")||e.slider.classList.contains("slider_theme_progress"))&&(37===t.keyCode?e._onTurnSlide(-1):39===t.keyCode&&e._onTurnSlide(1))})),window.addEventListener("resize",this._resizeWindow.bind(this))}},{key:"_slideEvents",value:function(){var e="object"===b(this.responsive)&&this.responsive[this.currentBreakpoint];if(!(this.noSwipe||1===this.slidesItems.length||e&&this.slidesItems.length<=e)){var t=new(h())(this.slider);t.on("swiperight",this._onSwipe.bind(this)),t.on("swipeleft",this._onSwipe.bind(this))}}},{key:"_dotsEvents",value:function(){var e,t=this;Array.prototype.slice.call(null===(e=this.slider.querySelector(".j-slider-dots"))||void 0===e?void 0:e.children).forEach((function(e){e.addEventListener("click",t._onDots.bind(t))}))}},{key:"_arrowEvents",value:function(){var e,t=this;Array.prototype.slice.call(null===(e=this.slider.querySelector(".j-slider-arrows"))||void 0===e?void 0:e.children).forEach((function(e){e.addEventListener("click",t._onArrow.bind(t))}))}},{key:"_onSwipe",value:function(e){var t="swiperight"===e.type?-1:1;this._onTurnSlide(t)}},{key:"_onArrow",value:function(e){var t,i=null===(t=e.target)||void 0===t?void 0:t.closest(".j-arrow");if(i){var s=g.c.getElementIndex(i)?1:-1;this._onTurnSlide(s)}}},{key:"_onTurnSlide",value:function(e){if(this._getSlidesParam(),this._checkTransform(e)?(this._changeActive(e),this._slideTo(this.duration),this.isCaption&&this._captionTo(this.duration)):this.infinityLoop&&(this._changeActive(e),this._slideTo(this.duration),this.isCaption&&this._captionTo(this.duration),this._rewindSlider()),this._disableArrow(),this.slider.classList.contains("is-full-size")){var t,i=[].slice.call(null===(t=this.slider.querySelector(this.slidesWrap))||void 0===t?void 0:t.children)[this.activeSlide];if(!i)return;var s=i.querySelector("picture");s&&[].slice.call(s.querySelectorAll("source")).forEach((function(e){e.dataset.big&&e.setAttribute("srcset",e.dataset.big)}))}}},{key:"_onDots",value:function(e){var t=e.currentTarget;if(t){var i=g.c.getElementIndex(t);this._setActive(i),this._slideTo(this.duration),this.isCaption&&this._captionTo(this.duration)}}},{key:"_disableArrow",value:function(){if(this.arrowLeft&&this.arrowRight&&!this.infinityLoop){var e=this.slidesItems.length-Math.floor(this.slideVisible);this.arrowLeft.classList.remove(j,A),this.arrowRight.classList.remove(j,A),this.slideVisible>this.slidesItems.length||1===this.slidesItems.length?(this.arrowLeft.classList.add(j),this.arrowRight.classList.add(j)):this.slidesItems.length===this.slideVisible?(this.arrowLeft.classList.add(A),this.arrowRight.classList.add(A)):0===this.activeSlide?(this.arrowLeft.classList.add(A),this.arrowRight.classList.remove(A)):this.activeSlide===e&&this.arrowRight.classList.add(A)}}},{key:"_initPlanoplan",value:function(e){var t=e.querySelector(this.widgetWrap);new y.Z(t).init()}},{key:"_observePlanoplan",value:function(){var e=this;if("IntersectionObserver"in window){var t=new IntersectionObserver((function(i){i.forEach((function(i){i.isIntersecting&&(e.planoplanElements.forEach((function(t){e._initPlanoplan(t)})),t.unobserve(e.slider))}))}));g.c.isInViewport(this.slider,window.innerHeight||document.documentElement.clientHeight)?this.planoplanElements.forEach((function(t){e._initPlanoplan(t)})):t.observe(this.slider)}else this.planoplanElements.forEach((function(t){e._initPlanoplan(t)}))}},{key:"_checkTransform",value:function(e){var t=this.infinityLoop?this.slideVisible:0,i=this.infinityLoop?0:Math.floor(this.slideVisible)-1,s=this.activeSlide+e-t;return s>=0&&s+i!==this.slidesItems.length}},{key:"_changeActive",value:function(e){this.activeSlide=this.activeSlide+e,this._setActiveClass(),this.isCounter&&this._setCurrentSlideNumber()}},{key:"_setActive",value:function(e){this.activeSlide=e,this._setActiveClass()}},{key:"_setCurrentSlideNumber",value:function(){var e=this.slider.querySelector(".j-counter-current"),t=this.infinityLoop?this.activeSlide+1-this.slideVisible:this.activeSlide+1;0===t?e.innerHTML="".concat(this.slidesItems.length):t===this.slidesItems.length+1?e.innerHTML="1":e.innerHTML="".concat(t)}},{key:"_setActiveClass",value:function(){var e,t,i=Array.prototype.slice.call(null===(e=this.slider.querySelector(this.slidesWrap))||void 0===e?void 0:e.children);if(i.forEach((function(e){e.classList.remove(L)})),null===(t=i[this.activeSlide])||void 0===t||t.classList.add(L),this.isDots){var s,r=Array.prototype.slice.call(null===(s=this.slider.querySelector(E))||void 0===s?void 0:s.children);r.forEach((function(e){e.classList.remove(L)})),r[this.activeSlide].classList.add(L)}}},{key:"_getSlidePosition",value:function(){return this.slidesData.width*this.activeSlide}},{key:"_getCaptionPosition",value:function(){return this.captionData.width*this.activeSlide}},{key:"_slideTo",value:function(e){var t=this._getSlidePosition(),i=this.slider.querySelector(this.slidesWrap);i.style.transform="translate3d(-".concat(t,"px, 0, 0)"),i.style.transitionDuration=e}},{key:"_captionTo",value:function(e){var t=this._getCaptionPosition(),i=this.slider.querySelector(this.captionWrap);i.style.transform="translate3d(-".concat(t,"px, 0, 0)"),i.style.transitionDuration=e}},{key:"_getSliderParameters",value:function(){this._getSlidesParam(),this.isCaption&&this._getCaptionParams()}},{key:"_getSlidesParam",value:function(){var e,t=this.slideVisible,i=Array.prototype.slice.call(null===(e=this.slider.querySelector(this.slidesWrap))||void 0===e?void 0:e.children);if(i.length){var s=Number(i[0].getBoundingClientRect().width.toFixed(4));this.slidesData={width:s,fullWidth:s*i.length,minTranslate:0,maxTranslate:s*i.length-s*t}}}},{key:"_getCaptionParams",value:function(){var e,t=Array.prototype.slice.call(null===(e=this.slider.querySelector(this.captionWrap))||void 0===e?void 0:e.children),i=this.slideVisible;t.length&&(this.captionData={width:t[0].offsetWidth,fullWidth:t[0].offsetWidth*t.length,minTranslate:0,maxTranslate:t[0].offsetWidth*t.length-t[0].offsetWidth*i})}},{key:"_ready",value:function(){this._setIntegerWidth(),this.slider.style.opacity="1",x.publish("slider:ready",this),this._getPlanoplan(),this._observePlanoplan()}},{key:"_setIntegerWidth",value:function(){this.responsive||(this.slider.style.width="".concat(this.slidesData.width*this.slideVisible,"px"))}},{key:"_removeIntegerWidth",value:function(){this.slider.style.width=""}},{key:"_resizeWindow",value:function(){this.responsive&&(this._setCountSlides(),this._redrawSlides(),this._disableArrow()),this._removeIntegerWidth(),this._getSliderParameters(),this._setIntegerWidth(),this._slideTo("0s"),this.isCaption&&this._captionTo("0s")}},{key:"_redrawSlides",value:function(){var e,t=Array.prototype.slice.call(null===(e=this.slider.querySelector(this.slidesWrap))||void 0===e?void 0:e.children),i=this._getElements(this.slidesWrap,this.slideVisible).width;t.forEach((function(e){e&&(e.style.minWidth=i,e.style.flexBasis=i)}))}},{key:"_setCountSlides",value:function(){if(this.responsive&&this._getCurrentBreakpoint()!==this.currentBreakpoint){this.currentBreakpoint=this._getCurrentBreakpoint();var e=this.currentBreakpoint;this.slideVisible=this.responsive[e]}}},{key:"_getBreakpoints",value:function(){if(this.responsive){var e=[];for(var t in this.responsive)Object.prototype.hasOwnProperty.call(this.responsive,t)&&e.push(parseInt(t));this.breakpointArray=e}}},{key:"_getPlanoplan",value:function(){var e=this;this.slidesItems.forEach((function(t){t.querySelector(e.widgetWrap)&&e.planoplanElements.push(t)}))}},{key:"_getCurrentBreakpoint",value:function(){var e=0;return this.breakpointArray.forEach((function(t){window.matchMedia("(min-width: ".concat(t,"px)")).matches&&(e=t)})),e}},{key:"_slideAutoplay",value:function(){var e=this,t=0,i=function(){t=window.setInterval((function(){e._checkTransform(1)?(e._changeActive(1),e._slideTo(e.duration),e.isCaption&&e._captionTo(e.duration)):e.infinityLoop&&(e._changeActive(1),e._slideTo(e.duration),e.isCaption&&e._captionTo(e.duration),e._rewindSlider())}),e.autoplaySpeed)};i(),this.stopOnClick&&this.slider.addEventListener("touchstart",(function(){t&&clearInterval(t),i()}))}},{key:"_initInfinityLoop",value:function(){this.infinityLoop&&(this._cloneSlides(),this.isCaption&&this._cloneCaptions(),this.isDots&&this._cloneDots(),this._setActive(this.slideVisible),this._slideTo("0s"),this.isCaption&&this._captionTo("0s"))}},{key:"_rewindSlider",value:function(){var e=this;if(this.infinityLoop){var t=0===this.activeSlide?this.slidesItems.length-1+this.slideVisible:this.slideVisible;this.isArrow&&this._preventArrowsClick(),setTimeout((function(){e.activeSlide=t,e._setActive(t),e._slideTo("0s"),e.isCaption&&e._captionTo("0s"),e.isArrow&&e._allowArrowsClick()}),300)}}},{key:"_cloneSlides",value:function(){var e;if(this.infinityLoop)for(var t=Array.prototype.slice.call(null===(e=this.slider.querySelector(this.slidesWrap))||void 0===e?void 0:e.children),i=0;i<this.slideVisible;i++){var s=t[i],r=t[t.length-1-i],n=s.cloneNode(!0),a=r.cloneNode(!0),o=this.slider.querySelector(this.slidesWrap);a.classList.add("cloned-slide"),n.classList.add("cloned-slide"),o&&(o.appendChild(n),o.insertBefore(a,o.children[0]))}}},{key:"_cloneCaptions",value:function(){var e;if(this.infinityLoop)for(var t=Array.prototype.slice.call(null===(e=this.slider.querySelector(this.captionWrap))||void 0===e?void 0:e.children),i=0;i<this.slideVisible;i++){var s=t[i],r=t[t.length-1-i],n=s.cloneNode(!0),a=r.cloneNode(!0),o=this.slider.querySelector(this.captionWrap);a.classList.add("cloned-slide"),n.classList.add("cloned-slide"),o&&(o.appendChild(n),o.insertBefore(a,o.children[0]))}}},{key:"_cloneDots",value:function(){var e;if(this.infinityLoop)for(var t=Array.prototype.slice.call(null===(e=this.slider.querySelector(E))||void 0===e?void 0:e.children),i=0;i<this.slideVisible;i++){var s=t[i],r=t[t.length-1-i],n=s.cloneNode(!0),a=r.cloneNode(!0),o=this.slider.querySelector(E);a.classList.add("cloned-slide"),n.classList.add("cloned-slide"),o&&(o.appendChild(n),o.insertBefore(a,o.children[0]))}}},{key:"_preventArrowsClick",value:function(){this.arrowLeft.style.pointerEvents="none",this.arrowRight.style.pointerEvents="none",this.arrowLeft.style.cursor="pointer",this.arrowRight.style.cursor="pointer"}},{key:"_allowArrowsClick",value:function(){this.arrowLeft.style.pointerEvents="",this.arrowRight.style.pointerEvents="",this.arrowLeft.style.cursor="",this.arrowRight.style.cursor=""}}],s&&C(t.prototype,s),n&&C(t,n),Object.defineProperty(t,"prototype",{writable:!1}),e}();t.default=P},9500:function(e,t,i){"use strict";i.r(t);var s=i(3007),r=i(3788);t.default=function(e){(new r.default).init({slider:e,dots:!1,arrow:!1,counter:!1,arrowPosition:s.Q.caption,noSwipe:!0,autoplay:!0,autoplaySpeed:5e3,infinityLoop:!0})}},3322:function(e,t,i){var s=(0,i(7566).twig)({id:"$resolved:845cc12cf541e176e50892c8e2c52ca824a5cc30adb6c6ae80c504396fac2761bf86bf1b01155a638e969c6b462c04130d8644c1ee3490f2d592dcb3addbb946:arrow-left.svg.twig",data:[{type:"raw",value:'<svg width="14" height="10" viewBox="0 0 14 10" fill="none">\r\n    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.8219 6.21348L4.97842 8.20881C5.3219 8.54876 5.3219 9.09824 4.97842 9.43818C4.63408 9.77813 4.07669 9.77813 3.73321 9.43818L0.38625 6.26738C0.137554 6.02046 0.000163078 5.69443 0.000163078 5.34492C0.000163078 4.99628 0.137554 4.6685 0.38625 4.42332L3.73321 1.25252C4.07669 0.912572 4.63408 0.912572 4.97842 1.25252C5.3219 1.5916 5.3219 2.14194 4.97842 2.48189L2.82364 4.47462H13.0367C13.5167 4.47462 13.9062 4.86412 13.9062 5.34405C13.9062 5.82397 13.5167 6.21348 13.0367 6.21348H2.8219Z"/>\r\n</svg>\r\n'}],allowInlineIncludes:!0,rethrow:!0});e.exports=function(e){return s.render(e)},e.exports.tokens=[{type:"raw",value:'<svg width="14" height="10" viewBox="0 0 14 10" fill="none">\r\n    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.8219 6.21348L4.97842 8.20881C5.3219 8.54876 5.3219 9.09824 4.97842 9.43818C4.63408 9.77813 4.07669 9.77813 3.73321 9.43818L0.38625 6.26738C0.137554 6.02046 0.000163078 5.69443 0.000163078 5.34492C0.000163078 4.99628 0.137554 4.6685 0.38625 4.42332L3.73321 1.25252C4.07669 0.912572 4.63408 0.912572 4.97842 1.25252C5.3219 1.5916 5.3219 2.14194 4.97842 2.48189L2.82364 4.47462H13.0367C13.5167 4.47462 13.9062 4.86412 13.9062 5.34405C13.9062 5.82397 13.5167 6.21348 13.0367 6.21348H2.8219Z"/>\r\n</svg>\r\n'}]},4875:function(e,t,i){var s=(0,i(7566).twig)({id:"$resolved:ae4dc22d5cd18ae750ae5e8e077e430b64bc59dd659f451b233611efd686a8870df5e36141307977beb8d82d5aadd76d74a0442f9a06bf537c4583ea4e411763:arrow-right.svg.twig",data:[{type:"raw",value:'<svg width="14" height="10" viewBox="0 0 14 10" fill="none">\r\n    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.1781 6.21348L9.02158 8.20881C8.6781 8.54876 8.6781 9.09824 9.02158 9.43818C9.36592 9.77813 9.92331 9.77813 10.2668 9.43818L13.6138 6.26738C13.8624 6.02046 13.9998 5.69443 13.9998 5.34492C13.9998 4.99628 13.8624 4.6685 13.6138 4.42332L10.2668 1.25252C9.92331 0.912572 9.36592 0.912572 9.02158 1.25252C8.6781 1.5916 8.6781 2.14194 9.02158 2.48189L11.1764 4.47462H0.963315C0.483315 4.47462 0.09375 4.86412 0.09375 5.34405C0.09375 5.82397 0.483315 6.21348 0.963315 6.21348H11.1781Z"/>\r\n</svg>\r\n'}],allowInlineIncludes:!0,rethrow:!0});e.exports=function(e){return s.render(e)},e.exports.tokens=[{type:"raw",value:'<svg width="14" height="10" viewBox="0 0 14 10" fill="none">\r\n    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.1781 6.21348L9.02158 8.20881C8.6781 8.54876 8.6781 9.09824 9.02158 9.43818C9.36592 9.77813 9.92331 9.77813 10.2668 9.43818L13.6138 6.26738C13.8624 6.02046 13.9998 5.69443 13.9998 5.34492C13.9998 4.99628 13.8624 4.6685 13.6138 4.42332L10.2668 1.25252C9.92331 0.912572 9.36592 0.912572 9.02158 1.25252C8.6781 1.5916 8.6781 2.14194 9.02158 2.48189L11.1764 4.47462H0.963315C0.483315 4.47462 0.09375 4.86412 0.09375 5.34405C0.09375 5.82397 0.483315 6.21348 0.963315 6.21348H11.1781Z"/>\r\n</svg>\r\n'}]},7368:function(e,t,i){i(4875),i(3322);var s=(0,i(7566).twig)({id:"$resolved:9b10e9b6283d04ea31c29b6cfda3200239c75cd372bc4182ef93e641f54128489696b1436e83b50631265fd18c8a3a6584d91958d8209ac1b508d38974fe888a:arrows-tmpl.twig",data:[{type:"raw",value:'\r\n<div class="slider__arrows j-slider-arrows">\r\n    <button class="button-circle slider__arrow j-arrow j-arrow-left" aria-label="Следующий слайд">\r\n        '},{type:"logic",token:{type:"Twig.logic.type.include",only:!1,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"$resolved:845cc12cf541e176e50892c8e2c52ca824a5cc30adb6c6ae80c504396fac2761bf86bf1b01155a638e969c6b462c04130d8644c1ee3490f2d592dcb3addbb946:arrow-left.svg.twig"}]}},{type:"raw",value:'\r\n    </button>\r\n    <button class="button-circle slider__arrow j-arrow j-arrow-right" aria-label="Предыдущий слайд">\r\n        '},{type:"logic",token:{type:"Twig.logic.type.include",only:!1,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"$resolved:ae4dc22d5cd18ae750ae5e8e077e430b64bc59dd659f451b233611efd686a8870df5e36141307977beb8d82d5aadd76d74a0442f9a06bf537c4583ea4e411763:arrow-right.svg.twig"}]}},{type:"raw",value:"\r\n    </button>\r\n</div>\r\n"}],allowInlineIncludes:!0,rethrow:!0});e.exports=function(e){return s.render(e)},e.exports.tokens=[{type:"raw",value:'\r\n<div class="slider__arrows j-slider-arrows">\r\n    <button class="button-circle slider__arrow j-arrow j-arrow-left" aria-label="Следующий слайд">\r\n        '},{type:"logic",token:{type:"Twig.logic.type.include",only:!1,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"$resolved:845cc12cf541e176e50892c8e2c52ca824a5cc30adb6c6ae80c504396fac2761bf86bf1b01155a638e969c6b462c04130d8644c1ee3490f2d592dcb3addbb946:arrow-left.svg.twig"}]}},{type:"raw",value:'\r\n    </button>\r\n    <button class="button-circle slider__arrow j-arrow j-arrow-right" aria-label="Предыдущий слайд">\r\n        '},{type:"logic",token:{type:"Twig.logic.type.include",only:!1,ignoreMissing:!1,stack:[{type:"Twig.expression.type.string",value:"$resolved:ae4dc22d5cd18ae750ae5e8e077e430b64bc59dd659f451b233611efd686a8870df5e36141307977beb8d82d5aadd76d74a0442f9a06bf537c4583ea4e411763:arrow-right.svg.twig"}]}},{type:"raw",value:"\r\n    </button>\r\n</div>\r\n"}]},7138:function(e,t,i){var s=(0,i(7566).twig)({id:"$resolved:dbd67ccbbe549c487214631f7d56d28438085001fac6e2724c4f7d0ff7221707cff58fccc76223bbb77273ce992f33df890c46d6790f083a6bdae1af2eb791d3:caption-tmpl.twig",data:[{type:"raw",value:'<div class="slider__caption">\r\n    <div class="slider__caption-wrap j-caption">\r\n        '},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"items",match:["items"]}],output:[{type:"raw",value:'\r\n            <div class="slider__caption-item" style="min-width: '},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"width",match:["width"]}]},{type:"raw",value:"; flex-basis: "},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"width",match:["width"]}]},{type:"raw",value:';">\r\n                '},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]}]},{type:"raw",value:"\r\n            </div>\r\n        "}]}},{type:"raw",value:"\r\n    </div>\r\n</div>\r\n"}],allowInlineIncludes:!0,rethrow:!0});e.exports=function(e){return s.render(e)},e.exports.tokens=[{type:"raw",value:'<div class="slider__caption">\r\n    <div class="slider__caption-wrap j-caption">\r\n        '},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"items",match:["items"]}],output:[{type:"raw",value:'\r\n            <div class="slider__caption-item" style="min-width: '},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"width",match:["width"]}]},{type:"raw",value:"; flex-basis: "},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"width",match:["width"]}]},{type:"raw",value:';">\r\n                '},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]}]},{type:"raw",value:"\r\n            </div>\r\n        "}]}},{type:"raw",value:"\r\n    </div>\r\n</div>\r\n"}]},6633:function(e,t,i){var s=(0,i(7566).twig)({id:"$resolved:97941c5504c570cba10898ffbf5302ff673c478c06c94835844585d65bc9a036ce8f3d1793086709f9d5528cbdcf7eee435a7b3ec2cc46213c71be9a61dfcf76:counter-tmpl.twig",data:[{type:"raw",value:'<div class="slider__counter">\r\n    <span class="slider__counter-current j-counter-current">1</span>\r\n    <span>/</span>\r\n    <span>'},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"items",match:["items"]},{type:"Twig.expression.type.key.period",key:"length"}]},{type:"raw",value:"</span>\r\n</div>\r\n"}],allowInlineIncludes:!0,rethrow:!0});e.exports=function(e){return s.render(e)},e.exports.tokens=[{type:"raw",value:'<div class="slider__counter">\r\n    <span class="slider__counter-current j-counter-current">1</span>\r\n    <span>/</span>\r\n    <span>'},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"items",match:["items"]},{type:"Twig.expression.type.key.period",key:"length"}]},{type:"raw",value:"</span>\r\n</div>\r\n"}]},7221:function(e,t,i){var s=(0,i(7566).twig)({id:"$resolved:f474dd86e0099de01c254fb0a973f9e121c9bb589ea27f7a0b94907f163710d11f998155365834774d27e106fa3640b9ef4bf48c3cd0e75371822530883ec0df:dots-tmpl.twig",data:[{type:"raw",value:'<ul class="slider__dots j-slider-dots">\r\n    '},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"i",expression:[{type:"Twig.expression.type.variable",value:"items",match:["items"]}],output:[{type:"raw",value:'\r\n        <li class="slider__dots-item">\r\n            <button class="slider__dot" aria-label="'},{type:"output",stack:[{type:"Twig.expression.type.string",value:"Слайд #"},{type:"Twig.expression.type.variable",value:"loop",match:["loop"]},{type:"Twig.expression.type.key.period",key:"index"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"}]},{type:"raw",value:'"></button>\r\n        </li>\r\n    '}]}},{type:"raw",value:"\r\n</ul>\r\n"}],allowInlineIncludes:!0,rethrow:!0});e.exports=function(e){return s.render(e)},e.exports.tokens=[{type:"raw",value:'<ul class="slider__dots j-slider-dots">\r\n    '},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"i",expression:[{type:"Twig.expression.type.variable",value:"items",match:["items"]}],output:[{type:"raw",value:'\r\n        <li class="slider__dots-item">\r\n            <button class="slider__dot" aria-label="'},{type:"output",stack:[{type:"Twig.expression.type.string",value:"Слайд #"},{type:"Twig.expression.type.variable",value:"loop",match:["loop"]},{type:"Twig.expression.type.key.period",key:"index"},{type:"Twig.expression.type.operator.binary",value:"~",precidence:6,associativity:"leftToRight",operator:"~"}]},{type:"raw",value:'"></button>\r\n        </li>\r\n    '}]}},{type:"raw",value:"\r\n</ul>\r\n"}]},1888:function(e,t,i){var s=(0,i(7566).twig)({id:"$resolved:9841417faff24b4f2ae42ca41df72f395ad4c73074a85b056e727d9a529b2ae4b2c217e1115507e1e2b5312057ec83f5c31496ad14a38dcbcdb18840784b2837:slides-tmpl.twig",data:[{type:"raw",value:'\r\n<div class="slider__wrapper">\r\n    <div class="slider__slides'},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"modify",match:["modify"]}],output:[{type:"raw",value:" slider__slides_"},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"modify",match:["modify"]}]}]}},{type:"raw",value:'">\r\n        <div class="slider__slides-inner '},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"wrap",match:["wrap"]}]},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"innerModify",match:["innerModify"]}],output:[{type:"raw",value:" "},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"innerModify",match:["innerModify"]}]}]}},{type:"raw",value:'"'},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"innerData",match:["innerData"]}],output:[{type:"raw",value:" "},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"innerData",match:["innerData"]}]}]}},{type:"raw",value:">\r\n            "},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"items",match:["items"]}],output:[{type:"raw",value:'\r\n                <div class="slider__item'},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"slidesModify",match:["slidesModify"]}],output:[{type:"raw",value:" "},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"slidesModify",match:["slidesModify"]}]}]}},{type:"raw",value:'" style="min-width: '},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"width",match:["width"]}]},{type:"raw",value:"; flex-basis: "},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"width",match:["width"]}]},{type:"raw",value:';">\r\n                    '},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]}]},{type:"raw",value:"\r\n                </div>\r\n            "}]}},{type:"raw",value:'\r\n        </div>\r\n    </div>\r\n</div>\r\n<div class="slider__controls'},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"modify",match:["modify"]}],output:[{type:"raw",value:" slider__controls_"},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"modify",match:["modify"]}]}]}},{type:"raw",value:'"></div>\r\n'}],allowInlineIncludes:!0,rethrow:!0});e.exports=function(e){return s.render(e)},e.exports.tokens=[{type:"raw",value:'\r\n<div class="slider__wrapper">\r\n    <div class="slider__slides'},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"modify",match:["modify"]}],output:[{type:"raw",value:" slider__slides_"},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"modify",match:["modify"]}]}]}},{type:"raw",value:'">\r\n        <div class="slider__slides-inner '},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"wrap",match:["wrap"]}]},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"innerModify",match:["innerModify"]}],output:[{type:"raw",value:" "},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"innerModify",match:["innerModify"]}]}]}},{type:"raw",value:'"'},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"innerData",match:["innerData"]}],output:[{type:"raw",value:" "},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"innerData",match:["innerData"]}]}]}},{type:"raw",value:">\r\n            "},{type:"logic",token:{type:"Twig.logic.type.for",keyVar:null,valueVar:"item",expression:[{type:"Twig.expression.type.variable",value:"items",match:["items"]}],output:[{type:"raw",value:'\r\n                <div class="slider__item'},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"slidesModify",match:["slidesModify"]}],output:[{type:"raw",value:" "},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"slidesModify",match:["slidesModify"]}]}]}},{type:"raw",value:'" style="min-width: '},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"width",match:["width"]}]},{type:"raw",value:"; flex-basis: "},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"width",match:["width"]}]},{type:"raw",value:';">\r\n                    '},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"item",match:["item"]}]},{type:"raw",value:"\r\n                </div>\r\n            "}]}},{type:"raw",value:'\r\n        </div>\r\n    </div>\r\n</div>\r\n<div class="slider__controls'},{type:"logic",token:{type:"Twig.logic.type.if",stack:[{type:"Twig.expression.type.variable",value:"modify",match:["modify"]}],output:[{type:"raw",value:" slider__controls_"},{type:"output",stack:[{type:"Twig.expression.type.variable",value:"modify",match:["modify"]}]}]}},{type:"raw",value:'"></div>\r\n'}]}}]);
//# sourceMappingURL=first-screen-section.8bac10a7ad4cc4d9.js.map