/**
 * @package    AcyMailing for Joomla!
 * @version    5.10.7
 * @author     acyba.com
 * @copyright  (C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

!function(t){"use strict";"function"==typeof define&&define.amd?define(t):"undefined"!=typeof module&&void 0!==module.exports?module.exports=t():window.Sortable=t()}(function(){"use strict";function t(t,e){if(!t||!t.nodeType||1!==t.nodeType)throw"Sortable: `el` must be HTMLElement, and not "+{}.toString.call(t);this.el=t,this.options=e=_({},e),t[V]=this;var n={group:Math.random(),sort:!0,disabled:!1,store:null,handle:null,scroll:!0,scrollSensitivity:30,scrollSpeed:10,draggable:/[uo]l/i.test(t.nodeName)?"li":">*",ghostClass:"sortable-ghost",chosenClass:"sortable-chosen",dragClass:"sortable-drag",ignore:"a, img",filter:null,preventOnFilter:!0,animation:0,setData:function(t,e){t.setData("Text",e.textContent)},dropBubble:!1,dragoverBubble:!1,dataIdAttr:"data-id",delay:0,forceFallback:!1,fallbackClass:"sortable-fallback",fallbackOnBody:!1,fallbackTolerance:0,fallbackOffset:{x:0,y:0}};for(var i in n)!(i in e)&&(e[i]=n[i]);at(e);for(var o in this)"_"===o.charAt(0)&&"function"==typeof this[o]&&(this[o]=this[o].bind(this));this.nativeDraggable=!e.forceFallback&&K,r(t,"mousedown",this._onTapStart),r(t,"touchstart",this._onTapStart),r(t,"pointerdown",this._onTapStart),this.nativeDraggable&&(r(t,"dragover",this),r(t,"dragenter",this)),ot.push(this._onDragOver),e.store&&"function"===e.store.get&&this.sort(e.store.get(this))}function e(t,e){"clone"!==t.lastPullMode&&(e=!0),S&&S.state!==e&&(l(S,"display",e?"none":""),e||S.state&&(t.options.group.revertClone?(E.insertBefore(S,x),t._animate(w,S)):E.insertBefore(S,w)),S.state=e)}function n(t,e,n){if(t){n=n||z;do{if(">*"===e&&t.parentNode===n||m(t,e))return t}while(t=i(t))}return null}function i(t){var e=t.host;return e&&e.nodeType?e:t.parentNode}function o(t){t.dataTransfer&&(t.dataTransfer.dropEffect="move"),t.preventDefault()}function r(t,e,n){t.addEventListener(e,n,J)}function a(t,e,n){t.removeEventListener(e,n,J)}function s(t,e,n){if(t)if(t.classList)t.classList[n?"add":"remove"](e);else{var i=(" "+t.className+" ").replace(H," ").replace(" "+e+" "," ");t.className=(i+(n?" "+e:"")).replace(H," ")}}function l(t,e,n){var i=t&&t.style;if(i){if(void 0===n)return z.defaultView&&z.defaultView.getComputedStyle?n=z.defaultView.getComputedStyle(t,""):t.currentStyle&&(n=t.currentStyle),void 0===e?n:n[e];e in i||(e="-webkit-"+e),i[e]=n+("string"==typeof n?"":"px")}}function c(t,e,n){if(t){var i=t.getElementsByTagName(e),o=0,r=i.length;if(n)for(;o<r;o++)n(i[o],o);return i}return[]}function d(t,e,n,i,o,r,a){t=t||e[V];var s=z.createEvent("Event"),l=t.options,c="on"+n.charAt(0).toUpperCase()+n.substr(1);s.initEvent(n,!0,!0),s.to=e,s.from=o||e,s.item=i||e,s.clone=S,s.oldIndex=r,s.newIndex=a,e.dispatchEvent(s),l[c]&&l[c].call(t,s)}function h(t,e,n,i,o,r,a,s){var l,c,d=t[V],h=d.options.onMove;return(l=z.createEvent("Event")).initEvent("move",!0,!0),l.to=e,l.from=t,l.dragged=n,l.draggedRect=i,l.related=o||e,l.relatedRect=r||e.getBoundingClientRect(),l.willInsertAfter=s,t.dispatchEvent(l),h&&(c=h.call(d,l,a)),c}function u(t){t.draggable=!1}function f(){tt=!1}function p(t,e){var n=t.lastElementChild.getBoundingClientRect();return e.clientY-(n.top+n.height)>5||e.clientX-(n.left+n.width)>5}function g(t){for(var e=t.tagName+t.className+t.src+t.href+t.textContent,n=e.length,i=0;n--;)i+=e.charCodeAt(n);return i.toString(36)}function v(t,e){var n=0;if(!t||!t.parentNode)return-1;for(;t&&(t=t.previousElementSibling);)"TEMPLATE"===t.nodeName.toUpperCase()||">*"!==e&&!m(t,e)||n++;return n}function m(t,e){if(t){var n=(e=e.split(".")).shift().toUpperCase(),i=new RegExp("\\s("+e.join("|")+")(?=\\s)","g");return!(""!==n&&t.nodeName.toUpperCase()!=n||e.length&&((" "+t.className+" ").match(i)||[]).length!=e.length)}return!1}function b(t,e){var n,i;return function(){void 0===n&&(n=arguments,i=this,setTimeout(function(){1===n.length?t.call(i,n[0]):t.apply(i,n),n=void 0},e))}}function _(t,e){if(t&&e)for(var n in e)e.hasOwnProperty(n)&&(t[n]=e[n]);return t}function D(t){return Q?Q(t).clone(!0)[0]:Z&&Z.dom?Z.dom(t).cloneNode(!0):t.cloneNode(!0)}function y(t){for(var e=t.getElementsByTagName("input"),n=e.length;n--;){var i=e[n];i.checked&&it.push(i)}}if("undefined"==typeof window||!window.document)return function(){throw new Error("Sortable.js requires a window with a document")};var w,T,C,S,E,x,N,k,B,Y,O,X,A,M,P,R,I,L,F,U,j={},H=/\s+/g,W=/left|right|inline/,V="Sortable"+(new Date).getTime(),q=window,z=q.document,G=q.parseInt,Q=q.jQuery||q.Zepto,Z=q.Polymer,J=!1,K=!!("draggable"in z.createElement("div")),$=function(t){return!navigator.userAgent.match(/Trident.*rv[ :]?11\./)&&(t=z.createElement("x"),t.style.cssText="pointer-events:auto","auto"===t.style.pointerEvents)}(),tt=!1,et=Math.abs,nt=Math.min,it=[],ot=[],rt=b(function(t,e,n){if(n&&e.scroll){var i,o,r,a,s,l,c=n[V],d=e.scrollSensitivity,h=e.scrollSpeed,u=t.clientX,f=t.clientY,p=window.innerWidth,g=window.innerHeight;if(B!==n&&(k=e.scroll,B=n,Y=e.scrollFn,!0===k)){k=n;do{if(k.offsetWidth<k.scrollWidth||k.offsetHeight<k.scrollHeight)break}while(k=k.parentNode)}k&&(i=k,o=k.getBoundingClientRect(),r=(et(o.right-u)<=d)-(et(o.left-u)<=d),a=(et(o.bottom-f)<=d)-(et(o.top-f)<=d)),r||a||(a=(g-f<=d)-(f<=d),((r=(p-u<=d)-(u<=d))||a)&&(i=q)),j.vx===r&&j.vy===a&&j.el===i||(j.el=i,j.vx=r,j.vy=a,clearInterval(j.pid),i&&(j.pid=setInterval(function(){if(l=a?a*h:0,s=r?r*h:0,"function"==typeof Y)return Y.call(c,s,l,t);i===q?q.scrollTo(q.pageXOffset+s,q.pageYOffset+l):(i.scrollTop+=l,i.scrollLeft+=s)},24)))}},30),at=function(t){function e(t,e){return void 0!==t&&!0!==t||(t=n.name),"function"==typeof t?t:function(n,i){var o=i.options.group.name;return e?t:t&&(t.join?t.indexOf(o)>-1:o==t)}}var n={},i=t.group;i&&"object"==typeof i||(i={name:i}),n.name=i.name,n.checkPull=e(i.pull,!0),n.checkPut=e(i.put),n.revertClone=i.revertClone,t.group=n};t.prototype={constructor:t,_onTapStart:function(t){var e,i=this,o=this.el,r=this.options,a=r.preventOnFilter,s=t.type,l=t.touches&&t.touches[0],c=(l||t).target,h=t.target.shadowRoot&&t.path&&t.path[0]||c,u=r.filter;if(y(o),!w&&!(/mousedown|pointerdown/.test(s)&&0!==t.button||r.disabled)&&(c=n(c,r.draggable,o))&&N!==c){if(e=v(c,r.draggable),"function"==typeof u){if(u.call(this,t,c,this))return d(i,h,"filter",c,o,e),void(a&&t.preventDefault())}else if(u&&(u=u.split(",").some(function(t){if(t=n(h,t.trim(),o))return d(i,t,"filter",c,o,e),!0})))return void(a&&t.preventDefault());r.handle&&!n(h,r.handle,o)||this._prepareDragStart(t,l,c,e)}},_prepareDragStart:function(t,e,n,i){var o,a=this,l=a.el,h=a.options,f=l.ownerDocument;n&&!w&&n.parentNode===l&&(L=t,E=l,T=(w=n).parentNode,x=w.nextSibling,N=n,R=h.group,M=i,this._lastX=(e||t).clientX,this._lastY=(e||t).clientY,w.style["will-change"]="transform",o=function(){a._disableDelayedDrag(),w.draggable=a.nativeDraggable,s(w,h.chosenClass,!0),a._triggerDragStart(t,e),d(a,E,"choose",w,E,M)},h.ignore.split(",").forEach(function(t){c(w,t.trim(),u)}),r(f,"mouseup",a._onDrop),r(f,"touchend",a._onDrop),r(f,"touchcancel",a._onDrop),r(f,"pointercancel",a._onDrop),r(f,"selectstart",a),h.delay?(r(f,"mouseup",a._disableDelayedDrag),r(f,"touchend",a._disableDelayedDrag),r(f,"touchcancel",a._disableDelayedDrag),r(f,"mousemove",a._disableDelayedDrag),r(f,"touchmove",a._disableDelayedDrag),r(f,"pointermove",a._disableDelayedDrag),a._dragStartTimer=setTimeout(o,h.delay)):o())},_disableDelayedDrag:function(){var t=this.el.ownerDocument;clearTimeout(this._dragStartTimer),a(t,"mouseup",this._disableDelayedDrag),a(t,"touchend",this._disableDelayedDrag),a(t,"touchcancel",this._disableDelayedDrag),a(t,"mousemove",this._disableDelayedDrag),a(t,"touchmove",this._disableDelayedDrag),a(t,"pointermove",this._disableDelayedDrag)},_triggerDragStart:function(t,e){(e=e||("touch"==t.pointerType?t:null))?(L={target:w,clientX:e.clientX,clientY:e.clientY},this._onDragStart(L,"touch")):this.nativeDraggable?(r(w,"dragend",this),r(E,"dragstart",this._onDragStart)):this._onDragStart(L,!0);try{z.selection?setTimeout(function(){z.selection.empty()}):window.getSelection().removeAllRanges()}catch(t){}},_dragStarted:function(){if(E&&w){var e=this.options;s(w,e.ghostClass,!0),s(w,e.dragClass,!1),t.active=this,d(this,E,"start",w,E,M)}else this._nulling()},_emulateDragOver:function(){if(F){if(this._lastX===F.clientX&&this._lastY===F.clientY)return;this._lastX=F.clientX,this._lastY=F.clientY,$||l(C,"display","none");var t=z.elementFromPoint(F.clientX,F.clientY),e=t,n=ot.length;if(e)do{if(e[V]){for(;n--;)ot[n]({clientX:F.clientX,clientY:F.clientY,target:t,rootEl:e});break}t=e}while(e=e.parentNode);$||l(C,"display","")}},_onTouchMove:function(e){if(L){var n=this.options,i=n.fallbackTolerance,o=n.fallbackOffset,r=e.touches?e.touches[0]:e,a=r.clientX-L.clientX+o.x,s=r.clientY-L.clientY+o.y,c=e.touches?"translate3d("+a+"px,"+s+"px,0)":"translate("+a+"px,"+s+"px)";if(!t.active){if(i&&nt(et(r.clientX-this._lastX),et(r.clientY-this._lastY))<i)return;this._dragStarted()}this._appendGhost(),U=!0,F=r,l(C,"webkitTransform",c),l(C,"mozTransform",c),l(C,"msTransform",c),l(C,"transform",c),e.preventDefault()}},_appendGhost:function(){if(!C){var t,e=w.getBoundingClientRect(),n=l(w),i=this.options;s(C=w.cloneNode(!0),i.ghostClass,!1),s(C,i.fallbackClass,!0),s(C,i.dragClass,!0),l(C,"top",e.top-G(n.marginTop,10)),l(C,"left",e.left-G(n.marginLeft,10)),l(C,"width",e.width),l(C,"height",e.height),l(C,"opacity","0.8"),l(C,"position","fixed"),l(C,"zIndex","100000"),l(C,"pointerEvents","none"),i.fallbackOnBody&&z.body.appendChild(C)||E.appendChild(C),t=C.getBoundingClientRect(),l(C,"width",2*e.width-t.width),l(C,"height",2*e.height-t.height)}},_onDragStart:function(t,e){var n=t.dataTransfer,i=this.options;this._offUpEvents(),R.checkPull(this,this,w,t)&&((S=D(w)).draggable=!1,S.style["will-change"]="",l(S,"display","none"),s(S,this.options.chosenClass,!1),E.insertBefore(S,w),d(this,E,"clone",w)),s(w,i.dragClass,!0),e?("touch"===e?(r(z,"touchmove",this._onTouchMove),r(z,"touchend",this._onDrop),r(z,"touchcancel",this._onDrop),r(z,"pointermove",this._onTouchMove),r(z,"pointerup",this._onDrop)):(r(z,"mousemove",this._onTouchMove),r(z,"mouseup",this._onDrop)),this._loopId=setInterval(this._emulateDragOver,50)):(n&&(n.effectAllowed="move",i.setData&&i.setData.call(this,n,w)),r(z,"drop",this),setTimeout(this._dragStarted,0))},_onDragOver:function(i){var o,r,a,s,c=this.el,d=this.options,u=d.group,g=t.active,v=R===u,m=!1,b=d.sort;if(void 0!==i.preventDefault&&(i.preventDefault(),!d.dragoverBubble&&i.stopPropagation()),!w.animated&&(U=!0,g&&!d.disabled&&(v?b||(s=!E.contains(w)):I===this||(g.lastPullMode=R.checkPull(this,g,w,i))&&u.checkPut(this,g,w,i))&&(void 0===i.rootEl||i.rootEl===this.el))){if(rt(i,d,this.el),tt)return;if(o=n(i.target,d.draggable,c),r=w.getBoundingClientRect(),I!==this&&(I=this,m=!0),s)return e(g,!0),T=E,void(S||x?E.insertBefore(w,S||x):b||E.appendChild(w));if(0===c.children.length||c.children[0]===C||c===i.target&&p(c,i)){if(0!==c.children.length&&c.children[0]!==C&&c===i.target&&(o=c.lastElementChild),o){if(o.animated)return;a=o.getBoundingClientRect()}e(g,v),!1!==h(E,c,w,r,o,a,i)&&(w.contains(c)||(c.appendChild(w),T=c),this._animate(r,w),o&&this._animate(a,o))}else if(o&&!o.animated&&o!==w&&void 0!==o.parentNode[V]){O!==o&&(O=o,X=l(o),A=l(o.parentNode));var _=(a=o.getBoundingClientRect()).right-a.left,D=a.bottom-a.top,y=W.test(X.cssFloat+X.display)||"flex"==A.display&&0===A["flex-direction"].indexOf("row"),N=o.offsetWidth>w.offsetWidth,k=o.offsetHeight>w.offsetHeight,B=(y?(i.clientX-a.left)/_:(i.clientY-a.top)/D)>.5,Y=o.nextElementSibling,M=!1;if(y){var P=w.offsetTop,L=o.offsetTop;M=P===L?o.previousElementSibling===w&&!N||B&&N:o.previousElementSibling===w||w.previousElementSibling===o?(i.clientY-a.top)/D>.5:L>P}else m||(M=Y!==w&&!k||B&&k);var F=h(E,c,w,r,o,a,i,M);!1!==F&&(1!==F&&-1!==F||(M=1===F),tt=!0,setTimeout(f,30),e(g,v),w.contains(c)||(M&&!Y?c.appendChild(w):o.parentNode.insertBefore(w,M?Y:o)),T=w.parentNode,this._animate(r,w),this._animate(a,o))}}},_animate:function(t,e){var n=this.options.animation;if(n){var i=e.getBoundingClientRect();1===t.nodeType&&(t=t.getBoundingClientRect()),l(e,"transition","none"),l(e,"transform","translate3d("+(t.left-i.left)+"px,"+(t.top-i.top)+"px,0)"),e.offsetWidth,l(e,"transition","all "+n+"ms"),l(e,"transform","translate3d(0,0,0)"),clearTimeout(e.animated),e.animated=setTimeout(function(){l(e,"transition",""),l(e,"transform",""),e.animated=!1},n)}},_offUpEvents:function(){var t=this.el.ownerDocument;a(z,"touchmove",this._onTouchMove),a(z,"pointermove",this._onTouchMove),a(t,"mouseup",this._onDrop),a(t,"touchend",this._onDrop),a(t,"pointerup",this._onDrop),a(t,"touchcancel",this._onDrop),a(t,"pointercancel",this._onDrop),a(t,"selectstart",this)},_onDrop:function(e){var n=this.el,i=this.options;clearInterval(this._loopId),clearInterval(j.pid),clearTimeout(this._dragStartTimer),a(z,"mousemove",this._onTouchMove),this.nativeDraggable&&(a(z,"drop",this),a(n,"dragstart",this._onDragStart)),this._offUpEvents(),e&&(U&&(e.preventDefault(),!i.dropBubble&&e.stopPropagation()),C&&C.parentNode&&C.parentNode.removeChild(C),E!==T&&"clone"===t.active.lastPullMode||S&&S.parentNode&&S.parentNode.removeChild(S),w&&(this.nativeDraggable&&a(w,"dragend",this),u(w),w.style["will-change"]="",s(w,this.options.ghostClass,!1),s(w,this.options.chosenClass,!1),d(this,E,"unchoose",w,E,M),E!==T?(P=v(w,i.draggable))>=0&&(d(null,T,"add",w,E,M,P),d(this,E,"remove",w,E,M,P),d(null,T,"sort",w,E,M,P),d(this,E,"sort",w,E,M,P)):w.nextSibling!==x&&(P=v(w,i.draggable))>=0&&(d(this,E,"update",w,E,M,P),d(this,E,"sort",w,E,M,P)),t.active&&(null!=P&&-1!==P||(P=M),d(this,E,"end",w,E,M,P),this.save()))),this._nulling()},_nulling:function(){E=w=T=C=x=S=N=k=B=L=F=U=P=O=X=I=R=t.active=null,it.forEach(function(t){t.checked=!0}),it.length=0},handleEvent:function(t){switch(t.type){case"drop":case"dragend":this._onDrop(t);break;case"dragover":case"dragenter":w&&(this._onDragOver(t),o(t));break;case"selectstart":t.preventDefault()}},toArray:function(){for(var t,e=[],i=this.el.children,o=0,r=i.length,a=this.options;o<r;o++)n(t=i[o],a.draggable,this.el)&&e.push("cid["+o+"]="+t.getAttribute(a.dataIdAttr)||g(t));return e},sort:function(t){var e={},i=this.el;this.toArray().forEach(function(t,o){var r=i.children[o];n(r,this.options.draggable,i)&&(e[t]=r)},this),t.forEach(function(t){e[t]&&(i.removeChild(e[t]),i.appendChild(e[t]))})},save:function(){var t=this.options.store;t&&t.set(this)},closest:function(t,e){return n(t,e||this.options.draggable,this.el)},option:function(t,e){var n=this.options;if(void 0===e)return n[t];n[t]=e,"group"===t&&at(n)},destroy:function(){var t=this.el;t[V]=null,a(t,"mousedown",this._onTapStart),a(t,"touchstart",this._onTapStart),a(t,"pointerdown",this._onTapStart),this.nativeDraggable&&(a(t,"dragover",this),a(t,"dragenter",this)),Array.prototype.forEach.call(t.querySelectorAll("[draggable]"),function(t){t.removeAttribute("draggable")}),ot.splice(ot.indexOf(this._onDragOver),1),this._onDrop(),this.el=t=null}},r(z,"touchmove",function(e){t.active&&e.preventDefault()});try{window.addEventListener("test",null,Object.defineProperty({},"passive",{get:function(){J={capture:!1,passive:!1}}}))}catch(t){}return t.utils={on:r,off:a,css:l,find:c,is:function(t,e){return!!n(t,e,t)},extend:_,throttle:b,closest:n,toggleClass:s,clone:D,index:v},t.create=function(e,n){return new t(e,n)},t.version="1.6.1",t});
