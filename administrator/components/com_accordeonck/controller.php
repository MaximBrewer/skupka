<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2016. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;

// Import JTableMenu
JLoader::register('JTableModule', JPATH_PLATFORM . '/joomla/database/table/menu.php');
JLoader::register('JTableModule', JPATH_PLATFORM . '/joomla/database/table/module.php');

class AccordeonckController extends JControllerLegacy
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
	public function previewModuleStyles() {
		$input = new JInput();
		$params = new JRegistry();
		$this->setModuleParams($params);
		$menuID = $input->get('menuID', '', 'string');
		$params->set('menuID', $menuID);
		$styles = $this->renderModuleStyles($params);
		$customcss = $input->get('customcss', '', 'raw');
		$styles .= $customcss;
		echo '|okck|<style>' . $styles . '</style>';

		die;
	}
	
	/**
	 * Ajax method to save the CSS in the file and the params in the module
	 */
	public function saveModuleStyles($id = 0, $param = '', $value = '') {
		$input = new JInput();
		$id = $input->post->get('id', $id, 'int');
//		$param = $input->post->get('param', $param, 'string');
//		$value = $input->post->get('value', $value, 'raw');

		$row = JTable::getInstance('Module');

		// load the module
		$row->load( (int) $id ); 
		if ($row->id === null) {
			echo 'Error : Can not load the module ID : ' . $id;
			die;
		}
		$row->params = new JRegistry($row->params);
		$this->setModuleParams($row->params);
		// set the new params
//		$row->params->set($param, $value);
		$params = $row->params;
		$row->params = $row->params->toString();

		if ($id)
		{
			if (!$row->store()) {
				echo 'Error : Can not save the module ID : ' . $id;
				echo($this->_db->getErrorMsg());
				die;
			}
		}

		// write the css in the css file
		/*$params->set('menuID', 'accordeonck' . $id);
		$styles = $this->renderModuleStyles($params);
		$customcss = $input->get('customcss', '', 'raw');
		$styles .= $customcss;
		if (! JFile::write(JPATH_SITE . '/modules/mod_accordeonck/themes/custom/custom_' . $id . '.css', $styles)) {
			echo 'Error : Can not write the CSS file for the module ID : ' . $id;
			die;
		}*/

		echo "1";
		die;
	}

	/*
	 * Get the styles settings and store them in a Registry variable
	 */
	public function setModuleParams(& $params) {
		$input = new JInput();

		$menustyles = $input->get('menustyles', '', 'raw');
		$level1itemgroup = $input->get('level1itemgroup', '', 'raw');
		$level1itemnormalstyles = $input->get('level1itemnormalstyles', '', 'raw');
		$level1itemhoverstyles = $input->get('level1itemhoverstyles', '', 'raw');
		$level1itemactivestyles = $input->get('level1itemactivestyles', '', 'raw');
		$level1itemnormalstylesicon = $input->get('level1itemnormalstylesicon', '', 'raw');
		$level1itemhoverstylesicon = $input->get('level1itemhoverstylesicon', '', 'raw');
		$level2menustyles = $input->get('level2menustyles', '', 'raw');
		$level2itemgroup = $input->get('level2itemgroup', '', 'raw');
		$level2itemnormalstyles = $input->get('level2itemnormalstyles', '', 'raw');
		$level2itemhoverstyles = $input->get('level2itemhoverstyles', '', 'raw');
		$level2itemactivestyles = $input->get('level2itemactivestyles', '', 'raw');
		$level2itemnormalstylesicon = $input->get('level2itemnormalstylesicon', '', 'raw');
		$level2itemhoverstylesicon = $input->get('level2itemhoverstylesicon', '', 'raw');
		$level3menustyles = $input->get('level3menustyles', '', 'raw');
		$level3itemgroup = $input->get('level3itemgroup', '', 'raw');
		$level3itemnormalstyles = $input->get('level3itemnormalstyles', '', 'raw');
		$level3itemhoverstyles = $input->get('level3itemhoverstyles', '', 'raw');
		$level3itemactivestyles = $input->get('level3itemactivestyles', '', 'raw');
		$level3itemnormalstylesicon = $input->get('level3itemnormalstylesicon', '', 'raw');
		$level3itemhoverstylesicon = $input->get('level3itemhoverstylesicon', '', 'raw');
		$headingstyles = $input->get('headingstyles', '', 'raw');
		$customcss = $input->get('customcss', '', 'raw');
//		$orientation = $input->get('orientation', 'horizontal', 'string');
//		$layout = $input->get('layout', 'default', 'string');
		
		$params->set('menustyles', $menustyles);
		$params->set('level1itemgroup', $level1itemgroup);
		$params->set('level1itemnormalstyles', $level1itemnormalstyles);
		$params->set('level1itemhoverstyles', $level1itemhoverstyles);
		$params->set('level1itemactivestyles', $level1itemactivestyles);
		$params->set('level1itemnormalstylesicon', $level1itemnormalstylesicon);
		$params->set('level1itemhoverstylesicon', $level1itemhoverstylesicon);
		$params->set('level2menustyles', $level2menustyles);
		$params->set('level2itemgroup', $level2itemgroup);
		$params->set('level2itemnormalstyles', $level2itemnormalstyles);
		$params->set('level2itemhoverstyles', $level2itemhoverstyles);
		$params->set('level2itemactivestyles', $level2itemactivestyles);
		$params->set('level2itemnormalstylesicon', $level2itemnormalstylesicon);
		$params->set('level2itemhoverstylesicon', $level2itemhoverstylesicon);
		$params->set('level3menustyles', $level3menustyles);
		$params->set('level3itemgroup', $level3itemgroup);
		$params->set('level3itemnormalstyles', $level3itemnormalstyles);
		$params->set('level3itemhoverstyles', $level3itemhoverstyles);
		$params->set('level3itemactivestyles', $level3itemactivestyles);
		$params->set('level3itemnormalstylesicon', $level3itemnormalstylesicon);
		$params->set('level3itemhoverstylesicon', $level3itemhoverstylesicon);
		$params->set('headingstyles', $headingstyles);
		$params->set('customcss', $customcss);
//		$params->set('orientation', $orientation);
//		$params->set('layout', $layout);
	}
	/**
	 * Method to render the <style>
	 */
	public function renderModuleStyles($params) {
		// load the helper of the module
		if (file_exists(JPATH_ROOT.'/modules/mod_accordeonck/helper.php')) {
			require_once JPATH_ROOT.'/modules/mod_accordeonck/helper.php';
		} else {
			echo JText::_('CK_MODULE_ACCORDEONCK_NOT_INSTALLED');
			die;
		}

		$menuID = $params->get('menuID', 'accordeonck_previewmodule');

		// check if the method exist in the module, else it is an old version
		if (! method_exists('modAccordeonckHelper','createModuleCss') ) {
			echo 'Error : ' . JText::_('CK_METHOD_CREATEMODULECSS_NOT_FOUND');
			die;
		}

		// render the styles
		$styles = modAccordeonckHelper::createModuleCss($params, $menuID);

		return $styles;
	}
	
	/**
	 * Ajax method to clean the name of the google font
	 */
	public function cleanGfontName() {
		$input = new JInput();
		$gfont = $input->get('gfont', '', 'string');

		// <link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
		// Open+Sans+Condensed:300
		// Open Sans
		if ( preg_match( '/family=(.*?) /', $gfont . ' ', $matches) ) {
			if ( isset($matches[1]) ) {
				$gfont = $matches[1];
			}
		}

		$gfont = str_replace(' ', '+', ucwords (trim($gfont)));
		echo trim(trim($gfont, "'"));
		die;
	}
	
	/**
	* Save the param in the module options table
	*
	* @param 	integer 	$id  	the module ID
	* @param 	string 		$param	the param name
	* @param 	string 		$value	the param value
	*/
	public function saveParam($id = 0, $param = '', $value = '') {
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
	public function loadParam($id = 0, $param = '', $ajax = true, $all = false, $json = false) {
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
	public function checkUpdate($name = 'accordeonck', $type='component', $folder='system') {
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
	public function saveItemParam($id = 0, $param = '', $value = '') {
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
	 * Ajax method to save the json data into the .mmck file
	 *
	 * @return  boolean - true on success for the file creation
	 *
	 */
	function exportParams() {
		$input = JFactory::getApplication()->input;
		// create a backup file with all fields stored in it
		$fields = $input->get('jsonfields', '', 'string');
		$backupfile_path = JPATH_ROOT . '/administrator/components/com_accordeonck/export/exportAccordeonckParams'. $input->get('moduleid',0,'int') .'.mmck';
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
		exit;
	}
	
	/**
	 * Ajax method to get the legacy styles from the module and return them in the customizer
	 *
	 * @return  string - the params
	 *
	 */
	function importModuleParamsFirstUse() {
		$app = JFactory::getApplication();
		$input = $app->input;
		$id = $input->get('id', 0, 'int');
		
		$row = JTable::getInstance('Module');

		// load the module
		$row->load( (int) $id );

		if ($row->id === null) {
			echo 'Error : Can not load the menu item ID : ' . $id;
			echo json_encode(array('success'=> '0', 'message'=> 'Error : Can not load the menu item ID : ' . $id));
			exit;
		}

		$legacyParams = new JRegistry($row->params);
		$params = new JRegistry();
		// set the new params
//		$row->params->set($param, $value);
//		$params = $row->params->toString();
		$legacyPrefixes = array(
			'menu' => 'menustyles',
			'level1link' => 'level1itemnormalstyles',
			'level2link' => 'level2itemnormalstyles',
			'level3link' => 'level3itemnormalstyles');
		
		foreach ($legacyPrefixes as $legacyPrefix => $prefix) {
			
			$this->convertLegacyParams($legacyParams, $params, $prefix, $legacyPrefix);
		}

//		$params = $row->params->toString();
//		echo json_encode(array('success'=> '1', 'params'=> $row->params));
		
//		$params = new JRegistry();
//		$params->set('level1itemnormalstylesbgcolor1', '#ff0000');
		$params = $params->toString();
		echo $params;
		exit;
	}
	
	function convertLegacyParams($legacyParams, &$params, $prefix, $legacyPrefix) {
		if ( $legacyParams->exists($legacyPrefix . 'usemargin')) {
			if ($legacyParams->get($legacyPrefix . 'usemargin') == '1' ) {
				$params->set($prefix . 'margintop', $legacyParams->get($legacyPrefix . 'margin'));
				$params->set($prefix . 'marginbottom', $legacyParams->get($legacyPrefix . 'margin'));
				$params->set($prefix . 'marginright', $legacyParams->get($legacyPrefix . 'margin'));
				$params->set($prefix . 'marginleft', $legacyParams->get($legacyPrefix . 'margin'));
				$params->set($prefix . 'paddingtop', $legacyParams->get($legacyPrefix . 'padding'));
				$params->set($prefix . 'paddingbottom', $legacyParams->get($legacyPrefix . 'padding'));
				$params->set($prefix . 'paddingright', $legacyParams->get($legacyPrefix . 'padding'));
				$params->set($prefix . 'paddingleft', $legacyParams->get($legacyPrefix . 'padding'));
			}
		}
		
		if ( $legacyParams->exists($legacyPrefix . 'usebackground')) {
			if ($legacyParams->get($legacyPrefix . 'usebackground') == '1' ) {
				$params->set($prefix . 'bgcolor1', $legacyParams->get($legacyPrefix . 'bgcolor1'));
			}
			if ($legacyParams->get($legacyPrefix . 'usegradient') == '1' ) {
				$params->set($prefix . 'bgcolor2', $legacyParams->get($legacyPrefix . 'bgcolor2'));
			}
		}

		if ( $legacyParams->exists($legacyPrefix . 'usefont')) {
			if ($legacyParams->get($legacyPrefix . 'usefont') == '1' ) {
				$params->set($prefix . 'fontsize', $legacyParams->get($legacyPrefix . 'fontsize'));
				$params->set($prefix . 'fontcolor', $legacyParams->get($legacyPrefix . 'fontcolor'));
				$params->set(str_replace('normal', 'hover', $prefix) . 'fontcolor', $legacyParams->get($legacyPrefix . 'fontcolorhover'));
				$params->set($prefix . 'descfontsize', $legacyParams->get($legacyPrefix . 'descfontsize'));
				$params->set($prefix . 'descfontcolor', $legacyParams->get($legacyPrefix . 'descfontcolor'));
			}
		}
		
		if ( $legacyParams->exists($legacyPrefix . 'useroundedcorners')) {
			if ($legacyParams->get($legacyPrefix . 'useroundedcorners') == '1' ) {
				$params->set($prefix . 'roundedcornerstl', $legacyParams->get($legacyPrefix . 'roundedcornerstl'));
				$params->set($prefix . 'roundedcornerstr', $legacyParams->get($legacyPrefix . 'roundedcornerstr'));
				$params->set($prefix . 'roundedcornersbr', $legacyParams->get($legacyPrefix . 'roundedcornersbr'));
				$params->set($prefix . 'roundedcornersbl', $legacyParams->get($legacyPrefix . 'roundedcornersbl'));
			}
		}
		
		if ( $legacyParams->exists($legacyPrefix . 'useshadow')) {
			if ($legacyParams->get($legacyPrefix . 'useshadow') == '1' ) {
				$params->set($prefix . 'shadowcolor', $legacyParams->get($legacyPrefix . 'shadowcolor'));
				$params->set($prefix . 'shadowblur', $legacyParams->get($legacyPrefix . 'shadowblur'));
				$params->set($prefix . 'shadowspread', $legacyParams->get($legacyPrefix . 'shadowspread'));
				$params->set($prefix . 'shadowoffsetx', $legacyParams->get($legacyPrefix . 'shadowoffsetx'));
				$params->set($prefix . 'shadowoffsety', $legacyParams->get($legacyPrefix . 'shadowoffsety'));
				$params->set($prefix . 'shadowinset', $legacyParams->get($legacyPrefix . 'shadowinset'));
			}
		}
		
		if ( $legacyParams->exists($legacyPrefix . 'useborders')) {
			if ($legacyParams->get($legacyPrefix . 'useborders') == '1' ) {
				$params->set($prefix . 'bordertopcolor', $legacyParams->get($legacyPrefix . 'bordercolor'));
				$params->set($prefix . 'bordertopwidth', $legacyParams->get($legacyPrefix . 'borderwidth'));
				$params->set($prefix . 'borderbottomcolor', $legacyParams->get($legacyPrefix . 'bordercolor'));
				$params->set($prefix . 'borderbottomwidth', $legacyParams->get($legacyPrefix . 'borderwidth'));
				$params->set($prefix . 'borderrightcolor', $legacyParams->get($legacyPrefix . 'bordercolor'));
				$params->set($prefix . 'borderrightwidth', $legacyParams->get($legacyPrefix . 'borderwidth'));
				$params->set($prefix . 'borderleftcolor', $legacyParams->get($legacyPrefix . 'bordercolor'));
				$params->set($prefix . 'borderleftwidth', $legacyParams->get($legacyPrefix . 'borderwidth'));
			}
		}

		$params->set('menustylesimageplus', $legacyParams->get('imageplus'));
		$params->set('menustylesimageminus', $legacyParams->get('imageminus'));
		$params->set('menustylesparentarrowwidth', '20');
	}
	
	/**
	 * Extract the css family name of the google font from the url
	 * @param string $gfont the font url
	 *
	 * @return string the font family
	 */
	static function get_gfontfamily($gfont) {
		// Open+Sans+Condensed:300
		if ( preg_match( '/(.*?):/', $gfont, $matches) ) {
			if ( isset($matches[1]) ) {
				$gfont = $matches[1];
			}
		}

		return ucwords(str_replace("+", " ", $gfont));
	}
	
	/**
	 * Ajax method to read the fields values from the selected preset
	 *
	 * @return  json - 
	 *
	 */
	function loadPresetFields() {
		$input = JFactory::getApplication()->input;
		$preset = $input->get('folder', '', 'string');
		$folder_path = JPATH_ROOT . '/administrator/components/com_accordeonck/presets/';
		// load the fields
		$fields = '{}';
		if ( file_exists($folder_path . $preset. '/styles.json') ) {
			$fields = @file_get_contents($folder_path . $preset. '/styles.json');
			$fields = str_replace("\n", "", $fields);
		} else {
			echo '{"result" : 0, "message" : "File Not found : '.$folder_path . $preset. '/styles.json'.'"}';
			exit();
		}
		// load the custom css
//		$customcss = '';
//		if ( file_exists($folder_path . $preset. '/custom.css') ) {
//			$customcss = @file_get_contents($folder_path . $preset. '/custom.css');
//		} else {
//			echo '{"result" : 0, "message" : "File Not found : '.$folder_path . $preset. '/custom.css'.'"}';
//			exit();
//		}

		echo '{"result" : 1, "fields" : "'.$fields.'", "customcss" : ""}';
		exit();
	}

	/**
	 * Ajax method to read the custom css from the selected preset
	 *
	 * @return  string - the custom CSS on success, error message on failure
	 *
	 */
	function loadPresetCustomcss() {
		$input = JFactory::getApplication()->input;
		$preset = $input->get('folder', '', 'string');
		$folder_path = JPATH_ROOT . '/administrator/components/com_accordeonck/presets/';

		// load the custom css
		$customcss = '';
		if ( file_exists($folder_path . $preset. '/custom.css') ) {
			$customcss = @file_get_contents($folder_path . $preset. '/custom.css');
		} else {
			echo '|ERROR| File Not found : '.$folder_path . $preset. '/custom.css';
			exit();
		}

		echo $customcss;
		exit();
	}
}

