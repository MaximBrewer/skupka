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
	
	public function display($cachable = false, $urlparams = array())
	{
		
		if(!$this->sstatus())
		{  
			JRequest::setVar('view', 'vdata');
			JRequest::setVar('layout', 'information');
		}
		else
		{
		$this->showToolbar();
		$this->getConnectExternalDatabase();
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
	function showToolbar()
	{
		
		$view = JFactory::getApplication()->input->get('view', 'vdata');

		$version = new JVersion;
		$joomla = $version->getShortVersion();
		$jversion = substr($joomla,0,3);
		

		$user = JFactory::getUser();
		$canViewDashboard = $user->authorise('core.access.dashboard', 'com_vdata');
		$canViewConfig = $user->authorise('core.access.config', 'com_vdata');
		$canViewProfiles = $user->authorise('core.access.profiles', 'com_vdata');
		$canViewImport = $user->authorise('core.access.import', 'com_vdata');
		$canViewExport = $user->authorise('core.access.export', 'com_vdata');
		$canViewCronFeed = $user->authorise('core.access.cron', 'com_vdata');
		$canViewLog = $user->authorise('core.access.log', 'com_vdata');
		$canViewQuick = $user->authorise('core.access.quick', 'com_vdata');
		// $canViewNotification = $user->authorise('core.access.quick', 'com_vdata');
		$canViewDisplay = $user->authorise('core.access.display', 'com_vdata');
		
		if($jversion>=3.0){
			
			if($canViewDashboard){
				JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('DASHBOARD_DESC').'">'.JText::_('DASHBOARD').'</span>',
					'index.php?option=com_vdata&view=vdata',
					$view == 'vdata');
			}
			
			if($canViewConfig){
				JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('CONFIG_DESC').'">'.JText::_('CONFIG').'</span>',
					'index.php?option=com_vdata&view=config',
					$view == 'config');
			}
			
			if($canViewProfiles){
				JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('PROFILES_DESC').'">'.JText::_('PROFILES').'</span>',
					'index.php?option=com_vdata&view=profiles',
					$view == 'profiles');
			}
			
			if($canViewImport){
				JHtmlSidebar::addEntry(
						'<span class="add_item hasTip" title="'.JText::_('IMPORT_DESC').'">'.JText::_('IMPORT').'</span>',
						'index.php?option=com_vdata&view=import',
						$view == 'import');
			}
			
			if($canViewExport){
				JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('EXPORT_DESC').'">'.JText::_('EXPORT').'</span>',
					'index.php?option=com_vdata&view=export',
					$view == 'export');
			}
			
			if($canViewCronFeed){
				JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('CRON_FEED_DESC').'">'.JText::_('CRON_FEED').'</span>',
					'index.php?option=com_vdata&view=schedules',
					$view == 'schedules');
			}
			
			if($canViewQuick){
				JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('VDATA_PHP_MY_ADMIN_DESC').'">'.JText::_('VDATA_PHP_MY_ADMIN').'</span>',
					'index.php?option=com_vdata&view=quick',
					$view == 'quick');
			}
			
			// if($canViewNotification){
				JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('VDATA_NOTIFICATION_DESC').'">'.JText::_('VDATA_NOTIFICATION').'</span>',
					'index.php?option=com_vdata&view=notifications',
					$view == 'notifications');
			// }
			if($canViewDisplay){
				JHtmlSidebar::addEntry('<span class="add_item hasTip" title="'.JText::_('VDATA_DISPLAY_DESC').'">'.JText::_('VDATA_DISPLAY').'</span>', 'index.php?option=com_vdata&view=display', $view=='display');
			}
			$logStatus = $this->getVdatConfig('logging');
			if($canViewLog && $logStatus){
				JHtmlSidebar::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('COM_VDATA_LOGS_DESC').'">'.JText::_('COM_VDATA_LOGS').'</span>',
					'index.php?option=com_vdata&view=logs',
					$view == 'logs');
			}
			
		}
		else{
			
			if($canViewDashboard){
				JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('DASHBOARD_DESC').'">'.JText::_('DASHBOARD').'</span>' , 'index.php?option=com_vdata&view=vdata', $view == 'vdata' );
			}
			
			if($canViewConfig){
				JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('CONFIG_DESC').'">'.JText::_('CONFIG').'</span>' , 'index.php?option=com_vdata&view=config', $view == 'config' );
			}
		
			if($canViewProfiles){
				JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('PROFILES_DESC').'">'.JText::_('PROFILES').'</span>' , 'index.php?option=com_vdata&view=profiles', $view == 'profiles' );
			}
		
			if($canViewImport){
				JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('IMPORT_DESC').'">'.JText::_('IMPORT').'</span>' , 'index.php?option=com_vdata&view=import', $view == 'import' );
			}
		
			if($canViewExport){
				JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('EXPORT_DESC').'">'.JText::_('EXPORT').'</span>' , 'index.php?option=com_vdata&view=export', $view == 'export' );
			}
		
			if($canViewCronFeed){
				JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('CRON_FEED_DESC').'">'.JText::_('CRON_FEED').'</span>' , 'index.php?option=com_vdata&view=schedules', $view == 'schedules' );
			}
		
			if($canViewQuick){
				JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('VDATA_PHP_MY_ADMIN_DESC').'">'.JText::_('VDATA_PHP_MY_ADMIN').'</span>' , 'index.php?option=com_vdata&view=quick', $view == 'quick' );
			}
			
			// if($canViewNotification){
				JSubMenuHelper::addEntry(
					'<span class="add_item hasTip" title="'.JText::_('VDATA_NOTIFICATION_DESC').'">'.JText::_('VDATA_NOTIFICATION').'</span>',
					'index.php?option=com_vdata&view=notifications',
					$view == 'notifications');
			// }
			if($canViewDisplay){
				JSubMenuHelper:addEntry('<span class="add_item hasTip" title="'.JText::_('VDATA_DISPLAY_DESC').'">'.JText::_('VDATA_DISPLAY').'</span>', 'index.php?option=com_vdata&view=display', $view=='display');
			}
			$logStatus = $this->getVdatConfig('logging');
			if($canViewLog && $logStatus){
				JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('COM_VDATA_LOGS_DESC').'">'.JText::_('COM_VDATA_LOGS').'</span>' , 'index.php?option=com_vdata&view=logs', $view == 'logs' ); 
			}
			
			//JSubMenuHelper::addEntry( '<span class="add_item hasTip" title="'.JText::_('VDATA_WIDGET_DESC').'">'.JText::_('VDATA_WIDGET').'</span>' , 'index.php?option=com_vdata&view=widget', $view == 'widget' );
			
		
		}
		
	}
	
	function getConnectExternalDatabase(){
         $obj = new stdClass();
		 $db = JFactory::getDbo();
         $obj->result = 'success';
         $driver_object = json_decode($this->getVdatConfig('dbconfig'));
           if(isset($driver_object->local_db) && $driver_object->local_db==0){
            $option = array();
                try
                {
                    $option['driver']   = $driver_object->driver;
                    $option['host']     = $driver_object->host;
                    $option['user']     = $driver_object->user;
                    $option['password'] = $driver_object->password;
                    $option['database'] = $driver_object->database;
                    $option['prefix']   = $driver_object->dbprefix;
                    $db = JDatabaseDriver::getInstance( $option );
                    $db->connect();
					return true;
                }
                catch (RuntimeException $e)
                {
					$mainframe = JFactory::getApplication();
					$view = JFactory::getApplication()->input->get('view', 'vdata');
					if($view!='config')
						$mainframe->redirect('index.php?option=com_vdata&view=config', $e->getMessage());
           
                }
				
           }
    }

    function getVdatConfig($field)
    {
		$db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName($field));
		$query->from($db->quoteName('#__vd_config'));
		$query->where('id=1');
        $db->setQuery($query);
        return $db->loadResult();
		
    }
  
}