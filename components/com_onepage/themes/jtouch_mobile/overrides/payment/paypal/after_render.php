<?php 
// PLEASE DO NOT USE $return !

$desc = str_replace("\r\r\n", '<br />', $plugin->payment_name.'<br />'.$plugin->payment_desc); 
$desc = str_replace("\r\n", '<br />', $desc); 
$desc = str_replace("\n", '<br />', $desc); 

?><input type="radio" name="virtuemart_paymentmethod_id" id="<?php echo 'payment_id_' . $plugin->virtuemart_paymentmethod_id; ?>" value="<?php echo $plugin->virtuemart_paymentmethod_id; ?>" >
<label class="hasToolTip" data-uk-html="true" data-uk-placement="bottom" data-uk-tooltip="{pos:'bottom-left'}" title="<?php  echo htmlentities($desc);  ?>" for="<?php echo 'payment_id_' . $plugin->virtuemart_paymentmethod_id; ?>" >
 <div class="payment">
 <div class="payment_name"><?php echo $plugin->payment_name; ?></div>
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
 <?php 
 
 
 ?>
 

 </label>
 <?php if (!empty($desc)) { ?>
  <div class="vmpayment_description_<?php echo $plugin->virtuemart_paymentmethod_id; ?>"></div>
 <?php } ?>
 <script type="text/javascript">
 //  jQuery('.hasTooltip').tooltip();
  /*
   window.addEvent('domready', function(){ 
       var JTooltips = new Tips($$('.hasToolTip'), 
       { maxTitleChars: 50, fixed: false}); 
    });
  */
  //console.log(jQuery('body')); 
  
  
  
  // init code
  /*
    jQuery('#payment_html').on("mouseenter.tooltip.uikit focus.tooltip.uikit", "[data-uk-tooltip]", function(e) {
        var ele = jQuery(this);

        if (!ele.data("tooltip")) {
            var obj = jQuery.UIkit.tooltip(ele, jQuery.UIkit.Utils.options(ele.attr("data-uk-tooltip")));
            ele.trigger("mouseenter");
        }
    });
  */
 </script>
  
