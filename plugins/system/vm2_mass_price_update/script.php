<?php
// No direct access
defined( '_JEXEC' ) or die;

class plgsystemvm2_mass_price_updateInstallerScript
{

	function postflight( $type, $parent )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->update( '#__extensions' )->set( 'enabled=1' )->where( 'type=' . $db->q( 'plugin' ) )->where( 'element=' . $db->q( 'vm2_mass_price_update' ) );
		$db->setQuery( $query )->execute();

	}
}