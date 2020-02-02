<?php
defined('_JEXEC') or die('Restricted access');
/**
* Param Filter: Virtuemart 2 search module
* Version: 3.0.6 (2015.11.23)
* Author: Dmitriy Usov
* Copyright: Copyright (C) 2012-2015 usovdm
* License GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
* http://myext.eu
**/

if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
$config= VmConfig::loadConfig();
if (!class_exists( 'VirtueMartModelCustomfields' )) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'customfields.php');

function getVmCategories(){
	$db = JFactory::getDBO();
	$q  = 'SELECT c.`virtuemart_category_id`, cl.`category_name`, cc.`category_parent_id`, cc.`category_child_id` FROM `#__virtuemart_categories` AS c';
	$q .= ' JOIN `#__virtuemart_categories_'.VMLANG.'` AS cl using (`virtuemart_category_id`)';
	$q .= ' JOIN `#__virtuemart_category_categories` AS cc ON cl.`virtuemart_category_id` = cc.`category_child_id`';
	$q .= ' WHERE c.published = "1" ORDER BY c.`ordering`';
	$db->setQuery($q);
	return $db->loadObjectList('virtuemart_category_id');
}

function mcfRekurseCats(&$category_model,$virtuemart_category_id,$level,$onlyPublished,$keyword,&$sortedCats,$deep=0){
	// to clone VirtueMartModelCategory::rekurseCats to fix vendorId
	$level++;

	if(($deep===0 or $level <= $deep) and $childs = $category_model->hasChildren($virtuemart_category_id)){

		$childCats = $category_model->getCategories($onlyPublished, $virtuemart_category_id, false, $keyword, 1); // set vendorId to 1
		if(!empty($childCats)){

			$siblingCount = count($childCats);
			foreach ($childCats as $key => $category) {
				$category->level = $level;
				$category->siblingCount = $siblingCount;
				$sortedCats[] = $category;
				mcfRekurseCats($category_model,$category->virtuemart_category_id,$level,$onlyPublished,$keyword,$sortedCats,$deep);
			}
		}
	}
}


function getCategoriesChildTree($categories,$category){
	$child = ''.$category;
	foreach($categories as $k => $v){
		if($v->category_parent_id == $category){
			$child .= ','.getCategoriesChildTree($categories, $v->virtuemart_category_id);
		}
	}
	return $child;
}

function getCategoriesCount($categories,$ids){
	$db = JFactory::getDBO(); 
	$q  = 'SELECT COUNT(f.`virtuemart_product_id`) as count FROM `#__virtuemart_product_custom_plg_param` as f';
	$q .= ' LEFT JOIN `#__virtuemart_product_categories` AS pc using (`virtuemart_product_id`)';
	$q .= ' LEFT JOIN `#__virtuemart_products` AS p using (`virtuemart_product_id`)';
	$q .= ' WHERE p.`published` = 1 AND pc.`virtuemart_category_id` IN ("'.implode('","',$categories).') AND f.`virtuemart_product_id` IN ("'.implode('","',$ids).'")';
	$q .= ' GROUP BY ml.`virtuemart_category_id`';
	$db->setQuery($q);
	return $db->loadObjectList('virtuemart_manufacturer_id');
}

function getManufacturers(){
	$db = JFactory::getDBO();
	$q  = 'SELECT ml.`virtuemart_manufacturer_id`, ml.`mf_name` FROM `#__virtuemart_manufacturers` AS m';
	$q .= ' JOIN `#__virtuemart_manufacturers_'.VMLANG.'` AS ml using (`virtuemart_manufacturer_id`)';
	$q .= ' LEFT JOIN `#__virtuemart_product_manufacturers` AS pm using (`virtuemart_manufacturer_id`)';
	$q .= ' LEFT JOIN `#__virtuemart_products` AS p using (`virtuemart_product_id`)';
	$q .= ' WHERE m.`published` = 1 AND p.`published` = 1';
	$q .= ' GROUP BY ml.`virtuemart_manufacturer_id`';
	$q .= ' ORDER BY ml.`mf_name`';
	$db->setQuery($q);
	return $db->loadObjectList();
}


