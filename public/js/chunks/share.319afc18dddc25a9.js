"use strict";(self.webpackChunkmulti_kelnik_2_0=self.webpackChunkmulti_kelnik_2_0||[]).push([[557],{3972:function(t,e,i){i.r(e);var n=i(281);function s(t){return s="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},s(t)}function r(t,e){for(var i=0;i<e.length;i++){var n=e[i];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,a(n.key),n)}}function o(t,e,i){return(e=a(e))in t?Object.defineProperty(t,e,{value:i,enumerable:!0,configurable:!0,writable:!0}):t[e]=i,t}function a(t){var e=function(t,e){if("object"!==s(t)||null===t)return t;var i=t[Symbol.toPrimitive];if(void 0!==i){var n=i.call(t,e||"default");if("object"!==s(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===e?String:Number)(t)}(t,"string");return"symbol"===s(e)?e:String(e)}var l="is-open",c="is-hidden",h=function(){function t(){!function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}(this,t),o(this,"target",void 0),o(this,"shareContainer",void 0),o(this,"shareContent",void 0),o(this,"email",void 0),o(this,"formContainer",void 0),o(this,"stepBack",void 0)}var e,i,s;return e=t,(i=[{key:"init",value:function(t){this._setOptions(t),this._getElements(),this._bindEvents()}},{key:"_setOptions",value:function(t){this.target=t.target}},{key:"_getElements",value:function(){this.shareContainer=this.target.querySelector(".j-share__container"),this.shareContent=this.shareContainer.querySelector(".j-share__content"),this.email=this.shareContent.querySelector(".j-share__email"),this.email&&(this.formContainer=this.shareContainer.querySelector(".j-share__form"),this.stepBack=this.shareContainer.querySelector(".j-share__form-back"))}},{key:"_bindEvents",value:function(){this.target&&this.target.addEventListener("click",this._open.bind(this)),this.email&&(this.email.addEventListener("click",this._emailForm.bind(this)),this.stepBack.addEventListener("click",this._stepBack.bind(this)))}},{key:"_open",value:function(t){t.target&&(t.target.closest(".j-share__button")&&this.target.classList.toggle(l),this.target.classList.contains(l)||this._setInitialContent(),n.c.clickOutside([".j-share"],this._clickedOutside.bind(this)))}},{key:"_emailForm",value:function(t){t.target&&(this.shareContent.classList.add(c),this.shareContent.classList.remove(l),this.formContainer.classList.add(l),this.formContainer.classList.remove(c))}},{key:"_setInitialContent",value:function(){var t=this;setTimeout((function(){t.formContainer.classList.remove(l),t.shareContent.classList.remove(c)}),300)}},{key:"_stepBack",value:function(){this.shareContent.classList.remove(c),this.shareContent.classList.add(l),this.formContainer.classList.remove(l),this.formContainer.classList.add(c)}},{key:"_clickedOutside",value:function(){this.target.classList.remove(l)}}])&&r(e.prototype,i),s&&r(e,s),Object.defineProperty(e,"prototype",{writable:!1}),t}();e.default=h}}]);
//# sourceMappingURL=share.319afc18dddc25a9.js.map