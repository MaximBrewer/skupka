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

class  VdataControllerSchedules extends VdataController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		$this->model = $this->getModel('schedules');
		JFactory::getApplication()->input->set( 'view', 'schedules' );
		// Register Extra tasks
		$this->registerTask( 'add', 'edit' );
	}
	
	function display($cachable = false, $urlparams = false)
	{
		
		$user = JFactory::getUser();
		$canViewCronFeed = $user->authorise('core.access.cron', 'com_vdata');
		if(!$canViewCronFeed){
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
		JFactory::getApplication()->input->set( 'view', 'schedules' );
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
			$msg = JText::_( 'SCHEDULE_SAVED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules');
		}

	}
	
	function apply()
	{
		if($this->model->store()) {
			$msg = JText::_( 'SCHEDULE_SAVED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0), $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0) );
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
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules');
		}
		
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$session = JFactory::getSession();
		$session->clear('columns');
		
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
	}
	
	function getProfiles(){
		JSession::checkToken() or jexit('{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}');
		$input = JFactory::getApplication()->input;
		$profileid = $input->get('profileid', 0);
		
		$obj = new stdClass();
		$obj->result = 'success';
		$profiles = $this->model->getProfiles();
		$obj->html = '<option value="">'.JText::_('SELECT_PROFILE').'</option>';
		$obj->html .= '<option value="-1">'.JText::_('CREATE_PROFILE').'</option>';
		if(empty($profiles)){
			$obj->html .= '<option value="-1">'.JText::_('CREATE_PROFILE').'</option>';
			jexit(json_encode($obj));
		}
		foreach($profiles as $profile){
			$obj->html .= '<option value="'.$profile->id.'"';
			if($profileid && ($profileid==$profile->id)){
				$obj->html .= 'selected="selected"';
			}
			$obj->html .= '>'.$profile->title.'</option>';
		}
		
		jexit(json_encode($obj));
	}
	
	function publish(){
		$cid = JFactory::getApplication()->input->get('cid', array(), 'ARRAY');
		$model = $this->getModel('schedules');
		$model->publishList($cid, 1);
		$this->setRedirect('index.php?option=com_vdata&view=schedules');
		
	}
	
	function unpublish(){
		$cid = JFactory::getApplication()->input->get('cid', array(), 'ARRAY');
		$model = $this->getModel('schedules');
		$model->publishList($cid, 0);
		$this->setRedirect('index.php?option=com_vdata&view=schedules');
	}
	
	function save_st(){
		
		JRequest::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		if($this->model->save_st()){
			$session = JFactory::getSession();
			$session->clear('columns');
		
			$msg = JText::_( 'SCHEDULE_SAVED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
		} else {
			jerror::raiseWarning('', $this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules');
		}

	}
	
	function saveAsCopy(){
		
		$cid = JFactory::getApplication()->input->get('cid', array(), 'ARRAY');
		@sort($cid);
		if($this->model->copyList($cid)){
			// $msg = count($cid)>1?JText::_('SCHEDULES_COPY_SUCCESSFUL'):JText::_('SCHEDULE_COPY_SUCCESSFUL');
			 $msg = JText::_('SCHEDULES_COPY_SUCCESSFUL');
			$this->setRedirect('index.php?option=com_vdata&view=schedules', $msg);
		}
		else{
			JFactory::getApplication()->enqueueMessage($this->model->getError(), 'error');
			$this->setRedirect('index.php?option=com_vdata&view=schedules');
		}
	
	}
	
}