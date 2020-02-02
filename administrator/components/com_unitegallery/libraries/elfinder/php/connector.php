<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');


echo "nothing here";
exit();

//set_time_limit(0); // just in case it too long, not recommended for production
error_reporting(E_ALL | E_STRICT); // Set E_ALL for debuging

ini_set('max_file_uploads', 50);   // allow uploading up to 50 files at once

// needed for case insensitive search to work, due to broken UTF-8 support in PHP
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set('mbstring.func_overload', 2);

if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('Europe/Moscow');
}

include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderConnector.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinder.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeDriver.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeLocalFileSystem.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeMySQL.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';





$opts = array(
	'locale' => 'en_US.UTF-8',
	'debug' => true,
	'roots' => array(
		array(
			'driver'     => 'LocalFileSystemUG',
			'path'       => GlobalsUG::$path_images,
			'startPath'  => GlobalsUG::$path_images,
			'URL'        => GlobalsUG::$url_images,
			// 'treeDeep'   => 3,
			// 'alias'      => 'File system',
			'mimeDetect' => 'internal',
			'tmbPath'    => 'elfinder_tmb',
			'utf8fix'    => true,
			'tmbCrop'    => false,
			'tmbBgColor' => 'transparent',
			'accessControl' => 'access',
			'acceptedName'    => '/^[^\.].*$/',
			// 'disabled' => array('extract', 'archive'),
			// 'tmbSize' => 128,
			'attributes' => array(
				array(
					'pattern' => '/\.js$/',
					'read' => true,
					'write' => false
				),
				array(
					'pattern' => '/^\/icons$/',
					'read' => true,
					'write' => false
				)
			)
			// 'uploadDeny' => array('application', 'text/xml')
		),
		// array(
		// 	'driver'     => 'LocalFileSystem',
		// 	'path'       => '../files2/',
		// 	// 'URL'        => dirname($_SERVER['PHP_SELF']) . '/../files2/',
		// 	'alias'      => 'File system',
		// 	'mimeDetect' => 'internal',
		// 	'tmbPath'    => '.tmb',
		// 	'utf8fix'    => true,
		// 	'tmbCrop'    => false,
		// 	'startPath'  => '../files/test',
		// 	// 'separator' => ':',
		// 	'attributes' => array(
		// 		array(
		// 			'pattern' => '~/\.~',
		// 			// 'pattern' => '/^\/\./',
		// 			'read' => false,
		// 			'write' => false,
		// 			'hidden' => true,
		// 			'locked' => false
		// 		),
		// 		array(
		// 			'pattern' => '~/replace/.+png$~',
		// 			// 'pattern' => '/^\/\./',
		// 			'read' => false,
		// 			'write' => false,
		// 			// 'hidden' => true,
		// 			'locked' => true
		// 		)
		// 	),
		// 	// 'defaults' => array('read' => false, 'write' => true)
		// ),
		
		// array(
		// 	'driver' => 'FTP',
		// 	'host' => '192.168.1.38',
		// 	'user' => 'dio',
		// 	'pass' => 'hane',
		// 	'path' => '/Users/dio/Documents',
		// 	'tmpPath' => '../files/ftp',
		// 	'utf8fix' => true,
		// 	'attributes' => array(
		// 		array(
		// 			'pattern' => '~/\.~',
		// 			'read' => false,
		// 			'write' => false,
		// 			'hidden' => true,
		// 			'locked' => false
		// 		),
		// 		
		// 	)
		// ),
		// array(
		// 	'driver' => 'FTP',
		// 	'host' => '10.0.1.3',
		// 	'user' => 'frontrow',
		// 	'pass' => 'frontrow',
		// 	'path' => '/',
		// 	'tmpPath' => '../files/ftp',
		// ),
		
		// array(
		// 	'driver'     => 'LocalFileSystem',
		// 	'path'       => '../files2/',
		// 	'URL'        => dirname($_SERVER['PHP_SELF']) . '/../files2/',
		// 	'alias'      => 'Files',
		// 	'mimeDetect' => 'internal',
		// 	'tmbPath'    => '.tmb',
		// 	// 'copyOverwrite' => false,
		// 	'utf8fix'    => true,
		// 	'attributes' => array(
		// 		array(
		// 			'pattern' => '~/\.~',
		// 			// 'pattern' => '/^\/\./',
		// 			// 'read' => false,
		// 			// 'write' => false,
		// 			'hidden' => true,
		// 			'locked' => false
		// 		),
		// 	)
		// ),
		
		// array(
		// 	'driver' => 'MySQL',
		// 	'path' => 1,
		// 	// 'treeDeep' => 2,
		// 	// 'socket'          => '/opt/local/var/run/mysql5/mysqld.sock',
		// 	'user' => 'root',
		// 	'pass' => 'hane',
		// 	'db' => 'elfinder',
		// 	'user_id' => 1,
		// 	// 'accessControl' => 'access',
		// 	// 'separator' => ':',
		// 	'tmbCrop'         => true,
		// 	// thumbnails background color (hex #rrggbb or 'transparent')
		// 	'tmbBgColor'      => '#000000',
		// 	'files_table' => 'elfinder_file',
		// 	// 'imgLib' => 'imagick',
		// 	// 'uploadOverwrite' => false,
		// 	// 'copyTo' => false,
		// 	// 'URL'    => 'http://localhost/git/elfinder',
		// 	'tmpPath' => '../filesdb/tmp',
		// 	'tmbPath' => '../filesdb/tmb',
		// 	'tmbURL' => dirname($_SERVER['PHP_SELF']) . '/../filesdb/tmb/',
		// 	// 'attributes' => array(
		// 	// 	array(),
		// 	// 	array(
		// 	// 		'pattern' => '/\.jpg$/',
		// 	// 		'read' => false,
		// 	// 		'write' => false,
		// 	// 		'locked' => true,
		// 	// 		'hidden' => true
		// 	// 	)
		// 	// )
		// 	
		// )
	)
		
);

// sleep(3);
header('Access-Control-Allow-Origin: *');
$connector = new elFinderConnectorUG(new elFinderUG($opts), true);
$connector->run();

// echo '<pre>';
// print_r($connector);