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

class Sh404sefViewWizard extends ShlMvcView_Base
{
	// we are in 'wizard' view
	protected $_context = 'wizard';

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$document = JFactory::getDocument();

			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			// build the toolbar
			$this->toolbar = $this->_makeToolbarJ3();
		}
		else
		{ // J2
		// if redirecting to another page, we need to simply send some javascript
		// to : a / close the popup, b / redirect the parent page to where we
		// want to go
		// push a title
			$this->stepTitle = $this->pageTitle;

			if (!empty($this->redirectTo))
			{
				$document = JFactory::getDocument();
				if (!empty($this->redirectTo))
				{
					$js = 'window.addEvent( \'domready\', function () {
      setTimeout( \'shRedirectTo()\', 100);
    });
    function shRedirectTo() {
      parent.window.location="' . $this->redirectTo . '";
    }

    ';
					$document->addScriptDeclaration($js);
				}
			}
			else
			{
				// build the toolbar
				$this->toolbar = $this->_makeToolbarJ2();

				// add our own css
				JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/wizard.css');
			}

			// collect any error
			$this->errors = $this->getErrors();
		}

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
		// if redirect is set, no toolbar
		if (!empty($this->redirectTo))
		{
			return;
		}

		// Get the JComponent instance of JToolBar
		$bar = JToolBar::getInstance('toolbar');

		// add path to our custom buttons
		$bar->addButtonPath(JPATH_COMPONENT . '/' . 'classes');

		// display all buttons we are supposed to display
		foreach ($this->visibleButtonsList as $button)
		{
			// we cannot use Joomla's buttons from a popup, as they use href="#" which causes the page to load in parallel with
			// closing of the popup. Need use href="javascript: void(0);"
			$bar
				->appendButton('Shpopupstandardbutton', $button, JText::_('COM_SH404SEF_WIZARD_' . strtoupper($button)), $task = $button,
					$list = false);
		}

		return $bar;
	}

	/**
	 * Create toolbar for current view
	 *
	 * @param midxed $params
	 */
	private function _makeToolbarJ3($params = null)
	{
		// if redirect is set, no toolbar
		if (!empty($this->redirectTo))
		{
			return;
		}

		// Get the JComponent instance of JToolBar
		$bar = JToolBar::getInstance('toolbar');

		// prepare configuration button
		$bar->addButtonPath(SHLIB_ROOT_PATH . 'toolbarbutton');

		// display all buttons we are supposed to display
		$buttonsGroup = array();
		$this->visibleButtonsList = array_reverse($this->visibleButtonsList);
		foreach ($this->visibleButtonsList as $buttonType)
		{
			$button = array();
			switch ($buttonType)
			{
				case 'next':
					$button['type'] = 'primary';
					$button['onclick'] = "Joomla.submitbutton('next');";
					break;
				case 'previous':
					$button['type'] = '';
					$button['onclick'] = "Joomla.submitbutton('previous');";
					;
					break;
				case 'terminate':
					$button['type'] = 'success';
					$button['onclick'] = "Joomla.submitbutton('terminate');";
					;
					break;
				case 'cancel':
					$button['type'] = '';
					$button['onclick'] = "Joomla.submitbutton('cancel');";
					;
					break;
			}
			$button['size'] = 'large';
			$button['text'] = JText::_('COM_SH404SEF_WIZARD_' . strtoupper($buttonType));
			// build a list of buttons
			// store button in button group
			$buttonsGroup[] = array($button);

		}
		$bar = ShlHtmlBs_Helper::buttonsToolbar($buttonsGroup);
		return $bar;
	}
}
