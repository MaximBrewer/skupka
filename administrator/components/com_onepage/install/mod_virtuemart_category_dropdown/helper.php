<?php
defined('_JEXEC') or die;

class modVirtuemartCategorydropdownHelper {
   
   
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
   public static function getCats($cat_id=0, $selected=0, &$hasResults, $level=0, $module_id=0)
   {
		$hasResults = false; 
	   $ret2 = ''; 
	   
	   
	  $cat_id = (int)$cat_id; 
	  
	 
	    {
		
		   $list = modVirtuemartCategorydropdownHelper::getTopCats($cat_id); 
		   
		   
		  $retA = array(); 
		  $ret = ''; 
		    $myid = ''; 
		    $mid = JRequest::getVar('my_item_id', ''); 
			if (!empty($mid))
		    $myid = '&my_item_id='.$mid; 
			//if (!empty($cat_id))
			{
				
			// $level = JRequest::getVar('level', 0); 
			// $level = (int)$level; 
			// $level++; 
			 /*
			 $key = 'MOD_VIRTUEMART_CATEGORY_DROPDOWN_LEVEL2'; 
			 if (!empty($level))
			 {
				 $key = 'MOD_VIRTUEMART_CATEGORY_DROPDOWN_LEVEL'.$level; 
			 }
			 */
			 
			 $key = self::getLevelLabel($level, $module_id); 
			 $ret = '<option>'.JText::_($key).'</option>'; 
			 $retA[] = $ret; 
			}
		   if (!empty($list))
				{
				   foreach ($list as $rid => $row)
				     {
					    $ret = '<option '; 
						if (!empty($selected))
						{
							$selected = (int)$selected; 
							$cat_id = (int)$row['virtuemart_category_id']; 
							if ($selected === $cat_id)
							{
								$ret .= ' selected="selected" '; 
							}
							
							
						}
						$ret .= ' value="'.$row['virtuemart_category_id'].'" rel="'.JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$row['virtuemart_category_id'].$myid).'">'.$row['category_name'].'</option>'; 
						$retA[] = $ret; 
					 }
					 $hasResults = true; 
				}
				
				
		  
		}
		if ((empty($cat_id) && (!$hasResults)))
		{
			
			//$ret = '<option>'.JText::_('MOD_VIRTUEMART_CATEGORY_DROPDOWN_LEVEL1').'</option>'; 
		}
		
