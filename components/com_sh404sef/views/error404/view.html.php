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
 *
 */

defined('_JEXEC') or die;

/**
 * sh404SEF 404 error page view
 *
 */
class Sh404sefViewError404 extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$languageTag = JFactory::getLanguage()->getTag();

		// fetch page from keystore
		$content = new stdClass();
		$content->text = ShlDb_Keystore::getInstance(Sh404sefClassConfig::COM_SH404SEF_KEYSTORE_TABLE_NAME)
			->get(Sh404sefClassConfig::COM_SH404SEF_KEYSTORE_KEY_404_ERROR_PAGE . '.' . $languageTag);
		$content->params = new JRegistry;
		if (empty($content->text))
		{
			// try again with default language
			$languageTag = Sh404sefHelperLanguage::getDefaultLanguageTag();
			$content->text = ShlDb_Keystore::getInstance(Sh404sefClassConfig::COM_SH404SEF_KEYSTORE_TABLE_NAME)
				->get(Sh404sefClassConfig::COM_SH404SEF_KEYSTORE_KEY_404_ERROR_PAGE . '.' . $languageTag);
			if (empty($content->text))
			{
				// resort to default text
				$content->text = JText::_('COM_SH404SEF_DEF_404_MSG');
			}
		}

		// replace similar URLs tags, by triggering the onPrepareContent event
		$dispatcher = ShlSystem_factory::dispatcher();
		JPluginHelper::importPlugin('content');
		$dispatcher->trigger('onContentPrepare', array('com_content.archive', &$content, &$content->params, 0));

		// output the result
		echo ShlMvcLayout_Helper::render('com_sh404sef.general.error_404_main', array('text' => $content->text, 'language_tag' => $languageTag));
	}
}
