<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/
jimport( 'joomla.application.component.view' );

class vmeeProViewrule extends JViewLegacy {

	function display($tpl = null)   {
        $model = $this->getRuleModel();
        $request = JRequest::get();
        $layout = isset($request['layout']) ? $request['layout'] : 'default';
        $this->setToolbar($layout);
        $this->setLayout($layout);
		$templateListModel = $this->getModel('templatelist');
			
		if($layout == null || $layout == 'default'){
			$id_from_request = JFactory::getApplication()->input->get('rule_id',null,'RAW');
			if(is_array($id_from_request))
				$id = $id_from_request[0];
			else
				$id = $id_from_request;
				
			$rule = $model->getRule($id);
			//$templateList = $templateListModel->getTemplateList($rule->getTrigger());
			$templateList = $templateListModel->getTemplateList();
			$this->assignRef('rule', $rule);
		}
		else if($layout == 'add'){
			//get triger list 
			$triggerList = $model->getTriggerList();
			$templateList = $templateListModel->getTemplateList();
			$this->assignRef('triggerList', $triggerList);
		}
		
		$this->assignRef('templateList', $templateList);
        parent::display();
    }
    
	private function setToolbar($tpl){
		JRequest::setVar( 'hidemainmenu', 1 );
    	JToolBarHelper::title( VMEE_PRO_TITLE.' - <span style="color:marron;font-weight: normal;"><small> [edit mode] </small></span>', 'interamind_logo' );
    	
    	if($tpl == null || $tpl == 'default'){
    		JToolBarHelper::apply('apply', 'Apply');
			JToolBarHelper::save('save', 'Save');
			JToolBarHelper::cancel('cancel', 'Cancel');
			JToolBarHelper::custom( 'test', 'mail_next.png', 'mail_next.png', 'Send test email', false, false );
			$doc = JFactory::getDocument();
			$root = JUri::root().'/administrator/components/com_vmeeplus';
			$style = '.icon-32-mail_next 	{ background-image: url(' . $root . '/images/32x32/mail_next.png); }';
			$doc->addStyleDeclaration( $style );
			JToolBarHelper::preferences( 'com_vmeeplus', 650 );
			JToolBarHelper::custom("help", "help", "help", "Help", false);
    	}
		else if($tpl == 'add'){
    		JToolBarHelper::apply('createNew', 'Apply');
			JToolBarHelper::cancel('cancel', 'Cancel');
    	}
    }
    
 	/**
     * @return vmeeProModelRule
     */
	function getRuleModel() {
		$model = $this->getModel();
		return $model;
	}
}
