<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

defined('_JEXEC') or ('Restrict Access');

/* @var $this VMInvoiceViewOrder */

JFilterOutput::objectHTMLSafe($this->orderData);

$document = JFactory::getDocument();
/* @var $document JDocumentHTML */
//$document->addScriptDeclaration('window.addEvent("domready",function(){$("whisperDiv").style.visibility="hidden";});');

if ($this->orderajax) {
	array_unshift($this->orderStatus, JHTML::_('select.option', '', JText::_('COM_NETBASEVM_EXTEND_SELECT'), 'id', 'name')); 
	array_unshift($this->vendors, JHTML::_('select.option', '', JText::_('COM_NETBASEVM_EXTEND_SELECT'), 'id', 'name'));
}

if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
	array_unshift($this->taxRates,JHTML::_('select.option', -1, JText::_('COM_NETBASEVM_EXTEND_OTHER'), 'value', 'name'));
	
//select for tax rules
$rulesTypes = array(
	'DBTaxRulesBill' => 'COM_NETBASEVM_EXTEND_MODIFIER_BEFORE_BILL',
	'taxRulesBill' => 'COM_NETBASEVM_EXTEND_TAX_BEFORE_BILL',
	'DATaxRulesBill' => 'COM_NETBASEVM_EXTEND_MODIFIER_AFTER_BILL', 
	'payment' => 'COM_NETBASEVM_EXTEND_PAYMENT', 
	'shipment' => 'COM_NETBASEVM_EXTEND_SHIPPING'
);

$this->rulesType = array();
foreach ($rulesTypes as $key => $name)
	$this->rulesType[] = JHTML::_('select.option', $key, JText::_($name), 'value', 'name');

//select for calc kinds
$kinds = array(
	'+%' => '+%', 
	'-%' => '-%', 
	'+' => '+', 
	'-' => '-'
);

$this->calcKinds = array();
foreach ($kinds as $key => $name)
	$this->calcKinds[] = JHTML::_('select.option', $key, $name, 'value', 'name');

/* disabled, only simple amount select
//select for calc kinds
$kinds = array(
	'+' => '+', 
	'-' => '-', 
	'+%' => '+%', 
	'-%' => '-%', 
);

$this->calcKinds = array();
foreach ($kinds as $key => $name)
	$this->calcKinds[] = JHTML::_('select.option', $key, $name, 'value', 'name');
*/
?>

<script type="text/javascript">

function show_all_attributes(){
	var trs = $$('tr[id^=attr_]');
	var opened = 0;
	
	trs.each(function(el){ //if there are unopened attrs, open them all
		if (el.style.display=='none'){
			show_attributes(el.id.substring(5));
			opened++;}
	});

	if (opened==0){ //else close all
		trs.each(function(el){
			show_attributes(el.id.substring(5));
		});
	}
}

function show_attributes(i) {
	var row = document.getElementById('attr_'+i);
	
	if (row.style.display == 'none') {
		row.style.display = 'table-row';
		document.getElementById('show_attr_'+i).getElementsByTagName('SPAN')[0].innerHTML='<?php echo JText::_('COM_NETBASEVM_EXTEND_HIDE'); ?>';
	}
	else {
		row.style.display = 'none';
		if (document.getElementById('attrs_'+i).value.trim()==''){
			document.getElementById('show_attr_'+i).getElementsByTagName('SPAN')[0].innerHTML='<?php echo JText::_('COM_NETBASEVM_EXTEND_ADD'); ?>';
			document.getElementById('show_attr_'+i).className = 'addProduct';}
		else{
			document.getElementById('show_attr_'+i).getElementsByTagName('SPAN')[0].innerHTML='<?php echo JText::_('COM_NETBASEVM_EXTEND_EDIT'); ?>';
			document.getElementById('show_attr_'+i).className = 'editProduct';}
	}	
}
</script>
<?php if (! $this->orderajax) { ?><div id="orderInfo"><?php	} ?>
<br/>
<fieldset>
    <legend class="addProduct" style="border:none !important; color:#315b8c;"><?php echo JText::_('COM_NETBASEVM_EXTEND_ADD_PRODUCT'); ?></legend>
	<table class="adminlist" cellspacing="1" style="width:40% !important;">
		<tbody>
			<tr>
				<th><?php echo JText::_('COM_NETBASEVM_EXTEND_NAME'); ?></th> 
				<th width="1%"><?php echo JText::_('COM_NETBASEVM_EXTEND_ACTION'); ?></th>
			</tr>
			<tr>
				<td>
					<input type="hidden" id="newproduct_id" name="newproduct_id" value="<?php echo $this->newproduct_id?>" />
					
					<div id="newproduct_search" style="display:<?php echo $this->newproduct_id ? 'none' : 'block' ?>">
					<input type="text" id="newproduct" name="newproduct" class="fullWidth" autocomplete="off" onkeyup="generateWhisper(this, event,'<?php echo JURI::base(); ?>');" onkeydown="moveWhisper(this, event);" />
					<br />
					<div id="newproductwhisper"></div>
					</div>
					
					<?php if ($this->newproduct_id) { /* product selected before: now select price */?>
					<div id="newproduct_select"><?php echo $this->newproduct_name ?>
					<a href="javascript:void(0)" onclick="$('newproduct_search').style.display='block';$('newproduct_id').value='';$('newproduct_select').destroy();"
					><?php echo JText::_('COM_NETBASEVM_EXTEND_DISCARD');?></a>
					
					<span style="float:right">
					<?php echo JText::_('COM_NETBASEVM_EXTEND_PRICE'); ?>: 
					<?php echo JHTML::_('select.genericlist', $this->productPrices, 'newproduct_price', null, 'value', 'text', $this->productPriceSelected);  ?>
					</span>
					</div>
					<?php } ?>
	            </td>
	          	<td align="center">
	              	<a href="javascript:showOrderData('<?php echo addslashes(JURI::base()); ?>',true,false,false)" title="<?php echo JText::_('COM_NETBASEVM_EXTEND_ADD'); ?>" class="addProduct"><span class="unseen"><?php echo JText::_('COM_NETBASEVM_EXTEND_ADD'); ?></span></a>
	          	</td>
			</tr>
		</tbody>
	</table>
