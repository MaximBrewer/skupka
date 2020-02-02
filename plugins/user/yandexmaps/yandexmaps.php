<?php
defined('JPATH_BASE') or die;
require_once JPATH_ROOT . "/plugins/user/profile/profile.php";

class PlgUserYandexMaps extends JPlugin {
	private $date = '';
	protected $autoloadLanguage = true;

	function __construct(&$subject, $config = array()) {
		parent::__construct($subject, $config);
		JFormHelper::addFieldPath(dirname(__FILE__) . '/forms/fields');

		$this->loadLanguage();
	}
	
	public function onContentPrepareData($context, $data) {
		if (isset($data->profile)) {
			$body = implode('', JResponse::getBody(true));
		}

		return true;
	}

	public function onContentPrepareForm($form, $data) {
	
		if (!($form instanceof JForm)) {
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}

		// Check we are manipulating a valid form.
		$name = $form->getName();
		if (!in_array($name, ['com_admin.profile', 'com_users.user', 'com_users.profile', 'com_users.registration'])) {
			return true;
		}

		JFactory::getDocument()->addStyleSheet(JURI::root().'/plugins/user/yandexmaps/assets/style.css');
		JForm::addFormPath(dirname(__FILE__) . '/forms');
		$form->loadFile('profile', true);
		
		return true;
	}
}
