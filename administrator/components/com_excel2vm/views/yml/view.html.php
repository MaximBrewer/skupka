<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class Excel2vmViewYml extends JViewLegacy {

	function display($tpl = null) {
		$db = JFactory::getDBO();

		$title = $GLOBALS['component_name'].'. '.JText::_('IMPORT');
		$model =  $this->getModel();


		$this->assign('config', $model->config);
		//$this->assign('fields', $model->active);


		$this->assign('versions', $model->core->get_last_version());
		$this->assign('profiles', $model->profile_list());
		$this->assign('yml_config', $model->getYmlConfig());
		$this->assign('yml_export_config', $model->getYmlExportConfig());

        @$this->assign('currencies', $this->get('Currencies'));
        @$this->assign('groups', $this->get('Groups'));
        @$this->assign('manufacturers', $this->get('Manufacturers'));
        @$this->assign('export_categories', $model->getCategoryList(@$this->yml_export_config->export_categories?$this->yml_export_config->export_categories:0));
		JToolBarHelper :: title($title, 'logo');
		JToolBarHelper :: preferences('com_excel2vm',500);


		parent :: display($tpl);
	}

}

?>