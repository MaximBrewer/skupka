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

class Sh404sefViewNotfound extends ShlMvcView_Base
{
	// we are in 'urls' view
	protected $_context = 'notfound';

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$this->refreshAfter = JFactory::getApplication()->input->getCmd('refreshafter');

		// get model and update context with current
		$model = $this->getModel();
		$model->updateContext($this->_context . '.' . $this->getLayout());

		// get url id
		$notFoundUrlId = JRequest::getInt('notfound_url_id');

		// read url data from model. This is the 404 request we want to
		// redirect to something else
		// must be called before model->getList()
		$url = $model->getUrl($notFoundUrlId);

		// and push url into the template for display
		$this->url = $url;

		// attach data, according to specific layout requested
		if ($this->getLayout() == 'default')
		{
			$this->_attachDataDefault();
		}

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			// add our own css
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_list.css');

			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			// variable for modal, not used in 3..x+
			$params = array();

			if ($this->getLayout() == 'default')
			{
				// add display filters
				$this->_addFilters();
			}

			// insert bootstrap theme
			ShlHtml_Manager::getInstance()->addAssets(JFactory::getDocument());
		}
		else
		{
			// add our own css
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_urls.css');
			// add our own css
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/list.css');

			// link to  custom javascript
			JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/list.js');
			// link to  custom javascript
			JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/notfound.js');

			// add behaviors and styles as needed
			$modalSelector = 'a.modalediturl';
			$js = '\\function(){window.parent.shAlreadySqueezed = false;if(window.parent.shReloadModal) {parent.window.location=\''
				. $this->defaultRedirectUrl . '\';window.parent.shReloadModal=true}}';
			$params = array('overlayOpacity' => 0, 'classWindow' => 'sh404sef-popup', 'classOverlay' => 'sh404sef-popup', 'onClose' => $js);
			Sh404sefHelperHtml::modal($modalSelector, $params);

			$options = $model->getDisplayOptions();
			$this->optionsSelect = $this->_makeOptionsSelect($options);

			// add confirmation phrase to toolbar
			$this->toolbarTitle = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_NOT_FOUND_SELECT_REDIRECT'), $icon = 'sh404sef',
				$class = 'sh404sef-toolbar-title');
			$this->_makeToolbarDefaultJ2($params);
		}

		// now display normally
		parent::display($this->joomlaVersionPrefix);

	}

	/**
	 * Push data needed for display into the view
	 * for the default layout
	 */
	private function _attachDataDefault()
	{
		// get a notFound model
		$model = $this->getModel();

		// current options
		$options = (object) array('layout' => $this->getLayout());

		// check if we have similar urls, if not switch to displaying all SEF
		// make sure we use latest user state
		$model->updateContextData();
		$filters = $model->getDisplayOptions();
		$this->filterSimilarUrls = $filters->filter_similar_urls;
		if ($filters->filter_similar_urls)
		{
			$total = $model->getTotal($options);
			if (empty($total))
			{
				// switch to show all SEF
				$model->setDisplayOptions('filter_similar_urls', 0);
				$this->filterSimilarUrls = false;

				// reset data in model, as it has been cached from getting the total
				$model->resetData();

				// and add a message to tell user
				$this->alertMsg = JText::_('COM_SH404SEF_NOT_FOUND_SWITCHING_TO_DISPLAY_ALL_SEF');
			}
		}

		// read data from model
		$list = $model->getList($options);

		// and push it into the view for display
		$this->items = $list;
		$this->itemCount = is_array($this->items) ? count($this->items) : 0;
		$this->pagination = $model->getPagination($options);
		$options = $model->getDisplayOptions();
		$this->options = $options;

		// additional text displayed
		$this->mainTitle = JText::_('COM_SH404SEF_NOT_FOUND_SELECT_REDIRECT_FOR');
	}

	/**
	 * Create toolbar for current view
	 *
	 * @param midxed $params
	 */
	private function _makeToolbarDefaultJ2($params = null)
	{
		// add confirmation phrase to toolbar
		$this->toolbarTitle = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_NOT_FOUND_SELECT_REDIRECT'), $icon = 'sh404sef',
			$class = 'sh404sef-toolbar-title');

		// Get the JComponent instance of JToolBar
		$bar = JToolBar::getInstance('toolbar');

		// add save button as an ajax call
		$bar->addButtonPath(JPATH_COMPONENT . '/' . 'classes');
		$params['class'] = 'modalediturl';
		$params['id'] = 'modalediturlsave';
		$params['closewindow'] = 1;
		$bar
			->appendButton('Shajaxbutton', 'selectnfredirect', JText::_('COM_SH404SEF_NOT_FOUND_SELECT_REDIRECT'),
				"index.php?option=com_sh404sef&c=notfound&task=selectnfredirect&shajax=1&tmpl=component", $params);

		// other button are standards
		$bar->appendButton('Standard', 'back', JText::_('COM_SH404SEF_BACK_TO_NOT_FOUND'), 'backPopup', false, false);

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

		// select similar urls or all
		$current = $options->filter_similar_urls;
		$name = 'filter_similar_urls';
		$data = array(array('id' => 1, 'title' => JText::_('COM_SH404SEF_NOT_FOUND_SHOW_SIMILAR_URLS')),
			array('id' => 0, 'title' => JText::_('COM_SH404SEF_NOT_FOUND_SHOW_ALL_URLS')));
		$selects->filter_similar_urls = Sh404sefHelperHtml::buildSelectList($data, $current, $name, $autoSubmit = true, $addSelectAll = false);

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

		// select aliases
		$data = array(array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_ALIASES, 'text' => JText::_('COM_SH404SEF_ONLY_ALIASES')),
			array('value' => Sh404sefHelperGeneral::COM_SH404SEF_NO_ALIASES, 'text' => JText::_('COM_SH404SEF_ONLY_NO_ALIASES')));
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_ALIASES'), 'filter_alias',
			JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_alias, true));

		// select custom
		$data = array(array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_CUSTOM, 'text' => JText::_('COM_SH404SEF_ONLY_CUSTOM')),
			array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_AUTO, 'text' => JText::_('COM_SH404SEF_ONLY_AUTO')));
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_URL_TYPES'), 'filter_url_type',
			JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_url_type, true));

		// select similar urls or all
		$data = array(array('value' => 1, 'text' => JText::_('COM_SH404SEF_NOT_FOUND_SHOW_SIMILAR_URLS')),
			array('value' => 0, 'text' => JText::_('COM_SH404SEF_NOT_FOUND_SHOW_ALL_URLS')));
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_NOT_FOUND_SHOW_SIMILAR_URLS'), 'filter_similar_urls',
			JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_similar_urls, true), true);
	}
}
