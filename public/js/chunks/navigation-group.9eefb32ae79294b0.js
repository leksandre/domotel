"use strict";(self.webpackChunkmulti_kelnik_2_0=self.webpackChunkmulti_kelnik_2_0||[]).push([[556],{9893:function(t,i,n){n.r(i);var e=n(281);function o(t){return o="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},o(t)}function r(t){return function(t){if(Array.isArray(t))return a(t)}(t)||function(t){if("undefined"!=typeof Symbol&&null!=t[Symbol.iterator]||null!=t["@@iterator"])return Array.from(t)}(t)||function(t,i){if(!t)return;if("string"==typeof t)return a(t,i);var n=Object.prototype.toString.call(t).slice(8,-1);"Object"===n&&t.constructor&&(n=t.constructor.name);if("Map"===n||"Set"===n)return Array.from(t);if("Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return a(t,i)}(t)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function a(t,i){(null==i||i>t.length)&&(i=t.length);for(var n=0,e=new Array(i);n<i;n++)e[n]=t[n];return e}function s(t,i){for(var n=0;n<i.length;n++){var e=i[n];e.enumerable=e.enumerable||!1,e.configurable=!0,"value"in e&&(e.writable=!0),Object.defineProperty(t,c(e.key),e)}}function u(t,i,n){return(i=c(i))in t?Object.defineProperty(t,i,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[i]=n,t}function c(t){var i=function(t,i){if("object"!==o(t)||null===t)return t;var n=t[Symbol.toPrimitive];if(void 0!==n){var e=n.call(t,i||"default");if("object"!==o(e))return e;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===i?String:Number)(t)}(t,"string");return"symbol"===o(i)?i:String(i)}var v="is-show",l="is-group",f="is-open",d=function(){function t(i){!function(t,i){if(!(t instanceof i))throw new TypeError("Cannot call a class as a function")}(this,t),u(this,"navigation",void 0),u(this,"navigationSelector",void 0),u(this,"navigationModify",void 0),u(this,"navigationItems",void 0),u(this,"windowWidth",e.c.getWindowWidth()),this.navigation=i.navigation,this.navigationSelector=i.navigationSelector||".j-group-navigation__navigation",this.navigationModify=i.navigationModify||"navigation_theme_group",this.navigationItems=r(this.navigation.querySelectorAll(".j-group-navigation__item"))}var i,n,o;return i=t,n=[{key:"init",value:function(){this._setNavigationItems(),this._setState(),this._bindEvents(),this._setActive()}},{key:"_setNavigationItems",value:function(){var t=this;this.navigationItems.forEach((function(i){i.isGroup=Boolean(i.dataset.group)||!1,i.show=Number(i.dataset.show)||!1,i.groupStart=Number(i.dataset.groupStart)||!1,i.groupEnd=Number(i.dataset.groupEnd)||!1,i.navigation=i.querySelector(t.navigationSelector)||null}))}},{key:"_setState",value:function(){var t=this;this.navigationItems.forEach((function(i){var n=i;n.classList.remove(f),n.isVisible=!!n.show&&t.windowWidth>=n.show,n.isVisible?n.classList.add(v):n.classList.remove(v),n.isGroup&&(n.isGroupActive=(!n.groupEnd||t.windowWidth<n.groupEnd)&&(!n.groupStart||t.windowWidth>n.groupStart),n.isGroupActive?(n.classList.add(l),t.navigationModify&&n.navigation.classList.add(t.navigationModify)):(n.classList.remove(l),t.navigationModify&&n.navigation.classList.remove(t.navigationModify)))}))}},{key:"_setActive",value:function(){this.navigation.classList.add("is-active")}},{key:"_bindEvents",value:function(){this._bindActionsEvents(),this._windowSizeEvents()}},{key:"_bindActionsEvents",value:function(){var t=this;this.navigationItems.forEach((function(i){var n=i;n.isGroup&&n.isGroupActive&&(n.addEventListener("mouseover",(function(){t._stateHandler(n)})),n.addEventListener("mouseout",(function(){t._stateHandler(n,!1)})))}))}},{key:"_windowSizeEvents",value:function(){var t=this;["resize","orientationchange"].forEach((function(i){window.addEventListener(i,(function(){t.windowWidth=e.c.getWindowWidth(),t._setState(),t._bindActionsEvents()}))}))}},{key:"_stateHandler",value:function(t){var i=!(arguments.length>1&&void 0!==arguments[1])||arguments[1];if(t.isGroupActive){var n=i?"add":"remove",e=this._checkDirection(t);t.classList[n](e,f)}}},{key:"_checkDirection",value:function(t){return t.navigation.getBoundingClientRect().left+t.navigation.offsetWidth+50<this.windowWidth?"is-right":"is-left"}}],n&&s(i.prototype,n),o&&s(i,o),Object.defineProperty(i,"prototype",{writable:!1}),t}();i.default=d}}]);
//# sourceMappingURL=navigation-group.9eefb32ae79294b0.js.map