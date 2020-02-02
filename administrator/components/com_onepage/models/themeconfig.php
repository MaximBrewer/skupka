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


// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cache.php'); 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 

/**
* Cache Model
*
* @package		Joomla.Administrator
* @subpackage	com_cache
* @since		1.6
*/
class JModelThemeconfig extends OPCModel
{
	function __construct() {
		parent::__construct();

	}
	function getForm()
	{
	  $theme = OPCconfig::get('selected_template'); 
	  				  jimport('joomla.filesystem.folder'); 
				  jimport('joomla.filesystem.file'); 
	  $theme = JFile::makeSafe($theme); 
	  $file = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'config.xml'; 
	  if (file_exists($file))
	   {
	   
	      $config_name = $theme; 
	      $form = $this->renderForm($theme, $file); 
		  return $form; 
	   }
	   
	   return ''; 
	  
	  
	  
	  
	}
	
	private function renderForm($file, $path)
	{
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jformrender.php');  
	 
	 
	  	  
	 $ret = array(); 
	 
	 
	  
	   if (!file_exists($path)) return; 
	   
	   
	   $default = new stdClass(); 
       $default->isEmpty = true; 	   
	   //$data->adwords_id = 1; 
	   $data = OPCconfig::getValue('theme_config', $file, 0, $default, false);
	   
	   
	   
	   $title = $description = ''; 
	   
	   if (function_exists('simplexml_load_file'))
	   {
	   $fullxml = simplexml_load_file($path);
	   
	   $title = $fullxml->name; 
	   $description = $fullxml->description; 
	   
	   
	   }
	   
	   
	   
	   
	   
	   
	   
	   	$xml = file_get_contents($path); 
		$xml = str_replace('extension', 'form', $xml); 
		$xml = str_replace('params', 'fieldset', $xml); 
		$xml = str_replace('<fieldset', '<fields name="'.$file.'"><fieldset name="test" label="'.$title.'" ', $xml); 
		$xml = str_replace('param', 'field', $xml); 
		$xml = str_replace('</fieldset>', '</fieldset></fields>', $xml); 
		

		
		// removes BOM: 
		$bom = pack('H*','EFBBBF');
		$text = preg_replace("/^$bom/", '', $xml);
		if (!empty($text)) $xml = $text; 

		$t1 = simplexml_load_string($xml); 
		if ($t1 === false) return; 
		
	    $test = JForm::getInstance($file, $xml, array(),true);
		
		
		if (!empty($data))
		{
			
		foreach ($data as $k=>$vl)
		{
		  $test->setValue($k, $file, $vl); 
		}
		}
		
		$fieldSets = $test->getFieldsets();
		
		
		$test = OPCparametersJForm::render($test); 
		
	   
	   
	   
	   
	   
	   
	   $ret['params'] = $test;
		if (empty($title))
	   $ret['title'] = $file.'.php'; 
	    else $ret['title'] = (string)$title; 

		
	   
	    $ret['description'] = (string)$description; 
		
		
	 
	 return $ret; 
	}
	
	public function store()
	{
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	
	 $theme = OPCconfig::get('selected_template'); 
	  				  jimport('joomla.filesystem.folder'); 
				  jimport('joomla.filesystem.file'); 
	  $theme = JFile::makeSafe($theme); 
	  $data = JRequest::get('post');
	    $config = OPCconfig::buildObject($data[$theme]); 
		
		
		   if (!is_object($config)) $config = new stdClass(); 
	   
	   $prevConfig = OPCconfig::getValue('theme_config', $theme, 0, $config); 	   
	   
	   
	  OPCconfig::store('theme_config', $theme, 0, $config); 
	  //$data = OPCconfig::getValue('theme_config', $theme, 0, $default, false);
	   
	  
	  
	   return ''; 
	}
	

}
