<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
if (! class_exists('MapYandexLoader')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_mapyandex'.DS.'libraries'.DS.'loader.php');
}
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldMapYandexAllMaps extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'MapYandexAllMaps';
 
	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public function getOptions()
	{
		// Initialize variables.
		$options = array();
 
		$db	= JFactory::getDbo();
		$query	= $db->getQuery(true);
 
        $query->select('id As value, name_map_yandex As name');
		$query->from('#__map_yandex AS a');
		$query->order('a.name');
		$query->where('state = 1');
 
		// Get the options.
		$db->setQuery($query);
 
		$options = $db->loadObjectList();
 
		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
 
		return $options;
	}
}
?>