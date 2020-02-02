<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

 
class emp_templates_template {
	
	var $id = null;
	var $name = null;
	var $subject= null;
	var $body = null;
	
	/**
	 * @param int $id
	 */
	function emp_templates_template($id = null){
		if(!empty($id)){
			$this->id = $id;
			$this->init();
		}
	}
	
	private function init(){
		$row = $this->getData();
		
		$this->setId($row->id);
		$this->setName($row->name);
		$this->setSubject($row->subject);
		$this->setBody($row->body);
		
	}
	
	public function getData(){
		$row = JTable::getInstance('VmeePlusTemplates', 'Table');
		$row->load( $this->id );
		return $row;
	}
	
	public function save(){
		$row = JTable::getInstance('VmeePlusTemplates', 'Table');
		$data = get_object_vars($this);
		
		if (!$row->bind($data)) {
			JError::raiseWarning('', JText::_('RULE_NOT_SAVED'));
			return false;
		}
		if (!$row->store(true)) {
			JError::raiseWarning('', JText::_('RULE_NOT_SAVED'));
			return false;
		}
		return true;
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
	
	function setName($name){
		$this->name = $name;
	}
	
	function getName(){
		return $this->name;
	}
	
	function setSubject($subject){
		$this->subject = $subject;
	}
	
	function getSubject(){
		return $this->subject;
	}
	
	function setBody($body){
		$this->body = $body;
	}
	
	function getBody(){
		return $this->body;
	}
}