<?php
/* license: commercial ! */
defined('_JEXEC') or 	die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;

class plgSystemUsertabs extends JPlugin {


	function __construct(& $subject, $config) {
		
		 parent::__construct($subject, $config);
	}
	
	private function _init() {
		 $ia = JFactory::getApplication()->isAdmin(); 
		 if (!$ia) return false; 
		 
		 $action = 'vm.product'; 
		 $assetName = 'com_virtuemart.product'; 
		 $z = JFactory::getUser()->authorise($action, $assetName);
		 return $z; 
		
	}
	
	
	private function createTable() {
		$db = JFactory::getDBO(); 
	  $q = "CREATE TABLE IF NOT EXISTS `#__usertabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `virtuemart_user_id` int(11) NOT NULL,
  `authorized_user_id` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`virtuemart_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"; 
      $db->setQuery($q); 
      $db->query(); 

	}
	
	static $_done; 
	// accepts either $product object or the ID itself
	public function plgGetUserTabs($virtuemart_user_id, &$html) {
			
		   if (is_object($virtuemart_user_id) && (isset($virtuemart_user_id->virtuemart_user_id))) $virtuemart_user_id = $virtuemart_user_id->virtuemart_user_id; 
	       $data = $this->getData($virtuemart_user_id); 
		   
		   if ((empty($data)) || (empty($data[0]['id']))) return; 
		   
		   foreach ($data as $k=>$v) {
			  if (empty($v['tabname'])) unset($data[$k]); 
			}
			
	       
	   
	       $framework = $this->params->get('framework'); 
			if (empty($framework)) {
			   $layout = 'usertabs_fe'; 
			}
			else
			{
				$layout = 'usertabs_fe_'.$framework; 
			}
			
			$root = Juri::root(); 
		    if (substr($root, -1) !== '/') $root .= '/'; 
			
			$path = self::getIncludePath($layout); 
			ob_start(); 
			if (!empty($path)) include($path); 
			$htmlZX = ob_get_clean(); 
			
			ob_start(); 
			$jsf = self::getIncludePath($layout.'.includes'); 
			if (!empty($jsf)) include($jsf); 
			$js = ob_get_clean(); 
			
			$html .= $htmlZX.$js; 
			
			if (empty(self::$_done)) self::$_done = array(); 
			self::$_done[$virtuemart_user_id] = $virtuemart_user_id; 
			
		   
	   
	}
	
	
	
	function checkCompat() {
		self::loadVM(); 
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
	
	
	
	}
	
	function onExtensionAfterSave($tes2, $test) {
	  $this->createTable(); 
	  $this->checkCompat(); 
	}
	private static function getIncludePath($layout) {
	   $paths = self::getIncludePaths($layout);
	   if (empty($paths)) return ''; 
	   return $paths[0]; 
	}
	private static function getIncludePaths($layout='') {
	   $ret = array(); 
	   $tp = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.JFactory::getApplication()->getTemplate().DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'plg_system_usertabs'.DIRECTORY_SEPARATOR; 
	  
	   if (file_exists($tp)) {
	      if (!empty($layout)) {
		     if (file_exists($tp.$layout.'.php')) $ret = array(0 => $tp.DIRECTORY_SEPARATOR.$layout.'.php'); 
		  }
		  else
		  {
			  $ret[] = $tp; 
		  }
	   }
	   if (empty($layout)) {
	     $ret[] = __DIR__.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR;
	   }
	   else
	   {
		   if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.$layout.'.php')) {
		      $ret[] = __DIR__.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.$layout.'.php'; 
		   }
	   }
	   
	   return $ret; 
	}
	
	private function loadVM() {
		/* Require the config */
	
		if (!class_exists( 'VmConfig' )) require(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		VmConfig::loadConfig();
		if(!class_exists('VmImage')) require(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
		if(!class_exists('shopFunctionsF'))require(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php'); 
		
		
	}
	
	public function plgVmBuildTabs(&$view, &$tabs)
	{
		
		if (!$this->_init()) return; 
		
		JFactory::getLanguage()->load('plg_system_usertabs', dirname(__FILE__).DIRECTORY_SEPARATOR); 
		JFactory::getLanguage()->load('plg_system_usertabs', JPATH_ADMINISTRATOR); 
		$class = get_class($view); 
		
		switch ($class)
		{
			case 'VirtuemartViewUser': 
			
			  $virtuemart_user_id = $vmid = JRequest::getVar('virtuemart_user_id'); 
			  
			   
			  // unknown category ID: 
			  if (empty($virtuemart_user_id)) return; 
			  if (is_array($virtuemart_user_id)) $virtuemart_user_id = reset($virtuemart_user_id); 
			  $virtuemart_user_id = (int)$virtuemart_user_id; 


			 
			
			
				 $data = $this->getData($virtuemart_user_id); 
				 $paths = self::getIncludePaths(); 
				 
					  $tabs['usertabs'] = JTExt::_('PLG_SYSTEM_USERTABS'); 
				foreach ($paths as $p) {
				  $view->addTemplatePath( $p );
				}
					  //$view->addTemplatePath( __DIR__.DIRECTORY_SEPARATOR.'tabs'.DIRECTORY_SEPARATOR.'product'.DIRECTORY_SEPARATOR );
					
					
					
					
					$view->assignRef('tabdata', $data); 
				    //$view->assignRef('opc_forms', $forms); 
					
				 
			  
			  
			  
			  break; 
			  
		}
		
			  
	}
	public function onGetAuthorizedUsers(&$list) {
		$user_id = JFactory::getUser()->get('id'); 
		$db = JFactory::getDBO(); 
		$q = 'select u.virtuemart_user_id as id, u.first_name, u.last_name  from #__usertabs as t, #__virtuemart_userinfos as u where t.`authorized_user_id` = u.virtuemart_user_id and  t.virtuemart_user_id = '.(int)$user_id. ' and u.address_type = "BT"'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		$list = $res; 
	}
	public function getData($virtuemart_user_id) {
	   $db = JFactory::getDBO(); 
	   $q = 'select `authorized_user_id` from `#__usertabs` where `virtuemart_user_id` = '.(int)$virtuemart_user_id; 
	   $db->setQuery($q); 
	   $res = $db->loadAssocList(); 
	
	   if (empty($res)) return array(); 
	   
	   	   
	   return $res; 
	   
	}
	
	
	
