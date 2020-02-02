/* 
 * jQuery Mobile Framework : "stepper" plugin: add incrementer decrementer buttons to quantity input
 * Copyright (c) Nora Brown
 * CC 3.0 Attribution.  May be relicensed without permission/notifcation.
 * https://github.com/nabrown/jQuery-Mobile-Stepper-Widget
 * Looks for container w/ data-role="stepper"
*/	
(function (jQuery, undefined){
	if (typeof jQuery.widget == 'undefined') return; 
	jQuery.widget("mobile.stepper", jQuery.mobile.widget, {
		options: {
			direction: "horizontal",
			shadow: false,
			excludeInvisible: true,
			step: 1,
			theme: "a"
		},
		_create: function(){
			var jQueryel = this.element,
				o = jQuery.extend(this.options, jQueryel.data("options")),
			 	jQueryinput = jQueryel.find('input'),
				jQueryincBtn = jQuery('<a class="inc" data-role="button">+</a>'),
				jQuerydecBtn = jQuery('<a class="dec" data-role="button">-</a>'),
				// Get min and max from input's attributes
				min = parseInt(jQueryinput.attr('min')),
				max = parseInt(jQueryinput.attr('max')),
				flCorners = o.direction == "horizontal" ? [ "ui-corner-left", "ui-corner-right" ] : [ "ui-corner-top", "ui-corner-bottom" ];
			
			// Insert button markup
			if(o.direction == "horizontal"){
				jQueryinput.before(jQuerydecBtn).after(jQueryincBtn);
			}else{
				jQueryinput.before(jQueryincBtn).after(jQuerydecBtn).wrap('<div class="step-input-wrap" />');
			}
			
			// Bind increment and decrement functions to click event
			jQueryel.find('.inc, .dec').click(function(){
				
				var jQuerybtn = jQuery(this),
					oldVal = parseInt(jQueryinput.val());
				
				if (jQuerybtn.hasClass('inc')){
					var newVal = oldVal == max ? max : oldVal + parseInt(o.step);
				} else {
					var newVal = oldVal == min ? min : oldVal - parseInt(o.step);
				}
				jQueryinput.val(newVal);
				jQueryinput.trigger('change');
			}).buttonMarkup({theme: o.theme}); // Enhance button markup

			jQueryel.addClass( "ui-controlgroup ui-controlgroup-" + o.direction );
				
			function flipClasses( els ) {
				els.removeClass( "ui-btn-corner-all ui-shadow" )
					.eq( 0 ).addClass( flCorners[ 0 ] )
					.end()
					.last().addClass( flCorners[ 1 ] ).addClass( "ui-controlgroup-last" );
			}
			
			flipClasses( jQueryel.find( ".ui-btn" + ( o.excludeInvisible ? ":visible" : "" ) ) );
			flipClasses( jQueryel.find( ".ui-btn-inner" ) );

			if ( o.shadow ) {
				jQueryel.addClass( "ui-shadow" );
			}
		}
	});
	
	//auto self-init widgets
	jQuery( document ).bind( "pagecreate create", function( e ){
		jQuery( ":jqmData(role='stepper')", e.target ).stepper({ excludeInvisible: false });
	});
	

})(jQuery);

