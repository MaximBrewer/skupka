<?php
/**
 * Favorites View for Favorites Component
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

jimport( 'joomla.application.component.view' );

/**
 * Favorites View
 *
 * @package    Joomla.Components
 * @subpackage 	Favorites
 */
class FavoritesViewFavoriteslist extends JViewLegacy
{
	/**
	 * Favoriteslist view display method
	 * @return void
	 **/
	function display($tpl = null){
		$app =& JFactory::getApplication();

		// Get data from the model
		$rows = & $this->get( 'Data');
		
		// draw menu
		JToolBarHelper::title(   JText::_( 'FAVORITES_MANAGER' ), 'wishlist_icon_main.png' );
		JToolBarHelper::editList();
		JToolBarHelper::addNew();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();
		if(isset($rows[0]->published)){
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
		}
		// configuration editor for config.xml
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_wishlist', '550');

		$this->assignRef('rows', $rows );
		$pagination =& $this->get('Pagination');
		$this->assignRef('pagination', $pagination);

		// SORTING get the user state of order and direction
		$default_order_field = 'fav_id';
		$lists['order_Dir'] = $app->getUserStateFromRequest('com_wishlistfilter_order_Dir', 'filter_order_Dir', 'ASC');
		$lists['order'] = $app->getUserStateFromRequest('com_wishlistfilter_order', 'filter_order', $default_order_field);
		$lists['search'] = $app->getUserStateFromRequest('com_wishlistsearch', 'search', '');
		$this->assignRef('lists', $lists);


		parent::display($tpl);
	}
}