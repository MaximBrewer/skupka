dojo.declare("OfflajnSkin", null, {
	constructor: function(args) {
    dojo.mixin(this,args);
    this.init();
    if(this.hidden.changeSkin){
      this.hidden.changeSkin();
      this.hidden.changeSkin = null;
    }
    if(window[this.name+'delay'] == true){
      window[this.name+'delay'] == false;
      this.hidden.value = this.hidden.options[1].value;
      this.changeSkin();
    }
  },

  init: function() {
    var label = dojo.byId(this.name + '-lbl');
    this.label = label ? label.innerHTML.toLowerCase() : 'preset';
    this.hidden = dojo.byId(this.id);
    //this.span = dojo.create("span", {style: "margin-left: 10px; position: absolute;"}, this.hidden.parentNode.parentNode, "last");
    this.span = dojo.create("span", {style: "margin-left: 10px;"}, this.hidden.parentNode.parentNode, "last");
    this.c = dojo.connect(this.hidden, 'onchange', this, 'changeSkin');
    this.initPreview();
  },

  initPreview: function() {
    var id = this.id,
        root = this.root,
        data = this.data;
    if (window.jQuery) jQuery(function($) {
      $("#offlajnlistcontainer"+id).parent().on("mouseenter", ".listelement", function(e) {
        var $this = $(this),
            i = $this.index()-1,
            j = 0, prop;
        for (prop in data) {
          if (i == j++) {
            if (data[prop].preview) {
              var $img = $('<img src="' + root + data[prop].preview + '">'),
                  off = $this.parent().parent().offset();
              $img.css({
                position: "absolute",
                opacity: 0,
                zIndex: 9999,
                top: off.top,
                left: off.left + $this.parent().parent().outerWidth()
              }).appendTo(document.body)
                .animate({opacity: 1}, 300);
              $this.one("mouseleave", function() {
                $img.animate({opacity: 0}, {
                  duration: 300,
                  complete: function() {$img.remove()}
                });
              });
            }
            break;
          }
        }
      });
    });
  },

  changeSkin: function() {
    if(this.hidden.value != 'custom'){
      this.changeSkinNext();
      this.hidden.value = 'custom';
      OfflajnFireEvent(this.hidden, 'change');
    }
  },

  changeSkinNext: function() {
    var value = this.hidden.value;
    var def = this.data[value];
    for (var k in def) {
      var p = dojo.byId(this.control + k);

      if(!p) {
        var n = this.id.replace(this.name, '');
        p = dojo.byId(n + k);
      }
      if(p) {
        var v = def[k];
        if(v.indexOf("**") >= 0){
            var newv = v.split('|*|');
            var oldv = p.value.split('|*|');
            for(var i = 0; i < oldv.length; i++){
                if(newv[i] != '**'){
                    oldv[i] = newv[i];
                }
            }
            v = oldv.join('|*|');
        }else if(v.length > 0 && v.indexOf("{") == 0){
          var orig = {};
          if(p.value.length > 0 && p.value.indexOf("{") == 0){
            orig = dojo.fromJson(p.value);
          }
          var newValues = dojo.fromJson(v);
          for(var key in newValues){
            if(!orig[key]) orig[key] = {};
            for(var key2 in newValues[key]){
              orig[key][key2] = newValues[key][key2];
            }
          }
          v = dojo.toJson(orig);
        }
        p.value = v;
        OfflajnFireEvent(p, 'change');
      }
    }
    this.span.innerHTML = "The <b>"+value.replace(/^.*?_/,"").replace(/_/g," ")+" "+this.label+"</b> has been set.";

    if(this.dependency){
      window[this.dependency+'delay'] = true;
    }
  }
});
