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
if (!empty($newitemid))
$Itemid = $newitemid;

echo $intro_article; 


?>

<div class="registration_wrapper">
<div class="dob0">
<!-- main onepage div, set to hidden and will reveal after javascript test -->

<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-valid2ate" novalidate="novalidate">
<div class="dob1" id="dob1" style="width:100%;">
<div class="op_inner">
<?php 

$iter = 0;


if (empty($no_login_in_template))
{

?>
   
<?php
 if (VM_REGISTRATION_TYPE != 'NO_REGISTRATION') 
 {
 
?>

<!-- login box -->

	   
<div id="tab_selector">
<fieldset>
 <input name="regtypesel" type="radio"  id="op_login_btn" onclick="javascript: return t_unhide('logintab');"  style="border: none;" class="styled" /><label for="op_login_btn" class="radio" id="op_round_and_separator"><?php echo OPCLang::_('COM_ONEPAGE_SHOW_LOGIN'); ?></label>
 <br style="clear: both;"/>
 <input class="styled" name="regtypesel"  type="radio" checked="checked" id="op_register_btn" onclick="javascript: return t_hideFx('logintab');" style="border: none;" /><label for="op_register_btn" class="radio"><?php echo OPCLang::_('COM_ONEPAGE_REGISTER_AND_CHECKOUT'); ?></label>
</fieldset>
</div>

                        	
								
								<div>
								<div>
								    
									  
									
									<div id="logintab" style="display: none;">
									    			
			<div>
			  <div class="before_input"></div><div class="middle_input">
				<input type="text" id="username_login" name="username_login" value="" class="inputbox" size="20" onfocus="inputclear(this)" autocomplete="off" />
				<?php
				echo '<input type="hidden" id="saved_username_login_field" name="savedtitle" value="'. OPCLang::_('COM_VIRTUEMART_USERNAME') .'" />';
				echo '<label for="username_login" id="label_username_login" class="userfields">'.OPCLang::_('COM_VIRTUEMART_USERNAME').'</label>';				
				?>
				<div class="after_input">&nbsp;</div></div>
			</div>
			
			<div class="formField">
				<div class="before_input"></div><div class="middle_input">
				<input type="password" id="passwd_login" name="<?php 
				if ((version_compare(JVERSION,'1.7.0','ge')) || (version_compare(JVERSION,'2.5.0','ge'))) echo 'password';
				else echo 'passwd'; 
				?>" value="" class="inputbox" size="20" onkeypress="return submitenter(this,event)" onfocus="inputclear(this)" autocomplete="off" />
				<?php
				echo '<input type="hidden" id="saved_password_field" name="savedtitle" value="'. OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1') .'" />';
				echo '<label for="passwd_login" id="label_passwd_login" class="userfields">'.OPCLang::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1').'</label>';				
				?>

				<div class="after_input">&nbsp;</div></div>
			</div>
			<br style="clear: both;"/>
	<?php if( @VM_SHOW_REMEMBER_ME_BOX == '1' ) : ?>

	<div>	<label for="remember_login"><?php echo $remember_me = VmConfig::isJ15()? OPCLang::_('Remember me') : OPCLang::_('JGLOBAL_REMEMBER_ME'); ?></label></div>
	<div>
	<input type="checkbox" name="remember" id="remember_login" value="yes" checked="checked" />
	</div>
	
	<?php else : ?>
	<input type="hidden" name="remember" value="yes" />
	<?php endif; ?>
	<div style="width: 100%;">
	<span style="float: left;">
	(<a title="<?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD');; ?>" href="<?php echo $lostPwUrl =  JRoute::_( 'index.php?option='.$comUserOption.'&view=reset' ); ?>"><?php echo OPCLang::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?></a>)
	</span>
	<input type="button" name="LoginSubmit" class="op_login_button" value="<?php echo OPCLang::_('COM_VIRTUEMART_LOGIN'); ?>" onclick="javascript: return op_login();"/>
	
	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="<?php echo OPCUtility::getToken(); ?>" value="1" />
	<br style="clear: both;"/>
	</div>
	
									 
									</div>
								</div>
								
								</div>
								
								

							
							
<?php 
}
?>  
	 


<!-- user registration and fields -->

	
	   
                             
<?php	

}
if (!empty($registration_html))
{
?>
<h4><?php $iter++; echo $iter.'. ';?><?php echo OPCLang::_('COM_VIRTUEMART_YOUR_ACCOUNT_REG') ?> </h4>
<?php
echo $registration_html; 
}
?>
<h4><?php $iter++; echo $iter.'. ';?><?php echo OPCLang::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL') ?> </h4>
<?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>
								
