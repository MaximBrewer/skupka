<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class Excel2vmViewBackup extends JViewLegacy {

	function display($tpl = null) {
		$db = JFactory::getDBO();
		$option=JRequest::getVar('option', '', '', 'string');
		$view=JRequest::getVar('view', '', '', 'string');

		$title = $GLOBALS['component_name'].'. '.JText::_('RECOVER');

		$model =  $this->getModel();

			$this->assign('list', $this->get('Backups'));
			$this->assign('cat_list', $this->get('Categories'));

			JToolBarHelper :: title($title, 'logo');
			JToolBarHelper :: save('new_backup', JText::_('CREATE_A_BACKUP_COPY'));
			JToolBarHelper :: trash('clear', 'Очистка',false);
			

		parent :: display($tpl);
	}

}

?>