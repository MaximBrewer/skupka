<?php
/**
 * @version		$Id: color.php 2014-1-13 14:29 sakis Terz $
 * @package		customfieldsforall
 * @copyright	Copyright (C)2014 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_PLUGINS.DIRECTORY_SEPARATOR.'vmcustom'.DIRECTORY_SEPARATOR.'customfieldsforall'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'filter.php');
$input=JFactory::getApplication()->input;
$filterInput=CustomfieldsForAllFilter::getInstance(); //filter

$class='';
$wrapper_class='';
$selects= array();

$options=$viewData->values;
$custom_params=$viewData->custom_params;
$required=!empty($custom_params['is_required'])&& $viewData->pb_group_id==''?true:false;

$document=JFactory::getDocument();
$document->addStyleSheet( JURI::root(true).'/plugins/vmcustom/customfieldsforall/assets/css/customsforall_fe.css');
if($required)$document->addScript( JURI::root(true).'/plugins/vmcustom/customfieldsforall/assets/js/customfields_fe.js');

//VM version dependent variables
if(version_compare(VM_VERSION, '2.9','lt')){
	$virtuemart_customfield_id=end($viewData->options)->virtuemart_customfield_id; 
	$field_name='customPlugin['.$virtuemart_customfield_id.']['.$this->_name.']';
} else {
	$virtuemart_customfield_id=$viewData->virtuemart_customfield_id; 
	$field_name='customProductData['.$viewData->virtuemart_product_id.']['.$viewData->virtuemart_custom_id.']['.$virtuemart_customfield_id.']';
}

if($viewData->calculate_price){
	if(!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'currencydisplay.php');
	$currency = CurrencyDisplay::getInstance();
	if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
	$calculator = calculationHelper::getInstance();
}


if(!empty($options)){
	$wrapper_id='cf4all_wrapper_'.$viewData->virtuemart_customfield_id.'_'.$viewData->pb_group_id;
	if($required)$wrapper_class=' cf4all_required';
	?>

<div class="cf4all_wrapper cf4all_color_buttons <?php echo $wrapper_class?>" id="<?php echo $wrapper_id?>">
<?php 
if($required):?>
<span class="cf4all_error_msg" style="display:none"><?php echo JText::_('PLG_CUSTOMSFORALL_REQUIRED_FIELD_MULTI')?></span>
<?php 
endif;?>

	<?php
	foreach ($options as $key=>$v) {		
		$custom_value_name_multi=explode('|', $v->customsforall_value_name);
		$label_html='';
		$count_multi_values=count($custom_value_name_multi);
		$width=100/$count_multi_values;
		if($count_multi_values==1)$customsforall_value_label=$custom_value_name_multi[0];
		//multi-colors
		foreach($custom_value_name_multi as $custom_value_name){
			//validate that is a color either hex or a standard color name
			$color=$filterInput->checkNFormatColor($custom_value_name);
			if(empty($color))continue;
			$ishex=false;
			if(strpos($color, '#')!==false)$ishex=true;
			$label_style='background-color:'.$color.'; width:'.$width.'%;';
			$label_html.='<div class="cf4all_inner_value" style="'.$label_style.'"></div>';
		}
		
		$ishex=false;
		if(strpos($color, '#')!==false)$ishex=true;
		$class='cf4all_color_btn_medium';
		$input_id='cf4all_input_'.$virtuemart_customfield_id.'_'.$v->customsforall_value_id.'_'.$viewData->pb_group_id.$input->get('bundled_products','');
		$price='';
		$title='';
		$tooltip='';

		//use the color name/label as tooltip
		if(!empty($v->customsforall_value_label))$tooltip.=JText::_($v->customsforall_value_label).' ';
		else if($ishex==false)$tooltip.=JText::_($color).' ';

		$custom_price=(float)$v->custom_price;
		if(!empty($viewData->calculate_price) && !empty($custom_price)){
			if($custom_price>=0)$op='+';
			else $op='';
			$price=$op.$currency->priceDisplay($calculator->calculateCustomPriceWithTax($custom_price));

			if($custom_params['display_price']=='tooltip'){
				$tooltip.=$price.' ';
			}
		}

		if(!empty($tooltip)){
			JHTML::_('behavior.tooltip');//load the tooltips script
			$title=' title="'.$tooltip.'"';
			$class.=' hasTip';
		}
		?>
	<div class="inline-control-group">
	<input type="checkbox" value="<?php echo $v->id ?>" id="<?php echo $input_id?>" class="cf4all_checkbox" name="<?php echo $field_name?>[customsforall_option<?php echo $key;?>]"/> 
	<label class="cf4all_button cf4all_color_btn <?php echo $class?>" for="<?php echo $input_id?>" <?php echo $title ?>><?php echo $label_html?></label>
	</div>
		<?php		
	}?>

</div>
	<?php
	if($viewData->calculate_price){
		$script='
		jQuery(function($){ 
			var checkboxes=jQuery("#'.$wrapper_id.'").find("input[type=checkbox]");
			checkboxes.click(function(){
				var form = $(this).closest("form");
				var virtuemart_product_id = jQuery(form).find("input[name=\'virtuemart_product_id[]\']").val();
				Virtuemart.setproducttype(form, virtuemart_product_id);
			});
		});';
		
		$document->addScriptDeclaration($script);
	}
}
