<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendUri extends NextendUriAbstract{
    
    function NextendUri(){
        $this->_baseuri = JURI::root();
        
        $this->_currentbase = JURI::base();
        
        $this->_relative = $this->find_relative_path($this->_currentbase, $this->_baseuri);
        
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') {
            $this->_baseuri = str_replace('http://', 'https://', $this->_baseuri);
        }
    }
    
    static function ajaxUri($query = '', $magento = 'nextendlibrary'){
        return JUri::current();
    }
    
}