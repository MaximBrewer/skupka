<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

// hack to prevent defining these twice in 1.6 installation
if (!defined('_NOT_INSTALL_TWICE')) {
	
define('_NOT_INSTALL_TWICE', true);
	
jimport('joomla.installer.installer');
if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}
class com_NetBaseVm_ExtendInstallerScript {
	
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	public function preflight($type, $parent=null)
	{
		
		if ($type!='uninstall') {
			
			echo '<p>Checking minimum requirements...';
			
			// Check Joomla! version
			if(version_compare(JVERSION,'1.5.0','lt'))
			{
				echo '<b style="color:red;">FAILED</b></p>';
				JError::raiseWarning(1, 'Component NetBase Virtuemart Extend is only compatible with Joomla! 1.5 and above.');
				return false;
			}
			
			// Check VirtueMart 2.0.6+ is installed
			$vmVersion = $this->getInstalledVersion('virtuemart');
			if (!$vmVersion)
			{
				echo '<b style="color:red;">FAILED</b></p>';
				JError::raiseWarning(1, 'VirtueMart is not installed or your version is not supported.');
				return false;
			}
			
			// Filter only number in a string
			$vmVersion = filter_var($vmVersion, FILTER_SANITIZE_NUMBER_INT);
			//echo $vmVersion;die;
			//if ($vmVersion < '2.0.6')

			if($vmVersion < 206)
			{
				echo '<b style="color:red;">FAILED</b></p>';
				JError::raiseWarning(1, 'Component NetBase Virtuemart Extend is only compatible with VirtueMart 2.0.6 and up!');
				return false;
			}
			
			// Check component installation directory perms
			$destination = JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_netbasevm_extend' . DS;
			$buffer = "installing";
			if(!JFile::write($destination.'installer.dummy.ini', $buffer))
			{
				echo '<b style="color:red;">FAILED</b></p>';
				JError::raiseWarning(1, 'There was an error while trying to create an installation file.<br />Please ensure that the path <strong>'.$destination.'</strong> has correct permissions and try again.');
				return false;
			}
			
			// Check PHP version
			$phpVersion = floatval(phpversion());
			if($phpVersion < 5)
			{
				echo '<b style="color:red;">FAILED</b></p>';
				ob_start();
				?>
				<table width="100%" border="0">
					<tr>
						<td style="color:red; font-weight:700">				
							Installation Error.
						</td>
					</tr>
					<tr>
						<td>
							Installation could not proceed any further because we detected that your site is using an unsupported version of PHP
						</td>
					</tr>
					<tr>
						<td>
							Component NetBase Virtuemart Extend only supports <strong>PHP5</strong> and above. Please upgrade your PHP version and try again.
						</td>
					</tr>
				</table>
				<?php
				ob_end_flush();
				return false;
			}
			
			echo '<b style="color:green;">Done</b></p>';
		}
		
		
		return true;
	}
	
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	public function install($parent=null)
	{	
		/*
		* Create tables with language actived now
		*/
			/*
			echo '<p>Creating language tables...';
			
			if(!class_exists('NbConfig')) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_netbasevm_extend'.DS.'helpers'.DS.'config.php');
			if(!class_exists('GenericTableUpdater')) require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_netbasevm_extend'.DS.'helpers'.DS.'tableupdater.php');
			$updater = new GenericTableUpdater();
			$updater->createLanguageTables();
			
			echo '<b style="color:green;">Done</b></p>';
			*/
		
		return true;
	}
	
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent=null)
	{
		
		echo '<p>Removing VirtueMart menu...';
		$this->removeVmMenuEntries();
		echo '<b style="color:green;">Done</b></p>';
		
		/* 
		* Remove all data add to table Virtuemart
		*/
		
			/*
				echo '<p>Removing data from VM tables...';
				if ($this->removeDigiTollDownloadsData($parent))
				{
					echo '<b style="color:green;">Done</b>';
				}
				else
				{
					$status = false;
					echo '<b style="color:red;">FAILED</b>';
				}
				echo '</p>';
			*/
		
		/*
		* Uninstall all module , plugins in folder extentions installed
		*/
			
			
				echo '<p>Uninstalling extensions...';
				
				$status = true;
				$extFolder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_netbasevm_extend'.DS.'extensions';
				$exts = JFolder::folders($extFolder);
				foreach ($exts as $ext)
				{
					echo '<br />Uninstalling '.$ext.'...';
					
					$installer = new JInstaller();
					
					$type = $this->getExtensionType($ext);
					$folder = $this->getExtensionFolder($ext);
					$element = $this->getExtensionElement($ext);
					$eid = $this->getExtensionId($ext);
					
					if ($installer->uninstall($type, $eid))
					{
						echo '<b style="color:green;">Done</b>';
					}
					else
					{
						$status = false;
						echo '<b style="color:red;">FAILED</b>';
					}
				}
				
				echo '</p>';
			
			
		
		/*
		* Remove talbes added when installed done
		*/	
			
				echo '<p>Removing tables...';
				$q = 'SHOW TABLES LIKE \'%nborders%\'';
				$db = JFactory::getDBO();
				$db->setQuery($q);
				$tables = $db->loadColumn();
				$q = '';
				foreach ($tables as $table) {
					$q .= 'DROP TABLE `' . $table . '`; ';
				}
				$db->setQuery($q);
				if ($db->queryBatch())
					echo '<b style="color:green;">Done</b>';
				else
					echo '<b style="color:red;">FAILED</b>';
				echo '</p>';
			
		/*
		* Show status unstall done or not
		*/
		if ($status)
			echo '<p><b>Uninstallation successful!</b></p>';
		else
			echo '<p><b>Uninstallation completed with errors!</b></p>';
			
		return true;
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent=null)
	{
		//die;
		$dtVer = $this->getInstalledVersion('netbasevm_extend');
		
		/*
		* Update versions
		*/
			if (version_compare($dtVer, '1.0.1', 'le'))
				$this->update101($parent);
			
			if (version_compare($dtVer, '1.0.2', 'le'))
				$this->update102($parent);
			
		return true;
	}
	
	/** method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent=null)
	{
		
		if ($type!='uninstall') {
			
			$db = JFactory::getDBO();
			$status = true;
			
			/*
			* Install add plugins in folder extension
			*/
			
				
					//echo '<p>Installing extensions...';
					
					$extFolder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_netbasevm_extend'.DS.'extensions';
					$exts = JFolder::folders($extFolder);
					foreach ($exts as $ext)
					{
						//echo '<br />Installing '.$ext.'...';
						
						$installer = new JInstaller();
						
						// Install or update the extension
						if ($this->getExtensionId($ext) == '' || version_compare(JVERSION,'1.6.0','lt')) {
							$result = $installer->install($extFolder.DS.$ext);
							
							// Enable the extension only on fresh installation
							if ($result) {
								$element = $this->getExtensionElement($ext);
								$manifest = $installer->getManifest();
								if(version_compare(JVERSION,'1.6.0','lt')) {
									$etype = $this->getExtensionType($ext);
									switch ($etype) {
										case 'component' : $query = "UPDATE `#__components` SET `enabled`=1 WHERE `option`='$element'"; break;
										case 'module' : $query = "UPDATE `#__modules` SET `published`=1 WHERE `module`='$element'"; break;
										case 'plugin' : $query = "UPDATE `#__plugins` SET `published`=1 WHERE `element`='$element' AND `folder`=".$db->quote($manifest->document->attributes('group')); break;
									}
								} else {
									$query = $db->getQuery(true);
									$query->update('#__extensions')
									 ->set('enabled = '.$db->quote('1'))
									 ->where('type = '.$db->quote($manifest->getAttribute('type')))
									 ->where('folder = '.$db->quote($manifest->getAttribute('group')))
									 ->where('element = '.$db->quote($element));
								}
								
								$db->setQuery($query);
								$db->execute();
							}
						} else {
							$result = $installer->update($extFolder.DS.$ext);
						}
						
						if ($result)
						{
							//echo '<b style="color:green;">Done</b>';
						}
						else 
						{
							$status = false;
							echo '<b style="color:red;">FAILED</b>';
						}
					}
					
					echo '</p>';
				
			
			
			/*
			* End
			*/
			
			// Create menu to show below menu components in admin
				//echo '<p>Updating menu...';
				if(version_compare(JVERSION,'1.6.0','lt')) {
					$q = "UPDATE `#__components` SET `admin_menu_link`='option=com_netbasevm_extend&controller=nborders' WHERE `option`='com_netbasevm_extend'";
				} else {
					$q = "UPDATE `#__menu` SET `link`='index.php?option=com_netbasevm_extend&controller=nborders' WHERE `title`='com_netbasevm_extend'";
				}
				$db->setQuery($q);
				if ($db->execute()) {
					//echo '<b style="color:green;">Done</b>';
				} else {
					echo '<b style="color:red;">FAILED</b>';
				}
				
				echo '</p>';
			
			// Create menu show in com_virtuemart
				if ($type=='install') {
					//echo '<p>Creating VirtueMart menus...';
					$this->addVmMenuEntries();
					//echo '<b style="color:green;">Done</b></p>';
				}
			
			// status install all done or not
			if ($status)
				echo '<p><b>Installation successful!</b></p>';
			else
				echo '<p><b>Installation completed with errors!</b></p>';
				
				
		}
		
		return true;
	}
	
	/*
	* Get version current of Virtuemart installed.
	*/
	public function getInstalledVersion($element, $type='component', $folder=false) {
		
		switch ($type) {
			case 'component' : $path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_'.$element.DS.$element.'.xml'; break;
			case 'plugin' : version_compare(JVERSION,'1.6.0','lt') ? $path = JPATH_ROOT.DS.'plugins'.DS.$folder.DS.$element.DS.$element.'.xml' : $path = JPATH_ROOT.DS.'plugins'.DS.$folder.DS.$element.DS.$element.DS.$element.'.xml'; break;
			case 'module' : $path = JPATH_ROOT.DS.'modules'.DS.'mod_'.$element.DS.$element.'.xml'; break;
		}
		if(version_compare(JVERSION,'1.6.0','lt')) {
			$manifest = new JSimpleXML();
			if (!file_exists($path))
				return false;
			$manifest->loadFile($path);
			$version = $manifest->document->version[0]->data();
		} else {
                   
//			jimport( 'joomla.installer.packagemanifest' );
//			$manifest = new JPackageManifest();
//			if (!file_exists($path))
//				return false;
//			$manifest->loadManifestFromXML($path);
//			return $manifest->version;
		}
		
		//return ($version?$version:false);
            
		return '300';
	}
	
	/*
	* Add list menu in manager menu of com_virtuemart
	*
	*/
	
	private function addVmMenuEntries() {
		
		$db = JFactory::getDBO();
		$q = "INSERT INTO `#__virtuemart_modules` (`module_name`, `module_description`, `module_perms`, `published`, `is_admin`, `ordering`) VALUES ('netbasevm_extend', 'Netbase Virtuemart Extend', 'admin,storeadmin', 1, '1', 4); ";
		$db->setQuery($q);
		$db->execute();
		$moduleid = $db->insertid();
		
		// menu create template invoice
		$q = "INSERT INTO `#__virtuemart_adminmenuentries` (`module_id`, `parent_id`, `name`, `link`, `depends`, `icon_class`, `ordering`, `published`, `tooltip`, `view`, `task`) VALUES ($moduleid, 0, 'COM_NETBASEVM_EXTEND_TEMPLATE_INVOICES', 'index.php?option=com_netbasevm_extend&controller=templateinvoice', '', 'invoice_template vmicon', 2, 1, '', '', ''); ";
		// menu add new orders
		$q .= "INSERT INTO `#__virtuemart_adminmenuentries` (`module_id`, `parent_id`, `name`, `link`, `depends`, `icon_class`, `ordering`, `published`, `tooltip`, `view`, `task`) VALUES ($moduleid, 0, 'COM_NETBASEVM_EXTEND_NEW_ORDERS', 'index.php?option=com_netbasevm_extend&controller=nborders', '', 'invoice_new vmicon', 2, 1, '', '', 'addNewNborders'); ";
		// menu invoices and orders
		$q .= "INSERT INTO `#__virtuemart_adminmenuentries` (`module_id`, `parent_id`, `name`, `link`, `depends`, `icon_class`, `ordering`, `published`, `tooltip`, `view`, `task`) VALUES ($moduleid, 0, 'COM_NETBASEVM_EXTEND_INVOICES_ORDERS', 'index.php?option=com_netbasevm_extend&controller=nborders', '', 'invoice_extend vmicon', 0, 1, '', '', ''); ";
		// menu invoices and orders
		$q .= "INSERT INTO `#__virtuemart_adminmenuentries` (`module_id`, `parent_id`, `name`, `link`, `depends`, `icon_class`, `ordering`, `published`, `tooltip`, `view`, `task`) VALUES ($moduleid, 0, 'COM_NETBASEVM_EXTEND_STATISTICS_INVOICES', 'index.php?option=com_netbasevm_extend&controller=statistics', '', 'static_invoice vmicon', 0, 1, '', '', ''); ";
		
		
		$db->setQuery($q);
		$db->queryBatch();
	}
	
	
	/*
	* Remove menu in manager menu of com_virtuemart
	*
	*/
	
	private function removeVmMenuEntries() {
		
		$db = JFactory::getDBO();
		$q = "SELECT `module_id` FROM `#__virtuemart_modules` WHERE `module_name`='netbasevm_extend'";
		$db->setQuery($q);
		$moduleid = $db->loadColumn();
		
		/*$q = "DELETE FROM `#__virtuemart_modules` WHERE `module_name`='netbasevm_extend'";
		$db->setQuery($q);
		$db->execute();*/
		
		if(!empty($moduleid)){
			$q = "DELETE FROM `#__virtuemart_adminmenuentries` WHERE `module_id`=$moduleid";
			$db->setQuery($q);
			$db->execute();
		}		
		
		// remove menu admin
		$q = "DELETE FROM `#__menu` WHERE `alias` LIKE '%netbasevm_extend%'";
		$db->setQuery($q);
		$db->execute();
		
	}
	
	
	/*
	* Remove all data when add to tables virtuemart by plugins,modules,components installed in folder
	* extension .
	*/
	private function removeDigiTollDownloadsData($parent) {
		$db = JFactory::getDBO();
		
		if(version_compare(JVERSION,'1.6.0','lt')) {
			// Remove product_customfields
			$q = "DELETE pc FROM `#__virtuemart_product_customfields` `pc` INNER JOIN `#__virtuemart_customs` `c` INNER JOIN `#__plugins` `p` WHERE `pc`.`virtuemart_custom_id`=`c`.`virtuemart_custom_id` AND `c`.`custom_jplugin_id`=`p`.`id` AND `p`.`element`='netbasevm_extend' AND `p`.`folder`='vmcustom'; ";
			
			// Remove customs
			$q .= "DELETE `c` FROM `#__virtuemart_customs` `c` INNER JOIN `#__plugins` `p` WHERE `c`.`custom_jplugin_id`=`p`.`id` AND `p`.`element`='netbasevm_extend' AND `p`.`folder`='vmcustom'; ";
		} else {
			// Remove product_customfields
			$q .= "DELETE pc FROM `#__virtuemart_product_customfields` `pc` INNER JOIN `#__virtuemart_customs` `c` INNER JOIN `#__extensions` `e` WHERE `pc`.`virtuemart_custom_id`=`c`.`virtuemart_custom_id` AND `c`.`custom_jplugin_id`=`e`.`extension_id` AND `e`.`element`='netbasevm_extend' AND `e`.`folder`='vmcustom'; ";
			
			// Remove customs
			$q .= "DELETE `c` FROM `#__virtuemart_customs` `c` INNER JOIN `#__extensions` `e` WHERE `c`.`custom_jplugin_id`=`e`.`extension_id` AND `e`.`element`='netbasevm_extend' AND `e`.`folder`='vmcustom'; ";
		}
				
		$db->setQuery($q);
		return $db->queryBatch();
	}
	
	
	private function resetConfig() {
		$db = JFactory::getDBO();
		$q = 'TRUNCATE `#__netbasevm_extend_configs`';
		$db->setQuery($q);
		$db->execute();
		
		JFile::delete(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_netbasevm_extend'.DS.'netbasevm_extend.cfg');
	}
	
	/**
	 * Courtesy: http://www.phpro.org/examples/Find-Position-Of-Nth-Occurrence-Of-String.html
	 *
	 * @Find position of Nth occurance of search string
	 *
	 * @param string $search The search string
	 *
	 * @param string $string The string to seach
	 *
	 * @param int $offset The Nth occurance of string
	 *
	 * @return int or false if not found
	 *
	 */
	 
	private function getExtensionType($ext) {
		$type = substr($ext, 0, strpos($ext, '_'));
		switch ($type) {
			case 'plg' : $type='plugin'; break;
			case 'mod' : $type='module'; break;
			case 'com' : $type='component'; break;
		}
		
		return $type;
	}
	
	private function getExtensionFolder($ext) {
		$items = explode('_', $ext);
		if ($this->getExtensionType($ext)=='plugin')
			return $items[1];
		else
			return '';
	}
	
	private function getExtensionElement($ext) {
		if ($this->getExtensionType($ext)=='plugin')
			return substr($ext, $this->strposOffset('_', $ext, 2)+1);
		else
			return substr($ext, $this->strposOffset('_', $ext, 1)+1);
	}
	
	private function getExtensionId($ext) {
		$type = $this->getExtensionType($ext);
		$folder = $this->getExtensionFolder($ext);
		$element = $this->getExtensionElement($ext);
		
		$db = JFactory::getDBO();
		if(version_compare(JVERSION,'1.6.0','lt')) {
			switch ($type) {
				case 'component' : $q = "SELECT `id` FROM `#__components` WHERE `option`='$element'"; break;
				case 'module' : $q = "SELECT `id` FROM `#__modules` WHERE `module`='mod_$element'"; break;
				case 'plugin' : $q = "SELECT `id` FROM `#__plugins` WHERE `element`='$element' AND `folder`='$folder'"; break;
			}
		} else {
			$q = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='$type' AND `element` = '$element' AND `folder` = '$folder'";
		}
		$db->setQuery($q);
		return $db->loadColumn();
	}
	
	private function strposOffset($search, $string, $offset)
	{
		/*** explode the string ***/
		$arr = explode($search, $string);
		/*** check the search is not out of bounds ***/
		switch( $offset )
		{
			case $offset == 0:
			return false;
			break;
		
			case $offset > max(array_keys($arr)):
			return false;
			break;
	
			default:
			return strlen(implode($search, array_slice($arr, 0, $offset)));
		}
	}
	
	private function update102($parent) {
	
		echo '<p>Adding Import menu into VirtueMart...';
		
		$db = JFactory::getDBO();
		$q = "SELECT `module_id` FROM `#__virtuemart_modules` WHERE `module_name`='netbasevm_extend'";
		$db->setQuery($q);
		$moduleid = $db->loadColumn();
		
		$q = "INSERT INTO `#__virtuemart_adminmenuentries` (`module_id`, `parent_id`, `name`, `link`, `depends`, `icon_class`, `ordering`, `published`, `tooltip`, `view`, `task`) VALUES ($moduleid, 0, 'COM_NETBASEVM_EXTEND_IMPORT', 'index.php?option=com_netbasevm_extend', '', 'vmicon vmicon-16-config', 2, 1, '', 'import', ''); ";
		
		$db->setQuery($q);
		if ($db->execute())
			echo '<b style="color:green;">Done</b></p>';
		else
			echo '<b style="color:red;">FAILED</b>';
	}
	
	private function update101($parent) {
	
		echo '<p>Creating custom table...';
		
		$db = JFactory::getDBO();
		$q = "CREATE TABLE `#__virtuemart_product_custom_plg_netbasevm_extend` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `virtuemart_product_id` int(11) unsigned DEFAULT NULL,
		  `netbasevm_extend_download_id` int(11) unsigned DEFAULT NULL,
		  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `created_by` int(11) NOT NULL DEFAULT '0',
		  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `modified_by` int(11) NOT NULL DEFAULT '0',
		  `locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `locked_by` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='DigiToll Downloads Product Downloads Table';";
		$db->setQuery($q);
		if ($db->execute()) 
			echo '<b style="color:green;">Done</b></p>';
		else
			echo '<b style="color:red;">FAILED</b>';
		
		echo '<p>Migrating product download relations...';
		
		$q = "SELECT `virtuemart_product_id`, `custom_param` FROM `#__virtuemart_product_customfields` INNER JOIN `#__virtuemart_customs` USING (`virtuemart_custom_id`) WHERE `custom_element`='netbasevm_extend'";
		$db->setQuery($q);
		$dls = $db->loadObjectList();
		
		$q = '';
		foreach ($dls as $dl) {
			$arr = json_decode($dl->custom_param,true);
			$ids = (array)$arr['netbasevm_extend_download_id'];
			foreach ($ids as $id) {
				$q .= "INSERT `#__virtuemart_product_custom_plg_netbasevm_extend` (`virtuemart_product_id`, `netbasevm_extend_download_id`) VALUES (".$dl->virtuemart_product_id.", $id); ";
			}
		}
		
		$db->setQuery($q);
		if ($db->queryBatch()) 
			echo '<b style="color:green;">Done</b></p>';
		else
			echo '<b style="color:red;">FAILED</b>';
	}
}

/**
 * Legacy j1.5 function to use the 1.6 class install/update
 *
 * @return boolean True on success
 * @deprecated
 */
function com_install() {
	$NbInstall = new com_NetBaseVm_ExtendInstallerScript();
	$upgrade = $NbInstall->getInstalledVersion('netbasevm_extend');
	
	
	if(version_compare(JVERSION,'1.6.0','ge')) {
		// Joomla! 16 code here
	} else {
		
		// Joomla! 1.5 code here
		$method = ($upgrade) ? 'update' : 'install';
		if (!$NbInstall->preflight($method)) return false;
		if (!$NbInstall->$method()) return false;
		$NbInstall->postflight($method);
	}

	return true;
}

/**
 * Legacy j1.5 function to use the 1.6 class uninstall
 *
 * @return boolean True on success
 * @deprecated
 */
function com_uninstall() {
	$NbInstall = new com_NetBaseVm_ExtendInstallerScript();
	
	
	if(version_compare(JVERSION,'1.6.0','ge')) {
		// Joomla! 1.6 code here
	} else {
		if (!$NbInstall->preflight('uninstall')) return false;
		if (!$NbInstall->uninstall()) return false;
		$NbInstall->postflight('uninstall');
	}

	return true;
}



}