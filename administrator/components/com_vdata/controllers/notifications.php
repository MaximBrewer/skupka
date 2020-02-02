<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class  VdataControllerNotifications extends VdataController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		$this->model = $this->getModel('notifications');
		JFactory::getApplication()->input->set( 'view', 'notifications' );
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
	}
	
	function display($cachable = false, $urlparams = false)
	{
		$user = JFactory::getUser();
		$canViewNotifications = $user->authorise('core.access.notifications', 'com_vdata');
		
		if(!$canViewNotifications){
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
		JFactory::getApplication()->input->set( 'view', 'notifications' );
		JFactory::getApplication()->input->set( 'layout', 'form'  );
		JFactory::getApplication()->input->set('hidemainmenu', 1);
		
		parent::display();
	}

	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		if($this->model->store()) {
			$msg = JText::_( 'NOTIFICATION_SAVED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=notifications', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=notifications');
		}
	}
	
	function apply()
	{
		if($this->model->store()) {
			$msg = JText::_( 'NOTIFICATION_SAVED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=notifications&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0), $msg );
		} else {
			JFactory::getApplication()->enqueueMessage( $this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=notifications&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0) );
		}

	}
	
	function save2copy()
	{
		if($this->model->store()) {
			$msg = JText::_( 'NOTIFICATION_SAVED_AS_COPY' );
			$this->setRedirect( 'index.php?option=com_vdata&view=notifications&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0), $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=notifications&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0) );
		}
	}
	
	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		if($this->model->delete()) {
			$msg = JText::_( 'RECORDS_DELETED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=notifications', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=notifications');
		}
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vdata&view=notifications', $msg );
	}
	
	function publish(){
		$cid = JFactory::getApplication()->input->get('cid', array(), 'ARRAY');
		$this->model->publishList($cid, 1);
		$this->setRedirect('index.php?option=com_vdata&view=notifications');
		
	}
	
	function unpublish(){
		$cid = JFactory::getApplication()->input->get('cid', array(), 'ARRAY');
		$this->model->publishList($cid, 0);
		$this->setRedirect('index.php?option=com_vdata&view=notifications');
	}
	
	function getTableColumns(){
		JSession::checkToken() or jexit('{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}');
		$result = new stdClass();
		$table = JFactory::getApplication()->input->get('table', '', 'RAW');
		if(empty($table)){
			$result->result = 'error';
			$result->error = JText::_('SELECT_TABLE');
			jexit(json_encode($result));
		}
		$html = $this->model->getTableColumns($table);
		
		$result->result = 'success';
		$result->html = $html;
		jexit(json_encode($result));
	}
	
	function getNotificationColumn(){
		JSession::checkToken() or jexit('{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}');
		$result = new stdClass();
		$table = JFactory::getApplication()->input->get('table', '', 'RAW');
		$query = JFactory::getApplication()->input->get('query', '', 'RAW');
		$tableColumns = JFactory::getApplication()->input->get('table_columns', array(), 'raw');
		if(empty($table) && empty($query)){
			$result->result = 'error';
			$result->error = JText::_('ENER_QUERY_OR_SELECT_TABLE');
			jexit(json_encode($result));
		}
		
		$obj  = $this->model->getNotificationColumn($table, $tableColumns, $query);
		
		if($obj->result=='error'){
			$result->result = 'error';
			$result->error = $obj->error;
			jexit(json_encode($result));
		}
		$result->result = 'success';
		$result->html = $obj->html;
		jexit(json_encode($result));
	}
	
	
}