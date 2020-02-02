<?php 
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 wwww.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech..com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class  VdataControllerLogs extends VdataController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
		JFactory::getApplication()->input->set( 'view', 'logs' );
		
		$this->model = $this->getModel('logs');
		$this->registerTask( 'add'  , 	'edit' );
	}
	
	function display($cachable = false, $urlparams = false){
		
		$user = JFactory::getUser();
		$canViewLog = $user->authorise('core.access.log', 'com_vdata');
		
		if(!$canViewLog){
			$msg = JText::_( 'ALERT_AUTHORIZATION_ERROR' );
			$this->setRedirect( 'index.php?option=com_vdata', $msg , 'error');
		}
			
		parent::display();
		
	}
	
	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JFactory::getApplication()->input->set( 'view', 'logs' );
		JFactory::getApplication()->input->set( 'layout', 'form' );
		JFactory::getApplication()->input->set('hidemainmenu', 1);

		parent::display();
	}
	

	//to publish/unpublish the items
	
	function truncate()
	{
		$model = $this->getModel('logs');
		if(!$model->truncate()) {
			JFactory::getApplication()->enqueueMessage($model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=logs');
		} else {
			$msg = JText::_( 'Clear log successful' );
			$this->setRedirect( 'index.php?option=com_vdata&view=logs', $msg );
		}
		
	}
	
	function remove()
	{
		$model = $this->getModel('logs');
		
		if(!$model->delete()) {
			JFactory::getApplication()->enqueueMessage($model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=logs');
		} else {
			$msg = JText::_( 'Record(s) Deleted' );
			$this->setRedirect( 'index.php?option=com_vdata&view=logs', $msg );
		}
		
	}
	
	/**
	 * cancel editing a record
	 * @return void
	 */
	
	function cancel()
	{
		$msg = JText::_( 'Operation Cancelled' );
		$this->setRedirect( 'index.php?option=com_vdata&view=logs', $msg );
	
	}
	
	
}