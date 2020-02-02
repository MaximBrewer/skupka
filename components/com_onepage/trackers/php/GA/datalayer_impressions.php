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
// per specs from https://developers.google.com/tag-manager/enhanced-ecommerce#details
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!isset($product)) $product = $this->product; 
$pid = $product->pid;

 
?>

if (typeof dataLayer == 'undefined')
dataLayer = [];



	
// Measure a view of product details. This example assumes the detail view occurs on pageload,
// and also tracks a standard pageview of the details page.

dataLayer.push({
  'ecommerce': {
	'currencyCode': '<?php echo $this->escapeSingle($this->currency['currency_code_3']); ?>',
    'detail': {
      'actionField': {'list': 'OPC: Product Details Impression'},    // 'detail' actions have an optional list property.
      'products': [{
        'name': '<?php echo trim($this->escapeSingle($product->product_name)); ?>',         // Name or ID is required.
        'id': '<?php echo $this->escapeSingle($pid); ?>',
        'price': <?php echo number_format($product->prices['salesPrice'], 2, '.', ''); ?>,
        'brand': '<?php if (!empty($product->mf_name )) echo $this->escapeSingle($product->mf_name ); ?>',
        'category': '<?php echo $this->escapeSingle($product->category_name); ?>',
        'variant': '<?php echo $this->escapeSingle($product->product_sku); ?>'
       }]
     }
   }
});

    if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking GTM: dataLayer.ecommerce.detail product page view added.'); 
	  }

<?php
if (!empty($this->params->adwords_remarketing)) {
?>
 
 var google_tag_params = {
  'ecomm_pagetype': 'product', 
  <?php /* stAn - commented to match the feed if (!empty($product->category_name)) { ?>
  'ecomm_category': '<?php echo $this->escapeSingle($product->category_name); ?>',  
  <?php } */  ?>
  'ecomm_prodid': '<?php echo $this->escapeSingle($pid); ?>',
  'ecomm_pname': '<?php echo $this->escapeSingle($product->product_name); ?>',
  'ecomm_pvalue': <?php echo number_format($product->prices['salesPrice'], 2, '.', ''); ?>,
  'ecomm_totalvalue': <?php echo number_format($product->prices['salesPrice'], 2, '.', ''); ?>,
  'dynx_itemid': '<?php echo $this->escapeSingle($pid); ?>',
  'dynx_itemid2': '<?php echo $this->escapeSingle($pid); ?>',
  'dynx_pagetype': 'offerdetail',
  'dynx_totalvalue': <?php echo number_format($product->prices['salesPrice'], 2, '.', ''); ?>,
};

if (typeof dataLayer != 'undefined')
dataLayer.push({
    'event': '<?php echo $this->escapeSingle($this->params->tag_event); ?>',
    'google_tag_params': window.google_tag_params
   });
   
   
    if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking GTM: Fired Adwords remarketing tag for product pagetype: product, event: <?php echo $this->escapeSingle($this->params->tag_event); ?> and product name <?php echo $this->escapeSingle($product->product_name); ?> and ID <?php echo $this->escapeSingle($pid); ?>'); 
	  }
<?php
}
?>   

