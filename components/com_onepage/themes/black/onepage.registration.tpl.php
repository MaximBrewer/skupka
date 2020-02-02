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


?>

<div <?php if (empty($no_jscheck) || (!defined("_MIN_POV_REACHED"))) echo 'style="display: none;"'; ?> id="onepage_main_div">


<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-valida2te" novalidate="novalidate">

<div id="full_checkout">

<div class="continue_and_coupon">


</div>
<?php 
 // will show coupon if enabled from common/couponField.tpl.php with corrected width to size
echo $html_in_between; // from configuration file.. if you don't want it, just comment it or put any html here to explain how should a customer use your cart, update quantity and so on

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
<?php } 

/*
// stAn: print coupon code at any position:
 $coupon_text = $cart->couponCode ? OPCLang::_('COM_VIRTUEMART_COUPON_CODE_CHANGE') : OPCLang::_('COM_VIRTUEMART_COUPON_CODE_ENTER');
			  $vars = array('coupon_text'=> $coupon_text, 
			  'coupon_display'=>''); 
if (!class_exists('OPCrenderer'))
 require (JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
 $renderer = OPCrenderer::getInstance(); 			  
 $op_coupon_ajax = $renderer->fetch($this, 'couponField_ajax', $vars); 
 $op_coupon_ajax = str_replace('type="button', 'onclick="return Onepage.setCouponAjax(this);" type="button', $op_coupon_ajax); 
echo $op_coupon_ajax;
*/
?>


<!-- main onepage div, set to hidden and will reveal after javascript test -->



<!-- login box -->

	<div style="display: none;">
	
	
	<ul id="vmtabs" class="shadetabs">
	<?php
	
	if (empty($no_login_in_template) && (VM_REGISTRATION_TYPE != 'NO_REGISTRATION')) { ?>
	<li><a  href="#" rel="logintab" id="atab1" onclick="javascript: return tabClick('atab1');"><?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?></a></li>
	<?php } ?>
	<li class="selected"><a class="selected" href="#" rel="registertab" id="atab2" onclick="javascript: return tabClick('atab2');"><?php echo OPCLang::_('COM_VIRTUEMART_REGISTER') ?></a></li>
	</ul>
	
	<div class="vmTabSub">
	<div class="vmTabSubInner">
	<strong><?php echo OPCLang::_('COM_ONEPAGE_REGISTER_OR_LOG_IN'); ?></strong>
	</div>	
	</div>
	
	</div>
	
	
	<div class="left_section">
	<div class="vmTabContent">
		
	
	<div class="vmTabContentInner" id="tabscontent">
	<?php if (empty($no_login_in_template) && (VM_REGISTRATION_TYPE != 'NO_REGISTRATION')) { ?>
		<div id="logintab" class="tabcontent3" style="display: none;">
		 
	      <div class="wrap_login" style="float: none;">
	    		
			<div class="formField">
			  <input type="text" id="username_login" name="username_login" class="inputbox" size="20" autocomplete="off" title="<?php echo OPCLang::_('COM_VIRTUEMART_USERNAME');?>" placeholder="<?php echo OPCLang::_('COM_VIRTUEMART_USERNAME');?>" />
			</div>
			<div class="formField">
				<input type="password" id="passwd_login" name="<?php 
				if ((version_compare(JVERSION,'1.7.0','ge')) || (version_compare(JVERSION,'2.5.0','ge'))) echo 'password';
				else echo 'passwd'; 
				?>" class="inputbox" size="20" onkeypress="return submitenter(this,event)"  autocomplete="off" title="<?php echo OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1');?>" placeholder="<?php echo OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1');?>"/>
			</div>
	
	<?php if (JPluginHelper::isEnabled('system', 'remember')) { ?>
	
	<div class="formLabel">	</div>
	<div class="formField">
	<input type="checkbox" name="remember" id="remember_login" value="yes" checked="checked" />
	<label for="remember_login"><?php echo OPCLang::_('JGLOBAL_REMEMBER_ME'); ?></label>
	</div>
	<?php  } else { ?>
	<input type="hidden" name="remember" value="yes" />
	<?php } ?>
	<div style="width: 100%; margin-left: 0; margin-right:0; padding-left:0; padding-right:0; clear: both;" >
	<div style="text-align: center; margin-right: auto; margin-left: auto;">
	<input type="button" name="LoginSubmit" class="buttonopc" value="<?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?>" onclick="javascript: return op_login();"/>
	</div>
	
	<p style="text-align: center; margin-right: auto; margin-left: auto; padding:0; margin-top:0;">
	(<a title="<?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?>" href="<?php echo $lostPwUrl =  JRoute::_( 'index.php?option='.$comUserOption.'&view=reset' ); ?>"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD');  ?></a>)
	</p>

	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="<?php echo OPCUtility::getToken(); ?>" value="1" />
	</div>

		</div>	
	 
	</div>
	<?php } // end of login 
	?>
	
	
		<div id="registertab" class="tabcontent3" style="display: block">
		<h2 class="bandBoxStyleHeader delim0 bandBoxStyle fieldsetnum_0"><?php echo OPCLang::_('COM_VIRTUEMART_YOUR_ACCOUNT_DETAILS'); ?></h2>
		
		<?php	echo $registration_html; ?>
		
		</div>
		
		
	  </div>
	</div>
<!-- user registration and fields -->
<div class="tabcontent3">
<?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>



<!-- customer note box -->

