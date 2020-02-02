<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: token, Content-Type');
header('Access-Control-Max-Age: 1728000');
header('Content-Length: 0');
header('Content-Type: text/plain');


date_default_timezone_set('Europe/Moscow');

const _JEXEC = 1;


ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define('JPATH_BASE', realpath(dirname(__FILE__) . '/../../../../'));
require_once JPATH_BASE . '/includes/defines.php';


// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Load the configuration
require_once JPATH_CONFIGURATION . '/configuration.php';

require_once JPATH_BASE . '/includes/framework.php';

$plugin = JPluginHelper::getPlugin('vmcustom', 'uploadproductfile');
if ($plugin) {
    $pluginParams = new JRegistry($plugin->params);
    $plugin_folders = $pluginParams->get('plugin_folders');
} else {
    $plugin_folders = "phototexture";
}

$image_path = JPATH_BASE . '/images/' . $plugin_folders;
if (!file_exists($image_path)) {
    mkdir($image_path, 0777, true);
}
jimport('joomla.filesystem.folder');
$url = str_replace(JURI::root(true), "", JURI::root());

// получить все фото
if (isset($_GET['action']) && $_GET['action'] == "get_catalog") {
    $images = array('path' => $url.'images/' . $plugin_folders . '/', 'images' => array());
    $items = JFolder::files($image_path, $filter = '.jpg');
    if ($items) {
        $images['images'] = $items;
    }
    echo json_encode($images);
    die();
}

if(isset($_POST)&&isset($_FILES['file'])){

    $file = $_FILES['file'];
    jimport('joomla.filesystem.file');

    $filename = JFile::makeSafe($file['name']);
    $ext =  JFile::getExt($filename);
    $src = $file['tmp_name'];


    $dest = $image_path .'/' . time().'.'.$ext;

    if (JFile::upload($src, $dest))
    {
        // Redirect to a page of your choice
    }
    else
    {
        // Redirect and throw an error message
    }
    die();

}

// удаление фото
if(isset($_POST['action'])&&$_POST['action']=='delete'){
   $item=$_POST['item'];
   if(file_exists($image_path.'/'.$item)){
       jimport('joomla.filesystem.file');
       JFile::delete($image_path.'/'.$item);
       echo json_encode(array('suc'=>true));
   }else{
       echo json_encode(array('suc'=>false));
   }
}