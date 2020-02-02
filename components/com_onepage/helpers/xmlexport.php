<?php
/**
 * Controller for the OPC ajax and checkout
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
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

class OPCXmlExport  {
 public static $classes; 
 public static $config; 
 public static $cats; 
 public static $maxreached;
 public static function addClass($className, $config, $xml, $entity='')
  {
		if (!class_exists('VmConfig')) {
		  
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		  VmConfig::loadConfig(); 

		}
	  
        if (!class_exists($className)) return; 
		$class = new $className($config, $xml); 
		$class->config = $config; 
		
		
		if (empty($entity))
		$entity = strtolower(substr($className, 0, -4)); 
		
		$xmlfile = $entity; 
		
		$default = array(); 
		//$ic = OPCconfig::getValue('tracking_config_pairing', 'custom_pairing', null, $default); 					  
		
	
		$class->entity = $entity; 
		$class->xml = $xml; 
		$class->writer = new OPCwriter($config->xmlpath); 
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'xmlexport.php'); 
		$class->general = new stdClass(); 
		$JModelXmlexport = new JModelXmlexport(); 
		$JModelXmlexport->getGeneral($class->general); 
		
		$default = JURI::root(); 
		if (substr($default, -1) != '/') $default .= '/'; 
		$class->config->xml_live_site = OPCconfig::getValue('xmlexport_config', 'xml_live_site', 0, $default); 
		
		$class->params =& $class->config; 
		if (empty(OPCXmlExport::$classes)) OPCXmlExport::$classes = array(); 
		OPCXmlExport::$classes[$className] =& $class; 
		
  }
  
  public static function &prepareCats($langs)
  {
     return; 
    //vmrouterHelperSEFforOPC
	if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'cats.php'))
	 {
	   include(JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'cats.php'); 
	   $ct = time(); 
	   if (($ct - $time) > 36000)
	   return $cats; 
	 }
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'com_virtuemart_helper.php'); 
	foreach ($langs as $lang)
	{
	 $clang = strtolower(str_replace('-', '_', $lang)); 
	 $query = array(); 
	 $qeury['langswitch'] = $clang; 
	 $helper = vmrouterHelperSEFforOPC::getInstance($query);
	 
	 $db = JFactory::getDBO(); 
	 $q = 'select virtuemart_category_id from #__virtuemart_categories'; 
	 $db->setQuery($q); 
	 $cats = $db->loadAssocList(); 
	 
	 $mycats[$clang] = array(); 
	 $helper->nostatic = true; 
	 foreach ($cats as $catrow)
	  {
	    $cat = $catrow['virtuemart_category_id']; 
	    $mycats[$clang][$cat] = array(); 
		$mycats[$clang][$cat]['cats'] = array(); 
		$mycats[$clang][$cat]['route'] = ''; 
     		 
	    $categoryRoute = $helper->getCategoryRoute($cat); 
		if (isset($categoryRoute->route))
					{
					$arr = explode('~/~', $categoryRoute->route);
					if (count($arr)>0)
					{
					$catname = ''; 
					foreach ($arr as $c)
					  {
					   $catname = $c; 
					   $catname = str_replace("\xc2\xa0", '', $catname);
					   $mycats[$clang][$cat]['cats'][] = trim($c);
					   $helper->catsOfCats[$query['virtuemart_category_id']][] = trim($c); 
					  }
					}
					else
					{
					$mycats[$clang][$cat]['cats'][] = trim($categoryRoute->route); 
					$helper->catsOfCats[$query['virtuemart_category_id']][] = trim($categoryRoute->route);
					}
					
					$mycats[$clang][$cat]['route'] = $categoryRoute->route; 
					 
					
					}
					
		
	   
	  }
	 
	 
	}
	jimport( 'joomla.filesystem.folder' );
	if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage'))
	{
	  @JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage'); 
	  $data = ''; 
	  @JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'index.html', $data); 
	}
	if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage'))
	{
	  $data = '<?php defined(\'_JEXEC\') or die(\'Restricted access.\');'."\n";
	  $data .= '$time = '.time().'; '."\n"; 
	  $data .= '$cats = '.var_export($mycats, true).';'; 
	  @JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'cats.php', $data); 
	}
	 return $mycats; 
	
  }
  
  public static function cleardir($class2, $onlydirs=false) {
	   
	  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'xmlexport.php'); 
		$cnf = new stdClass(); 
		$JModelXmlexport = new JModelXmlexport(); 
		$JModelXmlexport->getGeneral($cnf); 
	  
	  if (empty($cnf->xml_export_path)) return; 
	  
	  
	  if (empty($class2)) return; 
	  
	  if (empty($class2->config->xmlfile)) return; 
	  
	  $name = $class2->config->xmlfile; 
	  
	  OPCXmlExport::log("OPC Export: clearing data of ".$class2->entity); 
	 
	  $path = JPATH_SITE.DIRECTORY_SEPARATOR.$cnf->xml_export_path; 
	  if (substr($path, -1, 1) == DIRECTORY_SEPARATOR) $path = substr($path, 0, -1); 
	  if (file_exists($path)) {
		  $files = JFolder::files($path, '.', false, true, array()); 
		  $folders = JFolder::folders($path, '.', false, true, array()); 
		  $files = array_merge($files, $folders); 
		   
		  if (!empty($files))
		  foreach ($files as $x) {
			  //$path.$name
			 // OPCXmlExport::log("OPC Export: searching ".$path.DIRECTORY_SEPARATOR.$name.' in '.$x); 
			  if (stripos($x, $path.DIRECTORY_SEPARATOR.$name)===0) {
				  if (is_dir($x)) {
				    if (JFolder::delete($x)===false) {
						OPCXmlExport::log("OPC Export: Cannot delete ".$x); 
					}
					else 
					{
					OPCXmlExport::log("OPC Export: Deleted ".$x); 
					}
					
				  }
				  elseif (empty($onlydirs)) {
					  
					  $testf = $path.DIRECTORY_SEPARATOR.$name; 
					  
					  if ($x === $testf) {
						  OPCXmlExport::log('To be removed later: '.$testf); 
						  continue; 
					  }
					  
				   if (JFile::delete($x)===false) {
					   OPCXmlExport::log("OPC Export: Cannot delete ".$x); 
				   }
				   else {
					   OPCXmlExport::log("OPC Export: Deleted ".$x); 
				   }
				  }
			  }
		  }
	  }
	
  }
  
  public static function doWork($langs)
  {
	  
    $allshg = array(); 
	$startmem = memory_get_usage(FALSE); 
	//echo '1:'.round(memory_get_usage(FALSE)/1024/1024).'Mb'.'<br />'; 
    if (!empty(OPCXmlExport::$classes))
	foreach (OPCXmlExport::$classes as $class2)
	{
	
	  $allshg[] = $class2->config->shopper_group; 
	  
	
	  
	   
	  
	}
	
	
	
	$allshg = array_unique($allshg); 
    
    if (empty(OPCXmlExport::$classes)) {
		// "OPC Export: No Class loaded. Please save config first."; 
		OPCXmlExport::log("OPC Export: No Class loaded. Please save config first."); 
		return true; 
	}
	
	/*
	$cleardir = JRequest::getInt('cleardir', false); 
	
	if ($cleardir) {
//		self::cleardir(); 
	}
	*/
	
	
	// zero step: 
    $from = JRequest::getInt('from', 1); 
	$to = JRequest::getInt('to', OPCXmlExport::$config->product_count); 
	/*
	if (empty($from) && (empty($to)) && ($compress != 2)) 
	{
	 self::$cats = OPCXmlExport::prepareCats($langs); 
	 return; 
	}
	*/
	
	// case 2, all exported at once: 
	/*
	if (($from == 1) && ($to >= OPCXmlExport::$config->product_count))
	self::$cats =& OPCXmlExport::prepareCats($langs); 
    */
	
	// last step: 
	
	

	 $from = JRequest::getInt('from', 0); 
	  if (($from === 0) && ($to === 0)) {
	    if (OPCXmlExport::compress()) {
			
		  OPCXmlExport::log("OPC Export: Last step finished."); 
		  OPCXmlExport::$maxreached = true; 
		  return true; 
		}
	  }
	  
	 
	 
	 // normal steps: 
	$from = JRequest::getInt('from', 0); 


	$finished = true; 
	if (!empty($to))
	{
	if (($from == 0) && ($to >= 1))
	{

		
     OPCXmlExport::clear(); 
	 OPCXmlExport::startHeaders(); 
	 
	
	}
	
	
	
	//self::$cats =& OPCXmlExport::prepareCats($langs); 
	
	
	
	$finished = OPCXmlExport::getItems($langs, $allshg); 
	
	
	
	}
	
	OPCXmlExport::$maxreached = $finished; 
	
	if ($finished) {
		OPCXmlExport::log("OPC Export: From ".$from.' to '.$to); 
	}
	
	
	
	if (($finished) && (($from === 0) && ($to === 0)))
	{
	  OPCXmlExport::log("OPC Export: Closing files"); 
	 
	  OPCXmlExport::endHeaders(); 
	  OPCXmlExport::close(); 
	  OPCXmlExport::group(); 
	  //in the next step: OPCXmlExport::compress(); 
	  
	}
	
	$endmem = memory_get_usage(FALSE); 
	$mem = $endmem-$startmem; 
	$mms = 'Mem: '.round($mem/1024/1024).'Mb';
	OPCXmlExport::log($mms); 
	
	return false; 
  }
  
  
  public static function doWorkCli($langs)
  {
	if (php_sapi_name() !== 'cli') die('Access denied - use CRON to access php directly!'); 
    if (!class_exists('cliHelper')) return; 
    
	$allshg = array(); 
	$startmem = memory_get_usage(FALSE); 
	OPCXmlExport::log('Starting with memory: '.round(memory_get_usage(FALSE)/1024/1024).'Mb'); 
    if (!empty(OPCXmlExport::$classes))
	foreach (OPCXmlExport::$classes as $class2)
	{
	  $allshg[] = $class2->config->shopper_group; 
	}
	
	
	
	$allshg = array_unique($allshg); 
    
    if (empty(OPCXmlExport::$classes)) {
		// "OPC Export: No Class loaded. Please save config first."; 
		OPCXmlExport::log("OPC Export: No Class loaded. Please save config first."); 
		return true; 
	}


	 $from = 0;
	 $finished = true; 
     OPCXmlExport::clear(); 
	 OPCXmlExport::startHeaders(); 
	 $finished = OPCXmlExport::getItems($langs, $allshg); 
	 OPCXmlExport::$maxreached = $finished; 
	 OPCXmlExport::log("OPC Export Finished"); 
	 OPCXmlExport::log("OPC Export: Closing files"); 
	 
	 OPCXmlExport::endHeaders(); 
	 OPCXmlExport::close(); 
	 OPCXmlExport::group(); 
	 OPCXmlExport::compress(); 
	  
	
	
	$endmem = memory_get_usage(FALSE); 
	$mem = $endmem-$startmem; 
	$mms = 'Memory usage: '.round($mem/1024/1024).'Mb';
	OPCXmlExport::log($mms); 
	
	return true; 
  }
  
  public static function getLog() {
	  $ret = ''; 
	  $last = ''; 
	  if (!empty(self::$msgs)) {
	  foreach (self::$msgs as $msg) {
		  if ($msg !== '.') {
			  if ($last === '.') $ret .= "<br />\n"; 
			  $ret .= $msg."<br />\n"; 
		  }
		  else {
			  $ret .= '.'; 
		  }
		  $last = $msg; 
	  }
	  }
	  return $ret; 
  }
  static $msgs; 
  public static function log($msg) {
	  if (empty(self::$msgs)) self::$msgs = array(); 
	  
	  static $lastmsg; 
	  
	  if ((defined('OPCCLI')) && (class_exists('cliHelper'))) {
		    if ($msg === '.') {
			  if ((!empty($lastmsg)) && ($lastmsg !== '.')) {
				  //new line: 
				  cliHelper::debug($msg, false, false); 
			  }
			  else {
			    cliHelper::debug($msg, false); 
			  }
			}
			else {
				if ((!empty($lastmsg)) && ($lastmsg === '.')) {
				  cliHelper::debug($msg, true, true); 
				}
				else {
					cliHelper::debug($msg, true, false); 
				}
			}
	  }
      else {
		  self::$msgs[] = $msg; 
      }
	  $lastmsg = $msg; 
  }
  
  private static function getItems($langs, $shoppergroups)
  {
    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'com_virtuemart_helper.php'); 
	$from = JRequest::getInt('from', 0); 
	if ($from === 1) $from = 0; 
	
	$to = JRequest::getInt('to', (int)OPCXmlExport::$config->product_count); 
	
	
    $db = JFactory::getDBO(); 
	$lang = 'en_gb'; 
	//$q = 'select * from #__virtuemart_products as p inner join #__virtuemart_products_'.$lang.' as l on p.virtuemart_product_id = l.virtuemart_product_id and p.published = 1'; 
	
	$iter = 400; 
	$rounds = 0; 
	//for ($is = $from; $is<=$to; $is = $is + $iter + 1 )
	{
	$rounds++; 
	$q = 'select virtuemart_product_id from #__virtuemart_products as p'; // order by virtuemart_product_id asc '; 
	
	$to = (int)$to; 
	$from = (int)$from; 
	$to2 = $to-$from+1; 
	$qa = ''; 
	
	
	if (!empty(OPCXmlExport::$config->xml_export_test))
	{
	   $products  = ''; 
	   $pp = array(); 
	   $z = explode(',', OPCXmlExport::$config->xml_export_test); 
	   if ((!empty($z)) && (is_array($z)))
	   {
		   foreach ($z as $v)
		   {
			   $pp2 = (int)$v; 
			   if (!empty($pp2)) $pp[] = $pp2;
		   }
		   if (!empty($pp)) $products = implode(',', $pp); 
	   }
	   else
	   {
		   $zi = (int)OPCXmlExport::$config->xml_export_test; 
		   if (!empty($zi)) $products = $zi; 
	   }
	   if (!empty($products))
	   {
		   $qa = ' and virtuemart_product_id IN ('.$products.')'; 
	   }
		   
	}
	
	if (!((empty($from) || ($from == 1)) && ($to == OPCXmlExport::$config->product_count)))
	{
	$q .= ' where p.published = 1 '; 
	
	
	
	
	
	//test: 
	//$q .= ' and virtuemart_product_id = 1848 '; 
	
	if (!empty($qa)) $q .= $qa; 
	
	$q .= ' order by virtuemart_product_id limit '.$from.', '.$to2; 
	}
	else
	{
	$q .= ' where p.published = 1 '; 



	if (!empty($qa)) $q .= $qa; 
	}
	//$q .= ' where p.virtuemart_product_id = 11083'; 
	//$ito =$is+$iter; 
	//$q .= ' limit '.$is.', '.$ito; 
	
	
	
	//echo $q.'<br />'; 
	
	
	
	$db->setQuery($q); 
	//echo '3:'.round(memory_get_usage(FALSE)/1024/1024).'Mb<br />'; 
	//$products = $db->loadObjectList(); 
	//$products = $db->loadResultArray(); 
	
	
	
	$products = $db->loadAssocList(); 
	
	
	
	
	
	
	
	
	$productModel = OPCmini::getModel('product'); 
	
	if (empty($products)) return true; 
	foreach ($products as $n=>$row)
	{
	  $product_id = (int)$row['virtuemart_product_id']; 
	  $product = self::getProductAndParent($product_id, $productModel, $shoppergroups, $langs, true, true, false); 
	  
	  
	  //echo '4:'.round(memory_get_usage(FALSE)/1024).'Kb<br />'; 
	  
	  
	  
	  if (empty($product)) 
	  {
	  OPCXmlExport::log('x'); 
	  continue; 
	  }
	  $vm1 = array(); 
      OPCXmlExport::addItem($product, $vm1); 
	}
	}
	
	$count = count($products); 
	if ($count < ($to - $from)) return true; 
	if ($count < $to2) return true; 
	
	if ($to >= OPCXmlExport::$config->product_count) return true; 
	
	return false; 
  }
  
  
  public static function getProductAndParent($product_id, &$productModel=null, $shoppergroups=array(), $langs=array(), $front = TRUE, $withCalc = TRUE, $onlyPublished = TRUE, $quantity = 1,$customfields = TRUE,$virtuemart_shoppergroup_ids=0) {
	  
	  if (empty($productModel)) $productModel = VmModel::getModel('product'); 
	  if (empty($langs)) $langs = VmConfig::get('active_languages', array()); 
	  $product = self::getProduct($productModel, $shoppergroups, $langs, $product_id, true, true, false); 
	  
	  
	 
	  $product->product_parent_id = (int)$product->product_parent_id; 
	  
	   $arr = array('product_s_desc', 
	 'product_desc', 
	 'product_name', 
	 'prices', 
	 'categories', 
	 'product_gtin', 
	 'product_mpn', 
	 'product_weight', 
	 'product_weight_uom', 
	 'product_length', 
	 'product_width', 
	 'product_height', 
	 'product_lwh_uom', 
	 'product_in_stock', 
	 'product_availability', 
	 'product_available_date', 
	 'product_special', 
	 'product_unit', 
	 'product_packaging', 
	 'product_params',
	 'product_parent_id'
	 ); 
	  
	  $tcp = 0; 
	 //if (!empty($product->product_parent_id))
	 while($product->product_parent_id !== 0)
	 {
		 //recursion protection: 
		 $tcp++; 
		 if ($tcp > 10) break; 
		 
	 $pp = self::getProduct($productModel, $shoppergroups, $langs, $product->product_parent_id, true, true, false); 
	 if (!empty($pp)) {
	 /*
	 if (empty($product->categories))
	 {
		 
		 {
			 
			 if (!empty($pp->categories))
			 {
				 $product->categories = $pp->categories; 
			 }
			 
			 
			 
		 }
	 }
	 */
	 $product->parentProduct = $parent = $pp; 
	 
	
	 
	 if (!empty($langs)) {
	 foreach ($langs as $l)
	 foreach ($arr as $k=>$key)
	 {
		
		 
	  if (empty($product->$key) && (!empty($parent->$key)))
	  {
		 
		 $product->$key = $parent->$key; 
		 
		 
		
	  }	
		 $l = strtolower(str_replace('-', '_', $l)); 
		 $lkey = $l.'_'.$key; 
		 if (isset($parent->$lkey)) 
		 {
			 if (empty($product->$lkey))
			 {
				 $product->$lkey = $parent->$lkey; 
			 }
			 
		 }
		 
	  
	  
	 }
	 }
	 else
	 {
	  foreach ($arr as $k=>$key)
	 {
	  if (empty($product->$key) && (!empty($parent->$key)))
	  {
		 
		 $product->$key = $parent->$key; 
		 
		 
		
	  }
	 }	  
	 }
	 

	 
	 
	 }
	 
	 $product->product_parent_id = (int)$pp->product_parent_id; 
	 
	 
	 }
	 
	 return $product; 
  }
  
  
  public static function getPID($pidformat, $product_id, $product_sku, $lang, $prefix='', $suffix='')
   {
	   
	   $pidformat = (int)$pidformat; 
	   
	    
		 
		
			 
switch ($pidformat)
{
  case 0: 
   $pid = $product_id; 
   break;
  case 2: 
    $pid = $product_sku; 
	if (empty($pid))
    $pid = $product_id; 
	break; 
  case 3: 
    $pid = $product_sku; 
	if (empty($pid))
    $pid = $product_id; 
    
	
	$lang = str_replace('-', '_', $lang); 
	$lang = strtolower($lang); 
	
	$sku_prefix_key = 'sku_prefix_'.$lang; 
	$sku_suffix_key = 'sku_suffix_'.$lang; 
	
	
	
	if (!empty($prefix))
	{
		
		$pid = $prefix.$pid; 
	}

	if (!empty($suffix))
	{
		$pid = $pid.$suffix; 
	}

	
	
	 break; 
	 
}
		return $pid; 
		 
		 
		
   }
  
  /**
	 * This function creates a product with the attributes of the parent.
	 *
	 * @param int     $virtuemart_product_id
	 * @param boolean $front for frontend use
	 * @param boolean $withCalc calculate prices?
	 * @param boolean published
	 * @param int quantity
	 * @param boolean load customfields
	 */
	public static function getProduct (&$productModel, $shg, $langs, $virtuemart_product_id = NULL, $front = TRUE, $withCalc = TRUE, $onlyPublished = TRUE, $quantity = 1,$customfields = TRUE,$virtuemart_shoppergroup_ids=0) {
	    
		if (!class_exists('VirtueMartCart'))
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cart.php');
		
		$cart = VirtuemartCart::getCart(false); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php'); 
		if (empty($cart->vendorId)) $cart->vendorId = 1; 
		$vendor_data = OPCtrackingHelper::getVendorInfo($cart->vendorId); 
		if (!empty($vendor_data['virtuemart_country_id'])) {
		 $cart->BT = array(); 
		 $cart->BT['virtuemart_country_id'] = $vendor_data['virtuemart_country_id']; 
		}
		
		
		$productModel = new VirtueMartModelProduct(); 
		$productModel->starttime = microtime (TRUE); 
		if (isset($virtuemart_product_id)) {
			$virtuemart_product_id = $productModel->setId ($virtuemart_product_id);
			$parent_id = $virtuemart_product_id; 
		}
		else return false; 

		if($virtuemart_shoppergroup_ids !=0 and is_array($virtuemart_shoppergroup_ids)){
			$virtuemart_shoppergroup_idsString = implode('',$virtuemart_shoppergroup_ids);
		} else {
			$virtuemart_shoppergroup_idsString = $virtuemart_shoppergroup_ids;
		}


		$front = $front?TRUE:0;
		$withCalc = $withCalc?TRUE:0;
		$onlyPublished = $onlyPublished?TRUE:0;
		$customfields = $customfields?TRUE:0;
		$productModel->withRating = false; 

		if ($productModel->memory_limit<$mem = round(memory_get_usage(FALSE)/(1024*1024),2)) {
			$m = round(memory_get_usage(FALSE)/(1024*1024),2); 
			$strm = 'low memory.. '.$m.'Mb'; 
			OPCXmlExport::log($strm); 
			return false;
		}
		$child = self::getProductSingle ($productModel, $shg, $langs, $parent_id, $virtuemart_product_id, $front,$quantity,$customfields,$virtuemart_shoppergroup_ids);
		if (!isset($child->virtuemart_product_id))
		{
		  return false; 
		}
		if(!isset($child->orderable)){
			$child->orderable = TRUE;
		}
		//store the original parent id
		$pId = $child->virtuemart_product_id;
		$ppId = $child->product_parent_id;
		$published = $child->published;

		
		
		
		$i = 0;
		
		$count = 0; 
		//Check for all attributes to inherited by parent products
		while (!empty($child->product_parent_id)) {
			$count++; 
			if ($count > 100) return false; 
			
			$parentProduct = self::getProductSingle ($productModel, $shg, $langs, $parent_id, $child->product_parent_id, $front,$quantity,$customfields,$virtuemart_shoppergroup_ids);
			if ($child->product_parent_id === $parentProduct->product_parent_id) {
				vmError('Error, parent product with virtuemart_product_id = '.$parentProduct->virtuemart_product_id.' has same parent id like the child with virtuemart_product_id '.$child->virtuemart_product_id);
				break;
			}
			$attribs = get_object_vars ($parentProduct);

			foreach ($attribs as $k=> $v) {
				if ('product_in_stock' != $k and 'product_ordered' != $k) {// Do not copy parent stock into child
					if (strpos ($k, '_') !== 0 and empty($child->$k)) {
						$child->$k = $v;
					//	vmdebug($child->product_parent_id.' $child->$k',$child->$k);
					}
				}
			}
			$i++;
			if ($child->product_parent_id != $parentProduct->product_parent_id) {
				$child->product_parent_id = $parentProduct->product_parent_id;
			}
			else {
				$child->product_parent_id = 0;
			}

		}

		//vmdebug('getProduct Time: '.$runtime);
		$child->published = $published;
		$child->virtuemart_product_id = $pId;
		$child->product_parent_id = $ppId;

		if ($withCalc) {
		
			//$child->prices = $productModel->getPrice ($child, array(), 1);
			if (!class_exists ('calculationHelper')) {
			   require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'calculationh.php');
			}
				$session = JFactory::getSession(); 
		

		
		if (!empty($shg)) {
			$saveda = $session->get('vm_shoppergroups_add',null,'vm');
			$savedr = $session->get('vm_shoppergroups_remove',null,'vm');
		
		foreach ($shg as $sh)
		{		
			// reset calculator: 
			$calculator = calculationHelper::getInstance ();
			$calculator::$_instance = null; 
			if (!empty($sh)) {
			  @$session->set('vm_shoppergroups_add',array($sh),'vm');
			}
			
			if (!empty($savedr))
			$session->set('vm_shoppergroups_remove',null,'vm');
			
			$calculator = calculationHelper::getInstance ();
		    // Calculate the modificator
		    //$variantPriceModification = $calculator->calculateModificators ($product, $customVariant);
				  
	  

			
			self::getProductPrices($child,$quantity,array($sh),true, $productModel, $calculator);
		    $child->priceshg[$sh] = $child->prices; 
	  


			$child->prices = $child->priceshg[$sh]; 
			
			$cart = VirtuemartCart::getCart(false); 
			$cart->BT = array(); 
			
			$calculator->_cart = $cart; 
		    $prices = $calculator->getProductPrices ($child, 0.0, $quantity);

			
			
			$child->pricesCalc[$sh] = $prices; 
		   }
		     
			unset($child->prices); 
			if (!empty($saveda)) {
		     $session->set('vm_shoppergroups_add',$saveda,'vm');
			}
			if (!empty($savedr)) {
			 $session->set('vm_shoppergroups_remove',$savedr,'vm');
			}
		} 
		else {
			$calculator = calculationHelper::getInstance ();
			$prices = self::getProductPrices($child,$quantity,array(1),true, $productModel, $calculator);
			$child->pricesCalc[0] = $prices; 
			$child->pricesCalc[1] = $prices; 
		}
		}

		if (empty($child->product_template)) {
			//$child->product_template = VmConfig::get ('producttemplate');
		}


		if(!empty($child->canonCatLink)) {
			// Add the product link  for canonical
			$child->canonical = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id . '&virtuemart_category_id=' . $child->canonCatLink;
		} else {
			$child->canonical = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id;
		}
		$child->canonical = JRoute::_ ($child->canonical,FALSE);
		if(!empty($child->virtuemart_category_id)) {
			$child->link = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id . '&virtuemart_category_id=' . $child->virtuemart_category_id, FALSE);
		} else {
			$child->link = $child->canonical;
		}

		$child->quantity = $quantity;

	
		return $child;
		


		
	}
	
	public static function getProductPrices(&$product, $quantity, $sh, $fe, &$productModel, &$calculator)
	{
	  if (!is_array($sh)) $sh = array($sh); 
	  
	  if (method_exists($productModel, 'getProductPrices'))
	  return $productModel->getProductPrices($product,$quantity,$sh,true);
	  
	  
	  $productModel->getRawProductPrices($product, $quantity, $sh, $fe, 0); 
	   if (!isset($product->selectedPrice))
	   $product->selectedPrice = -1; 
	  $product->prices = $calculator->getProductPrices ($product, 0.0, $quantity);
	  
	 
	}
	
	public static function getProductSingle ($productModel, $shg, $langs, $parent_id, $virtuemart_product_id = NULL, $front = TRUE, $quantity = 1,$customfields=TRUE,$virtuemart_shoppergroup_ids=0) {
       $db = JFactory::getDBO(); 
		//$productModel->fillVoidProduct($front);
		if (!empty($virtuemart_product_id)) {
			$virtuemart_product_id = $productModel->setId ($virtuemart_product_id);
		}

		if($virtuemart_shoppergroup_ids===0){
			$virtuemart_shoppergroup_ids = array(1); 
		}

		if($virtuemart_shoppergroup_ids !=null and is_array($virtuemart_shoppergroup_ids)){
			$virtuemart_shoppergroup_idsString = implode('',$virtuemart_shoppergroup_ids);
		} else {
			$virtuemart_shoppergroup_idsString = $virtuemart_shoppergroup_ids;
		}

		$front = $front?TRUE:0;
		$customfields = $customfields?TRUE:0;
		
		
		
		
		 
		
		
		if (!empty($parent_id)) {

		$q = 'select p.*, '; 
		$q .= ' (select pmf.virtuemart_manufacturer_id from #__virtuemart_product_manufacturers as pmf where pmf.virtuemart_product_id = p.virtuemart_product_id order by pmf.virtuemart_manufacturer_id desc limit 0,1 ) as mvirtuemart_manufacturer_id, '; 
		foreach ($langs as $lang)
		{
		 $lang = strtolower(str_replace('-', '_', $lang)); 
		 if (empty($lang)) continue; 
		 $q .= '(select '.$lang.'_l.product_s_desc from #__virtuemart_products_'.$lang.' as '.$lang.'_l where '.$lang.'_l.virtuemart_product_id = p.virtuemart_product_id) as '.$lang.'_product_s_desc, ';
		 $q .= '(select '.$lang.'_l.product_desc from #__virtuemart_products_'.$lang.' as '.$lang.'_l where '.$lang.'_l.virtuemart_product_id = p.virtuemart_product_id) as '.$lang.'_product_desc, ';
		 $q .= '(select '.$lang.'_l.product_name from #__virtuemart_products_'.$lang.' as '.$lang.'_l where '.$lang.'_l.virtuemart_product_id = p.virtuemart_product_id) as '.$lang.'_product_name, ';
		 
		 $q .= '(select '.$lang.'_l.mf_name from #__virtuemart_manufacturers_'.$lang.' as '.$lang.'_l where '.$lang.'_l.virtuemart_manufacturer_id = mvirtuemart_manufacturer_id) as '.$lang.'_mf_name, ';
		 
		 $q .= '(select '.$lang.'_l.mf_desc from #__virtuemart_manufacturers_'.$lang.' as '.$lang.'_l where '.$lang.'_l.virtuemart_manufacturer_id = mvirtuemart_manufacturer_id) as '.$lang.'_mf_desc, ';
		 
		}
		$q .= ' (select  GROUP_CONCAT(pcf.virtuemart_customfield_id) from  #__virtuemart_product_customfields as pcf where pcf.virtuemart_product_id = p.virtuemart_product_id) as virtuemart_custom_fields, ';
		$q .= ' (select  GROUP_CONCAT(pcf.virtuemart_custom_id) from  #__virtuemart_product_customfields as pcf where pcf.virtuemart_product_id = p.virtuemart_product_id) as virtuemart_custom_field_ids, ';
		
		$q .= ' (select  GROUP_CONCAT(media.virtuemart_media_id) from  #__virtuemart_product_medias as media where media.virtuemart_product_id = p.virtuemart_product_id) as virtuemart_product_medias, ';
		$q .= ' (select  GROUP_CONCAT(shoppergroups.virtuemart_shoppergroup_id) from  #__virtuemart_product_shoppergroups as shoppergroups where shoppergroups.virtuemart_product_id = p.virtuemart_product_id) as shg, ';
		$q .= ' (select  GROUP_CONCAT(cats.virtuemart_category_id) from  #__virtuemart_product_categories as cats where cats.virtuemart_product_id = p.virtuemart_product_id order by ordering asc) as cats, ';
		
		$q .= ' (select  GROUP_CONCAT(pp.virtuemart_product_id) from  #__virtuemart_products as pp where pp.product_parent_id = p.virtuemart_product_id) as children ';
		
		$q .= ' from #__virtuemart_products as p';
		
		//$q .= ' LEFT JOIN `#__virtuemart_product_manufacturers` as mf on `#__virtuemart_product_manufacturers`.`virtuemart_product_id`= p.`virtuemart_product_id` '; 
		$q .= ' WHERE p.`virtuemart_product_id` = "'.$parent_id.'" limit 0,1'; 
		
		$db->setQuery($q); 
		$product = $db->loadObject(); 
		
		
		
		
		$e = $db->getErrorMsg(); 
		
		
		
		if (empty($product)) return new stdClass(); 
		
		$kname = $lang.'_product_name'; 
		$product->product_name = $product->$kname; 
		
		
		
		$product->virtuemart_manufacturer_id = $product->mvirtuemart_manufacturer_id;
		//echo $q.'<br /><br />'; 
		$err = $db->getErrorMsg(); 
		if (!empty($err))
		OPCXmlExport::log($err); 
	
		$product->child_type = array(); 
		/*
		COM_ONEPAGE_XML_CHILDPRODUCTS_HANDLING_OPT1="Include both child and parent products"
		COM_ONEPAGE_XML_CHILDPRODUCTS_HANDLING_OPT2="Include only child products and products without child products (skip parent products)"
		COM_ONEPAGE_XML_CHILDPRODUCTS_HANDLING_OPT3="Include only parent products"

		*/
		$product->child_type[] = 1; 
		if (!empty($product->product_parent_id)) $product->child_type[] = 2; 
		
		// has children, ie is parent: 
		if (!empty($product->children) && (empty($product->product_parent_id))) $product->child_type[] = 3; 
		// does not have children and is not a child product (it's parent product)
		if (empty($product->children) && (empty($product->product_parent_id))) $product->child_type[] = 3;
		// is parent with no children, same as above
		if ((empty($product->product_parent_id)) && (empty($product->children))) $product->child_type[] = 2; 
		// is child and does not have subchildren: 
		if ((!empty($product->product_parent_id)) && (empty($product->children))) $product->child_type[] = 2; 
		
		
		
		
		// to object: 

		
			
		 if (empty($product->virtuemart_manufacturer_id)) $product->virtuemart_manufacturer_id = ''; 
		 /*
			if (!empty($product->virtuemart_manufacturer_id)) {
				$mfTable = $productModel->getTable ('manufacturers');
				$mfTable->load ((int)$product->virtuemart_manufacturer_id);
				
				foreach ($mfTable as $key=>$mf)
				{
				  $product->$key = $mf; 
				}
				
			}
			else {
			   //$product = (object)$product; 
				$product->virtuemart_manufacturer_id = array();
				$product->mf_name = '';
				$product->mf_desc = '';
				$product->mf_url = '';
			}
			*/
			
			
			$medias = explode(',', $product->virtuemart_product_medias); 
			if (is_array($medias))
			{
			  $product->virtuemart_media_id = $medias; 
			}
			else
			  $product->virtuemart_media_id = array($product->virtuemart_product_medias); 

			// shopper groups handling
			if (empty($product->shg)) $product->shoppergroups = array(); 
			else
			{
			$sh = explode(',', $product->shg); 
		    if (is_array($sh))
		    $product->shoppergroups = $sh; 
		    else $product->shoppergroups = array($product->shg); 
			}
		 
			
			// Load the categories the product is in
			if (empty($product->cats)) $product->categories = array(); 
			else
			{
			 $ar = explode(',', $product->cats); 
			 if (is_array($ar))
			 $product->categories = $ar; 
			 else 
			 $product->categories = array($product->cats); 
			}
			
			//if (!empty($product->categories))
		    //$product->virtuemart_category_id = $product->categories[0];	
			
		

			
			
				if (!empty(self::$config->xml_export_customs))
				{
					$customfieldModel = VmModel::getModel ('Customfields');
					if (method_exists($customfieldModel, 'getproductCustomslist'))
					{
					$product->customfields = $customfieldModel->getproductCustomslist ($productModel->_id);

					if (empty($product->customfields) and !empty($product->product_parent_id)) {
						
						$product->customfields = $customfieldModel->getproductCustomslist ($product->product_parent_id, $productModel->_id);
						$product->customfields_fromParent = TRUE;
					}
					}
					
					require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
					OPCrenderer::filterCustomFields($product); 
					
				}
			
			 
			
			
			
			{


				
				// Fix the product packaging
				if ($product->product_packaging) {
					$product->packaging = $product->product_packaging & 0xFFFF;
					$product->box = ($product->product_packaging >> 16) & 0xFFFF;
				}
				else {
					$product->packaging = '';
					$product->box = '';
				}

				// set the custom variants
				//vmdebug('getProductSingle id '.$product->virtuemart_product_id.' $product->virtuemart_customfield_id '.$product->virtuemart_customfield_id);
				if (!empty(self::$config->xml_export_customs))
				if (!empty($product->virtuemart_custom_fields )) {

					$customfieldModel = VmModel::getModel ('Customfields');
					if (method_exists($customfieldModel, 'getProductCustomsField'))
					{
					// Load the custom product fields
					$product->customfields = $customfieldModel->getProductCustomsField ($product);
					$product->customfieldsRelatedCategories = $customfieldModel->getProductCustomsFieldRelatedCategories ($product);
					$product->customfieldsRelatedProducts = $customfieldModel->getProductCustomsFieldRelatedProducts ($product);
					//  custom product fields for add to cart
					$product->customfieldsCart = $customfieldModel->getProductCustomsFieldCart ($product);
					$child = $productModel->getProductChilds ($productModel->_id);
					$product->customsChilds = $customfieldModel->getProductCustomsChilds ($child, $productModel->_id);
					}
				}

				// Check the stock level
				if (empty($product->product_in_stock)) {
					$product->product_in_stock = 0;
				}
			}
			
			return $product; 
			
		}
		else {
			return $productModel->fillVoidProduct ($front);
			
		}
		return false; 
	}

  
  
  public static function updateProduct(&$product, &$class, &$vm1)
  {
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	  
	  OPCmini::toObject($product); 
	
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
	  	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'com_virtuemart_helper.php'); 
	  
	  
	  
	  
      $lang = $class->config->language; 
	$a = explode('-', $lang); 
	
	$langkey = strtolower(str_replace('-', '_', $lang)); 
	$lang = strtolower($a[0]); 
	  $vm1 = array(); 
	  $vm1['atr'] = ''; 
	  $vm1['product_id'] = $product->virtuemart_product_id; 
	  if (empty($product->product_sku)) $product->product_sku = $product->virtuemart_product_id; 
	  $vm1['sku'] = $product->product_sku ;
	  
	  $key = $langkey.'_product_name'; 
	  if (isset($product->$key)) {
	  $vm1['product_name'] = $product->$key ; 
	  $product->product_name = $vm1['product_name']; 
	  }
	  if (isset($product->$key)) {
	   $key = $langkey.'_mf_name'; 
	   $vm1['manufacturer_name'] = $product->$key ; 
	  
	   $vm1['manufacturer'] = $vm1['manufacturer_name']; 
	   $vm1['mf_name'] = $product->$key ; 
	    $product->mf_name = $vm1['mf_name']; 
	  }
	 
	  $key = $langkey.'_mf_desc'; 
	  if (isset($product->$key )) {
	  $vm1['mf_desc'] = $product->$key ; 
	  $product->mf_desc = $vm1['mf_desc']; 
	  }
	  
	  if (isset($product->product_gtin)) {
		  $vm1['ean'] = $product->product_gtin; 
	  }
	  else {
		  $vm1['ean'] = ''; 
	  }
	  
	  $ida = $product->virtuemart_media_id; 
	  if (is_array($ida)) $id = reset($ida);
	  else $id = $ida; 
	  
	  if (empty($id))
	  {
		  if (!empty($product->parentProduct))
		  {
			  if (!empty($product->parentProduct->virtuemart_media_id))
			  {
				  $ida = $product->parentProduct->virtuemart_media_id; 
				  if (is_array($ida)) $id = reset($ida);
				  else $id = $ida; 
				  
				
			  }
		  }
	  }
	  
	  
	  
	  $img = OPCImage::getMediaData($id);
	  
	  $site = OPCXmlExport::$config->xml_live_site; 
	  
	  if (is_array($product->virtuemart_manufacturer_id))
	  {
	    $vm1['manufacturer_id'] = reset($product->virtuemart_manufacturer_id); 
	  }
	  else
	  $vm1['manufacturer_id'] = 0; 
	  
	  if (!empty($img['file_url']))
	  {
	   if (stripos($img['file_url'], 'http')===false)
	   $vm1['thumb_url'] = $site.$img['file_url']; 
	   else
	   $vm1['thumb_url'] = $img['file_url']; 
      
	  }
	  else $vm1['thumb_url'] = ''; 
	  
	  $product->imagePaths = array(); 
	  $product->imagePaths[] = $vm1['thumb_url']; 
	  
	  if (!empty($ida))
	  {
	    foreach ($ida as $im)
		 {
		   $img = OPCImage::getMediaData($id);
		   if (!empty($img['file_url']))
		    {
			  $product->imagePaths[] = OPCXmlExport::getLink('', $vm1['thumb_url']); 
			}
		 }
	  }
	 
 
	  //$vm1['thumb_url'] = $img->file_url; 
	  $vm1['fullimg'] = $vm1['thumb_url']; 
	  $key = $langkey.'_product_s_desc'; 
	  
	  $vm1['desc'] =& $product->$key; 
	  $product->product_s_desc =& $vm1['desc']; 
	  $key = $langkey.'_product_desc'; 
	  $vm1['fulldesc'] =& $product->$key; 
	  $product->fulldesc =& $vm1['fulldesc']; 
	  
	  
  
  
    if (!empty($class->config->cname))
    $utm = 'utm_source='.$class->config->cname; 
    else
	$utm = ''; 
  
    if ($class->config->url_type == 1)
	$product->url = OPCXmlExport::getLink('', $product->link); 
	else
	if ($class->config->url_type == 2)
	$product->url = OPCXmlExport::getLink('', $product->link, $utm); 
	else
	if ($class->config->url_type == 3)
	$product->url = OPCXmlExport::getLink('', 'index.php?option=com_virtuemart&lang='.$lang.'&view=productdetails&virtuemart_product_id='.(int)$product->virtuemart_product_id, $utm); 
	else
	$product->url = OPCXmlExport::getLink('', 'index.php?option=com_onepage&view=redirect&virtuemart_product_id='.(int)$product->virtuemart_product_id.'&nosef=1&tmpl=component&format=opchtml', $utm); 
	
	
	
	$vm1['link'] = $product->url; 
	$vm1['node_link'] = OPCXmlExport::getLink('','index.php?option=com_virtuemart&lang='.$lang.'&view=productdetails&virtuemart_product_id='.(int)$product->virtuemart_product_id, $utm); 
	
	if (!isset($product->pricesCalc) && (isset($product->prices))) {
	   $product->pricesCalc[1] = $product->prices; 
	}
	
	if (isset($product->pricesCalc[$class->config->shopper_group])) {
	$vm1['cena_s_dph'] = $product->pricesCalc[$class->config->shopper_group]['salesPrice']; 
	$product->prices = $product->pricesCalc[$class->config->shopper_group]; 
	
	if (!empty($product->prices['discountedPriceWithoutTax']))
	$vm1['cena_txt'] = $product->pricesCalc[$class->config->shopper_group]['discountedPriceWithoutTax']; 
	else
	$vm1['cena_txt'] = $product->pricesCalc[$class->config->shopper_group]['priceBeforeTax']; 
	$product->prices = $product->pricesCalc[$class->config->shopper_group]; 
	
	}
	
	$vm1['tax_rate'] = OPCXmlExport::getTaxRate($product, $class->config); 
	
	$vm1['avai_obr'] = $product->product_availability; 
	
	if (empty($product->product_availability))
	{
	$vm1['avaitext'] = $class->config->avaitext;
	$vm1['avaidays'] = $class->config->avaidays;
	}
	else
	{
	  $img = $product->product_availability; 
	  $pattern = '/[^\w]+/'; //'[^a-zA-Z\s]'; 
	  $key = preg_replace( $pattern, '_', $img ); 
	  
	  
	  if (isset($class->config->{$key.'txt'}))
	  $vm1['avaitext'] = $class->config->{$key.'txt'}; 
	  else
	  $vm1['avaitext'] = $product->product_availability; 
	  
	  if (isset($class->config->{$key.'days'}))
	  $vm1['avaidays'] = $class->config->{$key.'days'}; 
	  else
	  $vm1['avaidays'] = $product->product_availability; 
	  
	}
	
	
	
	$lang = $class->config->language; 
	$lang = strtolower(str_replace('-', '_', $lang)); 
	 
	 $vm1['cats'] = array(); 
	  $cats = array(); 
	  
	  
	  if (!empty($product->categories))
	  foreach ($product->categories as $cat)
	  {
	  	//
	
	
	 $clang = strtolower(str_replace('-', '_', $lang)); 
	 $query = array(); 
	 $qeury['langswitch'] = $clang; 
	 $helper = vmrouterHelperSEFforOPC::getInstance($query);
		 
	      $categoryRoute = $helper->getCategoryRoute($cat); 
		  if (isset($categoryRoute->route))
					{
					$arr = explode('~/~', $categoryRoute->route);
					if (count($arr)>0)
					{
					$catname = ''; 
					foreach ($arr as $c)
					  {
					   $catname = $c; 
					   $catname = str_replace("\xc2\xa0", '', $catname);
					   $mycats[$clang][$cat]['cats'][] = trim($c);
					   $helper->catsOfCats[$cat][] = trim($c); 
					  }
					}
					else
					{
					$mycats[$clang][$cat]['cats'][] = trim($categoryRoute->route); 
					$helper->catsOfCats[$query['virtuemart_category_id']][] = trim($categoryRoute->route);
					}
					
					$mycats[$clang][$cat]['route'] = $categoryRoute->route; 
					 
					
					}
					
		
	   
	     $vm1['cats'][$cat] = $mycats[$clang][$cat]['cats'];
		 
		 
	  }
	 
	 
	
	$count = 0; 
	$sk = -1; 
	$l = 0; 
	foreach ($vm1['cats'] as $key=>$cat)
	{
	  
	  if (count($cat)>$count)
	  {
	  $sk = $key; 
	  $count = count($cat); 
	  $l = implode(' ', $cat); 
	  }
	  if (count($cat)==$count)
	  {
	    $l2 = implode(' ', $cat); 
		if ($l2 > $l) 
		{
		  $count = count($cat); 
		  $l=$l2; 
		  $sk = $key; 
		}
	  }
	}
	
	$vm1['longest_cat_id'] = 0; 
	
	if ($sk >= 0)
	{
	 $vm1['longest_cats'] = $vm1['cats'][$sk]; 
	 $vm1['longest_cat_id'] = $sk; 
	}
	if (!isset($vm1['longest_cats'])) $vm1['longest_cats'] = array(); 
	
	$vm1['published'] = (bool)$product->published; 
	$vm1['attribs'] = array(); 
	//if (empty($vm1['manufacturer']))
	{
	   if (!empty($product->virtuemart_custom_field_ids))
	   if (!empty($product->virtuemart_custom_fields))
	   {
	     $a = explode(',',$product->virtuemart_custom_field_ids); 
		 $a2 = explode(',',$product->virtuemart_custom_fields);
		 if (count($a) == count($a2))
		  {
		   
		     // attribs: 
			 if (!defined('VM_VERSION') || (VM_VERSION < 3))
			 {
			  $custom_field_col = 'custom_value'; 
			 }
			 else
			 {
				 $custom_field_col = 'customfield_value'; 
			 }
			
			 $q = 'select cs.custom_title, cf.'.$custom_field_col.', cf.virtuemart_customfield_id, cf.virtuemart_custom_id from #__virtuemart_customs as cs, #__virtuemart_product_customfields as cf where cs.virtuemart_custom_id = cf.virtuemart_custom_id and cf.virtuemart_customfield_id IN ('.$product->virtuemart_custom_fields.') '; 
			 $db = JFactory::getDBO(); 
			 $db->setQuery($q); 
			 $res = $db->loadAssocList(); 
			 if (!empty($res))
			 {
			 $vm1['attribs'] = $res; 
			 if (empty($product->virtuemart_customfields))
			 {
				
				 foreach ($res as $kkx=>$rr) {
				 if ((!isset($rr['customfield_value'])) && (isset($rr['custom_value']))) {
					 
					 $res[$kkx]['customfield_value'] = $rr['custom_value']; 
				 }
				 }
				  $product->virtuemart_customs = $res; 
				 
			 }
			 }
		  }
	   }
	}
	

	 
	
  }
  
  public static function getBillTaxes()
  {
    static $result; 
	if (!empty($result)) return $result; 
    $db = JFactory::getDBO(); 
	$null = $db->getNullDate();
    $jnow = JFactory::getDate();
    if (method_exists($jnow, 'toMySQL'))
    $now = $jnow->toMySQL();
    else $now = $jnow->toSQL(); 

	$q = 'select c.*, ';
	$q .= ' (select  GROUP_CONCAT(cat.virtuemart_category_id) from  #__virtuemart_calc_categories as cat where cat.virtuemart_calc_id = c.virtuemart_calc_id) as categories, ';
	//$q .= ' (select  GROUP_CONCAT(st.virtuemart_state_id) from  #__virtuemart_calc_states as st where st.virtuemart_calc_id = c.virtuemart_calc_id) as states, ';
	$q .= ' (select  GROUP_CONCAT(mf.virtuemart_manufacturer_id) from  #__virtuemart_calc_manufacturers as mf where mf.virtuemart_calc_id = c.virtuemart_calc_id) as mafucaturers, ';
	//$q .= ' (select  GROUP_CONCAT(co.virtuemart_country_id) from  #__virtuemart_calc_countries as co where co.virtuemart_calc_id = c.virtuemart_calc_id) as countries ';
	$q .= ' (select  GROUP_CONCAT(sg.virtuemart_shoppergroup_id) from  #__virtuemart_calc_shoppergroups as sg where sg.virtuemart_calc_id = c.virtuemart_calc_id) as shoppergroups ';
	
	$q .= ' from #__virtuemart_calcs as c '; 
	$q .= ' where c.published = 1 and c.calc_kind = \'TaxBill\' and c.calc_value_mathop = \'+%\' '; 
	$q .= ' AND ( c.publish_up = "' . $db->escape($null) . '" OR c.publish_up <= "' . $db->escape($now) . '" ) '; 
	$q .= ' AND ( c.publish_down = "' . $db->escape($null) . '" OR c.publish_down >= "' . $db->escape($now) . '" ) ';
	
	
	
	
	//$Q .= ' GROUP BY c.virtuemart_calc_id '; 
	
	//$q .= ', GROUP_CONCAT(IFNULL(st.virtuemart_state_id, \'\')) '; 
	//$q .= ', IFNULL(GROUP_CONCAT(co.virtuemart_country_id), 0) as countries, IFNULL(GROUP_CONCAT(mf.virtuemart_manufacturer_id), 0) as manufs, IFNULL(GROUP_CONCAT(cat.virtuemart_category_id), 0) as cats '; 
	//$q .= ', GROUP_CONCAT(st.virtuemart_state_id), GROUP_CONCAT(co.virtuemart_country_id), GROUP_CONCAT(mf.virtuemart_manufacturer_id), GROUP_CONCAT(cat.virtuemart_category_id) '; 	
	//$q .= 'left outer join #__virtuemart_calc_categories as cat using(virtuemart_calc_id) '; 
	//$q .= 'left outer join #__virtuemart_calc_manufacturers as mf using(virtuemart_calc_id)'; 
	//$q .= 'left outer join #__virtuemart_calc_countries as co using(virtuemart_calc_id)'; 
	//$q .= 'left outer join #__virtuemart_calc_states as st using(virtuemart_calc_id)'; 
	
	//
	$db->setQuery($q); 
	$res = $db->loadAssocList(); 
	$result = $res; 
	return $res; 
	
  }
  
  public static function getTaxRate(&$product, &$config)
  {
    $prices = $product->prices; 
    $taxes = array('DBTax', 'Tax', 'VatTax', 'DATax'); 
    $taxes = array('DBTax', 'Tax', 'VatTax', 'DATax'); 
    foreach ($prices as $key=>$val)
     {
	   
	   if (!empty($val))
	   if (is_array($val))
	   {
	   foreach ($val as $atax)
	   if (in_array($key, $taxes))
	    {
		   
		   if ($atax[2]=='+%')
		    {
			  return (double)$atax[1]; 
			}
		}
	   }
	 }
	 

	$billtax = OPCXmlExport::getBillTaxes(); 
    foreach ($billtax as $tax)
	 {
	    $match0 = $match1 = $match2 = false; 
		
	    if (!empty($tax['categories']))
		{
	    $cats = explode(',', $tax['categories']); 
		
		if (!empty($cats))
		{
		  foreach ($product->categories as $cat_id)
		   {
		      if (in_array($cat_id, $cats)) 
			  $match0 = (double)$tax['calc_value']; 
		   }
		}
		else
		$match0 = (double)$tax['calc_value']; 
		}
		else
		$match0 = (double)$tax['calc_value']; 
		
		if (!empty($tax['manufacturers']))
		{
		$msfs = explode(',', $tax['manufacturers']); 
		if (!empty($msfs))
		{
		   if (in_array($product->virtuemart_manufacturer_id, $msfs)) 
		   $match1 = (double)$tax['calc_value'];
		   
		   
		}
		else 
	    $match1 = (double)$tax['calc_value'];
		
		}
		else 
	    $match1 = (double)$tax['calc_value'];
		
		if (!empty($tax['shoppergroups']))
		{
		$sgs = explode(',', $tax['shoppergroups']); 
		if (!empty($sgs))
		  {
		     if (in_array($config->shopper_group, $sgs))
			 $match2 = (double)$tax['calc_value'];
		  }
		  else
		  $match2 = (double)$tax['calc_value'];
		
		}
		else
		$match2 = (double)$tax['calc_value'];
		
		if (!empty($match1))
		if (!empty($match2))
		if (!empty($match0))
		return $match1;
		
		
	 }
	return (double)0; 
	 
  }
  
  public static function getLink($domain='', $path, $merge='')
  {
    if (!empty($merge))
	{
	  if (strpos($path, '?')===false) $path .= '?'.$merge;
	  else $path .= '&'.$merge; 
	}
  

  
    if (strpos($path, 'http')===0) return $path; 
    if ($domain == '') $domain = OPCXmlExport::$config->xml_live_site; 
	
	
	if (substr($domain, -1) === '/') $domain = substr($domain, 0, -1); 
	if (substr($path, 0, 1) === '/') $path = substr($path, 1); 
	
	$t1 = explode('/', $domain); 
	$t2 = explode('/', $path); 
	if ((count($t1)>1) && (count($t2)>1)) {
	 $e1 = $t1[count($t1)-1]; 
	 $e2 = $t2[0]; 
	 
	
	 
	 if ($e1 === $e2) {
	   unset($t2[0]); 
	   $path = implode('/', $t2); 
	 }
	}
	
	
	
	return $domain.'/'.$path; 
  }
  
   public static function checkGeneric($config, $product)
  {
	  

	  
	  if (!empty($config->in_stock_only))
	  {
		  if (empty($product->product_in_stock)) {
			  OPCXmlExport::log("OPC Export Generic Product Check: ".$product->product_name."(".$product->virtuemart_product_id.") empty stock !"); 
			  return false; 
		  }
		  
		  
	  
	  
	  if (!empty($config->stock_calc)) {
	    $x1 = (float)$product->product_in_stock; 
		$x2 = (float)$product->product_ordered; 
		$z = $x1 - $x2; 
		if ($z <= 0) {
			OPCXmlExport::log("OPC Export Generic Product Check: ".$product->product_name."(".$product->virtuemart_product_id.") Empty or negative stock after products ordered !"); 
			return false; 
		}
	  }
	  }
	  $ea = OPCmini::parseCommas($config->only_category_filter); 
	  if (!empty($ea))
	  {
	   if (empty($product->categories))   {
		   OPCXmlExport::log("OPC Export Generic Product Check: ".$product->product_name."(".$product->virtuemart_product_id.") No categories !"); 
		   return false; 
	   }
	   if (!empty($product->categories))
	   foreach ($product->categories as $c)
	   {
		    $c = (int)$c; 
			if (!isset($ea[$c])) {
				OPCXmlExport::log("OPC Export Generic Product Check: ".$product->product_name."(".$product->virtuemart_product_id.") Product not in category ".$c." !"); 
				return false; 
			}
			
	   }
	  }
	  
	  $ea = OPCmini::parseCommas($config->not_category_filter); 
	 
	  if (!empty($ea))
	  {
	   if (empty($product->categories)) {
		   if (isset($ea[0])) {
			   OPCXmlExport::log("OPC Export Generic Product Check: ".$product->product_name."(".$product->virtuemart_product_id.") Empty categories while 'not in category' enabled !"); 
			   return false; 
		   }
		   
	   }
	   if (!empty($product->categories))
	   foreach ($product->categories as $c)
	   {
		    $c = (int)$c; 
			
			if (isset($ea[$c])) {
				OPCXmlExport::log("OPC Export Generic Product Check: ".$product->product_name."(".$product->virtuemart_product_id.") Product skipped as it is in filtered category ".$c." !"); 
				return false; 
				
			}
			
	   }
	   
	  }
	 
	  
	  
	  return true; 
  }
  
  public static function addItem($product, $vm1m)
  {
  
  // internal error:
  if (empty($product->virtuemart_product_id )) 
  {
	 OPCXmlExport::log('Skipping product - empty ID '.var_export($vm1m, true)); 
     return; 
  }
      foreach (OPCXmlExport::$classes as $class)
	  {
	     if (!self::checkGeneric($class->config, $product))
		 {
			 OPCXmlExport::log('Skipping product '.$product->product_name.'('.$product->virtuemart_product_id.') - general check not passed'); 
			 continue; 
		 }
	     if (method_exists($class, 'addItem'))
		 {
		  // check the shopper group:
		  if (empty($product->shoppergroups) || (in_array($class->config->shopper_group, $product->shoppergroups)))
		  if (in_array($class->config->child_type, $product->child_type))
		  {
		   $product2 = $product; 
		   OPCXmlExport::updateProduct($product2, $class, $vm1); 
		   
		  $product2->is_override = false; 
		   
		   //get pairing info: 
		   //if (!empty($product->categories))
		   if (!empty($vm1['cats']))
		   {
		   //$product2->paired_category_name = reset($vm1['cats']);
		   $deepestcat = $vm1['longest_cats'][count($vm1['longest_cats'])-1]; 
		   $product2->paired_category_name = $deepestcat; 
		   
		   if (!empty($vm1['longest_cat_id']))
		     {	
				// take the first: 
				 $cat_id = $vm1['longest_cat_id']; //$product->categories[0]; 
				 $default = new stdClass(); 
				 $entity = $class->entity;
				 $res = OPCconfig::getValue('xmlexport_pairing', $entity, $cat_id, $default);
				
				if ($res === $default)
				{
					$sorted = $vm1['cats']; 
					$map = array_map('count', $vm1['cats']); 
					arsort($map); 
				
				
				
				
					foreach ($map as $k=>$v)
					{
						$res = OPCconfig::getValue('xmlexport_pairing', $entity, $k, $default);
						if ($res !== $default)
						{
							if ((!empty($res)) && (!empty($res->txt)))
							{
							 $product2->paired_category_name = $res->txt; 
							 $product2->pairedObj = $res; 
							 $product2->is_override = true; 
							 break;
							}
						}
					}
				
			
				}
				else
				{
					if ((!empty($res)) && (!empty($res->txt)))
					{
						$product2->paired_category_name = $res->txt; 
						$product2->is_override = true; 
						
					}
					$product2->pairedObj = $res; 
				}
				
			}
			}
			else
			{
				
			  $product2->paired_category_name = '';
			}
			
			
		   if (empty($vm1['product_name'])) {
			   
			   
			   
			 OPCXmlExport::log('Skipping product '.$product->product_name.'('.$product->virtuemart_product_id.') - empty product name'); 
		     continue;   
		   }
		   $ret = $class->addItem($product2, $vm1); 
		   
		   
		   
		   
		   $class->writer->write($ret); 
		  }
		  else
		  {
			  
		  }
		 }
	  }

  }
  
  public static function clear()
  {
	  
    	
	
    foreach (OPCXmlExport::$classes as $class)
	  {
		 $class->writer->open(); 
	     if (method_exists($class, 'clear'))
		 {
			OPCXmlExport::cleardir($class); 
			$class->writer = new OPCwriter($class->config->xmlpath);
		    $ret = $class->clear(); 
			$class->writer->write($ret); 
		 }
	  }
  }
  
  public static function startHeaders()
  {
	  
	 
    foreach (OPCXmlExport::$classes as $class)
	  {
	     if (method_exists($class, 'startHeaders'))
		 {
		  $ret = $class->startHeaders(); 
		  $class->writer->write($ret); 
		
		 }
	  }
  }
  
  public static function endHeaders()
  {
    foreach (OPCXmlExport::$classes as $class)
	  {
	     if (method_exists($class, 'endHeaders'))
		 {
		  $ret = $class->endHeaders(); 
		  $class->writer->write($ret); 
		 }
	  }
  }
  
  public static function close()
  {
    foreach (OPCXmlExport::$classes as $class)
	  {
	     if (method_exists($class, 'close'))
		 {
		  $ret = $class->close(); 
		  $class->writer->write($ret); 
		 }
		 $class->writer->close(); 
	  }
	  
	
	  
	  
	 
  }
  
  //this is the last function, if any of XML's are not finished, this will return false and the fucntion will be repeated
  public static function compress()
  {
	
	  $x1 = 0; 
	  $c = count(OPCXmlExport::$classes); 
	  
	  $xml_export_disable_compression = OPCconfig::getValue('xmlexport_config', 'xml_export_disable_compression', 0, false); 
	  
	  
	  foreach (OPCXmlExport::$classes as $class)
	  {
	   if ($class->writer->isFinished()) {
		  
		 if (empty($xml_export_disable_compression)) {
		  
	         $x1 += $class->writer->compress();
		  
		   
		   
		    
		 }
		 else {
			 OPCXmlExport::log("OPC Export: compression disabled"); 
			 $x1++; 
		 }
		 
		 
		 
		}
		else {
			OPCXmlExport::log("OPC Export: export not finished"); 
			continue;
		}
	  }
	  
	  if ($x1 === $c) {
		   foreach (OPCXmlExport::$classes as $class) {
			   
			   OPCXmlExport::log("OPC Export: compression finished ".$x1.' of '.$c); 
			   
			   OPCXmlExport::cleardir($class, true); 
		   }
		  return true; 
	  }
	 
	  
	  return false; 
  }
  
  public static function group() {
	  foreach (OPCXmlExport::$classes as $class)
	  {
	    $class->writer->group(); 
	  }
  }
  
  

}

