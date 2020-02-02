<?php
/* 
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* this is loaded from: \components\com_onepage\helpers\cache.php
* public static function storeShipingCalculation
*
* this option searches IDs of those plugins that do not change their calculation per payment selected
*/
defined('_JEXEC') or die('Restricted access');
$cache_only = array('usps', 'ups', 'fedex', 'acs');