<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

$dispatcher = JDispatcher::getInstance();

$dispatcher->trigger( 'onInitNextendLibrary' );


function nextendimportsmartslider2($key) {
    nextendimport($key);
}

function nextendimportaccordionmenu($key) {
    nextendimport($key);
}

function nextendSubLibraryPath($subLibrary) {
    return NEXTENDLIBRARY . $subLibrary . '/';
}