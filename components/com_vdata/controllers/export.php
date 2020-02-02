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


class VdataControllerExport extends VdataController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
		$this->model = $this->getModel('export');
		JFactory::getApplication()->input->set( 'view', 'export' );
	}
	
	function display($cachable = false, $urlparams = false){
		
		$user = JFactory::getUser();
		$canViewExport = $user->authorise('core.access.export', 'com_vdata');
		if(!$canViewExport){
			$msg = JText::_( 'ALERT_AUTHORIZATION_ERROR' );
			$this->setRedirect( 'index.php?option=com_vdata', $msg , 'error');
		}
		
		parent::display();
	}
	
	function exportready()	{		
		JSession::checkToken('REQUEST') or jexit( JText::_('INVALID_TOKEN') );
		//select either profile or query
		
		
        JFactory::getApplication()->input->set('layout', 'export');
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
	
	function export_start()	{
		
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		$model = $this->getModel('export');		
		$profile = $model->getProfile();
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		
		$user = JFactory::getUser();
		$log = new stdClass();
		$log->iotype = $profile->iotype;
		$log->profileid = $profile->id;
		$profile_params = json_decode($profile->params);
		$log->table = $profile_params->table;
		$log->side = $app->isAdmin() ? 'administrator' : 'site';
		$log->user = $user->get('id',0);

		$exportitem = $session->get('exportitem');
		if(empty($exportitem)){
			jexit('{"result":"error", "error":"'.JText::_('EXCEPTION').'", "error_msg":"'.JText::_('REINITIATE_PROCESS').'"}');
		}
		if($exportitem->server=='write_local'){
			$log->iofile = JPATH_ROOT.'\\'.$exportitem->path;
		}
		elseif($exportitem->server=='write_remote'){
			$log->iofile = $exportitem->path;
		}
		
		$offset = JFactory::getApplication()->input->getInt('offset');
		if($offset==0){
			$session->set('op_start',JFactory::getDate()->toSql(true));
		}
		
		JPluginHelper::importPlugin('vdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		try	{
			$return = $dispatcher->trigger('batchExport');
			
			if($return[0]->result =='success')	{
				jexit(json_encode($return[0]));
			}
			else	{
				$config = $this->getVdConfig();
				//on success redirect to profile,on error redirect to same page with errror message
				if(property_exists($return[0],'qty')){
					$msg = JText::sprintf('EXPORT_SUCCESS', $return[0]->qty);
					$return[0]->success_msg = $msg;
					$redirect_url = 'index.php?option=com_vdata&view=export';
					$redirect_url = JRoute::_($redirect_url, false);
					$return[0]->redirect_url = $redirect_url;
					// unset($return[0]->file);//
					
					if($return[0]->dlink){
						$return[0]->down = JRoute::_(JURI::base().'index.php?option=com_vdata&view=export&task=download&file='.$return[0]->dname, false);
					}
					
					$log->message = JText::sprintf('LOG_EXPORT_SUCCESS_MSG', $return[0]->qty);
					$log->iocount = $return[0]->qty;
					$log->op_start = $session->get('op_start');
					$log->op_end = JFactory::getDate()->toSql(true);
					$log->logfile = $session->get('logfile');
					$log->status = 'success';
					if($config->logging){
						$log_status = $model->setLog($log);
					}
					jexit(json_encode($return[0]));
				}
				else{
					$msg = JText::sprintf('EXPORT_FAIL', $return[0]->error);
					$return[0]->error_msg = $msg;
					$redirect_url = 'index.php?option=com_vdata&view=export&layout=export&profileid='.$profile->id;
					$redirect_url = JRoute::_($redirect_url, false);
					$return[0]->redirect_url = $redirect_url;
					
					$down = $session->get('down','');
					if(!empty($down))
						$log->iofile = JPATH_ROOT.'\\'.'components\\com_vdata\\uploads\\'.$down;
					$log->message = JText::sprintf('LOG_EXPORT_ERROR_MSG', $return[0]->error_msg);
					$log->op_start = $session->get('op_start');
					$log->op_end = JFactory::getDate()->toSql(true);
					$log->logfile = $session->get('logfile');
					$log->status = 'error';
					if($config->logging){
						$log_status = $model->setLog($log);
					}
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
		$this->setRedirect( 'index.php?option=com_vdata&view=export', $msg );
	}
	
	function download(){
		$file = JFactory::getApplication()->input->get('file', '');
		// header('Content-Type: application/xml; charset="utf8"');
		header("Content-type: application/octet-stream");
		header('Content-Disposition: attachment; filename="'.$file.'"');
		// header("Content-Transfer-Encoding: Binary");
		$filepath = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vdata'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$file;
		//validate file exists
		readfile($filepath);

		jexit();
		
	}
	
	function save_st()
	{
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		if($this->model->store()) {
			$msg = JText::_( 'SCHEDULE_SAVED' );
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules');
		}
	}
	
	function save_st2copy(){ 
		if($this->model->store()) {
			$msg = JText::_( 'SCHEDULE_SAVED_AS_COPY' );
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
		} else {
			JFactory::getApplication()->enqueueMessage($this->model->getError());
			$this->setRedirect( 'index.php?option=com_vdata&view=schedules');
		}
	}
	
	function close_st()
	{
		$msg = JText::_( 'OP_CANCEL' );
		$this->setRedirect( 'index.php?option=com_vdata&view=schedules', $msg );
	}
	
	function create_st(){
		
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		$session = JFactory::getSession();
		$exportitem = $session->get('exportitem', null);
		$schedule_columns = (object)JFactory::getApplication()->input->post->getArray();
		
		$db = JFactory::getDbo();
		$query = 'select i.*, e.element as plugin from #__vd_profiles as i join #__extensions as e on (i.pluginid=e.extension_id and e.enabled=1) where i.id = '.$exportitem->profileid;
		$db->setQuery( $query );
		$profile = $db->loadObject();
		
		$col = new stdClass();
		
		if($profile->quick){
			$quick = JFactory::getApplication()->input->getInt('quick',0);
			$col->quick = $quick;
		}
		if($exportitem->source <> 'remote'){
			$fields = JFactory::getApplication()->input->get('field', array(), 'ARRAY');
			$col->fields = $fields;
		}
		else{
			$fields = JFactory::getApplication()->input->get('fields', array(), 'RAW');
			$col->fields = $fields;
			$remote_table = JFactory::getApplication()->input->get('table', '', 'RAW');
			$col->table = $remote_table;
			$unkey = JFactory::getApplication()->input->get('unkey', '', 'string');
			$col->unkey = $unkey;
			if($profile->quick != 1){
				$join_table = JFactory::getApplication()->input->get('join_table', array(), 'RAW');
				$col->join_table = $join_table;
			}
		}
		// var_dump($exportitem);
		// var_dump($schedule_columns);
		
		// var_dump($col);
		$session->set('columns', json_encode($col));
		$this->setRedirect('index.php?option=com_vdata&view=schedules&task=edit&profileid='.$exportitem->profileid.'&iotype=1');
		
	}
	
	function getVdConfig()
	{
		$db = JFactory::getDbo();
		$query = "select * from #__vd_config where id =1";
		$db->setQuery($query);
		return $db->loadObject();
	}
	
}