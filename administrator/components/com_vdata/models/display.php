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

class VdataModelDisplay extends VDModel
{
    var $_data;
	var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
        $mainframe = JFactory::getApplication();
		$context	= 'com_vdata.display.list.'; 
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
		$context = 'com_vdata.display.list.';
		$db = JFactory::getDbo();
		
		$keyword = $app->getUserStateFromRequest( $context.'search', 'search', '', 'string' );
		$filter_type = $app->getUserStateFromRequest( $context.'filter_type', 'filter_type', -1, 'int' );
		
		$query = 'select i.*, IFNULL(p.title,i.qry) as profile from #__vd_display as i left join #__vd_profiles as p on i.profileid=p.id where 1=1';
		$user = JFactory::getUser();
		
		
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
		$query = 'select i.* from #__vd_display as i where i.id = '.(int)$this->_id;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		if(empty($item)) {
				$item = new stdClass();
				$item->id = null;
				$item->title = null;
				$item->profileid = null; 
				$item->qry = null;
				$item->uniquekey = null;
				$item->uniquealias = null; 
				$item->uidprofile = null;
				$item->keyword = null;
				$item->likesearch = null;
				$item->morefilterkey = null;
				$item->access = null;
				$item->state = null;
				$item->norowitem = null;
				$item->listtmplid = null;
				$item->itemlisttmpl = null;
				$item->detailtmplid = null;
				$item->itemdetailtmpl = null;
				$item->fieldtype = null;
				
		}
		
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
		$context			= 'com_vdata.display.list.';
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
		$id=$post['id'];
		
		$isNew = empty($id) ? true : false;	
	    $date =JFactory::getDate();
		if($post['keyword']==1){ $post['likesearch']=implode(",",$post['likesearch']);}
		$post['morefilterkey']=json_encode($post['morefilterkey']);
	
		$post['fieldtype']=json_encode($post['fieldtype']);
		$post['itemlisttmpl'] = JRequest::getVar( 'itemlisttmpl', '', 'post', 'string', JREQUEST_ALLOWHTML );
		$post['itemdetailtmpl'] = JRequest::getVar( 'itemdetailtmpl', '', 'post', 'string', JREQUEST_ALLOWHTML );
		$post['created'] = $date->toSql();
		if(!$post["id"]){ $post['modified'] = $date->toSql(); }
		if(empty($post['uidprofile'])){
		 $post['uidprofile']=$this->getUniqueID(10);
		}
		if(!empty($post['uidprofile']))
		{
			$str = $this->getUniqueID(10);
			$query = 'select count(*) as uc from '.$this->_db->quoteName('#__vd_display').' where uidprofile = '.$this->_db->quote($post['uidprofile']);
			$query .= ' and id <> '.$this->_db->quote($id);
			$this->_db->setQuery($query);
			if($this->_db->loadResult()){
				JFactory::getApplication()->enqueueMessage(JText::_('UNIQUE_ID_ALREADY_EXISTS'), 'Notice');
				return false;
			}
		}
		
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
		/* Check for request forgeries */
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
	
