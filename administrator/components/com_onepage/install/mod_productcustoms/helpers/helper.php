<?php 
defined('_JEXEC')or die;

class PCH {
	
	public static function getFilterCats() {
		
		$categories = self::getCatsFromGet(); 
		
		self::makeUnique($categories); 
		if (!empty($categories)) {
		  self::getListChilds($categories); 
		}
		
		
		if (method_exists('VmConfig', 'setdbLanguageTag')) {
		if(empty(VmConfig::$vmlang)) {
			VmConfig::setdbLanguageTag();
		}
		if (empty(VmConfig::$vmlang)) return; 
		$db = JFactory::getDBO(); 
		$ret = array(); 
		
		foreach ($categories as $id) {
		  $cat = new stdClass(); 
		  $q = 'select `category_name` from `#__virtuemart_categories_'.$db->escape(VmConfig::$vmlang).'` where virtuemart_category_id ='.(int)$id; 
		  $db->setQuery($q); 
		  $cat_name = $db->loadResult();
		  if (!empty($cat_name)) {
		   $cat->virtuemart_category_id = (int)$id; 
		   $cat->category_name = (string)$cat_name; 
		   //COM_VIRTUEMART_CATEGORIES
		   $ret[$id] = $cat; 
		  }
		}
		
		}
		
		
		return $ret; 
		
	}
	
	public static function getParams($id=0, $module=null)
	{
		jimport( 'joomla.registry.registry' );
		
		if ((!empty($module)) && (!empty($module->params))) {
		 return Jregistry($module->params); 
		}
		
		if (empty($id))
		$id = JRequest::getVar('module_id', null); 
	
		if (!empty($id))
		 {
		    $id = (int)$id; 
			$q = 'select `params` from `#__modules` where `id` = '.$id.' and `module` = \'mod_productcustoms\' limit 1'; 
			
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$params_s = $db->loadResult(); 
			
			
			
			if (!empty($params_s))
			{
			$params = new JRegistry($params_s); 
			
			return $params; 
			}
			
		 }
		 
		 {
			
			$q = 'select `params` from `#__modules` where `module` = \'mod_productcustoms\' and `published` = 1 limit 1'; 
			
			$db = JFactory::getDBO(); 
			$db->setQuery($q); 
			$params_s = $db->loadResult(); 
			
			
			
			if (!empty($params_s))
			$params = new JRegistry($params_s); 
			
			return $params; 
		 }
		 
		 $r = new JRegistry(); 
		 return $r; 
		 
		 
	}
	
	public static function getChildCustoms(&$customs, &$parents=array()) {
		
		$nc = array(); 
		foreach ($customs as $k=>$c) {
			$c = (int)$c; 
			if (empty($c)) unset($customs[$k]); 
			$nc[$c] = $c; 
		}
		$db = JFactory::getDBO(); 
		$customs = $nc; 
		
		if (empty($customs)) return array(); 
		
		$return_customs = $customs; 
		
		$q = 'select `custom_parent_id`, `virtuemart_custom_id` from `#__virtuemart_customs` where `custom_parent_id` IN ('.implode(',', $return_customs).') '; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		
		
		$parents = array(); 
		if (!empty($res)) {
			foreach ($res as $row) {
				$newid = (int)$row['virtuemart_custom_id']; 
				$return_customs[$newid] = $newid; 
				
				$current_id = (int)$row['custom_parent_id']; 
				if (!isset($parents[$newid])) $parents[$newid] = array(); 
				
				$parents[$newid][$current_id] = $current_id; 
			}
		}
		
		
		return $return_customs; 
		
	}
	
