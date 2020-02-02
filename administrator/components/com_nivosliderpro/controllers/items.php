<?php

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class NivoSliderProControllerItems extends JControllerAdmin
{
	public function getModel($name = 'Item', $prefix = 'NivoSliderProModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	
	public function add(){
		$sliderID = JRequest::getInt("sliderid");
		
		$view = "item";
		$layout = "edit";
		$option = JRequest::getCmd('option');
		
		$redirectUrl = JRoute::_("index.php?option=$option&view=$view&layout=$layout&sliderid=$sliderID",false);
		
		$this->setRedirect($redirectUrl);
	}
	
	/**
	 * 
	 * set redirect url
	 */
	private function setRedirectToSlides(){
		$sliderID = JRequest::getCmd("sliderid");
		$redirectUrl = HelperNivoPro::getViewUrl_Items($sliderID);
		$this->setRedirect($redirectUrl);
	}
	
	/**
	* set redirect after all functions.
	* 
	*/
	public function publish(){		
		parent::publish();
		$this->setRedirectToSlides();
	}
	
	public function reorder(){
		parent::reorder();
		$this->setRedirectToSlides();		
	}
	
	public function delete(){
		parent::delete();
		$this->setRedirectToSlides();		
	}
	
	public function saveorder(){
		parent::saveorder();
		$this->setRedirectToSlides();
	}
	
	public function checkin(){
		parent::checkin();
	}
	
}