<?php

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class NivosliderproControllerSliders extends JControllerAdmin
{
	public function getModel($name = 'Slider', $prefix = 'NivoSliderProModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		
		return $model;
	}
	
	
}