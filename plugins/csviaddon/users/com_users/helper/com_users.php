<?php
/**
 * Users helper file
 *
 * @author 		RolandD Cyber Produksi
 * @link 		https://csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2019 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: com_virtuemart.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Joomla User helper class.
 *
 * @package     CSVI
 * @subpackage  JUsers
 * @since       6.0
 */
class Com_UsersHelperCom_Users
{
	/**
	 * Template helper
	 *
	 * @var    CsviHelperTemplate
	 * @since  6.0
	 */
	protected $template = null;

	/**
	 * Logger helper
	 *
	 * @var    CsviHelperLog
	 * @since  6.0
	 */
	protected $log = null;

	/**
	 * Fields helper
	 *
	 * @var    CsviHelperImportFields
	 * @since  6.0
	 */
	protected $fields = null;

	/**
	 * Database connector
	 *
	 * @var    JDatabaseDriver
	 * @since  6.0
	 */
	protected $db = null;

	/**
	 * Constructor.
	 *
	 * @param   CsviHelperTemplate  $template  An instance of CsviHelperTemplate.
	 * @param   CsviHelperLog       $log       An instance of CsviHelperLog.
	 * @param   CsviHelperFields    $fields    An instance of CsviHelperFields.
	 * @param   JDatabaseDriver     $db        Database connector.
	 *
	 * @since   4.0
	 */
	public function __construct(CsviHelperTemplate $template, CsviHelperLog $log, CsviHelperFields $fields, JDatabaseDriver $db)
	{
		$this->template = $template;
		$this->log = $log;
		$this->fields = $fields;
		$this->db = $db;
	}

