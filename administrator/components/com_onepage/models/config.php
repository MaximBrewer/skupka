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
	
	 
    
  // Load the virtuemart main parse code

	//require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
//	require_once( JPATH_ROOT . '/includes/domit/xml_domit_lite_include.php' );
//	require_once( JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'ajax'.DIRECTORY_SEPARATOR.'ajaxhelper.php' );	
	
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
	
	class JModelConfig extends OPCModel
	{	
		function __construct()
		{
			parent::__construct();
		
		}
		
		function getAcyFields() {
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php');  
			return OPCUserFields::getAcyFields(); 
			
		}
		
		function getThemePositions()
		{
			$tpla = Array(
			
			"checkoutAdvertises" => '', 
			"intro_article" => '', 
			"captcha" 	=> '', 
			"delivery_date" => '', 
			'op_userfields_cart'=>'',
            "op_basket" => '',
            "op_coupon" => '', 
            "html_in_between" => '', 
            "shipping_method_html" => '',
            "op_userfields" => '',
            "op_shipto" => '',
            "op_tos" => '',
             "op_payment" => '',
             "tos_con" => '', 
             "google_checkout_button" => '',
             "paypal_express_button" => '',
			 "checkbox_products" => '',
			 "shipping_estimator" => '',
              ) ;
		
             return $tpla; 		
		}
		
		function checkOtherPlugins()
		{
			
		}
		
		function checkNumberingPlugin($enabled=false)
		{
			if (defined('OPC_NUMBERING_CHECK')) return ''; 
			define('OPC_NUMBERING_CHECK', 1); 
			$msg = ''; 
			/*
					  if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcnumbering'))
					  {
						  			JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcnumbering'); 
					  }
					  
		
					  
		if (JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opcnumbering'.DIRECTORY_SEPARATOR.'opcnumbering.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcnumbering'.DIRECTORY_SEPARATOR.'opcnumbering.php')===false)
		  {
			  $msg .= 'Cannot copy numbering plugin to plugins/system directory. <br />'; 
			  $msgs = true; 
		  }
		  
		  if (JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opcnumbering'.DIRECTORY_SEPARATOR.'opcnumbering.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcnumbering'.DIRECTORY_SEPARATOR.'opcnumbering.xml')===false)
		  {
			  if (empty($msgs))
			  $msg .= 'Cannot copy numbering plugin to plugins/system directory. <br />'; 
		  }
		  JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opcnumbering'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcnumbering'.DIRECTORY_SEPARATOR.'index.html');
		  */
		  $this->copyPlugin('system', 'opcnumbering'); 
		  
		  
		  		require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'numbering.php');
				  $JModelNumbering = new JModelNumbering; 
				  $agendas = $JModelNumbering->getAgendas(); 

		  
		  
		  
			$db = JFactory::getDBO(); 
		   $q = "select `element` from `#__extensions` where `element` = 'opcnumbering' and `type` = 'plugin' and `folder` = 'system' limit 0,1 "; 
		   $db->setQuery($q); 
		   $res = $db->loadResult(); 
		   if (empty($res))
		   {
			   $q = 'INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES '; 
			   $q .= ' (NULL, \'plg_system_opcnumbering\', \'plugin\', \'opcnumbering\', \'system\', 0, 1, 1, 0, \'{"name":"plg_system_opcnumbering","type":"plugin","creationDate":"March 2015","author":"RuposTel s.r.o.","copyright":"RuposTel s.r.o.","authorEmail":"admin@rupostel.com","authorUrl":"www.rupostel.com","version":"1.0.1","description":"OPC - One Page Checkout Order and Invoice Numbering Plugin","group":"","filename":"opcnumbering"}\', \'{}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', 0, 0)'; 
			   $db->setQuery($q); 
			   $db->query(); 
		   }
		   else
		   {
			   if ($enabled)
			   {
				   
				  	   
				  $q = 'update `#__extensions`  set `enabled` = 1, `state` = 0 where element = "opcnumbering" '; 
				   
				   $db->setQuery($q); 
				   $z = $db->query(); 

			   }
		   }
		   
		   
			return $msg; 
					  
		}
		
		function installext($id, $name='', &$msg='')
		{
		   
		   $id = (int)$id; 
		   $msg = ''; 
		   $exts = $this->getOPCExtensions(); 
		   
		   if (($id < 0) && (!empty($name))) {
			   foreach ($exts as $idx=>$e)
			   {
				   if (isset($e['data']))
					   if (isset($e['data']['element']))
				   if ($e['data']['element'] === $name) {
				     $id = $idx; 
					 break; 
				   }
				   if (isset($e['name']))
				   if ($e['name'] === $name) {
				     $id = $idx; 
					 break; 
				   }
				   
			   }
		     
		   }
		   
		   if ($id < 0)  return; 
		   
		   
		   
		   if (isset($exts[$id]))
		    {
			   $e = $exts[$id]; 
			   $p_dir = $e['dir']; 
			   // per example from: \administrator\components\com_installer\models\install.php
			   $this->setState('action', 'install');
			   
			   JFactory::getLanguage()->load('com_installer', JPATH_ADMINISTRATOR); 
			   
			   JClientHelper::setCredentialsFromRequest('ftp');
			   $app = JFactory::getApplication();
			   
			   $type = JInstallerHelper::detectType($p_dir);
			   $package = array(); 
			   $package['packagefile'] = null;
			   $package['extractdir'] = null;
		       $package['dir'] = $p_dir;
		       $package['type'] = $type;
			   
			   
			   $installer = JInstaller::getInstance();
			   
			   
			   // Install the package
			  if (!$installer->install($package['dir'])) {
					// There was an error installing the package
					$msg = JText::sprintf('COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));
					$result = false;
			 } else {
				// Package installed sucessfully
				$msg = JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));
				$result = true;
			}
		
		
			 
			}
			return $msg; 
		}
		
		function getOPCRegistration()
		{
		   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');

		   
		   $ret = new stdClass(); 
		   $ret->opc_registration_company = ''; 
		   $ret->opc_registration_name = ''; 
		   $ret->opc_registration_username = '';
		   $ret->opc_registration_email = '';  
		   $ret->opc_registration_hash = '0_0'; 
		   $reg = OPCconfig::getValue('opc_registration', '', 0, $ret, false); 
		   
		   if (is_array($reg))
		   {
		     foreach ($reg as $k=>$v) $ret->$k = $v; 
		   }
		   
		   if (!is_object($reg)) return $ret; 
		   
		   
		   if (empty($reg)) return $ret; 
		   
		   return $reg; 
		   
		   
		   
		}
		
		function storeRegistration()
		{
		    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');

		   
		   $ret = new stdClass(); 
		   $d = JRequest::getVar('opc_registration_company', '');
		   $ret->opc_registration_company = str_replace('"', '\"', $d); 
		   $d = JRequest::getVar('opc_registration_name', '');
		   $ret->opc_registration_name = str_replace('"', '\"', $d); 
		   $d = JRequest::getVar('opc_registration_username', '');
		   $ret->opc_registration_username = str_replace('"', '\"',$d); 
		   $d = JRequest::getVar('rupostel_email', ''); 
		   $ret->opc_registration_email = str_replace('"', '\"', $d); 
		   
		   $ret->opc_registration_hash = JRequest::getVar('opc_registration_hash', ''); 
		
		   $reg = OPCconfig::store('opc_registration', '', 0, $ret); 
		  
		   if (!empty($ret->opc_registration_hash))
		   {
		   
		      include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'api.php'); 
			  
			  
			  if ($api_key !== $ret->opc_registration_hash)
			   {
			   
			   
			      $a = explode('_', $ret->opc_registration_hash); 
				  
				  if (count($a)==2)
				  {
				 
				  if (is_numeric($a[0]) && (ctype_xdigit($a[1])))
				  {
				  
				  $api_key = $a[0].'_'.$a[1]; 
			      $towrite = '<?php defined( \'_JEXEC\' ) or die( \'Restricted access\' ); '."\n";
				  $towrite .= ' $api_key = \''.$api_key.'\'; '."\n";
			      $towrite .= ' $api_stamp = \''.time().'\'; '."\n";
				  
				  jimport('joomla.filesystem.folder'); 
				  jimport('joomla.filesystem.file'); 
				  JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'api.php', $towrite); 
		
				  
				  }
				  }
				  
				  
				  
			   }
			   else
			   {
			    
			   }
			  
		   }
		     
			$delivery_data = JRequest::getVar('delivery_data', array()); 
			$store = new stdClass(); 
			
			if (!empty($delivery_data['enabled']))
			$store->enabled = true; 
			else 
			$store->enabled = false; 
			
			if (!empty($delivery_data['required']))
			$store->required = true; 
			else 
			$store->required = false; 
			
			
			$offset = (int)$delivery_data['offset']; 
			$store->offset = $offset; 
			
			$offsetmax = (int)$delivery_data['offsetmax']; 
			$store->offsetmax = $offsetmax; 
			
			$firstday = (int)$delivery_data['firstday']; 
			$store->firstday = $firstday; 
			
			$days = array(
	1 => JText::_('MONDAY'), 
	2 => JText::_('TUESDAY'), 
	3 => JText::_('WEDNESDAY'), 
	4 => JText::_('THURSDAY'), 
	5 => JText::_('FRIDAY'), 
	6 => JText::_('SATURDAY'), 
	0 => JText::_('SUNDAY'), 
	);
		  foreach ($days as $i=>$day)
		  {
		     $key = 'day_'.$i;
			 if (!empty($delivery_data['days'][$i]))
			  {
			     $store->$key = true; 
			  }
			  else
			     $store->$key = false; 
		  }
		  
		  $format = $delivery_data['format']; 
		  $store->format = $format; 
		  
		  $format = $delivery_data['storeformat']; 
		  $store->storeformat = $format; 
			
			$store->hollidays = $delivery_data['hollidays']; 
			
		    $reg = OPCconfig::store('opc_delivery_date', '', 0, $store); 
		   $acyf = JRequest::getVar('acymailing_fields', array()); 
		  
		   if (!empty($acyf)) {
			   if (is_array($acyf)) {
				   foreach ($acyf as $k=>$v) {
					   if (empty($v)) {
						   OPCconfig::clearConfig('acymailing_fields', $k, 0); 
					   }
					   else {
						   OPCconfig::store('acymailing_fields', $k, 0, $v); 
					   }
				   }
			   }
		   }
		   
		}
		
		function getCurrencies()
		{
		$db = JFactory::getDBO(); 
		$q = 'select vendor_currency, vendor_accepted_currencies from #__virtuemart_vendors where 1'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		echo $db->getErrorMsg(); 
		$arr = array(); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		$cm = OPCmini::getModel('currency'); 
		if (!empty($res))
		 {
		 foreach ($res as $row)
		 {
		    $a = explode(',', $row['vendor_accepted_currencies']); 
			
			$vc = $row['vendor_currency'];
			
			if (!isset($arr[$vc]))
			$arr[$vc] = $cm->getCurrency($vc); 
			
			foreach ($a as $c)
			 {
			   $arr[$c] = $cm->getCurrency($c); 
			 }
	     }
		 }
		 
		
		 
		 
		 
		 return $arr; 
		}
		
		function getDisabledOPC()
		{
		
		  	if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
			include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');
			
			if ($disable_onepage) return true; 
			else
			{
			if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "select * from `#__extensions` where `element` = 'opc' and `type` = 'plugin' and `folder` = 'system' limit 0,1 "; 
	  }
	  else
	  {
	    $q = "select * from #__plugins where element = 'opc' and folder = 'system'  limit 1 "; 
	  }
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$r = $db->loadAssoc(); 
		if (!empty($r['enabled'])) return false; 
		if (!empty($r['published'])) return false; 
		
					
			}
			return true; 
			
		}
