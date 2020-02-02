<?php
// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
if (!defined( 'DS' )) {
	define( 'DS', DIRECTORY_SEPARATOR );
}
if ( file_exists( JPATH_ROOT.DS."administrator".DS."components".DS."com_ttfsp".DS."admin.ttfsp.php") ) {
require_once (dirname(__FILE__).DS.'helper.php');

$office	 = 		intval($params->def('office', 0)); // Учреждение
$spec	 = 		intval($params->def('spec', 0)); // Специализация
$scount	 = 		intval($params->def('scount', 5)); // Количество выводимых специалистов
$desc	 = 		intval($params->def('desc', 0)); // Показывать описание специалиста
$sspec	 = 		intval($params->def('sspec', 0)); // Показывать специализацию специалиста
$comment	 = 		intval($params->def('comment', 0)); // Показывать ссылку - Отзывы о работе специалиста
//$text_comment = 	$params->def('text_comment', ''); // Текст ссылки - Отзывы о работе специалиста
$fiolink	 = 		intval($params->def('link', 1)); // Название специалиста сделать ссылкой на запись
$button	 = 		intval($params->def('button', 0)); // Показывать кнопку - Запись на прием
//$text_button = 	$params->def('text_button', ''); // Текст кнопки - Запись на прием
$photo	 = 		intval($params->def('photo', 0)); // Показывать фото специалиста
$add_link = 		$params->def('add_link', ''); // Произвольная ссылка
$text_add_link = $params->def('text_add_link', ''); // Текст произвольной ссылки
$myparams = modSpecFSPHelper::getmyparams();
$url_site = $myparams['url_site'];
$rows = modSpecFSPHelper::getrows($office, $spec, $scount);
$rowspec = array();
if ($sspec)
	$rowspec = modSpecFSPHelper::getrowsspec();
require(JModuleHelper::getLayoutPath('mod_specfsp'));
} else {
	echo 'Error. Not found component TT FS+';
}
?>