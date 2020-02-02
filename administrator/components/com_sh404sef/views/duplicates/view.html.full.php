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

class Sh404sefViewDuplicates extends ShlMvcView_Base
{
	// we are in 'urls' view
	protected $_context = 'duplicates';

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();

		$this->refreshAfter = JFactory::getApplication()->input->getCmd('refreshafter');

		// get model and update context with current
		$model = $this->getModel();
		$model->updateContext($this->_context . '.' . $this->getLayout());

		// read data from model
		$list = $model->getList((object) array('layout' => $this->getLayout(), 'simpleUrlList' => true));

		// and push it into the view for display
		$this->items = $list;
		$this->itemCount = count($this->items);
		$this->pagination = $model->getPagination();
		$options = $model->getDisplayOptions();
		$this->options = $options;
		$this->mainUrl = $model->getMainUrl();

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$document = JFactory::getDocument();

			// add our own css
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_list.css');

			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			// add display filters
			$this->_addFilters();

			// insert bootstrap theme
			ShlHtml_Manager::getInstance()->addAssets(JFactory::getDocument());

			// render submenu sidebar
			$this->sidebar = JHtmlSidebar::render();
		}
		else
		{
			// add confirmation phrase to toolbar
			$this->toolbarTitle = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_DUPLICATE_MANAGER'), $icon = 'sh404sef',
				$class = 'sh404sef-toolbar-title');

			// build the toolbar
			$this->_makeToolbarJ2();

			// add our own css
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_urls.css');
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/list.css');

			// link to  custom javascript
			JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/list.js');

			$this->optionsSelect = $this->_makeOptionsSelect($options);
		}

		// link to  custom javascript
		JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/list.js');

		// now display normally
		parent::display($this->joomlaVersionPrefix);
	}

	/**
	 * Create toolbar for current view
	 *
	 * @param midxed $params
	 */
	private function _makeToolbarJ2($params = null)
	{
		// Get the JComponent instance of JToolBar
		$bar = JToolBar::getInstance('toolbar');

		// add save button as an ajax call
		$bar->appendButton('Standard', 'main', JText::_('COM_SH404SEF_DUPLICATES_MAKE_MAIN'), 'makemainurl', true, true);

		// other button are standards
		$bar->appendButton('Standard', 'back', JText::_('COM_SH404SEF_BACK'), 'backPopup', false, false);

		// push in to the view
		$this->toolbar = $bar;

		return $bar;
	}

	private function _makeOptionsSelect($options)
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

	private function _addFilters()
	{
		// component selector
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_COMPONENTS'), 'filter_component',
			JHtml::_('select.options', Sh404sefHelperGeneral::getComponentsList(), 'element', 'name', $this->options->filter_component, true));

		// language list
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_LANGUAGES'), 'filter_language',
			JHtml::_('select.options', JHtml::_('contentlanguage.existing', $all = false, $translate = true), 'value', 'text',
				$this->options->filter_language, false));

		// select custom
		$data = array(array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_CUSTOM, 'text' => JText::_('COM_SH404SEF_ONLY_CUSTOM')),
			array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_AUTO, 'text' => JText::_('COM_SH404SEF_ONLY_AUTO')));
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_URL_TYPES'), 'filter_url_type',
			JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_url_type, true));
	}
}
