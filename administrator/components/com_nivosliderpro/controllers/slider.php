<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class NivosliderProControllerSlider extends JControllerForm {
	
	/**
	 * 
	 * bypass direct saving restrictions
	 */
	private function holdID(){		
		$context = "$this->option.edit.$this->context";
		$recordId = JRequest::getInt("id");
		$this->holdEditId($context, $recordId);
		
	}
	
	public function save($key=null, $urlVar=null){
		$this->holdID();
		return parent::save($key,$urlVar);
	}
	
	public function cancel($key=null){
		$this->holdID();
		return parent::cancel($key);
	}
	
	
}

?>