</fieldset class="order-items">
<div style="clear:both"></div>
<br/>
<fieldset>
<legend class="basket" style="border:none !important; color:#315b8c;"><?php echo JText::_('COM_NETBASEVM_EXTEND_ORDER_ITEMS'); ?></legend>
<table class="adminlist admintable add-new-order" cellspacing="1">
	<thead>
		<tr> 
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_ACTIONS'); ?></th>
			<th width="6%" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_PRODUCT_SKU'); ?></th>
			<th><?php echo JText::_('COM_NETBASEVM_EXTEND_NAME'); ?></th>
			<th width="1%" nowrap="nowrap" style="cursor:pointer" onclick="show_all_attributes();"><?php echo JText::_('COM_NETBASEVM_EXTEND_ATTRIBUTES'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_ORDER_STATUS'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_VENDOR'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_QUANTITY'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_PRICE_NET'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_TAX'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_PRICE_GROSS'); ?></th>
			<?php if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2) {?>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_DISCOUNT'); ?></th>
			<?php }?>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_SUBTOTAL'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_TOTAL'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		    $orderSubtotal = 0;
			$taxSubtotal = 0;	
		 	$count = count($this->productsInfo);
			//print_r($this->productsInfo);			
			for ($i = 0; $i < $count; $i++) {
				$product = $this->productsInfo[$i];
				/* @var $product TableVmOrderItem */
				JFilterOutput::objectHTMLSafe($product);

				//cleanup parameters
				$params = explode ("\n", preg_replace('/<\s*br\s*\/?\s*>/Uis', "\n", $product->product_attribute));
				foreach ($params as $key => $value)
					if (($value = JString::trim($value))) $params[$key] = $value;
					else unset($params[$key]);
					
				$attributes = trim(implode("\n", $params));
				$notFromVM = empty($product->product_id) ? ' class="hasTip" title="'.JText::_('COM_NETBASEVM_EXTEND_THIS_PRODUCT_IS_NOT_FROM_VIRTUEMART').'"' : '';
				?>
					
				<tr valign="top" class="row<?php echo (@$j++ % 2); ?>"<?php if ($i == $count - 1) { ?> id="lastProduct"<?php } ?>>
              		<td><a href="javascript:void(0)" onclick="deleteProduct(this)" title="" class="deleteProduct"><?php echo JText::_('COM_NETBASEVM_EXTEND_DELETE'); ?></a></td>
        			<td<?php echo $notFromVM?>><input type="text" name="order_item_sku[]" value="<?php echo $product->order_item_sku; ?>" class="fullWidth" /></td>
        			<td>
        				<input type="text" name="order_item_name[]" value="<?php echo $product->order_item_name; ?>" class="fullWidth" /> 
						<input type="hidden" name="product_id[]" value="<?php echo $product->product_id; ?>" />
						<input type="hidden" name="order_item_id[]" value="<?php echo $product->order_item_id; ?>" />
					</td>
					<td align="center" valign="middle"><a id="show_attr_<?php echo $i; ?>" href="javascript:void(0)" onclick="show_attributes(<?php echo $i; ?>)" class="<?php if (trim($attributes," \n")=="") echo 'addProduct'; else echo 'editProduct'; ?>" title="<?php if (trim($attributes," \n")=="") echo JText::_('COM_NETBASEVM_EXTEND_ADD'); else echo JText::_('COM_NETBASEVM_EXTEND_EDIT'); ?>">
					<span class="unseen"><?php if (trim($attributes," \n")=="") echo JText::_('COM_NETBASEVM_EXTEND_ADD'); else echo JText::_('COM_NETBASEVM_EXTEND_EDIT'); ?></span></a></td>
  					<td><?php echo JHTML::_('select.genericlist', $this->orderStatus, 'order_status[]', null, 'id', 'name', $product->order_status,'status_'.$i); ?></td>
  					<td><?php echo JHTML::_('select.genericlist', $this->vendors, 'vendor_id[]', null, 'id', 'name', $product->vendor_id,'vendor_id_'.$i); ?></td>
  					<td><input type="text" class="fullWidth" name="product_quantity[]" value="<?php echo $product->product_quantity; ?>" /></td>					
					<td nowrap="nowrap"><input type="text" class="price" name="product_item_price[]" value="<?php echo $product->product_item_price*1; ?>" /></td>				
					<td nowrap="nowrap">
					<?php echo JHTML::_('select.genericlist', $this->taxRates, 'tax_rate[]', null, 'value', 'name', $product->tax_rate,'tax_rate_'.$i);  ?>
					<input style="float: left; clear: both; display: block;" type="text" class="price" name="product_tax[]" value="<?php echo $product->product_tax*1; ?>" id="product_tax_<?php echo $i ?>"/>
					</td>
					<td style="text-align: center;">
						<input type="hidden" name="product_price_with_tax[]" value="<?php echo $product->product_price_with_tax; ?>" />
						<?php echo round($product->product_price_with_tax, 2); ?>
					</td>
					<?php if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2) {?>
					<td nowrap="nowrap">
						<input type="text" name="product_price_discount[]" value="<?php echo $product->product_price_discount; ?>" class="price" />
					</td>
					<?php }?>
					<td style="text-align: center;" nowrap="nowrap" align="left"><?php echo round($product->subtotal, $this->nbDecimal); ?></td>
					
					<td style="text-align: center;" nowrap="nowrap" align="left"><?php echo round($product->total, $this->nbDecimal); ?></td>
				</tr>
				<tr id="attr_<?php echo $i; ?>" style="display:none">
				<th valign="top"><?php echo JText::_('COM_NETBASEVM_EXTEND_PRODUCT_ATTRIBUTES'); ?>:</th>
					<td colspan="<?php echo COM_NETBASEVM_EXTEND_ORDERS_ISVM2 ? 14 : 12?>">

					<textarea rows="3" cols="30" class="fullWidth" name="product_attribute[]" id="attrs_<?php echo $i; ?>"><?php echo $attributes; ?></textarea>
					</td>
				</tr>
		<?php 
			} 
		?>	  
	
	</tbody>
