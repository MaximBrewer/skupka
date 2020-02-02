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


class TableSchedules extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    var $id = null;
 
    /**
     * @var string
     */
    
    var $title = null;
    var $url = null;
    var $params = null;
	var $qry = null;
	var $iotype = null;
	var $profile = null;
    var $access = null;
	var $uid = null;
	
    function TableSchedules( &$db ) {
        parent::__construct('#__vd_schedules', 'id', $db);
    }
	
	function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);	
	}
	
	function check()
	{
		$this->id = intval($this->id);
		$this->params = json_encode($this->params);
		if(empty($this->title))	{
			$this->setError( JText::_('PLZ_ENTER_TITLE') );
			return false;
		}
		$source = JFactory::getApplication()->input->get('source','', 'STRING');
		$server = JFactory::getApplication()->input->get('server', '', 'STRING');
		$path = JFactory::getApplication()->input->get('path', '', 'RAW');
		if($source!='remote' && empty($path)){
			$this->setError( JText::_('SELECT_FORMAT') );
			return false;
		}
		try{
			$query = JFactory::getApplication()->input->get('qry','', 'RAW');
			if(!empty($query)){
				$this->_db->setQuery($query);
				$this->_db->loadObjectList();
			}
		}
		catch(RuntimeException $e){
			$this->setError($e->getMessage());
			return false;
		}
		
		if($this->_db->getErrorNum())	{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return parent::check();
		
	}
	
	function store($updateNulls = false)
	{	
		$source = JFactory::getApplication()->input->get('source','', 'STRING');
		$server = JFactory::getApplication()->input->get('server', '', 'STRING');
		$path = JFactory::getApplication()->input->get('path', '', 'RAW');
		
		// $access = JFactory::getApplication()->input->get('access', '');
		// $this->access = $access;
		
		$obj = new stdClass();
		$obj->source = $source;
		if($source<>'remote'){
			$obj->server = $server;
			$obj->path = $path;
		}
		else{
			$driver = JFactory::getApplication()->input->get('driver', '', 'RAW');
			$host = JFactory::getApplication()->input->get('host', '', 'RAW');
			$user = JFactory::getApplication()->input->get('user', '', 'RAW');
			$password = JFactory::getApplication()->input->get('password', '', 'RAW');
			$database = JFactory::getApplication()->input->get('database', '', 'RAW');
			$dbprefix = JFactory::getApplication()->input->get('dbprefix', '', 'RAW');
			
			$obj->driver = $driver;
			$obj->host = $host;
			$obj->user = $user;
			$obj->password = $password;
			$obj->database = $database;
			$obj->dbprefix = $dbprefix;
		}
		$this->params = json_encode($obj);
		
		if(!parent::store($updateNulls))	{
			return false;
		}
		return true;
	}
	
	function delete($oid=null)
	{
		$this->id = $oid;
		
		$schedule = $this->getSchedule();
		if(empty($schedule)){
			$this->setError(JText::_('SCHEDULE_NOT_FOUND'));
			return false;
		}
		
		if(!parent::delete($oid))	{
			return false;
		}
		return true;
	}
	
	function getSchedule(){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__vd_schedules');
		$query->where('id='.(int)$this->id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	
}
