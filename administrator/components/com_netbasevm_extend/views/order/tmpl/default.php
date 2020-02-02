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

JHtmlBehavior::framework();
AdminUIHelper::startAdminArea($this);

$files = array(
	'administrator/components/com_netbasevm_extend/assets/js/ajaxcontent.js', 
	'administrator/components/com_netbasevm_extend/assets/js/autility.js');
if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2)
	$files[] = 'administrator/components/com_netbasevm_extend/assets/js/rows.js';
	
$document = JFactory::getDocument();
foreach ($files as $file){
	$version = ($mtime = filemtime(JPATH_SITE.'/'.$file)) ? $mtime : time();
	$document->addScript(JURI::root().$file.'?v='.$version);}

JHTML::_('behavior.tooltip');
JHTML::_('behavior.calendar');

JToolBarHelper::title(JText::sprintf('COM_NETBASEVM_EXTEND_ORDER_NUMBER_EDITING', $this->orderData->order_id ? $this->orderData->order_id : ''), 'order');
JToolBarHelper::save();
JToolBarHelper::apply();
JToolBarHelper::cancel();

JFilterOutput::objectHTMLSafe($this->orderData);

/* @var $document JDocumentHTML */

$js  = '		var AddProduct = \'' . JText::_('COM_NETBASEVM_EXTEND_SELECT_PRODUCT', TRUE) . '\';' . "\n";
$js .= '		var AreYouSure = \'' . JTEXT::_('COM_NETBASEVM_EXTEND_ARE_YOU_SURE', TRUE) . '\';' . "\n";
$js .= '		function submitbutton (pressbutton) {' . "\n";
$js .= '			var form = document.adminForm;' . "\n";
$js .= '			if (pressbutton == \'cancel\') {' . "\n";
$js .= '		  		if (typeof Joomla != "undefined") Joomla.submitform(pressbutton); else submitform(pressbutton);' . "\n";
$js .= '				return;' . "\n";
$js .= '			}' . "\n";
$js .= '			if (form.status.value == \'\')' . "\n";
$js .= '				alert(\'' . JText::_('COM_NETBASEVM_EXTEND_SELECT_STATUS', TRUE) . '\');' . "\n";
$js .= '			else if (form.vendor.value == \'\')' . "\n";
$js .= '				alert(\'' . JText::_('COM_NETBASEVM_EXTEND_SELECT_VENDOR', TRUE) . '\');' . "\n";
$js .= '			else if (form.order_currency.value == \'\')' . "\n";
$js .= '				alert(\'' . JText::_('COM_NETBASEVM_EXTEND_SELECT_CURRENCY', TRUE) . '\');' . "\n";
$js .= '			else if (form.payment_method_id.value == \'\')' . "\n";
$js .= '				alert(\'' . JText::_('COM_NETBASEVM_EXTEND_SELECT_PAYMENT', TRUE) . '\');' . "\n";
$js .= '			else if (form.shipment_method_id.value == \'\')' . "\n";
$js .= '				alert(\'' . JText::_('Select Shipment', TRUE) . '\');' . "\n";
$js .= '			else if ($("orderInfo").getElements("select[name^=order_status]").some(function(el){if (!el.options[el.selectedIndex].value){el.focus(); return true;} return false;}))' . "\n";
$js .= '				alert(\'' . JText::_('COM_NETBASEVM_EXTEND_SELECT_ITEM_STATUS', TRUE) . '\');' . "\n";
$js .= '			else {' . "\n";
$js .= '				if ($("user_id").value==""){' . "\n";
$js .= '					$("update_userinfo").value=1;' . "\n";
$js .= '					if (form.B_first_name.value.trim() == \'\'){' . "\n";
$js .= '						alert(\'' . JText::_('COM_NETBASEVM_EXTEND_FILL_IN_FIRST_NAME', TRUE) . '\');' . "\n";
$js .= '						form.B_first_name.focus();}' . "\n";
$js .= '					else if (form.B_last_name.value.trim() == \'\'){' . "\n";
$js .= '						alert(\'' . JText::_('COM_NETBASEVM_EXTEND_FILL_IN_LAST_NAME', TRUE) . '\');' . "\n";
$js .= '						form.B_last_name.focus();}' . "\n";
$js .= '					else if (form.B_email.value.trim() == \'\'){' . "\n";
$js .= '						alert(\'' . JText::_('COM_NETBASEVM_EXTEND_FILL_IN_E-MAIL', TRUE) . '\');' . "\n";
$js .= '						form.B_email.focus();}' . "\n";
$js .= '					else {' . "\n";
$js .= '						alert("' . JText::_('COM_NETBASEVM_EXTEND_NEW_CUSTOMER_DESC', TRUE) . '");' . "\n";
$js .= '						submitform( pressbutton );}' . "\n";
$js .= '				} else {' . "\n";
$js .= '					if (changed_userinfo==true && $("B_user_info_id").value>0){' . "\n";
$js .= '						if (confirm("' . JText::_('COM_NETBASEVM_EXTEND_UPDATE_ALSO_DEFAULT_VALUES', TRUE) . '"))' . "\n";
$js .= '							$("update_userinfo").value=1;' . "\n";
$js .= '					} ' . "\n";
$js .= '					if (typeof Joomla != "undefined") Joomla.submitform( pressbutton ); else submitform( pressbutton );}' . "\n";
$js .= '				}' . "\n";
$js .= '		}' . "\n";
$js .= '	if (typeof Joomla != "undefined")
				Joomla.submitbutton = submitbutton;' . "\n";


