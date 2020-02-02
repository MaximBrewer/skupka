<?php
/**
 * Favorites Controller for Favorites Component
 * 
 * @package    Favorites & Wishlist
 * @subpackage com_wishlist
 * @license  GNU/GPL v2
 * @Copyright (C) 2013 2KWeb Solutions. All rights reserved.
 * This program is distributed under the terms of the GNU General Public License
 *
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Favorites Model
 *
 * @package    Joomla.Components
 * @subpackage 	Favorites
 */
class FavoritesController extends JControllerLegacy
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	public function display(){
		parent::display();
	}
}