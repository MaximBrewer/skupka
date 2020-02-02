<?php

/**
 * @copyright	Copyright (C) 2012 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');
jimport('joomla.database.table');
jimport('joomla.filesystem.file');


class plgSystemMaximenuckmobile extends JPlugin {

	protected $pluginpath;

	function __construct(&$subject, $params) {
		$this->pluginpath = '/plugins/system/maximenuckmobile/';
		parent::__construct($subject, $params);
	}

	/**
	 * @param       JForm   The form to be altered.
	 * @param       array   The associated data for the form.
	 * @return      boolean
	 * @since       1.6
	 */
	function onContentPrepareForm($form, $data) {
		if (
			($form->getName() != 'com_modules.module' && $form->getName() != 'com_advancedmodules.module' || ($form->getName() == 'com_modules.module' && $data && $data->module != 'mod_maximenuck') || ($form->getName() == 'com_advancedmodules.module' && $data && $data->module != 'mod_maximenuck'))
			&& ($form->getName() != 'com_menus.item' && $form->getName() != 'com_menumanagerck.itemedition')
			)
			return;

		JForm::addFormPath(JPATH_SITE . '/plugins/system/maximenuckmobile/params');

		// get the language
		// $lang = JFactory::getLanguage();
		// $langtag = $lang->getTag(); // returns fr-FR or en-GB
		$this->loadLanguage();

		// module options
		// $app = JFactory::getApplication();
		// $plugin = JPluginHelper::getPlugin('system', 'maximenuckmobile');
		// $pluginParams = new JRegistry($plugin->params);

		// load the additional options in the module
		if ($form->getName() == 'com_modules.module' || $form->getName() == 'com_advancedmodules.module') {
			$form->loadFile('mobile_menuparams_maximenuck', false);
		}
		
		// menu item options
		if ($form->getName() == 'com_menus.item' || $form->getName() == 'com_menumanagerck.itemedition') {
			$form->loadFile('mobile_itemparams_maximenuck', false);
		}
	}

	function onAfterDispatch() {

		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$doctype = $document->getType();


		// si pas en frontend, on sort
		if ($app->isAdmin()) {
			return false;
		}

		// si pas HTML, on sort
		if ($doctype !== 'html') {
			return;
		}

		// si Internet Explorer on sort
		jimport('joomla.environment.browser');
		$browser = JBrowser::getInstance();
		$browserType = $browser->getBrowser(); // info : il existe aussi un browser version
		if ($browserType == 'msie' and $browser->getVersion() < 9) {
			// return false; // pb in Joomla! 3.7
		}

		// get the language
		// $lang = JFactory::getLanguage();
		$this->loadLanguage();

		JHTML::_("jquery.framework", true);
		if (!class_exists('MaximenuMobile_Detect')) {
			require_once dirname(__FILE__) . '/MaximenuMobile_Detect.php';
		}
		$document->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
		$document->addScript(JUri::base(true) . '/plugins/system/maximenuckmobile/assets/maximenuckmobile.js');
		// $document->addScriptDeclaration("var CKTEXT_PLG_MAXIMENUCK_MENU = '" . JText::_('PLG_MAXIMENUCK_MENU') . "';");
		// call the google fonts
		$gfont_urls = json_decode($this->params->get('maximenumobile_googlefonts', ''));
		if (count($gfont_urls)) {
			foreach ($gfont_urls as $gfont_url) {
				$document->addStylesheet($gfont_url);
			}
		}

		$menuIDs = Array();
		
		foreach ($this->getMaximenuModules() as $module) {
			if (!$module->params) 
				continue;
				
			$moduleParams = new JRegistry($module->params);
			if (!$moduleParams->get('maximenumobile_enable', '0'))
				continue;

			$menuID = ( $moduleParams->get('menuid', '') != '' && !is_numeric($moduleParams->get('menuid', '')) )? $moduleParams->get('menuid', '') : 'maximenuck' . $module->id;
			$resolution = $this->params->get('maximenumobile_resolution', '640');
			$container = $moduleParams->get('maximenumobile_container', 'body');
			$useimages = $moduleParams->get('maximenumobile_useimage', '0');
			$usemodules = $moduleParams->get('maximenumobile_usemodule', '0');
			$theme = $this->params->get('maximenumobile_theme', 'default');
			$document->addStyleSheet(JUri::base(true) . '/plugins/system/maximenuckmobile/themes/' . $theme . '/maximenuckmobile.css');
			$showdesc = $moduleParams->get('maximenumobile_showdesc', '0');
			$showlogo = $moduleParams->get('maximenumobile_showlogo', '1');

			// set the text for the menu bar
			switch ($moduleParams->get('maximenumobile_showmobilemenutext', '')) {
				case 'none':
					$mobilemenutext = '';
					break;
				case 'default':
				default:
					$mobilemenutext = JText::_('PLG_MAXIMENUCK_MENU');
					break;
				case 'custom':
					$mobilemenutext = $moduleParams->get('maximenumobile_mobilemenutext', '');
					break;
			}

			array_push($menuIDs, $menuID);

			$legacy = '';
			if ($this->params->get('legacy', '0')) {
				$legacy .= "jQuery('.mobilemaximenuck').addClass('mobilemenuck');";
				$legacy .= "jQuery('.mobilemaximenucktopbar').addClass('topbar');";
				$legacy .= "jQuery('.mobilebarmaximenuck').addClass('mobilebarmenuck');";
				$legacy .= "jQuery('.mobilebuttonmaximenuck').addClass('mobilebuttonmenuck');";
				$legacy .= "jQuery('.mobilemaximenucktogglericon').addClass('mobilemaximenutogglericon');";
				$legacy .= "jQuery('.mobilemaximenuckbackbutton').addClass('ckbackbutton');";
			}
			$js = "jQuery(document).ready(function($){
                    $('#" . $menuID . "').MobileMaxiMenu({"
					. "usemodules : " . $usemodules . ","
					. "container : '" . $container . "',"
					. "showdesc : " . $showdesc . ","
					. "showlogo : " . $showlogo . ","
					. "useimages : " . $useimages . ","
					. "menuid : '" . $menuID . "',"
					. "showmobilemenutext : '" . $moduleParams->get('maximenumobile_showmobilemenutext', '') . "',"
					. "mobilemenutext : '" . $mobilemenutext . "',"
					. "mobilebackbuttontext : '" . JText::_('MOD_MAXIMENUCK_MOBILEBACKBUTTON') . "',"
					. "displaytype : '" . $moduleParams->get('maximenumobile_display', 'flat') . "',"
					. "menubarbuttoncontent : '" . ($this->params->get('maximenumobile_theme', '') == 'custom' ? $this->params->get('maximenumobile_menubarbuttoncontent', '') : '') . "',"
					. "topbarbuttoncontent : '" . ($this->params->get('maximenumobile_theme', '') == 'custom' ? $this->params->get('maximenumobile_topbarbuttoncontent', '') : '') . "',"
					. "uriroot : '" . JUri::root(true) . "',"
					. "displayeffect : '" . $moduleParams->get('maximenumobile_displayeffect', 'normal') . "',"
					. "menuwidth : '" . $moduleParams->get('maximenumobile_menuwidth', '300') . "',"
					. "openedonactiveitem : '" . $moduleParams->get('maximenumobile_openedonactiveitem', '0') . "'"
					. "});"
					. $legacy
					. "});";
			$document->addScriptDeclaration($js);
			
			$css = $this->getMediaQueries($resolution, $menuID, $moduleParams);
			$document->addStyleDeclaration($css);
		}
	}

	private function getMaximenuModules_old() {
		$db = JFactory::getDBO();
		$query = "
            SELECT id, params
            FROM #__modules
            WHERE published=1
            AND module='mod_maximenuck'
            ;";
		$db->setQuery($query);
		$modules = $db->loadObjectList('id');
		return $modules;
	}
	
	/**
	 * Load published modules.
	 *
	 * @return  array
	 *
	 * @since   3.2
	 */
	protected function getMaximenuModules()
	{
		static $clean;

		if (isset($clean))
		{
			return $clean;
		}

		$app = JFactory::getApplication();
		$Itemid = $app->input->getInt('Itemid');
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$lang = JFactory::getLanguage()->getTag();
		$clientId = (int) $app->getClientId();

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params, mm.menuid')
			->from('#__modules AS m')
			->join('LEFT', '#__modules_menu AS mm ON mm.moduleid = m.id')
			->where('m.published = 1')

			->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id')
			->where('module = \'mod_maximenuck\'')
			->where('e.enabled = 1');

		$date = JFactory::getDate();
		$now = $date->toSql();
		$nullDate = $db->getNullDate();
		$query->where('(m.publish_up = ' . $db->quote($nullDate) . ' OR m.publish_up <= ' . $db->quote($now) . ')')
			->where('(m.publish_down = ' . $db->quote($nullDate) . ' OR m.publish_down >= ' . $db->quote($now) . ')')

			->where('m.access IN (' . $groups . ')')
			->where('m.client_id = ' . $clientId)
			->where('(mm.menuid = ' . (int) $Itemid . ' OR mm.menuid <= 0)');

		// Filter by language
		if ($app->isSite() && $app->getLanguageFilter())
		{
			$query->where('m.language IN (' . $db->quote($lang) . ',' . $db->quote('*') . ')');
		}

		$query->order('m.position, m.ordering');

		// Set the query
		$db->setQuery($query);
		$clean = array();

		try
		{
			$modules = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JLog::add(JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $e->getMessage()), JLog::WARNING, 'jerror');

			return $clean;
		}

		// Apply negative selections and eliminate duplicates
		$negId = $Itemid ? -(int) $Itemid : false;
		$dupes = array();

		for ($i = 0, $n = count($modules); $i < $n; $i++)
		{
			$module = &$modules[$i];

			// The module is excluded if there is an explicit prohibition
			$negHit = ($negId === (int) $module->menuid);

			if (isset($dupes[$module->id]))
			{
				// If this item has been excluded, keep the duplicate flag set,
				// but remove any item from the cleaned array.
				if ($negHit)
				{
					unset($clean[$module->id]);
				}

				continue;
			}

			$dupes[$module->id] = true;

			// Only accept modules without explicit exclusions.
			if (!$negHit)
			{
				$module->name = substr($module->module, 4);
				$module->style = null;
				$module->position = strtolower($module->position);
				$clean[$module->id] = $module;
			}
		}

		unset($dupes);

		// Return to simple indexing that matches the query order.
		// $clean = array_values($clean);

		return $clean;
	}

	/**
	 * Set the mediaqueries to hide - show the module and mobile bar
	 *
	 * @return  string - the css to load in the page
	 *
	 */
	private function getMediaQueries($resolution, $menuID, $moduleParams) {
		$detect_type = $this->params->get('maximenumobile_detectiontype', 'resolution');
		$detect = new MaximenuMobile_Detect;
		$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
		$bodypadding = ($moduleParams->get('maximenumobile_container', 'body') == 'body' || $moduleParams->get('maximenumobile_container', 'body') == 'topfixed') ? 'body { padding-top: 40px !important; }' : '';
		if ($detect_type == 'resolution') {
			$css = ".mobilebarmaximenuck { display: none; }
	@media only screen and (max-width:" . str_replace('px', '', $resolution) . "px){
    #" . $menuID . " { display: none !important; }
    .mobilebarmaximenuck { display: block; }
	.hidemenumobileck {display: none !important;}
    " . $bodypadding . " }";
		} elseif (($detect_type == 'tablet' && $detect->isMobile()) || ($detect_type == 'phone' && $detect->isMobile() && !$detect->isTablet())) {
			$css = "#" . $menuID . " { display: none !important; }
    .mobilebarmaximenuck { display: block; }
	.hidemenumobileck {display: none !important;}
    " . $bodypadding;
		} else {
			$css = '';
		}

		return $css;
	}

	/**
	 * Ajax entry point for other functions
	 *
	 * @return  mixed - the return from the called function
	 *
	 */
	function onAjaxMaximenuckmobile() {
		$app = JFactory::getApplication();
		$input = $app->input;
		
		$method = $input->get('method');

		if (method_exists($this, $method)) {
			$results = call_user_func('self::' . $method);
		}
		
		return $results;
	}

	/**
	 * Define the list of css prefixes
	 *
	 * @return  array - the prefixes list
	 *
	 */
	function getPrefixes() {
		$prefixes = array('menubar','menubarbutton','topbar','topbarbutton','menu','level1menuitem','level2menuitem','level3menuitem','togglericon');
		return $prefixes;
	}

	/**
	 * Ajax method to get all the fields from the interface and compile the CSS
	 *
	 * @return  string - the compiled CSS
	 *
	 */
	function getCss() {
		// check that the module exists
		if (file_exists(JPATH_ROOT.'/modules/mod_maximenuck/helper.php')) {
			require_once JPATH_ROOT.'/modules/mod_maximenuck/helper.php';
		} else {
			echo JText::_('CK_MODULE_MAXIMENUCK_NOT_INSTALLED');
			die;
		}
		// check if the method exists in the module, else it is an old version : can not work
		if (! class_exists('modMaximenuckHelper') || ! method_exists('modMaximenuckHelper','createCss') ) {
			echo 'Error : ' . JText::_('CK_METHOD_CREATEMODULECSS_NOT_FOUND');
			die;
		}
		$input = JFactory::getApplication()->input;
		$menuID = $input->get('menuID', '', 'string');

		$prefixes = $this->getPrefixes();
		$styles_css = new stdClass();
		$params = new stdClass();
		$gfontcalls = Array();
		// loop each prefix to compile the corresponding css
		foreach ($prefixes as $prefix) {
			$params->$prefix = new JRegistry($input->get($prefix, '', 'raw'));
			$tmp = modMaximenuckHelper::createCss($menuID, $params->$prefix, $prefix, false, '', false);
			$tmp["font-family"] = "font-family: " .$params->$prefix->get($prefix . 'fontfamily', '') . ";";
			$styles_css->$prefix = $tmp;
			$gfontcalls = $this->getGooglefontCall($gfontcalls, $params->$prefix->get($prefix . 'fontfamily', ''));
		}

		$css = '';
		$css .= "/* Maximenu CK mobile - http://www.joomlack.fr */\n";
		$css .= "/* Automatic styles generated from the plugin options */\n\n";

		// styles for the collapsing bar
		$css .= ".mobilebarmaximenuck {display:none;position:relative;left:0;top:0;right:0;z-index:100;}\n";
		$css .= ".mobilebarmaximenuck .mobilebarmenutitleck {display: block;" . implode($styles_css->menubar) . "}\n";
		$css .= ".mobilebarmaximenuck .mobilebuttonmaximenuck {cursor:pointer;box-sizing: border-box;position:absolute; top: 0; right: 0;line-height:0.8em;font-family:Segoe UI;text-align: center;" 
				. implode($styles_css->menubarbutton) 
				. "}\n";

		// styles for the menu
		$css .= ".mobilemaximenuck {box-sizing: border-box;width: 100%;" . implode($styles_css->menu) . "}\n";
		$css .= ".mobilemaximenuck .mobilemaximenucktopbar {position:relative;}\n";
		$css .= ".mobilemaximenuck .mobilemaximenucktitle {display: block;" . implode($styles_css->topbar) . "}\n";
		$css .= ".mobilemaximenuck .mobilemaximenuckclose {cursor:pointer;box-sizing: border-box;position:absolute; top: 0; right: 0;line-height:0.8em;font-family:Segoe UI;text-align: center;" 
				. implode($styles_css->topbarbutton) 
				. "}\n";
		// for the links
		$css .= ".mobilemaximenuck a {display:block;" . $styles_css->menu["fontcolor"] . "}\n";
		$css .= ".mobilemaximenuck a:hover {text-decoration: none;}\n";

		// styles for the menu items
		$css .= ".mobilemaximenuck div.maximenuck {position:relative;}\n";
		$css .= ".mobilemaximenuck div.level1.maximenuck > a {" . implode($styles_css->level1menuitem) . "}";
		$css .= ".mobilemaximenuck div.level2.maximenuck > a {" . implode($styles_css->level2menuitem) . "}";
		$css .= ".mobilemaximenuck div.level2.maximenuck + .mobilemaximenucksubmenu div.maximenuck > a {" . implode($styles_css->level3menuitem) . "}";

		// styles for the accordion icons
		$css .= "/* for accordion */\n";
		$css .= ".mobilemaximenuck .mobilemaximenucktogglericon:after {cursor:pointer;text-align:center;" . implode($styles_css->togglericon) . "}\n";
		$togglericonclosed = $params->togglericon->get('togglericoncontentclosed', '') == 'custom' ? $params->togglericon->get('togglericoncontentclosedcustomtext', '') : $params->togglericon->get('togglericoncontentclosed', '');
		$togglericonopened = $params->togglericon->get('togglericoncontentopened', '') == 'custom' ? $params->togglericon->get('togglericoncontentopenedcustomtext', '') : $params->togglericon->get('togglericoncontentopened', '');
		$css .= ".mobilemaximenuck .mobilemaximenucktogglericon:after {display:block;position: absolute;right: 0;top: 0;content:\"" . $togglericonclosed . "\";}\n";
		$css .= ".mobilemaximenuck .open .mobilemaximenucktogglericon:after {content:\"" . $togglericonopened . "\";}\n";

		// add google font
		$css .= "\n\n/* Google Font stylesheets */\n\n";
		$css .= implode("\n", $gfontcalls);
		// replace the path for correct image rendering
		$customcss = $input->get('customcss', '', 'raw');
		if ($input->get('action')) {
			$customcss = str_replace('../..', JUri::root(true) . '/plugins/system/maximenuckmobile', $customcss);
		}
		$css .= "\n\n/* Custom CSS generated from the plugin options */\n\n";
		$css .= $customcss;

		echo $css;
	}

	/**
	 * Ajax method to save the compiled CSS into the custom theme file
	 *
	 * @return  boolean - true on success for the file creation
	 *
	 */
	function saveCssToFile() {
		$input = JFactory::getApplication()->input;
		// create a backup file with all fields stored in it
		// useful to create a custom preset
		$fields = $input->get('jsonfields', '', 'string');
		$backupfile_path = JPATH_ROOT . $this->pluginpath . 'themes/custom/backup_styles.json';
		JFile::write($backupfile_path, $fields);

		$customcss = $input->get('customcss', '', 'raw');
		$backupfile_path = JPATH_ROOT . $this->pluginpath . 'themes/custom/backup_custom.css';
		JFile::write($backupfile_path, $customcss);

		// get the css styles to write in the file
		$css = "";
		ob_start();
		$this->getCss();
		$css .= ob_get_contents(); // get the css code to write
		ob_end_clean();

		// write in the css file
		$file_path = JPATH_ROOT . $this->pluginpath . 'themes/custom/maximenuckmobile.css';
		echo JFile::write($file_path, $css);
		exit();
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
		$folder_path = JPATH_ROOT . '/plugins/system/maximenuckmobile/presets/';
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
		$customcss = '';
		if ( file_exists($folder_path . $preset. '/custom.css') ) {
			$customcss = @file_get_contents($folder_path . $preset. '/custom.css');
		} else {
			echo '{"result" : 0, "message" : "File Not found : '.$folder_path . $preset. '/custom.css'.'"}';
			exit();
		}

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
		$folder_path = JPATH_ROOT . '/plugins/system/maximenuckmobile/presets/';

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
	/**
	 * Call to the google font
	 *
	 * @return  string - the CSS code
	 *
	 */
	private function getGooglefontCall($gfontlist, $font) {
		if ($font && ! in_array($font, $this->get_standard_fonts())) {
			$css = "@import url(https://fonts.googleapis.com/css?family=" . $font . ");\n";
			if (! in_array($css, $gfontlist)) {
				$gfontlist[] = $css;
			}
		}

		return $gfontlist;
	}

	/**
	 * List of standard fonts to avoid to call google and returns a 404 error
	 *
	 * @return  array - the list of standard fonts
	 *
	 */
	private function get_standard_fonts() {
		$fonts = Array('Times New Roman'
			, 'Helvetica'
			, 'Georgia'
			, 'Courier New'
			, 'Arial'
			, 'Verdana'
			, 'Comic Sans MS'
			, 'Tahoma', 'Segoe UI'
			, 'sans-serif'
			, 'serif'
			, 'cursive'
			);
		return $fonts;
	}
}