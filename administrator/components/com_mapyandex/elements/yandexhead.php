<?php
/*
 * @package Joomla 1.5
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @component Yandex Map Component
 * @copyright Copyright (C) Aleksandr Ermakov www.slyweb.ru
 */
defined('_JEXEC') or die();

class JElementYandexHead extends JElement
{
	var	$_name = 'YandexHead';

	function fetchTooltip($label, $description, &$node, $control_name, $name) {
		return '&nbsp;';
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		if ($value) {
			return '<p style="background: #CCE6FF;color: #0069CC;padding:5px"><strong>' . JText::_($value) . '</strong></p>';
		} else {
			return '<hr />';
		}
	}
}
?>