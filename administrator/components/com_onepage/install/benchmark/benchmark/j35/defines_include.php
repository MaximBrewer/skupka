<?php
defined('_JEXEC') or die;

if (defined('BROOT')) {
global $bench_arr; 
if (empty($bench_arr))
$bench_arr = array();
if (empty($bench_arr[0])) 
$GLOBALS['bench_arr'] = $bench_arr; 
$GLOBALS['bench_arr']['global']['system']['name'] = 'system';
$GLOBALS['bench_arr']['global']['system']['start'] = microtime(true); 


define('BENCHDIR', BROOT . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'benchmark' . DIRECTORY_SEPARATOR . 'benchmark' . DIRECTORY_SEPARATOR . 'j35'); 

if (!defined('JPATH_PLATFORM'))
{
	define('JPATH_PLATFORM', BROOT . DIRECTORY_SEPARATOR . 'libraries');
}
if (!class_exists('JLoader'))
{
	require_once JPATH_PLATFORM . '/loader.php';
}
if (file_exists(BENCHDIR . DIRECTORY_SEPARATOR . 'dispatcher.php')) {
	
JLoader::register('JEventDispatcher', BENCHDIR . DIRECTORY_SEPARATOR . 'dispatcher.php' );
//JLoader::register('JDispatcher', BENCHDIR . DIRECTORY_SEPARATOR . 'dispatcher.php' );
JLoader::register('JComponentHelper', BENCHDIR . DIRECTORY_SEPARATOR . 'helper.php' );
JLoader::register('JDocumentRendererModule', BENCHDIR . DIRECTORY_SEPARATOR . 'module.php' );
JLoader::register('JDocumentRendererHtmlModule', BENCHDIR . DIRECTORY_SEPARATOR . 'modulehtml.php' );
}


}