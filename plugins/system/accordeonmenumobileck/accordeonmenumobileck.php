<?php

/**
 * @copyright	Copyright (C) 2012 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');

class plgSystemAccordeonmenumobileck extends JPlugin {

	function __construct(&$subject, $params) {
		parent::__construct($subject, $params);
	}

	/**
	 * @param       JForm   The form to be altered.
	 * @param       array   The associated data for the form.
	 * @return      boolean
	 * @since       1.6
	 */
	function onContentPrepareForm($form, $data) {
		if ($form->getName() != 'com_modules.module' && $form->getName() != 'com_advancedmodules.module' || ($form->getName() == 'com_modules.module' && $data && $data->module != 'mod_accordeonck') || ($form->getName() == 'com_advancedmodules.module' && $data && $data->module != 'mod_accordeonck'))
			return;

		JForm::addFormPath(JPATH_SITE . '/plugins/system/accordeonmenumobileck/params');
		JForm::addFieldPath(JPATH_SITE . '/modules/mod_accordeonck/elements');
		// get the language
//		$lang = JFactory::getLanguage();
//		$langtag = $lang->getTag(); // returns fr-FR or en-GB
		$this->loadLanguage();

		// module options
		$app = JFactory::getApplication();
		$plugin = JPluginHelper::getPlugin('system', 'accordeonmenumobileck');
		$pluginParams = new JRegistry($plugin->params);

		$form->loadFile('mobile_menuparams_accordeonck', false);
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
		$browserType = $browser->getBrowser();
		if ($browserType == 'msie' and $browser->getVersion() < 9) {
			return false;
		}
		
		// get the language
//		$lang = JFactory::getLanguage();
		$this->loadLanguage();

		JHTML::_("jquery.framework", true);
		if (!class_exists('Mobile_Detect')) {
			require_once dirname(__FILE__) . '/Mobile_Detect.php';
		}
		$document->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
		$document->addScript(JUri::base(true) . '/plugins/system/accordeonmenumobileck/assets/accordeonmenumobileck.js');
//		$document->addScriptDeclaration("var CKTEXT_PLG_ACCORDEONCK_MENU = '" . JText::_('PLG_ACCORDEONMENUMOBILECK_MENU') . "';");
		$menuIDs = Array();

		foreach ($this->getAccordeonmenuModules() as $module) {
			if (!$module->params) 
				continue;
			
			$moduleParams = new JRegistry($module->params);
			if (!$moduleParams->get('accordeonmobile_enable', '0'))
				continue;

			$menuID = $moduleParams->get('tag_id', '') ? $moduleParams->get('tag_id', '') : 'accordeonck' . $module->id;
			$resolution = $this->params->get('accordeonmobile_resolution', '640');
			$useimages = $moduleParams->get('accordeonmobile_useimage', '0');
			$container = $moduleParams->get('accordeonmobile_container', 'body');
			$usemodules = $moduleParams->get('accordeonmobile_usemodule', '0');
			$showdesc = $moduleParams->get('accordeonmobile_showdesc', '0');
			$theme = $this->params->get('accordeonmobile_theme', 'default');
			$document->addStyleSheet(JUri::base(true) . '/plugins/system/accordeonmenumobileck/themes/' . $theme . '/accordeonmenuckmobile.css');

			// set the text for the menu bar
			switch ($moduleParams->get('accordeonmobile_showmobilemenutext', '')) {
				case 'none':
					$mobilemenutext = '';
					break;
				case 'default':
				default:
					$mobilemenutext = JText::_('PLG_ACCORDEONMENUMOBILECK_MENU');
					break;
				case 'custom':
					$mobilemenutext = $moduleParams->get('accordeonmobile_mobilemenutext', '');
					break;
			}

			array_push($menuIDs, $menuID);

			$js = "jQuery(document).ready(function($){
                    $('#" . $menuID . "').MobileAccordeonMenu({"
					. "usemodules : " . $usemodules . ","
					. "container : '" . $container . "',"
					. "showdesc : " . $showdesc . ","
					. "useimages : " . $useimages . ","
					. "menuid : '" . $menuID . "',"
					. "showmobilemenutext : '" . $moduleParams->get('maximenumobile_showmobilemenutext', '') . "',"
					. "mobilemenutext : '" . $mobilemenutext . "',"
					. "mobilebackbuttontext : '" . JText::_('PLG_ACCORDEONCK_MOBILEBACKBUTTON') . "',"
					. "displaytype : '" . $moduleParams->get('accordeonmobile_display', 'flat') . "',"
					. "displayeffect : '" . $moduleParams->get('accordeonmobile_displayeffect', 'normal') . "'"
					. "});
                });";
			$document->addScriptDeclaration($js);

			$css = $this->getMediaQueries($resolution, $menuID, $moduleParams);
			$document->addStyleDeclaration($css);
		}
	}
	
	private function getAccordeonmenuModules_old() {
		$db = JFactory::getDBO();
		$query = "
			SELECT id, params
			FROM #__modules
			WHERE published=1
			AND module='mod_accordeonck'
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
	protected function getAccordeonmenuModules()
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
			->where('module = \'mod_accordeonck\'')
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

	private function getMediaQueries($resolution, $menuID, $moduleParams) {
		$detect_type = $this->params->get('detectiontype', 'resolution');
		$detect = new Mobile_Detect;
		$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
		$bodypadding = ($moduleParams->get('accordeonmobile_container', 'body') == 'body' || $moduleParams->get('accordeonmobile_container', 'body') == 'topfixed') ? 'body { padding-top: 40px !important; }' : '';
		if ($detect_type == 'resolution') {
			$css = "@media only screen and (max-width:" . str_replace('px', '', $resolution) . "px){
    #" . $menuID . " { display: none !important; }
    .mobilebaraccordeonmenuck { display: block; }
    .hidemenumobileck {display: none !important;}
    " . $bodypadding . " }";
		} elseif (($detect_type == 'tablet' && $detect->isMobile()) || ($detect_type == 'phone' && $detect->isMobile() && !$detect->isTablet())) {
			$css = "#" . $menuID . " { display: none !important; }
    .mobilebaraccordeonmenuck { display: block; }
	.hidemenumobileck {display: none !important;}
    " . $bodypadding;
		} else {
			$css = '';
		}

		return $css;
	}
}