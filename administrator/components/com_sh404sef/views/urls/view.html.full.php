<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

class Sh404sefViewUrls extends Sh404sefViewUrlsBase
{
	protected function _makeOptionsSelect($options)
	{
		// return set of select lists
		return $this->_doMakeOptionsSelect($options);
	}

	/**
	 * Create toolbar for default layout view
	 *
	 * @param midxed $params
	 */
	protected function _makeToolbarDefaultJ2($params = null)
	{
		// add title
		$title = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_SEF_URL_LIST'), $icon = 'sh404sef', $class = 'sh404sef-toolbar-title');
		JFactory::getApplication()->JComponentTitle = $title;

		// add "New url" button
		$bar = JToolBar::getInstance('toolbar');
		$bar->addButtonPath(JPATH_COMPONENT . '/' . 'classes');
		$params['class'] = 'modalediturl';
		$params['size'] = array('x' => 800, 'y' => 600);
		$js = '\\function(){window.parent.shAlreadySqueezed = false;if(window.parent.shReloadModal) parent.window.location=\''
			. $this->defaultRedirectUrl . '\';window.parent.shReloadModal=true}';
		$params['onClose'] = $js;
		$bar->appendButton('Shpopupbutton', 'new', JText::_('New'), "index.php?option=com_sh404sef&c=editurl&task=edit&tmpl=component", $params);

		// add edit button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 800, 'y' => 600);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=editurl&task=edit&tmpl=component';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'edit', $url, JText::_('Edit'), $msg = '', $task = 'edit', $list = true, $hidemenu = true, $params);

		// add delete with duplicates button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 300);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=editurl&task=confirmdeletedeldup&tmpl=component';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'deletedeldup', $url, JText::_('COM_SH404SEF_DELETE_URLS_WITH_DUP'),
				$msg = JText::_('VALIDDELETEITEMS', true), $task = 'delete', $list = true, $hidemenu = true, $params);

