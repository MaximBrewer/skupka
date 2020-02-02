<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

jimport('joomla.application.component.controller');

class vmeeProControllerRule extends JControllerLegacy
{
	/**
	 * @return vmeeProModelRule
	 */
	function getRuleModel() {
		return $this->getModel('rule');
	}
	
	
	function display($cachable = false, $urlparams = array()) {
		
		if ( ! JFactory::getApplication()->input->get( 'view' ,null,'RAW') ) {
			JRequest::setVar('view', 'rule' );
		}
		$document = JFactory::getDocument();
		$viewType	= $document->getType();
		$view = $this->getView( 'rule', $viewType, '', array( 'base_path'=>$this->basePath));
		// Get/Create the model
		if ($model = $this->getModel('templatelist')) {
			// Push the model into the view 
			$view->setModel($model, false);
		}
		parent::display();
    } 
    
    function test(){
    	if(emp_helper::isDemo()){
    		JError::raiseWarning(1, JText::_('This action is disabled in demo system'));
    		$this->display();
    		return;
    	}
    	$rule = $this->getRuleModel()->createRuleFromRequest();
    	$isSuccess = $this->getRuleModel()->apply($rule);
    	
    	if(!$isSuccess){
    		JError::raiseWarning('', JText::_('RULE_SAVE_FAILURE'));
    		JError::raiseWarning('', JText::_('Test was not performed'));
    	}else{
    		$mainframe = JFactory::getApplication();
    		$mainframe->enqueueMessage(JText::_('RULE_SAVE_SUCCESS'));
    		$isSuccess = $this->getRuleModel()->test($rule);
    		if(!$isSuccess){
    			JError::raiseWarning('', JText::_('Test failed'));
    		}else{
      			$mainframe->enqueueMessage(JText::_('Test ended successfully'));
    		}
    	}
    	
    	$this->display();
    }
    function cancel(){
    	$mainframe = JFactory::getApplication();
    	$mainframe->redirect('index.php?option=com_vmeeplus&controller=ruleList');
    }
    
    function apply(){
    	if(emp_helper::isDemo()){
			JError::raiseWarning(1, JText::_('Save is disabled in demo system'));
			$this->display();
			return;
		}
    	$rule = $this->getRuleModel()->createRuleFromRequest();
    	$isSuccess = $this->getRuleModel()->apply($rule);
    	
    	if(!$isSuccess){
			JError::raiseWarning('', JText::_('RULE_SAVE_FAILURE'));
    	}else{
    		$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage(JText::_('RULE_SAVE_SUCCESS'));
    	}
		$this->display();
    }
    
    function getConditionsStore(){
    	$ruleId = JFactory::getApplication()->input->get('rule_id', false,'RAW');
    	$conditions = $this->getRuleModel()->getConditions($ruleId);
    	$data = array(
			'identifier' => "conditionId",
			'items' => $conditions
		);
    	return $this->returnAsJson($data);
    }
    
    function save(){
    	if(emp_helper::isDemo()){
    		JError::raiseWarning(1, JText::_('Save is disabled in demo system'));
    		$this->display();
    		return;
    	}
    	$rule = $this->getRuleModel()->createRuleFromRequest();
    	$isSuccess = $this->getRuleModel()->apply($rule);
    	
    	if(!$isSuccess){
			JError::raiseWarning('', JText::_('RULE_SAVE_FAILURE'));
    	}else{
    		$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage(JText::_('RULE_SAVE_SUCCESS'));
    		$mainframe->redirect('index.php?option=com_vmeeplus&controller=ruleList');
    	}
		parent::display();
    }
    
	function saveCondition() {
		$result = array('error'=>'');
		$isAdded = $this->getRuleModel()->saveCondition();
		
    	$rule_id = JFactory::getApplication()->input->get('rule_id', false,'RAW');
    	$name = JFactory::getApplication()->input->get('name', false,'RAW');
    	$class_name = JFactory::getApplication()->input->get('class_name', false,'RAW');
    	$operator = JFactory::getApplication()->input->get('operator', false,'RAW');
    	$value = JFactory::getApplication()->input->get('value', false,'RAW');
    	
    	$condition = new vmeeCondition();
    	$condition->setRuleId($rule_id);
    	$condition->setName($name);
//    	$condition->setClassName($class_name);
    	$condition->setOperator($operator);
    	$condition->setValue($value);
    	$result = $condition->save();
    	
    	$this->returnAsJson($result);
    }
    
    function addNewCondition(){
    	$result = array('error'=>'');
    	$isAdded = $this->getRuleModel()->addNewCondition();
    	echo $isAdded;exit();
    	if(!$isAdded){
    		$result['error'] = JText::_('NEW_CONDITION_NOT_ADDED');
    	}
    	$this->returnAsJson($result);
    }
    
