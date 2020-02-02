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



?><div class="checkbox_products_wrapper"><?php
foreach ($products as $product_id => $data)
{
	if (empty($data['checked'])) $data['checked'] = ''; 
	
	?><div class=" " style="width: 100%; clear: both;" ><label for="checkbox_product<?php echo $product_id; ?>">
	 <input id="checkbox_product<?php echo $product_id; ?>" type="checkbox" value="<?php echo $product_id; ?>" <?php echo $data['onchange']; ?> <?php echo $data['checked']; ?> name="checkbox_products[]" />
	<span class="checkbo_product_name"><?php echo $data['product_name']; ?></span><?php if (!empty($data['price'])) { ?><span class="checkbox_product_price">&nbsp;( <?php echo $data['price']; ?> )</span><?php } ?>
		 <div style="clear: both;"><?php echo $data['product']->product_s_desc; ?></div>
		
		 
	  </label>
	  </div>
	  <?php
	
}
?></div>