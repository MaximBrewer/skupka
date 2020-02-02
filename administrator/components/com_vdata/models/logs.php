<?php 
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMTech
# copyright Copyright (C) 2014 wwww.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech..com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );

class VdataModelLogs extends VDModel
{
	
	var $_data;
	var $_list;
	var $_total;
	
	
	function __construct($config = array())
	{
		$config['filter_fields'] = array(
			'i.message',
			'i.iotype',
			'p.title',
			'i.side',
			'i.op_start',
			'i.status',
			'i.id'
		);
		
		parent::__construct($config);
		$mainframe = JFactory::getApplication();
		
		$context			= 'com_vdata.logs.list.'; 
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
		$context = 'com_vdata.logs.list.';
		$db = JFactory::getDbo();

		$filter_type = $app->getUserStateFromRequest( $context.'filter_type', 'filter_type', -1, 'int' );
		$filter_time = $app->getUserStateFromRequest( $context.'filter_time', 'filter_time', '', 'string' );
		$filter_profile = $app->getUserStateFromRequest( $context.'filter_profile', 'filter_profile', '', 'string' );
		$filter_loc 	= $app->getUserStateFromRequest( $context.'filter_loc', 'filter_loc', '', 'string' );
		$filter_result 	= $app->getUserStateFromRequest( $context.'filter_result', 'filter_result', '', 'string' );

		$query = 'select i.*,p.title FROM #__vd_logs as i left join #__vd_profiles as p on i.profileid=p.id where 1=1';
		
		//filter by import/export
		if($filter_type!=-1){
			$query .= ' and i.iotype='.$db->quote($filter_type);
		}
		
		//filter by time
		if(!empty($filter_time)){
			
			switch($filter_time){
				case 'today':
					$query .= ' and DATE(i.op_start) = CURDATE()';
				break;
				case 'yesterday':
					$query .= ' and DATE(i.op_start) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
				break;
				case 'week':
					$query .= ' and DATE(i.op_start) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
				break;
				case 'month':
					$query .= ' and DATE(i.op_start) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)';//DATE(NOW() - INTERVAL 30 DAY)
				break;
				case 'year':
					$query .= ' and DATE(i.op_start) >= DATE_SUB(NOW(),INTERVAL 1 YEAR)';
				break;
			}
			
		} 
		
		//filter by profile
		if(!empty($filter_profile)){
			$query .= ' and i.profileid='.$db->quote($filter_profile);
		}
		
		//filter by import/export location
		if(!empty($filter_loc)){
			$query .= ' and i.side='.$db->quote($filter_loc);
		}
		
		//filter by result
		if(!empty($filter_result)){
			$query .= ' and i.status='.$db->quote($filter_result);
		}
		
		
		return $query;
	}
	
	function _buildItemOrderBy()
	{
        $mainframe = JFactory::getApplication();
		
		$context = 'com_vdata.logs.list.';
 
        $filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
 
        $orderby = ' group by i.id order by '.$filter_order.' '.$filter_order_Dir . ' ';
 
        return $orderby;
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
	
	
	function setId($id)
	{
		
		$this->_id		= $id;
		$this->_data	= null;
	}

	function getItem()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__vd_logs'.
					'  WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			if($this->_data->id)
				$this->_data->profile = $this->getProfile($this->_data->profileid);
			else
				$this->_data->profile = null;
				
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = '';
			$this->_data->iotype = '';
			$this->_data->profileid = '';
			$this->_data->table = '';
			$this->_data->message = 'predefined';
			$this->_data->iofile = '';
			$this->_data->op_start = date('Y-m-d');
			$this->_data->op_end = date('Y-m-d');
			$this->_data->side = '';
			$this->_data->user = 0;
			$this->_data->logfile = '';
			$this->_data->status = '';
			$this->_data->profile = null;
			
		}

		return $this->_data;
	   
	
	}
	
	public function getProfile($profileid)
	{
		$query = 'select * from #__vd_profiles where id='.$this->_db->quote($profileid);
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}
	
	function getProfiles(){
		
		$query = 'select * from #__vd_profiles as i where 1=1';
		$query .= ' order by i.title asc';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
		
	}
	
	public function getTable($type = 'logs', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	function truncate()
	{
		jimport('joomla.filesystem.file');
		$query = 'select * from '.$this->_db->quoteName('#__vd_logs');
		$this->_db->setQuery($query);
		$logs = $this->_db->loadObjectList();
		//remove log files
		foreach($logs as $log){
			if(!empty($log->logfile))
				JFile::delete(JPATH_ROOT.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.$log->logfile);
		}
		//remove uploaded/downloaded import/export files
		$dir_iterator = new RecursiveDirectoryIterator(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vdata'.DIRECTORY_SEPARATOR.'uploads');
		$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
		foreach ($iterator as $file) {
			if ($file->isFile()){
				// echo $file->getFilename();// echo $file->getExtension();
				if(in_array($file->getExtension(), array('csv','xml','json'))){
					JFile::delete($file->getPathname());//$file->getPathname()//$file
				}
			}
		}
		//truncate table
		$query = 'truncate table #__vd_logs';
		$this->_db->setQuery($query);
		try{
			$this->_db->execute();
			
		}
		catch(Exception $e){
			$this->setError( $e->getMessage() );
			return false;
		}
		return true;
	}
	
	function delete()
	{
		
		$cids = JFactory::getApplication()->input->get( 'cid', array(0), 'ARRAY' );

		$row =& $this->getTable('Vcharts', 'VchartTable');

		if (count( $cids ))
		{
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}						
		}
		return true;
	}
	
}

