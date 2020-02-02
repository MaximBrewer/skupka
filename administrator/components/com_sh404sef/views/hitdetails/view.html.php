<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      sh404SEF
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      4.7.3.3292
 * @date        2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('Direct Access to this location is not allowed.');
}

class Sh404sefViewHitdetails extends ShlMvcView_Base
{
	// we are in 'urls' view
	protected $_context = 'hitdetails';

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

		// get url id
		//$urlId = JFatc->input->getInt('url_id');

		// read url data from model. This is the request we want to
		// redirect to something else
		// must be called before model->getList()
		$url = $model->getUrl(/*$urlId*/);

		// and push url into the template for display
		$this->url = $url;

		// read data from model
		$list = $model->getList($options);

		// and push it into the view for display
		$this->items = $list;
		$this->itemCount = is_array($this->items) ? count($this->items) : 0;
		$this->pagination = $model->getPagination($options);
		$options = $model->getDisplayOptions();
		$this->options = $options;
		$this->request_type = $model->getRequestType();
	}

	private function _addFilters()
	{
		$data = array(
			array('value' => Sh404sefHelperUrl::IS_EXTERNAL, 'text' => JText::_('COM_SH404SEF_ONLY_EXTERNAL')),
			array('value' => Sh404sefHelperUrl::IS_INTERNAL, 'text' => JText::_('COM_SH404SEF_ONLY_INTERNAL'))
		);
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_SHOW_INTERNAL_AND_EXTERNAL'), 'filter_hit_type',
			JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_hit_type, true));

		$data = array(
			array('value' => Sh404sefHelperGeneral::COLLAPSE_BY_REFERRER, 'text' => JText::_('COM_SH404SEF_COLLAPSE_BY_REFERRER')),
			array('value' => Sh404sefHelperGeneral::COLLAPSE_BY_IP, 'text' => JText::_('COM_SH404SEF_COLLAPSE_BY_IP')),
			array('value' => Sh404sefHelperGeneral::COLLAPSE_BY_USER_AGENT, 'text' => JText::_('COM_SH404SEF_COLLAPSE_BY_USER_AGENT'))
		);
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_COLLAPSE_NONE'), 'filter_collapse',
			JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_collapse, true));

		$data = array(
			array('value' => Sh404sefHelperGeneral::HIDE_EMPTY_REFERRERS, 'text' => JText::_('COM_SH404SEF_HIDE_EMPTY_REFERRERS'))
		);
		JHtmlSidebar::addFilter(JText::_('COM_SH404SEF_SHOW_ALL_REFERRERS'), 'filter_hide_empty',
			JHtml::_('select.options', $data, 'value', 'text', $this->options->filter_hide_empty, false));
	}
}
