<?php
/**
** Parts of this code is written by joomlaprofessionals.com Copyright (c) 2012, 2015 All Right Reserved.
** Many part of this code is from VirtueMart Team Copyright (c) 2004 - 2015. All rights reserved.
** Some parts might even be Joomla and is Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved. 
** http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
** This source is free software. This version may have been modified pursuant
** to the GNU General Public License, and as distributed it includes or
** is derivative of works licensed under the GNU General Public License or
** other free or open source software licenses.
**
** THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY 
** KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
** IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
** PARTICULAR PURPOSE.

** <author>Joomlaproffs / Virtuemart team</author>
** <email>info@joomlaprofessionals.com</email>
** <date>2015</date>
*/	

defined('_JEXEC') or die('Restricted access');



JHTML::script('plugins/system/onepage_generic/onepage.js');
JHTML::script('plugins/system/onepage_generic/onepage_generic.js');
JHTML::stylesheet ( 'plugins/system/onepage_generic/onepage_generic.css');
JHtml::_('behavior.framework');


$taskRoute = "";

$vendorModel = VmModel::getModel('vendor');
$vendordata = $vendorModel->getVendor($this->cart->vendor->virtuemart_vendor_id);
$vendorModel->addImages($vendordata,1);
if (VmConfig::get('enable_content_plugin', 0)) {
		shopFunctionsF::triggerContentPlugin($vendordata, 'vendor','vendor_terms_of_service');
}

$lang = JFactory::getLanguage();
$extension = 'com_users';
$lang->load($extension);


$this->assignRef("vendordata", $vendordata);

vmJsApi::jPrice();



$plugin=JPluginHelper::getPlugin('system','onepage_generic');
$params=new JRegistry($plugin->params);
$countryreload = $params->get("country_reload", 0);
$popupaddress = $params->get("popup_address", 1);
$buttoncolour = $params->get("buttoncolour", "#3071A9")

?>
  <style type="text/css">
  .opg-button-primary
  {
    background:<?php echo $buttoncolour; ?> !important;
  }
  .opg-progress-striped .opg-progress-bar {
  background-image: -webkit-linear-gradient(-45deg, <?php echo $buttoncolour; ?> 25%, transparent 25%, transparent 50%, <?php echo $buttoncolour; ?> 50%, <?php echo $buttoncolour; ?> 75%, transparent 75%, transparent);
  background-image: linear-gradient(-45deg, <?php echo $buttoncolour; ?> 25%, transparent 25%, transparent 50%, <?php echo $buttoncolour; ?> 50%, <?php echo $buttoncolour; ?> 75%, transparent 75%, transparent);
  background-size: 30px 30px;
}
  
  </style>
<?php

JFactory::getLanguage()->load('plg_system_onepage_generic',JPATH_ADMINISTRATOR);

$userFieldsModel = VmModel::getModel('userfields');

$showextraterms = $params->get('show_extraterms',0);	

JHtml::_('behavior.formvalidation');


$document = JFactory::getDocument();
$document->addStyleDeclaration('#facebox .content {display: block !important; height: 480px !important; overflow: auto; width: 560px !important; }');
 $customernote = 0;
 foreach($this->cart->BTaddress["fields"] as $singlefield) 
 {
     if($singlefield['name']=='customer_note') 
 	 {
	   $customernote = true;
	 }
	 
 } 
 foreach($this->cart->STaddress["fields"] as $singlefield) 
 {
     if($singlefield['name']=='shipto_customer_note') 
 	 {
	   $customernote = true;
	 }
	 
 } 
 foreach($this->userFieldsCart["fields"] as $singlefield) 
 {
     if($singlefield['name']=='customer_note') 
 	 {
	   $customernote = true;
	 }
	 
 } 
$agreetotos = 0;
foreach($this->cart->BTaddress['fields'] as $name => $cartfield)
{
 if($cartfield['required'] == 1)
 {
	 if($cartfield['name'] == "tos")
	 {
	   $agreetotos = 1;
	 }
  }
 } 

foreach($this->cart->STaddress['fields'] as $name => $cartfield)
{
 if($cartfield['required'] == 1)
 {
	 if($cartfield['name'] == "tos")
	 {
	   $agreetotos = 1;
	 }
  }
 } 
 
 
