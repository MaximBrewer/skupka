<?php
defined('_JEXEC') or die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
* featured/Latest/Topten/Random Products Module
*
* @version $Id: mod_virtuemart_product_slider.php 2789 2011-02-28 12:41:01Z oscar $
* @package VirtueMart
* @subpackage modules
*
* @copyright (C) 2010 - Patrick Kohl
* @copyright (C) 2011 - 2016 The VirtueMart Team
* @author Max Milbers, Valerie Isaksen, Alexander Steiner
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* VirtueMart is Free Software.
* VirtueMart comes with absolute no warranty.
*
* www.virtuemart.net
*/


defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');

VmConfig::loadConfig();
VmConfig::loadJLang('mod_virtuemart_product_slider', true);

// Setting
$max_items = 		$params->get( 'max_items', 2 ); //maximum number of items to display
$layout = $params->get('layout','default');
$category_id = 		$params->get( 'virtuemart_category_id', null ); // Display products from this category only
$filter_category = 	(bool)$params->get( 'filter_category', 0 ); // Filter the category
$display_style = 	$params->get( 'display_style', "div" ); // Display Style
$products_per_row = $params->get( 'products_per_row', 1 ); // Display X products per Row
$show_price = 		(bool)$params->get( 'show_price', 1 ); // Display the Product Price?
$show_addtocart = 	(bool)$params->get( 'show_addtocart', 1 ); // Display the "Add-to-Cart" Link?
$headerText = 		$params->get( 'headerText', '' ); // Display a Header Text
$footerText = 		$params->get( 'footerText', ''); // Display a footerText
$Product_group = 	$params->get( 'product_group', 'featured'); // Display a footerText

// new parametrs
$moduleclass_sfx = $params->get( 'moduleclass_sfx' );// суффикс модуля
$large = $params->get( 'large', ''); // широкоформатные компьютеры
$medium = $params->get( 'medium', ''); // настольные компьютеры
$small = $params->get( 'small', ''); // планшеты
$extrasmall = $params->get( 'extrasmall', ''); // смартфоны
$nav_dots = $params->get( 'nav_dots', 1 ); // Dots
$nav_nav = $params->get( 'nav_nav', 0 ); // кнопки навигации
$loop = $params->get( 'loop', '1'); // зацикливание
$autoplay = $params->get( 'autoplay', '0'); // автозапуск
$autoplayTimeout = $params->get( 'autoplayTimeout', ''); // скорость смены слайдов
$twoImage = $params->get('twoImage','0'); // показ дополнительного изображения
$productMargin = $params->get( 'productMargin', ''); // отступ между товарами
$colorSlider = $params->get('colorSlider','');// основной цвет
$shadow = $params->get( 'shadow', 0 ); // тени
$heightIMG = $params->get( 'heightIMG', ''); // высота блока с изображением
$customfield = $params->get( 'customfield', '0'); // Custom Field
$cartStyle = $params->get( 'cartStyle', '1'); // кнопка Купить

// Add style and script
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base().'modules/mod_virtuemart_product_slider/assets/product-slider.css');
$doc->addScript(JURI::base().'modules/mod_virtuemart_product_slider/assets/product-slider.js');

// CSS
// блок с товаром
$style = '.vmslider{margin:0 -'.round($productMargin/2).'px}.vmslider-product-wrap{padding: 5px '.round($productMargin/2).'px}';

if($shadow == '0'){
    $style .='.vmslider-wrap'.$moduleclass_sfx.' .vmslider-product:hover{border-color:'.$colorSlider.'}';
}

//изображение
$style .= '.vmslider-wrap'.$moduleclass_sfx.' .vmslider-product .vmslider-image{height:'.$heightIMG.'px;}';
if($twoImage == '1'){
    $style .= '.vmslider-wrap'.$moduleclass_sfx.' .vmslider-product:hover .vmslider-image .image1{opacity:1}';
}

// название
$style .= '.vmslider-wrap'.$moduleclass_sfx.' .vmslider-product .vmslider-name:hover a{color:'.$colorSlider.';}';

// корзина
if($cartStyle == '1'){
    $style .= '.vmslider-wrap'.$moduleclass_sfx.' .vmslider-product .vmslider-cart.sliderCart span.addtocart-button > input,.vmslider-wrap'.$moduleclass_sfx.' .vmslider-product .vmslider-cart.sliderCart span.addtocart-button >input:hover{background:'.$colorSlider.';}';
}

// точки навигации
if($nav_dots == '1'){
    $style .= '.vmslider-wrap'.$moduleclass_sfx.' .vmslider .slick-dots li button:before{background:'.$colorSlider.';}';
}


// вставка стилей на страницу
$doc->addStyleDeclaration($style);

$mainframe = Jfactory::getApplication();
$virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',vRequest::getInt('virtuemart_currency_id',0) );


vmJsApi::jPrice();
vmJsApi::cssSite();

$cache = $params->get( 'vmcache', true );
$cachetime = $params->get( 'vmcachetime', 300 );
//vmdebug('$params for mod products',$params);
if($cache){
	vmdebug('Use cache for mod products');
	$key = 'products'.$category_id.'.'.$max_items.'.'.$filter_category.'.'.$display_style.'.'.$products_per_row.'.'.$show_price.'.'.$show_addtocart.'.'.$Product_group.'.'.$virtuemart_currency_id.'.'.$category_id;
	$cache	= JFactory::getCache('mod_virtuemart_product_slider', 'output');
	$cache->setCaching(1);
	$cache->setLifeTime($cachetime);

	if ($output = $cache->get($key)) {
		echo $output;
		echo vmJsApi::writeJS();
		vmdebug('Use cached mod products');
		return true;
	}
}

$vendorId = vRequest::getInt('vendorid', 1);

if ($filter_category ) $filter_category = TRUE;

$productModel = VmModel::getModel('Product');
//VirtueMartModelProduct::$omitLoaded = $params->get( 'omitLoaded', 0);
$products = $productModel->getProductListing($Product_group, $max_items, $show_price, true, false,$filter_category, $category_id);
$productModel->addImages($products);

if (!class_exists('shopFunctionsF'))
	require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
shopFunctionsF::sortLoadProductCustomsStockInd($products,$productModel);

$totalProd = 		count( $products);
if(empty($products)) return false;

if (!class_exists('CurrencyDisplay'))
	require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
$currency = CurrencyDisplay::getInstance( );

ob_start();

/* Load tmpl default */
require(JModuleHelper::getLayoutPath('mod_virtuemart_product_slider',$layout));
$output = ob_get_clean();
echo $output;

if($cache){
	$cache->store($output, $key);
}

echo vmJsApi::writeJS();
?>
