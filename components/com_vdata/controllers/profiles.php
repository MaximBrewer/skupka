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

class  VdataControllerProfiles extends VdataController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->model = $this->getModel('profiles');
		
		JFactory::getApplication()->input->set( 'view', 'profiles' );
	
		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );

	}
	
	function display($cachable = false, $urlparams = false)
	{
		
		$user = JFactory::getUser();
		$canViewProfiles = $user->authorise('core.access.profiles', 'com_vdata');
		if(!$canViewProfiles){
			$msg = JText::_( 'ALERT_AUTHORIZATION_ERROR' );
			$this->setRedirect( 'index.php?option=com_vdata', $msg , 'error');
		}
		
		parent::display();
	}
	
	function profile_wizard(){
		$this->setRedirect( 'index.php?option=com_vdata&view=profiles&layout=wizard', '' , '');
	}
	
	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JFactory::getApplication()->input->set( 'view', 'profiles' );
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
			$schedule_flag = $this->model->getAssocSchedule();
			if($schedule_flag){
				$id = JFactory::getApplication()->input->getInt('id', 0);
				if($id){
					$profile = $this->model->getProfileById($id);
					$msg = JText::sprintf( 'PROFILE_CHANGED_HANDLE_SCHEDULE_FIELDS', $profile->title, $schedule_flag );
				}
				else{
					$msg = JText::_( 'PROFILE_CHANGED_HANDLE_SCHEDULE' );
				}
				$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
			}
			else{
				$return = JFactory::getApplication()->input->getInt('type', '-1');
				$iotype = JFactory::getApplication()->input->getInt('iptype', '-1');
				$profileid = JFactory::getApplication()->input->getInt('id', 0);
				if( ($return==0) && ($iotype==0) ){
					$this->setRedirect('index.php?option=com_vdata&view=import&profileid='.$profileid);
				}
				elseif( ($return==1) && ($iotype==1) ){
					$this->setRedirect('index.php?option=com_vdata&view=export&profileid='.$profileid);
				}
				elseif($return==2){
					if($iotype==0){
						$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add&iotype=0&profileid='.$profileid);
					}
					elseif($iotype==1){
						$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add&iotype=1&profileid='.$profileid);
					}
					elseif($iotype==2){
						$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add&iotype=2&profileid='.$profileid);
					}
					else{
						$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add');
					}
				}
				else	{
					$msg = JText::_( 'PROFILE_SAVED' );
					$this->setRedirect( 'index.php?option=com_vdata&view=profiles', $msg );
				}
			}
			
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError(), 'error');
			$this->setRedirect( 'index.php?option=com_vdata&view=profiles');
		}

	}
	
	function apply()
	{
		if($this->model->store()) {
			$schedule_flag = $this->model->getAssocSchedule();
			if($schedule_flag){
				$id = JFactory::getApplication()->input->getInt('id', 0);
				if($id){
					$profile = $this->model->getProfileById($id);
					$msg = JText::sprintf( 'PROFILE_CHANGED_HANDLE_SCHEDULE_FIELDS', $profile->title, $schedule_flag );
				}
				else{
					$msg = JText::_( 'PROFILE_CHANGED_HANDLE_SCHEDULE' );
				}
				$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
			}
			else{
				
				$return = JFactory::getApplication()->input->getInt('type', '-1');
				$iotype = JFactory::getApplication()->input->getInt('iptype', '-1');
				$profileid = JFactory::getApplication()->input->getInt('id', 0);
				if( ($return==0) && ($iotype==0) ){
					$this->setRedirect('index.php?option=com_vdata&view=import&profileid='.$profileid);
				}
				elseif( ($return==1) && ($iotype==1) ){
					$this->setRedirect('index.php?option=com_vdata&view=export&profileid='.$profileid);
				}
				elseif($return==2){
					if($iotype==0){
						$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add&iotype=0&profileid='.$profileid);
					}
					elseif($iotype==1){
						$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add&iotype=1&profileid='.$profileid);
					}
					elseif($iotype==2){
						$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add&iotype=2&profileid='.$profileid);
					}
					else{
						$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add');
					}
				}
				else{
					$msg = JText::_( 'PROFILE_SAVED' );
					$this->setRedirect( 'index.php?option=com_vdata&view=profiles&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0), $msg );
				}
			}
			
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError(), 'error');
			$this->setRedirect( 'index.php?option=com_vdata&view=profiles&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0) );
		}

	}
	
	function save2copy(){
		
		if($this->model->store()) {
			$msg = JText::_( 'PROFILE_SAVED_AS_COPY' );
			$this->setRedirect( 'index.php?option=com_vdata&view=profiles&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0), $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError(), 'error');
			$this->setRedirect( 'index.php?option=com_vdata&view=profiles&task=edit&cid[]='.JFactory::getApplication()->input->getInt('id', 0) );
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
			$this->setRedirect( 'index.php?option=com_vdata&view=profiles', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError(), 'error');
			$this->setRedirect( 'index.php?option=com_vdata&view=profiles');
		}
		
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$return = JFactory::getApplication()->input->getInt('type', '-1');
		$iotype = JFactory::getApplication()->input->getInt('iptype', '-1');
		if( ($return==0) && ($iotype==0) )	{
			$this->setRedirect('index.php?option=com_vdata&view=import');
		}
		elseif( ($return==1) && ($iotype==1) )	{
			$this->setRedirect('index.php?option=com_vdata&view=export');
		}
		elseif($return==2)	{
			if($iotype==0){
				$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add&iotype=0');
			}
			elseif($iotype==1){
				$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add&iotype=1');
			}
			elseif($iotype==2){
				$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add&iotype=2');
			}
			else{
				$this->setRedirect('index.php?option=com_vdata&view=schedules&task=add');
			}
		}
		else	{
			$msg = JText::_( 'OP_CANCEL' );
			$this->setRedirect( 'index.php?option=com_vdata&view=profiles', $msg );
		}
	}
	
	function plugintaskajax()
	{
		$plugRoute = JFactory::getApplication()->input->get('plugin', '');
		$plugin = explode('.', $plugRoute);
		
		if(count($plugin)==2)	{
		
			JPluginHelper::importPlugin('vdata', $plugin[0]);
			$dispatcher = JDispatcher::getInstance();
			
			try{
				$dispatcher->trigger($plugin[1]);
			}catch(Exception $e){
				if($plugRoute=='custom.onEditProfile'){
					jexit($e->getMessage());
				}
				else{
					jexit('{"result":"error", "error":"'.$e->getMessage().'"}');
				}	
			}
		
		}
		
	}
		
	function export()
	{
		$model = $this->getModel('profiles');		
		$profile = $model->getProfile();
		
		JPluginHelper::importPlugin('vdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('startExport');
			jexit(/*JText::_('INTERNAL_SERVER_ERROR')*/);
		}catch(Exception $e){
			JFactory::getApplication()->enqueueMessage($e->getMessage());
			$this->setRedirect('index.php?option=com_vdata&view=profiles');
		}
			
	}
	
	function getRemoteProfiles(){
		
		JSession::checkToken() or jexit('{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}');
		$term = JFactory::getApplication()->input->get('term', '');
		$iotype = JFactory::getApplication()->input->getInt('iotype');
		$session_setting = JFactory::getApplication()->input->getInt('session_setting', '');
		//fetch profile id,title for autocomplete
		// $url = 'http://www.joomlawings.com/index.php';
		$url = 'http://www.wdmtech.com/demo/index.php';
		
		$install_components = $this->model->getInstallComponents();
		$enable_plg_elements = $this->model->getPlugins();
		$enable_plg_elements = json_encode($enable_plg_elements);
		
		$postdata = array("term"=>$term, "iotype"=>$iotype, "option"=>"com_vdata", "task"=>"getOptProfiles", "token"=>JSession::getFormToken(), "scope"=>$install_components, "plg_element"=>$enable_plg_elements,"session_setting"=>$session_setting);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($ch);
		if($result === false){
			jexit('{"result":"error", "error":"'.curl_error($ch).'"}');
		}
		curl_close($ch);
		jexit($result);
		
	}
	
	function schedule(){
		
		$input = JFactory::getApplication()->input;
		$profileid = $input->getInt('id', 0);
		$iotype = $input->getInt('iotype', 0);
		$this->setRedirect('index.php?option=com_vdata&view=schedules&task=edit&profileid='.$profileid.'&iotype='.$iotype);
		
	}
	
	function saveAsCopy(){
		
		$cid = JFactory::getApplication()->input->get('cid', array(), 'ARRAY');
		@sort($cid);
		if($this->model->copyList($cid)){
			$msg = count($cid)>1?JText::_('PROFILES_COPY_SUCCESSFUL'):JText::_('PROFILE_COPY_SUCCESSFUL');
			$this->setRedirect('index.php?option=com_vdata&view=profiles', $msg);
		}
		else{
			JFactory::getApplication()->enqueueMessage($this->model->getError(), 'error');
			$this->setRedirect('index.php?option=com_vdata&view=profiles');
		}
	}
	
}