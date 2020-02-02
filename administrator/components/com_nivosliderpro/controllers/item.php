<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class NivoSliderProControllerItem extends JControllerForm {
	
	/**
	 * 
	 * get slider id
	 */
	private function setRedirectToSlides(){
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$sliderID = $data["sliderid"];
		$redirectUrl = HelperNivoPro::getViewUrl_Items($sliderID);
		$this->setRedirect($redirectUrl);
	}
	
	
	/**
	 * 
	 * cancel the slide save
	 */
	public function cancel($key=null){
		
		//bypass direct saving restrictions
		$context = "$this->option.edit.$this->context";
		$recordId = JRequest::getInt("id");
		$this->holdEditId($context, $recordId);
		
		parent::cancel($key);
		$this->setRedirectToSlides();
	}
	
	
	/**
	 * 
	 * save and rediret to the url
	 */
	public function save($key = null, $urlVar = null){
		
		parent::save($key,$urlVar);
		
		
		$task = $this->getTask();
		switch($task){
			case "save":
				$this->setRedirectToSlides();				
			break;
		}
		
		return(false);
	}
	
	
}

?>