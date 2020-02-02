<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//the name of the class must be the name of your component + InstallerScript
//for example: com_contentInstallerScript for com_content.
class com_mapYandexInstallerScript
{
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		$jversion = new JVersion();

		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
		
		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   
/*
		// Show the essential information at the install/update back-end
		echo '<p>Installing component manifest file version = ' . $this->release;
		echo '<br />Current manifest cache commponent version = ' . $this->getParam('version');
		echo '<br />Installing component manifest file minimum Joomla version = ' . $this->minimum_joomla_release;
		echo '<br />Current Joomla version = ' . $jversion->getShortVersion();
		echo '<br />$type = ' . $type;
*/
		// abort if the current Joomla release is older
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			Jerror::raiseWarning(null, 'Cannot install com_democompupdate in a Joomla release prior to '.$this->minimum_joomla_release);
			return false;
		}
 
		// abort if the component being installed is not newer than the currently installed version
		if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
				Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);
				return false;
			}
		}
		else { $rel = $this->release; }
                // $parent is the class calling this method
                // $type is the type of change (install, update or discover_install)
	
	}
 
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install( $parent ) {

		// echo JText::_('COM_MAPYANDEX_INSTALL');
		// You can have the backend jump directly to the newly installed component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
	}
 
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
		echo '<p>' . JText::_('COM_MAPYANDEX_UPDATE_ to ' . $this->release) . '</p>';
		// You can have the backend jump directly to the newly updated component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
	}
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
		// load language file
		
		$lang = JFactory::getLanguage();
		$extension = 'com_mapyandex';
		$base_dir = JPATH_SITE;
		$language_tag = 'ru-RU';
		$reload = true;
		$lang->load($extension, $base_dir, $language_tag, $reload);
		
		// define the following parameters only if it is an original install
		if ( $type == 'install' ) {
			$params['draggable_placemark'] = 1;
			$params['new_placemark'] = 1;
			$params['use_jquery'] = 1;
			$params['map_baloon_or_placemark'] = 1;
			$params['userpathtoimg'] = '/images/mapyandex/yandexmarkerimg/';
		}
		$this->setParams( $params );
		if(!defined('DS')){
		define('DS',DIRECTORY_SEPARATOR);
		}

		$folder[0][0]	=	'images' . DS . 'mapyandex' . DS ;
		$folder[0][1]	= 	JPATH_ROOT . DS .  $folder[0][0];
		$folder[1][0]	=	'images' . DS . 'mapyandex' . DS . 'yandexmarkerimg' . DS;
		$folder[1][1]	= 	JPATH_ROOT . DS .  $folder[1][0];
	
		$message = '';
		$error	 = array();
		foreach ($folder as $key => $value)
		{
			if (!JFolder::exists( $value[1]))
			{
				if (JFolder::create( $value[1], 0755 ))
				{
					
					$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
					JFile::write($value[1].DS."index.html", $data);
					$message .= '<div><p><b><span style="color:#009933">Директория</span> ' . $value[0] 
							   .' <span style="color:#009933">создана!</span></b><p></div>';
					$error[] = 0;
				}	 
				else
				{
					$message .= '<div><p><b><span style="color:#CC0033">Директория</span> ' . $value[0]
							   .' <span style="color:#CC0033">не создана!</span></b> Пожалуйста создайте их вручную!<p></div>';
					$error[] = 1;
				}
			}
			else//Folder exist
			{
				$message .= '<div><p><b><span style="color:#009933">Диерктория</span> ' . $value[0] 
							   .' <span style="color:#009933">существует!</span></b><p></div>';
				$error[] = 0;
			}
		}
		echo $message;
			
		
		if(!is_writable(JPATH_ROOT . DS .'images/mapyandex/yandexmarkerimg/')) {
			$config = JFactory::getConfig($_SERVER['DOCUMENT_ROOT'].JURI::root(true).'configuration.php');
			
			if($config->get('ftp_enable')) {
				$ftp = new JFtp();
				$ftp->connect($config->get('ftp_host'),$config->get('ftp_port'));
				
				$l = $ftp->login($config->get('ftp_user'),$config->get('ftp_pass'));
				
				if(!is_writable(JPATH_ROOT . DS .'images/mapyandex/yandexmarkerimg/') && !($l && $ftp->isConnected())) {
			
				  
							Jerror::raiseWarning(null, JText::_('COM_MAPYANDEX_POSTFLIGHT_TEST_COPY_ERROR').$config->get('ftp_enable'));
							return false;
				}
			} else {
	
				$a = JPATH_ROOT . DS .'images/mapyandex/yandexmarkerimg/';
			
				Jerror::raiseWarning(null, JText::_('Ошибка при установке. Нет прав на запись директории '.$a.' и FTP  не настроен или отключен!'));
				return false;
			}
		}
		
		$folder[1][0]	=	'images' . DS . 'mapyandex' . DS . 'yandexmarkerimg' . DS;
		$folder[1][1]	= 	JPATH_ROOT . DS .  $folder[1][0];
		
		if (JFolder::exists( $folder[1][1]))
		{
			JFolder::delete($folder[1][1]);
		}

		
		$ret = JFolder::copy(JPATH_ROOT . DS .'media/com_mapyandex/yandexmarkerimg/',$folder[1][1]);
			if($ret == true) {
				echo '<p>' .JText::_('COM_MAPYANDEX_POSTFLIGHT_TEST_COPY'). '</p>';
			} else {
				echo '<p>' .JText::_('COM_MAPYANDEX_POSTFLIGHT_TEST_COPY_ERROR'). '</p>';
			}
		
		
				
			$db = JFactory::getDBO();
			$status = new stdClass;
			$status->modules = array();
			$status->plugins = array();
			$src = $parent->getParent()->getPath('source');
			$manifest = $parent->getParent()->manifest;
			
			$plugins = $manifest->xpath('plugins/plugin');
			foreach ($plugins as $plugin)
			{
				$name = (string)$plugin->attributes()->plugin;
				$group = (string)$plugin->attributes()->group;
				$path = $src.'/plugins/'.$group;
				if (JFolder::exists($src.'/plugins/'.$group.'/'.$name))
				{
					$path = $src.'/plugins/'.$group.'/'.$name;
				}
				$installer = new JInstaller;
				$result = $installer->install($path);
			
				$query = "UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element=".$db->Quote($name)." AND folder=".$db->Quote($group);
				$db->setQuery($query);
				$db->query();
				$status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
			}	
			
		
			echo '<p>' .JText::_('COM_MAPYANDEX_POSTFLIGHT'). '</p>';
	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
		
		$db = JFactory::getDBO();
		$status = new stdClass;
		$status->modules = array();
		$status->plugins = array();
		$src = $parent->getParent()->getPath('source');
		$manifest = $parent->getParent()->manifest;
		$plugins = $manifest->xpath('plugins/plugin');
		foreach ($plugins as $plugin)
		{
			$name = (string)$plugin->attributes()->plugin;
			$group = (string)$plugin->attributes()->group;
			
			$db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "'.$name.'" AND `folder` = "'.$group.'"');
			$id = $db->loadResult();
			if(!empty($id)) {
				$installer = new JInstaller;
				$result = $installer->uninstall('plugin',$id,1);
				$status->plugins[] = array('name'=>$name,'group'=>$group, 'result'=>$result);
				echo '<p>' . JText::_('COM_MAPYANDEX_UNINSTALL_PLUGIN') . '</p>';
			}
		}
		echo '<p>' . JText::_('COM_MAPYANDEX_UNINSTALL') . '</p>';
	}
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_mapyandex"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
 
	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_mapyandex"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_mapyandex"' );
				$db->query();
		}
	}
}
