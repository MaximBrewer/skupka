<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

jimport('joomla.application.component.view');

class Sh404sefViewDefault extends ShlMvcView_Base
{

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$this->footerText = JText::sprintf('COM_SH404SEF_FOOTER_' . strtoupper(Sh404sefConfigurationEdition::$id),
			Sh404sefFactory::getConfig()->version, Sh404sefConfigurationEdition::$name, date('Y'));

		// required assets for the messages manager
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$document = JFactory::getDocument();
			ShlMsg_Manager::getInstance()->addAssets($document);
			ShlHtml_Manager::getInstance()
			               ->addAssets($document)
			               ->addSpinnerAssets($document);
		}

		// prepare the view, based on layout
		$method = '_makeView' . ucfirst($this->getLayout());
		if (is_callable(array($this, $method)))
		{
			$this->$method();
		}

		JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_list.css');

		parent::display($this->joomlaVersionPrefix);
	}

	/**
	 * Attach css, js and create toolbar for default view
	 *
	 * @param midxed $params
	 */
	private function _makeViewDefault($params = null)
	{

		// prepare database stats, etc
		$this->_prepareControlPanelData();

		// Get the JComponent instance of JToolBar
		$bar = JToolBar::getInstance('toolbar');

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			// render submenu sidebar
			$this->sidebar = JHtmlSidebar::render();

			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			// add title
			JToolbarHelper::title('sh404SEF: ' . JText::_('COM_SH404SEF_CONTROL_PANEL'), 'sh404sef-toolbar-title');

			// prepare configuration button
			if (Sh404sefHelperAcl::userCan('sh404sef.view.configuration'))
			{
				$bar->addButtonPath(SHLIB_ROOT_PATH . 'toolbarbutton');
				$params = array();
				$params['class'] = 'modaltoolbar btn-success';
				$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['configuration'];
				$params['buttonClass'] = 'btn-success btn btn-small modal';
				$params['iconClass'] = 'icon-options';
				$url = 'index.php?option=com_sh404sef&tmpl=component&c=configuration&view=configuration&component=com_sh404sef&hidemainmenu=1';
				$bar
					->appendButton('J3popuptoolbarbutton', 'configj3', JText::_('COM_SH404SEF_CONFIGURATION'), $url, $params['size']['x'],
						$params['size']['y'], $top = 0, $left = 0, $onClose = '', $title = '', $params);
			}
			$html = '<div class="wbl-spinner-black" id="toolbar-sh404sef-spinner"></div>';
			$bar->appendButton('custom', $html, 'sh-progress-button-cpprogress');

			// add analytics and other ajax calls loader
			$sefConfig = Sh404sefFactory::getConfig();
			$analyticsBootstrap = $sefConfig->analyticsReportsEnabled ? 'shSetupAnalytics({report:"dashboard",showFilters:"no"});' : '';
			$js = 'jQuery(document).ready(function(){ ' . $analyticsBootstrap . '  shSetupQuickControl(); shSetupSecStats(); shSetupUpdates();});';
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);
		}
		else
		{
			// add behaviors and styles as needed
			$modalSelector = 'a.modalediturl';
			$js = '\\function(){window.parent.shAlreadySqueezed = false;if(window.parent.shReloadModal) {parent.window.location=\'index.php?option=com_sh404sef\';window.parent.shReloadModal=true}}';
			$params = array('overlayOpacity' => 0, 'classWindow' => 'sh404sef-popup', 'classOverlay' => 'sh404sef-popup', 'onClose' => $js);
			Sh404sefHelperHtml::modal($modalSelector, $params);

			// import tabs
			jimport('joomla.html.pane');

			// add tooltips handler
			JHTML::_('behavior.tooltip');

			// add title
			$title = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_CONTROL_PANEL'), $icon = 'sh404sef',
				$class = 'sh404sef-toolbar-title');
			JFactory::getApplication()->JComponentTitle = $title;

			// add a div to display our ajax-call-in-progress indicator
			$bar->addButtonPath(JPATH_COMPONENT . '/' . 'classes');
			$html = '<div id="sh-progress-cpprogress"></div>';
			$bar->appendButton('custom', $html, 'sh-progress-button-cpprogress');

			// add modal handler for configuration
			JHTML::_('behavior.modal');
			$configbtn = '<a class="modal" href="index.php?option=com_sh404sef&tmpl=component&c=configuration&view=configuration&component=com_sh404sef&hidemainmenu=1" rel="{handler: \'iframe\', size: {x: window.getSize().x*0.90, y: window.getSize().y*0.90}, onClose: function() {}}"><span class="icon-32-options"></span>'
				. JText::_('COM_SH404SEF_CONFIGURATION') . '</a>';
			if (Sh404sefHelperAcl::userCan('sh404sef.view.configuration'))
			{
				$bar->appendButton('custom', $configbtn, 'sh-configbutton-button');
			}
			// add analytics and other ajax calls loader
			$sefConfig = Sh404sefFactory::getConfig();
			$analyticsBootstrap = Sh404sefHelperAcl::userCan('sh404sef.view.analytics') && $sefConfig->analyticsReportsEnabled ? 'shSetupAnalytics({report:"dashboard",showFilters:"no"});' : '';
			$js = 'window.addEvent(\'domready\', function(){ ' . $analyticsBootstrap
				. '  shSetupQuickControl(); shSetupSecStats(); shSetupUpdates();});';
			JFactory::getDocument()->addScriptDeclaration($js);
		}

		// add our javascript
		JHTML::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/' . $this->joomlaVersionPrefix . '_cp.' . Sh404sefConfigurationEdition::$id . '.js');
		// add our own css
		JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_cp.css');
	}

	/**
	 * Attach css, js and create toolbar for Info view
	 *
	 * @param midxed $params
	 */
	private function _makeViewInfo($params = null)
	{
		// add our own css
		JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/list.css');

		// decide on help file language
		$languageCode = Sh404sefHelperLanguage::getFamily();
		$basePath = JPATH_ROOT . '/administrator/components/com_sh404sef/language/%s.readme.php';
		// fall back to english if language readme does not exist
		jimport('joomla.filesystem.file');
		if (!JFile::exists(sprintf($basePath, $languageCode)))
		{
			$languageCode = 'en';
		}
		$this->readmeFilename = sprintf($basePath, $languageCode);

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			// render submenu sidebar
			$this->sidebar = JHtmlSidebar::render();

			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			JToolbarHelper::title(JText::_('COM_SH404SEF_TITLE_SUPPORT'), 'sh404sef-toolbar-title');
		}
		else
		{
			// add title
			$title = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_TITLE_SUPPORT'), $icon = 'sh404sef',
				$class = 'sh404sef-toolbar-title');
			JFactory::getApplication()->JComponentTitle = $title;
		}
	}

	private function _prepareControlPanelData()
	{
		$sefConfig = Sh404sefFactory::getConfig();
		$this->sefConfig = $sefConfig;

		// get currently store
		$this->messageList = ShlMsg_Manager::getInstance()->get(array('scope' => 'com_sh404sef', 'acknowledged' => false));

		// update information
		$versionsInfo = Sh404sefHelperUpdates::getUpdatesInfos();
		$this->updates = $versionsInfo;

		// url databases stats
		$database = ShlDbHelper::getDb();
		$this->cpStats = array();
		$this->cpStatsMore = array();
		try
		{
			// URLS -------------------
			$sql = 'SELECT count(*) FROM #__sh404sef_urls WHERE ';
			$this->cpStats['URL'] = array();
			$database->setQuery($sql . "`newurl` <> '' and`rank` = 0");
			$value = $database->loadResult();
			$sefCount = $value;
			$this->cpStats['URL'][JText::_('COM_SH404SEF_CP_TOTAL_URLS')] = array('value' => $value, 'flag' => '',
			                                                                      'link'  => 'index.php?option=com_sh404sef&c=urls&layout=default&view=urls');

			$database->setQuery($sql . "`newurl` <> '' and `cpt` <> 0 and `rank` = 0");
			$value = $database->loadResult();
			$this->cpStats['URL'][JText::_('COM_SH404SEF_CP_VISITED')] = array('value' => $value, 'flag' => '',
			                                                                   'link'  => 'index.php?option=com_sh404sef&c=urls&layout=default&view=urls&filter_requested_urls=1');

			$database->setQuery($sql . "`newurl` <> '' and `cpt` = 0 and `rank` = 0");
			$value = $database->loadResult();
			$this->cpStats['URL'][JText::_('COM_SH404SEF_CP_NEVER_VISITED')] = array('value' => $value, 'flag' => '',
			                                                                         'link'  => 'index.php?option=com_sh404sef&c=urls&layout=default&view=urls&filter_requested_urls=2');

			// Joomla 2 B/C
			$database->setQuery($sql . "`dateadd` > '0000-00-00' and `newurl` != '' ");
			$customCount = $database->loadResult();

			// 404 -------------------
			$this->cpStats['404s'] = array();
			$database->setQuery($sql . "`newurl` = '' ");
			$value = $database->loadResult();
			$count404 = $value;
			$this->cpStats['404s'][JText::_('COM_SH404SEF_CP_TOTAL_404')] = array('value' => $value, 'flag' => '',
			                                                                      'link'  => 'index.php?option=com_sh404sef&c=urls&layout=view404&view=urls');

			$database->setQuery($sql . "`dateadd` > '0000-00-00' and `newurl` = '' and `referrer_type` = " . Sh404sefHelperUrl::IS_INTERNAL);
			$value = $database->loadResult();
			$this->cpStats['404s'][JText::_('COM_SH404SEF_CP_INTERNAL')] = array('value' => $value, 'flag' => (empty($value) ? '' : 'wbl-flag-warning'),
			                                                                     'link'  => 'index.php?option=com_sh404sef&c=urls&layout=view404&view=urls&filter_hit_type=' . Sh404sefHelperUrl::IS_INTERNAL);

			$database->setQuery($sql . "`dateadd` > '0000-00-00' and `newurl` = '' and (`referrer_type` = " . Sh404sefHelperUrl::IS_EXTERNAL . " OR `referrer_type` = " . Sh404sefHelperUrl::IS_UNKNOWN . ")");
			$value = $database->loadResult();
			$this->cpStats['404s'][JText::_('COM_SH404SEF_CP_EXTERNAL')] = array('value' => $value, 'flag' => '',
			                                                                     'link'  => 'index.php?option=com_sh404sef&c=urls&layout=view404&view=urls&filter_hit_type=' . Sh404sefHelperUrl::IS_EXTERNAL);

			// Aliases ---------------
			$sql = 'SELECT count(*) FROM #__sh404sef_aliases';
			$this->cpStatsMore['Aliases'] = array();
			$database->setQuery($sql);
			$value = $database->loadResult();
			$this->cpStatsMore['Aliases'][JText::_('COM_SH404SEF_CP_TOTAL_ALIASES')] = array('value' => $value, 'flag' => '',
			                                                                                 'link'  => 'index.php?option=com_sh404sef&c=aliases&layout=default&view=aliases');

			$database->setQuery($sql . " where `hits` <> 0");
			$value = $database->loadResult();
			$this->cpStatsMore['Aliases'][JText::_('COM_SH404SEF_CP_USED')] = array('value' => $value, 'flag' => '',
			                                                                        'link'  => 'index.php?option=com_sh404sef&c=aliases&layout=default&view=aliases&filter_requested_urls=' . Sh404sefHelperGeneral::SHOW_REQUESTED);

			$database->setQuery($sql . " where `hits` = 0");
			$value = $database->loadResult();
			$this->cpStatsMore['Aliases'][JText::_('COM_SH404SEF_CP_NEVER_USED')] = array('value' => $value, 'flag' => '',
			                                                                              'link'  => 'index.php?option=com_sh404sef&c=aliases&layout=default&view=aliases&filter_requested_urls=' . Sh404sefHelperGeneral::SHOW_NOT_REQUESTED);

			// shURLs ----------------
			$sql = 'SELECT count(*) FROM #__sh404sef_pageids as s join #__sh404sef_urls as u';
			$sql .= ' on s.newurl = u.newurl where u.`newurl` <> \'\'';
			$this->cpStatsMore['shURLs'] = array();
			$database->setQuery($sql);
			$value = $database->loadResult();
			$this->cpStatsMore['shURLs'][JText::_('COM_SH404SEF_CP_TOTAL_SHURLS')] = array('value' => $value, 'flag' => '',
			                                                                               'link'  => 'index.php?option=com_sh404sef&c=pageids&layout=default&view=pageids');

			$database->setQuery($sql . " and s.`hits` <> 0");
			$value = $database->loadResult();
			$this->cpStatsMore['shURLs'][JText::_('COM_SH404SEF_CP_USED')] = array('value' => $value, 'flag' => '',
			                                                                       'link'  => 'index.php?option=com_sh404sef&c=pageids&layout=default&view=pageids&filter_requested_urls=' . Sh404sefHelperGeneral::SHOW_REQUESTED);

			$database->setQuery($sql . " and s.`hits` = 0");
			$value = $database->loadResult();
			$this->cpStatsMore['shURLs'][JText::_('COM_SH404SEF_CP_NEVER_USED')] = array('value' => $value, 'flag' => '',
			                                                                             'link'  => 'index.php?option=com_sh404sef&c=pageids&layout=default&view=pageids&filter_requested_urls=' . Sh404sefHelperGeneral::SHOW_NOT_REQUESTED);
		}
		catch (Exception $e)
		{
			ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			$sefCount = 0;
			$count404 = 0;
			$customCount = 0;
		}

		$this->sefCount = $sefCount;
		$this->Count404 = $count404;
		$this->customCount = $customCount;
	}
}
