<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.7.3.3292
 * @date  2016-03-15
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('joomla.application.component.view');

class Sh404sefViewConfiguration extends ShlMvcView_Base
{

	public function display($tpl = null)
	{
		// version prefix
		$this->joomlaVersionPrefix = Sh404sefHelperGeneral::getJoomlaVersionPrefix();
		$this->refreshAfter = JFactory::getApplication()->input->getCmd('refreshafter');

		if ($this->getLayout() != 'close')
		{
			switch (Sh404sefConfigurationEdition::$id)
			{
				case 'community':
				case 'lite':
					$this->byComponentItemsCount = 4;
					JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/configuration.community.css');
					break;
				default:
					$this->byComponentItemsCount = 5;

			}

			// get model
			$model = $this->getModel();
			// ask for the form
			$this->form = $model->getForm();

			// prepare layouts objects, to be used by sub-layouts
			$this->layoutRenderer = array();
			$this->layoutRenderer['default'] = new ShlMvcLayout_File('com_sh404sef.configuration.fields.default', sh404SEF_LAYOUTS);
			$this->layoutRenderer['shlegend'] = new ShlMvcLayout_File('com_sh404sef.configuration.fields.legend', sh404SEF_LAYOUTS);
			$this->layoutRenderer['Rules'] = new ShlMvcLayout_File('com_sh404sef.configuration.fields.rules', sh404SEF_LAYOUTS);


			$document = JFactory::getDocument();

			// insert custom stylesheet
			JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/configuration.css');

			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				ShlHtmlBs_helper::addBootstrapCss($document);
				ShlHtmlBs_helper::addBootstrapJs($document);

				JHtml::styleSheet(Sh404sefHelperGeneral::getComponentUrl() . '/assets/css/j3_list.css');

				// insert bootstrap theme
				ShlHtml_Manager::getInstance()->addAssets(JFactory::getDocument());
			}

			// add ga_auth js and css, in case we open configuration
			$document->addStyleSheet(JUri::root(true) . '/media/com_sh404sef/assets/css/wb_gaauth.css');
			$document->addScript(JUri::root(true) . '/media/com_sh404sef/assets/js/wb_gaauth_' . $this->joomlaVersionPrefix . '.js');
		}

		parent::display($this->joomlaVersionPrefix);
	}

}
