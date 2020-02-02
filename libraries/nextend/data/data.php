<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.json.json');

class NextendData {
    
    var $_data;
    
    function NextendData() {
        
        $this->_data = array();
    }
    
    function loadJSON($json) {
        $this->_data = json_decode($json, true);
        if(!is_array($this->_data)) $this->_data = array();
    }
    
    function loadArray($array) {
        if(!$this->_data) $this->_data = array();
        if(is_array($array))
            $this->_data = array_merge($this->_data, $array);
    }
    
    function toJSON() {

        return json_encode($this->_data);
    }
    
    function toArray() {
        return $this->_data;
    }
    
    function get($key, $default = '') {

        if (isset($this->_data[$key])) return $this->_data[$key];
        return $default;
    }
    
    function set($key, $value) {

        $this->_data[$key] = $value;
    }
}
