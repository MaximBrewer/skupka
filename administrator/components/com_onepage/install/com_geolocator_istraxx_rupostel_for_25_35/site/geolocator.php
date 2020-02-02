<?php
/**
 * @version		$Id: geolocator.php 115 2012-01-03 11:31:41Z stAn $
 * @package		GeoLocator
 * @copyright	Copyright (C) 2006 - 2012 RuposTel.com
 * @license		GNU/GPL
 * GeoLocator is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined('_JEXEC') or die ('Restricted access');

include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php');
if (class_exists('geoHelper')) 
echo 'You are from: '.geoHelper::getCountry();

?>