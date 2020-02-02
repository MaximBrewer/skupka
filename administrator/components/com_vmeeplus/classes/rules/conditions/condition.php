<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/
 
class emp_rules_conditions_condition{
	
	var $id = null;
	var $rule_id = null;
	var $cond_type = null;
	var $operator = null;
	var $value = null;
	var $text_value = null;
	var $display_name = null;
	var $conditionPlugin = null;
	
	/**
	 * @param int $id
	 */
	function emp_rules_conditions_condition($id = null){
		if(!empty($id)){
			$this->id = $id;
			$this->init();
		}
	}
	
	private function init(){
		$row = $this->getData();
		
		$this->id = $row->id;
		$this->rule_id = $row->rule_id;
		$this->cond_type = $row->cond_type;
		$this->operator = $row->operator;
		$this->value = $row->value;
		$this->text_value = $row->text_value;
		
		/*$q = "SELECT field, class, display_name FROM #__vmee_plus_cond_types WHERE id=".$this->cond_type;
		$db =JFactory::getDBO();
		$db->setQuery($q);
		$result = $db->loadAssoc();*/
		
		$condPluginManager = new emp_conditionPluginManager();
		$this->conditionPlugin = $condPluginManager->getConditionPluginByType($this->cond_type);
	}
	
	public function getData(){
		$row = JTable::getInstance('VmeePlusConditions', 'Table');
		$row->load( $this->id );
		return $row;
	}
	
	public function save(){
		$row = JTable::getInstance('VmeePlusConditions', 'Table');
		$data = get_object_vars($this);
		
		if (!$row->bind($data)) {
			JError::raiseWarning('', JText::_('CONDITION_NOT_SAVED'));
			return false;
		}
		if (!$row->store(true)) {
			JError::raiseWarning('', JText::_('CONDITION_NOT_SAVED'));
			return false;
		}
		return true;
	}
	
	public function delete(){
		$row = JTable::getInstance('VmeePlusConditions', 'Table');
		return $row->delete( $this->id );
	}
	
	/**
	 * 
	 * Evaluate the condition
	 * 
	 * @param string $operator
	 * @param object $value
	 * @param array	$args	Arguments needed for this condition, like order ID, product ID, etc.
	 * @param array	$errors	List of errors to return by this function
	 * @return boolean
	 */
	public function evaluateCondition($args, & $errors){
		return $this->conditionPlugin->evaluateCondition($this->getOperator(), $this->getValue(), $args, $errors);
	}
	
	/**
	 * @param int $id
	 */
	function setId($id){
		$this->id = $id;
	}
	
	function getId(){
		return $this->id;
	}
	
	/**
	 * @param int $id
	 */
	function setRuleId($id){
		$this->rule_id = $id;
	}
	
	function getRuleId(){
		return $this->rule_id;
	}
	
	/**
	 * @param int $id
	 */
	function setCondType($cond_type){
		$this->cond_type = $cond_type;
	}
	
	function getCondType(){
		return $this->cond_type;
	}
	
	function getName(){
		return $this->conditionPlugin->getDisplayName();
	}
	
	/**
	 * @param string $operator
	 */
	function setOperator($operator){
		$this->operator = $operator;
	}
	
	function getOperator(){
		return $this->operator;
	}
	
	/**
	 * @param string $value
	 */
	function setValue($value){
		$this->value = $value;
	}
	
	function getValue(){
		return $this->value;
	}
	
	function setTextValue($textValue){
		$this->text_value = $textValue;
	}
	
	function getTextValue(){
		return $this->text_value;
	}
	
	function __toString(){
		return 'id='.$this->getId().' name='.$this->getCondTypeId().' class='.$this->getClassName().' field='.$this->getField().' value='.$this->getValue().' rule_id='.$this->getRuleId();
	}
}