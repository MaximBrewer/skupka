<?php 
error_reporting(0);

define( 'DS', DIRECTORY_SEPARATOR );
$rootFolder = explode(DS,dirname(__FILE__));

//current level in diretoty structure
$currentfolderlevel = 4;

array_splice($rootFolder,-$currentfolderlevel);


$base_folder = implode(DS,$rootFolder);


if(!is_dir($base_folder.DS.'libraries'.DS.'joomla')) exit('Error: Could not loaded Joomla.');

define( '_JEXEC', 1 );
define('JPATH_BASE',implode(DS,$rootFolder));

// Include the Slim REST framework
//require_once ( JPATH_BASE .DS.'libraries/slim/Slim.php' );
// Include the Joomla framework
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$application  = JFactory::getApplication('site');
$application ->initialise();
$rootUri = explode('/',JURI::root(true));
array_splice($rootUri,-$currentfolderlevel);
$base_uri =   JURI::getInstance()->getScheme().'://'
		. JURI::getInstance()->getHost()
		. implode('/',$rootUri).'/';
