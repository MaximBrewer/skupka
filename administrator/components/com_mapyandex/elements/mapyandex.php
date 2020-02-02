<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */

defined('_JEXEC') or die( 'Restricted access' );

class JElementMapYandex extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'mapyandex';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db =& JFactory::getDBO();
		var_dump($control_name);
		
		$query = "SELECT * FROM ".$db->nameQuote('#__map_yandex');
		$db->setQuery( $query );
		$options = $db->loadObjectList();

		

		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'id', 'name_map_yandex', $value, $control_name.$name );
	}
}
