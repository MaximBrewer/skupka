<?php

/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

defined('_JEXEC') or die();
	$class='vmcustom-textinput';
$product = $viewData['product'];
$params = $viewData['group'];
$values = $viewData['values']; 

$name = 'customProductData['.$product->virtuemart_product_id.']['.$params->virtuemart_custom_id.']['.$params->virtuemart_customfield_id .'][comment]';
?>
<select name="<?php echo $name; ?>" onchange="return updatePrice(this, <?php echo (int)$product->virtuemart_product_id; ?>);" class="vm-chzn-select">
<?php

foreach ($values as $v=>$row)
{ 
	?><option value="<?php echo $row['id']; ?>"><?php echo htmlentities($row['attrib']); ?></option>
	<?php
}
?>

 </select> 
	
<script>
//updatePrice(<?php echo $name; ?>, 	<?php echo (int)$product->virtuemart_product_id; ?>); 
</script>
<?php
	