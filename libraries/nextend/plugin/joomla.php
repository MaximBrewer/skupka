<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

jimport('joomla.plugin.plugin');

class NextendPluginBase extends JPlugin{

}

class NextendPlugin extends NextendPluginAbstract{
    
    static function addPlugin($group, $class){
        if(!isset(self::$classes[$group])) self::$classes[$group] = array();
        self::$classes[$group][] = $class;
    }
    
    static function callPlugin($group, $method, $args = null){
        JPluginHelper::importPlugin( $group );    
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger($method, $args);
    }
}