/*------------------------------------------------------------------------
* Color Swatch Plugin
* author    CMSMart Team
* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Technical Support:  Forum - http://cmsmart.net/forum
* version 1.0
-------------------------------------------------------------------------*/

(function($){
	$(document).ready(function(){		

            $('#jform_params_max_item').blur(function(){
                    var vl = jQuery(this).val();
                    var vl2 = isNaN(vl);
                    if(vl2 == true){
                            alert('Entered the wrong data, you need to fill "max item" exact numbers !');
                            jQuery(this).val('2');
                    }
            });
               
		$("#basic-options").click();
        /*
		$("#basic-options").addClass("icon-arrow-down5");
		$("#advanced-options").addClass("icon-arrow-down5");
        $("#license_key-options").addClass("icon-arrow-down5");
        */
        
		$(".panel input[type='radio']").after("<span class='cms-radio'></span>");
		
		/////
		$.each([ 1, 2, 3, 4, 5 ], function(key, val){
			$('.select .cms_switch'+val).css('display','none');
			
			var value = ($('.select .cms_switch'+val).val());
			
			var style = ($('.select .cms_switch'+val).val() == 1) ? 'on' : 'off',
					switcher = ('<div id="cms'+val+'" class="switcher-'+style+'"></div');
			$('.select .cms_switch'+val).parent('.select').after(switcher).hide();
			
			$('#cms'+val).click(function (){
				if($('.select .cms_switch'+val).val() == 1){
					$('#cms'+val).attr('class','switcher-off');
					$('.select .cms_switch'+val).val(0);
					
				}else{
					$('#cms'+val).attr('class','switcher-on');
					$('.select .cms_switch'+val).val(1);
				}		
			});
		});

		var cmsclass = $('#cms1').attr('class');
		if(cmsclass == 'switcher-on'){
			$('#jform_params_widthp').parent('li').slideDown();
			$('#jform_params_heightp').parent('li').slideDown();
		}else {
			$('#jform_params_widthp').parent('li').slideUp();
			$('#jform_params_heightp').parent('li').slideUp();
		}
		
		$('#cms1').click(function (){
			var el = $(this).attr('class');
			if(el == 'switcher-on'){
				$('#jform_params_widthp').parent('li').slideDown();
				$('#jform_params_heightp').parent('li').slideDown();
			}else {
				$('#jform_params_widthp').parent('li').slideUp();
				$('#jform_params_heightp').parent('li').slideUp();
			}
		});
		///////////////////
		var cmsclass2 = $('#cms2').attr('class');
		if(cmsclass2 == 'switcher-on'){
			$('#jform_params_cat_width').parent('li').slideDown();
			$('#jform_params_cat_height').parent('li').slideDown();
			$('#jform_params_el_image').parent('li').slideDown();
			$('#jform_params_el_block').parent('li').slideDown();
		}else {
			$('#jform_params_cat_width').parent('li').slideUp();
			$('#jform_params_cat_height').parent('li').slideUp();
			$('#jform_params_el_image').parent('li').slideUp();
			$('#jform_params_el_block').parent('li').slideUp();
		}
		$('#cms2').click(function (){
			var el = $(this).attr('class');
			if(el == 'switcher-on'){
				$('#jform_params_cat_width').parent('li').slideDown();
				$('#jform_params_cat_height').parent('li').slideDown();
				$('#jform_params_el_image').parent('li').slideDown();
				$('#jform_params_el_block').parent('li').slideDown();
			}else {
				$('#jform_params_cat_width').parent('li').slideUp();
				$('#jform_params_cat_height').parent('li').slideUp();
				$('#jform_params_el_image').parent('li').slideUp();
				$('#jform_params_el_block').parent('li').slideUp();
			}
		});
		
		
		
	});
})(jQuery)