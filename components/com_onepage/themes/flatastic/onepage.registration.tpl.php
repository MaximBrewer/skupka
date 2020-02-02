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
global $Itemid; 


 
//joomore hacked;
global $jmshortcode;
if (!empty($jmshortcode) && (is_object($jmshortcode)))
$lang = $jmshortcode->shortcode;


if (!empty($newitemid))
$Itemid = $newitemid;

if (empty($registration_html)) $no_login_in_template = true; 

echo $intro_article; 

?>
<div id="top_basket_wrapper">
<?php
echo $op_basket; // will show either basket/basket_b2c.html.php or basket/basket_b2b.html 
echo $html_in_between; // from configuration file.. if you don't want it, just comment it or put any html here to explain how should a customer use your cart, update quantity and so on
echo $google_checkout_button; // will load google checkout button if you have powersellersunite.com/googlecheckout installed


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

if (!empty($paypal_express_button)) { ?>
<div id="op_paypal_express" style="float: right; clear: both; width: 100%; padding-top: 10px;">
 <?php echo $paypal_express_button; ?>
</div>
<?php } 


?>
</div>
<?php

?>
<!-- main onepage div, set to hidden and will reveal after javascript test -->
<div <?php if (empty($no_jscheck) || (!defined("_MIN_POV_REACHED"))) echo 'style="display: none;"'; ?> id="onepage_main_div" class="cart-view">

<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-validate">

<!-- login box -->
<?php 
if (!empty($no_login_in_template))  {
 echo '<div style="display: none;">';
}
?>

<div class="op_inside loginsection login-box">
<ul id="tab_selector">
 <li  id="op_login_btn" onclick="javascript: return tabClick('logintab', 'registertab', 'op_login_btn', 'op_register_btn');"><?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?></li>
 
 <li  id="op_register_btn" class="active" onclick="javascript: return tabClick('registertab', 'logintab', 'op_register_btn', 'op_login_btn');" ><?php echo OPCLang::_('COM_VIRTUEMART_REGISTER') ?></li>
</ul>

								    <div id="registertab" class="tabs">
									 <fieldset>
									  <?php	echo $registration_html; ?>
									 </fieldset>
									</div>
									<div id="logintab" class="tabs" style="display: none;">
                                     <fieldset>
                                    	<h3 class="module-title"><span><span><?php echo JText::_('DR_VIRTUEMART_CART_TITLE'); ?></span></span></h3>
                                    <div class="width100">	
        <div class="userdata">
		<div id="form-login-username" class="control-group floatleft width50"> 
			<div class="controls">
				<div class="input formField2">
					<label for="modlgn-username" class="label"><?php echo JText::_('COM_VIRTUEMART_USERNAME'); ?></label>
                    <div class="clear"></div>
                   <input type="text" id="username_login" name="username_login" class="inputbox" size="20" autocomplete="off"  />
                     <div class="clear"></div>
                    <a href="<?php echo JRoute::_('index.php?option='.$comUserOption.'&view=remind'); ?>" class="remind" ><?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_USERNAME'); ?></a>
				</div>
			</div>
            
		</div>
		<div id="form-login-password" class="control-group floatleft width50">
			<div class="controls">
				<div class="input formField">
					<label for="modlgn-passwd" class="label"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
                    <div class="clear"></div>
                    <input type="password" id="passwd_login"  name="<?php 
				if ((version_compare(JVERSION,'1.7.0','ge')) || (version_compare(JVERSION,'2.5.0','ge'))) echo 'password';
				else echo 'passwd'; 
				?>" class="inputbox" size="20" onkeypress="return submitenter(this,event)"  autocomplete="off" />
                     <div class="clear"></div>
                    <a href="<?php echo JRoute::_('index.php?option='.$comUserOption.'&view=reset'); ?>" class="reset"><?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?></a>
				</div>
			</div>
		</div>
        <div class="clear"></div>
		<div class="width100 remember">
            <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
            <input  style="float:left;" type="checkbox" name="remember" id="remember_login" class="rememberinputbox" value="yes" checked="checked" />
            <label style="float:left; margin-left:10px;" class="control-label"  for="remember_login"><?php echo $remember_me = VmConfig::isJ15()? OPCLang::_('Remember me') : OPCLang::_('JGLOBAL_REMEMBER_ME'); ?></label>
             <div class="clear"></div>
         	  <?php else : ?>
            <input type="hidden" name="remember" value="yes" />
			<?php endif; ?>
			</div>
            <div class="clear"></div>
            <div id="form-login-submit">
			<div class="controls">
				<input type="button" name="LoginSubmit" class="button" value="<?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?>" onclick="javascript: return op_login();"/>
			</div>
		</div>
			

    	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
		<input type="hidden" name="<?php echo OPCUtility::getToken(); ?>" value="1" />


	</div>
    </div>
		<div class="clear"></div>						
			
