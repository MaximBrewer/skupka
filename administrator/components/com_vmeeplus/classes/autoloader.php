<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @copyright    Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @package		This file is part of InteraMind VM Email Manager Plus Component
 **/

function empAutoload($class) {
	
	if(JLoader::load($class)) {
		return true;
	}
	
	$parts = explode('_', $class);
	if (count($parts) > 1 && $parts[0] == 'emp') {
		$prefix = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'classes';
		$main = "";
		for ($i = 1; $i < sizeof($parts)-1; $i++) {
			$main = $main.DIRECTORY_SEPARATOR.$parts[$i];
		}
		$filename = $parts[sizeof($parts)-1];
		$path = $prefix.$main.DIRECTORY_SEPARATOR.$filename.".php";
		if (file_exists($path)) {
			require_once($path);
			return true;
		} else {
			$prefix = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'classes';
			$path = $prefix.$main.DIRECTORY_SEPARATOR.$filename.".php";
			if (file_exists($path)) {
				require_once($path);
				return true;
			}
		}
	}
	elseif(count($parts) == 1 && stripos($parts[0], 'table') === 0){
		//check if it is table
		$filename = substr($parts[0], 5);
		$prefix = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_vmeeplus'.DIRECTORY_SEPARATOR.'tables';
		$path = $prefix.DIRECTORY_SEPARATOR.strtolower($filename).".php";
		if (file_exists($path)) {
			require_once($path);
			return true;
		}
	}
	return false;
}

spl_autoload_register("empAutoload");
