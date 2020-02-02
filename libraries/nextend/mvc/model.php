<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
class NextendModel{
    var $_controller;
    
    function  NextendModel($controller){
       $this->_controller =  $controller;
    }

    function route($query) {
        return $this->_controller->route($query);
    }

    function canDo($action, $key = null){
        return $this->_controller->canDo($action, $key);
    }

    function getModel($model) {
        return $this->_controller->getModel($model);
    }
}
?>
