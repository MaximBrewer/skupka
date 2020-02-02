<?php
/**
 * @version		
 * @package		RuposTel OnePage Utils
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 

class JModelXmlexport extends OPCModel
{
	
  public function getGeneralXmlForm($file)
  {
	  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
	  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jformrender.php');  
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  
	  $gpath = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'forms'.DIRECTORY_SEPARATOR.'export.xml'; 
	   
	   $xml = file_get_contents($gpath); 
	   $xml = str_replace('{general}', $file, $xml); 
	   
	   $general = JForm::getInstance($file.'_general', $xml, array(),true);
	   $general->removeField('run_only_for_affiliate', $file); 
	   
	    $default = new stdClass();  
	   //$data->adwords_id = 1; 
	   //$data = OPCconfig::getValue('tracking_config', $file, 0, $default);
		
	   $data = OPCconfig::getValue('xmlexport_config', $file, 0, $default); 
	  
	   
		foreach ($data as $k=>$vl)
		{
		  if ($general->setValue($k, $file, $vl)===false)
		    {
			
			}
		}
		
		$fieldSets = $general->getFieldsets();
		$generalf = OPCparametersJForm::render($general); 
		return $generalf; 
  }
  
  public static function getCategoryForms($category_id=0, $product_id=0) {
	   require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	   require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jformrender.php');  
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'xmlexport.php'); 
	   
	   $i = new JModelXmlexport(); 
	   
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 $i->getGeneral($i); 

		$i->avai = $i->getAvai(); 
		$i->trackingfiles = $i->getPhpExportThemes(); 
		$ret = array(); 
		foreach ($i->trackingfiles as $file) {
			
			$xc = new VirtueMartControllerXmlexport(); 
			$class = $xc->getClass($file); 
			if (empty($class)) {
				
				continue; 
			}
			
		 if (!empty($category_id))
		 {
			 $ff = $i->getCategoryXmlForm($file, $category_id); 
			 
			 
		 if (!empty($ff))
		 $ret[$file] = $ff;
		 }
		 if (!empty($product_id))
		 {
			 $ff = $i->getProductXmlForm($file, $product_id); 
		     if (!empty($ff))
			 $ret[$file] = $ff; 
		 }
		}
		return $ret; 
		
  }
  private function getCategoryXmlForm($file, $category_id)
  {
	  
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  
	  	   $title = $description = ''; 
	   $path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'xmlexport'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'.xml'; 
	   
	   
	   
	   if (function_exists('simplexml_load_file'))
	   {
	   $fullxml = simplexml_load_file($path);
	   if ($fullxml === false) return ''; 
	   $title = $fullxml->name; 
	   $description = $fullxml->description; 
	   
	   
	   }
	   else
	   return ''; 
   
	   
		
		$catForm = $fullxml->category_form; 
		$newXml = $catForm->asXML(); 
		$newXml = (string)$newXml; 
		
		
		if (empty($newXml)) return '';
		
		$newXml = str_replace('<category_form', '<fields name="'.$file.'"', $newXml); 
		$newXml = str_replace('</category_form', '</fields', $newXml); 
		//$newXml = str_replace('</fieldset', '<field type="hidden" name="category_id" /></fieldset', $newXml); 
		$newXml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n".'<form>'.$newXml.'</form>'; 
		
	 
		$test = JForm::getInstance($file, $newXml, array(),true);
		JForm::addFieldPath(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'xmlexport'.DIRECTORY_SEPARATOR.'fields'); 
		
		$obj = new stdClass(); 
		
		$default = new stdClass(); 
	    $res = OPCconfig::getValue('xmlexport_pairing', $file, $category_id, $default); 
		$obj->paired_category = null; 
		
		if (!empty($res))
	    if (isset($res->id))
		$obj->paired_category = $res->id; 
	
		$obj->current_category = $category_id; 
		
		$data = array('paired_category' => $obj); 
		//$test->bind($data); 
		foreach ($data as $k=>$vl)
		{
		  $test->setValue($k, $file, $vl); 
		}

		$fieldSets = $test->getFieldsets();
		
	    //$test->load($fullxml);
		
		$test = OPCparametersJForm::render($test); 
		return $test; 

  }
  private function getProductXmlForm($file) 
  {
	  
  }
  private function storeGeneral()
  {
      $enabled = JRequest::getVar('xml_general_enable', false); 
     if (!empty($enabled)) $enabled = true; 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 OPCconfig::store('xmlexport_config', 'xml_general_enable', 0, $enabled); 
	 
	 $path = JRequest::getVar('xml_export_path', 'export'); 
	 
	 $path = str_replace('/', DS, $path); 
	 $path = str_replace('\\', DS, $path); 
	 if (substr($path, -1) != DS) $path .= DS; 
	 
	 if (strpos($path, JPATH_SITE.DIRECTORY_SEPARATOR)===0)
	 {
	   $path = substr($path, strlen(JPATH_SITE.DIRECTORY_SEPARATOR)); 
	 }
	 OPCconfig::store('xmlexport_config', 'xml_export_path', 0, $path); 
	
	 
	 
	 $default = JURI::root(); 
	 if (substr($default, -1) != '/') $default .= '/'; 
	 $livesite = JRequest::getVar('xml_live_site', $default); 
	 
	 OPCconfig::store('xmlexport_config', 'xml_live_site', 0, $livesite); 
	 //$data = OPCconfig::getValue('xmlexport_config', 'xml_live_site', 0, ''); 
	 
	 //
	 
	 $custom_pair = JRequest::getVar('custom_pair', array()); 
	 $custom_pair_other = JRequest::getVar('custom_pair_other', array()); 
	 foreach ($custom_pair as $id=>$val)
	 {
		 if (empty($val))
		 {
			 if (!empty($custom_pair_other[$id]))
				 $val = $custom_pair_other[$id]; 
		 }
		 OPCconfig::store('tracking_config_pairing', 'custom_pairing', $id, $val); 
	 }
	 
	 $xml_export_customs = JRequest::getVar('xml_export_customs', false); 
	 OPCconfig::store('xmlexport_config', 'xml_export_customs', 0, $xml_export_customs); 
	 
	  $xml_export_customs = JRequest::getVar('xml_export_test', ''); 
	 OPCconfig::store('xmlexport_config', 'xml_export_test', 0, $xml_export_customs); 

	  $xml_export_customs = JRequest::getVar('xml_export_disable_compression', ''); 
	 OPCconfig::store('xmlexport_config', 'xml_export_disable_compression', 0, $xml_export_customs); 
	 
	 
	 $xml_disable_categorycache = JRequest::getVar('xml_disable_categorycache', false); 
	 OPCconfig::store('xmlexport_config', 'xml_disable_categorycache', 0, $xml_disable_categorycache);
	 
	 $num = JRequest::getInt('xml_export_num', 100000); 
	 OPCconfig::store('xmlexport_config', 'xml_export_num', 0, $num); 
	 
	 jimport( 'joomla.filesystem.folder' );
	 if (JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.$path)===false)
	 return JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY', $path); 
	 
	 jimport('joomla.filesystem.file');
	 $test = true; 
	 $data = ''; 
	 $ret = JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.$path.'test.writable', $data); 
	 if ($ret === false)
	 return JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', 'test_file', $path); 
	 
	 $ret = JFile::delete(JPATH_SITE.DIRECTORY_SEPARATOR.$path.'test.writable'); 
	 if ($ret === false)
	 return 'Cannot delete test file in '.JPATH_SITE.DIRECTORY_SEPARATOR.$path; 
	 
	 $delete = JRequest::getVar('delete_ht', 0); 
	 $xml_export_disable_compression = OPCconfig::getValue('xmlexport_config', 'xml_export_disable_compression', 0, false); 
     if ((empty($delete)) && (empty($xml_export_disable_compression)))
	 {
	 if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$path.'.htaccess'))
	 {
	    $data = $this->getHt(); 
		$x = JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.$path.'.htaccess', $data); 
	 }
	 }
	 else
	 {
	   JFile::delete(JPATH_SITE.DIRECTORY_SEPARATOR.$path.'.htaccess'); 
	 }
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
	$config = new JModelConfig(); 
		 $config->loadVmConfig(); 
	
	if ($enabled) {
	if (file_exists(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php')) {
	$x = file_get_contents(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php'); 
	if (strpos($x, 'plgVmBuildTabs')===false) {
		
		$newCode = '
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger(\'plgVmBuildTabs\', array(&$view, &$load_template));
		'; 
		$search = 'foreach ( $load_template as $tab_content => $tab_title ) {'; 
	    $count = 0;
		$x = str_replace($search, $newCode.$search, $x, $count); 
		if ($count > 0) {
			jimport( 'joomla.filesystem.file' );
		if (JFile::copy(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php', JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.opc_bck.php')!==false) {
		 JFile::write(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'adminui.php', $x); 
		 JFactory::getApplication()->enqueueMessage(JText::_('COM_ONEPAGE_ADDED_SUPPORT_FOR_TABS')); 
			}
		}
		}
	}
	 
	 $config->installext(-1, 'vmaddtabs'); 
	 $db = JFactory::getDBO(); 
	 $q = " UPDATE `#__extensions` SET  `enabled` =  1, `state` = 0 WHERE  element = 'vmaddtabs' and folder = 'system' limit 1"; 
	 $db->setQuery($q); 
	 $db->query(); 
			
	
	}
	
	
	
	 return; 
  }
  
  public function getCoreGroups()
  {
	  return array ('size', 'gender', 'age_group', 'size_type', 'size_system', 'color', 'material', 'pattern', 'manufacturer', 'ean', 'isbn', 'mpm', 'bloz'); 
  }
  
  public function buildCustomFields()
  {
	  $db = JFactory::getDBO(); 
	  $q = 'select * from #__virtuemart_customs where 1'; 
	  $db->setQuery($q); 
	  $arr = $db->loadAssocList(); 
	  if (empty($arr)) $arr = array(); 
	  
	  if (defined('VM_VERSION') && (VM_VERSION >= 3))
		  $custom_value_col = 'customfield_value'; 
	  else
	   $custom_value_col = 'custom_value'; 
   
	  foreach ($arr as $k=>$v)
	  {
		  $arr[$k]['values'] = array(); 
		  $id = (int)$v['virtuemart_custom_id']; 
		  $q = 'select distinct `'.$custom_value_col.'` from #__virtuemart_product_customfields where virtuemart_custom_id = '.$id; 
		  $db->setQuery($q); 
		  $res = $db->loadAssocList(); 
		  if (!empty($res))
		  {
			  foreach ($res as $v2)
			  {
				  $arr[$k]['values'][] = $v2[$custom_value_col]; 
			  }
		  }
		  
	  }
	 
	  
	  return $arr; 
  }
  
  
  public function isPluginEnabled($item, &$forms)
  { 
    if (is_object($forms[$item]['config']))
	if (!empty($forms[$item]['config']->enabled))
			   return true; 
			   
	return false; 
  }
  
  public function getNumProducts()
  {
    $db = JFactory::getDBO(); 
	$q = 'select count(*) from #__virtuemart_products'; 
	$db->setQuery($q); 
	return $db->loadResult(); 
	
  }
  public function getGeneral(&$ref)
  {
    $ref->enabled = $this->isEnabled(); 
    $ref->xml_export_path = $this->getPath(); 
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	
	$default = JURI::root(); 
	
	
	if (substr($default, -1) != '/') $default .= '/'; 
    $ref->xml_live_site = OPCconfig::getValue('xmlexport_config', 'xml_live_site', 0, $default); 
	$ref->xml_export_num = OPCconfig::getValue('xmlexport_config', 'xml_export_num', 0, 100000); 
	$ref->product_count = $this->getNumProducts(); 
	
	
	$ref->xml_export_customs = OPCconfig::getValue('xmlexport_config', 'xml_export_customs', 0, false); 
    $ref->coreGroups = $this->getCoreGroups(); 
	
	$ref->xml_export_test = OPCconfig::getValue('xmlexport_config', 'xml_export_test', 0, ''); 
	
	$ref->xml_export_disable_compression = OPCconfig::getValue('xmlexport_config', 'xml_export_disable_compression', 0, false); 
	
  }
  function getHt()
  {
    $data = '

RewriteEngine On	
SetEnvIfNoCase Request_URI "\.xml$" no-gzip dont-vary
SetEnvIfNoCase Request_URI "\.csv$" no-gzip dont-vary
	
AddEncoding gzip .gz
<FilesMatch "\.xml\.gz$">
  ForceType application/xml
</FilesMatch>
<FilesMatch "\.csv\.gz$">
  ForceType text/csv
</FilesMatch>
<FilesMatch "\.(gz)$">
  	Header set Content-Encoding: gzip
	Header set Cache-Control "max-age=1, private"
  	SetEnv no-gzip 1
</FilesMatch>

RewriteCond %{HTTP:Accept-encoding} gzip
RewriteCond %{REQUEST_FILENAME}\.gz -s
RewriteRule ^(.*)\.xml $1\.xml\.gz [L,QSA,T=appliction/xml]

RewriteCond %{HTTP:Accept-encoding} gzip
RewriteCond %{REQUEST_FILENAME}\.gz -s
RewriteRule ^(.*)\.csv $1\.csv\.gz [L,QSA,T=text/csv]

RewriteRule \.xml\.gz$ - [T=application/xml,E=no-gzip:1]
RewriteRule \.csv\.gz$ - [T=text/csv,E=no-gzip:1]


	'; 
	return $data; 
  }
  function checkCompression($ref)
  {
	$path = $this->getXMLPath($ref); 
    if (!file_exists($path)) return; 
	
	JFolder::create($path.'compression_test'); 
	JFile::write($path.'compression_test'.DIRECTORY_SEPARATOR.'.htaccess', $data); 
	
	$data = '<xml><data>Test OK</data></xml>'; 
	$data = gzencode ($data); 
	JFile::write($path.'compression_test'.DIRECTORY_SEPARATOR.'test.xml.gz', $data); 
	
	
	
  }
  
  
  function store()
  {
  
     require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	
	  
    require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'config.php'); 
	
	/*
	if (!OPCJ3)
	{
	 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opcparameters.php'); 
	}
	else
	{
	   require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jformrender.php'); 
	}
	*/
	
	
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 $files = $this->getPhpExportThemes();
		
		 $data = JRequest::get('post');
		  jimport('joomla.filesystem.file');
		
	  $msg = $this->storeGeneral(); 		
	
	 
     foreach ($files as $file)
	 {
	  
	   $file = JFile::makeSafe($file);
	
	   $path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'xmlexport'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'.xml'; 
	   $nd = new stdClass(); 
	   
	   //$params = new OPCparameters($nd, $file, $path, 'opctracking'); 
	   $enabled = JRequest::getVar('plugin_'.$file, false); 
	   if (!$enabled) $data[$file]['enabled'] = false;
	  else $data[$file]['enabled'] = true;
	   $config = OPCconfig::buildObject($data[$file]); 
	   
	   $default = new stdClass();  
	   $dataO = OPCconfig::getValue('xmlexport_config', $file, 0, $default); 
	   /*
	   if (isset($dataO->catlink)) $c1 = $dataO->catlink; 
	   if (isset($config->catlink)) {
	      if ($config->catlink != $c1) {
			  $res = OPCconfig::clearConfig('xmlexport_pairing', $file); 
		      JFactory::getApplication()->enqueueMessage('Category pairing data were deleted due to change of taxonomy URL'); 
		  }
	   }
	   */
	   
	   OPCconfig::store('xmlexport_config', $file, 0, $config); 
	   /*
	   if (false)
	   foreach ($data[$file] as $key=>$param)
	    {
		  echo $key.' '.$param; 
		}
	   */
	   
	 }
	 
	 
	if (!empty($msg)) return $msg; 
	   
	   
	
  
   return;
  }
 
  function getPhpExportThemes()
{
  $path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'xmlexport'.DIRECTORY_SEPARATOR.'php'; 
  if (!file_exists($path)) return array(); 
  jimport('joomla.filesystem.folder');
  jimport('joomla.filesystem.file');
  $files = JFolder::files($path, $filter = '.php', false, true);
  $arr = array(); 
  
  foreach ($files as $f)
  {
    $pi = pathinfo($f); 
	$file = $pi['filename']; 
	$jf = JFile::makesafe($file);
    // security here: 	
	if ($jf != $file) continue; 
	$path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'xmlexport'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'.xml'; 
	if (!file_exists($path)) continue; 
	$arr[] = $file; 
	
    
  }
  return $arr; 
  
}

 function getPath()
 {
    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
    return OPCconfig::getValue('xmlexport_config', 'xml_export_path', 0, 'export'.DIRECTORY_SEPARATOR); 
 }
  
 function getConfig()
 {
 
 } 
 function isEnabled()
  {
    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
    return OPCconfig::getValue('xmlexport_config', 'xml_general_enable', 0, false); 
  }
  
  function getLanguages()
  {
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 $langs = VmConfig::get('active_languages', array()); 
		 if (empty($langs)) $langs = array('en-GB'); 
		 return $langs; 
		 
		 
  }
  
  function getShopperGroups()
  {
  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 $config = new JModelConfig(); 
		 $config->loadVmConfig(); 
		 return $config->listShopperGroups(); 
  }
  
  
  function getThemeConfig($file)
  {
  
     
    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	$default = new stdClass(); 
	 $data = OPCconfig::getValue('xmlexport_config', $file, 0, $default);
	  
	   $generalconfig = new stdClass(); 
	   
	   $this->getGeneral($generalconfig); 
	   
	   if (!isset($data->xmlfile))
	   $data->xmlfile = $file.'.xml'; 
	   
	   if (empty($data->xmlfile))
	   $data->xmlpath = $generalconfig->xml_export_path.$file.'.xml'; 
	   else
	   $data->xmlpath = $generalconfig->xml_export_path.$data->xmlfile; 
	   
	   $data->file = $data->name = $data->entity = $file; 
	   
	   
	   
	   $data->xmlurl = $this->getXMLUrl($generalconfig, $data); 
	   $data->xmlurl2 = $this->getXMLUrl($generalconfig, $data, true); 
	   
	   if (!isset($data->cname))
	   $data->cname = $file; 
	   
	   return $data; 
	 
	 
  }
  function getJforms($files)
    { 
	
	 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jformrender.php'); 
	  /*
	  if (!OPCJ3)
	  {
	 require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opcparameters.php'); 
	  }
	  else
	  {
	  
	  }
	  */
	  
	 $ret = array(); 
	 foreach ($files as $file)
	 {
	   $path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'xmlexport'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'.xml'; 
	   if (!file_exists($path)) continue; 
	   
	   
	   //$data = new stdClass();  
	   //$data->adwords_id = 1; 
	   
	   $data = $this->getThemeConfig($file); 
	   
	   $title = $description = ''; 
	   
	   if (function_exists('simplexml_load_file'))
	   {
	   $fullxml = simplexml_load_file($path);
	   
	   $title = $fullxml->name; 
	   $description = $fullxml->description; 
	   
	   
	   }
	   else
	   return; 
	   
	   /*
	   if (!OPCJ3)
	   {
	    $params = new OPCparameters($data, $file, $path, 'opcexport'); 
	    $test = $params->vmRender($file); 
	   }
	   else
	   */
	   {
	   
	   
	   
	   	   $xml = file_get_contents($path); 
		$xml = str_replace('extension', 'form', $xml); 
		$xml = str_replace('params', 'fieldset', $xml); 
		$xml = str_replace('<fieldset', '<fields name="'.$file.'"><fieldset name="test" label="'.$title.'" ', $xml); 
		$xml = str_replace('param', 'field', $xml); 
		$xml = str_replace('</fieldset>', '</fieldset></fields>', $xml); 
		//$fullxml = simplexml_load_string($xml);
		
		// removes BOM: 
		$bom = pack('H*','EFBBBF');
		$text = preg_replace("/^$bom/", '', $xml);
		if (!empty($text)) $xml = $text; 
		
		
		$x1 = stripos($xml, '<category_form'); 
		if ($x1 !== false)
	    {
			$end = '</category_form>'; 
			 $x2 = stripos($xml, $end); 
			 $xml2 = $xml; 
			 $xml = substr($xml2, 0, $x1).substr($xml2, $x2+strlen($end)); 
			 
		}
	
		//echo $file; @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); 
		//echo $xml; 
		$t1 = simplexml_load_string($xml); 
		if ($t1 === false) continue; 
		
		
	    $test = JForm::getInstance($file, $xml, array(),true);
		
		//$test->bind($data); 
		foreach ($data as $k=>$vl)
		{
		  $test->setValue($k, $file, $vl); 
		}

		$fieldSets = $test->getFieldsets();
		
	    //$test->load($fullxml);
		
		$test = OPCparametersJForm::render($test); 
		
		
		
		//$test->bind($payment);
	   }
	   
	   
	   
	   
	   
	  
	   
	   $ret[$file]['config'] = $data; 
	   $ret[$file]['xml'] = $fullxml; 
	   $ret[$file]['params'] = $test;
		if (empty($title))
	   $ret[$file]['title'] = $file.'.php'; 
	    else $ret[$file]['title'] = (string)$title; 

		$ret[$file]['general'] = $this->getGeneralXmlForm($file); 
	    //$ret[$file]['category'] = $this->getCategoryXmlForm($file); 
		$ret[$file]['product'] = $this->getProductXmlForm($file); 
	    $ret[$file]['description'] = (string)$description; 
		
		
	 }
	 return $ret; 
	}
	
	function getAvai()
	{
	
	jimport( 'joomla.filesystem.folder' );
if(!class_exists('shopFunctionsF'))require(JPATH_VM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php');
				if (method_exists('shopFunctionsF', 'loadVmTemplateStyle'))
				{
				$vmtemplate = shopFunctionsF::loadVmTemplateStyle();
				
				if(is_Dir(JPATH_ROOT.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$vmtemplate.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'availability'.DIRECTORY_SEPARATOR)){
					$imagePath = 'templates'.DIRECTORY_SEPARATOR.$vmtemplate.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'availability'.DIRECTORY_SEPARATOR;
				} else {
					$imagePath = 'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'availability'.DIRECTORY_SEPARATOR;
				}
				}
				else
				$imagePath = 'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'availability'.DIRECTORY_SEPARATOR;
				$imagePath = JPATH_SITE.DIRECTORY_SEPARATOR.$imagePath; 
	  	  
	  jimport('joomla.filesystem.file');
	  $avail = JFolder::files($imagePath, '.gif|.png|.jpg|.jpeg', false, false);
	  
	  
	  $ret = array(); 
	  
	  
	  foreach ($avail as $img)
	  {
	      $obj = new stdClass(); 
	      $pattern = '/[^\w]+/'; //'[^a-zA-Z\s]'; 
		  $key2 = preg_replace( $pattern, '_', $img ); 
		  
		  
		  
		  //$key2 .= md5($img); 
	      $obj->$key2 = $img; 
		  $obj->img = $img; 
		  //1-2m.gif
		  
	  switch ($img)
      {
        case "24h.gif": 
    		    $avai = 24; 
    		   $deliverydate = "24 hours";
    		   break;
        case "ihned.gif": 
    		$avai = 24; 
    		$deliverydate = "24 hours"; 
    		break;
        case "2-3d.gif": 
    		$avai =  60; 
    		$deliverydate = "2-3 days"; 
    		break;
        case "48h.gif": 
    		$avai = 48; 
    		$deliverydate = "48 hours";
    		break;
		case "1-2m.gif":
			$avai = 60;
			$deliverydate = "1 to 2 months";
			break; 
		case "1-4w.gif":
			$avai = 14; 
			$deliverydate = "1 to 4 weeks";
			break; 
		case "14d.gif":
			$avai = 14; 
			$deliverydate = "2 weeks";
			break; 
	   case "24h.gif":
			$avai = 1; 
			$deliverydate = "24 hours";
			break; 
	   case "3-5d.gif":
			$avai = 4; 
			$deliverydate = "3 to 5 days";
			break; 
	   case "48h.gif":
			$avai = 2; 
			$deliverydate = "48 hours";
			break; 
	   case "7d.gif":
			$avai = 7; 
			$deliverydate = "7 days";
			break; 
	  case "not_available.gif":
			$avai = 60; 
			$deliverydate = "Not available";
			break; 		

        case "on-order.gif": 
    		$avai =  168; 
    		$deliverydate = "1 week"; 
    		break;
        default: 
    		$avai = 60; 
    		$deliverydate = "2-3 days"; 
        
      }
		  $obj->avai = $avai; 
		  $obj->deliverytext = $deliverydate; 
		  
		  $ret[$key2] = $obj; 
	  }
	  
	  return $ret; 
	  
	}
	function getXMLUrl($generalconfig, $extconfig, $ondemand=false)
	{
	   $path = $generalconfig->xml_export_path; 
	   if (stripos($path, JPATH_SITE)===0)
	   $path = substr($path, strlen(JPATH_SITE)); 
	   $path = str_replace(DS, '/', $path); 
	   if (substr($path, 0,1) == '/') $path = substr($path, 1); 
	   $url = $generalconfig->xml_live_site.$path.$extconfig->xmlfile; 
	   
	   if (!empty($ondemand)) {
	     $url = $generalconfig->xml_live_site.'index.php?option=com_onepage&view=xmlexport&format=opchtml&tmpl=component&lang=en&dowork=dowork&print=1&file='.$extconfig->name; 
	   }
	   
	   return $url; 
	}
	
	function getXMLPath($generalconfig)
	{
	   $path = $generalconfig->xml_export_path; 
	   if (stripos($path, JPATH_SITE)===0) return $path; 
	   $path = JPATH_SITE.DIRECTORY_SEPARATOR.$path; 
	   
	   
	   return $path; 
	}
}
