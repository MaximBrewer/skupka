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

jimport('joomla.application.component.view');

class Sh404sefViewEditurl extends ShlMvcView_Base
{
	// we are in 'editurl' view
	protected $_context = 'editurl';

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$this->refreshAfter = JFactory::getApplication()->input->getCmd('refreshafter');

		// get model and update context with current
		$model = $this->getModel();
		$model->updateContext($this->_context . '.' . $this->getLayout());

		// get url id
		$cid = JRequest::getVar('cid', array(0), 'default', 'array');
		$cid = intval($cid[0]);

		// get home page flag, and make sure id is 0 if editing home data
		$home = JRequest::getInt('home');
		if ($home == 1)
		{
			$cid = 0;
		}
		$this->home = $home;

		// optional starting pane in case of tabbed edition
		$startOffset = JRequest::getInt('startOffset', 0);
		$this->startOffset = $startOffset;

		// read url data from model
		$url = $model->getById($cid);

		// if editing home, set home url
		if ($this->home == 1)
		{
			$url->set('newurl', sh404SEF_HOMEPAGE_CODE);
		}

		// controllers may forbid to edit sef or non-sef urls
		$noUrlEditing = empty($this->noUrlEditing) ? false : $this->noUrlEditing;
		$this->noUrlEditing = $noUrlEditing;

		// and push url into the template for display
		$this->url = $url;

		// we only allow editing of non-sef url for new urls, that is when non sef url field is empty
		// of for 404s, when we have a sef but no non-sef
		$newUrl = $url->get('newurl');
		$this->canEditNewUrl = empty($newUrl);

		// are we creating a new url rcord or editing an existing one
		$oldUrl = $url->get('oldurl');
		$existingUrl = !empty($newUrl) || !empty($oldUrl);

		// now read meta for this url, using meta  model
		if ($existingUrl)
		{
			$metaModel = ShlMvcModel_Base::getInstance('metas', 'Sh404sefModel');
			$metas = $metaModel->getList((object) array('newurl' => $url->get('newurl')), $returnZeroElement = true);
			$meta = $metas[0];
		}
		else
		{
			$meta = JTable::getInstance('metas', 'Sh404sefTable');
		}
		$this->meta = $meta;

		// now read aliases for this url, using an aliases model
		if (!empty($newUrl) && (!empty($oldUrl) || $this->home == 1))
		{
			$aliasModel = ShlMvcModel_Base::getInstance('aliases', 'Sh404sefModel');
			$aliases = $aliasModel->getDisplayableList((object) array('newurl' => $url->get('newurl')));
		}
		else
		{
			$aliases = '';
		}
		$this->aliases = $aliases;

		// now read pageid for this url, using pageid model
		$pageidModel = ShlMvcModel_Base::getInstance('pageids', 'Sh404sefModel');
		$pageids = $pageidModel->getList((object) array('newurl' => $url->get('newurl')), $returnZeroElement = true);
		$this->pageid = $pageids[0];

		// url used to create QRCode
		$sefConfig = Sh404sefFactory::getConfig();
		$this->qrCodeUrl = JUri::root() . ltrim($sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') . $this->url->get('oldurl');

		// push social seo data
		$this->_pushDataSocial_seo();

		// find active starting panel
		$this->activePanel = $this->_getActiveStartingPanel();

		// add title. If there is an id, we are editing an existing url, or else we create a new one
		// other case : edting home page, there is a specific title
		if ($this->home == 1)
		{
			$title = JText::_('COM_SH404SEF_HOME_PAGE_EDIT_TITLE');
		}
		else
		{
			$title = $url->get('id') ? JText::_('COM_SH404SEF_EDIT_URL_TITLE') : JText::_('COM_SH404SEF_ADD_URL_TITLE');
		}

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			// add modal css and js
			ShlHtmlBs_helper::addBootstrapCss(JFactory::getDocument());
			ShlHtmlBs_helper::addBootstrapJs(JFactory::getDocument());

			// add title
			JToolbarHelper::title('sh404SEF: ' . $title);

			// prepare layouts objects, to be used by sub-layouts
			$this->layoutRenderer = array();
			$this->layoutRenderer['custom'] = new ShlMvcLayout_File('com_sh404sef.form.fields.custom', sh404SEF_LAYOUTS);
			$this->layoutRenderer['shlegend'] = new ShlMvcLayout_File('com_sh404sef.configuration.fields.legend', sh404SEF_LAYOUTS);
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
			$this->toolbarTitle = Sh404sefHelperGeneral::makeToolbarTitle($title, $icon = 'sh404sef', $class = 'sh404sef-toolbar-title');

			// add tooltips
			JHTML::_('behavior.tooltip');

			// link to  custom javascript
			JHtml::script(Sh404sefHelperGeneral::getComponentUrl() . '/assets/js/' . $this->joomlaVersionPrefix . '_edit.js');
		}

