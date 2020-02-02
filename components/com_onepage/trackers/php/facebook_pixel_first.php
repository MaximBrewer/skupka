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

if (!empty($this->params->pixel_code)) {

	$email = JFactory::getUser()->get('email', ''); 
	$this->params->pixel_code = str_replace('insert_email_variable', $email, $this->params->pixel_code); 
	echo $this->params->pixel_code; 

	$this->isPureJavascript = true; 


	$root = Juri::root(); 
	if (substr($root, -1) !== '/') $root .= '/'; 


	
	
	?>
	<script>

	var productQueryUrl = '<?php echo $root; ?>index.php?option=com_onepage&view=xmlexport&task=getproduct&format=opchtml&pidformat=<?php echo $this->params->pidformat; ?>&pid_prefix=<?php echo urlencode($this->params->pid_prefix); ?>&pid_suffix=<?php echo urlencode($this->params->pid_suffix); ?><?php 
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php');
	$lang = OPCloader::getLangCode(); 
	if (!empty($lang)) echo '&lang='.$lang; 
	?>'; 

	/* <![CDATA[ */
	if (typeof jQuery !== 'undefined') {
		jQuery(document).ready( function() {
			
			
			var cartForms = jQuery("form.product"); 
			if (cartForms.length == 0) return; 
			cartForms.each( function() {
				var productform = jQuery(this); 
				
				
				
				var jelcart = productform.find('button[name="addtocart"], input[name="addtocart"], a[name="addtocart"]');
				if (jelcart.length > 0) {
					
		
		if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null)) 
							{
								console.log('Add to cart FB Pixel Tracking Attached'); 
							}
		
		jelcart.click(function() {
			
			if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null)) 
							{
								console.log('Add to cart FB Pixel Tracking Click Detected'); 
							}
			
			
			if (fbq === 'undefined') return; 
			
			
			
					
					var virtuemart_product_id = productform.find('input[name="virtuemart_product_id[]"]').val();
					if ((typeof virtuemart_product_id !== 'undefined') && (virtuemart_product_id !== 'undefined'))
					{
						if ((typeof _productTrackingData !== 'undefined') && (typeof _productTrackingData[virtuemart_product_id] !== 'undefined')) 
						{
							var productData = _productTrackingData[virtuemart_product_id]; 
							var fbData = {
							'content_ids': [productData.productPID],
							'content_type': 'product',
							'value': productData.productPrice,
							'currency': productData.productCurrency_currency_code_3
							};
					
							fbq('track', 'AddToCart', fbData); 
							if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null)) 
							{
								console.log('Add to cart FB Pixel Tracking', fbData); 
							}
						}
						else {
							
							
							
						var myUrl = productQueryUrl+'&virtuemart_product_id='+virtuemart_product_id; 
						
						
						
						
						jQuery.ajax({ 
						type: 'GET',
						cache: false, 
						dataType: 'json',
						timeout: '10000', 
						url: myUrl, 
						data: []
						}).done( function (data, testStatus ) {
							
							
							
						if ((typeof data !== 'undefined') && (data !== null)) {
							if (typeof data.productCurrency_currency_code_3 !== 'undefined') {
								var fbData = {
							'content_ids': [data.productPID],
							'content_type': 'product',
							'value': data.productPrice,
							'currency': data.productCurrency_currency_code_3
							};
					
							fbq('track', 'AddToCart', fbData); 
							if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null)) {
								console.log('Add to cart FB Pixel Tracking', fbData); 
							}
								
								
								
							}
						}
						}).fail( function(err) {
							console.log(err); 
						}); 

						
					}
					}
			
			
		});
		}
	});
})
}

/* ]]> */
</script>

	

</script>
<script> if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	{
		console.log('OPC Tracking: Facebook Pixel tracking Loaded'); 
	}
	</script>
<?php
}	  