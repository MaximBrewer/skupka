<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date		2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

jimport('joomla.application.component.view');

class Sh404sefViewUrlsBase extends ShlMvcView_Base
{
	// we are in 'urls' view
	protected $_context = 'urls';

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$this->footerText = JText::sprintf('COM_SH404SEF_FOOTER_' . strtoupper(Sh404sefConfigurationEdition::$id),
			Sh404sefFactory::getConfig()->version, Sh404sefConfigurationEdition::$name, date('Y'));

		// get model and update context with current
		$model = $this->getModel();

		$context = $model->setContext($this->_context . '.' . $this->getLayout());

		// display type: simple for very large sites/slow slq servers
		$sefConfig = Sh404sefFactory::getConfig();

		// if set for a slowServer, display simplified version of the url manager
		$this->slowServer = $sefConfig->slowServer;

		// read data from model
		$list = $model
			->getList((object) array('layout' => $this->getLayout(), 'simpleUrlList' => $this->slowServer, 'slowServer' => $sefConfig->slowServer));

		// and push it into the view for display
		$this->items = $list;
		$this->itemCount = count($this->items);
		$this->pagination = $model
			->getPagination(
				(object) array('layout' => $this->getLayout(), 'simpleUrlList' => $this->slowServer, 'slowServer' => $sefConfig->slowServer));
		$options = $model->getDisplayOptions();
		$this->options = $options;

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			// add our own css
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_list.css');
			JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/j3.js');

			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			// variable for modal, not used in 3..x+
			$params = array();

			// add display filters
			$this->_addFilters();

			// render submenu sidebar
			$this->sidebar = Sh404sefHelperHtml::renderSubmenu();

			// insert bootstrap theme
			ShlHtml_Manager::getInstance()->addAssets(JFactory::getDocument());
		}
		else
		{
			// add our own css
			//JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_urls.css');
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/list.css');

			// link to  custom javascript
			JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/list.js');

			// add behaviors and styles as needed
			$modalSelector = 'a.modalediturl';
			$js = '\\function(){window.parent.shAlreadySqueezed = false;if(window.parent.shReloadModal) {parent.window.location=\''
				. $this->defaultRedirectUrl . '\';window.parent.shReloadModal=true}}';
			$params = array('overlayOpacity' => 0, 'classWindow' => 'sh404sef-popup', 'classOverlay' => 'sh404sef-popup', 'onClose' => $js);
			Sh404sefHelperHtml::modal($modalSelector, $params);

			$this->optionsSelect = $this->_makeOptionsSelect($options);
		}

		// build the toolbar
		$toolbarMethod = '_makeToolbar' . ucfirst($this->getLayout() . ucfirst($this->joomlaVersionPrefix));
		if (is_callable(array($this, $toolbarMethod)))
		{
			$this->$toolbarMethod($params);
		}

		// now display normally
		parent::display($this->joomlaVersionPrefix);
	}

	protected function _makeOptionsSelect($options)
	{
		$selects = $this->_doMakeOptionsSelect($options);
		// return set of select lists
		return $selects;
	}

	protected function _addFilters()
	{
		$this->_doAddFilters();
	}

	protected function _doMakeOptionsSelect($options)
	{
		$selects = new StdClass();

		// component list
		$current = $options->filter_component;
		$name = 'filter_component';
		$selectAllTitle = JText::_('COM_SH404SEF_ALL_COMPONENTS');
		$selects->components = Sh404sefHelperHtml::buildComponentsSelectList($current, $name, $autoSubmit = true, $addSelectAll = true,
			$selectAllTitle);

		// language list
		$current = $options->filter_language;
		$name = 'filter_language';
		$selectAllTitle = JText::_('COM_SH404SEF_ALL_LANGUAGES');
		$selects->languages = Sh404sefHelperHtml::buildLanguagesSelectList($current, $name, $autoSubmit = true, $addSelectAll = true, $selectAllTitle);

		// select duplicates
		$current = $options->filter_duplicate;
		$name = 'filter_duplicate';
		$selectAllTitle = JText::_('COM_SH404SEF_ALL_DUPLICATES');
		$data = array(array('id' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_DUPLICATES, 'title' => JText::_('COM_SH404SEF_ONLY_DUPLICATES')),
			array('id' => Sh404sefHelperGeneral::COM_SH404SEF_NO_DUPLICATES, 'title' => JText::_('COM_SH404SEF_ONLY_NO_DUPLICATES')));
		$selects->filter_duplicate = Sh404sefHelperHtml::buildSelectList($data, $current, $name, $autoSubmit = true, $addSelectAll = true,
			$selectAllTitle);

		// select aliases
		$current = $options->filter_alias;
		$name = 'filter_alias';
		$selectAllTitle = JText::_('COM_SH404SEF_ALL_ALIASES');
		$data = array(array('id' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_ALIASES, 'title' => JText::_('COM_SH404SEF_ONLY_ALIASES')),
			array('id' => Sh404sefHelperGeneral::COM_SH404SEF_NO_ALIASES, 'title' => JText::_('COM_SH404SEF_ONLY_NO_ALIASES')));
		$selects->filter_alias = Sh404sefHelperHtml::buildSelectList($data, $current, $name, $autoSubmit = true, $addSelectAll = true,
			$selectAllTitle);

		// select custom
		$current = $options->filter_url_type;
		$name = 'filter_url_type';
		$selectAllTitle = JText::_('COM_SH404SEF_ALL_URL_TYPES');
		$data = array(array('id' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_CUSTOM, 'title' => JText::_('COM_SH404SEF_ONLY_CUSTOM')),
			array('id' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_AUTO, 'title' => JText::_('COM_SH404SEF_ONLY_AUTO')));
		$selects->filter_url_type = Sh404sefHelperHtml::buildSelectList($data, $current, $name, $autoSubmit = true, $addSelectAll = true,
			$selectAllTitle);

		// return set of select lists
		return $selects;
	}

	protected function _doAddFilters()
	{
		// component selector
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_COMPONENTS'), 'filter_component',
			JHtml::_('select.options', Sh404sefHelperGeneral::getComponentsList(), 'element', 'name', $this->options->filter_component, true));

		// language list
		$languages = JHtml::_('contentlanguage.existing', $all = false, $translate = true);
		foreach($languages as $id => $language)
		{
			// this will be used to filter short codes in urls
			// so we keep only the short code, not the full language code
			$languages[$id]->value = Sh404sefHelperLanguage::getUrlCodeFromTag($languages[$id]->value);
		}
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_LANGUAGES'), 'filter_language',
			JHtml::_('select.options', $languages, 'value', 'text',
				$this->options->filter_language, false));
	}

}

// now include version (lite/pro) specific things
include_once str_replace('.php', '.' . Sh404sefConfigurationEdition::$id . '.php', basename(__FILE__));
