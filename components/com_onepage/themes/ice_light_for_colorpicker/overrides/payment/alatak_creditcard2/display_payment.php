<?php  defined ('_JEXEC') or die();

/**
 * @author stAn based on paypal pro 
 * @version display_payment.php
 * @package VirtueMart
 * @subpackage payment
 * @copyright Copyright RuposTel.com
 * @license http://www.gnu.org/licenses/gpl.html GNU/GPL
 */


	$js='

	
	 	 
	

'; // addScriptDeclaration
if (VM_VERSION < 3) {
	static $jsLoaded = false;

	if (!$jsLoaded) {
		JFactory::getDocument()->addScript(JURI::root(true) . '/plugins/vmpayment/alatak_creditcard/alatak_creditcard/assets/js/jquery.creditCardValidator.js');
	}
		$js = "	//<![CDATA[" . $js . "//]]>";
		JFactory::getDocument()->addScriptDeclaration($js);
		$jsLoaded=true;

} else {
if (!$jsLoaded) {
	vmJsApi::addJScript('/plugins/vmpayment/alatak_creditcard/alatak_creditcard/assets/js/jquery.creditCardValidator.js');
}
	vmJsApi::addJScript('creditCardForm', $js);
}


$creditCards = $method->creditcards;
if (empty($creditCards)) {
			$creditCards = array('visa', 'visa_electron', 'mastercard', 'maestro', 'discover');
		} elseif (!is_array($creditCards)) {
			$creditCards = (array)$creditCards;
		}



defined('_JEXEC') or die();

$customerData = $viewData['customerData'];

JHTMLOPC::_('behavior.tooltip');
JHTMLOPC::script('vmcreditcard.js', 'components/com_onepage/assets/js/', false);
VmConfig::loadJLang('com_virtuemart', true);
vmJsApi::jCreditCard();

$doc = JFactory::getDocument();
//$doc->addScript(JURI::root(true).'/plugins/vmpayment/paypal/paypal/assets/js/site.js');

?>
<div id="paymentMethodOptions_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" class="paymentMethodOptions" >
  
    <span class="vmpayment_cardinfo">
        <?php 
       
       
        ?>
        <table border="0" cellspacing="0" cellpadding="2" width="100%">
		
			 <tr>
			
                <td  align="right"><?php echo JText::_('VMPAYMENT_ALATAK_CREDITCARD_CCNAME'); ?></td>
                <td>
				<input type="text" name="name_on_card_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="name_on_card_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" value="<?php echo $viewData['name_on_card']; ?>" placeholder="<?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_CCNAME_PLACEHOLDER') ?>">
                </td>
            </tr>
            <tr valign="top">
                <td  align="right">
                    <label for="creditcardtype"><?php echo JText::_('VMPAYMENT_PAYPAL_CC_CCTYPE'); ?></label>
                </td>
                <td>

                	
                    <?php
					$attribs = 'class="cc_type" rel="'.$viewData['virtuemart_paymentmethod_id'].'"';
					echo $this->renderCreditCardList($creditCards, '', $viewData['virtuemart_paymentmethod_id'], false, $attribs); 

                    ?>
                </td>
            </tr>
            <tr valign="top">
                <td  align="right">
                    <label for="cc_type"><?php echo JText::_('VMPAYMENT_PAYPAL_CC_CCNUM'); ?></label>
                </td>
                <td>
				<div>
                    <input type="text" size="30" class="inputbox card_number" id="cc_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"
                        name="cc_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" value="<?php echo $viewData['card_number']; ?>" placeholder="1234 5678 9012 3456" 
                        autocomplete="off" onchange="return changeCC(this)" />
                    </div>
					<div style="clear: both; color: red;" class="invalid" id="cc_cardnumber_errormsg_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"></div>
                </td>
            </tr>
			
			
            <tr valign="top">
                <td  align="right">
                    <label for="cc_cvv"><?php echo JText::_('VMPAYMENT_ALATAK_CREDITCARD_CVV') ?></label>
                </td>
                <td>
                    <input type="text" class="inputbox" id="cc_cvv_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" name="cc_cvv_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" maxlength="3" size="5" value="<?php echo $viewData['cvv']; ?>" placeholder="123" autocomplete="off" />
                    
                </td>
            </tr>
            <tr>
			
                <td  align="right"><?php echo JText::_('VMPAYMENT_ALATAK_CREDITCARD_EXDATE'); ?></td>
                <td>
				<div>
				<input onchange="javascript: ccChanged(this);" type="text" name="expiry_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="expiry_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" value="<?php echo $viewData['expiry_date']; ?>" maxlength="5"  placeholder="mm/yy" class="expiry_date">
                    </div>
                    <div style="clear: both;" id="cc_expiredate_errormsg_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"></div>
                </td>
            </tr>
			
			
			<tr class="vertical maestro" style="display: none; opacity: 0;">
			
				<td>
					<label for="issue_date"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_ISSUE_DATE') ?></label>
				</td>
				<td>
					<input type="text" name="issue_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="issue_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"  placeholder="mm/yy" value="<?php echo $viewData['issue_date']; ?>" maxlength="5" class="issue_date">
				</td>
		    </tr>
			<tr class="vertical maestro" style="display: none; opacity: 0;">
				<td>
					<span class="or">or</span>
					<label for="issue_number"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_ISSUE_NUMBER') ?></label>
				</td>
				<td>
					<input type="text" name="issue_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="issue_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" placeholder="12" maxlength="2" value="<?php echo $viewData['issue_number']; ?>" class="issue_number">
				</td>
			
		    </tr>
			
			
        </table>
    </span>