</fieldset>

									</div>
                                    </div>
<?php
if (!empty($no_login_in_template))  {
 echo '</div>';
}
?>

 <div class="clear"></div>
 <div class="cart-view">
		<h3 class="module-title"><span><span><?php echo OPCLang::_('DR_VIRTUEMART_CART_BILLING'); ?></span></span></h3>
		<div class="billing-box after">
			<div class="billto-shipto">
	<div class=" floatleft" style="width: 100%;">
    <div class="text-indent" id="bill_to_section">

		<span class="font"><span class="vmicon vm2-billto-icon"></span>
		<?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?></span>
		<?php // Output Bill To Address ?>
		<div class="output-billto">
		<?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>
		<div class="clear"></div>
		</div>
        </div>
	</div>

	
	<div class="clear"></div>
</div>
		</div>
</div>
 <div class="clear"></div>
<!-- end shipping address info -->


<!-- payment method -->


<!-- end payment method -->

<!-- customer note box -->

<?php
	
if($show_full_tos)
{
?>

				                                                   	
<!-- remove this section if you have 'must agree to tos' disabled' -->


<!-- show full TOS -->
	
<!-- end of full tos -->

<?php 

{
?>
 <div class="clear"></div>
<div class="op_inside cart-view custoterms">
                             <h3 class="module-title"><span><span><?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?></span></span></h3>
                        	<div class="op_rounded_content">
								<?php 
                                    echo $tos_con;
                                ?>
								<!-- end shipping methodd -->
							</div>
</div>



<?php 
}
}
?>
<div id="checkoutForm">
<?php
if ($tos_required)
{

?>
	<div id="agreed_div" class="formLabel2 " style="width: 80%;">
	

<input value="1" type="checkbox" id="agreed_field" name="tosAccepted" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?> class="terms-of-service"  required="required" autocomplete="off" />
					<label for="agreed_field"><?php echo OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); 
					if (!empty($tos_link))
					{
					?><a target="_blank" href="<?php echo $tos_link; ?>" onclick="javascript: return op_openlink(this); " >(<?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?>)</a><?php } ?></label>
				
		
	</div>



<?php
}


?>

<div id="agreed_div2" class="formLabel2 " style="width: 80%;">
<?php

echo $privacy_checkbox; 
?>
</div>
<div id="agreed_div3" class="formLabel2 " style="width: 80%;">
<?php
echo $acymailing_checkbox; 

?>
</div>
</div>
<!-- end of customer note -->
 <div class="clear"></div>
<?php echo $captcha; ?>	
<?php $txt = JText::_('COM_ONEPAGE_CREATEACCOUNT'); ?>
 <div style="float: left; clear: both;">
	<button id="confirmbtn_button" class="vm-button-correct" type="submit" autocomplete="off" onclick="<?php echo $onsubmit; ?>" ><span><?php echo OPCLang::_('COM_ONEPAGE_CREATEACCOUNT') ?></span></button>
 </div>

<!-- end of tricks -->


</form>
<!-- end of checkout form -->
<!-- end of main onepage div, set to hidden and will reveal after javascript test -->
</div>
<div id="tracking_div"></div>




