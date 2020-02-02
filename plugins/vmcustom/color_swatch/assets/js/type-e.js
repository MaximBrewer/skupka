/*------------------------------------------------------------------------* Color Swatch Plugin for Virtuemart* author    CMSMart Team* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL* Websites: http://cmsmart.net* Email: team@cmsmart.net* Technical Support:  Forum - http://cmsmart.net/forum* version 2.0.0-------------------------------------------------------------------------*/

(function($){
	jQuery(document).ready(function (){		jQuery(".color_swatch_input").each(function (){ 			jQuery(this).click(function (){				jQuery(".color_swatch_input").not(this).prop("checked", false);			});		});		jQuery(".price-plugin").remove();		jQuery(".product-field-display >span >.label_color").each(function (){			var elthis = jQuery(this);			jQuery(this).click(function (){				elthis.addClass("color_selected");				jQuery(".product-field-display >span >.label_color").not(this).removeClass("color_selected");			});		});	});
})(jQuery)

