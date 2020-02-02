<?php
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);
/**
* Param Filter: Virtuemart 2 search module
* Version: 3.0.6 (2015.11.23)
* Author: Dmitriy Usov
* Copyright: Copyright (C) 2012-2015 usovdm
* License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://myext.eu
**/

require_once('helper.php');
vmJsApi::jQuery();
vmJsApi::cssSite();

if (!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'currencydisplay.php');
$customfieldsModel = new VirtueMartModelCustomfields();
$cache = JFactory::getCache('com_virtuemart','callback');

$doc = JFactory::getDocument();
global $param_search_ids;
$param_search_ids = array();
$multiple = $chosen = $slider = false;
$categories_html = $manufacturers_html = $price_html = '';

/* ===== + Params ===== */
$class_sfx = $params->get('class_sfx', '');
$moduleclass_sfx = $params->get('moduleclass_sfx','');
$module_id = $module->id;

$method = $params->get('method','get');
$layout = $params->get('layout','default');
$limit = $params->get('limit','10');
$Itemid = (int)$params->get('itemid','');
$show_all_count = $params->get('show_all_count',true);
$stock = $params->get('stock',false);
$children = $params->get('children',-1);
$count_show = $params->get('count_show',false);
$count_zero_show = $count_zero_show_tmp = $params->get('count_zero_show','disable');
	$count_zero_show_txt = $count_zero_show_txt_tmp = $count_zero_show == 'disable' ? ' disabled="disabled"' : '';
$mcf_mod = str_replace(array('x','i'),'',$params->get('mcf_mod'));
$mcf_mod_ajax = $params->get('mod_ajax',true);
$mcf_mod_ajax_div = $params->get('mod_ajax_div');
$mcf_mod_ajax_css = '';
if($mcf_mod_ajax)
	$mcf_mod_ajax_css .= ' mcf_mod_ajax';
if($mcf_mod_ajax_div)
	$mcf_mod_ajax_css .= ' mcf_mod_ajax_div';
$mcf_init = $params->get('mcf_init','');
$mcf_reinit_start = $params->get('mcf_reinit_start','');
$mcf_reinit_mod = $params->get('mcf_reinit_mod','');
$mcf_body = $params->get('mcf_body');
$mcf_reinit_body = $params->get('mcf_reinit_body','');
$mcf_body = explode(';',$mcf_body);
$mcf_init_mod = array();
$mcf_init_mod[] = $mcf_mod($mcf_body[0]);
$mcf_init_mod[] = $mcf_init_mod[0]($mcf_mod($mcf_body[1]));
	
$mcf_prepend_text = $params->get('mcf_prepend_text','');
$mcf_append_text = $params->get('mcf_append_text','');
	
$mcf_mod_uniq_display = $params->get('mcf_mod_uniq_display',true);
$mcf_mod_uniq_result = $params->get('mcf_mod_uniq_result',false);

$mcf_view_assign = $params->get('view_assign',array(-1));
$mcf_category_assign = $params->get('category_assign',array(0));

$parent_id = $params->get('Parent_Category_id',null); //parent customfield id !!!
$parent_auto = $params->get('parent_auto',false);
$categories_show = $params->get('categories_show',true);
$loader_img = $params->get('loader_img','aload-blue_on_white').'.gif';
$in_active_category = $params->get('in_active_category',false);
$mcf_only_subcategories = $params->get('mcf_only_subcategories',0);
$mcf_subcategories = $params->get('mcf_subcategories',0);
$mcf_init_body = $mcf_mod($mcf_body[2]);
$mcf_init_mod = $mcf_init_body('',$mcf_init_mod[1]);
$mcf_category_heading = JText::_($params->get('categories_heading',''));
$mcf_category_select_heading = JText::_($params->get('categories_select_heading',''));
$categories_layout = $params->get('categories_layout','checkbox');
$categories_toshow = $params->get('categories_toshow',array());
$categories_toshow = array_diff($categories_toshow,array(0));

$manufacturers_show = $params->get('manufacturers_show',true);
$mcf_manufacturers_heading = JText::_($params->get('manufacturers_heading',''));
$mcf_manufacturers_select_heading = JText::_($params->get('manufacturers_select_heading',''));
$manufacturers_layout = $params->get('manufacturers_layout','checkbox');

$price_show = $params->get('price_show',true);
$mcf_price_heading = JText::_($params->get('price_heading',''));
$price_discount = $params->get('price_discount',0);
$price_mcur = $params->get('price_mcur',0);
$price_layout = $params->get('price_layout','input');
$mcf_price_select_heading = JText::_($params->get('price_select_heading',''));