</table>
<br/>
<table cellpadding="0" cellspacing="0" width="100%">
	<!-- Subtotal row -->		
	<tr>
    	<td width="65%" align="right" class="key"><?php echo JText::_('COM_NETBASEVM_EXTEND_SUBTOTAL'); ?>:</td>
    	<?php if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){?>
	    	<td width="15%" align="center" class="hasTip" title="<?php echo JText::_('COM_NETBASEVM_EXTEND_TAX_TOTAL'); ?>">
	    		<?php echo round($this->orderData->order_tax, $this->nbDecimal)?>
	    		<input type="hidden" name="order_tax" value="<?php echo $this->orderData->order_tax*1?>">
	     	</td>
            <td width="15%" align="center">
    			<?php echo round($this->orderData->order_discountAmount, $this->nbDecimal)?>
    			<input type="hidden" name="order_discountAmount" value="<?php echo $this->orderData->order_discountAmount*1?>">
    		</td>
            <td width="15%" align="center" class="hasTip" title="<?php echo JText::_('COM_NETBASEVM_EXTEND_SUBTOTAL'); ?>">
	    		<?php echo round($this->orderData->order_subtotal, $this->nbDecimal)?>
	    		<input type="hidden" name="order_subtotal" id="order_subtotal" value="<?php echo $this->orderData->order_subtotal*1?>">
	    	</td>
    	<?php }?>
    	<td width="15%" align="center">
    		<?php echo round($this->orderData->order_salesPrice, $this->nbDecimal)?>
    		<?php if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){?>
    			<input type="hidden" name="order_salesPrice" id="order_salesPrice" value="<?php echo $this->orderData->order_salesPrice*1?>">
    		<?php } ?>
    	</td>
	</tr>
