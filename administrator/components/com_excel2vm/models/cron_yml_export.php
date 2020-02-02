<?php

ini_set("default_charset","utf-8");
/* Подключение FrameWork Joomla */
$my_path=dirname(__FILE__);
$level='';
for($i=1;$i<=10;$i++){
   if(file_exists($my_path.$level."/configuration.php")) {
		$absolute_path=dirname($my_path.$level."/configuration.php");
		require_once ($my_path.$level."/configuration.php");
   }
   else
      $level.="/..";
}
if(!class_exists('jconfig'))die("Joomla Configuration File not found!");

$absolute_path=realpath($absolute_path);

define('_JEXEC',1);
define('JPATH_BASE',$absolute_path);




define('DS',DIRECTORY_SEPARATOR);
define('JPATH_COMPONENT_ADMINISTRATOR',JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_excel2vm');
define('JPATH_COMPONENT_SITE',JPATH_BASE.DS.'components'.DS.'com_excel2vm');


require_once (JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once (JPATH_BASE.DS.'includes'.DS.'framework.php');
//require_once (JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'environment'.DS.'request.php');

global $mainframe;
$mainframe=JFactory :: getApplication('site');
$mainframe->initialise();


require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'yml.php');
$model=new Excel2vmModelYml("export");
$model->yml_export();

exit();

?>