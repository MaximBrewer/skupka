<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
// No direct access
defined ('_JEXEC') or die ('resticted aceess');
$show_vmmenu 	= $params->get('show_vmmenu', 1);
$vmMenu="";
$user = JFactory::getUser();
$lang = JFactory::getLanguage();
		$version = new JVersion;
		$joomla = $version->getShortVersion();
		$jversion = substr($joomla,0,3);
if ($show_vmmenu) {
	$hideMainmenu=false;
}
if ($vdataComponentItems) {
	$class = '';
	if ($hideMainmenu) {
		$class = "disabled";
	}
	if($jversion<3){
		$vmMenu='<ul id="menu">';
		$vmMenu.='<li class="node '.$class.'"><a href="'.$vdataComponentItems->link.'">'.$vdataComponentItems->text.'</a>';
	} else{
		$vmMenu='<ul id="vm-menu" class="nav '.$class.'" >';
		$vmMenu.='<li class="dropdown" ><a class="dropdown-toggle" data-toggle="dropdown" href="#">'.$vdataComponentItems->text.'<span class="caret"></span></a>';
	}

	if (!$hideMainmenu) {
		if (!empty($vdataComponentItems->submenu)) {
			if($jversion<3){
				$vmMenu.='<ul>';
			} else {
				$vmMenu.='<ul class="dropdown-menu">';
			}

			foreach ($vdataComponentItems->submenu as $sub) {
				$vmMenu.='<li><a class="'.$sub->class.'" href="'.$sub->link.'">'.$sub->text.'</a></li>';
			}
			$vmMenu.='</ul>';
		}
	}
	$vmMenu.='</li></ul>';
}


echo $vmMenu;