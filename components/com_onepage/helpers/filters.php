<?php
/**
 * 
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */
defined('_JEXEC') or die;
class OPCfilters {
	// returns true if filter was not matched
	// returns false if filter matched the rule and method should be removed
	public static function checkAdvancedConditionsOPC($type, $config) {
		if (empty($config)) return true; 
		if (!class_exists('VirtueMartCart'))
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
		
		switch ($type) {
			case 'category': 
			  return self::checkCategoryFilter($config); 
			case 'product': 
			  return self::checkProductFilter($config); 
			default: return true; 
		}
		return true; 
	}
	public static function checkProductFilter($config) {
	$cart = VirtuemartCart::getCart(); 
		$pids = array(); 
			  if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
 			     $products = $cart->cartProductsData; 
				 
				 foreach ($products as $p_key => $prow) {
					if (is_array($prow)) {
					$pids[$prow['virtuemart_product_id']] = (int)$prow['virtuemart_product_id']; 
					}
					else {
						if (is_object($prow)) {
							$virtuemart_product_id = (int)$prow->virtuemart_product_id; 
							$pids[$virtuemart_product_id] = (int)$virtuemart_product_id; 
						}
					}
				 }
			  }
				else {
				  $products = $cart->products;
				  foreach ($products as $p_key => $prow2) {
					$prow = (array)$prow2; 
					$pids[$prow['virtuemart_product_id']] = (int)$prow['virtuemart_product_id']; 
				 }
			  }
			  
			  
			  
			 
			  if (empty($pids)) return true; 
			  
			  foreach ($config as $pidC) {
				  if (in_array($pidC, $pids)) {
					  return false; 
				  }
			  }
			  return true; 
			  
	}
	public static function checkCategoryFilter($config) {
		$cart = VirtuemartCart::getCart(); 
		$pids = array(); 
			  if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
 			     $products = $cart->cartProductsData; 
				 
				 foreach ($products as $p_key => $prow) {
					$pids[$prow['virtuemart_product_id']] = (int)$prow['virtuemart_product_id']; 
				 }
			  }
				else {
				  $products = $cart->products;
				  foreach ($products as $p_key => $prow2) {
					$prow = (array)$prow2; 
					$pids[$prow['virtuemart_product_id']] = (int)$prow['virtuemart_product_id']; 
				 }
			  }
			  
			  
			  
			 
			  if (empty($pids)) return true; 
			  
			 
	  
			  
			  
			  $ids = self::getFullPaths($config); 
			  
			  
			  
			  
			  
			  $q = 'select c.`virtuemart_product_id` from #__virtuemart_product_categories as c, #__virtuemart_products as p where c.virtuemart_category_id IN ('.implode(',', $ids).') and (c.virtuemart_product_id IN ('.implode(',', $pids).'))  or (p.product_parent_id = c.virtuemart_product_id and p.virtuemart_product_id IN ('.implode(',', $pids).') ) limit 0,1'; 
			  return self::cachedQ($q); 
			  
			  
		
	}
	
	public static function getFullPaths($config) {
		static $cache; 
		foreach ($config as $k=>$v) $key = $k.'~.~'.$v; 
		if (isset($cache[$key])) return $cache[$key]; 
		
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'com_virtuemart_helper.php'); 
		
		$query = array(); 
			  $query['langswitch'] = VMLANG; 
			  $helper = vmrouterHelperSEFforOPC::getInstance($query);
		
		$allcats = array(); 
			  foreach ($config as $cat_id) {
				  //$cats = vmrouterHelperSEFforOPC::getAllProductCats($pid); 
				  
				  //foreach ($cats as $c) 
				  {
					$virtuemart_category_id = $cat_id; //$c['virtuemart_category_id']; 
					$allcats[$virtuemart_category_id] = $virtuemart_category_id; 
				    $CatParentIds = $helper->getCategoryRecurse($virtuemart_category_id,0) ;
					if (!empty($CatParentIds)) {
						foreach ($CatParentIds as $cid) {
							$allcats[$cid] = $cid; 
						}
					}
				  }
				  
				  
			  }
			  $cache[$key] = $allcats; 
			  return $allcats; 
		
	}
	
	// returns true if no records exists !
	public static function cachedQ($q) {
		static $qr; 
		if (isset($qr[$q])) return $qr[$q]; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$res = $db->loadResult(); 
		if (!empty($res)) {
				  $qr[$q] = false; 
				  return false; 
			  }
	   $qr[$q] = true; 
	   return true; 
	}
	
}