		// add link to css
		JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/' . $this->joomlaVersionPrefix . '_editurl.css');

		// now display normally
		parent::display($this->joomlaVersionPrefix);
	}

	private function _pushDataSocial_seo()
	{
		// Open graph data params
		$ogData['og_enable'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList($this->meta->og_enable, 'og_enable');
		$ogData['og_type'] = Sh404sefHelperOgp::buildOpenGraphTypesList($this->meta->og_type, 'og_type', $autoSubmit = false,
			$addSelectDefault = true, $selectDefaultTitle = JText::_('JOPTION_USE_DEFAULT'), $customSubmit = '');
		$ogData['og_image'] = $this->meta->og_image;
		$ogData['og_enable_description'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList($this->meta->og_enable_description,
			'og_enable_description');
		$ogData['og_enable_site_name'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList($this->meta->og_enable_site_name, 'og_enable_site_name');
		$ogData['og_enable_fb_admin_ids'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList($this->meta->og_enable_fb_admin_ids,
			'og_enable_fb_admin_ids');
		$ogData['og_site_name'] = $this->meta->og_site_name;
		$ogData['fb_admin_ids'] = $this->meta->fb_admin_ids;

		$ogData['og_enable_location'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList($this->meta->og_enable_location, 'og_enable_location');
		$ogData['og_latitude'] = $this->meta->og_latitude;
		$ogData['og_longitude'] = $this->meta->og_longitude;
		$ogData['og_street_address'] = $this->meta->og_street_address;
		$ogData['og_locality'] = $this->meta->og_locality;
		$ogData['og_postal_code'] = $this->meta->og_postal_code;
		$ogData['og_region'] = $this->meta->og_region;
		$ogData['og_country_name'] = $this->meta->og_country_name;

		$ogData['og_enable_contact'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList($this->meta->og_enable_contact, 'og_enable_contact');
		$ogData['og_email'] = $this->meta->og_email;
		$ogData['og_phone_number'] = $this->meta->og_phone_number;
		$ogData['og_fax_number'] = $this->meta->og_fax_number;

		//push params in to view
		$this->ogData = $ogData;

		// twitter cards data
		$this->twCardsData = array();
		$enabled = isset($this->meta->twittercards_enable) ? $this->meta->twittercards_enable : SH404SEF_OPTION_VALUE_USE_DEFAULT;
		$this->twCardsData['twittercards_enable'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList($enabled, 'twittercards_enable');
		$this->twCardsData['twittercards_site_account'] = $this->meta->twittercards_site_account;
		$this->twCardsData['twittercards_creator_account'] = $this->meta->twittercards_creator_account;

		// google authorship
		$this->googleAuthorshipData = array();
		$enabled = isset($this->meta->google_authorship_enable) ? $this->meta->google_authorship_enable : SH404SEF_OPTION_VALUE_USE_DEFAULT;
		$this->googleAuthorshipData['google_authorship_enable'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList($enabled, 'google_authorship_enable');
		$this->googleAuthorshipData['google_authorship_author_profile'] = $this->meta->google_authorship_author_profile;
		$this->googleAuthorshipData['google_authorship_author_name'] = $this->meta->google_authorship_author_name;

		// google authorship
		$this->googlePublisherData = array();
		$enabled = isset($this->meta->google_publisher_enable) ? $this->meta->google_publisher_enable : SH404SEF_OPTION_VALUE_USE_DEFAULT;
		$this->googlePublisherData['google_publisher_enable'] = Sh404sefHelperHtml::buildBooleanAndDefaultSelectList($enabled, 'google_publisher_enable');
		$this->googlePublisherData['google_publisher_url'] = $this->meta->google_publisher_url;
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
		$bar->appendButton('Shajaxbutton', 'save', 'Save', "index.php?option=com_sh404sef&c=editurl&task=save&shajax=1&tmpl=component", $params);

		// add apply button as an ajax call
		$params['id'] = 'modalediturlapply';
		$params['closewindow'] = 0;
		$bar->appendButton('Shajaxbutton', 'apply', 'Apply', "index.php?option=com_sh404sef&c=editurl&task=apply&shajax=1&tmpl=component", $params);

		// other button are standards
		$bar->appendButton('Standard', 'back', 'Back', 'back', false, false);
		JToolBarHelper::cancel('cancel');

		return $bar;
	}

	private function _getActiveStartingPanel()
	{
		switch ($this->startOffset)
		{
			case 1:
				$active = 'seo';
				break;
			case 2:
				$active = 'aliases';
				break;
			case 3:
				$ative = 'social_seo';
				break;
			default:
				if ($this->home)
				{
					$active = 'seo';
				}
				else
				{
					$active = 'editurl';
				}
				break;
		}

		return $active;
	}
}
