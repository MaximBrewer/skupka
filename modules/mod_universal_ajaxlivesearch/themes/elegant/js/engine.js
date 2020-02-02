var AJAXSearch = {};

dojo.declare("AJAXSearchelegant", AJAXSearchDropBase, {
  constructor: function(args) {
    this.resultboxTopOffset = 0;
  },
  
  closeResults : function(e){
  dojo.style(this.textBox,"paddingRight","39px");
    if(e && e.button && e.button > 0) return;
    if(this.dummyCloseResult == 1){
      this.dummyCloseResult = 0;
      return;
    }
    if(this.actFx && this.actFx.status() == "playing"){
      if(dojo.hasClass(this.textBox, "search-caption-on"))
        return;
      this.actFx.stop();
    }
    this.actFx = this.getCloseResultBoxAnimation();
    dojo.style(this.closeButton, "visibility", "hidden");
   // dojo.style(this.textBox,"paddingRight","30px");
    dojo.attr(this.textBox, "value", this.searchBoxCaption);
    dojo.addClass(this.textBox, "search-caption-on");
    this.suggestBox.value= ""
    
  },  
  
  getResultBoxAnimation: function(){
    return dojo.animateProperty({
      node: this.searchResultsMoovable, 
      properties: {
        height: {start: dojo.style(this.searchResultsMoovable, 'height'), end: this.innerHeight}
      }, 
      duration: 500
    }).play();
  },
  
  getCloseResultBoxAnimation: function(){
    return dojo.animateProperty({
      node: this.searchResultsMoovable, 
      properties: {
        height: 0
      }, 
      duration: 500, 
      onEnd : dojo.hitch(this,'removeResults')
    }).play();
  },
  
  getCategoryLeftPosition: function(){
    return this.textBoxPosition.x;
  }
});