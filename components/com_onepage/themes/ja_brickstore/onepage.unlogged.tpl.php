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

/*

$opt = array('msgList'=>array('warning'=>'<div id="opc_error_msg"></div>'), 'name'=>NULL, 'params'=>array(), 'content'=>NULL); 
$layout = new JLayoutFile('joomla.system.message', null, null); 
$html = $layout->render($opt); 
$html = str_replace('id="system-message"', 'id="system-message" style="display:none;"', $html); 
echo $html;
*/


?>





<div class="vm-cart-header-container">
	<div class="width50 floatleft vm-cart-header">
		<h1><?php echo vmText::_ ('COM_VIRTUEMART_CART_TITLE'); ?></h1>
		<div class="payments-signin-button" ></div>
	</div>
	<?php if (VmConfig::get ('oncheckout_show_steps', 1) && $this->checkout_task === 'confirm') {
		echo '<div class="checkoutStep" id="checkoutStep4">' . vmText::_ ('COM_VIRTUEMART_USER_FORM_CART_STEP4') . '</div>';
	} ?>
	<div class="width50 floatleft right vm-continue-shopping">
		<?php // Continue Shopping Button
		if (empty($no_continue_link) && (!empty($continue_link)) && ($continue_link != '//')) { 
			?><a href="<?php echo $continue_link ?>" class="continue_link"><?php echo OPCLang::_('COM_VIRTUEMART_CONTINUE_SHOPPING') ?></a><?php
		} ?>
	</div>
	<div class="clear"></div>
</div>


<?php


echo $intro_article; 
 ?>
 
 <div id="cart-view" class="cart-view">
 <?php
 $return_url = base64_decode($return_url); 
 echo shopFunctionsF::getLoginForm ($this->cart, FALSE,$return_url);
 
 
 
if (!empty($ajaxify_cart)) { ?>
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-donotvalidate" novalidate="novalidate">
<?php } 

?>
<div class="billto-shipto">
	<div class="width50 floatleft">

		<span><span class="vmicon vm2-billto-icon"></span>
			<?php echo vmText::_ ('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?></span>
		<?php // Output Bill To Address ?>
		<div class="output-billto">
		<?php echo $registration_html; ?>
		<?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>
		
			
			<div class="clear"></div>
		</div>

		<?php
		if($this->pointAddress){
			$this->pointAddress = 'required invalid';
		}

		?>
		
		
	</div>
<?php if ($no_shipto != '1') { ?>
	<div class="width50 floatleft">

		<span><span class="vmicon vm2-shipto-icon"></span>
			<?php echo vmText::_ ('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?></span>
		<?php // Output Bill To Address ?>
		<div class="output-shipto">
		<?php echo $op_shipto; // will list shipping user fields from ps_userfield::listUserFields with modification of ids and javascripts ?>
		
			
			<div class="clear"></div>
		</div>
		

	</div>
<?php } ?>
	<div class="clear"></div>
</div>


<div id="system-message-container">
			<div id="system-message" style="display:none;">
							<div class="alert alert-notice">
										<a class="close" data-dismiss="alert">×</a>

											<h4 class="alert-heading">Attentie</h4>
						<div>
							<div class="alert-message" id="opc_error_msgs"></div>
													</div>
									</div>
					</div>
	</div>

<?php






echo $op_basket; // will show either basket/basket_b2c.html.php or basket/basket_b2b.html 



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
<?php
$config = OPCconfig::getValue('theme_config', $selected_template, 0, false, false); 
$login_is_default = false; 
if (!empty($config) && (!empty($config->login_is_default))) $login_is_default = true; 








echo $op_userfields_cart; 



if (empty($is_registration_template)) {
?>

<fieldset class="vm-fieldset-customer-note">
		<div class="cart customer-note" title="">
		<span class="cart customer-note"><?php echo JText::_('COM_VIRTUEMART_CNOTES_CART'); ?>Aantekeningen en speciale verzoeken</span>

						<textarea id="customer_note_field" name="customer_note" cols="60" rows="1" class="inputbox" maxlength="2500"><?php 
						  if (!empty($cart->BT['tos'])) echo strip_tags($cart->BT['tos']); 
						  
						?></textarea>			</div>
	
	</fieldset>
<?php } ?>	
	
	<fieldset class="vm-fieldset-tos">
		<div class="cart tos" title="">
		<label for="agreed_field"><span class="cart tos"><?php echo JText::_('COM_VIRTUEMART_STORE_FORM_TOS'); ?></span></label>

						
						<input class="terms-of-service" type="checkbox" id="agreed_field" name="agreed" value="1">
						
						<div class="terms-of-service">
	<label >
		<a href="<?php echo $tos_link; ?>" class="terms-of-service" id="terms-of-service" rel="facebox" target="_blank">
			<span class="vmicon vm2-termsofservice-icon"></span>
			<?php echo JText::_('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED'); ?>		</a>
	</label>

	<div id="full-tos" style="display: none;">
		<h2><?php echo JText::_('COM_VIRTUEMART_STORE_FORM_TOS'); ?></h2>
				</div>
</div>
			</div>
	
	</fieldset>


	
<div class="right_checkout" style="<?php 
 //if (empty($no_login_in_template) && (VM_REGISTRATION_TYPE != 'NO_REGISTRATION')) echo 'margin-top: 26px;'; 
?>">


<?php if (!empty($third_address)) { ?>
<h3 class="shipping_h3"><?php echo OPCLang::_('COM_ONEPAGE_THIRD_ADDRESS'); ?></h3>
<fieldset class="other_address">
<?php echo $third_address; ?>
</fieldset>
<br style="clear: both;"/>
<?php } ?>
<!-- end shipping address info -->

<jdoc:include type="modules" name="opc_under_shipping_section" style="none" />
<?php
if (!empty($delivery_date)) { 
echo $delivery_date; 
 ?>

<?php } ?>	

<?php
if (!empty($checkbox_products)) {
	?><div class="op_inside checkbox_wrapper" style="padding-bottom: 10px;" ><h3 class="payment_h3"><?php echo OPCLang::_('COM_ONEPAGE_CHECKBOX_SECTION') ?></h3>
<fieldset>
<?php
	echo $checkbox_products; 
	?>
</fieldset>	&nbsp;
</div>
	<?php
}
?>

<?php 
echo $italian_checkbox;
?>

<div class="opc_captcha"><?php echo $captcha;  ?></div>
<!-- content of next div will be changed by javascript, please don't change it's id -->
<div id="payment_info"></div>
<!-- end of total amount and payment info -->
<!-- submit button -->

<div class="checkout-button-top" id="onepage_submit_section"> 
 <button <?php echo $op_onclick?> id="confirmbtn" type="submit" name="checkout" value="1" class="vm-button-correct" data-dynamic-update="1">
  <span><?php echo JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'); ?></span> 
 </button>
 
</div>

</div>
<!-- end of submit button -->
</div>
</form>

</div>
<!-- end of checkout form -->
<!-- end of main onepage div, set to hidden and will reveal after javascript test -->

<div id="tracking_div"></div>

