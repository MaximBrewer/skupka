<?php
/**
 *
 * The file for the advanced setting
 *
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2012-2018 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @version $Id: default_advanced.php 2015-04-01 19:44 sakis $
 * @since		1.8.0
 */

defined('_JEXEC') or die('Restricted access');


if($item->data_type=='float' || $item->data_type=='int' || $item->data_type=='date'){
	ShopFunctions::$categoryTree='';
	if(count($item->filter_category_ids)>0){
		$categoryTree=cfHelper::categoryListTree($item->filter_category_ids);
	}else $categoryTree=cfHelper::categoryListTree();


/*
 * The class name of each li indicates for each display type should be displayed
 * e.g. class:setting6 will be displayed for the display type 6 (range slider), setting5 for range inputs
 */
?>

<div class="bdpopup cf_advacned_settings" id="window<?php echo $item->id?>">
	<a id="hide_popup<?php echo $item->id?>" class="hide_popup"></a>
	<h3><?php echo JText::_('COM_CUSTOMFILTERS_ADV_SETTINGS');?></h3>
	<ul class="adminformlist">
		<li class="setting6">
			<label class="cflabel" for="slider_min_value_<?php echo $item->id?>"><?php echo JText::_('COM_CUSTOMFILTERS_SLIDER_MIN_VALUE_LABEL');?>:</label> <input type="text"
			name="slider_min_value[<?php echo $item->id?>]"
			id="slider_min_value_<?php echo $item->id?>"
			value="<?php echo $item->slider_min_value ?>" class="inputbox"
			size="4" maxlength="8" />
		</li>
		<li class="setting6">
			<label class="cflabel" for="slider_max_value_<?php echo $item->id?>"><?php echo JText::_('COM_CUSTOMFILTERS_SLIDER_MAX_VALUE_LABEL');?>
			:</label> <input type="text"
			name="slider_max_value[<?php echo $item->id?>]"
			id="slider_max_value_<?php echo $item->id?>"
			value="<?php echo $item->slider_max_value ?>" class="inputbox"
			size="4" maxlength="8" />
		</li>
		<li class="setting5 setting6 setting8">
			<label class="cflabel" for="categories_<?php echo $item->id?>"><?php echo JText::_('COM_CUSTOMFILTERS_FILTER_TO_CATEGORIES');?>
			:</label> 
			<select class="cf-choosen-select"
			data-placeholder="<?php echo JText::_('JOPTION_ALL_CATEGORIES');?>"
			id="categories_<?php echo $item->id?>"
			name="filter_categories[<?php echo $item->id?>][]"
			multiple="multiple">
			<?php echo $categoryTree; ?>
			</select>
		</li>
	</ul>
	<button class="btn bdokbutton" id="close_btn<?php echo $item->id?>"
		onclick="return false;">OK</button>
</div>
<script type="text/javascript">displayPopup(<?php echo $item->id?>);</script>
<?php }?>
