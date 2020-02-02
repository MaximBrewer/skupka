<?php
defined('_JEXEC')or die;
require_once(__DIR__.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'); 
//$datas = PCH::collectCustomsFromGet(); 
$path = JModuleHelper::getLayoutPath('mod_productcustoms', 'default_js'); 
require($path);
$path = JModuleHelper::getLayoutPath('mod_productcustoms'); 
require($path);