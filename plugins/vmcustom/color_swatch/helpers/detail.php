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
if(!class_exists('vRequest')) require(dirname(dirname(dirname(dirname(__DIR__)))).'/administrator/components/com_virtuemart/helpers/vrequest.php');

$path = vRequest::getInt('path');
$w = vRequest::getInt('w');
$h = vRequest::getInt('h');
$page = vRequest::getInt('page');
$usezoom = vRequest::getInt('usezoom');
$url = vRequest::getString('url');
$order_image = vRequest::getInt('order_image');
$pathList = dirname(dirname(dirname(dirname(__DIR__)))).'/images/stories/virtuemart/color_swatch/images/'.$path.'/thumbnail';

$images = list_files($pathList, $order_image);

function list_files($directory = '.', $order = 1) {
	$array = array();
	if ($directory != '.') {
		$directory = rtrim($directory, '/') . '/';
	}
	if(is_dir($directory)){
		$dir = opendir($directory);
		$list = array();
		while($file = readdir($dir)){
			if ($file != '.' and $file != '..') {
				$ctime = filectime($directory . $file) . ',' . $file;
				$list[$ctime] = $file;
			}
		}
		closedir($dir);
			if ($order == 1) { // Name
			asort($list);
		} elseif ($order == 2) { // Time
			ksort($list);
		} elseif ($order == 3) { // Random
			shuffle($list);
		}
				
		return $list;
	}
	
	
}
$count = count($images);

// check image thumbnail @ken
if($images){
	foreach ($images as $key=> $img){
		if(strpos($img, 'thumbnail_') !== false){
			unset($images[$key]);
		}
	}
}


if ($count) {
	if ($page) {
		echo '
			<script src="'.$url.'plugins/vmcustom/color_swatch/assets/js/modernizr.custom.17475.js"></script>
			<script src="'.$url.'plugins/vmcustom/color_swatch/assets/js/jquery.elastislide.js"></script>
			<script src="'.$url.'plugins/vmcustom/color_swatch/assets/js/jquery.elevatezoom.js"></script>
			<style src="'.$url.'plugins/vmcustom/color_swatch/assets/css/elastislide.css"></style>
			<div class="main-image">';
		if($images){
			if($usezoom){
				echo '
				<a rel="vm-additional-images"><img id="color_zoom" data-zoom-image="'.$url.'images/stories/virtuemart/color_swatch/images/'.$path.'/'.array_values($images)[0].'" alt="" src="'.$url.'images/stories/virtuemart/color_swatch/images/'.$path.'/'.array_values($images)[0].'"></a>
				<div class="clear"></div>';
			}else{
				echo '
				<script>
					jQuery(document).ready(function() {
						Virtuemart.updateImageEventListeners();
					});
					Virtuemart.updateImageEventListeners = function() {
						jQuery("a[rel=vm-additional-images]").fancybox({
							"titlePosition" 	: "inside",
							"transitionIn"	:	"elastic",
							"transitionOut"	:	"elastic"
						});
						jQuery(".additional-images a.product-image.image-0").removeAttr("rel");
						jQuery(".additional-images img.product-image").click(function() {
							jQuery(".additional-images a.product-image").attr("rel","vm-additional-images" );
							jQuery(this).parent().children("a.product-image").removeAttr("rel");
							var src = jQuery(this).parent().children("a.product-image").attr("href");
							jQuery(".main-image img").attr("src",src);
							jQuery(".main-image img").attr("alt",this.alt );
							jQuery(".main-image a").attr("href",src );
							jQuery(".main-image a").attr("title",this.alt );
							jQuery(".main-image .vm-img-desc").html(this.alt);
						}); 
					}
				</script>';	
				echo '
				<a rel="vm-additional-images" href="'.$url.'images/stories/virtuemart/color_swatch/images/'.$path.'/'.array_values($images)[0].'"><img id="color_zoom" data-zoom-image="'.$url.'images/stories/virtuemart/color_swatch/images/'.$path.'/'.array_values($images)[0].'" alt="" src="'.$url.'images/stories/virtuemart/color_swatch/images/'.$path.'/'.array_values($images)[0].'"></a>
				<div class="clear"></div>';
			}
		}	
			
		echo '
			</div>
			<div class="additional-images">
				<ul id="carousel" class="elastislide-list">
		';
		
		foreach ($images as $key=> $img){
			echo "
				<li>
				<img id='addition_thumb' onclick='replaceImg(this.src);' src='".$url."images/stories/virtuemart/color_swatch/images/".$path."/thumbnail/".$images[$key]."' >
				</li>	
			";
		}
		echo '</ul>
			</div>';
		
	} else {
		echo '<img src="' . $url . 'images/stories/virtuemart/color_swatch/images/' . $path . '/' . array_values($images)[0] . '" class="browseProductImage" >';
	}
}

