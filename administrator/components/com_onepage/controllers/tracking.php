<?php
/**
 * @version		$Id: contact.php 21555 2011-06-17 14:39:03Z chdemko $
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class JControllerTracking extends JControllerBase
{
     function getViewName() 
	{ 
		return 'tracking';		
	} 

   function getModelName() 
	{		
		return 'tracking';
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
		$model	= $this->getModel('tracking');
		$msg = $model->store(); 
		if (empty($msg)) $msg = JText::_('COM_ONEPAGE_OK'); 

		$this->setRedirect(JRoute::_('index.php?option=com_onepage&view=tracking', false), $msg);
		return false;
	}

	
}
