<?php
/*------------------------------------------------------------------------

# mod_vdisplay - 

# ------------------------------------------------------------------------

# author    Team WDMtech

# copyright Copyright (C) 2013 wwww.wdmtech.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.wdmtech.com

# Technical Support:  Forum - http://www.wdmtech..com/support-forum

-----------------------------------------------------------------------*/

// No direct access to this file
class modVdisplayHelper
{
    /**
     * Retrieves the hello message
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */    
    public static function getDisplaySearch($params)
    {
		//$displayid= JFactory::getApplication()->input->get('displayid');
		$displayid=$params->get('displayid');
		$layout=JFactory::getApplication()->input->get('layout');
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
					->select($db->quoteName(array('profileid','qry','likesearch','morefilterkey','fieldtype')))
					->from($db->quoteName('#__vd_display'))
					->where('id ='.$db->quote($displayid));
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
		
    }
	public static function getEmptySearch($arrayEmptyFields,$profileid,$qry)
    {
		
		$arrayEmpty=array();
		$colArray=array();
		$resultArray=array();
		foreach($arrayEmptyFields as $emptyCol)
		{
			$colArray[]=$emptyCol;
			if(strpos($emptyCol, '.') !== FALSE)
			{
				$arrayEmpty[]=$emptyCol;
			}
			else
			{
				if(!empty($qry)){
					$arrayEmpty[]=$emptyCol;
				}
				if(!empty($profileid)){
				$arrayEmpty[]="i.".$emptyCol;
				}
			}
		}
		//print_r($arrayEmpty);jexit;
		$totalEmptyField=implode(",",$arrayEmpty);
		$displayid= JFactory::getApplication()->input->get('displayid');
		$db = JFactory::getDbo();
		if(!empty($profileid)){
				$query1="select params from #__vd_profiles where id=".$db->quote($profileid);
				$db->setQuery( $query1 );
				$result1=$db->loadResult();
				$params=json_decode($result1);
				$table=$params->table;
				$fields=$params->fields;
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
				$stringField=implode(",",$refFieldValue);
				for($j=0;$j<count($tablearray);$j++){
				$join[]=$tablearray[$j]. " as ".$tablearray[$j]." ON ".$tablearray[$j].".".$onJoin[$j]."=i.".$reffield[$j];
				}
				$join = count($join) ? 'JOIN ' . implode(' JOIN ', $join) : '';
				for($n=0;$n<count($arrayEmpty);$n++){
					$query="select distinct $arrayEmpty[$n] from $table as i $join "; 
					$db->setQuery($query);
					$result = $db->loadColumn();
					$resultArray[$colArray[$n]]=$result;
				}
		}
		if(!empty($qry)){
				$qryArray=explode(" ",strtolower($qry));
				$keyIndex=array_search("from",$qryArray);
				$table=$qryArray[$keyIndex+1];
				for($n=0;$n<count($arrayEmpty);$n++){
				$query="select distinct $arrayEmpty[$n] from $table "; 
				$db->setQuery($query);
				$result = $db->loadColumn();
				$resultArray[$colArray[$n]]=$result;
				}
			}
		
		
		return $resultArray;
		
    }
}