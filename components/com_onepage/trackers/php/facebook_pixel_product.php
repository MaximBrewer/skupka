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
$this->isPureJavascript = true; 

?><script>
        var _productTrackingData = _productTrackingData || [];
<?php foreach ($this->products as $key=>$product) { 
	

	  
	  if (isset($product->pricesCalc) && (count($product->pricesCalc)>=1)) {
		  $prices = reset($product->pricesCalc); 
		  if (is_array($prices)) $prices = (object)$prices; 
		  
	  }
	  else 
	  if (isset($product->prices[0])) $prices = $product->prices[0]; 
	  
	  if (!isset($prices->priceWithoutTax)) {
		  $prices = new stdClass();
		  $prices->priceWithoutTax = 0; 
	  }
	  
	  
	  $db = JFactory::getDBO(); 
	  $q = 'select currency_code_2, currency_code_3, currency_name from #__virtuemart_currencies where virtuemart_currency_id = '.(int)$prices->product_currency.' limit 1'; 
	  $db->setQuery($q); 
	  $currency_info = $db->loadAssoc(); 
	  if (!empty($currency_info)) { 
	  ?>
	  
	  var productData = {
		  'productPID': "<?php echo $this->escapeDouble($product->pid); ?>",
		  'productID': "<?php echo $this->escapeDouble($product->virtuemart_product_id); ?>",
		  'productCategory': '<?php echo $this->escapeSingle($product->category_name ); ?>',
          'productCategoryID': '<?php echo $this->escapeSingle($product->virtuemart_category_id ); ?>',
		  'productPrice': <?php echo number_format((float)($prices->salesPrice), 2, '.', ''); ?>,
		  'productCurrency_currency_code_2': '<?php echo $this->escapeSingle($currency_info['currency_code_2']); ?>',
		  'productCurrency_currency_code_3': '<?php echo $this->escapeSingle($currency_info['currency_code_3']); ?>',
		  'productCurrency_currency_name': '<?php echo $this->escapeSingle($currency_info['currency_name']); ?>',
	  }; 
	  
	  _productTrackingData[productData.productID] = productData; 
    
		
	<?php }
	}	?>
	
	<?php
	if ((!empty($prices)) && (!empty($currency_info))) { ?>
	
	if (typeof fbq !== 'undefined') { 
fbq('track', 'ViewContent', {
content_ids: ['<?php echo $this->escapeSingle($this->product->pid); ?>'],
content_type: 'product',
value: <?php echo number_format((float)($prices->salesPrice), 2, '.', ''); ?>,
currency: '<?php echo $this->escapeSingle($currency_info['currency_code_3']); ?>'
});
	}

	<?php } ?>
        </script>