<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2016 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once( JPATH_ADMINISTRATOR.'/components/com_vdata/classes/mvc/controller.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_vdata/classes/mvc/model.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_vdata/classes/mvc/view.php' );

class VdataController extends VDController
{
	
	function display($cachable = false, $urlparams = false)
	{
		if(!$this->sstatus())
		{  
			JRequest::setVar('view', 'vdata');
			JRequest::setVar('layout', 'information');
		}   
		
		parent::display();	
	}
	function sstatus()
	{return true;
		$db = JFactory::getDbo();
		$task = JFactory::getApplication()->input->get('task', '');
		if($task =='checkstatus')
			return true;
		$query = 'select `sstatus` from `#__vd_config`';
		$db->setQuery($query);
		if($db->loadResult())
		{
		return true;	
		}
		else
		return false;	
			
		
	}
	function checkstatus(){
		JSession::checkToken() or jexit('{"result":"error", "error":"'.JText::_('INVALID_TOKEN').'"}');
		$password = JFactory::getApplication()->input->get('password', '', 'RAW');
		$emailaddress = JFactory::getApplication()->input->get('emailaddress', '', 'RAW');
		$url = 'http://www.wdmtech.com/demo/index.php';
		$postdata = array("option"=>"com_vmap", "task"=>"checkstatus", "password"=>$password, "emailaddress"=>$emailaddress, "componentname"=>"com_vdata", "token"=>JSession::getFormToken());
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$status = curl_exec($ch);
		$sub = new stdClass();
		$sub->result ="success";
		$sub->status = "No";  
		if($status === false)
		{  
		jexit('{"result":"error", "error":"'.curl_error($ch).'"}');
		}
		else
		{
			$status = json_decode($status); 
			if(isset($status->result) && $status->result=="success")
			{
				
				$sub->msg = $status->error;
				if(isset($status->status) && $status->status=="subscr")
				{
					$db =  JFactory::getDbo();
					$query = 'update `#__vd_config` set `sstatus`=1';
					$db->setQuery($query);
					$db->execute();
					$sub->result ="success";
					$sub->status ="ok";
				}
			}
			
		}
		
		curl_close($ch);
		jexit(json_encode($sub));
		
	}
	function set_import()
	{
		//server only access
		if( empty($_SERVER['REMOTE_ADDR']) || empty($_SERVER['SERVER_ADDR']) || ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) ){
			jexit('{"result":"error", "error":"'.JText::_('ACCESS_DENIED').'"}');
		}
		
		$cf = $this->getValidSchedule();
		// $cf = $this->authenticate(0);
		JPluginHelper::importPlugin('vdata', 'custom');
		// JPluginHelper::importPlugin('vdata', $profile->plugin);
		
		$session = JFactory::getSession();
		$session->set('op_start',JFactory::getDate()->toSql(true));
		
