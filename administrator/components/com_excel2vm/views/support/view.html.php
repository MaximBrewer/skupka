<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class Excel2vmViewSupport extends JViewLegacy {

	function display($tpl = null) {
		$db = JFactory::getDBO();
		$option=JRequest::getVar('option', '', '', 'string');
		$view=JRequest::getVar('view', '', '', 'string');

		$title = $GLOBALS['component_name'].'. '.JText::_('SUPPORT');

		$model =  $this->getModel();
        $this->assign('changelist', $model->getChangeList());
        $this->assign('data', $model->getData());
        $this->assign('my_version', $model->getMyVersion());
        $this->assign('order_id', $model->order_id);
		JToolBarHelper :: title($title, 'logo');
		parent :: display($tpl);
	}

}

?>