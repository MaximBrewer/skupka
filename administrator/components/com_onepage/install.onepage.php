<?php
/**
 * OPC script file
 *
 * This file is executed during install/upgrade and uninstall
 *
 * @author stAn, RuposTel s.r.o.
 * @package One Page Checkout
 *
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
*/
defined('_JEXEC') or die('Restricted access');


jimport( 'joomla.application.component.model');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// hack to prevent defining these twice in 1.6 installation
if (!defined('_OPC_SCRIPT_INCLUDED')) {
	define('_OPC_SCRIPT_INCLUDED', true);


	/**
	 * OPC custom installer class
	 */
	class com_onepageInstallerScript {

	  public function preflight()
		{
			if (function_exists('ignore_user_abort'))
			@ignore_user_abort(true); 
		    if (function_exists('set_time_limit'))
			@set_time_limit(3600); 
		    if (function_exists('ini_set'))
			@ini_set('max_execution_time', 3600);
			
			jimport('joomla.installer.installer');
			$installer =  JInstaller::getInstance();
				if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'))
		 {
			
		  
			
		
		  $installer->set('message', 'Virtuemart 2 not found !  If you are trying to install One Page Checkout for Virtuemart 1.1.x, please download the proper version from <a href="http://www.rupostel.com/">RuposTel.com site</a>');
		   $installer->abort('Virtuemart 2 not found !  If you are trying to install One Page Checkout for Virtuemart 1.1.x, please download the proper version from <a href="http://www.rupostel.com/">RuposTel.com site</a>'); 
		   //echo 'Virtuemart 2 not found !  If you are trying to install One Page Checkout for Virtuemart 1.1.x, please download the proper version from <a href="http://www.rupostel.com/">RuposTel.com site</a>'; 
		   return false; 
		 }
		 
		 // check file permissions: 
		 jimport('joomla.filesystem.folder');
		 jimport('joomla.filesystem.file');
		 
		 $tmp_path = JFactory::getConfig()->get('tmp_path'); 
		if (!JFolder::exists($tmp_path))
		{
			$installer->abort('Your tmp_path ('.$tmp_path.')in your configuration.php points to non existing directory. Please fix your joomla global configuration.');
 			return false; 
		}
		$data = ' '; 
		if (!JFile::write($tmp_path.DIRECTORY_SEPARATOR.'test.html', $data))
		{
			$installer->abort('Your tmp_path ('.$tmp_path.')in your configuration.php is not writable! ');
 			return false; 
		}
		@JFile::delete($tmp_path.DIRECTORY_SEPARATOR.'test.html');

		
		 $errors = ''; 
		 $rand = rand(1000, 10000); 
		 $rand = $rand.'.html'; 
		 $buffer = 'OPC installation tests'; 
		 // check plugin directory
		 if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'))
		 {
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc')===false)
		  {
		    $errors .= 'Cannot create OPC plugin directory in /plugins/system/opc/<br />'; 
		  }
		  else
		  {
			  @JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc');
		  }
		 }
		 else
		 {
			if (@JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC plugin directory in /plugins/system/opc/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.$rand); 
			}
		 }
		 
		 // let's install opctracking plugin: 
		 if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'))
		 {
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking')===false)
		  {
		    $errors .= 'Cannot create OPC plugin directory in /plugins/vmpayment/opctracking/<br />'; 
		  }
		  else
		  {
			  @JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking');
		  }
		 }
		 else
		 {
			if (@JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC plugin directory in /plugins/vmpayment/opctracking/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.$rand); 
			}
		 }
		 
		 
		 // let's install opctracking plugin: 
		 
		 if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'))
		 {
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart')===false)
		  {
		    $errors .= 'Cannot create OPC plugin directory in /plugins/system/opccart/<br />'; 
		  }
		  else
		  {
			  @JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart');
		  }
		 }
		 else
		 {
			if (@JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC plugin directory in /plugins/system/opccart/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.$rand); 
			}
		 }
		 
		 
		  // let's install opcregistration plugin: 
		 if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'))
		 {
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration')===false)
		  {
		    $errors .= 'Cannot create OPC plugin directory in /plugins/system/opcregistration/<br />'; 
		  }
		  else
		  {
			  @JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration');
		  }
		 }
		 else
		 {
			if (@JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC plugin directory in /plugins/system/opcregistration/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.$rand); 
			}
		 }
		 
		 
		 // check FE component directory
		 if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'))
		 {
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage')===false)
		  {
		    $errors .= 'Cannot create OPC frontend directory in /components/com_onepage/<br />'; 
		  }
		  else
		  {
			  @JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage');
		  }
		  }
		 		 else
		 {
			if (@JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC frontend directory in /components/com_onepage/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.$rand); 
			}
		 }
		// check BE component directory
		 if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'))
		 {
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage')===false)
		  {
		    $errors .= 'Cannot create OPC backend directory in /administrator/components/com_onepage/<br />'; 
		  }
		  }
		 else
		 {
			if (@JFile::write(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.$rand, $buffer)===false)
			{
				$errors .= 'Cannot write to OPC backend directory in /administrator/components/com_onepage/<br />'; 
			}
			else
			{
				@JFile::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.$rand); 
			}
		 }

		
		 if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.'opchtml'))
		 {
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.'opchtml')===false)
		  {
		    $errors .= 'Cannot create OPC ajax document helper directory in /libraries/joomla/document/opchtml/<br />'; 
		  }
		  else
		  {
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.'opchtml');  			  
		  }
		 }
		 
		
		//deprecated plugins: 
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc_shipping_last'))
		@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc_shipping_last');
		
		
			    $db = JFactory::getDBO(); 
				$q = "delete from `#__extensions` where `element` = 'opc_shipping_last' and `folder` = 'system'"; 
				$db->setQuery($q); 
				$db->query(); 
				
							
				
				if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctracking'))
		        @JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctracking');
				
				$db = JFactory::getDBO(); 
				$q = "delete from `#__extensions` where `element` = 'opctracking' and `folder` = 'system'"; 
				$db->setQuery($q); 
				$db->query(); 
				

		 
		  
		  if (!empty($errors))
		  {
			  $installer->abort('<div style="margin-top: 20px; margin-bottom: 20px; color: white;">'.$errors.'Please ignore other messages printed here by Joomla. Please update your permissions and try again.</div>'); 
			  
			  return false; 
		  }
		  
		 
			return true; 
		}

		/**
		 * Install script
		 * Triggers after database processing
		 *
		 * @param object JInstallerComponent parent
		 * @return boolean True on success
		 */
		public function install () {

		jimport('joomla.installer.installer');
		$installer =  JInstaller::getInstance();
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'))
		 {
		   echo 'Virtuemart 2 not found !  If you are trying to install One Page Checkout for Virtuemart 1.1.x, please download the proper version from <a href="http://www.rupostel.com/">RuposTel.com site</a>'; 
		   $installer->set('message', 'Virtuemart 2 not found !  If you are trying to install One Page Checkout for Virtuemart 1.1.x, please download the proper version from <a href="http://www.rupostel.com/">RuposTel.com site</a>');
		   return false; 
		 }

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		$path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage';


		$source 	= $installer->getPath('source');
		// installs the themes
		if (substr($source, strlen($source)) != DIRECTORY_SEPARATOR) $source .= DIRECTORY_SEPARATOR;

		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'))
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/system/opc/<br />'; 
		  }
		
		/*
		if (@JArchive::extract($source.'opcsystem.zip',JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR)===false)
		{
		  echo 'Cannot extract OPC system plugin to /plugins/system/opc<br />'; 
		}
		*/

		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc')) {
		  if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc')===false) {
		   echo 'Cannot create OPC system plugin directory'; 
		  }
		}
		
		if (@JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'opc.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'opc.php')===false) {
		  echo 'Cannot copy OPC system plugin !'; 
		}
		
	    @JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'opc.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'opc.xml'); 		
	    @JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'index.html'); 		
		
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'))
		 {
		  if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config')===false)
		   echo 'Cannot create config directory in /components/com_onepage/config<br />'; 
		 }
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
		 {
		   JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'onepage.cfg.php', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		 }

		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'))
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/vmpayment/opctracking/<br />'; 
		  }
		 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'index.html'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.php'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.xml'); 
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'))
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/system/opccart/<br />'; 
		  }
		  
		
		
		
		
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'index.html'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'opccart.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'opccart.php'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'carthelper.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'carthelper.php'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'opccart.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'opccart.xml'); 
		

		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'))
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/system/opcregistration/<br />'; 
		  }
		
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'index.html'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'opcregistration.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'opcregistration.php'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'opcregistration.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'opcregistration.xml'); 

		
		$db = JFactory::getDBO(); 
		$q = "select * from `#__extensions` where `name` = 'plg_system_onepage' and element = 'onepage' ";
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) 
		 {
		    $q = " UPDATE `#__extensions` SET  enabled =  '0' WHERE  element = 'plg_system_onepage' and folder = 'system' "; 
			$db->setQuery($q); 
			$db->query(); 
			//echo 'Disabled Linelab One Page Checkout extension in Plugin Manager <br />'; 
		 }
		 

		 
		// we renamed the plugin so we don't have cross compatiblity issues with linelab opc 
		$db = JFactory::getDBO(); 
		$q = "select * from `#__extensions` where name = 'plg_system_onepage' and element = 'opc' ";
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) 
		 {
		    $q = "delete from `#__extensions` WHERE  element = 'plg_system_onepage' and element = 'opc' "; 
			$db->setQuery($q); 
			$db->query(); 
			echo 'Renamed OPC plugin from plg_system_onepage to plg_system_opc <br />'; 
		 }

		 
		$db = JFactory::getDBO(); 
		$q = "select * from `#__extensions` where `name` = 'plg_system_opc' and `folder` = 'system' ";
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		
		if (empty($res))
		{
		$q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
		$q .= " (NULL, 'plg_system_opc', 'plugin', 'opc', 'system', 0, 0, 1, 0, '{\"legacy\":false,\"name\":\"plg_system_opc\",\"type\":\"plugin\",\"creationDate\":\"December 2011\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"1.7.0\",\"description\":\"One Page Checkout for VirtueMart 2\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0) "; 
		$db->setQuery($q); 
		$db->query(); 
		}
		else
		{
		 if (count($res)>1) echo 'More then one instance of onepage system plugin found! Please delete one of them manually.'; 
		}

		return true;
		}


		/**
		 * Update script
		 * Triggers after database processing
		 *
		 * @param object JInstallerComponent parent
		 * @return boolean True on success
		 */
		public function update () {
			jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		$path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR;

		jimport('joomla.installer.installer');
		$installer = JInstaller::getInstance();

		$source 	= $installer->getPath('source');
		// installs the themes
		if (substr($source, strlen($source)) != DIRECTORY_SEPARATOR) $source .= DIRECTORY_SEPARATOR;
		 /*
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'))
		 JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'); 
		
		if (JArchive::extract($source.'opcsystem.zip',JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR)===false)
		{
		  echo 'Cannot extract OPC system plugin to /plugins/system/opc <br />'; 
		}
		*/
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc')) {
		  if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc')===false) {
		   echo 'Cannot create OPC system plugin directory'; 
		  }
		}
		if (@JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'opc.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'opc.php')===false) {
		  echo 'Cannot copy OPC system plugin !'; 
		}
		
	    @JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'opc.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'opc.xml'); 		
	    @JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'.DIRECTORY_SEPARATOR.'index.html'); 		
		

		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'))
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/vmpayment/opctracking/<br />'; 
		  }
		 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'index.html'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.php'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'.DIRECTORY_SEPARATOR.'opctracking.xml'); 
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'))
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/system/opccart/<br />'; 
		  }
	
	if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'))
		 if (@JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration')===false)
		  {
		    echo 'Cannot create OPC plugin directory in /plugins/system/opcregistration/<br />'; 
		  }
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'index.html'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'opcregistration.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'opcregistration.php'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'opcregistration.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'.DIRECTORY_SEPARATOR.'opcregistration.xml'); 
		  
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'index.html', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'index.html'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'carthelper.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'carthelper.php'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'opccart.php', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'opccart.php'); 
		JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'opccart.xml', JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'.DIRECTORY_SEPARATOR.'opccart.xml'); 
		


		
		$db = JFactory::getDBO(); 
		$q = "select * from `#__extensions` where `name` = 'plg_system_onepage' and `element` = 'onepage' ";
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) 
		 {
		    $q = " UPDATE `#__extensions` SET  enabled =  '0' WHERE  name = 'plg_system_onepage' and folder = 'system' and element = 'onepage' "; 
			$db->setQuery($q); 
			$db->query(); 
			//echo 'Disabled Linelab One Page Checkout extension in Plugin Manager <br />'; 
		 }
		 
		//update from prior opc versions: 
		$db = JFactory::getDBO(); 
		$q = "delete from `#__extensions` WHERE  element = 'opctracking' and folder = 'system' "; 
		$db->setQuery($q); 
		$db->query(); 


		 
		// we renamed the plugin so we don't have cross compatiblity issues with linelab opc 
		$db = JFactory::getDBO(); 
		$q = "select * from #__extensions where name = 'plg_system_onepage' and element = 'opc' ";
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (!empty($res)) 
		 {
		    $q = " delete from `#__extensions` WHERE  name = 'plg_system_onepage' and element = 'opc' "; 
			$db->setQuery($q); 
			$db->query(); 
			echo 'Renamed OPC plugin from plg_system_onepage to plg_system_opc <br />'; 
		 }

		 
	

		
		
		
		$db = JFactory::getDBO(); 
		$q = 'select * from #__extensions where element = "opc" and name="plg_system_opc" limit 999'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		if (empty($res))
		{
		
		$q = ' INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES ';
		$q .= " (NULL, 'plg_system_opc', 'plugin', 'opc', 'system', 0, 0, 1, 0, '{\"legacy\":false,\"name\":\"plg_system_opc\",\"type\":\"plugin\",\"creationDate\":\"December 2011\",\"author\":\"RuposTel s.r.o.\",\"copyright\":\"RuposTel s.r.o.\",\"authorEmail\":\"admin@rupostel.com\",\"authorUrl\":\"www.rupostel.com\",\"version\":\"1.7.0\",\"description\":\"One Page Checkout for VirtueMart 2\",\"group\":\"\"}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0) "; 
		$db->setQuery($q); 
		$db->query(); 
		}
		
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
		 {
		   JFile::copy($source.'admin'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'onepage.cfg.php', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		 }
		
			$q = 'update `#__extensions` set `enabled` = 1, `state` = 0 where element = "com_onepage" '; 
			$db->setQuery($q); 
			$db->query(); 

		
		

			return true; 
		}



		/**
		 * Uninstall script
		 * Triggers before database processing
		 *
		 * @param object JInstallerComponent parent
		 * @return boolean True on success
		 */
		public function uninstall ($parent=null) {
			jimport('joomla.filesystem.folder');
		    jimport('joomla.filesystem.file');
		    jimport('joomla.filesystem.archive');
			 
			//@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmextended'.DIRECTORY_SEPARATOR.'opc'); 
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc');
		
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opctracking');
			
				if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctracking'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctracking');
			
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctrackingsystem'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opctrackingsystem');
			
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opccart');
			
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcregistration');

			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcnumbering'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opcnumbering');
		
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc_currency'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc_currency');
		
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc_shipping_last'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'opc_shipping_last');
		
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opc_shipping_last'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpayment'.DIRECTORY_SEPARATOR.'opc_shipping_last');
		
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'vmarticle'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'vmarticle');
		
		
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'producttabs'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'producttabs');
		
			
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.'opchtml'))
			@JFolder::delete(JPATH_SITE.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.'opchtml'); 
			
			$db = JFactory::getDBO(); 
			$q = "delete from #__extensions where element = 'opc' or element='opc_shipping_last' or element='opc_currency' or element='opcnumbering' or element='opcregistration' or element='opctrackingsystem' "; 
			$db->setQuery($q); 
			$db->query(); 
			
			$db = JFactory::getDBO(); 
			$q = "delete from #__extensions where element = 'opctracking' limit 5"; 
			$db->setQuery($q); 
			$db->query(); 
			
			
			
			/*
			$q = "delete from #__assets where alias = 'com-onepage'"; 
			$db->setQuery($q); 
			$db->query(); 
			*/
			$q = "drop table if exists #__vmtranslator_translations"; 
			$db->setQuery($q); 
			$db->query(); 

			$q = "drop table if exists #__onepage_config"; 
			$db->setQuery($q); 
			$db->query(); 
			
			$q = "drop table if exists #__virtuemart_plg_opctracking"; 
			$db->setQuery($q); 
			$db->query(); 
			
			
			$db = JFactory::getDBO(); 
				$q = "delete from `#__extensions` where `element` = 'opccart' and `folder` = 'system'"; 
				$db->setQuery($q); 
				$db->query();
			
			$db = JFactory::getDBO(); 
				$q = "delete from `#__extensions` where `element` = 'plg_system_opccart' and `folder` = 'system'"; 
				$db->setQuery($q); 
				$db->query();
				
				
				$db = JFactory::getDBO(); 
				$q = "delete from `#__extensions` where `element` = 'vmarticle' and `folder` = 'system'"; 
				$db->setQuery($q); 
				$db->query();

				//producttabs
				$db = JFactory::getDBO(); 
				$q = "delete from `#__extensions` where `element` = 'producttabs' and `folder` = 'system'"; 
				$db->setQuery($q); 
				$db->query();
				
				
				$db = JFactory::getDBO(); 
				$q = "delete from `#__extensions` where `element` = 'opccart' and `folder` = 'system'"; 
				$db->setQuery($q); 
				$db->query();	
			
			
			return true;
		}

		/**
		 * Post-process method (e.g. footer HTML, redirect, etc)
		 *
		 * @param string Process type (i.e. install, uninstall, update)
		 * @param object JInstallerComponent parent
		 */
		public function postflight ($type, $parent=null) {
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'api.php')) 
			{
				include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'api.php'); 
				
				if ($api_key !== '0_0') {
				 $db = JFactory::getDBO(); 
				 $q = "update #__update_sites set location = Replace(location, '[license_key]', '".$db->escape($api_key)."') where location like '%rupostel.com%'"; 
				 $db->setQuery($q); 
				 $db->query(); 
				}
			}

			return true;
		}

		

	}

	/**
	 * Legacy j1.5 function to use the 1.6 class install/update
	 *
	 * @return boolean True on success
	 * @deprecated
	 */
	function com_install() {
	 if(version_compare(JVERSION,'1.7.0','ge')) {
	 return true; 
// Joomla! 1.7 code here
} elseif(version_compare(JVERSION,'1.6.0','ge')) {
	 return true; 
// Joomla! 1.6 code here
} elseif(version_compare(JVERSION,'2.5.0','ge')) {
	 return true; 
// Joomla! 2.5 code here
} else {
// Joomla! 1.5 code here

		//joomla 1.5 code is removed
		 return true; 
		}
	}

	/**
	 * Legacy j1.5 function to use the 1.6 class uninstall
	 *
	 * @return boolean True on success
	 * @deprecated
	 */
	function com_uninstall() {
	 if(version_compare(JVERSION,'1.7.0','ge')) {
// Joomla! 1.7 code here
} elseif(version_compare(JVERSION,'1.6.0','ge')) {
// Joomla! 1.6 code here
} else {
// Joomla! 1.5 code here

			  //joomla 1.5 code is removed
			return true;
}
	}

} // if(defined)


