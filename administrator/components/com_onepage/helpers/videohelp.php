<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );

class OPCVideoHelp {
 public static $videos = array('COM_ONEPAGE_SHIPPING_DISABLE_LABEL' => 'http://www.rupostel.com/one-page-checkout-component/video-tutorials/disable-shipping-for-all-products-in-virtuemart', 
 'COM_ONEPAGE_SHIPPING_ZERO_WEIGHT_LABEL'=> 'http://www.rupostel.com/one-page-checkout-component/video-tutorials/digital-products-free-shipping-virtuemart', 
 'COM_ONEPAGE_SELECT_REGISTRATION_TYPE'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/supported-registration-modes-in-virtuemart', 
 'COM_ONEPAGE_REGISTRATION_REDIRECT_JOOMLA_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/redirect-joomla-registration-to-virtuemart', 
 'COM_ONEPAGE_SHIPPING_DISABLE_PAYMENT_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/disable-payment-method-per-selected-shipping-method',
 'COM_ONEPAGE_DEFAULT_SHIPPING'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/default-shipping-options',
 'COM_ONEPAGE_DEFAULT_SHIPPING_SEARCH_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/default-shipping-options',
 'COM_ONEPAGE_SHIPPING_DISABLE_SHIPTO_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/disable-ship-to-section', 
 'COM_ONEPAGE_REGISTRATION_ONE_SHIPPING_ADDRESS_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/single-ship-to-address-per-customer', 
 'COM_ONEPAGE_SHIPPING_DELAY_SHIP_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/delayed-shipping-feature-ups-usps-fedex-acs-au-post', 
 'COM_ONEPAGE_PAYMENT_HIDE_PAYMENT_IF_ONE_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/hide-payment-if-only-one-available',
 'COM_ONEPAGE_TRACKING_GENERAL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/opc-tracking-feature-google-analytics-tracking',
 
 'COM_ONEPAGE_REGISTRATION_USERNAME_IS_EMAIL_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/email-as-username-options',
 'COM_ONEPAGE_DISPLAY_NO_BASKET_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/hide-top-basket-during-the-checkout', 
 'COM_ONEPAGE_SHIPPING_INSIDE_BASKET_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/shipping-options-shown-as-select-drop-down-inside-basket-area', 
 'COM_ONEPAGE_SHIPPING_INSIDE_AS_SELECTBOX_DESC'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/shipping-methods-shown-as-select-drop-down', 
 'COM_ONEPAGE_RENAME_THEME'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/rename-opc-theme-before-opc-udpate',
 'COM_ONEPAGE_DISPLAY_SHOW_FULL_TOS_UNLOGGED'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/terms-of-service-article-options',
 'COM_ONEPAGE_DISPLAY_SHOW_TOS_UNLOGGED'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/agreement-checkbox-options',
 'COM_ONEPAGE_PAYMENT_INSIDE_BASKET_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/payment-methods-shown-as-select-drop-down-inside-basket-area', 
 'COM_ONEPAGE_PAYMENT_INSIDE_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/payment-methods-shown-as-select-drop-down',
 'COM_ONEPAGE_DISPLAY_ARTICLE_ID_LABEL'=>'http://www.rupostel.com/one-page-checkout-component/video-tutorials/joomla-article-above-basket', 
 ); 
 public static function show($text)
   {
   
     if (!array_key_exists($text, self::$videos)) return;
	 
      $w = '<a class="video_link" title="'.JText::_('COM_ONEPAGE_VIDEO_HELP').'" target="_blank" href="';
	  $w .= self::$videos[$text]; 
	  $w .= '" style="clear:both;margin-top:7px;display:inline-block;"><span style="height: 15px; width: 16px; display: inline-block; clear:both;" class="icon-16-info videospan glyphicon-facetime-video icon-video">&nbsp;</span></a>'; 
	  echo $w; 
      
   }
   
  public static function get($text)
   {
     ob_start(); 
	 self::show($text); 
	 return ob_get_clean(); 
   }
}	