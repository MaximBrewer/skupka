<?php
/**
 * @version		$Id: radio.php 2013-08-02 18:39 sakis Terz $
 * @package		customfieldsforall
 * @copyright	Copyright (C)2013 breakdesigns.net . All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

$input=JFactory::getApplication()->input;
$custom_params=$viewData->custom_params;
$required=!empty($custom_params['is_required'])&& $viewData->pb_group_id==''?true:false;

$document=JFactory::getDocument();
$document->addStyleSheet( JURI::root(true).'/plugins/vmcustom/customfieldsforall/assets/css/customsforall_fe.css');
if($required)$document->addScript( JURI::root(true).'/plugins/vmcustom/customfieldsforall/assets/js/customfields_fe.js');

$options=$viewData->values;
$custom_params=$viewData->custom_params;
$wrapper_class='';


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
	if($custom_params['display_price']=='tooltip')JHTML::_('behavior.tooltip');//load the tooltips script
}


if(!empty($options)){
	$wrapper_id='cf4all_wrapper_'.$viewData->virtuemart_customfield_id.'_'.$viewData->pb_group_id;
	if($required)$wrapper_class=' cf4all_required';
	?>

<div class="cf4all_wrapper <?php echo $wrapper_class?>" id="<?php echo $wrapper_id?>">
<?php 
if($required):?>
<span class="cf4all_error_msg" id="cf4all_required_msg" style="display:none"><?php echo JText::_('PLG_CUSTOMSFORALL_REQUIRED_FIELD')?></span>
<?php 
endif;?>
<?php
$checked='';
if(!$required)$checked='checked';
foreach ($options as $v) {
	$label=JText::_($v->customsforall_value_name);
	$title='';
	$class='';
	//generate the price
	$price='';
	$custom_price=(float)$v->custom_price;
	
	if(!empty($viewData->calculate_price) && !empty($custom_price)){
		if($custom_price>=0)$op='+';
		else $op='';
		$price=$op.$currency->priceDisplay($calculator->calculateCustomPriceWithTax($custom_price));

		if($custom_params['display_price']=='tooltip'){ 
			$title='title="'.$price.'"';
			$class='hasTip';
		}
		else if($custom_params['display_price']=='label')$label.='&nbsp;'.$price;
	}
	$input_id='cf4all_input_'.$virtuemart_customfield_id.'_'.$v->customsforall_value_id.'_'.$viewData->pb_group_id.$input->get('bundled_products','');
	
	?>
	<div class="control-group">	
	<label class="radio inline <?php echo $class?>" for="<?php echo $input_id?>" <?php echo $title ?>>
	<input type="radio" value="<?php echo $v->id ?>" id="<?php echo $input_id?>" class="cf4all_radio" name="<?php echo $field_name?>[customsforall_option]" <?php echo $checked?> /> 
	<?php echo $label?>
	</label>
	</div>
	<?php
	$checked='';
}?>

</div>

<?php
}
