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
echo $op_coupon; // will show coupon if enabled from common/couponField.tpl.php with corrected width to size
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
<div <?php if (empty($no_jscheck) || (!defined("_MIN_POV_REACHED"))) echo 'style="display: none;"'; ?> id="onepage_main_div">

<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-valid2ate" novalidate >

<!-- login box -->
<?php 
if (!empty($no_login_in_template))  {
 echo '<div style="display: none;">';
}
?>

<div class="op_inside loginsection">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix">  
<div id="tab_selector">
 <button  id="op_login_btn" onclick="javascript: return Onepage.op_unhide('logintab', 'registertab', 'allcheckout_sections');"  type="button" style="border: none;"><span class="op_round" id="op_round_and_separator" ><span class="inspan" ><?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?></span></span></button>
 <button  id="op_register_btn" onclick="javascript: return Onepage.op_unhide2('registertab', 'allcheckout_sections', 'logintab');" style="border: none;" type="button"><span class="op_round" id="op_round_and_separator" ><span class="inspan" ><?php echo OPCLang::_('COM_VIRTUEMART_REGISTER') ?></span></span></button>
</div>

                        	<div class="op_rounded_content">
								<div style="padding-left: 10px;">
								<div>
								<div>
								    <div id="registertab">
									 <fieldset><legend><?php echo OPCLang::_('COM_VIRTUEMART_REGISTER'); ?></legend>
									  <?php	echo $registration_html; ?>
									 </fieldset>
									</div>
									<div id="logintab" style="display: none;">
									<fieldset><legend><?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?></legend>
			<?php 
			// css 3 mod: 
			if (false) { ?>
									    			<div class="formLabel">
			   <label for="username_login"><?php echo OPCLang::_('COM_VIRTUEMART_USERNAME'); ?>:</label>
			</div>
			<?php 
			}
			?>
			<div class="formField2">
			  <input type="text" id="username_login" name="username_login" class="inputbox" size="20" autocomplete="off" placeholder="<?php echo OPCLang::_('COM_VIRTUEMART_USERNAME'); ?>" />
			</div>
			<?php if (false) { ?> 
			<div class="formLabel">
			 <label for="passwd_login"><?php echo OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1') ?>:</label> 
			</div>
			<?php } ?>
			<div class="formField">
				<input type="password" id="passwd_login" placeholder="<?php echo OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1') ?>" name="<?php 
				if ((version_compare(JVERSION,'1.7.0','ge')) || (version_compare(JVERSION,'2.5.0','ge'))) echo 'password';
				else echo 'passwd'; 
				?>" class="inputbox" size="20" onkeypress="return Onepage.submitenter(this,event)"  autocomplete="off" />
			</div>
	<br />
	<br />
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<br />
	<div class="formLabel">	<label for="remember_login"><?php echo OPCLang::_('JGLOBAL_REMEMBER_ME'); ?></label></div>
	<div class="formField">
	<input type="checkbox" name="remember" id="remember_login" value="yes" checked="checked" />
	</div>
	<?php else : ?>
	<input type="hidden" name="remember" value="yes" />
	<?php endif; ?>
	<div class="formField" style="width: 60%; padding-left: 20%;">
	<span style="float: left; padding-right: 5%; padding-top: 5px;">
	(<a title="<?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?>" href="<?php echo $lostPwUrl =  JRoute::_( 'index.php?option='.$comUserOption.'&view=reset' ); ?>"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD');  ?></a>)
	</span>
	<input type="button" name="LoginSubmit" class="button" value="<?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?>" onclick="javascript: return Onepage.op_login();"/>

	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="<?php echo OPCUtility::getToken(); ?>" value="1" />
	</div>

									 </fieldset>
									</div>
								</div>
								</div>
								</div>
								<br style="clear: both;"/>

							</div>
							
	  </div>
	 </div></div></div></div>
</div>
</div>

<?php
if (!empty($no_login_in_template))  {
 echo '</div>';
}
?>
<div id="allcheckout_sections">
<!-- user registration and fields -->
<div class="bill_to_section op_inside op_userfields_style_<?php if (NO_SHIPTO == '1')  echo 'hundred'; else echo 'other'; ?>" id="bill_to_section" >
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix_1" style="width: 100%;">  
                              
                              
                                    <span class="col-module_header_color"><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?></span>                           		
                              
                        	
                        	<div class="op_rounded_content">
								<div>
								<div>
								<?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>
								</div>
								</div>
								<br style="clear: both;"/>

							</div>
	  </div>
	 </div></div></div></div>
</div>
</div>



<!-- end user registration and fields -->
<!-- shipping address info -->
<?php 
// stAn we disable ship to section only to unlogged users

if (NO_SHIPTO != '1') { ?>
<div class="shipto_section op_inside">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                              
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?></span>                           		
                                </span>
                                </span>
                             	</span>  
                        	
                        	<div class="op_rounded_content">
								<div>
								<div>
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
								</div>
								<br style="clear: both;"/>

							</div>
	  </div>
	 </div></div></div></div>
</div>
</div>

<?php }

 ?>
<!-- end shipping address info -->

<div class="shipping_method_section op_inside" <?php if ($no_shipping || ($shipping_inside_basket)) echo 'style="display: none;"'; ?>>
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                              
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL'); ?></span>                           		
                                </span>
                                </span>
                             	</span>  
                        	
                        	<div class="op_rounded_content">
								<!-- shipping methodd -->
								<div id="ajaxshipping" style="width: 90%;">
								<?php echo $shipping_method_html; // this prints all your shipping methods from checkout/list_shipping_methods.tpl.php ?>
								</div>
								<br style="clear: both;"/>
								<!-- end shipping methodd -->

							</div>
	  </div>
	 </div></div></div></div>
</div>
</div>

 <?php
 
if (!empty($delivery_date)) { 

echo $delivery_date; 
}
 ?>

<!-- payment method -->
<?php if (!empty($op_payment))
{

?>
<div id="payment_top_wrapper" <?php
if (!empty($force_hide_payment)) {
 echo ' style="display: none;" '; 
 
 }
 ?> >
<div class="payment_method_section op_inside">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                              
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php echo OPCLang::_('COM_VIRTUEMART_CART_PAYMENT'); ?></span>                           		
                                </span>
                                </span>
                             	</span>  
                        	
                        	<div class="op_rounded_content">


<?php echo $op_payment; ?>
<br style="clear: both;"/>
								<!-- end shipping methodd -->

							</div>
	  </div>
	 </div></div></div></div>
</div>
</div>
</div>



<?php 

} 
?>
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
<div class="op_inside">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                              
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?></span>                           		
                                </span>
                                </span>
                             	</span>  
                        	
                        	<div class="op_rounded_content">



<?php 

	echo $tos_con;
?>

<br style="clear: both;"/>
								<!-- end shipping methodd -->

							</div>
	  </div>
	 </div></div></div></div>
</div>
</div>
<?php 
}
}
?>
<!-- end of customer note -->