	function deleteCondition() {
    	$id = JFactory::getApplication()->input->get('conditionId', false,'RAW');
    	$result = array('error'=>'');
    	if (!$id) {
    		$result['error'] = JText::_('CONDITION_NOT_DELETED');
    	}else{
    		$isDeleted = $this->getRuleModel()->deleteCondition($id);
    		if(!$isDeleted)
    			$result['error'] = JText::_('CONDITION_NOT_DELETED');
    	}
    	$this->returnAsJson($result);
    }
    
    function getAllCondtionsConf(){
    	require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'conditionPluginManager.php';
		$conditionsArr = null;
		$conditionPluginManager = new emp_conditionPluginManager();
		$list = $conditionPluginManager->getConditionTypes();
		$rule = $this->getRuleModel()->createRuleFromRequest();
		$excludeConditionTypes = $rule->getExcludeConditionTypes();
		foreach ($list as $conditionPlugin) {
			if(in_array($conditionPlugin->getType(), $excludeConditionTypes)){
				continue;
			}
			$conditionsArr[] = array(
				'type' => $conditionPlugin->getType(),
				'displayName' => $conditionPlugin->getDisplayName(),
				'operators' => $conditionPlugin->getOperators(),
				'details' => $conditionPlugin->getDescription()
			);
		}
		
//		$list = $this->getConditionsTypes();
		
		/*foreach ($list as $row) {
			$conditionsArr[] = array(
					'id' => (string)$row['id'],
					'name' => (string)$row['name'],
					'displayName' => (string) JText::_($row['display_name']),
					'class' => (string)$row['class'],
					'field' => (string)$row['field'],
					'operators' => explode(',', (string)$row['operators']),
					'details' => (string)$row['details'] );
		}*/
    	
    	$this->returnAsJson($conditionsArr);
    }
    
    function getConditionValuesFormatted(){
    	$result = array('error'=>'');
    	
    	$condType = JFactory::getApplication()->input->get('conditionType', false,'RAW');
    	if(!$condType){
    		$result['error'] = JText::_('COULD_NOT_FIND_CONDITION_TYPE');
    	}
    	else{
    		$condPluginManager = new emp_conditionPluginManager();
    		$condition = $condPluginManager->getConditionPluginByType($condType);
    		$result = $condition->getPossibleValues();
    		if(!isset($result) || empty($result)){
    			$result['error'] = JText::_('ERROR_FORMATING_POSSIBLE_VALUES');
    		}
    	}
    	$this->returnAsJson($result);
    }
    
 	function add(){
    	JRequest::setVar('view', 'rule');
    	JRequest::setVar('layout', 'add');
    	
		$document = JFactory::getDocument();
		$viewType	= $document->getType();
		$view = $this->getView( 'rule', $viewType, '', array( 'base_path'=>$this->basePath));
		// Get/Create another model for templateList to use in this view
		if ($model = $this->getModel('templatelist')) {
			// Push the model into the view 
			$view->setModel($model, false);
		}
		
		parent::display();
    }
    
	function createNew(){
		if(emp_helper::isDemo()){
			JError::raiseWarning(1, JText::_('Save is disabled in demo system'));
			$this->cancel();
			return;
		}
    	JRequest::setVar('view', 'rule');
		$document = JFactory::getDocument();
		$viewType	= $document->getType();
		$view = $this->getView( 'rule', $viewType, '', array( 'base_path'=>$this->basePath));
		// Get/Create another model for templateList to use in this view
		if ($model = $this->getModel('templatelist')) {
			// Push the model into the view 
			$view->setModel($model, false);
		}
		
		$rule_id = $this->getRuleModel()->createNewRule();
		JRequest::setVar('rule_id', $rule_id);
		
		parent::display();
    }
    
    /**
     * 
     * Executes the rule logic via rule model. 
     * @param int Rule ID
     * @param Array $args
     */
    public function run($rule_id, $args = null){
    	if($rule_id){
    		$model = $this->getRuleModel();
    		$model->execute($rule_id, $args);
    	}
    }
    
	function returnAsJson($data) {
    	$document = JFactory::getDocument();
		$document->setMimeEncoding( 'application/json' );
		JResponse::setHeader( 'Content-Disposition', 'attachment; filename="result.json"' );
		// Output the JSON data.
		echo json_encode( $data );
		$mainframe = JFactory::getApplication();
		
		$mainframe->close();
    }
    
	function help(){
    	$redirect = "index.php?option=com_vmeeplus&controller=help";
    	$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect);
    }
    
}
?>