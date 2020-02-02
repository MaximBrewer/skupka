<?php
/*------------------------------------------------------------------------
* Color Swatch Plugin for Virtuemart
* author    CMSMart Team
* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://cmsmart.net/forum
* version 2.0.0
-------------------------------------------------------------------------*/
header("Content-Type: text/scriptlet");

if(!class_exists('vRequest')) require(dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/administrator/components/com_virtuemart/helpers/vrequest.php');

$zoom = vRequest::getInt('zoom');
$w = vRequest::getInt('w');
$h = vRequest::getInt('h');
$wp = vRequest::getInt('wp');
$hp = vRequest::getInt('hp');
$url = vRequest::getString('url');
$proid = vRequest::getInt('proid');
?>

jQuery(document).ready(function (){
	jQuery(".product-field-display > span > .label_color").click(function (){
		var elthis = jQuery(this);
		var labeltext = elthis.parent('.colors_box').children('.color_title:first').text();
		elthis.parents('.product-field-display').find('#color_selected').html(' / '+labeltext);
		
		var path = jQuery(this).children('.path_image').val(),
			order_image = jQuery(this).children('.order_image').val(),
			usezoom = jQuery(this).children('.usezoom').val(),
			w = <?php echo $w?>,
			h = <?php echo $h?>,
			wp = <?php echo $wp?>,
			hp = <?php echo $hp?>,
			proid = <?php echo $proid?>;
			
		var page = jQuery('.productdetails-view').length;
			
		jQuery.ajax({
			type: "post",
			url: "<?php echo $url?>plugins/vmcustom/color_swatch/helpers/detail.php?url=<?php echo $url ?>",
			data: {
				page : page,
				path : path,
				w 	 : w,
				h	 : h,
				order_image : order_image,
				usezoom : usezoom
			},
			beforeSend : function (){
				jQuery('.productdetails-view').fadeTo('slow', 0.3);
				jQuery(".main-image").after("<img id='color_ajax_loading' src='<?php echo $url ?>plugins/vmcustom/color_swatch/assets/images/loading.gif'>");
			},
			success: function(data){
				// Display color image in category list
				if (jQuery('.vm-product-media-container').length) {
					elthis.closest('.spacer').find('.vm-product-media-container').fadeTo('slow', 1);
					jQuery('#color_ajax_loading').remove();
					jQuery('.vm-product-media-container script').remove();
					jQuery('.vm-product-media-container style').remove();
					if (data) {	
						elthis.closest('.spacer').find('.vm-product-media-container a img').replaceWith(data);
					}
				}
				 
				// Display color image in product detail
				if (jQuery('.productdetails-view').length) {
					jQuery('.productdetails-view').fadeTo('slow', 1);
					jQuery('#color_ajax_loading').remove();
					if (data) {
						jQuery(".main-image").replaceWith(data);
						if(jQuery(".additional-images").size() > 1)
							jQuery(".additional-images:eq(1)").remove();
						jQuery('#carousel').elastislide({
							minItems : 2
						});
						
						// Image zoom
						<?php if ($zoom == 1) { ?>
						jQuery('#color_zoom').elevateZoom({
							cursor: "crosshair",
							zoomWindowFadeIn: 500,
							zoomWindowFadeOut: 750,
							zoomType : "window",
							zoomWindowWidth : wp,
							zoomWindowHeight : hp
							
						});
						<?php } ?> 
					}
				}

				jQuery("#carousel").css("max-height","auto !important");
				return false;
			}
		});	
	});
});	

function replaceImg(url, w, h){
	jQuery(document).ready(function (){
		var data = url.split('/thumbnail/');
		var dataimg = data[0]+'/'+data[1];
		
		<?php if($zoom == 1) {?>
			jQuery(".main-image > a").attr(dataimg);
		<?php }else{?>		
			jQuery(".main-image > a").attr('href', dataimg);
		<?php }?>
		jQuery(".main-image > a > img").attr('src', dataimg);
		<?php if($zoom == 1) {?>
		jQuery(".main-image > a > img").attr('data-zoom-image', dataimg);
		
		var ez =   jQuery('#color_zoom').data('elevateZoom');	  
  		ez.swaptheimage(dataimg, dataimg); 
  		<?php }?>
	});	
}