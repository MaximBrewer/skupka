<?php
defined('_JEXEC') or die('Restricted access');

if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
VmConfig::loadConfig();
// Load the language file of com_virtuemart.
JFactory::getLanguage()->load('com_virtuemart');
if (!class_exists( 'calculationHelper' )) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/calculationh.php');
if (!class_exists( 'CurrencyDisplay' )) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/currencydisplay.php');
if (!class_exists( 'VirtueMartModelVendor' )) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/models/vendor.php');
if (!class_exists( 'VmImage' )) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/image.php');
if (!class_exists( 'shopFunctionsF' )) require(JPATH_SITE.'/components/com_virtuemart/helpers/shopfunctionsf.php');
if (!class_exists( 'calculationHelper' )) require(JPATH_COMPONENT_SITE.'/helpers/cart.php');
if (!class_exists( 'VirtueMartModelProduct' )){
   JLoader::import( 'product', JPATH_ADMINISTRATOR .'/components/com_virtuemart/models' );
}