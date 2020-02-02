<?php
/**
*Time-Table FS+ - Joomla Component
* @package TT FS+
* @version 1.0.0
* @Copyright (C) 2010 FomSoft Plus
* @ All rights reserved
* @ Time-Table FS+ is Commercial Software
**/
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
if (!defined( 'DS' )) {
	define( 'DS', DIRECTORY_SEPARATOR );
}
if (!defined('JVERSION')) {
		define( 'JVERSION', '1.0' ); 
}
if (!defined('JPATH_ROOT')) {
global $mosConfig_absolute_path;		
define( 'JPATH_ROOT', $mosConfig_absolute_path); 
}

function com_uninstall()
{
$flagDel = JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."dflag.txt";
	if ( file_exists($flagDel)){
	@unlink( $flagDel);
	if (JVERSION == '1.0'){
	global $database;
	} else {
	$database = & JFactory::getDBO();
	}
	$db = $database;
//	$db->setQuery( "DELETE FROM `#__categories` WHERE `section` = 'com_ttfsp';");
//	@$db->query();
	$db->setQuery("DROP TABLE `#__ttfsp`;");
	@$db->query();
	$db->setQuery("DROP TABLE `#__ttfsp_spec`;");
	@$db->query();
	$db->setQuery("DROP TABLE `#__ttfsp_set`;");
	@$db->query();
	$db->setQuery("DROP TABLE `#__ttfsp_sprspec`;");
	@$db->query();
	$db->setQuery("DROP TABLE `#__ttfsp_addtime`;");
	@$db->query();
	$db->setQuery("DROP TABLE `#__ttfsp_el`;");
	@$db->query();
	$db->setQuery("DROP TABLE `#__ttfsp_sprsect`;");
	@$db->query();
	$db->setQuery("DROP TABLE `#__ttfsp_sprtime`;");
	@$db->query();
	$db->setQuery("DROP TABLE `#__ttfsp_dop`;");
	@$db->query();
	
}
}
?>
