<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;

// Import JTableMenu
JLoader::register('JTableModule', JPATH_PLATFORM . '/joomla/database/table/menu.php');

class MaximenuckController extends JControllerLegacy
{
	protected $params;
	
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		if (!isset($this->input)) $this->input = new JInput();

		parent::display();

		return $this;
	}
	
	/**
	 * Ajax method to render the <style>
	 */
	public function preview_module_styles() {
		// load the helper of the module
		if (file_exists(JPATH_ROOT.'/modules/mod_maximenuck/helper.php')) {
			require_once JPATH_ROOT.'/modules/mod_maximenuck/helper.php';
		} else {
			echo JText::_('CK_MODULE_MAXIMENUCK_NOT_INSTALLED');
			die;
		}
		$input = new JInput();

		$menuID = $input->get('menuID', '', 'string');
		$menustyles = $input->get('menustyles', '', 'raw');
		$level1itemnormalstyles = $input->get('level1itemnormalstyles', '', 'raw');
		$level1itemhoverstyles = $input->get('level1itemhoverstyles', '', 'raw');
		$level1itemactivestyles = $input->get('level1itemactivestyles', '', 'raw');
		$level1itemnormalstylesicon = $input->get('level1itemnormalstylesicon', '', 'raw');
		$level1itemhoverstylesicon = $input->get('level1itemhoverstylesicon', '', 'raw');
		$level2menustyles = $input->get('level2menustyles', '', 'raw');
		$level2itemnormalstyles = $input->get('level2itemnormalstyles', '', 'raw');
		$level2itemhoverstyles = $input->get('level2itemhoverstyles', '', 'raw');
		$level2itemactivestyles = $input->get('level2itemactivestyles', '', 'raw');
		$level2itemnormalstylesicon = $input->get('level2itemnormalstylesicon', '', 'raw');
		$level2itemhoverstylesicon = $input->get('level2itemhoverstylesicon', '', 'raw');
		$headingstyles = $input->get('headingstyles', '', 'raw');
		$orientation = $input->get('orientation', 'horizontal', 'string');
		$layout = $input->get('layout', 'default', 'string');

		$params= new JRegistry();
		$params->set('menustyles', $menustyles);
		$params->set('level1itemnormalstyles', $level1itemnormalstyles);
		$params->set('level1itemhoverstyles', $level1itemhoverstyles);
		$params->set('level1itemactivestyles', $level1itemactivestyles);
		$params->set('level1itemnormalstylesicon', $level1itemnormalstylesicon);
		$params->set('level1itemhoverstylesicon', $level1itemhoverstylesicon);
		$params->set('level2menustyles', $level2menustyles);
		$params->set('level2itemnormalstyles', $level2itemnormalstyles);
		$params->set('level2itemhoverstyles', $level2itemhoverstyles);
		$params->set('level2itemactivestyles', $level2itemactivestyles);
		$params->set('level2itemnormalstylesicon', $level2itemnormalstylesicon);
		$params->set('level2itemhoverstylesicon', $level2itemhoverstylesicon);
		$params->set('headingstyles', $headingstyles);
		$params->set('orientation', $orientation);
		$params->set('layout', $layout);
		
		// check if the method exist in the module, else it is an old version
		if (! method_exists('modMaximenuckHelper','createModuleCss') ) {
			echo 'Error : ' . JText::_('CK_METHOD_CREATEMODULECSS_NOT_FOUND');
			die;
		}

		// render the styles
		$styles = modMaximenuckHelper::createModuleCss($params, $menuID);

		echo '|okck|<style>' . $styles . '</style>';

		die;
	}
	
	/**
	 * Ajax method to clean the name of the google font
	 */
	public function clean_gfont_name() {
		// load the helper of the module
		if (file_exists(JPATH_ROOT.'/modules/mod_maximenuck/helper.php')) {
			require_once JPATH_ROOT.'/modules/mod_maximenuck/helper.php';
		} else {
			echo JText::_('CK_MODULE_MAXIMENUCK_NOT_INSTALLED');
			die;
		}
		
		$input = new JInput();
		$gfont = $input->get('gfont', '', 'string');

		$cleaned_gfont = modMaximenuckHelper::clean_gfont_name($gfont);

		echo $cleaned_gfont;

		die;
	}
	
	/**
	* Save the param in the module options table
	*
	* @param 	integer 	$id  	the module ID
	* @param 	string 		$param	the param name
	* @param 	string 		$value	the param value
	*/
	public function save_param($id = 0, $param = '', $value = '') {
		$input = new JInput();
		$id = $input->post->get('id', $id, 'int');
		$param = $input->post->get('param', $param, 'string');
		$value = $input->post->get('value', $value, 'raw');

		$row = JTable::getInstance('Module');

		// load the module
		$row->load( (int) $id ); 
		if ($row->id === null) {
			echo 'Error : Can not load the module ID : ' . $id;
			die;
		}
		$row->params = new JRegistry($row->params);
		// set the new params
		$row->params->set($param, $value);
		$row->params = $row->params->toString();

		if ($id)
		{
			if (!$row->store()) {
				echo 'Error : Can not save the module ID : ' . $id;
				echo($this->_db->getErrorMsg());
				die;
			}
		}
		echo "1";
		die;
	}
	
	/**
	* Load the param from the module options table
	*
	* @param 	integer 	$id  	the module ID
	* @param 	string 		$param	the param name
	*/
	public function load_param($id = 0, $param = '', $ajax = true, $all = false, $json = false) {
		$input = new JInput();
		$id = $input->post->get('id', $id, 'int');
		$param = $input->post->get('param', $param, 'string');
		$all = $input->post->get('all', $all, 'bool');

		$row = JTable::getInstance('Module');

		// load the module
		$row->load( (int) $id ); 
		if ($row->id === null && $ajax === true) {
			echo 'Error LOAD PARAM : Can not load the module ID : ' . $id;
			die;
		}
		$params = new JRegistry($row->params);
		if ( $ajax === true && $all === false ) {
			// get the needed params
			echo $params->get($param);
			die;
		} else if( $ajax === true && $all === true && $json === false ) {
			// get all the params
			echo $params;
			die;
		} else if( $ajax === false && $all === true && $json === true ) {
			// get all the params
			return $row->params;
			die;
		} else {
			return $params;
		}
	}
	
	/**
	* Check updates for the component, module, or plugins
	*/
	public function check_update($name = 'maximenuck', $type='component', $folder='system') {
		$input = new JInput();

		// init values
		$name = $input->get('name','','string') ? $input->get('name','','string') : $name;
		$type = $input->get('type','','string') ? $input->get('type','','string') : $type;
		$folder = $input->get('folder','','string') ? $input->get('folder','','string') : $folder;

		switch ($type) {
			case 'module' :
				$file_url = JPATH_SITE .'/modules/mod_'.$name.'/mod_'.$name.'.xml';
				$http_url = 'http://update.joomlack.fr/mod_'.$name.'_update.xml'; 
				$prefix = 'mod_';
				break;
			case 'plugin' :
				$file_url = JPATH_SITE .'/plugins/'.$folder.'/'.$name.'/'.$name.'.xml';
				$http_url = 'http://update.joomlack.fr/plg_'.$name.'_update.xml'; 
				$prefix = 'plg_';
				break;
			case 'component' :
			default :
				$file_url = JPATH_SITE .'/administrator/components/com_'.$name.'/'.$name.'.xml';
				$http_url = 'http://update.joomlack.fr/com_'.$name.'_update.xml';
				$prefix = 'com_';
				break;
		}

		// $xml_latest = false;
		$installed_version = false;

		// get the version installed
		if (! $xml_installed = JFactory::getXML($file_url)) {
			die;
		} else {
			$installed_version = (string)$xml_installed->version;
		}

		// get the latest available version
		error_reporting(0); // needed because the udpater triggers some warnings in joomla 2.5
		jimport('joomla.updater.updater');
		$updater = JUpdater::getInstance();
		$updater->findUpdates(0, 600);

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__updates')->where('element = \'' . $prefix . $name . '\'');
		$db->setQuery($query);

		if( $row = $db->loadObject() ) {
			$latest_version = $row->version;
		} else {
			die;
		}

		// return a message if there is an update
		if (VERSION_COMPARE($latest_version, $installed_version) > 0) {
			echo '<a href="'.$row->infourl.'"><span style="background-color: #d9534f;
    border-radius: 10px;
    color: #fff;
    display: inline-block;
    font-size: 12px;
    font-weight: 700;
    line-height: 1;
    min-width: 10px;
    padding: 3px 7px;
    text-align: center;
    vertical-align: baseline;
	text-shadow: none;
    white-space: nowrap;">' . JText::_('CK_UPDATE_FOUND') . ' : ' . $latest_version . '</apan></a>';
		}

		die;
	}
	
	/**
	* Save the param in the menu options table
	*
	* @param 	integer 	$id  	the menu item ID
	* @param 	string 		$param	the param name
	* @param 	string 		$value	the param value
	*/
	public function save_item_param($id = 0, $param = '', $value = '') {
		if (!isset($this->input)) $this->input = new JInput();
		$id = $this->input->post->get('id', $id, 'int');
		$param = $this->input->post->get('param', $param, 'string');
		$value = $this->input->post->get('value', $value, 'string');

		$row = JTable::getInstance('Menu');

		// load the module
		$row->load( (int) $id );
		if ($row->id === null) {
			echo 'Error : Can not load the menu item ID : ' . $id;
			die;
}
		$row->params = new JRegistry($row->params);
		// set the new params
		$row->params->set($param, $value);
		$row->params = $row->params->toString();

		if ($id)
		{
			if (!$row->store()) {
				echo 'Error : Can not save the menu item ID : ' . $id;
				echo($this->_db->getErrorMsg());
				die;
			}
		}
		echo "1";
		die;
/*
		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveparam($id, $param, $value);

		if ($return)
		{
			echo "1";
		}*/
	}
	
	/**
	* Save multiple params in the menu options table
	*
	* @param 	integer 	$id  	the menu item ID
	* @param 	string 		$param	the param name
	* @param 	string 		$value	the param value
	*/
	public function init_item_params_migration($id = 0, $state = array()) {
		if (!isset($this->input)) $this->input = new JInput();
		$id = $this->input->post->get('id', $id, 'int');
		$state = $this->input->post->get('state', $state, 'string');
		if ($state === 'all') {
			$paramstosave = Array("itemnormalstylesmargintop" => ''
					,"itemnormalstylesmarginright" => ''
					,"itemnormalstylesmarginbottom" => ''
					,"itemnormalstylesmarginleft" => ''
					,"itemnormalstylespaddingtop" => ''
					,"itemnormalstylespaddingright" => ''
					,"itemnormalstylespaddingbottom" => ''
					,"itemnormalstylespaddingleft" => ''
					,"itemnormalstylesroundedcornerstl" => ''
					,"itemnormalstylesroundedcornerstr" => ''
					,"itemnormalstylesroundedcornersbr" => ''
					,"itemnormalstylesroundedcornersbl" => ''
					,"itemhoverstylesmargintop" => ''
					,"itemhoverstylesmarginright" => ''
					,"itemhoverstylesmarginbottom" => ''
					,"itemhoverstylesmarginleft" => ''
					,"itemhoverstylespaddingtop" => ''
					,"itemhoverstylespaddingright" => ''
					,"itemhoverstylespaddingbottom" => ''
					,"itemhoverstylespaddingleft" => ''
					,"itemhoverstylesroundedcornerstl" => ''
					,"itemhoverstylesroundedcornerstr" => ''
					,"itemhoverstylesroundedcornersbr" => ''
					,"itemhoverstylesroundedcornersbl" => ''
					,"itemactivestylesmargintop" => ''
					,"itemactivestylesmarginright" => ''
					,"itemactivestylesmarginbottom" => ''
					,"itemactivestylesmarginleft" => ''
					,"itemactivestylespaddingtop" => ''
					,"itemactivestylespaddingright" => ''
					,"itemactivestylespaddingbottom" => ''
					,"itemactivestylespaddingleft" => ''
					,"itemactivestylesroundedcornerstl" => ''
					,"itemactivestylesroundedcornerstr" => ''
					,"itemactivestylesroundedcornersbr" => ''
					,"itemactivestylesroundedcornersbl" => ''
			);
		} else {
			$paramstosave = Array("item".$state."stylesmargintop" => ''
					,"item".$state."stylesmarginright" => ''
					,"item".$state."stylesmarginbottom" => ''
					,"item".$state."stylesmarginleft" => ''
					,"item".$state."stylespaddingtop" => ''
					,"item".$state."stylespaddingright" => ''
					,"item".$state."stylespaddingbottom" => ''
					,"item".$state."stylespaddingleft" => ''
					,"item".$state."stylesroundedcornerstl" => ''
					,"item".$state."stylesroundedcornerstr" => ''
					,"item".$state."stylesroundedcornersbr" => ''
					,"item".$state."stylesroundedcornersbl" => ''
			);
		}

		$row = JTable::getInstance('Menu');

		// load the module
		$row->load( (int) $id );
		if ($row->id === null) {
			echo 'Error : Can not load the menu item ID : ' . $id;
			die;
}
		$row->params = new JRegistry($row->params);
		// set the new params
		foreach ($paramstosave as $name => $value) {
			$row->params->set($name, $value);
		}
		
		// convert the params to string to store it
		$row->params = $row->params->toString();

		if ($id)
		{
			if (!$row->store()) {
				echo 'Error : Can not save the menu item ID : ' . $id;
				echo($this->_db->getErrorMsg());
				die;
			}
		}
		echo "1";
		die;
	}
	
	/**
	 * Ajax method to save the json data into the .mmck file
	 *
	 * @return  boolean - true on success for the file creation
	 *
	 */
	function exportParams() {
		$input = JFactory::getApplication()->input;
		// create a backup file with all fields stored in it
		$fields = $input->get('jsonfields', '', 'string');
		$backupfile_path = JPATH_ROOT . '/administrator/components/com_maximenuck/export/exportParams'. $input->get('moduleid',0,'int') .'.mmck';
		if (JFile::write($backupfile_path, $fields)) {
			echo 'true';
		} else {
			echo 'false';
		}

		exit();
	}
	
	/**
	 * Ajax method to import the .mmck file into the interface
	 *
	 * @return  boolean - true on success for the file creation
	 *
	 */
	function uploadParamsFile() {
		$app = JFactory::getApplication();
		$input = $app->input;
		$file = $input->files->get('file', '', 'array');
		if (!is_array($file))
			exit();

		$filename = JFile::makeSafe($file['name']);

		// check if the file exists
		if (JFile::getExt($filename) != 'mmck') {
			$msg = JText::_('CK_NOT_MMCK_FILE', true);
			echo json_encode(array('error'=> $msg));
			exit();
		}

		//Set up the source and destination of the file
		$src = $file['tmp_name'];

		// check if the file exists
		if (!$src || !JFile::exists($src)) {
			$msg = JText::_('CK_FILE_NOT_EXISTS', true);
			echo json_encode(array('error'=> $msg));
			exit();
		}

		// read the file
		if (!$filecontent = JFile::read($src)) {
			$msg = JText::_('CK_UNABLE_READ_FILE', true);
			echo json_encode(array('error'=> $msg));
			exit();
		}

		// replace vars to allow data to be moved from another server
		$filecontent = str_replace("|URIROOT|", JUri::root(true), $filecontent);
//		$filecontent = str_replace("|qq|", '"', $filecontent);

//		echo $filecontent;
		echo json_encode(array('data'=> $filecontent));
		exit();
	}
}
