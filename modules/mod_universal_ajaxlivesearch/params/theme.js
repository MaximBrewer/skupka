var ThemeConfigurator = {};

dojo.declare("ThemeConfigurator", null, {
	constructor: function(args) {
    window.themeCfg = this;
	 dojo.mixin(this,args);
	 if(this.isJoomla3=="1"){
     if (window.opener && location.href.indexOf("universalajaxlivesearch.demo.offlajn.com") > -1) { // if DEMO
       this.navbar = dojo.byId("module-sliders");
       this.tabcontent = this.navbar;
     } else {
  	   this.navbar = dojo.query("#module-form .nav.nav-tabs")[0];
  	   this.tabcontent = dojo.query("#module-form div.tab-content")[0];
       pane = dojo.place('<li><a data-toggle="tab" href="#thememanager">Theme Manager</a></li>',this.navbar, 'last');
     }
     this.themePanel = dojo.place('<div id="thememanager" class="tab-pane"></div>',this.tabcontent, 'last');
     dojo.place(this.themeSelector, this.themePanel, 'last');
   }else{
   
   if(!this.joomfish){
	   var pane = dojo.place(this.themeSelector, dojo.byId('module-sliders') ? dojo.byId('module-sliders') : dojo.byId('menu-pane'), 'last');
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
   }
   this.tpc = dojo.byId('themeparamcontainer');
   this.themeDetails = dojo.byId(this.control+'theme-details');

   this.selectTheme = dojo.byId(this.selectTheme);
   this.savedindex = this.selectTheme.selectedIndex;
   dojo.connect(this.selectTheme, 'onchange', this, 'changeTheme');
   this.changeTheme();
  },
  
  changeTheme: function(e){
    this.theme = this.selectTheme.options[this.selectTheme.selectedIndex].value;
    if(this.theme == '' || this.theme == 'default') this.theme = 'default2';
    dojo.byId(this.control+'theme-details').innerHTML = eval('this.themeParams.'+this.theme);
    eval(eval('this.themeScripts.'+this.theme));
    if(e != undefined && this.savedindex != this.selectTheme.selectedIndex)
      setTimeout(dojo.hitch(this, "changeSkin"), 500 );
  },
  
  changeSkin: function(){
    var el = null;
    if(!this.joomfish){
      el = dojo.byId('paramsthemeskin') ? dojo.byId('paramsthemeskin') : dojo.byId('jformparamsthemethemeskin');
    }else{
      el = dojo.byId(this.control+'themeskin');
    }
    el.selectedIndex = 1;
    changeSkinsthemeskin(el);
  }
  
});
