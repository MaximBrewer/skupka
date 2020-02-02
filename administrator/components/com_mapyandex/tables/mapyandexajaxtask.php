<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include library dependencies
jimport('joomla.filter.input');
// import Joomla table library
jimport('joomla.database.table');
class TableMapYandexAjaxTask extends JTable
{

	function __construct(& $db) {
		parent::__construct('#__map_yandex', 'id', $db);
	}
	

}
?>