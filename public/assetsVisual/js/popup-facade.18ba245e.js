"use strict";(self["webpackChunkvisual"]=self["webpackChunkvisual"]||[]).push([[407],{6171:function(o,p,t){t.r(p),t.d(p,{default:function(){return u}});var s=t(606),e=t(5383);class u{constructor(o){this.openButtons=o.openButtons,this.popupsGroups={href:{Construct:s.Hy,popups:{},options:{modify:o.modify,removeDelay:300}},hrefNoHash:{Construct:s.eK,popups:{},options:{removeDelay:o.removeDelay}},ajax:{Construct:s.C8,popups:{},options:{removeDelay:o.removeDelay}},galleryPopup:{Construct:s.wY,popups:{},options:{modify:"popup_theme_white",removeDelay:300}},progressPopup:{Construct:s.oC,popups:{},options:{modify:"popup_theme_white",removeDelay:300}},callback:{Construct:s.Hy,popups:{},options:{removeDelay:o.removeDelay,closeButtonAriaLabel:o.closeButtonAriaLabel,modify:o.modify}},online:{Construct:s.ON,popups:{},options:{modify:"popup_theme_white",removeDelay:300,onCreate:()=>{(0,e.J7)(".j-popup__content")}}}},this._checkOpenButtonsExistence()}init(){this._groupPopupsByType(),this._initPopups()}_checkOpenButtonsExistence(){if(!this.openButtons.length)throw new Error("There is no PopupFacade open buttons in constructor")}_addToGroup(o,p,t){var s;const e=o.dataset[t];e||console.error(`Неверно задан параметр ${t} для ${p}`),e&&!(null===(s=this.popupsGroups[p])||void 0===s?void 0:s.popups[e])&&(this.popupsGroups[p].popups[e]=[]);const u=e&&this.popupsGroups[p].popups[e];u&&u.push(o)}_groupPopupsByType(){this.openButtons.forEach((o=>{o.hasAttribute("data-callback")?this._addToGroup(o,"callback","href"):o.hasAttribute("data-href")&&o.hasAttribute("data-no-hash")?this._addToGroup(o,"hrefNoHash","href"):o.hasAttribute("data-href")?this._addToGroup(o,"href","href"):o.hasAttribute("data-ajax-online")?this._addToGroup(o,"online","ajax"):o.hasAttribute("data-progress")?this._addToGroup(o,"progressPopup","ajax"):o.hasAttribute("data-ajax")?this._addToGroup(o,"ajax","ajax"):o.hasAttribute("data-gallery")&&this._addToGroup(o,"galleryPopup","slider")}))}_initPopups(){for(const o in this.popupsGroups)if(Object.prototype.hasOwnProperty.call(this.popupsGroups,o)){const p=o;for(const t in this.popupsGroups[p].popups)Object.prototype.hasOwnProperty.call(this.popupsGroups[p].popups,t)&&(Object.assign(this.popupsGroups[o].options,{openButtons:this.popupsGroups[o].popups[t]}),new this.popupsGroups[p].Construct(this.popupsGroups[p].options).init())}}}}}]);