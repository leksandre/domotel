"use strict";(self["webpackChunkvisual"]=self["webpackChunkvisual"]||[]).push([[209],{6070:function(t,e,i){var s;i.r(e),i.d(e,{default:function(){return g}}),function(t){t["OPEN"]="open",t["CLOSE"]="close"}(s||(s={}));var n=i(5399);class h{constructor(t){var e;this.targetY=null!==(e=t.targetY)&&void 0!==e?e:0,this.duration=t.duration||600,this.start=0,this.startY=window.pageYOffset}scroll(t,e=0){const i=t||this.targetY,s=i-this.startY-e,n=t=>{this.start||(this.start=t);const e=t-this.start,i=Math.min(e/this.duration,1);window.scrollTo(0,this.startY+s*i),e<this.duration&&(this.animationName=window.requestAnimationFrame(n.bind(this)))};this.animationName=window.requestAnimationFrame(n.bind(this))}}var o=h,l=i(9167);const r=new n.Z,c="is-open",a=0;class u{constructor(){this.isOpen=s.CLOSE,this.isCloseTimeout=!1,this.toggler=null,this.contentOuter=null,this.titleText="",this.hiddenText="Скрыть",this.isChangeTitle=!1,this.timeout=300,this.close=this.close.bind(this),this.open=this.open.bind(this)}init(t){this._setElements(t),this._setInitState(),this._subscribe(),this._bindEvents()}reinit(){this._setInitState()}open(){this.isChangeTitle&&this.titleText&&(l.c.clearHtml(this.title),l.c.insetContent(this.title,this.hiddenText)),this.element.classList.add(c),this.isOpen=s.OPEN,this._setContentHeight(this.height),setTimeout((()=>{r.publish("accordion:open")}),this.timeout)}close(){this.isChangeTitle&&this.titleText&&(l.c.clearHtml(this.title),l.c.insetContent(this.title,this.titleText)),this.blockElement&&this._scrollToElement(),setTimeout((()=>{this.element.classList.remove(c),this.isOpen=s.CLOSE,this._setContentHeight(a),r.publish("accordion:close")}),this.isCloseTimeout?this.timeout:0)}_setInitState(){this.height=this._getHeight(),this.isOpen=this.element.classList.contains(c)?s.OPEN:s.CLOSE,this.titleText=this.title&&this.element.dataset["initialTitle"]||"",this.isChangeTitle=Boolean(this.element.dataset["changeTitle"]),this.hiddenText=this.element.dataset["alternateTitle"]||this.hiddenText,this.title&&(this.title.innerHTML=this.titleText)}_setContentHeight(t){if(!this.contentOuter)throw new Error(`Необходимо указать элемент 'contentOuter': ${this.options.selectors.contentOuter}`);this.contentOuter.style.height=t?`${t}px`:`${t}`}_subscribe(){throw new Error("Метод _subscribe должен быть переопределен")}_setElements(t){this.options=t;const{selectors:e}=this.options;this.element=t.element,this.isCloseTimeout=t.isCloseTimeout,this.toggler=t.element.querySelector(e.toggler),this.content=t.element.querySelector(e.content),this.contentOuter=t.element.querySelector(e.contentOuter),this.title=t.element.querySelector(e.title),e.blockElement&&(this.blockElement=t.element.closest(e.blockElement))}_getHeight(){if(!this.content)throw new Error(`Необходимо указать элемент 'content': ${this.options.selectors.content}`);return this.content.offsetHeight}_bindEvents(){this.toggler&&this.toggler.addEventListener("click",this._onTogglerClick.bind(this));const t=["resize","orientationchange"];t.forEach((t=>{window.addEventListener(t,this._onResize.bind(this))}))}_onTogglerClick(){const t=this.isOpen===s.OPEN?this.close:this.open;this.height=this._getHeight(),t()}_onResize(){this.isOpen&&this._update()}_update(){this.height=this._getHeight(),this._setContentHeight(this.isOpen===s.OPEN?this.height:a)}_scrollToElement(){if(!this.blockElement)return;const t=this.blockElement.getBoundingClientRect().top+pageYOffset-2*this.blockElement.offsetTop,e=new o({targetY:t});e.scroll()}}class g extends u{_subscribe(){r.subscribe("closeAccordion",this.close),r.subscribe("openAccordion",this.open)}_setInitState(){super._setInitState(),this._setContentHeight(this.isOpen===s.OPEN?this.height:a)}}}}]);