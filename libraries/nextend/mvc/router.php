<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendRouterAbstract{
    
    var $_baseurl;
    
    static function getInstance() {

        static $instance;
        if (!is_object($instance)) {
            $instance = new NextendRouter();
        }
        return $instance;
    }
    
    function route($query){
        if(strpos($this->_baseurl, '?') === false){
            return $this->_baseurl.'?'.$query;
        }
        return $this->_baseurl.'&'.$query;
    }
}

if (nextendIsJoomla()) {
    nextendimport('nextend.mvc.router.joomla');
} elseif (nextendIsWordPress()) {
    nextendimport('nextend.mvc.router.wordpress');
}elseif (nextendIsMagento()) {
    nextendimport('nextend.mvc.router.magento');
}else{
    nextendimport('nextend.mvc.router.default');
}
?>
