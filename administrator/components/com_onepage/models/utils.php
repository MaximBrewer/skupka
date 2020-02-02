<?php  
/**
ONE PAGE CHECKOUT FOR VIRTUEMART BY RUPOSTEL.COM
*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cache.php'); 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 

/**
* Cache Model
*
* @package		Joomla.Administrator
* @subpackage	com_cache
* @since		1.6
*/
class JModelUtils extends OPCModel
{
	function to_parent_cats() {
		define('CATSEP', ','); 
		$q = 'select virtuemart_category_id from #__virtuemart_categories where published = 1'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) {
		  foreach ($res as $row) {
		     $category_id = (int)$row['virtuemart_category_id']; 
			 
			 $names[$category_id] = $category_id; 
		  }
		  
		  
$paths = array(); 
foreach ($names as $id=>$name) {
	$path = array(); 
	$path[] = (int)$id; 
	self::getPath($id, $path, FALSE); 
	
	//if ($id === 75) { var_dump($path); die(); }
	
	if (!empty($path)) {
	$path = array_reverse($path); 
	$namesZ = self::pathToNames($path, $names, CATSEP); 
	$paths[$id] = $namesZ;
	}	
}

foreach ($paths as $k=>$path) {
	if (strpos($path, 'UNPUBLISHED')!==false) {
		unset($paths[$k]); 
		continue; 
	}
}
$q = "SHOW VARIABLES LIKE 'max_allowed_packet';"; 
$db->setQuery($q); 
$bytesR = $db->loadAssoc(); 

if (isset($bytesR['Value'])) $bytes = $bytesR['Value']; 
if (empty($bytes)) $bytes = 1024*1024*15; 



$q = 'select * from #__virtuemart_product_categories where 1'; 
$db->setQuery($q); 
$res = $db->loadAssocList(); 
if (!empty($res)) {
	 $insq = $q = 'insert ignore into `#__virtuemart_product_categories` (`virtuemart_product_id`,`virtuemart_category_id`,`ordering`) values '; 
	 $values = array();
  $ordering2 = 0; 	 
  foreach ($res as $row) {
     $product_id = (int)$row['virtuemart_product_id']; 
	 $category_id = (int)$row['virtuemart_category_id']; 
	 
	 $ordering = (int)$row['ordering']; 
	 $ordering += 100; 
	 $ordering2++; 
	 $ordering += $ordering2; 
	 if (isset($paths[$category_id])) {
	 $cpath = $paths[$category_id]; 
	 
	
	 
	 if (isset($cpath)) {
		 
		$e = explode(CATSEP, $cpath); 
		
		if ((!empty($e)) && (count($e)>1)) {
		   for ($i=count($e)-2;$i>=0;$i-- ) {
			   
		      $cat_id = (int)$e[$i]; 
			  if (!empty($cat_id)) {
				$ordering++; 
			    $values[] = '('.(int)$product_id.','.(int)$cat_id.','.$ordering.')'; 
			  }
		   }
		}
		 
	 }
	 }
  }
  $c = count($values); 
  
  $bytes = (int)round(0.9 * $bytes); 
  //$bytes = 151; 
  $query = $insq.implode(',',$values); 
  if (!empty($values)) {
  if (strlen($query)<$bytes) {
	//echo __LINE__.':'.$query."<br />\n"; 
    $db->setQuery($query); 
	$db->query(); 
  }
  else {
	  $qx = $qx2 = ''; 
	  foreach ($values as $qq) {
	     if (!empty($qx)) $qx .= ','; 
		 
		 $qx .= $qq; 
		 $testL = $insq.$qx; 
		 if (strlen($testL)>$bytes) {
			if (!empty($qx2)) {
		    $query = $insq.$qx2; 
			
			//echo __LINE__.':'.$query."<br />\n"; 
			$db->setQuery($query); 
			$db->query(); 
			
			$qx2 = $qx = ''; 
			}
		 }
		 if (!empty($qx2)) $qx2 .= ','; 
		 $qx2 .= $qq; 
		 
	  }
	  
	  if (!empty($qx2)) {
	     $query = $insq.$qx2; 
		 echo __LINE__.':'.$query."<br />\n"; 
		 $db->setQuery($query); 
		 $db->query(); 
	  }
	  
	  
	  
  }
  }
  echo 'to_parent_cats: Executed '.$c.' queries'."\n"; 
  
}

		  
		  
		  
		
		}
			
	
	
	}
	public static function getPath($id, &$path, $onlydeepest=false, $recursion=0) {
	 $q = 'select category_parent_id from #__virtuemart_category_categories where category_child_id = '.(int)$id. ' and category_child_id != category_parent_id limit 0,1'; 
     $db = JFactory::getDBO(); 
	 $db->setQuery($q); 
     $res = $db->loadAssoc(); 
  
  if ($recursion < 10)
  if (!empty($res)) { 
    $parent_id = (int)$res['category_parent_id']; 
	if (in_array($parent_id, $path)) {
		$last_id = end($path); 
		$q = 'update #__virtuemart_category_categories set category_parent_id = 0 where category_child_id = '.(int)$last_id;
		$db->setQuery($q); 
		$db->query(); 
		$parent_id = 0; 
		
	}
	if (!empty($parent_id)) {
	   $path[] = $parent_id; 
	   $recursion++; 
	   return self::getPath($parent_id, $path, $recursion); 
	}
  }
  $recursion--; 
	}
	public static function pathToNames($path, $names, $SEP='~|~') {
	    $n = array(); 
  foreach ($path as $x=>$id) {
	//unpublished tree: 
	if (isset($names[$id])) {
    $n[] = $names[$id];    
	}
	else {
		$n[] = 'UNPUBLISHED'; 
	}
  }
  return implode($SEP, $n); 
	}
	
	function removeproducts() {
		{
			
			$lang_check = JRequest::getVar('lang_check', ''); 
		  
		 
		  
		  $lang_check = strtolower($lang_check); 
		   $lang_check = str_replace('-', '_', $lang_check); 
			
		  $db = JFactory::getDBO(); 
		  $q = 'SELECT a.virtuemart_product_id FROM #__virtuemart_products as a LEFT JOIN `'.$db->escape('#__virtuemart_products_'.$lang_check).'` as b on a.virtuemart_product_id = b.virtuemart_product_id WHERE b.virtuemart_product_id IS NULL'; 
		  $db->setQuery($q); 
		  $res = $db->loadAssocList(); 
		  $p = array(); 
		  if (empty($res)) return 'Nothing to remove...'; 
		  if (!empty($res)) {
			  foreach ($res as $row) {
				  
				  
					$pid = (int)$row['virtuemart_product_id']; 
					
					$this->runRemoveProducts($pid); 
			  }
		  }
		  
		  
		}
	}
	public static function getDBLangs() {
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 
		 $langs = VmConfig::get('active_languages', array()); 

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		if (!in_array('en-GB', $langs) and OPCmini::tableExists('virtuemart_products_en_gb')) {
			$langs[] = 'en-GB'; 
		}
		foreach ($langs as $k=>$lang) {
			$lang = strtolower($lang); 
			$lang = str_replace('-', '_', $lang); 
			$langs[$k] = $lang; 
		}
		
		return $langs; 
	}
	public static function runRemoveProducts($products) {
		
		
		
		if (empty($products)) return; 
		if (is_array($products)) {
			$products = implode(',', $products); 
		}
		
		
		
		$db = JFactory::getDBO(); 
		$q1 = 'delete from #__virtuemart_products where virtuemart_product_id IN ('.$products.')'; 
		$db->setQuery($q1); 
		$db->query(); 
		
		
		$langs = self::getDBLangs(); 
		
		
		foreach ($langs as $l ) {
		 $t = '#__virtuemart_products_'.$l; 
		 if (OPCmini::tableExists($t)) {
		  $q1 = 'delete from `'.$t.'` where virtuemart_product_id IN ('.$products.')'; 
		  $db->setQuery($q1); 
		  $db->query(); 
		 }
		}
		
		$q1 = 'delete from #__virtuemart_product_categories where virtuemart_product_id IN ('.$products.')'; 
		$db->setQuery($q1); 
		$db->query(); 
		
		$q1 = 'delete from #__virtuemart_product_customfields where virtuemart_product_id IN ('.$products.')'; 
		$db->setQuery($q1); 
		$db->query(); 
		
		$q1 = 'delete from #__virtuemart_product_manufacturers where virtuemart_product_id IN ('.$products.')'; 
		$db->setQuery($q1); 
		$db->query(); 
		
		$q1 = 'delete from #__virtuemart_product_medias where virtuemart_product_id IN ('.$products.')'; 
		$db->setQuery($q1); 
		$db->query(); 
		
		$q1 = 'delete from #__virtuemart_product_prices where virtuemart_product_id IN ('.$products.')'; 
		$db->setQuery($q1); 
		$db->query(); 
		
		if (OPCmini::tableExists('virtuemart_product_relations')) {
		$q1 = 'delete from #__virtuemart_product_relations where virtuemart_product_id IN ('.$products.')'; 
		$db->setQuery($q1); 
		$db->query(); 
		}
		$q1 = 'delete from #__virtuemart_product_shoppergroups where virtuemart_product_id IN ('.$products.')'; 
		$db->setQuery($q1); 
		$db->query(); 
		
	
		
		
		
		
		
	}
	
	function removecategories(&$msg='') {
		
		$langs = VmConfig::get('active_languages', array()); 

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		if (!in_array('en-GB', $langs) and OPCmini::tableExists('virtuemart_products_en_gb')) {
			$langs[] = 'en-GB'; 
		}
		foreach ($langs as $k=>$lang) {
			$lang = strtolower($lang); 
			$lang = str_replace('-', '_', $lang); 
			$langs[$k] = $lang; 
		}
		
		$db = JFactory::getDBO(); 
			$cats = array(); 
		
		$q = 'select category_child_id from #__virtuemart_category_categories as c '; 
		$q .= ' left join #__virtuemart_product_categories as p on p.virtuemart_category_id = c.category_child_id ';
		$q .= ' where c.category_child_id NOT IN (select category_parent_id from #__virtuemart_category_categories) and p.virtuemart_category_id IS NULL'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		$cats = array(); 
		foreach ($res as $row) {
			$cid = (int)$row['category_child_id']; 
			$cats[$cid] = $cid; 
		}
		/*
		$q = 'select c.virtuemart_category_id from #__virtuemart_categories as c '; 
		$q .= ' left join #__virtuemart_product_categories as p on p.virtuemart_category_id = c.virtuemart_category_id ';
		$q .= ' where p.virtuemart_category_id IS NULL '; 
		
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		foreach ($res as $row) {
			$cid = (int)$row['virtuemart_category_id']; 
			$path = $this->getPath($cid); 
			
			$cats[$cid] = $cid; 
		}
		*/
		
		
		
		
		$q = 'select c.virtuemart_category_id from #__virtuemart_categories as c '; 
		$q .= ' left join #__virtuemart_product_categories as p on p.virtuemart_category_id = c.virtuemart_category_id ';
		$q .= ' left join #__virtuemart_category_categories as cat2 on cat2.category_parent_id = c.virtuemart_category_id ';
		$q .= ' left join #__virtuemart_category_categories as cat3 on cat3.category_child_id = c.virtuemart_category_id ';
		$q .= ' where p.virtuemart_category_id IS NULL and cat2.category_parent_id IS NULL and cat2.category_child_id IS NULL'; 
		
		//$q .= ' where c.virtuemart_category_id NOT IN (select category_parent_id from #__virtuemart_category_categories) '; //and p.virtuemart_category_id IS NULL'; 
		//$q .= ' and c.virtuemart_category_id NOT IN (select virtuemart_category_id from #__virtuemart_product_categories) '; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		
		
		foreach ($res as $row) {
			$cid = (int)$row['virtuemart_category_id']; 
			$cats[$cid] = $cid; 
		}
		
		$q = 'select virtuemart_category_id from #__virtuemart_calc_categories where 1=1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		//unsed used: 
		foreach ($res as $row) {
			$cid = (int)$row['virtuemart_category_id']; 
			unset($cats[$cid]); 
		}
		
		
		$q = 'select `c`.`virtuemart_category_id` from `#__virtuemart_calc_categories` as `c` '; 
		$q .= ' left join `#__virtuemart_categories` as cx on `cx`.`virtuemart_category_id` = `c`.`virtuemart_category_id` ';
		$q .= ' where (cx.virtuemart_category_id IS NULL)'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		foreach ($res as $row) {
			$cid = (int)$row['virtuemart_category_id']; 
			$cats[$cid] = $cid; 
		}

		$q = 'select `c`.`virtuemart_category_id` from `#__virtuemart_category_medias` as `c` '; 
		$q .= ' left join `#__virtuemart_categories` as cx on `cx`.`virtuemart_category_id` = `c`.`virtuemart_category_id` ';
		$q .= ' where (cx.virtuemart_category_id IS NULL)'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		foreach ($res as $row) {
			$cid = (int)$row['virtuemart_category_id']; 
			$cats[$cid] = $cid; 
		}
		
		$q = 'select `c`.`category_parent_id` from `#__virtuemart_category_categories` as `c` '; 
		$q .= ' left join `#__virtuemart_categories` as cx on `cx`.`virtuemart_category_id` = `c`.`category_parent_id` ';
		$q .= ' where (cx.virtuemart_category_id IS NULL)'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		foreach ($res as $row) {
			$cid = (int)$row['category_parent_id']; 
			if (!empty($cid)) {
				$cats[$cid] = $cid;
			}			
		}
		
		$q = 'select `c`.`category_child_id` from `#__virtuemart_category_categories` as `c` '; 
		$q .= ' left join `#__virtuemart_categories` as cx on `cx`.`virtuemart_category_id` = `c`.`category_child_id` ';
		$q .= ' where (cx.virtuemart_category_id IS NULL)'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		foreach ($res as $row) {
			$cid = (int)$row['category_child_id']; 
			if (!empty($cid)) {
				$cats[$cid] = $cid;
			}			
		}
		
		$q = 'select `c`.`virtuemart_category_id` from `#__virtuemart_product_categories` as `c` '; 
		$q .= ' left join `#__virtuemart_categories` as cx on `cx`.`virtuemart_category_id` = `c`.`virtuemart_category_id` ';
		$q .= ' where (cx.virtuemart_category_id IS NULL)'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		foreach ($res as $row) {
			$cid = (int)$row['virtuemart_category_id']; 
			if (!empty($cid)) {
				$cats[$cid] = $cid;
			}			
		}
		


		
		//remove categories which are ONLY in language tables: 
		foreach($langs as $vmlang) {
			
			$q = 'select `c`.`virtuemart_category_id` from `#__virtuemart_categories_'.$vmlang.'` as `c` '; 
			$q .= ' left join `#__virtuemart_categories` as cx on `cx`.`virtuemart_category_id` = `c`.`virtuemart_category_id` ';
			$q .= ' where (cx.virtuemart_category_id IS NULL)'; 
			
			/*
			$q = 'select c.virtuemart_category_id from `#__virtuemart_categories_'.$vmlang.'` as c '; 
			//$q .= ' left join `#__virtuemart_categories` as p on p.virtuemart_category_id = c.virtuemart_category_id ';
			$q .= ' where c.virtuemart_category_id NOT IN (select virtuemart_category_id from #__virtuemart_categories)'; 
			*/
			/*
			$q = 'SELECT virtuemart_category_id from `#__virtuemart_categories_'.$vmlang.'` as `c` '; 
			$q .= ' WHERE  NOT EXISTS ( '; 
			$q .= ' SELECT 1 '; 
			$q .= ' FROM #__virtuemart_categories as i WHERE  c.virtuemart_category_id = i.virtuemart_category_id )'; 
			*/
			//$msg .= $q."\n<br />"; 
			$db->setQuery($q); 
			$res = $db->loadAssocList(); 
	
			foreach ($res as $row) {
				$cid = (int)$row['virtuemart_category_id']; 
				$cats[$cid] = $cid; 
			}
		}
		
		
		/*
		$db = JFactory::getDBO(); 
		$q = 'select virtuemart_category_id from #__virtuemart_categories where 1=1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		$cats = array(); 
		foreach ($res as $row) {
			$cid = (int)$row['virtuemart_category_id']; 
			$cats[$cid] = $cid; 
		}
		*/
		
		
		
		unset($cats[0]); 
		/*
		$q = 'select virtuemart_category_id from #__virtuemart_product_categories where 1=1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		//unsed used: 
		foreach ($res as $row) {
			$cid = (int)$row['virtuemart_category_id']; 
			unset($cats[$cid]); 
		}
		
		foreach ($cats as $cat_id) {
			$subcats = array(); 
			$this->getSubCats($cat_id, $subcats); 
			
		}
		*/
		
		
		/*
		$q = 'START TRANSACTION'; 
		$db->setQuery($q); 
		$db->query(); 
		*/
		
		
		foreach ($cats as $cat_id) {
			
			$this->removeCategory($cat_id, $langs); 
		}
		
		
		
		
		/*
		$q = 'COMMIT'; 
		$db->setQuery($q); 
		$db->query(); 
		*/
		if (!empty($cats)) {
		  $msg .= 'Removed CAT IDs: '.implode(',', $cats); 
		  return $msg; 
		  $this->removecategories($msg); 
		}
		else {
			if (!empty($msg)) return $msg; 
			return 'No categories deleted'; 
		}
		
	}
	public function grouproductstocategories() {
		
		$db = JFactory::getDBO(); 
		$q = 'delete from #__virtuemart_product_categories where virtuemart_category_id = 0'; 
		$db->setQuery($q); 
		$db->query(); 
		
		
		
		$q = 'select virtuemart_category_id, virtuemart_product_id from #__virtuemart_product_categories where 1=1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (empty($res)) return; 
		$names = array();
		foreach ($res as $row) {
			$cat_id = (int)$row['virtuemart_category_id']; 
			$prod_id = (int)$row['virtuemart_product_id']; 
			if (empty($prod_id)) continue; 
			
			
			$cat_name = $this->getCategoryName($cat_id); 
			$cat_name = md5($cat_name); 
			if (empty($cat_name)) continue; 
			if (empty($names[$cat_name])) $names[$cat_name] = array(); 
			$names[$cat_name][$prod_id] = $cat_id; 
		}
		
		
		$done = array(); 
		foreach ($names as $cat_name=>$data) {
			$p_id = $this->getPrefered($data); 
			
			foreach ($data as $prod_id => $cat_id) {
				
			
				
					if (isset($done[$cat_id])) continue; 
					$done[$cat_id] = true; 
					if ($p_id == $cat_id) continue; 
					$this->productsFromCatToCat($cat_id, $p_id); 
					
			} 
			
		}
		
		$names_by_cat_id = array(); 
		foreach ($res as $row) {
			$cat_id = (int)$row['virtuemart_category_id']; 
			$prod_id = (int)$row['virtuemart_product_id']; 
			if (empty($prod_id)) continue; 
			$cat_name = $this->getCategoryName($cat_id); 
			$cat_name = md5($cat_name); 
			if (empty($cat_name)) continue; 
			if (empty($names_by_cat_id[$cat_name])) $names_by_cat_id[$cat_name] = array(); 
			$names_by_cat_id[$cat_name][$cat_id] = $prod_id; 
		}
		
		 
		foreach ($names_by_cat_id as $cat_name=>$data) {
			$p_id = $this->getPrefered($data); 
			foreach ($data as $cat_id => $prod_id) {
					if (isset($done[$cat_id])) continue; 
					$done[$cat_id] = true; 
					if ($p_id == $cat_id) continue; 
					
					$this->productsFromCatToCat($cat_id, $p_id); 
					
			}
			
		}
		
		
		$catref = array(); 
		$q = 'select category_parent_id, category_child_id from #__virtuemart_category_categories where 1=1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		foreach ($res as $row) {
			$parent_id = $row['category_parent_id']; 
			$child_id = $row['category_child_id']; 
			$child_name = $this->getCategoryName($child_id); 
			$child_name = md5($child_name); 
			/*
			if (!empty($parent_id)) {
			 $parent_name = $this->getCategoryName($parent_id); 
			}
			else {
				$parent_name = '0'; 
			}
			*/
			$catref[$child_name][$parent_id][$child_id] = $child_id;
		}
		
		
		foreach ($catref as $child_name=>$row) {
			$all_cats = array(); 
			$c = count($row); 
			foreach ($row as $parent_id => $row2) {
				$c2 = count($row2); 
				if (($c === 1) && ($c2 === 1)) continue; 
				foreach ($row2 as $child_id => $child_id) {
					$all_cats[$child_id]= $child_id; 
				}
			}
			$p_id = $this->getPrefered($all_cats); 
			unset($all_cats[$p_id]); 
			
			$this->categoriesToCategory($all_cats, $p_id); 
		}
		
	}
	
	private function categoriesToCategory($from_cats=array(), $to_cat=0) {
		//will change all product association to "to-cat" and remove all entries of "from-cat"
		if (empty($from_cats)) return; 
		if (empty($to_cat)) return; 
		$db = JFactory::getDBO(); 
		foreach ($from_cats as $cat_id => $cat_idV) {
			$this->productsFromCatToCat($cat_idV, $to_cat); 
			/*
			$path = array(); 
			$this->getPath($to_cat, $path); 
			if (!empty($path)) {
			$parent_id = reset($path); 
			}
			else {
				$parent_id = 0; 
			}
			*/
			//$q = 'update #__virtuemart_category_categories set category_parent_id = '.(int)$parent_id.' where 
			/*
			$path = array(); 
			$path[] = $to_cat; 
			$this->getPath($catidV, $path); 
			if (!in_array($catidV, $path)) {
			 $q = 'update IGNORE #__virtuemart_category_categories set category_parent_id = '.(int)$to_cat.' where category_parent_id = '.(int)$cat_idV.' and category_child_id <> '.(int)$to_cat; 
			 $db->setQuery($q); 
			 $db->query(); 
			}
			*/
			
			
		}
		
		
	}
	
	private function productsFromCatToCat($from_cat, $to_cat) {
		if ($from_cat == $to_cat) return; 
		if (empty($from_cat)) return; 
		if (empty($to_cat)) return; 
		

		
		//these products are in both categories: 
		//we will remove them from one of them... 
		$db = JFactory::getDBO(); 
		$q = 'select cat1.virtuemart_product_id from #__virtuemart_product_categories as cat1, #__virtuemart_product_categories as cat2 where cat1.virtuemart_category_id = '.(int)$from_cat.' and cat2.virtuemart_category_id='.(int)$to_cat.' and cat1.virtuemart_product_id = cat2.virtuemart_product_id'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) {
		foreach ($res as $row) {
		   $pid = (int)$row['virtuemart_product_id']; 
		   $q = 'delete from #__virtuemart_product_categories where virtuemart_category_id = '.(int)$from_cat.' and virtuemart_product_id = '.(int)$pid; 
		   $db->setQuery($q); 
		   $db->query(); 
		}
		}
		{
		
			$q = 'update #__virtuemart_product_categories set virtuemart_category_id = '.(int)$to_cat.' where virtuemart_category_id = '.(int)$from_cat; 
			$db->setQuery($q); 
			$db->query(); 
		}
		
	}
	private function getPrefered($group, $isreversed=false) {
		$prefered = JRequest::getVar('prefered', ''); 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$pa = OPCmini::parseCommas($prefered); 
		
		
		$last = 0; $last_id = 0; 
		if (empty($isreversed)) {
		foreach ($group as $prod_id=>$cat_id) {
		   	$path = array(); 
			$path[] = $cat_id; 
			$this->getPath($cat_id, $path); 
			
			foreach ($pa as $pcat_id) {
				if (in_array($pcat_id, $path)) {
					return $cat_id; 
				}
			}
			
			if (count($path)>=$last) {
				$last = count($path); 
				$last_id = $cat_id; 
			}
		}
		}
		else {
			foreach ($group as $cat_id=>$prod_id) {
		   	$path = array(); 
			$path[] = $cat_id; 
			$this->getPath($cat_id, $path); 
			
			foreach ($pa as $pcat_id) {
				if (in_array($pcat_id, $path)) {
					return $cat_id; 
				}
			}
			
			if (count($path)>=$last) {
				$last = count($path); 
				$last_id = $cat_id; 
			}
		}
		}
		return $last_id; 
	}
	
	static $_category_names; 
	public function getCategoryName($cat_id) {
		$cat_id = (int)$cat_id; 
		
		if (empty(self::$_category_names)) self::$_category_names = array(); 
		if (isset(self::$_category_names[$cat_id])) return self::$_category_names[$cat_id]; 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		if (!defined('VMLANG')) {
		OPCmini::setVMLANG(); 
		}
		$db = JFactory::getDBO(); 
		$q = 'select category_name from `#__virtuemart_categories_'.VMLANG.'` where virtuemart_category_id = '.(int)$cat_id; 
		$db->setQuery($q); 
		$cat_name = $db->loadResult(); 
		if (!empty($cat_name)) {
		  self::$_category_names[$cat_id] = $cat_name; 
		}
		else {
			JFactory::getApplication()->enqueueMessage('Category ID '.$cat_id.' got no record in language table #__virtuemarty_categories_'.VMLANG); 
			$cat_name = ''; 
			self::$_category_names[$cat_id] = 'UNKNOWN';
			
			$q = 'select virtuemart_category_id from #__virtuemart_categories where virtuemart_category_id = '.(int)$cat_id; 
			$db->setQuery($q); 
			$res = $db->loadAssocList(); 
			
			if (empty($res)) {
				$this->removeCategory($cat_id, array()); 
			}
			else {
				$langs = self::getDBLangs(); 
				$cat_name = ''; 
				foreach ($langs as $lang) {
				 
				 $q = 'select category_name from `#__virtuemart_categories_'.$lang.'` where virtuemart_category_id = '.(int)$cat_id; 
				 $db->setQuery($q); 
				 $cat_name = $db->loadResult(); 
				 if (!empty($cat_name)) {
					 break; 
				 }
				}
				//category not found in ANY lang tables: 
				if (empty($cat_name)) {
					$this->removeCategory($cat_id, $langs); 
				}
				else {
					 self::$_category_names[$cat_id] = $cat_name; 
				}
			}
			
		}
		return $cat_name; 
		
	}
	
	private function removeCategory($cat_id, $langs=array()) {
		if (empty($cat_id)) return; 		
		$db = JFactory::getDBO(); 
		$q = 'delete from #__virtuemart_category_categories where category_child_id = '.(int)$cat_id; 
		$db->setQuery($q); 
		$db->query(); 
		
		
		$db = JFactory::getDBO(); 
		$q = 'delete from #__virtuemart_product_categories where virtuemart_category_id = '.(int)$cat_id; 
		$db->setQuery($q); 
		$db->query(); 
		
		$db = JFactory::getDBO(); 
		$q = 'delete from #__virtuemart_categories where virtuemart_category_id = '.(int)$cat_id; 
		$db->setQuery($q); 
		$db->query(); 
		
			$db = JFactory::getDBO(); 
		$q = 'delete from #__virtuemart_category_medias where virtuemart_category_id = '.(int)$cat_id; 
		$db->setQuery($q); 
		$db->query(); 
		
		if (!empty($langs))
		foreach ($langs as $l ) {
		 $t = '#__virtuemart_categories_'.$l; 
		 if (OPCmini::tableExists($t)) {
		  $q1 = 'delete from `'.$t.'` where `virtuemart_category_id` = '.(int)$cat_id; 
		  $db->setQuery($q1); 
		  $db->query(); 
		 }
		}
		
	}
		
	private function getSubCats($cat_id, &$all_cats=array()) {
		static $cache; 
		if (empty($cache)) $cache = array(); 
		if (isset($cache[$cat_id])) {
			$all_cats = array_merge($all_cats, $cache[$cat_id]); 
			return; 
		}
		
		$db = JFactory::getDBO(); 
		$q = 'select category_child_id from #__virtuemart_category_categories where category_parent_id = '.(int)$cat_id; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		$my_all_cats = array(); 
		if (!empty($res)) {
		 foreach ($res as $row) {
		   $subcat_id = (int)$row['category_child_id']; 
		   $my_all_cats[$subcat_id] = $subcat_id; 
		   $this->getSubCats($subcat_id, $my_all_cats); 
		 }
		 
		}
		if (!empty($my_all_cats)) {
			$cache[$cat_id] = $my_all_cats; 
			$all_cats = array_merge($all_cats, $my_all_cats); 
		}
		else {
			$cache[$cat_id] = array(); 
		}
		
	}
	
	function copyLangTable($table='products', $index='virtuemart_product_id') {
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 


		
		 {
		  $lang_from = JRequest::getVar('lang_from', ''); 
		  $lang_to = JRequest::getVar('lang_to', ''); 
		  if ($lang_from === $lang_to) return; 
		  
		  $lang_from = strtolower($lang_from); 
		  $lang_to = strtolower($lang_to); 
		  
		  $lang_to = str_replace('-', '_', $lang_to); 
		  $lang_from = str_replace('-', '_', $lang_from); 
		  $err = ''; 
		  if (!OPCmini::tableExists('virtuemart_'.$table.'_'.$lang_to)) {
			  $err .= 'Table does not exists '.'virtuemart_'.$table.'_'.$lang_to; 
		  }			  
		  
		  if (!OPCmini::tableExists('virtuemart_'.$table.'_'.$lang_from)) {
			  $err .= 'Table does not exists '.'virtuemart_products_'.$lang_to; 
		  }	
		  $cols = OPCmini::getColumns('virtuemart_'.$table.'_'.$lang_from); 
		  
		  foreach ($cols as &$c) { $c = '`'.$c.'`'; }
		  
		  if (empty($err)) {
			 $q = 'insert into `#__virtuemart_'.$table.'_'.$lang_to.'` ('.implode(',', $cols).') '; 
			 
			  foreach ($cols as &$c) { $c = 'a.'.$c; }
			 
			 $q .= 'select '.implode(',',$cols).' from `#__virtuemart_'.$table.'_'.$lang_from.'` as a left join `#__virtuemart_'.$table.'_'.$lang_to.'` as b on a.'.$index.' = b.'.$index.' where b.'.$index.' IS NULL'; 
			 $db = JFactory::getDBO(); 
			 $db->setQuery($q); 
			 $db->query(); 
			  
			  
			//$q = 'SELECT a.* FROM #__virtuemart_products as a LEFT JOIN #__virtuemart_products_nb_no as b on a.virtuemart_product_id = b.virtuemart_product_id WHERE b.virtuemart_product_id IS NULL'; 
					
		  }
		   
		    
		   
		   
		  
		  
		  
		 
		}
	}
	
	
	
	
	function getDefaults($var='', $def='')
	{
		$session = JFactory::getSession(); 
		$ret = $session->get('defaults', array(), 'opcutils'); 
		$retO = new stdClass(); 
		
		$user = JFactory::getUser();
		$recipient = $user->email;
		$retO->recipient = $recipient; 
		$retO->user = $retO->password = $retO->database = $retO->host = $retO->prefix = ''; 
		$retO->sg = 0; 
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		
		 $retO->cur = $config->getVendorCurrency(); 
		
		
		if (!empty($ret)) {
		  foreach ($ret as $k=>$v) {
		    $retO->$k = $v; 
		  }
		}
		
		
		if ((!empty($var)) && (isset($retO->$var))) return $retO->$var; 
		if (!empty($var)) return $def; 
		
		return $retO; 
		
	}
	
	function storeDefaults() {
		$post = JRequest::get('post'); 
		$rt = array(); 
		$session = JFactory::getSession(); 
		$ret = $session->get('defaults', array(), 'opcutils'); 
		$retO = new stdClass(); 
		if (!empty($ret)) {
		  foreach ($ret as $k=>$v) {
			  $rt[$k] = $v;  
		      $retO->$k = $v; 
		  }
		}
		if (!empty($post)) {
		foreach ($post as $k=>$v) {
		   if (strpos($k, 'opcxvars_')===0) {
		      $k2 = str_replace('opcxvars_', '', $k); 
			  $rt[$k2] = $v;  
			  $retO->$k = $v; 			  
		   }
		}
		}
		if (!empty($rt)) {
		 $session = JFactory::getSession(); 
		 $session->set('defaults', $rt, 'opcutils'); 
		 return $retO;   
		}
		
		return false; 
		
		
		
		
	}
	
	function __construct() {
		parent::__construct();
		$this->storeDefaults(); 

	}
	
	function pair()
	{
		die('pair...'); 
	}
	
	function getCustomConfig()
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
		
		$db = JFactory::getDBO(); 
		$q = 'select cs.virtuemart_custom_id from #__virtuemart_customs as cs, #__virtuemart_product_customfields as cp where cs.field_type = \'A\' and cs.virtuemart_custom_id = cp.virtuemart_custom_id limit 0,1'; 
		$db->setQuery($q); 
		$custom_id = $db->loadResult(); 
		$e = $db->getErrorMsg(); if (!empty($e)) die($e); 

		if (empty($custom_id)) { 
		
		  JFactory::getApplication()->enqueueMessage('A new custom field created');  
		  $q = 'INSERT INTO `#__virtuemart_customs` (`virtuemart_custom_id`, `show_title`, `custom_parent_id`, `virtuemart_vendor_id`, `custom_jplugin_id`, `custom_element`, `admin_only`, `custom_title`, `custom_tip`, `custom_value`, `custom_desc`, `field_type`, `is_list`, `is_hidden`, `is_cart_attribute`, `is_input`, `layout_pos`, `custom_params`, `shared`, `published`, `created_on`, `created_by`, `ordering`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES '; 
		  $q .= " (NULL, 1, 0, 1, 0, '', 0, 'Size', '', '', '', 'A', 0, 0, 1, 0, 'ontop', 'withParent=0|parentOrderable=0|wPrice=0|', 0, 1, '2015-04-13 12:42:27', 6151, 0, '2015-04-13 12:42:27', 6151, '0000-00-00 00:00:00', 0) ";
		  $db->setQuery($q); 
		  $db->query(); 
		  $custom_id = $db->insertid(); 
		   
		  $q = 'select * from #__virtuemart_product_customs where virtuemart_custom_id = '.(int)$custom_id.' limit 0,1'; 
		  $db->setQuery($q); 
		  $res = $db->loadAssoc(); 

		}
	    if (!isset($res))
		{
		$q = 'select * from #__virtuemart_product_customfields where virtuemart_custom_id = '.(int)$custom_id.' limit 0,1'; 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		}
		$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
		
		  $res['product_sku'] = 'NULL'; 
		  $res['product_gtin'] = 'NULL'; 
		  $res['product_mpn'] = 'NULL'; 
		  $res['customfield_price'] = 'NULL'; 
		  $res['customfield_value'] = 'product_name';  
		  $res['virtuemart_customfield_id'] = 'NULL'; 
		  $res['virtuemart_custom_id'] = $custom_id; 
		  $res['virtuemart_product_id'] = 0; 
		  $res['customfield_params'] = 'display_type="button"|data_type=""|is_required="0"|is_price_variant="0"|display_price="0"|'; 
		  
		  return $res; 

	}
	private  function clearBuf() {
	 $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); 
	}
	
	 function printCSV($arr, $name='') {
	
	   @header('Content-Type: text/csv');
	   @header('Content-disposition: attachment;filename=export'.$name.'.csv');
	   @header("Pragma: no-cache");
	   @header("Expires: 0");
	   $this->outputCSV($arr); 
	   JFactory::getApplication()->close(); 
	   die(); 
	}
	
	private function outputCSV($data) {
		/*
		
		$outstream = fopen("php://output", "w");
			function __outputCSV(&$vals, $key, $filehandler) {
				if (!is_array($vals)) { echo 'error !'; return; }
				fputcsv($filehandler, $vals, ',', "'", "\\"); // add parameters if you want
			}
		array_walk($data, "__outputCSV", $outstream);
		fclose($outstream);
		*/
		
		

$fp = fopen("php://output", "w");
fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

$first = reset($data); 
$header = array(); 
foreach ($first as $key=>$v) {

	
fputs($fp, $this->__encodeFunc($key).','); 
	$header[$key] = $key; 
	
}
fputs($fp, "\r\n"); 
//fputs($fp, implode(",", array_map("__encodeFunc", $header))."\r\n");	


reset($data); 

foreach($data as $row){
	$towrite = array(); 	
    //fputs($fp, implode(",", array_map("__encodeFunc", $row))."\r\n");
	
	
foreach ($header as $key=>$xx) {
  if (!isset($row[$key])) $row[$key] = ''; 
  $val = $this->__encodeFunc($row[$key]).','; 
  fputs($fp, $val); 
  
  // fputs($fp, implode(",", array_map("__encodeFunc", $header))."\r\n");	
}

fputs($fp, "\r\n"); 
	
	
}
fclose($fp);
		
		
		}
	
	
	function __encodeFunc($value) {
    ///remove any ESCAPED double quotes within string.
    $value = str_replace('\\"','"',$value);
    //then force escape these same double quotes And Any UNESCAPED Ones.
    $value = str_replace('"','\"',$value);
    //force wrap value in quotes and return
    return '"'.$value.'"';
    }
	
	
	
	public static function sendNotice($msg, $ev) {
	
	$mailer = JFactory::getMailer();
	$config = JFactory::getConfig();
	$sender = array( 
    $config->get( 'mailfrom' ),
    $config->get( 'fromname' ) 
	);
	if (empty($ev)) {
	$user = JFactory::getUser();
	$recipient = $user->email;
	}
	else $recipient = $ev; 
    //$recipient = 'import@rupostel.com'; 
	$mailer->addRecipient($recipient);
	$mailer->setSender($sender);
	$body   = $msg;
	$mailer->setSubject('NOTICE');
    $mailer->setBody($body);
	$mailer->isHTML(false);
	$send = $mailer->Send();
	}
	
	function export_prices() {
		$sg = JRequest::getVar('sg', 0); 
		$db = JFactory::getDBO(); 
		$q = 'select p1.product_sku, p2.product_price from #__virtuemart_product_prices as p2, #__virtuemart_products as p1 where p1.virtuemart_product_id = p2.virtuemart_product_id and p2.virtuemart_shoppergroup_id = '.$sg; 
		$res = $db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		
		if (!empty($res)) {
		  $res = $res; 
		  $ret2 = array(); 
		  foreach ($res as $k=>$v) {
		  if (empty($v['product_sku'])) continue; 
		  $ret2[$v['product_sku']] = array(); 
		  $ret2[$v['product_sku']]['product_sku'] = $v['product_sku']; 
		  $ret2[$v['product_sku']]['product_price'] = $v['product_price']; 
		  }
			
		 if (!empty($ret2)) {
		  $this->clearBuf(); 
		  $this->printCSV($ret2, '_prices'); 
		 }
		}
		
		
	}
	
	public function price_import($src, $nb, $sg=0, $returna=false) {
		  
		  
	if (class_exists('cliHelper')) { cliHelper::debug('Starting price import...'); 	}
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
	$errorLines = ''; 
		$error = false; 
 		$errors = array(); 
 		$db = JFactory::getDBO();
		if (empty($sg))
		$sg = $this->getDefaults('sg', 0); 
		//First check if the file has the right extension, we need jpg only
		$ext = JFile::getExt($src);
		if (strpos($ext, 'csv')===0)
		{
		 $row = 1;
		 $ramD = array(); 
		 if (($handle = fopen($src, "r")) !== FALSE) {
    	while (($data = fgetcsv($handle, 5000)) !== FALSE) {
		if ($row === 1) {
			$row++; 
			continue; 
		}
			
    	if ((count($data)==2) || ((count($data)==3) && (empty($data[2]))))
    	{
			$row++; 
		   //$q = 'set @PRODUCTID=NULL'; $db->setQuery($q); $db->query(); 
    	   $sku = $data[0]; 
		  
		  if (empty($sku)) continue;  
		  if (stripos($data[1], ',')) $data[1] = str_replace(',', '.', $data[1]); 
    	  $price = (double)$data[1]; 
		  if (empty($price)) continue; 
		  
		  $ramD[$sku] = $price; 
		  
		  if (defined('OPCCLI')) { 
		  echo "Confirm:\n";
		  echo "SKU:".$sku."\n";
		  echo "Price:".$price."\n";
		  echo "Press any key to continue..."; 
		  fgetc(STDIN); 
		  }
		  
    	}
		else
		{
			
			
			$row++; 
		   //$q = 'set @PRODUCTID=NULL'; $db->setQuery($q); $db->query(); 
    	   $sku = $data[0]; 
		   
		   $price_index = 3; 
		  if (empty($sku)) continue;  
		  if (stripos($data[$price_index], ',')) $data[$price_index] = str_replace(',', '.', $data[$price_index]); 
    	  $price = (double)$data[$price_index]; 
		  if (empty($price)) continue; 
		  
		  $ramD[$sku] = $price; 
		  
		  if (defined('OPCCLI')) { 
		  static $washere; 
		  if (empty($washere)) {
		  $errorLines .= var_export($data, true); 
		  echo $errorLines."\n";
		  echo "Confirm:\n";
		  echo "SKU:".$sku."\n";
		  echo "Price:".$price."\n";
		  echo "Press any key to continue..."; 
		  fgetc(STDIN); 
		  $washere = true; 
		  }
		  }
			
			/*
			$errorLines .= var_export($data, true); 
			
			$error = true;
			if (defined('OPCCLI')) { 
			echo $errorLines."\n";
			echo "Error with data !";
			echo "Press any key to continue..."; 
		    fgetc(STDIN); 
			}
			*/
			
		}
			
    	}
    	fclose($handle);
		}
		}
		
	if (class_exists('cliHelper')) { cliHelper::debug('Found '.count($ramD).' rows in price file...'); 	}	
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		 $config = new JModelConfig(); 
		 //$cid = JRequest::getInt('cur', 0); 
		 $cid = $this->getDefaults('cur', 0); 
		 if (empty($cid)) {
		   $cid = $config->getVendorCurrency(); 
		 }
		 
		
		
		if (empty($ramD)) {
			JFactory::getApplication()->enqueueMessage('No data found in CSV ! Check the CSV format.'.$errorLines); 
			return; 
		}
		
		if (class_exists('cliHelper')) { cliHelper::debug('Default VM currency ID is '.$cid); 	}	
		
		if (empty($nB))
		if (function_exists('fastcgi_finish_request')) {
			
			
			$root = Juri::root(); 
			
			if (substr($root, -1) !== '/') $root .= '/'; 
			$root .= 'administrator/'; 
			
			JSession::fork(); 
			session_write_close(); 
		  header("Location: ".$root.'index.php?option=com_onepage&view=utils', true);
		  header("Connection: close", true);
          header("Content-Length: 0", true);
          ob_end_flush();
          flush();
		  //session_write_close(); 
			
		  fastcgi_finish_request();
		}
		
		if (class_exists('cliHelper')) { cliHelper::debug('Loading products into memory...'); 	}	
		
		$q = 'select `virtuemart_product_id`, `product_sku` from #__virtuemart_products where 1 = 1'; 
		/*
		$qZ = array(); 
		foreach ($ramD as $sku=>$price) {
			$sku_parent = substr($sku, 0, -1).'X'; 
			$q1 = " ( product_sku LIKE '".$db->escape($sku)."' )"; 
			if (!isset($ramD[$sku_parent])) {
			 $q2 = " or (( product_sku LIKE '".$db->escape($sku_parent)."' ) and (product_parent_id = 0)) "; 
			}
			else $q2 = ''; 
			
			$qZ[] = $q1.$q2; 
			
			if (!isset($ramD[$sku_parent])) {
			  $ramD[$sku_parent] = $price; 
			}
			
			
		}
		$q .= implode(' or ', $qZ); 
		*/
		$db->setQuery($q); 
		$ids = $db->loadAssocList(); 
		
		
		$qZ = array(); 
		if (!empty($ids)) {
			
		  if (class_exists('cliHelper')) { cliHelper::debug('Loading prices into memory...'); 	}	
			
		  $q = 'select virtuemart_product_id from #__virtuemart_product_prices where 1=1'; 
		  $idsToSkus = array(); 
		  $skusID = array(); 
		  
		  foreach ($ids as $k=>$row) {
			//$qZ[] = " ( virtuemart_product_id = '".$db->escape($row['virtuemart_product_id'])."' )"; 
			$id = (int)$row['virtuemart_product_id']; 
			$idsToSkus[$row['product_sku']] = $id; 
			$skusID[$id] = $row['product_sku']; 
			unset($ids[$k]); 
		  }
		  unset($ids); 
		  //$q .= implode(' or ', $qZ); 
		  $db->setQuery($q); 
		  $idsIN = $db->loadAssocList(); 
		  
		  $idsIZ = array(); 
		  foreach ($idsIN as $k=>$row)
		  {
			  $id = (int)$row['virtuemart_product_id'];
			  if (!isset($skusID[$id])) continue; 
			  $idsIZ[$skusID[$id]] = $id; 
			  unset($idsIN[$k]); 
		  }
		  
		  
		   
		  if ($returna) {
			  return $ramD; 
		  }
		  unset($idsIN); 
		  $rq = ''; 
		  foreach ($ramD as $sku=>$price) {
			 if (!isset($idsToSkus[$sku])) {
				 if (class_exists('cliHelper')) { cliHelper::debug($sku.': Product does not exists...'); 	}	
				 continue; //product does not exists
			 }
		     if (isset($idsIZ[$sku])) {
				 
				 $sgq = "and `virtuemart_shoppergroup_id` = ".$sg;
				  if (empty($sg)) {
					  $sg = 0; 
					  $sqq = "and (`virtuemart_shoppergroup_id` = ".$sg." or `virtuemart_shoppergroup_id` IS NULL) "; 
				  }
				  if (class_exists('cliHelper')) { cliHelper::debug($sku.': Updating price...'); 	}	
				  $q = "update `#__virtuemart_product_prices` set `product_price`='".$db->escape($price)."', `product_override_price` = 0, `override` = 0, `created_on` = NOW(), `product_price_publish_up` = '0000-00-00 00:00:00', `modified_on` = NOW(), `product_currency` = ".(int)$cid." where (`virtuemart_product_id` = ".(int)$idsToSkus[$sku]." ".$sgq.") limit 1"; 
				  $db->setQuery($q); 
				  $res = $db->query(); 
				  
			 //	  $rq .= str_replace('#__', $db->getPrefix(), $q)."<br />\n"; 
			 }
			 else
			 {
				
				 //insert:
				 if (empty($sg)) $sg = 'NULL'; 
				 else $sg = (int)$sg;
				 if (class_exists('cliHelper')) { cliHelper::debug($sku.': Inserting new price...'); 	}	
				 $q = "insert into #__virtuemart_product_prices (product_price, virtuemart_product_id, virtuemart_shoppergroup_id, override, product_override_price, product_tax_id, product_discount_id, product_currency) values ('".$db->escape($price)."', ".(int)$idsToSkus[$sku].", ".$sg.", 0, 0, 0, 0, ".(int)$cid.")"; 
				 $db->setQuery($q); 
				 $db->query(); 
				 
				// $rq .= str_replace('#__', $db->getPrefix(), $q)."<br />\n"; 
				 
			 }
		  }
		  
		  
		}
		
		JFactory::getApplication()->enqueueMessage('Export finished !'); 
		/*
		 $q = "update `#__virtuemart_product_prices` as p, `#__virtuemart_products` as p2 set p.`product_price`='".$db->escape($price)."' where ((p.`virtuemart_product_id` = p2.`virtuemart_product_id`) and (p2.`product_sku` LIKE '".$db->escape($sku)."') and (p.`virtuemart_shoppergroup_id` = ".$sg.")) and (@PRODUCTID:=p2.`virtuemart_product_id`)"; 
		  $db->setQuery($q); 
		  
		  $res = $db->query(); 
		 
		  
		  echo str_replace('#__', $db->getPrefix(), $q)."<br />\n"; 
		  if (!empty($res)) {
			  $af = $db->getAffectedRows($res);
			  
			  
		     $q = 'select @PRODUCTID'; $db->setQuery($q); $product_id = $db->loadResult(); 
			 echo 'id:'.$product_id."<br />\n"; 
		  }
		  else
		  {
			  die('rrr'); 
		  }
		
		*/
		
		if (empty($errorLines)) $errorLines .= 'no errors'."<br />\n"; 
		$rq = 'queries:'."<br />\n".$rq; 
		
		if ($error) JFactory::getApplication()->enqueueMessage(JText::_('COM_ONEPAGE_CSV_IMPORT_ERROR')); ; 
		
		
		
		
		if (!empty($defaults->recipient))
		self::sendNotice('Price Import finished... '.'Errors: '.$errorLines.$rq, $defaults->recipient); 
	
	
	
	}
	function csv_upload()
		{
		
		$defaults = $this->getDefaults(); 
		
		//Retrieve file details from uploaded file, sent from upload form
		$file = JRequest::getVar('file_upload', null, 'files', 'array');
		@ignore_user_abort(true); 
		@set_time_limit(7200);
		
		$nB = $this->getDefaults('back', false); 
		if (empty($nB))
		if (function_exists('fastcgi_finish_request')) {
		JFactory::getApplication()->enqueueMessage('All proceses are done at background, you will receive an email once it is finished'); 
		}
		
		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');
 
		//Clean up filename to get rid of strange characters like spaces etc
		$filename = JFile::makeSafe($file['name']);
 
		//Set up the source and destination of the file
		$src = $file['tmp_name'];
		$this->price_import($src, $nb); 
	}
	
	
	function setCustoms($config, $parent_id)
	{
		$db = JFactory::getDBO(); 
		$config['virtuemart_product_id'] = $parent_id; 
		$q = 'select virtuemart_customfield_id from #__virtuemart_product_customfields where virtuemart_product_id = '.(int)$parent_id.' and virtuemart_custom_id = '.(int)$config['virtuemart_custom_id'].' limit 0,1'; 
		$db->setQuery($q); 
		$check = $db->loadResult(); 
		
		$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
		
		
		
		if (empty($check))
		{

			$in = $this->createInsert($config); 
			$q = 'insert into `#__virtuemart_product_customfields` '.$in;
			echo $q; 
			$db->setQuery($q); 
			$db->query(); 
			$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
		}
		//die('ok'); 
	}
	
	function createInsert($arr)
	{
		$db = JFactory::getDBO(); 
		$cols = $vals = array(); 
		foreach ($arr as $k=>$v)
		{
			$cols[] = '`'.$k.'`'; 
			if ($v === 'NULL')
			$vals[] = "NULL"; 
			else
			$vals[] = "'".$db->escape($v)."'"; 
		}
		$insert = ' ('.implode(',', $cols).') values ('.implode(',', $vals).')'; 
		return $insert; 
		
	}
	
	function setParent($product_id, $parent_id)
	{
		if (empty($product_id)) die('empty product id'); 
		$db = JFactory::getDBO(); 
		if (!is_array($product_id))
		$q = 'update #__virtuemart_products set product_parent_id = '.(int)$parent_id.' where virtuemart_product_id = '.(int)$product_id.' limit 0,1'; 
		else
		$q = 'update #__virtuemart_products set product_parent_id = '.(int)$parent_id.' where virtuemart_product_id IN ( '.implode(',', $product_id).') '; 
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
		
	}
	
	function checkCats($group)
	{
		$db = JFactory::getDBO(); 
		$cats = array(); 
		foreach ($group as $k=>$pp)
				{
				 $product_id = $pp['virtuemart_product_id']; 
				 if (empty($product_id)) continue; 
				 $product_ids[$product_id] = (int)$product_id; 
				 
				 foreach ($pp['cats'] as $k=>$c)
				 {
					 $cats[$c] = $c; 
				 }
				}
		
		
		
		$q = 'select virtuemart_product_id from #__virtuemart_products where virtuemart_product_id IN ('.implode(', ',$product_ids).') and product_parent_id > 0'; 
		$db->setQuery($q); 
		$arr = $db->loadAssocList(); 
		if (empty($arr)) return; 
		
		
		
		$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
		$ids = array(); 
		foreach ($arr as $row)
	    {
			$ids[$row['virtuemart_product_id']] = $row['virtuemart_product_id']; 
		}
		
		
		
		
		// remove categorized children: 
		$q = 'delete from #__virtuemart_product_categories where virtuemart_product_id IN ('.implode(',', $ids).')'; 
		echo $q."<br />\n"; 
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
		//add categories to parent: 
		$q = 'select virtuemart_product_id from #__virtuemart_products where virtuemart_product_id IN ('.implode(', ',$product_ids).') and product_parent_id = 0'; 
		$db->setQuery($q); 
		$arr = $db->loadAssocList(); 
		
		
		
		if (!empty($arr))
		foreach ($arr as $product)
		{
		 $product_id = $product['virtuemart_product_id']; 
		 $db = JFactory::getDBO(); 
		 if (!empty($cats))
		 {
			foreach ($cats as $cat)
			{
				if (empty($cat)) continue; 
				if (empty($product_id)) return; 
				
				$q = 'select virtuemart_product_id from #__virtuemart_product_categories where virtuemart_product_id = '.(int)$product_id.' and virtuemart_category_id = '.(int)$cat.' limit 0,1'; 
				$db->setQuery($q); 
				$r = $db->loadResult(); 
				$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
				if (empty($r))
				{
					// insert: 
					$q = 'insert into #__virtuemart_product_categories (id, virtuemart_product_id, virtuemart_category_id, ordering) '; 
					$q .= ' values (NULL, '.(int)$product_id.', '.(int)$cat.', 0)'; 
					
					$db->setQuery($q); 
					$db->query(); 
					echo $q."<br />\n"; 
					$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
				}
				else
				{
					
				}
				
			}
		 }
		 }
		
		
		
		return; 
		
		
		
		
	}
	
	
	function checkParentPrice($parent_id, $product_ids)
	{
		$db = JFactory::getDBO(); 
		$q = 'select virtuemart_product_id from #__virtuemart_product_prices where virtuemart_product_id = '.(int)$parent_id.' limit 0,1'; 
		$db->setQuery($q); 
		$r = $db->loadResult(); 
		
		if (empty($r))
		{
			$q = 'select * from #__virtuemart_product_prices where virtuemart_product_id IN ('.implode(',', $product_ids).') order by product_price desc limit 0,1'; 
			$db->setQuery($q); 
			$r = $db->loadAssoc(); 
			$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
			
			if (!empty($r))
			{
			$r['virtuemart_product_id'] = $parent_id; 
			$r['virtuemart_product_price_id'] = 'NULL'; 
			$in = $this->createInsert($r); 
			$q = 'insert into `#__virtuemart_product_prices` '.$in;
			$db->setQuery($q); 
			$db->query(); 
			$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
			echo $q."<br />\n"; 
			
			}
			else
			{
				//die('empty r'); 
			}
		}
		
		
	}
		//product_import cli
	function csv_upload_product($src='', $cli=false, $sg=null, $istest=false, $pricefile='') {
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		 $totalc = 0; 
		 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		
		
		$prices = array(); 
		if (!empty($pricefile)) {
			$prices = $this->price_import($pricefile, true, 0, true); 
		}
		
		
		
		if (empty($cli)) {
		 @ignore_user_abort(true); 
		@set_time_limit(7200);
		}
		
		if (function_exists('fastcgi_finish_request')) {
		  JFactory::getApplication()->enqueueMessage('All proceses are done at background, you will receive an email once it is finished'); 
		}
		
		if (empty($src)) {
	   //Retrieve file details from uploaded file, sent from upload form
		$file = JRequest::getVar('file_upload', null, 'files', 'array');

		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');
 
		//Clean up filename to get rid of strange characters like spaces etc
		$filename = JFile::makeSafe($file['name']);
 
		//Set up the source and destination of the file
		$src = $file['tmp_name'];
		}
		else {
			$filename = $src; 
		}
		
		
		$error = false; 
 		$errors = array(); 
 		$db = JFactory::getDBO();
		if (is_null($sg)) {
		  $sg = JRequest::getInt('sg', 0); 
		}
		//First check if the file has the right extension, we need jpg only
		$ext = JFile::getExt($filename);
		
		if (empty($cli))
		if (function_exists('fastcgi_finish_request')) {
			$root = Juri::root(); 
			if (substr($root, -1) !== '/') $root .= '/'; 
			$root .= 'administrator/'; 
		  
		  @session_write_close(); 
		  JSession::fork(); 
		  @header("Location: ".$root.'index.php?option=com_onepage&view=utils', true);
		  @header("Connection: close", true);
          @header("Content-Length: 0", true);
          @ob_end_flush();
          @flush();
		  
			
		  @fastcgi_finish_request();
		}
		
		
		if ((strpos($ext, 'csv')===0) && (file_exists($src)))
		{
		 $row = 0;
		 $ramD = array(); 
		 
		 $cp = 0; 
		
		 if (($handle = fopen($src, "r")) !== FALSE) {
			 
    	while (($data = fgetcsv($handle, 0, ',', '"')) !== FALSE) {
			
			
    	if (count($data)==11)
    	{
			$row++; 
			if ($row === 1) continue; 
			
			if (empty($data[0])) continue; 
			
		   $p = array(); 
		   $p['ID'] = $data[0]; 
		   $p['NAME'] = $data[1]; 
		   
		   if (empty($p['NAME'])) continue; 
		   
		   $p['SIZE'] = $data[2]; 
		   $p['CATEGORY'] = $data[3]; 
		   $p['SKU'] = $data[4]; 
		   $p['PRICE'] = $data[5]; 
		   
		   if (empty($p['PRICE']) && (!empty($prices)) && (!empty($prices[$p['ID']]))) {
			   $p['PRICE'] = $prices[$p['ID']]; 
			   
			   
		   }
		   
		   $p['CURRENCY'] = $data[6]; 
		   $p['SHORT DESCRIPTION'] = $data[7]; 
		   $p['LONG DESCRIPTION'] = $data[8]; 
		   $p['THUMBNAIL IMAGE'] = $data[9]; 
		   $p['FULL IMAGE'] = $data[10]; 
		   
		   $p['THUMBNAIL IMAGE'] = str_replace('#', '_', $p['THUMBNAIL IMAGE']); 
		   $p['FULL IMAGE'] = str_replace('#', '_', $p['FULL IMAGE']); 
		   
		   if (empty($ramD[$p['ID']])) $ramD[$p['ID']] = array(); 
		   
		   // group by p['id']: 
		   $ramD[$p['ID']][] = $p; 
		   
		  $cp++; 
		 
		  
    	}
		else
		{
			
			
			$error = true; 
		}
			
    	}
    	fclose($handle);
		}
		else
		{
			
		}
		}
		
		
		
		
		
		if (!empty($ramD)) {
			
			if (!empty($istest)) {
				foreach ($ramD as $pid => $products) {
			foreach ($products as $k=>$v) {
				foreach ($v as $k2=>$v2) {
				echo "\033[31m".$k2."\033[0m".':'.$v2."\n"; 
				
				
				}
				break 2; 
			}		
				}
			  
			  if ($cli) {
			  echo "\nPress any key to continue, CTR+C to terminate...\n"; 
			  fgetc(STDIN); 
			  
			  
			  
			  
			  }
			
			
		}
			
		 // 
		 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'schema.php'); 
		 
		// $product = OPCschema::getSchema('virtuemart_products'); 
		// $product = OPCschema::getSchema('virtuemart_products'); 
		
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		 $productModel = OPCmini::getModel('product'); 
		 $mediaModel = OPCmini::getModel('media'); 
		 $storedLang = VmConfig::$vmlangTag; 
		 /*
		 $langs = VmConfig::get('active_languages', array());
		 if ($langs and count($langs)>1){
			$langTable = $this->getTable('products');
			foreach($langs as $lang){
				VmConfig::$vmlangTag = $lang; 
				$langTable->emptyCache();
				$langTable->setLanguage($lang);
				//Disables the language fallback
				$langTable->_ltmp = true;
				
				
				
			}
		}
		*/
		
		
		
		$pricesNull = $productModel->fillVoidPrice();
		if (!class_exists('VmMediaHandler')) require(VMPATH_ADMIN.DS.'helpers'.DS.'mediahandler.php');
		$mediaNull = $mediaModel->createVoidMedia('product', 'image/jpeg'); 
		$customNull = self::getCustomConfig(); 
		$categories = array(); 
		
		
		$mid = self::getMultiVariantId(); 
		
		$products = self::getAllProducts(); 
		
		$icat = self::getImportCat(); 
		
		$c = 0; 
		foreach ($ramD as $k=>$group) {
		
		$parent = true; 
		$parent_id = 0; 
		
		foreach ($group as $xP) { 
		 $c++; 
		//this is to create the first product in the group as parent 
		start:
		
		 
		   $defaultP = new stdClass(); 
		   
		  if (($parent) && (count($group)>1)) {
		   
		   $defaultP->virtuemart_custom_id = $mid; 
		   //$defaultP->customfield_params = 'display_type="button"|data_type=""|is_required="0"|is_price_variant="0"|display_price="0"|'; 
		   $defaultP->customfield_price = 'NULL'; 
		   $defaultP->customfield_value = 'product_name';  
		   $defaultP->virtuemart_customfield_id = null;
		   /*
		   $defaultP->field = array(); 
		   $defaultP->field[0] = array(); 
		   
		   $defaultP->field[0]['virtuemart_custom_id'] = $mid; 
		   $defaultP->field[0]['customfield_params'] = 'display_type="button"|data_type=""|is_required="0"|is_price_variant="0"|display_price="0"|'; 
		   $defaultP->field[0]['customfield_price'] = 'NULL'; 
		   $defaultP->field[0]['customfield_value'] = 'product_name';  
		   $defaultP->field[0]['virtuemart_customfield_id'] = null;
		   $defaultP->field[0]['field_type'] = 'A';
		   */
		   $defaultP->field[0] = $customNull; 
		   $defaultP->field[0]['virtuemart_custom_id'] = $mid; 
		   $defaultP->field[0]['customfield_params'] = array(
		   'display_type'=>"button", 
		   'data_type'=>"",
		   'is_required'=>0,
		   'is_price_variant'=>0,
		   'display_price'=>0
		   ); 
		   $defaultP->customfield_params = $defaultP->field[0]['customfield_params']; 
		   $defaultP->field[0]['customfield_price'] = 'NULL'; 
		   $defaultP->field[0]['customfield_value'] = 'product_name';  
		   $defaultP->field[0]['virtuemart_customfield_id'] = null;
		   $defaultP->field[0]['field_type'] = 'A';
		   $defaultP->field[0]['custom_element'] = ''; 
		   $defaultP->field[0]['custom_jplugin_id'] = ''; 
		   //custom_jplugin_id
		  // $defaultP->field[0]['customfield_params'] = array();
		   
		   
		  }
		  
		  
		 //$defaultP = $productModel->fillVoidProduct(true); 
		 $defaultP->product_name = $xP['NAME']; 
		  if (($parent) && (count($group)>1)) {
			 $defaultP->product_sku = substr($xP['SKU'], 0, -1).'X'; 
			 $defaultP->product_parent_id = 0;
			 
			 
		 }
		 else
		 {
			  
			 
		   if (!empty($parent_id))
		   $defaultP->product_parent_id = $parent_id; 
	       $defaultP->product_name .= ' '.$xP['SIZE']; 
		   $defaultP->product_sku =$xP['SKU']; 
		 }
		 $defaultP->categories = array(); 
		 if (empty($products[$defaultP->product_sku])) {
		 $defaultP->virtuemart_product_id = null; 
		 }
		 else
		 {
			 $defaultP->virtuemart_product_id = $products[$defaultP->product_sku]; 
			 
			 $db = JFactory::getDBO(); 
			$q = 'select virtuemart_category_id from #__virtuemart_product_categories where virtuemart_product_id = '.$defaultP->virtuemart_product_id; 
			$db->setQuery($q); 
			$res = $db->loadAssocList(); 
			if (!empty($res))
			foreach ($res as $row) {
			  $row['virtuemart_category_id'] = (int)$row['virtuemart_category_id']; 
			  // do note delete categories: 
			  $defaultP->categories[$row['virtuemart_category_id']] = $row['virtuemart_category_id']; 
			}
			 
		 }
		 
		 $prices = array(); 
		 $prices['mprices'] = array(); 
		foreach ($pricesNull as $k=>$v) {
		 // do not update prices if empty... 
		 if (empty($xP['PRICE'])) continue; 
		 if (!isset($prices['mprices'][$k])) $prices['mprices'][$k] = array(); 
		 
		 $prices['mprices'][$k][0] = $v; 
         $prices['mprices']['virtuemart_currency_id'][0] = self::getCurrency($xP['CURRENCY']); 		 
		 $prices['mprices']['product_currency'][0] = self::getCurrency($xP['CURRENCY']); 		 
		 $xP['PRICE'] = str_replace(',', '.', $xP['PRICE']); 
		 $prices['mprices']['product_price'][0] = floatval($xP['PRICE']); 		 
		 $prices['mprices']['product_override_price'][0] = 0; 		 
		 if (!empty($defaultP->virtuemart_product_id)) {
			 
		   $prices['mprices']['virtuemart_product_id'][0] = $defaultP->virtuemart_product_id; 	
			
			
			
		 }
		}
		 $catId = self::getImportCat($xP['CATEGORY']); 
		 
		 if (!empty($prices['mprices'])) {
		   $defaultP->mprices = $prices['mprices']; 
		 }
	     else {
			 unset($defaultP->mprices); 
		 }
		 
		 if ((empty($xP['LONG DESCRIPTION'])) && (!empty($xP['SHORT DESCRIPTION']))) {
			 $xP['LONG DESCRIPTION'] = $xP['SHORT DESCRIPTION']; 
			 
		  }
		  
		  $xP['SHORT DESCRIPTION'] = strip_tags($xP['SHORT DESCRIPTION']); 
		  $xP['SHORT DESCRIPTION'] = str_replace("\r\n", ' ', $xP['SHORT DESCRIPTION']); 
		  $xP['SHORT DESCRIPTION'] = str_replace("\n", ' ', $xP['SHORT DESCRIPTION']); 
		  
		
		 $defaultP->product_s_desc = $xP['SHORT DESCRIPTION']; 
		 $defaultP->product_desc = $xP['LONG DESCRIPTION']; 
		 $defaultP->product_unit = "KG"; 
		 $defaultP->virtuemart_vendor_id = 1; 
		 
		 $defaultP->categories[$catId] = $catId; 
		 $defaultP->categories[$icat] = $icat; 
		 $defaultP->virtuemart_category_id = $defaultP->categories; 
		 
		 $id = $productModel->store($defaultP); 
		 $totalc++;  
		 $defaultP->virtuemart_product_id = $virtuemart_product_id = (int)$id; 
		 
		 foreach ($mediaNull as $k=>$v) {
		 $defaultP->$k = $v; 
		 }
		 $defaultP->file_url = $xP['FULL IMAGE']; 
		 $defaultP->file_url_thumb = $xP['THUMBNAIL IMAGE']; 
		 $defaultP->file_title = $xP['NAME']; 
		 $defaultP->file_description = $xP['SHORT DESCRIPTION']; 
		 $defaultP->file_meta = $xP['SHORT DESCRIPTION']; 
		 $defaultP->file_type = 'product'; 
		 $defaultP->file_is_product_image = 1; 
		 $defaultP->file_is_downloadable = 0; 

		 if (!empty($xP['FULL IMAGE'])) {
		 $pa = pathinfo($xP['FULL IMAGE']); 
		 $mime = ''; 
		 if (!empty($pa['extensions'])) {
		  $ext = $pa['extension']; 
		  if ($ext == 'jpg') $ext = 'jpeg'; 
		
		  $mime = 'image/'.strtolower(); 
		 }
		 $defaultP->file_mimetype = $mime; 
		 
		 $defaultP = (array)$defaultP; 
		
		
		 
		 
		 
		 
		 $copy = $defaultP; 
		 $id = $mediaModel->store($copy);
		 
		 
		 //$defaultP['media_action'] = 'upload'; 
		// $defaultP['active_media_id'] = $id; 
		 $defaultP['virtuemart_media_id'] = $id; 
		 $mediaModel->storeMedia ($defaultP, 'product');
		 $mediaModel->emptyCache();
		 }
		 if (($parent) && (count($group)>1)) {
		   $parent_id = $virtuemart_product_id; 
		   $parent = false; 
		   goto start; 
		 }
			
		
		}
		}		
		
		}
		JFactory::getApplication()->enqueueMessage('Imported or updated '.$c.' products'); 
		
		
		
		if (!empty($defaults->recipient)) {
		self::sendNotice('Import finished... '.'Imported or updated '.$c.' products', $defaults->recipient); 
		}
		else {
			echo 'Import finished... '.'Imported or updated '.$c.' products'; 
		}
		
		
	}
	public static function getAllProducts() {
	 $db = JFactory::getDBO(); 
	 $q = 'select virtuemart_product_id, product_sku from #__virtuemart_products where product_sku NOT LIKE "" limit 999999'; 
	 $db->setQuery($q); 
	 $res = $db->loadAssocList(); 
	 $ret = array(); 
	 foreach ($res as $k=>$row) {
		 $ret[$row['product_sku']] = (int)$row['virtuemart_product_id']; 
	 }
	 
	 return $ret; 
	}
	public static function getMultiVariantId() {
	  $db = JFactory::getDBO(); 
	  $q = "select `virtuemart_custom_id`from `#__virtuemart_customs` where `field_type` LIKE 'A' and `published` = 1"; 
	  $db->setQuery($q); 
	  $r = $db->loadResult(); 
	  $r = (int)$r; 
	  return $r; 
	}
	
	public static function getImportCat($name='IMPORT')
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
		
		static $c; 
		if (isset($c[$name])) return $c[$name]; 
		$db = JFactory::getDBO(); 
		
		$q = 'select virtuemart_category_id from `#__virtuemart_categories_'.VMLANG."` where category_name LIKE '".$db->escape($name)."' LIMIT 0,1"; 
		$db->setQuery($q); 
		$i = $db->loadResult(); 
		if (empty($i)) {
		  $catModel = VmModel::getModel ('Category');
		  $new = array(); 
		  $new['category_name'] = $name; 
		  
		  if ($name !== 'IMPORT') {
			  $new['category_parent_id'] = self::getImportCat(); 
			  $new['published'] = 1; 
		  }
		  else 
		  {
			  $new['category_parent_id'] = 0; 
			  $new['published'] = 0; 
		  }
		  
		 
		  
		  $i = $catModel->store($new); 
		  
		  
		}
		
		$c[$name] = $i; 
		return $i; 
		
	}
	public static function getCurrency($c3) {
	  static $c;
      if (isset($c[$c3])) return $c[$c3]; 	  
	  $db = JFactory::getDBO(); 
	  $q = "select virtuemart_currency_id from #__virtuemart_currencies where currency_code_3 = '".$db->escape($c3)."' limit 0,1"; 
	  $db->setQuery($q); 
	  $c[$c3] = (int)$db->loadResult(); 
	  return $c[$c3]; 
	  
	}
	
	function getDefaultRow_virtuemart_products() {
	
	}
	
	function createProducts() {
	  $q = '';
	}
	
	function buildUpdateInsert($row, $table, $new_data, $primary, $ignore='virtuemart_product_id', &$update, &$insert, &$check)
	{
		
		$db = JFactory::getDBO(); 
		$vals = array(); 
		$cols = array(); 
		foreach ($row as $key=>$val)
		{
			//echo $key."<br />\n"; 
			if ($key == $primary)
			{
				if (($key != $ignore) || (!isset($new_data[$primary]))) 
				$val = 'NULL'; 
			    else
				{
					$val = "'".$db->escape($new_id)."'";
				}
			}
			else
				if (isset($new_data[$key]))
				{
					// special case: 
					$val = "'".$db->escape($new_data[$key])."'";
					
				}
			else
			{
				$val = "'".$db->escape($val)."'";
			}
			
			$cols[] = '`'.$key.'`'; 
			$vals[] = $val; 
			
		}
		
		$update = 'update `'.$table.'` set '; 
		$c = 0;  
		foreach ($cols as $k=>$v)
		{
			if ($c !== 0) $update .= ', '; 
			$update .= $cols[$k].' = '.$vals[$k]; 
			$c++; 
		}
		
		if (empty($primary)) $primary = 'virtuemart_product_id'; 
		
		$update .= ' where '.$primary.' = '."'".$db->escape($new_id)."' limit 1"; 
		
		$insert = 'insert into `'.$table.'` ('.implode(',', $cols).') values ('.implode(',', $vals).')'; 
		
		
		$check = ''; 
		$cols = $vals = array(); 
		foreach ($new_data as $k=>$v)
		{
			if (isset($row[$k]))
			{
				$cols[] = '`'.$k.'`'; 
				$vals[] = "'".$db->escape($v)."'"; 
			}
		}
		if (!empty($cols))
		{
			if (empty($primary)) $primary = 'virtuemart_product_id'; 
			$check = 'select '.$primary.' from `'.$table.'` where '; 
			$c = 0; 
			foreach ($cols as $k=>$v)
			{
				if (!empty($c)) $check .= ' OR '; 
				$check .= $cols[$k]." = ".$vals[$k]; 
				$c++; 
			}
			$check .= ' limit 0,1'; 
			
		}
		
		
	}
	
	
	function copyProduct($product_id, &$new_data)
	{
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
	  
		if (!defined('VMLANG')) die('VMLANG NOT DEFINED'); 
		$db = JFactory::getDBO(); 
		$tables = array(
		'#__virtuemart_products' => 'virtuemart_product_id', 
		'#__virtuemart_products_en_gb' => '', 
		'#__virtuemart_products_'.VMLANG => '', 
		'#__virtuemart_product_categories' => '', 
		'#__virtuemart_product_prices' => 'virtuemart_product_price_id'); 
		
		$new_id = 0; 
		
		foreach ($tables as $table => $primary)
		{
		 $q = 'select * from `'.$table.'` where virtuemart_product_id = '.(int)$product_id; 
		 $db->setQuery($q); 
		 $res = $db->loadAssocList(); 
		 $e = $db->getErrorMsg(); if (!empty($e)) die($e); 
		
		 
		 foreach ($res as $row)
		 {
			 
			 $update = $insert = $check = ''; 
			 
			 $do_ins = false; 
			 
			 $this->buildUpdateInsert($row, $table, $new_data, $primary, 'virtuemart_product_id', $update, $insert, $check);
			 if (!empty($check))
			 {
				 echo "<br />\n".$check."<br />\n"; 
				 $db->setQuery($check); 
				 $new_id = $db->loadResult(); 
				 $e = $db->getErrorMsg(); if (!empty($e)) die($e); 
				 if (!empty($new_id))
				 {
				  if (!empty($primary))
				  $new_data[$primary] = $new_id; 
				  $this->buildUpdateInsert($row, $table, $new_data, $primary, 'virtuemart_product_id', $update, $insert, $check);	 
				 }
				 else
				 {
					  echo "<br />\n".$insert."<br />\n"; 
					  $db->setQuery($insert); 
					  $res1 = $db->query(); 
					  $e = $db->getErrorMsg(); if (!empty($e)) die($e); 
					  $last_id = $db->insertid(); 
					  if (!empty($primary))
					  $new_data[$primary] = $last_id; 
					  $e = $db->getErrorMsg(); if (!empty($e)) die($e); 
					  
				 }
			 }
			 else
			 {
				 echo "<br />\n".'missed insert: '.$insert."<br />\n"; 
				 
				 
			 }
			 if ((!empty($primary) && (empty($new_data[$primary]))))
			 {
				
				
				
				 
			 }
			 else
			 if ($do_ins)
			 {
				 
				 echo "<br />\n".$insert."<br />\n"; 
				 $db->setQuery($insert); 
				 $res1 = $db->query(); 
				 $e = $db->getErrorMsg(); if (!empty($e)) die($e); 
				 $last_id = $db->insertid(); 
				 $e = $db->getErrorMsg(); if (!empty($e)) die($e); 
				 
			 }
			 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
			  if ($table == '#__virtuemart_products_'.VMLANG)
		 {
			 
		 }
			
			
			 //echo $check."<br />\n".$update."<br />\n".$insert."<br />\n"; 
		 }
		 
		}
		
	}
	
	
	
	function pairSingle($id, &$groups=null)
	{
		if (empty($groups))
		$groups = $this->buildProducts(); 
		$custom_config = $this->getCustomConfig(); 
		
		$product_ids = array(); 
				
		
		if (isset($groups[$id]))
		{
			$group = $groups[$id];
			unset($groups); 
			$first = reset($group);
			
			foreach ($group as $k=>$pp)
				{
				 $product_id = $pp['virtuemart_product_id']; 
				 if (empty($product_id)) continue; 
				 $product_ids[$product_id] = (int)$product_id; 
				 
				 
				 
				}
				
			
			
			if ($first['parent_type'] == 'new')
			{
				$new_name = $first['best_name']; 
				if (!empty($first['product_sku']))
			    $new_sku = substr($first['product_sku'], 0, -1).'X'; 
			    else $new_sku = ''; 
				$new_data = array('product_name'=>$new_name, 'product_sku'=>$new_sku, 'slug'=>$new_sku); 
				$this->copyProduct($first['virtuemart_product_id'], $new_data); 
			
			if (!empty($new_data['virtuemart_product_id']))
			{
				
				if (!empty($product_ids))
				$this->setParent($product_ids, $new_data['virtuemart_product_id']); 
			    
				$this->setCustoms($custom_config, $new_data['virtuemart_product_id']); 
				$this->checkParentPrice($new_data['virtuemart_product_id'], $product_ids); 
			}
			
			}
			else
			{
				
				
				foreach ($group as $id=>$v)
				{
					
					if ($v['product_name'] == $v['best_name'])
					{
						unset($product_ids[$v['virtuemart_product_id']]); 
						
						
						if (!empty($product_ids))
						{
						$this->setParent($product_ids, $v['virtuemart_product_id']); 
						$this->checkParentPrice($v['virtuemart_product_id'], $product_ids); 
						}
						$this->setCustoms($custom_config, $v['virtuemart_product_id']); 
						
						
						break; 
					}
				}
			}
			
			 $this->checkCats($group); 
			 return true; 
		}
		else
		{
			die('Not found...'); 
		}
		die('pairSingle Model, nothing done...'); 
	}
	
	function countWords($t)
	{
		$t = trim($t); 
		$ta = explode(' ', $t); 
		
		foreach ($ta as $z=>$v)
		{
			$v2 = trim($v);
			if (empty($v2)) unset($ta[$z]); 
		}
		
		return count($ta); 
		
		
	}
	
	function processChars($str)
	{
//		$a = array('*'=>'', '/'=>'', '%'=>''); 
		$str = str_replace('*', '', $str);
		$str = str_replace('%', '', $str);
		
		$str = mb_strtolower($str); 
		/*
		$str = str_replace('shi.', 'shirt', $str); 
		$str = str_replace('hel.', 'helmet', $str); 
		$str = str_replace('hood.', 'hooded', $str); 
		*/
		return $str; 
	}
	
	function getBestName($all_names, $product_name, &$group)
	{
		foreach ($group as $u => $z)
		{
			$s = substr($z['product_sku'], -1); 
			$s = strtolower($s); 
			if ($s === 'x')
			{
				$product_name = $z['product_name']; 
				foreach ($group as $nk => $nkv)
			    {
			     $group[$nk]['best_name'] = $product_name; 
			    }
				
				return $product_name; 
			}
		}
		
		$all_names = trim($all_names); 
		$all_names = $this->processChars($all_names); 
		$product_name_l = $this->processChars($product_name); 
		// product_name is a substring of all products in the group: 
		$ok = false; 
		foreach ($group as $k=>$v)
		{
			$t1 = $this->processChars($v['product_name']); 
			
			$tw = str_replace($product_name_l, '', $t1); 
			if ($tw != $t1) 
			{
				$ok = true; 
			}
			$ok = false; 
			
		}
		if ($ok)
		{
			//die('product_name'.$product_name); 
			foreach ($group as $nk => $nkv)
			{
			 $group[$nk]['best_name'] = $product_name; 
			}
			return $product_name; 
		}
		$ta = explode(' ', $product_name); 
		$new_name = array(); 
		foreach ($ta as $k=>$v)
		{
			$v = trim($v); 
			if (empty($v)) 
			{
				continue; 
			}
			$vx = $this->processChars($v); 
			$ta2 = explode($vx, $all_names); 
			$c1 = count($ta2); 
			$c2 = count($group) ; // so we do not ignore the empty start...  
			
				if (false)
			if ($v == 'MUSQUIN')
			{
				var_dump($ta2); 
			var_dump($all_names); 
			var_dump($group); 
			echo 'c2: '; var_dump($c2); 
			echo 'c1: '; var_dump($c1);
			echo 'c1 >= c2'; 
			$stop = true; 
			}
			if ($c1 >= $c2)
			{
				$new_name[] = $v; 
			}
		}
		
		
		
		$nn = implode(' ', $new_name); 
		
		
		
		if (strlen($nn)<=3) 
		{
			return '';
		}
		foreach ($group as $kk=>$vv)
		{
			$group[$kk]['best_name'] = $nn; 
			$group[$kk]['parent_type'] = 'new'; 
		}
		
		
		
		
		return $nn; 
		
		
		
		
		
	}
	function buildProducts()
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
		
		$categoryModel = VmModel::getModel ('category');
		//$level++;
		$categoryModel->_noLimit = TRUE;
		$cid = JRequest::getVar('virtuemart_category_id', 0); 
		$carr = array(); 
		if (!empty($cid))
		{
			
		$records = $categoryModel->getCategoryTree ($cid);
		
		$carr[] = $cid; 
		foreach ($records as $cat)
		{
			$carr[] = $cat->virtuemart_category_id; 
		}
		}
	
		$s0 = $s1 = $s2 = ''; 
		
		//if (!empty($carr))
		{
			//$s0 = ' (select concat(select cx.virtuemart_category_id from #__virtuemart_product_categories as cx where p.virtuemart_product_id = cx.virtuemart_product_id), ",") as cats'; 
			//$s1 = ', #__virtuemart_product_categories as cx'; 
			//$s2 = ' and ( cx.virtuemart_category_id IN ('.implode(',', $arr).') and p.virtuemart_product_id = cx.virtuemart_product_id )'; 
		}
		
		// for testing only: 
		$limit = ' limit 0,500'; 
		$limit = ''; 
		$db = JFactory::getDBO(); 
		$q = 'select p.product_sku, p.virtuemart_product_id, l.product_name, p.product_parent_id '.$s0.' from #__virtuemart_products as p, `#__virtuemart_products_'.VMLANG.'` as l '.$s1.' where l.virtuemart_product_id = p.virtuemart_product_id and  p.product_sku <> "" '.$s2.' order by p.product_sku desc '.$limit; 
		
		
		$db->setQuery($q); 
		$arr = $db->loadAssocList(); 
		
	    $min = JRequest::getVar('min', 2); 
		$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
		
		
		
		$groups = array(); 
		$sku_s_override = ''; 
		for ($i=0; $i<count($arr); $i++)
		{
			////($arr as $row)
			$row = $arr[$i]; 
			if (empty($row)) continue; 
			
			$sku = $row['product_sku']; //."<br />\n"; 
			$sku_s = substr($sku, 0, -1); 
			
			
			if (!empty($sku_s_override))
			{
				$sku_s_test = substr($sku_s_override, 0, -1); 
				if (stripos($sku_s, $sku_s_test)===0)
				{
					
				}
				else
				{
				   $sku_s_override = ''; 	
				}
			}
			
			
			if (!isset($arr[$i+1])) 
			{
				if (empty($groups[$sku_s])) $groups[$sku_s] = array(); 
				$groups[$sku_s][$sku] = $row; 
				break; 
			}
			$next_row = $arr[$i+1]; 
			
			
			$sku_next = $next_row['product_sku']; 
			$sku_next_s = substr($sku_next, 0, -1); 
			
			
			
			
			$sku_s2 = substr($sku, 0, -2); 
			$sku_next_s2 = substr($sku_next, 0, -2); 
			
			$next_last = substr($sku_next, -1); 
			if (!is_numeric($next_last)) continue; 
			
			
			
			
			
			if ($sku_s === $sku_next_s)
			{
				
				if (!empty($sku_s_override))
				{
					// check name match
					$mgroup = array(); 
					$all_names = ''; 
						
						
						if (!empty($sku_s_override))
						{
							$t1e = reset($groups[$sku_s_override]); 
							$mgroup[] = $t1e; 
							$all_names = $t1e['product_name'].' ';
						}
						
						
						
						$mgroup[] = $next_row;
						$mgroup[] = $row;
						
						$all_names .= $next_row['product_name'].' '.$row['product_name'];
						
						$test = $this->getBestName($all_names, $next_row['product_name'], $mgroup); 
						
						if (!empty($test))
						{
							
							
							
							
							
							if (!empty($sku_s_override))
							{
							 $groups[$sku_s_override][$sku] = $row; 
							 $groups[$sku_s_override][$sku_next] = $next_row; 
							
							 
							 continue; 
							}
						}
						else
						{
							$sku_s_override = ''; 
						}
							
				}
					
				
				if (empty($groups[$sku_s])) $groups[$sku_s] = array(); 
				
				// get rid of duplicities: 
				$groups[$sku_s][$sku] = $row; 
				if (empty($next_row)) continue; 
				$groups[$sku_s][$sku_next] = $next_row; 
				
			}
			else
			{
				if (empty($next_row)) continue; 
				
		   
		  
		   
				
				if ($sku_s2 === $sku_next_s2)
				{
					$last2 = substr($sku_next, -2); 
					$lasts2 = substr($sku, -2); 
					$t1 = (int)$last2 - (int)$lasts2; 
					$t1 = abs($t1); 
					
					if ((is_numeric($last2) && (is_numeric($lasts2)) && ($t1 === 1)))
					{
						$mgroup = array(); 
						$all_names = ''; 
						
						
						if (!empty($sku_s_override))
						{
							$t1e = reset($groups[$sku_s_override]); 
							$mgroup[] = $t1e; 
							$all_names = $t1e['product_name'].' ';
						}
						
						
						
						$mgroup[] = $next_row;
						$mgroup[] = $row;
						$all_names .= $next_row['product_name'].' '.$row['product_name'];
						$test = $this->getBestName($all_names, $next_row['product_name'], $mgroup); 
						if (!empty($test))
						{
							
							
							if (empty($groups[$sku_s])) $groups[$sku_s] = array(); 
							
							
							if (empty($sku_s_override))
							{
							 $groups[$sku_s][$sku] = $row; 
							 $groups[$sku_s][$sku_next] = $next_row; 
							 $sku_s_override = $sku_s; 
							}
							else
							{
								//sku_s_override
								$groups[$sku_s_override][$sku] = $row; 
								$groups[$sku_s_override][$sku_next] = $next_row; 
							}
							
							
						}
						
					}
					
				}
			}
			
		}
		
		$db = JFactory::getDBO(); 
		foreach ($groups as $k7=>$v7)
		{
			if (count($groups[$k7])<=$min) {
				unset($groups[$k7]); 
			    continue; 
			}
			
			
		
			
			$group_cats=array(); 
			foreach ($v7 as $k6=>$v6)
			{
			$product_id = $v6['virtuemart_product_id']; 
	
			$q = 'select virtuemart_category_id from #__virtuemart_product_categories where virtuemart_product_id = '.(int)$product_id; 
			
			$db->setQuery($q); 
			$arr = $db->loadAssoc();
			$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
			if (empty($arr)) $arr = array(); 
			
			if (!empty($arr))
			{
				foreach ($arr as $cat)
				{
					$group_cats[$cat] = $cat; 
				}
			}
			
			}
			
			
			/*
			$nc = array(); 
			foreach ($v as $k9=>$v9)
			{
				$c = explode(',', $v9['cats']); 
				if (!empty($c))
				foreach ($c as $cat)
				{
					if (!empty($cat)) $nc[$cat] = $cat; 
				}
			}
			*/
			foreach ($v7 as $k8=>$v8)
			{
				$groups[$k7][$k8]['cats'] = $group_cats; 
			}
			
			
		}
		
		if (!empty($carr))
		{
			foreach ($groups as $k=>$v)
			{
				$found = false; 
				$first = reset($v); 
				foreach ($carr as $mycat)
				{
					if (in_array($mycat, $first['cats'])) $found = true; 
				}
				if (!$found) unset($groups[$k]); 
			}
		}
		
		
		/*
		if (!empty($arr))
		{
			//$s1 = ', #__virtuemart_product_categories as cx'; 
			//$s2 = ' and ( cx.virtuemart_category_id IN ('.implode(',', $arr).') and p.virtuemart_product_id = cx.virtuemart_product_id )'; 
		}
		*/
		
		
		foreach ($groups as $k2=>$v2)
		{
			 $min = 999; 
			 $ref = null; 
			 $all_names = ''; 
			 foreach ($v2 as $k3=>$v3) {
				 if (!isset($v3['product_name']))
				 {
					 
					 unset($groups[$k2][$k3]); 
					 
				 }
				 $name = $v3['product_name']; 
				 $all_names .= ' '.$name; 
				 $c = $this->countWords($name); 
				 if (empty($ref))
			     $ref = $groups[$k2][$k3]; 
			 
				 if ($c < $min) 
				 {
					 $min = $c; 
					 $ref = $groups[$k2][$k3]; 
				 }
				 $groups[$k2][$k3]['count'] = $c; 
			 }
			 
			 $groups[$k2][$k3]['all_names'] = $all_names; 
			 
			
			 if (!empty($ref))
			 {
				 $ref['is_main'] = true; 
				 
				 $this->getBestName($all_names, $ref['product_name'], $groups[$k2]);
				 
			 }
			 
			 
		}
		

		return $groups; 
	}
	
	function searchtext($search, $searchext, $start=false, $os=false, $xc=false, $cs = false )
	{
	  jimport('joomla.filesystem.file');
	  jimport('joomla.filesystem.folder');
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cache.php'); 
	  
	  
	  if (empty($search)) return ''; 
	  
	
	  if (empty($searchext)) return ''; 
	  
	  if ($searchext == '*') $searchext = '.'; 
	  else $searchext = '.'.$searchext; 
	 
	  
	  $ftest = OPCcache::getValue('opcsearch'.$searchext); 
	  
	  if (empty($ftest))
	  $files = JFolder::files(JPATH_SITE, $searchext, true, true); 
	  else $files = $ftest; 
	  
	  OPCcache::store($files, 'opcsearch'.$searchext); 
	  
	  
	  
	  $resa = array(); 
	  foreach ($files as $f)
	  {
	     // exclude cache: 
		 if ($xc)
	     if (stripos($f, 'cache')!== false) continue; 
		 
		 if ($os)
		 if (filesize($f)>500000) continue; 
		 
		 $data = file_get_contents($f); 
		 
		 if ($start) {
		    if (substr($data, 0, strlen($search)) === $search) {
			   $resa[] = $f; 
			   continue; 
			}
			continue; 
		 }
		 
		 if (!$cs)
		 {
		 if (stripos($data, $search)!==false)
		 $resa[] = $f; 
		 }
		 else
		 {
		  if (strpos($data, $search)!==false)
		  $resa[] = $f; 
		  
		 }
		 
		 
		 
	  }
	  
	  $ret = implode($resa, "<br />\n"); 
	  return $ret; 
	  
	  
	  
	  
	}

	function searchtextObs($search, $searchext)
	{
	  jimport('joomla.filesystem.file');
	  jimport('joomla.filesystem.folder');
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cache.php'); 
	  
	  if (empty($search)) {
	   $search = JRequest::getVar('searchwhat', '', 'post','string',	  JREQUEST_ALLOWRAW);
	  }
	  if (empty($search)) return ''; 
	  
	  if (empty($searchext)) {
	   $searchext = JRequest::getVar('ext'); 
	  }
	  
	  if (empty($searchext)) return ''; 
	  
	  if ($searchext == '*') $searchext = '.'; 
	  else $searchext = '.'.$searchext; 
	  $xc = JRequest::getVar('excludecache', false); 
	   $cs = JRequest::getVar('casesensitive', false); 
	  
	  $ftest = OPCcache::getValue('opcsearch'.$searchext); 
	  
	  if (empty($ftest))
	  $files = JFolder::files(JPATH_SITE, $searchext, true, true); 
	  else $files = $ftest; 
	  
	  OPCcache::store($files, 'opcsearch'.$searchext); 
	  
	  $os = JRequest::getVar('onlysmall', false); 
	  
	  $resa = array(); 
	  foreach ($files as $f)
	  {
	     // exclude cache: 
		 if ($xc)
	     if (stripos($f, 'cache')!== false) continue; 
		 
		 if ($os)
		 if (filesize($f)>500000) continue; 
		 
		 $data = file_get_contents($f); 
		 if (!$cs)
		 {
		 if (stripos($data, $search)!==false)
		 $resa[] = $f; 
		 }
		 else
		 {
		  if (strpos($data, $search)!==false)
		  $resa[] = $f; 
		  
		 }
		 
		 
		 
	  }
	  
	  $ret = implode($resa, "<br />\n"); 
	  return $ret; 
	  
	  
	  
	  
	}
	
	function getCats($published=1)
	{
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		$langs = VmConfig::get('active_languages', array('en-GB')); 
		
		foreach ($langs as $lang)
		{
			$lang = str_replace('-', '_', $lang); 
			$lang = strtolower($lang); 
			$db = JFactory::getDBO(); 
			


			$vendorId = 1;

			$select = ' c.`virtuemart_category_id`, l.`category_description`, l.`category_name`, c.`ordering`, c.`published`, cx.`category_child_id`, cx.`category_parent_id`, c.`shared` ';

			$joinedTables = ' FROM `#__virtuemart_categories_'.$lang.'` l
				JOIN `#__virtuemart_categories` AS c using (`virtuemart_category_id`)
				LEFT JOIN `#__virtuemart_category_categories` AS cx
				ON l.`virtuemart_category_id` = cx.`category_child_id` ';

			$where = array();
			if ($published)
			$where[] = " c.`published` = 1 ";
			//$where[] = ' (c.`virtuemart_vendor_id` = "'. (int)$vendorId. '" OR c.`shared` = "1") ';
			$whereString = '';
			if (count($where) > 0){
				$whereString = ' WHERE '.implode(' AND ', $where) ;
			} else {
				$whereString = 'WHERE 1 ';
			}
			$orderBy = ''; 
			$groupBy = ''; 
			$filter_order_Dir = ''; 
			$joinedTables .= $whereString .$groupBy .$orderBy .$filter_order_Dir ;
			$q = 'SELECT '.$select.$joinedTables;
			$db->setQuery($q);
			$res = $db->loadAssocList(); 
			
			
			
			if (empty($res)) { 
			 continue; 
			}
			$mycats = $this->sortArray($res); 
			
			$cats[$lang] =  $mycats; 
			

		}
		$this->checkOdering($cats); 
		
		return $cats;
		

	}
	
	function getCatsById() {
		$this->cats =  $this->getCats(1); ; 
		$ret = array(); 
		if (empty($this->cats)) $this->cats = array(); 
foreach ($this->cats as $lang=>$arr)
foreach ($arr as $k=>$cat)
{
	
	if (!isset($cat['virtuemart_category_id'])) {
	  continue; 
	}
		$id = (int)$cat['virtuemart_category_id']; 
			$ret[$id] = $cat['category_name'];
	}
	
	return $ret; 
	
	}


	
	
	static $cats; 
	
	function fix_img_filenames() {
	  $db = JFactory::getDBO(); 
	  $q = 'select virtuemart_media_id, file_url, file_url_thumb from #__virtuemart_medias where 1'; 
	  $db->setQuery($q); 
	  $res = $db->loadAssocList(); 
	  $ids = array(); 
	  $all = $allT = array(); 
	  $dirs = array(); 
	  if (!empty($res)) {
	    foreach ($res as $k=>$v) {
		  if (!empty($v['file_url']))
		  {
			  $fp = '';
		   $dir = $this->getDirPath($v['file_url'], $fp); 
		   
		   if (!$dir) continue; 
		   $dirs[$dir] = $dir; 
		   
		   if (function_exists('mb_strtolower')) {
			   $k = mb_strtolower($fp); 
			   
		   }
		   else
		   {
		    $k = strtolower($fp); 
		   }
		   
		   $all[$k] =  $fp; 
		   $ids[$k] = (int)$v['virtuemart_media_id']; 
		  }
		  
		  if (!empty($v['file_url_thumb']))
		  {
		   $fp = ''; 
		   $dir = $this->getDirPath($v['file_url_thumb'], $fp); 
		   if (!empty($dir)) {
		   $dirs[$dir] = $dir; 
		   
		   if (function_exists('mb_strtolower')) {
			   $k = mb_strtolower($fp); 
		   }
		   else
		   {
		    $k = strtolower($fp); 
		   }
		   
		   $allT[$k] =  $fp; 
		   $ids[$k] = (int)$v['virtuemart_media_id']; 
		   }
		  }
	  
		}
	  }
	  $missing  = ''; 
	  $alf = array(); 
	  foreach ($dirs as $k) {
	     $files = scandir($k); 
		 foreach ($files as $fx) {
		   if (($fx === '.') || ($fx === '..')) continue; 
		   if (function_exists('mb_strtolower')) {
		    $kL = mb_strtolower($k.$fx); 
		   }
		   else
		   {
			   $kL = strtolower($k.$fx); 
		   }
		   $alf[$kL] = $k.$fx; 
		 }
	  }
	  $query = array(); 
	  foreach ($all as $fk => $fr) {
	    
		if (isset($alf[$fk])) {
		  if ($alf[$fk] !== $fr) {
		    $msg = 'files do not match:'."<br />"; 
			$msg .= $alf[$fk]."<br />"; 
			$msg .= $fr."<br />"; 
			JFactory::getApplication()->enqueueMessage($msg, 'notice'); 
			if (isset($ids[$fk])) {
			$rel = $this->toRelImage($alf[$fk]); 
			if (!empty($rel)) {
			  $query[] = "update `#__virtuemart_medias` set `file_url` = '".$db->escape($rel). "' where `virtuemart_media_id` = ".(int)$ids[$fk].' limit 1'; 
			}
			}
		  }
		}
		else
		{
			$missing .= $fr."\r\n"; 
			
		}
		//
	  }
	  
	   foreach ($allT as $fk => $fr) {
	    
		if (isset($alf[$fk])) {
		  if ($alf[$fk] !== $fr) {
		    $msg = 'files do not match:'."<br />"; 
			$msg .= $alf[$fk]."<br />"; 
			$msg .= $fr."<br />"; 
			JFactory::getApplication()->enqueueMessage($msg, 'notice'); 
			if (isset($ids[$fk])) {
			$rel = $this->toRelImage($alf[$fk]); 
			if (!empty($rel)) {
			  $query[] = "update `#__virtuemart_medias` set `file_url_thumb` = '".$db->escape($rel). "' where `virtuemart_media_id` = ".(int)$ids[$fk].' limit 1'; 
			}
			}
		  }
		}
		else
		{
			$missing .= $fr."\r\n"; 
			
		}
		//
	  }
	  
	  foreach ($query as $q) {
	    $db->setQuery($q); 
		$db->query(); 
	  }
	  if (!empty($missing)) {
	    @header("Content-type: text/plain");
	    @header("Content-Disposition: attachment; filename=missing_images.txt");
		@header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		@header("Cache-Control: post-check=0, pre-check=0", false);
		@header("Pragma: no-cache");
		echo $missing; 
		JFactory::getApplication()->close(); 
	  }
	 
	  
	  
	}
	
	function toRelImage($imagep) {
		
	   if (strpos($imagep, JPATH_SITE)===0) {
		   
	      $len = strlen(JPATH_SITE)+1; 
		  $imagep = substr($imagep, $len); 
		  $imagep = str_replace(DIRECTORY_SEPARATOR, '/', $imagep); 
		  return $imagep; 
	   }
	   return false; 
	}
	function getDirPath($relImg, &$imagePath='') {
		// universal image protocol
		if (strpos($relImg, '//')===0) {
		  return false; 
		}
		
		//expect file path: 
		$k = str_replace('/', DIRECTORY_SEPARATOR, $relImg); 
		
		// includes a full path in J root
		$pa = pathinfo($k); 
		if (strpos($relImg, JPATH_SITE)===0) {
			return $pa['dirname'].DIRECTORY_SEPARATOR; 
		}
		
		// specific protocol
		if (strpos($relImg, 'http')===0) {
		  return false; 
		}
		// classic images starting with image/stories/etc/...
		
	    $dir = JPATH_SITE.DIRECTORY_SEPARATOR.$pa['dirname'].DIRECTORY_SEPARATOR;
		// fix double paths: 
		$dir = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $dir); 
		
		
		if (file_exists($dir)) {
		  $imagePath = $dir.$pa['basename']; 
		  return $dir; 
		}
		else
		{
			return false; 
		}
		return false; 

		
	}
	
	function cat_prod_copy( $source_cat,  $dest_cat, $action_cat) {
	   // copy: 
	   $msg = ''; 
	   $db = JFactory::getDBO(); 
	   $c = 0; 
	   //if ($action_cat === 0) 
	   {
		   
	     $q = 'select c1.id, c1.virtuemart_product_id, c1.ordering from #__virtuemart_product_categories as c1 where c1.virtuemart_category_id = '.(int)$source_cat; 

		 $db->setQuery($q); 
		 $prods = $db->loadAssocList(); 
		 if (!empty($prods)) {
			foreach ($prods as $prod) {
		   
		   $ids[$prod['id']] = (int)$prod['id']; 
		   
		   $q = 'insert into #__virtuemart_product_categories (id, virtuemart_product_id, virtuemart_category_id, ordering) values (NULL, '.(int)$prod['virtuemart_product_id'].','.(int)$dest_cat.', '.(int)$prod['ordering'].')'; 
		   $db->setQuery($q); 
		   $c++;
		   //echo $q."<br />"; 
		   try {
		     $db->query(); 
		   }
		   catch (Exception $e) {
		    // do nothing as we alredy got the product there... 
			$msg = $e->getMessage(); 
			$c--;
		   }
			}
		 }
		 
		
	   
	   }
	   if (!empty($ids))
	   if ($action_cat === 1)  {
	     $q = 'delete from #__virtuemart_product_categories where id in ('.implode(',', $ids).')'; 
		 $db->setQuery($q); 
		 $db->query(); 
	   }
	   
	   return 'Updated '.$c.' product categories '.$msg; 
	   
	
	
	}
	
	function movemenu()
	{
	   
	   $cats = $this->getCats(); 
	   $lang = JRequest::getVar('vm_lang', 'en_gb'); 
	   $vmmenu = JRequest::getVar('vm_menu_'.$lang); 
	   $jmenu = JRequest::getVar('selected_menu'); 
	   $tomenu = JRequest::getVar('menu_'.$jmenu); 
	   $tolanguage = JRequest::getVar('tojlanguage', '*'); 
	   $config = array('vm_lang'=>$lang, 'vm_menu_'.$lang=>$vmmenu, 'selected_menu'=>$jmenu, 'menu_'.$jmenu=>$tomenu, 'tojlanguage'=>$tolanguage); 		
	   $session = JFactory::getSession(); 
	   $config = $session->set('opc_utils', $config); 

		
		if (empty($vmmenu))
		$copy = $cats[$lang]; 
		else $copy = $cats[$lang][$vmmenu]['children']; 
		
	    $this->checkMinMax($copy); 
		
		$this->shiftLftRgt($cats[$lang], $copy); 

		
		
		
	    
		// pre-cache: 
		self::$cats = $cats[$lang]; 
		
		//JTable::addIncludePath(JPATH_SITE.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'table'); 
		//JTable::addIncludePath(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'tables'); 
		//$table = JTable::getInstance('Menu', 'MenusTable', array());
		//$data = $table->load(300);
		
		
		
		$vmcid = $this->getVmComponentId(); 
		
		$count = 0; 
		//$this->sortforinsert($copy); 
		//$tomenulevel = $this->getToMenuLevel($tomenu); 
		
		$this->copyTable('menu', 'menu_working'); 
		$menu = $this->getWholeTable('menu'); 


		//$this->removeDeleted($menu); 
		
		
		$this->recalculate($menu, $copy, $jmenu, $tomenu, $vmmenu, $lang, $vmcid); 
		
		
		
		$this->checkDuplicities($menu);

	    
		$q = $this->createQuery($menu, 'menu_working'); 
		

		$this->flushTable('menu_working'); 
		
		$db = JFactory::getDBO(); 
		
		
		if (is_array($q))
		{
		foreach ($q as $nq)
		{
		$db->setQuery($nq); 
		
		$db->query(); 
		}
		}
		
		
		$e = $db->getErrorMsg(); if (!empty($e)) { echo $e; die(); }
		
		$this->backupTable('menu'); 
		
		$this->copyTable('menu_working', 'menu'); 
		
		
		
		return; 
		
	
	}
	static $count; 
	function removeEntries(&$menu, $lft, $rgt)
	{
	  // requires first left to be 0
	  // calculate number of items being removed
	  // not including the currnet one: 
	  $diff = (($rgt-1)-$lft)/2;
	  //including the current one: 
	  
	  if ($diff<0) { return; } //die('ee'); 
	  foreach ($menu as &$item)
	   {
	     if (!isset($item['delete']))
	     if (($item['lft']>=$lft) && ($item['rgt']<=$rgt))
		   {
		     $item['delete'] = 1; 
			 self::$count++; 
			 echo self::$count."<br />\n"; 
		   }
		   
		   if (($item['lft']>$lft))
		   {
		     $item['lft'] -= $lft; //$diff; 
			 $item['rgt'] -= $lft;  // $diff;  
		   }
		   
		   if (($item['rgt']>$rgt)) 
		   $item['rgt'] -= $lft; //$diff; 
		   
	   }
	   
	   
		
	}
	function removeDeleted(&$menu)
	{
	   $this->checkDuplicities($menu, 'before delete'); 
	   $found = false; 
	   // in this round we mark it as deleted
	   foreach ($menu as $key=>&$item)
	   {
	     if (!isset($item['delete']))
	     if ($item['published']<0)
		   {
		     $left = $item['lft']; 
			 $right = $item['rgt']; 
			 $diff = $right-$left; 
			 if ($diff<0) 
			 {
			 
			 //die('ee diff smaller zero'); 
			 }
			 
			 $this->removeEntries($menu, $left, $right); 
			 $found = true; 
		   }
	   }
	   if (!$found) return;
	   

	   // in this round we unset it
	   foreach ($menu as $key2=>$item2)
	    {
		  if (!empty($item2['delete'])) 
		  {
		  //echo $key2; die('delete'); 
		  unset($menu[$key2]); 
		  }
		}
		
	   echo count($menu); 
	 
	   $this->checkDuplicities($menu, 'from delete'); 
	}
	
	function tryAlias(&$menu, &$myitem, &$count)
	{
	   $arr = array();
	   foreach ($menu as $key=>&$item)
	    {
		   //if (!($myitem['id']==$item['id'])) continue; 
		   
		   $str = $item['client_id'].'-'.$item['parent_id'].'-'.$item['alias'].'-'.$item['language']; 
		   if (isset($arr[$str]))
		   {
		     $count++; 
			 $item['alias'] = $item['alias'].'-'.$count; 
		     $this->tryAlias($menu, $item, $count); 
			 $str = $item['client_id'].'-'.$item['parent_id'].'-'.$item['alias'].'-'.$item['language']; 
		     
		   }
		   
		   $arr[$str] = $key; 
		   
		}
		
		
	}
	function checkDuplicities(&$menu, $msg='')
	{
	 echo "<br />\n".$msg."<br />\n"; 
	  // joomla defines duplicity as client_id, parent_id, alias, language
	  $arr = array(); 
	  foreach ($menu as $key=>&$item)
	   {
	     $str = $item['client_id'].'-'.$item['parent_id'].'-'.$item['alias'].'-'.$item['language']; 
	     if (isset($arr[$str]))
		 {
		   
		   $count = 1; 
		   $item['alias'] = $item['alias'].'-'.$count; 
		   $this->tryAlias($menu, $item, $count); 
		   $str = $item['client_id'].'-'.$item['parent_id'].'-'.$item['alias'].'-'.$item['language']; 
		 }

		 $arr[$str] = $key; 
	   }
	  
	  $test = array(); 
	  $lftrgt = array(); 
	  
	  foreach ($menu as $m1)
	  {
	    //$menu[$m1['parent_id']] = $m1['rgt']; 
		//foreach ($menu as $m2)
		{
		// if (empty($m1['parent_id'])) continue; 
		
		 
		 
		 // skip for root
		 if (!empty($m1['parent_id']))
		 {
		  $left = $m1['lft']; 
		  $right = $menu[$m1['parent_id']]['rgt']; 
		  if ($right <= $left)
		   {
		     
			 $msg = "<br />\n".'parent id '.$m1['parent_id']."<br />\n"; 
			 $msg .= ' right for parent: '.$right."<br />\n"; 
			 $msg .= ' left for item: '.$left." right for item ".$m1['rgt']." <br />\n"; 
			 $msg .= ' for item id '.$m1['id']."<br />\n"; 
			 $msg .= ' error consistency right smaller left';
			 echo 'item:'; 
			 var_dump($m1);
			 echo 'parent:'; 
			 var_dump($menu[$m1['parent_id']]); 
			 
		     die('error consistency right smaller left'.$msg); 
		   }
		   }
		if (!isset($lftrgt[$m1['lft']]))
		{
		 $lftrgt[$m1['lft']] = $m1['id']; 
		 
		 //if ($m1['lft']===0) die('ok'); 
		}
		else
		{
		  echo 'id '.$m1['id'].' shares the same left with '.$lftrgt[$m1['lft']]."<br />\n"; 

		  die('shares...'); 
		}
		if (!isset($lftrgt[$m1['rgt']]))
		$lftrgt[$m1['rgt']] = $m1['id'];
		else
		{
		echo 'id '.$m1['id'].' shares the same right with '.$lftrgt[$m1['rgt']]."<br />\n"; 
		echo ' count '.count($menu); 

		die('shares the same right with.'); 
		}
		
		}
	  
	  }
	  //var_dump($menu[1]); 
	  // -1 because we start from 0
	  $c = (count($menu)*2)-1; 
	  for ($i=0; $i<$c; $i++)
	   {
	      if (!isset($lftrgt[$i]))
		   {
		     echo ' 2: missing value for left or right on position '.$i."<br />\n"; 
			 echo 'count: '.count($menu); 
			 echo 'before: '; 
			 var_dump($menu[$lftrgt[$i-1]]);
			 echo 'next: '; 
			 if (!isset($menu[$lftrgt[$i+1]])) var_dump($menu[1]); 
			 var_dump($menu[$lftrgt[$i+1]]);
			 var_dump($menu[$lftrgt[$i+2]]);
			 //var_dump($menu[685]); 
			 die('2: missing'); 
			 
			 
		   }
	   }
	   
	}
	
	function shiftLftRgt(&$orig, &$copy)
	{
	  
	  $ca = count($orig); 
	  $cc = count($copy); 
	  $diff = $ca - $cc - 1; 
	  
	  
	  
	  
	  foreach ($copy as $key=>$item)
	   {
	     
	     if (!isset($item['virtuemart_category_id'])) continue; 
		 
	     $copy[$key]['lft'] = $item['lft'] - $diff; 
		 $copy[$key]['rgt'] = $item['rgt'] - $diff; 
		 if ((!isset($smallest_level)) || (((int)$item['level']<=$smallest_level)))
		 {
		 
		 $smallest_level = $item['level']; 
		 }
	   }
	   
	   // if not zero, we need to recalculate level as well: 
	   if (!empty($smallest_level))
	   foreach ($copy as $key=>$item)
	   {
	     $copy[$key]['level'] = (int)$copy[$key]['level'] - $smallest_level; 
	   }
	   
	   
	   
	   
	   
	   
	   
	   
	}
	function backupTable($table)
	{
	  
	  $this->copyTable($table, $table.'_backup'); 
	  return; 
	   if ($this->tableExists($table.'_backup'))
	   {
	     //$this->flushTable($table.'_backup'); 
		 $this->copyTable($table, $table.'_backup'); 
	   }
	  
	}
	function createQuery(&$menu, $table)
	{
	  
	  $qa = array(); 
	  
	  $keys = $this->toKeys($menu);
	  $q = 'insert into `#__'.$table.'` ('.$keys.') values '."\n";  	
	  
	  $qi = ''; 
	  
	  foreach ($menu as &$val)
	  {
	  
	  $qai = $q; 
	  
	  $vals = $this->toVal($val); 
	  if (!empty($qi)) $qi .= ', '; 
	  $qi .= '('.$vals.')'."\n"; 
	  
	  $qai .= $qi; 
	  $qa[] = $qai; 
	  }
	  $q .= $qi; 
	  
	  return $qa; 
	
	}
	
	function toVal(&$val)
	{
	  $db = JFactory::getDBO(); 
	  
	  $q = ''; 
	  $nm = array('id', 'published', 'parent_id', 'level', 'component_id', 'ordering', 'checked_out', 'browserNav', 'access', 'template_style_id', 'lft', 'rgt', 'home', 'client_id'); 
	  foreach ($val as $key=>$value)
	    {
		  if (!empty($q))
		  $q .= ", ";
		  if (in_array($key, $nm)) $q .= $value; 
		  else
		  $q .= "'".$db->escape($value)."'"; 
		  
		}
		return $q;
	
	}
	function toKeys(&$menu)
	{
	  $first = reset($menu); 
	  $q = ''; 
	  foreach ($first as $key=>$val)
	    {
		  if (!empty($q))
		  $q .= ', '.$key; 
		  else
		  $q .= $key; 
		}
		return $q; 
	}
	function insertTo($menu, $lft, $count)
	{
	  
	}
	//$this->recalculate($menu, $copy, $jmenu, $tomenu, $vmmenu); 
	function recalculate(&$menu, &$vmmenu, $jmenu, $tomenu, $vmmenu2, $lang, $vmcid)
	{
	 	

	   $found = false; 
	   $largest_left = 0;
	   $largest_right_for_largest_left = 0; 
       $largest_left_to = 0;
 	   
	   $startlevel = 1; 
	   $largest_id = 0; 
	   
	   foreach ($menu as $i)
	     {
		   // will get autoincrement from largest ID
		   if ($i['id']>$largest_id)
		   $largest_id = $i['id']; 
		   
		   if ($i['menutype'] == $jmenu)
		    {
			  $found_menu = true; 
			  if ($i['id'] == $tomenu)
			  {
			  $found = true; 
			  $startlevel = $i['level']+1; 
			  $largest_left_to = $i['lft'];
			  $found_right_to = $i['rgt']; 
			  $found_left_to = $i['lft']; 
			  }
			  if (!$found)
			  if (($i['lft']>=$largest_left_to) && ($startlevel<=$i['level']))
			  {
			  $largest_left_to = $i['lft'];
			  $right_to = $i['rgt']; 
			   
			  }
			  
			}
			if ($i['lft']>=$largest_left)
			 {
			 
			   $largest_left = $i['lft'];
			   if (!$found)
			   $right_to = $i['rgt'];
			   $largest_right_for_largest_left = $i['rgt']; 
			   $lid = $i['id']; 
			   
			   
			 }
		 }
		
	    $diff = count($vmmenu); 
		if (!$found)
		{
		  //$largest_left_to = $largest_right_for_largest_left
		}
		// original 
		//var_dump($largest_left_to); die(); 
		//var_dump($menu[550]); 
		//var_dump($menu[1]); 
		//var_dump($largest_right_for_largest_left); die(); 
		/*
		http://www.evanpetersen.com/item/nested-sets.html
		Deleting a node with children

		You will remove the node and promote all immediate children to be direct 
		descendants of the parent node of the node you are removing

		Decrement all left and right values by 1 if left value is greater than node 
		to delete’s left value and right value is less than node to delete’s right
		Decrement all left values by 2 if left value is greater than node to delete’s right value.
		Decrement all right values by 2 if right value is greater than node to delete’s right value.
		Remove node
		*/
		
		//var_dump($diff); die(); 
		//var_dump($menu[579]);
		//var_dump($menu[$tomenu]); die();
		
		// $largest_left_to = $i['lft'];
		// $found_right_to = $i['rgt']; 
		if (isset($found_right_to) && (isset($largest_left_to)))
		{
		  //check if we already have some items in the menu to which we are inserting
		  $count = ((($found_right_to-1)-$largest_left_to)/2); 
		}
		echo 'menu1 rgt: '; var_dump($menu[1]['rgt']); 
		echo 'count: '.$diff; 
		echo 'largest left: '; var_dump($largest_left); 
		echo 'largest right for largest left: '; var_dump($largest_right_for_largest_left); //die(); 
		$sb = ($diff*2)+$menu[1]['rgt']; 
		echo 'largest right should be: '.$sb."<br />\n";
		foreach ($menu as &$m)
		{
		
		//var_dump($menu[1]); die(); 
		  if (false)
		  if ($m['rgt']>$largest_right_for_largest_left)
		  {
		  
		  // from 337 has to be 355, count 9, diff 18
		  // from 337 has to be 339, count 1, diff 2
		  // from 337, has to be 341,count 2, diff 4
		  // from 337, has to be 343, count 3, diff 6
		  // from 337, has to be 345, count 4, diff 8
		  // from 337, has to be 347, count 5, diff 10
		  //$diff * 2
		  $df = ($diff*2);
		  //var_dump($diff); 
		  //var_dump($m); 
		  // +2 because we are addding one to left and one to right, later
		  $up = $m['rgt'] + $df; 
		  //echo $m['id'].' is larger rgt: '.$m['rgt'].' updating to: '.$up."<br />\n"; 
		  $m['rgt'] = $up; 
		  
		  //die('hhh'); 
		  }
		  
		  // only if found: $largest_left_to
		  if (!empty($largest_left_to))
		   {
		   
		     if ($m['lft']>$largest_left_to)
			  {
			    $m['lft']+=$diff; 
			  }
			  //if ($m['rgt']>=$right_to)
			  //if (isset($right_to))
			  
			  if ($m['rgt']>=$largest_left_to)
			  {
			  
			    //$largest_left_to = $i['lft'];
				// $right_to = $i['rgt']; 
				//$count = ((($right_to-1)-$largest_left_to)/2); 
				$rgt = (($diff * 2)+$largest_left_to+1); 
				$m['rgt']+=$diff*2;
				/*
				echo 'right:'; 
				echo $found_right_to; 
				var_dump($rgt); 
				echo 'rgt to:'; 
			    var_dump($right_to); 
				echo 'curr:'; 
			    var_dump($m['rgt']); 
			     
				echo 'after: ';
				var_dump($m['rgt']); 
				echo 'largest left to: '; 
				var_dump($largest_left_to); 
				*/
			  }
		   }
		   else
		   {
		     // if not found: 
			 //it's the latest largest left
			 if (($m['lft']>$largest_left) && ($m['rgt']>=$largest_left))
			 {
			   $m['rgt']+=$diff*2;
			   //$largest_left
			 }
			 else
			 if ($m['rgt']>$largest_right_for_largest_left)
			 {
			   $m['rgt']+=$diff*2;
			 }
		   }
		}
		
	//var_dump($menu[1]); die(); 
	   if (!empty($found_menu))
	   {
	     $largest_left = $largest_left_to; 
		 
	   }
	   
	     {
		 
		 /*
		 foreach ($vmmenu as $ii)
		 {
		   if (isset($ii['lft']))
		   echo 'id: '.$ii['virtuemart_category_id'].' '.$ii['lft'].' '.$ii['rgt']."<br />\n"; 
		 }
		 */
		  $this->checkMinMax($vmmenu); 
		    foreach ($vmmenu as &$item)
			 {
			 
			   if (!isset($item['virtuemart_category_id'])) continue; 
			   
			  //var_dump($largest_left); die(); 
				// tu je problem: 
				$i = var_export($item, true); 
			   $item['lft'] += $largest_left; 
			   $item['rgt'] += $largest_left; 
			   $item['level'] += $startlevel; 
			   
			   if (($item['lft']>$sb) || ($item['rgt']>$sb))
			    {
				  echo ' before: '.$i.' after: '; 
				  var_dump($item); 
				  die('error - lft or rgt values are incorrect for vm menu'); 
				}
			   if ($item['level']>10)
			   {
			     
			   }
			   
			   $item['id_indexed'] += $largest_id+1; 
			   $arr = $this->converToMenu($vmmenu, $lang, $item, $jmenu, $tomenu, $largest_id, $vmcid, $found);
			   
			   $menu[$item['id_indexed']] = $arr; 
			   
			  
			  
			 }
			 /*
			 foreach ($vmmenu as $ii22)
		 {
		   if (isset($ii22['lft']))
		   echo 'id: '.$ii22['virtuemart_category_id'].' '.$ii22['lft'].' '.$ii22['rgt']."<br />\n"; 
		 }
		 */
			 
			 
			//var_dump($menu[1581]); die(); 
		 }
		 
		 
		 return true; 
		 
		 
		 
	}
	
	function checkMinMax($arr, $parent_id='parent_id_indexed', $id='id_indexed')
	{
	 $lftrgt = array(); 
	// var_dump($arr); die(); 
	  $max_left = $max_right = 0; 
	 $min_left = null;
	   foreach ($arr as $m1)
	  {
	   if (empty($m1['virtuemart_category_id'])) continue;
	   
	   if ($m1['lft']>=$max_left) $max_left = $m1['lft']; 
	   if (!isset($min_left)) $min_left = $m1['lft']; 
	   if ($m1['lft']<=$min_left) $min_left = $m1['lft']; 
	   if ($m1['rgt']>=$max_right) $max_right = $m1['rgt']; 
	   
	    //$menu[$m1['parent_id']] = $m1['rgt']; 
		//foreach ($menu as $m2)
		/*
		{
		 if (empty($m1['parent_id'])) continue; 
		 $left = $m1['lft']; 
		 $right = $arr[$m1['parent_id']]['rgt']; 
		 if ($right <= $left)
		   {
		     
			 $msg = "<br />\n".'parent id '.$m1['parent_id']."<br />\n"; 
			 $msg .= ' right for parent: '.$right."<br />\n"; 
			 $msg .= ' left for item: '.$left." right for item ".$m1['rgt']." <br />\n"; 
			 $msg .= ' for item id '.$m1['id']."<br />\n"; 
			 echo 'item:'; 
			 var_dump($m1);
			 echo 'parent:'; 
			 var_dump($arr[$m1['parent_id']]); 
			 
		     die('error consistency right smaller left'.$msg); 
			 
		   }
		}
		*/
		if (!isset($lftrgt[$m1['lft']]))
		{
		 $lftrgt[$m1['lft']] = $m1[$id]; 
		}
		else
		{
		  echo 'id '.$m1[$id].' shares the same left with '.$lftrgt[$m1['lft']]."<br />\n"; 
		  die(); 
		}
		if (!isset($lftrgt[$m1['rgt']]))
		{
		
		if (empty($m1['rgt'])) { echo 'empty rgt: '; var_dump($m1); die('empty rgt'); }
		$lftrgt[$m1['rgt']] = $m1[$id];
		}
		else
		{
		echo 'id '.$m1[$id].' shares the same right with '.$lftrgt[$m1['rgt']]."<br />\n"; 
		die('id shares..'); 
		}
	}
	
		
		// -1 because we start from 0
	  
	  //$starta = reset($arr); 
	  $start = $min_left;
	  $c = (count($arr)*2)-3+$start; 
	  if ($c != $max_right)
	  {
	  echo 'max_right should be '.$c; 
	  echo 'max_right is: '.$max_right; 
	  }
	  echo 'count is: '.count($arr); 
	  echo 'first_left is: '.$min_left; 
	  
	  for ($i=$start; $i<=$c; $i++)
	   {
	      if (!isset($lftrgt[$i]))
		   {
		     echo ' 1:missing value for left or right on position '.$i."<br />\n";
					echo 'before: '; 
					var_dump($arr[$lftrgt[$i-1]]);
				    echo 'after: '; 
					var_dump($arr[$lftrgt[$i+1]]); 
			 die('1'); 
		   }
	   }
	   
	   if (($max_right-$min_left) > ((count($arr)*2)-$min_left))
		{
		  echo 'count: '.count($arr); 
		  echo ' max_right: '.$max_right."<br />\n"; 
		  echo ' min_left: '.$min_left; 
		  echo ' max_left: '.$max_left; 
		  die('max right is not correct'); 
		}
	   
	}
	
	function &converToMenu(&$cats, $lang, &$vmitem, $jmenu, $tomenu, $largest_id, $vmcid, $found)
	{
	 
			$arr = array(); 
			$key = $vmitem['virtuemart_category_id']; 
			$arr['id'] = (int)$vmitem['id_indexed']; 
			$arr['menutype'] = $jmenu; 
			$arr['title'] = $vmitem['category_name']; 
			$arr['alias'] = $this->getAlias($vmitem);
			$arr['note'] = 'virtuemart_category_id:'.$vmitem['virtuemart_category_id'];
			$arr['path'] = $this->getSefPath(self::$cats, $key, '', $arr['alias']); 
			$arr['link'] = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$key; 
			$arr['type'] = 'component'; 
			$arr['published'] = $vmitem['published']; 
			
			if (empty($vmitem['category_parent_id'])) 
			{
			// if the parent is top category, check if we have another top here: 
			if ($found)
			$arr['parent_id'] = (int)$tomenu; 
			else
			$arr['parent_id'] = 1; //$vmitem['id_indexed']; 
			}
			else
			{
			 // if the parent is outisde our scope, check if we have another top here: 
			 if (!isset($cats[$vmitem['category_parent_id']]['id_indexed']))
			 $arr['parent_id'] = (int)$tomenu; 
			 else
			 $arr['parent_id'] = $cats[$vmitem['category_parent_id']]['id_indexed'];//  $vmitem['parent_id_indexed']; // $this->getParent($vmmenu, $vmitem, $tomenu, $jmenu); 
			}
			
			
			$arr['level'] = $vmitem['level'];
			$arr['component_id'] = $vmcid; 
			
			$arr['ordering'] = '0'; //$vmitem['ordering']; 
			$arr['checked_out'] = '0'; 
			$arr['checked_out_time'] = '0000-00-00 00:00:00'; 
			$arr['browserNav'] = 0; 
			$arr['access'] = 1; 
			$arr['img'] = ''; 
			$arr['template_style_id'] = '0'; 
			$arr['params'] = '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1,"page_title":"","show_page_heading":0,"page_heading":"","pageclass_sfx":"","menu-meta_description":"","menu-meta_keywords":"","robots":"","secure":0}'; 
			$arr['lft'] = $vmitem['lft'];
			$arr['rgt'] = $vmitem['rgt'];
			$arr['home'] = 0; 
			$l = JRequest::getVar('tojlanguage', '*'); 
			$arr['language'] = $l; 
			$arr['client_id'] = 0; 
			
			if ($arr['level']>10)
			  {
			    var_dump($arr); die('level 10'); 
			  }
		return $arr; 
	}
	
	function &getWholeTable($table)
	{
	   $db = JFactory::getDBO(); 
	   $q = 'select * from `#__'.$table.'` where 1 limit 99999'; 
	   $db->setQuery($q); 
	   $arr = $db->loadAssocList(); 
	   $newa = array(); 
	   foreach ($arr as $key=>$val)
	    {
		  $newa[$val['id']] = $val; 
		}

		return $newa; 
	   
	   

	}
	
	function getToMenuLevel($Itemid)
	{
	  $db=JFactory::getDBO(); 
	  $q = 'select level from #__menu where id = "'.$Itemid.'" limit 0,1'; 
	  $db->setQuery($q); 
	  return $db->loadResult();
	}
	function sortforinsert(&$items)
	{
	  $copy = array(); 
	  // sort by level
	  $levels = array(); 
	  foreach ($items as $key=>$item)
	   {
	     $items[$key]['alias'] = $this->getAlias($items[$key]); 
	     $path = $this->getSefPath($items, $key, $vmmenu='', $items[$key]['alias']); 
		 $arr = explode('/', $path); 
		 //without root: 
		 $level = count($arr)-1; 
		 $items[$key]['level'] = $level; 
		 $levels[$level][$key] = $key; 
	   }
	   ksort($levels); 
	   foreach ($levels as $val)
	   foreach ($val as $cat_id=>$id)
	    $copy[$cat_id] = $items[$cat_id]; 
	   
	   $items = $copy; 
	}
	
	function checkOdering(&$items, $debug=false)
	 {
	    $parents = array();
		
		// group by parents and ordering: 
		if (empty($items)) return;
	    foreach ($items as &$item)
		 {
		   if (!isset($item['category_parent_id'])) continue; 
		   $co =& $item['ordering']; 
		   $cid =& $item['virtuemart_category_id']; 
		   //if (!isset($parents[$item['category_parent_id']])) $parents[$item['category_parent_id']] = array(); 
		   $parents[$item['category_parent_id']][$co][$cid] =& $item; 
		 }
		 
		
		 
		 
		 
	    //
		//foreach ($parent_i as $ordering=>$myitems)
		 foreach ($parents as $parent_id=>$parent_i)
		 {
		  
		   //$c = count($myitems); 
		   $i = 0; 
		   //if ($c != 1)
		   
		   {
		   $newa = $parents[$parent_id]; 
		   ksort($newa); 
		   foreach ($newa as $o2=>$item2)
		    {
			  
			   {
				  foreach ($item2 as $kk=>$val)
				  {
				    $i++;
			        $items[$kk]['ordering'] = $i; 
					
					//echo 'duplicity found for parent '.$parent_id.' and category '.$kk.'<br />'."\n"; 
				  }
			   }
			   
			}
			//break 1; 
			}
			if (false)
		   if (count($myitems)>1)
		    {
			  // reorder here: 
			  $c = 1; 
			  foreach ($parents[$parent_id][$ordering] as $cat_id=>$item)
			    {
				  // incremental:
				  $items[$cat_id]['ordering'] = $c; 
				  $c++; 
				}
			}
		 }
		 if ($debug)
		foreach ($parents as $p=>$k)
		  {
		    echo 'parent: '.$p.' has orderings of '; 
			{
			foreach ($k as $order=>$mitems)
			 foreach ($mitems as $cat_id=>$val)
			  echo $items[$cat_id]['ordering'].'(k:'.$order.'), '; 
			}
			echo "<br />\n"; 
		  }
		 return; 
	    $order = -1; 
		$ordering = array(); 
	    foreach ($items as $key=>$item)
		  {
		    $ordering[$item['category_parent_id']][$item['ordering']][$key] = $key;
		  }
		foreach ($ordering as $j=>$f)
		  foreach ($f as $order_x => $cat_id)
		  {
		     $num = count($ordering[$j][$order_x]);
		     if ($num>1)
			  {
			   $shift = 1; 
			   $shiftwhat = array(); 
			   for ($i=1; $i<=$num; $i++)
			    {
				  if (!empty($ordering[$j][$order_x+$i])) 
				   $shiftwhat[$order_x+$i] = $cat_id; 
				}
			   // we have a problem
			    $this->shiftOrdering($ordering, $items, $shiftwhat); 
			  }
		  }
	 }
	function shiftOrdering(&$arr)
	 {
	   foreach ($shiftwhat as $order_key=>$cat_id)
	     {
		   
		 }
	 }
	function getId($id, $menutype)
	{
	 if (!empty(self::$cats[$id]['Itemid'])) {
	 
	 return self::$cats[$id]['Itemid']; 
	 }
	 $db=JFactory::getDBO(); 
	 $q = "select id from #__menu where note LIKE 'virtuemart_category_id:".$id."' and menutype LIKE '".$menutype."' limit 0,1"; 
	 $db->setQuery($q); 
	 $r = $db->loadResult();
	 
	 if (!empty($r))
	 self::$cats[$id]['Itemid'] = $r; 
	 
	 return $r; 
	}
	
	function getParent($copy, $vmitem, $tomenu, $menutype)
	{
	 $parent = $vmitem['category_parent_id']; 
	 if (empty($copy[$parent])) 
	 {
	 if (!empty($tomenu)) return $tomenu; 
	 else
	 return 1; 
	 }
	 $id = $copy[$parent]['virtuemart_category_id']; 
	 
	 $r = $this->getId($id, $menutype); 
	 if (!empty($r)) return $r; 
	 // default for VM: 
	 if (!empty($tomenu)) return $tomenu; 
	 // else return top menu: 
	 return 1; 
	}
	
	function getSefPath(&$cats, $key, $vmmenu='', $alias)
	{
	  if (isset($cats[$key]['sefpath'])) return $cats[$key]['sefpath']; 
	  $arr = array(); 
	  $arr[] = $alias; 
	  $current = $cats[$key]; 
	  // max 10 recursions allowed, no more 
	  for ($i=0; $i<=10; $i++)
	   {
	     $parent = $current['category_parent_id']; 
		 if (!empty($parent))
		  {
		     $current = $cats[$parent]; 
			 $arr[] = $this->getAlias($current); 
			  
		  }
		  else
		  break; 
	   }
	  $path = ''; 
	 // will use full path to the category: 
	 foreach ($arr as $val)
	   {
	      //if (!empty($path))
		  $path = $val.'/'.$path; 
		  
	      //$path = $path.'/'.$val; 
		  //
	   }
	   //$path = 'root/'.$path; 
	   $cats[$key]['sefpath'] = $path; 
	   return $path; 
	}
	
	function getAlias($item, $unique=false)
	{
	// replace: 
	$vals = 'Á|A,Â|A,Å|A,Ă|A,Ä|A,À|A,Ć|C,Ç|C,Č|C,Ď|D,É|E,È|E,Ë|E,Ě|E,Ì|I,Í|I,Î|I,Ï|I,Ĺ|L,Ľ|L,Ń|N,Ň|N,Ñ|N,Ò|O,Ó|O,Ô|O,Õ|O,Ö|O,Ŕ|R,Ř|R,Š|S,Ś|O,Ť|T,Ů|U,Ú|U,Ű|U,Ü|U,Ý|Y,Ž|Z,Ź|Z,á|a,â|a,å|a,ä|a,à|a,ć|c,ç|c,č|c,ď|d,đ|d,é|e,ę|e,ë|e,ě|e,è|e,ì|i,í|i,î|i,ï|i,ĺ|l,ń|n,ň|n,ñ|n,ò|o,ó|o,ô|o,ő|o,ö|o,š|s,ś|s,ř|r,ŕ|r,ť|t,ů|u,ú|u,ű|u,ü|u,ý|y,ž|z,ź|z,˙|-,ß|ss,Ą|A,µ|u,Ą|A,µ|u,ą|a,Ą|A,ę|e,Ę|E,ś|s,Ś|S,ż|z,Ż|Z,ź|z,Ź|Z,ć|c,Ć|C,ł|l,Ł|L,ó|o,Ó|O,ń|n,Ń|N,А|A,а|a,Б|B,б|b,В|V,в|v,Г|G,г|g,Д|D,д|d,Е|E,е|e,Ж|Zh,ж|zh,З|Z,з|z,И|I,и|i,Й|Y,й|y,К|K,к|k,Л|L,л|l,М|M,м|m,Н|N,н|n,О|O,о|o,П|P,п|p,Р|R,р|r,С|S,с|s,Т|T,т|t,У|U,у|u,Ф|F,ф|f,Х|Ch,х|ch,Ц|Ts,ц|ts,Ч|Ch,ч|ch,Ш|Sh,ш|sh,Щ|Sch,щ|sch,Ы|I,ы|i,Э|E,э|e,Ю|U,ю|iu,Я|Ya,я|ya,Ъ| ,ъ| ,Ь| ,ь| ,ľ|l, |_,"|in,&|and,\'|_'; 
	$vala = explode(',', $vals); 
	$name = $item['category_name'];
	foreach ($vala as $s)
	{
	  $vv = explode('|', $s);
	  $search = $vv[0]; 
	  $rep = $vv[1]; 
	  $name = str_replace($search, $rep,  $name);
	  $name = str_replace(',', '_', $name); 
	  //$name = preg_replace("/[^A-Za-z0-9 ]/", '_', $name);
	}
	
	if ($unique)
	 {
	    //$q = "select * from #__menu where alias LIKE '".$name."' and menutype LIKE '".$menutype."' limit 0,1"; 
	 }
	return mb_substr($name, 0, 255); 
	
	
	}
	
	function getLevel($copy, $item)
	{
	  return $item['level']; 
	}
	function getVmComponentId()
	{
	 $db=JFactory::getDBO(); 
	 $q = "select extension_id from #__extensions where element LIKE 'com_virtuemart' and type LIKE 'component' limit 0,1"; 
	 $db->setQuery($q); 
	 $r = $db->loadResult();
	 if (!empty($r)) return $r; 
	 // default for VM: 
	 return 10000; 
	  
	}
	
	function sortArray($res, $index='virtuemart_category_id', $skey='category_parent_id', $top=0)
	{
			$mycats = array(); 
			// future ID:
			$int = 0; 
			foreach ($res as $c)
			{
			  $int++; 
			  $ind = $c[$index]; 
			  if (!isset($mycats[$ind])) $mycats[$ind] = array(); 
			  $this->merge($mycats[$ind], $c); 
			 
			  if (!isset($mycats[$ind]['children'])) 
			  {
			  $mycats[$ind]['lft'] = null; 
			  $mycats[$ind]['rgt'] = null; 
			  $mycats[$ind]['level'] = 0; 
			  $mycats[$ind]['id_indexed'] = $int; 
			  $mycats[$ind]['parent_id_indexed'] = 0; 
			  $mycats[$ind]['children'] = array(); 
			  
			  }
			  
			  if (!isset($mycats[$ind]['category_parent_id']))
			  $mycats[$ind]['category_parent_id'] = 0; 
			  $mycats[$mycats[$ind]['category_parent_id']][$ind] = $ind;
			  // is empty, or set (1), or equals to itself
			  if (!empty($c[$skey]) && ($c[$skey]!=$top) && ($c[$skey] != $ind))
			  {
			  // better: $mycats[$c['category_parent_id']]['children'][$ind] =& $c; 
			  // $mycats[$c[$skey]]['children'][$ind] =& $c; 
			  // reference back to me: 
			  if (!isset($mycats[$c[$skey]]['id_indexed'])) $mycats[$c[$skey]]['id_indexed'] = 0; 
			  $mycats[$ind]['parent_id_indexed'] =& $mycats[$c[$skey]]['id_indexed']; 
			  $mycats[$c[$skey]]['children'][$ind] =& $mycats[$ind];
			  
			  $mycats[$c[$skey]][$ind] = $ind;
			  
			  }
			  
			}
			
			
			$r = 1; 
			$l = 0; 
			$count = 0; 
			$level = 0; 
			$largest_id = 0; 
			$this->getLftRgt($mycats, true, $l, $r, $count, $level, $largest_id); 
			
			return $mycats; 
	}
	
	function logTable($table)
	{

	$db = JFactory::getDBO(); 
	{
		$db->setQuery('SELECT * FROM `#__'.$table.'`');
		$result = $db->loadAssocList(); 
		$first = reset($result); 
		$num_fields = count($first); 
		
		
		$return.= 'DROP TABLE '.$table.';';
		$q = 'SHOW CREATE TABLE `#__'.$table.'`';
		$db->setQuery($q); 
		$row2 = $db->loadAssoc(); 
		
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			foreach ($result as $row)
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	//save file
	$handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);

	}
	
	function getLftRgt(&$mycats, $top=true, &$l, &$r, &$count, &$level, &$largest_id)
	{
	  //$mycats[0]['lft'] = 0; 
	  //$mycats[0]['rgt'] = 1; 
	  foreach ($mycats as $cat_id=>$cats)
			{
			   if (empty($cat_id)) continue; 
			  
			   // is a top category
			   if ($top)
			   {
			   
			   
			    
				
				
			   if (empty($mycats[$cat_id]['category_parent_id']))
			    {
				
				$count++; 

				
				if ($count==711)
				 {
				   
				 }
				  $mycats[$cat_id]['lft'] = $count; 
				  $mycats[$cat_id]['level'] = $level; 
				  $count_saved = $count; 
				  if (!empty($mycats[$cat_id]['children']))
				    {
					   $level++; 
					   
					   $this->getLftRgt($mycats[$cat_id]['children'], false, $count, $r, $count, $level, $largest_id); 
					   
					   $level--; 
					}
					
					{
					  $count++; 
					  $mycats[$cat_id]['rgt'] = $count; 
					}
				  $mycats[$cat_id]['count'] = $count - $count_saved; 
				}
			   }
			   else
			   {
			    $count++; 
				 

			      //if (!is_null($mycats[$cat_id]['lft'])) return; 
				  if (isset($mycats[$cat_id]['lft'])) return; 
			      
				  $mycats[$cat_id]['lft'] = $count; 
				  $mycats[$cat_id]['level'] = $level; 
				  $count_saved = $count;   
				  if (!empty($mycats[$cat_id]['children']))
				    {
					   $level++; 
				
					   $this->getLftRgt($mycats[$cat_id]['children'], false, $count, $r, $count, $level, $largest_id); 
					   
					   $level--; 
					}
					
					{
					  $count++; 
					  $mycats[$cat_id]['rgt'] = $count; 
					}
				  $mycats[$cat_id]['count'] = $count - $count_saved; 
				  
			   }
			   
			   $largest_id = $cat_id; 
				
			}
			if ($top)
			{
			 
			 
			}
	}
	
	function getRght(&$mycats, $top=true, &$l, &$r, &$count, &$level, $largest_id)
	{
	   foreach ($mycats as $cat_id=>$cats)
			{
			   if (empty($cat_id)) continue; 
			  
			   // is a top category
			   if ($top)
			   {
			    $count++; 
			   if (empty($mycats[$cat_id]['category_parent_id']))
			    {
				  $mycats[$cat_id]['rgt'] = $count+$mycats[$cat_id]['count']+1;
				  
				 
				  if (!empty($mycats[$cat_id]['children']))
				    {
					   $level++; 
					   
					   $this->getLftRgt($mycats[$cat_id]['children'], false, $count, $r, $count, $level, $largest_id); 
					   
					   $level--; 
					}
				 
				}
			   }
			   else
			   {
			    $count++; 
			      if (!is_null($mycats[$cat_id]['lft'])) return; 
			      
				  
				  $mycats[$cat_id]['rgt'] = $count+$mycats[$cat_id]['count']+1;
				  
				 
				  if (!empty($mycats[$cat_id]['children']))
				    {
					   $level++; 
				
					   $this->getLftRgt($mycats[$cat_id]['children'], false, $count, $r, $count, $level, $largest_id); 
					   
					   $level--; 
					}
				  
				  
			   }
			   
			   $largest_id = $cat_id; 
				
			}
			if ($top)
			{
			 
			 
			}
	
	}
	
	function getMenusSorted()
	{
		$menus = $this->getMenus(); 
		$db = JFactory::getDBO(); 
		$ret = array(); 
		foreach ($menus as $m)
		 {
		   //$q = "select * from #__menu as m left join #__extensions as e on e.extension_id = m.component_id where menutype LIKE '".$db->escape($m['menutype'])."' limit 9999"; 
		   $q = "select * from #__menu  as m, #__extensions as e where e.extension_id = m.component_id  and menutype LIKE '".$db->escape($m['menutype'])."' limit 9999"; 
		   $db->setQuery($q); 
		   $res = $db->loadAssocList(); 
		   if (empty($res)) return array(); 
		   $ret[$m['menutype']] = $this->sortArray($res, 'id', 'parent_id', 1); 
		   
		   //$this->getItemName($ret[$m['menutype']]); 
		 
		 }
		 
		return $ret; 
	}
	function getMenus()
	{
	  $db = JFactory::getDBO(); 
	  $q = 'select * from #__menu_types where 1 limit 999'; 
	  $db->setQuery($q); 
	  return $db->loadAssocList(); 
	}
	function merge(&$arr1, $arr2)
	{
	  if (empty($arr1)) $arr1 = $arr2; 
	  else
	  foreach ($arr2 as $key=>$arr2v)
	  {
	    $arr1[$key] = $arr2v; 
		//if (!empty($c['element'])) $mycats[$c[$skey]]['componentname'] = $c['element']; 
		if ($key=='element') $arr1['componentname'] =& $arr2['element']; 
	  }
	  if (array_key_exists ('type', $arr1))
	   {
	     $this->getItemName($arr1); 
	   }
	}
	
	function printChildren($arr, $value, $title, $prefix='->')
	{
		  foreach ($arr as $line)
	   {
	     if (!isset($line[$value]))
		 {
		   
		 }
	     echo '<option value="'.$line[$value].'">'.$prefix.$line[$title].'</option>'; 
		 if (!empty($line['children'])) 
		  {
		  $prefix = '->'.$prefix; 
		  $this->printChildren($line['children'], $value, $title, $prefix); 
		  }
	   }
	}
	
	/**
	 *  get the menu name, orig from: \administrator\components\com_menus\views\items\view.html.php
	 */
	public function getItemName(&$item)
	{
		$lang 		= JFactory::getLanguage();

		//$this->ordering = array();

		//foreach ($items as $key=>&$item) 
		{
			//$this->ordering[$item['parent_id']][] = $item['id'];
			 $item['item_type'] = $item['title'];
			if (empty($item['type'])) {
			 
			
			  return;
			 }
			// item type text
			switch ($item['type']) {
				case 'url':
					$value = JText::_('COM_MENUS_TYPE_EXTERNAL_URL');
					break;

				case 'alias':
					$value = JText::_('COM_MENUS_TYPE_ALIAS');
					break;

				case 'separator':
					$value = JText::_('COM_MENUS_TYPE_SEPARATOR');
					break;

				case 'component':
				default:
					if (empty($item['type']) || (empty($item['componentname']))) 
					{
					 $value = $item['title']; 
					 break; 
					}
					// load language
						$lang->load($item['componentname'].'.sys', JPATH_ADMINISTRATOR, null, false, false)
					||	$lang->load($item['componentname'].'.sys', JPATH_ADMINISTRATOR.'/components/'.$item['componentname'], null, false, false)
					||	$lang->load($item['componentname'].'.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
					||	$lang->load($item['componentname'].'.sys', JPATH_ADMINISTRATOR.'/components/'.$item['componentname'], $lang->getDefault(), false, false);

					if (!empty($item['componentname'])) {
						$value	= JText::_($item['componentname']);
						$vars	= null;

						parse_str($item['link'], $vars);
						if (isset($vars['view'])) {
							// Attempt to load the view xml file.
							$file = JPATH_SITE.'/components/'.$item['componentname'].'/views/'.$vars['view'].'/metadata.xml';
							if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
								// Look for the first view node off of the root node.
								if ($view = $xml->xpath('view[1]')) {
									if (!empty($view[0]['title'])) {
										$vars['layout'] = isset($vars['layout']) ? $vars['layout'] : 'default';

										// Attempt to load the layout xml file.
										// If Alternative Menu Item, get template folder for layout file
										if (strpos($vars['layout'], ':') > 0)
										{
											// Use template folder for layout file
											$temp = explode(':', $vars['layout']);
											$file = JPATH_SITE.'/templates/'.$temp[0].'/html/'.$item['componentname'].'/'.$vars['view'].'/'.$temp[1].'.xml';
											// Load template language file
											$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE, null, false, false)
											||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], null, false, false)
											||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE, $lang->getDefault(), false, false)
											||	$lang->load('tpl_'.$temp[0].'.sys', JPATH_SITE.'/templates/'.$temp[0], $lang->getDefault(), false, false);

										}
										else
										{
											// Get XML file from component folder for standard layouts
											$file = JPATH_SITE.'/components/'.$item['componentname'].'/views/'.$vars['view'].'/tmpl/'.$vars['layout'].'.xml';
										}
										if (JFile::exists($file) && $xml = simplexml_load_file($file)) {
											// Look for the first view node off of the root node.
											if ($layout = $xml->xpath('layout[1]')) {
												if (!empty($layout[0]['title'])) {
													$value .= ' » ' . JText::_(trim((string) $layout[0]['title']));
												}
											}
											if (!empty($layout[0]->message[0])) {
												$item['item_type_desc'] = JText::_(trim((string) $layout[0]->message[0]));
											}
										}
									}
								}
								unset($xml);
							}
							else {
								// Special case for absent views
								$value .= ' » ' . JText::_($item['componentname'].'_'.$vars['view'].'_VIEW_DEFAULT_TITLE');
							}
						}
					}
					else {
						if (preg_match("/^index.php\?option=([a-zA-Z\-0-9_]*)/", $item['link'], $result)) {
							$value = JText::sprintf('COM_MENUS_TYPE_UNEXISTING', $result[1]);
						}
						else {
							$value = JText::_('COM_MENUS_TYPE_UNKNOWN');
						}
					}
					break;
			}
			if (!empty($value))
			$item['item_type'] = $value;
		}

		

		

		
		
	}

	function flushTable($table)
	{
	  $db = JFactory::getDBO(); 
	  $q = 'delete from `#__'.$table.'` where 1 limit 99999'; 
	  $db->setQuery($q); 
	  $db->query(); 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) { echo $e; die(); }
	}
  function copyTable($from, $to)
  {
  $dbj = JFactory::getDBO();

  $prefix = $dbj->getPrefix();
  
   if (OPCloader::tableExists($to))
   {
      $q = 'drop table `'.$prefix.$to.'`'; 
	  $dbj->setQuery($q); 
	  $dbj->query(); 
	  $e = $dbj->getError(); if (!empty($e)) { echo $e; die(); }
   }

  $Config = new JConfig();
  $db = $Config->db;
  
  $sql = '
 CREATE  TABLE  `'.$db.'`.`'.$prefix.$to.'` (  `id` int( 11  )  NOT  NULL  AUTO_INCREMENT ,
 `menutype` varchar( 24  )  NOT  NULL  COMMENT  \'The type of menu this item belongs to. FK to #__menu_types.menutype\',
 `title` varchar( 255  )  NOT  NULL  COMMENT  \'The display title of the menu item.\',
 `alias` varchar( 255  )  CHARACTER  SET utf8 COLLATE utf8_bin NOT  NULL  COMMENT  \'The SEF alias of the menu item.\',
 `note` varchar( 255  )  NOT  NULL DEFAULT  \'\',
 `path` varchar( 1024  )  NOT  NULL  COMMENT  \'The computed path of the menu item based on the alias field.\',
 `link` varchar( 1024  )  NOT  NULL  COMMENT  \'The actually link the menu item refers to.\',
 `type` varchar( 16  )  NOT  NULL  COMMENT  \'The type of link: Component, URL, Alias, Separator\',
 `published` tinyint( 4  )  NOT  NULL DEFAULT  \'0\' COMMENT  \'The published state of the menu link.\',
 `parent_id` int( 10  )  unsigned NOT  NULL DEFAULT  \'1\' COMMENT  \'The parent menu item in the menu tree.\',
 `level` int( 10  )  unsigned NOT  NULL DEFAULT  \'0\' COMMENT  \'The relative level in the tree.\',
 `component_id` int( 10  )  unsigned NOT  NULL DEFAULT  \'0\' COMMENT  \'FK to #__extensions.id\', '; 
 
 $o = '`ordering` int( 11  )  NOT  NULL DEFAULT  \'0\' COMMENT  \'The relative ordering of the menu item in the tree.\', '; 
  
  if (( version_compare( JVERSION, '3.0', '<' ) == 1))
  {
    $sql .= $o; 
  }
 
 $sql .= '
 `checked_out` int( 10  )  unsigned NOT  NULL DEFAULT  \'0\' COMMENT  \'FK to #__users.id\',
 `checked_out_time` timestamp NOT  NULL DEFAULT  \'0000-00-00 00:00:00\' COMMENT  \'The time the menu item was checked out.\',
 `browserNav` tinyint( 4  )  NOT  NULL DEFAULT  \'0\' COMMENT  \'The click behaviour of the link.\',
 `access` int( 10  )  unsigned NOT  NULL DEFAULT  \'0\' COMMENT  \'The access level required to view the menu item.\',
 `img` varchar( 255  )  NOT  NULL  COMMENT  \'The image of the menu item.\',
 `template_style_id` int( 10  )  unsigned NOT  NULL DEFAULT  \'0\',
 `params` text NOT  NULL  COMMENT  \'JSON encoded data for the menu item.\',
 `lft` int( 11  )  NOT  NULL DEFAULT  \'0\' COMMENT  \'Nested set lft.\',
 `rgt` int( 11  )  NOT  NULL DEFAULT  \'0\' COMMENT  \'Nested set rgt.\',
 `home` tinyint( 3  )  unsigned NOT  NULL DEFAULT  \'0\' COMMENT  \'Indicates if this menu item is the home or default page.\',
 `language` char( 7  )  NOT  NULL DEFAULT  \'\',
 `client_id` tinyint( 4  )  NOT  NULL DEFAULT  \'0\',
 PRIMARY  KEY (  `id`  ) ,
 UNIQUE  KEY  `idx_client_id_parent_id_alias_language` (  `client_id` ,  `parent_id` ,  `alias` ,  `language`  ) ,
 KEY  `idx_componentid` (  `component_id` ,  `menutype` ,  `published` ,  `access`  ) ,
 KEY  `idx_menutype` (  `menutype`  ) ,
 KEY  `idx_left_right` (  `lft` ,  `rgt`  ) ,
 KEY  `idx_alias` (  `alias`  ) ,
 KEY  `idx_path` (  `path` ( 333  )  ) ,
 KEY  `idx_language` (  `language`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = utf8;'; 
  $dbj->setQuery($sql); 
  $dbj->query(); 
  $e = $dbj->getErrorMsg(); 
  if (!empty($e)) { echo $e; die(); }

  $sql = 'SET SQL_MODE=\'NO_AUTO_VALUE_ON_ZERO\'';
  $dbj->setQuery($sql); 
  $dbj->query(); 
  $e = $dbj->getErrorMsg(); 
  if (!empty($e)) { echo $e; die(); }


$sql = 'INSERT INTO `'.$db.'`.`'.$prefix.$to.'` SELECT * FROM `'.$db.'`.`'.$prefix.$from.'`;'; 

  $dbj->setQuery($sql); 
  $dbj->query(); 
  $e = $dbj->getErrorMsg(); 
  if (!empty($e)) { echo $e; die(); }
  
  }  
  

}

class nested {
  static $items; 
  var $item; 
  var $count; 
  function addChild(&$item, $idName='id', $parent_idName='parent_id')
  {
    self::$items[$item[$idName]] =& $this->toItem($item); 
	self::$items[$item[$parent_idName]] =& $this->toItem(self::$items[$item[$parent_idName]]); 
	if (!empty($item[$parent_idName]))
	if (isset(self::$items[$item[$parent_idName]]))
	self::$items[$item[$parent_idName]]->recalculate(); 
  }
  function &toItem(&$item)
  {
    if (!isset($item)) $item = array(); 
    $this->item = &$item; 
	return $this; 
  }
  function recalculate()
  {
    
  }
}
