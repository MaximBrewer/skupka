<?php 
// PLEASE DO NOT USE $return !

?><input type="radio" name="virtuemart_paymentmethod_id" id="<?php echo 'payment_id_' . $plugin->virtuemart_paymentmethod_id; ?>" value="<?php echo $plugin->virtuemart_paymentmethod_id; ?>" >
<label for="<?php echo 'payment_id_' . $plugin->virtuemart_paymentmethod_id; ?>">
 <div class="payment">
 <?php
   
   if (!empty($logo_list))
   foreach ($logo_list as $logo) {
		if (empty($logo)) continue; 
		
		
				$alt_text = substr ($logo, 0, strpos ($logo, '.'));
				?><span class="vmCartPaymentLogo" ><img align="middle" src="<?php echo $url . $logo; ?>"  alt="<?php echo $alt_text; ?>" />
				</span> 
				
				<?php
			}
 ?>
 <div class="payment_name"><?php echo $plugin->payment_name; ?></div>
 <div class="payment_description"><?php echo $plugin->payment_desc; ?></div>
<?php
			if (!empty($pluginSalesPrice)) {
			if (!empty($currency))
			$costDisplay = $currency->priceDisplay ($pluginSalesPrice); 
			
			?>
			<div class="payment_cost"> ( <?php echo JText::_ ('COM_VIRTUEMART_PLUGIN_COST_DISPLAY') . $costDisplay; ?>)</div>

			
			<?php 		
			}
			?> 
 
 </div>
 </label>
