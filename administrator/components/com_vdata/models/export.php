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

class VdataModelExport extends VDModel
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
		$query = 'select * from #__vd_profiles where iotype=1 ';
		$query .= ' order by title asc';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		return $items;
	}
    function getItem()
    {
        $session = JFactory::getSession();
        if($session->has('exportitem')) {
            $item = $session->get('exportitem');
        }
        else {
            $item = new stdClass();
            $item->profileid = null;
            $item->source = null;
            $item->server = null;
			$item->mode = 'w';
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
	
	//store export schedule task
	function store()	
	{
		$session = JFactory::getSession();
		$exportitem = $session->get('exportitem');
		
		$isNew = empty($exportitem->id) ? true : false ;
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		
		$obj = new stdClass();
		$obj->title = $exportitem->title;
		$obj->profileid = $exportitem->profileid;
		$obj->type = $exportitem->type;
		$obj->iotype = $exportitem->iotype;
		$obj->qry = $exportitem->qry;
		$obj->access = $exportitem->access;
		$obj->state = $exportitem->state;
		
		$task = JFactory::getApplication()->input->get('task', 'save_st');
		if($task=='save_st2copy'){
			$exportitem->id = 0;
			$str = $this->getUniqueID(10);
		}
		else{
			if(!empty($exportitem->uid)){
				// $str = JApplication::stringURLSafe($exportitem->uid);
				$str = $exportitem->uid;
			}
			else{
				// $str = JApplication::stringURLSafe($exportitem->title);
				$str = $this->getUniqueID(10);
			}
		}
		/* $str = str_replace( array( '\'', '"', ',' , ';', '<', '>', '-' ), '', $str); */
		$query = 'select count(*) as uc from '.$this->_db->quoteName('#__vd_schedules').' where uid = '.$this->_db->quote($str);		
		if(!$isNew){
			$query .= ' and id <> '.$this->_db->quote($exportitem->id);		
		}
		$this->_db->setQuery($query);
		if($this->_db->loadResult()){
			$currentTime = $date->toUnix();
			$str .= $currentTime;
			JFactory::getApplication()->enqueueMessage(JText::_('UNIQUE_ID_ALREADY_EXISTS'), 'Notice');
		}
		$obj->uid = $str;
		
		if($obj->type==1)
			$obj->url = JURI::root().'index.php?option=com_vdata&task=set_export&type='.$obj->uid;
		elseif($obj->type==2)
			$obj->url = JURI::root().'index.php?option=com_vdata&task=get_feeds&type='.$obj->uid;
			
		if($exportitem->id){
			$obj->modified =  $date->toSql();
			$obj->modified_by = $user->id;
		}
		else{
			$obj->created = $date->toSql();
			$obj->created_by = $user->get('id');
		}
		
		$params = new stdClass();
		$params->source = $exportitem->source;
		if($exportitem->source=='RSS2'){
			$params->rss2_title = $exportitem->rss2_title;
			$params->rss2_link = $exportitem->rss2_link;
			$params->rss2_desc = $exportitem->rss2_desc;
		}
		elseif($exportitem->source=='RSS1'){
			$params->rss1_title = $exportitem->rss1_title;
			$params->rss1_link = $exportitem->rss1_link;
			$params->rss1_desc = $exportitem->rss1_desc;
		}
		elseif($exportitem->source=='ATOM'){
			$params->atom_title = $exportitem->atom_title;
			$params->atom_category = $exportitem->atom_category;
			$params->atom_author_name = $exportitem->atom_author_name;
			$params->atom_author_email = $exportitem->atom_author_email;
		}
		elseif($exportitem->source=='SITEMAP'){
			
		}
		elseif($exportitem->source<>'remote') {
			$params->server = $exportitem->server;
			$params->path = $exportitem->path;
			$params->mode = $exportitem->mode;
		}
		else {
			if(property_exists($exportitem, 'db_loc') && ($exportitem->db_loc=='localdb'))
				$params->local_db = 1;
			else
				$params->local_db = 0;
			
			$params->driver = $exportitem->driver;
			$params->host = $exportitem->host;
			$params->user = $exportitem->user;
			$params->password = $exportitem->password;
			$params->database = $exportitem->database;
			$params->dbprefix = $exportitem->dbprefix;
			if( ($exportitem->iotype==1) )
				$params->operation = $exportitem->operation;
		}
		//save ftp details
		if(in_array($exportitem->source, array('csv', 'xml', 'json'))){
			$params->ftp = $exportitem->ftp;
		}
		//save notification details
		if(isset($exportitem->sendfile['status']) && ($exportitem->sendfile['status']==1) ){
			$params->sendfile = $exportitem->sendfile;
		}
		$obj->params = json_encode($params);
		$profile = $this->getProfile();
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
			$fields = JFactory::getApplication()->input->get('fields', array(), 'ARRAY');
			$col->fields = $fields;
			$remote_table = JFactory::getApplication()->input->get('table', '', 'RAW');
			$col->table = $remote_table;
			$unkey = JFactory::getApplication()->input->get('unkey','');
			$col->unkey = $unkey;
			if($profile->quick != 1){
				$join_table = JFactory::getApplication()->input->get('join_table', '', 'RAW');
				$col->join_table = $join_table;
			}
		}
		$obj->columns = json_encode($col);
	// echo "<pre>";print_r($obj);jexit('final');
		if($task=='save_st2copy'){
			$obj->title = JText::_('COPY_OF').' '.$exportitem->title;
			if(!$this->_db->insertObject('#__vd_schedules', $obj)){
				$this->setError($this->_db->stderr());
				return false;
			}
		}
		else{
			if($isNew){
				/* insert object */
				if(!$this->_db->insertObject('#__vd_schedules', $obj)){
					$this->setError($this->_db->stderr());
					return false;
				}
			}
			else{
				/* update object */
				$obj->id = $exportitem->id;
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
