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
defined('_JEXEC') or die('Restricted access');


class VdataControllerImport extends VdataController
{
	
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		$this->model = $this->getModel('import');
		JFactory::getApplication()->input->set( 'view', 'import' );
	
	}
	
	function display($cachable = false, $urlparams = false){
		
		$user = JFactory::getUser();
		$canViewImport = $user->authorise('core.access.import', 'com_vdata');
		
		if(!$canViewImport){
			$msg = JText::_( 'ALERT_AUTHORIZATION_ERROR' );
			$this->setRedirect( 'index.php?option=com_vdata', $msg , 'error');
		}
			
		parent::display();
		
	}
	
	function importready()	{

		$st = JFactory::getApplication()->input->getInt('st', 0);
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		JFactory::getApplication()->input->set('layout', 'import');
		parent::display();
          
	
	}
	
	function plugintaskajax()
	{
		
		$plugin = explode('.', JFactory::getApplication()->input->get('plugin', ''));
		
		if(count($plugin)==2)	{
		
			JPluginHelper::importPlugin('vdata', $plugin[0]);
			$dispatcher = JDispatcher::getInstance();
			
			try{
				$dispatcher->trigger($plugin[1]);
			}catch(Exception $e){
				jexit('{"result":"error", "error":"'.$e->getMessage().'"}');
			}
		
		}
		
	}
	
	function import_start()	{
		
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		
		$model = $this->getModel('import');
		$profile = $model->getProfile();
		
		$user = JFactory::getUser();
		$log = new stdClass();
		$log->iotype = $profile->iotype;
		$log->profileid = $profile->id;
		$profile_params = json_decode($profile->params);
		$log->table = $profile_params->table;
		$log->side = $app->isAdmin() ? 'administrator' : 'site';
		$log->user = $user->get('id',0);
		$importitem = $session->get('importitem');
		if(empty($importitem)){
			jexit('{"result":"error", "error":"'.JText::_('EXCEPTION').'", "error_msg":"'.JText::_('REINITIATE_PROCESS').'"}');
		}
		$iofile = $session->get('iofile', '');
		if(!empty($iofile)){
			// $log->iofile = $iofile;
			$log->iofile = is_array($iofile)? implode(', ',$iofile):$iofile;
		}
		elseif($importitem->server=='local')
			$log->iofile = JPATH_ROOT.'\\'.$importitem->path;
		else
			$log->iofile = $importitem->path;
		
		$offset = JFactory::getApplication()->input->getInt('offset');
		if($offset==0){
			$session->set('op_start',JFactory::getDate()->toSql(true));
		}
		
		
		JPluginHelper::importPlugin('vdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		try	{
			$return = $dispatcher->trigger('batchImport');
			if($return[0]->result =='success')	{
				jexit(json_encode($return[0]));
			}
			else	{
				$config = $this->getConfig();
				//on success redirect to profile,on error redirect to same page with errror message
				if(property_exists($return[0],'qty')){
					if(property_exists($return[0],'totalRecordInFile')){
						$msg = JText::sprintf('IMPORT_SUCCESS_OUT_OF', $return[0]->qty, $return[0]->totalRecordInFile);
					}else{
						$msg = JText::sprintf('IMPORT_SUCCESS', $return[0]->qty);
					}
					$return[0]->success_msg = $msg;
					$redirect_url = 'index.php?option=com_vdata&view=import';
					$redirect_url = JRoute::_($redirect_url, false);
					$return[0]->redirect_url = $redirect_url;
					
					$log->message = JText::sprintf('LOG_IMPORT_SUCCESS_MSG', $return[0]->qty);
					$log->iocount = $return[0]->qty;
					$log->op_start = $session->get('op_start');
					$log->op_end = JFactory::getDate()->toSql(true);
					$log->logfile = $session->get('logfile');
					$log->status = 'success';
					if($config->logging){
						$log_status = $model->setLog($log);
					}
					$session->clear('iofile');
					jexit(json_encode($return[0]));
				}
				else{
					$msg = JText::sprintf('IMPORT_FAIL', $return[0]->error);
					$return[0]->error_msg = $msg;
					$redirect_url = 'index.php?option=com_vdata&view=import&layout=import&profileid='.$profile->id;
					$redirect_url = JRoute::_($redirect_url, false);
					$return[0]->redirect_url = $redirect_url;
					/* if(property_exists($return[0], 'qty'))
						$ct = $return[0]->qty;
					else
						$ct = 0; */
					$log->message = JText::sprintf('LOG_IMPORT_ERROR_MSG', $return[0]->error_msg);
					$log->op_start = $session->get('op_start');
					$log->op_end = JFactory::getDate()->toSql(true);
					$log->logfile = $session->get('logfile');
					$log->status = 'error';
					if($config->logging){
						$log_status = $model->setLog($log);
					}
					$session->clear('iofile');
					jexit(json_encode($return[0]));
				}
			}
		}
		catch(Exception $e)	{
			$excep_msg = $e->getMessage();
			$excep_msg = $this->escapeJsonString($excep_msg);
			jexit('{"result":"error", "error":"'.JText::_('EXCEPTION').'", "error_msg":"'.$excep_msg.'"}');
		}
	}
	
	function escapeJsonString($value) { 
		$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
		$result = str_replace($escapers, $replacements, $value);
		return $result;
	}
	/**
	 * cancel an action
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vdata', $msg );
	}
	
	/**
	 * cancel an action
	 * @return void
	 */
	function close()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vdata&view=import', $msg );
	}
	
	function save_st()
	{
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		if($this->model->store()) {
			$msg = JText::_( 'SCHEDULE_SAVED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			// $this->setRedirect( 'index.php?option=com_vdata&view=schedules');
			$session = JFactory::getSession();
			$importitem = $session->get('importitem');
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules&task=edit&cid[]='.$importitem->id );
		}
	}
	
	function save_st2copy(){
		if($this->model->store()) {
			$msg = JText::_( 'SCHEDULE_SAVED_AS_COPY' );
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			// $this->setRedirect( 'index.php?option=com_vdata&view=schedules');
			$session = JFactory::getSession();
			$importitem = $session->get('importitem');
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules&task=edit&cid[]='.$importitem->id );
		}
	}
	
	function close_st()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
	}
	
	function create_st()
	{
		
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		$session = JFactory::getSession();
		$importitem = $session->get('importitem', null);
		$schedule_columns = (object)JFactory::getApplication()->input->post->getArray();
		
		$db = JFactory::getDbo();
		$query = 'select i.*, e.element as plugin from #__vd_profiles as i join #__extensions as e on (i.pluginid=e.extension_id and e.enabled=1) where i.id = '.$importitem->profileid;
		$db->setQuery( $query );
		$profile = $db->loadObject();
		
		$col = new stdClass();
		
		if($profile->quick){
			$quick = JFactory::getApplication()->input->getInt('quick',0);
			$col->quick = $quick;
		}
		
		switch($importitem->source){
			case 'csv':
				$fields = JFactory::getApplication()->input->get('csvfield', array(), 'ARRAY');
				$col->fields = $fields;
			break;
			case 'xml':
				$fields = JFactory::getApplication()->input->get('xmlfield', array(), 'ARRAY');
				$col->fields = $fields;
			break;
			case 'json':
				$fields = JFactory::getApplication()->input->get('jsonfield', array(), 'ARRAY');
				$col->fields = $fields;
			break;
			case 'remote':
				$fields = JFactory::getApplication()->input->get('fields', array(), 'RAW');
				$col->fields = $fields;
				$remote_table = JFactory::getApplication()->input->get('table', '', 'RAW');
				$col->table = $remote_table;
				if($profile->quick != 1){
					$join_table = JFactory::getApplication()->input->get('join_table', array(), 'RAW');
					$col->join_table = $join_table;
				}
			break;
		}

		$session->set('columns', json_encode($col));
		$this->setRedirect('index.php?option=com_vdata&view=schedules&task=edit&profileid='.$importitem->profileid.'&iotype=0');
		
	}
	
	function getConfig()
	{
		$db = JFactory::getDbo();
		$query = "select * from #__vd_config where id =1";
		$db->setQuery($query);
		return $db->loadObject();
	}
	
}