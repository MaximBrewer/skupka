<?php
if(  !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
 *
 * @package VirtueMart
 * @Author Kohl Patrick
 * @subpackage router
 * @copyright Copyright (C) 2010 Kohl Patrick - Virtuemart Team - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */


class vmrouterHelperSEFforOPC {

	/* language array */
	public $lang = null ;
	public $langTag = null ;
	public $query = array();
	/* Joomla menus ID object from com_virtuemart */
	public $menu = null ;

	/* Joomla active menu( itemId ) object */
	public $activeMenu = null ;
	public $menuVmitems = null;
	public $catsOfCats = array(); 
	public $nostatic = false; 
	/*
	  * $use_id type boolean
	  * Use the Id's of categorie and product or not
	  */
	public $use_id = false ;

	public $seo_translate = false ;
	private $orderings = null ;
	public static $limit = null ;
	/*
	  * $router_disabled type boolean
	  * true  = don't Use the router
	  */
	public $router_disabled = false ;

	/* instance of class */
	private static $_instances = array ();

	private static $_catRoute = array ();

	public $CategoryName = array();
	private $dbview = array('vendor' =>'vendor','category' =>'category','virtuemart' =>'virtuemart','productdetails' =>'product','cart' => 'cart','manufacturer' => 'manufacturer','user'=>'user');

	private function __construct($instanceKey,$query) {
		$this->setLangs($instanceKey);
		if (!$this->router_disabled = VmConfig::get('seo_disabled', false)) {

			$this->seo_translate = VmConfig::get('seo_translate', false);
			
			if ( JVM_VERSION===1 ) $this->setMenuItemId();
			else $this->setMenuItemIdJ17();
			$this->setActiveMenu();
			$this->use_id = VmConfig::get('seo_use_id', false);
			$this->seo_sufix = VmConfig::get('seo_sufix', '-detail');
			$this->seo_sufix_size = strlen($this->seo_sufix) ;
			$this->edit = ('edit' == JRequest::getCmd('task') );
			// if language switcher we must know the $query
			$this->query = $query;
		}

	}

	public static function getInstance(&$query = null, $nolimit=false) {

	
	
		if (!class_exists( 'VmConfig' )) {
			require(JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR. 'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
			VmConfig::loadConfig();
		}
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
		
		$instanceKey = VMLANG ;
		if (isset($query['langswitch']) ) {
			if ($query['langswitch'] != VMLANG ) $instanceKey = $query['langswitch'] ;
			unset ($query['langswitch']);

		} 
		
		if (! array_key_exists ($instanceKey, self::$_instances)){
			self::$_instances[$instanceKey] = new vmrouterHelperSEFforOPC ($instanceKey,$query);
			if (empty($nolimit))
			{
			if (self::$limit===null){
				$mainframe = Jfactory::getApplication(); ;
				$view = 'virtuemart';
				if(isset($query['view'])) $view = $query['view'];
				self::$limit= $mainframe->getUserStateFromRequest('com_virtuemart.'.$view.'.limit', VmConfig::get('list_limit', 20), 'int');
				// 				self::$limit= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', VmConfig::get('list_limit', 20), 'int');
			}
			}
		}
		return self::$_instances[$instanceKey];
	}

	/* multi language routing ? */
	public function setLangs($instanceKey){
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
		
		
		$langs = VmConfig::get('active_languages',false);
		if(count($langs)> 1) {
			if(!in_array($instanceKey, $langs)) {
				$this->vmlang = VMLANG ;
				$this->langTag = strtr(VMLANG,'_','-');
			} else {
				$this->vmlang = strtolower(strtr($instanceKey,'-','_'));
				$this->langTag= $instanceKey;
			}
		} else $this->vmlang = $this->langTag = VMLANG ;
		//$this->setLang($instanceKey);
		$this->Jlang = JFactory::getLanguage();
	}

	public function getCategoryRoute($virtuemart_category_id){
		// stAn: 
		return $this->getCategoryRouteNocache($virtuemart_category_id);
		$cache = JFactory::getCache('_virtuemart','');
		$key = $virtuemart_category_id. $this->vmlang ; // internal cache key
		if (!($CategoryRoute = $cache->get($key))) {
			$CategoryRoute = $this->getCategoryRouteNocache($virtuemart_category_id);
			$cache->store($CategoryRoute, $key);
		}
		return $CategoryRoute ;
	}
	
	public function storeSegments($oldquery,$newquery, $segments)
	 {
	    $db=JFactory::getDBO(); 
	    
		
	 }
	
	/* Get Joomla menu item and the route for category */
	public function getCategoryRouteNocache($virtuemart_category_id){
		//if (! array_key_exists ($virtuemart_category_id . $this->vmlang, self::$_catRoute))
		
		{
			$category = new stdClass();
			$category->route = '';
			$category->itemId = 0;
			$menuCatid = 0 ;
			$ismenu = false ;
		    $CatParentIds = array(); 
			// control if category is joomla menu
			if (isset($this->menu['virtuemart_category_id'])) {
				if (isset( $this->menu['virtuemart_category_id'][$virtuemart_category_id])) {
					$ismenu = true;
					$category->itemId = $this->menu['virtuemart_category_id'][$virtuemart_category_id] ;
				}  
				{
					$CatParentIds = $this->getCategoryRecurse($virtuemart_category_id,0) ;
					
					/* control if parent categories are joomla menu */
					foreach ($CatParentIds as $CatParentId) {
						// No ? then find the parent menu categorie !
						if (isset( $this->menu['virtuemart_category_id'][$CatParentId]) ) {
							if (!isset($category->itemId))
							$category->itemId = $this->menu['virtuemart_category_id'][$CatParentId] ;
							$menuCatid = $CatParentId;
							break;
						}
					}
				}
			}
			{
				if ( $this->use_id ) $category->route .= $virtuemart_category_id.'~/~';
				if (!isset ($this->CategoryName[$virtuemart_category_id])) 
				{
					$this->CategoryName[$virtuemart_category_id] = $this->getCategoryNames($virtuemart_category_id, $menuCatid, $CatParentIds );
					
					}
				$category->route .= $this->CategoryName[$virtuemart_category_id] ;
				
				
				
				
				if ($menuCatid == 0  && $this->menu['virtuemart']) $category->itemId = $this->menu['virtuemart'] ;
			}
			if ($this->nostatic) return $category; 
			self::$_catRoute[$virtuemart_category_id . $this->vmlang] = $category;
		}
		return self::$_catRoute[$virtuemart_category_id . $this->vmlang] ;
	}

	/*get url safe names of category and parents categories  */
	public static $categoryNamesCache; 
	public function getCategoryNames($virtuemart_category_id,$catMenuId=0, $parents=0){

		if (empty(self::$categoryNamesCache)) self::$categoryNamesCache = array(); 
		if (empty(self::$categoryNamesCache[$this->vmlang])) self::$categoryNamesCache[$this->vmlang] = array(); 
		$strings = array();
		$db = JFactory::getDBO();

		if (empty($parents))
		$parents_id = array_reverse($this->getCategoryRecurse($virtuemart_category_id,$catMenuId)) ;
		else $parents_id = array_reverse($parents);
		

		
		foreach ($parents_id as $id ) {
			if(!isset(self::$categoryNamesCache[$this->vmlang][$id])){
				$q = 'SELECT `category_name` as name
					FROM  `#__virtuemart_categories_'.$this->vmlang.'`
					WHERE  `virtuemart_category_id`='.(int)$id;

				$db->setQuery($q);
				$cslug = $db->loadResult();
				
				if (empty($cslug))
				{
				  $q = 'SELECT `category_name` as name
					FROM  `#__virtuemart_categories_en_gb`
					WHERE  `virtuemart_category_id`='.(int)$id;

					$db->setQuery($q);
					$cslug = $db->loadResult();
				}
				
				if (!$this->nostatic)
				self::$categoryNamesCache[$this->vmlang][$id] = $cslug;
				
				$strings[] = $cslug;
				
			} else {
			
				$strings[] = self::$categoryNamesCache[$this->vmlang][$id];
			}

		}
		
		if(function_exists('mb_strtolower')){
			return mb_strtolower(implode ('~/~', $strings ) );
		} else {
			return strtolower(implode ('~/~', $strings ) );
		}


	}
	/* Get parents of category*/
	
	public function getCategoryRecurse($virtuemart_category_id,$catMenuId,$first=true ) {
		static $idsArr = array();
		if ($first==true) $idsArr = array();

		$db			= JFactory::getDBO();
		$q = "SELECT `category_child_id` AS `child`, `category_parent_id` AS `parent`
				FROM  #__virtuemart_category_categories AS `xref`
				WHERE `xref`.`category_child_id`= ".(int)$virtuemart_category_id;
		$db->setQuery($q);
		$ids = $db->loadObject();
	

		
		if (isset ($ids->child)) {
			$idsArr[] = $ids->child;
			if($ids->parent != 0 and $catMenuId != $virtuemart_category_id and $catMenuId != $ids->parent) {
				$this->getCategoryRecurse($ids->parent,$catMenuId,false);
			}
		}
		return $idsArr ;
	}
	/* return id of categories
	 * $names are segments
	 * $virtuemart_category_ids is joomla menu virtuemart_category_id
	 */
	public function getCategoryId($slug,$virtuemart_category_id ){
		$db = JFactory::getDBO();
		$q = "SELECT `virtuemart_category_id`
				FROM  `#__virtuemart_categories_".$this->vmlang."`
				WHERE `slug` LIKE '".$db->escape($slug)."' ";

		$db->setQuery($q);
		if (!$category_id = $db->loadResult()) {
			$category_id = $virtuemart_category_id;
		}

		return $category_id ;
	}

	/* Get URL safe Product name */
	public static $productNamesCache; 
	public function getProductName($id){
		
		if (empty(self::$productNamesCache)) self::$productNamesCache = array(); 
		if (empty(self::$productNamesCache[$this->vmlang])) self::$productNamesCache[$this->vmlang] = array(); 
		

		if(!isset(self::$productNamesCache[$this->vmlang][$id])){
			$db = JFactory::getDBO();
			$query = 'SELECT `product_name` FROM `#__virtuemart_products_'.$this->vmlang.'`  ' .
				' WHERE `virtuemart_product_id` = ' . (int) $id;
			$db->setQuery($query);
			$name = $db->loadResult();
			
			if (empty($name))
			{
			  $db = JFactory::getDBO();
			  $query = 'SELECT `product_name` FROM `#__virtuemart_products_en_gb`  ' .
				' WHERE `virtuemart_product_id` = ' . (int) $id;
			  $db->setQuery($query);
			  $name = $db->loadResult();
			}
			
			if (!$this->nostatic)
			self::$productNamesCache[$this->vmlang][$id] = $name ;
		} else {
			$name = self::$productNamesCache[$this->vmlang][$id];
		}

		return $name.$this->seo_sufix;
	}

	var $counter = 0;
	var $product_paths = array(); 
	var $product_cats = array(); 
	public function getBestProductPath($id, $option=3)
	{
	  
	  $this->product_cats[$id] = $cats = $this->getAllProductCats($id); 
	  
	  $this->product_paths[$id] = $paths = $this->getAllCategoryPaths($cats); 

	  $last_max_ind = -1; 
	  $last_max = 0; 
	  $last_min = 999999; 
	  $last_min_ind = -1; 
	  
	  foreach ($paths as $k=>$p)
	   {
	  
	      //$t1 = implode('/', $p->route); 
	      $l = mb_strlen($p->route);  
	      if ($l>$last_max) 
		  {
		  $last_max_ind = $k; 
		  $last_max = $l; 
		  }
		  if ($l<$last_min) 
		  {
		  $last_min = $l; 
		  $last_min_ind = $k; 
		  }
	   }
	   
	   if ($last_max_ind == -1) return array(); 
	  //option1: return the longest path:
	  if ($option === 1)
	  return $paths[$last_max_ind]; 
	  //option2: return the shortest path
	  if ($option === 2)
	  return $paths[$last_min_ind]; 
	  //option3: return the top category of the deepest path
	  if ($option === 3)
	  {
	  $ar = explode('~/~', $paths[$last_max_ind]->route); 
	  $paths[$last_max_ind]->route = $ar[0];
	  return $paths[$last_max_ind]; 
	  }
	  //option4: return the top category of the smallest path:
	  $paths[$last_min_ind]->route = $ar[0];
	  return $paths[$last_min_ind];
	  
	  
	  
	  
	  
	}
	
	public function getAllCategoryPaths($ida)
	{
	  $ret = array(); 
	  foreach ($ida as $id)
	   {
	      $ret[] = $this->getCategoryRoute($id['virtuemart_category_id']); 
	   }
	  return $ret;
	}
	
	public function getAllProductCats($id)
	{
	  // check for child/parent products
	  $db			= JFactory::getDBO();
		$query = 'SELECT `product_parent_id` FROM `#__virtuemart_products`  ' .
			' WHERE `virtuemart_product_id` = ' . (int) $id. ' limit 0,1 ';
		$db->setQuery($query);
		$idp = $db->loadResult(); 
		if (!empty($idp)) $id = $idp; 
		
		$query = 'SELECT `virtuemart_category_id` FROM `#__virtuemart_product_categories`  ' .
				' WHERE `virtuemart_product_id` = ' . $id;
			$db->setQuery($query);
			
		$cats = $db->loadAssocList();
	    if (empty($cats)) return array(); 
		
		return $cats; 
		
	}
	/* Get parent Product first found category ID */
	public function getParentProductcategory($id){

		$virtuemart_category_id = 0;
		$db			= JFactory::getDBO();
		$query = 'SELECT `product_parent_id` FROM `#__virtuemart_products`  ' .
			' WHERE `virtuemart_product_id` = ' . (int) $id;
		$db->setQuery($query);
		/* If product is child then get parent category ID*/
		if ($parent_id = $db->loadResult()) {
			$query = 'SELECT `virtuemart_category_id` FROM `#__virtuemart_product_categories`  ' .
				' WHERE `virtuemart_product_id` = ' . $parent_id;
			$db->setQuery($query);

			//When the child and parent id is the same, this creates a deadlock
			//add $counter, dont allow more then 10 levels
			if (!$virtuemart_category_id = $db->loadResult()){
				$this->counter++;
				if($this->counter<10){
					$this->getParentProductcategory($parent_id) ;
				}
			}

		}
		$this->counter = 0;
		return $virtuemart_category_id ;
	}


	/* get product and category ID */
	public function getProductId($names,$virtuemart_category_id = NULL ){
		$productName = array_pop($names);
		$productName =  substr($productName, 0, -(int)$this->seo_sufix_size );
		$product = array();
		$categoryName = end($names);

		$product['virtuemart_category_id'] = $this->getCategoryId($categoryName,$virtuemart_category_id ) ;
		$db = JFactory::getDBO();
		$q = 'SELECT `p`.`virtuemart_product_id`
			FROM `#__virtuemart_products_'.$this->vmlang.'` AS `p`
			LEFT JOIN `#__virtuemart_product_categories` AS `xref` ON `p`.`virtuemart_product_id` = `xref`.`virtuemart_product_id`
			WHERE `p`.`slug` LIKE "'.$db->escape($productName).'" ';
		//$q .= "	AND `xref`.`virtuemart_category_id` = ".(int)$product['virtuemart_category_id'];
		$db->setQuery($q);
		$product['virtuemart_product_id'] = $db->loadResult();
		
		if (empty($product['virtuemart_product_id']))
		{
		 $q = 'SELECT `p`.`virtuemart_product_id`
			FROM `#__virtuemart_products_en_gb` AS `p`
			LEFT JOIN `#__virtuemart_product_categories` AS `xref` ON `p`.`virtuemart_product_id` = `xref`.`virtuemart_product_id`
			WHERE `p`.`slug` LIKE "'.$db->escape($productName).'" '; 
		 $db->setQuery($q);
		 $product['virtuemart_product_id'] = $db->loadResult();
		}
		
		/* WARNING product name must be unique or you can't acces the product */

		return $product ;
	}

	/* Get URL safe Manufacturer name */
	public function getManufacturerName($virtuemart_manufacturer_id ){
		$db = JFactory::getDBO();
		$query = 'SELECT `slug` FROM `#__virtuemart_manufacturers_'.$this->vmlang.'` WHERE virtuemart_manufacturer_id='.(int)$virtuemart_manufacturer_id.' limit 0,1';
		$db->setQuery($query);

		$manufacturername =  $db->loadResult();
		if (empty($manufacturername))
		{
		  $query = 'SELECT `slug` FROM `#__virtuemart_manufacturers_en_gb` WHERE virtuemart_manufacturer_id='.(int)$virtuemart_manufacturer_id.' limit 0,1';
		  $db->setQuery($query);
		  $manufacturername =  $db->loadResult();
		}
		return $manufacturername; 

	}

	/* Get Manufacturer id */
	public function getManufacturerId($slug ){
		$db = JFactory::getDBO();
		$query = "SELECT `virtuemart_manufacturer_id` FROM `#__virtuemart_manufacturers_".$this->vmlang."` WHERE `slug` LIKE '".$db->escape($slug)."' limit 0,1";
		$db->setQuery($query);

		return $db->loadResult();

	}
	/* Get URL safe Manufacturer name */
	public function getVendorName($virtuemart_vendor_id ){
		$db = JFactory::getDBO();
		$query = 'SELECT `slug` FROM `#__virtuemart_vendors_'.$this->vmlang.'` WHERE virtuemart_vendor_id='.(int)$virtuemart_vendor_id;
		$db->setQuery($query);

		return $db->loadResult();

	}
	/* Get Manufacturer id */
	public function getVendorId($slug ){
		$db = JFactory::getDBO();
		$query = "SELECT `virtuemart_vendor_id` FROM `#__virtuemart_vendors_".$this->vmlang."` WHERE `slug` LIKE '".$db->escape($slug)."' ";
		$db->setQuery($query);

		return $db->loadResult();

	}
	/* Set $this-lang (Translator for language from virtuemart string) to load only once*/
	private function setLang($instanceKey){

		if ( $this->seo_translate ) {
			/* use translator */
			$lang =JFactory::getLanguage();
			$extension = 'com_virtuemart.sef';
			$base_dir = JPATH_SITE;
			$lang->load($extension, $base_dir);

		}
	}

	/* Set $this->menu with the Item ID from Joomla Menus */
	private function setMenuItemIdJ17(){
return; 
		$home 	= false ;
		$component	= JComponentHelper::getComponent('com_virtuemart');

		//else $items = $menus->getItems('component_id', $component->id);
		//get all vm menus

		$db			= JFactory::getDBO();
		$query = 'SELECT * FROM `#__menu`  where `link` like "index.php?option=com_virtuemart%" and client_id=0 and published=1 and (language="*" or language="'.$this->langTag.'")'  ;
		$db->setQuery($query);
		// 		vmdebug('setMenuItemIdJ17 q',$query);
		$this->menuVmitems= $db->loadObjectList();
		$homeid =0;
		if(empty($this->menuVmitems)){
			vmWarn(JText::_('COM_VIRTUEMART_ASSIGN_VM_TO_MENU'));
		} else {

			// Search  Virtuemart itemID in joomla menu
			foreach ($this->menuVmitems as $item)	{
				$linkToSplit= explode ('&',$item->link);

				$link =array();
				foreach ($linkToSplit as $tosplit) {
					$splitpos = strpos($tosplit, '=');
					$link[ (substr($tosplit, 0, $splitpos) ) ] = substr($tosplit, $splitpos+1);
				}
				//vmDebug('menu view link',$link);

				//This is fix to prevent entries in the errorlog.
				if(!empty($link['view'])){
					$view = $link['view'] ;
					if (array_key_exists($view,$this->dbview) ){
						$dbKey = $this->dbview[$view];
					}
					else {
						$dbKey = false ;
					}

					if ( isset($link['virtuemart_'.$dbKey.'_id']) && $dbKey ){
						$this->menu['virtuemart_'.$dbKey.'_id'][ $link['virtuemart_'.$dbKey.'_id'] ] = $item->id;
					}
					elseif ($home == $view ) continue;
					else $this->menu[$view]= $item->id ;

					if ($item->home === 1) {
						$home = $view;
						$homeid = $item->id;
					}
				} else {
					vmdebug('my item with empty $link["view"]',$item);
					vmError('$link["view"] is empty');
				}

			}
		}



		// init unsetted views  to defaut front view or nothing(prevent duplicates routes)
		if ( !isset( $this->menu['virtuemart']) ) {
			if (isset ($this->menu['virtuemart_category_id'][0]) ) {
				$this->menu['virtuemart'] = $this->menu['virtuemart_category_id'][0] ;
			}else $this->menu['virtuemart'] = $homeid;
		}
		// if ( !isset( $this->menu['manufacturer']) ) {
		// $this->menu['manufacturer'] = $this->menu['virtuemart'] ;
		// }
		// if ( !isset( $this->menu['vendor']) ) {
		// $this->menu['manufacturer'] = $this->menu['virtuemart'] ;
		// }

	}
		//http://php.net/ucfirst
	function my_ucfirst($string, $e ='utf-8') { 
        if (function_exists('mb_strtoupper') && function_exists('mb_substr') && !empty($string)) { 
            $string = mb_strtolower($string, $e); 
            $upper = mb_strtoupper($string, $e); 
            preg_match('#(.)#us', $upper, $matches); 
            $string = $matches[1] . mb_substr($string, 1, mb_strlen($string, $e), $e); 
        } else { 
            $string = ucfirst($string); 
        } 
        return $string; 
    } 
	
	
	  function getManufacturerKeywords($manufacturer_id, $title, $lang)
  {
    $keyw = array();
    $products = $this->getAllProductsOfMf($manufacturer_id);
    $pbw = array();
    foreach ($products as $p)
	 {
	  $pbw = $this->mergeAr($pbw, $this->getWords($p));	 
	 }
	 // potom slova z nazvov kategorii oddelene ciarkami
	 foreach ($title as $k)
	 {
	    $keyw[] = $k;
	    $keyw2 = JoomSEF::_titleToLocation($k);
	    $keyw2 = str_replace(array("-", "_"), " ", $keyw2 );
	    $keyw[] = $keyw2;
		$keyw = $this->mergeAr($keyw, $this->getWords($keyw2));	  
	  
      }
      $keyw = $this->mergeAr($pbw, $keyw); 
      $ret = $this->Ar2Str($keyw);
      //$this->logger($keyw, "getMfKeys".$ret);
   	  return $ret;
  }

	
	
	/* Set $this->menu with the Item ID from Joomla Menus */
	private function setMenuItemId(){

		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		$component	= JComponentHelper::getComponent('com_virtuemart');
		$items = $menus->getItems('componentid', $component->id);

		if(empty($items)){
			vmWarn(JText::_('COM_VIRTUEMART_ASSIGN_VM_TO_MENU'));
		} else {
			// Search  Virtuemart itemID in joomla menu
			foreach ($items as $item)	{
				$view = $item->query['view'] ;
				if ($view=='virtuemart') $this->menu['virtuemart'] = $item->id;
				$dbKey = $this->dbview[$view];
				if ( isset($item->query['virtuemart_'.$dbKey.'_id']) )
					$this->menu['virtuemart_'.$dbKey.'_id'][ $item->query['virtuemart_'.$dbKey.'_id'] ] = $item->id;
				else $this->menu[$view]= $item->id ;
			}
		}

		// init unsetted views  to defaut front view or nothing(prevent duplicates routes)
		if ( !isset( $this->menu['virtuemart'][0]) ) {
			$this->menu['virtuemart'][0] = null;
		}
		if ( !isset( $this->menu['manufacturer']) ) {
			$this->menu['manufacturer'] = $this->menu['virtuemart'][0] ;
		}

	}
	/* Set $this->activeMenu to current Item ID from Joomla Menus */
	private function setActiveMenu(){
		if ($this->activeMenu === null ) {
			//$menu = JSite::getMenu();
			//$menu = JFactory::getApplication()->getMenu();
			$app		= JFactory::getApplication();
			$menu		= $app->getMenu('site');
			if ($Itemid = JRequest::getInt('Itemid',0) ) {
				$menuItem = $menu->getItem($Itemid);
			} else {
				$menuItem = $menu->getActive();
			}

			$this->activeMenu = new stdClass();
			$this->activeMenu->view			= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
			$this->activeMenu->virtuemart_category_id	= (empty($menuItem->query['virtuemart_category_id'])) ? 0 : $menuItem->query['virtuemart_category_id'];
			$this->activeMenu->virtuemart_product_id	= (empty($menuItem->query['virtuemart_product_id'])) ? null : $menuItem->query['virtuemart_product_id'];
			$this->activeMenu->virtuemart_manufacturer_id	= (empty($menuItem->query['virtuemart_manufacturer_id'])) ? null : $menuItem->query['virtuemart_manufacturer_id'];
/* added in 208 */
			$this->activeMenu->virtuemart_vendor_id	= (empty($menuItem->query['virtuemart_vendor_id'])) ? null : $menuItem->query['virtuemart_vendor_id'];

			$this->activeMenu->Component	= (empty($menuItem->component)) ? null : $menuItem->component;
		}

	}

	/*
	 * Get language key or use $key in route
	 */
	public function lang($key) {
		//if ($this->seo_translate ) 
		{
		
		 $lang = JFactory::getLanguage(); 
		 $current_lang = $lang->getTag(); 
		$language_tag = $this->langTag; 
		if ($current_lang != $language_tag)
		{
		$lang = JFactory::getLanguage();
		$extension = 'com_virtuemart.sef';
		$base_dir = JPATH_SITE;
		
		$reload = true;
		$lang->load($extension, $base_dir, $language_tag, true);
		}
		
		$text = $key; 
			$jtext = (strtoupper( $key ) );
			if ($this->Jlang->hasKey('COM_VIRTUEMART_SEF_'.$jtext) ){
				//vmdebug('router lang translated '.$jtext);
				$text = JText::_('COM_VIRTUEMART_SEF_'.$jtext);
			}
		}
		
		if ($current_lang != $language_tag)
		{
		
		$extension = 'com_virtuemart.sef';
		$base_dir = JPATH_SITE;
		
		$reload = true;
		$lang->load($extension, $base_dir, $current_lang, true);
		}

		
		return $text;
	}
	
	public function getCategoryMeta($cat_id, $lang='en_gb')
	{
	
	  $db = JFactory::getDBO(); 
	  $q = 'select * from  #__virtuemart_categories_'.$lang.' as l where l.virtuemart_category_id = '.(int)$cat_id.' limit 0,1'; 
	  
	  $db->setQuery($q); 
	  $res = $db->loadAssoc(); 
	  return $res; 
	  

	  }

	public function getProductMeta($cat_id, $lang='en_gb')
	{
	
	  $db = JFactory::getDBO(); 
	  $q = 'select * from  #__virtuemart_products_'.$lang.' as l where l.virtuemart_product_id = '.(int)$cat_id.' limit 0,1'; 
	  
	  $db->setQuery($q); 
	  $res = $db->loadAssoc(); 
	  
	  return $res; 
	  
	}

	  
function Ar2Str($keyw)
  {
   foreach ($keyw as &$v)
   {
    $v = mb_strtolower($v, "UTF-8");
   }
   $keyw = array_unique($keyw);
   
   $ret = '';
   foreach ($keyw as $str)
   {
     if (isset($str))
     if ($str!='')
        $ret .= trim($str).','; 
   }
   return $ret;
  }

	
	
	  function getAllProductsOfCat($catid, $lang)
  {
      $database = JFactory::getDBO();
      $sql = "SELECT l.product_name FROM `#__virtuemart_products` as pp, `#__virtuemart_product_categories` as ref, #__virtuemart_products_".$lang." as l WHERE ref.virtuemart_category_id = '".$catid."' and pp.virtuemart_product_id = ref.virtuemart_product_id and l.virtuemart_product_id = pp.virtuemart_product_id LIMIT 0,10";
      $database->setQuery($sql);
      $prods = $database->loadResultArray(0);

	  return $prods;
  }

  
    // vrati celu cestu ku kategorii a ostatne top kategorie,kt su priradene
  // pozor, zle zoradene! pouziva sa iba pri klucovych slovach
	function get_CatsOfCat($category_id, $lang)
	{
	if (isset($this->catsOfCats[$category_id])) return $this->catsOfCats[$category_id]; 
	 $database = JFactory::getDBO();
	 $category_id = (int)$category_id; 
	 $sql ="SELECT l.category_name FROM #__virtuemart_categories_".$lang." as l, #__virtuemart_categories as cat, #__virtuemart_category_categories as ref WHERE l.virtuemart_category_id = cat.virtuemart_category_id and cat.published=1 AND (cat.virtuemart_category_id=ref.category_child_id OR cat.virtuemart_category_id=ref.category_parent_id) AND (ref.category_parent_id=".$category_id." or ref.category_child_id=".$category_id.") LIMIT 0,10";
   
   $database->setQuery($sql);
   $res =  $database->loadResultArray(0);
   echo $database->getErrorMsg(); 
   $res = array_unique($res);

   return $res;

  }

	function getProductKeywords($product_id, $title, $lang)
	{
	    $database = JFactory::getDBO();
	 $keyw = array();
	 
	 

	 $kw = ''; 
	 foreach ($title as $w)
	  {
	    if (!empty($kw)) $kw .=','; 
		$w = str_replace('|', ' ', $w); 
		$kw .= $w; 
	  }
	  
	  if (!empty($this->product_paths[$product_id]))
	 foreach ($this->product_paths[$product_id] as $o)
	  {
	    $k = str_replace('~/~', ' ', $o->route); 
		if (!empty($kw)) $kw .=','; 
		$kw .= $k; 
		
	  }
	  
	
     return $kw;
  }
	
	function getCategoryKeywords($category_id, $title, $lang)
  {
  
   $database = JFactory::getDBO();
   // title vlastne nepotrebujeme
   $title = array();
   $keyw = array();
    
   $cats = $this->get_CatsOfCat($category_id, $lang);
   if (isset($cats)) $title = $this->mergeAr($title, $cats);
   
   $products = $this->getAllProductsOfCat($category_id, $lang);
   //if (isset($products)) $title = $this->mergeAr($title, $products);
	 
	 
	 $pbw = array();
	 // najskor prida klucove slova z nazvov produktov
	 foreach ($products as $p)
	 {
	  $pbw = $this->mergeAr($pbw, $this->getWords($p));	 
	 }
	 // potom slova z nazvov kategorii oddelene ciarkami
	 foreach ($title as $k)
	 {
	    $keyw[] = $k;
	    $keyw2 = JoomSEF::_titleToLocation($k);
	    $keyw2 = str_replace(array("-", "_"), " ", $keyw2 );
	    $keyw[] = $keyw2;
		$keyw = $this->mergeAr($keyw, $this->getWords($keyw2));	  
	  
	  
      }
      $keyw = $this->mergeAr($pbw, $keyw); 

   $ret = $this->Ar2Str($keyw);
   return $ret;

  }
  
  	function mergeAr($arr1, $arr2)
  {
   $ret = array();
   if (is_array($arr1))
   foreach ($arr1 as $v)
   {
    if (is_array($v))
     foreach ($v as $x)
      $ret[] = $x;
    else
     $ret[] = $v;
   }
   else
   if (isset($arr1))
    if ($arr1!='')
      $ret[] = $arr1;

   if (is_array($arr2))
   foreach ($arr2 as $v)
    if (is_array($v))
     foreach ($v as $x)
      $ret[] = $x;
     else
     $ret[] = $v;
   else
   if (isset($arr2))
    if ($arr2!='')
    $ret[] = $arr2;
  
  return $ret;
  }

  
  function getWords($str)
  {
  	  $test = explode(" ", $str);
  	  $arr = array();
	
	
	
	if ($test!=false)
    if (count($test)>1)
    {
    foreach ($test as $k2)
    {
       $k2 = trim($k2);
       if (strlen($k2)>2)
       {
         $arr[] = $k2;
         $keyw2 = JoomSEF::_titleToLocation($k2);
         $keyw2 = str_replace(array("-", "_"), " ", $keyw2 );
	     $arr[] = $keyw2;
       }
    }
      
      return $arr;
    }
    return null;
	
  }
  
	
	
	public function getCatDescPerRandomProduct($cat_id, $lang='en_gb')
	{
	  $db = JFactory::getDBO(); 
	  $q = 'select p.product_s_desc, p.product_desc, p.metadesc from #__virtuemart_products_'.$lang.' as p, #__virtuemart_product_categories as c, #__virtuemart_products as pp where c.virtuemart_category_id = '.$cat_id.' and pp.virtuemart_product_id = c.virtuemart_product_id and pp.virtuemart_product_id = p.virtuemart_product_id and pp.published = 1 and p.product_desc > "" order by pp.virtuemart_product_id desc limit 0,1'; 
	  $db->setQuery($q); 
	  $res = $db->loadAssoc(); 
	  
	  if (!empty($res))
	  {
	    if (!empty($res['metadesc'])) return $res['metadesc']; 
		if (!empty($res['product_s_desc'])) return $res['product_s_desc']; 
		if (!empty($res['product_desc'])) return $res['product_desc']; 
		
	  }
	  return ''; 
	  
	}
	
	
	/*
	 * revert key or use $key in route
	 */
	public function getOrderingKey($key) {
		if ($this->seo_translate ) {
			if ($this->orderings == null) {
				$this->orderings = array(
					'p.virtuemart_product_id'=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_ID'),
					'product_sku'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_SKU'),
					'product_price'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_PRICE'),
					'category_name'		=> JText::_('COM_VIRTUEMART_SEF_CATEGORY_NAME'),
					'category_description'=> JText::_('COM_VIRTUEMART_SEF_CATEGORY_DESCRIPTION'),
					'mf_name' 			=> JText::_('COM_VIRTUEMART_SEF_MF_NAME'),
					'product_s_desc'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_S_DESC'),
					'product_desc'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_DESC'),
					'product_weight'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_WEIGHT'),
					'product_weight_uom'=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_WEIGHT_UOM'),
					'product_length'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_LENGTH'),
					'product_width'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_WIDTH'),
					'product_height'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_HEIGHT'),
					'product_lwh_uom'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_LWH_UOM'),
					'product_in_stock'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_IN_STOCK'),
					'low_stock_notification'=> JText::_('COM_VIRTUEMART_SEF_LOW_STOCK_NOTIFICATION'),
					'product_available_date'=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_AVAILABLE_DATE'),
					'product_availability'  => JText::_('COM_VIRTUEMART_SEF_PRODUCT_AVAILABILITY'),
					'product_special'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_SPECIAL'),
					'created_on' 		=> JText::_('COM_VIRTUEMART_SEF_CREATED_ON'),
					// 'p.modified_on' 		=> JText::_('COM_VIRTUEMART_SEF_MDATE'),
					'product_name'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_NAME'),
					'product_sales'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_SALES'),
					'product_unit'		=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_UNIT'),
					'product_packaging'	=> JText::_('COM_VIRTUEMART_SEF_PRODUCT_PACKAGING'),
					'p.intnotes'			=> JText::_('COM_VIRTUEMART_SEF_INTNOTES'),
					'ordering' => JText::_('COM_VIRTUEMART_SEF_ORDERING')
				);
			}
			if ($result = array_search($key,$this->orderings )) {
				return $result;
			}
		}
		return $key;
	}
	/*
	 * revert string key or use $key in route
	 */
	public function compareKey($string, $key) {
		if ($this->seo_translate ) {
			if (JText::_('COM_VIRTUEMART_SEF_'.$key) == $string )
			{
				return true;
			}

		}
		if ($string == $key) return true;
		return false;
	}
}