class OPCWriter {
 public $file = ''; 
 public $suffix = '.tmp'; 
 private $method = 0; 
 private $buffer = ''; 
 private $block = true; 
 public function __construct($file)
 {
    if (empty($file)) {
		die('z'); 
	}
    $this->file = JPATH_SITE.DIRECTORY_SEPARATOR.$file; 
	/*$this->suffix = '.tmp'; 
	//if (file_exists($this->file.$this->suffix)) 
	*/
	
	
		
		
		$filekey = JRequest::getInt('filekey', 0); 
		
		$folder = $this->file.'_temp_'.$filekey.DIRECTORY_SEPARATOR; 
		clearstatcache(true, $folder); 
		
		if (!file_exists($folder)) {
			jimport( 'joomla.filesystem.folder' );
			JFolder::create($folder); 
		}
		$timeX = microtime(true) * 100000; 
		$timeX = (int)$timeX; 
		$newname = $timeX.'.tmp'; 
		$newname = str_pad($newname, 20, '0', STR_PAD_LEFT); 
		
		$this->folder = $folder; 
		$this->suffix = '_temp_'.$filekey.DIRECTORY_SEPARATOR.$newname; 
		//$this->suffix = $stamp.'.tmp'; 
	
	$this->block = false; 
	// check method: 
	$data = ''; 
	$this->buffer = ''; 
	
	$from = JRequest::getInt('from', 1); 
	$this->method = 2; 
	
	
	
	if ($from == 1)
	{

	$res = @file_put_contents($this->file.$this->suffix, $data, LOCK_EX); 
	if ($res === false)
	{
	  	  $res = @JFile::write($this->file.$this->suffix, $data); 
		  if ($res === false)
		  {
		   $this->method = 0; 
		  }
		  else
		  {
		   $this->method = 1; 
		  }
	}
	else
	{  
		$this->method = 2; 
	}
	}
	
 }
 
