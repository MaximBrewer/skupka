<?php
/* 
* php-ga is lincensed under GNU Lesser GPL as referenced here: https://code.google.com/p/php-ga/ 
*/


/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

spl_autoload_register(function($className) {
	if($className[0] == '\\') {
		$className = substr($className, 1);
	}
	
	// Leave if class should not be handled by this autoloader
	if(strpos($className, 'UnitedPrototype\\GoogleAnalytics') !== 0) return;
	
	$classPath = strtr(substr($className, strlen('UnitedPrototype')), '\\', '/') . '.php';
	
	if(file_exists(__DIR__ . $classPath)) {
		require(__DIR__ . $classPath);
	}
});
