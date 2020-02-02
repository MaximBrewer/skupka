<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendView {

    var $_controller;
    var $_path;
    var $_fragmentsPath;

    function NextendView($controller) {
        $this->_controller = $controller;
    }

    function defaultAction($tpl) {

        $this->render($tpl);
    }

    function noaccessAction($tpl) {
        $this->render($tpl);
    }

    function display($action, $tpl) {
        $action = $action . 'Action';
        if (method_exists($this, $action)) {
            $this->$action($tpl);
        }
    }

    function render($tpl) {
        $tplpath = $this->_path . 'tpl/' . $tpl . '.php';
        if (NextendFilesystem::existsFile($tplpath)) {
            include $tplpath;
        }
    }

    function loadFragment($file) {
        include $this->_controller->_fragmentsPath . str_replace('.','',$file) . '.php';
    }

    function route($query) {
        return $this->_controller->route($query);
    }

    function canDo($action, $key = null) {
        return $this->_controller->canDo($action, $key);
    }

    function getModel($model) {
        return $this->_controller->getModel($model);
    }

}

?>
