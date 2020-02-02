<?php

/**

 * @version		$Id: select.php 2013-08-02 11:57 sakis Terz $

 * @package		customfieldsforall

 * @copyright	Copyright (C)2013 breakdesigns.net . All rights reserved.

 * @license		GNU General Public License version 2 or later; see LICENSE.txt

 */



defined('_JEXEC') or die('Restricted access');

$custom_params=$viewData->custom_params;

$required=!empty($custom_params['is_required'])&& $viewData->pb_group_id==''?true:false;



$document=JFactory::getDocument();

$document->addStyleSheet('plugins/vmcustom/customfieldsforall/assets/css/customsforall_fe.css');

if($required)$document->addScript( JURI::root(true).'/plugins/vmcustom/customfieldsforall/assets/js/customfields_fe.js');

$class='';

$wrapper_class='';

$selects= array();

$options=$viewData->values;



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



<div class="cf4all_wrapper <?php echo $wrapper_class?>" id="<?php echo $wrapper_id?>">

<?php 

if($required):?>

<span class="cf4all_error_msg" style="display:none"><?php echo JText::_('PLG_CUSTOMSFORALL_REQUIRED_FIELD')?></span>

<?php 

$fist_option=array('value'=>0, 'text'=>JText::_('PLG_CUSTOMSFORALL_SELECT_AN_OPTION_FE'));

endif;

foreach ($options as $v) {

	$label=JText::_($v->customsforall_value_name);

	$price='';

	$custom_price=(float)$v->custom_price;

	if(!empty($viewData->calculate_price) && !empty($custom_price)){

		if($custom_price>=0)$op='+';

		else $op='';

		$price=$op.$currency->priceDisplay($calculator->calculateCustomPriceWithTax($custom_price));

		if($custom_params['display_price']=='label')$label.='&nbsp;('.$price.')';

	}

	$selects[] = array('value' => $v->id, 'text' =>$label );

}

if(!empty($fist_option))array_unshift($selects,$fist_option);

if(!empty($selects)){

	$html = JHTML::_('select.genericlist', $selects,$field_name.'[customsforall_option]', 'class="turnintodropdown"','value','text',$selects[0],false,true);

	echo $html;

}?>

</div>

<?php 

}
