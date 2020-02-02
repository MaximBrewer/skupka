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

class Sh404sefViewEditnotfound extends ShlMvcView_Base
{
	// we are in 'editurl' view
	protected $_context = 'editnotfound';

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

		// read url data from model
		$url = $model->getUrl($notFoundUrlId);

		// and push url into the template for display
		$this->url = $url;

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			// add title
			JToolbarHelper::title('sh404SEF: ' . JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT'));

			// CSS
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/configuration.css');
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/j3_list.css');

			// add tooltips
			// @TODO replace with a viable jQuery equivalent
			JHTML::_('behavior.tooltip');

			// insert bootstrap theme
			ShlHtml_Manager::getInstance()->addAssets(JFactory::getDocument());
		}
		else
		{
			// build the toolbar
			$toolBar = $this->_makeToolbar();
			$this->toolbar = $toolBar;

			// add title.
			$this->toolbarTitle = Sh404sefHelperGeneral::makeToolbarTitle(JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT'), $icon = 'sh404sef',
				$class = 'sh404sef-toolbar-title');

			// add tooltips
			JHTML::_('behavior.tooltip');

		}

		// link to  custom javascript
		JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/' . $this->joomlaVersionPrefix . '_edit.js');

		// add our own css
		JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_editurl.css');

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

		// Get the JComponent instance of JToolBar
		$bar = JToolBar::getInstance('toolbar');

		// add save button as an ajax call
		$bar->addButtonPath(JPATH_COMPONENT . '/' . 'classes');
		$params['class'] = 'modalediturl';
		$params['id'] = 'modalediturlsave';
		$params['closewindow'] = 1;
		$bar->appendButton('Shajaxbutton', 'save', 'Save', "index.php?option=com_sh404sef&c=editnotfound&task=save&shajax=1&tmpl=component", $params);

		// add apply button as an ajax call
		$params['id'] = 'modalediturlapply';
		$params['closewindow'] = 0;
		$bar
			->appendButton('Shajaxbutton', 'apply', 'Apply', "index.php?option=com_sh404sef&c=editnotfound&task=apply&shajax=1&tmpl=component",
				$params);

		// other button are standards
		$bar->appendButton('Standard', 'back', 'Back', 'back', false, false);
		JToolBarHelper::cancel('cancel');

		return $bar;
	}
}
