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
/* No direct access */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

class VdataModelSchedules extends VDModel
{
	var $_data = null;
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
        $mainframe = JFactory::getApplication();
		$context			= 'com_vdata.schedules.list.'; 
        /* Get pagination request variables */
        $limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        /* In case limit has been changed, adjust it */
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
		$array = JFactory::getApplication()->input->get('cid',  0, 'ARRAY');
		$this->setId((int)$array[0]);
	}
	
	function _buildQuery()
	{
		$app = JFactory::getApplication();
		$context = 'com_vdata.schedules.list.';
		$db = JFactory::getDbo();
		
		$keyword = $app->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$filter_type = $app->getUserStateFromRequest( $context.'filter_type', 'filter_type', -1, 'int' );
		
		$query = 'select i.*, IFNULL(p.title,i.qry) as profile from #__vd_schedules as i left join #__vd_profiles as p on i.profileid=p.id where 1=1';
		// $query = 'select i.* from #__vd_schedules as i where 1=1 ';
		$user = JFactory::getUser();
		/* if (!$user->authorise('core.admin')){
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query .= ' and i.access IN (' . $groups . ')';
		} */
		
		if( !$user->authorise('core.admin') && $user->authorise('core.edit.own', 'com_vdata') ){
			$query .= ' and i.created_by = '.(int) $user->id;
		}
		
		if(!empty($keyword))
			$query .= ' and i.title like '.$db->quote('%'.$keyword.'%');
		
		if($filter_type!=-1)
			$query .= ' and i.iotype='.$db->quote($filter_type);
		
		return $query;
	}
	
	function setId($id)
	{
		/* Set id and wipe data */
		$this->_id		= $id;
	}
	
	function getItem()
    {
		$query = 'select i.* from #__vd_schedules as i where i.id = '.(int)$this->_id;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		if(empty($item)) {
			$item = new stdClass();
			$item->id = null;
			$item->title = null;
			$item->profile = null;
			$item->query = null;
			$item->type=1;
			$item->iotype = -1;
			$item->access = null;
			$item->uid = null;
			$item->params = null;
			$item->profiles = null;
			$item->state = 0;
		}
		else{
			$user = JFactory::getUser();
			$query = 'select * from #__vd_profiles where 1=1';
			if($item->iotype==0)
				$query .= ' and iotype = 0';
			else
				$query .= ' and iotype = 1';
			$this->_db->setQuery( $query );
			$item->profiles = $this->_db->loadObjectList();
		}
		$item->params = json_decode($item->params);
		return $item;
    }
	
	function getItems()
    {
        if(empty($this->_data))	{
			$query = $this->_buildQuery();
			$orderby = $this->_buildItemOrderBy();
			$query .= $orderby;
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		echo $this->_db->getErrorMsg();
        return $this->_data;
    }
	
	function getTotal()
  	{
        /* Load the content if it doesn't already exist */
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);    
        }
        return $this->_total;
  	}
	
	function getPagination()
  	{
        /* Load the content if it doesn't already exist */
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
  	}
	
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		$context			= 'com_vdata.schedules.list.';
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
        $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
        return $orderby;
	}
	
	function store()
	{
		/* Check for request forgeries */
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
    	$post = JFactory::getApplication()->input->post->getArray();
		
		$row = $this->getTable();
		// JTable::addIncludePath(JPATH_ROOT.DS.'components'.DS.'com_vdata'.DS.'tables');
		// $row = JTable::getInstance('schedules', 'Table');
		
		$row->load($post['id']);
		if (!$row->bind( $post ))	{
        	$this->setError($row->getError());
			return false;
		}
		if (!$row->check())	{
        	$this->setError($row->getError());
			return false;
		}
		if (!$row->store())	{
        	$this->setError($row->getError());
			return false;
		}
		if(!$post['id'])	{
			$post['id'] = $row->id;
			JFactory::getApplication()->input->set('id', $post['id']);
		}
		return true;
	}
	
	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete()
	{
		/* Check for request forgeries */
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		$cids = JFactory::getApplication()->input->get( 'cid', array(), 'ARRAY' );
		if (count( $cids ) < 1) {
			$this->setError( JText::_( 'SELECT_MIN', true ) );
			return false;
		}
		
		$row =  $this->getTable();
		// JTable::addIncludePath(JPATH_ROOT.DS.'components'.DS.'com_vdata'.DS.'tables');
		// $row = JTable::getInstance('schedules', 'Table');
		
		foreach ($cids as $id)
		{
			if(!$row->delete($id))	{
				$this->setError($row->getError());
				return false;	
			}
		}
		return true;	
	}
	
	function getProfiles()
	{
		$user = JFactory::getUser();
		$iotype = JFactory::getApplication()->input->getInt('iotype',-1);
		$query = 'select * from #__vd_profiles where iotype='.(int)$iotype;
		$query .= ' order by title asc';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	function publishList($cid = array(), $publish = 1) 
	{
		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			$query = 'UPDATE #__vd_schedules'
				   . ' SET state = '.(int) $publish
				   . ' WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
				if (!$this->_db->query()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
		}
		return true;
	}
	
	function getViewLevels(){
		$query = $this->_db->getQuery(true);
		$query->select('id, title, ordering');
		$query->from('#__viewlevels');
		$query->order('ordering ASC');
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	function save_st(){
		
		$ioparams = (object)JFactory::getApplication()->input->post->getArray();
		$session = JFactory::getSession();
		$columns = $session->get('columns');
		$date = JFactory::getDate();
        $user = JFactory::getUser();
		
		$obj = new stdClass();
		$obj->title = $ioparams->title;
		$obj->profileid = $ioparams->profileid;
		$obj->iotype = $ioparams->iotype;
		$obj->access = $ioparams->access;
		$obj->state = $ioparams->state;
		$obj->iotype = $ioparams->iotype;
		
		$isNew = empty($ioparams->id) ? true : false;
		
		if($ioparams->iotype==1){
			
			$obj->qry = $ioparams->qry;
		}
		else{
			
		}
		
		$str = $this->getUniqueID(10);

		$query = 'select count(*) as uc from '.$this->_db->quoteName('#__vd_schedules').' where uid = '.$this->_db->quote($str);
		if(!$isNew){
			$query .= ' and id <> '.$this->_db->quote($ioparams->id);
		}
		$this->_db->setQuery($query);
		if($this->_db->loadResult()){
			// $this->setError(JText::_('UNIQUE_ID_ALREADY_EXISTS'));
			// return false;
			$currentTime = $date->toUnix();
			$str .= $currentTime;
			JFactory::getApplication()->enqueueMessage(JText::_('UNIQUE_ID_ALREADY_EXISTS'), 'Notice');
		}
		$obj->uid = $str;
		
		/* if($obj->iotype==1)
			$obj->url = JURI::root().'index.php?option=com_vdata&task=set_export&type='.$obj->uid;
		elseif($obj->iotype==2)
			$obj->url = JURI::root().'index.php?option=com_vdata&task=get_feeds&type='.$obj->uid; */
		
		if($ioparams->id){
			$obj->modified =  $date->toSql();//$date->toMySQL();
			$obj->modified_by = $user->id;
		}
		else{
			$obj->created = $date->toSql();//$date->toMySQL();
			$obj->created_by = $user->get('id');
		}
		
		$params = new stdClass();
		$params->source = $ioparams->source;
		
		if($ioparams->source<>'remote') {
			$params->server = $ioparams->server;
			$params->path = $ioparams->path;
			if($ioparams->iotype==1)
				$params->mode = $ioparams->mode;
		}
		else {
			
			if(property_exists($ioparams, 'db_loc') && ($ioparams->db_loc=='localdb'))
				$params->local_db = 1;
			else
				$params->local_db = 0;

			$params->driver = $ioparams->driver;
			$params->host = $ioparams->host;
			$params->user = $ioparams->user;
			$params->password = $ioparams->password;
			$params->database = $ioparams->database;
			$params->dbprefix = $ioparams->dbprefix;
			if( ($ioparams->iotype==1) )
				$params->operation = $ioparams->operation;
		}
		$obj->params = json_encode($params);
		$obj->columns = $columns;
		
		// var_dump($ioparams);
		// var_dump($columns);
		// var_dump($obj);
		
		// jexit();
		
		if($isNew){
			//insert object
			if(!$this->_db->insertObject('#__vd_schedules', $obj)){
				$this->setError($this->_db->stderr());
				return false;
			}
		}
		else{
			//update object
			$obj->id = $exportitem->id;
			if(!$this->_db->updateObject('#__vd_schedules', $obj, 'id')){
				$this->setError($this->_db->stderr());
				return false;
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
	
	function copyList($cid){
		
		if(count($cid)){
			$query = $this->_db->getQuery(true);
			foreach($cid as $id){
				
				$query->select('*')
					->from('#__vd_schedules')
					->where('id='.$this->_db->quote($id));
				$this->_db->setQuery($query);
				$schedule = $this->_db->loadObject();
				$query->clear();
				
				$query->select('count(*)')
					->from('#__vd_schedules')
					->where('title = '.$this->_db->quote($schedule->title));
				$this->_db->setQuery($query);
				$scount = $this->_db->loadResult();
				$query->clear();
				
				//copy profile
				unset($schedule->id);
				//change title,created_by,uid
				$schedule->uid = $this->getUID(true);
				
				if(!$this->_db->insertObject('#__vd_schedules', $schedule)){
					$this->setError($this->_db->stderr());
					return false;
				}
			}
		}
		else{
			$this->setError(JText::_('PLZ_SELECT_SCHEDULE'));
			return false;
		}
		return true;
	}
	
	function getUID( $isNew=true, $id=0 ){
		
		$str = $this->getUniqueID(10);
		
		$query = 'select count(*) as uc from '.$this->_db->quoteName('#__vd_schedules').' where uid = '.$this->_db->quote($str);
		if(!$isNew & $id){
			$query .= ' and id <> '.$this->_db->quote($id);
		}
		$this->_db->setQuery($query);
		if($this->_db->loadResult()){
			$currentTime = $date->toUnix();
			$str .= $currentTime;
		}
		return $str;
	}
	
}

?>