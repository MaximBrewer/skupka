<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

jimport('joomla.application.component.controller');

class vmeeProControllerTemplate extends JControllerLegacy
{
	
	function getTemplateModel() {
		return $this->getModel('template');
	}
	
	function display($cachable = false, $urlparams = array()) {
		if ( ! JFactory::getApplication()->input->get( 'view' ,null,'RAW') ) {
			JRequest::setVar('view', 'template' );
		}
		$document = JFactory::getDocument();
		$viewType	= $document->getType();
		$view = $this->getView( 'template', $viewType, '', array( 'base_path'=>$this->basePath));
		if ($model = $this->getModel('rule')) {
			// Push the model into the view
			$view->setModel($model, false);
		}
		parent::display();
    }
    
    function cancel(){
    	/* $document = JFactory::getDocument();
    	$viewType	= $document->getType();
    	$view = $this->getView( 'templateList', $viewType, '', array( 'base_path'=>$this->basePath));
    	if ($model = $this->getModel('rule')) {
    		// Push the model into the view
    		$view->setModel($model, false);
    	}
    	JRequest::setVar('view', 'templateList' );
    	parent::display(); */
    	$mainframe = JFactory::getApplication();
    	$mainframe->redirect('index.php?option=com_vmeeplus&controller=templateList');
    }
    
    function apply(){
    	if(emp_helper::isDemo()){
    		JError::raiseWarning(1, JText::_('Save is disabled in demo system'));
    		$this->cancel();
    		return;
    	}
    	$isSuccess = $this->getTemplateModel()->applyTemplateDetails();
    	if(!$isSuccess){
			JError::raiseWarning('', JText::_('TEMPLATE_SAVE_FAILURE'));
			$task = JFactory::getApplication()->input->get('task',null,'RAW');
			if($task == 'add'){
				$redirect = "index.php?option=com_vmeeplus";
				$redirect .= '&controller=templateList';
				$redirect = JRoute::_( $redirect, false );
				$this->setRedirect( $redirect);
			}
    	}else{
    		$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage(JText::_('TEMPLATE_SAVE_SUCCESS'));
    	}
    	$document = JFactory::getDocument();
    	$viewType	= $document->getType();
    	$view = $this->getView( 'template', $viewType, '', array( 'base_path'=>$this->basePath));
    	if ($model = $this->getModel('rule')) {
    		// Push the model into the view
    		$view->setModel($model, false);
    	}
//    	JRequest::setVar('view', 'template' );
		parent::display();
    }
    
    function save(){
    	if(emp_helper::isDemo()){
    		JError::raiseWarning(1, JText::_('Save is disabled in demo system'));
    		$this->cancel();
    		return;
    	}
    	$isSuccess = $this->getTemplateModel()->saveTemplateDetails();
    	if(!$isSuccess){
    		JError::raiseWarning('', JText::_('TEMPLATE_SAVE_FAILURE'));
    		$task = JFactory::getApplication()->input->get('task',null,'RAW');
    		if($task == 'add'){
    			$redirect = "index.php?option=com_vmeeplus";
    			$redirect .= '&controller=templateList';
    			$redirect = JRoute::_( $redirect, false );
    			$this->setRedirect( $redirect);
    		}
    	}else{
    		$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage(JText::_('TEMPLATE_SAVE_SUCCESS'));
    		JRequest::setVar('view', 'templateList' );
    		$document = JFactory::getDocument();
    		$viewType	= $document->getType();
    		$view = $this->getView( 'templateList', $viewType, '', array( 'base_path'=>$this->basePath));
    		if ($model = $this->getModel('rule')) {
    			// Push the model into the view
    			$view->setModel($model, false);
    		}
    	}
		
		parent::display();
    }
    
    function add(){
    	JRequest::setVar('view', 'template');
    	JRequest::setVar('layout', 'add');
    
    	$document = JFactory::getDocument();
    	$viewType	= $document->getType();
    	$view = $this->getView( 'template', $viewType, '', array( 'base_path'=>$this->basePath));
    	if ($model = $this->getModel('rule')) {
    		// Push the model into the view
    		$view->setModel($model, false);
    	}
    	parent::display();
    }
    
    function createNew(){
    	$this->apply();
    }

	function help(){
    	$redirect = "index.php?option=com_vmeeplus&controller=help";
    	$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect);
    }
}
?>