		if (empty($ret))
		{
			//$ret = '<option class="no_subcat_here">'.JText::_('MOD_VIRTUEMART_CATEGORY_DROPDOWN_NOMODELS').'</option>'; 
		}
		else
		{
			//$ret = $ret2.$ret; 
		}
		return $retA; 
   }
   
   public static function getLevelLabel($i=0, $module_id=0)
   {
	   $params = self::getParams($module_id); 
	   $key = 'level'.$i.'_text'; 
			$text = $params->get($key, ''); 
			if (empty($text)) $text = JText::_('MOD_VIRTUEMART_CATEGORY_DROPDOWN_LEVEL'.$i);
			$text = JText::_($text); 
			return $text; 
   }
   public static function getProductsQuery($cat_id, $module_id) {
	   if (empty($module_id)) {
	    $module_id = JRequest::getInt('module_id', 0); 
	   }
	   self::setVMLANG(); 
	   
	   $params = modVirtuemartCategorydropdownHelper::getParams($module_id); 
	   $all = $params->get('showallproducts', false); 
	   $cat_id = (int)$cat_id; 
	   $db = JFactory::getDBO(); 
	   if (empty($all)) {
	  
     	 
		  
		  $q = 'select l.product_name as product_name, r.virtuemart_product_id from #__virtuemart_products_'.VMLANG.' as l, #__virtuemart_product_categories as r, #__virtuemart_products as p where l.virtuemart_product_id = r.virtuemart_product_id and p.virtuemart_product_id = l.virtuemart_product_id and p.published = 1 and r.virtuemart_category_id = '.$cat_id.' order by REPLACE(l.product_name, "&#39;", "\'") asc'; 
		   
		  $db->setQuery($q); 
		   
		   $list = $db->loadAssocList(); 
		   return $list; 
	   }
	   
	  
	   $dcats = array(); 
	   $dcats[$cat_id] = $cat_id;  
	   if (!empty($all)) {
		   
		    $q = 'select `category_child_id` from `#__virtuemart_category_categories` where `category_parent_id` = '.$cat_id; 
			$db->setQuery($q); 
			$res = $db->loadAssocList(); 
			
			if (!empty($res)) {
			foreach ($res as $row)
			{
				$c = (int)$row['category_child_id']; 
				$dcats[$c] = $c; 
				$next[$c] = $c; 
			}

		   
	      for($i=0; $i<=10; $i++) {
		    $q = 'select `category_child_id` from `#__virtuemart_category_categories` as cf, `#__virtuemart_categories` as c where c.virtuemart_category_id = cf.category_parent_id and c.published = 1 and `category_parent_id` IN ('.implode(', ', $next).')'; 
			$db->setQuery($q); 
			$res = $db->loadAssocList(); 
			$next = array(); 
			if (empty($res)) break; 
			foreach ($res as $row)
			{
				$c = (int)$row['category_child_id']; 
				$dcats[$c] = $c; 
				$next[$c] = $c; 
				
			}
		  }
			}
		  
		  if (!empty($dcats)) {
		    $q = 'select distinct l.`product_name` as product_name, r.virtuemart_product_id from #__virtuemart_products_'.VMLANG.' as l, #__virtuemart_product_categories as r, #__virtuemart_products as p where l.virtuemart_product_id = r.virtuemart_product_id and p.virtuemart_product_id = l.virtuemart_product_id and p.published = 1 and r.virtuemart_category_id IN ('.implode(', ', $dcats).') order by REPLACE(l.product_name, "&#39;", "\'") asc'; 
		  }
		  else
		  {
			 $q = 'select `category_child_id` from `#__virtuemart_category_categories` where `category_parent_id` = '.$cat_id; 
		  }
		  $db->setQuery($q); 
		   
		   $list = $db->loadAssocList(); 
		   
		  
		   return $list; 
		
	   }
	   

   }
   public static function getProducts($cat_id=0, $prod_id=0, $module_id=0)
   {
		
	   $ret2 = ''; 
	   $retA = array(); 
	   if (empty($cat_id))
	   $cat_id = JRequest::getVar('ict', 0); 
	  $cat_id = (int)$cat_id; 
	  
	  $maxlevel = JRequest::getVar('maxlevel', 0); 
	   $level = JRequest::getVar('level', 0); 
	  
	  if (!empty($cat_id))
	    {
		
		
			
		   $list = modVirtuemartCategorydropdownHelper::getProductsQuery($cat_id, $module_id); 
		   
		  $ret = ''; 
		    $myid = ''; 
		    $mid = JRequest::getVar('my_item_id', ''); 
			if (!empty($mid))
		    $myid = '&my_item_id='.$mid; 
			 $label = JText::_('MOD_VIRTUEMART_CATEGORY_DROPDOWN_CHOOSE_PRODUCT'); //self::getLevelLabel($level, $module_id); 
			 $ret2 = '<option>'.$label.'</option>'; 
			 $retA[] = $ret2; 
			 
		   if (!empty($list))
				{
				   foreach ($list as $rid => $row)
				     {
					    $ret = '<option class="is_product_reply" '; 
						if ($row['virtuemart_product_id'] == $prod_id) $ret .= ' selected="selected" '; 
						$ret .= ' value="'.$row['virtuemart_product_id'].'" rel="'.JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$row['virtuemart_product_id'].'&virtuemart_category_id='.$cat_id.$myid).'">'.$row['product_name'].'</option>'; 
						$retA[] = $ret; 
					 }
				}
		  
		}
		else
		{
			 //$label = self::getLevelLabel(1, $module_id); 
			 $label = JText::_('MOD_VIRTUEMART_CATEGORY_DROPDOWN_CHOOSE_PRODUCT'); //self::getLevelLabel($level, $module_id); 
			$ret = '<option>'.$label.'</option>'; 
			$retA[] = $ret; 
		}
		
		if (empty($retA))
		{
			$ret = '<option>'.JText::_('MOD_VIRTUEMART_CATEGORY_DROPDOWN_NOMODELS').'</option>'; 
			$retA[] = $ret; 
		}
		else
		{
			//$ret = $ret2.$ret; 
		}

		return $retA; 
   }
   public static function getLangCode()
 {
	 $langO = JFactory::getLanguage();
			$lang = JRequest::getVar('lang', ''); 
			$locales = $langO->getLocale();
		$tag = $langO->getTag(); 
		$app = JFactory::getApplication(); 		
		
		
		if (class_exists('JLanguageHelper') && (method_exists('JLanguageHelper', 'getLanguages')))
		{
		$sefs 		= JLanguageHelper::getLanguages('sef');
		foreach ($sefs as $k=>$v)
		{
			if ($v->lang_code == $tag)
			if (isset($v->sef)) 
			{
				$ret = $v->sef; 

				return $ret; 
			}
		}
		}
		
		
		
			 if ( version_compare( JVERSION, '3.0', '<' ) == 1) {       
			if (isset($locales[6]) && (strlen($locales[6])==2))
			{
				$action_url .= '&amp;lang='.$locales[6]; 
				$lang = $locales[6]; 
				return $lang; 
			}
			else
			if (!empty($locales[4]))
			{
				$lang = $locales[4]; 
				
				if (stripos($lang, '_')!==false)
				{
					$la = explode('_', $lang); 
					$lang = $la[1]; 
					if (stripos($lang, '.')!==false)
					{
						$la2 = explode('.', $lang); 
						$lang = strtolower($la2[0]); 
					}
				
					
				}
		     	return $lang; 
			}
			else
			{
				return $lang; 
			
			}
			 }
			return $lang; 
 }
   public static function getAjax()
    { 
	  
	  $ret = ''; 
	  modVirtuemartCategorydropdownHelper::loadVm();
	  
	  $selected = 0; 
	  
	  
	  $level = JRequest::getVar('level', 0); 
	  $maxlevel = JRequest::getVar('maxlevel', 0); 
	  
	  //if ($level === $maxlevel)
	  {
		  $ret2 = $productsHtml = modVirtuemartCategorydropdownHelper::getProducts(); 
	  }
	  //else
	  {
	  $catHtml = array(); 
	  $catId = JRequest::getVar('ict', 0); 
	  if (!empty($catId))
	  {
		  $hasResults = false; 
		  $level = (int)$level; 
		  $level++; 
		  $ret = $catHtml = modVirtuemartCategorydropdownHelper::getCats($catId, $selected, $hasResults, $level); 
		  
		  $showProducts = JRequest::getVar('showProducts', 1); 
		  
		  if (!empty($showProducts))
		  if (empty($hasResults))
		  {
			  
			 // $ret2 = modVirtuemartCategorydropdownHelper::getProducts(); 
			  
			  
		  }
	  }
	  }
	  
	  
			   $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); 
		@header('Content-Type: text/html; charset=utf-8');
		@header("HTTP/1.1 200 OK");
		@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		@header("Cache-Control: no-cache");
		@header("Pragma: no-cache");
		
		$retA = array(); 
		$retA['products'] = $productsHtml; 
		$retA['cats'] = $catHtml; 
		$retA['level_txt'] = array(); 
		$params = modVirtuemartCategorydropdownHelper::getParams(); 
		for ($i=1; $i<=10; $i++)
		{
			
			$key = 'level'.$i.'_text'; 
			$text = $params->get($key, ''); 
			if (empty($text)) $text = JText::_('MOD_VIRTUEMART_CATEGORY_DROPDOWN_LEVEL'.$i);
			$text = JText::_($text); 
			$retA['level_txt'][$i] = array( '<option>'.$text.'</option>' ); 
		}
		/*
		if (!empty($ret2))
		{
		 @header("isProducts: 1"); 
		 echo $ret2; 
		}
		else
		{
		 echo $ret; 
		}
		*/
		echo json_encode($retA); 
		JFactory::getApplication()->close(); 
	    die(''); 
	}
	
	public static function getTopCats($top=0)
	{
		
		$top = (int)$top; 
		$db = JFactory::getDbo();
		
		
		$q = "SELECT a.category_child_id, b.virtuemart_category_id, b.category_name FROM #__virtuemart_category_categories AS a, #__virtuemart_categories as c,  #__virtuemart_categories_".VMLANG." AS b WHERE a.category_child_id = b.virtuemart_category_id and a.category_parent_id = ".$top." and c.virtuemart_category_id = b.virtuemart_category_id and c.published = 1 ORDER BY b.category_name ASC";

		$db->setQuery($q);
		$result = $db->loadAssocList(); 
		
		//echo $q; die(); 
		return $result; 
	}
	public static function getParams($module_id=0)
	{
		if (empty($module_id))  {
		  $module_id = JRequest::getInt('module_id', 0); 
		}
		static $paramsC; 
		if (!empty($paramsC[$module_id])) return $paramsC[$module_id]; 
		$db = JFactory::getDBO(); 
		if (!empty($module_id)) {
		
		$q = 'select `params` from `#__modules` where module = "mod_virtuemart_category_dropdown" and published = 1 and id =  '.(int)$module_id; 
		$db->setQuery($q); 
		$res = $db->loadResult(); 
		}
		if (empty($res)) {
		 $q = 'select `params` from `#__modules` where module = "mod_virtuemart_category_dropdown" and published = 1 limit 0,1'; 
		 $db->setQuery($q); 
		 $res = $db->loadResult(); 
		
		}
		
     if (!empty($res)) {
	  $pstring = $res;  
	}
	else
	{
		$pstring = json_encode(''); 
	}
	
	$params = $paramsC[$module_id] = new JRegistry($pstring); 
	
	return $params; 
		
		
	}
	public static function getD($params)
	{
		$c = 0;
		for ($i=1; $i<=5; $i++) { 
	$key = 'level'.$i.'_text'; 
	
	  
	$text = $params->get($key, ''); 
	
	if (empty($text)) break; 
	$c++;
		}
		return $c; 
	}
	
	