//initialize "user info change watcher"
$js .= 'var changed_userinfo=false;

	function addUserInfoCheck()
	{
			//for 1.5 without mootools upgrade
			userInputs = $("billing_address").getElements("input[name!=user]");
			userInputs.concat($("billing_address").getElements("textarea"));
			userInputs.concat($("billing_address").getElements("select"));

			userInputs.concat($("shipping_address").getElements("input"));
			userInputs.concat($("shipping_address").getElements("textarea"));
			userInputs.concat($("shipping_address").getElements("select"));
	
			jQuery.each(userInputs,function (input){
			
				if (typeof input.type != "undefined")
				{
					if (input.type=="text")
						input.addEvent("keyup", function(event){changed_userinfo=true;});
						
					if (input.type=="checkbox" || input.type=="radio")
						input.addEvent("click", function(event){changed_userinfo=true;});
				}
				
				//input.addEvent("change", function(event){changed_userinfo=true;});
                                input.addEventListener("change", function(event){changed_userinfo=true;});
			});
	}

	window.addEvent(\'domready\', function() {
		addUserInfoCheck();
	});'; 

$document->addScriptDeclaration($js);

?>	

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" id="baseurl" name="baseurl" value="<?php echo addslashes(JURI::base()); ?>" />
  <div class="purchase-order left">
  	<?php //echo  JHtml::_('tabs.start', 'new-order'); ?>
    <fieldset class="adminform garenal-form" style="width:50% !important; float: left;">
      	<legend style="border:none !important; color:#315b8c;"><?php echo JText::_('COM_NETBASEVM_EXTEND_GENERAL')?></legend>
    	<table style="float: left; margin-right: 10px;" cellspacing="0" class="admintable" width="100%">
    		<tbody>
    			<tr class="new_id">
    				<td class="key" nowrap="nowrap" align="left"><?php echo JText::_('ID'); ?></td>
    				<td>
    					<?php echo $this->orderData->order_id ? $this->orderData->order_id : JText::_('COM_NETBASEVM_EXTEND_NEW'); ?>
    					<input type="hidden" id="cid" name="cid" value="<?php echo $this->orderData->order_id; ?>" />
    					<input type="hidden" id="order_id" name="order_id" value="<?php echo $this->orderData->order_id; ?>" />
    				</td>
    			</tr>
    			<tr>
    				<td class="key" nowrap="nowrap" align="left"><?php echo JText::_('COM_NETBASEVM_EXTEND_CREATE_DATE'); ?></td>
    				<td><?php echo $this->orderData->cdate ? strftime(JText::_('COM_NETBASEVM_EXTEND_DATETIME_FORMAT'),$this->orderData->cdate) : ''; ?></td>
    			</tr>
    			<tr>
    				<td class="key" nowrap="nowrap" align="left"><?php echo JText::_('COM_NETBASEVM_EXTEND_MODIFIED_DATE'); ?></td>
    				<td><?php echo $this->orderData->mdate ? strftime(JText::_('COM_NETBASEVM_EXTEND_DATETIME_FORMAT'),$this->orderData->mdate) : ''; ?></td>
    			</tr>
    			<tr>
    				<td class="key" nowrap="nowrap" align="left"><span class="compulsory"><?php echo JText::_('COM_NETBASEVM_EXTEND_STATUS'); ?>&nbsp;<sup style="color:red;">*</sup></span></td>
    				<td>
    					<?php
    						array_unshift($this->orderStatus, JHTML::_('select.option', '', JText::_('COM_NETBASEVM_EXTEND_SELECT'), 'id', 'name')); 
    						echo JHTML::_('select.genericlist', $this->orderStatus, 'status', null, 'id', 'name', $this->orderData->order_status); 
    					?>
    					<label><input type="checkbox" name="notify" value="YF"><span class="label_status"><?php echo JText::_('COM_NETBASEVM_EXTEND_NOTIFY_CUSTOMER'); ?></span></label>
    					
    					<?php if (false /* disabled*/ && COM_NETBASEVM_EXTEND_ORDERS_ISVM2) {?>
    						<label class="hasTip" title="<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_APPLY_TO_ALL_ITEMS')); ?>::<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_STATUS_APPLY_ITEMS_DESC')); ?>">
    						<input type="checkbox" name="apply_status_to_all_items" value="1" checked />
    						<?php echo JText::_('COM_NETBASEVM_EXTEND_APPLY_TO_ALL_ITEMS'); ?></label>
    					<?php }  else {?>
    						<input type="button" class="input_bg applyStatus_button" title="<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_APPLY_TO_ALL_ITEMS')); ?>::<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_STATUS_APPLY_ITEMS_DESC')); ?>" value="<?php echo JText::_('COM_NETBASEVM_EXTEND_APPLY_TO_ALL_ITEMS'); ?>" onclick="applyStatus();">			
    					<?php } ?>
    				</td>
    			</tr>
    
    
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="compulsory"><?php echo JText::_('COM_NETBASEVM_EXTEND_VENDOR'); ?>&nbsp;<sup style="color:red;">*</sup></span></td>
    				<td class="key">
    					<?php
    						array_unshift($this->vendors, JHTML::_('select.option', '', JText::_('COM_NETBASEVM_EXTEND_SELECT'), 'id', 'name')); 
    						echo JHTML::_('select.genericlist', $this->vendors, 'vendor', null, 'id', 'name', $this->orderData->vendor_id); 
    					?>
    				</td>
    			</tr>
    				
    			<tr>
    				<td class="key" nowrap="nowrap"><span class="compulsory hasTip" title="<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_CURRENCY')); ?>::<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_CURRENCY_DESC')); ?>"><?php echo JText::_('COM_NETBASEVM_EXTEND_CURRENCY'); ?>&nbsp;<sup style="color:red;">*</sup></span></td>
    				<td>
    					<?php
    						foreach ($this->currencies as $currency)
    							$currency->name = JText::sprintf('COM_NETBASEVM_EXTEND_CURRENCY_SHORT_INFO', $currency->name, COM_NETBASEVM_EXTEND_ORDERS_ISVM2 ? $currency->symbol : $currency->id);
    						array_unshift($this->currencies, JHTML::_('select.option', '', JText::_('COM_NETBASEVM_EXTEND_SELECT'), 'id', 'name'));
    						echo JHTML::_('select.genericlist', $this->currencies, 'order_currency', null, 'id', 'name', $this->orderData->order_currency); 
    					?>					
    				</td>
    			</tr>
    		</tbody>
    	</table>
    <br/><br/>
    </fieldset>
    
    <fieldset class="adminform shipping-form" style="width:40%;float: right;">
      <legend style="border:none !important; color:#315b8c;"><?php echo JText::_('COM_NETBASEVM_EXTEND_SHIPPING') ?></legend>
      <?php if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2) {?>
      <table style="float: left; margin-right: 10px;" cellspacing="0" class="admintable">
    		<tbody>
    			<tr>
    				<td class="key" nowrap="nowrap" valign="top"><span style="padding:0px 5px;" class="compulsory"><?php echo 'Shipment'; ?>&nbsp;<sup style="color:red;">*</sup></span></td>
    				<td>
      <?php
	   
      array_unshift($this->shippings, JHTML::_('select.option', '', JText::_('COM_NETBASEVM_EXTEND_SELECT'), 'shipping_rate_id', 'name'));
      echo JHTML::_('select.genericlist', $this->shippings, 'shipment_method_id', "", 'shipping_rate_id', 'name', $this->orderData->shipment_method_id); 						
      ?>
      <input type="button" class="input_bg" title="<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_APPLY')); ?>::<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_APPLY_SHIPMENT_DESC')); ?>" value="<?php echo JText::_('COM_NETBASEVM_EXTEND_APPLY'); ?>" onclick="showOrderData(null,false,true,false);">		
      
	  				</td>
    			</tr>	
    			
    		</tbody>	
    	</table>
	  <?php } ?>
      
    </fieldset>
    <fieldset class="adminform payment-form" style="width:40%;float:right;">
      <legend style="border:none !important; color:#315b8c;"><?php echo JText::_('COM_NETBASEVM_EXTEND_PAYMENT') ?></legend>
    	<table style="float: left; margin-right: 10px;" cellspacing="0" class="admintable">
    		<tbody>
    			<tr>
    				<td class="key" nowrap="nowrap" valign="top"><span style="padding:0px 5px;" class="compulsory"><?php echo JText::_('COM_NETBASEVM_EXTEND_PAYMENT'); ?>&nbsp;<sup style="color:red;">*</sup></span></td>
    				<td>
    					<?php 
    						if (COM_NETBASEVM_EXTEND_ORDERS_ISVM1)
	    						foreach ($this->payments as $payment) {
	    							if ($payment->payment_method_discount != 0.00)
	    								$payment->name = JText::sprintf($payment->payment_method_discount_is_percent == 1 ? 'COM_NETBASEVM_EXTEND_PAYMENT_SHORT_INFO_PERCENT' : 'COM_NETBASEVM_EXTEND_PAYMENT_SHORT_INFO', $payment->name, - round($payment->payment_method_discount, 2));
	    							else
	    								$payment->name = $payment->name;
	    						} 
    						array_unshift($this->payments, JHTML::_('select.option', '', JText::_('COM_NETBASEVM_EXTEND_SELECT'), 'id', 'name'));
    						echo JHTML::_('select.genericlist', $this->payments, 'payment_method_id', null, 'id', 'name', $this->orderData->payment_method_id); 
    					?>
    					<?php if (COM_NETBASEVM_EXTEND_ORDERS_ISVM2){ ?>
    					<input type="button" class="input_bg" title="<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_APPLY')); ?>::<?php echo $this->escape(JText::_('COM_NETBASEVM_EXTEND_APPLY_PAYMENT_DESC')); ?>" value="<?php echo JText::_('COM_NETBASEVM_EXTEND_APPLY'); ?>" onclick="showOrderData(null,false,false,true);">
    					<?php } ?>
    				</td>
    			</tr>	
    			
    		</tbody>	
    	</table>
    </fieldset>
    
    <fieldset class="adminform" style="width:100% !important;">
    	<legend style="border:none !important; color:#315b8c;"><?php echo JText::_('COM_NETBASEVM_EXTEND_ADDITIONAL')?></legend>
    	<table style="float: left;width:100%" cellspacing="0" class="admintable">
    		<tbody>
    			<tr>
    				<td class="key" nowrap="nowrap" valign="top" width="10%"><span><?php echo JText::_('COM_NETBASEVM_EXTEND_CUSTOMER_NOTE'); ?></span></td>
    				<td>
    					<textarea name="customer_note" id="customer_note" cols="20" rows="4" style="width:44%"><?php echo trim($this->orderData->customer_note); ?></textarea>
    				</td>
    			</tr>
    			<tr>
    				<td class="key" nowrap="nowrap" valign="top"><br/><span ><?php echo JText::_('COM_NETBASEVM_EXTEND_COUPON_CODE'); ?></span></td><td>
                    <br/>
    					<input type="text" name="coupon_code" id="coupon_code" size="15" onchange="getCouponInfo(this.value,'<?php echo $this->orderData->order_currency?>');" onkeyup="getCouponInfo(this.value,'<?php echo $this->orderData->order_currency?>');"  value="<?php echo $this->orderData->coupon_code; ?>" />
    					<span id="coupon_info"></span>
    					
    					<script type="text/javascript">getCouponInfo($('coupon_code').value, '<?php echo $this->orderData->order_currency?>');</script>
    				</td>
    			</tr>
    			<?php ?>
    		</tbody>
    	</table>
        <br/><br/>
    </fieldset>
<?php require_once 'products.php'; ?>
    <?php require_once 'userinfo.php'; ?>

  </div>
	<div class="clr"></div>
	
	<input type="hidden" value="com_netbasevm_extend" name="option" /> 
	<input type="hidden" name="task" value="" /> 
	<input type="hidden" name="controller" value="order" /> 
</form>

<?php AdminUIHelper::endAdminArea(); ?>