   /**
	 * Method to Dropdown Profiles record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getProfiles(){
		$user = JFactory::getUser();
		$query = 'select * from #__vd_profiles where iotype=1';
		$query .= ' order by title asc';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	/**
	 * Method to Dropdown Profiles record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getProfilesQry(){
		$user = JFactory::getUser();
		$post = JFactory::getApplication()->input->post->getArray();
		$query=$post['qry'];
		$this->_db->setQuery($query);
		$this->_db->loadObject();
		//print_r($trp[0]);jexit();
		return $this->_db->loadObject();
	}
	
	function publishList($cid = array(), $publish = 1) {
		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			$query = 'UPDATE #__vd_display'
				   . ' SET state = '.(int) $publish
				   . ' WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
				if (!$this->_db->query()){
					$this->setError($this->_db->getErrorMsg());
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
	/**
	 * Method to Onchange Dropdown Profiles record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */	
	function getProfilesfields()
	{
		$data = JRequest::get('post');
	    $profielid=$data['id'];
		$db=$this->getDbc();
		$query = 'select params from #__vd_profiles where id='.$db->quote($profielid);
		$db->setQuery($query);
		return $db->loadObject();
		
	}
	function getProfilesDefaultFields()
	{
		
		$cid1 = JFactory::getApplication()->input->get('cid');
		$cid=	$cid1[0]; 
		$obj=new stdClass();
		
		$q='select qry,profileid from #__vd_display where id='.$this->_db->quote($cid);
		$this->_db->setQuery($q);
		$result=$this->_db->loadObject();
		$profileDt=$result->profileid;
		$qryDt=$result->qry;
		if(!empty($qryDt)){
			$query =$qryDt;
			$trp=$this->_db->setQuery($query);
			$profiles=$this->_db->loadObject();
			$profileArray=(array)$profiles;
			$fieldsArray=array();
			foreach($profileArray as $key=>$values)
			{
				$fieldsArray[]=$key;
			}
			$mainArray=array();
			$mainArray['table']=$fieldsArray;
			$mainTable=array();
			$mainTable['mainTable']=$mainArray;
			$obj->includekey=$mainArray;
			$obj->likeSearch=$mainTable;
			//print_r($obj->likeSearch);jexit();
		}
		if(!empty($profileDt)){
			$db=$this->getDbc();
			$query='select params from #__vd_profiles where id='.$db->quote($profileDt);
			$db->setQuery($query);
			$allfield=$db->loadResult();
			$allfield1=json_decode($allfield);
			if(property_exists($allfield1,'table')){
				$maintable=$allfield1->table;
				$arraymain=array();
				if(property_exists($allfield1,'fields')){
					$fields=(array)$allfield1->fields;
					$allkey=array();
					$arrayRef=array();
					$defArray=array();
					$refTables = array();
					$refCol = array();
					foreach($fields as $key=>$value)
					{
							if($value->data=='include'){
								$allkey[]=$key;
							}
							if($value->data=="reference"){
								if(!in_array($value->table,$refTables)){
									$refTables[]=$value->table;
									if(!in_array($value->reftext,$refCol)){
										$refCol[$value->table]=$value->reftext;
									}
								} 
							}
							if($value->data=="defined"){
								$allkey[]=$key;
							}
					}
					foreach($refTables as $table){
						$arrayRef[$table]=$refCol[$table];
					}
					
					$keySearch=array();
					$arraymain[$maintable]=$allkey;
					$keySearch['mainTable']=$arraymain;
					$keySearch['refrTable']=$arrayRef;
					$obj->includekey=$arraymain;
					$obj->likeSearch=$keySearch;
				}
				else{
					$arraymain[$maintable]=$this->quickFields($maintable);
					$keySearch['mainTable']=$arraymain;
					$obj->includekey=$arraymain;
					$obj->likeSearch=$keySearch;
				}
			}
		}
		return $obj;
	}
	/**
	 * Method to Default List Template record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getDefaultList()
	{   		
		$db=JFactory::getDBO();
		$query=$db->getQuery(true);
		$query->select('template');
		$query->from('#__vd_list_template where id=1');
		$db->setQuery($query);
		$result=$db->loadResult();
		return $result;
	}
   /**
	 * Method to Dropdown List Template record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getListtemplate()
	{
		
		$db=JFactory::getDBO();
		$query=$db->getQuery(true);
		$query->select('id,title');
		$query->from('#__vd_list_template');
		$db->setQuery($query);
		$result=$db->loadObjectList();
		return $result;
	}
	/**
	 * Method to Onchange Dropdown List Template record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getTemplatelist()
	{
		$data = JRequest::get('post');
	    $templateid=$data['id'];
		$db=JFactory::getDBO();
		$query=$db->getQuery(true);
		$query->select('template');
		$query->from('#__vd_list_template where id='.(int)$templateid);
		$db->setQuery($query);
		$result=$db->loadResult();
		return $result;
		
	}
	/**
	 * Method to Onchange Dropdown Detail Layout Template record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */	
	function getDefaultDetaillayout()
	{
		$db=JFactory::getDBO();
		$query=$db->getQuery(true);
		$query->select('template');
		$query->from('#__vd_detail_template where id=1');
		$db->setQuery($query);
		$result=$db->loadResult();
		return $result;
		
	}
  /**
	 * Method to Dropdown Detail Layout Template record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */	
	function getDetailtemplate()
	{
		$db=JFactory::getDBO();
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__vd_detail_template');
		$db->setQuery($query);
		$result=$db->loadObjectList();
		return $result;
		
	}
   /**
	 * Method to Onchange Dropdown Detail Layout Template record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */	
	function getTemplateDetail()
	{
		$data = JRequest::get('post');
	    $templateid=$data['id'];
		$db=JFactory::getDBO();
		$query=$db->getQuery(true);
		$query->select('template');
		$query->from('#__vd_detail_template where id='.(int)$templateid);
		$db->setQuery($query);
		$result=$db->loadResult();
		return $result;
		
	}
	
	function getDbc()
	{
		
		$hd_config = $this->getConfig();
		$dbconfig = json_decode($hd_config->dbconfig);
		if($dbconfig->local_db==1){
		 $dbc = JFactory::getDbo();
		}
		else{
		$option = array();
		if( property_exists($dbconfig, 'driver') && property_exists($dbconfig, 'host') && property_exists($dbconfig, 'user') && property_exists($dbconfig, 'password') && property_exists($dbconfig, 'database') && property_exists($dbconfig, 'dbprefix') ){

			$option['driver'] = $dbconfig->driver;
			$option['host'] = $dbconfig->host;
			$option['user'] = $dbconfig->user;
			$option['password'] = $dbconfig->password;
			$option['database'] = $dbconfig->database;
			if(!empty($dbconfig->dbprefix))
			 $option['prefix'] = $dbconfig->dbprefix;
		}

			try{

				$dbc = JDatabaseDriver::getInstance($option);
				$dbc->connect();
			}
			catch(Exception $e){
				throw new Exception($e->getMessage());
				return false;
			}
		}
		return $dbc;
	}


	function getConfig()

	{
		$db = JFactory::getDbo();
		$query = "select * from #__vd_config where id =1";
		$db->setQuery($query);
		return $db->loadObject();
	}
	function quickFields($main_table)
	{
	     $fields_array=array();
		 $db=$this->getDbc();
		 $query='SHOW COLUMNS FROM '.$main_table ;
		 $db->setQuery($query);
		 $result=$db->loadObjectList();
		 foreach($result as $result_value)
		 {
			 $fields_array[]=$result_value->Field;
		 }
		 return $fields_array;
	}
	
}
?>