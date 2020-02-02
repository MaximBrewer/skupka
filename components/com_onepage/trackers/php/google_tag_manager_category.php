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
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );


?>
<script>
if (typeof dataLayer == 'undefined')
dataLayer = [];


if (typeof dataLayer != 'undefined')
	
	 dataLayerImprCat = {}; 
	 dataLayerImprCat.event = 'categoryView'; 
     dataLayerImprCat.ecommerce = {};         
	 dataLayerImprCat.ecommerce.currencyCode =  '<?php echo $this->escapeSingle($this->currency['currency_code_3']); ?>'
      
	
	dataLayerImprCat.ecommerce.impressions = new Array(); 
	

  
<?php 

foreach ($this->products as $product) {
	$pid = $this->getPID($product->virtuemart_product_id, $product->product_sku); 
?> dataLayerImprCat.ecommerce.impressions.push({
       'name': '<?php echo $this->escapeSingle($product->product_name); ?>',       
       'id': '<?php echo $this->escapeSingle($pid); ?>',
       'price': <?php echo number_format($product->product_final_price, 2, '.', ''); ?>,
       'brand': '<?php echo $this->escapeSingle($product->mf_name); ?>',
       'category': '<?php echo $this->escapeSingle($product->category_name ); ?>',
       'variant': '<?php echo $this->escapeSingle($product->product_sku); ?>',
       'list': 'Impressions',
       'position': <?php echo $pos; ?>
     }); 
	 <?php
	$pos++; 
}
$this->isPureJavascript = true; 

?>
dataLayer.push(dataLayerImprCat); 

console.log('Datalayer Category'); 
console.log(dataLayer); 


</script>