function getCountManufacturers($ids,$children = -1){
	$db = JFactory::getDBO();
	$q  = 'SELECT pm.`virtuemart_manufacturer_id`,COUNT(pm.`virtuemart_product_id`) as count FROM `#__virtuemart_product_manufacturers` as pm';
	$q .= ' LEFT JOIN `#__virtuemart_products` AS p using (`virtuemart_product_id`)';
	$q .= ' WHERE p.`published` = 1';
	if(is_array($ids) && !empty($ids)){
		$q .= ' AND (pm.`virtuemart_product_id` IN ("'.implode('","',$ids).'")';
		if($children > 0){
			$q .= ' OR p.`product_parent_id` IN ("'.implode('","',$ids).'")';
		}
		$q .= ')';
		if($children == 0){
			$q .= ' AND p.`product_parent_id` = 0';
		}
	}
	$q .= ' GROUP BY pm.`virtuemart_manufacturer_id`';
	$db->setQuery($q);
	return $db->loadObjectList('virtuemart_manufacturer_id');
}

function getPriceLimits($discount = 0, $stock = 0, $price_mcur = 0, $cids = array(), $ids = array()){
	$db = JFactory::getDBO();
	$q = '';
	if($discount > 0){
		$q .= 'SELECT';
		$q .= ' MIN(CASE
					WHEN pp.`override` = 0 AND pd.`calc_value_mathop` = "+%" THEN pp.`product_price` + pp.`product_price` * pd.`calc_value` / 100
					WHEN pp.`override` = 0 AND pd.`calc_value_mathop` = "-%" THEN pp.`product_price` - pp.`product_price` * pd.`calc_value` / 100
					WHEN pp.`override` = 0 AND pd.`calc_value_mathop` = "+" THEN pp.`product_price` + pd.`calc_value`
					WHEN pp.`override` = 0 AND pd.`calc_value_mathop` = "-" THEN pp.`product_price` - pd.`calc_value`
					WHEN pp.`override` = 1 THEN pp.`product_override_price`
					ELSE pp.`product_price`
				END) as min';
		$q .= ',MAX(CASE
					WHEN pp.`override` = 0 AND pd.`calc_value_mathop` = "+%" THEN pp.`product_price` + pp.`product_price` * pd.`calc_value` / 100
					WHEN pp.`override` = 0 AND pd.`calc_value_mathop` = "-%" THEN pp.`product_price` - pp.`product_price` * pd.`calc_value` / 100
					WHEN pp.`override` = 0 AND pd.`calc_value_mathop` = "+" THEN pp.`product_price` + pd.`calc_value`
					WHEN pp.`override` = 0 AND pd.`calc_value_mathop` = "-" THEN pp.`product_price` - pd.`calc_value`
					WHEN pp.`override` = 1 THEN pp.`product_override_price`
					ELSE pp.`product_price`
				END) as max';
		$q .= ',pp.`product_currency` FROM `#__virtuemart_product_prices` AS pp';
		if($discount == 1){
			$q .= ' LEFT JOIN `#__virtuemart_calcs` as pd ON pd.`virtuemart_calc_id` = pp.`product_discount_id`';
		}elseif($discount == 2){
			$q .= ' LEFT JOIN `#__virtuemart_calcs` as pd ON pd.`virtuemart_calc_id` = pp.`product_tax_id`';
		}
	}else{
		$q .= 'SELECT MIN(`product_price`) as min, MAX(`product_price`) as max,pp.product_currency FROM `#__virtuemart_product_prices` AS pp';
	}
	$q .= ' LEFT JOIN `#__virtuemart_products` AS p using (`virtuemart_product_id`)';
	if(count($cids) > 0 && $cids[0] != 0){
		$q .= ' LEFT JOIN `#__virtuemart_product_categories` AS pc using (`virtuemart_product_id`)';
		$q .= ' WHERE p.`published` = "1" AND pc.`virtuemart_category_id` IN ("'.implode('","',$cids).'")';
		if(is_array($ids) && count($ids) > 0)
			$q .= ' AND pp.`virtuemart_product_id` IN ("'.implode('","',$ids).'")';
	}elseif(is_array($ids) && count($ids) > 0){
		$q .= ' WHERE p.`published` = "1" AND pp.`virtuemart_product_id` IN ("'.implode('","',$ids).'")';
	}else{
		$q .= ' WHERE p.`published` = "1"';
	}
	if($stock){
		$q .= ' AND p.`product_in_stock` > 0';
	}
	if($price_mcur){
		$q .= ' GROUP BY pp.`product_currency`';
	}
	$db->setQuery($q);
	return $db->loadObjectList();
}

