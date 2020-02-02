<?php

(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
if (!defined('_FSPLEGACY')) {
		define( '_FSPLEGACY', '1' ); 
class JRequest {
	function getVar( $param1, $param2 ){
		return  mosGetParam( $_REQUEST, $param1, $param2);
	}
	function getCmd( $param1, $param2 ){
		return  mosGetParam( $_REQUEST, $param1, $param2);
	}
	function getString( $param1, $param2 ){
		return  mosGetParam( $_REQUEST, $param1, $param2);
	}
	function get($param1) {
		return $_REQUEST;
	}
} 

class JRoute {
	function _ ($param1){
		return sefRelToAbs($param1);
	}
} 

class  JFactory {
	function getUser(){
		global $my;
		return $my;
	}
	function getDBO(){
		global $database;
		return $database;
	}
	function getApplication(){
	global $mainframe;
	return $mainframe;
	}
} 
class JHTML {
function _ ($param1,$param2=0, $param3=0, $param4=0, $param5=0, $param6=0, $param7=0 ) {
	switch ($param1) {
		case 'select.booleanlist' :
		$retparam = mosHTML::yesnoRadioList( $param2, $param3, $param4);
		break;
		case 'select.genericlist' :
		$retparam = mosHTML::selectList( $param2, $param3, $param4, $param5, $param6, $param7 );
		break;
		case 'list.category' :
		$retparam = mosAdminMenus::ComponentCategory( $param2, $param3, $param4  );
		break;
		case 'select.option' :
		$retparam = mosHTML::makeOption( $param2, $param3 );
		break;
		case 'grid.checkedout' :
		$retparam = mosCommonHTML::CheckedOutProcessing( $param2, $param3 );
		break;
		case 'list.accesslevel' :
		$retparam = mosAdminMenus::Access( $param2 );
		break;
		default :
		$retparam = '';
		break;
	}		
return $retparam;
}

}
class JArrayHelper {
	function getValue($param1,$param2=0, $param3=0){
		return mosGetParam( $param1, $param2, $param3 );
	}
	function toInteger($param1){
		return mosArrayToInts( $param1 );
	}
}

class JUtility {
function sendMail($param1='', $param2='', $param3='',  $param4='', $param5='', $param6=0, $param7=NULL, $param8=NULL, $param9=''){
	mosMail( $param1, $param2, $param3, $param4, $param5, $param6, $param7, $param8, $param9);
}
} 
}
?>