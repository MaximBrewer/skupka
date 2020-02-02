<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/


echo $intro_article; 
 

if (!empty($newitemid))
$Itemid = $newitemid;
?><div style="float: left; clear: both; width: 100%;"> <?php
echo $op_basket; // will show either basket/basket_b2c.html.php or basket/basket_b2b.html 
?></div>
<div class="coupon_right" <?php 
if (empty($cl)) {
  echo ' style="float: right;" '; 
}
 ?> >
<?php 
echo $op_coupon; 
?>
</div>

<?php
if (empty($no_continue_link) && (!empty($continue_link)) && ($continue_link != '//')) { 
?>
<div class="continue_and_coupon">
<div class="continue_left">
<?php

$cl = true;  ?>
<div class="continue_shopping2"><a href="<?php echo $continue_link ?>" class="continue_link2"><?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING') ?></a></div>
<?php 
 
?>
</div>

</div>
<?php 
}
?>

<?php
 // will show coupon if enabled from common/couponField.tpl.php with corrected width to size
echo $html_in_between; // from configuration file.. if you don't want it, just comment it or put any html here to explain how should a customer use your cart, update quantity and so on

if (!empty($checkoutAdvertises)) {
?>
<div id="checkout-advertise-box">
		<?php
		if (!empty($checkoutAdvertises)) {
			foreach ($checkoutAdvertises as $checkoutAdvertise) {
				?>
				<div class="checkout-advertise">
					<?php echo $checkoutAdvertise; ?>
				</div>
				<?php
			}
		}
		?>
	</div>
<?php 
}

?>

<?php if (!empty($paypal_express_button)) { ?>
<div id="op_paypal_express" style="float: right; clear: both; width: 100%; padding-top: 10px;">
 <?php echo $paypal_express_button; ?>
</div>
<?php } ?>
<?php if (!empty($google_checkout_button)) { ?>
<div id="op_google_checkout" style="float: right; clear: both; width: 100%; padding-top: 10px;">
 <?php echo $google_checkout_button;  // will load google checkout button if you have powersellersunite.com/googlecheckout installed
 ?>
</div>
<?php } ?>


<!-- main onepage div, set to hidden and will reveal after javascript test -->
<div <?php if (empty($no_jscheck) || (!defined("_MIN_POV_REACHED"))) echo 'style="display: none;"'; ?> id="onepage_main_div">
<br style="clear: both;" />
<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-donotvalidate" novalidate="novalidate">

<?php

$login_is_default = false; 



?>

<!-- login box -->
<div class="left_checkout" style="width: 100%; clear: both;">
	<div <?php
	if (empty($registration_html)) echo 'style="display: none;" '; 
	?> >
	<ul id="vmtabs" class="shadetabs">
		<li class="selected"><a  <?php if (!$login_is_default) echo ' class="selected" '; ?> href="#" rel="registertab" id="atab2" onclick="javascript: return tabClick('atab2');"><?php echo JText::_('COM_VIRTUEMART_REGISTER') ?></a></li>
		<?php
		if (empty($no_login_in_template) && (VM_REGISTRATION_TYPE != 'NO_REGISTRATION')) { ?>
		<li><a  <?php if ($login_is_default) echo ' class="selected" '; ?>  href="#" rel="logintab" id="atab1" onclick="javascript: return tabClick('atab1');"><?php echo JText::_('COM_VIRTUEMART_LOGIN'); ?></a></li>
		<?php } ?>
	</ul>
