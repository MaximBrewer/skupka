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

if ($show_addtocart) {
	vmJsApi::jPrice();
	vmJsApi::cssSite();
	//echo vmJsApi::writeJS();
}

$cache = false; 
$cachetime = 3600; 




if ($filter_category ) $filter_category = TRUE;

$productModel = VmModel::getModel('Product');


if (!empty($attribs['virtuemart_product_id']))
{

$product_id = $attribs['virtuemart_product_id']; 

$products = array(); 


$calculator = calculationHelper::getInstance();
$app = JFactory::getApplication(); 
$cur_stored = (int)$app->getUserStateFromRequest( 'virtuemart_currency_id', 'virtuemart_currency_id',$calculator->vendorCurrency );




$vendor_id = $cart->vendorId; 
if (empty($vendor_id)) $vendor_id = 1; 

			$vendorModel = VmModel::getModel('vendor');
			$vendor = $vendorModel->getVendor($vendor_id);
			

$customfieldsModel = VmModel::getModel ('Customfields');

{ 
VirtueMartModelProduct::$_products = array(); 



$currency = CurrencyDisplay::getInstance();

$product = $productModel->getProduct($product_id,TRUE,TRUE,TRUE,1);
$products[] = $product; 






			if ($product->customfields){

				if (!class_exists ('vmCustomPlugin')) {
					require(JPATH_VM_PLUGINS .DIRECTORY_SEPARATOR. 'vmcustomplugin.php');
				}
				$customfieldsModel -> displayProductCustomfieldFE ($product, $product->customfields);
			}
$isCustomVariant = false;
			if (!empty($product->customfields)) {
				foreach ($product->customfields as $k => $custom) {
					if($custom->field_type == 'C' and $custom->virtuemart_product_id != $virtuemart_product_id){
						$isCustomVariant = $custom;
					}
					if (!empty($custom->layout_pos)) {
						$product->customfieldsSorted[$custom->layout_pos][] = $custom;
						unset($product->customfields[$k]);
					}
				}
				$product->customfieldsSorted['normal'] = $product->customfields;
				unset($product->customfields);
			}

			$product->event = new stdClass();
			$product->event->afterDisplayTitle = '';
			$product->event->beforeDisplayContent = '';
			$product->event->afterDisplayContent = '';
			if (VmConfig::get('enable_content_plugin', 0)) {
				shopFunctionsF::triggerContentPlugin($product, 'productdetails','product_desc');
			}

			$productModel->addImages($product);

			$product->priceDisplay = array(); 
			
			if (!empty($product->prices['salesPrice'] ) ) $product->priceDisplay['salesPrice'] =  $currency->createPriceDiv('salesPrice','',$product->prices,true);
 
			if (!empty($product->prices['salesPriceWithDiscount']) ) $product->priceDisplay['salesPriceWithDiscount'] =  $currency->createPriceDiv('salesPriceWithDiscount','',$product->prices,true);
			
			
}
$currencyModel = VmModel::getModel('currency');
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

$app = JFactory::getApplication(); 
$t = $app->getTemplate(); 
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$t.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'mod_virtuemart_product_multi'.DIRECTORY_SEPARATOR.'mod_virtuemart_product_multi.css'))
{
	$root = Juri::root(true); 
	if (substr($root, -1)!=='/') $root .= '/'; 
	JHtml::stylesheet($root.'templates/'.$t.'/html/mod_virtuemart_product_multi/mod_virtuemart_product_multi.css'); 
}
else
{
	JHtml::stylesheet($root.'modules/mod_virtuemart_product_multi/tmpl/mod_virtuemart_product_multi.css'); 
}



$app = JFactory::getApplication(); 
$t = $app->getTemplate(); 
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$t.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'mod_virtuemart_product_multi'.DIRECTORY_SEPARATOR.'mod_virtuemart_product_multi.js'))
{
	$root = Juri::root(true); 
	if (substr($root, -1)!=='/') $root .= '/'; 
	JHtml::script($root.'templates/'.$t.'/html/mod_virtuemart_product_multi/mod_virtuemart_product_multi.js'); 
}
else
{
	JHtml::script($root.'modules/mod_virtuemart_product_multi/tmpl/mod_virtuemart_product_multi.js'); 
}

if (!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
$currencyDisplay = CurrencyDisplay::getInstance();




ob_start();

/* Load tmpl default */
$p = JModuleHelper::getLayoutPath('mod_virtuemart_product_multi','single'); 

require($p);
$output = ob_get_clean();
echo $output;

echo vmJsApi::writeJS();
}

