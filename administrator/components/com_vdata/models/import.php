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
jimport('joomla.application.component.modellist');
class VdataModelImport extends VDModel
{
    var $_total = null;
	var $_pagination = null;
	function __construct()
	{
		parent::__construct();
	}
	function getProfile()
	{
		$id = JFactory::getApplication()->input->getInt('profileid', 0);
		$query = 'select i.*, e.element as plugin from #__vd_profiles as i join #__extensions as e on (i.pluginid=e.extension_id and e.enabled=1) where i.id = '.$id;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		return $item;
	}
	function getProfiles()
	{
		$user = JFactory::getUser();
		$query = 'select * from #__vd_profiles where iotype=0 ';
		$query .= 'order by title asc ';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		return $items;
	}	
    function getItem()
    {
        $session = JFactory::getSession();
        if($session->has('importitem')) {
            $item = $session->get('importitem');
        }
        else {
            $item = new stdClass();
            $item->profileid = null;
            $item->source = null;
            $item->server = null;
            $item->path = null;
            $item->driver = null;
            $item->host = null;
            $item->user = null;
            $item->password = null;
            $item->database = null;
			$item->dbprefix = null;
        }
		return $item;
    }
	
	//store schedule task
	function store()
	{
		$session = JFactory::getSession();
		$importitem = $session->get('importitem');
		$isNew = empty($importitem->id) ? true : false;
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		
		$obj = new stdClass();
		$obj->title = $importitem->title;
		$obj->profileid = $importitem->profileid;
		$obj->type = $importitem->type;
		$obj->iotype = $importitem->iotype;
		$obj->access = $importitem->access;
		$obj->state = $importitem->state;
		
		$task = JFactory::getApplication()->input->get('task', 'save_st');
		if($task=='save_st2copy'){
			$importitem->id = 0;
			$str = $this->getUniqueID(10);
		}
		else{
			if(!empty($importitem->uid)){
				// $str = JApplication::stringURLSafe($importitem->uid);
				$str = $importitem->uid;
			}
			else{
				// $str = JApplication::stringURLSafe($importitem->title);
				$str = $this->getUniqueID(10);
			}
		}
		// $str = str_replace( array( '\'', '"', ',' , ';', '<', '>', '-' ), '', $str);
		$query = 'select count(*) as uc from '.$this->_db->quoteName('#__vd_schedules').' where uid = '.$this->_db->quote($str);
		if(!$isNew){
			$query .= ' and id <> '.$this->_db->quote($importitem->id);
		}
		$this->_db->setQuery($query);
		if($this->_db->loadResult()){
			$currentTime = $date->toUnix();
			$str .= $currentTime;
			JFactory::getApplication()->enqueueMessage(JText::_('UNIQUE_ID_ALREADY_EXISTS'), 'Notice');
		}
		$obj->uid = $str;
		
		
		$obj->url = JURI::root().'index.php?option=com_vdata&task=set_import&type='.$obj->uid;
		if($importitem->id){
			$obj->modified =  $date->toSql();//$date->toMySQL();
			$obj->modified_by = $user->id;
		}
		else{
			$obj->created = $date->toSql();//$date->toMySQL();
			$obj->created_by = $user->get('id');
		}
		
		$params = new stdClass();
		$params->source = $importitem->source;
		if($importitem->source<>'remote') {
			$params->server = $importitem->server;
			$params->path = $importitem->path;
		}
		else {
			if(property_exists($importitem, 'db_loc') && ($importitem->db_loc=='localdb'))
				$params->local_db = 1;
			else
				$params->local_db = 0;
			
			$params->driver = $importitem->driver;
			$params->host = $importitem->host;
			$params->user = $importitem->user;
			$params->password = $importitem->password;
			$params->database = $importitem->database;
			$params->dbprefix = $importitem->dbprefix;
		}
		//save ftp details
		if(in_array($importitem->source, array('csv', 'xml', 'json'))){
			$params->ftp = $importitem->ftp;
		}
		$obj->params = json_encode($params);
		
		$profile = $this->getProfile();
		$col = new stdClass();
		if($profile->quick){
			$quick = JFactory::getApplication()->input->getInt('quick',0);
			$col->quick = $quick;
		}
		
		
		switch($importitem->source){
			case 'csv':
				$fields = JFactory::getApplication()->input->get('csvfield', array(), 'ARRAY');
				$images = JFactory::getApplication()->input->get('images', array(), 'ARRAY');
				$col->images = $images;
				$col->fields = $fields;
			break;
			case 'xml':
				$fields = JFactory::getApplication()->input->get('xmlfield', array(), 'ARRAY');
				$base =  JFactory::getApplication()->input->get('base', '', 'RAW');
				$root = JFactory::getApplication()->input->get('root', array(), 'ARRAY');
				$images = JFactory::getApplication()->input->get('images', array(), 'ARRAY');
				$col->images = $images;
				$col->fields = $fields;
				$col->base = $base;
				$col->child_tags = $root;
			break;
			case 'json':
				$base =  JFactory::getApplication()->input->get('base', '', 'RAW');
				$root = JFactory::getApplication()->input->get('root', array(), 'ARRAY');
				$fields = JFactory::getApplication()->input->get('jsonfield', array(), 'ARRAY');
				$images = JFactory::getApplication()->input->get('images', array(), 'ARRAY');
				$col->images = $images;
				$col->fields = $fields;
				$col->base = $base;
				$col->root = $root;
			break;
			case 'remote':
				$fields = JFactory::getApplication()->input->get('fields', array(), 'ARRAY');
				$col->fields = $fields;
				$remote_table = JFactory::getApplication()->input->get('table', '', 'RAW');
				$col->table = $remote_table;
				$images = JFactory::getApplication()->input->get('images', array(), 'ARRAY');
				$col->images = $images;
				if($profile->quick != 1){
					$join_table = JFactory::getApplication()->input->get('join_table', '', 'RAW');
					$col->join_table = $join_table;
				}
			break;
			default:
				if($obj->type==2 && $obj->iotype==0){
					$fields = JFactory::getApplication()->input->get('field', array(), 'ARRAY');
					$col->fields = $fields;
					$images = JFactory::getApplication()->input->get('images', array(), 'ARRAY');
					$col->images = $images;
				}
			break;
		}
		$obj->columns = json_encode($col);
		
		// echo "<pre>";print_r($obj);jexit('final');
		
		if($task=='save_st2copy'){
			$obj->title = JText::_('COPY_OF').' '.$importitem->title;
			if(!$this->_db->insertObject('#__vd_schedules', $obj)){
				$this->setError($this->_db->stderr());
				return false;
			}
		}
		else{
			if($isNew){
				//insert object
				if(!$this->_db->insertObject('#__vd_schedules', $obj)){
					$this->setError($this->_db->stderr());
					return false;
				}
			}
			else{
				//update object
				$obj->id = $importitem->id;
				if(!$this->_db->updateObject('#__vd_schedules', $obj, 'id')){
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		return true;
	}
	
	function getUniqueID($length=10)
	{
		$str = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ23456789';
		$rn_str = '';
		for($i=0;$i<$length;$i++){
			$rn_str .= $str[rand(0, strlen($str)-1)];
		}
		return $rn_str;
	}
	
	function setLog($log)
	{
		return $this->_db->insertObject('#__vd_logs', $log);
	}
}
