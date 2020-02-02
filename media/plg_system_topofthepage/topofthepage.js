var plg_system_topofthepage_class = new Class({
    Implements: [Options],
    options: {
        buttontext:false,
        topalways:false,
        styles:false,
        scrollspy:200,
        opacity:1,
        smoothscroll:false,
        slidein:0,
        slideindir:'bottom',
        slider:false,
        zIndex:0
    },
    initialize: function(options) { 
        var self = this;
        options.opacity = (options.opacity <= 100 && options.opacity > 0)?options.opacity/100:1;
        self.setOptions(options);
        if(options.topalways) window.scrollTo(0,0);
        this.createTargetAnchor();
        if(self.options.zIndex > 0) {
//            NodeList.prototype.map = function(fn) { var a=[]; for ( var i=0; i<this.length; i++ ){ a.push(fn(this[i], i, this)); } return a; }; 
//            self.options.zIndex = Math.max.apply(null, document.getElementsByTagName('*').map(function(el){ return el.style.zIndex || 0; })) + 1;
            self.options.zIndex = highZ(document.body) + 1;
        }
        this.createButton();
        this.scrollSpy();
        if(options.smoothscroll) this.smoothScroll();
    },
    createTargetAnchor:function() {
        var self = this;
        var styles = {            
            'height':0,
            'width':0,
            'line-height':0,
            'display':'block',
            'font-size':0,
            'overflow':'hidden',
            'position':'absolute',
            'top':0,
            'left':0
        };
        var target = new Element('a',{
            id:'topofthepage',
            html:'&#xA0;',
            styles:styles
        });
        target.setAttribute('name','topofthepage');
        target.inject(document.body,'top');
    },
    createButton:function(){  
        var self = this;
        if(self.options.styles.left == 'center') {
            Object.erase(self.options.styles,'left');
            var center = true;
        }
        if(self.options.zIndex > 0) self.options.styles['z-index'] = self.options.zIndex;
        var href = '#topofthepage';
        var base = $$('base');
        if(base.length) {
            var uri = new URI(base[0].getAttribute('href'));
            uri.set('fragment','topofthepage');
            href = uri.toURI();
        }
        var pageuri = new URI(window.location);
        if(pageuri.get('query').length) {
            pageuri.set('fragment','topofthepage');
            href = pageuri.toURI();
        }
        var gototop = new Element('a',{
            'id':'gototop',
            'href':href.toString(),
            'styles':self.options.styles
        }).inject(document.body,'bottom');
        if(self.options.buttontext !== false) {
            gototop.set('html',self.options.buttontext);
        }
        if(center) {
            var page = window.getScrollSize().x/2;
            var buttonsize = gototop.measure(function(){
                return this.getSize();
            });
            gototop.setStyle('left',(page-(buttonsize.x/2)));
        }
    },
    scrollSpy:function(){
        var self = this;     
        var buttonMorph = new Fx.Morph('gototop',{
            duration:self.options.displaydur,
            transition:'linear'
        });
        var buttonMorphIn = {"opacity":[0,self.options.opacity]};
        var buttonMorphOut = {"opacity":[self.options.opacity,0]};
        if(parseInt(self.options.slidein)) {
            var slideamount = 0;
            switch(self.options.slideindir) {
                case 'top':
                    slideamount = document.id('gototop').getSize().y;
                    property = 'margin-bottom';
                    break;
                case 'bottom':
                    slideamount = document.id('gototop').getSize().y;
                    property = 'margin-top';
                    break;
                case 'left':
                    slideamount = document.id('gototop').getSize().x;
                    property = 'margin-right';
                    break;
                case 'right':
                    slideamount = document.id('gototop').getSize().x;
                    property = 'margin-left';
                    break;
            }
            var v = document.id('gototop').getStyle(property);
            var h = parseInt(v.replace('px',''))+slideamount;
            buttonMorphIn[property]=[h+'px',v];
            buttonMorphOut[property]=[v,h+'px'];
        }
        var scrollspy = new ScrollSpy({
            min:self.options.scrollspy,
            container: window,
            onEnter: function(position,enters) {
                buttonMorph.start(buttonMorphIn);
            },
            onLeave: function(position,leaves) {
                buttonMorph.start(buttonMorphOut);
            }
        });
    },
    smoothScroll:function(){
        var self = this;
        self.options.smoothscroll['links']='#gototop';
        var smoothscroll = new SmoothScroll(self.options.smoothscroll);
    }
});
window.addEvent('domready',function(){
    var totp = new plg_system_topofthepage_class(window.plg_system_topofthepage_options);
});
function highZ(parent, limit){
    limit = limit || Infinity;
    parent = parent || document.body;
    var who, temp, max= 1, A= [], i= 0;
    var children = parent.childNodes, length = children.length;
    while(i<length){
        who = children[i++];
        if (who.nodeType != 1) continue; // element nodes only
        if (deepCss(who,"position") !== "static") {
            temp = deepCss(who,"z-index");
            if (temp == "auto") { // z-index is auto, so not a new stacking context
                temp = highZ(who);
            } else {
                temp = parseInt(temp, 10) || 0;
            }
        } else { // non-positioned element, so not a new stacking context
            temp = highZ(who);
        }
        if (temp > max && temp <= limit) max = temp;                
    }
    return max;
}

function deepCss(who, css) {
    var sty, val, dv= document.defaultView || window;
    if (who.nodeType == 1) {
        sty = css.replace(/\-([a-z])/g, function(a, b){
            return b.toUpperCase();
        });
        val = who.style[sty];
        if (!val) {
            if(who.currentStyle) val= who.currentStyle[sty];
            else if (dv.getComputedStyle) {
                val= dv.getComputedStyle(who,"").getPropertyValue(css);
            }
        }
    }
    return val || "";
}