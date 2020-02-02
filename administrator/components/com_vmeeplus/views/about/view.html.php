<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/
jimport( 'joomla.application.component.view' );

class vmeeProViewtemplate extends JViewLegacy {

	private function getTemplateModel() {
		$model = $this->getModel();
		return $model;
	}
	
	function display($tpl = null)   {
		JToolBarHelper::title( IM_CART_TITLE, 'interamind_logo' );
		JToolBarHelper::apply('apply', 'Apply');
		JToolBarHelper::save('save', 'Save');
		JToolBarHelper::cancel('cancel', 'Cancel');
		JToolBarHelper::deleteList($msg = '', $task = 'remove', $alt = 'Delete');
         
        $ids_arr = JFactory::getApplication()->input->get('template_id',null,'RAW');
		$template_id = $ids_arr[0];
			
		$model = $this->getTemplateModel();
		$templateData = $model->getTemplateData($template_id);
		
		$this->assignRef('id',$templateData['id']);
		$this->assignRef('name',$templateData['name']);
		$this->assignRef('subject',$templateData['subject']);
		$this->assignRef('body',$templateData['body']);
		$this->assignRef('type',$templateData['type']);
		$this->assignRef('CC',$templateData['CC']);
		$this->assignRef('BCC',$templateData['BCC']);
		$this->assignRef('default',$templateData['default']);
		$this->assignRef('enabled',$templateData['enabled']);
		
        parent::display($tpl);
    }
}
