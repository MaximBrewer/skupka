
dojo.declare("OfflajnList", null, {
	constructor: function(args) {
    this.fireshow = 0;
    this.map = {};
    this.names = new Array();
    this.list = new Array;
	  dojo.mixin(this,args);
    this.showed = 0;
    this.focus = 0;
    this.zindex = 6;
    window.offlajnlistzindex = 10;
    if(this.height) this.height++;
    this.lineHeight = 20;
    this.init();
  },

  init: function() {
    this.hidden = dojo.byId(this.name);
    this.active = this.hidden;

    this.hidden.listobj = this;
    this.hidden.options = this.options;
    this.hidden.selectedIndex = this.selectedIndex;

    dojo.connect(this.hidden, 'onchange', this, 'setValue');
    this.change = 0;

    this.container = dojo.byId('offlajnlistcontainer' + this.name);
    this.offlajnlist = dojo.query('.offlajnlist', this.container)[0];
    this.currentText = dojo.query('.offlajnlistcurrent', this.container)[0];

    if (this.json && window[this.json] && window[this.json].length) {
      this.hidden.options = this.options = this.options.concat(window[this.json]);
      this.hidden.selectedIndex = this.selectedIndex = 0;
      this.currentText.innerHTML = this.options[0].text;
      for (var i = 0; i < this.options.length; i++)
        if (this.options[i].value == this.hidden.value) {
          this.hidden.selectedIndex = this.selectedIndex = i;
          this.currentText.innerHTML = this.options[i].text;
          break;
        }
    }

    if (this.width) {
      dojo.style(this.container, 'minWidth', this.width+'px');
    } else {
      dojo.style(this.container, 'minWidth', Math.ceil(dojo.style(this.container, 'width')+1)+'px');
      if(dojo.isIE == 7) {
        var span = dojo.query('#offlajnlistcontainer' + this.name + ' span');
        dojo.style(this.container, 'width', dojo.style(span[0], 'width')+30+'px');
      }
    }

    dojo.connect(this.container, 'onclick', this, 'controller');
    this.options.forEach(function(o, i){
      this.map[o.value] = i;
      this.names[i] = o.text;
    },this);
    this.keyListener;
  },

  initSelectBox: function(){
    if(this.selectbox) return;

    var i, elements = '<div class="content">';
    for (i = 0; i < this.options.length; i++)
      elements += '<div class="listelement">'+ this.options[i].text +'</div>';
    elements += '</div>';

    this.selectbox = dojo.create('div', {'id': 'offlajnlistelements' + this.name, 'class': 'offlajnlistelements', 'innerHTML': elements}, this.container, "after");
    this.list = dojo.query('.listelement', this.selectbox);

    this.list.connect('onmouseenter', this, 'addActive');

    dojo.style(this.selectbox, {
      opacity: 0,
      display: 'block'
    });

    this.lineHeight = dojo.position(this.list[0]).h;
    dojo.style(this.selectbox, {
      height: (this.height) ? this.height * this.lineHeight + 'px' : 'auto'
    });

    if(this.height) {
      this.content = dojo.query('#offlajnlistelements' + this.name + ' .content')[0];
      dojo.style(this.content, 'height', this.list.length * this.lineHeight + 'px');
      this.scrollbar = new OfflajnScroller({
        'extraClass': 'single-select',
        'selectbox': this.selectbox,
        'content': this.content
      });
    }

    this.maxW = 0;
    this.list.forEach(function(el, i){
      if (this.options[i].value == 'optgroup') dojo.addClass(el, "optgroup");
      el.i = i;
    },this);

    this.list.connect('onclick', this, 'selected');

    this.selectbox.h = dojo.marginBox(this.selectbox).h;
    dojo.style(this.selectbox, {
      height: 0
    });
    dojo.connect(document, 'onclick', this, 'blur');
    dojo.connect(this.selectbox, 'onclick', this, 'focused');

    if(this.fireshow)
      OfflajnFireEvent(this.hidden, 'click');
  },

  controller: function(){
    this.focused();
    this.initSelectBox();
    if(this.showed == 0){
      this.reposition();
      this.showList();
    }else{
      this.hideList();
    }
  },

  reposition: function(){
    var pos = dojo.coords(this.container, true);
    if(this.selectbox){

      dojo.style(this.selectbox, {
        left: pos.l + "px",
        top: pos.t + pos.h  + "px",
        width: pos.w -2 +"px" //-2px because of the side-borders
      });
      if(this.content) {
        dojo.style(this.content,{

         'width': pos.w - 12 + 'px',
         'float': 'left'
         });
      }
    }
  },

  showList: function(){
    this.keyListener = dojo.connect(document, 'keydown', this, 'keySearch');
    if(this.anim) this.anim.stop();
    this.showed = 1;
    dojo.addClass(this.container,'openedlist');
    dojo.addClass(this.selectbox,'openedlist');
    dojo.removeClass(this.active,'active');
    dojo.addClass(this.list[this.hidden.selectedIndex],'selected active');
    if(this.height) {
      var p = this.hidden.selectedIndex * this.lineHeight;
      this.scrollbar.setPosition(p);
    }
    this.active = this.list[this.hidden.selectedIndex];

    dojo.style(this.offlajnlist, 'zIndex', ++window.offlajnlistzindex);
    dojo.style(this.selectbox, {
      display: 'block',
      zIndex: window.offlajnlistzindex-1
    });
    window.offlajnlistzindex++;

    this.anim = dojo.animateProperty({
      node: this.selectbox,
      properties: {
          opacity : 1,
          height: this.selectbox.h
      }
    }).play();
  },

  keySearch: function(e) {
    //console.log(String.fromCharCode(e.keyCode));
    if(e.keyCode == 13) {
      this.hideList();
      OfflajnFireEvent(this.hidden, 'change');
      this.change = 0;
    } else if(e.keyCode == 38) {
      e.preventDefault();
      var index = this.hidden.selectedIndex-1;
        this.setSelected(index);
    } else if(e.keyCode == 40) {
      e.preventDefault();
      var index = this.hidden.selectedIndex+1;
        this.setSelected(index);
    }
    //console.log(this.names);
    var scroll = this.scrollbar;
    for(var i=0;i<this.names.length;i++) {
      if(this.names[i].toLowerCase().indexOf(String.fromCharCode(e.keyCode).toLowerCase()) == 0) {
        this.setSelected(i);
        break;
      }
    }
  },

  hideList: function(){
    dojo.disconnect(this.keyListener);
    if(this.anim) this.anim.stop();
    if(!this.selectbox) return;

    this.showed = 0;

    var h = dojo.marginBox(this.selectbox).h;
    dojo.removeClass(this.container,'openedlist');
    this.anim = dojo.animateProperty({
      node: this.selectbox,
      properties: {
          opacity : 0,
          height: 0
      },
      onEnd: dojo.hitch(this, function(el){
        dojo.style(el, {
          display: 'none',
          height: '0',
          zIndex: this.zindex-1
        });
        dojo.style(this.offlajnlist, 'zIndex', this.zindex);
        dojo.removeClass(this.selectbox,'openedlist');
      })
    }).play();
  },

  selected: function(e){
    if (dojo.hasClass(e.currentTarget, 'optgroup')) return;
    if(this.list[this.hidden.selectedIndex])
      dojo.removeClass(this.list[this.hidden.selectedIndex],'selected active');
    this.hidden.selectedIndex = e.target.i;
    this.hidden.value = this.hidden.options[this.hidden.selectedIndex].value;

    this.currentText.innerHTML = this.hidden.options[this.hidden.selectedIndex].text;
    if(this.list[this.hidden.selectedIndex])
      dojo.addClass(this.list[this.hidden.selectedIndex],'selected active');
    this.hideList();
    OfflajnFireEvent(this.hidden, 'change');
    this.change = 0;
  },

  setSelected: function(val) {
    if(!this.list[val]) return;
    if(this.list[this.hidden.selectedIndex])
      dojo.removeClass(this.list[this.hidden.selectedIndex],'selected active');

    this.hidden.selectedIndex = val;
    this.hidden.value = this.hidden.options[this.hidden.selectedIndex].value;

    this.currentText.innerHTML = this.hidden.options[this.hidden.selectedIndex].text;
    if(this.list[this.hidden.selectedIndex])
      dojo.addClass(this.list[this.hidden.selectedIndex],'selected active');

    if(this.height) {
        var p = this.hidden.selectedIndex * this.lineHeight;
        this.scrollbar.setPosition(p);
    }
  },

  addActive: function(e){
    var el = e.target;
    if(el != this.active){
      dojo.removeClass(this.active,'active');
      dojo.addClass(el,'active');
      this.active = el;
    }
  },

  focused: function(){
    this.focus = 1;
  },

  blur: function(e){
    if(!this.focus){
      this.hideList();
    }
    this.focus = 0;
  },

  setValue: function(e) {
    if(!this.change && this.map[this.hidden.value] != this.hidden.selectedIndex) {
      this.change = 1;
      e.target.i = this.map[this.hidden.value] ? this.map[this.hidden.value] : 0;
      this.selected(e);
    }
  }
});