		// add delete button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 300);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=editurl&task=confirmdelete&tmpl=component';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'delete', $url, JText::_('Delete'), $msg = JText::_('VALIDDELETEITEMS', true), $task = 'delete',
				$list = true, $hidemenu = true, $params);

		// separator
		JToolBarHelper::divider();

		// add import button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 380);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=import&opsubject=urls';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'import', $url, JText::_('COM_SH404SEF_IMPORT_BUTTON'), $msg = '', $task = 'import',
				$list = false, $hidemenu = true, $params);

		// add import button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 380);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=export&opsubject=urls';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'export', $url, JText::_('COM_SH404SEF_EXPORT_BUTTON'), $msg = '', $task = 'export',
				$list = false, $hidemenu = true, $params);

		// separator
		JToolBarHelper::divider();

		// add purge and purge selected  buttons
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 300);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=urls&task=confirmpurge&tmpl=component';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'purge', $url, JText::_('COM_SH404SEF_PURGE'), $msg = JText::_('VALIDDELETEITEMS', true),
				$task = 'purge', $list = false, $hidemenu = true, $params);

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
				"index.php?option=com_sh404sef&c=editurl&task=edit&home=1&tmpl=component", $params);

		// separator
		JToolBarHelper::divider();

		// add modal handler for configuration
		JHTML::_('behavior.modal');
		$configbtn = '<a class="modal" href="index.php?option=com_sh404sef&tmpl=component&c=configuration&view=configuration&component=com_sh404sef&hidemainmenu=1" rel="{handler: \'iframe\', size: {x: window.getSize().x*0.90, y: window.getSize().y*0.90}, onClose: function() {}}"><span class="icon-32-options"></span>'
			. JText::_('COM_SH404SEF_CONFIGURATION') . '</a>';
		if (Sh404sefHelperAcl::userCan('sh404sef.view.configuration'))
		{
			$bar->appendButton('custom', $configbtn, 'sh-configbutton-button');
		}
	}

	/**
	 * Create toolbar for default layout view
	 *
	 * @param midxed $params
	 */
	protected function _makeToolbarDefaultJ3($params = null)
	{
		// add title
		JToolbarHelper::title('sh404SEF: ' . JText::_('COM_SH404SEF_SEF_URL_LIST'), 'sh404sef-toolbar-title');

		// add "New url" button
		$bar = JToolBar::getInstance('toolbar');

		// prepare configuration button
		$bar->addButtonPath(SHLIB_ROOT_PATH . 'toolbarbutton');

		// add url
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['editurl'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-plus';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=editurl&task=edit&tmpl=component';
		$bar
			->appendButton('J3popuptoolbarbutton', 'new', JText::_('JTOOLBAR_NEW'), $url, $params['size']['x'], $params['size']['y'], $top = 0,
				$left = 0, $onClose = '', $title = '', $params);

		// add edit button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['editurl'];
		$params['buttonClass'] = 'btn btn-small btn-primary';
		$params['iconClass'] = 'icon-apply icon-white';
		$params['checkListSelection'] = true;
		$url = 'index.php?option=com_sh404sef&c=editurl&task=edit&tmpl=component';
		$bar
			->appendButton('J3popuptoolbarbutton', 'edit', JText::_('JTOOLBAR_EDIT'), $url, $params['size']['x'], $params['size']['y'], $top = 0,
				$left = 0, $onClose = '', $title = '', $params);

		// separator
		JToolBarHelper::spacer(20);

		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['confirm'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-trash';
		$params['checkListSelection'] = true;
		$url = 'index.php?option=com_sh404sef&c=editurl&task=confirmdeletedeldup&tmpl=component';
		$bar
			->appendButton('J3popuptoolbarbutton', 'deletedeldup', JText::_('COM_SH404SEF_DELETE_URLS_WITH_DUP'), $url, $params['size']['x'],
				$params['size']['y'], $top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_CONFIRM_TITLE'), $params);

		// add delete button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['confirm'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-trash';
		$params['checkListSelection'] = true;
		$url = 'index.php?option=com_sh404sef&c=editurl&task=confirmdelete&tmpl=component';
		$bar
			->appendButton('J3popuptoolbarbutton', 'delete', JText::_('JTOOLBAR_DELETE'), $url, $params['size']['x'], $params['size']['y'], $top = 0,
				$left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_CONFIRM_TITLE'), $params);

		// separator
		JToolBarHelper::spacer(20);

		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['import'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-upload';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=import&opsubject=urls';
		$bar
			->appendButton('J3popuptoolbarbutton', 'import', JText::_('COM_SH404SEF_IMPORT_BUTTON'), $url, $params['size']['x'],
				$params['size']['y'], $top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_IMPORTING_TITLE'), $params);

		// add import button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['export'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-download';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=export&opsubject=urls';
		$bar
			->appendButton('J3popuptoolbarbutton', 'export', JText::_('COM_SH404SEF_EXPORT_BUTTON'), $url, $params['size']['x'],
				$params['size']['y'], $top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_EXPORTING_TITLE'), $params);

		// separator
		JToolBarHelper::spacer(20);

		// add purge and purge selected  buttons
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['confirm'];
		$params['buttonClass'] = 'btn btn-small btn-danger';
		$params['iconClass'] = 'shl-icon-remove-sign';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=urls&task=confirmpurge&tmpl=component';
		$bar
			->appendButton('J3popuptoolbarbutton', 'purge', JText::_('COM_SH404SEF_PURGE'), $url, $params['size']['x'], $params['size']['y'],
				$top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_CONFIRM_TITLE'), $params);

		// separator
		JToolBarHelper::spacer(20);

		// edit home page button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['editurl'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-home';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=editurl&task=edit&home=1&tmpl=component';
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

	/**
	 * Create toolbar for 404 pages template
	 *
	 * @param midxed $params
	 */
	protected function _makeToolbarView404J2($params = null)
	{

		// Get the JComponent instance of JToolBar
		$bar = JToolBar::getInstance('toolbar');

		// and connect to our buttons
		$bar->addButtonPath(JPATH_COMPONENT . '/' . 'classes');

		// add title
		$title = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_404_MANAGER'), $icon = 'sh404sef', $class = 'sh404sef-toolbar-title');
		JFactory::getApplication()->JComponentTitle = $title;

		// add edit button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 800, 'y' => 600);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=editurl&task=edit&tmpl=component';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'edit', $url, JText::_('Edit'), $msg = '', $task = 'edit', $list = true, $hidemenu = true, $params);

		// add delete button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 300);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=editurl&task=confirmdelete404&tmpl=component';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'delete', $url, JText::_('Delete'), $msg = JText::_('VALIDDELETEITEMS', true), $task = 'delete',
				$list = true, $hidemenu = true, $params);

		// separator
		JToolBarHelper::divider();

		// add import button
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 380);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=export&opsubject=view404';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'export', $url, JText::_('Export'), $msg = '', $task = 'export', $list = false, $hidemenu = true,
				$params);

		// separator
		JToolBarHelper::divider();

		// add purge and purge selected  buttons
		$params['class'] = 'modaltoolbar';
		$params['size'] = array('x' => 500, 'y' => 300);
		unset($params['onClose']);
		$url = 'index.php?option=com_sh404sef&c=urls&task=confirmpurge404&tmpl=component';
		$bar
			->appendButton('Shpopuptoolbarbutton', 'purge', $url, JText::_('COM_SH404SEF_PURGE'), $msg = JText::_('VALIDDELETEITEMS', true),
				$task = 'purge', $list = false, $hidemenu = true, $params);

		// separator
		JToolBarHelper::divider();

		// add modal handler for configuration
		JHTML::_('behavior.modal');
		$configbtn = '<a class="modal" href="index.php?option=com_sh404sef&tmpl=component&c=configuration&view=configuration&component=com_sh404sef&hidemainmenu=1" rel="{handler: \'iframe\', size: {x: window.getSize().x*0.90, y: window.getSize().y*0.90}, onClose: function() {}}"><span class="icon-32-options"></span>'
			. JText::_('COM_SH404SEF_CONFIGURATION') . '</a>';
		$bar->appendButton('custom', $configbtn, 'sh-configbutton-button');

	}

	/**
	 * Create toolbar for 404 pages template
	 *
	 * @param midxed $params
	 */
	protected function _makeToolbarView404J3($params = null)
	{
		// separator
		JToolBarHelper::divider();

		// add title
		JToolbarHelper::title('sh404SEF: ' . JText::_('COM_SH404SEF_404_MANAGER'), 'sh404sef-toolbar-title');

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
		$url = 'index.php?option=com_sh404sef&c=editurl&task=edit&tmpl=component';
		$bar
			->appendButton('J3popuptoolbarbutton', 'edit', JText::_('JTOOLBAR_EDIT'), $url, $params['size']['x'], $params['size']['y'], $top = 0,
				$left = 0, $onClose = '', $title = '', $params);

		// add delete button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['confirm'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-trash';
		$params['checkListSelection'] = true;
		$url = 'index.php?option=com_sh404sef&c=editurl&task=confirmdelete404&tmpl=component';
		$bar
			->appendButton('J3popuptoolbarbutton', 'delete', JText::_('JTOOLBAR_DELETE'), $url, $params['size']['x'], $params['size']['y'], $top = 0,
				$left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_CONFIRM_TITLE'), $params);

		// separator
		JToolBarHelper::spacer(20);

		// add export button
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['export'];
		$params['buttonClass'] = 'btn btn-small';
		$params['iconClass'] = 'icon-download';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=wizard&task=start&tmpl=component&optype=export&opsubject=view404';
		$bar
			->appendButton('J3popuptoolbarbutton', 'export', JText::_('COM_SH404SEF_EXPORT_BUTTON'), $url, $params['size']['x'],
				$params['size']['y'], $top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_EXPORTING_TITLE'), $params);

		// separator
		JToolBarHelper::spacer(20);

		// add purge buttons
		$params = array();
		$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['confirm'];
		$params['buttonClass'] = 'btn btn-small btn-danger';
		$params['iconClass'] = 'shl-icon-remove-sign';
		$params['checkListSelection'] = false;
		$url = 'index.php?option=com_sh404sef&c=urls&task=confirmpurge404&tmpl=component';
		$bar
			->appendButton('J3popuptoolbarbutton', 'purge', JText::_('COM_SH404SEF_PURGE404'), $url, $params['size']['x'], $params['size']['y'],
				$top = 0, $left = 0, $onClose = '', $title = JText::_('COM_SH404SEF_CONFIRM_TITLE'), $params);

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

	protected function _doAddFilters()
	{
		if ('view404' != $this->getLayout())
		{
			parent::_doAddFilters();

			if (!$this->slowServer)
			{
				// select duplicates
				$data = array(array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_DUPLICATES, 'text' => JText::_('COM_SH404SEF_ONLY_DUPLICATES')),
					array('value' => Sh404sefHelperGeneral::COM_SH404SEF_NO_DUPLICATES, 'text' => JText::_('COM_SH404SEF_ONLY_NO_DUPLICATES')));
				JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_DUPLICATES'), 'filter_duplicate',
					JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_duplicate, true));

				// select aliases
				$data = array(array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_ALIASES, 'text' => JText::_('COM_SH404SEF_ONLY_ALIASES')),
					array('value' => Sh404sefHelperGeneral::COM_SH404SEF_NO_ALIASES, 'text' => JText::_('COM_SH404SEF_ONLY_NO_ALIASES')));
				JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_ALIASES'), 'filter_alias',
					JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_alias, true));
			}

			// select custom
			$data = array(array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_CUSTOM, 'text' => JText::_('COM_SH404SEF_ONLY_CUSTOM')),
				array('value' => Sh404sefHelperGeneral::COM_SH404SEF_ONLY_AUTO, 'text' => JText::_('COM_SH404SEF_ONLY_AUTO')));
			JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_ALL_URL_TYPES'), 'filter_url_type',
				JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_url_type, true));

			// Select Requested/not Requested/ Requested or not
			$data = array(
				array('value' => Sh404sefHelperGeneral::SHOW_REQUESTED, 'text' => JText::_('COM_SH404SEF_SHOW_REQUESTED_URLS')),
				array('value' => Sh404sefHelperGeneral::SHOW_NOT_REQUESTED, 'text' => JText::_('COM_SH404SEF_SHOW_NEVER_REQUESTED_URLS'))
			);
			JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_SHOW_REQUESTED_OR_NOT'), 'filter_requested_urls',
				JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_requested_urls, true));
		}
		else
		{
			// 404 view: select internal/external/all
			$data = array(array('value' => Sh404sefHelperUrl::IS_EXTERNAL, 'text' => JText::_('COM_SH404SEF_ONLY_EXTERNAL')),
				array('value' => Sh404sefHelperUrl::IS_INTERNAL, 'text' => JText::_('COM_SH404SEF_ONLY_INTERNAL')));
			JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_SHOW_INTERNAL_AND_EXTERNAL'), 'filter_hit_type',
				JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_hit_type, true));
		}
	}
}