</table>
<br/>
	<!-- Shipping and handling row -->
<table cellpadding="0" cellspacing="0" width="100%">	
    <tr>
		<td width="56%" align="right"><?php echo JText::_('COM_NETBASEVM_EXTEND_SHIPPING_AND_HANDLING_FEE'); ?>:
		<?php if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && isset($this->shippings[$this->orderData->shipment_method_id]))
				echo $this->shippings[$this->orderData->shipment_method_id]->name;
		?></td>
		<td width="7%" align="center"><input class="price" type="text" name="order_shipping" id="order_shipping" value="<?php echo $this->orderData->order_shipping*1; ?>" /></td>
		<td width="12%" align="center">
			<?php echo JHTML::_('select.genericlist', $this->taxRates, 'order_shipping_taxrate', null, 'value', 'name', $this->orderData->order_shipping_taxrate);  ?>
			<input class="price" type="text" style="float: left" name="order_shipping_tax" value="<?php echo $this->orderData->order_shipping_tax*1; ?>" id="order_shipping_tax"/>
		</td>
		<td width="9%" align="center"><?php echo round($this->orderData->order_shipping + $this->orderData->order_shipping_tax, $this->nbDecimal); ?></td>
		<td width="9%" align="center"><?php echo round($this->orderData->order_shipping, $this->nbDecimal); ?></td>
		<td width="9%" align="center"><?php echo round($this->orderData->order_shipping_tax, $this->nbDecimal); ?></td>
		<td width="9%" align="center"><?php echo round($this->orderData->order_shipping + $this->orderData->order_shipping_tax, $this->nbDecimal); ?></td>
	</tr>	
	<?php if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2) {?>
	<!-- Payment fee/discount row (VM2) -->
	<tr>
		<td width="56%" align="right"><?php echo JText::_('COM_NETBASEVM_EXTEND_PAYMENT_FEE_OR_DISCOUNT'); ?>:
		<?php if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2 && isset($this->payments[$this->orderData->payment_method_id]))
				echo $this->payments[$this->orderData->payment_method_id]->name;
		?></td>
		<td width="7%" align="center"><input class="price" type="text" name="order_payment" id="order_payment" value="<?php echo $this->orderData->order_payment*1; ?>" /></td>
		<td width="12%" align="center">
			<?php echo JHTML::_('select.genericlist', $this->taxRates, 'order_payment_taxrate', null, 'value', 'name', $this->orderData->order_payment_taxrate);  ?>
			<input class="price" type="text" name="order_payment_tax" style="float: left" value="<?php echo $this->orderData->order_payment_tax*1; ?>" id="order_payment_tax"/>
		</td>
		<td width="9%" align="center"><?php echo round($this->orderData->order_payment + $this->orderData->order_payment_tax, $this->nbDecimal); ?></td>
		<td width="9%" align="center"><?php echo round($this->orderData->order_payment, $this->nbDecimal); ?></td>
		<td width="9%" align="center"><?php echo round($this->orderData->order_payment_tax, $this->nbDecimal); ?></td>
		<td width="9%" align="center"><?php echo round($this->orderData->order_payment + $this->orderData->order_payment_tax, $this->nbDecimal); ?></td>
	</tr>	
	<?php } ?>
