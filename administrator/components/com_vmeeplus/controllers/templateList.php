<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

jimport('joomla.application.component.controller');

class vmeeProControllerTemplateList extends JControllerLegacy
{
	
	function getTemplateListModel() {
		return $this->getModel('templateList');
	}
	
	function display($cachable = false, $urlparams = array()) {
		
//		if ( ! JFactory::getApplication()->input->get( 'view' ,null,'RAW') ) {
			JRequest::setVar('view', 'templateList' );
//		}

		$document = JFactory::getDocument();
		$viewType	= $document->getType();
		$viewobj = $this->getView( 'templateList', $viewType, '', array( 'base_path'=>$this->basePath));
		if ($model = $this->getModel('rule')) {
			// Push the model into the view
			$viewobj->setModel($model, false);
		}
		parent::display();
    }
    
    function edit(){
    	$view = JFactory::getApplication()->input->get( 'view' ,null,'RAW');
    	if ( ! $view ) {
    		JError::raiseWarning('', JText::_('NO_TEMPLATE_SELECTED'));
		}
		else if($view == 'templateList'){
			$document = JFactory::getDocument();
			$viewType	= $document->getType();
			$viewobj = $this->getView( 'template', $viewType, '', array( 'base_path'=>$this->basePath));
			if ($model = $this->getModel('rule')) {
				// Push the model into the view
				$viewobj->setModel($model, false);
			}
			$ids_arr = JFactory::getApplication()->input->get('template_id',null,'RAW');
			$template_id = $ids_arr[0];
			if(!empty($template_id))
				JRequest::setVar('view', 'template' );
			
		}
		parent::display();
    }
    
    function add(){
    	$redirect = "index.php?option=com_vmeeplus";
    	
    	$task = JFactory::getApplication()->input->get('task',null,'RAW');
    	$redirect .= '&controller=template&task=add';
    	
    	$redirect = JRoute::_( $redirect, false );
    	$this->setRedirect( $redirect);
    	/* $view = JFactory::getApplication()->input->get( 'view' ,null,'RAW');
    	if ( ! $view ) {
    		JError::raiseWarning('', JText::_('NO_TEMPLATE_SELECTED'));
		}
		else if($view == 'templateList'){
			$ids_arr = JFactory::getApplication()->input->get('template_id',null,'RAW');
			$template_id = $ids_arr[0];
			if(empty($template_id)){
				JRequest::setVar('view', 'template' );
				JRequest::setVar('isNew', true );
			}
			
		}
		parent::display(); */
    }
    
	function remove(){
		if(emp_helper::isDemo()){
			JError::raiseWarning(1, JText::_('Delete is disabled in demo system'));
			$this->display();
			return;
		}
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );
		$id_from_request	= JFactory::getApplication()->input->get( 'template_id', array(), 'RAW' );
    	$model = $this->getModel('template');
    	$model->delete($id_from_request);
			
		$this->display();
    }
    
	function help(){
    	$redirect = "index.php?option=com_vmeeplus&controller=help";
    	$redirect = JRoute::_( $redirect, false );
		$this->setRedirect( $redirect);
    }
}
?>