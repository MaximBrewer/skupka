<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class Excel2vmViewExcel2vm extends JViewLegacy {

	function display($tpl = null) {
		$db = JFactory::getDBO();

		$title = $GLOBALS['component_name'].'. '.JText::_('IMPORT');
		$model =  $this->getModel();


		$this->assign('config', $model->config);
		$this->assign('fields', $model->active);


		$this->assign('versions', $model->core->get_last_version());
		$this->assign('profiles', $model->profile_list());
		$this->assign('uploaded_files', $model->get_files());

		JToolBarHelper :: title($title, 'logo');
		JToolBarHelper :: preferences('com_excel2vm',500);


		parent :: display($tpl);
	}

}

?>