</table>	
<br/>
<div class="rightCol30">
	<table class="admintable" cellspacing="1">
	  		<tbody>
	  			<?php if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1) {?>
				<tr> 
					<td class="key autoWidth" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_PAYMENT_FEE_OR_DISCOUNT'); ?></td>
					<td width="1%"><?php echo round($this->orderData->order_payment, 4); ?>
					<input type="hidden" name="order_payment" id="order_payment" value="<?php echo ($this->orderData->order_payment*1); ?>" />
					</td>
	  			</tr>
	 			<tr> 
					<td class="key autoWidth" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_OTHER_FEE_OR_DISCOUNT'); ?></td>
					<td width="1%"><input type="text" name="order_discount" id="order_discount" value="<?php echo ($this->orderData->order_discount*1); ?>" /></td>
	  			</tr>
	  			<?php } else { ?>
	  			<!-- Calculation rules for order (VM2) -->
	  			<tr> 
	  				<td colspan="2" class="key"><b><?php echo JText::_('COM_NETBASEVM_EXTEND_OTHER_FEE_OR_DISCOUNT'); ?>: </b></td>
	  			<tr> 
	  			<tr>
	  				<td colspan="2">
	  					<?php foreach ($this->orderData->calcRules as $rule) { //for helping in table obj ?>
	  					<input type="hidden" name="init_calc_rules[<?php echo $rule->virtuemart_order_calc_rule_id?>]" value="1" />
	  					<?php }?>
	  					<table width="100%" id="calc_rules">
	  					<tbody>
			  			<?php foreach ($this->orderData->calcRules as $rule) {
			  				$id = $rule->virtuemart_order_calc_rule_id;?>
				  			<tr>
				  				<td width="1%"><a href="javascript:void(0)" onclick="this.getParent('tr').dispose();"><img src="components/com_netbasevm_extend/assets/images/delete.png"></a></td>
				  				<td width="50%"><input type="text" name="calc_rule_name[<?php echo $id?>]" value="<?php echo htmlspecialchars($rule->calc_rule_name) ?>" class="fullWidth" title="<?php echo JText::_('COM_NETBASEVM_EXTEND_CALC_RULE_NAME');  ?>"></td>
				  				<td width="1%"><?php echo JHTML::_('select.genericlist', $this->rulesType, 'calc_kind['.$id.']', null, 'value', 'name', $rule->calc_kind); ?></td>
				  				<td width="1%"><?php echo InvoiceCurrencyDisplay::getSymbol($this->orderData->order_currency) ?></td>
				  				<td width="30%"><input type="text" name="calc_amount[<?php echo $id?>]" value="<?php echo $rule->calc_amount*1?>" class="fullWidth" title="<?php echo JText::_('COM_NETBASEVM_EXTEND_CALC_RULE_AMOUNT');  ?>"></td>
				  			</tr>
			  			<?php } ?>
			  			</tbody>
	  					<tfoot style="display:none">
				  			<tr>
				  				<td width="1%"><a href="javascript:void(0)" onclick="this.getParent('tr').dispose();"><img src="components/com_netbasevm_extend/assets/images/delete.png"></a></td>
				  				<td width="50%"><input type="text" name="calc_rule_name_model" value="" class="fullWidth" title="<?php echo JText::_('COM_NETBASEVM_EXTEND_CALC_RULE_NAME');  ?>"></td>
				  				<td width="1%"><?php echo JHTML::_('select.genericlist', $this->rulesType, 'calc_kind_model', null, 'value', 'name');  ?></td>
				  				<td width="1%"><?php echo InvoiceCurrencyDisplay::getSymbol($this->orderData->order_currency) ?></td>
				  				<td width="30%"><input type="text" size="5" name="calc_amount_model" value="" class="fullWidth" title="<?php echo JText::_('COM_NETBASEVM_EXTEND_CALC_RULE_AMOUNT');  ?>"></td>
				  			</tr>
			  			</tfoot>
		  				</table>
	  				</td>
	  			</tr>
	  			<tr> 
	  				<td colspan="2"><a href="javascript:void(0)" onclick="vmiRows.addNewRow('calc_rules')"><?php echo JText::_('COM_NETBASEVM_EXTEND_ADD_NEW_MODIFIER'); ?></a></td>
	  			<tr> 
	  			<?php } ?>
	 			<tr> 
					<td class="key autoWidth" nowrap="nowrap"><?php echo JText::_('COM_NETBASEVM_EXTEND_COUPON_DISCOUNT'); ?></td>
					<td width="1%" align="right"><input type="text" size="10" name="coupon_discount" id="coupon_discount" value="<?php echo round($this->orderData->coupon_discount, $this->nbDecimal); ?>" /></td>
	  			</tr>
				<tr>
					<td class="key autoWidth" nowrap="nowrap"><b><?php echo JText::_('COM_NETBASEVM_EXTEND_TOTAL'); ?></b></td>
					<td width="1%" align="right"><input type="text" size="10" name="order_total" id="order_total" value="<?php echo round($this->orderData->order_total, $this->nbDecimal); ?>" /></td>
				</tr>
			</tbody>
	</table>
	<a class="refreshOrder" href="javascript:showOrderData('<?php echo addslashes(JURI::base()); ?>',false,false,false)" title=""><?php echo JText::_('COM_NETBASEVM_EXTEND_REFRESH_AND_RECALCULATE'); ?></a>
</div>
</fieldset>
<div class="clr"></div>
<?php if (! $this->orderajax) { ?></div><?php } ?>