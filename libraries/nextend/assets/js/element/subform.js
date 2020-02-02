(function(dojo) {
    dojo.declare("NextendElementSubform", NextendElement, {
        constructor: function(args) {
            dojo.mixin(this, args);
            this.hidden = dojo.byId(this.hidden);
            this.form = this.hidden.form.nextendform;
            var name = dojo.attr(this.hidden, 'name').match(/\[(.*?)\]/g);
            
            if(name){
                this.name = name[name.length-1].substr(1,name[name.length-1].length-2);
                this.panel = dojo.byId('nextend-'+this.name+'-panel');
            }else{
                return;
            }
            
            dojo.connect(this.hidden, 'change', this, 'reset');
            this.reset();
        },

        reset: function() {
            if (this.value != this.hidden.value) {
                this.value = this.hidden.value;
                this.loadSubform();
            }
        },
        
        loadSubform: function(){
            if(typeof window.nextendajax == 'undefined') window.nextendajax = 0;
            window.nextendajax++;
            dojo.addClass(this.hidden.parentNode, 'nextend-ajax-active');
            var orig = [];
            if(this.value == this.origvalue){
                orig = dojo.clone(this.form.data);
            }
            var data = {
                orig: orig,
                control_name: this.form.control_name,
                xml: (typeof this.xml === 'undefined' ? this.form.xml : this.xml),
                tab: this.tab,
                name: this.name,
                value: this.hidden.value,
                loadedJSS: this.form.loadedJSS,
                loadedCSS: this.form.loadedCSS
            };
            var d = {};
            d.data = dojo.toJson(data);
            d.get = dojo.toJson(this.URLToArray(location.href));
            if(typeof this.form.extra != 'undefined'){
                dojo.mixin(d, this.form.extra);
            }
            dojo.mixin(d, {
                nextendajax: 1,
                mode: 'subform'
            });
            var newd = {};
            for(k in d){
                if(typeof d[k] == 'string' || typeof d[k] == 'number') newd[k] = d[k];
            }
            
            var xhrArgs = {
                url: this.form.url,
                handleAs: 'json',
                content: newd,
                load: dojo.hitch(this, 'load'),
                error: dojo.hitch(this, 'error')
            };
            var deferred = dojo.xhrPost(xhrArgs);
        },
        
        load: function(response){
            this.panel.innerHTML = response.html;
            eval(response.scripts);
            dojo.removeClass(this.hidden.parentNode, 'nextend-ajax-active');
            window.nextendajax--;
            njQuery(window).trigger("resize");
        },
        
        error: function(){
            window.nextendajax--;
            alert('There was an error with the AJAX call. Please refresh the page!');
        },
        URLToArray: function(url) {
            var request = {};
            var pairs = url.substring(url.indexOf('?') + 1).split('&');
            for (var i = 0; i < pairs.length; i++) {
                var pair = pairs[i].split('=');
                request[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
            }
            return request;
        }
    });
})(ndojo);