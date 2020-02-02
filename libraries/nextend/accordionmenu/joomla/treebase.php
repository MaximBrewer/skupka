<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.accordionmenu.treebase');

global $nextend_menu_loadposition;
$nextend_menu_loadposition = array();

class NextendTreebaseJoomla extends NextendTreebase {

    function initConfig() {
        parent::initConfig();
        $this->loadposition = $this->_data->get('loadposition', 0);

        if ($this->loadposition) {
            nextendimport('nextend.accordionmenu.joomla.loadmodule');
        }
    }

    function filterItemTree(&$item, &$content) {
        global $nextend_menu_loadposition;
        if ($this->loadposition == 1) {
            $regex = '/{loadposition\s+(.*?)}/i';
            preg_match($regex, $item->nname, $match);

            // No matches, skip this
            if ($match) {
                $item->p = true;
                $tmp = str_replace($match[0], '', $item->nname);
                $nextend_menu_loadposition[] = array($item->nname, $tmp);
                $item->nname = $tmp;
                $content = "<div>" . NextendLoadModule::parse($match[1]) . "</div>";
            }
        }
    }
}