	public static function getParentCustoms(&$customs, &$groups=array()) {
		
		$nc = array(); 
		foreach ($customs as $k=>$c) {
			$c = (int)$c; 
			if (empty($c)) unset($customs[$k]); 
			$nc[$c] = $c; 
		}
		$db = JFactory::getDBO(); 
		$customs = $nc; 
		
		if (empty($customs)) return array(); 
		
		$return_customs = $customs; 
		
		$q = 'select `custom_parent_id`, `virtuemart_custom_id` from `#__virtuemart_customs` where `virtuemart_custom_id` IN ('.implode(',', $return_customs).') '; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		$groups = array(); 
		if (!empty($res)) {
			foreach ($res as $row) {
				$newid = (int)$row['custom_parent_id']; 
				$return_customs[$newid] = $newid; 
				
				$current_id = (int)$row['virtuemart_custom_id']; 
				if (!isset($groups[$newid])) $groups[$newid] = array(); 
				
				$groups[$newid][$current_id] = $current_id; 
			}
		}
		
		
		return $return_customs; 
		
	}
	
	public static function canShowChilds($customs, &$groups=array()) {
		
		if (empty($customs)) return false; 
		
		
		$params = self::getParams(); 
		$customs_with_childs = $params->get('customs_with_childs', array()); 
		if (empty($customs_with_childs)) return false; 
		
		$all_customs = self::getParentCustoms($customs, $groups); 
		if (empty($all_customs)) return false; 
		
		$ng = array(); 
		$ret = self::getChildCustoms($customs_with_childs, $ng); 
		
		foreach ($ret as $k=>$r) {
			$customs_with_childs[$k] = $r; 
		}
		
		
		foreach ($customs_with_childs as $id) {
			$id = (int)$id; 
			foreach ($all_customs as $id2) {
				$id2 = (int)$id2; 
				if ($id2 === $id) {
					
					return true; 
				}
			}
		}
		return false; 
	}
	
	public static function getCategoryProducts($keyword, $prods=5, $popup=false, $order_by='', $categories = array(), $manufs=array(), $customs=array()) {
		
		if (empty($categories)) {
		 $categories = self::getCatsFromGet(); 
		}
		if (empty($manufs)) {
		 $manufs = self::getManufsFromGet(); 
		}
		if (empty($customs)) {
		 $customs = self::getCustomsFromGet(); 
		}
		self::makeUnique($categories); 
		//find child categories: 
		$custom_groups = array(); 
		$canshowchilds = self::canShowChilds($customs, $custom_groups); 
		
		$limitstart = JRequest::getInt('limitstart', 0); 
		
		
	$s = 'select p.`virtuemart_product_id`, p.`product_parent_id` '; 
		if (!empty($customs)) {
			$s .= ', cf.virtuemart_custom_id as cid '; 
		}
		
	$s .= ' from '; 
	$qf = array(); 
	$qf[] = ' #__virtuemart_products as p '; 
	//$qf[] = ' #__virtuemart_products as parents '; 
	if (!empty($categories)) {
	 self::getListChilds($categories); 
	 $qf[] = ' #__virtuemart_product_categories as cat '; 
	 
	}
	if (!empty($customs)) {
	
	 $qf[] = ' #__virtuemart_customs as c '; 
	 $qf[] = ' #__virtuemart_product_customfields as cf '; 
	}
	

	
	$w = ' where '; 
	if (!empty($categories)) {
	$gc = array(); 
	foreach ($categories as $cat_id) {
		$cat_id = (int)$cat_id; 
		if (empty($cat_id)) continue; 
		$gc[] = ' ( cat.virtuemart_category_id = '.(int)$cat_id.') ';
	}
	
	$wc[] = ' ('.implode(' OR ', $gc).') and ((cat.virtuemart_product_id = p.product_parent_id) or (cat.virtuemart_product_id = p.virtuemart_product_id)) '; 
	}
	
	if (!empty($customs)) {
	 $qcu = ''; 
	 //if (empty($canshowchilds)) 
	 {
	  $qcu .= ' ('; 
	 }
	 $qcu .= ' (cf.virtuemart_product_id = p.virtuemart_product_id) '; 
	 
	 //if (empty($canshowchilds)) 
	 {
	 
	 $qcu .= ' or (cf.virtuemart_product_id = p.product_parent_id)) '; 
	 
	 }
	 $qcu .= ' and (cf.virtuemart_custom_id = c.virtuemart_custom_id) '; 
	 $gg = array(); 
	 
	  
	 foreach ($custom_groups as $local_group) {
		 $gg2 = array(); 
		 foreach ($local_group as $cid) {
		 $gg2[] = ' (cf.virtuemart_custom_id = '.(int)$cid.') '; 
		 }
		 $gg[] = ' ( '.implode(' or ', $gg2).' ) '; 
	     //$qcu .= ' c.virtuemart_custom_id IN ('.implode(',', $customs).') )' ; 
	 }
	 $gcu .= ' ( '.implode(' or ', $gg).' ) '; 
	 
	 
	 if (!empty($gg)) {
	   $wc[] = $qcu.' and '.$gcu;
	 }	 
	}
	
	//$wc[] = ' (parents.virtuemart_product_id = p.product_parent_id ) '; 
	
	/*
	if ($canshowchilds) {
	  $wc[] = ' ( p.product_parent_id > 0 ) '; 
	}
	else {
		$wc[] = ' ( p.product_parent_id = 0 ) '; 
	}
	*/
	
	
	
	$q = $s.implode(',', $qf).$w.implode(' and ',$wc); 
	//$q .= ' limit '.$limitstart.','.$prods; 
	
	
	$db = JFactory::getDBO(); 
	if (class_exists('RupHelper')) {
		$res = RupHelper::runQuery($db, $q); 
	}
	else {
		
	 $db->setQuery($q); 
	 $res = $db->loadAssocList(); 
	 
	 
	 
	 
	
	}
	
	
	
	    $ids = array(); 
		if (!empty($res)) {
		
		
		
		if (!empty($custom_groups)) {
		 //self::filterRes($res, $custom_groups, $canshowchilds, $limitstart, $prods); 
		 self::filterRes($res, $customs, $custom_groups, $canshowchilds, $limitstart, $prods); 
		}
			
		foreach ($res as $k=>$v) {
			$v['virtuemart_product_id'] = (int)$v['virtuemart_product_id']; 
			$ids[$v['virtuemart_product_id']] = $v['virtuemart_product_id']; 
		}
		}
		
		
		
		return $ids; 
		
	}
	
