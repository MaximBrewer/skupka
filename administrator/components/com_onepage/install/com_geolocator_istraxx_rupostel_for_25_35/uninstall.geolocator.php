<?php
/**
 * @version		$Id: install.php 115 2012-01-03 11:31:41Z stAn $
 * @package		GeoLocator
 * @copyright	Copyright (C) 2006 - 2012 RuposTel.com
 * @license		GNU/GPL
 * GeoLocator is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined('_JEXEC') or die ('Restricted access');
 
 /**
 * Uninstall function
 * @return
 */
function com_uninstall()
{
        
    	$db = JFactory::getDBO();
    	$q="drop table if exists #__geodata ";
    	$db->setQuery($q);
    	$db->query();
    
}
?> 