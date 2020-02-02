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

class JControllerEdit extends JControllerBase
{
     function getViewName() 
	{ 
		return 'edit';		
	} 

   function getModelName() 
	{		
		return 'edit';
	}
	/*
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}
*/
	public function proceed()
	{

		// Check for request forgeries.
		return;

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('default');
		$msg = $model->proceed(); 
		// Redirect back to the contact form.
		$lang_name = JRequest::getVar('selected_lang', ''); 
		$lang_code = JRequest::getVar('selected_code', ''); 
		if (empty($lang_name) || (empty($lang_code)))
		$this->setRedirect(JRoute::_('index.php?option=com_vmtranslator&view=default&', false, 'Error'));
		else
		$this->setRedirect(JRoute::_('index.php?option=com_vmtranslator&view=edit&', false));
		return false;
	}

	
}
