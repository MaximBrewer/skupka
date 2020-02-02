<?php
/**
 * @copyright	Copyright (c) 2017 content. All rights reserved.
 * @license		GNU General Public License version 2 or later;
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * content - YandexMapsContentId Plugin
 *
 * @package		Joomla.Plugin
 * @subpakage	content.YandexMapsContentId
 */
class plgcontentYandexMapsContentId extends JPlugin {

	/**
	 * Constructor.
	 *
	 * @param 	$subject
	 * @param	array $config
	 */
	function __construct(&$subject, $config = array()) {
		// call parent constructor
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	public function onContentBeforeSave($context, $article, $isNew) {}
	public function onContentPrepareForm($form, $data) {
		if (!($form instanceof JForm)) {
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}
		// Check we are manipulating a valid form.
		$name = $form->getName();
		if (!in_array($name, array('com_content.article'))) {
			return true;
		}

		JForm::addFormPath(JPATH_ROOT . '/plugins/content/yandexmapscontentid/forms');
		$form->loadFile('address', true);
		if (!$this->params->get('ask_yandex_maps_object_id', 0)) {
			$form->removeField('yandex_maps_object', 'metadata');
		}
		if (!$this->params->get('ask_minimap', 1) and !(!$this->params->get('ask_yandex_maps_object_id', 0) and !$this->params->get('ask_icon', 0))) {
			$form->removeField('address', 'metadata');
		}
		if (!$this->params->get('ask_icon', 0)) {
			$form->removeField('ask_icon', 'metadata');
		}
		return true;
	}
}