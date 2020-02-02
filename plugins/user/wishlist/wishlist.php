<?php
/**
 * @package    Favorites & Wishlist
 * @subpackage com_wishlist
 * @license  GNU/GPL v2
 * @Copyright (C) 2013 2KWeb Solutions. All rights reserved.
 * This program is distributed under the terms of the GNU General Public License
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
class plgUserWishlist extends JPlugin
{
	public function onUserAfterDelete($user, $succes, $msg)
	{
		if (!$succes) {
			return false;
		}

		$db = JFactory::getDbo();
		$db->setQuery(
			'DELETE FROM ' .$db->quoteName('#__virtuemart_favorites'). 'WHERE '
			.$db->quoteName('user_id'). '=' .(int) $user['id']
		);
		$db->Query();

		return true;
	}


	public function onUserLogin($user, $options = array())
	{
		$merge_list = $this->params->get('merge_list', 1);
		if($merge_list == 1) {
			$db = JFactory::getDBO();
			$userID = JUserHelper::getUserId($user['username']);
			// Update the user related fields for the Joomla sessions table.
			$db->setQuery(
			'SELECT'.$db->quoteName('product_id').
			',' .$db->quoteName('user_id').
			' FROM' .$db->quoteName('#__virtuemart_favorites').
			' WHERE' .$db->quoteName('user_id'). '='
				.$db->Quote($_COOKIE['virtuemart_wish_session'])
			);
			$items = $db->loadObjectList();
			if (count($items) > 0)
			{
				foreach ($items as $item) {
					$db->setQuery(
					'SELECT ' .$db->quoteName('fav_id'). ' FROM '.$db->quoteName('#__virtuemart_favorites').
					' WHERE ' .$db->quoteName('user_id'). '=' .$db->Quote($userID).
					' AND ' .$db->quoteName('product_id'). '=' .$db->Quote($item->product_id)
					);
					if (count($db->loadObjectList()) == 0)
					{
						$db->setQuery(
						'UPDATE '.$db->quoteName('#__virtuemart_favorites').
						'SET '.$db->quoteName('user_id'). '=' .$db->Quote($userID).
						','.$db->quoteName('isGuest'). '= 0 WHERE' .$db->quoteName('user_id'). '='.
						$db->Quote($item->user_id)
						);
						$db->query();
					}
				  }
				  $db->setQuery(
						'DELETE FROM '.$db->quoteName('#__virtuemart_favorites').
						' WHERE' .$db->quoteName('user_id'). '='.
						$db->Quote($_COOKIE['virtuemart_wish_session'])
						);
				 $db->query();
				 // Load user_joomla plugin language (not done automatically).
				 $lang = JFactory::getLanguage();
				 $lang->load('plg_user_wishlist', JPATH_ADMINISTRATOR);
				 JFactory::getApplication()->enqueueMessage(JText::_(''));
			}
		}
		return true;
	}
}