<?php 

									$comment = OPCLang::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									$comment = OPCLang::_('COM_VIRTUEMART_COMMENT'); 
									


?>





</div>
<!-- end user registration and fields -->
<!-- shipping address info -->
<?php if ($no_shipto != '1') { ?>
<div id="ship_to_section">

<h2 class="bandBoxStyleHeader"><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); // this is from conf file, it is a title for "Shipping Address" ?></h2>
<label <?php if (!empty($only_one_shipping_address_hidden)) echo ' style="display: none;" '; ?> >
								<?php if (empty($only_one_shipping_address_hidden)) { ?>
								<input type="checkbox" id="sachone" <?php if (!empty($op_shipto_opened)) echo ' checked="checked" '; ?> name="sa" value="adresaina" onkeypress="showSA(sa, 'idsa');" onclick="javascript: showSA(sa, 'idsa');" autocomplete="off" />
								<?php } else { ?>
								<input type="hidden" id="sachone" name="sa" value="adresaina" onkeypress="showSA(sa, 'idsa');" onclick="javascript: showSA(sa, 'idsa');" autocomplete="off" />								
								<?php } ?>
								<?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL');  ?></label>
								<div id="idsa" style="<?php if (empty($op_shipto_opened)) echo 'display: none;'; ?>">
<?php echo $op_shipto; // will list shipping user fields from ps_userfield::listUserFields with modification of ids and javascripts ?>
</div>
</div>
<?php } ?>

<?php
if (!empty($delivery_date)) { 
echo $delivery_date; 
 ?>

<?php } ?>	



<!-- show TOS and checkbox before button -->
<?php
	
		$agreement_txt = OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS');
	
if ($tos_required)
{
?>

<!-- remove this section if you have 'must agree to tos' disabled' -->


	<div id="agreed_div" class=" op_clearfix">
		
					<label for="agreed_field" class="bottom_c">
					<input type="checkbox" id="agreed_field" name="agreed" value="1" class="inputboxchecckbox" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?> class="terms-of-service"  required="required" autocomplete="off" />
					<?php if (!empty($tos_link))
					JHTMLOPC::_('behavior.modal', 'a.opcmodal'); 
					?><span class="tos_label_text"><?php echo OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); ?></span>
					<a target="_blank" rel="{handler: 'iframe', size: {x: 500, y: 400}}" class="opcmodal"  href="<?php echo $tos_link; ?>" onclick="javascript: return Onepage.op_openlink(this); ">
					 (<?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?>)
					</a>
					</label>
		
	</div>
<?php echo $italian_checkbox; ?>

<?php
}

?>

<!-- end show TOS and checkbox before button -->



<!-- show total amount at the bottom of checkout and payment information, don't change ids as javascript will not find them and OPC will not function -->
<div id="onepage_info_above_button">
<div id="onepage_total_inc_sh">
<?php
/*
 content of next divs will be changed by javascript, please don't change it's id, you may freely format it and if you add any content of txt fields it will not be overwritten by javascript 
*/
?>
<?php 
/*
* END of order total at the bottom
*/
?>
</div>
<div><br /></div>
<!-- content of next div will be changed by javascript, please don't change it's id -->

<!-- end of total amount and payment info -->
<!-- submit button -->
<div class="op_clearfix captcha"><?php echo $captcha; ?></div>
<div class="op_clearfix">
<div class="return_to_cart">&nbsp;</div>

<div id="payment_info"></div>
<div class="payment_info">&nbsp;</div>

<div id="onepage_submit_section" class="newclass checkout_button_wrap ">
  <?php $txt = JText::_('COM_ONEPAGE_CREATEACCOUNT'); ?>
<input type="submit" class="buttonopc myGreenBackground opcbutton " value="<?php echo str_replace('"', '\"', $txt); ?>" <?php echo $op_onclick?> id="confirmbtn" />
</div>
</div>

</div>
<div class="right_section">

<!-- end shipping address info -->
<!-- shipping methodd -->


<div class="payment_and_totals">
<div class="my_relative">
<div class="opc_payment_wrapper">
 <!-- payment method -->
<?php if (!empty($op_payment) )
{
?>
<div id="payment_top_wrapper" <?php
if (!empty($force_hide_payment)) {
 echo ' style="display: none;" '; 
 
 }
 ?> >

<h2 class="bandBoxStyleHeader"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL') ?></h2>
<?php echo $op_payment; ?>

</div>
<?php 

} 
?>

<?php
if (!empty($payment_inside_basket))
{
?>	<div id="basket_payment" class="vmsectiontableentry1">

		<div id="payment_inside_basket_label"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL'); ?> </div>
		<div id="payment_inside_basket"><?php echo $payment_select; ?></div>
		<div id='payment_inside_basket_cost'></div>
	</div>	
<?php


}



?>

</div>


<!-- end payment method -->


</div>
</div>

<!-- end shipping methodd -->

<br style="clear: both;"/>
</div>


<div class="op_clearfix">
<?php if ($show_full_tos) { ?>
<h2 class="bandBoxStyleHeader "><?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); // change this to 'Agreement' ?></h2>
<!-- show full TOS -->
	
<?php echo $tos_con; ?>
<!-- end of full tos -->
<?php } ?>
</div>

</div>
<!-- end of submit button -->
</div>
</form>


<!-- end of checkout form -->
<!-- end of main onepage div, set to hidden and will reveal after javascript test -->
</div>
<div id="tracking_div"></div>


<br style="clear: both; float: none;" />
<br style="clear: both; float: left;" />

