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
 * Joomla User import.
 *
 * @package     CSVI
 * @subpackage  JoomlaUser
 * @since       6.0
 */
class User extends \RantaiImportEngine
{
	/**
	 * User table
	 *
	 * @var    \UsersTableUser
	 * @since  6.0
	 */
	private $userTable = null;

	/**
	 * The addon helper
	 *
	 * @var    \Com_UsersHelperCom_Users
	 * @since  6.0
	 */
	protected $helper = null;

	/**
	 * List of available custom fields
	 *
	 * @var    array
	 * @since  7.2.0
	 */
	private $customFields = '';

	/**
	 * Run this before we start.
	 *
	 * @return  void
	 *
	 * @throws  \Exception
	 *
	 * @since   7.2.0
	 */
	public function onBeforeStart()
	{
		// Load the tables that will contain the data
		$this->loadCustomFields();
	}

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
					case 'registerDate':
					case 'lastvisitDate':
					case 'lastResetTime':
						$this->setState($name, $this->convertDate($value));
						break;
					default:
						$this->setState($name, $value);
						break;
				}
			}
		}

		// There must be an email
		if ($this->getState('email', false))
		{
			$this->loaded = true;

			if (!$this->getState('id', false))
			{
				$this->setState('id', $this->helper->getUserId());
			}

			// Load the current content data
			$this->userTable->load($this->getState('id'));

			if ($this->userTable->get('id', 0) > 0 && !$this->template->get('overwrite_existing_data'))
			{
				$this->log->add(\JText::sprintf('COM_CSVI_DATA_EXISTS_PRODUCT_SKU', $this->getState('email', '')));
				$this->loaded = false;
			}
		}
		else
		{
			$this->loaded = false;

			$this->log->addStats('skipped', \JText::_('COM_CSVI_MISSING_REQUIRED_FIELDS'));
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
			if (!$this->getState('id', false) && $this->template->get('ignore_non_exist'))
			{
				// Do nothing for new users when user chooses to ignore new users
				$this->log->addStats('skipped', \JText::sprintf('COM_CSVI_DATA_EXISTS_IGNORE_NEW', $this->getState('email', '')));
			}
			else
			{
				$userdata = array();

				// If it is a new Joomla user but no username is set, we must set one
				if (!$this->getState('username', false))
				{
					$userdata['username'] = $this->getState('email');
				}
				else
				{
					$userdata['username'] = $this->getState('username');
				}

				// Check if we have an encrypted password
				if ($this->getState('password_crypt', false))
				{
					$userdata['password'] = $this->getState('password_crypt');
					$this->setState('password', true);
				}
				elseif ($this->getState('password', false))
				{
					// Check if we have an encrypted password
					$userdata['password'] = \JUserHelper::hashPassword($this->getState('password'));
				}

				// No user id, need to create a user if possible
				if (empty($this->userTable->id) && $this->getState('email', false) && $this->getState('password', false))
				{
					// Set the creation date
					$userdata['registerDate'] = $this->date->toSql();
				}
				elseif (empty($this->userTable->id) && (!$this->getState('email', false) || !$this->getState('password', false)))
				{
					$this->log->addStats('incorrect', 'COM_CSVI_NO_NEW_USER_PASSWORD_EMAIL');

					return false;
				}

				// Only store the Joomla user if there is an e-mail address supplied
				if ($this->getState('email', false))
				{
					// Check if there is a fullname
					if ($this->getState('fullname', false))
					{
						$userdata['name'] = $this->getState('fullname');
					}
					elseif ($this->getState('name', false))
					{
						$userdata['name'] = $this->getState('name');
					}
					else
					{
						$userdata['name'] = $this->getState('email', '');
					}

					// Set the email
					$userdata['email'] = $this->getState('email');

					// Set if the user is blocked
					if ($this->getState('block', false))
					{
						$userdata['block'] = $this->getState('block');
					}

					// Set the sendEmail
					if ($this->getState('sendEmail', false))
					{
						$userdata['sendEmail'] = $this->getState('sendEmail');
					}

					// Set the registerDate
					if ($this->getState('registerDate', false))
					{
						$userdata['registerDate'] = $this->getState('registerDate');
					}

					// Set the lastvisitDate
					if ($this->getState('lastvisitDate', false))
					{
						$userdata['lastvisitDate'] = $this->getState('lastvisitDate');
					}

					// Set the activation
					if ($this->getState('activation', false))
					{
						$userdata['activation'] = $this->getState('activation');
					}

					// Set the params
					if ($this->getState('params', false))
					{
						$userdata['params'] = $this->getState('params');
					}

					// Set the lastResetTime
					if ($this->getState('lastResetTime', false))
					{
						$userdata['lastResetTime'] = $this->getState('lastResetTime');
					}

					// Set the resetCount
					if ($this->getState('resetCount', false))
					{
						$userdata['resetCount'] = $this->getState('resetCount');
					}

					// Set the otpKey
					if ($this->getState('otpKey', false))
					{
						$userdata['otpKey'] = $this->getState('otpKey');
					}

					// Set the otep
					if ($this->getState('otep', false))
					{
						$userdata['otep'] = $this->getState('otep');
					}

					// Set the requireReset
					if ($this->getState('requireReset', false))
					{
						$userdata['requireReset'] = $this->getState('requireReset');
					}

					$usergroups = array();

					// Check if we have a group ID
					if (!$this->getState('group_id', false)
						&& !$this->getState('usergroup_name', false)
						&& !$this->getState('usergroup_path', false)
						&& !$this->getState('usergroup_name_delete'))
					{
						$this->log->addStats('incorrect', 'COM_CSVI_NO_USERGROUP_NAME_FOUND');

						return false;
					}
					elseif (!$this->getState('group_id', false) && $this->getState('usergroup_path'))
					{
						$groups = explode('|', $this->getState('usergroup_path'));
						$query  = $this->db->getQuery(true)
							->select($this->db->quoteName('id'))
							->from($this->db->quoteName('#__usergroups'));

						foreach ($groups as $group)
						{
							if (strpos($group, $this->template->get('group_separator', '/')) !== false)
							{
								$usergroups[] = $this->helper->getUserGroupIdFromPath($group);
							}
							else
							{
								$query->clear('where')
									->where($this->db->quoteName('title') . ' = ' . $this->db->quote($group));
								$this->db->setQuery($query);
								$usergroups[] = $this->db->loadResult();
							}
						}

						// Clean out any empty values
						$usergroups = array_filter($usergroups, 'strlen');

						if (empty($usergroups))
						{
							$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_NO_USERGROUP_FOUND', $this->getState('usergroup_path')));

							return false;
						}
					}
					elseif (!$this->getState('usergroup_path', false) && $this->getState('usergroup_name'))
					{
						$groups = explode('|', $this->getState('usergroup_name'));
						$query  = $this->db->getQuery(true)
							->select($this->db->quoteName('id'))
							->from($this->db->quoteName('#__usergroups'));

						foreach ($groups as $group)
						{
							$query->clear('where')
								->where($this->db->quoteName('title') . ' = ' . $this->db->quote($group));
							$this->db->setQuery($query);
							$result = $this->db->loadResult();
							$usergroups[] = $result;

							if (!$result)
							{
								$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_NO_USERGROUP_FOUND', $group));
								$this->log->add('Usergroup with name  ' . $group . ' not found', false);
							}
						}

						// Clean out any empty values
						$usergroups = array_filter($usergroups, 'strlen');

						if (!$usergroups)
						{
							$this->log->addStats('incorrect', \JText::_('COM_CSVI_NO_VALID_USERGROUP_FOUND'));
							$this->log->add('Cannot process user. No valid usergroups found.', false);

							return false;
						}
					}

					// Store/update the user
					if ($this->userTable->save($userdata))
					{
						$this->processCustomFields($this->userTable->id);
						$this->log->add('Joomla user stored', false);

						if ($this->queryResult() === 'UPDATE')
						{
							$this->log->addStats('updated', 'COM_CSVI_UPDATE_USERINFO');
						}
						else
						{
							$this->log->addStats('added', 'COM_CSVI_ADD_USERINFO');
						}

						// If user wants to append usergroup, collect the existing ones
						if ($this->template->get('append_usergroup', false))
						{
							$query = $this->db->getQuery(true)
								->select($this->db->quoteName('group_id'))
								->from($this->db->quoteName('#__user_usergroup_map'))
								->where($this->db->quoteName('user_id') . ' = ' . (int) $this->userTable->id);
							$this->db->setQuery($query);
							$existingGroups = $this->db->loadColumn();

							$usergroups = array_unique(array_merge($usergroups, $existingGroups));
						}

						// Empty the usergroup map table
						$query = $this->db->getQuery(true);
						$query->delete($this->db->quoteName('#__user_usergroup_map'));
						$query->where($this->db->quoteName('user_id') . ' = ' . (int) $this->userTable->id);
						$this->db->setQuery($query)->execute();
						$this->log->add('Delete the existing user group to import new ones');

						// Store the user in the usergroup map table
						$query->clear();
						$query->insert($this->db->quoteName('#__user_usergroup_map'));

						if (!empty($usergroups))
						{
							foreach ($usergroups as $group)
							{
								if ($group)
								{
									$query->values($this->userTable->id . ', ' . $group);
								}
							}
						}
						else
						{
							$query->values($this->userTable->id . ', ' . $this->getState('group_id'));
						}

						$this->db->setQuery($query);

						// Store the map
						if ($this->db->execute())
						{
							$this->log->add('Joomla user mapping stored');
						}
						else
						{
							$this->log->add('Could not store Joomla user mapping');
						}

						// If user wants to remove assigned usergroups then do it
						$removeUserGroups = explode('|', $this->getState('usergroup_name_delete', false));

						if ($removeUserGroups)
						{
							$tobeRemoved = array();

							$query->clear()
								->select($this->db->quoteName('id'))
								->from($this->db->quoteName('#__usergroups'));

							foreach ($removeUserGroups as $removeGroup)
							{
								$query->clear('where')
									->where($this->db->quoteName('title') . ' = ' . $this->db->quote($removeGroup));
								$this->db->setQuery($query);
								$tobeRemoved[] = $this->db->loadResult();
							}

							$tobeRemoved = array_filter($tobeRemoved, 'strlen');

							if ($tobeRemoved)
							{
								$removeGroupIds = implode(',', $tobeRemoved);
								$query->clear()
									->delete($this->db->quoteName('#__user_usergroup_map'))
									->where($this->db->quoteName('group_id') . ' IN (' . $removeGroupIds . ')');
								$this->log->add('Removed user from assigned group');
								$this->db->setQuery($query)->execute();
							}
						}
					}
					else
					{
						$this->log->add('COM_CSVI_DEBUG_JOOMLA_USER_NOT_STORED');
						$this->log->addStats('incorrect', \JText::sprintf('COM_CSVI_USERINFO_NOT_ADDED', $this->userTable->getError()));
					}
				}
				else
				{
					$this->log->add('COM_CSVI_DEBUG_JOOMLA_USER_SKIPPED');
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
		$this->userTable = $this->getTable('User');
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
		$this->userTable->reset();
	}

	/**
	 * Get a list of custom fields that can be used as available field.
	 *
	 * @return  void.
	 *
	 * @since   7.2.0
	 *
	 * @throws  \Exception
	 */
	private function loadCustomFields()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('name'))
			->from($this->db->quoteName('#__fields'))
			->where($this->db->quoteName('state') . ' = 1')
			->where($this->db->quoteName('context') . ' = ' . $this->db->quote('com_users.user'));
		$this->db->setQuery($query);
		$this->customFields = $this->db->loadObjectList();

		$this->log->add('Load the Joomla custom fields for users');
	}

	/**
	 * Update custom fields data.
	 *
	 * @param   int  $id  Id of the article
	 *
	 * @return  bool Returns true if all is OK | Returns false otherwise
	 *
	 * @since   7.2.0
	 */
	private function processCustomFields($id)
	{
		if (count($this->customFields) === 0)
		{
			$this->log->add('No custom fields found', false);

			return false;
		}

		foreach ($this->customFields as $field)
		{
			$fieldName = $field->name;

			if ($this->getState($fieldName, ''))
			{
				$query = $this->db->getQuery(true);
				$query->select($this->db->quoteName('id'))
					->from($this->db->quoteName('#__fields'))
					->where($this->db->quoteName('name') . '  = ' . $this->db->quote($fieldName));
				$this->db->setQuery($query);
				$fieldId = $this->db->loadResult();

				$query->clear()
					->delete($this->db->quoteName('#__fields_values'))
					->where($this->db->quoteName('field_id') . ' = ' . (int) $fieldId)
					->where($this->db->quoteName('item_id') . ' = ' . (int) $id);
				$this->db->setQuery($query)->execute();
				$this->log->add('Removed existing custom field values');

				$importValues = explode('|', $this->getState($fieldName, ''));
				$query->clear()
					->insert($this->db->quoteName('#__fields_values'))
					->columns($this->db->quoteName(array('field_id', 'item_id', 'value')));

				foreach ($importValues as $fieldValues)
				{
					$query->values(
						(int) $fieldId . ',' .
						(int) $id . ',' .
						$this->db->quote($fieldValues)
					);
				}

				$this->db->setQuery($query)->execute();
				$this->log->add('Custom field values added');
			}
		}
	}
}
