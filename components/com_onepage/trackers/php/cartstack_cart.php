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


$total = 0; 
foreach ($this->products as $key=>$product) {
  
  $total += ((float)$product->prices['salesPrice'] * (float)$product->product_quantity); 
  
}
?>

<script src="https://api.cartstack.com/js/cs.js" type="text/javascript"></script>
<script type="text/javascript">
/* <![CDATA[ */	
      var _cartstack = _cartstack || [];
          _cartstack.push(['setSiteID', '<?php echo $this->params->site_id; ?>']); /* required */
          _cartstack.push(['setAPI', 'tracking']); /* required */
          _cartstack.push(['setCartTotal', '<?php echo $total; ?>']); /* optional */
		  <?php
		  foreach ($this->products as $key=>$product) 
	{ 
	$pid = $product->pid; 
	
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
//$img_info = OPCimage::op_image_tag($product->thumb_url, '', 1, 'product', 180, 0, true); 
$iurl = ''; $width = 0; 
if (!empty($product->virtuemart_media_id)) {
$img_id = reset($product->virtuemart_media_id); 
if (!empty($img_id)) {

$resize = true; 
$width = 180; $h=0; $root = Juri::root(); 
$iurl = OPCimage::getCreateImageUrlAndSizeById($img_id, $width, $h, $root); 
if (!empty($iurl)) { 

}
}
}

?>
try { 
_cartstack.push(['setCartItem', {
     'quantity':'<?php echo (int)$product->quantity; ?>',
     'productID':"<?php echo $this->escapeDouble($pid); ?>",
     'productName':"<?php echo $this->escapeDouble($product->product_name); ?>",
     'productDescription':"<?php echo $this->escapeDouble($product->product_s_desc); ?>",
     'productURL':'<?php echo $product->vm1_link; ?>', <?php if (!empty($iurl)) { ?>
     'productImageURL':'<?php echo $iurl; ?>',   <?php } if (!empty($width)) { ?>
	 'imageWidth':'<?php echo $width; ?>',   <?php }  ?>
     'productPrice':'<?php echo number_format($product->product_final_price, 2, '.', ''); ?>',
     
    }]);
}
catch (e) {
	;
}



<?php

	}
		  ?>
	//callAfterAjax
	if (typeof addOpcTriggerer != 'undefined') {
	  if (typeof cartStackOnChange != 'undefined')
	  addOpcTriggerer('callAfterAjax', 'cartStackOnChange(\'<?php echo $this->params->site_id; ?>\')'); 
	}
	
	
	
		  
	if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: cartstack cart event'); 
	  }
	  
	 if (typeof jQuery != 'undefined') {
	    jQuery(document).ready( function() {
		   var g = jQuery('input#guest_email');
		   if (g.length > 0) {
		     g.blur( function() {
			    updateEmailCartStack('<?php echo $this->params->site_id; ?>', this.value); 
			 }); 
		   }
		}); 
	 }
	 if (typeof jQuery != 'undefined') {
	    jQuery(document).ready( function() {
		   var g = jQuery('input#email_field');
		   if (g.length > 0) {
		     g.blur( function() {
			    updateEmailCartStack('<?php echo $this->params->site_id; ?>', this.value); 
			 }); 
		   }
		}); 
	 }
	 //email_field
		  
/* ]]> */		  
</script>