	public static function filterRes(&$res, $selectedCustoms, $custom_groups, $canshowchilds, $limitstart=0, $prods) {
		
		$products = array(); 
		$parents = array(); 
		foreach ($res as $row) {
			$virtuemart_custom_id = (int)$row['cid']; 
			
			$product_parent_id = (int)$row['product_parent_id']; 
			
			$virtuemart_product_id = (int)$row['virtuemart_product_id']; 
			if (empty($product_parent_id)) $product_parent_id = $virtuemart_product_id;
			if (!isset($products[$virtuemart_product_id])) $products[$virtuemart_product_id] = array(); 
			$products[$virtuemart_product_id][$virtuemart_custom_id] = $virtuemart_custom_id;
			if (!isset($parents[$product_parent_id])) $parents[$product_parent_id] = array();
			$parents[$product_parent_id][$virtuemart_product_id] = $virtuemart_custom_id;
		}
		
		
		
		
		
		foreach ($parents as $parent_id =>$data) {
			foreach ($data as $product_id => $custom_id) {
				if (!isset($products[$virtuemart_product_id])) $products[$virtuemart_product_id] = array(); 
				$products[$virtuemart_product_id][$custom_id] = $custom_id;
			}
		}
		
		
		
		foreach ($products as $product_id=>$data) {
			
			//unset parents:
			if ($canshowchilds) {
				if (isset($parents[$product_id])) {
					unset($products[$product_id]); 
					continue; 
				}
			}
			else {
			 //unset childs:
			 	    if (!isset($parents[$product_id])) {
					unset($products[$product_id]); 
					continue; 
				}

			}
			
			if ($canshowchilds) {
				//selectedCustoms
				
			
				
				foreach ($selectedCustoms as $id=>$selected_id) {
			
			
				if (isset($data[$selected_id])) {
					
					break; 
				}
			    else 
			    {
				 unset($products[$product_id]); 
				
			    }
			
		}
				
			}
			else {
		foreach ($custom_groups as $local_group) {
			$found = false; 
			
			foreach ($local_group as $cid) {
				if (isset($data[$cid])) {
					$found = true; 
					break; 
				}
			}
			if (!$found) {
				unset($products[$product_id]); 
				continue 2; 
			}
			
		}
			}
		}
		
		$ret = array(); 
		$n=0; 
		foreach ($products as $product_id=>$data) {
			if ($limitstart > $n) {
				$n++; 
				continue; 
			}
			$r = array(); 
			$virtuemart_product_id = $product_id; 
			$r['virtuemart_product_id'] = $virtuemart_product_id; 
			$ret[$virtuemart_product_id] = $r; 
			if (count($ret) === $prods) break; 
			$n++; 
		}
		$res = $ret; 
		
		
		
	}
	public static function getGet() {
		$app = JFactory::getApplication(); 
$get = array(); 


$get['option'] = 'com_rupsearch'; 
$get['view'] = 'search'; 

$get['keyword'] = JRequest::getVar('keyword', ''); 
$get['virtuemart_category_id'] = self::getCatsFromGet(false); 

if (empty($get['virtuemart_category_id'])) {
	$get['virtuemart_category_id'] = self::getCatsFromGet(true);
}

$get['virtuemart_manufacturer_id'] = JRequest::getVar('virtuemart_manufacturer_id', array()); 
$get['virtuemart_custom_id'] = JRequest::getVar('virtuemart_custom_id', array()); 

$get['limit'] = (int)$app->getUserStateFromRequest('com_virtuemart.category.limit', 'limit', JRequest::getInt('limit', VmConfig::get ('llimit_init_FE',24)));
		
$get['limitstart'] = (int)$app->getUserStateFromRequest('com_virtuemart.category.limitstart', 'limitstart',  JRequest::getInt('limitstart',0));

$get['prod'] = (int)$get['limit']; 
$get['internal_caching'] = 0; 



$Itemid = JRequest::getInt('Itemid', 0); 
if (!empty($Itemid)) $get['Itemid'] = $Itemid; 

$lang = JRequest::getWord('lang', ''); 
if (!empty($lang)) $get['lang'] = $lang; 

return $get; 
	}
	public static function getListChilds(&$categories, &$other=array()) {
		
		$db = JFactory::getDBO(); 
		
		if (!empty($other)) $selected = $other; 
		else $selected = $categories; 
		
		if (empty($selected)) return; 
		
		$q = 'select cc.category_child_id from #__virtuemart_category_categories as cc,#__virtuemart_categories as c where cc.category_parent_id IN ('.implode(',', $selected).') and c.virtuemart_category_id = cc.category_child_id and c.published = 1'; 
		
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		//if we have no child categories, display the current level categories: 
		if (empty($res)) {
			$first = reset($selected); 
			$first = (int)$first; 
			if (!empty($first)) {
			$q = 'select cc.category_parent_id id from #__virtuemart_category_categories as cc where cc.category_child_id = '.(int)$first.' limit 1';
			$db->setQuery($q); 
			$parent_id = $db->loadResult(); 
			if (!empty($parent_id)) {
			$q = 'select `cc`.`category_child_id` from `#__virtuemart_category_categories` as `cc`,`#__virtuemart_categories` as `c` where `cc`.`category_parent_id` = '.(int)$parent_id.' and `c`.`virtuemart_category_id` = `cc`.`category_child_id` and `c`.`published` = 1'; 
		
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		
		
			}
			}
		}
		
		if (!empty($res)) {
			$new = array(); 
			foreach ($res as $row) {
				$i = (int)$row['category_child_id']; 
				if (!in_array($i, $selected)) {
				 $new[$i] = $i; 
				}
				$categories[$i] = $i; 
			}
			
			
			
			//return self::getListChilds($categories, $new); 
		}
		
		
	}
	public static function makeUnique(&$categories) {
		$new = array(); 
		foreach ($categories as $cat) {
			$cat = (int)$cat; 
			if (!empty($cat)) {
			  $new[$cat] = $cat; 
			}
		}
		$categories = $new; 
	}
	
