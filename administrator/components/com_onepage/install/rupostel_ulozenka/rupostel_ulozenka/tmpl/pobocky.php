<?php
defined('_JEXEC') or die('Restricted access');
$costDisplay = $viewData['single_price'];

if (!empty($costDisplay))
{
$currencyDisplay = CurrencyDisplay::getInstance();	
$costDisplay = $currencyDisplay->priceDisplay ($costDisplay);
}
else $costDisplay = '&nbsp;'; 
?>
<div class="sectiontableentry1_null" style="clear: both;">
 <div class="shipping_section_ulozenka">
	<input type="hidden" name="ulozenka_pobocka" id="ulozenka_pobocka" value="<?php echo $viewData['first_opt'] ?>" />
	<input type="radio" name="virtuemart_shipmentmethod_id" value="<?php echo $viewData['virtuemart_shipmentmethod_id']; ?>" id="shipment_id_<?php 
	echo $viewData['virtuemart_shipmentmethod_id'] ?>" multielement="ulozenka_pobocky" multielementgetjs="getUlozenka()" multielementgetphp="plgListMultipleOPC" ifsel="ifsel" />
 

 <label for="shipment_id_<?php echo $viewData['virtuemart_shipmentmethod_id'] ?>"><span class="vmshipment"><?php 
 
 echo $viewData['plg_name'];
 //echo $viewData['method']->shipment_name; 
 ?>
 <span id="ulozenka_cena" class="vmshipment_cost fee" style=""><?php echo $costDisplay; ?></span>
 </span></label>
 
 <?php
  
 ?>

</div>
<div class="shipping_section_combo">
	<div class="ulozenka_pobocky_div">{combobox}</div>
</div>
	
</div>