</div>	
</div>
	<div class="left_checkout" style="width: 100%; clear: both;"><div>
	<div class="vmTabContent"  <?php 
	
	if (empty($registration_html) and (!empty($no_login_in_template))) echo ' style="display: none;" '; 
	
	?> >
	<div class="vmTabSub" >
	<div class="vmTabSubInner">
	<strong><?php echo JText::_('COM_ONEPAGE_REGISTER_OR_LOG_IN'); ?></strong>
	</div>	
	</div>	
	
	<div class="vmTabContentInner" id="tabscontent">
	<?php if (empty($no_login_in_template) && (VM_REGISTRATION_TYPE != 'NO_REGISTRATION')) { 
	
	?>
		<div id="logintab" class="tabcontent3" <?php if (!$login_is_default) echo ' style="display: none;" '; else echo ' style="display: block;" '; ?> >
		  <fieldset>
	      <div class="wrap_login" style="float: none;">
	    			<div class="formLabel">
			   <label for="username_login"><?php echo JText::_('COM_VIRTUEMART_USERNAME'); ?>:</label>
			</div>
			<div class="formField">
			  <input type="text" id="username_login" name="username_login" class="inputbox" size="20" autocomplete="off" />
			</div>
			<div class="formLabel">
			 <label for="passwd_login"><?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1') ?>:</label> 
			</div>
			<div class="formField">
				<input type="password" id="passwd_login" name="<?php 
				if ((version_compare(JVERSION,'1.7.0','ge')) || (version_compare(JVERSION,'2.5.0','ge'))) echo 'password';
				else echo 'passwd'; 
				?>" class="inputbox" size="20" onkeypress="return submitenter(this,event)"  autocomplete="off" />
			</div>
	
	<?php if (JPluginHelper::isEnabled('system', 'remember')) { ?>
	
	<div class="formLabel">	<label for="remember_login"><?php echo JText::_('JGLOBAL_REMEMBER_ME'); ?></label></div>
	<div class="formField">
	<input type="checkbox" name="remember" id="remember_login" value="yes" checked="checked" />
	</div>
	<?php  } else { ?>
	<input type="hidden" name="remember" value="yes" />
	<?php } ?>
	<div style="width: 100%; margin-left: 0; margin-right:0; padding-left:0; padding-right:0; clear: both;" >
	<div style="text-align: center; margin-right: auto; margin-left: auto;">
	<input type="button" name="LoginSubmit" class="buttonopc" value="<?php echo JText::_('COM_VIRTUEMART_LOGIN'); ?>" onclick="javascript: return op_login();"/>
	</div>
	
	<p style="text-align: center; margin-right: auto; margin-left: auto; padding:0; margin-top:0;">
	(<a title="<?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?>" href="<?php echo $lostPwUrl =  JRoute::_( 'index.php?option='.$comUserOption.'&view=reset' ); ?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD');  ?></a>)
	</p>

	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="<?php echo OPCUtility::getToken(); ?>" value="1" />
	</div>

		</div>	
	 </fieldset>
	</div>
	<?php } // end of login 
	?>
	
	
		<div id="registertab" class="tabcontent3" <?php if ($login_is_default) echo ' style="display: none;" '; else echo ' style="display: block" ';  ?> >
		<fieldset><legend class="sectiontableheader"><?php echo JText::_('COM_VIRTUEMART_YOUR_ACCOUNT_DETAILS');  ?></legend>
		<?php	echo $registration_html; ?>
		</fieldset>
		</div>
		
		
	  </div>
	</div>
	</div>
<!-- user registration and fields -->
<div class="tabcontent3">
<h3><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?></h3>
<fieldset>
<?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>
</fieldset></div>
<!-- end user registration and fields -->


</div>


<!-- end show TOS and checkbox before button -->

<br style="clear: both;">
<div>


</div>
<!-- end of customer note -->

<!-- show total amount at the bottom of checkout and payment information, don't change ids as javascript will not find them and OPC will not function -->

<div id="onepage_info_above_button">
<div id="onepage_total_inc_sh" style="display: none;">
<?php
/*
 content of next divs will be changed by javascript, please don't change it's id, you may freely format it and if you add any content of txt fields it will not be overwritten by javascript 
*/
?>
<div id="totalam">
<div class="bottomtotals dynamic_lines_bottom" id="tt_order_subtotal_div"><span id="tt_order_subtotal_txt" class="bottom_totals_txt"></span><span id="tt_order_subtotal" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_order_payment_discount_before_div"><span id="tt_order_payment_discount_before_txt" class="bottom_totals_txt"></span><span class="bottom_totals" id="tt_order_payment_discount_before"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_order_discount_before_div"><span id="tt_order_discount_before_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_before" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_shipping_rate_div"><span id="tt_shipping_rate_txt" class="bottom_totals_txt"></span><span id="tt_shipping_rate" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_shipping_tax_div"><span id="tt_shipping_tax_txt" class="bottom_totals_txt"></span><span id="tt_shipping_tax" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_tax_total_0_div"><span id="tt_tax_total_0_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_0" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_tax_total_1_div"><span id="tt_tax_total_1_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_1" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_tax_total_2_div"><span id="tt_tax_total_2_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_2" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_tax_total_3_div"><span id="tt_tax_total_3_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_3" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_tax_total_4_div"><span id="tt_tax_total_4_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_4" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_order_payment_discount_after_div"><span id="tt_order_payment_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_payment_discount_after" class="bottom_totals"></span><br class="op_clear"/></div>
<div class="bottomtotals dynamic_lines_bottom" id="tt_order_discount_after_div"><span id="tt_order_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_after" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_genericwrapper_bottom" class="bottomtotals dynamic_lines_bottom" style="display: none;"><span class="bottom_totals_txt dynamic_col1_bottom">{dynamic_name}</span><span class="bottom_totals dynamic_col2_bottom">{dynamic_value}</span><br class="op_clear"/></div>
<div id="tt_total_div" class="bottomtotals dynamic_lines_bottom"><span id="tt_total_txt" class="bottom_totals_txt"></span><span id="tt_total" class="bottom_totals"></span><br class="op_clear"/></div>
</div>
<?php 
/*
* END of order total at the bottom
*/
?>
</div>
<?php if ($tos_required) {  
 include(__DIR__.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'tos.php'); 
} 
echo $italian_checkbox;
?>
<div class="opc_captcha"><?php echo $captcha;  ?></div>
<!-- content of next div will be changed by javascript, please don't change it's id -->
<div id="payment_info"></div>
<!-- end of total amount and payment info -->
<!-- submit button -->
<div id="onepage_submit_section" class="newclass">
<input type="submit" class="buttonopc" value="<?php 
$txt = JText::_('COM_ONEPAGE_CREATEACCOUNT');
$txt = str_replace('"', '\"', $txt); 
echo $txt; 
?>" <?php echo $op_onclick?> id="confirmbtn" />
</div>
<br style="clear: both;"/>
</div>
<!-- end of submit button -->
</form>
<!-- end of checkout form -->
<!-- end of main onepage div, set to hidden and will reveal after javascript test -->
</div>
<div id="tracking_div"></div>

<script>
 if (typeof pay_btn != 'undefined')
 {	 
   pay_btn['default'] = '<?php echo $txt; ?>';
 }
</script>
 