function getCategoryCustomfields($active_category_id, $custom_ids = array()){
	if(is_array($active_category_id)){
		$active_category_id = implode('","',$active_category_id);
	}
	$db = JFactory::getDBO();
	$q  = 'SELECT DISTINCT f.`virtuemart_custom_id`,f.custom_title,f.custom_tip,f.custom_params FROM `#__virtuemart_customs` AS f';
	$q .= ' INNER JOIN `#__virtuemart_product_custom_plg_param_ref` AS pf ON f.`virtuemart_custom_id` = pf.`virtuemart_custom_id`';
	$q .= ' LEFT JOIN `#__virtuemart_product_categories` AS pc ON pf.`virtuemart_product_id` = pc.`virtuemart_product_id`';
	$q .= ' LEFT JOIN `#__virtuemart_products` AS p ON pf.`virtuemart_product_id` = p.`virtuemart_product_id`';
	$q .= ' LEFT JOIN `#__virtuemart_categories` AS c ON pc.`virtuemart_category_id` = c.`virtuemart_category_id`';
	$q .= ' WHERE p.published = "1" AND c.published = "1" AND f.published = "1" AND f.custom_element="param" AND pc.virtuemart_category_id IN ("'.$active_category_id.'") AND f.custom_params LIKE "%s=\"1\"%" ORDER BY f.`ordering`';
	// if(count($custom_ids) > 0){
		// $custom_ids = implode('","',$custom_ids);
		// if(!empty($custom_ids))
			// $q .= ' OR f.`virtuemart_custom_id` IN ("'.$custom_ids.'")';
	// }
	$db->setQuery($q);
	return $db->loadObjectList();
}

function getCustomfields($parent_id = '',$custom_ids = array(),$limit = 0){
	$db = JFactory::getDBO();
	$types_parent_id = is_array($parent_id) ? $parent_id : explode(';',$parent_id);
	
	$q  = 'SELECT f.`virtuemart_custom_id`,f.`custom_title`,f.`custom_tip`,f.`custom_params` FROM `#__virtuemart_customs` AS f';
	$q .= ' WHERE f.`published` = "1" AND f.`custom_element`="param" AND f.`custom_params` LIKE "%s=\"1\"%"';

	if(!empty($parent_id) && count($types_parent_id) > 0){
		$types_parent_id = implode('","',$types_parent_id);
		$q .= 'AND f.`custom_parent_id` IN ("'.$types_parent_id.'") ';
	}
	if(count($custom_ids) > 0){
		$custom_ids = implode('","',$custom_ids);
		if(!empty($custom_ids))
			$q .= ' OR f.`virtuemart_custom_id` IN ("'.$custom_ids.'")';
	}
	$q .= ' ORDER BY f.`ordering`';
	if($limit > 0){
		$q .= ' LIMIT 0,'.$limit;
	}
	$db->setQuery($q);
	return $db->loadObjectList();
}

function getCustomIntValues($ids = 0, $children = -1){
	
	$db = JFactory::getDBO(); 
	$q  = 'SELECT DISTINCT f.`virtuemart_custom_id`,f.`intval` as `value` FROM `#__virtuemart_product_custom_plg_param_ref` as f';
	$q .= ' LEFT JOIN `#__virtuemart_products` as p using(`virtuemart_product_id`)';
	$q .= ' WHERE f.`val` = 0';
	$q .= ' AND p.`published` = "1"';
	if(is_array($ids)){
		if(empty($ids)){
			return array();
		}
		$q .= ' AND (f.`virtuemart_product_id` IN ("'.implode('","',$ids).'")';
		if($children > 0){
			$q .= ' OR p.`product_parent_id` IN ("'.implode('","',$ids).'")';
		}
		$q .= ')';
		if($children == 0){
			$q .= ' AND p.`product_parent_id` = 0';
		}
	}
	// $q .= ' GROUP BY f.`virtuemart_custom_id`';
	$q .= ' ORDER BY f.`virtuemart_custom_id`';
	$db->setQuery($q);
	$res = $db->loadObjectList();
	$result = array();
	if(!empty($res)){
		foreach($res as &$v){
			$result[$v->virtuemart_custom_id][] = $v->value;
		}unset($v);
	}
	return $result;
}

function getCustomTextValues($ids = array()){
	$db = JFactory::getDBO(); 
	$q  = 'SELECT f.`virtuemart_custom_id`,GROUP_CONCAT(DISTINCT f.`value`) as `values` FROM `#__virtuemart_product_custom_plg_param` as f';
	$q .= ' LEFT JOIN `#__virtuemart_customs` as c using(`virtuemart_custom_id`)';
	$q .= ' WHERE c.`custom_params` LIKE \'%|l="0"|%\' AND f.`value` != \'||\'';
	if(count($ids))
		$q .= ' AND f.`virtuemart_product_id` IN ("'.implode('","',$ids).'")';
	$q .= ' GROUP BY f.`virtuemart_custom_id`';
	$q .= ' ORDER BY f.`virtuemart_custom_id`';
	$db->setQuery($q);
	return $db->loadObjectList('virtuemart_custom_id');
}

