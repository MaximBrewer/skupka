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




require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'excel2vm.php');
$model=new Excel2vmModelExcel2vm('cron_profile');



if(!is_dir($model->cron_file_dir)){
   $model->cron_log("$model->cron_file_dir - не является корректной дирректорией. Проверьте правильность пути, начиная от корня сервера. Правильный путь к корню сайта - ".JPATH_BASE);
   exit();
}
$perms=substr(sprintf('%o', fileperms($model->cron_file_dir)), -4);
if(!is_readable($model->cron_file_dir)){
    $model->cron_log("Папка $model->cron_file_dir не может быть прочитана, т.к. на нее установлены права - $perms. Установите права - 755 ");
    exit();
}
if(@$model->remote_file){
    $extension=pathinfo($model->remote_file, PATHINFO_EXTENSION);
    if(!in_array(strtolower($extension),array('xls','xlsx','csv'))){
        $model->cron_log("Удаленный файл $model->remote_file имеет недопустимое расширение - $extension");
        exit();
    }
    $remote_file=file_get_contents($model->remote_file);

    if(!$remote_file){
       $model->cron_log("Удаленный файл $model->remote_file не может быть прочитан");
       exit();
    }

    $path=explode("/",$model->remote_file);
    $file_name=end($path);
    if(!file_put_contents($model->cron_file_dir.DS.$file_name,$remote_file)){
        $model->cron_log("Удаленный файл $model->remote_file не может быть записан в папке $model->cron_file_dir");
        exit();
    }
}
if(@$_GET['filename']){
    $file_name=$_GET['filename'];
    if(!file_exists($model->cron_file_dir.$file_name)){
        $model->cron_log("Файл ".$model->cron_file_dir.$file_name." не существует!");
        exit();
    }
}
else{
    $file_name=$model->getFileForCron();
    if(!$file_name){
       $model->cron_log("Не найден подходящий файл для импорта");
       exit();
    }
}

$file_perms= substr(sprintf('%o', fileperms($model->cron_file_dir.$file_name)), -4);
if(!is_readable($model->cron_file_dir.$file_name)){
    $model->cron_log("Файл $file_name не может быть прочитан, т.к. на него установлены права - $file_perms. Установите права - 644 ");
    exit();
}
$model->cron_log("Инициализация. Файл - $file_name");
if(@$_GET['profile']){
    $model->cron_log("Профиль - ".@$_GET['profile']);
}

$model->import_file_name=$file_name;
$model->import();
$log=json_decode(file_get_contents(JPATH_COMPONENT_ADMINISTRATOR . DS . 'log.txt'));
$model->cron_log("Импорт завершен. Новых категорий - $log->cn. Новых товаров - $log->pn. Обновленных категорий - $log->cu. Обновленных товаров - $log->pu");
exit();

?>