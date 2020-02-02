//toolTip usage:
// call dojo.global.toolTips.connectToolTips();

window.OfflajnFireEvent = function(element, event){
	if ((document.createEventObject && !dojo.isIE) || (document.createEventObject && dojo.isIE && dojo.isIE < 9)){
		var evt = document.createEventObject();
		var ret = element.fireEvent('on'+event,evt);
	}else{
		var evt = document.createEvent("HTMLEvents");
		evt.initEvent(event, true, true );
		var ret = !element.dispatchEvent(evt);
	}
	var mini = jQuery(".minicolors-input", element.parentNode);
	if (mini.length) setTimeout(function() {
		for (var i = 0; i < mini.length; i++)
			jQuery(mini[i]).attr('value', mini[i].value).keyup();
	}, 1);
	return ret;
};

dojo.declare("OfflajnParams", null, {
	constructor: function(args) {
    dojo.mixin(this,args);
    this.panelContainer = dojo.byId('module-sliders');
    var allpanels = dojo.query('.panel', this.panelContainer);
    var subpanels = dojo.query('.panel .panel', this.panelContainer);
    allpanels.diff(subpanels);
    this.panels = allpanels;
    this.relatedNews = dojo.byId('related-news-iframe');
    this.rightColumn = dojo.query('div.panel.dashboard .column.right', this.panelContainer)[0];
    this.boxTitle = dojo.query('.box-title', this.rightColumn)[0];
    this.contentBox = dojo.byId('content-box');
    this.generalInfo = dojo.query('.column.left iframe')[0];

    this.loadLastState();
    dojo.forEach(this.panels,function(panel,i){
      if(!dojo.attr(panel, 'id')){
        dojo.attr(panel, 'id', 'offlajnpanel-'+i)
      }
      var panelTitle = dojo.query('h3', panel)[0];
      dojo.connect(panelTitle,"onclick",this,"openClosePanel");
      var els = dojo.query('div.content', panel);
      if(els.length == 0) return;
      panelTitle.content = els[0];

      if(dojo.hasClass(panel, 'alwaysopen') || dojo.indexOf(this.lastState, dojo.attr(panel, "id")) >= 0){
        panelTitle.content.state = 1;  // Panel state: 1-open 0-close
        dojo.style(panelTitle.content,"opacity","1");
        dojo.style(panelTitle.content,"height","100%");
        dojo.style(panelTitle.content,"overflow","visible");
      }else{
        panelTitle.content.state = 0;  // Panel state: 1-open 0-close
        dojo.style(panelTitle.content,"opacity","0");
        dojo.style(panelTitle.content,"height","0");
        dojo.style(panelTitle.content,"overflow","hidden");
      }
    },this);
    dojo.connect(window, "onresize", this, "resizeBoxes");

    var optionsbasic30 = dojo.byId('options-basic');
    if(optionsbasic30)
      dojo.style(optionsbasic30, 'display', 'block');
    this.resizeBoxes();
    if(optionsbasic30)
      dojo.removeAttr(optionsbasic30, 'style');
    dojo.global.toolTips = this;
    this.connectToolTips();
    //window.labelFix = this.labelFix;
    //dojo.addOnLoad(window.labelFix);
  },

  loadLastState : function(){
    if(window.localStorage){
      if(!localStorage[this.moduleName+"lastState"]){
        var config = new Array("offlajnpanel-0");
        localStorage[this.moduleName+"lastState"] = dojo.toJson(config);
      }
      this.lastState = dojo.fromJson(localStorage[this.moduleName+"lastState"]);
    }
  },

  openClosePanel : function(event){
    var panelTitle = event.currentTarget;
    if(panelTitle.animation && panelTitle.animation.status() == "playing")
      panelTitle.animation.stop();

    if(panelTitle.content.state){
      panelTitle.content.state = 0;
      panelTitle.animation = dojo.animateProperty({
          node: panelTitle.content,
          properties: {
              height: 0,
              opacity : 0
          },
          beforeBegin: function(element){
            dojo.style(element,"overflow","hidden");
          },
          onEnd : function(element){
            element.state = 0;
          }
        }).play();
        this.lastState = dojo.fromJson(localStorage[this.moduleName+"lastState"]);
        var idx = this.lastState.indexOf(panelTitle.parentNode.id);
        if(idx!=-1) this.lastState.splice(idx, 1);
        localStorage[this.moduleName+"lastState"] = dojo.toJson(this.lastState);
    }else{
      var height=0;
      if(this.joomla17){
        height = dojo.style(panelTitle.content.children[0],'height');
      }else{
        height = dojo.style(panelTitle.content.children[0],'height');
        if (panelTitle.content.children[1]){
          height+= dojo.style(panelTitle.content.children[1],'height');
        }
      }
      panelTitle.content.state = 1;
      panelTitle.animation = dojo.animateProperty({
          node: panelTitle.content,
          properties: {
              height: {end: height , units:"px" },   // onEndre 100% ra magasságot, hogy ne ugráljon témaváltáskor
              opacity : 1
          },
          beforeBegin: function(element){
            dojo.style(element,"overflow","hidden");
          },
          onEnd : function(element){
            element.state = 1;
            dojo.style(element,"height","100%");
            dojo.style(element,"overflow","visible");
          }
        }).play();

        this.lastState = dojo.fromJson(localStorage[this.moduleName+"lastState"]);
        this.lastState.push(panelTitle.parentNode.id);
        localStorage[this.moduleName+"lastState"] = dojo.toJson(this.lastState);
    }
  },

  resizeBoxes : function(event){
    if(this.boxTitle || this.rightColumn) {
      var h = dojo.position(this.rightColumn).h-dojo.position(this.boxTitle).h;
    } else return;
    if (!h) return;
    if (this.relatedNews) this.relatedNews.style.height = h-1+"px";
    if (this.generalInfo) this.generalInfo.style.height = h-1+"px";
  },

  connectToolTips : function(parentElement){
    this.tips = dojo.query('.hasOfflajnTip', parentElement?parentElement:this.panelContainer);
    dojo.forEach(this.tips,function(tip){
      if (!tip.title) return;
      if (!tip.toolTipped) dojo.connect(tip,"onmouseenter",this,"showToolTip");
      tip.toolTipped = 1;
    },this);
  },

  showToolTip : function(event){
    var element = event.currentTarget;
    if (element && !element.toolTipText){
      element.toolTipText = element.title;
      element.title = "";
      var pos = dojo.position(element, true);
      var tooltippos = dojo.attr(element,'tooltippos') ? dojo.attr(element,'tooltippos') : 'R';
      if(pos.x < 200) tooltippos = 'T';
      element.tooltip = new WW.ToolTip({
  	    parent: element,
  	    wnd: dojo.body(),
  	    pos: tooltippos,
  	    ico: "Inf",
  	    msg: element.toolTipText
  		});
    }
    element.tooltip.play();
  }/*,

  labelFix: function(c){
    if(!c) c = dojo.byId('module-sliders');
    var labels = dojo.query('li > label', c);
    dojo.forEach(labels, function(el){
      var h = dojo.contentBox(el.parentNode).h;
      console.log(h);
      dojo.marginBox(el, {'h':h});
    });
  }*/

});

