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
 * Joomla Tags helper.
 *
 * @package     CSVI
 * @subpackage  JoomlaTags
 * @since       7.7.0
 */
class Com_TagsHelperCom_Tags
{
	/**
	 * Template helper
	 *
	 * @var    CsviHelperTemplate
	 * @since  7.7.0
	 */
	protected $template = null;

	/**
	 * Logger helper
	 *
	 * @var    CsviHelperLog
	 * @since  7.7.0
	 */
	protected $log = null;

	/**
	 * Fields helper
	 *
	 * @var    CsviHelperFields
	 * @since  7.7.0
	 */
	protected $fields = null;

	/**
	 * Database connector
	 *
	 * @var    JDatabase
	 * @since  7.7.0
	 */
	protected $db = null;

	/**
	 * Parent ids array
	 *
	 * @var    array
	 * @since  7.7.0
	 */
	protected $parentId = array();

	/**
	 * Constructor.
	 *
	 * @param   CsviHelperTemplate $template An instance of CsviHelperTemplate.
	 * @param   CsviHelperLog      $log      An instance of CsviHelperLog.
	 * @param   CsviHelperFields   $fields   An instance of CsviHelperFields.
	 * @param   JDatabaseDriver    $db       Database connector.
	 *
	 * @since   7.7.0
	 */
	public function __construct(
		CsviHelperTemplate $template,
		CsviHelperLog $log,
		CsviHelperFields $fields,
		JDatabaseDriver $db)
	{
		$this->template = $template;
		$this->log      = $log;
		$this->fields   = $fields;
		$this->db       = $db;
	}

	/**
	 * Get the tag based on alias.
	 *
	 * @param   string $alias The tag alias.
	 * @param   string $path  The tag path.
	 *
	 * @return  int  The ID of the field.
	 *
	 * @since   7.7.0
	 *
	 * @throws  RuntimeException
	 */
	public function getTagId($alias, $path)
	{
		if (!$alias && !$path)
		{
			return false;
		}

		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName('id'))
			->from($this->db->quoteName('#__tags'))
			->where($this->db->quoteName('alias') . '  = ' . $this->db->quote($alias))
			->where($this->db->quoteName('path') . '  = ' . $this->db->quote($path));
		$this->db->setQuery($query);
		$tagId = $this->db->loadResult();

		if (!$tagId)
		{
			$this->log->add('No tags found with alias ' . $alias);

			return false;
		}

		return $tagId;
	}

	/**
	 * Get the tag ID based on it's path.
	 *
	 * @param   string  $path  The path of the tag
	 *
	 * @return  int  The ID of the tag.
	 *
	 * @since   7.7.0
	 */
	public function getTagPathId($path)
	{
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName('id'))
			->from($this->db->quoteName('#__tags'))
			->where($this->db->quoteName('path') . ' = ' . $this->db->quote($path));
		$this->db->setQuery($query);
		$this->log->add('Find the tag ID');

		return $this->db->loadResult();
	}
}
