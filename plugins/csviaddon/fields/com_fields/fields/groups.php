<?php
/**
 * @package     CSVI
 * @subpackage  Joomla Fields
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2019 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with Field groups.
 *
 * @package     CSVI
 * @subpackage  Joomla Fields
 * @since       7.9.0
 */
class CsviFieldFormFieldGroups extends JFormFieldCsviForm
{
	/**
	 * Method to instantiate the form field object.
	 *
	 * @param   JForm  $form  The form to attach to the form field object.
	 *
	 * @since   7.9.0
	 *
	 * @throws  Exception
	 */
	public function __construct($form = null)
	{
		$this->type = 'Groups';

		parent::__construct($form);
	}

	/**
	 * Select field groups.
	 *
	 * @return  array  An array of field groups.
	 *
	 * @since   7.9.0
	 */
	protected function getOptions()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('id', 'value') . ',' . $this->db->quoteName('title', 'text'))
			->from($this->db->quoteName('#__fields_groups'))
			->order($this->db->quoteName('id'));

		$this->db->setQuery($query);
		$groups = $this->db->loadObjectList();

		if (empty($groups))
		{
			$groups = array();
		}

		return array_merge(parent::getOptions(), $groups);
	}
}
