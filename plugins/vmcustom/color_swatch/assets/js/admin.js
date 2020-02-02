/*------------------------------------------------------------------------* Color Swatch Plugin for Virtuemart* author    CMSMart Team* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL* Websites: http://cmsmart.net* Email: team@cmsmart.net* Technical Support:  Forum - http://cmsmart.net/forum* version 2.0.0-------------------------------------------------------------------------*/

(function($){
	$(document).ready(function(){
		$("#basic-options").click();
		$("#basic-options").addClass("icon-equalizer");
		$("#advanced-options").addClass("icon-cogs");
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
			$('#jform_params_widthp').parents('.control-group').slideDown();
			$('#jform_params_heightp').parents('.control-group').slideDown();
		}else {
			$('#jform_params_widthp').parents('.control-group').slideUp();
			$('#jform_params_heightp').parents('.control-group').slideUp();
		}
		
		$('#cms1').click(function (){
			var el = $(this).attr('class');
			if(el == 'switcher-on'){
				$('#jform_params_widthp').parents('.control-group').slideDown();
				$('#jform_params_heightp').parents('.control-group').slideDown();
			}else {
				$('#jform_params_widthp').parents('.control-group').slideUp();
				$('#jform_params_heightp').parents('.control-group').slideUp();
			}
		});
		///////////////////
		var cmsclass2 = $('#cms2').attr('class');
		if(cmsclass2 == 'switcher-on'){
			$('#jform_params_cat_width').parents('.control-group').slideDown();
			$('#jform_params_cat_height').parents('.control-group').slideDown();
			$('#jform_params_el_image').parents('.control-group').slideDown();
			$('#jform_params_el_block').parents('.control-group').slideDown();
		}else {
			$('#jform_params_cat_width').parents('.control-group').slideUp();
			$('#jform_params_cat_height').parents('.control-group').slideUp();
			$('#jform_params_el_image').parents('.control-group').slideUp();
			$('#jform_params_el_block').parents('.control-group').slideUp();
		}
		$('#cms2').click(function (){
			var el = $(this).attr('class');
			if(el == 'switcher-on'){
				$('#jform_params_cat_width').parents('.control-group').slideDown();
				$('#jform_params_cat_height').parents('.control-group').slideDown();
				$('#jform_params_el_image').parents('.control-group').slideDown();
				$('#jform_params_el_block').parents('.control-group').slideDown();
			}else {
				$('#jform_params_cat_width').parents('.control-group').slideUp();
				$('#jform_params_cat_height').parents('.control-group').slideUp();
				$('#jform_params_el_image').parents('.control-group').slideUp();
				$('#jform_params_el_block').parents('.control-group').slideUp();
			}
		});
		
		function browserName(){
		   var Browser = navigator.userAgent;
		   if (Browser.indexOf('MSIE') >= 0){
			Browser = 'MSIE';
		   }
		   else if (Browser.indexOf('Firefox') >= 0){
			Browser = 'Firefox';
		   }
		   else if (Browser.indexOf('Chrome') >= 0){
			Browser = 'Chrome';
		   }
		   else if (Browser.indexOf('Safari') >= 0){
			Browser = 'Safari';
		   }
		   else if (Browser.indexOf('Opera') >= 0){
			  Browser = 'Opera';
		   }
		   else{
			Browser = 'UNKNOWN';
		   }
		   return Browser;
		}
		function browserVersion(){
		   var index;
		   var version = 0;
		   var name = browserName();
		   var info = navigator.userAgent;
		   index = info.indexOf(name) + name.length + 1;
		   version = parseFloat(info.substring(index,index + 3));
		   return version;
		}
		
		//document.write('<h2>Bạn đang dùng trình duyệt ' + browserName() + ' version ' + browserVersion() + '</h2>');
		if(browserName() == 'MSIE' && browserVersion() == 8){
			jQuery('FIELDSET INPUT').css({
				'margin':'0 !important',
				'border':'0'
			});
			jQuery('.pane-sliders input[type="text"]').css({
				'margin-top':'6px !important'
			});
		}
		
	});
})(jQuery)

