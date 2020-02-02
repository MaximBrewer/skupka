<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.tab');
nextendimport('nextend.form.tabs.tabbed');

class NextendTabGroupped extends NextendTabTabbed {

    var $_tabs;
    
    function render($control_name) {
        $this->initTabs();      
        foreach($this->_tabs AS $tabname => $tab) {
            $tab->render($control_name);
        }
    }
}