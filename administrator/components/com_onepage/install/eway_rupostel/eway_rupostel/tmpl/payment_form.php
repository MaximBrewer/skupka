<?php
/**
 *
 * @author stAn, RuposTel.com
 * @version $Id: eway_rupostel.php 
 * @package eWay Payment Plugin
 * @subpackage payment
 * @copyright Copyright (C) RuposTel.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * eWay Payment is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * Based on Authorize.net plugin by Virtuemart.net team
 *
 * http://rupostel.com
 */
defined('_JEXEC') or die('Restricted access');

?>
<span class="vmpayment_cardinfo"><?php echo $viewData['sandbox_msg']; ?>
		    <table border="0" cellspacing="0" cellpadding="2" width="100%">
		    <tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="cardholder"><?php $cardholder = JText::_('PLG_VMPAYMENT_EWAY_RUPOSTEL_CARDHOLDER');  if ($cardholder=='PLG_VMPAYMENT_EWAY_RUPOSTEL_CARDHOLDER') $cardholder = "Cardholder Name";  echo $cardholder; ?></label>
		        </td>
		        <td> <input type="text" class="inputbox" onfocus="javascript: focusme('<?php echo $viewData['vmid']; ?>')" id="cc_cardholder_<?php echo $viewData['vmid']; ?>" name="cc_cardholder_<?php echo $viewData['vmid']; ?>" value="<?php echo $viewData['cc_cardholder']; ?>" autocomplete="off" />
					</td>
		    </tr>
			<tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="creditcardtype"><?php echo JText::_('VMPAYMENT_AUTHORIZENET_CCTYPE'); ?></label>
		        </td>
		        <td> <?php echo $viewData['creditCardList']; ?>
					</td>
		    </tr>
		    <tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="cc_type"><?php echo JText::_('VMPAYMENT_AUTHORIZENET_CCNUM'); ?></label>
		        </td>
		        <td>
				<script type="text/javascript">
				//<![CDATA[  
				  function focusme(id)
				  {
				    var d = document.getElementById('payment_id_'+id); 
					if (d !=null)
					 {
					   console.log(d.tagName); 
					   console.log(d.type); 
					   if (d.tagName == 'INPUT')
					   if (d.type='radio')
					   if (!d.checked)
					     {
						   if (typeof jQuery != 'undefined')
						   {
						   jQuery(d).click(); 
						   console.log('click'); 
						   }
						   else
						   if (typeof d.click != 'undefined')
						   {
						   console.log('js click'); 
						   d.click(); 
						   }
						 }
					   if (d.tagName='option')
					     {
						   d.selected = true; 
						 }
					 }
				  }
				  function checkEway(id, el)
				   {
				     ccError=razCCerror(id);
					CheckCreditCardNumber(el.value, id);
					if (!ccError) {
					 //el.value='';
					}
					focusme('<?php echo $viewData['vmid']; ?>');
				   }
				//]]> 
				</script>
		        <input type="text" class="inputbox" id="cc_number_<?php echo $viewData['vmid']; ?>" name="cc_number_<?php echo $viewData['vmid']; ?>" value="<?php echo $viewData['cc_number']; ?>"    autocomplete="off"   onchange="javascript:checkEway(<?php echo $viewData['vmid']; ?>, this);"  />
		        <div style="display: none; clear: both; color:red;" id="cc_cardnumber_errormsg_<?php echo $viewData['vmid']; ?>"></div>
		    </td>
		    </tr>
		    <tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="cc_cvv"><?php echo JText::_('VMPAYMENT_AUTHORIZENET_CVV2'); ?></label>
		        </td>
		        <td>
		            <input type="text" class="inputbox" id="cc_cvv_<?php echo $viewData['vmid']; ?>" name="cc_cvv_<?php echo $viewData['vmid']; ?>" maxlength="4" size="5" value="<?php echo $viewData['cc_cvv']; ?>" autocomplete="off" />

			<span class="hasTip" title="<?php echo JText::_('VMPAYMENT_AUTHORIZENET_WHATISCVV'); ?>::<?php echo JText::sprintf("VMPAYMENT_AUTHORIZENET_WHATISCVV_TOOLTIP", $viewData['cvv_images']); ?> ">
			<?php echo		JText::_('VMPAYMENT_AUTHORIZENET_WHATISCVV'); ?>
			</span></td>
		    </tr>
		    <tr>
		        <td nowrap width="10%" align="right"><label><?php echo JText::_('VMPAYMENT_AUTHORIZENET_EXDATE'); ?></label></td>
		        <td> 
				<?php echo $viewData['months'];  ?>/
				<script type="text/javascript">
				//<![CDATA[  
				  function changeDate(id, el)
				   {
				     var month = document.getElementById('cc_expire_month_'+id); 
					 if(!CreditCardisExpiryDate(month.value,el.value, id))
					 {
					  //el.value='';
					  //month.value='';
					 }
				   }
				//]]> 
				</script><?php echo $viewData['years']; ?>
<div id="cc_expiredate_errormsg_<?php echo $viewData['vmid']; ?>"></div>
</td>  </tr>  	</table></span>
