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

if (empty($GLOBALS['pos'])) {
  $GLOBALS['pos'] = 1; 
}

	
$products_tags = array(); 

$total = 0; 
foreach ($this->products as $key=>$product) { 

$pid = $product->pid;  
$products_tags[] = "'".$this->escapeSingle($pid)."'"; 
$total += ($product->product_final_price * $product->product_quantity); 


}





?>
<script>
//<![CDATA[

if (typeof dataLayer == 'undefined')
dataLayer = [];



	
if (typeof dataLayerImpr == 'undefined') {
	 dataLayerImpr = {}; 
	 
     dataLayerImpr.ecommerce = {};         
	 dataLayerImpr.ecommerce.currencyCode =  '<?php echo $this->escapeSingle($this->currency['currency_code_3']); ?>'
      
	
	dataLayerImpr.ecommerce.impressions = new Array(); 
}
  
<?php 

if (empty($this->listName)) $this->listName = 'Impressions'; 

 
if (empty($GLOBALS['done_pids'])) $GLOBALS['done_pids'] = array(); 
if (!empty($this->products)) {
foreach ($this->products as $product) {
	$pid = $this->getPID($product->virtuemart_product_id, $product->product_sku); 
	if (!empty($GLOBALS['done_pids'][$pid])) continue; 
	$GLOBALS['done_pids'][$pid] = $pid; 
	
?> dataLayerImpr.ecommerce.impressions.push({
       'name': '<?php echo trim($this->escapeSingle($product->product_name)); ?>',       
       'id': '<?php echo $this->escapeSingle($pid); ?>',
       'price': <?php echo number_format($product->product_final_price, 2, '.', ''); ?>,
       'brand': '<?php echo $this->escapeSingle($product->mf_name); ?>',
       'category': '<?php echo $this->escapeSingle($product->category_name ); ?>',
       'variant': '<?php echo $this->escapeSingle($product->product_sku); ?>',
       'list': '<?php echo $this->escapeSingle($this->listName); ?>',
       'position': <?php echo $GLOBALS['pos']; ?>
	   
     }); 
	 <?php
	$GLOBALS['pos']++; 
}
$GLOBALS['pos']--; 
}
$this->isPureJavascript = true; 

?>


//]]>
</script>





<script>
//<![CDATA[ 






    if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
		  
	     console.log('OPC Tracking GTM: dataLayer.ecommerce.impressions: product impression event, triggered for <?php echo $GLOBALS['pos']; ?> products'); 
	  }
	
//]]>	
</script>

