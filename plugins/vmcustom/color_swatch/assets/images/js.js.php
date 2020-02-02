<?php
/*------------------------------------------------------------------------
 * Color Swatch Plugin
* author    CMSMart Team
* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Technical Support:  Forum - http://cmsmart.net/forum
* version 1.0
-------------------------------------------------------------------------*/
header("Content-Type: text/scriptlet");
if(!class_exists('vRequest')) require(dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/administrator/components/com_virtuemart/helpers/vrequest.php');
$zoom = vRequest::getInt('zoom');
$w = vRequest::getInt('w');
$h = vRequest::getInt('h');
$wp = vRequest::getInt('wp');
$hp = vRequest::getInt('hp');
$url = vRequest::getString('url');
?>

jQuery(document).ready(function (){

	
	   
	jQuery(".product-field-display >span >#label_color").each(function (){
		var elthis = jQuery(this);
		
		jQuery(this).click(function (){
			var path = jQuery(this).children('#path_image').val(),
				w = <?php echo $w?>,
				h = <?php echo $h?>,
				wp = <?php echo $wp?>,
				hp = <?php echo $hp?>;
			jQuery.ajax({
				 type: "post",
			  	 url: "<?php echo $url?>plugins/vmcustom/color_swatch/helpers/detail.php?url=<?php echo $url ?>",
			  	 data: { 
			  	 	path : path,
			  	 	w 	 : w,
			  	 	h	 : h
			  	 },
				 beforeSend : function (){
					jQuery('.productdetails-view').fadeTo('slow', 0.3);
					jQuery(".main-image").after("<img id='color_ajax_loading' src='<?php echo $url ?>plugins/vmcustom/color_swatch/assets/images/loading.gif'>");
				 },
			  	 success: function(data){
			     	jQuery('.productdetails-view').fadeTo('slow', 1);
					jQuery('#color_ajax_loading').remove();
					
			     	jQuery(".main-image").replaceWith(data);
			     	jQuery(".additional-images:eq(1)").remove();
			     	jQuery( '#carousel' ).elastislide({
			     		minItems : 2,
			     		horizontal : true
			     	});
			     	////
			     	<?php if($zoom == 1) {?>
			     	jQuery('#color_zoom').elevateZoom({
						cursor: "crosshair",
						zoomWindowFadeIn: 500,
						zoomWindowFadeOut: 750,
						zoomType : "window",
						zoomWindowWidth : wp,
						zoomWindowHeight : hp
						
					});
					<?php }?> 
					jQuery("#carousel").css("max-height","auto !important");
					return false;
			  	 }
			});
			
		});
	});
});	

function replaceImg(url){
	jQuery(document).ready(function (){
		jQuery(".main-image > a").attr('href', url);
		jQuery(".main-image > a > img").attr('src', url);
		<?php if($zoom == 1) {?>
		jQuery(".main-image > a > img").attr('data-zoom-image', url);
		
		var ez =   jQuery('#color_zoom').data('elevateZoom');	  
  		ez.swaptheimage(url, url); 
  		<?php }?>
	});	
}