		$dispatcher = JDispatcher::getInstance();
		try	{
			$cron_feed_model =  $this->getModel('schedules');
			$cron_feed_tbl = $cron_feed_model->getTable();
			$cron_feed_tbl->hit($cf->id);
			$return = $dispatcher->trigger('setImport');
		}
		catch(Exception $e)	{
			$excep_msg = $e->getMessage();
			jexit('{"result":"error", "error":"'.$excep_msg.'"}');
		}
	}
	
	function set_export()
	{
		//server only access
		if( empty($_SERVER['REMOTE_ADDR']) || empty($_SERVER['SERVER_ADDR']) || ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) ){
			jexit('{"result":"error", "error":"'.JText::_('ACCESS_DENIED').'"}');
		}
		$cf = $this->getValidSchedule();
		// $cf = $this->authenticate(1);
		JPluginHelper::importPlugin('vdata', 'custom');
		// JPluginHelper::importPlugin('vdata', $profile->plugin);
		
		$session = JFactory::getSession();
		$session->set('op_start',JFactory::getDate()->toSql(true));
		
		$dispatcher = JDispatcher::getInstance();
		try	{
			$cron_feed_model =  $this->getModel('schedules');
			$cron_feed_tbl = $cron_feed_model->getTable();
			$cron_feed_tbl->hit($cf->id);
			$return = $dispatcher->trigger('setExport');
		}
		catch(Exception $e)	{
			$excep_msg = $e->getMessage();
			jexit('{"result":"error", "error":"'.$excep_msg.'"}');
		}
	}
	
	function getValidSchedule(){
		
		$input = JFactory::getApplication()->input;
		$schedule_uid = $input->get('type','');
		
		$db = JFactory::getDbo();
		$query = 'select * from #__vd_schedules where state=1 and uid='.$db->quote($schedule_uid);
		$db->setQuery($query);
		$schedule = $db->loadObject();
		if( empty($schedule) ){
			jexit('{"result":"error", "error":"'.JText::_('INVALID_TYPE').'"}');
		}
		return $schedule;
		
	}
	
	function get_feeds()
	{
		$cf = $this->authenticate(2);
		JPluginHelper::importPlugin('vdata', 'custom');
		// JPluginHelper::importPlugin('vdata', $profile->plugin);
		
		$session = JFactory::getSession();
		$session->set('op_start',JFactory::getDate()->toSql(true));
		
		$dispatcher = JDispatcher::getInstance();
		try	{
			$cron_feed_model =  $this->getModel('schedules');
			$cron_feed_tbl = $cron_feed_model->getTable();
			$tst = $cron_feed_tbl->hit($cf->id);
			$return = $dispatcher->trigger('getFeeds');
			
		}
		catch(Exception $e)	{
			$excep_msg = $e->getMessage();
			jexit('{"result":"error", "error":"'.$excep_msg.'"}');
		}
		
	}
	
	function set_feed()
	{
		
		$cf = $this->authenticate(2);
		JPluginHelper::importPlugin('vdata', 'custom');
		$dispatcher = JDispatcher::getInstance();
		
		$session = JFactory::getSession();
		$session->set('op_start',JFactory::getDate()->toSql(true));
		
		try	{
			$cron_feed_model =  $this->getModel('schedules');
			$cron_feed_tbl = $cron_feed_model->getTable();
			$tst = $cron_feed_tbl->hit($cf->id);
			$return = $dispatcher->trigger('setFeed');
			
		}
		catch(Exception $e)	{
			$excep_msg = $e->getMessage();
			jexit('{"result":"error", "error":"'.$excep_msg.'"}');
		}
		
	}
	
	function authenticate($type)
	{
		$user = JFactory::getUser();
		// $groups = implode(',', $user->getAuthorisedViewLevels());
		$input = JFactory::getApplication()->input;
		$schedule_uid = $input->get('type','');
		$username = $input->get('username', '');
		$password = $input->get('password', '');
		
		$db = JFactory::getDbo();
		$query = 'select * from #__vd_schedules where state=1 and uid='.$db->quote($schedule_uid);
		$db->setQuery($query);
		$schedule = $db->loadObject();
		if( empty($schedule) || ($schedule->type!=$type) )
			jexit('{"result":"error", "error":"'.JText::_('INVALID_TYPE').'"}');
		
			
		if( !in_array($schedule->access, $user->getAuthorisedViewLevels()) ){
			if( !empty($username) ){
				$credentials = array();
				$credentials['username'] = $username;
				$credentials['password'] = $password;
				jimport( 'joomla.user.authentication' );
				$authenticate =  JAuthentication::getInstance();
				$response	  = $authenticate->authenticate( $credentials );
				if( $response->status==1 ){
					//check user whether use can access schedule
					$userId    = JUserHelper::getUserId($username);
					$groups = JAccess::getAuthorisedViewLevels($userId);
					if(!in_array($schedule->access, $groups))
						jexit('{"result":"error", "error":"'.JText::_('ACCESS_DENIED').'"}');
				}
				else
					jexit('{"result":"error", "error":"'.$response->error_message.'"}');
			}
			else
				jexit('{"result":"error", "error":"'.JText::_('ACCESS_DENIED').'"}');
		}
		return $schedule;
	}
	
	
	function notification(){
		
		//server only access
		if( empty($_SERVER['REMOTE_ADDR']) || empty($_SERVER['SERVER_ADDR']) || ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) ){
			jexit('{"result":"error", "error":"'.JText::_('ACCESS_DENIED').'"}');
		}
		$input = JFactory::getApplication()->input;
		$widget_id = $input->getInt('widget',0);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query = 'SELECT * FROM '.$db->quoteName('#__vd_widget').' WHERE '.$db->quoteName('id').'='.$db->quote($widget_id);
		$db->setQuery($query);
		$widget = $db->loadObject();
		if(empty($widget)){
			jexit('{"result":"error", "error":"'.JText::_('WIDGET_NOT_FOUND').'"}');
		}
		$res = $this->sendNotification($widget);
		if($res->result=='success'){
			jexit('{"result":"success", "message":"'.JText::_('NOTIFICATION_SENT_SUCCESSFULLY').'"}');
		}
		else{
			jexit('{"result":"error", "error":"'.$res->error.'"}');
		}
		
	}
	
	function sendNotification($widget){
		
		$obj = new stdClass();
		$widget_details = json_decode($widget->detail);
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		if(isset($widget_details->notification->custom_condition) && !empty($widget_details->notification->custom_condition)){
			if (preg_match("/^select (.*)/i", trim($widget_details->notification->custom_condition)) > 0){
				
				try{
					$db->setQuery($widget_details->notification->custom_condition);
					$result = $db->loadObjectList();
				}
				catch(Exception $e){
					$obj->result = 'error';
					$obj->error = $e->getMessage();
					return $obj;
				}
				if(empty($result)){
					$obj->error = JText::_('NOTIFICATION_CONDITION_FAILED');
					$obj->result = 'error';
					return $obj;
				}
				if(isset($widget_details->notification->email_tmpl) && !empty($widget_details->notification->email_tmpl)){
					$body = $widget_details->notification->email_tmpl;
					$body = $this->filterTemplate($body, $result);
					return $this->sendMail($body, $widget->name);
				}
				else{
					$obj->error = JText::_('NOTIFICATION_TEMPLATE_NOT_FOUND');
					$obj->result = 'error';
					return $obj;
				}
				
			}
		}
		elseif(!empty($widget_details->existing_database_table)){
			$query = 'SELECT * FROM '.$db->quoteName($widget_details->existing_database_table);
			$query .= ' WHERE 1=1 ';
			foreach($widget_details->notification->condition_col as $key=>$col){
				
				switch($widget_details->notification->condition[$key]){
					case 'equal':
						$query .= $widget_details->notification->closure.' '.$db->quoteName($col).' = '.$widget_details->notification->condition_val[$key];
					break;
					case 'not_equal':
						$query .= $widget_details->notification->closure.' '.$db->quoteName($col).' <> '.$widget_details->notification->condition_val[$key];
					break;
					case 'in':
						$query .= $widget_details->notification->closure.' '.$db->quoteName($col).' IN ('.$widget_details->notification->condition_val[$key].')';
					break;
					case 'not_in':
						$query .= $widget_details->notification->closure.' '.$db->quoteName($col).' NOT IN ('.$widget_details->notification->condition_val[$key].')';
					break;
					case 'less':
						$query .= $widget_details->notification->closure.' '.$db->quoteName($col).' < '.$widget_details->notification->condition_val[$key];
					break;
					case 'less_or_equal':
						$query .= $widget_details->notification->closure.' '.$db->quoteName($col).' <= '.$widget_details->notification->condition_val[$key];
					break;
					case 'greater':
						$query .= $widget_details->notification->closure.' '.$db->quoteName($col).' > '.$widget_details->notification->condition_val[$key];
					break;
					case 'greater_or_equal':
						$query .= $widget_details->notification->closure.' '.$db->quoteName($col).' >= '.$widget_details->notification->condition_val[$key];
					break;
					case 'between':
						$between = explode(',', $widget_details->notification->condition_val[$key]);
						if(count($between)==2){
							$query .= $widget_details->notification->closure.' '.$db->quoteName($col).' BETWEEN '.$between[0].' AND '.$between[1];
						}
					break;
				}
			}
			
			try{
				$db->setQuery($query);
				$result = $db->loadObjectList();
			}
			catch(Exception $e){
				$obj->result = 'error';
				$obj->error = $e->getMessage();
				return $obj;
			}
			if(empty($result)){
				$obj->error = JText::_('NOTIFICATION_CONDITION_FAILED');
				$obj->result = 'error';
				return $obj;
			}
			if(isset($widget_details->notification->email_tmpl) && !empty($widget_details->notification->email_tmpl)){
				$body = $widget_details->notification->email_tmpl;
				$body = $this->filterTemplate($body, $result);
				return $this->sendMail($body, $widget->name);
			}
			else{
				$obj->error = JText::_('NOTIFICATION_TEMPLATE_NOT_FOUND');
				$obj->result = 'error';
				return $obj;
			}
			
		}
		else{
			$obj->error = JText::_('NOTIFICATION_CONDITION_INVALID');
			$obj->result = 'error';
			return $obj;
		}
	}
	
	function filterTemplate($body, $result){
		
		preg_match_all('~\{{(.+?)\}}~', $body, $matches);
		
		foreach($matches[0] as $key=>$match){
			if(isset($matches[1][$key]) && property_exists($result[0], $matches[1][$key])){
				$place_value = array();
				foreach($result as $idx=>$row){
					array_push($place_value, $row->{$matches[1][$key]});
				}
				$place_value = implode(',', $place_value);
				$body = preg_replace('/'.$match.'/', $place_value, $body);
			}
		}
		return $body;
		
	}
	
	function sendMail($body, $widget_name){
		
		$obj = new stdClass();
		$mailer = JFactory::getMailer();
		
		//sender
		$config = JFactory::getConfig();
		$sender = array( 
			$config->get( 'mailfrom' ),
			$config->get( 'fromname' ) 
		);
		$mailer->setSender($sender);
		
		//recipient
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query = 'SELECT '.$db->quoteName('email').' from '.$db->quoteName('#__users').' where '.$db->quoteName('sendEmail').'=1';
		$db->setQuery($query);
		$recipients = $db->loadColumn();
		$mailer->addRecipient($recipients);
		
		//subject
		// $mailer->setSubject(JText::_('COM_VDATA_WIDGET_NOTIFICATION_SUBJECT'));
		$mailer->setSubject(JText::sprintf('COM_VDATA_WIDGET_NOTIFICATION_SUBJECT', $widget_name));
		
		//body
		$mailer->setBody($body);
		$mailer->isHTML();
		$mailer->Encoding = 'base64';
		
		//send
		$send = $mailer->send();
		if($send !== true){
			$obj->result = 'error';
			$obj->error = $send->__toString();
		}
		else{
			$obj->result = 'success';
			$obj->message = JText::_('NOTIFICATION_SENT_SUCCESSFULLY');
		}
		return $obj;
		
	}
	
	function get_notification(){
		//server only access
		/* if( empty($_SERVER['REMOTE_ADDR']) || empty($_SERVER['SERVER_ADDR']) || ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) ){
			jexit('{"result":"error", "error":"'.JText::_('ACCESS_DENIED').'"}');
		} */
		$input = JFactory::getApplication()->input;
		$notification_id = $input->getInt('id',0);
		$op_start = JFactory::getDate($op_start)->format('m-d-Y');
		$log_file = 'com_vdata_plg_custom_'.$op_start.'.txt';
		JLog::addLogger(
			array(
			'text_file' => $log_file,
			'text_file_no_php' => 'false'
			),
			JLog::ALL,
			array('com_vdata')
		);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query = 'SELECT * FROM '.$db->quoteName('#__vd_notifications').' WHERE state=1';
		$db->setQuery($query);
		$notifications = $db->loadObjectList();
		if(empty($notifications)){
			JLog::add(JText::_('NO_NOTIFICATION_FOUND'), JLog::ERROR, 'com_vdata');
			jexit('{"result":"error", "error":"'.JText::_('NO_NOTIFICATION_FOUND').'"}');
		}
		
		foreach($notifications as $notification){
			
			$res = $this->sendEmailNotification($notification);
			if($res->result=='success'){
				JLog::add($res->message, JLog::INFO, 'com_vdata');
				// jexit('{"result":"success", "message":"'.JText::_('NOTIFICATION_SENT_SUCCESSFULLY').'"}');
			}
			else{
				JLog::add($res->error, JLog::INFO, 'com_vdata');
				// jexit('{"result":"error", "error":"'.$res->error.'"}');
			}
		}
		jexit('{"result":"success", "message":"'.JText::_('NOTIFICATION_SENT_SUCCESSFULLY').'"}');
	}
	
	function sendEmailNotification($notification){
		
		$obj = new stdClass();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query_params = json_decode($notification->params);
		if(empty($query_params->custom->table) && empty($query_params->query)){
			$obj->result = 'error';
			$obj->error = JText::_('NOTIFICATION_CONDITION_NOT_MET');
			return $obj;
		}
		
		if(!empty($query_params->custom->table)){
			$query = "SELECT ";
			if(isset($query_params->custom->columns) && count($query_params->custom->columns)>0){
				foreach($query_params->custom->columns as $sidx=>$scolumn){
					$query .= $db->quoteName($scolumn);
					if($sidx<(count($query_params->custom->columns)-1)){
						$query .= ", ";
					}
				}
			}
			else{
				$query .= " * ";
			}
			
			$query .= " FROM ".$db->quoteName($query_params->custom->table);
			
			if(!empty($query_params->filters) && property_exists($query_params->filters, 'column')){
				
				$query .= " WHERE ";
				$m = $n = -1;
				foreach($query_params->filters->column as $j=>$column){
					
					$query .= $db->quoteName($column)." ";
					
					if($query_params->filters->cond[$j]=='between' || $query_params->filters->cond[$j]=='notbetween'){
						$n = ($n<0)?0:$n+1;
					}
					else{
						$m = ($m<0)?0:$m+1;
						$value = $this->getQueryFilteredValue($query_params->filters->value[$m]);
					}
					
					if($query_params->filters->cond[$j]=='in'){
						$query .= " IN ( ".$db->quote($value)." )";
					}
					elseif($query_params->filters->cond[$j]=='notin'){
						$query .= " NOT IN ( ".$db->quote($value)." )";
					}
					elseif($query_params->filters->cond[$j]=='between'){
						$values = explode(',', $value);
						$value1 = $this->getQueryFilteredValue($values[0]);
						$value2 = $this->getQueryFilteredValue($values[1]);
						// $value1 = $this->getQueryFilteredValue($query_params->filters->value1[$n]);
						// $value2 = $this->getQueryFilteredValue($query_params->filters->value2[$n]);
						$query .= " BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
					}
					elseif($query_params->filters->cond[$j]=='notbetween'){
						$values = explode(',', $value);
						$value1 = $this->getQueryFilteredValue($values[0]);
						$value2 = $this->getQueryFilteredValue($values[1]);
						// $value1 = $this->getQueryFilteredValue($query_params->filters->value1[$n]);
						// $value2 = $this->getQueryFilteredValue($query_params->filters->value2[$n]);
						$query .= " NOT BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
					}
					elseif($query_params->filters->cond[$j]=='like'){
						$query .= " LIKE ".$db->quote($value);
					}
					elseif($query_params->filters->cond[$j]=='notlike'){
						$query .= " NOT LIKE ".$db->quote($value);
					}
					elseif($query_params->filters->cond[$j]=='regexp'){
						$query .= " REGEXP ".$db->quote($query_params->filters->value[$j]);
					}
					else{
						$query .= $query_params->filters->cond[$j]." ".$db->quote($value);
					}

					//sql operator
					if($j < (count($query_params->filters->column)-1)){
						$query .= " ".$query_params->custom->clause." ";
					}
				}
			}
			
			if(isset($query_params->filters->additional) && !empty($query_params->filters->additional)){
				if(!empty($query_params->filters) && property_exists($query_params->filters, 'column')){
					$query .= " AND ".$query_params->filters->additional;
				}
				else{
					$query .= " WHERE ".$query_params->filters->additional;
				}
			}
			
			// if(!empty($query_params->custom->groupby)){
				// $query .= " GROUP BY ".$db->quoteName($query_params->custom->groupby);
			// }
			if(!empty($query_params->custom->orderby)){
				$query .= " ORDER BY ".$db->quoteName($query_params->custom->orderby)." ".$query_params->custom->orderdir;
			}
			
			try{
				$db->setQuery($query);
				$data = $db->loadObjectList();
			}
			catch(Exception $e){
				$obj->result = 'error';
				$obj->error = $e->getMessage();
				return $obj;
			}
		}
		else{
			if (preg_match("/^select (.*)/i", trim($query_params->query)) > 0){
				try{
					$db->setQuery($query_params->query);
					$data = $db->loadObjectList();
				}
				catch(RuntimeException $e){
					$obj->result = 'error';
					$obj->error = $e->getMessage();
					return $obj;
				}
			}
		}
		
		if($db->getErrorNum())	{
			$obj->result = 'error';
			$obj->error = $db->getErrorMsg();
			return $obj;
		}
		
		
		if(empty($data)){
			$obj->error = JText::_('NOTIFICATION_CONDITION_FAILED_NO_DATA_FOUND');
			$obj->result = 'error';
			return $obj;
		}

		$notification_tmpl = json_decode($notification->notification_tmpl);
		if(isset($notification_tmpl->tmpl) && !empty($notification_tmpl->tmpl)){
			$body = $notification_tmpl->tmpl;
			// $body = $this->filterTemplate($body, $data);
			return $this->sendNotifyMail($notification_tmpl->subject,$body, $notification_tmpl->recipient, $data);
		}
		else{
			$obj->error = JText::_('NOTIFICATION_TEMPLATE_NOT_FOUND');
			$obj->result = 'error';
			return $obj;
		}
		
		$obj->result = 'error';
		$obj->error = 'test';
		return $obj;
		
	}
	
	function getQueryFilteredValue($value){
		
		$val = $value;
		$hd_sql_pattern = '/^@vdSql:(.*?)$/';
		$hd_php_pattern = '/^@vdPhp:(.*?)$/';
		if(preg_match($hd_sql_pattern, $value, $sqlmatches)){
			$val = $sqlmatches[1];
		}
		elseif(preg_match($hd_php_pattern, $value, $phpmatches)){
			$func = $phpmatches[1];
			$val = (eval("return $func;"));
		}
		
		return $val;
	}
	
	function sendNotifyMail($subject, $body, $recipient, $data){
		$obj = new stdClass();
		$mailer = JFactory::getMailer();
		
		//sender
		$config = JFactory::getConfig();
		$sender = array( 
			$config->get( 'mailfrom' ),
			$config->get( 'fromname' ) 
		);
		$mailer->setSender($sender);

		//subject
		$mailer->setSubject($subject);
		
		
		$db = JFactory::getDbo();
		//recipient
		$mailRecipients = array();
		if(isset($recipient->group) && !empty($recipient->group)){
			jimport('joomla.access.access');
			jimport('joomla.user.user');
			$groupUsers = JAccess::getUsersByGroup($recipient->group);
			
			foreach($groupUsers as $groupUser){
				$groupUserEmail = JFactory::getUser($groupUser)->email;
				if(!in_array($groupUserEmail, $mailRecipients)){
					array_push($mailRecipients, $groupUserEmail);
				}
			}
			
		}
		if(isset($recipient->sendmail) && $recipient->sendmail==1){
			$query = $db->getQuery(true);
			$query = 'SELECT '.$db->quoteName('email').' from '.$db->quoteName('#__users').' where '.$db->quoteName('sendEmail').'=1';
			$db->setQuery($query);
			$recipients = $db->loadColumn();
			foreach($recipients as $recipientMail){
				if(!in_array($recipientMail, $mailRecipients)){
					array_push($mailRecipients, $recipientMail);
				}
			}
		}
		if(isset($recipient->custom) && !empty($recipient->custom)){
			$recipients = explode(',', $recipient->custom);
			foreach($recipients as $recipientMail){
				if(!in_array($recipientMail, $mailRecipients)){
					array_push($mailRecipients, $recipientMail);
				}
			}
		}
		
		
		$th = '';
		foreach($data as $i=>$row){
			$td .= '<tr>';
			foreach($row as $column=>$value){
				if($i==0){
					$th .= '<th>'.$column.'</th>';
				}
				$td .= '<td>'.$value.'</td>';
			}
			$td .= '</tr>';
		}
		$thead = '<tr>'.$th.'</tr>';
		
		$table = '<table>'.$thead.''.$td.'</table>';
		
		//body
		$tpl = $body.$table;
		
		$mailer->setBody($tpl);
		$mailer->isHTML();
		$mailer->Encoding = 'base64';
		$mailer->addRecipient($mailRecipients);
		$send = $mailer->Send();
		if ( $send !== true ) {
			JLog::add($send->__toString(), JLog::ERROR, 'com_vdata');
		}
		else {
			JLog::add(JText::_('NOTIFICATION_SENT_SUCCESSFULLY'), JLog::INFO, 'com_vdata');
		}
		
		if(isset($recipient->column->value) && !empty($recipient->column->value)){
			
			foreach($data as $i=>$row){
				if(property_exists($row, $recipient->column->value)){
					
					$html = '<table>';
					foreach($row as $column=>$value){
						$html .= '<tr>';
						$html .= '<td>'.$column.'</td>';
						$html .= '<td>'.$value.'</td>';
						$html .= '</tr>';
					}
					$html .= '</table>';
					
					$body .= $html; 
					$mailer->setBody($body);
					$mailer->isHTML();
					$mailer->Encoding = 'base64';
					
					if(is_numeric($row->{$recipient->column->value})){
						$userMail = JFactory::getUser($row->{$recipient->column->value})->email;
						if(!empty($userMail)){
							$mailer->addRecipient($userMail);
						}
					}
					else{
						$mailer->addRecipient($row->{$recipient->column->value});
					}
					$send = $mailer->Send();
					if ( $send !== true ) {
						JLog::add($send->__toString(), JLog::INFO, 'com_vdata');
					}
				}
			}
		}
		
		if(empty($mailRecipients) && !isset($recipient->column->value)){
			$obj->result = 'success';
			$obj->error = JText::_('NO_RECIPIENT_FOUND');
			return $obj;
		}

		$obj->result = 'success';
		$obj->error = JText::_('NOTIFICATION_SENT_SUCCESSFULLY');
		return $obj;
	}
	
	function getConfig($field='*'){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if($field=='*'){
			$query->select('*');
		}
		else{
			$query->select($db->quoteName($field));
		}
		$query->from($db->quoteName('#__vd_config'));
		$query->where("id=1");
		$db->setQuery($query);
		if($field=='*'){
			$result = $db->loadObject();
		}
		else{
			$result = $db->loadResult();
		}
		return $result;
	}
	
	
	function getstatistics(){
		
		if( empty($_SERVER['REMOTE_ADDR']) || empty($_SERVER['SERVER_ADDR']) || ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']) ){
			jexit('{"result":"error", "error":"'.JText::_('ACCESS_DENIED').'"}');
		}
		
		$notification = json_decode($this->getConfig('notification'));
		
		if($notification->interval===0){
			jexit('{"result":"success", "message":"'.JText::_('VDATA_STATISTIC_NOTIFICATION_DISABLED').'"}');
		}
		
		if(strpos($notification->tmpl, '{TOTAL_IMPORT}')!== false)
		{
			$totalImport = $this->getTotalImport($notification->interval);
			$notification->tmpl = str_replace('{TOTAL_IMPORT}', $totalImport, $notification->tmpl);
		}
		if(strpos($notification->tmpl, '{TOTAL_EXPORT}')!== false)
		{
			$totalExport = $this->getTotalExport($notification->interval);
			$notification->tmpl = str_replace('{TOTAL_EXPORT}', $totalExport, $notification->tmpl);
		}
		if(strpos($notification->tmpl, '{PROFILE_STATS}')!== false)
		{
			$profileStats = $this->getProfileStats($notification->interval);
			if(!empty($profileStats)){
			$notification->tmpl = str_replace('{PROFILE_STATS}', $profileStats, $notification->tmpl);
			}
		}
		if(strpos($notification->tmpl, '{FEED_HITS}')!== false)
		{
			$feedHits = $this->getFeedHits($notification->interval);
			if(!empty($feedHits)){
			$notification->tmpl = str_replace('{FEED_HITS}', $feedHits, $notification->tmpl);
			}
		}
		if(strpos($notification->tmpl, '{CRON_STATS}')!== false)
		{
			$cronStats = $this->getCronStats($notification->interval);
			if(!empty($cronStats)){
			$notification->tmpl = str_replace('{CRON_STATS}', $cronStats, $notification->tmpl);
			}
		}
		
		//sender 
		$config = JFactory::getConfig();
		$sender = array(
			$config->get( 'mailfrom' ),
			$config->get( 'fromname' ) 
		);
		//subject
		$subject = JText::_('VDATA_IMPORT_EXPORT_STATISTIC_NOTIFICATION_SUBJECT');
		//recipients
		$recipients = explode(',', $notification->recipients);
		
		$result = $this->sendEmail($sender, $subject, $recipients, $notification->tmpl);
		
		jexit(json_encode($result));
	}
	
	function getTotalImport($interval){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$intervalText =  ' 1 DAY ';
		if($interval=='day'){
			$intervalText = ' 1 DAY ';
		}
		elseif($interval=='week'){
			$intervalText = ' 1 WEEK';
		}
		elseif($interval=='month'){
			$intervalText = ' 1 MONTH';
		}
		$query->select('IFNULL(sum(iocount) ,0) as count')
			->from('#__vd_logs')
			->where('iotype=0')
			->where($db->quoteName('op_start')." between date_sub(now(),INTERVAL ".$intervalText.") and now()");
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	function getTotalExport($interval){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$intervalText =  ' 1 DAY ';
		if($interval=='day'){
			$intervalText = ' 1 DAY ';
		}
		elseif($interval=='week'){
			$intervalText = ' 1 WEEK';
		}
		elseif($interval=='month'){
			$intervalText = ' 1 MONTH';
		}
		$query->select('IFNULL(sum(iocount), 0) as count')
			->from('#__vd_logs')
			->where('iotype=1')
			->where($db->quoteName('op_start')." between date_sub(now(),INTERVAL ".$intervalText.") and now()");
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	function getProfileStats($interval){
		
		$text = '';	
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$intervalText =  ' 1 DAY ';
		if($interval=='day'){
			$intervalText = ' 1 DAY ';
		}
		elseif($interval=='week'){
			$intervalText = ' 1 WEEK';
		}
		elseif($interval=='month'){
			$intervalText = ' 1 MONTH';
		}
		$query->select('p.id as ID, p.title as Title,IF(l.iotype=0, "IMPORT","EXPORT") as Type, sum(l.iocount) as Count')
			->from('#__vd_logs as l')
			->join('inner', '#__vd_profiles as p on l.profileid=p.id')
			->where($db->quoteName('l.op_start')." between date_sub(now(),INTERVAL ".$intervalText.") and now()")
			->group('l.profileid');
			
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$text .= '<table>';
		foreach($result as $i=>$row){
			//heading
			if($i==0){
				$text .= '<tr>';
				foreach($row as $field=>$data){
					$text .= '<th>'.$field.'</th>';
				}
				$text .= '</tr>';
			}
			$text .= '<tr>';
			foreach($row as $field=>$data){
				$text .= '<td>'.$data.'</td>';
			}
			$text .= '</tr>';
		}
		$text .= '</table>';
		return $text;
		
	}
	
	function getFeedHits($interval){
		
		$text = '';
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id as ID,title as Title,IF(iotype=0, "IMPORT","EXPORT") as Type, hits as Hits')
			->from('#__vd_schedules')
			->where('type=2');
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$text .= '<table>';
		foreach($result as $i=>$row){
			//heading
			if($i==0){
				$text .= '<tr>';
				foreach($row as $field=>$data){
					$text .= '<th>'.$field.'</th>';
				}
				$text .= '</tr>';
			}
			$text .= '<tr>';
			foreach($row as $field=>$data){
				$text .= '<td>'.$data.'</td>';
			}
			$text .= '</tr>';
		}
		$text .= '</table>';
		return $text;
		
	}
	
	function getCronStats($interval){
		
		$text = '';
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$intervalText =  ' 1 DAY ';
		if($interval=='day'){
			$intervalText = ' 1 DAY ';
		}
		elseif($interval=='week'){
			$intervalText = ' 1 WEEK';
		}
		elseif($interval=='month'){
			$intervalText = ' 1 MONTH';
		}
		//s.hits as Hits
		$query->select('s.id as ID,s.title as Title, sum(l.iocount) as Total')
			->from('#__vd_logs as l')
			->join('left', '#__vd_schedules as s on l.cronid=s.id')
			->where('l.side="cron"')
			->where($db->quoteName('l.op_start')." between date_sub(now(),INTERVAL ".$intervalText.") and now()")
			->group('l.cronid');
			
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		$text .= '<table>';
		foreach($result as $i=>$row){
			//heading
			if($i==0){
				$text .= '<tr>';
				foreach($row as $field=>$data){
					$text .= '<th>'.$field.'</th>';
				}
				$text .= '</tr>';
			}
			$text .= '<tr>';
			foreach($row as $field=>$data){
				$text .= '<td>'.$data.'</td>';
			}
			$text .= '</tr>';
		}
		$text .= '</table>';
		return $text;
	
	}
	
	
	function sendEmail($sender, $subject, $recipient, $body){
		
		$result = new stdClass();
		
		$mailer = JFactory::getMailer();
		
		//set sender
		$mailer->setSender($sender);
		
		//set subject
		$mailer->setSubject($subject);
		
		//set recipient
		$mailer->addRecipient($recipient);
		
		//set body
		$mailer->setBody($body);
		$mailer->isHTML();
		$mailer->Encoding = 'base64';
		
		$send = $mailer->Send();
		if ( $send !== true ) {
			$result->result = 'error';
			$result->error = $send->__toString();
			// JLog::add($send->__toString(), JLog::ERROR, 'com_vdata');
		}
		else {
			$result->result = 'success';
			$notificationRecipients = is_array($recipient)?implode(',', $recipient):$recipient;
			$result->message = JText::sprintf('VDATA_IMPORT_EXPORT_STATISTIC_NOTIFICATION_MAIL_SUCCESS', $notificationRecipients);
		}
		return $result;
	}
	
	
}