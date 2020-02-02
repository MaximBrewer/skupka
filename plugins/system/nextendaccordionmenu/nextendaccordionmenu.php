<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
jimport('joomla.plugin.plugin');

class plgSystemNextendAccordionMenu extends JPlugin {

    static $request = null;
    
    function onAfterRoute(){
        self::$request = $_REQUEST;
    }
    
}
if(isset($_REQUEST['nextendaccordionmenuajax'])){

    if (isset($_REQUEST['moduleid']) && isset($_REQUEST['parent'])) {
        /*$expires = 60 * 15;        // 15 minutes
        header('Pragma: public');
        header('Cache-Control: maxage=' . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');*/
        //sleep(2);
      	global $menu;
        jimport('nextend.library');
      	
      	$db = JFactory::getDbo();
      	
      	$db->setQuery("select * from #__modules where id=".intval($_REQUEST['moduleid'])." limit 1");
      
      	$module = $db->loadObjectList();
      	$module = $module[0];
      	
      	$params = new JRegistry;
      	$params->loadString($module->params);
      	nextendimport('nextend.accordionmenu.joomla.menu');
      	$menu = new NextendMenuJoomla($module, $params, dirname(__FILE__));
      	
      	$tree = $menu->getTreeInstance();
        
        $cache = NextendCacheData::getInstance();
        echo $cache->cache('mod_accordionmenu', $tree->ajaxlifetime, array($tree, 'renderChild'), array($_REQUEST['parent'], $tree->_menu->_cachehash.'-'.$tree->_menu->_cachemoduleid));
        
      	die;
    }else{
        header("HTTP/1.0 405 Method Not Allowed");
        die('Unable to load the menu items for this parent!');
    }

}
?>