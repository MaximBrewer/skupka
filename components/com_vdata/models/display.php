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
//defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

class VdataModelDisplay extends VDModel
{
	var $_data = null;
    var $_total = null;
	var $_pagination = null;
	
	function __construct()
	{
		parent::__construct();
        $mainframe = JFactory::getApplication();
		$allGetVar1 = JFactory::getApplication()->input->getArray(array());
		foreach($allGetVar1 as $key => $value)
		if($key!='language'||$key!='Itemid'||$key!='option'||$key!='view'||$key!='displayid'||$key!='layout'){
		$extra_text = '.'.$key; break;	
		}
			
		$context			= 'com_vdata.display.items.list.'; 
        /* Get pagination request variables */
		 JRequest::getInt('limitstart');
		$limit = $mainframe->getUserStateFromRequest($context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState($context.'limit', $limit);
        $this->setState($context.'limitstart', $limitstart);
		
		$array = JFactory::getApplication()->input->get('cid',  0, 'ARRAY');
		$this->setId((int)$array[0]);
		
	}
	function setId($id)
	{
		/* Set id and wipe data */
		$this->_id		= $id;
		$this->_data	= null;
	}
	/**
	 * Method to Get Template(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getItemTemplate()
	{
		 $displayid = JFactory::getApplication()->input->get('displayid');
		 $allGetVar = JFactory::getApplication()->input->getArray(array());
		 //print_r($allGetVar);jeixt();
		 if($displayid)
		 {
			$query="select count(*) from #__vd_display where id=".$this->_db->quote($displayid)." and state=1";
			$this->_db->setQuery( $query );
			$count=$this->_db->loadResult();
			if($count<1)
			{
            	JFactory::getApplication()->redirect(JURI::root(), JText::_('NO_PROFILE_FOUND'));
			}

		 }
		
		 $query="select * from #__vd_display where id=".$this->_db->quote($displayid)." and state=1";
		 $this->_db->setQuery( $query );
		 $profiles=$this->_db->loadObject();
		 return $profiles;
	}
		
	/**
	 * Method to Fetch  Items Record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getItems(){
		$context			= 'com_vdata.display.items.list.'; 
		if(empty($this->_data))	{
		$query = $this->_buildQuery();
		$filter = $this->_buildContentFilter();
		$query .= $filter;
		$orderby = $this->_buildItemOrderBy();
		$query .= $orderby;
		///echo '<br>'.$query;
		$this->_data = $this->_getList($query, $this->getState($context.'limitstart'), $this->getState($context.'limit'));
		}
		//echo '<br>'.$query;
		echo $this->_db->getErrorMsg();
		return $this->_data;
	}
	/**
	 * Method to Fetch  Item Record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getItem(){ 
	   
		if(empty($this->_data))	{
		$query = $this->_buildQuery();
		$filter = $this->_buildContentFilter();
        $query .= $filter;
		
		$orderby = '';
		$query .= $orderby;
		$this->_data = $this->_getList($query, '', '');
		}
		echo $this->_db->getErrorMsg();
		return $this->_data;      
		
	}
	/**
	 * Method to Fetch Query(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function _buildQuery()
	{
	    $displayTable=$this->getItemTemplate();
		$qry=$displayTable->qry;
		$profileid=$displayTable->profileid;
		if(!empty($qry)){
		$query=$qry;
		}
		if(!empty($profileid)){
		$db=$this->getDbc();
		$query1="select params from #__vd_profiles where id=".$db->quote($profileid);
		$db->setQuery( $query1 );
		$result1=$db->loadResult();
		$params=json_decode($result1);
		//echo "<pre>"; print_r($params);jexit();
		$table=$params->table;
		$fields=$params->fields;
		//echo "<pre>";print_r($fields);jexit();
		$tablearray=array();
		$reffield=array();
		$join=array();
		$onJoin=array();
		$refFieldValue=array();
		foreach($fields as $key=>$profile){
			
		if($profile->data=="reference"){
			$reffield[]=$key;
			foreach($profile->reftext as $val){
			$refFieldValue[]=$profile->table.".".$val." as "."'$profile->table".".$val'";
			}
		$tablearray[]=$profile->table;
        $onJoin[]=$profile->on;		}}
		//echo "<pre>";print_r($onJoin);jexit();
		$stringField=implode(",",$refFieldValue);
		//echo "<pre>";print_r($reffield);jexit();
		for($j=0;$j<count($tablearray);$j++){
		//$join[]=$tablearray[$j]. " as a".$j." ON a".$j.".id=i.".$reffield[$j]; 
		$join[]=$tablearray[$j]. " as ".$tablearray[$j]." ON ".$tablearray[$j].".".$onJoin[$j]."=i.".$reffield[$j];
		}
		//echo "<pre>";print_r($stringField);jexit();
		$join = count($join) ? 'JOIN ' . implode(' JOIN ', $join) : '';
		$query="select i.*,$stringField from $table as i $join "; }
		//print_r($query);jexit();
		return $query;
	}
	/**
	 * Method to Filter for Fetch Query(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function _buildContentFilter()
	{
		$displayTable=$this->getItemTemplate();
		$qry=$displayTable->qry;
		$profileid=$displayTable->profileid;
		$itemAlias=$displayTable->uniquealias;
		$arrayColumn=array();
		if(!empty($profileid)){
			$db=$this->getDbc();  
			$query2="select params from #__vd_profiles where id=".$db->quote($profileid);
			$db->setQuery( $query2);
			$result11=$db->loadResult();
			$params=json_decode($result11);
			foreach($params->fields as $keys=>$values)
			{
				$arrayColumn[]=$keys;
			}
			if(!empty($params->filters) && property_exists($params->filters, 'column')){
			$op=$params->filters->op;
			$value=$params->filters->value;
			$condition=$params->filters->cond;
			$column=$params->filters->column;
			$filterCondition=array();
			for($n=0;$n<count($column);$n++){
			$filterCondition[]='i.'.$column[$n].$condition[$n].$value[$n];
			}
			}
			if(!empty($filterCondition)){
			$profileCondiltion=implode(" ".$op." ",$filterCondition);}
		}
		if(!empty($qry)){
			$query2=$qry;
			$this->_db->setQuery( $query2);
			$result=$this->_db->loadObject();
			foreach($result as $keys=>$values)
			{
				$arrayColumn[]=$keys;
			}
			
		}
		//echo "<pre>";print_r($arrayColumn);
		$date =JFactory::getDate();
		$currentdate=$date->toSql();
		$mainframe =JFactory::getApplication();
		$user = JFactory::getUser();
		$where = array();
		$context = 'com_vdata.display.list.';
		$searchPhrase= $mainframe->getUserStateFromRequest( $context.'searchPhrase', 'searchPhrase',	'',	'array' );
		//echo "<pre>";print_r($searchPhrase);jexit();
		$searchSubstr= $mainframe->getUserStateFromRequest( $context.'searchSubstr', 'searchSubstr',	'',	'array' );
		$searchRadio= $mainframe->getUserStateFromRequest( $context.'searchRadio', 'searchRadio',	'',	'array' );
		$searchCheckbox= $mainframe->getUserStateFromRequest( $context.'searchCheckbox', 'searchCheckbox',	'',	'array' );
		$searchDrop= $mainframe->getUserStateFromRequest( $context.'searchDrop', 'searchDrop',	'',	'array' );
		$likeSearch= $mainframe->getUserStateFromRequest( $context.'likeSearch', 'likeSearch',	'',	'string' );
		$likeSearchValue= $mainframe->getUserStateFromRequest( $context.'likeSearchValue', 'likeSearchValue',	'',	'string' );
		
		$allGetVar = JFactory::getApplication()->input->getArray(array());
		if(!empty($profileCondiltion))
		{
			$where[] = $profileCondiltion;
		}
		
		 foreach($allGetVar as $varKey=>$varValue)
		{
			if($varKey!='language' && $varKey!='id'){
			if(in_array($varKey,$arrayColumn))
			{
				//var_dump($varValue);
				
				if(!empty($profileid)){
				 $where[] = 'LOWER( i.'.$varKey.' ) ='.$this->_db->Quote($varValue);
				}
				if(!empty($qry)){
				 $where[] = 'LOWER('.$varKey.' ) ='.$this->_db->Quote($varValue);
				}
				 
			}
			}
			
			
		} 
		
		
		
		if(!empty($allGetVar['id'])){
        $itemid=$allGetVar['id'];			
			 if(!empty($profileid)){
			    $where[] = 'LOWER( i.'.$itemAlias.' ) = '.$this->_db->Quote($itemid);
			 }
			 if(!empty($qry)){
				$where[] = 'LOWER('.$itemAlias.' ) = '.$this->_db->Quote($itemid);
			 }
		}
		//print_r($where);jexit();
		/******************************************
		              Phrase String Search 
		*******************************************/
		if(!empty($searchPhrase)){
				foreach($searchPhrase as $key=>$searchVal)
				{
					foreach($searchVal as $searchValue){
                       
                       if($searchValue!='')
					   { 
                           if ((strpos($key, '.') !== FALSE)||!empty($qry) )
							{
							  $searchShecma=$key;
							}
							else
							{
							  $searchShecma='i.'.$key;
							}					   
						    $where[] = 'LOWER('.$searchShecma.' ) = '.$this->_db->Quote($searchValue, false );
						}
					}
				
				}
		}
		/******************************************
		              Substring  Search 
		*******************************************/
		
	
		if(!empty($searchSubstr)){
				foreach($searchSubstr as $key=>$searchVal)
				{
					foreach($searchVal as $searchValue){
                       if($searchValue!='')
						{  	
						   if ((strpos($key, '.') !== FALSE)||!empty($qry))
							{
							  $searchShecma=$key;
							}
							else
							{
							  $searchShecma='i.'.$key;
							}					   
						   
						   $where[] = 'LOWER('.$searchShecma.' ) LIKE '.$this->_db->Quote( '%'.$searchValue.'%', false );
					   }
					}
				
				}
		}
		//print_r($where); jexit();
		/******************************************
		               CheckBox Type Search 
		*******************************************/
		
     		if(!empty($searchCheckbox)){
				 
		  foreach($searchCheckbox as $key=>$values){
			
	        foreach($values as $chVal) {
				if($chVal!=''){  
				
					if ((strpos($key, '.') !== FALSE)||!empty($qry))
					{
					  $searchShecma=$key;
					}
					else
					{
					  $searchShecma='i.'.$key;
					}
					//$inValue="''.$val.'";
				   	  $where[]='LOWER('.$searchShecma.' ) IN ('.implode(",",$this->_db->Quote($values)).')';
				}
			}
		  }
		    
				
	    }
		
	
		/******************************************
		              Radio Type Search 
		*******************************************/
		if(!empty($searchRadio)){
			
				foreach($searchRadio as $key=>$searchValue)
				{
					
                       if(trim($searchValue)!='')
						{  
					
						   if ((strpos($key, '.') !== FALSE)||!empty($qry))
							{
							  $searchShecma=$key;
							}
							else
							{
							  $searchShecma='i.'.$key;
							}					   
						   
						   $where[] = 'LOWER('.$searchShecma.' ) = '.$this->_db->Quote($searchValue, false );
					   }
				
				}
		}
	
		/******************************************
		               More Like Type Search 
		*******************************************/
		if(!empty($likeSearch)){
			$likeSearchSchema=explode(",",$likeSearchValue);
			$like=array();
			foreach($likeSearchSchema as $schemaValue)
			{
				if ((strpos($key, '.') !== FALSE)||!empty($qry))
				{
				$searchShecma=$schemaValue;
				}
				else
				{
				$searchShecma="i.".$schemaValue;
				}
				$like[] = 'LOWER('.$searchShecma.' ) LIKE '.$this->_db->Quote( '%'.$likeSearch.'%', false );			
			}
			    $where[]=implode(" OR ",$like);
			
		}
		//print_r($like);
		/******************************************
		               More Dropdown Search 
		*******************************************/
		if(!empty($searchDrop)){
			foreach($searchDrop as $key=>$searchValue)
			{
				if($searchValue!='')
				{ 
					if ((strpos($key, '.') !== FALSE)||!empty($qry))
					{
					  $searchShecma=$key;
					}
					else
					{
					  $searchShecma='i.'.$key;
					}					   
				    $where[] = 'LOWER('.$searchShecma.' ) = '.$this->_db->Quote($searchValue, false );
				}
			}
			
		}
		//print_r($where);jexit();
	    if(!empty($qry)){
		//$where[]='language in (' . $this->_db->quote(JFactory::getLanguage()->getTag()) . ',' . $this->_db->quote('*') . ')';
		//$where[] =' access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		}
		 if(!empty($profileid)){
		//$where[]='(i.publish_up!=STR_TO_DATE("0000-00-00 00:00:00","%Y-%m-%d %H:%i:%s")AND i.publish_up <=STR_TO_DATE("'.$currentdate.'","%Y-%m-%d %H:%i:%s"))';
		//$where[]='(i.publish_down>=STR_TO_DATE("'.$currentdate.'","%Y-%m-%d %H:%i:%s") OR i.publish_down= STR_TO_DATE("0000-00-00 00:00:00","%Y-%m-%d %H:%i:%s"))';
		//$where[] =' access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		//$where[]='i.language in (' . $this->_db->quote(JFactory::getLanguage()->getTag()) . ',' . $this->_db->quote('*') . ')';
		//$where[] ='i.access in ('.implode(',', $user->getAuthorisedViewLevels()).')';
		 }	
		$filter = count($where) ? ' WHERE ' . implode(' AND ', $where) : '';
		return $filter;
	}
		
	/**
	 * Method to Order by Fetch Query(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function _buildItemOrderBy()
	{
		$mainframe = JFactory::getApplication();
		$context='com_vdata.display.list.';
		//$filter_order= $mainframe->getUserStateFromRequest( $context.'filter_order', 'filter_order', 'i.id', 'cmd' );
		//$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		$displayTable=$this->getItemTemplate();
		$qry=$displayTable->qry;
		//print_r($qry);jexit();
		$profileid=$displayTable->profileid;
		if(!empty($qry)){
		$query=$qry;
		$orderby='';
		//$orderby = ' group by i.'.$groupby.' order by '.$filter_order.' '.$filter_order_Dir . ' ';
		}
		if(!empty($profileid)){
		$db=$this->getDbc();
		$query1="select params from #__vd_profiles where id=".$db->quote($profileid);
		$db->setQuery( $query1 );
		$result1=$db->loadResult();
		$params=json_decode($result1);
		$groupby=$params->groupby;
		$filter_order=$params->orderby;
		$filter_order_Dir=$params->orderdir;
		$orderby = ' group by i.'.$groupby.' order by '.$filter_order.' '.$filter_order_Dir . ' ';
		//echo "<pre>"; print_r($params->orderdir);jexit();
		}
		return $orderby;
	}
	/**
	 * Method to  Total Record (s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getTotal()
  	{
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
		$query = $this->_buildQuery();
		$filter = $this->_buildContentFilter();
		$query .= $filter;
		$this->_total = $this->_getListCount($query);    
		}
		return $this->_total;
  	}
	/**
	 * Method to  Pagination For Record (s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function getPagination()
  	{	$context			= 'com_vdata.display.items.list.'; 
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination($this->getTotal(), $this->getState($context.'limitstart'), $this->getState($context.'limit') );
		}
		return $this->_pagination;
  	}
	
	function getTwoProfilesMap()
	{
		$displayTable=$this->getItemTemplate();
		$templaterp=$displayTable->itemlisttmpl;
		//print_r($displayTable->itemlisttmpl);jexit();
		$keysArray=array();
		$twoProfile="@vdata-";
		if ((strpos($templaterp, '@vdata-') !== FALSE))
		{
			$position = strpos($templaterp, $twoProfile); 
			$findValue=substr($templaterp, $position+strlen($twoProfile));
			$findValArray=explode('"',$findValue);
			$FinalValues=explode('-',$findValArray[0]);
			$keysArray['refKey']=$FinalValues[1];
			$query1="select id,uniquekey,uniquealias from #__vd_display where uidprofile=".$this->_db->quote($FinalValues[0]);
			$this->_db->setQuery( $query1 );
			$result1=$this->_db->loadObject();
			$displayid=$result1->id;
			$uniquekey=$result1->uniquekey;
			
			$newurl="index.php?option=com_vdata&view=display&layout=items&displayid=".$displayid."&".$FinalValues[1]."=";
			$keysArray['urls']=$newurl;
			return $keysArray;
		}
		return $keysArray;
		
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
	//For define value of params in profiles
	function getParamsValue()
	{
		$displayTable=$this->getItemTemplate();
		$qry=$displayTable->qry;
		//print_r($qry);jexit();
		$profileid=$displayTable->profileid;
		if(!empty($profileid)){
		$db=$this->getDbc();
		$query1="select params from #__vd_profiles where id=".$db->quote($profileid);
		$db->setQuery( $query1 );
		$result1=$db->loadResult();
		$params=json_decode($result1);
		return $params;
		}
	}
}

?>