<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendPluginAbstract{
    static $classes = array();
    
    static function addPlugin($group, $class){
        if(!isset(self::$classes[$group])) self::$classes[$group] = array();
        if(!is_object($class)) $class = new $class();
        self::$classes[$group][] = $class;
    }
    
    static function callPlugin($group, $method, $args = null){
        if(isset(self::$classes[$group])){
            foreach(self::$classes[$group] AS $class){
                if(is_callable(array($class, $method))){
                    call_user_func_array(array($class, $method), $args);
                }
            }
        }
    }
}

if (nextendIsJoomla()) {
    nextendimport('nextend.plugin.joomla');
} elseif (nextendIsWordPress()) {
    nextendimport('nextend.plugin.wordpress');
}elseif (nextendIsMagento()) {
    nextendimport('nextend.plugin.magento');
}else{
    nextendimport('nextend.plugin.default');
}
?>