$customfields_show = $params->get('customfields_show',true);
$mcf_customfields_select_heading_global = JText::_($params->get('customfields_select_heading',''));
$customfields_layout = $params->get('customfields_layout','auto');
/* ===== - Params ===== */

$mcf_tmpl_option = JRequest::getVar('option');
$mcf_tmpl_view = JRequest::getVar('view');
$mcf_module_id = JRequest::getInt('mcf_id');
$preload_virtuemart_category_id = JRequest::getInt('preload_virtuemart_category_id', 0);
$opened_category_id = JRequest::getInt('virtuemart_category_id', 0);
$active_category_id = $params->get('active_category_id',JRequest::getInt('virtuemart_category_id', 0));
$active_category_id = !JRequest::getInt('virtuemart_category_id',0) && JRequest::getInt('ac', 0) > 0 ? JRequest::getInt('ac', 0) : $active_category_id;
if($mcf_only_subcategories && $in_active_category){
	$active_category_id = JRequest::getInt('osc',0) > 0 ? JRequest::getInt('osc', 0) : $active_category_id;
}
$mcf_search = JRequest::getBool('search', false);
$mcf_ajax = JRequest::getInt('mcf_ajax',0);

$price_left = JRequest::getVar('pl','');
$price_right = JRequest::getVar('pr','');

// VM2 cached pagination fix
// if($opened_category_id){
	// $session = JFactory::getSession();
	// $session->set('vmlastvisitedcategoryid', (int) $opened_category_id, 'vm');
// }

if($mcf_ajax){
	$cids = JRequest::getVar('cids',array());
}else{
	$cids = JRequest::getVar('cids',array($active_category_id));
}
foreach($cids as &$v){
	$v = (int)$v;
}unset($v);

$show_assing_filter = true;
$show_filter = false;
if($mcf_view_assign[0] != '-1' && $mcf_tmpl_option == 'com_virtuemart'){
	if(!in_array($mcf_tmpl_view,$mcf_view_assign)){
		$show_assing_filter = false;
	}
}
if($mcf_category_assign[0] == 0)
	unset($mcf_category_assign[0]);
if(count($mcf_category_assign) > 0){
	if(!in_array($opened_category_id,$mcf_category_assign)){
		$show_assing_filter = false;
	}
}
$param_search_ids_clear = false;
if($mcf_search){
	if(!$mcf_mod_uniq_display || $mcf_module_id == $module_id){
		$show_assing_filter = true;
	}else{
		$show_assing_filter = false;
	}
	if($mcf_mod_uniq_result && $mcf_module_id != $module_id){
		$param_search_ids = '-1';
		$param_search_ids_clear = true;
	}
}

