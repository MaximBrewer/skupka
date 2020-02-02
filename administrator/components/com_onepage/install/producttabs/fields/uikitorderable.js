 (function () {
        jQuery(document).on('ready', function () {
            var sortable = jQuery('[data-uk-sortable]');
			if (sortable.length > 0) {
            sortable.on('stop.uk.sortable', function (e, el, type) {
                setOrdering(sortable, el);
            });
            setOrdering(sortable);
			}
			
		  var rJ = jQuery('.uk-icon-remove'); 
		  rJ.click(function() {
		    var el = jQuery(this).parents('div.sortrow'); 
			
			return remove(el); 
		  }); 
        });
		function remove(el) {
		   
		  el.appendTo('.unused_values'); 
		  sortable = jQuery('[data-uk-sortable]');
		  setOrdering(sortable); 
		}
        function setOrdering(sortable, activeEl) {
           
			var id = ''; 
			var aA = new Array(); 
            
			if (activeEl) {
			 var group = activeEl.element.attr('data-group'); 
			 var str = '[data-group='+group+'].sortrow'; 
			}
			else
			{
				var str = '>div.sortrow'; 
			}
			 sortable.find(str).each(function () {
				
				
				var jQ = jQuery(this); 
				var val = jQ.attr('data-val'); 
				var classN = jQ.attr('class'); 
				//if (typeof classN != 'undefined')
				//	if (classN.indexOf('unused_separator')>=0) return false; 
				
				var p = jQ.parents('div.unused_values'); 
				if (p.length > 0) return false; 
				
				var p = jQ.prevAll('.unused_separator'); 
				if (p.length > 0) return false; 
				
				
			 if ((typeof val != 'undefined') && (val != 'undefined')) {
				id = jQ.attr('data-val-id'); 
				aA.push(val); 
				console.log(val); 
				
			 }
                
            });
			console.log('...'); 
			var aS = aA.join(','); 
			if (id) {
			   var d = document.getElementById(id); 
			   if (d != null) 
			   {
				   d.value = aS; 
			   }
			}
			//console.log(aS); 
		
            
        }
  			
    })(jQuery);

	
UIkit.ready(function() {UIkit.$body.prepend('<div class="uk-float-right uk-badge">UIkit version ' + UIkit.version + '</div>')});