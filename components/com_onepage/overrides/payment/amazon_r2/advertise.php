<?php
/** 
 * @version		$Id: opc.php$
 * @copyright	Copyright (C) 2005 - 2014 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 

// no direct access
defined('_JEXEC') or die;
$cart =& $extra['cart']; 

$error = false; 
		$config['serviceURL'] = '';
		$config['widgetURL'] = '';
		$config['caBundleFile'] = '';
		$config['clientId'] = '';
		$config['merchantId'] = $method->sellerId;
		$config['accessKey'] = $method->accessKey;
		$config['secretKey'] = $method->secretKey;
		$config['applicationName'] = 'VirtueMart';
		$config['applicationVersion'] = '3.2.1';
		$config['region'] = $method->region;
		$config['environment'] = $method->environment;
		$config['cnName'] = 'sns.amazonaws.com';//$method->cnname;

		if(!class_exists('OffAmazonPaymentsService_Client')) require VMPATH_PLUGINS.'/vmpayment/amazon/library/OffAmazonPaymentsService/Client.php';
		if(!class_exists('OffAmazonPaymentsService_Regions')) require VMPATH_PLUGINS.'/vmpayment/amazon/library/OffAmazonPaymentsService/Regions.php';
		try {
			$client = new OffAmazonPaymentsService_Client($config);
			if (class_exists('plgVmpaymentAmazon')) {
			   plgVmpaymentAmazon::$widgetScriptLoaded = true; 
			 }
		} catch (Exception $e) {
			$error = true; 
		}

if (($client) && (empty($error))) {
$widgetURL = $client->getMerchantValues()->getWidgetUrl();
JHTML::script($widgetURL, false);

static $wasRun; 
if (empty($wasRun)) {

//comes from private function  getButtonWidgetImageURL () 
$region = $method->region;
$region_europe = array('UK', 'DE');
$url = '';
if (in_array($region, $region_europe)) {
			if ($region == "UK") {
				$domain = "co.uk";
			} else {
				$domain = "de";
			}
			if ($method->environment == 'sandbox') {
				$mode = "-sandbox";
			} else {
				//TODO
				$mode = "";
			}
			$url = "https://payments" . $mode . ".amazon." . $domain . "/gp/widgets/button?sellerId=" . $method->sellerId . "&size=" . $method->sign_in_widget_size . "&color=" . $method->sign_in_widget_color . "";
		} else {
			if ($method->environment == 'sandbox') {
				$url = $method->sandbox_signin;
			} else {
				$url = $method->production_signin;
			}
		}

		/* redirect page url*/
		
		
		
		$rurl = 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&format=raw&nt=getAmazonSessionId&pm=' . $method->virtuemart_paymentmethod_id.'&disableopc=disbleopc&nosef=1';
		
		$Itemid = (int)JRequest::getVar('Itemid'); 
		if (!empty($Itemid)) { 
		 $rurl .= '&Itemid='.$Itemid; 
		}
		$lang = (int)JRequest::getVar('lang'); 
		if (!empty($lang)) { 
		 $rurl .= '&lang='.$lang; 
		}

		
		$ssl = VmConfig::get('use_ssl', false); 
		$redirect_page = JRoute::_($rurl, false, $ssl);
		/* redirect page url*/
		/*digital goods */
		$digi = false; 
		if (!$method->digital_goods) {
			$digi = false;
		}
		if ($cart) {
		  $weight = $ref->getOrderWeight($cart, 'GR');
		
		}
		
		
		if ($weight == 0) {
			$digi = true;
		} else {
			$digi = false;
		}
		/* digital goods end*/
$viewData['buttonWidgetImageURL'] = $url; 
$viewData['renderAmazonAddressBook'] = (!$digi); 
$viewData['sign_in_css'] = '#opc_amazon_comes_here';
$viewData['sellerId'] = $method->sellerId; 
$viewData['redirect_page'] = $redirect_page; 
$viewData['layout'] = 'cart'; 
$viewData['virtuemart_paymentmethod_id'] = $method->virtuemart_paymentmethod_id; 
$viewData['include_amazon_css'] = $method->include_amazon_css;
//$signInButton = $ref->renderByLayout('signin', $viewData); 






	$doc = JFactory::getDocument();

	$signInButton = '';
	$signInButton = str_replace('"', '\"', $signInButton); 
	
	vmJsApi::addJScript(  '/plugins/vmpayment/amazon/assets/js/amazon.js');
	if ($viewData['include_amazon_css']) {
		$doc->addStyleSheet(JURI::root(true) . '/plugins/vmpayment/amazon/assets/css/amazon.css');
	}
	$renderAmazonAddressBook = $viewData['renderAmazonAddressBook'] ? 'true' : 'false';

	$js = "
jQuery(document).ready(function($) {
	jQuery(this).off('initializeAmazonPayment');
	jQuery(this).on('initializeAmazonPayment', function() {
		/* opc code override */
		amazonPayment.showAmazonButton('" . $viewData['sellerId'] . "', '" . $viewData['redirect_page'] . "', " . $renderAmazonAddressBook . ");
		
		
	});
	jQuery(this).trigger('initializeAmazonPayment');
});
";


	


	

		$js .= "
jQuery(document).ready( function($) {
	jQuery('#leaveAmazonCheckout').click(function(){
		amazonPayment.leaveAmazonCheckout();
	});
});
";
		

		
			$js .= "
	jQuery(document).ready(function($) {
	jQuery('#checkoutFormSubmit').attr('disabled', 'true');
	jQuery('#checkoutFormSubmit').removeClass( 'vm-button-correct' );
	jQuery('#checkoutFormSubmit').addClass( 'vm-button' );
	jQuery('#checkoutFormSubmit').text( '" . vmText::_('VMPAYMENT_AMAZON_CLICK_PAY_AMAZON', true) . "' );
	});
";
			$doc->addScriptDeclaration($js);
			
		


ob_start(); 	
?>
<div id="opc_amazon_comes_here">
<div class="amazonSignTip"><?php echo  vmText::_('VMPAYMENT_AMAZON_SIGNIN_TIP', true); ?>
</div>
<div id="amazonSignInButton"><div id="payWithAmazonDiv" ><img src="<?php echo  $viewData['buttonWidgetImageURL']; ?>" style="cursor: pointer;"/></div>
<div id="amazonSignInErrorMsg">&nbsp;</div>
</div>
</div>
<?php
$html = ob_get_clean(); 
$html .= '&nbsp;'; 


$htmlIn[] = $html; 
$wasRun = true; 
}
}