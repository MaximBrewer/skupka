<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	
	jimport( 'joomla.filesystem.file' );
	
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
    
  // Load the virtuemart main parse code

	//require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
//	require_once( JPATH_ROOT . '/includes/domit/xml_domit_lite_include.php' );
//	require_once( JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'ajax'.DIRECTORY_SEPARATOR.'ajaxhelper.php' );	
	

	
	class JModelOrder_export extends OPCModel
	{	
		function __construct()
		{
			parent::__construct();
		
		}
		
		function getJforms() {
			
			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
			$ehelper = new OnepageTemplateHelper;
			$xmlfiles = $ehelper->getXMLs(); 
			
			 require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php'); 
	     $ehelper = new OnepageTemplateHelper;
		 $export_templates = $ehelper->getExportTemplates('ALL');
		 
		 $forms = array(); 
		 $xmlpath = JPATH_COMPONENT_ADMINISTRATOR_ONEPAGE.DIRECTORY_SEPARATOR.'export'.DIRECTORY_SEPARATOR.'templates';
		 foreach ($export_templates as $tx) {
			

			$tid = (int)$tx['tid']; 
			$form = $this->getJform($tx['tid_xml'], 'order_export_config', $xmlpath, $tid, $tx['tid_configkey']); 
			
			if (!empty($form))
			$forms[$tx['tid_xml']] = $form; 
			
		 }
			
			return $forms; 			
			
		}
		
		
    function getJform($file, $data_config='tracking_config', $xmlpath=null, $tid=0, $key='')
    { 
	
	    if (empty($key)) $key = $file; 
		
		if (!class_exists('VmConfig'))	  
		{
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		}
		VmConfig::loadConfig(); 

	 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jformrender.php');  
	
	 
	  	  
	 $ret = array(); 

	 
	  	   
		   
		   
		   $path = $xmlpath.DIRECTORY_SEPARATOR.$file; 
		  
		   if (!file_exists($path)) {
		     return ''; 
		   }
	   	   
	   
	   $default = new stdClass();  
	   
	   $data = OPCconfig::getValue($data_config, $key, $tid, $default);
	  
	  
	   
	   $title = $description = ''; 
	   
	   if (function_exists('simplexml_load_file'))
	   {
	   $fullxml = simplexml_load_file($path);
	   
	   $title = $fullxml->name; 
	   $description = $fullxml->description; 
	   
	   
	   }
	   else return ''; 
	   
	   
	   
	   
	   
	   
	   
	   	   $xml = file_get_contents($path); 
		$xml = str_replace('extension', 'form', $xml); 
		$xml = str_replace('params', 'fieldset', $xml); 
		$xml = str_replace('<fieldset', '<fields name="'.$key.'"><fieldset name="test" label="'.$title.'" ', $xml); 
		$xml = str_replace('param', 'field', $xml); 
		$xml = str_replace('</fieldset>', '</fieldset></fields>', $xml); 
		//$fullxml = simplexml_load_string($xml);

		
		// removes BOM: 
		$bom = pack('H*','EFBBBF');
		$text = preg_replace("/^$bom/", '', $xml);
		if (!empty($text)) $xml = $text; 

		$t1 = simplexml_load_string($xml); 
		if ($t1 === false) return ''; 
		
		
		
		
	    $test = JForm::getInstance($key, $xml, array(),true);
		
		$multilang_atribs = array(); 
		
	
		
		
		
		
		$nm = array(); 
		
		
		
		foreach ($data as $k=>$vl)
		{
			$test->setValue($k, $key, $vl); 
			if (isset($nm[$key]))
			if (isset($nm[$key]->$k)) $nm[$key]->$k = $vl; 
			
		}
		
		$fieldSets = $test->getFieldsets();
		
		
		
		
		$test = OPCparametersJForm::render($test); 
		
		
		
	   
	   
	  
	   $ret['params'] = $test;
		if (empty($title))
	   $ret['title'] = $file; 
	    else $ret['title'] = (string)$title; 
		
		if (isset($nm[$key]))
		$ret['nm'] = $nm[$key]; 
	   
	    $ret['description'] = (string)$description; 
		
		
	 
	   return $ret; 
	}
		
		function store()
		{
		
	    require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
	    // load basic stuff:
	    OPCLang::loadLang(); 

		
		
		$user = JFactory::getUser();

		
		
		 jimport('joomla.filesystem.folder');
         jimport('joomla.filesystem.file');
		 jimport('joomla.filesystem.archive');
		jimport('joomla.archive.archive'); 
	        $msg = '';
			
			
		
		 $db = JFactory::getDBO();
		 $data = JRequest::get('post');
		
		 
		 $this->storeGeneral($data); 
		 
		 
		 
		 
	foreach ($data as $k=>$d)
	{

	  
	  
		if (strpos($k, 'tid_')!==false && (strpos($k, 'payment_contentid')===false))
		{
		 {
		  /* we have a standard variable:
		  tid_special_, tid_ai_, tid_num_, tid_back_,  tid_forward_
		  tid_nummax_, tid_itemmax_
		  tid_type_
		  */
		  if (!defined($k))
		  {
		  $this->setTemplateSetting($k, $data[$k]);
		  //echo 'template setting: '.$k.'value: '.$data[$k];
		  define($k, $data[$k]);
		  }
		  $a = explode('_', $k);
		  if (count($a)==3)
		  {
		   $tid = $a[2];
		   
		   
		   $checkboxes = array('tid_special_', 'tid_ai_', 'tid_num_', 'tid_forward_', 'tid_back_', 'tid_enabled_', 'tid_foreign_', 'tid_email_', 'tid_autocreate_');
		   foreach ($checkboxes as $ch)
		   {
		   if (!isset($data[$ch.$tid]) && (!defined($ch.$tid)))
		   {
		    $this->setTemplateSetting($ch.$tid, 0);
		    define($ch.$tid, '0');
		    //echo ':'.$ch.$tid.' val: 0';
		   }
		   }
		  }
			
		 }
		}
		
		
		
	  
	} 
	
	
	 require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php'); 
	     $ehelper = new OnepageTemplateHelper;
		 $export_templates = $ehelper->getExportTemplates('ALL');
		 
		 
		 foreach ($export_templates as $tx) {
		  $xmlf = $tx['tid_configkey']; 
		  
		  
		  $tid = $tx['tid']; 
		  if (!empty($data[$xmlf])) {
			  $a = (object)$data[$xmlf]; 
			  OPCconfig::store('order_export_config', $xmlf, $tid, $a);
			  
			  
		  }
		 }
	
	return true; 
	
	}
	
	function storeGeneral($data)
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		if (!empty($data['api_url']))
		{
			OPCconfig::store('xml_export', 'api_url', 0, $data['api_url']); 
		}
		
		if (!empty($data['api_username']))
		{
			OPCconfig::store('xml_export', 'api_username', 0, $data['api_username']); 
		}
		
		if (!empty($data['api_password']))
		{
			OPCconfig::store('xml_export', 'api_password', 0, $data['api_password']); 
		}
		if (!empty($data['xml_debug']))
		{
			OPCconfig::store('xml_export', 'xml_debug', 0, true); 
		}
		else
		{
			OPCconfig::store('xml_export', 'xml_debug', 0, false); 
		}
		
		if (!empty($data['xml_export']))
		{
			OPCconfig::store('xml_export', 'xml_export', 0, $data['xml_export']); 
		}
		
		
		
	}
	
	function checkTable()
	{
	   return; 
	   $q = "show columns from #__onepage_exported where field = 'status'";
		 $db = JFactory::getDBO();
		 $db->setQuery($q); 
		 $x = $db->loadAssocList(); 
		 if (!empty($x))
		 {
		   if (stripos($x[0]['Type'], 'enum') !== false)
		   {
		     $db = JFactory::getDBO();
			 $db->setQuery("ALTER TABLE  `#__onepage_exported` CHANGE  `status`  `status` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'NONE'");
			 $db->query(); 
		   }
		 }
	}
		
	function setTemplateSetting($k, $value)
	{ 
	
	if ($value === 'on') $value = '1';
	
		  $db = JFactory::getDBO();
		  
		  $a = explode('_',$k);
		  
		  if (count($a)==3)
		  {
		   $keyname = $a[0].'_'.$a[1];
		  
		   $tid = $a[2];
		   if (is_numeric($tid))
		   {
		   
		   if (is_array($value))
		   {
			   $value = json_encode($value); 
		   }
		   
		   $keyname = $db->escape($keyname);
		   
		  
		   
		   $q = 'select `value` from #__onepage_export_templates_settings where `keyname` = "'.$keyname.'" and `tid` = "'.$tid.'"';
		   $db->setQuery($q);
		   $res = $db->loadResult();
		   
		   
		   $value = $db->escape($value);
		   $msg = $db->getErrorMsg(); if (!empty($msg)) {echo $msg; die(); }
		   if (!isset($res) || $res===false)
		   {
		    // ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , `tid` INT NOT NULL DEFAULT '0', `keyname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', `value` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', `original` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' )
		    $q = 'insert into #__onepage_export_templates_settings (`id`, `tid`, `keyname`, `value`, `original`) values (NULL, "'.$tid.'", "'.$keyname.'", "'.$value.'", ""); ';
		    
		   }
		   else
		   {
		    $q = 'update `#__onepage_export_templates_settings` set `value` = "'.$value.'" where `tid`="'.$tid.'" and `keyname`= "'.$keyname.'"';
		     //($res != $data[$k]))
		   }
		  
		   $db->setQuery($q);
		   $db->query();
		   $msg = $db->getErrorMsg(); if (!empty($msg)) {echo $msg; die(); }
		   }
		  }
	
	}
}

