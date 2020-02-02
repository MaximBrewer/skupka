var AJAXSearch = {};

dojo.copyTouch = function(sourceObj, targetObj){
    targetObj.screenX = sourceObj.screenX;
    targetObj.screenY = sourceObj.screenY;
    targetObj.identifier = sourceObj.identifier;
};

dojo.declare("AJAXSearchflat", AJAXSearchBase, {
  constructor: function(args) {
    this.controlPanelShoved = 0;
    this.noResultMessageDivShoved = 0;
    this.searchResults = dojo.byId("offlajn-ajax-tile-results");
    if(!this.searchResults){
      alert('For using the Flat theme you should enable the "AJAX Live Search results" module or insert this code into your template:\n <div id="offlajn-ajax-tile-results"></div>');
    }
    dojo.style(this.searchResults,"height","0px");
    this.resultWidthStyle = dojo.create("style", {}, document.head);
    this.actualPage = 1;

    this.searchPageOut = dojo.create("div", {id: "offlajn-ajax-search-page-out"}, this.searchResults);
    this.searchResultsInner = dojo.create("div", {id: "offlajn-ajax-search-results-inner"}, this.searchResults);
    this.shovedResultIds=[];

    this.touch = {screenX: 0, screenY: 0, identifier: ''};
    dojo.connect(this.searchResultsInner, "ontouchstart", this, "touchStart");
    dojo.connect(this.searchResultsInner, "ontouchend", this, "touchEnd");

    dojo.connect(this.textBox, "onclick", this, "stopEventBubble");

    this.resultHeightFx = dojo.animateProperty({
      node: this.searchResults,
      properties: {
/*          opacity: 1,*/
          height: { end: dojo.position(this.searchResultsInner).h, units:"px" }
      },
      duration: 300
    });

    this.categoryChooserTopCorrection = 5;

    this.searchImageWidth = parseInt(this.searchImageWidth);
    this.resultsPadding = parseInt(this.resultsPadding);
  },

  createControlPanel : function(){
    if (!this.controlPanelShoved){
      this.controlPanel = dojo.create("div", {"class": "offlajn-ajax-search-control-panel"}, this.searchResults, "first");
      this.innerControlPanel = dojo.create("div", {"class": "inner-control-panel"}, this.controlPanel);
      this.previousButton = dojo.create("div", {"class": "offlajn-prev offlajn-button", innerHTML: this.controllerPrev}, this.innerControlPanel);
      this.nextButton = dojo.create("div", {"class": "offlajn-next offlajn-button", innerHTML: this.controllerNext}, this.innerControlPanel);
      this.paginators = dojo.create("div", {"class": "offlajn-paginators"}, this.innerControlPanel);
      if(!('ontouchstart' in window)) dojo.connect(this.previousButton, "onclick", dojo.hitch(this,'refreshPage',-1,"flipright"));
      dojo.connect(this.previousButton, "ontouchend", dojo.hitch(this,'refreshPage',-1,"flipright"));
      if(!('ontouchstart' in window)) dojo.connect(this.nextButton, "onclick", dojo.hitch(this,'refreshPage',1,"flipleft"));
      dojo.connect(this.nextButton, "ontouchend", dojo.hitch(this,'refreshPage',1,"flipleft"));
      this.controlPanelShoved = 1;
    }
  },

  closeControlPanel : function(){
    dojo.query(".offlajn-ajax-search-control-panel").forEach(dojo.destroy);
    this.controlPanelShoved = 0;
  },
  type: function(evt, force){
    this.keycode = evt.keyCode || evt.which;
    this.ktype = evt.type;
    clearTimeout(this.t);
    this.t = setTimeout(dojo.hitch(this, function() {
      if(((this.targetsearch == 3 && this.keycode==13) || (this.keycode>40 || this.keycode==13) || this.keycode==32 || this.keycode==8 || this.keycode==0 || this.ktype=="click") && this.textBox.value.length >=this.minChars || force==1){
       var categories = new Array();
        dojo.forEach(this.searchCategoriesList, function(entry, i){
          if (dojo.hasClass(entry, "selected"))
            categories.push(dojo.attr(entry,"id").match(/\d+/)[0]);
        });
        dojo.style(this.closeButton, "visibility", "visible");
        dojo.style(this.textBox,"paddingRight","70px");
        dojo.addClass(this.closeButton, "search-area-loading");
        dojo.xhrGet({
            url : this.searchFormUrl,
            content: { option: "com_universal_ajax_live_search", lang: this.lang, module_id : this.moduleId, search_exp: this.textBox.value, 'categ[]' : categories, dojo_preventCache : new Date().getTime() },
            handleAs:"text",
            preventCache : false,
            load: dojo.hitch(this,'processResult'),
            error: function(e){
              console.log('Error: '+e);
            }
        });
      }
    }), this.keypressWait);
  },


  processResult : function(d,xhr){
    d = d.match(/startofofflajnsearchresult(.+)endofofflajnsearchresult/)[1];
    try{
      var data = eval('('+d+')');
    }catch(err){
      alert('Error: '+d); return;
    }

    // timestamp check if this is the latest search
    var regexp = /.*&dojo_preventCache=(\d+)/i;
    var result = xhr.url.match(regexp);
    if (result[1]){
      if (result[1]>this.timeStamp){
        this.timeStamp = result[1];
      }else{
        dojo.removeClass(this.closeButton, "search-area-loading");
        dojo.style(this.closeButton, "visibility", "visible");
        return;
      }
    }

    this.list=[];
    this.pluginCounter=[];
    this.selected = 0;
    this.onResize();
    this.newResultIds=[];
    this.newResults = {};

    if(data.length!=0 && !data.nores){
      for(var i in data){
        var pluginResults = data[i];
        for(var j=0;j<pluginResults.length;j++){
          pluginResults[j].pluginName = i;
          this.newResultIds.push(pluginResults[j].id);
          this.newResults[pluginResults[j].id] = pluginResults[j];  // THE NEW RESULTS AFTER AN AJAX LOAD
        }
      }

      this.createControlPanel();
      if (this.noResultMessageDivShoved){
        dojo.destroy(this.noResultMessageDiv);
        this.noResultMessageDivShoved = 0;
        dojo.query("div[class=no-result-suggest]").forEach(dojo.destroy);
      }

      if(this.actualPage==1){
        var correction = 0;   //if an item is removed the array will be smaller
        var length = this.shovedResultIds.length;
        for(var i=0;i<length && i<this.resultsPerPage;i++){
          if(dojo.indexOf(this.newResultIds.slice(0,this.resultsPerPage), this.shovedResultIds[i-correction])==-1){  //not in array
            this.removeCard(this.shovedResultIds[i-correction]);
            correction++;
          }
        }

        for(var i=0;i<this.newResultIds.length && i<this.resultsPerPage;i++){
          if(dojo.indexOf(this.shovedResultIds, this.newResultIds[i])==-1 ){
            this.addCard(this.newResults[this.newResultIds[i]]);
            this.shovedResultIds.push(this.newResultIds[i]);
          }
        }
      }else{
        var length = this.shovedResultIds.length;
        for(var i=0;i<length;i++){
          this.removeCard(this.shovedResultIds[0]);
        }

        for(var i=0;i<this.newResultIds.length && i<this.resultsPerPage;i++){
          if(dojo.indexOf(this.shovedResultIds, this.newResultIds[i])==-1 ){
            this.addCard(this.newResults[this.newResultIds[i]]);
            this.shovedResultIds.push(this.newResultIds[i]);
          }
        }

        this.actualPage = 1;
      }

      /*Paginators begin*/
      this.pageNumber = Math.floor(this.newResultIds.length/this.resultsPerPage+0.99999); // The number of the pages
      this.paginators.innerHTML = "";
      for(var i=1;i<=this.pageNumber;i++){
        var el = dojo.create("div", {"class": "offlajn-button", innerHTML:i}, this.paginators);
        if (i==1) dojo.addClass(el, "pushed");
        el.pageID = i;
        if(!('ontouchstart' in window)) dojo.connect(el, "onclick", this, "jumptoPage");
        dojo.connect(el, "ontouchend", this, "jumptoPage");
      }
      /*Paginators end*/

    /*Get the first card height*/

    this.cardHeight = dojo.position(dojo.byId(this.shovedResultIds[0])).h;
/*    if(this.fullWidth=="1"){
      this.cardHeight = (dojo.position(this.searchResults).w*this.resultImageHeight/this.resultImageWidth +10);
    }*/
    }else if (data.nores && data.nores[0] && data.nores[0].tag && (data.nores.length>1 || this.textBox.value!=data.nores[0].tag)){

      this.createControlPanel();
      this.paginators.innerHTML = "";
      var length = this.shovedResultIds.length;
      for(var i=0;i<length;i++){
        this.removeCard(this.shovedResultIds[0]);
      }
      this.shovedResultIds = [];
      dojo.query("div[class=no-result-suggest]").forEach(dojo.destroy);

      if(!this.noResultMessageDivShoved){
        this.noResultMessageDiv = dojo.create("div", {'id': "no-result-message", innerHTML: '<span>'+this.stext+'</span>'}, this.searchResultsInner);
        this.noResultMessageDivShoved = 1;
      }

      for (var j=0;j<data.nores.length-1;j++) {
        if (this.textBox.value!=data.nores[j].tag)
          dojo.create("div", {'class': "no-result-suggest", innerHTML: data.nores[j].tag}, this.searchResultsInner);
      }
      dojo.query("div[class=no-result-suggest]").connect("onclick", this,  "changeText");
      dojo.query("div[class=no-result-suggest]").connect("onclick", this,  "type");
    }else{ // if there are no results
      this.createControlPanel();
      this.paginators.innerHTML = "";
      var length = this.shovedResultIds.length;
      for(var i=0;i<length;i++){
        this.removeCard(this.shovedResultIds[0]);
      }
      this.shovedResultIds = [];

      if(!this.noResultMessageDivShoved){
        this.noResultMessageDiv = dojo.create("div", {'id': "no-result-message", innerHTML: '<span>'+this.noResults+'</span>'}, this.searchResultsInner);
        this.noResultMessageDivShoved = 1;
      }

    }


/*    console.log(this.newResults);
    console.log(this.newResults[this.newResultIds[3]]); */ // <- get the 3rd result; newResults - storing the results

    dojo.removeClass(this.closeButton, "search-area-loading");
    this.actualiseResultHeight();
    this.actualiseResultWidth();

    if (window.Shadowbox) {
      Shadowbox.clearCache();
      Shadowbox.setup();
    }
  },

  addCard : function(element){
    var tmpl =
      ((this.showCat==1)?'<div class="search-result-card-category">'+element.pluginName+'</div>':'')+
      '<div class="search-result-card minimized front">'+
        '<div class="search-result-overlay">'+
          '<div class="search-result-beacon">'+
            '<div class="beaconCircle1"></div>'+
            '<div class="beaconCircle2"></div>'+
            '<div class="imgbeacon"></div>'+
          '</div>'+
        '</div>'+
        ((element.price)?'<div class="search-result-price">'+element.price+'</div>':'')+
        ((typeof element.product_img!="undefined")?element.product_img:'<img src=""/>')+
        ((!('ontouchstart' in window))?
        '<div class="search-result-content">'+
          '<div class="search-result-title">'+
            '<span title="'+element.title+'">'+element.title+'</span>'+
          '</div>'+
          ((this.showIntroText=="1")?'<div class="search-result-inner">'+element.text+'</div>':'')+
          ((typeof element.product_img!="undefined")?element.product_img:'<img src=""/>')+
        '</div>':'')+
        '<div class="search-result-content">'+
          '<div class="search-result-title">'+
            '<span title="'+element.title+'">'+element.title+'</span>'+
          '</div>'+
          ((this.showIntroText=="1")?'<div class="search-result-inner">'+element.text+'</div>':'')+
        '</div>'+
      '</div>';

    var srLink = '';
    if(this.linktarget == 0) {
      srLink = dojo.create("a", {id: element.id, 'class': "search-result-link", 'onclick':"return false;", 'href' : element.href,innerHTML:tmpl}, this.searchResultsInner);
    } else {
      srLink = dojo.create("a", {id: element.id, 'class': "search-result-link", 'target': "_blank", 'href' : element.href,innerHTML:tmpl}, this.searchResultsInner);
    }
    var marginSeparator = dojo.create("div", {'class': "search-result-link search-result-sep", 'onclick':"return false;"}, this.searchResultsInner);

    var img = dojo.query("img",srLink)[1];

    if(!('ontouchstart' in window)){
      dojo.addClass(img,"blurred");
      dojo.style(img, { "-webkit-filter":"url('#searchblur')",
                        "-moz-filter":"url('#searchblur')",
                        "-o-filter":"url('#searchblur')",
                        "filter":"url('#searchblur')"});
    }

    if (element.href.match(/\.jpg$|\.png$/i)) {
      dojo.attr(srLink, "rel", "shadowbox[UniversalAJAXLiveSearch];"); //options={slideshowDelay:5}
    } else {
      dojo.connect(srLink,'onclick',this,'stopEventBubble');
      // SUGGESTION
      dojo.connect(srLink,'onclick',this,'saveSuggestion');
//      dojo.connect(cardfront,'onclick',this,'addClickedCard');
    }
    dojo.connect(dojo.query("img",srLink)[0],'onload',this,'showCard');
  },

  removeCard : function(id){
    if(dojo.byId(id) && dojo.byId(id).children[0]){
      try {
        dojo.byId(id).children[0].addClass("minimized");
      } catch(err) {

      }

      dojo.addClass(dojo.byId(id), "minimized");

      setTimeout(dojo.hitch(this, function(id) {
        dojo.destroy(dojo.byId(id).nextSibling);
        dojo.destroy(id);
        this.actualiseResultHeight();
      },id),400);

      dojo.animateProperty({
          node: dojo.byId(id),
          properties: {
            height: {end: 0, units:"px"}
          },
          duration: 400,
        }).play();
   }
   this.shovedResultIds.splice(this.shovedResultIds.indexOf(id),1); // remove the element from the array
  },

  showCard: function(evt){
    setTimeout(dojo.hitch(this, function(card) {
    if(dojo.hasClass(card, "minimized"))
      dojo.removeClass(card, "minimized");
    if(card.previousSibling)
      dojo.addClass(card.previousSibling, "show")
    this.actualiseResultHeight();
    },evt?evt.currentTarget.parentNode:null),Math.floor((Math.random()*400)+1));
  },

  addClickedCard: function(event){
    var card = event.currentTarget;
    dojo.addClass(card,"clicked");
  },

  getResultBoxAnimation: function(){
    if(this.fadeInResult){ //fade-in and down
      dojo.style(this.searchResultsMoovable, "height", this.innerHeight+"px");
      this.textBoxPos = dojo.position(this.searchForm, true);
      dojo.style(this.searchResultsMoovable, "opacity", 0);
      dojo.style(this.searchResultsMoovable, "top", '-10px');
      this.fadeInResult=0;
      return dojo.animateProperty({
        node: this.searchResultsMoovable,
        properties: {
            opacity: 1,
            top: { end:0, units:"px" }
        },
        duration: 300
      }).play();
    }else{
      return dojo.animateProperty({
        node: this.searchResultsMoovable,
        properties: {
          height: {start: dojo.style(this.searchResultsMoovable, 'height'), end: this.innerHeight}
        },
        duration: 500
      }).play();
    }
  },

  getCloseResultBoxAnimation: function(){
    return dojo.animateProperty({
      node: this.searchResultsMoovable,
      properties: {
        opacity: 0,
        top: { end:10, units:"px" }
      },
      duration: 300,
      onEnd : dojo.hitch(this,'removeResults')
    }).play();
  },

  getCategoryLeftPosition: function(){
    var categorySize = dojo.marginBox(this.searchCategories);
    return this.textBoxPosition.x+this.textBoxPosition.w-categorySize.w + 1;
  },

  closeResults : function(e){
    if(e && e.button && e.button > 0) return;

    dojo.addClass(this.searchResults, "hidde");
    dojo.style(this.textBox,"paddingRight","40px");
    this.actualiseResultHeight(1);
    dojo.style(this.closeButton, "visibility", "hidden");

    setTimeout(dojo.hitch(this, function() {
                var length = this.shovedResultIds.length;
                for(var i=0;i<length;i++){
                  dojo.destroy(this.shovedResultIds[0]);
                  this.shovedResultIds.splice(this.shovedResultIds.indexOf(this.shovedResultIds[0]),1);
                }
                this.shovedResultIds = [];
                this.closeControlPanel();
                dojo.removeClass(this.closeButton, "search-area-loading");

                dojo.attr(this.textBox, "value", this.searchBoxCaption);
                dojo.addClass(this.textBox, "search-caption-on");
                this.suggestBox.value= "";
                if (this.noResultMessageDivShoved){
                  dojo.destroy(this.noResultMessageDiv);
                  this.noResultMessageDivShoved=0;
                  dojo.query("div[class=no-result-suggest]").forEach(dojo.destroy);
                }
                this.searchResultsInner.innerHTML="";
                dojo.removeClass(this.searchResults, "hidde");
              }),250);

  },

  actualiseResultHeight : function(fix){
    if(this.shovedResultIds[0]) {
      this.cardHeight = dojo.position(dojo.byId(this.shovedResultIds[0])).h;
    }
    var size = dojo.position(this.searchResultsInner).h+(38)*this.controlPanelShoved;
    var maxsize = this.lineNumber*this.cardHeight+(38)*this.controlPanelShoved;
    if (maxsize>0 && size>maxsize)
      size = maxsize;

    if (fix)
      dojo.style(this.searchResults,"height","0px");
    else
      dojo.style(this.searchResults,"height",size+"px");
  },

  actualiseResultWidth : function(){
    if(this.fullWidth=="0"){

      var count = Math.floor(dojo.position(this.searchResults).w / (this.searchImageWidth+10));
      this.lineNumber = Math.floor(this.resultsPerPage/count+0.99999);
      var margin = ((dojo.position(this.searchResults).w % (this.searchImageWidth+10)) / count /2 -1 ) +5;
      if (dojo.isIE!=8)
        this.resultWidthStyle.innerHTML="#offlajn-ajax-tile-results .search-result-card{margin: 0px "+Math.floor(margin)+"px "+Math.floor(margin)+"px; }";
    }else{
      this.lineNumber = this.resultsPerPage;
    }
  },

  showPageByID: function(pagenumber,directionClass){
    if(pagenumber!=this.actualPage){

      var paginators = dojo.query(".offlajn-button", this.paginators);
      var oldButton = paginators[this.actualPage-1];
      var newButton = paginators[pagenumber-1];
      if (!oldButton) return; // there is not such page...

      if(dojo.hasClass(oldButton, "pushed"))
        dojo.removeClass(oldButton, "pushed");

      if(!dojo.hasClass(newButton, "pushed"))
        dojo.addClass(newButton, "pushed");

      this.searchResultsInner.innerHTML="";
      this.shovedResultIds=[];
      for(var i=(pagenumber-1)*this.resultsPerPage;i<this.newResultIds.length && i<pagenumber*this.resultsPerPage;i++){
        if(dojo.indexOf(this.shovedResultIds, this.newResultIds[i])==-1){
          this.addCard(this.newResults[this.newResultIds[i]]);
          this.shovedResultIds.push(this.newResultIds[i]);
        }
      }
      if (window.Shadowbox) {
        var i, max = this.searchPageOut.children.length;
        for (i = 0; i < max; ++i) dojo.attr(this.searchPageOut.children[i], "rel", "");
        Shadowbox.clearCache();
        Shadowbox.setup();
      }
      this.actualPage=pagenumber;
    }
  },

  refreshPage: function(direction,directionClass){
    var gotoPage = this.actualPage+direction;
    if(gotoPage>this.pageNumber)
      gotoPage-=this.pageNumber;
    else if(gotoPage<=0){
      gotoPage+=this.pageNumber;
    }
    this.showPageByID(gotoPage,directionClass);
  },

  jumptoPage: function(event){
    this.showPageByID(event.currentTarget.pageID);
  },

  onResize: function(){
    if(this.fullWidth=="1"){
      this.cardHeight = dojo.position(this.searchResults).w*this.resultImageHeight/this.resultImageWidth +10;
      this.lineNumber = this.resultsPerPage;
    }
    this.actualiseResultWidth();
    this.actualiseResultHeight();
  },

  /*TOUCH Evenets*/
  touchStart: function(e){
    dojo.copyTouch(e.changedTouches[0], this.touch);
    this.touch.scrollY = window.scrollY;
  },

  touchEnd: function(e){
    if(this.touch.identifier == e.changedTouches[0].identifier){
      var dist = Math.sqrt(Math.pow(e.changedTouches[0].screenX-this.touch.screenX, 2) + Math.pow(e.changedTouches[0].screenY-this.touch.screenY, 2));
      if(dist > 100){
        if(Math.abs(this.touch.scrollY- window.scrollY)<125){ //horizontal
          var scroll = e.changedTouches[0].screenX-this.touch.screenX;
          if(scroll > 0){
            setTimeout(dojo.hitch(this,"refreshPage",-1,"flipright"),1);
          }else{
            setTimeout(dojo.hitch(this,"refreshPage",1,"flipleft"),1);
          }
        }
      }
    }
  },

  /*Category Chooser*/
  showCategoryChooser : function(evt){
    if(!this.categoryChooserVisible){
      if(this.categoryFx && this.categoryFx.status() == "playing"){
        this.categoryFx.stop();
      }
      this.categoryChooserVisible = 1;
      this.textBoxPosition = dojo.position(this.textBox, true);

      var left = this.getCategoryLeftPosition();

      dojo.style(this.searchCategories,{
        left: left+'px',
        top: (this.textBoxPosition.y+this.textBoxPosition.h-10+this.categoryChooserTopCorrection)+'px',
        visibility : 'visible',
        opacity : '0'
      });
      this.categoryFx = dojo.animateProperty({node: this.searchCategories, properties: {opacity : 1, top: { end:this.textBoxPosition.y+this.textBoxPosition.h+this.categoryChooserTopCorrection, units:"px" }}, duration: 200}).play();
      dojo.addClass(this.categoryChooser,"opened");
      this.hideCategories = dojo.connect(dojo.body(),'onclick',this,'hideCategoryChooser');
    }else{
      this.hideCategoryChooser();
    }
  },

  hideCategoryChooser : function(evt){
    if(this.dummyHideCategory == 1){
      this.dummyHideCategory = 0;
      return;
    }
    dojo.disconnect(this.hideCategories);
    if(this.categoryFx && this.categoryFx.status() == "playing"){
      this.categoryFx.stop();
    }
    this.categoryFx = dojo.animateProperty({
        node: this.searchCategories,
        properties: {opacity : 0},
        onEnd: function(){
          dojo.style(this.node,{visibility : 'hidden'})},
        duration: 200
        }).play();
    dojo.removeClass(this.categoryChooser,"opened");
    this.categoryChooserVisible = 0;
  },

  categorySelection: function(evt){
    var node = evt.currentTarget;
    if(dojo.hasClass(node, "selected"))
      dojo.removeClass(node, "selected")
    else
      dojo.addClass(node, "selected");

    var textBoxVal = dojo.attr(this.textBox, "value");
    if (textBoxVal.length>=this.minChars && textBoxVal != this.searchBoxCaption){
      this.type(null,1); // force AJAX
    }
  }


});