dojo.declare("WW.ToolTip", null, {

	// msg: String
	//  message
	msg: "",

	// dur: Integer
	//  animation duration
	dur: 300,

	// ico: String
	//  message icon
	// values:
	//	"Inf", "Err"
	ico: "Err",

	// pos: String
	//  horizontal message position
	// values:
	//  "L", "R", "T", "B"
	pos: "L",

	constructor: function(args) {
	  dojo.mixin(this, args);
    var div = dojo.create('div', {
      'class' : 'tool-tip-container',
      'innerHTML' : '<span class="tooltip'+this.ico+'">'+this.msg+'<div class="arrow'+this.pos+'"></div></span>'
    });
	  this.domNode = dojo.place(div, dojo.body());
		dojo.style(this.domNode, "opacity", 0);
		var p = dojo.position(this.parent, true);
		var bw = dojo.position(document.documentElement).w;
		this.domNode.style.top = p.y + "px";
    if (this.pos != 'R') this.domNode.style.left = p.x + "px";
		this.domNode.style.display = "block";
    switch (this.pos) {
      case 'L' : this.prop = {left: {start:p.x+p.w+60, end:p.x+p.w+10}}; break;
      case 'R' : this.prop = {right:{start:bw-p.x+60, end:bw-p.x+10}}; break;
      case 'T' : this.prop = {top: {start:p.y-80, end:p.y-30}}; break;
      case 'B' : this.prop = {top: {start:p.y+p.h+60, end:p.y+p.h+10}}; break;
    }
		this.prop.opacity = 1;
  	this.ani = dojo.animateProperty({
	    node: this.domNode,
	    duration: this.dur,
	    properties: this.prop
		});
    dojo.connect(this.parent, "onmouseenter", this, "open");
		this.onclickWnd = dojo.connect(this.parent, "onmouseleave", this, "close");
		this.onEsc = dojo.connect(document, "onkeypress", this, "onkeypress");
	},

  play: function(){
    if(this.ani2) this.ani2.stop();
		this.ani.play();
  },

	onkeypress: function(e) {
	  if (e.keyCode == 27) this.close();
	},

  open: function() {
    this.domNode.style.visibility = "visible";
  },

	close: function(event) {
	  this.ani.stop();
    //dojo.disconnect(this.onEsc);
	  //dojo.disconnect(this.onclickWnd);
    /*
	  var prop;
	    {left: this.prop.left.start} :
	    {right:this.prop.right.start};
    */
    switch (this.pos) {
      case 'L' : prop = {left: this.prop.left.start}; break;
      case 'R' : prop = {right:this.prop.right.start}; break;
      case 'T' : prop = {top:this.prop.top.start}; break;
      case 'B' : prop = {top:this.prop.bottom.start}; break;
    }
		prop.opacity = 0;
  	this.ani2 = dojo.animateProperty({
	    node: this.domNode,
	    duration: this.dur,
	    properties: prop,
			onEnd: function() {this.node.style.visibility = "hidden";}
		}).play();
	}

});

Array.prototype.diff = function(a) {
    return this.filter(function(i) {return !(a.indexOf(i) > -1);});
};

/* bugfix: save, close, etc.. buttons are not working on admin */
document.getElementById = (function(parent) {
  return function(id) {
    var res = parent.apply(this, arguments);
    return !res && id == 'adminForm' ? document.adminForm : res;
  };
})(document.getElementById);
