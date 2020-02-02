<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

jimport('joomla.application.component.controller');

class vmeeProControllerRuleList extends JControllerLegacy
{
	/**
	 * @return vmeeProModelRuleList
	 */
	function getRuleListModel() {
		return $this->getModel('ruleList');
	}
	
	
	function display($cachable = false, $urlparams = Array()) {
		if ( ! JFactory::getApplication()->input->get( 'view' ,null,'RAW') ) {
			JRequest::setVar('view', 'ruleList' );
		}
		parent::display();
    }
    
    function delete(){
    	
    }
    
    function enable(){
    	$view = JFactory::getApplication()->input->get( 'view' ,null,'RAW');
    	if ( ! $view ) {
    		JError::raiseWarning('', JText::_('NO_RULE_SELECTED'));
		}
		else if($view == 'ruleList'){
			$ids_arr = JFactory::getApplication()->input->get('rule_id',null,'RAW');
			$rule_id = $ids_arr[0];
			if(!empty($rule_id)){
				$model = $this->getRuleListModel();
				$model->setEnabled($rule_id, true);
			}
				
			
		}
		parent::display();
    }
    
    function disable(){
    	$view = JFactory::getApplication()->input->get( 'view' ,null,'RAW');
    	if ( ! $view ) {
    		JError::raiseWarning('', JText::_('NO_RULE_SELECTED'));
		}
		else if($view == 'ruleList'){
			$ids_arr = JFactory::getApplication()->input->get('rule_id',null,'RAW');
			$rule_id = $ids_arr[0];
			if(!empty($rule_id)){
				$model = $this->getRuleListModel();
				$model->setEnabled($rule_id, false);
			}
				
			
		}
		parent::display();
    }
    
    function edit(){
		if ( ! JFactory::getApplication()->input->get( 'view' ,null,'RAW') ) {
			JRequest::setVar('view', 'ruleList' );
		}
		else if(JFactory::getApplication()->input->get( 'view' ,null,'RAW') == 'ruleList'){
			
			$ids_arr = JFactory::getApplication()->input->get('rule_id',null,'RAW');
			$template_id = $ids_arr[0];
			if(!empty($template_id)){
				JRequest::setVar('view', 'rule' );
				$document = JFactory::getDocument();
				$viewType	= $document->getType();
				$view = $this->getView( 'rule', $viewType, '', array( 'base_path'=>$this->basePath));
				// Get/Create the model
				if ($model = $this->getModel('templatelist')) {
					// Push the model into the view 
					$view->setModel($model, false);
				}
			}
			else
				JRequest::setVar('view', 'ruleList' );
		}
		
		parent::display();
    }
    
    function add(){
    	$redirect = "index.php?option=com_vmeeplus&controller=rule&task=add";
    	$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect);
    }
    
	function remove(){
		$mainframe = JFactory::getApplication();
		if(emp_helper::isDemo()){
			JError::raiseWarning(1, JText::_('Delete is disabled in demo system'));
			$this->display();
			return;
		}
    	$id_from_request = JFactory::getApplication()->input->get('rule_id',null,'RAW');
    	$model = $this->getModel('rule');
    	
		if(is_array($id_from_request)){
			if(sizeof($id_from_request) > 0){
				foreach ($id_from_request as $rule_id) {
					if($rule_id >= 1 && $rule_id <= 5){
						JError::raiseWarning('', sprintf(JText::_("SERVICE RULE FRMTS CANNOT BE DELETED"),$model->getRuleName($rule_id)));
					}
					else{
						$result = $model->deleteRule($rule_id);
						if($result === true){
							$mainframe->enqueueMessage(sprintf(JText::_("RULE FRMTS DELETED SUCCESSFULLY"),$model->getRuleName($rule_id)));
						}
						else{
							JError::raiseWarning('', sprintf(JText::_("ERROR WHILE DELETING RULE FRMTS"),$model->getRuleName($rule_id)));
						}
					}
					
					
				}
			}
		}
		else{
			$result = $model->deleteRule($id_from_request);
		}
			
		parent::display();
    }
    
    function help(){
    	$redirect = "index.php?option=com_vmeeplus&controller=help";
    	$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect);
    }
	
}
?>