<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.form.element.subform');

class NextendElementPlugingroup extends NextendElementSubform {

    var $_group = null;

    var $_list = null;

    function getOptions() {
        $this->loadList();
        $list = array();
        foreach ($this->_list AS $k) {
            foreach ($k AS $kk => $vv) {
                $list[] = $kk;
            }
        }
        sort($list);
        if (!in_array($this->_value, $list)) $this->_value = $list[0];
        return $list;
    }

    function setOptions($options) {

        foreach ($this->_group AS $gk => $gv) {
            $group = $this->_xml->addChild('optgroup', '');
            $group->addAttribute('label', htmlspecialchars($gv));
            foreach ($this->_list[$gk] AS $k => $v) {
                $group->addChild('option', htmlspecialchars($v[0]))->addAttribute('value', $k);
            }
        }
    }

    function getSubFormfolder($value) {
        $this->loadList();
        $v = explode('_', $value);
        return $this->_list[$v[0]][$value][1];
    }

    function onRender() {
        $php = NextendXmlGetAttribute($this->_xml, 'php');
        if ($php) {
            $v = explode('_', $this->_value);
            require_once($this->_list[$v[0]][$this->_value][1] . NextendXmlGetAttribute($this->_xml, 'php'));

            $class = 'NextendGenerator'.$this->_value;
            $generator = new $class($this->_form);
            $generator->initAdmin();
        }
    }

    function loadList() {
        if ($this->_list == null) {
            $this->_group = array();
            $this->_list = array();
            NextendPlugin::callPlugin(NextendXmlGetAttribute($this->_xml, 'plugingroup'), NextendXmlGetAttribute($this->_xml, 'method'), array(&$this->_group, &$this->_list));
            
            $v = explode('_', $this->_value);
            if (!isset($this->_list[$v[0]][$this->_value])) {
                $keys = array_keys($this->_list);
                $ks = array_keys($this->_list[$keys[0]]);
                $this->_value = $this->_list[$keys[0]][$ks[0]];
            }
        }
    }

}