<?php
/**
 * @package		RuposTel Ajax search pro
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('_JEXEC') or die;

class RupHelper {
	
	public static function getIncludes()
	{
	  if (!class_exists('VmConfig'))	  
		{
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		}
		VmConfig::loadConfig(); 
		if(!class_exists('shopFunctionsF'))require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php');
		
		if (!class_exists('VmImage'))
			require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'image.php');
			
	   
				if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
		if (!class_exists('VmConfig'))
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		 
		  if (method_exists('VmConfig', 'loadJLang'))
		  {
		  VmConfig::loadJLang('com_virtuemart',TRUE);
		  VmConfig::loadJLang('com_virtuemart_orders',TRUE);
		  }
		  
		 if (method_exists('VmConfig', 'loadJLang'))
		 VmConfig::loadJLang('com_virtuemart');
		 else
		  {
		     $lang = JFactory::getLanguage();
			 $extension = 'com_virtuemart';
			 $base_dir = JPATH_SITE;
			 $language_tag = $lang->getTag();
			 $reload = false;
			 $lang->load($extension, $base_dir, $language_tag, $reload);
			 
			 $lang = JFactory::getLanguage();
			 $extension = 'com_virtuemart';
			 $base_dir = JPATH_ADMINISTRATOR;
			 $language_tag = $lang->getTag();
			 $reload = false;
			 $lang->load($extension, $base_dir, $language_tag, $reload);
			 
		  }
		  
	}

	
	public static function updateStats()
	{
		$keyword = JRequest::getVar('keyword'); 
		$qt = "CREATE TABLE IF NOT EXISTS `#__com_rupsearch_stats` (
  `keyword` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `md5` char(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `count` bigint(20) NOT NULL,
  `accessstamp` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `md5` (`md5`),
  KEY `count` (`count`),
  KEY `accessstamp` (`accessstamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1; "; 
	
	
	 $db = JFactory::getDBO(); 
	 
	    $prefix = $db->getPrefix();
		
		$table = '#__com_rupsearch_stats'; 
   $table = str_replace('#__', '', $table); 
   $table = str_replace($prefix, '', $table); 
 
   $q = "SHOW TABLES LIKE '".$db->getPrefix().$table."'";
	 $db->setQuery($q);
	  $r = $db->loadResult();
	   if (empty($r)) 
	   {
	     $db->setQuery($qt); 
		 $db->query(); 
	   }

	 $md5 = md5($keyword); 
	 $q = 'insert DELAYED into #__com_rupsearch_stats (`keyword`, `md5`, `count`, `accessstamp`) values '; 
	  $q .= " ('".$db->escape($keyword)."', '".$md5."', 1, ".time().") "; 
	  $q .= ' on duplicate key update count = count + 1, accessstamp = '.time(); 
	 $db->setQuery($q); 
	 $db->query();
	 $e = $db->getErrorMsg(); //if (!empty($e)) echo $e; 

	// 2 months old will get deleted
	/*
	 $old = time() - (60*60*24*60); 
	 $q = 'delete from #__com_rupsearch_stats where accessstamp < '.$old; 
	 $db->setQuery($q); 
	 $db->query();
	*/
	
	}
	
	
	public static function loadLangFiles() {
	
 // load virtuemart language files
$jlang = $lang = $langO = JFactory::getLanguage();
$jlang->load('com_virtuemart', JPATH_SITE, $jlang->getDefault(), true);
$jlang->load('com_virtuemart', JPATH_SITE, null, true);





$extension = 'com_search';
$base_dir = JPATH_SITE;
$language_tag = $lang->getTag(); 
$lang->load($extension, $base_dir, $language_tag, true);


 
			$clang = JRequest::getVar('lang', ''); 
			
			
			$locales = $langO->getLocale();
		$tag = $langO->getTag(); 
		$app = JFactory::getApplication(); 		
		
		$found = false; 
		
		if (class_exists('JLanguageHelper') && (method_exists('JLanguageHelper', 'getLanguages')))
		{
		$sefs 		= JLanguageHelper::getLanguages('sef');
		foreach ($sefs as $k=>$v)
		{
			if ($v->lang_code == $tag)
			if (isset($v->sef)) 
			{
				$ret = $v->sef; 

				$clang2 = $ret; 
				if ((!empty($clang))  && ($clang === $clang2)) $found = true; 
				
				
			}
		}
		
		if ((empty($clang)) || (!$found)) $clang = $clang2; 
		
		
		}
		
		
		
			 if ( version_compare( JVERSION, '3.0', '<' ) == 1) {       
			if (isset($locales[6]) && (strlen($locales[6])==2))
			{
				
				$clang = $locales[6]; 
				
			}
			else
			if (!empty($locales[4]))
			{
				$clang = $locales[4]; 
				
				if (stripos($clang, '_')!==false)
				{
					$la = explode('_', $clang); 
					$clang = $la[1]; 
					if (stripos($clang, '.')!==false)
					{
						$la2 = explode('.', $clang); 
						$clang = strtolower($la2[0]); 
					}
				
					
				}
		     	
			}
			
			 }
			 
			 return $clang; 
	
	}
	
	
	
	public static function renderHidden()
	{
		//preserve head data: 
		$doc = JFactory::getDocument(); 
  /*
  $title = $doc->getTitle(); 
  $kw = $document->getMetaData('keywords');
  $desc =	$document->getDescription( );
  $robot = $document->getMetaData('robots');
  $uth = $document->getMetaData('author');
   */
  $hd = $doc->getHeadData(); 
		
		
		
	 $db = JFactory::getDBO(); 
	 $q = 'select virtuemart_category_id from #__virtuemart_categories where published = 1 limit 0,1'; 
	 $db->setQuery($q); 
	 $cat_id = $db->loadResult(); 
	 
	  $controllerClassName = 'VirtueMartControllerCategory' ;
	  if (!class_exists($controllerClassName)) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'category.php');
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'category'.DIRECTORY_SEPARATOR.'view.html.php'); 
	  //$view = new VirtuemartViewCategory(); 
	   $isset = JRequest::getVar('virtuemart_category_id', null); 
	  $cat_id = JRequest::getVar('virtuemart_category_id', $cat_id); 
	  //new: JRequest::setVar('virtuemart_category_id', $cat_id); 
	  
	  $oldoption = JRequest::getVar('option'); 
	  
	  
	  //new: JRequest::setVar('option', 'com_virtuemart'); 
	  
	  
	  //JRequest::setVar('virtuemart_category_id', 1); 
	  $config = array(); 
	  $config['base_path'] = JPATH_VM_SITE;
	  $config['view_path'] = JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views'; 
	  $controller = new $controllerClassName($config); 
	  if (method_exists($controller, 'addViewPath')) { 
	  $controller->set('_basePath', JPATH_VM_SITE); 
	  $controller->set('_setPath', JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views'); 
	  $controller->addViewPath(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views');
	  }
	  $tp = self::getTemplatePath('category'); 

	  //$view = $controller->getView('category', 'html', '', $config);
	  
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'rupview.php'); 
		$view = new rupSearch(); 
	  if (method_exists($view, 'addTemplatePath'))
	  {
	   $view->addTemplatePath(JPATH_VM_SITE.'/views/category/tmpl');
	  
	  
	  if (!empty($tp))
	   {
	    $view->addTemplatePath($tp); 
		
		
	   }
	   }
	   else
	  if (method_exists($view, 'addIncludePath'))
	  {
	   $view->addIncludePath(JPATH_VM_SITE.'/views/category/tmpl');
	  
	  
	  if (!empty($tp))
	   {
	    $view->addIncludePath($tp); 
		
		
	   }
	   }
	   
	   
	   $es = ''; 

	   $orderByList = array(); 
	   $orderByList['manufacturer'] = ''; 
	   $orderByList['orderby'] = ''; 
	   
	     $view->orderByList['orderby'] = ''; 
	     $view->orderByList['manufacturer'] = ''; 
	   
	   if (method_exists($view, 'assignRef'))
	   {
		   $view->assignRef('orderByList', $orderByList); 
	   }
	   
	   
	   
	   
	   
	  
	  
	  $view->viewName = 'category'; 
	  if (method_exists($view, 'setLayout'))
	  $view->setLayout('default'); 
	  else
	  if (method_exists($view, 'set'))
	  $view->set('layout', 'default');
	  else	  
	  if (method_exists($view, 'assignRef'))
	  $view->assignRef('layout', 'default'); 
	  else
	  $view->layout =  'default';

	  
	  	 
	  if (method_exists($view, 'set'))
	  $view->set('format', 'html');
	  else	  
	  if (method_exists($view, 'assignRef'))
	  $view->assignRef('format', 'html'); 
	  else
	  $view->format = 'html'; 

	   $cc = 0; 
	   
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'pagination.php'); 
	   
	   $prodcs = array(); 
	   $rp = new rupPagination($cc, 0, 1 ,1 );
	   $view->vmPagination = $rp;
	  
	  
	
		
	  ob_start(); 
	  $view->loadYag(); 
	  $view->display(); 
	  
	 
	  
	  $x = ob_get_clean(); 
	  
	  
	  
	  //restore head data: 
	  $hd2 = $doc->getHeadData(); 
  $hd3 = array(); 
  foreach ($hd2 as $key=>$val)
    {
	   switch ($key)
	   {
	    case 'title': 
			$hd3[$key] = $hd[$key]; 
		     break; 
	    case 'description': 
			$hd3[$key] = $hd[$key]; 
		     break; 
	    case 'link':
			$hd3[$key] = $hd[$key]; 
		     break; 
	    case 'metaTags': 
			$hd3[$key] = $hd[$key]; 
		     break; 
	    case 'links':
			$hd3[$key] = $hd[$key]; 
		     break; 
	    case 'styleSheets': 
			$hd3[$key] = $hd2[$key]; 
		     break; 
	    case 'style': 
			$hd3[$key] = $hd2[$key]; 
		     break; 
	    case 'scripts': 
			$hd3[$key] = $hd2[$key]; 
		     break; 
	    case 'script': 
			$hd3[$key] = $hd2[$key]; 
		     break; 
	    case 'custom':
			$hd3[$key] = $hd2[$key]; 
		     break; 
		default:
		  $hd3[$key] = $hd2[$key]; 
		     break; 
			 
		}

			 
	}
  $doc->setHeadData($hd3); 
  
  
  
  
	}
	public static function getModuleItemid($itemid) {
	 if (!empty($itemid)) return (int)$itemid; 
	 $my_itemid = (int)$itemid; 

if (empty($my_itemid)) {
 $app = JFactory::getApplication();
	 $menu = $app->getMenu();
	 $items = $menu->getItems( 'link', 'index.php?option=com_rupsearch&view=search', false );
	
	 foreach ($items as $item)
	 {
	    if (($item->language === '*') || ($item->language === $language_tag))
		{
			$my_itemid = $item->id; 
			break; 
		}
	 }


}
return $my_itemid; 
	}
	
 public static function setVMLANG() {
	 
	  if (!class_exists('VmConfig'))
		    {
			     if (!class_exists('VmConfig'))
				require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
				VmConfig::loadConfig ();

			}
			if(!defined('VMLANG'))		
			if (method_exists('VmConfig', 'setdbLanguageTag')) {
			   VmConfig::setdbLanguageTag();
			}
	 
    
	if ((!defined('VMLANG')) && (!empty(VmConfig::$vmlang))) {
	  define('VMLANG', VmConfig::$vmlang); 
	}
		
 }
	public static function getToCats(&$top_cats, &$category_name) {
	   
	   self::setVMLANG(); 
	 
	 $vm_cat_id = JRequest::getVar('virtuemart_category_id', JRequest::getVar('vm_cat_id', 0, 'get', 'INT') , 'get', 'INT'); 
	 
	 
	 
	 $vm_cat_id = (int)$vm_cat_id; 
	 $c = 0; 
	 $vm_cat_id = self::getTop($vm_cat_id, $c); 
	 
	 $topc = array(); 
	 if (!empty($top_cats)) {
	    foreach ($top_cats as $t) {
		  $t = trim($t); 
		  $t = (int)$t; 
		  if (!empty($t)) {
			  $topc[$t] = array(); 
			  $topc[$t]['category_child_id'] = $t; 
		  }
		}
	 }
	 $db = JFactory::getDBO(); 
	 if (empty($topc)) {
	 
	 $q = 'select `category_child_id` from #__virtuemart_category_categories where `category_parent_id` = 0 order by `ordering` desc'; 
	 $db->setQuery($q); 
	 $topc = $db->loadAssocList(); 
	 }
	 
	 
	 $mq = $tcq = ''; 
	 
	 $tc = array(); 
	 if (!empty($topc)) { 
	   foreach ($topc as $k=>$row) { 
	     $tc[] = (int)$row['category_child_id']; 
	   }
	 }
	 if (!empty($tc)) { 
	  $tcq = implode(',', $tc); 
	  $tcq = ' (`c`.`published` = 1 and `c`.`virtuemart_category_id` IN ('.$tcq.') ) '; 
	 }
	 if (!empty($vm_cat_id)) { 
	   $mq = '  (c.`virtuemart_category_id` = '.(int)$vm_cat_id.') ';
	 }
	 
	 if ((!empty($mq)) && (!empty($tcq)))  {
	   $tcq = ' or '.$tcq; 
	 }
	 
	 $q = 'select l.`category_name`, l.`virtuemart_category_id` from `#__virtuemart_categories_'.VMLANG.'` as l, `#__virtuemart_categories` as c where (l.`virtuemart_category_id` = c.`virtuemart_category_id`) and ('.$mq.$tcq.') '; 
	
	 $db->setQuery($q); 
	 try { 
	 
	 $top_cats = (array)$db->loadAssocList(); 
	 
	 }
	   catch (Exception $e) {
		   $msg = (string)$e; 
		   echo $msg; 
	 }
	 
	
	 
	 if (!empty($top_cats))
	 foreach ($top_cats as $k=>$v)
	 {
		 $v['virtuemart_category_id'] = (int)$v['virtuemart_category_id']; 
		 if ($v['virtuemart_category_id'] === $vm_cat_id) {
		   $category_name = $v['category_name']; 
		 
		   if (!in_array($v['virtuemart_category_id'], $tc)) {
		     unset($top_cats[$k]); 
		   }
		 }
		 
	 }
	 
	 
	  self::sortCats($top_cats); 
	 
	 
	}
	
	public static function sortCats(&$cats) {
		$copy = $cats; 
		usort($copy, array('rupHelper', "sort_cats"));
		
		$ret = array(); 
		//for ($i=0; $i<count($copy); $i++)
		foreach ($copy as $i=>$val3)
		{
		foreach ($cats as $key=>$val) 
		{
			 $val2 = $copy[$i]; 
			 if ($val2 === $val) {
				 $ret[$key] = $val; 
			 }
		 }
		}
		
		$cats = $ret; 
		return $ret; 
		//$attributes = $copy; 
	}
	
	public static function sort_cats($a, $b) {
		$a1 = $a['category_name']; 
		$b1 = $b['category_name']; 
	
	
	$tag = JFactory::getLanguage()->getTag(); 
	$tag = strtolower($tag); 
	$tag = str_replace('-', '_', $tag); 
	
	if (!class_exists('Collator')) return true; 
	
	$c = new Collator($tag);
    return $c->compare($a1, $b1); 
		
	}
	
	
	
	public static function getTop($id, &$count)
	{
		// only up to 10th level: 
		$id = (int)$id; 
		if ($count > 10) return $id; 
		
		$db = JFactory::getDBO(); 
		$q = 'select `category_child_id`, `category_parent_id` from `#__virtuemart_category_categories` where `category_child_id` = '.$id.' limit 0,1'; 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		
		if (empty($res['category_parent_id'])) return (int)$res['category_child_id']; 
		if ($res['category_parent_id'] === $res['category_child_id']) 
			return (int)$res['category_child_id']; 
		$count++; 
		
		
		
		
		return self::getTop($res['category_parent_id'], $count); 
		 
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
			$q = 'select `params` from `#__modules` where `id` = '.$id.' and `module` = \'mod_virtuemart_ajax_search_pro\' limit 1'; 
			
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
			
			$q = 'select `params` from `#__modules` where `module` = \'mod_virtuemart_ajax_search_pro\' and published = 1 limit 1'; 
			
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
	
	
	
	public static function getProducts($keyword, $prods=5, $popup=false, $order_by='')
	{
	self::setVMLANG(); 
	
	 $params = self::getParams(); 
	 
	 if (empty($keyword)) {
		 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_productcustoms'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')) {
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_productcustoms'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'); 
		 return PCH::getCategoryProducts($keyword, $prods, $popup, $order_by); 
		 }
	 }
	 
	$priorities = $params->get('search_priority', 'PRODUCT_SKU,PRODUCT_NAME,PRODUCT_NAME_WORDS,PRODUCT_NAME_MULTI_WORDS,PRODUCT_SKU_STARTS_WITH,PRODUCT_DESC,PRODUCT_S_DESC,PRODUCT_ATTRIBS,MF_NAME,CAT_NAME,PRODUCT_SKU_ENDS'); 
	 $priorities = explode(',', $priorities); 
	 $prod_o = $prods; 
	 $prods++; 
	
	  $db = JFactory::getDBO(); 
	  $or = $or2 = $or3 = '';
	  $ko = trim($keyword);  
	  $ko2 = preg_replace('/\s+/', ' ',$keyword);
	  if (!empty($ko2)) {
	   $ko = $ko2; 
	  }
	  $ae = explode(' ', $ko); 
	  
	  $no_short = $params->get('no_short', false); 
	  
	  $f_cats = $params->get('cat_search', false); 
	  $only_current = JRequest::getVar('only_current', $params->get('only_current', false)); 
	  if (!empty($only_current)) $f_cats = true; 
	  
	  $vm_cat_id = JRequest::getInt('vm_cat_id', 0); 
	  if (empty($vm_cat_id)) $f_cats = false; 
	  
	  
	  
	  
	  if ((!empty($ae)) && (count($ae)>1))
	   {
	      if (empty($or)) {
			  $or = ' OR ('; 
			  $or2 = ' OR ('; 
			  $or3 = ' OR ('; 
		  }
		  $i = 0;
		  $all = count($ae); 
	      foreach ($ae as $word)
		    {
			   if (empty($word)) continue; 
				
			   $i++; 
			   if (strlen($word)<=2) 
			   {
				   $word .= ' '; 
			   }
			   
			   // take just the base of the word: 
			   if (empty($no_short)) { 
			   if (mb_strlen($word)>8)
			   {
				   $word = mb_substr($word, 0, -3); 
			   }
			   else
			   if ((mb_strlen($keyword)>6) && (mb_strlen($keyword)<9))
			   {
				      $word = mb_substr($word, 0, -2); 
			   }
			   else
			   $word = mb_substr($word, 0, -1); 
			   }
			   
			   $or .= " ( l.`product_name` LIKE '%".$db->escape($word)."%' ) "; 
			   $or2 .= " ( l.`product_s_desc` LIKE '%".$db->escape($word)."%' ) "; 
			   $or3 .= " ( l.`product_desc` LIKE '%".$db->escape($word)."%' ) "; 
			   
			   if ($i!=$all) 
			   {
				   $or .= ' AND '; 
				   $or2 .= ' AND '; 
				   $or3 .= ' AND '; 
			   }
			}
			
		  $or .= ') '; 
		  $or2 .= ') '; 
		  $or3 .= ') '; 
	   }
	   
	   if (empty($i)) {
		     $or = $or2 = $or3 = '';
	   }
	   
	  $child_handling = JRequest::getVar('op_childhandling', $params->get('child_products', 0)); 
	
	  
	  $only_in_stock = $params->get('only_in_stock', false); 
	  
	  if (empty($order_by)) $order_by = $params->get('order_byf', ''); 
	  if (empty($order_by)) $order_by = $params->get('order_by', ''); 
	  if ($order_by === 'none') $order_by = ''; 
	  
	  
	  
	  $stock = ''; 
	  if (!empty($only_in_stock))
	  {
		  $stock = ' p.`product_in_stock` > 0 and '; 
	  }
	  $order_single = ''; 
	  $order_sql = ''; 
	  $product_limit = " LIMIT 0,".$prods.' ';
	  $global_limit = " LIMIT 0,".$prods.' ';
	  $order_single2  = $product_limit2 = ''; 
	  
	  //we won't use internal limits: 
	  $product_limit = $product_limit2 = ''; 
	   $order_all = ''; 
	  if (!empty($order_by))
	  {
		  switch ($order_by)
		  {
			  case 'product_name': 
			  $order_sql = ', l.`product_name` as SORTFIELD '; 
			  $order_all = ' order by SORTFIELD '; 
			  break; 
			  case 'created_on': 
			  $order_sql = ', p.created_on as SORTFIELD '; 
			  $order_all = ' order by SORTFIELD desc '; 
			  break; 
			   case 'available_on':
			   
			   //$order_sql = ', "{ROWPRIORITY}" as `rank`, p.product_available_date as SORTFIELD '; 
			   //$order_sql = ', "{ROWPRIORITY}" as `rank`, p.product_available_date as SORTFIELD '; 
			    $order_sql = ', p.product_available_date as SORTFIELD '; 
			    $order_all = ''; 
			   //$order_all = ' order by SORTFIELD desc'; 
			   //$order_single2 = ' ORDER BY SORTFIELD asc'; 
			   //$product_limit2 = ' limit 0, '.$prods; //$product_limit;
			   //$product_limit = ''; 
			   $zero_select = ', 0 as product_available_date'; 
			   $order_single2 = ' ORDER BY p.product_available_date desc'; 
			   
			   //$global_limit = ''; 
			   break;
			  default: 
			   $order_sql = ''; 
			   $order_all = ''; 
		  }
	  }
	  
	  {
		  $product_limit2 = $product_limit; 
		  $product_limit = ''; 
		  
	  }
	  
	  $whereM = ' and (@WHEREM:=1) ';
	  
	  $search = $searchS = array(); 
	  
	  $c_from = $c_where = ''; 
	  if (!empty($f_cats)) {
		  $cats_ids = array($vm_cat_id); 
		  $c_from = ', `#__virtuemart_product_categories` as vpc '; 
		  $c_where = ' ( (`vpc`.`virtuemart_category_id` IN ('.implode(',', $cats_ids).') ) and (`vpc`.`virtuemart_product_id` = `p`.`virtuemart_product_id`) ) and '; 
	  }
	  
	  
		  $stock .= $c_where; 
	  
	  
	  if (!defined('VMLANG')) define('VMLANG', 'en_gb'); 
		  // search product sku
		  $q = " select p.`virtuemart_product_id`, p.`product_parent_id`".$order_sql; 
		  if (!empty($child_handling))
		  $q .= ", (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l ".$c_from; 
		  $q .= " where ".$stock." p.`product_sku` LIKE '".$db->escape($keyword)."' and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` ".$whereM." ".$order_single." ".$product_limit."\n"; 
		  
		  $searchS['PRODUCT_SKU'] = $search['PRODUCT_SKU'] = $q; 
		  
		  
		  
		  
		  if (mb_strlen($keyword)>3)
		  { 
		  //$q = " union ( "; 
		  $q .= " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .= ", (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  #__virtuemart_products as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
		  $q .= " from #__virtuemart_products AS p, #__virtuemart_products_".VMLANG." as l ".$c_from; 
		  $q .= " where p.product_sku LIKE '%".$db->escape($keyword)."%' and p.published = '1' and p.virtuemart_product_id = l.virtuemart_product_id ".$whereM.$order_single." ".$product_limit."\n"; 
		  //$q.= " ) "."\n"; 
		  
		   $search['PRODUCT_SKU_PARTIAL'] = $searchS['PRODUCT_SKU_PARTIAL'] = $q; 
		  
		  
		  }
		  
		  // search product name
		  
		  // exact match: 
	      //$q .= " union ("; 
		  $q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .=" , (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.product_parent_id = p.`virtuemart_product_id`) as children "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l ".$c_from; 
		  $q .= " where ".$stock." (l.`product_name` LIKE '".$db->escape($keyword)."' ) and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		  //$q .= " )"."\n";
		  
		  $search['PRODUCT_NAME'] = $searchS['PRODUCT_NAME']  = array(); 
		  $search['PRODUCT_NAME'][] = $q; 
		  $searchS['PRODUCT_NAME'][] = $q; 
		  
		  
		   $qz = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $qz .=" , (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
		  $qz .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l ".$c_from; 
		  $qz .= " where ".$stock." ( l.`product_name` LIKE '".$db->escape($keyword)."%')  and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		   
		  $search['PRODUCT_NAME'][] = $qz; 
		  $searchS['PRODUCT_NAME'][] = $qz; 
		  
		   $qz = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $qz .=" , (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
		  $qz .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l ".$c_from; 
		  $qz .= " where ".$stock." l.`product_name` LIKE '% ".$db->escape($keyword)." %' and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		   
		  $search['PRODUCT_NAME'][] = $qz; 
		  $searchS['PRODUCT_NAME'][] = $qz; 
		  
		  
		  
		  
		   // search attribs
		   $ft = $params->get('use_fulltext', false); 
		   if (!empty($ft)) { 
		   
		   
		   $q = " select distinct p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .=" , (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l, `#__virtuemart_product_customfields` as cf ".$c_from; 
		  $q .= " where ".$stock." (MATCH (cf.`customfield_value`) AGAINST('".$db->escape($keyword)."' IN BOOLEAN MODE) ) and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` and cf.`virtuemart_product_id` = p.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		   
		   
		   
		   }
		   else
		   {
		   
	      
		  $q = " select distinct p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .=" , (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l, `#__virtuemart_product_customfields` as cf ".$c_from; 
		  $q .= " where ".$stock." (cf.`customfield_value` LIKE '%".$db->escape($keyword)."%' ) and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` and cf.`virtuemart_product_id` = p.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		  //$q .= " )"."\n";
		   }
		   
		  $search['PRODUCT_ATTRIBS'] = $q;
		  $searchS['PRODUCT_ATTRIBS'] = $q;
		  
		  if ((!defined('VM_VERSION')) || (VM_VERSION < 3)) {
		   unset($search['PRODUCT_ATTRIBS']); 
		  }
		  
		  
		 // search manufacturer name
$q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql;
 if (!empty($child_handling))
		  $q .= " , (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
           $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_product_manufacturers` as pm, `#__virtuemart_manufacturers_".VMLANG."` as m ".$c_from;
           $q .= " where ".$stock." (m.`mf_name` LIKE '".$db->escape($keyword)."%'  ) and p.`published` = '1' and p.`virtuemart_product_id` = pm.`virtuemart_product_id` and pm.`virtuemart_manufacturer_id` = m.`virtuemart_manufacturer_id` ".$whereM.$order_single." ".$product_limit."\n"; 
           

           $search['MF_NAME'] =  $searchS['MF_NAME']= $q;
		   
		   
		   
		    // search manufacturer name
$q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql;
 if (!empty($child_handling))
		  $q .= " , (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
           $q .= " from `#__virtuemart_products` AS p,  `#__virtuemart_product_categories` as pc,  `#__virtuemart_categories_".VMLANG."` as cl, `#__virtuemart_categories` as c ".$c_from;


           $q .= " where ".$stock." (cl.`category_name` LIKE '".$db->escape($keyword)."%'  ) and (c.`published` = '1') and (p.`published` = '1') and (p.`virtuemart_product_id` = pc.`virtuemart_product_id`) and (cl.`virtuemart_category_id` = c.`virtuemart_category_id`) and (pc.`virtuemart_category_id` = c.`virtuemart_category_id`) ".$whereM.$order_single." ".$product_limit."\n"; 
           

           $search['CAT_NAME'] = $searchS['CAT_NAME'] = $q;
		   
		   
		    // search manufacturer name
$q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql;
 if (!empty($child_handling))
		  $q .= " , (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
           $q .= " from `#__virtuemart_products` AS p,  `#__virtuemart_product_categories` as pc,  `#__virtuemart_categories_".VMLANG."` as cl, `#__virtuemart_categories` as c, `#__virtuemart_category_categories` as cp ".$c_from;


           $q .= " where ".$stock." ((cl.`category_name` LIKE '".$db->escape($keyword)."%'  ) and (c.`published` = '1') and (p.`published` = '1') and (p.`virtuemart_product_id` = pc.`virtuemart_product_id`) and ((cl.`virtuemart_category_id` = c.`virtuemart_category_id`) )) ".$whereM.$order_single." ".$product_limit."\n"; 
           

           $search['CAT_NAME_PATH'] = $searchS['CAT_NAME_PATH']= $q;
		  
		  
		  // search product name for multi words
		  $search['PRODUCT_NAME_WORDS'] = array(); 
		  $searchS['PRODUCT_NAME_WORDS'] = array(); 
		  
		  if (!empty($or))
		  {
			   $q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		 if (!empty($child_handling))
		  $q .= ", (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children  "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l ".$c_from; 
		  $q .= " where ".$stock." (l.`product_name` LIKE ' ".$db->escape($keyword)." ') and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` "; 
		  // the search from exact match: 
		  $q .= " ".$whereM.$order_single." ".$product_limit."\n"; 
		  
		  
		  $search['PRODUCT_NAME_WORDS'][] = $q;
		  //simplified: 
		  $searchS['PRODUCT_NAME_WORDS'][] = $q;

			 
	     
		  $q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .= ", (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children  "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l ".$c_from; 
		  $q .= " where ".$stock." (0 ".$or.") and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` "; 
		  // the search from exact match: 
		  $q .= " ".$whereM.$order_single." ".$product_limit."\n"; 
		  
		  
		  $search['PRODUCT_NAME_WORDS'][] = $q;
		  $searchS['PRODUCT_NAME_WORDS'][] = $q;
		  //simplified: 
		  
		  
		 
		  
		  }
		  else
		  {
			  
			   $q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .=" , (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l ".$c_from; 
		  $q .= " where ".$stock." l.`product_name` LIKE '% ".$db->escape($keyword)." %' and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` "; 
		  $q .= $whereM.$order_single." ".$product_limit."\n"; 

		   $search['PRODUCT_NAME_WORDS'][] = $q;
		   $searchS['PRODUCT_NAME_WORDS'][] = $q;
			  
			  $q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .=" , (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l ".$c_from; 
		  $q .= " where ".$stock." l.`product_name` LIKE '%".$db->escape($keyword)."%' and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` "; 
		  $q .= $whereM.$order_single." ".$product_limit."\n"; 

		   $search['PRODUCT_NAME_WORDS'][] = $q;
		   $searchS['PRODUCT_NAME_WORDS'][] = $q;
			
			  
		  
		  }
		  
		  $keyword2 = ''; 
		  
		  if (empty($no_short)) { 
		  if ((mb_strlen($keyword)>6) && (mb_strlen($keyword)<9))
		  {
			  $keyword2 = mb_substr($keyword, 0, -2); 
		  }
		 
			  if ((mb_strlen($keyword)>8))
			  {
				  $keyword2 = mb_substr($keyword, 0, -3); 
			  }
		  else
		  {
			   $keyword2 = mb_substr($keyword, 0, -1); 
		  }
		  }
		  
		  if (!empty($keyword2))
		  {
			  // search product name for multi words
	      //$q .= " union ( "; 
		  $q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .= ", (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children  "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l ".$c_from; 
		  $q .= " where ".$stock." (l.`product_name` LIKE '%".$db->escape($keyword2)."%' ".$or.") and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		  //$q .= " )"."\n";
		  
		   $search['PRODUCT_NAME_MULTI_WORDS'] = $q;
		   $searchS['PRODUCT_NAME_MULTI_WORDS'] = $q;
		  }
		  
		  
		 
		  // product SKU starts with... 
		  //$q .= " union ( "; 
		  $q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .= ", (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l".$c_from;
		  $q .= " where ".$stock." p.`product_sku` LIKE '".$db->escape($ko)."%' and p.`published` = '1'  and p.`virtuemart_product_id` = l.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		  //$q .= " )"."\n";
		   $search['PRODUCT_SKU_STARTS_WITH'] = $q;
		   $searchS['PRODUCT_SKU_STARTS_WITH'] = $q;
		   
		   
		     // product SKU ends with... 
		  
		    $q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .= ", (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l".$c_from;
		  $q .= " where ".$stock." p.`product_sku` LIKE '%".$db->escape($ko)."' and p.`published` = '1'  and p.`virtuemart_product_id` = l.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		  //$q .= " )"."\n";
		   $search['PRODUCT_SKU_ENDS'] = $q;
		   $searchS['PRODUCT_SKU_ENDS'] = $q;
		   
		  
		  
		  
		  $x = JRequest::getInt('search_desc', 1); 
		  
		  $optional_search = $params->get('optional_search', 2); 
		  if ($optional_search === 2) $x = true; 
		  else 
		  if (($optional_search === 1) && (!empty($x))) $x = true; 
		  if (empty($optional_search)) $x = 0; 
		  
		  
		  //to set desc search: 
		  if ((!empty($x)) || (in_array('PRODUCT_DESC', $priorities)))
		  {
		  // product desc includes the phrase
		  //$q .= " union ( "; 
		  
		  $ft = $params->get('use_fulltext', false); 
		   if (!empty($ft)) { 
		   $q = "select p.`virtuemart_product_id`, p.`product_parent_id`".$order_sql; 
		  if (!empty($child_handling))
		  $q .= ", (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children  "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l ".$c_from; 
		  $q .= " where ".$stock." (MATCH (l.`product_desc`) AGAINST('".$db->escape($keyword)."' IN BOOLEAN MODE) ) and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		   }
		   else
		   {
		  $q = "select p.`virtuemart_product_id`, p.`product_parent_id`".$order_sql; 
		  if (!empty($child_handling))
		  $q .= ", (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children  "; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l ".$c_from; 
		  $q .= " where ".$stock." (l.`product_desc` LIKE '%".$db->escape($keyword)."%' ".$or2." ) and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		  //$q = " )"."\n";
		   }
		   
		  $search['PRODUCT_DESC'] = $q;
		  $searchS['PRODUCT_DESC'] = $q;
		  }
		  if ((!empty($x)) || (in_array('PRODUCT_S_DESC', $priorities))) {
		  $ft = $params->get('use_fulltext', false); 
		   if (!empty($ft)) { 
		   
		    $q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .= ", (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children"; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l".$c_from; 
		  
		  $q .= " where ".$stock." (MATCH (l.`product_s_desc`) AGAINST('".$db->escape($keyword)."' IN BOOLEAN MODE) ) and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		   
		   
		   
		   
		   }
		   else
		   {
		  
		  
		  $q = " select p.`virtuemart_product_id`, p.`product_parent_id` ".$order_sql; 
		  if (!empty($child_handling))
		  $q .= ", (select  GROUP_CONCAT(pp.`virtuemart_product_id`) from  `#__virtuemart_products` as pp where pp.`product_parent_id` = p.`virtuemart_product_id`) as children"; 
		  $q .= " from `#__virtuemart_products` AS p, `#__virtuemart_products_".VMLANG."` as l".$c_from; 
		  
		  $q .= " where ".$stock." (l.`product_s_desc` LIKE '%".$db->escape($keyword)."%' ".$or3.") and p.`published` = '1' and p.`virtuemart_product_id` = l.`virtuemart_product_id` ".$whereM.$order_single." ".$product_limit."\n"; 
		  
		  
		  
		  
		  }
		   $search['PRODUCT_S_DESC'] = $q;
		   $searchS['PRODUCT_S_DESC'] = $q;
		  }
		  if (!empty($order_all)) $q .= $order_all; 
		  $debug = $params->get('debug', false); 
		  
		  
		 
		  
		 
		  $f = true; 
		  $q = ''; 
		  /*
		  if (!empty($order_by)) {
		  $q = 'select 0 as virtuemart_product_id, 0 as product_parent_id '.$zero_select; 
		  $prods++; 
		  if (!empty($global_limi)) {
		    $global_limit = " LIMIT 0,".$prods.' ';
		  }
		  $f = false; 
		  }
		  */
		  
		  //foreach ($search as $key=>$val)
		  $singl = array();  $ret = array(); 
		  $row = 1000; 
		  $ids = array(); 
		  $px = $params->get('search_method', 0); 
		  $onlysku = true; // if the products are found only by searching SKU, ignore child handling and display childs as well
		  if (!empty($px)) {
			   foreach ($priorities as $k=>$v) {
				   
				   $issku = false; 
				   
				   if (stripos($v, 'PRODUCT_SKU')===0) {
					   $issku = true; 
				   }
				   
			   $row++; 
			     $key = strtoupper(trim($v));
			     if (empty($search[$key])) continue; 
				 $val = $search[$key]; 
			  if (!is_array($val)) { 
			  $val = str_replace('{ROWPRIORITY}', $row, $val); 
			  $nw = ''; 
			  if (!empty($ids)) {
			    $nw = ' and  p.`virtuemart_product_id` NOT IN ('.implode(',', $ids).')'; 
			  }
			  $val = str_replace($whereM, $nw, $val); 
			  $val .= $order_single2.$product_limit2.$order_all.$global_limit; 
			    $qd = $val; 
		$rr = self::runQuery($db, $val, $debug, $key); 
			  
			    if (!empty($rr))
			    foreach ($rr as $kX=>$vX) {
					
					//we got results: 
					if (!$issku) $onlysku = false; 
					
					$pid = $vX['virtuemart_product_id'] = (int)$vX['virtuemart_product_id']; 
					$ids[$pid] = $pid; 
					$ret[] = $vX; 
					
					if (count($ids)>=$prods) {
						break 2; 
					}
					
				}
			  }
			  else
			  {
				  foreach ($val as $k2X => $val2) {
				     $row++; 
					 $val2 = str_replace('{ROWPRIORITY}', $row, $val2); 
					 $val2 .= $order_single2.$product_limit2.$order_all.$global_limit;
					 $nw = ''; 
			  if (!empty($ids)) {
			    $nw = ' and  p.`virtuemart_product_id` NOT IN ('.implode(',', $ids).')'; 
			  }
			  $val2 = str_replace($whereM, $nw, $val2); 
			  $qd = $val2; 
			  $rr = self::runQuery($db, $val2, $debug, $key.$k2X); 
						
			  
			    if (!empty($rr))
			    foreach ($rr as $kX=>$vX) {
					
					//we got results: 
					if (!$issku) $onlysku = false; 
					
					$pid = $vX['virtuemart_product_id'] = (int)$vX['virtuemart_product_id']; 
					$ids[$pid] = $pid; 
					$ret[] = $vX; 
					
					
					if (count($ids)>=$prods) {
						break 3; 
					}
					
				}
					 
				  }
			  }
			  
			  if (count($ids)>=$prods) {
			   break; 
			  }
			  
			   }
			   
			   
		  }
		  else {
		  
		  foreach ($priorities as $k=>$v)
		  {
			  $row++; 
			  $key = strtoupper(trim($v));
			  if (empty($search[$key])) continue; 
			  $val = $search[$key]; 
			  if (!is_array($val)) { 
			  $val = str_replace('{ROWPRIORITY}', $row, $val); 
			  $singl[] = $val; 
			  if ($f)
			  {
				  $q .= $val."\n"; 
				  $f = false; 
			  }
			  else
			  {
				  $q .= ' union ('.$val.$order_single2.$product_limit2.')'."\n"; 
			  }
			  $f = false; 
			  }
			  else
			  {
				  foreach ($val as $val2) {
				  $row++; 
				  $val2 = str_replace('{ROWPRIORITY}', $row, $val2); 
				  $singl[] = $val2; 
			  if ($f)
			  {
				  $q .= $val2."\n"; 
				  $f = false; 
			  }
			  else
			  {
				  $q .= ' union ('.$val2.$order_single2.$product_limit2.')'."\n"; 
			  }
			   $f = false; 
				  }
			 
			  }
		  }
		  if (!empty($order_all)) {
		   $q .= $order_all; 
		  }
		  if (count($priorities)>1) { 
	      $q .= $global_limit;
		  }
		
		 // echo $q;
		
		
		//echo $q; die(); 
		
				$qd = str_replace('#__', $db->getPrefix(), $q); 
		$start = microtime(true); 
		  $ret = self::runQuery($db, $q, $debug); 
		  
		$q = str_replace('#__', $db->getPrefix(), $q); 
			//echo $q; 
		
		if (!empty($debug)) { 
		
		$end = microtime(true); 
		  $len = $end - $start; 
		
		self::getDebugQuery($db, $q, $len, $singl); 
		
		}
		
		  }
		$debug = $params->get('debug', false); 
		
		
		if ($debug)
		{
		  $e = $db->getErrorMsg(); if (!empty($e)) { echo $e; die(); }
		 
		  
		}
		
		
		if (empty($ret)) return array(); 
		
		
		
		
		if ($popup) return $ret; 
		$proddb = array(); 
		foreach ($ret as $key=>$val)
		 {
		 
		 
		 
		
		/*
		COM_ONEPAGE_XML_CHILDPRODUCTS_HANDLING_OPT1="Include both child and parent products"
		COM_ONEPAGE_XML_CHILDPRODUCTS_HANDLING_OPT2="Include only child products and products without child products (skip parent products)"
		COM_ONEPAGE_XML_CHILDPRODUCTS_HANDLING_OPT3="Include only parent products"

		*/
		
		
		
		if ((!empty($child_handling)) && (!$onlysku))
		{

		$child_type = array(); 
		$child_type[] = 1; 
		//if (!empty($val['product_parent_id'])) $child_type[] = 2; 
		
		// has children, ie is parent: 
		if (!empty($val['children']) && (empty($val['product_parent_id']))) $child_type[] = 3; 
		
		if (!empty($val['children'])) $child_type[] = 3;
	    if (empty($val['product_parent_id'])) $child_type[] = 3; 
		
		
		// does not have children and is not a child product (it's parent product)
		if (empty($val['children']) && (empty($val['product_parent_id']))) $child_type[] = 3;
		// is parent with no children, same as above
		if ((empty($val['product_parent_id'])) && (empty($val['children']))) $child_type[] = 2; 
		// is child and does not have subchildren: 
		if ((!empty($val['product_parent_id'])) && (empty($val['children']))) $child_type[] = 2; 
		
		 if (!in_array($child_handling, $child_type))
		 {
		 
		 continue; 
		 }
		 
		}
		
		 
		 
		   $proddb[] = $val['virtuemart_product_id']; 
		 }
		 
		 
		 
		return $proddb; 
		
	}
	public static function runQuery(&$db, $q, $debug=true, $msg=''){
		$ret = array(); 
		$start = microtime(true); 
	try
		{
			$qd = str_replace('#__', $db->getPrefix(), $q); 
		$db->setQuery($q); 
		$ret = $db->loadAssocList(); 

		
		if (!empty($debug)) 
		{ 
		 $e = $db->getErrorMsg(); 
		 if (!empty($e)) 
		 {
		   echo $e; 
		   //echo '<br /><br /><b>query: </b><br /><br />'.$qd; 
		 }
		 
		 $end = microtime(true); 
		 $len = $end - $start; 
		 
		 //echo '<br /><br /><b>query: </b><br /><br />'.str_replace("\n", "<br />\n", $qd); 
		 $c = count($ret); 
		 //echo '<br />n.results: '.$c."<br />\n"; 
		 //echo '<br />time: '.$len."s<br />\n"; 
		 //if (!empty($msg)) echo '<br />'.$msg."<br />\n"; 
		 echo '<script>console.log(\'query\', \''.addslashes(json_encode($qd)).'\');</script>'; 
		}
		
		
		

		  
		  
		}
		catch (Exception $e)
		{
			
			if ($debug)
			{
			
			 echo '<br /><br /><b>error query: </b><br /><br />'.$qd; 
			$msg = (string)$e; 
			echo '<br /><br />Error: '.$msg.'<br />'; 
			die(); 
			}
		}
		return $ret; 
	}
	public static function getDebugQuery(&$db, $q, $len, $singl=array()) {
	
	$q2 = 'explain '.$q; 
		$db->setQuery($q2); 
		$expl = $db->loadAssocList(); 
		//JFactory::getApplication()->enqueueMessage($len.'s <br />'.$q.'<br />'.print_r($expl)); 
		$GLOBALS['opcsearchbench'] = array(); 
		$GLOBALS['opcsearchbench']['time'] = $len.'s'; 
		$GLOBALS['opcsearchbench']['query'] = $q; 
		$GLOBALS['opcsearchbench']['EXPLAIN'] = $expl; 
		foreach ( $singl as $k=>$q3)
		{
			$start = microtime(true); 
			$q2 = 'explain '.$q3; 
			$db->setQuery($q2); 
			$expl = $db->loadAssocList(); 
			$end = microtime(true); 
		    $len = $end - $start; 
			$GLOBALS['opcsearchbench']['EXPLAIN_'.$k] = $expl; 
			$GLOBALS['opcsearchbench']['EXPLAIN_'.$k]['query'] = $q3; 
			$GLOBALS['opcsearchbench']['EXPLAIN_'.$k]['len'] = $len.'s'; 
		}
	}
	// original code from shopfunctionsF::renderMail
	public static function getVMView(&$ref, $viewName, $vars=array(),$controllerName = NULL, $layout='default', $format='html')
	{
		
		if (!class_exists('CurrencyDisplay'))
	require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
	    $cache_file = $ref->cache_file; 
	    $originallayout = JRequest::getVar( 'layout' );
	  	if(!class_exists('VirtueMartControllerVirtuemart')) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'virtuemart.php');