global $html;
$categories = $html = '';
if($show_assing_filter){
	/* ===== + Categories load -> tmpl ===== */
	
	if($mcf_subcategories || $categories_show){
		$categories = getVmCategories();
		if(!empty($categories_toshow)){
			foreach($categories as $k => &$v){
				if(in_array($k,$categories_toshow)){
					$categories_tmp[$k] = $v;
				}
			}
			$categories = $categories_tmp;
		}unset($v);
	}
	$parent_category_id = $mcf_only_subcategories && $active_category_id > 0 ? $active_category_id : 0;
	if(false!=$mcf_init_mod()&&$categories_show){
		$show_filter = true;
		require(JModuleHelper::getLayoutPath('mod_virtuemart_param_filter','categories'.DS.$categories_layout.DS.'default')); // Generate html
		$categories_html  = $html;
	}else{
		$categories_html  = $html;
	}
	if($param_search_ids_clear && $param_search_ids == ''){
		$param_search_ids = '-1';
	}
	if(($parent_auto || $in_active_category) && !$mcf_only_subcategories && $active_category_id > 0){
		if(empty($param_search_ids) && empty($price_left) && empty($price_right) && !$param_search_ids_clear){
			$param_search_ids = getProductsByCategory($cids);
		}
		// $categories_html  = $html;
		// $categories_html .= '<input type="hidden" name="virtuemart_category_id" value="'.$active_category_id.'" />';
		if(!$categories_show){
			$categories_html .= '<input type="hidden" name="cids[]" value="'.$active_category_id.'" />';
		}
		$categories_html .= '<input type="hidden" name="ac" value="'.$active_category_id.'" />'; // active_category
	}
	if($mcf_only_subcategories && $in_active_category && $active_category_id > 0){
		$cids = array_diff($cids,array('',null));
		if(count($cids) == 0){
			$cids[] = $active_category_id;
		}
		// $categories_html .= '<input type="hidden" name="cids[]" value="'.$active_category_id.'" />';
	}
	if($mcf_subcategories){
		if (!class_exists('VirtueMartModelCategory')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'category.php');
		$category_model = new VirtueMartModelCategory();
		$subcategories = array();
		foreach($cids as &$v){
			$subcategories[] = (int)$v;
			$category_child = array();
			$category_model->_noLimit = true;
			// vendorId fixed by clone rekurseCats
				// fixed empty vendor for categories SQL queries
				/* if(!VmConfig::isSuperVendor()){
					if (!class_exists('vmAccess')){
						VmConfig::$_virtuemart_vendor_id[Null] = 1;
					}else{
						echo 'error';
						// $_virtuemart_vendor_id moved to vmAccess class in administrator/components/com_virtuemart/models/config.php
					}
				} */


			if($v === 0){
				$category_child = $category_model->getCategories(true, (int)$v);
			}else{
				mcfRekurseCats($category_model,(int)$v,0,1,'',$category_child);
			}
			foreach($category_child as &$child){
				$subcategories[] = $child->virtuemart_category_id;
			}unset($child);
		}unset($v);
		$categories_html .= '<input type="hidden" name="sc" value="1" />'; // учитывать подкатегории
	}
	if($mcf_only_subcategories && $in_active_category && $active_category_id > 0){
		$categories_html .= '<input type="hidden" name="osc" value="'.$active_category_id.'" />'; // показать только подкатегории
	}
	if($children >= 0){
		$categories_html .= '<input type="hidden" name="ch" value="'.$children.'" />'; // искать по дочерним
	}
	if((($active_category_id > 0 && $in_active_category)
		|| $stock || $children >= 0) 
		&& !$mcf_search){
		$categories_html .= '<input type="hidden" name="preload_virtuemart_category_id" value="'.$active_category_id.'" />';
	}
	
	$cids_adv = $mcf_subcategories ? $subcategories : $cids;
	if(($parent_auto || $in_active_category) && $active_category_id > 0 && $mcf_subcategories && !empty($cids_adv) && empty($param_search_ids)) {
		$param_search_ids = getProductsByCategory($cids_adv);
	}

	/* ===== - Categories load -> tmpl ===== */

	/* ===== + Manufacturer load -> tmpl ===== */
	$manufacturers = $html = '';
	if($manufacturers_show){
		$show_filter = true;
		$manufacturers = getManufacturers();
		$manufacturers_count = getCountManufacturers($param_search_ids,$children);
		
		$mids = JRequest::getVar('mids',array());
		$mids = array_diff($mids,array(''));
		require(JModuleHelper::getLayoutPath('mod_virtuemart_param_filter','manufacturers'.DS.$manufacturers_layout)); // Generate html
		$manufacturers_html = $html;
	}
	/* ===== - Manufacturer load -> tmpl ===== */

	/* ===== + Price load -> tmpl ===== */
	$html = '';
	if($price_show){
		$show_filter = true;
		if($parent_auto || $in_active_category){
			$price_limits = getPriceLimits($price_discount,$stock,$price_mcur,$cids_adv);
			$price_limits_visible = getPriceLimits($price_discount,$stock,$price_mcur,$cids_adv,$param_search_ids);
		}else{
			$price_limits = getPriceLimits($price_discount,$stock,$price_mcur);
			$price_limits_visible = getPriceLimits($price_discount,$stock,$price_mcur,array(),$param_search_ids);
		}
		if($price_discount > 0){
			$html .= '<input type="hidden" name="d" value="'.$price_discount.'" />';
		}
		if($price_mcur){
			$html .= '<input type="hidden" name="mcur" value="1" />';
			$tmp_price_limits = array('min'=>'','max'=>'');
			$currency = CurrencyDisplay::getInstance();
			foreach($price_limits as $v){
				if($price_mcur && $v->product_currency != $currency->getId()){
					$tmp_min = $currency->convertCurrencyTo($v->product_currency,$v->min,1);
					$tmp_max = $currency->convertCurrencyTo($v->product_currency,$v->max,1);
					$v->min = $currency->convertCurrencyTo($currency->getId(),$tmp_min,0);
					$v->max = $currency->convertCurrencyTo($currency->getId(),$tmp_max,0);
				}
				if(empty($tmp_price_limits['min']) || $v->min < $tmp_price_limits['min']){
					$tmp_price_limits['min'] = $v->min;
				}
				if(empty($tmp_price_limits['max']) || $v->max > $tmp_price_limits['max']){
					$tmp_price_limits['max'] = $v->max;
				}
			}
			$price_limits[0] = (object)$tmp_price_limits;
			if($price_limits[0]->min > $price_left){
				$price_limits[0]->min = $price_left;
			}
			if($price_limits[0]->max < $price_right){
				$price_limits[0]->max < $price_right;
			}
			$tmp_price_limits = array('min'=>'','max'=>'');
			foreach($price_limits_visible as $v){
				if($price_mcur && $v->product_currency != $currency->getId()){
					$tmp_min = $currency->convertCurrencyTo($v->product_currency,$v->min,1);
					$tmp_max = $currency->convertCurrencyTo($v->product_currency,$v->max,1);
					$v->min = $currency->convertCurrencyTo($currency->getId(),$tmp_min,0);
					$v->max = $currency->convertCurrencyTo($currency->getId(),$tmp_max,0);
				}
				if(empty($tmp_price_limits['min']) || $v->min < $tmp_price_limits['min']){
					$tmp_price_limits['min'] = $v->min;
				}
				if(empty($tmp_price_limits['max']) || $v->max > $tmp_price_limits['max']){
					$tmp_price_limits['max'] = $v->max;
				}
			}
			$price_limits_visible[0] = (object)$tmp_price_limits;
		}
		require(JModuleHelper::getLayoutPath('mod_virtuemart_param_filter','price'.DS.$price_layout)); // Generate html
		$price_html = $html;
	}
	/* ===== - Price load -> tmpl ===== */

	/* ===== Customfields load -> preload ===== */
	$types = array();
	if($customfields_show){
		$custom_ids = JRequest::getVar('cpi', array()); // Собираем переданные поля
		foreach($custom_ids as &$v){
			$v = (int)$v;
		}unset($v);
		if($parent_auto && count($cids_adv) > 0 && $mcf_mod_ajax){ // Автоматическая подборка полей + АЯКС = подгружать поля только из товаров отмеченных категорий
			$types = $cache->call('getCategoryCustomfields',$cids_adv,$custom_ids);
		}elseif($parent_auto && $active_category_id > 0){ // Автоматическая подборка полей - АЯКС + открытая категория = подгружать поля только из товаров открытой категории
			$types = $cache->call('getCategoryCustomfields',$active_category_id,$custom_ids);
		}elseif($parent_auto && $mcf_mod_ajax){ // Автоматическая подборка полей + АЯКС - отмеченнык категории - открытая категория
			//
		}elseif($parent_id != null){
			$types = getCustomfields($parent_id,array()); // поля указанных родитей
		}elseif(count($custom_ids) > 0){
			$types = getCustomfields($parent_id,$custom_ids); // все передаваемые поля
		}
		if(is_array($types) && count($types) > 0){
			$pre_int_values = $cache->call('getCustomIntValues',0,$children); // инициализация всех ручных числовых полей
			$pre_int_values_visible = $cache->call('getCustomIntValues',$param_search_ids,$children); // инициализация активных ручных числовых полей
			$type_req = $types[0];
		}else{
			$types = array();
		}
	}
	if(count($types) == 0){
		$type_req = getCustomfields('',array(),1);
		$type_req = @$type_req[0];
	}else{
		$types_ids = array();
		foreach($types as &$v){
			$types_ids[] = $v->virtuemart_custom_id;
		}unset($v);
		$show_filter = true;
		$custom_values = getCustomValues($types_ids);
		$custom_int_count = getCustomfieldCount($types_ids,$param_search_ids,array(),'int',$children,$stock);
		
		$custom_text_count = getCustomfieldCount($types_ids,$param_search_ids,array(),'text',$children,$stock);
	}

	/* ===== Module tmpl ===== */
	require(JModuleHelper::getLayoutPath('mod_virtuemart_param_filter',$layout));
	if($chosen){
		$doc->addStyleSheet($doc->baseurl."/components/com_virtuemart/assets/css/chosen.css");
		$doc->addScript($doc->baseurl."/components/com_virtuemart/assets/js/chosen.jquery.min.js");
	}
	if($params->get('mcf_jqueryui',1)){
		$doc->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js');
		$doc->addScript($doc->baseurl."/modules/mod_virtuemart_param_filter/assets/jquery.ui.touch-punch.js");
	}
	$doc->addScriptDeclaration(PHP_EOL .'mcf_reinit["#paramfilter-'.$module_id.'-start"] = function(){'."\n".$mcf_reinit_start."\n".'};');
	$doc->addScriptDeclaration(PHP_EOL .'mcf_reinit["#paramfilter-'.$module_id.'-mod"] = function(){'."\n".$mcf_reinit_mod."\n".'};');
	$doc->addScriptDeclaration(PHP_EOL .'mcf_reinit["#paramfilter-'.$module_id.'-div"] = function(){'."\n".$mcf_reinit_body."\n".'};');
	
	$doc->addScript($doc->baseurl."/modules/mod_virtuemart_param_filter/assets/js.js");
	$doc->addStyleSheet($doc->baseurl."/modules/mod_virtuemart_param_filter/assets/style.css");
}










