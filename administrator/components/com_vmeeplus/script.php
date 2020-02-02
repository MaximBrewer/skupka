<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * 
 */

define ('MENU_TITLE', '');
jimport('joomla.filesystem.file');
class com_vmeeplusInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		jimport('joomla.installer.installer');
		jimport('joomla.application.component.helper');
		
		try{
		
			$phpVer = phpversion();
			if(version_compare($phpVer, '5.2.0') < 0){
				echo "<p style=\"color:red\">Could not install VirtueMart Email Manager. You are running an old version of PHP: ".$phpVer."</p><p style=\"color:red\">You need to un-install this component, upgrade your PHP version and re-install the package.</p>";
				throw new Exception(JText::_('PHP_VERSION_TOO_LOW'));
			}
		
			$installer = JInstaller::getInstance();
			$src = $installer->getPath('source');
		
			$jlang = JFactory::getLanguage();
			$jlang->load('com_vmeeplus', JPATH_ADMINISTRATOR, 'en-GB', true);
			$jlang->load('com_vmeeplus', JPATH_ADMINISTRATOR, null, true);
		
			$manifest = new SimpleXMLElement(file_get_contents($src.DIRECTORY_SEPARATOR.'manifest.xml'));
			$proversion = $manifest->version;
		
			echo '<p style="text-align: center;"><a target="_blank" href="http://www.remarkety.com/?utm_source=email_manager_plus&utm_medium=banner&utm_campaign=email_plus_install"><img src="components/com_vmeeplus/images/increase_sales_728_90.gif"></a></p>';
			echo '<h2>Interamind Email Manager Plus ' .$proversion.' installation:</h2>';
		
			$vmDir = $this->getVmPath(false);
			if(!is_writable($vmDir)){
				throw new Exception($vmDir . '<span style="color: red;"> '. JText::_("not writable") . '</span>');
			}
		
			$comVMEmailsPath = JPath::clean(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_vmeeplus".DIRECTORY_SEPARATOR);
			$cfg1 = JFile::read($comVMEmailsPath."vmeepro.cfg.php");
		
			//replace the placeholder for the ISDEMO define
			$strIsDemo = JURI::root() == "http://demo.interamind.com/" ? "true" : "false";
			$cfg1 = str_replace("\"demoTrueOrFalse\"",$strIsDemo,$cfg1);
		
			//create the updated php file
			if (!JFile::write($comVMEmailsPath."vmeepro.cfg.php", $cfg1)){
				throw new Exception($comVMEmailsPath."vmeepro.cfg.php" . '<span style="color: red;"> '. JText::_("not writable") . '</span>');
			}
		
		
			$vmFiles = array('shopfunctionsf');
			$timeStamp = date("d_m_Y_H_i_s");
			foreach ($vmFiles as $file){
				$vmfile = $this->getVmFilePath($file);
				if(!is_writable($vmfile)){
					throw new Exception($vmfile . '<span style="color: red;"> '. JText::_("not writable") . '</span>');
				}
				if(!@JFile::copy($vmfile, $vmfile.'.vmempro_backup_' . $timeStamp)){
					$msg = sprintf(JText::_('BACKUP_FAILED_FORMAT'),$vmfile);
					throw new Exception($msg);
				}
				echo '<p><img src="components/com_vmeeplus/images/green_check.gif" />' . JText::_("BACKUP_SUCCESS") . '</p>';
			}
		
			//insert our code inside VM files
			$installCodeDir = $path = $src.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'install';
		
			//shopfunctionsf
			if(!$this->handleShopFunctions($installCodeDir)){
				$msg = sprintf(JText::_('WRITE_FILE_FAILED_FORMAT'),$this->getVmFilePath('shopfunctionsf'));
				throw new Exception($msg);
			}
			echo '<p><img src="components/com_vmeeplus/images/green_check.gif" />' . JText::_("WRITE_FILE_SUCCESS") . '</p>';
			//install plugins
			$db = JFactory::getDBO();
			$status = new JObject();
			$status->modules = array();
			$status->plugins = array();
		
			foreach ($manifest->plugins->plugin as $plugin) {
				$pname = (string)$plugin['name'];
				$pgroup = (string)$plugin['group'];
				$path = $src.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$pgroup.DIRECTORY_SEPARATOR.$pname;
				$installer2 = new JInstaller();
				$result = $installer2->install($path);
				$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);
		
				$query = "UPDATE #__extensions SET enabled=1 WHERE element=".$db->Quote($pname)." AND folder=".$db->Quote($pgroup);
				$db->setQuery($query);
				$db->execute();
			}
		
			//$this->upgradeDb();
		
			if(count($status->plugins)){
				echo '<p><span style="font-weight: bold; text-decoration: underline;">' . JText::_('INSTALLED_PLUGINS_TITLE') . '</span></p>';
				?>
		<table>
			<tr>
				<th><?php echo JText::_('Plugin'); ?></th>
				<th><?php echo JText::_('Group'); ?></th>
				<th><?php echo JText::_('Result'); ?></th>
			</tr>
			<?php foreach ($status->plugins as $plugin){ ?>
			<tr>
				<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
				<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
				<td><?php echo ($plugin['result'])?'<img src="components/com_vmeeplus/images/green_check.gif" />':'<img src="components/com_vmeeplus/images/cancel_f2.png" />'; ?></td>
			</tr>
			<?php }?>
		</table>
		<?php 		}
		echo '<h2>' . JText::_("THANK_YOU_FOR_INSTALLING") . '</h2>';
		echo '<a href="www.interamind.com"><h2>www.interamind.com</h2></a>';
			}
			catch (Exception $e){
				$Errormsg = JText::_("Error ocured during installation");
				echo '<h2 style="color:red">' . JText::_("INSTALL_FAILED") . '</h2>';
				echo '<p style="color:red">' . $Errormsg . $e->getMessage() . '</p>';
				return $this->raiseError($Errormsg . $e->getMessage());
			}
			return true;
	}
 
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		jimport('joomla.filesystem.folder');
		$db = JFactory::getDBO();
		//1. disable scheduler and sendmail plugins
		$plugins = array();
		$plugins['sendmail'] = 'vmeepro';
		$plugins['tagHandlerVmeePro'] = 'vmee';
		foreach ($plugins as $name=>$group){
			$query = "UPDATE #__extensions SET enabled=0 WHERE element=".$db->Quote($name)." AND folder=".$db->Quote($group);
			$db->setQuery($query);
			$db->execute();
			$pluginsPath = JPATH_PLUGINS . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $name;
			JFolder::delete($pluginsPath);
		}
		
		
		
		//delete the plugins code
		
		//3. remove hacks in VM files
		$uninstallCodeDir = $path = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'uninstall';
		//shopfunctionsf
		if(!$this->handleShopFunctionsUninstall($uninstallCodeDir)){
			$this->raiseError('Could not remove vmeeplus from: '. $this->getVmFilePath('shopfunctions'). '. Please check documentation for manual removal');
		}
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		$installer = JInstaller::getInstance();
		$src = $installer->getPath('source');
		//insert our code inside VM files
		$installCodeDir = $src.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'install';
		
		//shopfunctionsf
		if(!$this->handleShopFunctions($installCodeDir)){
			$msg = sprintf(JText::_('WRITE_FILE_FAILED_FORMAT'),$this->getVmFilePath('shopfunctionsf'));
		}
		
		$db = JFactory::getDBO();
		$manifest = new SimpleXMLElement(file_get_contents($src.DIRECTORY_SEPARATOR.'manifest.xml'));
		foreach ($manifest->plugins->plugin as $plugin) {
			$pname = (string)$plugin['name'];
			$pgroup = (string)$plugin['group'];
			$path = $src.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$pgroup.DIRECTORY_SEPARATOR.$pname;
			$installer2 = new JInstaller();
			$result = $installer2->install($path);
			$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);
		
			$query = "UPDATE #__extensions SET enabled=1 WHERE element=".$db->Quote($pname)." AND folder=".$db->Quote($pgroup);
			$db->setQuery($query);
			$db->execute();
		}
	}
 
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		
	}
 
	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		
	}
	
	function raiseError($msg) {
		JError::raiseWarning(1, JText::_('Component').' '.JText::_('Install').': '.$msg);
		return false;
	}
	
	function interadebug($msg) {
		//global $mainframe;
		//$mainframe->enqueueMessage($msg);
	}
	
	function getVmPath($admin = true){
		if($admin){
			return JPath::clean(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtuemart".DIRECTORY_SEPARATOR);
		}
		else{
			return JPath::clean(JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtuemart".DIRECTORY_SEPARATOR);
		}
	}
	
	function getVmFilePath($vm_fileName){
		$path = '';
		$admin = true;
		switch ($vm_fileName){
			case 'shopfunctionsf':
				$path = 'helpers';
				$admin = false;
				break;
		}
		$comVmPath = $this->getVmPath($admin);
		$filePath = $comVmPath.$path.DIRECTORY_SEPARATOR.$vm_fileName.".php";
	
		return $filePath;
	}
	
	function handleShopFunctions($installDir){
		$bRes = true;
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtuemart".DIRECTORY_SEPARATOR."version.php");
		$vm_version = vmVersion::$RELEASE;
		$isNewVersion = version_compare($vm_version, '2.0.21');
		if($isNewVersion > 0){
			$isNewVersion = true;
			$content = JFile::read($installDir . DIRECTORY_SEPARATOR . 'shopfunctionsf_2.0.22.txt');
		}else{
			$isNewVersion = false;
			$content = JFile::read($installDir . DIRECTORY_SEPARATOR . 'shopfunctionsf.txt');
		}
		$vmFilePath = $this->getVmFilePath('shopfunctionsf');
	
		$vmFileContent = JFile::read($vmFilePath);
	
	
		if(strpos($vmFileContent,'//START_VM_EMAILS_HERE') !== false){
			//emails manager code exist
			$vmFileContent = preg_replace('/\/\/START_VM_EMAILS_HERE(.*)\s\/\/END_VM_EMAILS_HERE\s/Us', $content, $vmFileContent);
		}
		elseif(strpos($vmFileContent,'//VMEEPRO START') !== false){
			//emails plus code exist
			$vmFileContent = preg_replace('/\/\/VMEEPRO START(.*)\s\/\/VMEEPRO END\s/Us', $content."\n", $vmFileContent);
		}
		else{
			//fresh installation
			if($isNewVersion){
				$vmFileContent = preg_replace('/\$user\s*=\s*FALSE.*\s*return\s*\$user\;/Us', $content."\n", $vmFileContent, 1);
			}else{
				$vmFileContent = preg_replace('/\$user\s*=\s*self::sendVmMail(.|[\r\n])*?\}/', $content."\n", $vmFileContent, 1);
			}
		}
		if(!@JFile::write($vmFilePath,$vmFileContent)){
			$bRes = false;
		}
		return $bRes;
	}
	
	function handleShopFunctionsUninstall($uninstallDir){
		$bRes = true;
		$vmFilePath = $this->getVmFilePath('shopfunctionsf');
		$vmFileContent = @JFile::read($vmFilePath);
	
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_virtuemart".DIRECTORY_SEPARATOR."version.php");
		$vm_version = vmVersion::$RELEASE;
		$isNewVersion = version_compare($vm_version, '2.0.21');
		if($isNewVersion > 0){
			$isNewVersion = true;
			$content = @JFile::read($uninstallDir . DIRECTORY_SEPARATOR . 'shopfunctionsf_2.0.22.txt');
		}else{
			$isNewVersion = false;
			$content = @JFile::read($uninstallDir . DIRECTORY_SEPARATOR . 'shopfunctionsf.txt');
		}
	
		if($vmFileContent !== false &&  $content !== false){
			$vmFileContent = preg_replace('/\/\/VMEEPRO START(.*)\s\/\/VMEEPRO END\s/Us', $content, $vmFileContent);
			if(!@JFile::write($vmFilePath,$vmFileContent)){
				$bRes = false;
			}
		}
		else{
			$bRes = false;
		}
		return $bRes;
	}
	
	function isCodeIstalled(){
		$vmFilePath = $this->getVmFilePath('shopfunctionsf');
		$vmFileContent = JFile::read($vmFilePath);
	
		if(strpos($vmFileContent,'//VMEEPRO START') !== false){
			//emails manager code exist
			return true;
		}
		return false;
	}
	
	function upgradeDb(){
		$db_upgrade = JFactory::getDBO();
		$fields = $db_upgrade->getTableColumns("#__vmee_plus_rules");
		if(!array_key_exists("attachments", $fields)){
			$q = "ALTER TABLE `#__vmee_plus_rules` ADD `attachments` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL";
			$db_upgrade->setQuery($q);
			$db_upgrade->execute();
		}
	}
}