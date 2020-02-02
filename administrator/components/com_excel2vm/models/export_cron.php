<?php

ini_set("display_errors","1");
ini_set("display_startup_errors","1");
ini_set('error_reporting', E_ALL);

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
$lang = JFactory::getLanguage();
$lang->setLanguage('ru-RU');
$lang->load('com_excel2vm',JPATH_ADMINISTRATOR);




require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'export.php');
$model=new Excel2vmModelExport('cron_export_profile');



if(!is_dir($model->export_directory_path)){
   $model->cron_log("$model->export_directory_path - не является корректной дирректорией. Проверьте правильность пути, начиная от корня сервера. Правильный путь к корню сайта - ".JPATH_BASE);
   exit();
}
$perms=substr(sprintf('%o', fileperms($model->export_directory_path)), -4);
if(!is_executable($model->export_directory_path) OR !is_writable($model->export_directory_path)){
    $model->cron_log("В папку $model->export_directory_path запрещена запись, т.к. на нее установлены права - $perms. Установите права - 755 ");
    exit();
}


$model->cron_log("Инициализация.");
if(@$_GET['profile']){
    $model->cron_log("Профиль - ".@$_GET['profile']);
}

ob_start();
$model->export();
$data=ob_get_contents();
ob_end_clean();
$data2=@json_decode($data);

$model->cron_log("Экспорт завершен");
exit();

?>