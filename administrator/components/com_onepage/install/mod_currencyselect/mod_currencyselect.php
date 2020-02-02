<?php
defined('_JEXEC') or die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
* featured/Latest/Topten/Random Products Module
*
* @version $Id: mod_virtuemart_product.php 2789 2011-02-28 12:41:01Z oscar $
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) 2010 - Patrick Kohl
* @copyright (C) 2011 - The VirtueMart Team
* @author Max Milbers, Valerie Isaksen, Alexander Steiner
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/



defined('DS') or define('DS', DIRECTORY_SEPARATOR);

JFactory::getLanguage()->load('mod_currencyselect'); 
if (!class_exists( 'VmConfig' )) require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');



VmConfig::loadConfig();
VmConfig::loadJLang('mod_virtuemart_product', true);

if (!class_exists('VmView'))
	require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmview.php');

if (!class_exists('shopFunctionsF'))
require(JPATH_VM_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctionsf.php');

 if (!class_exists('CurrencyDisplay')) require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .  DIRECTORY_SEPARATOR. 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');

$params = new JRegistry; 

// Setting
$max_items = 		1; //maximum number of items to display
$layout = 'single'; 
$category_id = 		null; 
$filter_category = 	0; 
$display_style = 	'div'; 
$products_per_row = '1'; 
$show_price = 		1; 
$show_addtocart = 	1; 
$headerText = 		'';
$footerText = 		'';
$Product_group = 	'featured'; 

if (!class_exists('VirtuemartCart'))
	require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php');

$cart = VirtuemartCart::getCart(); 


if(!class_exists('calculationHelper')) 
	require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .  DIRECTORY_SEPARATOR. 'helpers' . DIRECTORY_SEPARATOR .'calculationh.php');
			$calculator = calculationHelper::getInstance();

if (empty($cart->pricesCurrency))
$cart->pricesCurrency = $calculator->_currencyDisplay->getCurrencyForDisplay();
			
$mainframe = JFactory::getApplication();
$virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',vRequest::getInt('virtuemart_currency_id',$cart->pricesCurrency) );



$cache = false; 
$cachetime = 3600; 


$productModel = VmModel::getModel('Product');

$currencyModel = VmModel::getModel('currency');
if (isset($cart->vendorId)) $vendor_id = $cart->vendorId; 
if (empty($vendor_id)) $vendor_id = 1; 
$currencies = $currencyModel->getVendorAcceptedCurrrenciesList($vendor_id);
$db = JFactory::getDBO(); 
foreach ($currencies as $a=>$c)
{

$q = 'select * from #__virtuemart_currencies where virtuemart_currency_id = '.(int)$c->virtuemart_currency_id.' limit 0,1'; 
$db->setQuery($q); 
$res = $db->loadAssoc(); 

foreach ($res as $k=>$v)
{
	$currencies[$a]->$k = $v; 
}
}


if (!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
$currencyDisplay = CurrencyDisplay::getInstance();




ob_start();

/* Load tmpl default */
$p = JModuleHelper::getLayoutPath('mod_currencyselect','single'); 

require($p);
$output = ob_get_clean();
echo $output;




