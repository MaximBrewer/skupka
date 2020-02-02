<?php
/**
 * @package		RuposTel.com
 * @copyright	Copyright (C) 2005 - 2011 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class JControllerFilters extends JControllerBase
{
     function getViewName() 
	{ 
		return 'filters';		
	} 

   function getModelName() 
	{		
		return 'filters';
	}

    public function save()
	{
	  return $this->apply(); 
	}
	public function apply()
	{

		// Check for request forgeries.
		

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('filters');
		$msg = $model->store(); 


		$this->setRedirect(JRoute::_('index.php?option=com_onepage&view=filters', false), $msg);
		return false;
	}

	
}
