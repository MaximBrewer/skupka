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

defined('_JEXEC') or die();

$document = JFactory::getDocument();
//get data
$data = $viewData[1]->customfields;

//get param
$style = $viewData[2]->style_thumbnail;
$width_th = $viewData[2]->widthth;
$heigth_th = $viewData[2]->heightth;
$loadjs = $viewData[2]->loadjs;
$widths = $viewData[2]->widths;
$heigths = $viewData[2]->heights;
$show_type = $viewData[2]->show_type;
$show_title_cs = $viewData[2]->show_title_cs;
$show_tooltip = $viewData[2]->show_tooltip;
$usezoom =  $viewData[2]->usezoom;
$widthp = $viewData[2]->widthp;
$heigthp = $viewData[2]->heightp;

$termId = $viewData[2]->virtuemart_customfield_id;

// foreach ($data as $dt){
$dt = $viewData[2];
$thumbnail = $dt->thumbnail;
$proName = $dt->product_name;
$imagesLink = $dt->images;
$thumb = substr($thumbnail, 0, 6);
$status = $dt->status;
$title = $dt->title;
$tooltip = $dt->tooltip;

// @ken
$pathList = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/images/stories/virtuemart/color_swatch/images/'.$dt->virtuemart_customfield_id.'/thumbnail';
$list = array();
if (is_dir($pathList)){
  if ($dh = opendir($pathList)){
    while (($file = readdir($dh)) !== false){
		if(strpos($file, 'thumbnail_') === false ){
			if ($file != '.' and $file != '..') {
				$list[] = $file;
			}
		}
      
    }
    closedir($dh);
  }
}

if ($status == 1) {
	?>
	<span class="colors_box" <?php if ($show_type == 'horizontal') { ?> <?php } ?> >
		<label class="label_color" <?php if($show_tooltip == 1){?>title="<?php echo $tooltip?>"<?php }?> for="color_swatch_<?php echo $dt->virtuemart_customfield_id ?>">
			<?php if ($thumb != 'thumbn') { ?>
				<span class="span_color" id="span_thumb_color_<?php echo $dt->virtuemart_customfield_id;?>"></span>
			<?php } else { if($list){?>
				<span class="span_color" id="span_thumb_image">
					<img src="images/stories/virtuemart/color_swatch/images/<?php echo $dt->virtuemart_customfield_id ?>/thumbnail/<?php echo $thumbnail?>">
				</span>
			<?php } } ?>
			<input class="path_image" type="hidden" value="<?php echo $dt->virtuemart_customfield_id ?>">
			<input class="order_image" type="hidden" value="<?php echo $dt->order_image; ?>">
		</label>
		<input
			id="color_swatch_<?php echo $dt->virtuemart_customfield_id ?>"
			class="color_swatch_input" type="radio"
			value="<?php echo $dt->virtuemart_customfield_id?>"
			name="customProductData[<?php echo $viewData[1]->virtuemart_product_id?>][<?php echo $viewData[2]->virtuemart_custom_id?>][<?php echo $dt->virtuemart_customfield_id ?>][comment]">
		<?php if($show_title_cs == 1 && $show_type == 'vertical'){?>
			<span class="color_title"><?php echo $title?></span><br>
		<?php } ?>
		<?php if ($show_title_cs == 1 && $show_type == 'horizontal') { ?>
			<span class="color_title"><?php echo $title?></span>
		<?php } ?>
	</span>
	<?php 
}
// }

$id = "'colors_box'";

$document->addStyleSheet('plugins/vmcustom/color_swatch/assets/css/style.css.php?type='.$show_type.'&style='.$style.'&width='.$width_th.'&height='.$heigth_th.'&w='.$widths.'&h='.$heigths.'&thumb='.$thumb.'&show_title='.$show_title_cs.'&id='.$dt->virtuemart_customfield_id);
$document->addStyleSheet('plugins/vmcustom/color_swatch/assets/css/elastislide.css');

if($usezoom == 1 && $loadjs == 1)
	$document->addScript('plugins/vmcustom/color_swatch/assets/js/jquery.elevatezoom.js');

if($loadjs == 1){
	$document->addScript('plugins/vmcustom/color_swatch/assets/js/jquery-1.8.2-ui.min.js');
	$document->addScript('plugins/vmcustom/color_swatch/assets/js/jquery.elastislide.js');
	$document->addScript('plugins/vmcustom/color_swatch/assets/js/modernizr.custom.17475.js');
}

$document->addScript('plugins/vmcustom/color_swatch/assets/js/js.js.php?zoom='.$usezoom.'&w='.$widths.'&h='.$heigths.'&wp='.$widthp.'&hp='.$heigthp.'&url='.JURI::base().'&proid='.$viewData[2]->virtuemart_product_id);
$document->addScript('plugins/vmcustom/color_swatch/assets/js/type-e.js');