<div class="op_inside">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix_1" style="width: 100%;">  
                              
                            <div id="customer_note_block" >
							 <span id="customer_note_input" class="formField">
								<span class="col-module_header_color">Комментарии к заказу</span>
							   <textarea rows="3" cols="30" name="customer_comment" id="customer_note_field" ></textarea>
							
							 </span>
							 <br style="clear: both;" />
							 <div id="payment_info" style="padding-top; 20px;"></div>
                        	 </div>
                        	
                        	<div class="op_rounded_content">
                        	 <div>
                        	 
                        	 </div>
                        	 <div id="rbsubmit" >
                        	   <!-- show total amount at the bottom of checkout and payment information, don't change ids as javascript will not find them and OPC will not function -->

							   <div id="onepage_info_above_button">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="korzina_itogo_1_niz">Итого к оплате:</td>
    <td class="korzina_itogo_2_niz" align="right"><span id="tt_total" class="bottom_totals"></span></td>
  </tr>
</table>

<?php 
/*
* END of order total at the bottom
*/
?>
</div>
 
<!-- content of next div will be changed by javascript, please don't change it's id -->
 
<!-- end of total amount and payment info -->
<!-- submit button -->

 
 <!-- show TOS and checkbox before button -->
<?php

if ($tos_required)
{

?>
	<div id="agreed_div" class="formLabel2 agree_top_wrapper" >
	
<div class="agreed_input_wrapper">
<input value="1" type="checkbox" id="agreed_field" name="tosAccepted" <?php if (!empty($agree_checked)) echo '  '; ?> class="terms-of-service"  required="required" autocomplete="off" /></div>
					<div class="agreed_label_wrapper"><label for="agreed_field"><span class="label_text"><a href="/soglasie-na-obrabotku-personalnykh-dannykh.html" target="_blank">Согласен на обработку персональных данных</a></label>
					</div>
				
		
	</div>
	


<?php
}
echo $italian_checkbox; 
?>
<!-- end show TOS and checkbox before button -->

 
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><button id="confirmbtn_button" type="submit" autocomplete="off" <?php echo $op_onclick; ?> ><span class="op_round"><span id="confirmbtn" class="inspan" ><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU') ?></span></span></button></td>
  </tr>
</table>

 </div>
<br style="clear: both;"/>
</div>
<!-- end of submit button -->




               	  </div>
                        	
               	</div> 
                        	</div>
                        	<div style="clear: both;"></div>
<?php echo $captcha; ?>							
	   </div>
	</div></div></div></div>  


<!-- end of tricks -->


</form>
<!-- end of checkout form -->
<!-- end of main onepage div, set to hidden and will reveal after javascript test -->
</div>
<div id="tracking_div"></div>



<br style="clear: both; float: none;" />
<br style="clear: both; float: left;" />