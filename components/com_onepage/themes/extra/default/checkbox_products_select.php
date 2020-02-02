<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*
*  DEV NOTES: use $data to get proper values
*  it's very important that you use name="checkbox_products[]" so opc can check status of all checkboxes upon each opc call 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

$css = '
span.checkbox_product_price div {
 display: inline-block; 
}

#vmMainPageOPC .checkbox_product_price .opc_price_general, #vmMainPageOPC .checkbo_product_name, #vmMainPageOPC .checkbox_product_price, #vmMainPageOPC .checkbox_product_price .opc_price_general .opc_price_general {
 display: inline-block; 
 float: none; 
 clear: none; 
 
}
'; 

JFactory::getDocument()->addStyleDeclaration($css); 


$desc = ''; 
?><div class="checkbox_products_wrapper" style="padding-bottom: 10px;">
<select name="checkbox_products" <?php echo $onchange; ?>>
<?php
if (!empty($checkbox_products_first)) {
	?><option value=""><?php echo htmlentities(JText::_($checkbox_products_first)); ?></option><?php
}
 foreach ($products as $product_id => $data) { ?>
 <option <?php if (!empty($data['checked'])) echo ' selected="selected" '; ?> id="checkbox_product<?php echo $product_id; ?>"  value="<?php echo $product_id; ?>"><?php 
 $optname = $data['product_name']; 
 
 if (!empty($data['price'])) {  $optname .= ' ('.$data['price'].')';  } 
 echo htmlentities($optname); 
 
 ?></option>
 <?php
 $desc .= '<div style="clear: both; '; 
 if (empty($data['checked'])) $desc .= ' display: none; '; 
 $desc .= '" '; 
 $desc .= ' id="checkbox_product_desc_'.$product_id.'">'.$data['product']->product_s_desc.'</div>'; 
 ?>
<?php } ?>
</select>


<?php
echo $desc; 

?></div>