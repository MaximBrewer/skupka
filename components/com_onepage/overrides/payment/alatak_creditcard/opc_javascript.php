<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//NOTE: to customize html of this plugin, you can either move your display_payment.php into /components/com_onepage/themes/YOUR THEME/overrides/payment/alatak_creditcard/display_payment.php
// or remove display_payment.php file and your Joomla override will be used instead
// opc_javascript is used to load script before ajax (i.e. if the default cart view for EU country does not display the credit card list, it still can get binded to javascript after US country is selected and payment method shown after ajax



$paymentForm = 'adminForm'; 
if (empty($viewData))
{
	$viewData = array(); 
	$viewData['virtuemart_paymentmethod_id'] = $method->virtuemart_paymentmethod_id; 
	
			$creditCards = $method->creditcards;
		if (empty($creditCards)) {
			$creditCards = array('visa', 'visa_electron', 'mastercard','amex','discover',
				'diners_club_international','diners_club_carte_blanche','jcb','laser','maestro');
		} elseif (!is_array($creditCards)) {
			$creditCards = (array)$creditCards;
		}
		foreach ($creditCards as $key => $creditCard) {
			$creditCards[$key] = '"' . $creditCard . '"';
		}
		$creditCardsList = implode(',', $creditCards);

		$viewData['creditcards'] = $creditCardsList; 
	
}
	JFactory::getDocument()->addStyleSheet(JURI::root(true) . '/components/com_onepage/overrides/payment/alatak_creditcard/alatak_creditcard.css');


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

//never allow to diselect the payment method:
$x = str_replace('.attr("checked", false)', '', $x); 

$document = JFactory::getDocument();
$document->addScriptDeclaration($js); 


	
	
	


