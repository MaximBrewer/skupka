dojo.require('dojo.uacss');

dojo.declare("AJAXSearchBase", null, {
  constructor: function(args) {
    dojo.mixin(this,args);
    this.list = new Array(); // the search result array
    this.selected = 0;   //the actual selected search result
    this.pluginCounter = new Array();
    this.timeStamp = 0; // the latest timestamp
    this.categoryChooserVisible = 0; // Is the category chooser visible? 0:No 1:Yes
    this.resultsVisible = 0; // Are the results visible? 0:No 1:Yes
    this.fadeInResult=1; // At first time the results should come up with fade-in effect
    if(this.voicesearch == 1) dojo.attr(this.textBox, "x-webkit-speech", "");
    if(dojo.isIE <= 7){
      var w = dojo.position(this.textBox.parentNode).w-dojo.style(this.textBox, 'paddingLeft')-dojo.style(this.textBox, 'paddingRight');
      dojo.style(this.textBox, 'width', (w-1)+'px');
    }

    this.searchBoxContainer = dojo.query(".offlajn-ajax-search-container", this.node)[0];

	  if(this.catChooser){
      this.categoryChooser = dojo.query(".category-chooser", this.searchForm)[0];
      dojo.place(this.searchCategories,dojo.body());
      this.searchCategoriesList = dojo.query(".search-categories-inner div", this.searchCategories);
    }

	  if(this.catChooser){
      dojo.connect(this.categoryChooser,'onclick',this,'showCategoryChooser');
      dojo.connect(this.categoryChooser, "onclick", this, "stopCategoryChooserEventBubble");
    }

    dojo.attr(this.textBox, "value", this.searchBoxCaption);
    dojo.addClass(this.textBox, "search-caption-on");
    this.suggestBox.value= ""

    if(this.searchResult){
      dojo.connect(this.textBox,'oninput',this,'type');
      dojo.connect(this.textBox,'onkeyup',this,'type');
      dojo.connect(this.textBox,'oncompositionupdate',this,'type'); //Japanese support
    }

	  dojo.connect(this.textBox,'onfocus',this,'textBoxFocus');
	  dojo.connect(this.textBox,'onblur',this,'textBoxBlur');
	  dojo.connect(this.textBox,'onwebkitspeechchange',this,'soundEnd');

    //if suggestion
	  if(this.keywordSuggestion=="1"){
      dojo.connect(this.textBox,'onkeyup',this,'suggest');
    }
    dojo.connect(this.suggestBox,'onfocus',this,'suggestBoxFocus');
    dojo.connect(this.suggestBox, "onclick", this, "stopEventBubble");

	  dojo.connect(this.closeButton,'onclick',this,'closeResults');
	  dojo.connect(this.searchButton,'onclick',this,'loadResult');

    dojo.connect(this.textBox, "onclick", this, "stopInputEventBubble");

	  if(this.catChooser)
    dojo.forEach(this.searchCategoriesList, function(entry, i){
      dojo.connect(entry, "onclick", this, "categorySelection");
      dojo.connect(entry, "onclick", this, "stopEventBubble");
    },this);
	  dojo.connect(window,'onresize',this,'onResize');

  },

  stopEventBubble: function(e){
    e.stopPropagation();
  },

  stopInputEventBubble: function(e){
    this.dummyCloseResult = 1;
  },

  stopCategoryChooserEventBubble: function(e){
    this.dummyHideCategory = 1;
  },

  loadResult : function(e){
    if(this.targetsearch == 3) {
      e.stopPropagation();
    } else {
      if(this.textBox.value.length >=this.minChars){
        this.searchForm.submit();
      }
    }
  },

  type: function(evt, force){
    if (window.event) {
      if (window.event.type == 'keyup' && window.event.keyCode != 13) return;
      this.keycode = window.event.keyCode;
      this.ktype = window.event.type;
    } else if (evt) {
      if (evt.type == 'keyup' && evt.which != 13) return;
      this.keycode = evt.which;
      this.ktype = evt.type;
    }
    if (this.ktype == 'input') this.keycode = 48;
    clearTimeout(this.t);
    this.t = setTimeout(dojo.hitch(this, function() {
      if(((this.targetsearch == 3 && this.keycode==13) || (this.keycode>40 || this.keycode==13) || this.keycode==32 || this.keycode==8 || this.keycode==0 || this.ktype=="click") && this.textBox.value.length >=this.minChars || force==1){
       var categories = new Array();
        dojo.forEach(this.searchCategoriesList, function(entry, i){
          if (dojo.hasClass(entry, "selected"))
            categories.push(dojo.attr(entry,"id").match(/\d+/)[0]);
        });
        dojo.style(this.closeButton, "visibility", "visible");
        dojo.style(this.textBox,"paddingRight","52px");
        dojo.addClass(this.closeButton, "search-area-loading");
        dojo.xhrGet({
            url : this.searchFormUrl,
            content: { option: "com_universal_ajax_live_search", lang: this.lang, module_id : this.moduleId, search_exp: this.textBox.value, 'categ[]' : categories, dojo_preventCache: new Date().getTime() },
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

  suggest: function(evt){
    if (window.event) {
      this.keycode = window.event.keyCode;
      this.ktype = window.event.type;
    } else if (evt) {
      this.keycode = evt.which;
      this.ktype = evt.type;
    }
    if((this.keycode>40 || this.keycode==32 || this.keycode==8 || this.keycode==0 || this.ktype=="click") && this.textBox.value.length >=2 || this.keycode==46 || this.keycode==8 ){
      dojo.xhrGet({
          url : this.searchFormUrl,
          content: { option: "com_universal_ajax_live_search", format: "raw", search_exp: this.textBox.value, suggest: 1, dojo_preventCache : new Date().getTime() },
          handleAs:"text",
          preventCache : false,
          load: dojo.hitch(this,'processSuggestion'),
          error: function(e){
            console.log('Error: '+e);
          }
      });
    }else if(this.keycode==39 && this.getCaret(this.textBox)==this.textBox.value.length && this.suggestBox.value!= ""){
      this.textBox.value+= this.suggestBox.value.substr(this.textBox.value.length);
      this.type(evt,1);
    }
  },

  suggestBoxFocus : function(evt){
    this.textBox.focus();
  },

  getCaret: function (el) {
    if (el.selectionStart) {
      return el.selectionStart;
    } else if (document.selection) {
      el.focus();

      var r = document.selection.createRange();
      if (r == null) {
        return 0;
      }

      var re = el.createTextRange(),
          rc = re.duplicate();
      re.moveToBookmark(r.getBookmark());
      rc.setEndPoint('EndToStart', re);

      return rc.text.length;
    }
    return 0;
  },

  saveSuggestion : function(evt){
    dojo.xhrGet({
        url : this.searchFormUrl,
        content: { option: "com_universal_ajax_live_search", format: "raw", search_exp: this.textBox.value, savesuggest:1, dojo_preventCache : new Date().getTime() },
        handleAs:"text",
        preventCache : false
    });
    if(this.linktarget != '1') { //check if target is _blank or not
      setTimeout(dojo.hitch(this, function(ct) {
        if(!ct)
          document.location.href = dojo.attr(this.list[this.selected-1],"href");
        else
          document.location.href = ct.href;
      },evt?evt.currentTarget:null),200);
    }
  },

  processSuggestion : function(d,xhr){
    try{
      var data = eval('('+d+')');
    }catch(err){
      alert('SuggestionError: '+d); return;
    }
    if (data[0])
      this.suggestBox.value= this.textBox.value + data[0].suggestion.toLowerCase().substr(this.textBox.value.length);
    else
      this.suggestBox.value= "";
  },

   changeText: function(e) {
    this.textBox.value = e.currentTarget.innerHTML;
  },

  textBoxFocus : function(){
    if (this.catChooser) this.hideCategoryChooser();
    dojo.addClass(this.searchBoxContainer,"active");
    if(dojo.hasClass(this.textBox, "search-caption-on")){
      dojo.attr(this.textBox, "value", "");
      dojo.removeClass(this.textBox, "search-caption-on");
    }
    dojo.addClass(this.node,"active");
  },

  soundEnd : function(e) {
    this.textBoxFocus();
    dojo.attr(this.textBox, "value", e.results[0].utterance);
    this.type(e,1);
  },


  textBoxBlur : function(){
    if(dojo.hasClass(this.searchBoxContainer, "active") && !this.resultsVisible){
      dojo.removeClass(this.searchBoxContainer,"active");
    }
    if (this.textBox.value.length==0){
      dojo.attr(this.textBox, "value", this.searchBoxCaption);
      dojo.addClass(this.textBox, "search-caption-on");
      this.suggestBox.value= ""
    }
    if(dojo.hasClass(this.node, "active")) dojo.removeClass(this.node,"active");
  },

  showCategoryChooser : function(evt){
    if(!this.categoryChooserVisible){
      this.textBox.blur();
      if(dojo.style(this.closeButton, "visibility")== "visible"){ //remove results if the were
        dojo.attr(this.textBox, "value", "");
        this.closeResults();
      }

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

  }

});

dojo.copyTouch = function(sourceObj, targetObj){
    targetObj.screenX = sourceObj.screenX;
    targetObj.screenY = sourceObj.screenY;
    targetObj.identifier = sourceObj.identifier;
};

/*The drop-style searches base*/
dojo.declare("AJAXSearchDropBase", AJAXSearchBase, {
  constructor: function(args) {
    dojo.mixin(this,args);
    this.categoryChooserTopCorrection = 0;

    this.searchResults = dojo.create("div", {id: "search-results"+this.id}, dojo.body());
    this.searchResultsMoovable = dojo.create("div", {id: "search-results-moovable"+this.id}, this.searchResults);
    this.searchResultsInner = dojo.create("div", {id: "search-results-inner"+this.id}, this.searchResultsMoovable);

    this.touch = {screenX: 0, screenY: 0, identifier: ''};
    dojo.connect(this.searchResults, "onclick", this, "stopEventBubble");

    dojo.connect(document, "onclick", this, "closeResults");
	  dojo.connect(this.textBox,'onkeypress',this,'arrowNavigation');
  },

  onResize: function(){
    this.textBoxPos = dojo.position(this.searchForm.parentNode, true);
    var left = this.textBoxPos.x;
    if (this.resultAlign==1){ // if search result aligned to right
      left+=this.textBoxPos.w-this.searchRsWidth;
    }
    dojo.style(this.searchResults,{
      left: left+'px',
      top: this.textBoxPos.y+this.textBoxPos.h+this.resultboxTopOffset+'px'
    });
    if(this.dynamicResult=="1"){
      dojo.style(this.searchResults,{
        left: left+'px',
        top: this.textBoxPos.y+this.textBoxPos.h+this.resultboxTopOffset+'px',
        width : this.textBoxPos.w+'px'
      });
      dojo.style(this.searchResultsInner,{
        width : this.textBoxPos.w+'px'
      });

      if(dojo.query(".no-result", this.searchResults).length>0)
      dojo.style(dojo.query(".no-result", this.searchResults)[0],{
        width : this.textBoxPos.w+'px'
      });

      dojo.forEach(dojo.query(".result-element", this.searchResults), function(entry, i){
        dojo.style(entry,{
          width : this.textBoxPos.w+'px'
        });

        var span = dojo.query("span", entry);
        dojo.style(span[0],{
          width : (this.textBoxPos.w-17)+'px'
        });

        if(this.showIntroText == 1 && span[1]) {
            dojo.style(span[1],{
              width : (this.textBoxPos.w-17)+'px'
            });
        }

        if(dojo.hasClass(entry, "result-products")){
          dojo.style(span[0],{
            width : (this.textBoxPos.w-12-this.searchImageWidth-17)+'px'
          });
          if(this.showIntroText == 1 && span[1]) {
            dojo.style(span[1],{
              width : (this.textBoxPos.w-12-this.searchImageWidth-17)+'px'
            });
          }
        }

      },this);
      this.searchRsWidth = this.textBoxPos.w;
    }
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
        dojo.style(this.textBox,"paddingRight","52px");
        return;
      }
    }

    dojo.attr(this.searchResultsInner, { innerHTML: "" });
    this.list=[];
    this.pluginCounter=[];
    this.selected = 0;
    this.onResize();

    if(data.length!=0 && !data.nores){
      this.paginationBand = new Array();
      var actualPlugin = 0;
      for(var i in data){
        this.paginationBand[actualPlugin] = new Array;
        var pluginResults = data[i];
        var pluginNameDiv = dojo.create("div", {'class': "plugin-title"}, this.searchResultsInner);
        if (actualPlugin==0){
          dojo.addClass(pluginNameDiv, 'first');
        }
        /*Adding plugin title*/
        dojo.create("div", {'class': "plugin-title-inner", innerHTML: i+" ("+pluginResults.length+")"}, pluginNameDiv);

        /*Generate pagination*/
        this.generatePagination(pluginNameDiv,pluginResults.length,actualPlugin);
        this.pluginCounter.push(pluginResults.length);

        /*Generate resultList*/
        this.generateResultList(pluginResults, actualPlugin);

        actualPlugin++;
      }
      /*Set the selected item to 0 (it is invisible)*/
      this.selectItem(0);
    }

    this.tags="";

    if(this.searchResultsInner.childNodes.length){
      this.innerHeight = dojo.marginBox(this.searchResultsInner).h;
    } else if (data.nores && data.nores[0] && data.nores[0].tag && (data.nores.length>1 || this.textBox.value!=data.nores[0].tag)) { /* No results but keywords found. */
      var pluginNameDiv = dojo.create("div", {'class': "plugin-title first suggest"}, this.searchResultsInner);
      dojo.create("div", {'class': "plugin-title-inner", innerHTML: this.stext}, pluginNameDiv);
      dojo.create("div", {'class': "ajax-clear"}, pluginNameDiv);
      for (var j=0;j<data.nores.length-1;j++) {
        if (this.textBox.value!=data.nores[j].tag)
          this.tags += "<a class='sugg-keyword'>"+data.nores[j].tag+"</a>, ";
      }
      this.tags += "<a class='sugg-keyword'>"+data.nores[j].tag+"</a>";

      dojo.create("div", {'class': "no-result-suggest", innerHTML: '<span>'+this.tags+'</span>'}, this.searchResultsInner);
      dojo.query("a[class=sugg-keyword]").connect("onclick", this,  "changeText");
      dojo.query("a[class=sugg-keyword]").connect("onclick", this,  "type");
      this.innerHeight = dojo.marginBox(this.searchResultsInner).h;
    }else{ /* No result for the keyword */
      var pluginNameDiv = dojo.create("div", {'class': "plugin-title first"}, this.searchResultsInner);
      dojo.create("div", {'class': "plugin-title-inner", innerHTML: this.noResultsTitle}, pluginNameDiv);
      dojo.create("div", {'class': "ajax-clear"}, pluginNameDiv);
      dojo.create("div", {'class': "no-result", innerHTML: '<span>'+this.noResults+'</span>'}, this.searchResultsInner);
      this.innerHeight = dojo.marginBox(this.searchResultsInner).h;
    }
    dojo.removeClass(this.closeButton, "search-area-loading");
    this.onResize();
    this.animateResult();
  },

  /*Generate pagination*/
  generatePagination : function(pluginNameDiv, dataLength, actualPlugin){
    this.paginationBand[actualPlugin].paginators= new Array();
    if (dataLength>this.productsPerPlugin){ //not generate if there is just 1 page
      var pagination = dojo.create("div", {'class': "pagination "+"paginator-"+actualPlugin}, pluginNameDiv);
      var pageNumber = Math.floor(dataLength/this.productsPerPlugin+0.99999);  // 0.99999 constant because: 1.00001 must round to 2, 2.00001 to 3, etc..
      for(var num=0;num < pageNumber; num++){
        var paginatorElement = dojo.create("div", {'class': "pager"}, pagination);
        paginatorElement.parentPlugin = actualPlugin;
        paginatorElement.page = num;

     	  dojo.connect(paginatorElement,'onclick',this,'moovePage'); // coonect an event to paginators

        if (num==0){
          dojo.addClass(paginatorElement, 'active');
          this.paginationBand[actualPlugin].activePaginator = paginatorElement;
        }

        this.paginationBand[actualPlugin].paginators.push(paginatorElement); //adding the paginators to the band to adding active class when arrows are in use
      }
    }
    dojo.create("div", {'class': "ajax-clear"}, pluginNameDiv);
  },

  /*Generate resultList*/
  generateResultList : function(pluginResults, actualPlugin){

    var pageContainer = dojo.create("div", {'class': "page-container"}, this.searchResultsInner);
    var pageBand = dojo.create("div", {'class': "page-band "+"page-band-"+actualPlugin}, pageContainer);
    this.paginationBand[actualPlugin].band = pageBand;
    pageBand.currentPage=0;
    pageBand.maxPage = Math.floor(pluginResults.length/this.productsPerPlugin+0.99999);
    pageBand.plugin = actualPlugin;
    //connect the mouse scroller
    if(this.enableScroll==1){
      dojo.connect(pageBand, (!dojo.isMozilla ? "onmousewheel" : "DOMMouseScroll"), this, "scrollResultList");
  	  dojo.connect(pageBand, "ontouchstart", this, "touchStart");
  	  dojo.connect(pageBand, "ontouchend", this, "touchEnd");
  	  dojo.connect(pageBand, "ontouchmove", dojo.stopEvent);
    }
    var page = null;

    for(var j=0;j<pluginResults.length;j++){
      if (j%this.productsPerPlugin==0){ // 2 is the count to show
        page = dojo.create("div", {'class': "page-element list"+Math.floor(j/this.productsPerPlugin)}, pageBand);
      }
      var atag = null;
      var introText="";
      if (this.showIntroText==1 && pluginResults[j].text){
        introText = '<span class="small-desc">'+pluginResults[j].text+'</span>';
      }
      if(pluginResults[j].product_img){ //Virtuemart products
        atag = dojo.create("a", {'class': "result-element result-products", innerHTML: pluginResults[j].product_img+'<span>'+pluginResults[j].title+'</span>'+introText, href:pluginResults[j].href, target: (this.linktarget=="1" ? '_blank' : '') }, page);
      }else{  // Other search results
        atag = dojo.create("a", {'class': "result-element", 'onclick':"return false;", innerHTML: '<span>'+pluginResults[j].title+'</span>'+introText, href:pluginResults[j].href}, page);
      }

      // SUGGESTION
      dojo.connect(atag,'onclick',this,'saveSuggestion');

      atag.plugin=actualPlugin;
      atag.item=j;

      this.list.push(atag);
      dojo.create("div", {'class': "ajax-clear"}, atag);
    }
    if(pluginResults.length<this.productsPerPlugin){
      dojo.style(pageContainer, "height", dojo.marginBox(pageBand).h +"px");
    }
    if(pluginResults.length>=this.seemoreAfter && this.seemoreEnabled){
    var seemorediv = dojo.create("div", {'id':"seemore",'class': "seemore", innerHTML: '<span>'+this.seeMoreResults+'</span>'}, this.searchResultsInner);
    dojo.connect(seemorediv,'onclick',this,'loadResult');
    dojo.removeClass(this.searchResultsInner, "withoutseemore");
    //dojo.style(this.searchResultsInner,"paddingBottom","0px");
    }else{
    dojo.addClass(this.searchResultsInner, "withoutseemore");
     //dojo.style(this.searchResultsInner,"paddingBottom","10px");
    }
  },

  moovePage : function(event){
    var pager = event.target;
    var band = this.paginationBand[pager.parentPlugin].band;
    dojo.removeClass(this.paginationBand[pager.parentPlugin].activePaginator,"active");
    dojo.addClass(pager, "active");
    this.paginationBand[pager.parentPlugin].activePaginator = pager;

    if(band.actFx && band.actFx.status() == "playing"){
      band.actFx.stop();
    }
    band.actFx = dojo.animateProperty({node: band, properties: {left: -pager.page*this.searchRsWidth}, duration: 500}).play();
    this.textBox.focus();
  },

  scrollPluginResults : function(band, page){
    if(band.actFx && band.actFx.status() == "playing"){
      band.actFx.stop();
    }
    band.actFx = dojo.animateProperty({node: band, properties: {left: -page*this.searchRsWidth}, duration: 250}).play();
    band.currentPage=page;
  },

  animateResult : function(){
    if(this.actFx && this.actFx.status() == "playing"){
      this.actFx.stop();
    }
    this.resultsVisible = 1;
    dojo.style(this.searchResults, "visibility", "visible");
    if(this.innerHeight){
      this.actFx = this.getResultBoxAnimation();
    } else{ // No results found
      this.actFx = dojo.animateProperty({
          node: this.searchResultsMoovable,
          properties: {
              height: 0
          },
          duration: 500,
          onEnd : dojo.hitch(this,'removeResults')
          }).play();
    }
    dojo.style(this.closeButton, "visibility", "visible");
    dojo.style(this.textBox,"paddingRight","52px");
  },

  closeResults : function(e){
  dojo.style(this.textBox,"paddingRight","30px");
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
    dojo.style(this.textBox,"paddingRight","30px");
    dojo.attr(this.textBox, "value", this.searchBoxCaption);
    dojo.addClass(this.textBox, "search-caption-on");
    this.suggestBox.value= ""

  },

  removeResults: function(){
    this.fadeInResult=1;
    dojo.attr(this.searchResultsInner, { innerHTML: "" });
    if(this.searchResultsInner.childNodes.length){
      this.innerHeight = dojo.marginBox(this.searchResultsInner).h;
    }else{
      this.innerHeight=0;
    }
    dojo.style(this.searchResults, "visibility", "hidden");
    this.resultsVisible=0;
    if(dojo.hasClass(this.searchBoxContainer, "active") && !this.resultsVisible){
      dojo.removeClass(this.searchBoxContainer,"active");
    }
  },

  /*keyCodes:
    UP: 38
    DOWN: 40
    ENTER: 13*/

  arrowNavigation: function(evt){
    if(evt.keyCode==27){ //blur if esc pressed
      this.textBox.blur();
      if(dojo.style(this.closeButton, "visibility")== "visible"){ //remove results if the were
        dojo.attr(this.textBox, "value", "");
        this.closeResults();
      }
    }
    if(this.list.length){
      if(evt.keyCode==38){
        this.selectItem(this.selected-1);
      }else if (evt.keyCode==40){
        this.selectItem(this.selected+1);
      }else if (evt.keyCode==13){
        if(this.selected==0){
          this.loadResult();
        }else{
          this.saveSuggestion();
          // Here was the href loading
        }
      }

      if(this.selected>0){
        var actPlugin = this.list[this.selected-1].plugin;
        var actPluginItem = this.list[this.selected-1].item;
        var band = this.paginationBand[actPlugin].band;
        var pgNumber = Math.floor(actPluginItem/this.productsPerPlugin);
        if(this.paginationBand[actPlugin].activePaginator){
          dojo.removeClass(this.paginationBand[actPlugin].activePaginator,"active");

          this.scrollPluginResults(band,pgNumber);
          var pager = this.paginationBand[actPlugin].paginators[pgNumber];
          dojo.addClass(pager, "active");
          this.paginationBand[actPlugin].activePaginator = pager;
        }
      }

    }else if(evt.keyCode==13){
    	this.loadResult();
    }
  },
  //scrolling the results
  scrollResultList : function(evt){
    var scroll = evt[(!dojo.isMozilla ? "wheelDelta" : "detail")] * (!dojo.isMozilla ? 1 : -1);
    var band = evt.currentTarget;
    if (band.maxPage>1){
      var actPlugin = band.plugin;
      dojo.removeClass(this.paginationBand[actPlugin].activePaginator,"active");
      var pgNumber = band.currentPage;
      if(scroll<0 && pgNumber<band.maxPage-1){
        pgNumber++;
      }else if(scroll<0 && pgNumber>=band.maxPage-1){
        pgNumber=0;
      }else if(scroll>0 && pgNumber>0){
        pgNumber--;
      }else if(scroll>0 && pgNumber<=0){
        pgNumber=band.maxPage-1;
      }
      this.scrollPluginResults(band,pgNumber);
      var pager = this.paginationBand[actPlugin].paginators[pgNumber];
      dojo.addClass(pager, "active");
      this.paginationBand[actPlugin].activePaginator = pager;
      dojo.stopEvent(evt);
    }
  },

  selectItem : function(num){
    if(num>=this.list.length+1){
      num-=this.list.length+1;
    }
    if(num<0){
      num+=this.list.length+1;
    }
    if(this.list[this.selected-1]){
      dojo.removeClass(this.list[this.selected-1], "selected-element");
    }
    if(this.list[num-1]){
      dojo.addClass(this.list[num-1], "selected-element");
    }
    this.selected=num;
  },

  post_to_url: function(path, params, method) {
    method = method || "post"; // Set method to post by default, if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", key);
        hiddenField.setAttribute("value", params[key]);

        form.appendChild(hiddenField);
    }

    document.body.appendChild(form);
    form.submit();
  },

  /*TOUCH Evenets*/
  touchStart: function(e){
    dojo.copyTouch(e.changedTouches[0], this.touch);
  },

  touchEnd: function(e){
    if(this.touch.identifier == e.changedTouches[0].identifier){
      var dist = Math.sqrt(Math.pow(e.changedTouches[0].screenX-this.touch.screenX, 2) + Math.pow(e.changedTouches[0].screenY-this.touch.screenY, 2));
      if(dist > 80){
        var deg = Math.asin((e.changedTouches[0].screenY-this.touch.screenY)/dist)*180/Math.PI;
        if(deg < 45 && deg > -45){ //horizontal
          var scroll = e.changedTouches[0].screenX-this.touch.screenX;
            this.paginateTouch(e,scroll);
        }
      }
    }
  },

  paginateTouch: function(evt, direction){
    var band = evt.currentTarget;
    if (band.maxPage>1){
      var actPlugin = band.plugin;
      dojo.removeClass(this.paginationBand[actPlugin].activePaginator,"active");
      var pgNumber = band.currentPage;
      if(direction<0 && pgNumber<band.maxPage-1){
        pgNumber++;
      }else if(direction<0 && pgNumber>=band.maxPage-1){
        pgNumber=0;
      }else if(direction>0 && pgNumber>0){
        pgNumber--;
      }else if(direction>0 && pgNumber<=0){
        pgNumber=band.maxPage-1;
      }
      this.scrollPluginResults(band,pgNumber);
      var pager = this.paginationBand[actPlugin].paginators[pgNumber];
      dojo.addClass(pager, "active");
      this.paginationBand[actPlugin].activePaginator = pager;
      dojo.stopEvent(evt);
    }
  }

});