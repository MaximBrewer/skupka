<?php

	// TradeTracker VirtueMart 2.0.12 and higher conversion file v1.1
	$tt_db = JFactory::getDBO();

	// If for some reason the cart doesn't contain the last order ID, try and get it.
	$virtuemart_order_id = $this->order['details']['BT']->virtuemart_order_id; 
	

	

	$tt_order_items = $this->order['items']; 
	
	if (!empty($this->params->allow_visitor_data))
	{
		$email = $this->order['details']['BT']->email; 
	}
	else
    $email = ''; 
	

	// Now loop through the products to display a pixel per product from the basket.
	$tt_scriptData = '';
	foreach ($tt_order_items as $index => $tt_order_item)
	{
		$tt_order_amount = ((float) $tt_order_item->product_final_price - (float) $tt_order_item->product_tax) * $tt_order_item->product_quantity;
		$tt_order_descrMerchant = 'Internal ID: ' . $virtuemart_order_id . ' | Product name: ' . $tt_order_item->order_item_name . ' | SKU: ' . $tt_order_item->order_item_sku;

		$tt_scriptData .= "
		<script type=\"text/javascript\">
			var ttConversionOptions = ttConversionOptions || [];
			ttConversionOptions.push({
				type: 'sales',
				campaignID: '" . htmlentities($tt_campaignID, ENT_QUOTES) . "',
				productID: '" . htmlentities($tt_productID, ENT_QUOTES) . "',
				transactionID: '" . htmlentities($this->order['details']['BT']->order_number, ENT_QUOTES) . "',
				transactionAmount: '" . htmlentities($tt_order_amount, ENT_QUOTES) . "',
				quantity: '" . htmlentities($tt_order_item->product_quantity, ENT_QUOTES) . "',
				email: '".htmlentities($email, ENT_QUOTES)."',
				descrMerchant: '" . htmlentities($tt_order_descrMerchant, ENT_QUOTES) . "',
				descrAffiliate: '',
				currency: '".htmlentities($this->order['details']['BT']->currency_code_3, ENT_QUOTES)."',
				trackingGroupID: '" . htmlentities($tt_trackingGroupID, ENT_QUOTES) . "'
			});
		</script>

		<noscript>
			<img src=\"//ts.tradetracker.net/?" . (empty($tt_trackingGroupID) ? 'cid=' . rawurlencode($tt_campaignID) : 'tgi=' . rawurlencode($tt_trackingGroupID)) . "&amp;pid=" . rawurlencode($tt_productID) . "&amp;tid=" . rawurlencode($this->order['details']['BT']->order_number) . "&amp;tam=" . rawurlencode($tt_order_amount) . "&amp;data=&amp;qty=1&amp;eml=&amp;descrMerchant=" . rawurlencode($tt_order_descrMerchant) . "&amp;descrAffiliate=&amp;event=sales\" alt=\"\" style=\"width: 1px; height: 2px; border: 0px;\" />
		</noscript>" . PHP_EOL;
	}

	$tt_scriptData .= "
	<script type=\"text/javascript\"><!-- // --><![CDATA[
		(function(ttConversionOptions) {
			var campaignID = 'campaignID' in ttConversionOptions ? ttConversionOptions.campaignID : ('length' in ttConversionOptions && ttConversionOptions.length ? ttConversionOptions[0].campaignID : null);
			var tt = document.createElement('script'); tt.type = 'text/javascript'; tt.async = true; tt.src = '//tm.tradetracker.net/conversion?s=' + encodeURIComponent(campaignID) + '&t=m';
			var s = document.getElementsByTagName('script'); s = s[s.length - 1]; s.parentNode.insertBefore(tt, s);
		})(ttConversionOptions);
	// ]]></script>" . PHP_EOL;

	// Return the combined transaction data.
	return $tt_scriptData;