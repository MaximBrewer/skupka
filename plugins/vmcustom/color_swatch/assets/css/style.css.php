<?php 
/*------------------------------------------------------------------------
* Color Swatch Plugin for Virtuemart
* author    CMSMart Team
* copyright Copyright (C) 2012 Cmsmart Team. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Technical Support:  Forum - http://cmsmart/forum
* Email: team@cmsmart.net
* version 2.0.0
-------------------------------------------------------------------------*/
header("Content-Type: text/css");

if(!class_exists('vRequest')) require(dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/administrator/components/com_virtuemart/helpers/vrequest.php');

$type = vRequest::getVar('type');
$thumb = vRequest::getVar('thumb');
$style = vRequest::getVar('style');
$show_title = vRequest::getInt('show_title');
$width = vRequest::getInt('width');
$height = vRequest::getInt('height');
$widths = vRequest::getInt('w');
$heights = vRequest::getInt('h');
$id = vRequest::getInt('id');
?>

.span_color, #span_img {
    cursor: pointer;
    display: block;
    
}
.color_swatch_input{
	display: none !important;
}
.color_selected {
    border: 1px solid red !important;
}
.colors_box{
	float:left;
}
.colors_box .label_color {
	border: 1px solid transparent;
	margin-bottom: 3px !important;
	display:block;
	width:<?php echo $width?>px;
	height:<?php echo $height?>px;
	<?php if ($type == 'vertical') { ?> 
		float: left; 
	<?php } ?>
}
<?php if ($type == 'horizontal') {?>
.vm-customfields-wrap .product-field-type-E {
    margin: 18px 8px 0 0;
	width: auto;
}
<?php } ?>
.product-field-type-E .product-fields-title {
	float: left;
	height: 18px;
}
.product-field-display {
    clear: both;
    display: block;
    position: relative;
}

<?php if ($type == 'horizontal') {?>
.color_title {    
	display: block;    
	float: left;
}
.product-field-display label {
    margin-right: 10px;
 }
.product-field-display {
 	padding-top: 5px;
}
<?php }else { ?>
.product-field-display label {
	 clear: both;
	 margin-top: 10px;
}
<?php }
if($style == 'circle'){?>
	.span_color, #span_img, .span_color > img{
		-webkit-border-radius: <?php echo $width/2?>px;
		-moz-border-radius: <?php echo $width/2?>px;
		border-radius: <?php echo $width/2?>px;
	}
	.product-field-display label {
		-webkit-border-radius: <?php echo ($width+4)/2?>px;
		-moz-border-radius: <?php echo ($width+4)/2?>px;
		border-radius: <?php echo ($width+4)/2?>px;
	}
<?php }?>

.colors_box #span_thumb_color_<?php echo $id;?>{
	display:block;
	text-indent:-9999em;
	background:#<?php echo $thumb?>; 
	width:<?php echo $width?>px; 
	height:<?php echo $height?>px;
}
#span_thumb_image img{
	width:<?php echo $width?>px; 
	height:<?php echo $height?>px;
}
.colors_box #span_thumb_image{
	width:<?php echo $width?>px !important;
	height:<?php echo $height?>px !important;
}
<?php if($show_title == 1 && $type == 'vertical'){?>
	.colors_box .color_title{
		float:left;
		margin-top:5px;
		width:<?php echo $width?>px; 
		text-align:left; 
		margin-left:8px; 
		margin-top:<?php echo $height/2; ?>px; 
	}
<?php } ?>
<?php if($show_title == 1 && $type == 'horizontal'){?>
	.colors_box .color_title{
		width:<?php echo $width?>px;
		text-align:center; 
	}
<?php } ?>
#carousel img {
    cursor: pointer;
}
.color_cat_select {
    color: rgba(0, 0, 0, 0);
}
#color_ajax_loading {
    margin-left: 10%;
    margin-top: -40%;
    position: absolute;
}
#color_ajax_loading2 {
   display: block;
    margin-left: 10%;
    margin-top: -16%;
    position: absolute;
}
.zoomWindowContainer > .zoomWindow{
	top: -1px !important;
}
.addtocart-bar {
    display: block;
    margin-top: 57px !important;
    padding: 0 !important;
}
.product-price {
    line-height: 20px;
}
.elastislide-carousel{
	padding: 0px ! important;
	display: block;
	transition: all 500ms ease-in-out 0s;
}
.elastislide-carousel .elastislide-list{
	padding: 0 !important;
	max-height: auto
}
.elastislide-carousel #carousel > li{
	border: 1px solid #dedede;
    margin-left: 3px;
    padding: 4px;
}
.elastislide-carousel #carousel img { 
	border:1px solid transparent;
	margin-right: 0 !important;
	width:<?php echo $widths;?>px;
	height:<?php echo $heights;?>px;
}
.product-field.product-field-type-E:first-child {
    margin-top: 0;
}
.vm-product-media-container .main-image{
	border: 1px solid #dedede;
}