<br style="clear: both;"/>

							
	  
	 




<!-- end user registration and fields --> 
<!-- shipping address info -->

</div>






<!-- customer note box -->
<!-- end of customer note -->



<h4><?php $iter++; echo $iter.'. '; echo strip_tags(OPCLang::_('COM_VIRTUEMART_REGISTER')); ?></h4>
                           
	   

                        	                        	 
                        	
                        	 <div id="rbsubmit" style="width: 100%; float: right;">
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
 
<!-- content of next div will be changed by javascript, please don't change it's id -->
 
<!-- end of total amount and payment info -->
<!-- submit button -->

 
 <!-- show TOS and checkbox before button -->
<?php
	if(OPCLang::_('COM_VIRTUEMART_AGREEMENT_TOS')){
		$agreement_txt = OPCLang::_('COM_VIRTUEMART_AGREEMENT_TOS');
	}



if ($show_full_tos) { ?>
<!-- show full TOS -->
	
<?php echo $tos_con; ?>
<!-- end of full tos -->
<?php } 
	
if ($tos_required)
{

{

?>
	<div id="agreed_div" class="formLabel fullwidth" style="text-align: left;">
	<div class="left_checkbox">
	<input value="1" type="checkbox" id="agreed_field"  name="tosAccepted" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?> class="terms-of-service" <?php if (VmConfig::get('agree_to_tos_onorder', 1)) echo ' required="required" '; ?> autocomplete="off" />
    </div>
	<div class="right_label">
					<label for="agreed_field"><?php echo OPCLang::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); 
					if (!empty($tos_link))
					{
					JHTMLOPC::_('behavior.modal', 'a.opcmodal'); 
					?><a target="_blank" rel="{handler: 'iframe', size: {x: 500, y: 400}}" class="opcmodal" href="<?php echo $tos_link; ?>" onclick="javascript: return op_openlink(this); " ><br />
					<?php 
					$text = OPCLang::_('COM_VIRTUEMART_CART_TOS'); 
					$text = trim($text); 
					if (!empty($text))
					{
					?>
					(<?php echo OPCLang::_('COM_VIRTUEMART_CART_TOS'); ?>)
					<?php 
					}
					?>
					</a><?php } ?></label>
		</div>		
		
	</div>
	<div class="formField" id="agreed_input">
	<?php
	$lang = JFactory::getLanguage();
	$language_tag = $lang->getTag();
	echo $italian_checkbox;
	?>
</div>


<?php
}

}
?>
<!-- end show TOS and checkbox before button -->


<br style="clear: both;"/>
</div>
<!-- end of submit button -->




                        	 </div>
                        	
                        	
                        	
                        	<div style="clear: both;"></div>
	  
	 


<div class="bottom_button">
 <div id="payment_info"></div>
	<button id="confirmbtn_button" type="submit" <?php echo $op_onclick ?>  ><h4 id="confirmbtn"><?php echo strip_tags(OPCLang::_('COM_VIRTUEMART_ORDER_REGISTER')); ?></h4></button>
 </div>

<!-- end of tricks -->
<?php
echo $captcha; 
?> 
</div>
</form>
<!-- end of checkout form -->
<!-- end of main onepage div, set to hidden and will reveal after javascript test -->
</div>
</div>
<div id="tracking_div"></div>

<script type="text/javascript">
addOpcTriggerer('callAfterRender', 'resetHeight()'); 
</script>
<br style="end_br" />

<br style="clear: both; float: none;" />
<br style="clear: both; float: left;" />