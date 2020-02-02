<?php
/**
 * @package		RuposTel Ajax search pro
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('_JEXEC') or die;

class rupSearch extends VmView {
  var $path; 
  function display($tpl = NULL)
   {
	 
	 $path = $this->getPath(); 
	 $this->path = $path; 
	 $this->app = JFactory::getApplication();
	 $this->searchcustom = '';
	 $this->searchCustomValues = '';
	 $this->search = null; 
	 $this->showsearch = false; 
	 $this->showcategory = false; 
	 $this->show_store_desc = false; 
	 $this->keyword = false; 
	 $this->categoryId = 0; 
	 /*
	 if (!isset($this->showproducts))
	 {
		 $x = debug_backtrace(); 
		 foreach ($x as $l) echo $l['file'].' '.$l['line']."<br />\n"; 
		 return; 
	 }
	 */
	
	 /*
	 if (!empty($this->overridepath) && (file_exists($this->overridepath)))
	 $this->path = $this->overridepath; 
     */
	 
	  // in vm3.0.17+ the products should be 2 dimensional
	 //$this->fallback = true; 
	 
	 $f = true; 
	 $f2 = false; 
	 if (method_exists($this, 'assignRef'))
	 {
		 $this->assignRef('show_store_desc', $f2); 
		 $this->assignRef('showproducts', $f); 
	 }
	 else
	 {
	  $this->showproducts = true; 
	 }
	 if (empty($this->category))
	 {
	 $category = new stdClass(); 
	 $category->category_name = ''; 
	 $category->category_description = ''; 
	 $category->haschildren = false; 
	 $category->limit_list_step = 99999; 
	 
	 if (method_exists($this, 'assignRef'))
	 {
		 
		 $this->assignRef('category', $category); 
	 }
	 else
	 {
	  $this->category = $category; 
	 }
	 }
	 
	 
	 if (empty($this->path)) return ''; 
	 ob_start(); 
	 if (file_exists($this->path))
	 include($this->path); 
	 $html = ob_get_clean(); 
	 
	 if (isset($this->overridepath))
	 {
	 // disable sorting: 
	 $html = str_replace('yagVmCategoryViewOrder"', 'yagVmCategoryViewOrder" style="display: none;"', $html); 
	 }
	 $html = str_replace('</noscript>', '', $html); 
	 $html = str_replace('<noscript>', '', $html); 
	 
	 
	 	if (class_exists('vmJsApi'))
		{
			if (method_exists('vmJsApi', 'jQuery'))
				vmJsApi::jQuery();
			if (method_exists('vmJsApi', 'jSite'))
				vmJsApi::jSite();
			if (method_exists('vmJsApi', 'cssSite'))
				vmJsApi::cssSite(); 
			if (method_exists('vmJsApi', 'jDynUpdate')) 
				vmJsApi::jDynUpdate(); 
			if (method_exists('vmJsApi', 'writeJs')) 
				$html .= vmJsApi::writeJs(); 
		}
	 $html .= '<script> if (typeof sessMin == \'undefined\') var sessMin = 15; </script>'; 
	 echo $html; 
   }
   
   function getPath($layout='default', $theme='')
   {
   
        if ($theme == 'default') $theme = ''; 
		$viewName = $this->viewName; 
		if (method_exists($this, 'addTemplatePath')) 
		{
	     $this->addTemplatePath(JPATH_VM_SITE.'/views/'.$viewName.'/tmpl');
		}
		else
		{
		if (method_exists($this, 'addIncludePath')) 
		{
	     $this->addIncludePath(JPATH_VM_SITE.'/views/'.$viewName.'/tmpl');
		}
			
		}
		//
		/*
        $vmtemplate = VmConfig::get('vmtemplate','default');
		if($vmtemplate=='default'){
			if(JVM_VERSION >= 2){
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home`="1"';
			} else {
				$q = 'SELECT `template` FROM `#__templates_menu` WHERE `client_id`="0" AND `menuid`="0"';
			}
			$db = JFactory::getDbo();
			$db->setQuery($q);
			$template = $db->loadResult();
		} else {
			$template = $vmtemplate;
		}
		*/
		
		
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
	  
		

		if (($template) && (method_exists($this, 'addTemplatePath'))) {
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'category'.DIRECTORY_SEPARATOR.'default.php'))
			 {
			   $this->addTemplatePath(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'category');
			   $this->path = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'category'.DIRECTORY_SEPARATOR.'default.php'; 
			   return $this->path; 
			 }
		    else
			{
			 $this->addTemplatePath(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName);
			 if (!empty($theme))
			 $this->path = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR.'default_'.$theme.'.php'; 
			 else
			 $this->path = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR.'default.php'; 
			}
		}
else
if (($template) && (method_exists($this, 'addIncludePath'))) {
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'category'.DIRECTORY_SEPARATOR.'default.php'))
			 {
			   $this->addIncludePath(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'category');
			   $this->path = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_rupsearch'.DIRECTORY_SEPARATOR.'category'.DIRECTORY_SEPARATOR.'default.php'; 
			   return $this->path; 
			 }
		    else
			{
			 $this->addIncludePath(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName);
			 if (!empty($theme))
			 $this->path = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR.'default_'.$theme.'.php'; 
			 else
			 $this->path = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR.'default.php'; 
			}
		}	
		if (file_exists($this->path)) return $this->path; 
		
			if (!empty($theme))
			$this->path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'default_'.$theme.'.php'; 
			else
			$this->path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'default.php'; 
		
		if (!file_exists($this->path))
		{
			$this->path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'category'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'default.php'; 
		}
		
		return $this->path; 
   }
   function getName()
   {
     return 'category'; 
   }
   
   function loadYag()
   {
      if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_yagendooproductmanager'.DIRECTORY_SEPARATOR.'ecm'.DIRECTORY_SEPARATOR.'yag_mapping.php'))
	  {
	   $oldoption = JRequest::getVar('option'); 
	   $oldview = JRequest::getVar('view'); 
	   JRequest::setVar('view', 'category'); 
	   JRequest::setVar('option', 'com_virtuemart'); 
	   if (method_exists($this, 'assignRef'))
	   $name = 'category'; 
	   $this->assignRef('name', $name); 
	   if (method_exists($this, 'set'))
	   $this->set('name', 'category'); 
	   
	   $saved = $this->_path['template']; 
	   
	   $this->_path['template'] = array(); 
	   $this->_path['template'][0] = ''; 
	   
	   include(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_yagendooproductmanager'.DIRECTORY_SEPARATOR.'ecm'.DIRECTORY_SEPARATOR.'yag_mapping.php'); 
	   
	   if (!empty($this->_path['template']))
		{	   
	     $this->overridepath = $this->_path['template'][0].DIRECTORY_SEPARATOR.$this->layout.'.php'; 
	    }
		else
		{
		  $this->_path['template'] = $saved; 
		}
	   //echo $this->_yagViewPath; 
	   JRequest::setVar('option', $oldoption); 
	   JRequest::setVar('view', $oldview); 
	  }
   }
   
   function loadTemplate2($theme)
   {
     
     $path = $this->getPath($this->layout, $theme);
	 echo 'path:'.$path; die(); 
	 include($this->path); 	 
	 
   }
   
   /*
	 * generate custom fields list to display as search in FE
	 */
	public function getSearchCustom() {
	}
	public function setCanonicalLink($tpl,$document,$categoryId,$manId){
	}
	public function setTitleByJMenu($app){
	}
   
}