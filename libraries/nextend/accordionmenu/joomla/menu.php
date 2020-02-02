<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.accordionmenu.menu');
nextendimport('nextend.data.data');
nextendimport('nextend.parse.parse');

class NextendMenuJoomla extends NextendMenu {

    var $_data;
    var $_module;

    function NextendMenuJoomla(&$module, &$params, $path) {
        parent::NextendMenu($path);
        $this->_data = new NextendData();
        $this->id = $module->id;
        $config = $params->toArray();
        $this->_data->loadArray(version_compare(JVERSION, '1.6.0', 'l') ? $config : $config['config']);
        
        
        $cacheby = (array)explode('||', $this->_data->get('ajax_cacheby', 'acl'));
        
        $user = JFactory::getUser();
        if(in_array('acl', $cacheby)){
           $this->_cachehash.= (version_compare(JVERSION, '1.6.0', 'ge') ? json_encode($user->getAuthorisedViewLevels()) : $user->aid.'-'.$user->gid).'-'; 
        }
        if(in_array('userid', $cacheby)){
           $this->_cachehash.= $user->id.'-'; 
        }
        if(in_array('language', $cacheby)){
            $lang = JFactory::getLanguage();
            $this->_cachehash.= $lang->getTag().'-'; 
        }
        $ajax_moduleid = $this->_data->get('ajax_moduleid', '');
        if(!$ajax_moduleid){
            $this->_cachemoduleid = $module->id;
        }else{
            $this->_cachemoduleid = $ajax_moduleid;
        }
        
        $this->_cachehash.=$this->_cachemoduleid;
        
        $this->_module = $module;
        $this->setThemePath();
        $this->setInstance();
    }

    function setInstance() {
        $this->_instance = $this->_module->id;
    }

    function getTreeInstance() {
        $type = $this->_data->get('type', 'joomla');
        JPluginHelper::importPlugin('nextendmenu', $type);
        $class = 'plgNextendMenu' . $type;
        if (!class_exists($class)) {
            echo 'Error in menu type!';
            return false;
        }
        $dispatcher = JDispatcher::getInstance();
        $class = new $class($dispatcher);
        $this->_typepath = $class->getPath();
        require_once($this->_typepath . 'menu.php');
        $class = 'NextendTree' . $type;
        return new $class($this, $this->_module, $this->_data);
    }
    
    function _render(){
        $cacheval = NextendParse::parse($this->_data->get('menucache', '0|*|300000'));
        if ($cacheval[0]) {
            $cache = JFactory::getCache('mod_accordionmenu', 'callback', 'file');
            $cache->setCaching(1);
            $cache->setLifeTime($cacheval[1]);
            $user = JFactory::getUser();
            echo $cache->call(array($this, '__render'), md5(serialize($_GET).serialize($user->getAuthorisedViewLevels())));
        } else {
            echo $this->__render();
        }
    }
    
    function __render($hash = ''){
        ob_start();
        parent::_render();
        return ob_get_clean();
    }

    function setThemePath() {
        $theme = $this->_data->get('theme', 'default');
        JPluginHelper::importPlugin('nextendmenutheme', $theme);
        $class = 'plgNextendMenutheme' . $theme;
        if (!class_exists($class)) {
            echo 'Error in menu theme!';
            return false;
        }
        $dispatcher = JDispatcher::getInstance();
        $class = new $class($dispatcher);
        $this->_themePath = $class->getPath();
    }

    function getTitle() {
        return $this->_module->title;
    }
    
    function getAjaxUrl(){
        return JUri::root(true).'/';
    }

}