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

class Sh404sefViewAnalytics extends ShlMvcView_Base
{
	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$this->footerText = JText::sprintf('COM_SH404SEF_FOOTER_' . strtoupper(Sh404sefConfigurationEdition::$id),
				Sh404sefFactory::getConfig()->version, Sh404sefConfigurationEdition::$name, date('Y'));

		// prepare the view, based on request
		// do we force reading updates from server ?
		$options = Sh404sefHelperAnalytics::getRequestOptions();

		// push display options into template
		$this->options = $options;

		// Get the JComponent instance of JToolBar
		$bar = JToolBar::getInstance('toolbar');

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$document = JFactory::getDocument();
			ShlHtml_Manager::getInstance($document)
				->addAssets($document)
				->addSpinnerAssets($document);

			// render submenu sidebar
			$this->sidebar = Sh404sefHelperHtml::renderSubmenu();

			// add custom css
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_list.css');

			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			// add title
			JToolbarHelper::title('sh404SEF: ' . JText::_('COM_SH404SEF_ANALYTICS_MANAGER'), 'sh404sef-toolbar-title');

			// needed javascript
			jimport('joomla.html.html.bootstrap');
			JHtml::_('formbehavior.chosen', 'select');

			// add Joomla calendar behavior, needed to input start and end dates
			if ($options['showFilters'] == 'yes')
			{
				JHTML::_('behavior.calendar');
			}

			// prepare configuration button
			if (Sh404sefHelperAcl::userCan('sh404sef.view.configuration'))
			{
				$params = array();
				$params['class'] = 'modaltoolbar btn-success';
				$params['size'] = Sh404sefFactory::getPConfig()->windowSizes['configuration'];
				$params['buttonClass'] = 'btn-success btn btn-small modal';
				$params['iconClass'] = 'icon-options';
				$url = 'index.php?option=com_sh404sef&tmpl=component&c=configuration&view=configuration&component=com_sh404sef&hidemainmenu=1';
				// prepare configuration button
				$bar->addButtonPath(SHLIB_ROOT_PATH . 'toolbarbutton');
				$bar
					->appendButton('J3popuptoolbarbutton', 'configj3', JText::_('COM_SH404SEF_CONFIGURATION'), $url, $params['size']['x'],
						$params['size']['y'], $top = 0, $left = 0, $onClose = '', $title = '', $params);
			}

			// separator
			JToolBarHelper::spacer(20);

			// save progress div
			$html = '<div class="wbl-spinner-black" id="toolbar-sh404sef-spinner"></div>';
			$bar->appendButton('custom', $html, 'sh-progress-button-cpprogress');

			// add quick control panel loader
			$js = 'jQuery(document).ready(function(){  shSetupAnalytics({report:" ' . $options['report'] . '"});});';
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);

		}
		else
		{
			// add Joomla calendar behavior, needed to input start and end dates
			if ($options['showFilters'] == 'yes')
			{
				JHTML::_('behavior.calendar');
			}
			// add tooltips handler
			JHTML::_('behavior.tooltip');

			// add title
			$app = JFactory::getApplication();
			$title = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_ANALYTICS_MANAGER'), $icon = 'sh404sef',
				$class = 'sh404sef-toolbar-title');
			JFactory::getApplication()->JComponentTitle = $title;

			// add a div to display our ajax-call-in-progress indicator
			$bar->addButtonPath( JPATH_COMPONENT . '/' . 'classes');
			$html = '<div id="sh-progress-cpprogress"></div>';
			$bar->appendButton( 'custom', $html, 'sh-progress-button-cpprogress');

			// add modal handler for configuration
			JHTML::_('behavior.modal');
			$configbtn = '<a class="modal" href="index.php?option=com_sh404sef&tmpl=component&c=configuration&view=configuration&component=com_sh404sef&hidemainmenu=1" rel="{handler: \'iframe\', size: {x: window.getSize().x*0.90, y: window.getSize().y*0.90}, onClose: function() {}}"><span class="icon-32-options"></span>'
					. JText::_('COM_SH404SEF_CONFIGURATION') . '</a>';
			if (Sh404sefHelperAcl::userCan('sh404sef.view.configuration'))
			{
				$bar->appendButton('custom', $configbtn, 'sh-configbutton-button');
			}
			// add quick control panel loader
			$js = 'window.addEvent(\'domready\', function(){  shSetupAnalytics({report:" ' . $options['report'] . '"});});';
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);
		}

		// call methods to prepare display based on report type
		$method = '_makeView' . ucfirst($options['report']);
		if (is_callable(array($this, $method)))
		{
			$this->$method($tpl);
		}

		// add our javascript
		JHTML::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/' . $this->joomlaVersionPrefix . '_cp.' . Sh404sefConfigurationEdition::$id . '.js');

		// add our own css
		JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_cp.css');

		// flag to know if we should display placeholder for ajax fillin
		$this->isAjaxTemplate = true;

		parent::display($this->joomlaVersionPrefix);
	}

}
