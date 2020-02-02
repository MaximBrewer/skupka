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
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class VdataModelNotifications extends VDModel
{
    var $_data = null;
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
 
        $mainframe = JFactory::getApplication();
		
		$context			= 'com_vdata.notifications.list.'; 
        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);

		$array = JFactory::getApplication()->input->get('cid',  0, 'ARRAY');
		$this->setId((int)$array[0]);
	}
	
	function _buildQuery()
	{
		$app = JFactory::getApplication();
		$context = 'com_vdata.notifications.list.';
		$db = JFactory::getDbo();
		
		$keyword = $app->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		
		$user = JFactory::getUser();
		$query = 'select n.* FROM #__vd_notifications as n';
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query .= ' and n.access IN (' . $groups . ')';
		}
		
		if(!empty($keyword))
			$query .= ' and n.title like '.$db->quote('%'.$keyword.'%');
		
		return $query;
	}
	
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	
	function getItem()
    {
		
		$query = 'select n.* FROM #__vd_notifications as n where n.id = '.(int)$this->_id;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		if(empty($item))	{
			$item = new stdClass();
			$item->id = null;
			$item->title = null;
			$item->params = '';
			$item->notification_tmpl = '';
			$item->url = '';
			$item->widget = '';
			$item->state = 0;
		}
		
		$item->params = json_decode($item->params);
		$item->notification_tmpl = json_decode($item->notification_tmpl);
		return $item;
    }
	
	function getTables()
	{
		
		$query = 'show tables';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadColumn();
		$items = str_replace($this->_db->getPrefix(), '#__', $items);
		return $items;
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
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);    
        }
        return $this->_total;
  	}
	
	function getPagination()
  	{
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
  	}
	
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		
		$context			= 'com_vdata.notifications.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'n.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
 
        $orderby = ' group by n.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
 
        return $orderby;
	}

	function store()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		$post = JFactory::getApplication()->input->post->getArray(array());
		$params = JFactory::getApplication()->input->post->getArray(array("params"=>"RAW"));
		$notification_tmpl = JFactory::getApplication()->input->post->getArray(array("notification_tmpl"=>"RAW"));
		$post['params'] = $params['params'];
		$post['notification_tmpl'] = $notification_tmpl['notification_tmpl'];
		
		$row = $this->getTable();
		
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
		
		// Check for request forgeries
		JSession::checkToken() or jexit( JText::_('INVALID_TOKEN') );
		
		$cids = JFactory::getApplication()->input->get( 'cid', array(), 'ARRAY' );

		if (count( $cids ) < 1) {
			$this->setError( JText::_( 'SELECT_MIN', true ) );
			return false;
		}
		
		$row =  $this->getTable();

		foreach ($cids as $id)
		{
			
			if(!$row->delete($id))	{
				$this->setError($row->getError());
				return false;
				
			}

		}
		
		return true;
		
	}
	
	function publishList($cid = array(), $publish = 1) {
		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			$query = 'UPDATE #__vd_notifications'
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
	
	function getTableFields($table){
		
		$query = 'show fields FROM '.$table;
		$this->_db->setQuery($query);
		$columns = $this->_db->loadObjectList();
		return $columns;
	}
	
	function getTableColumns($table, $selected=''){
		$obj = new stdClass();
		$query = 'show fields FROM '.$table;
		$this->_db->setQuery($query);
		$columns = $this->_db->loadObjectList();
		$html = '<option value="">'.JText::_('SELECT_COLUMN').'</option>';
		foreach($columns as $column){
			$html .= '<option value="'.$column->Field.'"';
			if(!empty($selected) && $column->Field==$selected){
				$html .= ' selected="selected"';
			}
			$html .= '>'.$column->Field.'</option>';
		}
		return $html;
	}
	
	function getNotificationColumn($table, $tableColumns, $query, $selected=''){
		
		$obj = new stdClass();
		if(!empty($table)){
			
			$html = '<option value="">'.JText::_('SELECT_COLUMN').'</option>';
			$query = "SELECT ";
			if(!empty($tableColumns)){
				foreach($tableColumns as $idx=>$column){
					$query .= $this->_db->quoteName($column);
					if($idx<(count($tableColumns)-1)){
						$query .= ", ";
					}
				}
			}
			else{
				$query .= " * ";
			}
			$query .= " FROM ".$this->_db->quoteName($table);
			$this->_db->setQuery($query);
			$columns = $this->_db->loadObjectList();
			if(!empty($columns)){
				foreach($columns[0] as $column=>$val){
					$html .= '<option value="'.$column.'"';
					if(!empty($selected) && $column==$selected){
						$html .= ' selected="selected"';
					}
					$html .= '>'.$column.'</option>';
				}
			}
			
			$obj->result = 'success';
			$obj->html = $html;
			return $obj;
		}
		
		try{
			
			$this->_db->setQuery($query);
			$result = $this->_db->loadObjectList();
		}
		catch(RuntimeException $e){
			$obj->result = 'error';
			$obj->error = $e->getMessage();
			return $obj;
		}
		
		if($this->_db->getErrorNum()){
			$obj->result = 'error';
			$obj->error = $this->_db->getErrorMsg();
			return $obj;
		}
		
		
		//columns from custom query
		$html = '<option value="">'.JText::_('SELECT_COLUMN').'</option>';
		if(!empty($result)){
			foreach($result[0] as $key=>$value){
				$html .= '<option value="'.$key.'"';
				if($key==$selected){
					$html .= ' selected="selected"';
				}
				$html .= '>'.$key.'</option>';
			}
		}
		
		$obj->result = 'success';
		$obj->html = $html;
		return $obj;
		
	}
	
	function getUserGroups(){

		$query = $this->_db->getQuery(true)
			->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level')
			->from($this->_db->quoteName('#__usergroups') . ' AS a')
			->join('LEFT', $this->_db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id, a.title, a.lft, a.rgt')
			->order('a.lft ASC');
		$this->_db->setQuery($query);
		$options = $this->_db->loadObjectList();
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
		}
		
		return $options;
	}
	
}

?>