	public static function loadVM() {
		static $run; 
		if (!empty($run)) return; 
		$run = true; 
		
		if (!class_exists('VmConfig'))
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
			VmConfig::loadConfig(); 
			
			$tag = JFactory::getLanguage()->getTag(); 
			if (class_exists('vmLanguage')) {
				if (method_exists('vmLanguage', 'setLanguageByTag')) {
					vmLanguage::setLanguageByTag($tag); 
					
				}
			}
	}
	
	public static function getLink($get, $local) {
		$link = 'index.php?option=com_rupsearch&view=search'; 
		$app = JFactory::getApplication(); 
		self::loadVM(); 
		
		
		
		//$link .= '&limit='.$limit.'&limitstart='.$limitStart; 
		
		foreach ($get as $r => $v) {
		  	if (!is_array($v)) {
			  $link .= '&'.urlencode($r).'='.urlencode($v);
			}	
			else {
				
				foreach ($v as $i=>$val) {
					$vx = (int)$vx; 
					if (empty($vx)) continue; 
					$link .= '&'.urlencode($r.'['.$vx.']').'='.urlencode($$vx);
					
				}
			}
			
		}
		
		foreach ($local as $r => $v) {
		  	if (!is_array($v)) {
			  $link .= '&'.urlencode($r).'='.urlencode($v);
			}	
			else {
				
				foreach ($v as $i=>$val) {
					$vx = (int)$vx; 
					if (empty($vx)) continue; 
					$link .= '&'.urlencode($r.'['.$vx.']').'='.urlencode($$vx);
					
				}
			}
			
		}
		
		
		return $link; 
		
	}
	
