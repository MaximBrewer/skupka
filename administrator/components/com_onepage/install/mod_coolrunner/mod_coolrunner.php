<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

if (empty($params))
{
	$db = JFactory::getDBO(); 
	$q = 'select `params` from `#__modules` where `module` = "mod_coolrunner" and `published` = 1';
	$q = $db->setQuery($q); 
	$res = $db->loadResult(); 
	
	if (!empty($res))
	$params = new JRegistry($res); 


}
JFactory::getLanguage()->load('mod_coolrunner'); 
$shipping_methods = $params->get('shipping_methods', ''); 

$path = JModuleHelper::getLayoutPath('mod_coolrunner', 'default_js'); 
require($path);
$path = JModuleHelper::getLayoutPath('mod_coolrunner'); 
require($path);