</div>
<script type="text/javascript">
	 var apayment_id = '<?php echo $viewData['virtuemart_paymentmethod_id']; ?>'; 
	 
	 function creditCardCallback(e) {
		 
		 if (typeof e !== 'object') return 
			
			if (e.card_type != null)
			{
			if (typeof e.card_type.name != 'undefined')
			{
		    if (e.card_type.name.toString().indexOf('maestro')>=0)
			{
				jQuery(".vertical.maestro").show(); 
			}
			else
			{
				jQuery(".vertical.maestro").hide(); 
			}
			var cs = document.getElementById('cc_type_'+apayment_id); 
			if (cs != null)
			{
				//cs.value = e.cart_type; 
				jQuery(cs).val(e.card_type.name.toString()).change();
			}
			}
			}
			
			
			if (e.luhn_valid !== true)
			{
				var cc = document.getElementById('cc_number_'+apayment_id); 
				if (cc != null)
				{
				ccError=razCCerror(apayment_id);
                CheckCreditCardNumber(cc.value, apayment_id);
                       
				}
			}
            
        }
       
	 function luhn_valid(e, card_number) {
	        if (e.length_valid && e.luhn_valid) {
	        	 jQuery("#card_number_' . $viewData['virtuemart_paymentmethod_id'] . '").removeAttr("class").attr("class","");
	             jQuery("#card_number_' . $viewData['virtuemart_paymentmethod_id'] . '").addClass("card_number valid " + e.card_type.name);
	             jQuery("#card_type_' . $viewData['virtuemart_paymentmethod_id'] . '").val(e.card_type.name);
				jQuery("#payment_id_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("checked", true);

	        } else {
	            jQuery(card_number).removeClass("valid").addClass("card_number "  + e.card_type.name);
				jQuery("#payment_id_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("checked", false);

	        }
			if (e.card_type.name==="amex") {
					jQuery("#cvv_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("placeholder", "1234");
					jQuery("#cvv_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("maxlength", "4");
			} else {
					jQuery("#cvv_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("placeholder", "123");
					jQuery("#cvv_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("maxlength", "3");
			}
    }
	



 function ccChanged(el)
  {
    var month = document.getElementById('cc_expire_month_<?php echo $viewData['virtuemart_paymentmethod_id'] ?>'); 
	if(!CreditCardisExpiryDate(month.value,el.value, '<?php echo $viewData['virtuemart_paymentmethod_id']; ?>'))
	 {
	  el.value='';
	  month.value='';
	 } 
  }
  
  function changeCC(el)
	{
		jQuery(el).validateCreditCard(creditCardCallback, {accept: [<?php echo  $viewData['creditcards']; ?> ]});
		
		ccError=razCCerror('<?php echo $viewData['virtuemart_paymentmethod_id']; ?>');
                            CheckCreditCardNumber(el.value, '<?php echo $viewData['virtuemart_paymentmethod_id']; ?>');
                        if (!ccError) {
                        this.value='';
						}
		
	}
  
</script>
