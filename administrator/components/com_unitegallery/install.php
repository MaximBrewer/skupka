<?php

defined('_JEXEC') or die('Restricted access');


class com_unitegalleryInstallerScript
{

	public function __constructor(JAdapterInstance $adapter){
		
	}
 

	public function preflight($route, JAdapterInstance $adapter){
	}
 

	private function publishPlugin(){
		
		$sql = "UPDATE `#__extensions` SET `enabled`=1 where (`type`=\"plugin\" and `element`=\"unitegallery\")";
		
		$db = JFactory::getDbo();
		$db->setQuery($sql);
		$db->query();		
	}
	

	private function createOptionsTable(){
		
		$db = JFactory::getDbo();
		
		$sql = "
CREATE TABLE IF NOT EXISTS `#__unitegallery_options` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `val` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;		
		";
		
		$db->setQuery($sql);
		$db->query();
	}
	
	
	/**
	 * update content id
	 */
	private function updateContentID(){
		$db = JFactory::getDbo();
		
		try{
		
			//alter table change
			$sql = "ALTER TABLE `#__unitegallery_items`
			add `contentid` varchar(60);";
		
			$db->setQuery($sql);
			$db->query();
		
			$sql = "ALTER TABLE `#__unitegallery_items`
			add `content` text NOT NULL;";
		
			$db->setQuery($sql);
			$db->query();
		
		}catch(Exception $e){
			//throw $e;
			//skip errors
		}
	}
	
	
	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, JAdapterInstance $adapter){
		
		if($route != "update")
			return(false);		

		//update content id
		$this->updateContentID();
		
		//create options table
		$this->createOptionsTable();
		
	}	
	
	
	
	/**
	 * 
	 * install the modules from "modules" folder
	 */
	public function installModules(JAdapterInstance &$adapter,$type="install"){
		
		$ds = "";
		if(defined("DIRECTORY_SEPARATOR"))
			$ds = DIRECTORY_SEPARATOR;
		else
			$ds = DS;
		
		$manifest = $adapter->get("manifest");
		
		$installer = new JInstaller();
		$p_installer = $adapter->getParent();
		
		// Install modules
		if (is_object($manifest->modules->module)){	
			foreach($manifest->modules->module as $module){
				$attributes = $module->attributes();
				$modulePath = $p_installer->getPath("source") . $ds . $attributes['folder'] . $ds . $attributes['module'];
				
				if($type == "install")
					$installer->install($modulePath);
				else 
					$installer->update($modulePath);
			}
		}
		
	}

	/**
	 * 
	 * install the plugins from "plugins" folder
	 */
	public function installPlugins(JAdapterInstance &$adapter,$type="install"){
		
		$ds = "";
		if(defined("DIRECTORY_SEPARATOR"))
			$ds = DIRECTORY_SEPARATOR;
		else
			$ds = DS;
		
		$manifest = $adapter->get("manifest");
		
		$installer = new JInstaller();
		$p_installer = $adapter->getParent();
		
		// Install plugins
		if (is_object($manifest->plugins->plugins)){	
			foreach($manifest->plugins->plugin as $plugin){
				$attributes = $plugin->attributes();
				$pluginPath = $p_installer->getPath("source") . $ds . $attributes['folder'] . $ds . $attributes['plugin'];
				
				if($type == "install")
					$installer->install($pluginPath);
				else 
					$installer->update($pluginPath);
			}
		}
		
	}
	
	
	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter){
		
		$this->installModules($adapter,"install");
		$this->installPlugins($adapter,"install");
		
		$this->publishPlugin();		
	}
 
	
	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $adapter){
		
		$this->installModules($adapter,"update");
		$this->installPlugins($adapter,"update");
		
		$this->publishPlugin();
	}

	
	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $adapter){
		
	}
}

?>