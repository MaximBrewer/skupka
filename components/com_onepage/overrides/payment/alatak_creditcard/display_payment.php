<?php  defined ('_JEXEC') or die();
/**
 * @version 2.5.0
 * @package VirtueMart
 * @subpackage Plugins - vmpayment
 * @author 		    Valérie Isaksen (www.alatak.net)
 * @copyright       Copyright (C) 2012-2015 Alatak.net. All rights reserved
 * @license		    gpl-2.0.txt
 *
 */
//NOTE: to customize html of this plugin, you can either move your display_payment.php into /components/com_onepage/themes/YOUR THEME/overrides/payment/alatak_creditcard/display_payment.php
// or remove this file and your Joomla override will be used instead
// opc_javascript is used to load script before ajax (i.e. if the default cart view for EU country does not display the credit card list, it still can get binded to javascript after US country is selected and payment method shown after ajax
$viewData['dynamic_update'] = false; 
ob_start(); 
if (file_exists(VMPATH_SITE.DS.'helpers'.DS.'vmtemplate.php')) { 
		if(!class_exists('VmTemplate')) require(VMPATH_SITE.DS.'helpers'.DS.'vmtemplate.php');
		$vmStyle = VmTemplate::loadVmTemplateStyle();
		$template = $vmStyle['template'];
}
else {
	$template = JFactory::getApplication('site')->getTemplate(); 
	
}

$templatePath = VMPATH_ROOT . DS . 'templates' . DS . $template . DS . 'html' . DS . 'vmpayment' . DS . 'alatak_creditcard' . DS . 'display_payment' . '.php';

if (file_exists($templatePath)) {
	include($templatePath); 
}
else {
 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'alatak_creditcard'.DIRECTORY_SEPARATOR.'alatak_creditcard'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'display_payment.php'))
 include(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'alatak_creditcard'.DIRECTORY_SEPARATOR.'alatak_creditcard'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'display_payment.php'); 
}
$x = ob_get_clean(); 
echo $x; 

echo vmJsApi::writeJs(); 


if (empty($x)) {
?>
<div id="ccoffline_form">

	<ul >

		<li>
			<label for="card_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_CCNUM') ?></label>
			<input type="text" name="card_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" value="<?php echo $viewData['card_number']; ?>" id="card_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" placeholder="1234 5678 9012 3456" class="card_number" >
			<input type="hidden" name="card_type_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="card_type_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" >
		</li>
		<li class="vertical">
			<ul>
				<li>
					<label for="expiry_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_EXDATE') ?></label>
					<input type="text" name="expiry_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="expiry_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" value="<?php echo $viewData['expiry_date']; ?>" maxlength="5" placeholder="mm/yy" class="expiry_date">
				</li>

				<li>
					<label for="cvv_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_CVV') ?></label>
					<input type="text" name="cvv_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="cvv_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" value="<?php echo $viewData['cvv']; ?>" maxlength="3" placeholder="123" class="cvv">
				</li>
			</ul>
		</li>

		<li class="vertical maestro" style="display: none; opacity: 0;">
			<ul>
				<li>
					<label for="issue_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_ISSUE_DATE') ?></label>
					<input type="text" name="issue_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="issue_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"  placeholder="mm/yy" value="<?php echo $viewData['issue_date']; ?>" maxlength="5" class="issue_date">
				</li>

				<li>
					<span class="or">or</span>
					<label for="issue_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_ISSUE_NUMBER') ?></label>
					<input type="text" name="issue_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="issue_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" placeholder="12" maxlength="2" value="<?php echo $viewData['issue_number']; ?>" class="issue_number">
				</li>
			</ul>
		</li>

		<li>
			<label for="name_on_card_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_CCNAME') ?></label>
			<input type="text" name="name_on_card_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="name_on_card_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" value="<?php echo $viewData['name_on_card']; ?>" placeholder="<?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_CCNAME_PLACEHOLDER') ?>">
		</li>
	</ul>

</div>
<?php 
}
