<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendDatabaseJoomlaAbstract extends NextendDatabaseAbstract {

    var $db = null;

    function NextendDatabaseJoomlaAbstract() {
        $this->db = JFactory::getDBO();
    }

    function setQuery($query) {
        $this->db->setQuery($query);
    }

    function query() {
        $this->db->query();
    }

    function loadAssoc() {
        return $this->db->loadAssoc();
    }

    function loadAssocList($key = null) {
        return $this->db->loadAssocList($key);
    }
    
    function escape($s){
        return $this->db->escape($s);
    }

    function quote($s) {
        return $this->db->quote($s);
    }

    function quoteName($s) {
        return $this->db->quoteName($s);
    }
    
    function insertid(){
        return $this->db->insertid();
    }

}

if (version_compare(JVERSION, '2.5.0', 'l')) {

    class NextendDatabase extends NextendDatabaseJoomlaAbstract {

        function quote($s) {
            return $this->db->Quote($s);
        }

        function quoteName($s) {
            return $this->db->nameQuote($s);
        }

    }
}else{

    class NextendDatabase extends NextendDatabaseJoomlaAbstract {

    }
}
?>
