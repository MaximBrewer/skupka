<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaTags
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2019 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

defined('_JEXEC') or die;

/**
 * Joomla Tags table.
 *
 * @package     CSVI
 * @subpackage  JoomlaTags
 * @since       7.7.0
 */
class TagsTableTags extends JTableNested
{
	/**
	 * Table constructor.
	 *
	 * @param   string    $table  Name of the database table to model.
	 * @param   string    $key    Name of the primary key field in the table.
	 * @param   JDatabase &$db    Database driver
	 * @param   array     $config The configuration parameters array
	 *
	 * @since   7.7.0
	 */
	public function __construct($table, $key, &$db, $config = array())
	{
		parent::__construct('#__tags', 'id', $db, $config);
	}

	/**
	 * Check if insert id has to be kept as given
	 *
	 * @return  bool  True if row inserted | False otherwise.
	 *
	 * @since   7.7.0
	 */
	public function checkId($tagId)
	{
		$db = JFactory::getDbo();

		if (!$tagId)
		{
			return false;
		}

		$query = $db->getQuery(true)
			->select($db->quoteName($this->_tbl_key))
			->from($db->quoteName($this->_tbl))
			->where($db->quoteName($this->_tbl_key) . ' = ' . (int) $tagId);

		$db->setQuery($query);

		$id = $db->loadResult();

		if (!$id)
		{
			$query->clear()
				->insert($db->quoteName($this->_tbl))
				->columns(array($db->quoteName($this->_tbl_key)))
				->values((int) $tagId);
			$db->setQuery($query)->execute();
			$id = $db->insertid();
		}

		return $id;
	}

	/**
	 * Reset the primary key.
	 *
	 * @return  boolean  Always returns true.
	 *
	 * @since   7.7.0
	 */
	public function reset()
	{
		parent::reset();

		// Reset the primary key
		$this->id = null;
	}
}
