<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class configControllerConfig extends JControllerBase
{	
   function getViewName() 
	{ 
		return 'config';		
	} 

   function getModelName() 
	{		
		return 'config';
	}
	function getName()
	{
	  return 'config'; 
	}
	function __construct($config = array())
	{
		

		parent::__construct($config);
	}
	function apply()
	{
	  return $this->save(); 
	}
   function save()  // <-- edit, add, delete 
  {
       require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'language.php'); 
	    // load basic stuff:
	    OPCLang::loadLang(); 
    JRequest::setVar( 'view', '[ config ]' );
    JRequest::setVar( 'layout', 'default'  );  
    
    $model = $this->getModel('config');
    $reply = $model->store();
    if ($reply===true) {
    $msg = JText::_('COM_ONEPAGE_CONFIGURATION_SAVED');
    } else { $msg = $reply; 
    }
    $link = 'index.php?option=com_delivery';
	
	  $x = JFactory::getApplication()->set('messageQueue', array()); 
			$x = JFactory::getApplication()->set('_messageQueue', array()); 
			$session = JFactory::getSession();
            $sessionQueue = $session->set('application.queue', array());
	
    $this->setRedirect($link, $msg); 
  }

}