	/**
	 * Get the user id, this is necessary for updating existing users.
	 *
	 * @return  mixed  ID of the user if found | False otherwise.
	 *
	 * @since   5.9.5
	 */
	public function getUserId()
	{
		$id = $this->fields->get('id');

		if ($id)
		{
			return $id;
		}
		else
		{
			$email = $this->fields->get('email');

			if ($email)
			{
				$query = $this->db->getQuery(true)
					->select('id')
					->from($this->db->quoteName('#__users'))
					->where($this->db->quoteName('email') . '  = ' . $this->db->quote($email));
				$this->db->setQuery($query);
				$this->log->add('Find the user ID');

				return $this->db->loadResult();
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * Get the user group id, this is necessary for updating existing user groups.
	 *
	 * @param   string  $workTitle  The title to use
	 *
	 * @return  mixed  ID of the user if found | False otherwise.
	 *
	 * @since   6.5.0
	 */
	public function getUsergroupId($workTitle = null)
	{
		$id = $this->fields->get('id');

		if ($id)
		{
			return $id;
		}
		else
		{
			$title = $this->fields->get('title');

			if (empty($title))
			{
				$title = $workTitle;
			}

			if ($title)
			{
				$query = $this->db->getQuery(true)
						->select('id')
						->from($this->db->quoteName('#__usergroups'))
						->where($this->db->quoteName('title') . '  = ' . $this->db->quote($title));
				$this->db->setQuery($query);
				$this->log->add('Find the user group ID');

				return $this->db->loadResult();
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * Get the user accesslevel id, this is necessary for updating existing access level.
	 *
	 * @param   string  $title  The name of the access level.
	 *
	 * @return  mixed  ID of the user if found | False otherwise.
	 *
	 * @since   7.1.0
	 */
	public function getAccessLevelId($title)
	{
		if (!$title)
		{
			return false;
		}

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('id'))
			->from($this->db->quoteName('#__viewlevels'))
			->where($this->db->quoteName('title') . '  = ' . $this->db->quote($title));
		$this->db->setQuery($query);
		$this->log->add('Find the access level ID');
		$id = $this->db->loadResult();

		if (!$id)
		{
			$this->log->add('Access level ID not found for title ' . $title);
		}

		return $id;
	}

	/**
	 * Load the needed field value of the user group.
	 *
	 * @param   string  $name          The name of the user group.
	 * @param   string  $fieldSelect   The field to select.
	 * @param   string  $fieldToCheck  The field to check with.
	 *
	 * @return  mixed  The ID or Name of the user group.
	 *
	 * @since   7.1.0
	 */
	public function getAccessLevelGroupId($name, $fieldSelect, $fieldToCheck)
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName($fieldSelect))
			->from($this->db->quoteName('#__usergroups'))
			->where($this->db->quoteName($fieldToCheck) . ' = ' . $this->db->quote($name));
		$this->db->setQuery($query);

		$result = $this->db->loadResult();

		return $result;
	}

	/**
	 * Load the id of user group from path.
	 *
	 * @param   string  $groupPath  The user group path.
	 *
	 * @return  mixed  The ID or Name of the user group.
	 *
	 * @since   7.9.0
	 */
	public function getUserGroupIdFromPath($groupPath)
	{
		$groupId = '';

		if (!$groupPath)
		{
			return false;
		}

		// Load the user path separator
		$groupSeparator = $this->template->get('group_separator', '/');

		if (!is_array($groupPath))
		{
			$groupPath = (array) $groupPath;
		}

		// Get all group names in the field delimited with |
		foreach ($groupPath as $path)
		{
			// Explode slash delimited path tree into array
			$groupList     = explode($groupSeparator, $path);
			$groupCount    = count($groupList);
			$groupId       = null;
			$groupParentId = 0;

			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('id'))
				->from($this->db->quoteName('#__usergroups'));

			for ($i = 0; $i < $groupCount; $i++)
			{
				$query->clear('where')
					->where($this->db->quoteName('title') . ' = ' . $this->db->quote($groupList[$i]))
					->where($this->db->quoteName('parent_id') . ' = ' . (int) $groupParentId);
				$this->db->setQuery($query);
				$groupId       = $this->db->loadResult();
				$this->log->add('Check if usergroup ' . $groupList[$i] . ' exists');
				$groupParentId = $groupId;
			}
		}

		return $groupId;
	}

	/**
	 * Get the user group paths from ids.
	 *
	 * @param   array  $pathIds  The array of path ids.
	 * @param   bool   $parent   Include parent path or not
	 *
	 * @return  mixed  User group paths| False otherwise.
	 *
	 * @since   7.9.0
	 */
	public function getGroupPath($pathIds, $parent = false)
	{
		// If no pathids no point in moving ahead
		if (!$pathIds)
		{
			return false;
		}

		// Make pathIds as array by default
		if (!is_array($pathIds))
		{
			$pathIds = (array) $pathIds;
		}

		$groupSeparator = $this->template->get('group_separator', '/');
		$groupPaths     = array();
		$query          = $this->db->getQuery(true)
			->from($this->db->quoteName('#__usergroups'));

		foreach ($pathIds as $pathId)
		{
			$paths = array();

			// If the pathid is 1 then its the parent group itself
			if ((int) $pathId === 1)
			{
					$query->select($this->db->quoteName('title'))
					->where($this->db->quoteName('id') . ' = ' . (int) $pathId);
				$this->db->setQuery($query);
				$groupPaths[] = $this->db->loadResult();
			}
			else
			{
				$query->select($this->db->quoteName(array('title', 'parent_id')));

				while (($parent) ? $pathId >= 1 : $pathId > 1)
				{
					$query->clear('where')
						->where($this->db->quoteName('id') . ' = ' . (int) $pathId);
					$this->db->setQuery($query);
					$result = $this->db->loadObject();

					if (is_object($result))
					{
						$paths[] = $result->title;
						$pathId  = $result->parent_id;
					}
					else
					{
						$this->log->add('Cannot get usergroup id');

						return '';
					}
				}

				$paths        = array_reverse($paths);
				$groupPaths[] = implode($groupSeparator, $paths);
			}
		}

		return $groupPaths;
	}

	/**
	 * Get the id from title
	 *
	 * @param   string  $title  The name of the access level.
	 *
	 * @return  mixed  ID of the user if found | False otherwise.
	 *
	 * @since   7.1.0
	 */
	public function getTitleId($title)
	{
		if (!$title)
		{
			return false;
		}

		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('id'))
			->from($this->db->quoteName('#__usergroups'))
			->where($this->db->quoteName('title') . '  = ' . $this->db->quote($title));
		$this->db->setQuery($query);
		$this->log->add('Find the user group ID from title');
		$id = $this->db->loadResult();

		return $id;
	}
}
