/*Copyright Breakdesigns.net- All Rights Reserved*/
jQuery(document).ready(function(){	
	cfautocomplete();    
	})
	
	function cfautocomplete(){
		var baseURL=vmSiteurl+'?option=com_customfilters&task=module.getSuggestions&tmpl=component';	
		jQuery('#cf-autocomplete_0').autocomplete({
		      source: function( request, response ) {
		    	  jQuery.ajax({
		            url: baseURL,
		            dataType: "json",
		            data: {
		              q: request.term
		            },
		            success: function( data ) {
		              response( data );
		            }
		          });
		        },
		        minLength: 2,
		       
		        open: function() {
		          $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		        },
		        close: function() {
		          $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		        }
		      });
	    }