	public static function getCustomsCategories($categories, $inclchild=true) {
		$datas = array(); 
		if (!empty($categories)) {
	
	self::makeUnique($categories); 
	//find child categories: 
	if ($inclchild)
	self::getListChilds($categories); 
	
	$q = 'select distinct c.custom_title, c.custom_value, c.virtuemart_custom_id';
	$q .= ', c.custom_parent_id '; 
	//$q .= ', sum(p.published) as mycount '; 
	$q .= ' from #__virtuemart_product_categories as cat, #__virtuemart_products as p, #__virtuemart_customs as c, #__virtuemart_product_customfields as cf where cat.virtuemart_category_id IN ('.implode(',', $categories).') and ((cat.virtuemart_product_id = p.product_parent_id) or (cat.virtuemart_product_id = p.virtuemart_product_id)) and ((cf.virtuemart_product_id = p.virtuemart_product_id) or (cf.virtuemart_product_id = p.product_parent_id)) and cf.virtuemart_custom_id = c.virtuemart_custom_id'; 
	
	//echo $q; die(); 
	
	$db = JFactory::getDBO(); 
	$db->setQuery($q); 
	$res = $db->loadAssocList(); 
	
	
	
	
	
	foreach ($res as $row) {
		$title = 'UNKNOWN'; 
		if (!empty($row['custom_parent_id'])) {
			$q = 'select c.custom_value from #__virtuemart_customs as c where c.virtuemart_custom_id = '.(int)$row['custom_parent_id']; 
			$db->setQuery($q); 
			$title = $db->loadResult(); 
			
			
		
		}
		if (empty($datas[$title])) $datas[$title] = array(); 
		if (!empty($row['custom_value'])) $custom_title = $row['custom_value']; //.' ('.$row['mycount'].')'; 
		else
		if (!empty($row['custom_title'])) $custom_title = $row['custom_title']; //.' ('.$row['mycount'].')'; 
	
		$datas[$title][$row['virtuemart_custom_id']] = $custom_title; 
		
		
		
		
		
	}
	
	$ignore = array('UNKNOWN', 'NS '); 
	foreach ($datas as $title=>$v) {
		foreach ($ignore as $se) {
		 if (strpos($title, $se)===0) unset($datas[$title]); 
		}
	}
	}
	
	$customs = self::getCustomsFromGet(); 
	$allfields = 0; 
	foreach ($datas as $title => $data) {
		foreach ($data as $cid => $titleX) {
			$allfields++;
		}
	}
	
	
	
	foreach ($datas as $title => $data) {
		foreach ($data as $cid => $titleX) {
			//get quantities: 
			$test = $customs;
			$test[$cid] = $cid; 
			$c = ''; 
			if ($allfields < 20) {
			$res = self::getCategoryProducts('', 100, false, '', array(), array(), $test);
			$c = count($res); 
			}
			$mytitle = $titleX;
			if (!empty($c)) $mytitle .= ' ('.$c.')';
			$datas[$title][$cid] = $mytitle; 
					
			
		}
	}
	
	return $datas; 
	}
	
