
dojo.declare("ThemeConfigurator", null, {
	constructor: function(args) {
	 dojo.mixin(this,args);
   if(!this.joomfish){
	  // var pane = dojo.place(this.themeSelector, dojo.byId('module-sliders') ? dojo.byId('module-sliders') : dojo.byId('menu-pane'), 'last');
   }else{
      var hides = dojo.query('.translateparams td .toolbar');
      dojo.forEach(hides,function(el){dojo.style(el, 'display', 'none')});
      var el = null;
      if(this.control == 'orig_params'){
        el = dojo.byId('original_value_params');
      }else if(this.control == 'defaultvalue_params'){
        el = dojo.byId('original_value_params');
      }else if(this.control == 'refField_params'){
        el = dojo.query('.translateparams .translateparams');
        el = el[0];
      }
      pane = dojo.place(this.themeSelector, el, 'last');
      if(this.control == 'defaultvalue_params'){
        dojo.style(pane, 'display', 'none');
      }
   }
   this.themeDetails = dojo.byId(this.id);
   this.title = dojo.byId(this.id.replace('-details', '-title'));
   this.selectTheme = dojo.byId(this.selectTheme);
   this.savedindex = this.selectTheme.selectedIndex;
   //this.savedindex = this.selectThemeIndex.value;
   dojo.connect(this.selectTheme, 'onchange', this, 'changeTheme');
   this.changeTheme();
   if(this.firstRun) setTimeout(dojo.hitch(this, 'changeSkin'), 500);
  },

  changeTheme: function(e){
    this.theme = this.selectTheme.value;
    if(this.theme == '' || this.theme == 'default') this.theme = 'default2';
    dojo.addClass(this.title, 'offlajnloading');
    this.themeDetails.innerHTML = '';
    jQuery.ajax({
      url: location.href,
      method: "POST",
      data: {
        'offlajnformrenderer': '1',
        'key': this.themeParams[this.theme]
      },
      success: dojo.hitch(this, function(e, data){
        dojo.removeClass(this.title, 'offlajnloading');
        this.themeDetails.innerHTML = data;
        window.head = document.getElementsByTagName('head')[0];
        dojo.query('link',this.themeDetails).forEach(function(el){
          dojo.place(el, head);
        });
        dojo.query('script',this.themeDetails).forEach(function(el){
          var src = dojo.attr(el, 'src');
          if (src) {
            var fileref=document.createElement('script');
            fileref.setAttribute("type","text/javascript")
            fileref.setAttribute("src", src);
            dojo.place(fileref, head);
          }
        });
        if(e != undefined && this.savedindex != this.selectTheme.selectedIndex)
          dojo.addOnLoad(dojo.hitch(this, "changeSkin"));

        dojo.global.toolTips.connectToolTips(this.themeDetails);
        if (window.init_conditions) init_conditions();
        dojo.addClass(dojo.body(), "params-loaded");
      }, e)
    });

    //this.themeDetails.innerHTML = this.themeParams[this.theme];

    ////dojo.addOnLoad(dojo.hitch(null,window.labelFix,this.themeDetails));
    //eval(this.themeScripts[this.theme]);
    /*
    if(e != undefined && this.savedindex != this.selectTheme.selectedIndex)
      dojo.addOnLoad(dojo.hitch(this, "changeSkin"));
    */
  },

  changeSkin: function(){
    var el = dojo.byId(this.control+'themethemeskin');
    if(!el) el = dojo.byId(this.control+'themeskin');
    if(!el) return; // Maybe bug
    if(el.selectedIndex != undefined){
      el.selectedIndex = 1;
      el.value = el.options[el.selectedIndex].value;
      OfflajnFireEvent(el, 'change');
    }else{
      el.changeSkin = dojo.hitch(this, 'changeSkin');
    }
   // changeSkinsthemeskin(el);
  }

});
