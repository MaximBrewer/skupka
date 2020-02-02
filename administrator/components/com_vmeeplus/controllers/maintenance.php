<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

jimport('joomla.application.component.controller');

class vmeeProControllerMaintenance extends JControllerLegacy
{
	/**
	* @return vmeeProModelMaintenance
	*/
	function getMaintenanceModel() {
		return $this->getModel('maintenance');
	}
	
	function display($cachable = false, $urlparams = array()) {
		if ( ! JFactory::getApplication()->input->get( 'view' ,null,'RAW') ) {
			JRequest::setVar('view', 'maintenance' );
		}
		parent::display();
    }
    
    function import(){
    	$mainframe = JFactory::getApplication();
    	if(emp_helper::isDemo()){
    		JError::raiseWarning(1, JText::_('This action is disabled in demo system'));
    		$this->display();
    		return;
    	}
    	
    	//import selected templates
    	$templates = JFactory::getApplication()->input->get('template_id', false,'RAW');
    	$msg = "There were no templates to import";
    	$msgType = 'notice';
    	if(!empty($templates)){
    		$model = $this->getMaintenanceModel();
    		$bRes = $model->importVMEmailsTemplates($templates);
    		if($bRes){
    			$msg = "Templates import ended successfully";
    			$msgType = 'message';
    		}
    	}
    	//go to templates list
    	$mainframe->redirect('index.php?option=com_vmeeplus&controller=templateList',$msg,$msgType);
    }
    

	function help(){
    	$redirect = "index.php?option=com_vmeeplus&controller=help";
    	$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect);
    }
    
}
?>