// 		$format = (VmConfig::get('order_html_email',1)) ? 'html' : 'raw';
		
		// calling this resets the layout
		//$controller = new VirtueMartControllerVirtuemart();
		//JRequest::setVar( 'layout', $layout );
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'rupview.php'); 
		
	   $oldoption = JRequest::getVar('option'); 
	   //new: JRequest::setVar('option', 'com_virtuemart'); 

		
		$view = new rupSearch(); 
		foreach ($ref as $k=>$v)
		{
			if (empty($view->$k))
			$view->$k = $v; 
		}
		
		$view->viewName = 'category'; 
		//Todo, do we need that? refering to http://forum.virtuemart.net/index.php?topic=96318.msg317277#msg317277
		//$controller->addViewPath(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'views');
		
		//$view = $controller->getView($viewName, $format);
	  
	  $view->showproducts = true; 
	  $show_prices  = VmConfig::get('show_prices',1);
	  $view->show_prices = $show_prices; 
	  if (isset($ref->keyword))
	  {
	  $view->keyword = $ref->keyword; 
	  $view->search = $view->keyword; 
	  }
	  if (!isset($view->currency))
	  {
	  
	    $currency = CurrencyDisplay::getInstance( );
		$view->currency =& $currency;
	  }
	  
	  if (empty($view->category))
	  {
		  
				$view->category = new stdClass();
				$view->category->category_name = '';
				$view->category->category_description= '';
				$view->category->haschildren= false;
			
	  }
	  if (!isset($view->searchcustom))
	  {
	    $view->searchcustom = '';
		$view->searchCustomValues = '';
	  }
	  if (!isset($view->categoryId))
	  {
		  $view->categoryId = 0; 
	  }
	  if (!isset($view->perRow))
	  $view->perRow = VmConfig::get('products_per_row',3);
  
	  $view->viewName = 'category'; 
	  if (method_exists($view, 'setLayout'))
	  $view->setLayout('default'); 
	  else
	  if (method_exists($view, 'set'))
	  $view->set('layout', 'default');
	  else	  
	  if (method_exists($view, 'assignRef'))
	  $view->assignRef('layout', 'default'); 
	  else
	  $view->layout =  'default';

	   $tp = self::getTemplatePath('category'); 
	   
	   if (!empty($tp))
	   {
		if (method_exists($view, 'addTemplatePath'))
		{
	      $view->addTemplatePath($tp); 
		}
		else
		{
			if (method_exists($view, 'addIncludePath'))
			{
			$view->addIncludePath($tp); 
			}
		}
		
		
	   }
	   
	   
	 
	  if (method_exists($view, 'set'))
	  $view->set('format', 'html');
	  else	  
	  if (method_exists($view, 'assignRef'))
	  $view->assignRef('format', 'html'); 
	  else
	  $view->format = 'html'; 

		
		//$view->setLayout($layout); 
		
		if (!$controllerName) $controllerName = $viewName;
		$controllerClassName = 'VirtueMartController'.ucfirst ($controllerName) ;
		if (!class_exists($controllerClassName)) require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$controllerName.'.php');

		//Todo, do we need that? refering to http://forum.virtuemart.net/index.php?topic=96318.msg317277#msg317277
		
		
		
		
		$path = $view->getPath('default', 'default'); 
	    
		foreach ($vars as $key => $val) {
	
		
			$view->$key = $val;
			//$view->assignRef($key, $val); 
		}
		$view->search = null; 
		$view->keyword = ''; 
		if (isset($view->category) && (is_object($view->category)))
		$view->category->haschildren = false; 
		//$count = count($ids); 
		$prods = JRequest::getInt('prods', 5); 
		$cc = count($vars['products']); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'pagination.php'); 
		$rp = new rupPagination($cc, 0, $prods , $vars['perRow'] );
		$vars['vmPagination'] = $rp;
		
		$view->vmPagination = $vars['vmPagination']; 
		$view->showRating = false; 
		$view->showBasePrice = false; 
		$view->showcategory = 0; 
		$productsLayout = VmConfig::get('productsublayout','products');
		if(empty($productsLayout)) $productsLayout = 'products';
		$view->productsLayout = $productsLayout; 
		//new: $cat = JRequest::getVar('virtuemart_category_id', null); 
		//new: JRequest::setVar('virtuemart_category_id', false); 
		
		$html3 = ''; 
		 if (empty($vars['products']))
	  {
		  
		  if (method_exists($view, 'assignRef'))
		  {
			  $f = false; 
		  $view->assignRef('showproducts', $f); 
		  $vars['showproducts'] = false; 
		  }
		  else
		  {
		  $view->showproducts = false; 
		  $vars['showproducts'] = false; 
		  }
	  
	  
	     $html3 = JText::_ ('COM_VIRTUEMART_NO_RESULT');
	  }
	  $vars['keyword'] = ''; 
	  foreach ($vars as $key => $val) {
	
		
			$view->$key = $val;
			//$view->assignRef($key, $val); 
		}
	  	 
		$orderByList = array(); 
	   $orderByList['manufacturer'] = ''; 
	   $orderByList['orderby'] = ''; 
	   
	     $view->orderByList['orderby'] = ''; 
	     $view->orderByList['manufacturer'] = ''; 
	   
	   if (method_exists($view, 'assignRef'))
	   {
		   $view->assignRef('orderByList', $orderByList); 
	   }
		
		
		ob_start(); 
		$view->loadYag(); 
		$html = $view->display();
		$html2 = ob_get_clean(); 
		//new: JRequest::setVar('virtuemart_category_id', $cat); 
		//new: JRequest::setVar( 'layout', $originallayout );
		
		 
	    //new:  JRequest::setVar('option', $oldoption); 
		
		$ret = $html.$html2.$html3; 
		return $ret; 
	}
	
	public static function getTemplatePath($viewName)
	{ 
	  if(!class_exists('shopFunctionsF'))require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php');
	  if (method_exists('shopFunctionsF', 'loadVmTemplateStyle'))
	  {
	  //$template = shopFunctionsF::loadVmTemplateStyle(); 

	  }
	  //else
	  {
	  $vmtemplate = VmConfig::get('vmtemplate','default');
	  $vmtemplate = VmConfig::get('categorytemplate', $vmtemplate);

		if(($vmtemplate=='default') || (empty($vmtemplate))) {
			
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home` <> "0"';
			
			
			$db = JFactory::getDbo();
			$db->setQuery($q);
			$template = $db->loadResult(); 
			  
			
			
		} else {
			
			if (is_numeric($vmtemplate)) {
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `id`="'.(int)$vmtemplate.'" ';
			    $db = JFactory::getDbo();
			    $db->setQuery($q);
			    $vmtemplate = $db->loadResult();
			}
			
			$template = $vmtemplate;
		}
	  }
	  
	  
	

		if($template){
			//$this->addTemplatePath(JPATH_ROOT.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName);
			$tp = JPATH_ROOT.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName;
			
			if (file_exists($tp)) return $tp; 
			}
	}
	
}	