public static function getPath($id, $max=6)
{

  require_once(__DIR__.DIRECTORY_SEPARATOR.'com_virtuemart_helper.php'); 
  $h = categoryHelperDD::getInstance(); 
  $ret = $h->getCategoryRecurse($id, 0); 
  $ret = array_reverse($ret); 
  $z = array(); 
  foreach ($ret as $k=>$v)
  {
	  $zi = $k+1; 
	  $z['value'.$zi] = $v; 
  }
  return $z; 

	
	$id = (int)$id; 
	if (empty($id)) return array(); 
	$db = JFactory::getDBO(); 
		
		
		
		$q  = 'select '; 
		$qs = $qf = $qw = $or = array(); 
		
		$i2 = $max; 
		for ($i=1; $i<=$max; $i++)
		{
			$qs[$i] = ' level'.$i.'.`category_child_id` as value'.$i.' '; 
			$qf[$i] = ' #__virtuemart_category_categories as level'.$i; 
			
			$z = $i + 1; 
			if ($z <= $max)
			{
			$qw[$i] = ' level'.$i.'.category_parent_id = level'.$z.'.category_child_id ';  
			}
			else
			{
			  $qw[$i] = ' level'.$i.'.category_parent_id = 0 ';  
			}
		}
		$max2 = $max; 
		
		for ($i=1; $i<=$max2; $i++)
		for ($i2 = 1; $i2<=($max2 - $i)+1; $i2++)
		{
			 
			
			$z = $i + 1; 
			$mm = ($max2 - $i2) + 1; 
			if ($z <= $mm)
			{
			$or[$i2][$i] = ' level'.$i.'.category_parent_id = level'.$z.'.category_child_id ';  
			}
			else
			{
			  $or[$i2][$i] = ' level'.$i.'.category_parent_id = 0 ';  
			}
		}
	
	    $w = array(); 
		foreach ($or as $i=>$next)
		{
		$w[] = '( '.implode(' and ', $next).' ) '; 
		
		
		
		
		}
		$w2 = ' ( '.implode(' or ', $w).' ) '; 
		
		
		$q = 'select '.implode(', ',$qs).' from '.implode(', ', $qf).' where (level1.`category_child_id` = '.$id.') and '.$w2.'   limit 0,1'; 
		
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		return $res; 
}	

	


	public static function getDepth()
	{
		$db = JFactory::getDBO(); 
		
		
		
		$q  = 'select '; 
		$qs = $qf = $qw = array(); 
		$max = 6; 
		for ($i=1; $i<=$max; $i++)
		{
			$qs[$i] = ' level'.$i.'.`category_child_id` '; 
			$qf[$i] = ' #__virtuemart_category_categories as level'.$i; 
			
			$z = $i + 1; 
			if ($z <= $max)
			{
			$qw[$i] = ' level'.$i.'.category_parent_id = level'.$z.'.category_child_id ';  
			}
			else
			{
			  $qw[$i] = ' level'.$i.'.category_parent_id = 0 ';  
			}
		}
		
		$q = 'select '.implode(', ',$qs).' from '.implode(', ', $qf).' where '.implode(' and ', $qw).' limit 0,1'; 
		
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		
		$q = 'select 
		level1.category_child_id, 
		level2.category_child_id, 
		level3.category_child_id,
		level4.category_child_id,
		level5.category_child_id		
		from 
		#__virtuemart_category_categories as level1, 
		#__virtuemart_category_categories as level2, 
		#__virtuemart_category_categories as level3,
		#__virtuemart_category_categories as level4, 
		#__virtuemart_category_categories as level5 
		where 
		level1.category_parent_id = level2.category_child_id
		and
		level2.category_parent_id = level3.category_child_id
		and 
		level3.category_parent_id = level4.category_child_id
		and
		level5.category_parent_id = 0
		limit 0,1
		'; 
	}
	
	public static function loadVm()
	{
	   $lang = JFactory::getLanguage();
	   $language_tag = $lang->getTag();
	   
//stAn, include VM: 
if (!class_exists('VmConfig'))
{
   if (!class_exists('VmConfig'))
		  require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
		 
		  VmConfig::loadConfig(true); 
		  if (method_exists('VmConfig', 'loadJLang'))
		  {
		  VmConfig::loadJLang('com_virtuemart',TRUE);
		  VmConfig::loadJLang('com_virtuemart_orders',TRUE);
		  }
		  
		  if (!defined('VMLANG') && (isset(VmConfig::$vmlang))) {
		    define('VMLANG', VmConfig::$vmlang); 
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
JFactory::getLanguage()->load('mod_virtuemart_category_dropdown'); 
JFactory::getLanguage()->load('mod_virtuemart_category_dropdown', JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_virtuemart_category_dropdown'); 

	}
}