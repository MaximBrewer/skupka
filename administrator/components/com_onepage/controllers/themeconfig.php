<?php
/**
 * 
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class JControllerThemeconfig extends JControllerBase
{
     function getViewName() 
	{ 
		return 'themeconfig';		
	} 

   function getModelName() 
	{		
		return 'themeconfig';
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
		$model	= $this->getModel('themeconfig');
		$msg = $model->store(); 
		if (empty($msg)) $msg = JText::_('COM_ONEPAGE_OK'); 

		$this->setRedirect(JRoute::_('index.php?option=com_onepage&view=themeconfig', false), $msg);
		return false;
	}
	
}
