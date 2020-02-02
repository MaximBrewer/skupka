<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendDatabaseAbstract {
    
    static function getInstance() {

        static $instance;
        if (!is_object($instance)) {
            $instance = new NextendDatabase();
        }
        return $instance;
    }

    function insert($table, $params) {
        $columns = array();
        $values = array();
        foreach($params AS $k => $p){
            $columns[] = $this->quoteName($k);
            $values[] = $this->quote($p);
        }
        $sql = "INSERT INTO ".$table." (".implode(',',$columns).") VALUES (".implode(',',$values).")";
        $this->setQuery($sql);
        $this->query();
    }

    function update($table, $params, $where = null) {
        $s = array();
        foreach($params AS $k => $p){
            $s[] = $this->quoteName($k). '='.$this->quote($p);
        }
        if($where != null) $where = ' WHERE '.$where;
        $sql = "UPDATE ".$table." SET ".implode(',',$s).$where;
        $this->setQuery($sql);
        $this->query();
    }
}

if (nextendIsJoomla()) {
    nextendimport('nextend.database.joomla');
} elseif (nextendIsWordPress()) {
    nextendimport('nextend.database.wordpress');
}elseif (nextendIsMagento()) {
    nextendimport('nextend.database.magento');
}else{
    nextendimport('nextend.database.default');
}

