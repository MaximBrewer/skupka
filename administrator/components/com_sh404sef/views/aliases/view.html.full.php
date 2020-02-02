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

class Sh404sefViewAliases extends ShlMvcView_Base
{

	// we are in 'urls' view
	protected $_context = 'aliases';

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$this->footerText = JText::sprintf('COM_SH404SEF_FOOTER_' . strtoupper(Sh404sefConfigurationEdition::$id),
				Sh404sefFactory::getConfig()->version, Sh404sefConfigurationEdition::$name, date('Y'));
		

		// get model and update context with current
		$model = $this->getModel();
		$context = $model->setContext($this->_context . '.' . $this->getLayout());

		// read data from model
		$list = $model->getList((object) array('layout' => $this->getLayout()));

		// and push it into the view for display
		$this->items = $list;
		$this->itemCount = count($this->items);
		$this->pagination = $model->getPagination();
		$options = $model->getDisplayOptions();
		$this->options = $options;
		$this->helpMessage = JText::_('COM_SH404SEF_ALIASES_HELP_NEW_ALIAS');

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$document = JFactory::getDocument();

			// add our own css
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_list.css');
			JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/j3.js');

			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			// add display filters
			$this->_addFilters();

			// variable for modal, not used in 3..x+
			$params = array();

			// render submenu sidebar
			$this->sidebar = Sh404sefHelperHtml::renderSubmenu();