	public static function getProductsCategoriesFromGet(&$categories) {
		if (empty($categories)) $categories = array(); 
		
		$virtuemart_product_id = JRequest::getInt('virtuemart_product_id', 0); 
	if (!empty($virtuemart_product_id)) {
		$db = JFactory::getDBO(); 
	$q = 'select p.virtuemart_category_id from #__virtuemart_product_categories as p, #__virtuemart_categories as c, #__virtuemart_products as px where (( p.virtuemart_product_id = '.(int)$virtuemart_product_id.' and px.virtuemart_product_id = p.virtuemart_product_id) or (px.virtuemart_product_id = '.(int)$virtuemart_product_id.' and px.product_parent_id = p.virtuemart_product_id)) and (c.virtuemart_category_id = p.virtuemart_category_id) and c.published = 1'; 
	$db->setQuery($q); 
	$prodcats = $db->loadAssocList(); 
	if (!empty($prodcats))
	foreach ($prodcats as $row) {
		$cat_id = (int)$row['virtuemart_category_id']; 
		$categories[$cat_id] = $cat_id; 
	}
	}
	}
	
	public static function getCatsFromGet($withProd=true) {
			
$category = JRequest::getVar('virtuemart_category_id'); 
$categories = array(); 
if (!empty($category)) {
	if (is_array($category)) foreach ($category as $c) $categories[(int)$c] = (int)$c; 
	else {
		$category = (int)$category; 
		$categories[$category] = $category; 
	}
}

if ($withProd) {
$virtuemart_product_id = JRequest::getInt('virtuemart_product_id', 0); 
if (!empty($virtuemart_product_id)) {
	self::getProductsCategoriesFromGet($categories); 
}
	
}

return $categories; 
	}
	
	public static function getCustomsFromGet() {
			
$category = JRequest::getVar('virtuemart_custom_id'); 
$categories = array(); 
if (!empty($category)) {
	if (is_array($category)) foreach ($category as $c) $categories[(int)$c] = (int)$c; 
	else {
		$category = (int)$category; 
		$categories[$category] = $category; 
	}
}
return $categories; 
	}
	
	public static function getManufsFromGet() {
			
$category = JRequest::getVar('virtuemart_custom_id'); 
$categories = array(); 
if (!empty($category)) {
	if (is_array($category)) foreach ($category as $c) $categories[] = (int)$c; 
	else {
		$category = (int)$category; 
		$categories[] = $category; 
	}
}
return $categories; 
	}
	
	
	
	public static function collectCustomsFromGet() {
	
$categories = self::getCatsFromGet(); 

return self::getCustomsCategories($categories, true); 


}
}