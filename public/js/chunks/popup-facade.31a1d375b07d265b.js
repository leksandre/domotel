"use strict";(self.webpackChunkmulti_kelnik_2_0=self.webpackChunkmulti_kelnik_2_0||[]).push([[407],{7516:function(o,t,e){e.r(t),e.d(t,{default:function(){return a}});var p=e(7675),r=e(1806);function n(o){return n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(o){return typeof o}:function(o){return o&&"function"==typeof Symbol&&o.constructor===Symbol&&o!==Symbol.prototype?"symbol":typeof o},n(o)}function u(o,t){for(var e=0;e<t.length;e++){var p=t[e];p.enumerable=p.enumerable||!1,p.configurable=!0,"value"in p&&(p.writable=!0),Object.defineProperty(o,i(p.key),p)}}function s(o,t,e){return(t=i(t))in o?Object.defineProperty(o,t,{value:e,enumerable:!0,configurable:!0,writable:!0}):o[t]=e,o}function i(o){var t=function(o,t){if("object"!==n(o)||null===o)return o;var e=o[Symbol.toPrimitive];if(void 0!==e){var p=e.call(o,t||"default");if("object"!==n(p))return p;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(o)}(o,"string");return"symbol"===n(t)?t:String(t)}var a=function(){function o(t){!function(o,t){if(!(o instanceof t))throw new TypeError("Cannot call a class as a function")}(this,o),s(this,"openButtons",void 0),s(this,"popupsGroups",void 0),this.openButtons=t.openButtons,this.popupsGroups={href:{Construct:p.Hy,popups:{},options:{modify:t.modify,removeDelay:300}},hrefNoHash:{Construct:p.eK,popups:{},options:{removeDelay:t.removeDelay}},ajax:{Construct:p.C8,popups:{},options:{removeDelay:t.removeDelay}},galleryPopup:{Construct:p.wY,popups:{},options:{modify:"popup_theme_white",removeDelay:300}},progressPopup:{Construct:p.oC,popups:{},options:{modify:"popup_theme_white",removeDelay:300}},callback:{Construct:p.Hy,popups:{},options:{removeDelay:t.removeDelay,closeButtonAriaLabel:t.closeButtonAriaLabel,modify:t.modify}},online:{Construct:p.ON,popups:{},options:{modify:"popup_theme_white",removeDelay:300,onCreate:function(){(0,r.J7)(".j-popup__content")}}}},this._checkOpenButtonsExistence()}var t,e,n;return t=o,(e=[{key:"init",value:function(){this._groupPopupsByType(),this._initPopups()}},{key:"_checkOpenButtonsExistence",value:function(){if(!this.openButtons.length)throw new Error("There is no PopupFacade open buttons in constructor")}},{key:"_addToGroup",value:function(o,t,e){var p,r=o.dataset[e];r||console.error("Неверно задан параметр ".concat(e," для ").concat(t)),!r||null!==(p=this.popupsGroups[t])&&void 0!==p&&p.popups[r]||(this.popupsGroups[t].popups[r]=[]);var n=r&&this.popupsGroups[t].popups[r];n&&n.push(o)}},{key:"_groupPopupsByType",value:function(){var o=this;this.openButtons.forEach((function(t){t.hasAttribute("data-callback")?o._addToGroup(t,"callback","href"):t.hasAttribute("data-href")&&t.hasAttribute("data-no-hash")?o._addToGroup(t,"hrefNoHash","href"):t.hasAttribute("data-href")?o._addToGroup(t,"href","href"):t.hasAttribute("data-ajax-online")?o._addToGroup(t,"online","ajax"):t.hasAttribute("data-progress")?o._addToGroup(t,"progressPopup","ajax"):t.hasAttribute("data-ajax")?o._addToGroup(t,"ajax","ajax"):t.hasAttribute("data-gallery")&&o._addToGroup(t,"galleryPopup","slider")}))}},{key:"_initPopups",value:function(){for(var o in this.popupsGroups)if(Object.prototype.hasOwnProperty.call(this.popupsGroups,o)){var t=o;for(var e in this.popupsGroups[t].popups)Object.prototype.hasOwnProperty.call(this.popupsGroups[t].popups,e)&&(Object.assign(this.popupsGroups[o].options,{openButtons:this.popupsGroups[o].popups[e]}),new this.popupsGroups[t].Construct(this.popupsGroups[t].options).init())}}}])&&u(t.prototype,e),n&&u(t,n),Object.defineProperty(t,"prototype",{writable:!1}),o}()}}]);
//# sourceMappingURL=popup-facade.31a1d375b07d265b.js.map