			// insert bootstrap theme
			ShlHtml_Manager::getInstance()->addAssets(JFactory::getDocument());
		}
		else
		{
			// add link to css
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
		$toolbarMethod = '_makeToolbar' . ucfirst($this->joomlaVersionPrefix);
		if (is_callable(array($this, $toolbarMethod)))
		{
			$this->$toolbarMethod($params);
		}

		// now display normally
		parent::display($this->joomlaVersionPrefix);
	}

	/**
	 * Create toolbar for default layout view
	 *
	 * @param midxed $params
	 */
	private function _makeToolbarJ2($params = null)
	{
		// Get the JComponent instance of JToolBar
		$bar = JToolBar::getInstance('toolbar');

		// add title
		$title = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_ALIASES_MANAGER'), $icon = 'sh404sef',
			$class = 'sh404sef-toolbar-title');
		JFactory::getApplication()->JComponentTitle = $title;

		// add "New url" button
		$bar->addButtonPath(JPATH_COMPONENT . '/' . 'classes');

		// add edit button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 800, 'y' => 600);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=editalias&task=edit&tmpl=component&view=editurl&startOffset=2';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'edit', $url, JText::_('Edit'), $msg = '', $task = 'edit', $list = true, $hidemenu = true, $params);

		// add delete button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 300);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=editalias&task=confirmdelete&tmpl=component';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'delete', $url, JText::_('Delete'), $msg = JText::_('VALIDDELETEITEMS', true), $task = 'delete',
				$list = true, $hidemenu = true, $params);

		// separator
		JToolBarHelper::divider();

		// add import button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 400);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=import&opsubject=aliases';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'import', $url, JText::_('COM_SH404SEF_IMPORT_BUTTON'), $msg = '', $task = 'import',
				$list = false, $hidemenu = true, $params);

		// add export button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 300);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=export&opsubject=aliases';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'export', $url, JText::_('COM_SH404SEF_EXPORT_BUTTON'), $msg = '', $task = 'export',
				$list = false, $hidemenu = true, $params);

		// separator
		JToolBarHelper::divider();

		// edit home page button
		$params['class'] = 'modalediturl';
		$params['size'] = array('x' => 800, 'y' => 600);
		$js = '\\function(){window.parent.shAlreadySqueezed = false;if(window.parent.shReloadModal) parent.window.location=\''
			. $this->defaultRedirectUrl . '\';window.parent.shReloadModal=true}';
		$params['onClose'] = $js;
		$bar
			->appendButton('Shpopupbutton', 'home', JText::_('COM_SH404SEF_HOME_PAGE_ICON'),
				"index.php?option=com_sh404sef&c=editalias&task=edit&view=editurl&home=1&tmpl=component&startOffset=1", $params);

		// add modal handler for configuration
		JHTML::_('behavior.modal');
		if (Sh404sefHelperAcl::userCan('sh404sef.view.configuration'))
		{
			// separator
			JToolBarHelper::divider();

			$configbtn = '<a class="modal" href="index.php?option=com_sh404sef&tmpl=component&c=configuration&view=configuration&component=com_sh404sef&hidemainmenu=1" rel="{handler: \'iframe\', size: {x: window.getSize().x*0.90, y: window.getSize().y*0.90}, onClose: function() {}}"><span class="icon-32-options"></span>'
				. JText::_('COM_SH404SEF_CONFIGURATION') . '</a>';
			$bar->appendButton('custom', $configbtn, 'sh-configbutton-button');
		}
	}

	private function _makeToolbarJ3($params = null)
	{
		// add title
		JToolbarHelper::title('sh404SEF: ' . JText::_('COM_SH404SEF_ALIASES_MANAGER'), 'sh404sef-toolbar-title');

		// add "New url" button
		$bar = JToolBar::getInstance('toolbar');

		// prepare configuration button
		$bar->addButtonPath(SHLIB_ROOT_PATH . 'toolbarbutton');

		// add edit button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['editurl'];
		$params['buttonClass'] = 'btn btn-small btn-primary';
		$params['iconClass'] = 'icon-edit icon-white';
		$params['checkListSelection'] = true;
		$url = 'index.php?option=com_sh404sef&c=editalias&task=edit&tmpl=component&view=editurl&startOffset=2';
		$bar
			->appendButton('J3popuptoolbarbutton', 'edit', JText::_('JTOOLBAR_EDIT'), $url, $params['size']['x'], $params['size']['y'], $top = 0,
				$left = 0, $onClose = '', $title = '', $params);

		// delete button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['confirm'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-trash';
		$params['checkListSelection'] = true;
		$url = 'index.php?option=com_sh404sef&c=editalias&task=confirmdelete&tmpl=component';
		$bar
			->appendButton('J3popuptoolbarbutton', 'delete', JText::_('JTOOLBAR_DELETE'), $url, $params['size']['x'], $params['size']['y'], $top = 0,
				$left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_CONFIRM_TITLE'), $params);

		// separator
		JToolBarHelper::spacer(20);

		// add import button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['import'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-upload';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=import&opsubject=aliases';
		$bar
			->appendButton('J3popuptoolbarbutton', 'import', JText::_('COM_SH404SEF_IMPORT_BUTTON'), $url, $params['size']['x'],
				$params['size']['y'], $top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_IMPORTING_TITLE'), $params);

		// add export button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['export'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-download';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=export&opsubject=aliases';
		$bar
			->appendButton('J3popuptoolbarbutton', 'export', JText::_('COM_SH404SEF_EXPORT_BUTTON'), $url, $params['size']['x'],
				$params['size']['y'], $top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_EXPORTING_TITLE'), $params);

		// separator
		JToolBarHelper::spacer(20);

		// edit home page button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['editurl'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-home';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=editalias&task=edit&view=editurl&home=1&tmpl=component&startOffset=1';
		$bar
			->appendButton('J3popuptoolbarbutton', 'home', JText::_('COM_SH404SEF_HOME_PAGE_ICON'), $url, $params['size']['x'], $params['size']['y'],
				$top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_HOME_PAGE_EDIT_TITLE'), $params);

		if (Sh404sefHelperAcl::userCan('sh404sef.view.configuration'))
		{
			// separator
			JToolBarHelper::spacer(20);

			// prepare configuration button
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

		// Select Requested/not Requested/ Requested or not
		$data = array(
			array('value' => Sh404sefHelperGeneral::SHOW_REQUESTED, 'text' => JText::_('COM_SH404SEF_SHOW_REQUESTED_URLS')),
			array('value' => Sh404sefHelperGeneral::SHOW_NOT_REQUESTED, 'text' => JText::_('COM_SH404SEF_SHOW_NEVER_REQUESTED_URLS'))
		);
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_SHOW_REQUESTED_OR_NOT'), 'filter_requested_urls',
			JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_requested_urls, true));
	}
}
