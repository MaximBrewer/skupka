<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

jimport('joomla.application.component.controller');

class vmeeProControllerHelp extends JControllerLegacy
{
	
	function display($cachable = false, $urlparams = array()) {
		if ( ! JFactory::getApplication()->input->get( 'view' ,null,'RAW') ) {
			JRequest::setVar('view', 'help' );
		}
		parent::display();
    }
    
	function sendDebugFiles(){
		$mainframe = JFactory::getApplication();
		if(emp_helper::isDemo()){
			JError::raiseWarning(1, JText::_('This action is disabled in demo system'));
			$this->display();
			return;
		}
		$resposnse = $this->getModel('help')->sendDebugFiles();
		if($resposnse){
			$mainframe->enqueueMessage("Request support email sent to InteraMind support with your system info and debug files.");	
		}else{
			$mainframe->enqueueMessage("Error occured while try to send support email.");
		}
		
		$this->display();
		
	}
    
}