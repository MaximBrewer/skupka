<?php
defined('_JEXEC')or die;
$get = PCH::getGet(); 

if (!defined('JUSTONCEHERE')) {
	
	$document = JFactory::getDocument(); 
	if (method_exists($document, 'addScript')) {
		
	$root = Juri::root(); 
	if (substr($root, -1) !== '/') $root .= '/'; 
	
	$document->addScript($root.'modules/mod_productcustoms/js/productcustoms.js'); 
	define('JUSTONCEHERE', 1); 

$js = '//<![CDATA[ 
var getUrl = \''.str_replace("'", "\'", json_encode($get)).'\'; 
//]]>'; 


$document->addScriptDeclaration($js); 
 } 
}