	public function onAfterRoute() {
		
		if (!$this->_init()) {
		    
		   return; 
		}
		
		$x = JRequest::getVar('sys_store_tab_content_user', false); 
		if (!empty($x)) {
			 $virtuemart_user_id = JRequest::getInt('virtuemart_user_id'); 
			
			
			 if (empty($virtuemart_user_id)) return; 
			  if (is_array($virtuemart_user_id)) $virtuemart_user_id = reset($virtuemart_user_id); 
			  $virtuemart_user_id = (int)$virtuemart_user_id; 
			
			$post = JRequest::get('post'); 
			$users = JRequest::getVar('usertabs_users', array()); 
			$this->removeAll($virtuemart_user_id); 
			
			foreach ($users as $id) {
				$id = (int)$id; 
				if (!empty($id)) {
					$this->insertUpdate($virtuemart_user_id, $id); 
				}
			}
			
			
			
			
			
		}
	}
	private function removeAll($virtuemart_user_id) {
	  
	  
	  if ((!empty($virtuemart_user_id)) ) {
	  $db = JFactory::getDBO(); 
	  $q = 'delete from `#__usertabs` where `virtuemart_user_id` = '.(int)$virtuemart_user_id; 
	  $db->setQuery($q); 
	  $db->query(); 
	  }
	}
	private function insertUpdate($virtuemart_user_id, $authorized_user_id) {
	   $db = JFactory::getDBO(); 
	   {
	     $q = "insert into `#__usertabs` (`id`, `virtuemart_user_id`, `authorized_user_id`) values (NULL, ".(int)$virtuemart_user_id.", '".(int)$authorized_user_id."')"; 
		 $db->setQuery($q); 
		 $db->query(); 
		 
		
	   }
	   
	}
	
	/*helper functions*/
	private static function toObject(&$product, $recursion=0) {
    
	
	if (is_object($product)) {
	 $copy = new stdClass(); 
	 $attribs = get_object_vars($product); 
	 $isO = true; 
	}
	elseif (is_array($product)) {
		  $copy = array(); 
		  $isO = false; 
		  $attribs = array_keys($product); 
		  $copy2 = array(); 
		  foreach ($attribs as $zza=>$kka) {
		       if (strpos($kka, "\0")===0) continue;
			   $copy2[$kka] = $product[$kka]; 
		  }
		  $attribs = $copy2; 
		}
		
	
    foreach ($attribs as $k=> $v) {
		if (strpos($k, "\0")===0) continue;
		if ($isO) {
	      $copy->{$k} = $v; 	
		}
		else
		{
			$copy[$k] = $v; 
		}
		
		//if ($recursion < 5)
		if ((is_object($v)) && (!($v instanceof stdClass))) {
		   $recursion++; 
		   if ($isO) {
		     OPCmini::toObject($copy->{$k}, $recursion); 
		   }
		   else
		   {
			   OPCmini::toObject($copy[$k], $recursion); 
		   }
		}
		else
		{
			if (is_array($v)) {
			   $recursion++; 
			   if ($isO) {
		        OPCmini::toObject($copy->{$k}, $recursion); 
			   }
			   else
			   {
				   OPCmini::toObject($copy[$k], $recursion); 
			   }
			}
		}
		/*
		if (is_array($v)) {
		
		  $keys = array_keys($v); 
	  
		  foreach ($keys as $kk2=>$z2) {
		     if (strpos($z2, "\0")===0) continue;
			 $copy->{$k}[$z2] = $v[$z2]; 
			 if ((is_object($v[$z2])) && (!($v[$z2] instanceof stdClass))) {
				$recursion++; 
			    OPCmini::toObject($copy->{$k}[$z2]); 
			 }
			 else
			 if (is_array($v[$z2])) {
			    $recursion++; 
			    OPCmini::toObject($copy->{$k}[$z2]); 
			 }
			 
		  }
		}
		*/
		
		
	}
	$recursion--;
	$product = $copy; 
 }

}


// No closing tag