<?php 
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
defined('_JEXEC') or die('Restricted access');

require_once(__DIR__.DIRECTORY_SEPARATOR.'checkout.controller.php'); 
$checkoutController = new checkoutControllerOpc(); 
/*
$path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'hika'; 
	if (method_exists($controller, 'addViewPath')) { 
					$checkoutController->addViewPath($path);
				}
				else
				if (method_exists($controller, 'addIncludePath')) {
					$checkoutController->addIncludePath($path);
				}
				*/
$checkoutController->show(); 
