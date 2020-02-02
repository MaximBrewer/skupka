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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class VdataModelVdata extends VDModel
{
    
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();
		
		$context			= 'com_vdata.vdata.list.'; 
        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);

		$array = JFactory::getApplication()->input->get('cid',  0, '', 'array');
		$this->setId((int)$array[0]);

	}
	function getConfiguration()
	{
	    $query = 'select * from '.$this->_db->quoteName('#__vd_config').' where id=1';
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
      return $item;	
	}
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}
	function getProfilesname(){
		$query = 'select i.*, e.name as plugin, e.element, concat("plg_", e.folder, "_", e.element) as extension FROM #__vd_profiles as i left join #__extensions as e on (e.extension_id=i.pluginid and e.enabled=1) where 1=1 group by i.id order by id desc';
		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();
		return $data;
	}
    function _buildQuery()
	{
		$query = 'select i.*, e.name as plugin, e.element, concat("plg_", e.folder, "_", e.element) as extension FROM #__vd_profiles as i left join #__extensions as e on (e.extension_id=i.pluginid and e.enabled=1)';

		return $query;
	}
	function getProfiles(){
		$query = 'select i.* from #__vd_widget as i order by ordering';
		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList();
		return $data;
	}
	function getProfile($profile_id)
	{
		
		$data = array();
		if($profile_id!='')
		{
		$query = 'select i.* from #__vd_widget as i where id="'.$profile_id.'" order by ordering';
		$this->_db->setQuery($query);
		$data = $this->_db->loadObject();
		}
		return $data;
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
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		
		$context			= 'com_vdata.profiles.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.ordering', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );
 
        $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
 
        return $orderby;
	}
	public function getTable($type = 'Profiles', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	function updateordering(){
		
		$ordering_data = JFactory::getApplication()->input->get('new_ordering', 0, 'ARRAY');
		$row = $this->getTable('Widget', 'Table');
		for($i=0;$i<count($ordering_data);$i++){
			$data = explode(':', $ordering_data[$i]);
			$query ='UPDATE #__vd_widget set ordering='.($i+1).' WHERE id='.(int)$data[0];
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		jexit();
	}
	function getWidgets()
    {
      $query = 'select * from #__vd_widget order by ordering';
	  $this->_db->setQuery();
	  $widget_data = $this->_db->loadObjectList();
        return $widget_data;
    }
	function delete()
	{
		
		$cids = JFactory::getApplication()->input->get( 'id',0);

		$row = $this->getTable('Widget', 'Table');

		if ($cids )
		{
			$query = 'delete from #__vd_widget where id='.(int)$cids;
			$this->_db->setQuery($query)->query();
			return true;				
		}
		return true;
	}
	function getPlugins()
	{
		
		$query = 'select extension_id, name from #__extensions  where type = "plugin" and folder = "vdata" and enabled = 1';
		$this->_db->setQuery( $query );
		$items = $this->_db->loadObjectList();
		/* 
		foreach($items as $key=>$value){
			$plugins[$value->extension_id] = $value->name;
			
		} */
		
		return $items;
		
	}
	
}

?>