(self.webpackChunkmulti_kelnik_2_0=self.webpackChunkmulti_kelnik_2_0||[]).push([[384],{3218:function(e,t,n){"use strict";n.r(t);var r=n(6163),i=n(2097),o=n.n(i);function a(e){return a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},a(e)}function s(e){return function(e){if(Array.isArray(e))return l(e)}(e)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(e)||function(e,t){if(!e)return;if("string"==typeof e)return l(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);"Object"===n&&e.constructor&&(n=e.constructor.name);if("Map"===n||"Set"===n)return Array.from(e);if("Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return l(e,t)}(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function l(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}function c(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,d(r.key),r)}}function u(e,t,n){return(t=d(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function d(e){var t=function(e,t){if("object"!==a(e)||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var r=n.call(e,t||"default");if("object"!==a(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"===a(t)?t:String(t)}var p=new r.Z,v=function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),u(this,"navigationWrapper",void 0),u(this,"wrapper",void 0),u(this,"driveElements",void 0),u(this,"foldingNavigation",void 0),u(this,"breaks",void 0),u(this,"offset",void 0),u(this,"isActive",!1)}var t,n,r;return t=e,(n=[{key:"init",value:function(e){var t,n;this.navigationWrapper=e.target,this.wrapper=this.navigationWrapper.closest(e.wrapper),this.driveElements=s(this.wrapper.querySelectorAll(e.driveElements)),this.foldingNavigation=e.foldingNavigation||null,this.breaks=null!==(t=e.breaks)&&void 0!==t?t:{min:320,max:1920},this.offset=null!==(n=e.offset)&&void 0!==n?n:15,this._bindEvents(),this._setSize(),this._setState()}},{key:"_bindEvents",value:function(){var e=this;["resize","orientationchange"].forEach((function(t){window.addEventListener(t,e._setSize.bind(e))}))}},{key:"_setSize",value:function(){if(!(this.isActive&&this.breaks.min>window.innerWidth&&window.innerWidth>this.breaks.max)){var e=this.driveElements.reduce((function(e,t){return e-t.offsetWidth}),this.wrapper.offsetWidth),t=getComputedStyle(this.navigationWrapper);e=e-parseInt(t.marginLeft)+parseInt(t.marginRight),this.navigationWrapper.style.width="".concat(e-this.offset,"px"),this.foldingNavigation&&this._setFoldingNavigationIcon()}}},{key:"_setState",value:function(){this._setFoldingNavigation(),this.navigationWrapper.classList.add("is-active"),this.isActive=!0,setTimeout((function(){p.publish("navigation:ready")}),300)}},{key:"_setFoldingNavigation",value:function(){this.foldingNavigation&&(o().init({mainNavWrapper:".j-folding-navigation",breakPoint:0,throttleDelay:"0",navDropdownLabel:"",navDropdownBreakpointLabel:""}),this._setFoldingNavigationIcon())}},{key:"_setFoldingNavigationIcon",value:function(){var e=this,t=document.querySelector(".priority-nav__dropdown-toggle");t&&setTimeout((function(){t.innerHTML="".concat('<svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">\n                <circle cx="19.5" cy="13.5" r="1.5"/>\n                <circle cx="19.5" cy="19.5" r="1.5"/>\n                <circle cx="19.5" cy="25.5" r="1.5"/>\n            </svg>'),e._setListener(t)}),100)}},{key:"_setListener",value:function(e){if(e){var t=document.querySelector(".priority-nav__dropdown");t&&s(t.querySelectorAll(".navigation__item")).forEach((function(n){n.addEventListener("click",(function(){t.classList.remove("show"),e.classList.remove("is-open")}))}))}}}])&&c(t.prototype,n),r&&c(t,r),Object.defineProperty(t,"prototype",{writable:!1}),e}();t.default=v},2097:function(e,t,n){var r,i,o;o=window||this,r=function(e){"use strict";var t,n,r,i,o,a,s,l={},c=[],u=!!document.querySelector&&!!e.addEventListener,d={},p=0,v=0,f={initClass:"js-priorityNav",mainNavWrapper:"nav",mainNav:"ul",navDropdownClassName:"nav__dropdown",navDropdownToggleClassName:"nav__dropdown-toggle",navDropdownLabel:"more",navDropdownBreakpointLabel:"menu",breakPoint:500,throttleDelay:50,offsetPixels:0,count:!0,moved:function(){},movedBack:function(){}},h=function(e,t,n){if("[object Object]"===Object.prototype.toString.call(e))for(var r in e)Object.prototype.hasOwnProperty.call(e,r)&&t.call(n,e[r],r,e);else for(var i=0,o=e.length;i<o;i++)t.call(n,e[i],i,e)},y=function(e,t){for(var n=t.charAt(0);e&&e!==document;e=e.parentNode)if("."===n){if(e.classList.contains(t.substr(1)))return e}else if("#"===n){if(e.id===t.substr(1))return e}else if("["===n&&e.hasAttribute(t.substr(1,t.length-2)))return e;return!1},m=function(e,t){var n={};return h(e,(function(t,r){n[r]=e[r]})),h(t,(function(e,r){n[r]=t[r]})),n};function g(e,t,n){var r;return function(){var i=this,o=arguments,a=function(){r=null,n||e.apply(i,o)},s=n&&!r;clearTimeout(r),r=setTimeout(a,t),s&&e.apply(i,o)}}var w=function(e,t){if(e.classList)e.classList.toggle(t);else{var n=e.className.split(" "),r=n.indexOf(t);r>=0?n.splice(r,1):n.push(t),e.className=n.join(" ")}},b=function(e,t){s=document.createElement("span"),o=document.createElement("ul"),(a=document.createElement("button")).innerHTML=t.navDropdownLabel,a.setAttribute("aria-controls","menu"),a.setAttribute("type","button"),o.setAttribute("aria-hidden","true"),e.querySelector(i).parentNode===e?(e.insertAfter(s,e.querySelector(i)),s.appendChild(a),s.appendChild(o),o.classList.add(t.navDropdownClassName),o.classList.add("priority-nav__dropdown"),a.classList.add(t.navDropdownToggleClassName),a.classList.add("priority-nav__dropdown-toggle"),a.setAttribute("type","button"),s.classList.add(t.navDropdownClassName+"-wrapper"),s.classList.add("priority-nav__wrapper"),e.classList.add("priority-nav")):console.warn("mainNav is not a direct child of mainNavWrapper, double check please")},S=function(e){var t=window.getComputedStyle(e),n=parseFloat(t.paddingLeft)+parseFloat(t.paddingRight);return e.clientWidth-n},L=function(){var e=document,t=window,n=e.compatMode&&"CSS1Compat"===e.compatMode?e.documentElement:e.body,r=n.clientWidth,i=n.clientHeight;return t.innerWidth&&r>t.innerWidth&&(r=t.innerWidth,i=t.innerHeight),{width:r,height:i}},N=function(e){n=S(e),e.querySelector(o).parentNode===e&&e.querySelector(o).offsetWidth,r=C(e)+d.offsetPixels,v=L().width};l.doesItFit=function(e){var t=0===e.getAttribute("instance")?t:d.throttleDelay;g((function(){var t=e.getAttribute("instance");for(N(e);n<=r&&e.querySelector(i).children.length>0||v<d.breakPoint&&e.querySelector(i).children.length>0;)l.toDropdown(e,t),N(e,t),v<d.breakPoint&&k(e,t,d.navDropdownBreakpointLabel);for(;n>=c[t][c[t].length-1]&&v>d.breakPoint;)l.toMenu(e,t),v>d.breakPoint&&k(e,t,d.navDropdownLabel);c[t].length<1&&(e.querySelector(o).classList.remove("show"),k(e,t,d.navDropdownLabel)),e.querySelector(i).children.length<1?(e.classList.add("is-empty"),k(e,t,d.navDropdownBreakpointLabel)):e.classList.remove("is-empty"),q(e,t)}),t)()};var q=function(e,t){c[t].length<1?(e.querySelector(a).classList.add("priority-nav-is-hidden"),e.querySelector(a).classList.remove("priority-nav-is-visible"),e.classList.remove("priority-nav-has-dropdown"),e.querySelector(".priority-nav__wrapper").setAttribute("aria-haspopup","false")):(e.querySelector(a).classList.add("priority-nav-is-visible"),e.querySelector(a).classList.remove("priority-nav-is-hidden"),e.classList.add("priority-nav-has-dropdown"),e.querySelector(".priority-nav__wrapper").setAttribute("aria-haspopup","true"))},_=function(e,t){e.querySelector(a).setAttribute("priorityNav-count",c[t].length)},k=function(e,t,n){e.querySelector(a).innerHTML=n};l.toDropdown=function(e,t){e.querySelector(o).firstChild&&e.querySelector(i).children.length>0?e.querySelector(o).insertBefore(e.querySelector(i).lastElementChild,e.querySelector(o).firstChild):e.querySelector(i).children.length>0&&e.querySelector(o).appendChild(e.querySelector(i).lastElementChild),c[t].push(r),q(e,t),e.querySelector(i).children.length>0&&d.count&&_(e,t),d.moved()},l.toMenu=function(e,t){e.querySelector(o).children.length>0&&e.querySelector(i).appendChild(e.querySelector(o).firstElementChild),c[t].pop(),q(e,t),e.querySelector(i).children.length>0&&d.count&&_(e,t),d.movedBack()};var C=function(e){for(var t=e.childNodes,n=0,r=0;r<t.length;r++)3!==t[r].nodeType&&(isNaN(t[r].offsetWidth)||(n+=t[r].offsetWidth));return n},E=function(e,n){window.attachEvent?window.attachEvent("onresize",(function(){l.doesItFit&&l.doesItFit(e)})):window.addEventListener&&window.addEventListener("resize",(function(){l.doesItFit&&l.doesItFit(e)}),!0),e.querySelector(a).addEventListener("click",(function(){w(e.querySelector(o),"show"),w(this,"is-open"),w(e,"is-open"),-1!==e.className.indexOf("is-open")?e.querySelector(o).setAttribute("aria-hidden","false"):(e.querySelector(o).setAttribute("aria-hidden","true"),e.querySelector(o).blur())})),document.addEventListener("click",(function(t){y(t.target,"."+n.navDropdownClassName)||t.target===e.querySelector(a)||(e.querySelector(o).classList.remove("show"),e.querySelector(a).classList.remove("is-open"),e.classList.remove("is-open"))})),document.onkeydown=function(e){27===(e=e||window.event).keyCode&&(document.querySelector(o).classList.remove("show"),document.querySelector(a).classList.remove("is-open"),t.classList.remove("is-open"))}};Element.prototype.remove=function(){this.parentElement.removeChild(this)},NodeList.prototype.remove=HTMLCollection.prototype.remove=function(){for(var e=0,t=this.length;e<t;e++)this[e]&&this[e].parentElement&&this[e].parentElement.removeChild(this[e])},l.destroy=function(){d&&(document.documentElement.classList.remove(d.initClass),s.remove(),d=null,delete l.init,delete l.doesItFit)},u&&"undefined"!=typeof Node&&(Node.prototype.insertAfter=function(e,t){this.insertBefore(e,t.nextSibling)});var A=function(e){var t=e.charAt(0);return"."!==t&&"#"!==t};return l.init=function(e){if(d=m(f,e||{}),u||"undefined"!=typeof Node)if(A(d.navDropdownClassName)&&A(d.navDropdownToggleClassName)){var n=document.querySelectorAll(d.mainNavWrapper);h(n,(function(e){c[p]=[],e.setAttribute("instance",p++),(t=e)?(i=d.mainNav,e.querySelector(i)?(b(e,d),o="."+d.navDropdownClassName,e.querySelector(o)?(a="."+d.navDropdownToggleClassName,e.querySelector(a)?(E(e,d),l.doesItFit(e)):console.warn("couldn't find the specified navDropdownToggle element")):console.warn("couldn't find the specified navDropdown element")):console.warn("couldn't find the specified mainNav element")):console.warn("couldn't find the specified mainNavWrapper element")})),document.documentElement.classList.add(d.initClass)}else console.warn("No symbols allowed in navDropdownClassName & navDropdownToggleClassName. These are not selectors.");else console.warn("This browser doesn't support priorityNav")},l}(o),void 0===(i="function"==typeof r?r.call(t,n,t,e):r)||(e.exports=i)}}]);
//# sourceMappingURL=navigation.bb2b4d870d434785.js.map