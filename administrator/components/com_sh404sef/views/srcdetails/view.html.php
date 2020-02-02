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

class Sh404sefViewSrcdetails extends ShlMvcView_Base
{
	// we are in 'urls' view
	protected $_context = 'srcdetails';

	public function display($tpl = null)
	{
		// get Joomla version prefix to load appropriate layout files
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$this->refreshAfter = JFactory::getApplication()->input->getCmd('refreshafter');

		// get model and update context with current
		$model = $this->getModel();
		$model->updateContext($this->_context . '.' . $this->getLayout());

		// attach data, according to specific layout requested
		if ($this->getLayout() == 'default')
		{
			$this->_attachDataDefault();
		}


		// add our own css
		JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_list.css');

		// add modal css and js
		ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
		ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

		if ($this->getLayout() == 'default')
		{
			// add display filters
			$this->_addFilters();
		}

		// insert bootstrap theme
		ShlHtml_Manager::getInstance()->addAssets(JFactory::getDocument());

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

		// read url data from model. This is the request we want to
		// redirect to something else
		// must be called before model->getList()

		// and push url into the template for display
		$this->url = $model->getUrl();

		// read data from model
		$list = $model->getList($options);

		// and push it into the view for display
		$this->items = $list;
		$this->itemCount = is_array($this->items) ? count($this->items) : 0;
		$this->pagination = $model->getPagination($options);
		$options = $model->getDisplayOptions();
		$this->options = $options;
	}

	private function _addFilters()
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
