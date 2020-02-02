<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/
jimport( 'joomla.application.component.view' );

class vmeeProViewruleList extends JViewLegacy {

	/**
	 * @return vmeeProModelRuleList
	 */
	private function getRuleListModel() {
		$model = $this->getModel();
		return $model;
	}
	
	function display($tpl = null)   {
		JToolBarHelper::title( VMEE_PRO_TITLE, 'interamind_logo' );
		JToolBarHelper::publishList('enable', 'Enable');
		JToolBarHelper::unpublishList('disable', 'Disable');
		JToolBarHelper::addNew('add', 'New');
		JToolBarHelper::editList('edit', 'Edit');
		JToolBarHelper::deleteList($msg = '', $task = 'remove', $alt = 'Delete');
		JToolBarHelper::preferences( 'com_vmeeplus',650 );
		JToolBarHelper::custom("help", "help", "help", "Help", false);
			
		$model = $this->getRuleListModel();
		$ruleList = $model->getRuleList();
		$state = $this->get('State');
		
		$this->sortDirection = $state->get('filter_order_Dir');
		$this->sortColumn = $state->get('filter_order');
		
		$this->assignRef('ruleList', $ruleList);
		
		$error = "";
		$isCodeInstalled = emp_helper::checkIfInstalled($error);
		
		if(!$isCodeInstalled){
			JError::raiseWarning( 1, $error);
		}
		
		if (emp_helper::allEmailsDisabled()) {
			JError::raise(E_WARNING, 0, "Note : the 'Disable all emails' setting is ON. no emails will be sent. use the options icon to enable email sending");
		}
		
        parent::display($tpl);
        vmeePlusHelper::addSubmenu('ruleList');
    }
}
