<?php
/**
 * Favorites View for com_wishlist Component
 * 
 * @package    Favorites & Wishlist
 * @subpackage com_wishlist
 * @license  GNU/GPL v2
 * @Copyright (C) 2013 2KWeb Solutions. All rights reserved.
 * This program is distributed under the terms of the GNU General Public License
 *
 */

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Favorites Component
 *
 * @package	Joomla.Components
 * @subpackage	Favorites
 */
class FavoritesViewFavoritessh extends JViewLegacy
{
	function display($tpl = null){
		$params = &JComponentHelper::getParams( 'com_wishlist' );
		$lic_validation = $params->get( 'lic_validation' );
		$app =& JFactory::getApplication();
		/*
		$params =& JComponentHelper::getParams( 'com_wishlist' );
		$params =& $app->getParams( 'com_wishlist' );	
		$dummy = $params->get( 'dummy_param', 1 ); 
		*/
		
		$data = $this->get('Data');
		$this->assignRef('data', $data);

		parent::display($tpl);
	}
}
?>