function getCustomValues($virtuemart_custom_ids = array()){
	$db = JFactory::getDBO();
	$q = 'SELECT * FROM `#__virtuemart_product_custom_plg_param_values`';
	$q .= ' WHERE `published` = 1';
	if(!empty($virtuemart_custom_ids)){
		$q .= ' AND `virtuemart_custom_id` IN ('.implode(',',$virtuemart_custom_ids).')';
	}
	$q .= '	ORDER BY `virtuemart_custom_id`,`ordering`';
	$db->setQuery($q);
	$res = $db->loadObjectList();
	$values = array();
	if(!empty($res)){
		foreach($res as &$v){
			$values[$v->virtuemart_custom_id][] = $v;
		}unset($v);
	}
	return $values;
}

function getCustomfieldCount($field_ids,$ids,$value = 0, $ft = 'text', $children = -1, $stock = false){
	$db = JFactory::getDBO();
	if(!$value){ // Собираем для всех полей
		$valuefield = $ft == 'int' ? 'intval' : 'val';
		$q  = 'SELECT `'.$valuefield.'` as value';
		if($children > 0){
			$q .= ',COUNT(DISTINCT (if(p.`product_parent_id` <> 0, p.`product_parent_id`, 0))) as parent, SUM((if(p.`product_parent_id` = 0, 1, 0))) as zero';
		}else{
			$q .= ',0 as parent,COUNT(f.`'.$valuefield.'`) as zero';
		}
		$q .= ' FROM `#__virtuemart_product_custom_plg_param_ref` as f';
		$q .= ' LEFT JOIN `#__virtuemart_products` as p using(`virtuemart_product_id`)';
		$q .= ' WHERE f.`virtuemart_custom_id`  IN ("'.implode('","',$field_ids).'") AND p.`published` = "1"';
		if(is_array($ids)){
			if(empty($ids)){
				return array();
			}
			$q .= ' AND (f.`virtuemart_product_id` IN ("'.implode('","',$ids).'")';
			if($children > 0){
				$q .= ' OR p.`product_parent_id` IN ("'.implode('","',$ids).'")';
			}
			$q .= ')';
		}
		if($children == 0){
			$q .= ' AND p.`product_parent_id` = 0';
		}
		if($stock){
			$q .= ' AND p.`product_in_stock` > 0';
		}
		$q .= ' GROUP BY `'.$valuefield.'`';
		$db->setQuery($q);
		$res = $db->loadAssocList('value');
		return $res;
	}else{ // Собираем только для значения
		// $q  = 'SELECT COUNT(f.`virtuemart_custom_id`) as count FROM `#__virtuemart_product_custom_plg_param` as f';
		// $q .= ' LEFT JOIN `#__virtuemart_products` as p using(`virtuemart_product_id`)';
		// $q .= ' WHERE f.`virtuemart_custom_id` = "'.$field_id.'" AND p.`published` = "1"';
		// if(is_array($ids)){
			// $q .= ' AND f.`virtuemart_product_id` IN ("'.implode('","',$ids).'")';
		// }
		// if($ft == 'int'){
			// $q .= ' AND f.`intvalue` = "'.(float)$value.'"';
		// }else{
			// $q .= ' AND f.`value` LIKE "%|'.$db->escape($value).'|%"';
		// }
		// $db->setQuery($q);
		// return $db->loadResult();
	}
}

function getProductsByCategory($cids=array()){
	if(is_array($cids) && count($cids)>0){
		$db = JFactory::getDBO();
		$q  = 'SELECT `virtuemart_product_id` FROM `#__virtuemart_product_categories`';
		$q .= ' LEFT JOIN `#__virtuemart_products` as p using(`virtuemart_product_id`)';
		$q .= ' WHERE p.`published` = 1 AND `virtuemart_category_id` IN ("'.implode('","',$cids).'")';
		$db->setQuery($q);
		return $db->loadColumn();
	}
}

function recursiveList($categories,$active_categories=array(),$parent_category_id,$depth,$tmpl){
	$html = '';
	require_once(JModuleHelper::getLayoutPath('mod_virtuemart_param_filter','categories'.DS.$tmpl.DS.'_item'));
	$func = str_replace('-','_','recursiveList'.$tmpl);
	$html = $func($categories,$active_categories,$parent_category_id,$depth,$tmpl);
	return $html;
}

function calcCount($counts, $vid){
	$count = 0;
	if(isset($counts[$vid]['parent'])){
		$count = $counts[$vid]['parent'];
		if($counts[$vid]['zero'] > 0){
			$count = $count > 0 ? $count - 1 : $count;
			$count += $counts[$vid]['zero'];
		}
	}
	return $count;
}