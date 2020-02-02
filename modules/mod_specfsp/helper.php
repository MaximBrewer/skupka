<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

class modSpecFSPHelper
{
///////////////////////////////////////////////////////////////////////////////
public static function getmyparams(){
		$database = JFactory::getDBO();
		$query = "SELECT name AS text, value FROM #__ttfsp_set";
		$database->setQuery( $query );
		$myparams = array();
		$myparams = $database->loadObjectList();
		foreach($myparams as $param) {
			$myparams[$param->text] = $param->value;
		}
		return $myparams;
	}
////////////////////////////////////////////////////////////////////////////////
public static function getrows($office, $spec, $scount){
	$database = JFactory::getDBO();
	$where = 'published = 1';	
	if ($spec) 	
		$where .= " AND idsprspec LIKE '%,".$spec.",%' ";
	if ($office)
		$where .= " AND idsprsect = ".$office;		 
$query = "SELECT *"
		. "\n FROM #__ttfsp_spec "
		. "\n  WHERE ".$where." ORDER BY ordering ASC LIMIT ".$scount
		;
$database->setQuery( $query );
$rows = $database->loadObjectList();
return $rows;
	}
////////////////////////////////////////////////////////////////////////////////
public static function getrowsspec(){
	$database = JFactory::getDBO();
	$where = 'published = 1';	
	$database->setQuery( "SELECT * FROM #__ttfsp_sprspec WHERE ".$where );
	$rows = $database->loadObjectList();
	return $rows;
	}	
}
