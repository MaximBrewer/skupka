<?php
/**
 *  @package     Joomla.Plugin
 * @subpackage  System.maskedinput
 *
 * @copyright   Copyright © 2014 Beagler.ru. All rights reserved.
 * @license     GNU General Public License version 3 or later;
 */

// No direct access
defined( '_JEXEC' ) or die;

class plgsystemmaskedinputInstallerScript
{

	function postflight( $type, $parent )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->update( '#__extensions' )->set( 'enabled=1' )->where( 'type=' . $db->q( 'plugin' ) )->where( 'element=' . $db->q( 'maskedinput' ) );
		$db->setQuery( $query )->execute();

	}
}