 public function compress()
 {
    
	  clearstatcache(true, $this->folder.'compressed.tmp'); 
	  if (file_exists($this->folder.'compressed.tmp')) {
	  
	    OPCXmlExport::log('File already compressed '.$this->file.' as with '.$this->folder.'compressed.tmp'); 
	  
		return 1; 
	  }
	
	clearstatcache(true, $this->file); 
	if (file_exists($this->file)) {
	OPCXmlExport::log('Compressing '.$this->file.'...'); 
      if (JFile::delete($this->file.'.gz')) {
	   $ret = $this->gzcompressfile($this->file, 9); 
	 }
	 else {
		 OPCXmlExport::log('Compression - cannot remove: '.$this->file.'.gz'); 
		 return 0; 
	 }
	}
	else {
		OPCXmlExport::log('Compression - file not found: '.$this->file.'...'); 
		return 0; 
	}
	
	 $endfile = ''; 
	 JFile::write($this->folder.'compressed.tmp', $endfile); 
	
	return 1; 
 }
 
//http://www.php.net/manual/en/function.gzwrite.php
private function gzcompressfile($source,$level=false){ 
    $start = time(); 
    $dest=$source.'.gz'; 
	
    $mode='wb'.$level; 
    $error=false; 
    if($fp_out=gzopen($dest,$mode)){ 
        if($fp_in=fopen($source,'rb')){ 
            while(!feof($fp_in)) 
			{
                gzwrite($fp_out,fread($fp_in,1024*512)); 
				$time2 = time(); 
				// stAn: if time is over 10 seconds, let's rather close, delete the file and return
				if (($time2 - $start) > 10)
				 {
				   fclose($fp_in); 
				   gzclose($fp_out); 
				   unlink($dest); 
				   OPCXmlExport::log('Deleting '.$dest); 
				   return -1; 
				 }
			}
            fclose($fp_in); 
            } 
          else $error=true; 
        gzclose($fp_out); 
        } 
      else $error=true; 
    if($error) return -2; 
      else return $dest; 
    } 

 
 public function open()
 {
   OPCXmlExport::log('Opening '.$this->file.$this->suffix.'... '); 
   
   $res = false; 
   $data = ''; 
   if ($this->method === 2)
	$res = @file_put_contents($this->file.$this->suffix, $data, LOCK_EX); 
	
	if ($this->method === 1)
	$res = @JFile::write($this->file.$this->suffix, $data); 
   
   if ($res === false)
   {
     $this->block = true; 
   }
   
   return $res; 
 }
 public function close()
 {
   if ($this->isFinished()) return; 
   OPCXmlExport::log( 'closing '.$this->file.$this->suffix.'... '); 
    
   if ($this->block) return; 
   if ($this->method === 1) {
     JFile::write($this->file.$this->suffix, $this->buffer); 
   }
   $file = $this->file; //substr($this->file, 0, -4); 
   
   
   $endfile = ''; 
   JFile::write($this->folder.'finished.tmp', $endfile); 
   
   
   $this->buffer = ''; 
 }
 public function isFinished() {
	clearstatcache(true, $this->folder.'finished.tmp'); 
	if (file_exists($this->folder.'finished.tmp')) {
		return true; 
	}
	return false; 
 }	 
 public function group() {
	
	clearstatcache(false); 
	clearstatcache(true); 
	
	if (file_exists($this->folder.'finished.tmp')) {
	jimport( 'joomla.filesystem.folder' );
	
	$path = $this->folder; 
	if (substr($path, -1, 1) == DIRECTORY_SEPARATOR) $path = substr($path, 0, -1); 
	
	$files = JFolder::files($path, 'tmp', false, true); 
	if (empty($files)) {
		OPCXmlExport::log("OPC Export: cannot find temp files"); 
	}
	
	sort($files); 
	
	
	if (!empty($files)) {
	
	
	$fh = fopen($this->file, 'w+');
	if (empty($fh)) {
		OPCXmlExport::log("OPC Export: cannot open file for writing ".$this->file); 
	}
	$bytes = 0; 
	foreach ($files as $file) {
		
		//Get all the matches from the file
        $fileContents = file_get_contents($file);
		$fileContents = trim($fileContents); 
		$bytesF = strlen($fileContents); 
		$bytes += $bytesF;
		
		OPCXmlExport::log("OPC Export: grouping file in folder ".$file.' into '.$this->file.' bytes '.$bytesF); 
		fputs($fh, $fileContents);
	}
	
	 fclose($fh);
	 
	 OPCXmlExport::log("OPC Export: written ".$bytes.' to '.$this->file); 
	}
	else {
		
	}
	
	
	}
	else {
		 OPCXmlExport::log("OPC Export: finished.tmp does not exists ! ".$this->folder.'finished.tmp'); 
	}
	 
 }
 
 
 public function write($data)
 {
   
    OPCXmlExport::log('.'); 
    if (empty($data)) return; 
	
	
	if ($this->block) return; 
	
    if ($this->method === 2)
	{
	clearstatcache(true, $this->file.$this->suffix); 
	if (!file_exists($this->file.$this->suffix))
	{
		//echo 'creating a new file: '.$this->file; 
	 OPCXmlExport::log("OPC Export: creating new file ".$this->file.$this->suffix.' datalength '.strlen($data)); 
	 file_put_contents($this->file.$this->suffix, $data, LOCK_EX ); 
	 
	}
	else
	{
	 file_put_contents($this->file.$this->suffix, $data, FILE_APPEND | LOCK_EX); 
	}
	}
	
	
	
	
	if ($this->method === 1)
	$this->buffer .= $data; 
	
 }
 
}