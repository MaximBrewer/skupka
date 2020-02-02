<?php
/**
 * @package Unite Gallery
 * @author UniteCMS.net / Valiano
 * @copyright (C) 2012 Unite CMS, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('_JEXEC') or die('Restricted access');


include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderConnector.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinder.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeDriver.class.php';
include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeLocalFileSystem.class.php';
// Required for MySQL storage connector
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeMySQL.class.php';
// Required for FTP connector support
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';


/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}

function runElfinderConnector(){
	
	// Documentation for connector options:
	// https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
	$opts = array(
	// 'debug' => true,
			'roots' => array(
					array(
							'driver'        => 'LocalFileSystemUG',   // driver for accessing file system (REQUIRED)
							'path'          => GlobalsUG::$path_images,         // path to files (REQUIRED)
							'URL'           => GlobalsUG::$url_images, // URL to files (REQUIRED)
							'accessControl' => 'access'             // disable and hide dot starting files (OPTIONAL)
					)
			)
	);
	
	// run elFinder
	$connector = new elFinderConnectorUG(new elFinderUG($opts));
	$connector->run();
}