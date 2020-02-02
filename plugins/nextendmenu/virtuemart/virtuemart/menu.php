<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.accordionmenu.joomla.treebase');

require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'version.php');
$VMVERSION = new vmVersion();
$version = property_exists($VMVERSION, 'RELEASE') && isset($VMVERSION->RELEASE) ? $VMVERSION->RELEASE : vmVersion::$RELEASE;
if (version_compare($version, '2.0.0', 'ge') ) {
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'virtuemart2.php';
}else{
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'virtuemart1.php';
}