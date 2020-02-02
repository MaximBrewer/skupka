<?php
//сохранение информации
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

$this_tablename = '#__virtuemart_product_custom_plg_uploadphoto';
$db = JFactory::getDbo();
$query = $db->getQuery(true);

if(isset($_GET['action'])&&$_GET['action']=="get"){

    $page_id=$_GET['page_id'];
    $query = $db->getQuery(true)
        ->select('uploadphoto')
        ->from($db->quoteName($this_tablename))
        ->where($db->quoteName('virtuemart_product_id') . ' = ' .$page_id);
    $db->setQuery($query);
    $result =$db->loadResult();
//    var_dump($result);
    if($result){
        echo $result;
    }else{
        echo false;
    }
    die();
}

//var_dump($_POST);
if(isset($_POST['action'])&&$_POST['action']=="save"){
    $page_id=$_POST['page_id'];
    $query = $db->getQuery(true)
        ->select('id')
        ->from($db->quoteName($this_tablename))
        ->where($db->quoteName('virtuemart_product_id') . ' = ' .$page_id);
    $db->setQuery($query);
    $result =$db->loadResult();
    if($result){
        // обновляем
        $data = new stdClass();
        $data->id =intval($result);
        $data->virtuemart_product_id =$page_id;
        $data->uploadphoto=$_POST['items'];
        $result = $db->updateObject($this_tablename, $data, 'id');
       if ($result){
           echo json_encode(array('suc'=>true));
           die();
       }else{
           echo json_encode(array('suc'=>false));
           die();
       }
    }else{
        // вставляем
        $data = new stdClass();
        $data->virtuemart_product_id =$page_id;
        $data->uploadphoto=$_POST['items'];
        $result = $db->insertObject($this_tablename, $data);

        if ($result){
            echo json_encode(array('suc'=>true));
            die();
        }else{
            echo json_encode(array('suc'=>false));
            die();
        };


    }

}