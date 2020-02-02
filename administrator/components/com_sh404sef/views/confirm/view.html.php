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

class Sh404sefViewConfirm extends ShlMvcView_Base
{
	// we are in 'editurl' view
	protected $_context = 'confirm';

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();

		// get action
		$this->task = empty($this->task) ? 'delete' : $this->task;

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/j3_list.css');
			if (!empty($this->redirectTo) && !empty($this->message))
			{
				// redirecting to parent page, but there's a message
				// let's enqueue it, so it's displayed after the redirect
				JFactory::getApplication()->enqueueMessage($this->message);
			}

			// insert bootstrap theme
			ShlHtml_Manager::getInstance()->addAssets(JFactory::getDocument());
		}
		else
		{  // J2
			// if redirecting to another page, we need to simply send some javascript
			// to : a / close the popup, b / redirect the parent page to where we
			// want to go
			if (!empty($this->redirectTo))
			{
				$js = 'window.addEvent( \'domready\', function () {
      setTimeout( \'shRedirectTo()\', 2000);
    });
    function shRedirectTo() {
      parent.window.location="' . $this->redirectTo . '";
      window.parent.SqueezeBox.close();
    }
    ';
				$document = JFactory::getDocument();
				$document->addScriptDeclaration($js);
			}
			else
			{
				// build the toolbar
				$toolBar = $this->_makeToolbar();
				$this->toolbar = $toolBar;

				// add confirmation phrase to toolbar
				$this->toolbarTitle = '<div class="headerconfirm" >' . JText::_('COM_SH404SEF_CONFIRM_TITLE') . '</div>';

				// link to  custom javascript
				JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/edit.js');
			}

			// add our own css
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/confirm.css');
		}

		// hack to in case of error
		$this->refreshAfter = JFactory::getApplication()->input->getCmd('refreshafter');

		// now display normally
		parent::display($this->joomlaVersionPrefix);
	}

	/**
	 * Create toolbar for current view
	 *
	 * @param midxed $params
	 */
	private function _makeToolbar($params = null)
	{
		// if redirect is set, no toolbar
		if (!empty($this->redirectTo))
		{
			return;
		}

		// Get the JComponent instance of JToolBar
		$bar = JToolBar::getInstance('toolbar');

		// add save button as an ajax call
		$bar->addButtonPath(JPATH_COMPONENT . '/' . 'classes');
		$params['class'] = 'modalediturl';
		$params['id'] = 'modalconfirmconfirm';
		$params['closewindow'] = 1;
		$bar
			->appendButton('Shajaxbutton', $this->task, JText::_('Delete'),
				'index.php?option=com_sh404sef&c=editurl&shajax=1&tmpl=component&task=' . $this->task, $params);

		// other button are standards
		JToolBarHelper::spacer();
		JToolBarHelper::divider();
		JToolBarHelper::spacer();
		// we cannot use Joomla's cancel button from a popup, as they use href="#" which causes the page to load in parallel with
		// closing of the popup. Need use href="javascript: void(0);"
		$bar->appendButton('Shpopupstandardbutton', 'cancel', JText::_('Cancel'), $task = 'cancel', $list = false);

		return $bar;
	}
}
