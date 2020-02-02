<?php
/*
*  @package      VirtueMart
*  @version      3.8 2015-09-03
*  @author       Kaurava Legh, iMaud studio
*  @copyright    Copyright (C) 2015 kauravalegh.com. All rights reserved.
*  Для магазина на CMS Joomla! и компоненте VirtueMart генерирует прайс товара
*  формата YML (xml) для предоставления ЯНДЕКС.Маркет. 
*  Описание формата YML размещено на сайте Яндекс:
*  http://partner.market.yandex.ua/legal/tt_rus/
*  http://legal.yandex.ru/market_adv_rules/
*  Файл-результат ymlexport.xml содержит описание магазина и полный прайс.
*  Для списка включенных категорий добавлена обработка категорий 4 уровня
*/

define('_JEXEC', 1);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', '../../../');
require_once(JPATH_BASE.'includes/defines.php');
require_once(JPATH_BASE.'includes/framework.php');

$app = JFactory::getApplication('site');
$app->initialise();
$db = JFactory::getDBO();

require_once(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/calculationh.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/vmmodel.php');
// if(!class_exists ('VmImage')) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/image.php');

include(dirname(__FILE__)."/functions.php");

VmConfig::loadConfig();
$calculator = calculationHelper::getInstance();
$show_out_of_stock_products = VmConfig::get("show_out_of_stock_products");

if (!class_exists('CurrencyDisplay')) {
	require(JPATH_VM_ADMINISTRATOR.'/helpers/currencydisplay.php');
}

$im_juri = JURI::getInstance();
$imliveurl = $im_juri->toString( array("scheme","host", "port")); // url корень сайта без закрывающего слеш
$plg_folder = str_replace("exportyml.php",'',$im_juri->toString(array("path"))); // папка плагина от корня сайта
$plg_folder = ltrim($plg_folder,'/');
$plg_folder = ltrim($plg_folder,DS);
$lang = VmConfig::get("vmlang", "ru_ru");
// $lang='ru_ru';
$vmVersion = vmConfig::getInstalledVersion();

$tbvm_curr = '#__virtuemart_currencies';
$tbvm_categories = '#__virtuemart_categories';
$tbvm_cats = '#__virtuemart_category_categories';
$tbvm_cats_lang = '#__virtuemart_categories_'.$lang;
$tbvm_prod = '#__virtuemart_products';
$tbvm_prod_cats = '#__virtuemart_product_categories';
$tbvm_prod_manuf = '#__virtuemart_product_manufacturers';
$tbvm_manuf = '#__virtuemart_manufacturers';
$tbvm_medias = '#__virtuemart_medias';
$tbvm_prod_medias = '#__virtuemart_product_medias';
$tbvm_customs = '#__virtuemart_customs';
$tbvm_prod_customfields = '#__virtuemart_product_customfields';

// все спецполя, которые являются элементами YML
$specTags = array('available','pickup','delivery','local_delivery_cost','typeprefix',
									'sales_notes','downloadable','country_of_origin','страна',
									'market_category','cpa','заказнамаркете','гарантия','manufacturer_warranty');
// возможные названия спецполя для элемента cpa Заказ на Маркете без пробелов и мал.буквы
$cpa_names = array('cpa','заказнамаркете');
									
// возможные значения опций спецполя Заказ на Маркете
$cpa_values_yes = array('1','true','да','участвует');
$cpa_values_no  = array('0','false','нет','неучаствует');

// замена слов в сроке гарантии согласно ISO 8601
$warr_words = array('года', 'год', 'лет','г','месяца','месяц','мес','м', ' ', '.');
$warr_sign = array('Y','Y','Y','Y','M','M','M','M','');

$img_extensions = array('jpg','jpeg','gif','bmp','png');

$query = $db->getQuery(true);
$query->select('enabled, params')
			->from('#__extensions')
			->where('element = '.$db->Quote('exportyml'));
$db->setQuery($query);
if (is_null($res = $db->loadAssoc())) { 
	echo '<p>'.nl2br($db->getErrorMsg()).'</p>'; 
	die;
}
if (!$res['enabled']) die; // плагин отключен? уходим из скрипта
$params = json_decode($res['params'], true);

if((float)$params['set_time_limit']) set_time_limit((float)$params['set_time_limit']*60);

$vendorId = 1; // выбираем данные для продавца с id=1
$vendorModel = VmModel::getModel('Vendor');
$vendor = $vendorModel->getVendor ($vendorId);
$storeName =  $vendor->vendor_store_name; // название магазина
$companyName =  $vendor->vendor_name;  // название компании
$storeURL =  $vendor->vendor_url;
$storeURL = $storeURL ? $storeURL : $imliveurl; // адрес сайта из VM или по-умолчанию
$storeURL = rtrim($storeURL,DS);
$storeURL = rtrim($storeURL,'/');
if(isset($params['transcode']) && $params['transcode']) {
	$storeURL = str_replace('%2F', '/', rawurlencode($storeURL));
	$storeURL = str_replace('%3A', ':', $storeURL);
	$storeURL = str_replace('%5C', '/', $storeURL);
}

$my_agency_name = htmlspecialchars($params['developer_name']);
$my_agency_email = $params['developer_email'];
$export_filename = $params['export_filename'] ? $params['export_filename'] : 'ymlexport.xml';
$list_categoryes = listInt($params['list_categoryes']);
$exclude_items = listInt($params['exclude_items']);
$min_quantity = (int)$params['min_quantity'];

if(!isset($params['costprice'])) $params['costprice'] = 0;

if (isset($params['notes_sw']) && $params['notes_sw']) {
	$sales_notes = trim(htmlspecialchars($params['sales_notes']));
	} else {
	$sales_notes = false;
}

// заголовок файла
// Описание HTML5 DOMDocument http://php5.kiev.ua/manual/ru/class.domdocument.html

$imp=new DOMImplementation;
$dtd = $imp->createDocumentType('yml_catalog', '', 'shops.dtd');
$xml = $imp->createDocument("", "", $dtd);
// $xml = $imp->createDocument();
$xml->version = '1.0';
$xml->encoding = 'UTF-8';

$yml_catalog = $xml->appendChild($xml->createElement('yml_catalog'));
$yml_catalog->setAttribute('date',date('Y-m-d H:i'));

$shop = $yml_catalog->appendChild($xml->createElement('shop'));

$sh_name = $shop->appendChild($xml->createElement('name'));
$sh_name->appendChild($xml->createTextNode(htmlspecialchars($storeName)));
// $sh_name->appendChild($xml->createTextNode(htmlspecialchars(mb_substr($storeName, 0, 20, 'UTF-8'))));
$sh_company = $shop->appendChild($xml->createElement('company'));
$sh_company->appendChild($xml->createTextNode(htmlspecialchars($companyName)));
$sh_url = $shop->appendChild($xml->createElement('url'));
$sh_url->appendChild($xml->createTextNode($storeURL));

if (!empty($my_agency_name)) {
	$agency = $shop->appendChild($xml->createElement('agency'));
	$agency->appendChild($xml->createTextNode(htmlspecialchars($my_agency_name)));
}
if (!empty($my_agency_email)) {
	if (emailValidate($my_agency_email)) {
		$email = $shop->appendChild($xml->createElement('email'));
		$email->appendChild($xml->createTextNode($my_agency_email));
	} else {
		echo '<p class="ymlexport_errormsg">'._JSHOP_IMDEXPORTYML_EMAIL_WRONG.'</p>';
	}
}

$currenciesArray = $vendor->vendor_accepted_currencies; // массив ID активных валют
$mainCurrID = $vendor->vendor_currency;

$query = $db->getQuery(true);
$query->select( '`virtuemart_currency_id`,`currency_code_3`,`currency_exchange_rate`' );
$query->from($tbvm_curr);
if(count($currenciesArray)) {
	$query->where('virtuemart_currency_id IN ('.implode($currenciesArray,",").')');
} else {
	$query->where('virtuemart_currency_id = '.$mainCurrID);
}
$db->setQuery($query);
if (is_null($aCurrencies = $db->loadObjectList('virtuemart_currency_id'))) { 
	echo '<p>'.nl2br($db->getErrorMsg()).'</p>'; 
	die;
}

$mainCurrName = $aCurrencies[(int)$mainCurrID]->currency_code_3;  // 3-букв.код главной валюты
$currencies = $shop->appendChild($xml->createElement('currencies'));
foreach ($aCurrencies as $aCur) {
	if($aCur->currency_code_3 == $mainCurrName) {
		$currency = $currencies->appendChild($xml->createElement('currency'));
		$currency->setAttribute('id',$aCur->currency_code_3);
		$currency->setAttribute('rate', '1');
	} elseif($aCur->currency_exchange_rate > 0) {
		$currency = $currencies->appendChild($xml->createElement('currency'));
		$currency->setAttribute('id',$aCur->currency_code_3);
		$currency->setAttribute('rate',$aCur->currency_exchange_rate);
	}
	/* сделать цены прайса в другой валюте:
	if($aCur->currency_code_3 == 'BYR')	$mainCurrID = $aCur->virtuemart_currency_id;
	*/
}

// список категорий
// этим пока не воспользовались:
// if(!class_exists('TableCategories')) require(JPATH_VM_ADMINISTRATOR.'/tables/categories.php');
// $categoryModel = VmModel::getModel('Category');
if(!class_exists('TableCategories')) require(JPATH_VM_ADMINISTRATOR.'/tables/medias.php');
$mediaModel = VmModel::getModel('Media');

$query = $db->getQuery(true);
$query->select( 'DISTINCT a.category_child_id, a.category_parent_id, b.category_name');
$query->from($tbvm_cats.' AS a');
$query->rightJoin($tbvm_cats_lang.' AS b ON b.virtuemart_category_id = a.category_child_id');
$query->order('a.category_parent_id,a.category_child_id');
$db->setQuery($query);
if (is_null($rows = $db->loadObjectList())) { 
	echo '<p>'.nl2br($db->getErrorMsg()).'</p>'; 
	die;
}
$list_cat_arr = explode(',', $list_categoryes);
$categories = $shop->appendChild($xml->createElement('categories'));
$list_parent_cat= array();
$category_name = array();


foreach ($rows as $i => $row) {
	
	if((is_null($row->category_parent_id)) && is_null($row->category_child_id)) {
		unset($rows[$i]);
		continue;
	}
	$cat_parent_id = (int)$row->category_parent_id;
	$cat_child_id = (int)$row->category_child_id;
	$cat_name = htmlspecialchars(trim(strip_tags($row->category_name)));
	if ($cat_name == '') {
		unset($rows[$i]);
		continue;
	}
	$category_name[$cat_child_id] = $cat_name;

	if(!$list_categoryes && $cat_child_id>0) { // пустой список
		$shop_cat = $categories->appendChild($xml->createElement('category'));
		$shop_cat->setAttribute('id',$cat_child_id);
		$shop_cat->appendChild($xml->createTextNode($cat_name));
		if ($cat_parent_id > 0) $shop_cat->setAttribute('parentId',$cat_parent_id);
		continue;
	}
	if($params['cat_list_control']) { // включить категории
		if(in_array($cat_parent_id, $list_cat_arr) && !in_array($cat_child_id, $list_cat_arr)) {
			array_push($list_cat_arr, $cat_child_id);
		}
		if($cat_parent_id>0 && in_array($cat_child_id, $list_cat_arr) && !in_array($cat_parent_id, $list_parent_cat)) {
			array_push($list_parent_cat, $cat_parent_id);
			$shop_cat = $categories->appendChild($xml->createElement('category'));
			$shop_cat->setAttribute('id',$cat_parent_id);
			$shop_cat->appendChild($xml->createTextNode($cat_name));
		}			
		if($cat_child_id>0 && in_array($cat_child_id, $list_cat_arr) && !in_array($cat_child_id, $list_parent_cat)) {
			array_push($list_parent_cat, $cat_child_id);
			$shop_cat = $categories->appendChild($xml->createElement('category'));
			$shop_cat->setAttribute('id',$cat_child_id);
			$shop_cat->appendChild($xml->createTextNode($cat_name));
			if ($cat_parent_id > 0) $shop_cat->setAttribute('parentId',$cat_parent_id);
		}
	} else { // исключить категории
		if(in_array($cat_parent_id, $list_cat_arr) && !in_array($cat_child_id, $list_cat_arr)) {
			array_push($list_cat_arr, $cat_child_id);
		}
		if(!in_array($cat_child_id, $list_cat_arr) && !in_array($cat_parent_id, $list_cat_arr)) {
			if($cat_parent_id > 0) {
				if (!in_array($cat_parent_id, $list_parent_cat) && in_array($cat_parent_id, $list_cat_arr)) {
					array_push($list_parent_cat, $cat_parent_id);
					$shop_cat = $categories->appendChild($xml->createElement('category'));
					$shop_cat->setAttribute('id',$cat_parent_id);
					$shop_cat->appendChild($xml->createTextNode($category_name[$cat_parent_id]));				
				}
			}
			$shop_cat = $categories->appendChild($xml->createElement('category'));
			$shop_cat->setAttribute('id',$cat_child_id);
			$shop_cat->appendChild($xml->createTextNode($cat_name));
			if ($cat_parent_id > 0) $shop_cat->setAttribute('parentId',$cat_parent_id);			
			continue; 
		}
	}
}

if($params['cat_list_control']) { // ещё раз, включить пропущенные категории вложенности от 4 уровня
	
	foreach($rows as $row) {
		
		$cat_parent_id = (int)$row->category_parent_id;
		$cat_child_id = (int)$row->category_child_id;

		if(in_array($cat_parent_id, $list_cat_arr)) {
			if(!in_array($cat_child_id, $list_cat_arr)) {
				array_push($list_cat_arr, $cat_child_id);
				$shop_cat = $categories->appendChild($xml->createElement('category'));
				$shop_cat->setAttribute('id',$cat_child_id);
				if ($cat_parent_id > 0) $shop_cat->setAttribute('parentId',$cat_parent_id);			
				$shop_cat->appendChild($xml->createTextNode($category_name[$cat_child_id]));
			}
		}
	}
}

// Информация о доставке
switch($params['delivery']) {
	case 1: // самовывоз
		$deliv = $shop->appendChild($xml->createElement('delivery'));
		$deliv->appendChild($xml->createTextNode("false"));
		break;
	case 2: // доставка включена в стоимость
		$deliv = $shop->appendChild($xml->createElement('delivery'));
		$deliv->appendChild($xml->createTextNode("true"));
		$deliv = $shop->appendChild($xml->createElement('deliveryIncluded'));
		$deliv = $shop->appendChild($xml->createElement('local_delivery_cost'));
		$deliv->appendChild($xml->createTextNode((float)$params['delivery_cost']));
		break;
	case 3: // стоимость доставки дополнительно
		$deliv = $shop->appendChild($xml->createElement('delivery'));
		$deliv->appendChild($xml->createTextNode("true"));
		$deliv = $shop->appendChild($xml->createElement('local_delivery_cost'));
		$deliv->appendChild($xml->createTextNode((float)$params['delivery_cost']));
		break;
}

if (isset($params['adult']) && $params['adult']) { 
	// Товары для удовлетворения сексуальных потребностей - тег <adult>
	$adult = $shop->appendChild($xml->createElement('adult'));
	$adult->appendChild($xml->createTextNode("true"));
}

if (isset($params['cpa'])) {
	switch((int)$params['cpa']) {
		case 1:
			$cpa = $shop->appendChild($xml->createElement('cpa'));
			$cpa->appendChild($xml->createTextNode("0"));
			break;
		case 2:
			$cpa = $shop->appendChild($xml->createElement('cpa'));
			$cpa->appendChild($xml->createTextNode("1"));
	}
}

$descr_field = '';
if (isset($params['prod_short_description']) && $params['prod_short_description']) $descr_field = 'b.product_s_desc, ';
if (isset($params['prod_full_description']) && $params['prod_full_description']) $descr_field .= 'b.product_desc, ';

// выбор типа цены
$price_type = ($params['price'])? (string)$params['price'] : false;

$group_child = false;
if(isset($params['group_child']) && $params['group_child']) {
	$group_id = 0;
	$group_list = array();
	$group_child = true;
}

// выбираем полную информацию по каждому наименованию товара из БД 

$query = $db->getQuery(true);
$query->select('a.virtuemart_product_id, pc.virtuemart_category_id')
			->from($tbvm_prod.' AS a')
			->leftjoin($tbvm_prod_cats.' AS pc ON a.virtuemart_product_id = pc.virtuemart_product_id')
			->leftjoin($tbvm_categories.' AS vc ON pc.virtuemart_category_id = vc.virtuemart_category_id')
			->leftjoin($tbvm_prod_manuf.' AS pm ON a.virtuemart_product_id = pm.virtuemart_product_id')
			->leftjoin($tbvm_manuf.' AS m ON pm.virtuemart_manufacturer_id = m.virtuemart_manufacturer_id')
			->where('a.published = '.$db->Quote('1'))
			->where('a.virtuemart_product_id NOT IN ('.$exclude_items.')')
			->where('((a.product_parent_id > 0) OR ((m.published IS NULL OR m.published = '.$db->Quote('1').') AND vc.published = '.$db->Quote('1').'))');
if($list_categoryes) {
	if($params['cat_list_control']) {
		$query->where('(pc.virtuemart_category_id IN ('.implode(",", $list_cat_arr).') OR a.product_parent_id > 0)');
	} else {
		$query->where('(pc.virtuemart_category_id NOT IN ('.implode(",", $list_cat_arr).') OR a.product_parent_id > 0)');
	}
}
$query->order('a.virtuemart_product_id, pc.virtuemart_category_id');
$db->setQuery($query);
$rows = $db->loadObjectList();
if (is_null($rows = $db->loadObjectList())) { 
	echo '<p>'.nl2br($db->getErrorMsg()).'</p>'; 
	ob_end_clean();
	die;
}

$pModel = VmModel::getModel('product');
$customfieldsModel = VmModel::getModel ('Customfields');
if(!class_exists ('vmCustomPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmcustomplugin.php');

$typePrefixOn = isset($params['type_prefix']) AND (int)$params['type_prefix'] AND isset($params['vendor_model']) AND $params['vendor_model'];

$id_list = array(); // список обработанных товаров для исключения дубликатов

$offers = $shop->appendChild($xml->createElement('offers'));

foreach ($rows as $row) {
	

	$product_id = $row->virtuemart_product_id;
		
	if(in_array($product_id, $id_list)) continue; // исключаем дубли по id товара
	$id_list[] = $product_id;
	
	// getProduct ($virtuemart_product_id = NULL, $front = TRUE, $withCalc = TRUE, $onlyPublished = TRUE, $quantity = 1,$virtuemart_shoppergroup_ids = 0)	
	$tmpProduct = $pModel->getProduct($product_id, true, true, true, 1);
	
	if(!$tmpProduct) continue;
	if($tmpProduct->product_parent_id) {
		$parentProduct = $pModel->getProduct($tmpProduct->product_parent_id, true, true, true, 1);
		if($parentProduct->published == 0) continue;
	}
	
	//new customfieldsforall start
	
	if (empty($tmpProduct->customfields) && !empty($tmpProduct->allIds)) {
		$tmpProduct->customfields = $customfieldsModel->getCustomEmbeddedProductCustomFields($tmpProduct->allIds);
	}
	if (!empty($tmpProduct->customfields)) {
		// $tmpProduct = clone($tmpProduct);
		$customfields = array();
		$customfield_id_list = array(); // обрабатываем дубли customfieldsforall
		foreach($tmpProduct->customfields as $cu){
			$customfield_id = $cu->virtuemart_custom_id;
			if(in_array($customfield_id, $customfield_id_list)) continue; //исключаем дубли custom_fields
			$customfield_id_list[] = $customfield_id;
			$customfields[] = clone ($cu);
		}

		$customfieldsSorted = array();
		$customfieldsModel -> displayProductCustomfieldFE($tmpProduct, $customfields);

		foreach ($customfields as $k => $custom) {
			if (!empty($custom->layout_pos)  ) {
				$customfieldsSorted[$custom->layout_pos][] = $custom;
				unset($customfields[$k]);
			}
		}
		$customfieldsSorted['normal'] = $customfields;
		$tmpProduct->customfieldsSorted = $customfieldsSorted;
		unset($tmpProduct->customfields);
	}

	//new customfieldsforall end

	if (isset($params['stock_enabled']) && $params['stock_enabled'] && isset($params['out_stock']) && $params['out_stock']) {
		if((int)$tmpProduct->product_in_stock < $min_quantity) continue;
	}
	
	if($group_child && $tmpProduct->product_parent_id == 0) {
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')
					->from($tbvm_prod)
					->where('product_parent_id = '.$product_id);
		$db->setQuery($query);
		$has_child_product = (boolean)$db->loadResult();
		if($has_child_product) { // this item has a child products
			if(!in_array($product_id, $group_list)) {
				$group_list[] = $product_id;
			}
			$product_group_id = $product_id;
		}
	}
	
	$product_name = htmlspecialchars(trim(strip_tags($tmpProduct -> product_name)));
	$product_cat_id = $row->virtuemart_category_id;
  $prod_vendor = $tmpProduct->mf_name;
  
	if($tmpProduct->product_parent_id) { // it is a child product!
		if($parentProduct) {
			if(!$product_cat_id) $product_cat_id = $parentProduct -> virtuemart_category_id;
			if(!$prod_vendor) $prod_vendor = $parentProduct -> mf_name;
			if($group_child) {
				if(!in_array($tmpProduct->product_parent_id, $group_list)) {
					$group_list[] = $tmpProduct->product_parent_id;
				}
				$product_group_id = $tmpProduct->product_parent_id;
			}
		}
	}
	
	if ((strlen($product_name)== 0) OR ($product_cat_id == 0)) continue;

	if($list_categoryes) {
		if($params['cat_list_control']) {
			if(!in_array($product_cat_id, $list_cat_arr)) continue;
		} else {
			if(in_array($product_cat_id, $list_cat_arr)) continue;
		}
	}
	
	$prices = $calculator->getProductPrices($tmpProduct);
	$price_type = ($price_type) ? $price_type : 'salesPrice'; // цена по-умолчанию
	if (method_exists('CurrencyDisplay','roundForDisplay') && method_exists('CurrencyDisplay','getInstance')) {
		$paymentCurrency = CurrencyDisplay::getInstance($mainCurrID);
		$price_value = $paymentCurrency->roundForDisplay($prices[$price_type],$mainCurrID,1.0,false);
		if(isset($prices['costPrice']) && $prices['costPrice']) {
			$cost_price = $paymentCurrency->roundForDisplay($prices['costPrice'],$mainCurrID,1.0,false);
		} else {
			$cost_price = 0;
		}
	} else {
		//  $price_value = $prices[$price_type] * $aCurrencies[$mainCurrID]->currency_exchange_rate;
		$price_value = $prices[$price_type];
		$cost_price = isset($prices['costPrice']) ? $prices['costPrice'] : 0;
	}
	if((float)$price_value <= 0) continue;
	
	if($tmpProduct->product_parent_id>0) {
		$link = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $tmpProduct->product_parent_id . '&virtuemart_category_id=' . $product_cat_id;
	} else {
		$link = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product_id . '&virtuemart_category_id=' . $product_cat_id;
	}

	if(!isset($params['urlnosef']) || $params['urlnosef'] == 0) {
		$link = ($vmVersion >= '2.6') ?  JRoute::_($link) : $tmpProduct -> link;
		$link = str_replace($plg_folder, '', ltrim($link,'/'));
		if(isset($params['transcode']) && $params['transcode']) $link = str_replace('%2F', DS, rawurlencode($link));
	}
	$url = $storeURL.DS.$link;
	
	$custfields = false;
	$tmpCustFields = array();
	$cpa_value = false;

	if(isset($tmpProduct->customfieldsSorted)) {
		$customfields = $tmpProduct->customfieldsSorted['normal'];
		if($customfields) {
			foreach ($customfields as $custfield) {
				$cf_title = JString::strtolower(str_replace(' ','',$custfield->custom_title));
				if(in_array($cf_title,	$specTags, true)) {
					if($custfield->field_type == 'B') {
						$custfield->custom_value = ($custfield->custom_value) ? 'true' : 'false';
						$tmpCustFields[$cf_title] = $custfield->custom_value;
					} elseif($custfield->custom_value) { 
						$tmpCustFields[$cf_title] = $custfield->custom_value;
					} elseif($custfield->customfield_value) {
						$tmpCustFields[$cf_title] = $custfield->customfield_value;
					}
				}
			}
		}
	}
	
	$offer = $offers->appendChild($xml->createElement('offer'));
	$offer->setAttribute('id',$product_id);
	
	if(isset($params['stock_enabled']) && $params['stock_enabled']) {
		$tmpAvailable = ((int)$tmpProduct->product_in_stock >= $min_quantity) ? 'true' : 'false';
	} elseif(isset($tmpCustFields['available'])) {
		$tmpAvailable = $tmpCustFields['available'];
	} else {
		$tmpAvailable = 'true';
	}
	$offer->setAttribute('available',$tmpAvailable);
		
	$seturl = $offer->appendChild($xml->createElement('url'));
	$seturl->appendChild($xml->createTextNode($url));

	$price = $offer->appendChild($xml->createElement('price'));
	$price->appendChild($xml->createTextNode($price_value));
	
	if($params['costprice'] && $cost_price>0 && $price_value>0) {
		$sale = ($cost_price-$price_value)/$cost_price; // скидка от единицы (не меньше 5% и не больше 95%)
		if($sale >= 0.05 && $sale <= 0.95) {
			$price = $offer->appendChild($xml->createElement('oldprice'));
			$price->appendChild($xml->createTextNode($cost_price));
		}
	}
	
	$currencyId = $offer->appendChild($xml->createElement('currencyId'));
	$currencyId->appendChild($xml->createTextNode($aCurrencies[$mainCurrID]->currency_code_3));
	$categoryId = $offer->appendChild($xml->createElement('categoryId'));
	$categoryId->appendChild($xml->createTextNode($product_cat_id));

	if(isset($tmpCustFields['market_category']) && $tmpCustFields['market_category']) {
		$tmpMac = $offer->appendChild($xml->createElement('market_category'));
		$tmpMac->appendChild($xml->createTextNode($tmpCustFields['market_category']));
	}	
	
	if($params['include_pictures']) {

		$product_images = array();	
		
		// главное изображение товара
		$pModel->addImages($tmpProduct, $vendorId);
		if(!empty($tmpProduct->images[0])) {
			$image =  $tmpProduct->images[0];
			if($image->file_name) {
				$img = new stdClass();
				$img->file_name = $image->file_name;
				$img->file_url = $image->file_url;
				$product_images[] = $img;
			}
		}
			
		// дополнительные изображения товара
		$media = VmModel::getModel('media');
		$files = $media->getFiles(false,false,$product_id,$product_cat_id);

		if(!empty($files)) {		
			foreach($files as $image) {
				if(JString::strpos($image->file_mimetype,'image')!== false) {
					// пропускаем, если это главное изображение или пусто
					if(empty($image->file_name) || $image->file_name === $product_images[0]->file_name) continue;
					$img = new stdClass();
					$img->file_name = $image->file_name;
					$img->file_url = $image->file_url;
					$product_images[] = $img;
				}
			}
		}
		if(count($product_images)==0) {
			// у товара нет картинки, берём картинку категории
			$files = $mediaModel->getFiles(false,false,null,$product_cat_id);
			if($files) {
				if($files[0]->file_name) {
					$image = $files[0]->file_url;
					$img = new stdClass();
					$img->file_name = $image->file_name;
					$img->file_url = $image->file_url;
					$product_images[] = $img;
				}
			}
		}
		if(count($product_images)) {
			foreach($product_images as $img) {
				$picture = $offer->appendChild($xml->createElement('picture'));
				if(isset($params['transcode']) && $params['transcode']) {
					$link = str_replace('%2F', '/', rawurlencode($img->file_url));
				} else {
					$link = str_replace(' ', '%20', $img->file_url);
				}
				$picture->appendChild($xml->createTextNode($storeURL.DS.$link));
			}
		}
	}
	
	if(isset($tmpCustFields['pickup']) && $tmpCustFields['pickup']) {
		$tmpPickup = $offer->appendChild($xml->createElement('pickup'));
		$tmpPickup->appendChild($xml->createTextNode($tmpCustFields['pickup']));
	}

	if(isset($tmpCustFields['delivery']) && $tmpCustFields['delivery']) {
		$deliv = $offer->appendChild($xml->createElement('delivery'));
		$deliv->appendChild($xml->createTextNode($tmpCustFields['delivery']));
	} else {
		// Информация о доставке
		switch($params['delivery']) {
		case 1: // самовывоз
			$deliv = $offer->appendChild($xml->createElement('delivery'));
			$deliv->appendChild($xml->createTextNode("false"));
			break;
		case 2: // доставка включена в стоимость
			$deliv = $offer->appendChild($xml->createElement('delivery'));
			$deliv->appendChild($xml->createTextNode("true"));
			$deliv = $offer->appendChild($xml->createElement('deliveryIncluded'));
			$deliv = $offer->appendChild($xml->createElement('local_delivery_cost'));
			$deliv->appendChild($xml->createTextNode((float)$params['delivery_cost']));
			break;
		case 3: // стоимость доставки дополнительно
			$deliv = $offer->appendChild($xml->createElement('delivery'));
			$deliv->appendChild($xml->createTextNode("true"));
			$deliv = $offer->appendChild($xml->createElement('local_delivery_cost'));
			if($params['delivery_free'] && ($prices[$price_type] >= $params['delivery_free'])) {
				$deliv->appendChild($xml->createTextNode('0'));
			} else {
				$deliv->appendChild($xml->createTextNode((float)$params['delivery_cost']));
			}
			break;
		}
	}
	
	$vendorModel = (isset($params['vendor_model']) && $params['vendor_model'] && $prod_vendor!="0" && strlen($prod_vendor)>0);

	if (!$vendorModel) {
		// если не тип vendor.model, то добавляем name
		$name = $offer->appendChild($xml->createElement('name'));
		$name->appendChild($xml->createTextNode($product_name));
	} else {
		$offer->setAttribute('type','vendor.model');
	}
		
	// если нужно, вывести тег typePrefix
	if(isset($tmpCustFields['typeprefix']) && $tmpCustFields['typeprefix']) {
		$tmpTypePrefix = $offer->appendChild($xml->createElement('typePrefix'));
		$tmpTypePrefix->appendChild($xml->createTextNode($tmpCustFields['typeprefix']));
	}
	
	// сокращаем название производителя до первой запятой
	$prod_vendor = strtok($prod_vendor,",");

	if($prod_vendor!="0" && strlen($prod_vendor)>0) {
		$vendor = $offer->appendChild($xml->createElement('vendor'));
		$vendor->appendChild($xml->createTextNode(htmlspecialchars($prod_vendor)));
	}
	
	if($group_child && ($tmpProduct->product_parent_id || $has_child_product)) $offer->setAttribute('group_id',$product_group_id); // группа товаров с вариациями цвет, размер
		
	if (isset($params['sku']) && $prod_vendor) {
		$vc_value = false;
		switch($params['sku']) {
			case 'sku':
				if(isset($tmpProduct -> product_sku) && ($tmpProduct -> product_sku)) {
					$vc_value = $tmpProduct -> product_sku;
				}
				break;
			case 'gtin':
				if(isset($tmpProduct -> product_gtin) && ($tmpProduct -> product_gtin)) {
					$vc_value = $tmpProduct -> product_gtin;
				}
				break;
			case 'mpn':
				if(isset($tmpProduct -> product_mpn) && ($tmpProduct -> product_mpn)) {
					$vc_value = $tmpProduct -> product_mpn;
				}
				break;
		}
		if ($vc_value) {
			$vendorCode = $offer->appendChild($xml->createElement('vendorCode'));
			$vendorCode->appendChild($xml->createTextNode($vc_value));
		}
	}
		
	if ($vendorModel) {
		if (isset($params['clear_vendor']) && $params['clear_vendor']) {
			// если в названии товара есть название производителя, то убираем его оттуда вместе с разными видами кавычек
			$product_name = clearVendorModel($product_name, $prod_vendor);
		}
		$model = $offer->appendChild($xml->createElement('model'));
		$model->appendChild($xml->createTextNode($product_name));
	}

	$prod_desc = '';
	if (isset($params['prod_short_description']) && $params['prod_short_description']) $prod_desc = (string)$tmpProduct -> product_s_desc.' ';
	if (isset($params['prod_full_description']) && $params['prod_full_description']) $prod_desc .= (string)$tmpProduct -> product_desc;
	$prod_desc = trim($prod_desc);
	if (!empty($prod_desc)) {
			$description = $offer->appendChild($xml->createElement('description'));
			if (isset($params['cdata']) && $params['cdata']) {
				$description->appendChild($xml->createCDATASection($prod_desc));
			} else {
				$prod_desc = trim(clearProductDescription($prod_desc));
				$description->appendChild($xml->createTextNode($prod_desc));
			}
	}

	$tmpSalesNotes = $sales_notes;
	if(isset($tmpCustFields['sales_notes'])) $tmpSalesNotes = $tmpCustFields['sales_notes'];
	if($tmpSalesNotes) {
		$notes_tag = $offer->appendChild($xml->createElement('sales_notes'));
		$notes_tag->appendChild($xml->createTextNode($tmpSalesNotes));
	}

	// Официальная гарантия на все товары - тег <manufacturer_warranty>
	
	$warr_value = 0;
	$warr_iso = '';
	if ( isset($tmpCustFields['manufacturer_warranty']) && $tmpCustFields['manufacturer_warranty'] ) {
		$warr_iso = $tmpCustFields['manufacturer_warranty'];
	} elseif ( isset($tmpCustFields['гарантия']) && $tmpCustFields['гарантия'] ) {
		$warr_iso = $tmpCustFields['гарантия'];
	}
	if($warr_iso) {
		if(JString::substr($warr_iso, 0, 1) != 'P') $warr_iso = 'P' . str_replace($warr_words, $warr_sign, $tmpCustFields['гарантия']);
		$xmlwarranty = $offer->appendChild($xml->createElement('manufacturer_warranty'));
		$xmlwarranty->appendChild($xml->createTextNode($warr_iso));
	}

	// Downloadable
	if(isset($tmpCustFields['downloadable']) && $tmpCustFields['downloadable']) {
		$tmpDL = $offer->appendChild($xml->createElement('downloadable'));
		$tmpDL->appendChild($xml->createTextNode($tmpCustFields['downloadable']));
	}
	
	$cofo = '';
	if(isset($tmpCustFields['country_of_origin']) && $tmpCustFields['country_of_origin']) {
		$cofo = trim($tmpCustFields['country_of_origin']);
	} elseif(isset($tmpCustFields['страна']) && $tmpCustFields['страна']) {
		$cofo = trim($tmpCustFields['страна']);
	}
	if($cofo) {
		$tmpCO = $offer->appendChild($xml->createElement('country_of_origin'));
		$tmpCO->appendChild($xml->createTextNode($cofo));
	}
	
	// артикул товара
	if (!$vendorModel && isset($params['sku']) && !$prod_vendor && $params['sku'] && ($tmpProduct -> product_sku)) {
		$param = $offer->appendChild($xml->createElement('param'));
		$param->setAttribute('name', 'Артикул');
		$param->appendChild($xml->createTextNode($tmpProduct -> product_sku));
	}
	
	if(isset($params['custfields']) && !empty($tmpProduct->customfieldsSorted)){
		foreach ($tmpProduct->customfieldsSorted as $fsetname => $fieldset) :
			if(!empty($fieldset)) {
				foreach ($fieldset as $field)
				{
					if($field->is_hidden && $field->admin_only) continue;
					if($fsetname == 'normal') {
						if(empty($field->custom_value)) continue;
						$cfn = JString::strtolower(str_replace(' ','',$field->custom_title));
						if(array_key_exists($cfn,$tmpCustFields)) continue;
						$param = $offer->appendChild($xml->createElement('param'));
						$param->setAttribute('name', $field->custom_title);
						$param->appendChild($xml->createTextNode($field->custom_value));
					} else {
						if(empty($field->customfield_value) || $field->customfield_value=='param') continue;
						if($field->customfield_value == "sttstockable") {
							if($v = $field->child->$product_id->selectoptions1) {
								$param = $offer->appendChild($xml->createElement('param'));
								$param->setAttribute('name', $field->selectname1);
								$param->appendChild($xml->createTextNode($v));
							}
						} else {
							$param = $offer->appendChild($xml->createElement('param'));
							$param->setAttribute('name', $field->custom_title);
							$param->appendChild($xml->createTextNode($field->customfield_value));
						}
					}
				}
			}
		endforeach;
	}
	
	// стандартные настраиваемые поля товара
	/* 
	if(isset($params['custfields']) && $params['custfields'] && $custfields) {
		foreach ($custfields as $custfield) {
			$cond = !($custfield->admin_only || $custfield->is_hidden);
			$cond = $cond && $custfield->custom_title && ($custfield->custom_value || $custfield->custom_value === '0'); // если название и значение не пустые
			if ($cond) {
				$cfvalue = $custfield->custom_value;
				if($custfield->field_type == 'B') {
					$cfvalue = ($cfvalue) ? 'Да' : 'Нет';
				}
				$param = $offer->appendChild($xml->createElement('param'));
				$param->setAttribute('name', $custfield->custom_title);
				$param->appendChild($xml->createTextNode(strip_tags($cfvalue)));
			}
		}
	}
	*/
	
	if($params['cpa']) {
		foreach($cpa_names as $cpa_name) {
			if(isset($tmpCustFields[$cpa_name])) {
				$cpa_value = JString::strtolower($tmpCustFields[$cpa_name]);
				$cpa_value = str_replace(' ','',$cpa_value);
				if(in_array($cpa_value,$cpa_values_no, true)) {
					$cpa = $offer->appendChild($xml->createElement('cpa'));
					$cpa->appendChild($xml->createTextNode("0"));
				} elseif(in_array($cpa_value,$cpa_values_yes, true)) {
					$cpa = $offer->appendChild($xml->createElement('cpa'));
					$cpa->appendChild($xml->createTextNode("1"));
				}
				break;
			}
		}
	}
	
	unset($custfields, $tmpCustFields, $rows);
	
}

$xml->formatOutput = true;
if ($params['export_method']) {
	// save result into file
	header("Content-Type: text/html; charset=UTF-8");
	$filename = $export_filename.'.xml';
	$fsurl = rtrim($params['export_path2save'], '/').'/';
	if ($params['export_path2save']) {
		if(substr($params['export_path2save'],0,1) == '/') {
			$path2save = $_SERVER["DOCUMENT_ROOT"].$fsurl;
		} else {
			$fsurl = $plg_folder.$fsurl;
			$path2save = $_SERVER["DOCUMENT_ROOT"].$fsurl;
		}
		if(!file_exists($path2save)) {
			echo "<h2>Нет такой папки на сайте!</h2>";
			echo "<p><code>".$fsurl."</code></p>";
			echo "<p>Исправьте значение параметра: [ <strong>Путь к файлу</strong> ] или создайте эту папку вручную.</p>";
			die;
		}
	} else {
		$fsurl = "/tmp/";
		$path2save = $app->getCfg('tmp_path').'/';
	}
	$fssrv = $path2save.$export_filename;
	$fsurl = $fsurl.$export_filename;
	echo '<p>Данные сохранены в файл <a href="'.$fsurl.'">'.$export_filename.'</a> ('.$xml->save($fssrv).' байт).</p>';
} else {
	// динамический файл (php)
	header("Content-Type: application/xml; charset=UTF-8");
	echo $xml->saveXML();
}