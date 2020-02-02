<?php
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;


class plgSystemVmaddtabs extends JPlugin {


	function __construct(& $subject, $config) {
		
		 parent::__construct($subject, $config);
		

	}
	
	private function _init() {
	    static $done; 
		if (!empty($done)) return; 
		require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'googlehelper.php'); 
		$googleHelper = new GoogleHelper; 
		$this->googleHelper = $googleHelper; 
		
		
		
		$action = 'vm.category'; 
		 $assetName = 'com_virtuemart.category'; 
		 $z = JFactory::getUser()->authorise($action, $assetName);
		 
		$done = true; 
	}
	
	public function plgVmBuildTabs(&$view, &$tabs)
	{
		
		$this->_init(); 
		
		JFactory::getLanguage()->load('plg_system_vmaddtabs', dirname(__FILE__).DIRECTORY_SEPARATOR); 
		JFactory::getLanguage()->load('plg_system_vmaddtabs', JPATH_ADMINISTRATOR); 
		$class = get_class($view); 
		switch ($class)
		{
			case 'VirtuemartViewCategory': 
			  $cid = $vmid = JFactory::getApplication()->input->get('cid'); 
			  // unknown category ID: 
			  if (empty($cid)) return; 
			  if (is_array($cid)) $cid = reset($cid); 
			  $cid = (int)$cid; 

			/*
			  $doc = JFactory::getDocument(); 
			  // not in jdocumenthtml context: 
			  if (!method_exists($doc, 'addScriptDeclaration')) return;
			  
			  
			  $root = Juri::root();
			  $root = str_replace('/administrator', '/', $root); 
			
			if (substr($root, -1) !== '/') $root .= '/'; 
			$js = $root.'plugins/system/vmaddtabs/assets/cat_helper.js'; 
			$css = $root.'plugins/system/vmaddtabs/assets/cat_helper.css'; 
			JHtml::script($js); 
			JHtml::stylesheet($css); 
			$js = '
			 var current_category = '.$cid.'; '; 
			 $doc->addScriptDeclaration($js); 
			 
			 
			 
			  $view->addTemplatePath( dirname(__FILE__).DIRECTORY_SEPARATOR.'tabs'.DIRECTORY_SEPARATOR.'category'.DIRECTORY_SEPARATOR );
			 
			  
			 
			  $view->assignRef('googleHelper', $this->googleHelper); 
			  $entity = 'google_cats'; 
			  $view->assignRef('root', $root); 
			  $view->assignRef('vmid', $cid); 
			  $view->assignRef('entity', $entity); 
			  $tabs['example_tab'] = JText::_('PLG_SYSTEM_VMADDTABS_GOOGLE'); 
			  // this loads: edit_example_tab.php file in the tabs/category folder
			  */
			  if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'xmlexport.php'))
			  {
				  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'xmlexport.php'); 
				  JFactory::getLanguage()->load('com_onepage', JPATH_ADMINISTRATOR); 
				  $forms = JModelXmlexport::getCategoryForms($cid); 
				 
				 if (!empty($forms)) {
					  $tabs['xmlexport'] = JTExt::_('COM_ONEPAGE_CATEGORY_PAIRING'); 
					  $view->addTemplatePath( JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'xmlexport'.DIRECTORY_SEPARATOR.'sublayout' );
				 
				   $view->assignRef('opc_forms', $forms); 
				 }
			  }
			  
			  
			  break; 
			  
		}
		
			  
	}
	/*
	private function _addHeaders() {
		   @header('Content-Type: text/html; charset=utf-8');
	   @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	   @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	}
	public function getOptions($entity, $keyword, $current_category=0)
	{
		$data = $this->googleHelper->getData(false); 	
		$items = array(); 
		$default = new stdClass(); 
		if (!empty($current_category)) { 
		$res = GoogleHelper::getValue('xmlexport_pairing', $entity, $current_category, $default); 
		}
		
	foreach ($data as $id=>$txt)
	{
		// echo $this->googleHelper->renderOption($this->entity, $this->vmid, $id, $txt); 
		if (!empty($keyword)) { 
		if (function_exists('mb_stripos')) {
			if (mb_stripos($txt, $keyword)===false) continue; 
		}
		else
		{
			if (stripos($txt, $keyword)===false) continue; 
		}
		}
		
		$item = new stdClass(); 
		$item->id = $id; 
		$item->name = $txt; 
		$item->cid = $current_category; 
		
		if (isset($default->id)) { 
		  if ($default->id == $item->id) $item->selected = true; 
		}
		
		$items[$id] = $item; 
	}
	$this->_addHeaders(); 
	echo json_encode($items); 
	JFactory::getApplication()->close(); 
	
	}
	
	public function plgVmAddTabsStoreCategory($entity, $current_cateogry, $data_id) {
		
		$this->_init(); 
		$data = $this->googleHelper->getData(); 	
		$data_id = (int)$data_id; 
		$current_cateogry = (int)$current_cateogry; 
		foreach ($data as $id=>$txt) {
			$id = (int)$id; 
		  if ($id === $data_id) {
		    $item = new stdClass(); 
		    $item->id = $id; 
		    $item->name = $txt; 
		    $item->cid = $current_cateogry; 
		  
		  }
		}
		
		if (empty($item)) return; 

		
	  $this->_addHeaders(); 
	  $msg = var_export($data_json, true); 
	  echo json_encode($msg); 
	  JFactory::getApplication()->close(); 
	  
	}
	public function plgVmOnSelfCallBE($type, $name, &$render) {
		if ($name !== 'vmaddtabs') return; 
		$this->_init(); 
		$cmd = JRequest::getVar('cmd', 'searchcats'); 
		$keyword = JRequest::getVar('keyword', ''); 
		
		$current_category = JRequest::getInt('current_category', 0); 
		$entity = JRequest::getVar('entity', 'google'); 
		
		if ($cmd === 'searchcats') return $this->getOptions($entity, $keyword, $current_category); 
		
		$item_id = JRequest::getInt('item_data_id'); 
		
		
		if (!empty($item_id)) { 
		
		 if ($cmd === 'store') return $this->plgVmAddTabsStoreCategory($entity, $current_category, $item_id); 
		}
		
		//http://vm2.rupostel.com/purity/administrator/index.php?option=com_virtuemart&view=plugin&type=vmcustom&name=vmaddtabs&cache=no&format=raw
		$render = ''; 
	}
	*/

}

// No closing tag