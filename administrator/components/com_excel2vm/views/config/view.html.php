<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class Excel2vmViewConfig extends JViewLegacy {

	function display($tpl = null) {
		@$db = JFactory::getDBO();
		$option=JRequest::getVar('option', '', '', 'string');
		$view=JRequest::getVar('view', '', '', 'string');

		$title = $GLOBALS['component_name'].'. '.JText::_('CONFIGURATIONS');
		@$model =  $this->getModel();
		@$this->assign('active', $this->get('Active'));
		@$this->assign('inactive', $this->get('Inactive'));
		@$this->assign('config', $model->config);
		@$this->assign('currencies', $this->get('Currencies'));
		@$this->assign('languages', $this->get('Languages'));
	   //	@$this->assign('default_lang', $model->default_lang);
		@$this->assign('groups', $this->get('Groups'));
        @$this->assign('unpublish_categories', $model->getCategoryList(@$this->config->unpublish_categories?$this->config->unpublish_categories:0));
        @$this->assign('reset_categories', $model->getCategoryList(@$this->config->reset_categories?$this->config->reset_categories:0));
		@$this->assign('profiles', $model->profile_list(true));
		@$this->assign('is_cherry', $model->is_cherry);
        $this->assign('versions', $model->core->get_last_version()); 
		JToolBarHelper :: title($title, 'logo');



		parent :: display($tpl);
	}

}

?>