foreach($this->userFieldsCart['fields'] as $name => $cartfield)
{
 if($cartfield['required'] == 1)
 {
	 if($cartfield['name'] == "tos")
	 {
	   $agreetotos = 1;
	 }
  }
 } 
 if (!class_exists('CurrencyDisplay'))
				require(VMPATH_ADMIN . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');
				$currency = CurrencyDisplay::getInstance();
 $listpayments = $params->get("list_allpayment", 0);				
 $listshipments = $params->get("list_allshipment", 0);	
 $captchaenabled = 0;				
 $usecaptcha = $params->get("use_recaptcha", 0);
 $captchakey = $params->get("recaptchakey", '');
 if($usecaptcha && !empty($captchakey))
 {
   JHTML::script('https://www.google.com/recaptcha/api.js');
   $captchaenabled = 1;
 }
 
 

$acceptmessage =  htmlspecialchars(JText::_('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS'), ENT_QUOTES);
$privacymeessage =  htmlspecialchars(JText::_('PLG_VMUIKITONEPAGE_PRIVACY_POLICY_ERROR'), ENT_QUOTES);
$selectshipment  =  htmlspecialchars(JText::_('COM_VIRTUEMART_CART_SELECT_SHIPMENT'), ENT_QUOTES);
$selectpayment =  htmlspecialchars(JText::_('COM_VIRTUEMART_CART_SELECT_PAYMENT'), ENT_QUOTES);
$invaliddata   =  htmlspecialchars(JText::_('COM_VIRTUEMART_CART_CHECKOUT_DATA_NOT_VALID'), ENT_QUOTES);
$productupdate =  htmlspecialchars(JText::_('COM_VIRTUEMART_PRODUCT_UPDATED_SUCCESSFULLY'), ENT_QUOTES);
$chosecountry =  htmlspecialchars(JText::_('PLG_SYSTEM_VMUIKIT_CHOOSE_COUNTRY'), ENT_QUOTES);
$captchainvalid =  htmlspecialchars(JText::_('PLG_SYSTEM_VMUIKIT_CAPTCHA_INVALID'), ENT_QUOTES);
$removeprouct =  htmlspecialchars(JText::_('COM_VIRTUEMART_PRODUCT_REMOVED_SUCCESSFULLY'), ENT_QUOTES);
$changetext   =  htmlspecialchars(JText::_('PLG_SYSTEM_VMUIKIT_ONEPAGE_CHNAGE'), ENT_QUOTES);
$noshipmethod   =  htmlspecialchars(JText::sprintf('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', ''), ENT_QUOTES);
$nopaymethod   =  htmlspecialchars(JText::sprintf('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', ''), ENT_QUOTES);
$minpurchaseerror   =  htmlspecialchars(vmText::sprintf('COM_VIRTUEMART_CART_MIN_PURCHASE',  $currency->priceDisplay($vendordata->vendor_min_pov)), ENT_QUOTES);



$button_primary  = $params->get("button_primary","opg-button-primary");
$button_danger  = $params->get("button_danger","opg-button-danger");
$form_danger  = $params->get("form_danger","opg-form-danger");


$document->addScriptDeclaration('
if (typeof vmonepage == "undefined") {
  var vmonepage = {};
};
var vmonepage = { 
  "CARTPAGE" : "yes",
  "shipmentfileds" : "'.count($this->cart->STaddress['fields']).'",
  "agree_to_tos_onorder" : "'.$agreetotos.'",
  "acceptmeessage" : "'.$acceptmessage.'",
  "privacymeessage" : "'.$privacymeessage.'",
  "minpurchaseerror" : "'.$minpurchaseerror.'",
  "selectshipment" : "'.$selectshipment.'",
  "selectpayment" : "'.$selectpayment.'",
  "invaliddata" : "'.$invaliddata.'",
  "productupdate" : "'.$productupdate.'",
  "chosecountry" : "'.$chosecountry.'",
  "removeprouct" : "'.$removeprouct.'",
  "changetext" : "'.$changetext.'",
  "noshipmethod" : "'.$noshipmethod.'",
  "nopaymethod" : "'.$nopaymethod.'",
  "onlyregistered" : "'.VmConfig::get('oncheckout_only_registered', 0).'",
  "couponenable" : "'.VmConfig::get('coupons_enable', 0).'",
  "showextraterms" : "'.$showextraterms.'",
  "token" : "'.JSession::getFormToken().'",
  "show_tax" :"'.VmConfig::get('show_tax').'",
  "customernote" : "'.$customernote.'",
  "countryreload" : "'.$countryreload.'",
  "captchaenabled" : "'.$captchaenabled.'",
  "captchainvalid" : "'.$captchainvalid.'",
  "listshipments" : "'.$listshipments.'",
  "listpayments" : "'.$listpayments.'",
  "popupaddress" : "'.$popupaddress.'",
  "button_primary" : "'.$button_primary.'",
  "button_danger" : "'.$button_danger.'",
  "form_danger" : "'.$form_danger.'"
  };
');


?>

<style>
input#register
{
 float:none !important;
}
.all_shopper_fields{
 border:none !important;
}
</style>

<?php 
if(count($this->cart->products) == 0)
{
?>
<div  class="opg-panel opg-panel-box">
		<strong><?php echo JText::_('COM_VIRTUEMART_EMPTY_CART') ?></strong>
			<?php if(!empty($this->continue_link_html)) : ?>
			<div class="opg-text-center">
				<?php 
				echo str_replace("continue_link", "opg-button ".$button_primary, $this->continue_link_html);
				?>
			</div>
			<?php endif; ?>		
	</div>	
	<?php
	 if($params->get("built_by") == 1)
	 {
	 ?>
    	 <p style="text-align: center;"><small style="text-size:8px; color:#b4b4b4;"><a style="text-size:8px; color:#b4b4b4;" title="one page checkout virtuemart" target="_blank" href="http://vmonepage.com">VMonepage</a>&nbsp;is built by&nbsp;<a style="text-size:8px; color:#b4b4b4;" title="joomlaproffs webshop ecommerce" target="_blank" href="http://www.joomlaprofessionals.com">joomlaprofessionals.com</a></small></p>
	 <?php
	 }
	 ?>
<?php
}
else
{
?>

	
<div class="opg-width-1-1" id="fullerrordiv">
</div>
	
   <?php
   if ($this->allowChangeShopper){
		echo $this->loadTemplate ('shopperform');
	}
   ?>
	
	<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_( 'index.php?option=com_virtuemart&view=cart'.$taskRoute,$this->useXHTML,$this->useSSL ); ?>" class="opg-form opg-width-1-1 ">
	
	 <a id="loadingbutton" class="opg-hidden" href="Javascript:void(0);" data-opg-modal="{target:'#lodingdiv', bgclose:false}"></a>
		 <div id="lodingdiv" class="opg-modal"><!-- lodingdiv Modal Started -->
		 <div class="opg-modal-dialog"><!-- lodingdiv Modal Started -->
		     <a id="loadingbtnclose" class="opg-modal-close opg-close opg-hidden"></a>
			<div class="opg-progress opg-progress-striped opg-active">
			    <div class="opg-progress-bar opg-text-center" style="width: 100%;"></div>
			</div>
    	</div> <!-- lodingdiv Modal ended -->
		</div><!-- lodingdiv Modal ended -->
	
	
	 <div id="cart-contents" class="opg-grid" data-opg-margin><!-- CART CONTENTS DIV START -->
		
		<?php
		$layoutwidth = $params->get("layout_width", 1);
		if($layoutwidth == 2)
		{
			 $leftdiv_width =  "opg-width-large-2-3 opg-width-medium-2-3";
			 $rightdiv_width = "opg-width-large-1-3 opg-width-medium-1-3";
		}
		else if($layoutwidth == 3)
		{
		     $leftdiv_width =  "opg-width-large-2-2 opg-width-medium-1-2";
			 $rightdiv_width = "opg-width-large-1-2 opg-width-medium-1-2";
		}
		else if($layoutwidth == 4)
		{
		     $leftdiv_width =  "opg-width-large-1-1 opg-width-medium-1-1";
			 $rightdiv_width = "opg-width-large-1-1 opg-width-medium-1-1";
		}
		else 
		{
			 $leftdiv_width =  "opg-width-large-3-5 opg-width-medium-3-5";
			 $rightdiv_width = "opg-width-large-2-5 opg-width-medium-2-5";
		}
		?>
		 <div id="leftdiv" class="opg-width-1-1 <?php echo $leftdiv_width; ?> opg-width-small-1-1   opg-border-rounded">
			<?php echo $this->loadTemplate('left'); ?>
		 </div>
		 <div id="right_div" class="tm-sidebar-a opg-width-1-1 <?php echo $rightdiv_width; ?> opg-width-small-1-1 o" >
		    <?php echo $this->loadTemplate('right'); ?>
		 </div>
		 
     </div><!-- CART CONTENT DIV END -->
	  <?php
	  	echo $this->loadTemplate('modalpage');
	  ?>
	 <?php
	 if($params->get("built_by") == 1)
	 {
	 ?>
    	 <p style="text-align: center;"><small style="text-size:8px; color:#b4b4b4;"><a style="text-size:8px; color:#b4b4b4;" title="one page checkout virtuemart" target="_blank" href="http://vmonepage.com">VMonepage</a>&nbsp;is built by&nbsp;<a style="text-size:8px; color:#b4b4b4;" title="joomlaproffs webshop ecommerce" target="_blank" href="http://www.joomlaprofessionals.com">joomlaprofessionals.com</a></small></p>
	 <?php
	 }
	 ?>
</form>

<?php
}
JHTML::script('plugins/system/onepage_generic/vmprices.js');
?>