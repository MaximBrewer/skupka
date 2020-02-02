(function(dojo) {
    dojo.declare("NextendElementFontmanager", NextendElement, {
        constructor: function(args) {
            this.base64 = 0;
            dojo.mixin(this, args);
            this.hidden = dojo.byId(this.hidden);
            this.button = dojo.byId(this.button);
            this.importbtn = dojo.byId(this.importbtn);
            dojo.connect(this.importbtn, 'click', this, 'doImport');
            this.exportbtn = dojo.byId(this.exportbtn);
            dojo.connect(this.exportbtn, 'click', this, 'doExport');
            this.message = dojo.byId(this.message);
            this.fontmanager = window.nextendfontmanager;
            dojo.connect(this.button, 'click', this, 'showFontmanager');
            
            var importbuttons = dojo.query('.nextend-font-import');
            for(var i = 0; i < importbuttons.length; i++){
                dojo.style(importbuttons[i], 'visibility', 'hidden');
            }
        },
        
        showFontmanager: function(){
            this.fontmanager.firsttab = this.firsttab;
            this.fontmanager.show(this.tabs, this.translatedTabs, this.base64 ? Base64.decode(this.hidden.value) : this.hidden.value);
            this.fontmanager.onSave = dojo.hitch(this,'save');
        },
        
        save: function(value){
            this.hidden.value = this.base64 ? Base64.encode(value) : value;
            this.fontmanager.onSave = function(){};
        },
        
        doImport: function(){
            dojo.style(this.hidden, 'width', '100%'); 
            if(typeof window.fontmanagercopy != 'undefined'){
                this.hidden.value = window.fontmanagercopy;
                this.fireEvent(this.hidden, 'change');
                this.changeMessage(this.txt.importingdone);
            }else{
                dojo.attr(this.hidden, 'type', 'text');
                this.hidden.focus();
                this.hidden.select();
            }
        },
        
        doExport: function(){
            window.fontmanagercopy = this.hidden.value;
            this.changeMessage(this.txt.youcanimport);
            var importbuttons = dojo.query('.nextend-font-import');
            for(var i = 0; i < importbuttons.length; i++){
                dojo.style(importbuttons[i], 'visibility', 'visible');
            }
            //dojo.style(this.hidden, 'width', '100%'); 
            //dojo.attr(this.hidden, 'type', 'text');

            //this.hidden.focus();
            //this.hidden.select();
        },
        
        changeMessage: function(text){
            if(this.timeout) clearTimeout(this.timeout);
            this.message.innerHTML = text;
            this.timeout = setTimeout(dojo.hitch(this, 'changeMessage', ''), 5000);
        }
    });
})(ndojo);