// admin
		function listExtsaAdmin(&$exts, &$langsr)
		{
		  jimport( 'joomla.filesystem.folder' );
		  jimport( 'joomla.filesystem.file' );
		  
		  $xts = array(); 
		  $langs = array(); 
		  
		  $files = JFolder::files(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language', 'ini', 1, true, array('.svn', 'CVS')); 
		 
		  foreach ($files as $f)
		   {
		     $f = str_replace('/', DS, $f); 
			 $f = str_replace('\\', DS, $f); 
			 $adminpath = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR; 
			 $adminpath = str_replace('/', DS, $adminpath); 
			 $adminpath = str_replace('\\', DS, $adminpath); 

		     $f = str_replace($adminpath, '', $f); 
			 $fa = explode(DS, $f); 
			 
			  $lang = $fa[0];
			 
			 // some error: 
			if (strlen($lang)>7) continue; 
			
			$langs[$lang] = $lang; 
			
			$xt = $fa[1]; 
			 
			 $xt = str_replace($lang.'.', '', $xt); 
			 if (stripos($xt, 'bck')===false)
			 if ($xt !== 'ini')
			 {
			  if (!isset($xts[$xt]))
			   {
			     $xts[$xt] = array(); 
			     $xts[$xt]['name'] = $xt;  
				 $xts[$xt]['lang'] = array();  
				 $xts[$xt]['lang'][$lang] = $lang; 
			   }
			   else
			    $xts[$xt]['lang'][$lang] = $lang; 
			 }
			 
		   }
		   
		   $exts = $xts; 
		   $langsr = $langs;
		   
		  return true; 
		}

		// site
		function listExts(&$exts, &$langsr)
		{
		  jimport( 'joomla.filesystem.folder' );
		  jimport( 'joomla.filesystem.file' );
		  
		  $xts = array(); 
		  $langs = array(); 
		  
		  $files = JFolder::files(JPATH_SITE.DIRECTORY_SEPARATOR.'language', 'ini', 1, true, array('.svn', 'CVS')); 
		  foreach ($files as $f)
		   {
			 $jpath_site = JPATH_SITE.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR;
			 $jpath_site = str_replace('/', DS, $jpath_site); 
		     $f = str_replace('/', DS, $f); 
		     $f = str_replace($jpath_site, '', $f); 
			 $fa = explode(DS, $f); 
			 
			 if (count($fa) <= 1) continue; 
			 
			 $lang = $fa[0];
			
			$langs[$lang] = $lang; 
			
			$xt = $fa[1]; 
			 
			 $xt = str_replace($lang.'.', '', $xt); 
			 if (stripos($xt, 'bck')===false)
			 if ($xt !== 'ini')
			 {
			  if (!isset($xts[$xt]))
			   {
			     $xts[$xt] = array(); 
			     $xts[$xt]['name'] = $xt;  
				 $xts[$xt]['lang'] = array();  
				 $xts[$xt]['lang'][$lang] = $lang; 
			   }
			   else
			    $xts[$xt]['lang'][$lang] = $lang; 
			 }
			 
		   }
		   
		   $exts = $xts; 
		   $langsr = $langs;
		   
		  return true; 
		}
		
		
		function getExtLangVars()
		{
		   
   $jlang = JFactory::getLanguage(); 
   	 if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {

   $jlang->load('com_content', JPATH_ADMINISTRATOR, 'en-GB', true); 
   $jlang->load('com_content', JPATH_ADMINISTRATOR, $jlang->getDefault(), true); 
   $jlang->load('com_content', JPATH_ADMINISTRATOR, null, true); 
   
   
  
 }
		}
		
		function getArticleSelector($name, $value, $required=false)
		{
		
		$id = $name; 
	
		if (empty($value) || (!is_numeric($value))) $value = null; 
		
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.opcmodal');

		// Build the script.
		$script = array();
		$html	= array();
		//if (stripos($id, '{')===false)
		{
		
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
			
		
		$html[] = '<script type="text/javascript">'; 
		$html[] = '//<![CDATA['; 
		
		$html[] = '	function jSelectArticle_'.$id.'(id, title, catid, object) {';
		$html[] = '		document.id("'.$id.'_id").value = id;';
		$html[] = '		document.id("'.$id.'_name").value = title;';
		$html[] = '		SqueezeBox.close();';
		$html[] = '	}';
		$html[] = '//]]>'; 
		$html[] = '</script>'; 

		}
		else
		{
		$html[] = '<script type="text/javascript">'; 
		$html[] = '//<![CDATA['; 
		$html[] = "
		function jSelectArticle(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById('sbox-window').close();
		}";
		$html[] = '//]]>'; 
		$html[] = '</script>'; 
		
		}
		// Add the script to the document head.
		//JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		}

		// Setup variables for display.
		
		 if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {
		$link	= 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_'.$id;
		}
		else
		$link = 'index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;object='.$id;
		$db	= JFactory::getDBO();
		$db->setQuery(
			'SELECT `title` ' .
			' FROM #__content' .
			' WHERE id = '.(int) $value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}
		
		if (empty($title)) {
		   if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
			$title = JText::_('COM_CONTENT_SELECT_AN_ARTICLE');
			else
			$title = JText::_('Select an Article');
		}
		
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
		
		// The current user display field.
		$html[] = '<div class="fltlft">';
		$html[] = '  <input type="text" id="'.$id.'_name" value="'.$title.'" disabled="disabled" size="35" />';
		$html[] = '</div>';

		// The user select button.
		
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html2 = ''; 
		$html2 .= '	<a class="opcmodal" id="modal_link_'.$id.'" title="';
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$html2 .= JText::_('COM_CONTENT_CHANGE_ARTICLE'); 
		else
		$html2 .= JText::_('Select an Article');
		$html2 .= '"  href="'.$link.'" rel="'.htmlentities('{handler: \'iframe\', size: {x: 800, y: 450}}').'">';
		if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$html2 .= JText::_('COM_CONTENT_CHANGE_ARTICLE_BUTTON');
		else
		$html2 .= JText::_('Select');
		$html2 .= '</a>';
		$html[] = $html2; 
		$html[] = '  </div>';
		$html[] = '</div>';

		// The active article id field.
		if (0 == (int)$value) {
			$value = '';
		} else {
			$value = (int)$value;
		}

		// class='required' for client side validation
		$class = '';
		if ($required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$id.'_id"'.$class.' name="'.$name.'" value="'.$value.'" />';

		$zhtml = implode("\n", $html);; 

		return $zhtml; 
	
		}
		
		public static function isLess($x) {
	  if (!class_exists('VmVersion')) {
		  require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'version.php'); 
		}	
	  
	  if (!isset(VmVersion::$REVISION)) return false; 
	  $rev = (int)VmVersion::$REVISION; 
	  
	  
	  // only VM3 is supported here: 
	  if ($rev < 8578) return false; 
	  //3.0.16....9204
	  //3.0.14....9194
	  //3.0.13.2..9162
	  //3.0.12....9058
	  //3.0.9.6...8956
	  //3.0.6.2...8771
	  //3.0.4.....8672
	  //3.0.2.....8615
	  //3.0.8.....8836
	  //3.0.0.....8578
	  $x = (int)$x; 
	  switch ($x) {
	    case 3016: 
		  if ($rev > 9204) return false; 
		case 3014: 
		  if ($rev <= 9203) return true; 
		case 3012:
		  if ($rev <= 9162) return true; 
		case 306: 
		  if ($rev <= 8771) return true; 
		case 304: 
		  if ($rev <= 8672) return true; 
		case 302: 
		  if ($rev <= 8615) return true; 
		case 308: 
		  if ($rev <= 8836) return true; 
		case 3090: 
		  if ($rev <= 8847) return true; 
		case 30910: 
		 if ($rev <= 8986) return true; 
		case 3098: 
		 if ($rev <= 8971) return true; 
		case 3096: 
		 if ($rev <= 8956) return true; 
		case 3094: 
		 if ($rev <= 8872) return true; 
		case 309:
		 if ($rev <= 8847) return true; 
		case 300: 
		  if ($rev <= 8578) return true; 
		
		
		
	  }
	  //custom build: 
		return false; 
	  
	}
	
		
		function getUserFieldsLists(&$corefields)
		{
		  
		  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'version.php'); 
		  $ver = vmVersion::$RELEASE; 
		  $ok = false; 
		  if (version_compare($ver,'2.0.6','ge')) $ok = true; 
		   
		  if ($ver == '${PHING.VM.RELEASE}') $ok = true; 
		  if (!$ok)
		  return false; 

       $jlang = JFactory::getLanguage(); 

		$jlang->load('com_virtuemart', JPATH_SITE, 'en-GB', true); 
		$jlang->load('com_virtuemart', JPATH_SITE, $jlang->getDefault(), true); 
		$jlang->load('com_virtuemart', JPATH_SITE, null, true); 

		  
		  if (!class_exists('VirtueMartModelUserfields'))
		  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'userfields.php'); 
		  $modelu = new VirtueMartModelUserfields();
		  
		  $corefields = $modelu->getCoreFields();
		  $corefields[] = 'register_account';
		  $modelu->setState('limitstart', 0);
		  $modelu->setState('limit', 99999);
		  $modelu->_noLimit = true; 
		  $modelu->_selectedOrderingDir = 'desc'; 
		  $uf = $modelu->getUserfieldsList();
		  
		  if (empty($uf)) return array(); 
		  
		  
		  $last = array (
  'virtuemart_userfield_id' => '0',
  'virtuemart_vendor_id' => '1',
  'userfield_jplugin_id' => '0',
  'name' => 'register_account',
  'title' => 'COM_VIRTUEMART_ORDER_REGISTER',
  'description' => '',
  'type' => 'checkbox',
  'maxlength' => '1000',
  'size' => NULL,
  'required' => '0',
  'cols' => '0',
  'rows' => '0',
  'value' => '',
  'default' => NULL,
  'registration' => '1',
  'shipment' => '0',
  'account' => '1',
  'readonly' => '0',
  'calculated' => '0',
  'sys' => '0',
  'params' => '',
  'ordering' => '101',
  'shared' => '0',
  'published' => '1',
  'created_on' => '2014-04-01 16:43:17',
  'created_by' => '42',
  'modified_on' => '2014-04-01 16:43:17',
  'modified_by' => '42',
  'locked_on' => '0000-00-00 00:00:00',
  'locked_by' => '0',
);
		  $last = (object)$last; 
		  $last->name = 'register_account'; 
		  $last->title = 'COM_VIRTUEMART_ORDER_REGISTER'; 
		  $last->type = 'checkbox'; 
		  
		  $uf[] = $last; 
		  
		  $uf = array_reverse($uf); 
		  
		  return $uf; 
		}
		private function fileContains($file, $string) { 
		if (!file_exists($file)) return false; 
		$handle = fopen($file, 'r');
		$valid = false; // init as false
		$time = time(); 
		while (($buffer = fgets($handle)) !== false) {
			$time2 = time(); 
			
			if (($time - $time2) > 50) break; 
			
			if (strpos($buffer, $string) !== false) {
			$valid = TRUE;
			break; // Once you find the string, you should break out the loop.
			}      
			}
		 fclose($handle);
		 return $valid; 
		}
		
		function patchcalculationh()
	{
		$msg = ''; 
		$path = JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php'; 
		$savepath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'calculationh_patched.php'; 
		
		
		$c = VmConfig::get('coupons_enable', true); 
	VmConfig::set('coupons_enable', 10); 
	$test = VmConfig::get('coupons_enable'); 
	VmConfig::set('coupons_enable', $c); 
	if ($test != 10)
	 {
	   $isadmin =false; 
	 }
	 else $isadmin = true; 
	 
	 
	
	 
	 if (!$isadmin)
	 {
	   $msg .= JText::_('COM_ONAPEGE_USER_IS_NOT_VIRTUEMART_ADMIN').'<br />'; 
	 }
		
		// feature removed: 
		return;
		
		if (!file_exists($path)) return; 
		$data = file_get_contents($path); 
		if (file_exists($savepath))
		$datas = file_get_contents($savepath); 
	    else $datas = ''; 
		
		$data = str_replace("\r\r\n", "\r\n", $data); 
		if(!class_exists('JFile')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'file.php');
	    if(!class_exists('JFolder')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');
		{
			$count = 0; 
			$data = str_replace('private', 'protected', $data, $count); 
			$data = str_replace('VmError(\'Unrecognised', ' // VmError(\'Unrecognised', $data); 
			$data = str_replace('VmWarn(\'Unrecognised', ' // VmError(\'Unrecognised', $data); 
			$content = urldecode('%3C%3Fphp').'
/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( \'_VALID_MOS\' ) && !defined( \'_JEXEC\' ) ) die( \'Direct Access to \'.basename(__FILE__).\' is not allowed.\' ); 
if (!class_exists(\'calculationHelper\'))
require(JPATH_SITE.DIRECTORY_SEPARATOR.\'administrator\'.DIRECTORY_SEPARATOR.\'components\'.DIRECTORY_SEPARATOR.\'com_virtuemart\'.DIRECTORY_SEPARATOR.\'helpers\'.DIRECTORY_SEPARATOR.\'calculationh.php\'); 
'; 


			if (empty($count) || ($count == 1))
			{
				 if (strcmp($datas, $content)!==0)
				 {
				 if (file_exists($savepath))
				 if (JFile::delete($savepath)==false)
				 {
					 //$msg = 'Patch for calculationh.php is not needed';
					 $msg .= 'Couln\'t remove OPC override '.$savepath; 
				 }
			
			
			
			
			if (JFile::write($savepath, $content)==false)
			{
				$msg .= '<br />Could not write to '.$savepath; 
			}
				 
			}
			}
			else
			{
			  if (strcmp($datas, $data)!==0)
			  {
			  if (JFile::write($savepath, $data)===false)
			  {
				  $msg = 'Cannot write to '.$savepath; 
			  }
			  else
			  {
				  //test: 
				  $data = file_get_contents($savepath); 
				  // we use url encode because php compiler sometimes doe not recognize tags inside a string
				  // this code tests config file after saving it
				  $x1 = (@eval('return true; '.urldecode('%3F%3E').' '.$data.' '.urldecode('%3C%3Fphp').' '));
				  $x2 = (@eval('return true; '.urldecode('%3F%3E').' '.$data.'  '));
				  if (!((($x1 !== true) && ($x2 === true)) || (($x1 === true) && ($x2 !== true))))
				  {
					$msg = 'The patch could not be applied'; 
				 
				 if (JFile::delete($savepath)==false)
				 {
						$msg .= ' and couln\'t remove OPC override '.$savepath; 
				 }   
				  }
			  }
			 }
			}
		}
		/*
		$link = 'index.php?option=com_onepage';
   
		if (empty($msg)) $msg = 'Patch applied sucessfully';
		else $msg = 'Patch not applied ! '.$msg; 
		$this->setRedirect($link, $msg);
		*/
			return $msg; 
	}
		function checkLangFiles()
		{
		
		$msg = ''; 
		
		  $orig = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'en-GB'.DIRECTORY_SEPARATOR.'en-GB.com_onepage.ini'; 
		  $orig_sys = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'en-GB'.DIRECTORY_SEPARATOR.'en-GB.com_onepage.ini'; 

		  if(!class_exists('JFile')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'file.php');
		  if(!class_exists('JFolder')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');
		 
		  //since VM3.0.6 and some previous VM versions, the OPC disable autoselect payment plugin is now availalbe: 
		  
		 
		  
		  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		  OPCUserFields::$cacheDisabled = true; 
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
		if (!OPCUserFields::fieldExists('customer_note')) {
		   OPCUserFields::createField('customer_note', array('published'=>1, 'type'=>'textarea', 'title'=>'COM_VIRTUEMART_CNOTES_CART', 'cart'=>1));
			$msg .= 'OPC detected that customer_note field was missing and created a new field.'."<br />"; 
		}
		}
		
		  
		if(version_compare(JVERSION,'3.5.1','ge')) {		  
		  if (self::isLess('3014')) {
			 if (!$this->fileContains(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php', "'3.5'")) {
			  
		     $msg .= '<div class="msgwrap">'.JText::_('COM_ONEPAGE_INVALID_ADDRESS_ERROR_FIX'); 
			 $btn = '<button type="button" class="btn btn-small btn-success" onclick="return fixShopfunctions(this);" />'.JText::_('COM_ONEPAGE_FIELD_MISCONFIGURATION_FIX').'</button>';
			 //$btn .= '<button type="button" class="btn btn-small btn-danger" onclick="return ignoreMsg(this);" />'.JText::_('COM_ONEPAGE_FIELD_MISCONFIGURATION_FIX').'</button>';
			 $msg .= $btn.'</div>'; 
			 }
		  
		  }
		}
		
		
		try {
	$db = JFactory::getDBO(); 	
	$q = 'SELECT @@character_set_database'; 
	$db->setQuery($q); 
	$res1 = $db->loadResult();  //utf8mb4
	
	$q = 'SELECT @@collation_database'; 
	$db->setQuery($q); 
	$res2 = $db->loadResult(); //utf8mb4_general_ci
	
	$config = JFactory::getConfig();
	$dbname = $config->get('db');
	
	
	if (($res1 === 'utf8mb4') && ($res2 === 'utf8mb4_general_ci')) {
	  //ALTER DATABASE <database_name> CHARACTER SET utf8 COLLATE utf8_unicode_ci;	
	  
	   $msg .= JText::_('COM_ONEPAGE_INVALID_DBCHARSET'); 
	   $btn = '<button type="button" class="btn btn-small btn-success" onclick="return fix_db_charset(this);" />'.JText::_('COM_ONEPAGE_FIELD_MISCONFIGURATION_FIX').'</button>';
	   $msg .= $btn; 
	  
	}
	
	
	}
	catch (Exception $e) {
		//do nothing here... 
	}
	
		  
		  
		  
		  
		  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		  
		   $db = JFactory::getDBO(); 
		   if ((defined('VM_VERSION')) && (VM_VERSION >= 3))
		  {
			$q = 'select * from #__virtuemart_userfields where cart = 1 and published = 1 ';   
			$db->setQuery($q); 
			$list = $db->loadAssocList(); 
			$ignore = array('tos', 'customer_note'); 
			$fields = array(); 
			
			$msg_code = '1035|'; 
			foreach ($list as $k=>$v)
			{
				if (in_array($v['name'], $ignore))
				{
					unset($list[$k]); 
					continue; 
				}
				$vn = $v['name']; 
				$msg_code .= '|'.$vn; 
				$fields[] = '<b>'.JText::_($v['title']).' (<span class="key_hash">'.$vn.'</span>)</b>'; 
			}
			
			$hash = md5($msg_code); 
			$default = false; 
			$ret = OPCconfig::getValue('opc_ignored_messages', $hash, 0, $default); 
			//var_dump($hash); var_dump($ret); die(); 
			if ($ret !== true)
			if (!empty($fields))
			{
			$btn = '<button type="button" class="btn btn-small btn-success" onclick="return fixCartFields(this);" />'.JText::_('COM_ONEPAGE_FIELD_MISCONFIGURATION_FIX').'</button>';
			$btn .= '&nbsp;<button type="button" ignhash="'.htmlentities(md5($msg_code)).'" class="btn btn-small btn-danger" onclick="return ignoreMsg(this);" />'.JText::_('COM_ONEPAGE_FIELD_MISCONFIGURATION_IGNORE').'</button>';
			$msg .= '<div class="msgwrap">'.JText::_('COM_ONEPAGE_FIELD_MISCONFIGURATION').implode(', ', $fields).'&nbsp;'.$btn."<br /></div>"; 
			}
			
		  }
				if(!class_exists('SoapClient')) {
				   $msg .= 'Error ! SoapClient class is not available. VAT validation and many other services may not work for you. Contact your hosting to enable php-soap library'."<br />\n"; 
				}
		  
		  $q = 'update `#__extensions`  set `state` = 0 where `state` = -1 and `manifest_cache` like "%RuposTel%"'; 
		  try {
		   $db->setQuery($q); 
		   $db->query(); 
		  }
		  catch (Exception $e) {
			  //silent ignore
		  }
		  
		 // $z = OPCmini::hasIndex('virtuemart_paymentmethods', array('virtuemart_vendor_id','published')); 
		 		  $z = OPCmini::hasIndex('virtuemart_media', array('file_title','file_type')); 
		  if (empty($z)) {
		     OPCmini::addIndex('virtuemart_media', array('file_title','file_type')); 
		  }
		 
		  $cols = OPCmini::getColumns('awocoupon_history'); 
		  if (isset($cols['session_id'])) {
		  
		  $z = OPCmini::hasIndex('awocoupon_history', array('session_id','order_id')); 
		  if (empty($z)) {
		     OPCmini::addIndex('awocoupon_history', array('session_id','order_id')); 
		  }
		  }
		  
		   $z = OPCmini::hasIndex('awocoupon_history', array('coupon_entered_id','session_id','timestamp')); 
		  if (empty($z)) {
		     OPCmini::addIndex('awocoupon_history', array('coupon_entered_id','session_id','timestamp')); 
		  }
		 
		 
		   $z = OPCmini::hasIndex('awocoupon_history', array('order_id','coupon_discount','shipping_discount')); 
		  if (empty($z)) {
		     OPCmini::addIndex('awocoupon_history', array('order_id','coupon_discount','shipping_discount')); 
		  }
		  
		   $z = OPCmini::hasIndex('awocoupon', array('coupon_code')); 
		  if (empty($z)) {
		     OPCmini::addIndex('awocoupon', array('coupon_code')); 
		  }
		 
		  $z = OPCmini::hasIndex('virtuemart_shoppergroups', array('virtuemart_vendor_id','shared', 'published')); 
		  if (empty($z)) {
		     OPCmini::addIndex('virtuemart_shoppergroups', array('virtuemart_vendor_id','shared', 'published')); 
		  }
		  
		  $z = OPCmini::hasIndex('virtuemart_vmusers', array('virtuemart_user_id','virtuemart_vendor_id')); 
		  if (empty($z)) {
		    $z = OPCmini::addIndex('virtuemart_vmusers', array('virtuemart_user_id','virtuemart_vendor_id')); 
		  }
		  
		  $z = OPCmini::hasIndex('virtuemart_product_medias', array('virtuemart_product_id','ordering')); 
		  if (empty($z)) {
		    OPCmini::addIndex('virtuemart_product_medias', array('virtuemart_product_id','ordering')); 
		  }
		  if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
		  $z = OPCmini::hasIndex('virtuemart_product_prices', array('virtuemart_product_id','virtuemart_shoppergroup_id', 'product_price_publish_up', 'product_price_publish_down', 'product_price')); 
		  if (empty($z)) {
		  $z = OPCmini::addIndex('virtuemart_product_prices', array('virtuemart_product_id','virtuemart_shoppergroup_id', 'product_price_publish_up', 'product_price_publish_down', 'product_price')); 
		  
		  }
		  }
		  
		  
		  //virtuemart_customs
		  $z = OPCmini::hasIndex('virtuemart_customs', array('virtuemart_custom_id','published')); 
		   if (empty($z)) {
		     $z = OPCmini::addIndex('virtuemart_customs', array('virtuemart_custom_id','published')); 
		   }
		   $z = OPCmini::hasIndex('virtuemart_product_customs', array('virtuemart_custom_id','virtuemart_product_id', 'ordering')); 
		   if (empty($z)) {
		      $z = OPCmini::addIndex('virtuemart_product_customs', array('virtuemart_custom_id','virtuemart_product_id', 'ordering')); 
		   }
		   
		   
		   $z = OPCmini::hasIndex('virtuemart_calcs', array('calc_kind','published', 'virtuemart_vendor_id', 'shared', 'publish_up', 'publish_down')); 
		   if (empty($z)) {
		     $z = OPCmini::addIndex('virtuemart_calcs', array('calc_kind','published', 'virtuemart_vendor_id', 'shared', 'publish_up', 'publish_down')); 
		   }
		   
		  
		  $z = OPCmini::hasIndex('virtuemart_products', array('virtuemart_vendor_id', 'virtuemart_product_id','published')); 
		  if (empty($z)) {
		     $z = OPCmini::addIndex('virtuemart_products', array('virtuemart_vendor_id', 'virtuemart_product_id','published')); 
		  }
		  
		   $z = OPCmini::hasIndex('virtuemart_orders', array('virtuemart_order_id', 'virtuemart_user_id')); 
		   if (empty($z)) {
		     $z = OPCmini::addIndex('virtuemart_orders', array('virtuemart_order_id', 'virtuemart_user_id')); 
		   }
		   
		  
		  //virtuemart_rating_vote_id
		  //virtuemart_product_id
		  //created_by
		  //created_on
		  $z = OPCmini::hasIndex('virtuemart_rating_votes', array('virtuemart_rating_vote_id', 'virtuemart_product_id','created_by', 'created_on')); 
		  if (empty($z)) {
		    $z = OPCmini::addIndex('virtuemart_rating_votes', array('virtuemart_rating_vote_id', 'virtuemart_product_id','created_by', 'created_on')); 
		  }
		  
		  //virtuemart_products
		  //virtuemart_product_id
		  //virtuemart_custom_id
		  
		   $z = OPCmini::hasIndex('virtuemart_calc_categories', 'virtuemart_category_id'); 
		 
		 if (empty($z)) { 
		 
		   $q = 'ALTER TABLE  `#__virtuemart_calc_categories` ADD INDEX  `opc_virtuemart_category_id` (  `virtuemart_category_id` )'; 
		   $db->setQuery($q); 
		   $db->query(); 
		 }
		 
		  $z = OPCmini::hasIndex('virtuemart_calc_countries', 'virtuemart_country_id'); 
		 
		 if (empty($z)) { 
		 
		   $q = 'ALTER TABLE  `#__virtuemart_calc_countries` ADD INDEX  `opc_virtuemart_country_id` (  `virtuemart_country_id` )'; 
		   $db->setQuery($q); 
		   $db->query(); 
		 }
		 //virtuemart_manufacturer_id
		  $z = OPCmini::hasIndex('virtuemart_calc_manufacturers', 'virtuemart_manufacturer_id'); 
		
		 if (empty($z)) { 
		 
		   $q = 'ALTER TABLE  `#__virtuemart_calc_manufacturers` ADD INDEX  `opc_virtuemart_manufacturer_id` (  `virtuemart_manufacturer_id` )'; 
		   $db->setQuery($q); 
		   $db->query(); 
		 }
		 //virtuemart_shoppergroup_id
		   $z = OPCmini::hasIndex('virtuemart_calc_shoppergroups', 'virtuemart_shoppergroup_id'); 
		 
		 if (empty($z)) { 
		 
		   $q = 'ALTER TABLE  `#__virtuemart_calc_shoppergroups` ADD INDEX  `opc_virtuemart_shoppergroup_id` (  `virtuemart_shoppergroup_id` )'; 
		   $db->setQuery($q); 
		   $db->query(); 
		 }
		 
		  $z = OPCmini::hasIndex('virtuemart_calc_states', 'virtuemart_state_id'); 
		 
		 if (empty($z)) { 
		 
		   $q = 'ALTER TABLE  `#__virtuemart_calc_states` ADD INDEX  `opc_virtuemart_state_id` (  `virtuemart_state_id` )'; 
		   $db->setQuery($q); 
		   $db->query(); 
		 }
		 
		  
		  $z = OPCmini::hasIndex('virtuemart_order_histories', 'virtuemart_order_id'); 
		 if (empty($z)) {
		   $q = 'ALTER TABLE  `#__virtuemart_order_histories` ADD INDEX  `opc_order_id_index` (  `virtuemart_order_id` )'; 
		   $db->setQuery($q); 
		   $db->query(); 
		   $msg .= JText::_('COM_ONEPAGE_FIXED_INDEX_ON_ORDER_HISTORY')."<br />"; 
		 }
		 
		  $z = OPCmini::hasIndex('virtuemart_order_calc_rules', 'virtuemart_order_id'); 
		 if (empty($z)) {
		   $q = 'ALTER TABLE  `#__virtuemart_order_calc_rules` ADD INDEX  `virtuemart_order_id` (  `virtuemart_order_id` )'; 
		   $db->setQuery($q); 
		   $db->query(); 

		 }
		 
		 $q = 'ALTER TABLE  `#__virtuemart_order_calc_rules` CHANGE  `virtuemart_order_id`  `virtuemart_order_id` INT( 1 ) UNSIGNED NULL DEFAULT NULL'; 
		 $db->setQuery($q); 
		 $db->query(); 
		 
		 $q = 'ALTER TABLE  `#__virtuemart_order_items` CHANGE  `virtuemart_order_id`  `virtuemart_order_id` INT( 1 ) UNSIGNED NULL DEFAULT NULL'; 
		 $db->setQuery($q); 
		 $db->query(); 
		 
		
		 
		 //g52p3_virtuemart_order_calc_rules"
		  
		
		  
		  
		  
		  
		  // install install debug
		 
		  
		  $orderxml = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'cart'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'order.xml'; 
		
		if (!file_exists($orderxml))
		{
		  JFile::copy(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'vm'.DIRECTORY_SEPARATOR.'order.xml', $orderxml); 
		}
		  
		$orderxml2 = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'default.xml';
		if (!file_exists($orderxml2))
		{
		  JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'opc'); 
		  $data = ''; 
		  JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'index.html', $data); 
		  JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'tmpl'); 
		  JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'index.html', $data); 
		  JFile::copy(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'vm'.DIRECTORY_SEPARATOR.'default.xml', $orderxml2); 
		}
		
		  $msg .= $this->patchcalculationh(); 
		  
		  $lang = JFactory::getLanguage();
		  if (method_exists($lang, 'getKnownLanguages'))
		  $list = $lang->getKnownLanguages(); 
		 
		 
		 
		 $key = 'en-GB'; 
	     
	  
		  if (!empty($list))
		  foreach ($list as $key=>$val)
		  {
			  
			  if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR)) continue; 
			  if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR)) continue; 
			  if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key)) continue;
			  
			  
			  if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.ini'))  
		 {
			 if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.ini'))
			 if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.ini'))
			 {
			 JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.ini' , JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.ini');
			 
			 }
			 }
			
			if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.sys.ini'))
			JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.sys.ini' , JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.sys.ini');
			
			if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.plg_system_opc.sys.ini'))
			JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'en-GB'.DIRECTORY_SEPARATOR.'en-GB'.'.plg_system_opc.sys.ini' , JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.plg_system_opc.sys.ini');

			if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.plg_vmpayment_opctracking.sys.ini'))
			JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'en-GB'.DIRECTORY_SEPARATOR.'en-GB'.'.plg_vmpayment_opctracking.sys.ini' , JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.plg_vmpayment_opctracking.sys.ini');

		
			
			if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.plg_system_opcregistration.sys.ini'))
			JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'en-GB'.DIRECTORY_SEPARATOR.'en-GB'.'.plg_system_opcregistration.sys.ini' , JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.plg_system_opcregistration.sys.ini');
			
			  if ($key == 'en-GB') continue; 
			if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.ini'))  
			{
				
				$orig_lang = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.ini'; 
		  $orig_sys_lang = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.ini'; 
				 
				 if (file_exists($orig_lang))
				 {
				 if (!JFile::copy($orig_lang, JPATH_ROOT.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.ini'))
			     {
	    		  $msg .= JText::_('COM_ONEPAGE_CANNOT_INSTALL_LANGUAGE_FILE').' /language/'.$key.'/'.$key.'.com_onepage.ini <br />';
	    		  
			      }
				 if (!JFile::copy($orig_sys_lang, JPATH_ROOT.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$key.DIRECTORY_SEPARATOR.$key.'.com_onepage.ini'))
			     {
	    		  $msg .= JText::_('COM_ONEPAGE_CANNOT_INSTALL_LANGUAGE_FILE').' /language/'.$key.'/'.$key.'.com_onepage.ini <br />';
	    		  
			      }
			 }
			 
			 
			 
			}
		  }
		  
		  
		  if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'en-GB'.DIRECTORY_SEPARATOR.'en-GB.com_onepage.ini'))
		   {
		        
		  
		   
		    if (!JFile::copy($orig, JPATH_ROOT.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'en-GB'.DIRECTORY_SEPARATOR.'en-GB.com_onepage.ini'))
			 {
	    		  $msg .= JText::_('COM_ONEPAGE_CANNOT_INSTALL_LANGUAGE_FILE').' /language/en-GB/en-GB.com_onepage.ini <br />';
	    		  
			 }
			 //else 
			 //  $msg .= 'OPC Language files installed in /language/en-GB/en-GB.com_onepage.ini <br />'; 

			 if (!JFile::copy($orig_sys, JPATH_ROOT.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'en-GB'.DIRECTORY_SEPARATOR.'en-GB.com_onepage.ini'))
			 {
	    		  $msg .= JText::_('COM_ONEPAGE_CANNOT_INSTALL_LANGUAGE_FILE').' /language/en-GB/en-GB.com_onepage.sys.ini <br />';
	    		  
			 }
			 //else
			  // $msg .= 'OPC Language files installed in /language/en-GB/en-GB.com_onepage.sys.ini <br />'; 

			 
		  }
		  
		   $x = JFactory::getApplication()->set('messageQueue', array()); 
		  $x = JFactory::getApplication()->set('_messageQueue', array()); 
		  
		$lang = JFactory::getLanguage();
		$extension = 'com_onepage';
		$lang->load($extension, JPATH_ADMINISTRATOR, 'en-GB');
		$tag = $lang->getTag();
		if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$tag.DIRECTORY_SEPARATOR.$tag.'.com_onepage.ini'))
		$lang->load('com_onepage', JPATH_ADMINISTRATOR, $tag, true, true);

		   
		  // since june 2012 we will use our own document type:
		  if(version_compare(JVERSION,'3.8.0','ge')) {
			  $fdocfile = JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Document'.DIRECTORY_SEPARATOR.'OpchtmlDocument.php'; 
			  if (!file_exists($fdocfile)) {
			     if (JFile::copy(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'OpchtmlDocument.php', $fdocfile)===false) 
				 { 
					$msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', 'OpchtmlDocument.php', $fdocfile).'<br />'; ; //'Cannot create own document type file in '.$fdocfile.'<br />'; 	  
				 }
			  }
		  }
		  else {
		  $fdoc = JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.'opchtml';
		  $fdocfile = JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.'opchtml'.DIRECTORY_SEPARATOR.'opchtml.php'; 

		  if (!file_exists($fdoc))
		  {
		    if (JFolder::create($fdoc)===false) $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY',$fdoc).' (opc document type)<br />'; 
		  }


		  // always update: 
		  /*
		  if (!file_exists($fdocfile))
		  */
		   {
		     if (JFile::copy(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'opchtml.php', $fdocfile)===false) $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', 'opchtml.php', $fdoc).'<br />'; ; //'Cannot create own document type file in '.$fdocfile.'<br />'; 
			 $st = ' '; 
			 if (!file_exists($fdoc.DIRECTORY_SEPARATOR.'index.html'))
			 if (JFile::write($fdoc.DIRECTORY_SEPARATOR.'index.html', $st)===false) $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', 'index.html', $fdoc).'<br />'; 
		   }
		   /*
		   else
		   {
		      $m = filemtime($fdocfile); 
			  $m2 = date('2014-10-10'); 
			  if ($m < $m2)
			   {
		     
			 if (JFile::copy(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'opchtml.php', $fdocfile)===false) $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', 'opchtml.php', $fdoc).'<br />'; ; //'Cannot create own document type file in '.$fdocfile.'<br />'; 
			 $st = ' '; 
			 
			 if (JFile::write($fdoc.DIRECTORY_SEPARATOR.'index.html', $st)===false) $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_FILE_IN', 'index.html', $fdoc).'<br />'; 

			   
			   }
		   }
		   */
		  }
		   
		   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			
			$db = JFactory::getDBO(); 
			$q = 'select `params` from `#__extensions` where `element` = \'com_sh404sef\' limit 0,1'; 
			$db->setQuery($q); 
			$data = $db->loadResult();
			
		
			if (!empty($data))
			{
			  $dt = json_decode($data); 
			  if (!empty($dt->shSecActivateAntiFlood))
			  {
			    $dt->shSecActivateAntiFlood = 0; 
				JFactory::getApplication()->enqueueMessage('sh404 Antiflood was disabled by OPC !', 'warning'); 
				
			  }
		
	
			  if (isset($dt->com_onepage___manageURL))
			  $dt->com_onepage___manageURL = (string)2; 
			
			  if (stripos($dt->shurlBlackList, 'nosef')===false)
			   {
			      if (!empty($dt->shurlBlackList))
				  $dt->shurlBlackList .= '|nosef'; 
				  else 
				  $dt->shurlBlackList .= 'nosef'; 
			   }
			  if (stripos($dt->shurlNonSefBlackList, 'nosef')===false)
			   {
			      if (!empty($dt->shurlNonSefBlackList))
				  $dt->shurlNonSefBlackList .= '|nosef=1|nosef'; 
				  else 
				  $dt->shurlNonSefBlackList .= 'nosef=1|nosef|controller=opc'; 
			   }
			   
			   $q = 'update `#__extensions` set `params` = '.$db->quote(json_encode($dt)).' where `element` = \'com_sh404sef\' '; 
			   $db->setQuery($q); 
			   $db->query(); 
			   
			   
			   
			   if (OPCmini::tableExists('sh404sef_urls'))
			   {
			    $q = 'delete from #__sh404sef_urls where newurl like \'%nosef=%\' or newurl like \'%option=com_onepage%\' or newurl like \'%view=opc%\' '; 
			    $db->setQuery($q); 
			    $db->query(); 
			   }
			   
		    }
			
			if (defined('VM_VERSION') && (VM_VERSION >= 3))
			{
			   $q = 'update #__virtuemart_userfields set required = 0 where name="tos" and type="custom" and cart="1" and sys="1" limit 1'; 
			   $db->setQuery($q); 
			   $db->query(); 
			}
			
			// vm3.0.6 fix here: 
			if (defined('VM_VERSION') && (VM_VERSION >= 3))
			{
			   $q = 'update #__virtuemart_userfields set sys = 0 where (name="first_name" or name="last_name" or name="middle_name") and type="text" and sys="1" limit 1'; 
			   $db->setQuery($q); 
			   $db->query(); 
			}
			
			
			$q = 'update #__virtuemart_userfields set `default` = NULL where name="address_type_name" and sys="1" limit 1'; 
			   $db->setQuery($q); 
			   $db->query(); 
			
			
			// we need to check a fatal error in vm 2.0.4: 
			if (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'invoice'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'mail_html_pricelist.php'))
			 {
			   
			   $ver = phpversion(); 
			   if ((strpos($ver, '5.3')===false) && ((strpos($ver, '5.4')===false)))
			    {
				  $content = file_get_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'invoice'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'mail_html_pricelist.php');
				  if (strpos($content, '__DIR__')!==false)
				  {
				  $content = str_replace('__DIR__', 'dirname(__FILE__)', $content); 
				  if (JFile::write(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'invoice'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'mail_html_pricelist.php', $content)!==false)
				   {
					   $msg .= JText::sprintf('COM_ONEPAGE_PATCHED', JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'invoice'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'mail_html_pricelist.php', 'http://www.rupostel.com/').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
				     //$msg .= 'Patched a Virtuemart bug (fatal error) in '.JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'invoice'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'mail_html_pricelist.php'.'<br />'; 
				   }
				   else 
					   $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_PATCH', JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'invoice'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'mail_html_pricelist.php', 'http://www.rupostel.com/').' Please replace __DIR__ with dirname(__FILE__) <br />'; 
					 //$msg .= JText::sprintf('COM_ONEPAGE_PATCHED', JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'invoice'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'mail_html_pricelist.php', 'http://www.rupostel.com/').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
				    //
				  }
				}
			 }
			 
			 
			 // another fatal error in vm: \administrator\components\com_virtuemart\models\userfields.php
			if (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'userfields.php')) 
			 {
			   $f = JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'userfields.php'; 
			   //$ver = phpversion(); 
			   //if (strpos($ver, '5.3')===false)
			    {
				  $content = file_get_contents($f);
				  if (strpos($content, '$_return[\'fields\'][$_fld->name][\'formcode\'] =  JHTML::_(\'select.radiolist\', $_values, $_prefix.$_fld->name, $_attribs, $_selected, \'fieldvalue\', \'fieldtitle\');')!==false)
				  {
				  $content = str_replace('$_return[\'fields\'][$_fld->name][\'formcode\'] =  JHTML::_(\'select.radiolist\', $_values, $_prefix.$_fld->name, $_attribs, $_selected, \'fieldvalue\', \'fieldtitle\');', '$_return[\'fields\'][$_fld->name][\'formcode\'] =  JHTML::_(\'select.radiolist\', $_values, $_prefix.$_fld->name, $_attribs, \'fieldvalue\', \'fieldtitle\', $_selected); //// this line was fixed by OPC', $content); 
				  if (JFile::write($f, $content)!==false)
				   {
					   $msg .= JText::sprintf('COM_ONEPAGE_PATCHED', $f, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=171').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
				     //$msg .= 'Patched a Virtuemart bug (fatal error) in '.$f.' according to this <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=171">description</a><br />'; 
				   }
				   else 
					   $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_PATCH', $f, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=171').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
				    //$msg .= 'Cannot patch a Virtuemart bug (fatal error) in '.$f.' Please read the following if you would like to use checkboxes in your registration <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=171">RuposTel support forum</a> <br />'; 
				  }
				}
			 }
			
			$file = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'; 
			
			$x = file_get_contents($file); 
			
			$s = 'vmError(\'Could not use path \'.$file.\' to store log\');'; 
			$p = stripos($x, $s); 
			if ($p !== false)
			{
			  JFile::copy($file, $file.'opc_backup.php'); 
			  $data = str_replace($s, '//OPC: removed a line that caused a fatal error', $x); 
			  if (JFile::write($file, $data)===false)
			 {
			  $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_PATCH', $file, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=806&start=10').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
			 }
			 else
			  $msg .= JText::sprintf('COM_ONEPAGE_PATCHED', $file, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=806&start=10').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
			}


			if(JVM_VERSION >= 2) {
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home`="1"';
			} else {
				$q = 'SELECT `template` FROM `#__templates_menu` WHERE `client_id`="0" AND `menuid`="0"';
			}
			$db = JFactory::getDbo();
			$db->setQuery( $q );
			$template = $db->loadResult();
			
			$fx = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart'.DIRECTORY_SEPARATOR.'helper.php'; 
			
			if(!class_exists('JFile')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'file.php');
		    if(!class_exists('JFolder')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');

			
			if (file_exists($fx))
			 {
			    if (JFolder::move($fx = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart', 
				$fx = JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart_opcrenamed')===true)
				 {
				   $msg .= JText::_('COM_ONEPAGE_RENAMED_LINELAB_THEMEOVERRIDES').' '. JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart_opcrenamed'."<br />\n"; 
				 }
				 else
				 {
				   $msg .= JText::_('COM_ONEPAGE_RENAMED_LINELAB_THEMEOVERRIDES_ERROR').'  '.JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$template.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'cart'."<br />\n"; 
				 }
				
			 }
			
			if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'hathor'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_onepage'))
			{
			  JFolder::delete(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'hathor'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_onepage'); 
			  JFactory::getApplication()->enqueueMessage('OPC: Template overrides are not supported by OPC at the backend, please refresh the configuration page'); 
			}

			if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'isis'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_onepage'))
			{
			  JFolder::delete(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'isis'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_onepage'); 
			  JFactory::getApplication()->enqueueMessage('OPC: Template overrides are not supported by OPC at the backend, please refresh the configuration page'); 
			}
			
			if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'bluestork'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_onepage'))
			{
			  JFolder::delete(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'bluestork'.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'com_onepage'); 
			  JFactory::getApplication()->enqueueMessage('OPC: Template overrides are not supported by OPC at the backend, please refresh the configuration page'); 
			}
			
			
			 // plugin fix for Joomla 1.5
			 if(!(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')))
			 {
			 // we need to fix a bug in VM2.0 and J1.5:
			$search = '$dispatcher->trigger(\'onVmSiteController\', $_controller);';
			$rep = '$dispatcher->trigger(\'onVmSiteController\', array($_controller));';
			$x = file_get_contents(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'virtuemart.php'); 
		
		   $search2 = "(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'ICal'.DIRECTORY_SEPARATOR.'PublicHolidays.php')";
		   $rep2 = "(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'ICal'.DIRECTORY_SEPARATOR.'PublicHolidays.php')";
		    
		   if ((strpos($x, $search)!==false) || ((strpos($x, $search2)!==false)))
		   {
		     $x = str_replace($search, $rep, $x); 
			 $x = str_replace($search2, $rep2, $x); 
		     JFile::copy(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'virtuemart.php', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'virtuemart.orig.opc_bck.php'); 
		     if (JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'virtuemart.php', $x)===false)
			 {
			  $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_PATCH', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'virtuemart.php', 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
			 }
			 else
			  $msg .= JText::sprintf('COM_ONEPAGE_PATCHED', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'virtuemart.php', 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170').'<br />'; //'Cannot Patch a Virtuemart 2.0.x bug which occurs in Joomla 1.5 described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=170">here.</a>.<br />'; 
			   ///$msg .= 'Patched Joomla 1.5 compatibility bug in /components/com_virtuemart/virtuemart.php '; 
			 
		   }
			 }

			// we need to check a dual "Product successfully added" error in vm 2.0.6
			$f = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'cart.php'; 
			if (file_exists($f))
			 {
			   
			   //$ver = phpversion(); 
			   //if (strpos($ver, '5.3')===false)
			    {
				  $content = file_get_contents($f);
				  if ((strpos($content, '$mainframe->enqueueMessage($msg);')!==false) && (strpos($content, 'OPC fix')===false))
				  {
				  $content = str_replace('$mainframe->enqueueMessage($msg);', '//// OPC fix: $mainframe->enqueueMessage($msg);', $content); 
				  if (JFile::write($f, $content)!==false)
				   {
				     $msg .= JText::sprintf('COM_ONEPAGE_PATCHED', $f, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=169').'<br />'; 
				   }
				   else 
					   $msg .= JText::sprintf('COM_ONEPAGE_CANNOT_PATCH', $f, 'http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=169').'<br />'; 
				    //$msg .= 'Cannot patch a Virtuemart bug in '.$f.' described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=5&t=169">here</a> <br />'; 
				  }
				}
			 }
		
        // this portion of code will check if any column named id in #__virtuemart_payment_{plugin_name} uses tinyint ID and alters the database structure
		// $msg string holds info for GUI to show what has changed in the DB structure or the mysql error
		// this code is not tested when there are more databases available to mysql user
		// $msg = ''; 
		$dbj = JFactory::getDBO();
		$prefix = $dbj->getPrefix();
		$q = "SHOW TABLES LIKE '".$prefix."virtuemart_payment_plg_%'";
		
	    $dbj->setQuery($q);
	    $r = $dbj->loadAssocList();
		
		if (!empty($r))
		foreach ($r as $key=>$table)
		 {
		   if (!is_array($table)) continue;
		   $plgtable = reset($table); 
		   $q = 'describe '.$plgtable.' id'; 
		   $dbj->setQuery($q); 
		   $res = $dbj->loadAssoc();
		   
		   
		   if (stripos($res['Type'], 'tinyint')!==false)
		    {
			  $msg .= JText::sprintf('COM_ONEPAGE_TINY_INT_ERROR', $plgtable).'<br />'; //.' uses tinyint as default index which is limited to 255 records (orders). OPC tries to fix this bug for you within DB structure.<br />'; 
			  
			  $q = 'ALTER TABLE  `'.$plgtable.'` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT'; 
			  $dbj->setQuery($q); 
			  $x = $dbj->query(); 
			  $e = $dbj->getErrorMsg(); 
			  if (!empty($e)) $msg .= $e.'<br />'; 
			  else $msg .= '<span style="color: green;">'.JText::sprintf('COM_ONEPAGE_DATABASE_UPDATED',$plgtable).'</span><br />'; 
			  
			}
		   
		 }
		
		$q = 'describe '.$prefix.'session data'; 
		$dbj->setQuery($q); 
		$r = $dbj->loadAssoc();
		$type = $r['Type']; 
		
		if ((stripos($type, 'varchar')!==false) || (stripos($type, 'text')===0))
		{
		  $msg .= JText::sprintf('COM_ONEPAGE_SESSION_SMALL', $type); //'Your session data column is too small for your shop: #__session.data is of type <span style="color: red;">'.$type.'</span>. OPC updates your session database structure to <span style="color: green;">mediumtext</span> which is recommended for VirtueMart implementation.<br />'; 
		  $q = 'ALTER TABLE  `'.$prefix.'session` CHANGE  `data`  `data` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL'; 
		  $dbj->setQuery($q); 
		  $dbj->query(); 
		  $e = $dbj->getErrorMsg(); 
		  if (!empty($e))
		  $msg .= $e.'<br />'; 
		  //else $msg .= '<span style="color: green;">Session data column updated sucessfully.</span>'; 
		  
		}
		//disabled in opc 257:
		/*
		if (!OPCJ3)
		{
		
				$db = JFactory::getDBO();
		// check shopper groups: 
		$q = 'select * from #__virtuemart_shoppergroups where virtuemart_shoppergroup_id = 1'; 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		$err = false; 
		if (empty($res)) 
		{
			
			// insert the default anonymous group
		$err = true;
			$q = "update #__virtuemart_shoppergroups set `shopper_group_name` = '-anonymous OLD -' where `shopper_group_name` = '-anonymous-' limit 1"; 
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "INSERT INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `custom_price_display`, `price_display`, `default`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES (1, 1, '-anonymous-', 'Shopper group for anonymous shoppers', 0, NULL, 2, 0, 1, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0)";
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$msg .= JText::_('COM_ONEPAGE_FIXED_ANONYMOUS').'<br />'; 
		}
		else
		{
			// update the default anonymous group
			
		if ($res['virtuemart_vendor_id'] != '1') $err = true; 
		if ($res['default'] != '2') $err = true; 
		if ($res['shared'] != '1') $err = true; 
		if ($res['published'] != '1') $err = true; 
		if ($err)
		{
			$q = 'delete from #__virtuemart_shoppergroups where virtuemart_shoppergroup_id = 1 limit 1'; 
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "update #__virtuemart_shoppergroups set `shopper_group_name` = '-anonymous OLD -' where `shopper_group_name` = '-anonymous-' limit 1"; 
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "INSERT INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `custom_price_display`, `price_display`, `default`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES (1, 1, '-anonymous-', 'Shopper group for anonymous shoppers', 0, NULL, 2, 0, 1, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0)";
			$db->setQuery($q); $db->query(); 
			$e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$msg .= JText::_('COM_ONEPAGE_FIXED_ANONYMOUS').'<br />'; 
			//$msg .= 'Fixed Virtuemart -anonymous- shopper group which is required for proper Virtueamrt functionality. Please check the configuration of your shipping, payment, calculation plugins.  <br />'; 
		}
		
		}
	
		// check shopper groups: 
		$q = 'select * from #__virtuemart_shoppergroups where virtuemart_shoppergroup_id = 2'; 
		$db->setQuery($q); 
		$res = $db->loadAssoc(); 
		$e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
		$err = false; 
		if (empty($res)) 
		{
			
			// insert the default -default- group
		$err = true;
			$q = "update #__virtuemart_shoppergroups set `shopper_group_name` = '-default OLD -' where `shopper_group_name` = '-default-' limit 1"; 
			$db->setQuery($q); $db->query(); 
			$e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "INSERT INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `custom_price_display`, `price_display`, `default`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES (2, 1, '-default-', 'This is the default shopper group.', 0, NULL, 1, 0, 1, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0)";
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$msg .= JText::_('COM_ONEPAGE_FIXED_DEFAULT').'<br />'; 
			//$msg .= 'Fixed Virtuemart -default- shopper group which is required for proper Virtueamrt functionality. Please check the configuration of your shipping, payment, calculation plugins.  <br />'; 
		}
		else
		{
			// update the default -default- group
			
		if ($res['virtuemart_vendor_id'] != '1') $err = true; 
		if ($res['default'] != '1') $err = true; 
		if ($res['shared'] != '1') $err = true; 
		if ($res['published'] != '1') $err = true; 
		if ($err)
		{
			$q = 'delete from #__virtuemart_shoppergroups where virtuemart_shoppergroup_id = 2 limit 1'; 
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "update #__virtuemart_shoppergroups set `shopper_group_name` = '-default OLD -' where `shopper_group_name` = '-default-' limit 1"; 
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$q = "INSERT INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `custom_price_display`, `price_display`, `default`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES (2, 1, '-default-', 'This is the default shopper group.', 0, NULL, 1, 0, 1, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0)";
			$db->setQuery($q); $db->query(); $e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 
			$msg .= JText::_('COM_ONEPAGE_FIXED_DEFAULT').'<br />'; 
			//$msg .= 'Fixed Virtuemart -default- shopper group which is required for proper Virtueamrt functionality. Please check the configuration of your shipping, payment, calculation plugins.  <br />'; 
		}
		
		}
		$q = 'update #__virtuemart_shoppergroups set `default` = "0" where ((`default` > 0) and ((virtuemart_shoppergroup_id <> 1) AND (virtuemart_shoppergroup_id <> 2))) limit 10'; 
		
		$db->setQuery($q);
		$db->query(); 
		$e = $db->getErrorMsg(); if (!empty($e)) { $msg .= $e.'<br />'; }; 		
		}
		*/
	 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
	 include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php');
	
     if (!isset($opc_plugin_order)) $opc_plugin_order = -999; 
	else $opc_plugin_order = (int)$opc_plugin_order; 

			if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "update `#__extensions` set `ordering` = ".$opc_plugin_order.", `state` = 0 where `element` = 'opc' and `type` = 'plugin' and `folder` = 'system' limit 2 "; 
	  }
	  else
	  {
	    $q = "update `#__plugins` set `ordering` = ".$opc_plugin_order." where `element` = 'opc' and `folder` = 'system'  limit 2 "; 
	  }
	  
	  
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "select * from `#__extensions` where `element` = 'opctracking' and `type` = 'plugin' and `folder` = 'vmpayment' limit 0,1"; 
	   $db->setQuery($q); 
	   $res = $db->loadAssoc(); 
	   // if plugin installed: 
	   if (!empty($res))
	   {
	    $q = 'select max(ordering) from `#__extensions` where 1'; 
	    $db->setQuery($q); 
	    $ordering = (int)$db->loadResult(); 
	    $q = "update `#__extensions` set `ordering` = ".$ordering.", `state` = 0 where `element` = 'opctracking' and `type` = 'plugin' and `folder` = 'vmpayment' limit 2 "; 
	   }
	  }
	  else
	  {
	  
	      $q = "select * from #__plugins where element = 'opctracking' and folder = 'vmpayment' limit 0,1"; 
	   $db->setQuery($q); 
	   $res = $db->loadAssoc(); 
	   // if plugin installed: 
	    if (!empty($res))
	    {
		  $q = 'select max(ordering) from #__plugins where 1'; 
	    $db->setQuery($q); 
	    $ordering = (int)$db->loadResult(); 
	    $q = "update #__plugins set ordering = ".$ordering." where element = 'opctracking' and folder = 'vmpayment'  limit 2 "; 
		}
	  }

	  
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$db->query(); 
		$e = $db->getErrorMsg();
		if (!empty($e))
		$msg .= $e.'<br />'; 
	    
		
		
		// install OPC config table: 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::clearTableExistsCache(); 
		
		if (!OPCmini::tableExists('onepage_moss')) {
			$f = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vat.php'; 
			require_once($f); 
					  
		    OPCVatWorker::createTable(); 
		}
		
		
	 if (!OPCmini::tableExists('onepage_config'))
	 {
		$q = ' CREATE TABLE IF NOT EXISTS `#__onepage_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_name` varchar(100) NOT NULL,
  `config_subname` varchar(100) NOT NULL,
  `config_ref` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`config_name`,`config_subname`,`config_ref`),
  KEY `config_name_2` (`config_name`,`config_subname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=85 ; 
'; 
    $db->setQuery($q); 
	$db->query(); 
	$e = $db->getErrorMsg(); 
	if (!empty($e))
	$msg .= $e.'<br />'; 

	}
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	$ind = OPCmini::hasIndex('onepage_config', 'config_name_3'); 
	if ($ind !== -1)
	if ($ind) {
		
	$q = 'ALTER TABLE `#__onepage_config` DROP INDEX `config_name_3`'; 
	try {
	$db->setQuery($q); 
	$db->query(); 
	} catch (Exception $e) {
	
	}
	}
	
	if (OPCmini::tableExists('onepage_export_templates_settings')) {
		$ind = OPCmini::hasIndex('onepage_export_templates_settings', 'tid_2'); 
		if ($ind !== -1)
		if ($ind) {
		$q = 'ALTER TABLE `#__onepage_export_templates_settings` DROP INDEX `tid_2`'; 
	try {
	$db->setQuery($q); 
	$db->query(); 
	} catch (Exception $e) {
	
	}
		}
	}
	
	
	
	if (OPCmini::tableExists('virtuemart_plg_opctracking')) {
		$ind = OPCmini::hasIndex('virtuemart_plg_opctracking', 'modified'); 
		if ($ind !== -1)
	    if (!$ind)
		{
			OPCmini::addIndex('virtuemart_plg_opctracking', array('modified'));
	
			
		}
		
	    
	
	
	}
	
	OPCmini::clearTableExistsCache(); 
  
  	 //update from prior opc versions: 
	 $db = JFactory::getDBO(); 
     $q = "delete from `#__extensions` WHERE  `element` = 'opctracking' and `folder` = 'system' "; 
     $db->setQuery($q); 
	 $db->query(); 
	 
	 if (is_dir(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctracking'))
	 JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctracking'); 
  
  
			if (!empty($msg))
			if (empty($_SESSION['onepage_err']))
    	         $_SESSION['onepage_err'] = serialize($msg);
    	         else 
    	         {
    	          $_SESSION['onepage_err'] = serialize($msg.unserialize($_SESSION['onepage_err']));
    	         }
		   
		}
		
		private function installAwoView() {
		$msg  = ''; 
		$templates = JPATH_ROOT.DIRECTORY_SEPARATOR.'templates'; 
		$dirs = scandir($templates); 
		foreach ($dirs as $dir) {
		
			if (($dir == '.') || ($dir == '..')) continue; 
			if (file_exists($templates.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.'html')) {
				$overrides_path = $templates.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.'html';
				if (!file_exists($overrides_path.DIRECTORY_SEPARATOR.'com_awocoupon')) {
					jimport( 'joomla.filesystem.folder' );
					JFolder::create($overrides_path.DIRECTORY_SEPARATOR.'com_awocoupon'); 
				}
				
				if (!file_exists($overrides_path.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'coupondelete')) {
					JFolder::create($overrides_path.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'coupondelete'); 
				}
				
				if (!file_exists($overrides_path.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'coupondelete')) {
					JFolder::create($overrides_path.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'coupondelete'); 
				}
				
				if (!file_exists($overrides_path.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'coupondelete'.DIRECTORY_SEPARATOR.'default.php')) {
					$from_file = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'coupondelete'.DIRECTORY_SEPARATOR.'default_include.php'; 
					$to_file = $overrides_path.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'coupondelete'.DIRECTORY_SEPARATOR.'default.php';
					JFile::copy($from_file, $to_file); 
					
					$msg .= 'AwoCoupon template for OPC compatiblity was installed to '.$to_file.'<br />'; 
				}
				
				
				
			}
		}
		
		return $msg; 
		
	}
		
		function alterVMConfig($data) {
		   {
    if (VmConfig::get('oncheckout_only_registered', 0))
	{
	  if (VmConfig::get('oncheckout_show_register', 0))
	  define('VM_REGISTRATION_TYPE', 'NORMAL_REGISTRATION'); 
	  else 
	  define('VM_REGISTRATION_TYPE', 'SILENT_REGISTRATION'); 
	}
	else
	{
	if (VmConfig::get('oncheckout_show_register', 0))
    define('VM_REGISTRATION_TYPE', 'OPTIONAL_REGISTRATION'); 
	else 
	define('VM_REGISTRATION_TYPE', 'NO_REGISTRATION'); 
	}
   }
	$reg_type = $data['opc_registraton_type']; 
	$set = array(); 
	switch ($reg_type)
	{
		case 'NO_REGISTRATION': 
		$set['oncheckout_only_registered'] =  0;
		$set['oncheckout_show_register'] =  0;
		break; 
		case 'OPTIONAL_REGISTRATION': 
		$set['oncheckout_only_registered'] =  0;
		$set['oncheckout_show_register'] =  1;

		break; 
		case 'SILENT_REGISTRATION': 
					$set['oncheckout_only_registered'] =  1;
		$set['oncheckout_show_register'] =  0;
		break; 
		default: 
					$set['oncheckout_only_registered'] =  1;
		$set['oncheckout_show_register'] =  1;

		break; 
		
	}
	
	if (!empty($data['enable_captcha_reg']))
	{
			  $set['reg_captcha'] = 1; 
	}
	else
	{
			  $set['reg_captcha'] = 0; 
	}
	
	if (!empty($data['use_ssl']))
	{
	  $set['useSSL'] = 1; 
	}
	else
	 $set['useSSL'] = 0; 
	 
	if (!empty($data['full_tos_unlogged']))
	{
		$set['oncheckout_show_legal_info'] =  1;
	}
	else
		$set['oncheckout_show_legal_info'] =  0;
	
	if (!empty($data['tos_logged']) && (!empty($data['tos_unlogged'])))
	{
		$set['agree_to_tos_onorder'] =  1;
	}
	else
	{
		$set['agree_to_tos_onorder'] =  0;
	}
	
	$set['maskIP'] = ''; 
	
	//ALWAYS DISABLE: if (!empty($data['op_disable_shipping']))
	
	$set['automatic_shipment'] =  0;
	$set['set_automatic_shipment'] = '-1'; 
	
	
	$set['automatic_payment'] = 0; 
	$set['set_automatic_payment'] = '-1'; 
	$set['automatic_shipment'] = 0; 
	$set['oncheckout_opc'] = 0; 
	
	
	$this->updateVmConfig($set);
	
	$msg = $this->fixVmCache(); 
	return $msg; 
		}
		
		function updateOPCShipping() {
			
			
			
			
		 if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opc_shipping_last'))
		  {
			JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opc_shipping_last'); 
			
			
			
		  }
		  
		    {
			  $q = 'select * from `#__extensions` where `element` = "opc_shipping_last" limit 0,1';  
			  $db = JFactory::getDBO(); 
			  $db->setQuery($q); 
			  $shipping = $db->loadAssoc(); 
			  $e = $db->getErrorMsg(); if (!empty($e)) { die($e); }
			  $q = "select * from `#__extensions` as e where `folder` LIKE 'vm%' and `element` NOT IN ('opc_shipping_last', 'opctracking') order by ordering desc limit 0,1"; 
			  $db->setQuery($q); 
			  $max = $db->loadResult(); 
			  $e = $db->getErrorMsg(); if (!empty($e)) { die($e); }
			  $max = $maxorig = (int)$max; 
			  $max = $max + 10; 
			  if (empty($shipping))
			  {
				  $q = 'INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES (NULL, \'OPC vmshipment and vmpayment - Disable autoselect shipping and payment for VM3.0.6 and above (last p\', \'plugin\', \'opc_shipping_last\', \'vmpayment\', 0, 1, 1, 0, \'{"legacy":false,"name":"OPC vmshipment and vmpayment - Disable autoselect shipping and payment for VM3.0.6 and above (last plugin)","type":"plugin","creationDate":"December 2011","author":"RuposTel s.r.o.","copyright":"RuposTel s.r.o.","authorEmail":"admin@rupostel.com","authorUrl":"www.rupostel.com","version":"3.0.6","description":"One Page Checkout for VirtueMart 2 - This plugins disables auto selected shipping method by VM3.0.6 and above","group":""}\', \'{}\', \'\', \'\', 0, \'0000-00-00 00:00:00\', '.$max.', 0) '; 
				  $db->setQuery($q); 
				  $db->query(); 
				  $e = $db->getErrorMsg(); if (!empty($e)) { die($e); }
			  }
			  else
			  {
				 
				
				  if ($shipping['ordering'] <= $maxorig)
				  {
				   $q = 'update `#__extensions` set ordering = '.(int)$max .', `state` = 0 where element = "opc_shipping_last" '; 
				   
				   $db->setQuery($q); 
				   $z = $db->query(); 
				   
				   $e = $db->getErrorMsg(); if (!empty($e)) { die($e); }
				   
				   $msg .= 'OPC vmshipment and vmpayment plugin ordering was corrected <br />'; 
				   
				  }
				  // always enable this plugin on VM3: 
				  if (defined('VM_VERSION') && (VM_VERSION >=3))
				  {
				   $q = 'update `#__extensions` set `enabled` = 1, `state` = 0 where element = "opc_shipping_last" '; 
				   
				   $db->setQuery($q); 
				   $z = $db->query(); 
				  }
				  
			  }
			  
			  
		  }
		  
		  
		  
		  $msg = '';  
		  // always copy !!!
		   $db = JFactory::getDBO(); 
		  try
		  {
			    $db = JFactory::getDBO(); 
				$q = "delete from `#__extensions` where `element` = 'opc_shipping_last' and `folder` = 'system'"; 
				$db->setQuery($q); 
				$db->query(); 
			    
				$this->copyPlugin('vmpayment', 'opc_shipping_last'); 
				
				/*
			 
		  if (JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opc_shipping_last'.DIRECTORY_SEPARATOR.'opc_shipping_last.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opc_shipping_last'.DIRECTORY_SEPARATOR.'opc_shipping_last.php')===false)
		  {
			  $msg .= 'Cannot copy opc_shipping_last plugin to vmpayment directory. If the plugin is already at it\'s place, ignore this message<br />'; 
			  $msgs = true; 
		  }
		  
		  if (JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opc_shipping_last'.DIRECTORY_SEPARATOR.'opc_shipping_last.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opc_shipping_last'.DIRECTORY_SEPARATOR.'opc_shipping_last.xml')===false)
		  {
			  if (empty($msgs))
			  $msg .= 'Cannot copy opc_shipping_last plugin to vmpayment directory. If the plugin is already at it\'s place, ignore this message<br />'; 
		  }
		  
		  
		  
		  JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opc_shipping_last'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opc_shipping_last'.DIRECTORY_SEPARATOR.'index.html');
		    
		    */
		  }
		  catch (Exception $e)
		  {
			   $msg .= 'Cannot copy opc_shipping_last plugin to vmpayment directory. If the plugin is already at it\'s place, ignore this message<br />'; 
		  }
		  
		 
		}
		function loadVmConfig()
		{
		  if (!class_exists('VmConfig'))
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		 
		 
		  VmConfig::loadConfig(true); 
		  
		  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
		  
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
		  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			OPCmini::setVMLANG(); 
		  OPCLang::loadLang(); 
		  
		  
		}
		function listShopperGroups()
		{
		  $db = JFactory::getDBO(); 
		  $q = 'select * from #__virtuemart_shoppergroups where published = 1'; 
		  $db->setQuery($q); 
		  return $db->loadAssocList(); 
		  
		}
		

		
		function getVendorCurrency() {
			$db = JFactory::getDBO(); 
		$q = 'select vendor_currency from #__virtuemart_vendors where virtuemart_vendor_id = 1'; 
		$db->setQuery($q); 
		$cid = (int)$db->loadResult(); 
		if (empty($cid)) {
		 $q = 'select vendor_currency from #__virtuemart_vendors where 1'; 
		 $db->setQuery($q); 
		 $cid = (int)$db->loadResult(); 
		}
		return (int)$cid; 
		}
		
		
		function renameTheme()
		{
		
		   $from = JRequest::getVar('orig_selected_template');   
		   $to = JRequest::getVar('selected_template');   
		   
		   if(!class_exists('JFile')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'file.php');
		  if(!class_exists('JFolder')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');
		  

		   
		   $path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR; 
		   if (!file_exists($path.$from)) 
		   {
		    JRequest::setVar('selected_template', JRequest::getVar('orig_selected_template'));   
		    return; 
		   }
		   $to = JFile::makeSafe($to); 
		   JRequest::setVar('selected_template', $to);   
		   
		   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		   
		   OPCconfig::copy('theme_config', $from, $to); 
		   
		   
		   
		   if (JFolder::copy($path.$from, $path.$to, '', true)===false)
		   return JText::sprintf('COM_ONEPAGE_CANNOT_CREATE_DIRECTORY', $path.$to); 
		 
		}
		function getFTypes() {
			$dispatcher = JDispatcher::getInstance(); 
				$ftypes = array(); 
				$dispatcher->trigger('opcGetFieldPaths', array( &$ftypes)); 
				if (empty($ftypes)) return array(); 
				
				$data = ''; 
				$fc = JPATH_CACHE.DIRECTORY_SEPARATOR.'opc_vm_userfields.txt'; 
				@file_put_contents($fc, $data); 
				foreach ($ftypes as $t) { 
			
		  $data = $t->header."\n"; 
		
		 if (!file_exists($fc)) {
		   @file_put_contents($fc, $data); 
		 }
		 else
		 {
			 @file_put_contents($fc, $data, FILE_APPEND); 
		 }
				}
				return $ftypes; 
		 
		}
		
		function storeTracking($data) {
		
		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		   $tosave = array(); 
		   foreach ($data as $key=>$val)
		    {
			  if (stripos($key, 'top_ostatus_')===0)
			   {
				   
				  
			      $n = str_replace('top_ostatus_', '', $key); 
				  $status = $val; 
				  $payment_id = $data['top_opayment_'.$n]; 
			
				  $mode = $data['top_omode_'.$n]; 
				  $lang = $data['top_olang_'.$n]; 
				  if (empty($payment_id)) $payment_id = ''; 
				  
				  if (empty($status)) continue; 
				  $ndata = array(); 
				  $ndata['order_status'] = $status; 
				
				  $ndata['payment_id'] = $payment_id; 
				  $ndata['article_id'] = '';
				  
				  $cl = strtolower(str_replace('-', '_', $lang)); 
				  $ndata['language'] = $lang; 
				  $ndata['mode'] = $mode; 
				  $ndata['tracking'] = $data['ttracking_'.$n]; 
				  
				  $tosave[] = $ndata; 
				  
			   }
			}
			
			require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'tracking.php'); 
			$modelT = new JModelTracking(); 
			$modelT->storeCustom($tosave); 
			//OPCconfig::store('ty_page', 'ty_page', 0, $tosave); 
		}
		
		function storeTY($data)
		{
		   
		   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		   $tosave = array(); 
		   foreach ($data as $key=>$val)
		    {
			  if (stripos($key, 'op_ostatus_')===0)
			   {
			      $n = str_replace('op_ostatus_', '', $key); 
				  $status = $val; 
				  $payment_id = $data['op_opayment_'.$n]; 
				  $article_id = $data['op_oarticle_'.$n]; 
				  $mode = $data['op_omode_'.$n]; 
				  $lang = $data['op_olang_'.$n]; 
				  if (empty($payment_id)) continue; 
				  if (empty($article_id)) continue; 
				  if (empty($status)) continue; 
				  $ndata = array(); 
				  $ndata['order_status'] = $status; 
				
				  $ndata['payment_id'] = $payment_id; 
				  $ndata['article_id'] = $article_id; 
				  $cl = strtolower(str_replace('-', '_', $lang)); 
				  $ndata['language'] = $lang; 
				  $ndata['mode'] = $mode; 
				  
				  $tosave[] = $ndata; 
				  
			   }
			}
			
			
			
			OPCconfig::store('ty_page', 'ty_page', 0, $tosave); 
		}
		
		private function storeVMuserfields($data) {
		  
		  
		  $dispatcher = JDispatcher::getInstance(); 
		  $dispatcher->trigger('opcStoreFieldConfig', array( $data)); 
		  
		  
		}
		
		function store($data = null)
		{
		
	    require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'language.php'); 
	    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
	    // load basic stuff:
	    OPCLang::loadLang(); 
		
		$this->removeCache(); 		
		
		$this->loadVmConfig(); 
		$user = JFactory::getUser();

		$this->storeRegistration(); 
		

		
		$opc_load_jquery = JRequest::getVar('opc_load_jquery', false); 
		if (!empty($opc_load_jquery)) $opc_load_jquery = true; 
		
		OPCConfig::store('opc_load_jquery', '', 0, $opc_load_jquery); 

		  if(!class_exists('JFile')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'file.php');
		  if(!class_exists('JFolder')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');
		  
		

	        $msg = '';
			
		$rename = JRequest::getVar('rename_to_custom', false); 
		if ($rename)
		$msg .= $this->renameTheme(); 

		 $db = JFactory::getDBO();
		 
		 if (empty($data))
		 $data = JRequest::get('post');
		
		$this->storeTY($data); 
		$this->storeTracking($data); 
		
		 $cfg = urldecode('%3C%3Fphp').'
if( !defined( \'_VALID_MOS\' ) && !defined( \'_JEXEC\' ) ) die( \'Direct Access to \'.basename(__FILE__).\' is not allowed.\' );
/*
*      One Page Checkout configuration file
*      Copyright RuposTel s.r.o. under GPL license
*      Version 2 of date 31.March 2012
*      Feel free to modify this file according to your needs
*
*
*     @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
*     @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*     One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
*     VirtueMart is free software. This version may have been modified pursuant
*     to the GNU General Public License, and as distributed it includes or
*     is derivative of works licensed under the GNU General Public License or
*     other free or open source software licenses.
* 
*/




';

$cfg .= '
		  if (!class_exists(\'VmConfig\'))
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.\'components\'.DIRECTORY_SEPARATOR.\'com_virtuemart\'.DIRECTORY_SEPARATOR.\'helpers\'.DIRECTORY_SEPARATOR.\'config.php\'); 
		  VmConfig::loadConfig(); 

';

	if (!empty($data['delete_ht']))
	{
	   if (JFile::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'.htaccess')===false)
	     {
		   $msg .= JText::sprintf('COM_VIRTUEMART_STRING_DELETED_ERROR', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'.htaccess'); 
		 }
	}
	
	$this->storeVMuserfields($data); 
	
	if (!empty($data['always_zero_tax']))
	{
		$cfg .= ' $always_zero_tax = true; '; 
	}
	
	if (!empty($data['opc_disable_customer_email']))
	{
		$cfg .= ' $opc_disable_customer_email = true; '; 
		
		 
	}
	
	
	
	if (!empty($data['opc_tax_name_display']))
	{
		$cfg .= ' $opc_tax_name_display = true; '; 
	}
	
	if (!empty($data['awo_fix']))
	{
		$cfg .= ' $awo_fix = true; '; 
	}
	
	
	if (!empty($data['opc_confirm_dialog']))
	{
		$cfg .= ' $opc_confirm_dialog = true; '; 
	}
	
	if (!empty($data['opc_switch_rd']))
	{
		$cfg .= ' $opc_switch_rd = true; '; 
	}
	if (!empty($data['opc_btrd_def']))
	{
		$cfg .= ' $opc_btrd_def = true; '; 
	}
	
	//opc_copy_bt_st
	if (!empty($data['opc_copy_bt_st']))
	{
		$cfg .= ' $opc_copy_bt_st = true; '; 
	}
	
	
	if (!empty($data['vendor_emails'])) {
		$cfg .= ' $vendor_emails = \''.addslashes(htmlentities($data['vendor_emails'])).'\'; '; ; 
	}
	
	if (!empty($data['opc_disable_customer_email_address'])) {
	  	$cfg .= ' $opc_disable_customer_email_address = \''.addslashes(htmlentities($data['opc_disable_customer_email_address'])).'\'; '; ; 
	}
	
	if (!empty($data['opc_no_cart_p_links']))
	{
		$cfg .= ' $opc_no_cart_p_links = true; '; 
	}
	
	if (!empty($data['opc_url_addtocart']))
	{
		$cfg .= ' $opc_url_addtocart = true; '; 
	}
	
	if (!empty($data['opc_no_joomla_notices']))
	{
		$cfg .= ' $opc_no_joomla_notices = true; '; 
	}
	
	if (!empty($data['opc_recalc_js']))
	{
		$cfg .= ' $opc_recalc_js = true; '; 
	}
	
	//opc_vat_field
	if (!empty($data['opc_vat_field']))
	{
		$cfg .= ' $opc_vat_field = \''.addslashes($data['opc_vat_field']).'\'; '; 
	}
	//opc_euvat_allow_invalid
	if (!empty($data['opc_euvat_allow_invalid']))
	{
		$cfg .= ' $opc_euvat_allow_invalid = true; '; 
	}
	
	if (!empty($data['one_or_the_other'])) {
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		OPCUserFields::$cacheDisabled = true; 
		
	  $cfg .= ' $one_or_the_other = array(); $one_or_the_other2 = array(); '; 
	  foreach ($data['one_or_the_other'] as $k=>$v)
	  {
		  
		  
		  $f1 = $v; 
		  
		  
		  if (empty($f1)) continue; 
		  
		  $ign = array('name', 'tos', 'username', 'password', 'password2', 'email', 'email2', 'agreed', 'register_account'); 
		  if (in_array($f1, $ign)) continue; 
		  
		  if (empty($data['one_or_the_other2'][$k])) continue; 
		  $f2 = $data['one_or_the_other2'][$k]; 
		  
		  if (in_array($f2, $ign)) continue; 
	
		  $cfg .= ' $one_or_the_other['.$k.'] = \''.addslashes($f1).'\'; '; 
		  $cfg .= ' $one_or_the_other2['.$k.'] = \''.addslashes($f2).'\'; '; 
		  
		$r = OPCUserFields::getIfRequired($f1); 
		if (!empty($r)) {
		   OPCUserFields::setNotRequired($f1); 
		   $msg .= JText::_('COM_ONEPAGE_SET_NOT_REQUIRED').': '.$f1."<br />"; 
		}
		$r = OPCUserFields::getIfRequired($f2); 
		if (!empty($r)) {
		   OPCUserFields::setNotRequired($f2); 
		   $msg .= JText::_('COM_ONEPAGE_SET_NOT_REQUIRED').': '.$f2."<br />"; 
		}
		  
		  
	  }
	
	}
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	if (!empty($data['disable_shipto_per_shipping'])) {
		$str = $data['disable_shipto_per_shipping']; 
		$ret = OPCmini::parseCommas($str, true); 
		$cfg .= ' $disable_shipto_per_shipping = \''.implode(',',$ret).'\'; '; 
	}
	
	if (!empty($data['opc_enable_shipipng_estimator']))
	{
		$data['opc_enable_shipipng_estimator'] = (int)$data['opc_enable_shipipng_estimator']; 
		
		$cfg .= ' $opc_enable_shipipng_estimator = '.$data['opc_enable_shipipng_estimator'].'; '; 
	}
	
	if (!empty($data['opc_estimator_step']))
	{
		$cfg .= ' $opc_estimator_step = true; '; 
	}
	
	if (!empty($data['op_dontrefresh_shipping']))
	{
		$cfg .= ' $op_dontrefresh_shipping = true; '; 
	}
	
	
	if (!empty($data['opc_debug_plugins']))
	{
		$cfg .= ' $opc_debug_plugins = true; '; 
	}
	
	if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
	 if (!empty($data['disable_vm_cart_reload']))
	 {
		 $cfg .= ' $disable_vm_cart_reload = true; '; 
		 $msg .= $this->alterCartsTable(true); 
	 }
	 else
	 {
		 $msg .= $this->alterCartsTable(false); 
	 }
	}
	
	if (!empty($data['override_payment_currency']))
	{
		$cfg .= ' $override_payment_currency = true; '; 
	}
	
	if (!empty($data['delivery_selector']))
	{
		$cfg .= ' $delivery_selector = \''.addslashes($data['delivery_selector']).'\'; '; 
		
		if (empty($data['custom_rendering_fields'])) $data['custom_rendering_fields'] = array(); 
		
		$data['custom_rendering_fields'][] = $data['delivery_selector']; 
	}
	
	if ((!empty($data['business_selector'])) && ((empty($data['business_fields2'])))) {
		$msg .= JText::_('COM_ONEPAGE_CONFIG_ERROR_SELECTOR')."<br />\n"; 
	}
	
	if ((!empty($data['business_selector']))) {
	  	 $cfg .= ' $business_selector = \''.addslashes($data['business_selector']).'\'; '; 
	}
	
	if (!empty($data['do_not_display_business'])) {
	  	 $cfg .= ' $do_not_display_business = true; '; 
	}
	
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
	
	OPCUserFields::$cacheDisabled = true; 
	
	if (!empty($data['business_fields']))
	foreach ($data['business_fields'] as $bfn) {
		
		$r = OPCUserFields::getIfRequired($bfn); 
		if (!empty($r)) {
		   $data['business_obligatory_fields'][$bfn] = $bfn; 
		   OPCUserFields::setNotRequired($bfn); 
		   $msg .= JText::_('COM_ONEPAGE_SET_NOT_REQUIRED').': '.$bfn."<br />"; 
		}
		
		
	}
	if (!empty($data['business_fields2']))
	foreach ($data['business_fields2'] as $bfn) {
		$r = OPCUserFields::getIfRequired($bfn); 
		if (!empty($r)) {
		   $data['business_obligatory_fields'][$bfn] = $bfn; 
		   OPCUserFields::setNotRequired($bfn); 
		   $msg .= JText::_('COM_ONEPAGE_SET_NOT_REQUIRED').': '.$bfn."<br />"; 
		}
	}
	
	
	if ((!empty($data['business_selector'])) && ((!empty($data['business_fields2']))))
	{
	
		 if (!is_array($data['business_fields2']))
		 {
			 $data['business_fields2'] = array($data['business_fields2']); 
			 
			 
		 }
		 if (!empty($data['is_business2']))
		 {
			 if (!isset($data['business_fields'])) $data['business_fields'] = array(); 
			 
			 $data['business_fields'] = array_merge($data['business_fields'], $data['business_fields2']); 
			 
			 
			 
			 $cfg .= ' $is_business2 = true; '; 
			 
		 }
		 
		 if (!empty($data['business_fields2']))
		 {
			 $im = array(); 
			 foreach ($data['business_fields2'] as $k=>$v)
			 {
				 $im[] = "'".addslashes($v)."'"; 
			 }
			 {
				 
				 $im = implode(',', $im); 
				 $cfg .= ' $business_fields2 = array('.$im.'); '; 
			 }
			 
			 if (!empty($data['business2_value']))
			 {
				  $cfg .= ' $business2_value = \''.addslashes($data['business2_value']).'\'; '; 
			 }
			 
		 }
		 
	}
	
	
	 if (!empty($data['estimator_fields']))
		 {
			 $im = array(); 
			 foreach ($data['estimator_fields'] as $k=>$v)
			 {
				 $im[] = "'".addslashes($v)."'"; 
			 }
			 if (!empty($im)) {
				 
				 $im = implode(',', $im); 
				 $cfg .= ' $estimator_fields = array('.$im.'); '; 
			 }
			 
			
			 
		 }
	
	
	if (!empty($data['checkbox_products']))
		 {
			 $ra = explode(',', $data['checkbox_products']); 
			 
			 $im = array(); 
			 foreach ($ra as $k=>$v)
			 {
				 
				 $v = trim($v); 
				 if (!is_numeric($v)) continue; 
				 $im[] = "'".addslashes($v)."'"; 
			 }
			 
				 
				 $im = implode(',', $im); 
				 $cfg .= ' $checkbox_products = array('.$im.'); '; 
			 
			 
			 if ((!empty($data['checkbox_products_display'])) && ($data['checkbox_products_display'] === '1'))
			 {
				  $cfg .= ' $checkbox_products_display = true; '; //legacy... 
			 }
			 else 
			 if ((!empty($data['checkbox_products_display'])) && ($data['checkbox_products_display'] === '2')) {
			    $cfg .= ' $checkbox_products_display = 2; '; //legacy... 
			 }
			 
			 if (!empty($data['checkbox_products_position']))
			 {
				  $cfg .= ' $checkbox_products_position = \''.addslashes($data['checkbox_products_position']).'\'; '; 
			 }
			 
			 if (!empty($data['opc_estimator_position']))
			 {
				  $cfg .= ' $opc_estimator_position = \''.addslashes($data['opc_estimator_position']).'\'; '; 
			 }
			 
			 if (!empty($data['checkbox_products_displaytype'])) $cfg .= ' $checkbox_products_displaytype = true; '; 
				
		     if (!isset($data['checkbox_products_first'])) $data['checkbox_products_first'] = 'COM_VIRTUEMART_LIST_EMPTY_OPTION'; 
			 $cfg .= ' $checkbox_products_first = \''.addslashes($data['checkbox_products_first']).'\'; '; 
			 //checkbox_products_position
			 $fl = $data['checkbox_order_start']; 
			 $fl = floatval($fl); 
			 if (!empty($fl)) {
				 $cfg .= ' $checkbox_order_start = \''.addslashes($data['checkbox_order_start']).'\'; '; 
			 }
			 //checkbox_order_start
			 
		 }
	
	
	if (!empty($data['default_acy_checked'])) $cfg .= ' $default_acy_checked = true; '; 
	if (!empty($data['default_italian_checked'])) $cfg .= ' $default_italian_checked = true; '; 
	
	if (!empty($data['op_color_codes_enabled']))
	{
		$cfg .= ' $op_color_codes_enabled = array(); '; 
		if (!empty($data['op_color_codes_enabled'][0]))
		$cfg .= ' $op_color_codes_enabled[0] = "'.str_replace('"', '\"', $data['op_color_codes_enabled'][0]).'"; '; 
	    if (!empty($data['op_color_codes_enabled'][1]))
		$cfg .= ' $op_color_codes_enabled[1] = "'.str_replace('"', '\"', $data['op_color_codes_enabled'][1]).'"; '; 
	    if (!empty($data['op_color_codes_enabled'][2]))
		$cfg .= ' $op_color_codes_enabled[2] = "'.str_replace('"', '\"', $data['op_color_codes_enabled'][2]).'"; '; 
	
	 if (!empty($data['op_color_codes_enabled'][3]))
		$cfg .= ' $op_color_codes_enabled[3] = "'.str_replace('"', '\"', $data['op_color_codes_enabled'][3]).'"; ';
	}
	
	//op_color_texts
	if (!empty($data['op_color_texts']))
	{
		$cfg .= ' $op_color_texts = array(); '; 
		if (!empty($data['op_color_texts'][0]))
		$cfg .= ' $op_color_texts[0] = "'.str_replace('"', '\"', $data['op_color_texts'][0]).'"; '; 
	    if (!empty($data['op_color_texts'][1]))
		$cfg .= ' $op_color_texts[1] = "'.str_replace('"', '\"', $data['op_color_texts'][1]).'"; '; 
	    if (!empty($data['op_color_texts'][2]))
		$cfg .= ' $op_color_texts[2] = "'.str_replace('"', '\"', $data['op_color_texts'][2]).'"; '; 
	
	 if (!empty($data['op_color_texts'][3]))
		$cfg .= ' $op_color_texts[3] = "'.str_replace('"', '\"', $data['op_color_texts'][3]).'"; ';
	}
	
	
	
	if (!empty($data['opc_stock_handling']))
		$cfg .= ' $opc_stock_handling = '.(int)$data['opc_stock_handling'].'; '; 
		
		
		if (!empty($data['opc_stock_zero_weight']))
		$cfg .= ' $opc_stock_zero_weight = '.(int)$data['opc_stock_zero_weight'].'; '; 
	
	if (!empty($data['op_colorfy_products']))
		$cfg .= ' $op_colorfy_products = '.(bool)$data['op_colorfy_products'].'; '; 
	
	if (!empty($data['op_ignore_ordered_products']))
		$cfg .= ' $op_ignore_ordered_products = '.(bool)$data['op_ignore_ordered_products'].'; '; 
	
	
	
	if (!empty($data['op_color_codes']))
	{
		$cfg .= ' $op_color_codes = array(); '; 
		if (!empty($data['op_color_codes'][0]))
		$cfg .= ' $op_color_codes[0] = "'.str_replace('"', '\"', $data['op_color_codes'][0]).'"; '; 
	    if (!empty($data['op_color_codes'][1]))
		$cfg .= ' $op_color_codes[1] = "'.str_replace('"', '\"', $data['op_color_codes'][1]).'"; '; 
	    if (!empty($data['op_color_codes'][2]))
		$cfg .= ' $op_color_codes[2] = "'.str_replace('"', '\"', $data['op_color_codes'][2]).'"; '; 
		if (!empty($data['op_color_codes'][3]))
		$cfg .= ' $op_color_codes[3] = "'.str_replace('"', '\"', $data['op_color_codes'][3]).'"; '; 
	}
	//op_colorfy_products, op_ignore_ordered_products,op_color_codes
	
	if (!empty($data['opc_cr_type']))
		$cfg .= ' $opc_cr_type = \''.$data['opc_cr_type'].'\'; '; 

	if (!empty($data['product_id_ty'])) {
		$cfg .= ' $product_id_ty = true; '; 
	}
	
	$data['do_not_show_opcregistration'] = (int)$data['do_not_show_opcregistration']; 
	if (!empty($data['do_not_show_opcregistration']))
		$cfg .= ' $do_not_show_opcregistration = 1; '; 
	
	if (isset($data['bt_fields_from']))
		$cfg .= ' $bt_fields_from = \''.$data['bt_fields_from'].'\'; '; 
	
	
	
	$en = false; 
	if (!empty($data['order_numbering']))
	{
		$en = true; 
		$cfg .= ' $order_numbering = '.(int)$data['order_numbering'].'; '; 
		
	}
	
	
	
	if (!empty($data['invoice_numbering']))
	{
		$en = true; 
		$cfg .= ' $invoice_numbering = '.(int)$data['invoice_numbering'].'; '; 
		
	}
	if ($en)
	$msg .= $this->checkNumberingPlugin($en); 
	
	
	if (isset($data['op_default_shipping_search']))
	{
	   $cfg .= ' $op_default_shipping_search = array(); '; 
	   if (is_array($data['op_default_shipping_search']))
	   {
	     $i = 0; 
	     foreach ($data['op_default_shipping_search'] as $key=>$val)
		  {
		     if (empty($val)) continue; 
		     $cfg .= ' $op_default_shipping_search['.$i.'] = "'.str_replace('"', '\"', $val).'"; ';    
			 $i++; 
		  }
	   }
	   else
	   {
		     $cfg .= ' $op_default_shipping_search[0] = "'.str_replace('"', '\"', $val).'"; ';    
	      
	   }
	}
	
	if (!empty($data['home_vat_countries']))
	 {
	    $home = str_replace('"', '', $data['home_vat_countries']); 
		$cfg .= ' $home_vat_countries = "'.$home.'"; ';    
	 }
	 
	 
	 if (!empty($data['home_vat_num']))
	 {
	    $home = str_replace('"', '', $data['home_vat_num']); 
		$cfg .= ' $home_vat_num = "'.$home.'"; ';    
	 }
	
	
	 if (isset($data['opc_payment_refresh']))
    $cfg .= '$opc_payment_refresh = true;
    ';
	
	 if (isset($data['use_original_basket']))
    $cfg .= '$use_original_basket = true;
    ';
    else $cfg .= '$use_original_basket = false; 
    ';
	//opc_php_js
	//theme_fix1
	if (isset($data['theme_fix1']))
    $cfg .= '$theme_fix1 = true;
    ';
    else $cfg .= '$theme_fix1 = false; 
    ';

	if (isset($data['email_fix1']))
    $cfg .= ' $email_fix1 = true; '; 

    if (isset($data['order_reuse_fix'])) { 
	  $cfg .= ' $order_reuse_fix = true; '; 
	  $msg .= $this->fixOrderReuse(); 
	}
	

	if (isset($data['email_fix2']))
    $cfg .= ' $email_fix2 = true; '; 

	
	if (isset($data['opc_override_registration']))
	{
	$cfg .= '$opc_override_registration = true; 
    ';
	$this->enableOpcRegistration(true); 
	}
	else
	{
	$this->enableOpcRegistration(false); 
	}
	
	if (isset($data['opc_override_registration_logged']))
	{
	$cfg .= '$opc_override_registration_logged = true; ';
	}
	
	require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
if (isset($data['opc_euvat'])) {
  $cfg .= '$opc_euvat = true; ';
}	

if (isset($data['opc_euvat'])) 	
if (!empty($data['opc_vat_field']))	
if (!OPCUserFields::fieldExists($data['opc_vat_field'])) 	
{
    

$coref = array();  
 $ulist = $this->getUserFieldsLists($coref); 
 
 $found = false; 
 $published = 1; 
 $datau = array(); 
  $datau2 = array(); 
 foreach ($ulist as $key=>$val)
  {
     if ($val->name == $data['opc_vat_field'])
	 {
	 $published = $val->published; 
	 $found = true;
	 if (empty($published))
	  foreach ($val as $key2=>$v)
	   {
	     $datau[$key2] = $v; 
	   }
	 }
	 
	 if ($val->name == 'opc_vat_info')
	 {
	 $published2 = $val->published; 
	 $found = true;
	
	 if (empty($published2))
	  foreach ($val as $key2=>$v)
	   {
	     $datau2[$key2] = $v; 
	   }
	 }
	 
	
	 
  }	
 
 
  if ((!$found) || (empty($published) || (empty($published2))))
   {
	   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');    
	   $modelu = OPCmini::getModel('userfields'); 
	   
      //$modelu = new VirtueMartModelUserfields();
	  if (empty($datau))
	   {
	   
		 
		 $datau = array (
  'virtuemart_userfield_id' => '0',
  'virtuemart_vendor_id' => '0',
  'userfield_jplugin_id' => '0',
  'name' => $data['opc_vat_field'],
  'title' => 'COM_ONEPAGE_EUVAT_FIELD',
  'description' => '',
  'type' => 'text',
  'maxlength' => '25',
  'size' => '30',
  'required' => '0',
  'cols' => '0',
  'rows' => '0',
  'value' => '',
  'default' => NULL,
  'registration' => '1',
  'shipment' => '0',
  'account' => '1',
  'readonly' => '0',
  'calculated' => '0',
  'sys' => '0',
  'params' => '',
  'ordering' => '99',
  'shared' => '0',
  'published' => '1',
  'created_on' => '0000-00-00 00:00:00',
  'created_by' => '0',
  'modified_on' => '0000-00-00 00:00:00',
  'modified_by' => '0',
  'locked_on' => '0000-00-00 00:00:00',
  'locked_by' => '0', );
  }
   else
	   {
	     $datau['published'] = 1; 
	   }
  if (empty($datau2)) {
  $datau2 = array (
  'virtuemart_userfield_id' => '0',
  'virtuemart_vendor_id' => '0',
  'userfield_jplugin_id' => '0',
  'name' => 'opc_vat_info',
  'title' => 'COM_ONEPAGE_EUVAT_FIELD_INFO',
  'description' => '',
  'type' => 'hidden',
  'maxlength' => '1000',
  'size' => NULL,
  'required' => '0',
  'cols' => '0',
  'rows' => '0',
  'value' => '',
  'default' => NULL,
  'registration' => '1',
  'shipment' => '0',
  'account' => '1',
  'readonly' => '0',
  'calculated' => '0',
  'sys' => '0',
  'params' => '',
  'ordering' => '99',
  'shared' => '0',
  'published' => '1',
  'created_on' => '0000-00-00 00:00:00',
  'created_by' => '0',
  'modified_on' => '0000-00-00 00:00:00',
  'modified_by' => '0',
  'locked_on' => '0000-00-00 00:00:00',
  'locked_by' => '0',
  
);
		 
		 
		 
	   }
	   else
	   {
	     $datau2['published'] = 1; 
	   }
	   
	    if (empty($datau['vNames'])) $datau['vNames'] = array(); 
		if (empty($datau['vValues'])) $datau['vValues'] = array(); 
		if (empty($datau2['vNames'])) $datau2['vNames'] = array(); 
		if (empty($datau2['vValues'])) $datau2['vValues'] = array(); 
 
	   $modelu->store($datau); 
	   $modelu->store($datau2); 
   }
	
}
else
{
	if (!empty($data['opc_euvat'])) {
	if ($data['opc_vat_field'] === 'opc_vat') {
    $q = 'update #__virtuemart_userfields set published = 1 where name LIKE "opc_vat" or name LIKE "opc_vat_info"'; 
    $db = JFactory::getDBO(); 
    $db->setQuery($q); 
    $db->query(); 
	}
	else {
	if ($data['opc_vat_field'] === 'opc_vat') {
   $q = 'update #__virtuemart_userfields set published = 0 where name LIKE "opc_vat" or name LIKE "opc_vat_info"'; 
   $db = JFactory::getDBO(); 
   $db->setQuery($q); 
   $db->query(); 
	}
	}
	}
}
if (isset($data['opc_euvat_button']))
{
    $cfg .= '$opc_euvat_button = true;
    ';
 
 
}

if (isset($data['opc_euvat_contrymatch']))
{
    $cfg .= '$opc_euvat_contrymatch = true;
    ';
 
 
}


	//disable_check
	if (isset($data['opc_no_activation']))
    $cfg .= '$opc_no_activation = true;
    ';
    else $cfg .= '$opc_no_activation = false; 
    ';

	
	//disable_check
	if (isset($data['disable_check']))
    $cfg .= '$disable_check = true;
    ';
    else $cfg .= '$disable_check = false; 
    ';
	
		 if (isset($data['opc_php_js2']))
    $cfg .= '$opc_php_js2 = true;
    ';
    else $cfg .= '$opc_php_js2 = false; 
    ';

	
	//only_one_shipping_address_hidden
	 if (isset($data['op_shipto_opened_default']))
    $cfg .= '$op_shipto_opened_default = true;
    ';
    else $cfg .= '$op_shipto_opened_default = false; 
    ';
	
	
	//only_one_shipping_address_hidden
	 if (isset($data['only_one_shipping_address_hidden']))
    $cfg .= '$only_one_shipping_address_hidden = true;
    ';
    else $cfg .= '$only_one_shipping_address_hidden = false; 
    ';
	
		 if (isset($data['opc_only_parent_links']))
    $cfg .= '$opc_only_parent_links = true;
    ';
    else $cfg .= '$opc_only_parent_links = false; 
    ';
	
	
	//opc_show_weight
	if (isset($data['opc_show_weight']))
    $cfg .= '$opc_show_weight = true;
    ';
	
		if (isset($data['opc_show_sdesc']))
    $cfg .= '$opc_show_sdesc = true;
    ';

	
	if (isset($data['opc_dynamic_lines']))
    $cfg .= '$opc_dynamic_lines = true;
    ';
    else $cfg .= '$opc_dynamic_lines = false; 
    ';
	
	
	
		 if (isset($data['opc_editable_attributes']))
    $cfg .= '$opc_editable_attributes = true;
    ';
    else $cfg .= '$opc_editable_attributes = false; 
    ';
	
	
	 if (isset($data['op_customer_shipping']))
    $cfg .= '$op_customer_shipping = true;
    ';
    else $cfg .= '$op_customer_shipping = false; 
    ';

	//allow_sg_update
	 if (isset($data['allow_sg_update']))
    $cfg .= '$allow_sg_update = true;
    ';
    else $cfg .= '$allow_sg_update = false; 
    ';
	
	 if (isset($data['allow_sg_update_logged']))
    $cfg .= '$allow_sg_update_logged = true;
    ';
    else $cfg .= '$allow_sg_update_logged = false; 
    ';
	
	
	 if (isset($data['do_not_allow_gift_deletion']))
    $cfg .= '$do_not_allow_gift_deletion = true;
    ';
    else $cfg .= '$do_not_allow_gift_deletion = false; 
    ';
	
	$gift_order_statuses = JRequest::getVar('gift_order_statuses', array());
	if (empty($gift_order_statuses))
	{
	  $cfg .= ' $gift_order_statuses = array(); '; 
	}
	else
	 {
	    $str = var_export($gift_order_statuses, true); 
		$cfg .= "\n".' $gift_order_statuses = '.$str.';'."\n"; 
	 }
	 
	 
	 $reuse_order_statuses = JRequest::getVar('reuse_order_statuses', array());
	
	if (!empty($reuse_order_statuses))
	 {
			foreach ($reuse_order_statuses as $txt)
			  {
				  $txtA[] = "'".$db->escape($txt)."'"; 
			  }
			  
		$cfg .= "\n".' $reuse_order_statuses = array('.implode(',', $txtA).');'."\n"; 
		
	 }
	
	//opc_async 
	 if (isset($data['opc_async']))
    $cfg .= '$opc_async = true;
    ';
    else $cfg .= '$opc_async = false; 
    ';
	
	 if (isset($data['use_free_text']))
    $cfg .= '$use_free_text = true;
    ';
    else $cfg .= '$use_free_text = false; 
    ';
	
    if (isset($data['disable_op']))
    $cfg .= '$disable_onepage = true;
    ';
    else $cfg .= '$disable_onepage = false; 
    ';
	
	
    if (isset($data['opc_italian_checkbox']))
    $cfg .= '$opc_italian_checkbox = true;
    ';
    else $cfg .= '$opc_italian_checkbox = false; 
    ';
	
	if (isset($data['opc_acymailing_checkbox']))
    $cfg .= '$opc_acymailing_checkbox = true;
    ';
    else $cfg .= '$opc_acymailing_checkbox = false; 
    ';
	
	$data['opc_acy_id'] = (int)$data['opc_acy_id']; 
	$cfg .= ' $opc_acy_id = (int)"'.$data['opc_acy_id'].'"; '; 
	
	//opc_do_not_alter_registration
	if (isset($data['opc_do_not_alter_registration']))
    $cfg .= '$opc_do_not_alter_registration = true;
    ';
    else $cfg .= '$opc_do_not_alter_registration = false; 
    ';
	
	
	if (isset($data['opc_debug']))
    $cfg .= '$opc_debug = true;
    ';
    
	if (isset($data['opc_debug_theme']))
    $cfg .= '$opc_debug_theme = true;
    ';
	
	
	if (isset($data['opc_debug2']))
    $cfg .= '$opc_debug2 = true;
    ';
   
	
	if (!empty($data['blank_screens_email']))
	{
	   $cfg .= ' $blank_screens_email = \''.addslashes($data['blank_screens_email']).'\'; '; 
	}
	
	if (isset($data['opc_memory']))
    $cfg .= ' $opc_memory = \''.addslashes($data['opc_memory']).'\'; '; 
    
	if (isset($data['rupostel_email']))
	$cfg .= ' $rupostel_email = \''.addslashes($data['rupostel_email']).'\'; '; 
	
    if (isset($data['opc_plugin_order']) && is_numeric($data['opc_plugin_order']))
    $cfg .= '$opc_plugin_order = \''.$data['opc_plugin_order'].'\';
    ';
    else $cfg .= '$opc_plugin_order = -9999; 
    ';
	
	//
	if (isset($data['opc_disable_for_mobiles']))
    $cfg .= '$opc_disable_for_mobiles = true;
    ';
    else $cfg .= '$opc_disable_for_mobiles = false; 
    ';

	
	if (isset($data['opc_request_cache']))
    $cfg .= '$opc_request_cache = true;
    ';
    else $cfg .= '$opc_request_cache = false; 
    ';

	 if (isset($data['opc_check_username']))
      $cfg .= '$opc_check_username = true;';
      else $cfg .= '$opc_check_username = false;';

	 if (isset($data['opc_rtl']))
      $cfg .= '$opc_rtl = true;';
      else $cfg .= '$opc_rtl = false;';  
	  
	  
	  
	
	if (!empty($data['opc_no_duplicit_username']) && (empty($data['op_usernameisemail'])))
	{
    $cfg .= '$opc_no_duplicit_username = true;
    ';
	$cfg .= '$opc_check_username = true;';
	}
    else $cfg .= '$opc_no_duplicit_username = false; 
    ';

if (isset($data['klarna_se_get_address']))
      $cfg .= '$klarna_se_get_address = true;';
      else $cfg .= '$klarna_se_get_address = false;';


if (isset($data['ajaxify_cart']))
      $cfg .= '$ajaxify_cart = true;';
      else $cfg .= '$ajaxify_cart = false;';

	  
	  
if (isset($data['opc_check_email']))
      $cfg .= '$opc_check_email = true;';
      else $cfg .= '$opc_check_email = false;';

	
	if (!empty($data['opc_no_duplicit_email']))
	{
    $cfg .= '$opc_no_duplicit_email = true;
    ';
	$cfg .= '$opc_check_email = true;';
	$cfg .= '$allow_duplicit = false;';
	}
    else $cfg .= '$opc_no_duplicit_email = false; 
    ';

	
	
	//show_single_tax
    if (!empty($data['show_single_tax']))
    $cfg .= '$show_single_tax = true;
    ';
    else $cfg .= '$show_single_tax = false; 
    ';
	
	 if (!empty($data['opc_calc_cache']))
    $cfg .= '$opc_calc_cache = true;
    ';
    else $cfg .= '$opc_calc_cache = false; 
    ';
	
	
	  if (!empty($data['visitor_shopper_group']))
    $cfg .= '$visitor_shopper_group = "'.$data['visitor_shopper_group'].'";
    ';
    else $cfg .= '$visitor_shopper_group = 0; 
    ';
	
		  if (!empty($data['no_coupon_ajax']))
    $cfg .= '$no_coupon_ajax = true;
    ';
    else $cfg .= '$no_coupon_ajax = false; 
    ';

	if (!empty($data['coupon_tax_display'])) {
	  $cfg .= ' $coupon_tax_display = '.(int)$data['coupon_tax_display'].'; '; 
	  
	  
	}

	if (!empty($data['coupon_tax_display_id'])) {
	  $cfg .= ' $coupon_tax_display_id = '.(int)$data['coupon_tax_display_id'].'; '; 
	  
	  
	}
	
	
	  if (!empty($data['business_shopper_group']))
    $cfg .= '$business_shopper_group = "'.$data['business_shopper_group'].'";
    ';
    else $cfg .= '$business_shopper_group = 0; 
    ';

	  if (!empty($data['zero_total_status']))
    $cfg .= '$zero_total_status = "'.$data['zero_total_status'].'";
    ';
    else $cfg .= '$zero_total_status = "C"; 
    ';
	
	//op_never_log_in
//option_sgroup
	  if (!empty($data['option_sgroup']))
    $cfg .= '$option_sgroup = '.(int)$data['option_sgroup'].';;
    ';
    else $cfg .= '$option_sgroup = false; 
    ';


	if (!empty($data['opc_conference_mode'])) {
	   	 $cfg .= '$opc_conference_mode = true; ';
		 $cfg .= '$op_never_log_in = true; ';
		 $cfg .= '$allow_duplicit = true; ';
	}
	else {
	
	  if (!empty($data['op_never_log_in']))
    $cfg .= '$op_never_log_in = true;
    ';
    else $cfg .= '$op_never_log_in = false; 
    ';
	
	

if (isset($data['allow_duplicit']))
      $cfg .= '$allow_duplicit = true;
      ';
      else $cfg .= '$allow_duplicit = false;
      ';
	
	
	}
	//no_alerts
	if (!empty($data['no_alerts']))
    $cfg .= '$no_alerts = true;
    ';
    else $cfg .= '$no_alerts = false; 
    ';


	if (!empty($data['disable_ship_to_on_zero_weight']))
    $cfg .= '$disable_ship_to_on_zero_weight = true;
    ';
    else $cfg .= '$disable_ship_to_on_zero_weight = false; 
    ';
	
	//
	if (!empty($data['op_use_geolocator']))
    $cfg .= '$op_use_geolocator = true;
    ';
    else $cfg .= '$op_use_geolocator = false; 
    ';
	
	
	if (!empty($data['append_details']))
    $cfg .= '$append_details = true;
    ';
    else $cfg .= '$append_details = false; 
    ';
	
	
	if (!empty($data['cancel_page_url']))
    $cfg .= ' $cancel_page_url = \''.addslashes($data['cancel_page_url']).'\'; '; 
    
	
	if (!empty($data['op_redirect_joomla_to_vm']))
    $cfg .= '$op_redirect_joomla_to_vm = true;
    ';
    else $cfg .= '$op_redirect_joomla_to_vm = false; 
    ';
	
	
	
		 if (!empty($data['password_clear_text']))
    $cfg .= '$password_clear_text = true;
    ';
    else $cfg .= '$password_clear_text = false; 
    ';

	
	$cfg .= ' $dpps_search = array(); $dpps_disable = array(); $dpps_default=array(); '."\n";
	if (!empty($data['disable_payment_per_shipping']))
	 {
	   $search = JRequest::getVar('dpps_search'); 
	   $cfg .= ' $disable_payment_per_shipping = true; '; 
	   foreach ($search as $i=>$v)
	    {
		  if ((!empty($data['dpps_disable'][$i])) && (!empty($v)))
		  {
		  $val = urlencode($v);
		  $val = str_replace("'", "\\'", $val); 
	      $cfg .= ' $dpps_search['.$i.'] = '."'".$val."';\n"; 
		  $cfg .= ' $dpps_disable['.$i.'] = '."'".$data['dpps_disable'][$i]."';\n"; 
		  if ($data['dpps_default'][$i] != $data['dpps_disable'][$i])
		  $cfg .= ' $dpps_default['.$i.'] = '."'".$data['dpps_default'][$i]."';\n"; 
		  else $cfg .= ' $dpps_default['.$i.'] = \'\'; ';  
		  }
		}
	 }
	 else 
	 $cfg .= ' $disable_payment_per_shipping = false; '; 
	

		  if (!empty($data['euvat_shopper_group']))
    $cfg .= '$euvat_shopper_group = "'.$data['euvat_shopper_group'].'";
    ';
    else $cfg .= '$euvat_shopper_group = 0; 
    ';
	
	
		
  if (!empty($data['payment_discount_before']))
    $cfg .= '$payment_discount_before = true;
    ';
    else $cfg .= '$payment_discount_before = false; 
    ';
	
	
  if (!empty($data['only_one_shipping_address']))
    $cfg .= '$only_one_shipping_address = true;
    ';
    else $cfg .= '$only_one_shipping_address = false; 
    ';



	 if (isset($data['no_extra_product_info']))
    $cfg .= '$no_extra_product_info = true;
    ';
    else $cfg .= '$no_extra_product_info = false; 
    ';

	
    if (!empty($data['enable_captcha_unlogged']))
    $cfg .= '$enable_captcha_unlogged = true;
    ';
    
	if (!empty($data['enable_captcha_reg']))
    $cfg .= '$enable_captcha_reg = true;
    ';
	
	 if (isset($data['send_pending_mail']))
    $cfg .= '$send_pending_mail = true;
    ';
    else $cfg .= '$send_pending_mail = false; 
    ';
	
	 if (isset($data['enable_captcha_logged']))
    $cfg .= '$enable_captcha_logged = true;
    ';
    else $cfg .= '$enable_captcha_logged = false; 
    ';

	
	 if (isset($data['hide_advertise']))
    $cfg .= '$hide_advertise = true;
    ';
    else $cfg .= '$hide_advertise = false; 
    ';

	
	if (isset($data['force_zero_paymentmethod'])) {
		$cfg .= ' $force_zero_paymentmethod = true; '; 
	}
	
	 if (isset($data['hide_payment_if_one']))
    $cfg .= '$hide_payment_if_one = true;
    ';
    else $cfg .= '$hide_payment_if_one = false; 
    ';
	
	if (!empty($data['disable_op']))
	{
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "update `#__extensions` set `enabled` = 0 where `element` = 'opc' and type = 'plugin' limit 2 "; 
	   
	  }
	  else
	  {
	    $q = "update #__plugins set published = 0 where element = 'opc'  limit 2 "; 
	  }
	  $db = JFactory::getDBO(); 
	  $db->setQuery($q); 
	  $db->query(); 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) { $msg .= $e; }
	  
	 
	  
	  
	  
	}
	else
	{
		 $this->updateOPCShipping(); 
		 $this->alterVMConfig($data); 
		 $msg .= $this->installAwoView(); 
		 
		 $msg .= $this->installOtherFiles(); 
		 
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  {
	   $q = "update `#__extensions` set `enabled` = 1, `state` = 0 where `element` = 'opc' and `type` = 'plugin' and `folder` = 'system' limit 2 "; 
	   // disable other opc solutions: 
	   $q2 = "update `#__extensions` set `enabled` = 0 where `element` = 'onestepcheckout' and `type` = 'plugin' and `folder` = 'system' "; 
	   $q3 = "update `#__extensions` set `enabled` = 0 where `element` = 'vponepagecheckout' and `type` = 'plugin' and `folder` = 'system' "; 
	  }
	  else
	  {
	    $q = "update `#__plugins` set `published` = 1 where `element` = 'opc' and folder = 'system'  limit 2 "; 
		$q2 = "update `#__plugins` set `published` = 0 where `element` = 'onestepcheckout' and folder = 'system' "; 
		$q3 = "update `#__plugins` set `published` = 0 where `element` = 'vponepagecheckout' and folder = 'system' "; 
	  }
	  $db = JFactory::getDBO(); 
	  $db->setQuery($q); 
	  $db->query(); 
	  $e = $db->getErrorMsg(); 
	  if (!empty($e)) { $msg .= $e; }
	  
	  $db = JFactory::getDBO(); 
	  $db->setQuery($q2); 
	  $db->query(); 

	  $db = JFactory::getDBO(); 
	  $db->setQuery($q3); 
	  $db->query(); 

	  
	}
    
    
		 $cfg .= "
/* If user in Optional, normal, silent registration sets email which already exists and is registered 
* and you set this to true
* his order details will be saved but he will not be added to joomla registration and checkout can continue
* if registration type allows username and password which is already registered but his new password is not the same as in DB then checkout will return error
*/
";

if (isset($data['email_after']))
      $cfg .= '$email_after = true;
      ';
      else $cfg .= '$email_after = false;
      ';
	  
if (empty($data['opc_link_type']))
      $cfg .= '$opc_link_type = 0;
      ';
      else 
	   $cfg .= '$opc_link_type = '.$data['opc_link_type'].'; 
      ';





	  
	  if (!empty($data['business_fields']))
	  {
	    foreach ($data['business_fields'] as $k=>$line)
		 {
		   
		   
		   $data['business_fields'][$k] = "'".$line."'"; 
		 }
		 // special cases
		 if (in_array('password', $data['business_fields'])) $data['business_fields'][] = 'opc_password'; 
		 if (in_array('username', $data['business_fields'])) $data['business_fields'][] = 'opc_username'; 
		 
		 $newa = implode(',', $data['business_fields']); 
	    $cfg .= ' $business_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $business_fields = array(); '; 
	  
	  
	   if (!empty($data['custom_rendering_fields']))
	  {
	    foreach ($data['custom_rendering_fields'] as $k=>$line)
		 {
		   $data['custom_rendering_fields'][$k] = "'".$line."'"; 
		 }
		

		 $newa = implode(',', $data['custom_rendering_fields']); 
	    $cfg .= ' $custom_rendering_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $custom_rendering_fields = array(); '; 
	  
	  
	    if (!empty($data['per_order_rendering']))
	  {
	    foreach ($data['per_order_rendering'] as $k=>$line)
		 {
		   $data['per_order_rendering'][$k] = "'".$line."'"; 
		 }
		

		 $newa = implode(',', $data['per_order_rendering']); 
	    $cfg .= ' $per_order_rendering = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $per_order_rendering = array(); '; 
	   
	   
	   if (!empty($data['opc_ajax_fields']))
	  {
	    foreach ($data['opc_ajax_fields'] as $k=>$line)
		 {
		   $data['opc_ajax_fields'][$k] = "'".$line."'"; 
		 }
		

		 $newa = implode(',', $data['opc_ajax_fields']); 
	    $cfg .= ' $opc_ajax_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $opc_ajax_fields = array(); '; 
	   
	   
	   
	     if (!empty($data['admin_shopper_fields']))
	  {
	    foreach ($data['admin_shopper_fields'] as $k=>$line)
		 {
		   $data['admin_shopper_fields'][$k] = "'".$line."'"; 
		 }
		

		 $newa = implode(',', $data['admin_shopper_fields']); 
	    $cfg .= ' $admin_shopper_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $admin_shopper_fields = array(); '; 
	   
	   
	  if (!empty($data['render_as_hidden']))
	  {
	    foreach ($data['render_as_hidden'] as $k=>$line)
		 {
		   $data['render_as_hidden'][$k] = "'".$line."'"; 
		   $data['render_as_hidden']['shipto_'.$k] = "'shipto_".$line."'"; 
		 }
		

		 $newa = implode(',', $data['render_as_hidden']); 
	    $cfg .= ' $render_as_hidden = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $render_as_hidden = array(); '; 
	   
	  if (!empty($data['render_in_third_address']))
	  {
	    foreach ($data['render_in_third_address'] as $k=>$line)
		 {
		   $data['render_in_third_address'][$k] = "'".$line."'"; 
		   
		 }
		

		 $newa = implode(',', $data['render_in_third_address']); 
	    $cfg .= ' $render_in_third_address = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $render_in_third_address = array(); '; 

	  if (!empty($data['html5_fields'])) {
		  $cfg .= ' $html5_fields = array(); '; 
		  foreach ($data['html5_fields'] as $fname => $vtype) {
			  if (empty($vtype)) continue; 
			  $cfg .= ' $html5_fields[\''.$fname.'\'] = \''.str_replace("'", '', $vtype)."'; "; 
		  }
	  }
	  
	  if (!empty($data['html5_fields_extra'])) {
		  $cfg .= ' $html5_fields_extra = array(); '; 
		  foreach ($data['html5_fields_extra'] as $fname => $vtype) {
			  if (empty($vtype)) continue; 
			  $cfg .= ' $html5_fields_extra[\''.$fname.'\'] = \''.urlencode($vtype)."'; "; 
		  }
	  }
	  
	   if (!empty($data['html5_placeholder'])) {
		  $cfg .= ' $html5_placeholder = array(); '; 
		  
		  foreach ($data['html5_placeholder'] as $fname => $vtype) {
			  if (empty($vtype)) continue; 
			  $cfg .= ' $html5_placeholder[\''.$fname.'\'] = \''.urlencode($vtype)."'; "; 
		  }
	  }
	  
	   if (!empty($data['html5_validation_error'])) {
		  $cfg .= ' $html5_validation_error = array(); '; 
		  
		  foreach ($data['html5_validation_error'] as $fname => $vtype) {
			  if (empty($vtype)) continue; 
			  $cfg .= ' $html5_validation_error[\''.$fname.'\'] = \''.urlencode($vtype)."'; "; 
		  }
	  }
	 
	   
	  
	    if (!empty($data['shipping_obligatory_fields']))
	  {
	    foreach ($data['shipping_obligatory_fields'] as $k=>$line)
		 {
		   $data['shipping_obligatory_fields'][$k] = "'".$line."'"; 
		 }
		

		 $newa = implode(',', $data['shipping_obligatory_fields']); 
	    $cfg .= ' $shipping_obligatory_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $shipping_obligatory_fields = array(); '; 
	  
	  
	  if (!empty($data['business_obligatory_fields']))
	  {
	    foreach ($data['business_obligatory_fields'] as $k=>$line)
		 {
		   $data['business_obligatory_fields'][$k] = "'".$line."'"; 
		 }
		

		 $newa = implode(',', $data['business_obligatory_fields']); 
	    $cfg .= ' $business_obligatory_fields = array('.$newa.'); ';
		 
	  }
	  else $cfg .= ' $business_obligatory_fields = array(); '; 
	  
	  
if (!empty($data['op_disable_shipping']))
 $cfg .= '$op_disable_shipping = true;
      ';
      else $cfg .= '$op_disable_shipping = false;
      ';
 
if (!empty($data['op_disable_shipto']))
 $cfg .= '$op_disable_shipto = true;
      ';
      else $cfg .= '$op_disable_shipto = false;
      ';
 

 if (!empty($data['op_no_display_name']))
 $cfg .= '$op_no_display_name = true;
      ';
      else $cfg .= '$op_no_display_name = false;
      ';
if (!empty($data['op_create_account_unchecked']))
 $cfg .= '$op_create_account_unchecked = true;
      ';
      else $cfg .= '$op_create_account_unchecked = false;
      ';	  

/*	  
	  	  if (!empty($data['tos_itemid']))
	    $cfg .= ' $tos_itemid = "'.$data['tos_itemid'].'"; '; 
	*/
	

	  
if (!empty($data['product_price_display']))
{
  $cfg .= ' $product_price_display = "'.$data['product_price_display'].'";'."\n"; 
}

if (!empty($data['subtotal_price_display']))
{
  $cfg .= ' $subtotal_price_display = "'.$data['subtotal_price_display'].'";'."\n"; 
}

if (!empty($data['opc_usmode']))
{
  $cfg .= ' $opc_usmode = true; '; 
}
else
{
  $cfg .= ' $opc_usmode = false; '; 
}


if (!empty($data['full_tos_logged']))
{
  $cfg .= ' $full_tos_logged = true; '; 
}
else
{
  $cfg .= ' $full_tos_logged = false; '; 
}

if (!empty($data['tos_scrollable']))
{
  $cfg .= ' $tos_scrollable = true; '; 
}
else
{
  $cfg .= ' $tos_scrollable = false; '; 
}

$legal_info = VmConfig::get('oncheckout_show_legal_info', '1'); 
if ((!empty($data['full_tos_unlogged'])))
{
  $cfg .= ' $full_tos_unlogged = true; '; 
}
else
{
  $cfg .= ' $full_tos_unlogged = false; '; 
}

$tosx = VmConfig::get('agree_to_tos_onorder', '1');


if (!empty($data['tos_logged']))
{
  $cfg .= ' $tos_logged = true; '; 
}
else
{
  $cfg .= ' $tos_logged = false; '; 
}



if (!empty($data['tos_unlogged']))
{
  $cfg .= ' $tos_unlogged = true; '; 
}
else
{
  $cfg .= ' $tos_unlogged = false; '; 
}



if (!empty($data['opc_email_in_bt']))
{
  $cfg .= ' $opc_email_in_bt = true; '; 
}
else
{
  $cfg .= ' $opc_email_in_bt = false; '; 
}


if (!empty($data['double_email']))
{
  $cfg .= ' $double_email = true; '; 
}
else
{
  $cfg .= ' $double_email = false; '; 
}

if (!empty($data['coupon_price_display']))
{
  $cfg .= ' $coupon_price_display = "'.$data['coupon_price_display'].'";'."\n"; 
}

if (!empty($data['other_discount_display']))
{
  $cfg .= ' $other_discount_display = "'.$data['other_discount_display'].'";'."\n"; 
}

 
if (isset($data['agreed_notchecked']))
      $cfg .= '$agreed_notchecked = true;
      ';
      else $cfg .= '$agreed_notchecked = false;
      ';

if ((int)$data['opc_default_shipping']===1)
      $cfg .= '
	  $opc_default_shipping = 1; 
	  $op_default_shipping_zero = true;
	  $shipping_inside_choose = false; 
      ';
      else 
	  if ((int)$data['opc_default_shipping']===3)
	  {
	   $cfg .= ' $shipping_inside_choose = true; 
	    $opc_default_shipping = 3; 
	   ';
	  
	  }
	  else
	  $cfg .= '
	   $op_default_shipping_zero = false;
	   $opc_default_shipping = '.(int)$data['opc_default_shipping'].';
       $shipping_inside_choose = false;
	  ';
	  
if (!empty($data['never_count_tax_on_shipping']))
      $cfg .= '$never_count_tax_on_shipping = true;
      ';
      else $cfg .= '$never_count_tax_on_shipping = false;
      ';

if (!empty($data['save_shipping_with_tax']))
      $cfg .= '$save_shipping_with_tax = true;
      ';
      else $cfg .= '$save_shipping_with_tax = false;
      ';


	  
if (isset($data['op_no_basket']))
      $cfg .= '$op_no_basket = true;
      ';
      else $cfg .= '$op_no_basket = false;
      ';
	  

	 if (!empty($data['utm_payment']))
	 $utm_p = (array)$data['utm_payment']; 
	 else $utm_p = array(); 
	 
	 $utm_p2 = array(); 
	 if (!empty($utm_p))
	  {
	     foreach ($utm_p as $ku=>$vu)
		  {
		    $utm_p2[(int)$ku] = (int)$vu;  
		  }
	  }
	 //$utm_p = OPCConfig::getValue('opc_config', 'utm_payments', 0, $default, false, false);
	 OPCconfig::store('opc_config', 'utm_payments', 0, $utm_p2); 
	 
	 
	  if (!empty($data['opc_payment_isunder']))
	 $opc_payment_isunder = (array)$data['opc_payment_isunder']; 
	 else $opc_payment_isunder = array(); 
	 
	 $opc_payment_isunder2 = array(); 
	 if (!empty($opc_payment_isunder))
	  {
	     foreach ($opc_payment_isunder as $ku=>$vu)
		  {
		    $opc_payment_isunder2[(int)$ku] = (int)$vu;  
		  }
	  }
	 OPCconfig::store('opc_config', 'opc_payment_isunder', 0, $opc_payment_isunder2); 
	 
	 // email fix per payment methods: 
	 
	  if (!empty($data['email_fix3']))
	 $utm_p = (array)$data['email_fix3']; 
	 else $utm_p = array(); 
	 
	 $utm_p2 = array(); 
	 if (!empty($utm_p))
	  {
	     foreach ($utm_p as $ku=>$vu)
		  {
		    $utm_p2[(int)$ku] = (int)$vu;  
		  }
	  }
	 //$utm_p = OPCConfig::getValue('opc_config', 'utm_payments', 0, $default, false, false);
	 OPCconfig::store('opc_config', 'email_fix3', 0, $utm_p2); 
	 
	  
	  
if (isset($data['shipping_template']))
      $cfg .= '$shipping_template = true;
      ';
      else $cfg .= '$shipping_template = false;
      ';

	  
	  $opclang = JRequest::getVar('opc_lang_orig', ''); 
      require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');

	  $currency_config = JRequest::getVar('country_currency', array()); 
	  $aset = array(); 
	  OPCconfig::clearConfig('currency_config'); 
	  if (!empty($currency_config))
	  {
	    
	    foreach ($currency_config as $cid=>$arr)
		 {
		    if (!empty($arr))
		    foreach ($arr as $country_id)
			 {
			    if (!empty($aset[$country_id])) continue; 
			    $aset[$country_id] = $country_id; 
				$q = 'select country_2_code from #__virtuemart_countries where virtuemart_country_id = '.(int)$country_id.' limit 0,1'; 
				$db = JFactory::getDBO(); 
				$db->setQuery($q); 
				$c2c = $db->loadResult(); 
				if (!empty($c2c))
				OPCconfig::store('currency_config', $c2c, 0, (int)$cid); 
			 }
		 }
	     
		
	  }
	  
	  if (!empty($data['currency_plg_can_change']))
	  OPCconfig::store('currency_config', 'can_change', 0, true); 
	  else 
	  OPCconfig::store('currency_config', 'can_change', 0, false); 
	  
	  
	   $can_change = OPCconfig::getValueNoCache('currency_config', 'can_change', 0, -1); 

		
	  if (!empty($data['currency_plg']))
	  {
	  $this->setPluginEnabled('opc_currency', 'system', true); 
	  
	  $data['currency_plg'] = (int)$data['currency_plg']; 
	  if (($data['currency_plg'] === 1) || (($data['currency_plg'] === 2)))
	  {
		  $cfg .= ' $currency_switch = '.$data['currency_plg'].'; '; 
	  }
	  
	  }
	  else
	  $this->setPluginEnabled('opc_currency', 'system', false); 
	  
	  if (!empty($data['currency_per_lang']))
	  {
		  if (is_array($data['currency_per_lang']))
		  {
			  $cfg .= ' $currency_per_lang = array(); '; 
			  foreach ($data['currency_per_lang'] as $k=>$v)
			  {
				  if (!empty($v))
				  $cfg .= ' $currency_per_lang[\''.addslashes($k).'\'] = \''.addslashes($v).'\'; '; 
			  }
		  }
	  }
	  
	   OPCconfig::clearConfig('opc_config', 'op_articleid'.$opclang, 0);
	  
	  if (!empty($data['op_articleid'])) {
	  OPCconfig::store('opc_config', 'op_articleid'.$opclang, 0, $data['op_articleid']); 
	  }
	  
	  OPCconfig::clearConfig('opc_config', 'adc_op_articleid'.$opclang, 0);
	  if (!empty($data['adc_op_articleid'])) {
	  OPCconfig::store('opc_config', 'adc_op_articleid'.$opclang, 0, $data['adc_op_articleid']); 
	  }
	  
	  
	  
	  OPCconfig::clearConfig('opc_config', 'tos_itemid'.$opclang, 0);
	  if (!empty($data['tos_itemid'])) {
	  
	  OPCconfig::store('opc_config', 'tos_itemid'.$opclang, 0, $data['tos_itemid']); 
	  }
	  
	  	  OPCconfig::clearConfig('opc_config', 'newitemid'.$opclang, 0);
	  if (!empty($data['newitemid'])) {

	  OPCconfig::store('opc_config', 'newitemid'.$opclang, 0, $data['newitemid']); 
	  }
	  
	  	  OPCconfig::clearConfig('opc_config', 'op_customitemidty'.$opclang, 0);
	  if (!empty($data['op_customitemidty'])) {

	  OPCconfig::store('opc_config', 'op_customitemidty'.$opclang, 0, $data['op_customitemidty']); 
	  
	  }
	  
	  /*
	  if (!empty($data['op_customitemidty']))
 {
  $cfg .= '$op_customitemidty = "'.(int)trim($data['op_customitemidty']).'";
      ';
 }
*/

/* 
if (!empty($data['newitemid']))
 $cfg .= '$newitemid = "'.trim($data['newitemid']).'";
      ';
      else $cfg .= '$newitemid = "";
      ';
*/
	  
	  //
	  /*
	  if (!empty($data['op_articleid']))
      $cfg .= '$op_articleid = "'.$data['op_articleid'].'";
	  ';
	  else $cfg .= '$op_articleid = "";
	  ';
	  

	  	  if (!empty($data['adc_op_articleid']))
      $cfg .= '$adc_op_articleid = "'.$data['adc_op_articleid'].'";
	  ';
	  else $cfg .= '$adc_op_articleid = "";
	  ';
     */

if (isset($data['op_sum_tax']))
      $cfg .= '$op_sum_tax = true;
      ';
      else $cfg .= '$op_sum_tax = false;
      ';

if (isset($data['op_last_field']))
      $cfg .= '$op_last_field = true;
      ';
      else $cfg .= '$op_last_field = false;
      ';


if (!empty($data['op_default_zip']))
{
	$cfg .= '$op_default_zip = "'.urlencode($data['op_default_zip']).'"; 
	';
}
else 
{
    if (($data['op_default_zip'] === '0')  || ($data['op_default_zip'] === 0))
	$cfg .= '$op_default_zip = 0; ';
	else
	$cfg .= '$op_default_zip = "99999";
	'; 
}



if (!empty($data['op_numrelated']) && (is_numeric($data['op_numrelated'])))
      $cfg .= '$op_numrelated = "'.$data['op_numrelated'].'"; 
      ';
      else $cfg .= '$op_numrelated = false;
      ';


$cfg .= '
// auto config by template
$cut_login = false;
      ';

if (isset($data['op_delay_ship']))
      $cfg .= '$op_delay_ship = true;
      ';
      else $cfg .= '$op_delay_ship = false;
      ';

if (isset($data['op_loader']))
      $cfg .= '$op_loader = true;
      ';
      else $cfg .= '$op_loader = false;
      ';


if (isset($data['op_usernameisemail']))
      $cfg .= '$op_usernameisemail = true;
      ';
      else $cfg .= '$op_usernameisemail = false;
      ';
      
      
if (isset($data['no_continue_link_bottom']))
      $cfg .= '$no_continue_link_bottom = true;
      ';
      else $cfg .= '$no_continue_link_bottom = false;
      ';

if (isset($data['op_default_state']))
      $cfg .= '$op_default_state = true;
      ';
      else $cfg .= '$op_default_state = false;
      ';
       
if (isset($data['list_userfields_override']))
      $cfg .= '$list_userfields_override = true;
      ';
      else $cfg .= '$list_userfields_override = false;
      ';
      
if (isset($data['no_jscheck']))
      $cfg .= '$no_jscheck = true;
      ';
      else $cfg .= '$no_jscheck = true;
      ';
      
if (isset($data['op_dontloadajax']))
      $cfg .= '$op_dontloadajax = true;
      		   $no_jscheck = true;
      ';
      else $cfg .= '$op_dontloadajax = false;
      ';
      
if (isset($data['shipping_error_override']))
		{
		$serr = urlencode($data['shipping_error_override']);
      $cfg .= '$shipping_error_override = "'.$serr.'";
      ';
       }
      else $cfg .= '$shipping_error_override = "";
      ';


if (isset($data['op_zero_weight_override']))
      $cfg .= '$op_zero_weight_override = true;
      ';
      else $cfg .= '$op_zero_weight_override = false;
      ';


if (isset($data['email_after']))
      $cfg .= '$email_after = true;
      ';
      else $cfg .= '$email_after = false;
      ';

if (isset($data['override_basket']))
      $cfg .= '$override_basket = true;
      ';
      else $cfg .= '$override_basket = false;
      ';


/*
	    if (empty($is_admin))
		{
		$selected_template_override = JRequest::getVar(\'opc_theme\', \'\'); 
		if (!empty($selected_template_override))
		{
		$test = str_replace(\'_\', \'\', $selected_template_override); 
		if (ctype_alnum($test))
		 {
		   $selected_template = $selected_template_override; 
		 }
		}
		}
*/
  $data['mobile_template'] = JFile::makeSafe($data['mobile_template']); 
if ($data['selected_template'] != 'default')
{
   $data['selected_template'] = JFile::makeSafe($data['selected_template']); 

   $cfg .= '
	  
	  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.\'components\'.DIRECTORY_SEPARATOR.\'com_onepage\'.DIRECTORY_SEPARATOR.\'helpers\'.DIRECTORY_SEPARATOR.\'mini.php\'); 
	 
	  $selected_template = OPCmini::getSelectedTemplate(\''.addslashes($data['selected_template']).'\', \''.addslashes($data['mobile_template']).'\'); 
	  

		
      ';
}
else
{
       $cfg .= '$selected_template = ""; 
       ';
}


if (!empty($data['mobile_template']))
{

  
  
  $cfg .= ' 
  $is_mobile = false; 
  $mobile_template = "'.$data['mobile_template'].'";  '; 
  /*
  if (empty($is_admin))
  if (empty($selected_template_override))
  {
$app = JFactory::getApplication(); 
$jtouch = $app->getUserStateFromRequest(\'jtpl\', \'jtpl\', -1, \'int\');
if (($jtouch > 0) || (defined(\'OPC_DETECTED_DEVICE\') && ((constant(\'OPC_DETECTED_DEVICE\')==\'MOBILE\') || ((constant(\'OPC_DETECTED_DEVICE\')==\'TABLET\')))))
 {
   $is_mobile = true; 
   $selected_template = $mobile_template; 
 }
 
 
 }
  
  ';
  */
}

if (!isset($data['adwords_timeout']))
$data['adwords_timeout'] = 4; 

$op_timeout = (int)$data['adwords_timeout']; 
$cfg .= ' $adwords_timeout = '.$op_timeout.'; '; 

if (isset($data['dont_show_inclship']))
      $cfg .= '$dont_show_inclship = true;
      ';
      else $cfg .= '$dont_show_inclship = false;
      ';

if (isset($data['no_continue_link']))
      $cfg .= '$no_continue_link = true;
      ';
      else $cfg .= '$no_continue_link = false;
      ';

	  require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'tracking.php'); 
	  $modelT = new JModelTracking(); 
	  $msg .= $modelT->setEnabled(); 
	  
	 // removed in 2.0.207
	 /*
	 if (false)
if (isset($data['adwords_enabled_0']) && (!empty($_POST['adwords_code_0'])))
{
    jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');
	
   $code = JRequest::getVar('adwords_code_0', '', 'post', 'string', JREQUEST_ALLOWRAW); // $_POST['adwords_code_0']; $code = $_POST['adwords_code_0'];
    if (JFile::write(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'body.html', $code) === false)
    {
         $msg .= 'Cannot write to: '.JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'body.html<br />';
    }
    else
    {
    $cfg .= '
    $adwords_name = array(); $adwords_code = array(); $adwords_amount = array();
	$adwords_name[0] = "body.html";
	
        
 		$adwords_amount[0] = "'.$data['adwords_amount_0'].'";
        $adwords_enabled[0] = true;
 	';
  }
}
else
{
 $cfg .= '
 	$adwords_name = array(); $adwords_code = array(); $adwords_amount = array();
 	$adwords_name[0] = "body.html";
 	$adwords_amount[0] = "'.$data['adwords_amount_0'].'";
 	';
	
	jimport('joomla.filesystem.file');
	$code = ""; 
	if (JFile::write(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'body.html', $code) === false)
    {
         $msg .= 'Cannot write to: '.JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'body.html';
    }
	
}
  */
if (isset($data['no_login_in_template']))
      $cfg .= '$no_login_in_template = true;
      ';
      else $cfg .= '$no_login_in_template = false;
      ';


$cfg .'


/* Following variables are to change shipping or payment to select boxes
*/
';

if (isset($data['shipping_inside']))
      $cfg .= '$shipping_inside = true;
      ';
      else $cfg .= '$shipping_inside = false;
      ';

if (isset($data['payment_inside']))
      $cfg .= '$payment_inside = true;
      ';
      else $cfg .= '$payment_inside = false;
      ';

if (isset($data['payment_saveccv']))
      $cfg .= '$payment_saveccv = true;
      ';
      else $cfg .= '$payment_saveccv = false;
      ';

	  
if (isset($data['payment_advanced']))
      $cfg .= '$payment_advanced = true;
      ';
      else $cfg .= '$payment_advanced = false;
      ';
	  

if (isset($data['fix_encoding']))
      $cfg .= '$fix_encoding = true;
      ';
      else $cfg .= '$fix_encoding = false;
      ';

if (isset($data['fix_encoding_utf8']))
      $cfg .= '$fix_encoding_utf8 = true;
$fix_encoding = false;
      ';
      else $cfg .= '$fix_encoding_utf8 = false;
      ';


if (isset($data['shipping_inside_basket']))
      $cfg .= '$shipping_inside_basket = true;
      ';
      else $cfg .= '$shipping_inside_basket = false;
      ';

if (isset($data['payment_inside_basket']))
      $cfg .= '$payment_inside_basket = true;
      ';
      else $cfg .= '$payment_inside_basket = false;
      ';

if (isset($data['email_only_pok']))
      $cfg .= '$email_only_pok = true;
      ';
      else $cfg .= '$email_only_pok = false;
      ';
      
if (!empty($data['no_taxes_show']))
      $cfg .= '$no_taxes_show = true;
      ';
      else $cfg .= '$no_taxes_show = false;
      ';
      
if (!empty($data['use_order_tax']))
      $cfg .= '$use_order_tax = true;
      ';
      else $cfg .= '$use_order_tax = false;
      ';
      
if (isset($data['no_taxes']))
      $cfg .= '$no_taxes = true;
      ';
      else $cfg .= '$no_taxes = false;
      ';

if (isset($data['never_show_total']))
      $cfg .= '$never_show_total = true;
      ';
      else $cfg .= '$never_show_total = false;
      ';

if (isset($data['email_dontoverride']))
      $cfg .= '$email_dontoverride = true;
      ';
      else $cfg .= '$email_dontoverride = false;
      ';



if (isset($data['show_only_total']))
      $cfg .= '$show_only_total = true;
      ';
      else $cfg .= '$show_only_total = false;
      ';

if (isset($data['show_andrea_view']))
      $cfg .= '$show_andrea_view = true;
      ';
      else $cfg .= '$show_andrea_view = false;
      ';

    
    if (isset($data['always_show_tax']))
    $cfg .= '$always_show_tax = true;
';
    else $cfg .= '$always_show_tax = false;
';
   if (isset($data['always_show_all']))
    $cfg .= '$always_show_all = true;
';
    else $cfg .= '$always_show_all = false;
';


     if (isset($data['add_tax']))
      $cfg .= '$add_tax = true;
      ';
      else $cfg .= '$add_tax = false;
      ';

 if (isset($data['add_tax_to_shipping_problem']))
      $cfg .= '$add_tax_to_shipping_problem = true;
      ';
      else $cfg .= '$add_tax_to_shipping_problem = false;
      ';

 
 if (isset($data['add_tax_to_shipping']))
      $cfg .= '$add_tax_to_shipping = true;
      ';
      else $cfg .= '$add_tax_to_shipping = false;
      ';

 if (isset($data['custom_tax_rate']))
      $cfg .= '$custom_tax_rate = "'.addslashes($data['custom_tax_rate']).'"; 
      ';
      else $cfg .= '$custom_tax_rate = 0;
      ';
 
if (isset($data['opc_auto_coupon']))
      $cfg .= '$opc_auto_coupon = "'.addslashes($data['opc_auto_coupon']).'"; 
      ';
      else $cfg .= '$opc_auto_coupon = \'\';
      ';
  
	 

     if (isset($data['no_decimals']))
      $cfg .= '$no_decimals = true;';
      else $cfg .= '$no_decimals = false;';

     if (isset($data['curr_after']))
      $cfg .= '$curr_after = true;';
      else $cfg .= '$curr_after = false;';

 
	  //
	  if (isset($data['load_min_bootstrap']))
      $cfg .= '$load_min_bootstrap = true;';
      else $cfg .= '$load_min_bootstrap = false;';

	  

	  
    
    $cfg .= "
/*
Set this to true to unlog (from Joomla) all shoppers after purchase
*/
";

 
   if (isset($data['unlog_all_shoppers']))
    $cfg .= '$unlog_all_shoppers = true;
		$no_login_in_template = true; 
';
    else $cfg .= '$unlog_all_shoppers = false;
'; 
  
  // vat_input_id, eu_vat_always_zero, move_vat_shopper_group, zerotax_shopper_group
    if (!empty($data['vat_input_id']))
	  $cfg .= '$vat_input_id = "'.$data['vat_input_id'].'"; '; 
	else $cfg .= '$vat_input_id = ""; '; 

    if (!empty($data['eu_vat_always_zero']))
	  $cfg .= '$eu_vat_always_zero = "'.$data['eu_vat_always_zero'].'"; '; 
	else $cfg .= '$eu_vat_always_zero = ""; '; 

	if (empty($data['vat_except'])) $data['vat_except'] = ''; 
    $te = strtoupper($data['vat_except']); 
	$eu = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'); 
	
	
    if (!empty($data['vat_except']))
	{
	  if (!in_array($te, $eu)) 
	 {
	 $msg .= 'Country code is not valid for EU ! Code used:'.$data['vat_except'].'<br />'; 
	 $msg .= "These are valid codes : 'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK' without apostrophies <br />"; 
	 }
	  $cfg .= '$vat_except = "'.$data['vat_except'].'"; '; 
	 }
	else $cfg .= '$vat_except = ""; '; 
	
	 if (!empty($data['move_vat_shopper_group']))
	  $cfg .= '$move_vat_shopper_group = "'.$data['move_vat_shopper_group'].'"; '; 
	 else $cfg .= '$move_vat_shopper_group = ""; '; 
	
	if (!empty($data['zerotax_shopper_group']))
	{
	  $str = ''; 
	  foreach ($data['zerotax_shopper_group'] as $g)
	   {
	     if (!empty($str)) $str .= ','.$g.'';
		 else $str = "".$g.""; 
	   }
	   $cfg .= ' $zerotax_shopper_group = array('.$str.'); '; 
	}
	else $cfg .= ' $zerotax_shopper_group = array(); '; 
	
$cfg .= " 
/* set this to true if you don't accept other than valid EU VAT id */
";
 if (!empty($data['must_have_valid_vat']))
	  $cfg .= '$must_have_valid_vat = true; '; 
	 else $cfg .= '$must_have_valid_vat = false; '; 

		 $cfg .= "
/*
* Set this to true to unlog (from Joomla) all shoppers after purchase
*/
";
		 if (isset($data['unlog_all_shoppers']))
		 {
		  $cfg .= ' $unlog_all_shoppers = true;
      ';
     }
     else $cfg .= ' $unlog_all_shoppers = false;
     ';
		 
		 $cfg .= "
/* This will disable positive messages on Thank You page in system info box */

";
      

       
    $cfg .= "
/* please check your source code of your country list in your checkout and get exact virtuemart code for your country
* all incompatible shipping methods will be hiddin until customer choses other country
* this will also be preselected in registration and shipping forms
* Your shipping method cannot have 0 index ! Otherwise it will not be set as default
*/     
";
     if (isset($data['default_country']))
     {
      $cfg .= ' $default_shipping_country = "'.$data['default_country'].'";
      ';
     }
     else $cfg .= ' $default_shipping_country = "";
     ';
	 
	 /*
	$cfg .= '
	if (!defined("DEFAULT_COUNTRY"))
	{
	 if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_geolocator".DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."helper.php"))
	 {
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR."administrator".DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_geolocator".DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR."helper.php"); 
	  if (class_exists("geoHelper"))
	   {
	     $country_2_code = geoHelper::getCountry2Code(""); 
		 if (!empty($country_2_code))
		 {
		 $db= JFactory::getDBO(); 
		 $db->setQuery("select virtuemart_country_id from #__virtuemart_countries where country_2_code = \'".$country_2_code."\' "); 
		 $r = $db->loadResult(); 
		 if (!empty($r)) 
		 $default_shipping_country = $r; 
		 }
	     //$default_shipping_country = 
	   }
	 }
	  define("DEFAULT_COUNTRY", $default_shipping_country); 
	 }
	 else
	 {
	  $default_shipping_country = DEFAULT_COUNTRY; 
	 
	 }
	';  
	*/
		 $cfg .= "
/* since VM 1.1.5 there is paypal new api which can be clicked on image instead of using checkout process
* therefore we can hide it from payments
* These payments will be hidden all the time
* example:  ".'$payments_to_hide = "4,3,5,2";
*/
';
		 
		 $cfg .= "
/* default payment option id
* leave commented or 0 to let VM decide
*/
";
	$pd = $data['default_payment'];
	if (!isset($data['default_payment']) || ($pd == 'default')) $pd = '""';
	$cfg .= '$payment_default = \''.$pd.'\';
	';
	
	if (!empty($data['default_payment_zero_total']))
	{
		$pdz = (int)$data['default_payment_zero_total']; 
		$cfg .= ' $default_payment_zero_total = '.$pdz.'; '; 
	}
	
	$cfg .= "
/* turns on google analytics tracking, set to false if you don't use it */
";
    /*
	if ($data['g_analytics']=='1')
	{
	  $cfg .= ' $g_analytics = true;
';
	}
	else 
	  $cfg .= ' $g_analytics = false;
';
    */
	
	$cfg .= "
/* set this to false if you don't want to show full TOS
* if you set show_full_tos, set this variable to one of theses:
* use one of these values:
* 'shop.tos' to read tos from your VirtueMart configuration
* '25' if set to number it will search for article with this ID, extra lines will be removed automatically
* both will be shown without any formatting
*/
";

/* disabled, now differenciated between logged and unlogged within the loader file which is further sent to the template
 	if (isset($data['show_full_tos']))
 	{
 	  $cfg .= ' $show_full_tos = true; 
';
 	} else  	  $cfg .= ' $show_full_tos = false; 
';
*/

	//tos_config
	$opclang = JRequest::getVar('opc_lang_orig', ''); 
    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	
	OPCconfig::store('opc_config', 'tos_config'.$opclang, 0, $data['tos_config']); 

	/*
 	$t = $data['tos_config'];
 	$t = trim(strtolower($t));
 	$cfg .= ' $tos_config = "'.$t.'"; 
';
 	*/
 	
	/*
 	if (isset($data['op_show_others']))
 	{
 	  $cfg .= ' $op_show_others = true; 
';
 	} else  	  $cfg .= ' $op_show_others = false; 
	
	
	
';
	*/

 	if (isset($data['op_fix_payment_vat']))
 	{
 	  $cfg .= ' $op_fix_payment_vat = true; 
';
 	} else  	  $cfg .= ' $op_fix_payment_vat = false; 
';

	
 	if (isset($data['op_free_shipping']))
 	{
 	  $cfg .= ' $op_free_shipping = true; 
';
 	} else  	  $cfg .= ' $op_free_shipping = false; 
';

 	
 	$cfg .= "
/* change this variable to your real css path of '>> Proceed to Checkout'
* let's hide 'Proceed to checkout' by CSS
* if it doesn't work, change css path accordingly, i recommend Firefox Firebug to get the path
* but this works for most templates, but if you see 'Proceed to checkout' link, contact me at stan@rupostel.sk
* for rt_mynxx_j15 template use '.cart-checkout-bar {display: none; }'
*/
";
 	
	$cfg .= '
$payment_info = array();
$payment_button = array();
$default_country_array = array();
';
	
	$cfg .= "\n".' /* URLs fetched after checkout encoded by base64_encode */'."\n";
	$cfg .= ' $curl_url = array('; 
    $arr = array(); 
	foreach ($data as $key=>$val)
	 {
	    
	
	    if (strpos($key, 'curl_url_')!==false)
		 {
		   if (!empty($val))
		   $arr[] = "'".base64_encode($val)."'"; 
		 }
	 }
	
	$arrt = implode(',', $arr); 
	if (empty($arr)) $arrt = ''; 
    $cfg .= $arrt.');'."\n"; 

	
	
	$payment_info = array();
 	$payment_button = array();
	
	// needs update:
	$langs = array(); 
	
	foreach ($langs as $l)
	{
	 $langcfg[$l] = "";
	}

	
		  if(!class_exists('JFile')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'file.php');
		  if(!class_exists('JFolder')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');

	
	

	

	
	foreach ($data as $k=>$d)
	{
	
	
	  // ok we will add a default country for a lang
	  if (strpos($k, 'op_lang_code_')!==false)
	  {
	   $id = str_replace('op_lang_code_', '', $k);
	   if (!empty($data[$k]) && (!empty($data['op_selc_'.$id])))
	   {
	    $cfg .= '
$default_country_array["'.$data[$k].'"] = "'.$data['op_selc_'.$id].'"; 
';
	   }
	  }
	  
	  	  if (strpos($k, 'op_group_')!==false)
	  {
	   $id = str_replace('op_group_', '', $k);
	   if (!empty($data[$k]) && (!empty($data['op_group_'.$id])))
	   {
	   if (!empty($data['op_lang_code2_'.$id]))
	    $cfg .= '
$lang_shopper_group["'.$data['op_lang_code2_'.$id].'"] = "'.$data['op_group_'.$id].'"; 
';
	   }
	  }
	  

	  	  if (strpos($k, 'op_selc2_')!==false)
	  {
	   $id = str_replace('op_selc2_', '', $k);
	   if (isset($data[$k]) && (!empty($data['op_group_ip_'.$id])))
	   {
	   if (isset($data['op_selc2_'.$id]))
	    $cfg .= '
$lang_shopper_group_ip["'.$data['op_selc2_'.$id].'"] = "'.$data['op_group_ip_'.$id].'"; 
';
	   }
	  }

	  
	  
	  if (strpos($k, 'hidepsid_')!==false)
	  {
	    $ida = explode('_', $k, 2);
	    $ida = $ida[1];
	    $id = $data[$k];

	    //$id = $d;
	    if (($id != 'del') && (count($data["hidep_".$ida])>0))
	    {
	    $def = $data["hidepdef_".$ida];
	    $cfg .= ' $hidep["'.$id.'"] = "';

	    if (isset($data["hidep_".$ida]))
	    {
	    foreach ($data["hidep_".$ida] as $h)
	    {
	      $cfg .= $h.'/'.$def.',';

	    }
	    } 
	    else
	    {

	    }
	    $cfg .= '";
';
	    }
	  }
	  
	
	  if (strpos($k, 'ONEPAGE_PAYMENT_EXTRA_INFO')!==false)
	  {
	    $arr = explode('_', $k);
	    $lang = $arr[1];
	    $id = $arr[count($arr)-1];
	    if (!isset($payment_info[$id]))
	    {
	    $payment_info[$id] = $id;
	    $cfg .= '$payment_info["'.$id.'"] = JText::_("COM_ONEPAGE_PAYMENT_EXTRA_INFO_'.$id.'"); 
';
	    }
	  }
	  if (strpos($k, 'ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON')!==false)
	  {
	    $arr = explode('_', $k);
	    $lang = $arr[1];
	    $id = $arr[count($arr)-1];
	    if (!isset($payment_button[$id]))
	    {
	    $payment_button[$id] = $id;
	    $cfg .= '$payment_button["'.$id.'"] = JText::_("COM_ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_'.$id.'"); 
';
	    }
	  }
	  

	  
	  
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
	
	
	$cfg .= '
	
	
if (class_exists(\'OPCmini\'))
{
jimport(\'joomla.filesystem.file\');
$selected_template = JFile::makeSafe($selected_template); 
if (!empty($selected_template) && (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR."overrides".DIRECTORY_SEPARATOR."onepage.cfg.php")))
{
  
  include(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."themes".DIRECTORY_SEPARATOR.$selected_template.DIRECTORY_SEPARATOR."overrides".DIRECTORY_SEPARATOR."onepage.cfg.php");
 
}
}
	



';

		

		
		$conf_file = JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."onepage.cfg.php";
		$ret = true;

		  if(!class_exists('JFile')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'file.php');
		  if(!class_exists('JFolder')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');
		
		 
		if (JFile::write($conf_file, $cfg)===false) 
		{
			$msg .= JText::_('COM_ONEPAGE_ACCESS_DENIED_CONFIG').' '.$conf_file.'<br />';
			$ret = false;
			// lets test if it is php valid
		
		}
		else
		{
		
			//unset($disable_onepage);
			
			
			
		    
			if (eval('?>'.file_get_contents($conf_file))===false)
			{
			
			 $msg .= JText::_('COM_ONEPAGE_CONFIG_VALIDATION_ERROR').' <br />';
			 $ret = false;
			 // we have a big problem here, generated file is not valid
			 if (!JFile::copy(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."onepage.cfg.php", JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."onepage.invalid.cfg.php"))
			 {
			 
			 }
			 if (!JFile::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'onepage.cfg.php', JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."onepage.cfg.php"))
			 {
	    		  $msg .= 'Copying of default onepage.cfg.php was not successfull <br />';
	    		  
			 }
			}

		}
		
		
		if (function_exists('opcache_invalidate')) {
			opcache_invalidate(JPATH_ROOT.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."onepage.cfg.php", true); 
		}
		
	// let's alter VM config here as last step: 
	
	
	/*
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	
	$configm = OPCmini::getModel('Config'); 
	
    $c = VmConfig::get('coupons_enable', true); 
	VmConfig::set('coupons_enable', 10); 
	
	
	
	$test = VmConfig::get('coupons_enable'); 
	VmConfig::set('coupons_enable', $c); 
	
	
	if ($test != 10)
	 {
	   $isadmin =false; 
	 }
	 else $isadmin = true; 
	 
	 if ((method_exists('VmConfig', 'isAtLeastVersion')) || (!$isadmin))
	 {
	   $msg .= 'Notice: You are running an old version of Virtuemart or you are not logged in as shop Administrator. Some Virtuemart settings cannot be updated with OPC. Please update TOS, registration type, SSL in your virtuemart configuration. (oncheckout_show_register, oncheckout_only_registered, agree_to_tos_onorder, automatic_shipment, oncheckout_show_legal_info, useSSL)  '; 
	   
	   $isadmin = false; 
	 }
	if ($isadmin)
	if (!$configm->store($set))
	{
		//$msg .= 'Error saving virtuemart configuration'; 
	}
	
	VmConfig::loadConfig(true); 
	*/
  
  
  
  
		
if (empty($_SESSION['onepage_err']))
    	         $_SESSION['onepage_err'] = serialize($msg);
    	         else 
    	         {
    	          $_SESSION['onepage_err'] = serialize($msg.unserialize($_SESSION['onepage_err']));
    	         }
		 
		 
		 return $ret;
	}
	
	
	public function installOtherFiles() {
		
		return ''; 
	}
	
	public function fixOrderReuse() {
		
		$f = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'orders.php'; 
	    if (file_exists($f)) {
			
		  $dt = file_get_contents($f); 
		  $c = false; 
		  $s1 = '$db->setQuery($q . \' WHERE `order_number`= "\'.$_cart->virtuemart_order_id.\'" AND `order_status` = "P" \');'; 
		  $s2 = '$db->setQuery($q . \' WHERE `virtuemart_order_id`= "\'.$_cart->virtuemart_order_id.\'" AND `order_status` = "P" \');'; 
		  if (strpos($dt, $s1) !== false) {
		    $dt = str_replace($s1, $s2, $dt); 
			$c = true; 
		  }
		  $s5 = 'removeOrderItems ($virtuemart_order_id, $auth = true)'; 
		  $s3 = 'function removeOrderItems2 ('; 
		  $s4 = 'vmAccess::manager(\'orders.edit\')'; 
		  $r3 = 'function removeOrderItems ('; 
		  $el = "\r\r\n"; 
		  $el2 = "\r\n"; 
		  
		  $zz5 = strpos($dt, $s5); 
		  if ($zz5 === false)  {
		  
		  $zz1 = strpos($dt, $s4); 
		  $zz2 = strpos($dt, $s3); 
		  
		  
		  
		  
		  if ($zz1!==false) 
		  if ($zz2===false) {
			if (strpos($dt, $el) !== false) $le = "\r\r\n"; 
			else
			if (strpos($dt, $el2) !== false) $le = "\r\n"; 
		    else
			$le = "\n"; 
			  
		    $dt = str_replace($r3, $s3, $dt); 
			$c = true; 
			$fn = $le.$le.'function removeOrderItems ($virtuemart_order_id){'.$le; 
			$fn .= ' /*this whole funciton was added by rupostel opc to fix double items for order reuse */ '.$le; 
			$fn .= ' if (empty($virtuemart_order_id)) return true; '.$le; 
			$fn .= ' $q = \'DELETE from `#__virtuemart_order_items` WHERE `virtuemart_order_id` = \' .(int) $virtuemart_order_id; $db = JFactory::getDBO(); $db->setQuery($q); '.$le; 
			$fn .= ' $db->query(); '.$le;
			$fn .= ' $q = \'DELETE from `#__virtuemart_order_calc_rules` WHERE `virtuemart_order_id` = \' .(int) $virtuemart_order_id; $db->setQuery($q); '.$le; 
			$fn .= ' $db->query(); '.$le;
			$fn .= ' return true; '.$le; 
			$fn .= ' } '.$le.$le.$le; 
			
			$dt = str_replace($s3, $fn.$s3, $dt); 
			
			
			
			
		  }
		  }
		  if (!empty($c)) {
			JFile::copy($f, $f.'.bck.'.time().'.php');
		    if (JFile::write($f, $dt)===false) {
			   return JText::_('COM_ONEPAGE_CANNOT_WRITE_FILE').$f."<br />"; 
			}
			else
			{
				return JText::_('COM_ONEPAGE_ALLOW_ORDER_REUSE_NOTE')."<br />"; 
			}
		  
		  }
		}
		return ''; 
	}
	
	public function getCalcs() {
	  		  $db = JFactory::getDBO(); 
		  $q = 'select calc_name, virtuemart_calc_id from #__virtuemart_calcs where published = 1'; 
		  $db->setQuery($q); 
		  $res = $db->loadAssocList(); 
		  $calcs = array(); 
		  if (empty($res)) return array(); 
		  foreach ($res as $k=>$row)
		  {
			  $id = (int)$row['virtuemart_calc_id'];
			  $calcs[$id] = $row['calc_name']; 
			  
		  }
		  
		
		 return $calcs; 

	}
	
	private function alterCartsTable($disable=true)
	{
		$db = JFactory::getDBO(); 
	    $q = 'SHOW CREATE TABLE '.$db->getPrefix().'virtuemart_carts'; 
		
		$db->setQuery($q); 
		$def = $db->loadAssocList(); 
		if (isset($def[0]['Create Table']))
		{
		  $ct = $def[0]['Create Table']; 
		  if (stripos($ct, '=MyISAM')!==false)
		  $eng = 'MyISAM'; 
	      else
		  if (stripos($ct, '=InnoDB')!==false)
		  $eng = 'InnoDB'; 
		  else
		  {
		    $eng = 'BLACKHOLE'; 
		  }
		
		}
		
		
		
		$msg = ''; 
		if ($disable)
		{
			
		if ($eng === 'BLACKHOLE') return; 

			 
	   $config = false; 
	   $config = OPCconfig::getValue('table_updater_carts', 'virtuemart_carts', 0, $config); 	 
	    if ($config === false)
		 {
		   // store the table defintion just once
		   OPCconfig::store('table_updater_carts','virtuemart_carts', 0, $eng); 
		  
			
			
		 }
		
		 
		 
			
			
			$q = 'show engines'; 
			$db->setQuery($q); 
			try
			{
			$res = $db->loadAssocList(); 
			}
			catch(Exception $e)
			{
				$found = false; 
			}
			$found = false; 
			if (!empty($res))
			foreach ($res as $k=>$v)
			{
				if (isset($v['Engine']))
					if (strtoupper($v['Engine'])==='BLACKHOLE')
					{
						$found = true; 
						break; 
					}
			}
			if (!$found)
			{
				$q = "SHOW VARIABLES LIKE 'have_blackhole_engine'";
				$db->setQuery($q); 
				try
				{
					$found = $db->loadResult(); 
				}
				catch (Exception $e)
				{
					$found = false; 
				}
			}
			
			if (!$found)
			{
				$msg .= 'BLACKHOLE MySQL engine is not available. OPC cannot alter table #__virtuemart_carts'; 
				return $msg; 
			}
			
			$q = 'ALTER TABLE `#__virtuemart_carts` ENGINE = BLACKHOLE'; 
			try
			{
			 $db->setQuery($q); 
			 $db->query(); 
			 $e = $db->getErrorMsg(); 
			 if (empty($e))
			 {
				 $msg .= JText::_('COM_ONEPAGE_CARTS_TABLE_DISABLED').' BLACKHOLE'; 
			 }
			}
			catch (Exception $e)
			{
				$msg .= 'BLACKHOLE MySQL engine is not available. OPC cannot alter table #__virtuemart_carts'; 
			}
			
		}
		else
		{
			
			$config2 = false; 
	        $config = OPCconfig::getValue('table_updater_carts', 'virtuemart_carts', 0, $config2, false); 	 
			
			
			$config = strtoupper($config); 
			$eng = strtoupper($eng); 
		if (!($config === 'MYISAM') && ($eng === 'INNODB'))
		{	
	    if ((!empty($config)) && ($config !== false) && ($config !== $eng) )
		 {
		   
		    $q = 'ALTER TABLE `#__virtuemart_carts` ENGINE = '.$config; 
			try
			{
			 $db->setQuery($q); 
			 $db->query(); 
			 $e = $db->getErrorMsg(); 
			 if (empty($e))
			 {
				 $msg .= JText::_('COM_ONEPAGE_CARTS_TABLE_DISABLED').' '.$config; 
			 }
			}
			catch (Exception $e)
			{
				//$msg .= 'Failed to switch back to '.$config.' engine on table #__virtuemart_carts'; 
			}
			
			//OPCconfig::clearConfig('table_updater_carts', 'virtuemart_carts', 0); 
			
		 }
		 else
		 {
			 if (($config === false) && ($eng === 'BLACKHOLE'))
			 {
				  $q = 'ALTER TABLE `#__virtuemart_carts` ENGINE = MyISAM'; 
			try
			{
			 $db->setQuery($q); 
			 $db->query(); 
			 $e = $db->getErrorMsg(); 
			 if (empty($e))
			 {
				 $msg .= JText::_('COM_ONEPAGE_CARTS_TABLE_DISABLED').' '.'MyISAM'; 
			 }
			}
			catch (Exception $e)
			{
				$msg .= 'Failed to switch back to MyISAM engine on table #__virtuemart_carts'; 
			}
			 }
		 }
		}
		}
		if (!empty($msg)) $msg .= "\n<br />"; 
		return $msg; 
	}
	private function fixVmCache()
	{
		$f = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpsplugin.php'; 
		if (file_exists($f))
		{
			$data = file_get_contents($f); 
			if (stripos($data, 'static $c')!==false)
			{
				$data = str_replace('static $c', '$c', $data); 
				if (JFile::write($f, $data)===false)
				{
					
					return 'Cannot modify broken VM core file in '.$f; 
				}
				else
				{
					
					return 'OPC modified VM core file to fix a VM bug described <a href="http://www.rupostel.com/phpBB3/viewtopic.php?f=7&t=1215"> at RuposTel forum</a> File modified: '.$f."\n<br />"; 
				}
			}
		}
		return ''; 
		
	}
	
	   private function setPluginEnabled($element, $folder='system', $enabled=false, $type='plugin') 
	    {
		  $db = JFactory::getDBO(); 
		  $q = "select * from `#__extensions` where `element` = '".$db->escape($element)."' and `type` = '".$db->escape($type)."' and `folder` = '".$db->escape($folder)."' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  if (empty($isInstalled) && (!$enabled)) return; 
		  
		  
		  if (!empty($isInstalled))
		  {
		    if ($enabled)
			{
		      $q = " UPDATE `#__extensions` SET  enabled =  '1', `state` = 0 WHERE  `element` = '".$db->escape($element)."' and `folder` = '".$db->escape($folder)."' "; 
			  $db->setQuery($q); 
			  $db->query(); 
			}
			else
			{
			  $q = " UPDATE `#__extensions` SET  `enabled` =  '0', `state` = 0 WHERE  `element` = '".$db->escape($element)."' and `folder` = '".$db->escape($folder)."' "; 
			  $db->setQuery($q); 
			  $db->query(); 
			}
		  }
		  
		 $this->copyPlugin($folder, $element); 
		 /*
		 //always copy files: 
		 
		     $element = JFile::makeSafe($element); 
			 $mf = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.$element.DIRECTORY_SEPARATOR.$element.'.xml'; 
			 
		     if (file_exists($mf))
			  {
			    $xml = @simplexml_load_file($mf); 
				
				//if (empty($xml)) return; 
				$jtype = $xml->getName(); 
				$xml_type = (string)$xml['type']; 
				$xml_group = (string)$xml['group']; 
				if ($jtype == 'extension')
				{
				  $xml_name = (string)$xml->name; 
				  if (!empty($xml->files))
				   {
				      foreach ($xml->files->children() as $k=>$file)
					  {
					 
					  foreach ($file->attributes() as $aname=>$e)
					   {
					   
					      if ($aname == $xml_type)
						  $xml_element = (string)$e; 
					   }
					  }
				   }
				}
				$xml_description = (string)$xml->description; 
				$xml_version = (string)$xml->version; 
				$xml_creationDate = (string)$xml->creationDate; 
				
				
				if ($xml_type === 'plugin')
				 {
				    $xml_element = JFile::makeSafe($xml_element); 
					$xml_group = JFile::makeSafe($xml_group); 
					$xml_element = JFile::makeSafe($xml_element); 
					
					$dest = JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$xml_group.DIRECTORY_SEPARATOR.$xml_element; 
					
				    if (!file_exists($dest))
					 if (JFolder::create($dest)===false) return;
					 
					$src = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.$xml_element; 
					$files = @scandir($src); 
					foreach ($files as $k=>$f)
					 {
					   if (($f == '.') || (($f == '..'))) 
					   {
					   unset($files[$k]); 
					   continue; 
					   }
					   if (is_dir($src.DIRECTORY_SEPARATOR.$f))
					   JFolder::copy($src.DIRECTORY_SEPARATOR.$f, $dest.DIRECTORY_SEPARATOR.$f); 
					   else
					   JFile::copy($src.DIRECTORY_SEPARATOR.$f, $dest.DIRECTORY_SEPARATOR.$f); 
					 }
					
				 }
				
			  }
			  
		*/  
		  if (empty($isInstalled) && ($enabled))
		  {
		     if (!empty($xml_element) 
			 && (!empty($xml_version))
			 && (!empty($xml_name))
			 && (!empty($xml_description)))
			 {
		  
		     $q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
			  $q .= " (NULL, '".$db->escape($xml_name)."', '".$db->escape($xml_type)."', '".$db->escape($xml_element)."', '".$db->escape($xml_group)."', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"".$db->escape($xml_name)."\",\"type\":\"".$db->getEscape($xml_type)."\",\"creationDate\":\"".$db->escape($xml_creationDate)."\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"".$db->escape($xml_version)."\",\"description\":\"".$db->escape($description)."\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0) "; 
		      
			  
			  $db->setQuery($q); 
		      $db->query(); 
			  $e = $db->getErrorMsg(); 
			    if (!empty($e)) { JFactory::getApplication()->enqueueMessage($e);  }
			  }
		  }
		  
		}
		
		function copyPlugin($type, $plugin)
		{
			jimport('joomla.filesystem.folder');
		    jimport('joomla.filesystem.file');
		   
				$dst = JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$plugin; 
				if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$plugin))
					  {
						  			JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$plugin); 
					  }
				
				try {
				$z = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.$plugin; 

				$src = realpath(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.$plugin); 
				$dst = realpath($dst);

				if ($src != $dst)
				{
				  if (JFolder::copy(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.$plugin, $dst, '', true)===false) {
				    
					
					$msg .= 'Cannot copy '.$plugin.' plugin to '.$type.' directory. If the plugin is already at it\'s place, ignore this message<br />'; 
					JFactory::getApplication()->enqueueMessage($msg); 
					$msgs = true; 
				   
				   
				   }
				  
				}
				
				
				}
				catch (Exception $e)
				{
					
					$s = (string)$e; 
					JFactory::getApplication()->enqueueMessage('Cannot copy to: '.$dst.': '.$s); 
				}
		}
		
		function enableOpcRegistration($enabled=false)
		{
			
			


			
		   $db = JFactory::getDBO(); 
		  $q = "select * from `#__extensions` where `element` = 'opcregistration' and `type` = 'plugin' and `folder` = 'system' limit 0,1"; 
		  $db->setQuery($q); 
		  $isInstalled = $db->loadAssoc(); 
		  
		  if (empty($isInstalled) && (!$enabled)) return;
		  
		  $this->copyPlugin('system', 'opcregistration'); 
		  
		  if (!empty($isInstalled))
		  {
		    if ($enabled)
			{
		      $q = " UPDATE `#__extensions` SET  `enabled` = '1', `state` = 0 WHERE  `element` = 'opcregistration' and `folder` = 'system' "; 
			  $db->setQuery($q); 
			  $db->query(); 
			}
			else
			{
			  $q = " UPDATE `#__extensions` SET  `enabled` = '0' WHERE  `element` = 'opcregistration' and folder = 'system' "; 
			  $db->setQuery($q); 
			  $db->query(); 
			}
		  }
		  if (empty($isInstalled) && ($enabled))
		  {
		     $q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
			  $q .= " (NULL, 'plg_system_opcregistration', 'plugin', 'opcregistration', 'system', 0, 1, 1, 0, '{\"legacy\":false,\"name\":\"plg_system_opcregistration\",\"type\":\"plugin\",\"creationDate\":\"December 2013\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"2.0.0\",\"description\":\"One Page Registration helper\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0) "; 
		      $db->setQuery($q); 
		      $db->query(); 
		  }
		  
		  
		}
	
	
		function updateVmConfig($arr)
		{
		   $db = JFactory::getDBO(); 
		   /*
		   if(!class_exists('VirtueMartModelConfig')) require(JPATH_VM_ADMINISTRATOR .'/models/config.php');
		   $configTable  = VirtueMartModelConfig::checkConfigTableExists();
		   */
		   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		   $configTable = OPCmini::tableExists('virtuemart_configs'); 
		   if (!empty($configTable))
		    {
			   $q = ' SELECT `config` FROM `#__virtuemart_configs` WHERE `virtuemart_config_id` = "1" limit 0,1';
			   $db->setQuery($q);
			   $res = $db->loadResult(); 
			   
			   $new = array(); 
			   
			   $config = explode('|', $res);
			   foreach($config as $item)
			   {
			   
			      $citem = explode('=', $item); 
			      $key = $citem[0]; 
				  $val = $citem[1]; 
				  $new[$key] = $val; 
				
				  
			   }
			   
			   
			   foreach ($arr as $key=>$val)
			    {
				  $new[$key] = serialize($val); 
				}
			   
			}
			
			$string = ''; 
			foreach ($new as $key => $val)
			{
			  if (!empty($string)) $string .= '|'; 
			  $string .= $key.'='.$val; 
			}
		
	       $q = "update #__virtuemart_configs set config = '".$db->escape($string)."' where virtuemart_config_id = 1";
			$db->setQuery($q); 
			$db->query(); 
			$e = $db->getErrorMsg(); if (!empty($e)) { die($e); }
		   
		}
		function getOPCExtensions(&$ename='')
		{
		   $_ename = $ename; 
		   $ename = -1; 
		   
		   if (!function_exists('simplexml_load_file')) return array(); 
		   
		  if(!class_exists('JFile')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'file.php');
		  if(!class_exists('JFolder')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');

		   
		   $exts = array(); 
		   if (!method_exists('JFolder', 'folders')) return array(); 
		   $folders = JFolder::folders(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install', '.', false, true); 
			
		   $li = 0; 
		   
		   if (!empty($folders))
		   foreach ($folders as $fo)
		     {
			    $files = JFolder::files($fo, '.xml', false, true); 
				
				if (!empty($files))
				$exts[] = reset($files); 
			 }
			 
			 
			 
			 $xt = array(); 
			 $db = JFactory::getDBO(); 
			 foreach ($exts as $file)
			 {
			  $xml=simplexml_load_file($file);
			  
			  $main = (string)$xml->getName();
			  if ($main === 'install')
			    {
				  if (!( version_compare( JVERSION, '3.0', '<' ) == 1))
				  continue; 
				}
			  $attribs = new stdClass(); 
			  foreach($xml->attributes() as $key=>$at)
			   {
			     $attribs->$key = (string)$at; 
			   }
			   
			  
			   $element = ''; 
			   $type = ''; 
			   $name = ''; 
			   $desc = ''; 
			   $group = ''; 
			   
			   $files = new stdClass(); 
			   if (isset($xml->files))
			   if (isset($xml->files->filename))
			   foreach ($xml->files->filename as $fn)
			   foreach($fn->attributes() as $key=>$at)
			   {
			     $files->$key = (string)$at; 
				 $element = (string)$at; 
			   }
			    
			  
			  
			  
			  if (isset($xml->name))
			   {
			     $name = (string)$xml->name; 
				 
				 if ($_ename === $name)
				 {
				   $ename = $li; 
				 
				 }
				 
				 $li++; 
				 
				 if (isset($attribs->group))
				 $group = $attribs->group; 
			 
				 $type = $attribs->type; 
				 $type = (string)$type; 
				 
				 $desc = (string)$xml->description; 
				  $dir = dirname($file); 
				  $n = JFile::makeSafe($name); 
				  $langf = $dir.DIRECTORY_SEPARATOR.'en-GB.'.$n.'.sys.ini'; 
				  if (file_exists($langf))
				  {
				     if (function_exists('parse_ini_file'))
					 {
					   $lang_array = parse_ini_file($langf); 
					   
					   if (isset($lang_array[$name])) 
					    {
						   $name = $lang_array[$name].' ('.$name.')'; 
						   
						}
						if (isset($lang_array[$desc]))
						$desc = $lang_array[$desc]; 
						
					 }
				  }
				 
			     $xt2['name'] = JText::_($name); 
				 
				 
				 $desc = strip_tags($desc, '<br />'); 
				 $xt2['description'] = $desc; 
				 
				 $xt2['dir'] = dirname($file).DIRECTORY_SEPARATOR; 
				 
				 if ($type == 'plugin')
				 {
				 $q = "select * from `#__extensions` where `name` = '".$db->escape($name)."' and `folder` = '".$db->escape($group)."' and `type` = '".$db->escape($type)."' "; 
				 if (!empty($element))
				  {
					 $q = "select * from `#__extensions` where `folder` = '".$db->escape($group)."' and `type` = '".$db->escape($type)."' "; 
				    $q .= " and `element` = '".$db->escape($element)."'"; 
				  }
				  $q .= " limit 0,1"; 
				 }
				 else
				 {
					 
					 
					 $q = "select * from `#__extensions` where `name` LIKE '".$db->escape($name)."' and `type` = '".$db->escape($type)."' "; 
				 if (!empty($element))
				  {
					$q = "select * from `#__extensions` where `type` = '".$db->escape($type)."' "; 
				    $q .= " and `element` = '".$db->escape($element)."'"; 
				  }
				  $q .= " limit 0,1"; 
				 }
				 
				 
				 
				 
				  
				  $db->setQuery($q); 
				  $res = $db->loadAssoc(); 
				 
				 
				  $xt2['data'] = $res; 
				  $xt2['link'] = ''; 
				  if (!empty($res))
				  {
				    if ($type == 'plugin')
				    $xt2['link'] = 'index.php?option=com_plugins&view=plugin&layout=edit&extension_id='.$res['extension_id']; 
					if ($type == 'module')
					$xt2['link'] = 'index.php?option=com_modules&view=module&layout=edit&id='.$res['extension_id']; 
					
				  }
				  
				  $xt[] = $xt2;  
			   }
			 }
			
			 return $xt; 
			 
		}
		
		
		function copylang()
		{
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
			
		   $err = $this->getlangerr(); 
		   $dbj = JFactory::getDBO(); 
		   $prefix = $dbj->getPrefix(); 
		   // CREATE TABLE recipes_new LIKE production.recipes; INSERT recipes_new SELECT * FROM production.recipes;
		   foreach ($err as $table)
		    {
			  $table = $prefix.$table;
			  $orig = str_replace(VMLANG, 'en_gb', $table); 
			  
			  
			  if ($this->tableExists($orig))
			  {
			    
			   $q = 'create table '.$table.' like '.$orig; 
			   $dbj->setQuery($q); 
			   $dbj->query(); 
			   $err = $dbj->getErrorMsg(); if (!empty($err)) { echo $err; die('err config.php'); }
			   // INSERT INTO recipes_new SELECT * FROM production.recipes;
			   $q = 'insert into '.$table.' select * from '.$orig; 
			   $dbj->setQuery($q); 
			   $dbj->query(); 
			   $err = $dbj->getErrorMsg(); if (!empty($err)) { echo $err; die('err config.php'); }
			   
			  }
			}
			
       require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			OPCmini::clearTableExistsCache(); 
		}
		
		function getlangerr()
		{
			
			
		  $this->loadVmConfig(); 
		  
		  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
		  
		  
		  if (!defined('VMLANG')) return array(); 
		  $le = array(); 
		  if (!$this->tableExists('virtuemart_paymentmethods_'.VMLANG))
		   {
		    $le[] = 'virtuemart_paymentmethods_'.VMLANG;
		   }
		  		  if (!$this->tableExists('virtuemart_categories_'.VMLANG))
		   {
		    $le[] = 'virtuemart_categories_'.VMLANG;
		   }
		  if (!$this->tableExists('virtuemart_manufacturercategories_'.VMLANG))
		   {
		    $le[] = 'virtuemart_manufacturercategories_'.VMLANG;
		   }
		  if (!$this->tableExists('virtuemart_manufacturers_'.VMLANG))
		   {
		    $le[] = 'virtuemart_manufacturers_'.VMLANG;
		   }
		  if (!$this->tableExists('virtuemart_products_'.VMLANG))
		   {
		    $le[] = 'virtuemart_products_'.VMLANG;
		   }
		  
		  if (!$this->tableExists('virtuemart_shipmentmethods_'.VMLANG))
		   {
		    $le[] = 'virtuemart_shipmentmethods_'.VMLANG;
		   }
		  
		  if (!$this->tableExists('virtuemart_vendors_'.VMLANG))
		   {
		    $le[] = 'virtuemart_vendors_'.VMLANG;
		   }
		  
		 return $le; 
		   
		}
		
		function getShipmentMethods()
		{
		$this->loadVmConfig(); 
		$onlyPublished = true; 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
		
		if (!defined('VMLANG')) define('VMLANG', 'en_gb');
		
			$where = array();
		if ($onlyPublished) {
			$where[] = ' `#__virtuemart_shipmentmethods`.`published` = 1';
		}

		$whereString = '';
		if (count($where) > 0) $whereString = ' WHERE '.implode(' AND ', $where) ;

		if ($this->tableExists('virtuemart_shipmentmethods_'.VMLANG))
		$table = 'virtuemart_shipmentmethods_'.VMLANG;
		else
		if ($this->tableExists('virtuemart_shipmentmethods_en_gb'))
		$table = 'virtuemart_shipmentmethods_en_gb';
		else
		$table = ''; 
		
		if (!empty($table))
		{
		$select = ' * FROM `#__'.$table.'` as l ';
		$joinedTables = ' JOIN `#__virtuemart_shipmentmethods`   USING (`virtuemart_shipmentmethod_id`) ';
		$joinedTables .= $whereString ;
		$q = 'SELECT '.$select.$joinedTables;
		
		}
		else
		$q = 'select * from #__virtuemart_shipmentmethods where published = 1'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		$err = $db->getErrorMsg(); 
		
		
		
		foreach ($res as $k=>$p)
		 {
		    $res[$k]['shipment_method_id'] = $p['virtuemart_shipmentmethod_id']; 
		    $res[$k]['shipment_method_name'] = $p['shipment_name']; 
		    $res[$k]['name'] = $p['shipment_name']; 
		 }
		
		return $res; 
		
		

		}
		

		function getPaymentMethods()
		{
		$this->loadVmConfig(); 
		$onlyPublished = true; 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
		
		if (!defined('VMLANG')) define('VMLANG', 'en_gb');
		
			$where = array();
		if ($onlyPublished) {
			$where[] = ' `#__virtuemart_paymentmethods`.`published` = 1';
		}

		$whereString = '';
		if (count($where) > 0) $whereString = ' WHERE '.implode(' AND ', $where) ;

		if ($this->tableExists('virtuemart_paymentmethods_'.VMLANG))
		$table = 'virtuemart_paymentmethods_'.VMLANG;
		else
		if ($this->tableExists('virtuemart_paymentmethods_en_gb'))
		$table = 'virtuemart_paymentmethods_en_gb';
		else
		$table = ''; 
		
		if (!empty($table))
		{
		$select = ' * FROM `#__'.$table.'` as l ';
		$joinedTables = ' JOIN `#__virtuemart_paymentmethods`   USING (`virtuemart_paymentmethod_id`) ';
		$joinedTables .= $whereString ;
		$q = 'SELECT '.$select.$joinedTables;
		
		}
		else
		$q = 'select * from #__virtuemart_paymentmethods where published = 1'; 
		$db = JFactory::getDBO(); 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		$err = $db->getErrorMsg(); 
		
		
		
		foreach ($res as $k=>$p)
		 {
		   $res[$k]['payment_method_id'] = $p['virtuemart_paymentmethod_id']; 
		   $res[$k]['payment_method_name'] = $p['payment_name']; 
		   $res[$k]['name'] = $p['payment_name']; 
		 }
		
		return $res; 
		
		

		}
		
		function getSC()
		{
		
		
		
	     $db = JFactory::getDBO();
		 $q = 'select * from #__virtuemart_countries where published = 1'; 
		
		 $db->setQuery($q);
		 $res = $db->loadAssocList();
		 
		 return $res;
		
		}
		
		function getShippingCountries()
		{
		return $this->getSC();
		
		
		}

	function install_ps_checkout()
	{
  		return true;
	}

    
    

	function install_ps_order()
	{
      return true;
	}
	function install($firstRun = false)
	{

	   return true;
	  
	}
	function getShippingRates()
	{
	  return array(); 
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
		   $keyname = $db->escape($keyname);
		   $q = 'select value from #__onepage_export_templates_settings where `keyname` = "'.$keyname.'" and `tid` = "'.$tid.'"';
		   $db->setQuery($q);
		   $res = $db->loadResult();
		   $value = $db->escape($value);
		   $msg = $db->getErrorMsg(); if (!empty($msg)) {JFactory::getApplication()->enqueueMessage($msg);return; }
		   if (!isset($res) || $res===false)
		   {
		    // ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , `tid` INT NOT NULL DEFAULT '0', `keyname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', `value` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', `original` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' )
		    $q = 'insert into #__onepage_export_templates_settings (`id`, `tid`, `keyname`, `value`, `original`) values (NULL, "'.$tid.'", "'.$keyname.'", "'.$value.'", ""); ';
		    
		   }
		   else
		   {
		    $q = 'update #__onepage_export_templates_settings set `value` = "'.$value.'" where `tid`="'.$tid.'" and `keyname`= "'.$keyname.'"';
		     //($res != $data[$k]))
		   }
		  
		   $db->setQuery($q);
		   $db->query();
		   $msg = $db->getErrorMsg(); if (!empty($msg)) {JFactory::getApplication()->enqueueMessage($msg); return;}
		   }
		  }
	
	}
	
	function getDefaultC()
	{
		
	 $dbj = JFactory::getDBO(); 
	   // array of avaiable country codes
	   if (!OPCJ3)
	   {
	   $q = "select virtuemart_country_id from #__virtuemart_userinfos as u, #__virtuemart_vmusers as v where v.virtuemart_vendor_id = '1' and v.user_is_vendor = 1 and v.perms = 'admin' limit 0,1";  
	   }
	   else
	   {
	   $q = "select virtuemart_country_id from #__virtuemart_userinfos as u, #__virtuemart_vmusers as v where v.virtuemart_vendor_id = '1' and v.user_is_vendor = 1 limit 0,1 ";  
	   }
	  $dbj->setQuery($q); 
	  $vendorcountry = $dbj->loadResult(); 

	   return $vendorcountry;
	   

		}
		
		function removeCache()
		{
		
		//stAn install debug: 

		
		   $dir = JPATH_SITE.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_onepage';
		   if (file_exists($dir))
		    {
			  $arr = @scandir($dir);
		 if (!empty($arr))
		 {
		  foreach ($arr as $file)
		  {
		   if (($file != 'overrides') && ($file != '.') && ($file != '..')) $ret[] = $file;
		  }
		 }
		 if (!empty($ret))
				foreach ($ret as $file)
				 {
				    JFile::delete($dir.DIRECTORY_SEPARATOR.$file); 
				 }
			}
		}
		
		function getTemplates()
		{
		 $dir = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes';;
		 $arr = @scandir($dir);
		 $ret = array();
		 
		 if (!empty($arr))
		 {
		  foreach ($arr as $file)
		  {
		   if (is_dir($dir.DIRECTORY_SEPARATOR.$file) && ($file != 'overrides') && ($file != '.') && ($file != '..')) $ret[] = $file;
		  }
		 }
		 return $ret;
		}
		function getClassNames()
		{
		return array(); 
		
    	}
    
    /**
 * strposall
 *
 * Find all occurrences of a needle in a haystack
 *
 * @param string $haystack
 * @param string $needle
 * @return array or false
 */
function strposall($haystack,$needle){
   
    $s=0;
    $i=0;
   
    while (is_integer($i)){
       
        $i = strpos($haystack,$needle,$s);
       
        if (is_integer($i)) {
            $aStrPos[] = $i;
            $s = $i+strlen($needle);
        }
    }
    if (isset($aStrPos)) {
        return $aStrPos;
    }
    else {
        return false;
    }
}

function retCss()
	{
		return ""; 	
	}

function retPhp()
	{
		return array(); 
	}

function tableExists($table)
{

 $dbj = JFactory::getDBO();
 $prefix = $dbj->getPrefix();
 $table = str_replace('#__', '', $table); 
 $table = str_replace($prefix, '', $table); 
 
  $q = "SHOW TABLES LIKE '".$dbj->getPrefix().$table."'";
	   $dbj->setQuery($q);
	   $r = $dbj->loadResult();
	   if (!empty($r)) return true;
 return false;

 $db = JFactory::getDBO();
 $q = "SHOW TABLES LIKE '".$db->getPrefix().$db->escape($table)."'";
 $db->setQuery($q);
 $r = $db->loadResult();
 if (!empty($r))
 return true;
 return false;
}
function createTempOrderTables()
{
 $db = JFactory::getDBO();
 if (!$this->tableExists('vm_orders_opctemp'))
 {
   $q = 'CREATE TABLE '.$db->getPrefix().'vm_orders_opctemp LIKE '.$db->getPrefix().'vm_orders';
   $db->setQuery($q);
   $db->query();
   $q = '';  
 }
 
}

// gets list of order statuses 
function getOrderStatuses()
{
  $db = JFactory::getDBO();
  $q = 'select * from #__virtuemart_orderstates where 1 limit 999';
  $db->setQuery($q);
  $res = $db->loadAssocList();
  if (empty($res)) return array();
  return $res; 
}

// get joomfish languages
function getJLanguages()
{
		$db = JFactory::getDBO();
	   $q = "SHOW TABLES LIKE '".$db->getPrefix()."languages'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	   
	   if (!empty($r))
	   {
	    if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		$q = "select lang_code from #__languages where 1 limit 999";
		else
	    $q = "select code from #__languages where 1 limit 999";
	    $db->setQuery($q);
	    $codes = $db->loadAssocList(); 
	   }
	   else $codes = array();
	   
	    if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
		foreach ($codes as $k=>$v)
		 {
		   $codes[$k]['code'] = $codes[$k]['lang_code'];
		 }
	   
	   return $codes;
}
function getPhpTrackingThemes()
{

  // stAn install debug: 

  $path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'; 

  		  if(!class_exists('JFile')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'file.php');
		  if(!class_exists('JFolder')) require(JPATH_LIBRARIES.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'folder.php');

  
  $files = JFolder::files($path, $filter = '.php', false, true);
  $arr = array(); 
  
 
  
  foreach ($files as $f)
  {
    $pi = pathinfo($f); 
	$file = $pi['filename']; 
	$jf = JFile::makesafe($file);
    // security here: 	
	if ($jf != $file) continue; 
	$path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.$file.'.xml'; 
	if (!file_exists($path)) continue; 
	$arr[] = $file; 
	
    
  }
  return $arr; 
  
}
/**
* Compiles a list of installed languages
*/
function getLanguages()
{
	

	// Initialize some variables
	$db		= JFactory::getDBO();
	 
	$path = JPATH_SITE.DIRECTORY_SEPARATOR.'language'; 
	$dirs = JFolder::folders( $path );

   $rows = array(); 
    $rowid = 0; 
	foreach ($dirs as $dir)
	{
		$files = JFolder::files( $path.DIRECTORY_SEPARATOR.$dir, '^([-_A-Za-z]*)\.xml$' );
		foreach ($files as $file)
		{
		    $file = str_replace('/', DS, $file); 
			//$data = JApplicationHelper::parseXMLLangMetaFile($path.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$file);

			$row 			= new StdClass();
			$row->id 		= $rowid;
			$row->language 	= substr($file,0,-4);

			/*
			foreach($data as $key => $value) {
				$row->$key = $value;
			}
			*/

			// if current than set published
			$params = JComponentHelper::getParams('com_languages');
			if ( $params->get('site', 'en-GB') == $row->language) {
				$row->published	= 1;
			} else {
				$row->published = 0;
			}

			$row->checked_out = 0;
			
			
			
            $row->short = $row->language;
			$rows[] = $row;
			$rowid++;
		}
	}
	return $rows; 
}

function getErrMsgs()
{
 $msg = ''; 
   $conf = JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_onepage".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."onepage.cfg.php";
   if ((file_exists($conf) && (!is_writable($conf))))
   $msg = 'File is not writable: '.$conf."<br />";
   
   $db = JFactory::getDBO(); 
   
   // check if there is ANY vendor within the shop
   $q = 'select * from #__virtuemart_vmusers where user_is_vendor = 1 and virtuemart_vendor_id <> 0'; 
   $db->setQuery($q); 
   $r = $db->loadAssocList(); 
   $novendor = false; 
   if (empty($r))
    {
	  $msg .= JText::_('COM_ONEPAGER_VENDOR_ERROR').' <br />'; 
	  $novendor = true; 
	}
	
	// more users marked as vendors are sharing the same vendor ID
	$arr = array(); 
	if (count($r)>1)
	{
	
	    foreach ($r as $vendor)
		 {
		   if (empty($arr[$vendor['virtuemart_vendor_id']])) $arr[$vendor['virtuemart_vendor_id']] = array(); 
		   $arr[$vendor['virtuemart_vendor_id']][] = $vendor['virtuemart_user_id']; 
		 }
	
		 foreach ($arr as $v_id => $users)
		  {
		     $count = count($users); 
			 $names = array(); 
		     if ($count > 1)
			   {
			      $msg .= 'PROBLEM: More than one user shares the same Vendor ID ('.$v_id.') which will lead to various problems<br />'; 
				  foreach ($users as $user_id)
				  {
				   
				    $q = 'select * from #__users where id = '.(int)$user_id.' limit 0,1'; 
				    $db->setQuery($q); 
				    $res = $db->loadAssoc(); 
					
				    if (empty($res))
					  {
					     $msg .= 'FIXED: User ID ('.$user_id.') in #__virtuemart_vmusers does not exists in #__users! OPC deactivates this vendor to fix further problems.<br />'; 
						 $q = 'update `#__virtuemart_vmusers` set `user_is_vendor` = "0", `virtuemart_vendor_id` = "0" where `virtuemart_user_id` = "'.(int)$user_id.'" and `user_is_vendor` = "1" and `virtuemart_vendor_id` = "'.$v_id.'" limit 1'; 
						 $db->setQuery($q); 
						 $db->query(); 
						 $msg .= $db->getErrorMsg(); 
						 $count--; 
					  }
					  else
					  $names[] = $res['username']; 
				  }
			   }
			  if ($count === 0)
			   {
			      $msg .= 'None of the vendors had a record in #__users and thus they all were deactivated. Deactivated users in #__virtuemart_users are: '.implode(', ', $users).'<br />'; 
			   }
			  if ($count > 1)
			   {
			      $msg .= 'MANUAL ACTION REQUIRED: There are still two vendors sharing the same virtuemart_vendor_id, please make sure that only one has virtuemart_vendor_id = 1 and user_is_vendor = 1 in your #__virtuemart_vmusers. List of original user_id\'s: '.implode(', ', $users).' with usernames ('.implode(', ', $names).') Having two or more vendors sharing the same Vendor ID will lead to unpredicted email or other issues. This also may be fixed by removing one of the users with Virtuemart user management.<br />'; 
			   }
			   
		  }
		 
	}
	
	
	$arr = array('airedirectwww', 'cache'); 
	foreach ($arr as $k=>$v)
	{
	  $q = "select * from `#__extensions` where `element`='".$db->escape($v)."' and `type`='plugin' and `enabled`=1 and `folder`='system'"; 
	  $db->setQuery($q); 
	  $res = $db->loadAssoc(); 
	  
	  if (!empty($res))
	  {
		  $msg .= 'Incompatible 3rd party plugin detected. This is only a notice and the underlying issue might have already been fixed by the developer. If you are having problems with your checkout, you may want to try to disable or reconfigure this plugin: '.JText::_($res['name']).' ('.$v.')<br />'; 
	  }
	}
	
	// note - user is marked as vendor, but has zero vendor id
	// this can lead either to make him a real vendor
	// OR to unmark him as a vendor
	$q = 'select * from #__virtuemart_vmusers where user_is_vendor = 1 and virtuemart_vendor_id = 0'; 
	$db->setQuery($q); 
	$res = $db->loadAssoc(); 
	if (!empty($res))
	 {
	   $q = 'select * from #__users where id = '.(int)$res['virtuemart_user_id'].' limit 0,1'; 
	   $db->setQuery($q); 
	   $juser = $db->loadAssoc(); 
	   
	    if (($novendor === false) || (empty($juser)))
		{
	     $q = 'update `#__virtuemart_vmusers` set `user_is_vendor` = "0", `virtuemart_vendor_id` = "0" where `virtuemart_user_id` = "'.(int)$res['virtuemart_user_id'].'" and `user_is_vendor` = "1" and virtuemart_vendor_id = 0'; 
		 $msg .= 'FIXED: A user ('.$res['virtuemart_user_id'].') was marked as a vendor, but had no Vendor ID associated. He was unmarked as vendor by OPC.'; 
		}
		else
		if ($novendor)
		{
		
		$q = 'update `#__virtuemart_vmusers` set `user_is_vendor` = "1", `virtuemart_vendor_id` = "1" where `virtuemart_user_id` = "'.(int)$res['virtuemart_user_id'].'" and `user_is_vendor` = "1" and virtuemart_vendor_id = 0'; 
		$msg .= 'FIXED: A user ('.$res['virtuemart_user_id'].') was marked as a vendor, but had no Vendor ID associated. Because OPC detected you had no valid vendors in your shop, this users was marked as your vendor. Please check your Virtuemart vendor settings closely.'; 
		}
		$db->setQuery($q); 
		$db->query(); 
		$msg .= $db->getErrorMsg(); 
		
	 }
	 // note: user is marked as vendor, but has no record in #__users - joomla
	 $q = 'select * from #__virtuemart_vmusers where user_is_vendor = "1"'; 
	 $db->setQuery($q); 
	 $res = $db->loadAssocList(); 
	 if (!empty($res))
	 foreach ($res as $user)
	  {
	     $q = 'select * from #__users where id = '.(int)$user['virtuemart_user_id'].' limit 0,1'; 
		 $db->setQuery($q); 
		 $juser = $db->loadAssoc(); 
		 if (empty($juser))
		  {
		     $msg .= 'Problem: A user ID ('.$user['virtuemart_user_id'].') in your #__virtuemart_vmusers is marked as vendor, but does not exists in #__users <br />'; 
			 $q = 'update `#__virtuemart_vmusers` set `user_is_vendor` = "0" where `virtuemart_user_id` = "'.(int)$user['virtuemart_user_id'].'" and `user_is_vendor` = "1" '; 
			 $db->setQuery($q); 
			 $db->query(); 
			 $msg .= $db->getErrorMsg(); 
			 $msg .= 'FIXED: A user ID ('.$user['virtuemart_user_id'].') in your #__virtuemart_vmusers was unmarked as vendor because he is not registered in #__users<br />'; 
		  }
		 
	  }
	 
	 
	
	/*
   $db = JFactory::getDBO(); 
   $q = 'select * from #__virtuemart_vmusers where user_is_vendor = 0 and virtuemart_vendor_id <> 0'; 
   $db->setQuery($q); 
   $r = $db->loadAssocList(); 
    */   

	
   
		if (empty($_SESSION['onepage_err']))
    	         $_SESSION['onepage_err'] = serialize($msg);
    	         else 
    	         {
    	          $_SESSION['onepage_err'] = serialize($msg.unserialize($_SESSION['onepage_err']));
    	         }

}

/* this function is from Virtuemart SVN for editing language files
*/

function getDecodeFunc($langCharset) {
	$func = 'strval';
	// get global charset setting
	$iso = explode( '=', @constant('_ISO') );
	// If $iso[1] is NOT empty, it is Mambo or Joomla! 1.0.x - otherwise Joomla! >= 1.5
	$charset = !empty( $iso[1] ) ? $iso[1] : 'utf-8';
	// Prepare the convert function if necessary
	if( strtolower($charset)=='utf-8' && stristr($langCharset, 'iso-8859-1' ) ) {
		$func = 'utf8_decode';
	} elseif( stristr($charset, 'iso-8859-1') && strtolower($langCharset)=='utf-8' ) {
		$func = 'utf8_encode';
	}
	if( !function_exists( $func )) {
		$func = 'strval';
	}
	return $func;
}







function check_syntax($file)
{
// load file
$code = file_get_contents($file);

$bom = pack("CCC", 0xef, 0xbb, 0xbf);
				if (0 == strncmp($code, $bom, 3)) {
					//echo "BOM detected - file is UTF-8\n";
					$code = substr($code, 3);
				}

// remove non php blocks
$x = 0; 
ob_start(); 
$f = @eval('$x = 1;'."?>$code"); 
$y = ob_get_clean(); 
return $x; 

}


function getLangVars()
{


  return array(); 
}
	
	function removepatchusps()
	{
		$msg = ''; 
	$file = JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'alatak_usps'.DIRECTORY_SEPARATOR.'alatak_usps.php'; 

		$file2 = str_replace('.php', '_opc_backup.php', $file); 			
if (JFile::copy($file2, $file)===false)
{
$msg = 'Could not copy '.$file2.' to '.$file.'<br />';
	
}
else 
{
	
if (JFile::delete($file2)===false)
{
$msg = 'Could not remove backup file'.$file2.'<br />';	
}

return $msg.'Patch was removed'; 
}
if (JFile::delete($file)===false)
{
$msg = 'Could not remove '.$file.'<br />';	
}
if (JFile::move($file2, $file)===false)
{
$msg = 'Could not move '.$file2.' to '.$file.'<br />';
	
}
else return 'Patch was removed'; 

if (!empty($msg))
{
  $msg .= 'Please restore the original file '.$file.' from '.$file2;	
  
}
return $msg; 



	}		
	
	function installShopfunctions(&$msg) {
	if(version_compare(JVERSION,'3.5.1','ge')) {
	   if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'shopfunctionsf'.DIRECTORY_SEPARATOR.'shopfunctionsf.php')) 
	   {
		 JFile::copy(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.bck.'.time().'.php'); 
	     $this->installExt(-1, 'shopfunctionsf.php', $msg); 
	   }
		}
	}
	
	function patchusps()
	{
		  jimport( 'joomla.filesystem.folder' );
		  jimport( 'joomla.filesystem.file' );
		
		$file = JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmshipment'.DIRECTORY_SEPARATOR.'alatak_usps'.DIRECTORY_SEPARATOR.'alatak_usps.php'; 
		if (file_exists($file))
		{
$file2 = str_replace('.php', '_opc_backup.php', $file); 			
if (JFile::copy($file, $file2)===false)
{
$msg = 'Could not copy '.$file.' to '.$file2. ' patch wasn\'t applied';
return $msg; 	
}
		  $data = file_get_contents($file); 	
		  $data = str_replace("\r\r\n", "\r\n", $data); 
		  $data = str_replace('function _sendRequest', "\r\n\tstatic ".'$uspsCache;'." \r\n\tfunction _sendRequest", $data); 
		  $x1 = strpos($data, 'function _sendRequest'); 
		  $x2 = strpos($data, '{', $x1); 
		  $x3 = strpos($data, 'return true;', $x2); 
		  $data2 = substr($data, 0, $x2+1)."\r\n".'
	if (!empty(plgVmShipmentAlatak_USPS::$uspsCache))
		if (isset(plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost]))
		{
			if (isset(plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost][\'method\']))
			if (plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost][\'method\'] == $method)
			{
				$xmlResult = plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost][\'result\']; 
				return  true; 
			}
		}			  
		'.substr($data, $x2+1, $x3-($x2+1))."\r\n".'
		if (empty(plgVmShipmentAlatak_USPS::$uspsCache)) plgVmShipmentAlatak_USPS::$uspsCache = array(); 
		if (empty(plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost])) plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost] = array(); 
		plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost][\'method\'] = $method;
		plgVmShipmentAlatak_USPS::$uspsCache[$xmlPost][\'result\'] = $xmlResult; 		
		
		
		'.substr($data, $x3); 
		  
		if (JFile::write($file, $data2)===false)
		{
			$msg = 'Could not write to '.$file;
			return $msg; 
		}
		else
		{
			$msg = 'Patch applied in '.$file;
			return $msg; 
			
		}
		}
		
	}
	
	/**
	 * Joomla modified function from installer.php file of /libraries/joomla/installer.php
	 *
	 * Method to extract the name of a discreet installation sql file from the installation manifest file.
	 *
	 * @access	public
	 * @param	string  $file 	 The SQL file
	 * @param	string	$version	The database connector to use
	 * @return	mixed	Number of queries processed or False on error
	 * @since	1.5
	 */
	function parseSQLFile($file)
	{
		// Initialize variables
		$queries = array();
		$db =  JFactory::getDBO();
		$dbDriver = strtolower($db->get('name'));
		if ($dbDriver == 'mysqli') {
			$dbDriver = 'mysql';
		}
		$dbCharset = ($db->hasUTF()) ? 'utf8' : '';

		if (!file_exists($file)) return 0;

		// Get the array of file nodes to process

		// Get the name of the sql file to process
		$sqlfile = '';
			// we will set a default charset of file to utf8 and mysql driver
			$fCharset = 'utf8'; //(strtolower($file->attributes('charset')) == 'utf8') ? 'utf8' : '';
			$fDriver  = 'mysql'; // strtolower($file->attributes('driver'));

			if( $fCharset == $dbCharset && $fDriver == $dbDriver) {
				$sqlfile = $file;
				// Check that sql files exists before reading. Otherwise raise error for rollback

				$buffer = file_get_contents($file);

				// Graceful exit and rollback if read not successful
				if ( $buffer === false ) {
					return false;
				}

				// Create an array of queries from the sql file
				jimport('joomla.installer.helper');
				$queries = JInstallerHelper::splitSql($buffer);

				if (count($queries) == 0) {
					// No queries to process
					return 0;
				}

				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							JError::raiseWarning(1, 'JInstaller::install: '.JText::_('SQL Error')." ".$db->stderr(true));
							return false;
						}
					}
				}
			}
		

		return (int) count($queries);
	}


		
	}

