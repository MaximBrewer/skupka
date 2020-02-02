<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
* This is the default Basket Template. Modify as you like.
*
* @version $Id: basket.html.php 
* @package OPC
* @subpackage templates
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/


$default = new stdClass(); 
$config = OPCconfig::getValue('theme_config', $selected_template, 0, $default, false); 

$product_subtotal = false; 

if (!empty($config) && (isset($config->product_subtotal)))
{
$product_subtotal = $config->product_subtotal; 

}


?>


<div id="basket_container">
<div class="cart_title">
<h1><?php echo OPCLang::_('COM_VIRTUEMART_CART_TITLE'); ?></h1>
</div>

<div class="continue_left">
<?php
if (empty($no_continue_link) && (!empty($continue_link)) && ($continue_link != '//')) { 
$cl = true;  ?>
<div class="continue_shopping2"><a href="<?php echo $continue_link ?>" class="continue_link2 opcbutton myBlueBackground myGrayColor"><?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING') ?></a></div>
<?php 
} 
?>
</div><!-- continue_left -->
<div class="update_all"><a href="#" onclick="return updateall();" class="updateAll opcbutton myBlueBackground myGrayColor"><?php echo JText::_('COM_VIRTUEMART_CART_ACTION'); ?></a></div>

	<div id="basket_cart">
		<div id="cart_header" class="vmsectiontableheader">
			<div class="op_col1 opc_col1_header">&nbsp; </div>
			<div class="op_col2 unit_column">&nbsp;<?php //echo OPCLang::_('COM_VIRTUEMART_CART_PRICE');
			?></div>
			<div class="op_col6">&nbsp;<?php 
			echo OPCLang::_('COM_VIRTUEMART_CART_QUANTITY');
			//echo OPCLang::_('COM_VIRTUEMART_CART_QUANTITY').' / '.OPCLang::_('COM_VIRTUEMART_CART_ACTION') ?></div>
			<div class="op_col7"><?php echo OPCLang::_('COM_VIRTUEMART_CART_PRICE') ?></div>
			<div class="op_col8">&nbsp;</div>
		</div>
		<div id="cart_products">
			<?php 
			$c = 0;
			foreach( $product_rows as $product ) {
			$c++; 
			if ($c&1) $i = '2'; else $i='1'; 
			$product['row_color'] = 'sectiontableentry'.$i; 
			 ?>
			<div  class="cart_prod <?php echo $product['row_color'] ?>">
				<div class="op_col1"><?php echo $this->op_show_image($product['product_full_image'], '', 40, 40, 'product'); ?></div>
				<div class="op_col2"><?php echo $product['product_name'].'<br />'.$product['product_attributes']; ?></div>
				
				<div class="op_col6">
					<div class="update_wrap">
							<?php echo $product['update_form']; ?>
							
					</div>
				</div>
				<div class="op_col7"><div class="price_wrap"><?php if (empty($product_subtotal)) { echo $product['product_price']; //echo $product['subtotal']; 
				}
				else
				{
					echo $product['product_subtotal']; 
				}
				?></div></div>
				<div class="op_col8"><div class="delete_wrap"><?php echo $product['delete_form']; ?></div></div>
			</div>
			<?php } ?>
		</div>
	</div><!-- end id basket_cart -->
		
	</div><!-- end basket_container -->

<!-- IMPORTNAT, THIS DIV IS CLOSED IN UNDERBASKET.PHP -->