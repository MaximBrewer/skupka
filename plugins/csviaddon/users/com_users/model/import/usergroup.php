<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaUser
 *
 * @author      RolandD Cyber Produksi <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2019 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://csvimproved.com
 */

namespace users\com_users\model\import;

defined('_JEXEC') or die;

/**
 * Joomla User group import.
 *
 * @package     CSVI
 * @subpackage  JoomlaUser
 * @since       6.5.0
 */
class Usergroup extends \RantaiImportEngine
{
	/**
	 * User table
	 *
	 * @var    \UsersTableUsergroup
	 * @since  6.0
	 */
	private $usergroupTable = null;

	/**
	 * The addon helper
	 *
	 * @var    \Com_UsersHelperCom_Users
	 * @since  6.5.0
	 */
	protected $helper = null;

	/**
	 * Start the product import process.
	 *
	 * @return  bool  True on success | false on failure.
	 *
	 * @since   6.0
	 */
	public function getStart()
	{
		// Process data
		foreach ($this->fields->getData() as $fields)
		{
			foreach ($fields as $name => $details)
			{
				$value = $details->value;

				switch ($name)
				{
					case 'parent_name':
						$this->setState('parent_id', $this->getParentId($value));
						break;
					default:
						$this->setState($name, $value);
						break;
				}
			}
		}

		// There must be a title
		if ($this->getState('title', false)
			|| $this->getState('title_path', false))
		{
			$this->loaded = true;

			if (!$this->getState('id', false))
			{
				if (strpos($this->getState('title_path', false), $this->template->get('group_separator', '/')) !== false)
				{
					$userGroupId = $this->helper->getUserGroupIdFromPath($this->getState('title_path', false));
					$this->setState('id', $userGroupId);
				}

				if ($this->getState('title'))
				{
					$this->setState('id', $this->helper->getUserGroupId($this->getState('title')));
				}
			}

			// Load the current content data
			if ($this->usergroupTable->load($this->getState('id')))
			{
				if (!$this->template->get('overwrite_existing_data'))
				{
					$this->log->add(\JText::sprintf('COM_CSVI_DATA_EXISTS_PRODUCT_SKU', $this->getState('title', '')));
					$this->loaded = false;
				}
			}
		}
		else
		{
			$this->loaded = false;

			$this->log->addStats('skipped', \JText::sprintf('COM_CSVI_MISSING_REQUIRED_FIELDS', 'title'));
		}

		return true;
	}

	/**
	 * Process a record.
	 *
	 * @return  bool  Returns true if all is OK | Returns false if no product SKU or product ID can be found.
	 *
	 * @since   6.0
	 */
	public function getProcessRecord()
	{
		if ($this->loaded)
		{
			$usergroup_delete = $this->getState('usergroup_delete', 'N');
			$id = $this->getState('id', false);

			// User wants to delete the product
			if ($id && $usergroup_delete == 'Y')
			{
				$this->usergroupTable->deleteUsergroup($id);
			}
			elseif (!$this->getState('id', false) && $this->template->get('ignore_non_exist'))
			{
				// Do nothing for new users when user chooses to ignore new users
				$this->log->addStats('skipped', \JText::sprintf('COM_CSVI_DATA_EXISTS_IGNORE_NEW', $this->getState('title', '')));
			}
			else
			{
				if (strpos($this->getState('title_path', false), $this->template->get('group_separator', '/')) !== false)
				{
					$groupSeparator = $this->template->get('group_separator', '/');
					$groupList      = explode($groupSeparator, $this->getState('title_path', false));
					$rootName       = $this->getRootName();

					// Enforce Public to be the root
					if ($groupList[0] !== $rootName)
					{
						array_unshift($groupList, $rootName);
					}

					$groupCount     = count($groupList);
					$groupParentId  = ($this->getState('parent_id', false)) ? $this->getState('parent_id', false) : 0;

					for ($i = 0; $i < $groupCount; $i++)
					{
						if ($i > 0)
						{
							$groupParentId = $this->helper->getTitleId($groupList[$i - 1]);
						}

						$this->setState('title', $groupList[$i]);
						$this->setState('parent_id', $groupParentId);
						$id = $this->helper->getUserGroupId($groupList[$i]);

						if ($id)
						{
							$this->usergroupTable->load($id);
						}

						$this->usergroupTable->bind($this->state);

						try
						{
							$this->usergroupTable->storeUsergroup();
							$this->usergroupTable->reset();
							$this->log->addStats('Information', 'COM_CSVI_JOOMLA_USERGROUP_PROCCESSED');
						}
						catch (\Exception $exception)
						{
							$this->log->addStats('Error', $exception->getMessage());
						}
					}
				}
				else
				{
					// Set default parent id if not set by user
					if (!$this->getState('parent_id', false))
					{
						$this->setState('parent_id', 1);
					}

					// Bind the data
					$this->usergroupTable->bind($this->state);

					// Store the product
					if (!$this->usergroupTable->storeUsergroup())
					{
						return false;
					}
				}
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Load the necessary tables.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function loadTables()
	{
		$this->usergroupTable = $this->getTable('Usergroup');
	}

	/**
	 * Clear the loaded tables.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function clearTables()
	{
		$this->usergroupTable->reset();
	}

	/**
	 * Do some post processing on the groups.
	 *
	 * @return  void
	 *
	 * @since   7.9.0
	 */
	public function getPostProcessing()
	{
		$this->usergroupTable->rebuildUsergroup();
	}

	/**
	 * Load the ID of the parent group.
	 *
	 * @param   string  $name  The name of the parent group.
	 *
	 * @return  int  The ID of the parent group.
	 *
	 * @since   6.5.0
	 */
	private function getParentId($name)
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('id'))
			->from($this->db->quoteName('#__usergroups'))
			->where($this->db->quoteName('title') . ' = ' . $this->db->quote($name));
		$this->db->setQuery($query);

		$id = $this->db->loadResult();

		if (!$id)
		{
			$id = 0;
		}

		return $id;
	}

	/**
	 * Get the name of the root entry.
	 *
	 * @return  string  .
	 *
	 * @since   7.9.0
	 */
	private function getRootName()
	{
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select($db->quoteName('title'))
			->from($db->quoteName('#__usergroups'))
			->where($db->quoteName('lft') . ' = 1');
		$db->setQuery($query);

		return $db->loadResult();
	}
}
