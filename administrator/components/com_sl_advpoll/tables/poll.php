<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

/**
 * Skyline  Advanced Poll Component - Poll Table Class.
 *
 */
class SL_AdvPollTablePoll extends JTable {

	/**
	 * Constructor.
	 *
	 * @param	JDatabase	A database connector object.
	 */
	public function __construct(&$db) {
		parent::__construct('#__sl_advpoll_polls', 'id', $db);
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param	array		Named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error.
	 * @see		JTable:bind
	 */
	public function bind($array, $ignore = '') {
		if (isset($array['params']) && is_array($array['params'])) {
			$registry			= new JRegistry();
			$registry->loadArray($array['params']);

			if ($registry->get('maxChoices') < 0) {
				$registry->set('maxChoices', 0);
			}

			$array['params']	= (string) $registry;
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overload the store method for the Poll table.
	 *
	 * @param	boolean	Toggle whether null values should be updated.
	 * @return	boolean	True on success, false on failure.
	 */
	public function store($updateNulls = false) {
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();

		if ($this->id) {
			// Existing item
			$this->modified		= $date->toSQL();
			$this->modified_by	= $user->get('id');
		} else {
			// New Poll. A Poll created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (empty($this->created)) {
				$this->created	= $date->toSQL();
			}

			if (!intval($this->created_by)) {
				$this->created_by	= $user->get('id');
			}
		}

		// Verify that the alias is unique
		$table	= JTable::getInstance('Poll', 'SL_AdvPollTable');
		if ($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0)) {
			$this->setError(JText::_('COM_SL_ADVPOLL_POLL_ERROR_UNIQUE_ALIAS'));
			return false;
		}

		// Attempt to store the user data.
		return parent::store($updateNulls);
	}

	/**
	 * Overloaded check method to ensure data integrity.
	 *
	 * @return	boolean		True on success.
	 */
	public function check() {
		// check for valid name
		if (trim($this->title) == '') {
			$this->setError(JText::_('COM_SL_ADVPOLL_POLL_ERROR_TABLES_TITLE'));
			return false;
		}

		// check for existing name
		$query = 'SELECT id'
				. ' FROM #__sl_advpoll_polls'
				. ' WHERE title = ' . $this->_db->quote($this->title) . ' AND catid = ' . (int) $this->catid
		;
		$this->_db->setQuery($query);

		$xid	= intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->setError(JText::_('COM_SL_ADVPOLL_POLL_ERROR_TABLES_NAME'));
			return false;
		}

		if (empty($this->alias)) {
			$this->alias = $this->title;
		}

		$this->alias	= JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-', '', $this->alias)) == '') {
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}

		// clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey)) {
			// only process if not empty
			$bad_characters = array("\n", "\r", '"', '<', '>');
			$after_clean	= JString::str_ireplace($bad_characters, '', $this->metakey);
			$keys			= explode(',', $after_clean);
			$clean_keys		= array();

			foreach ($keys as $key) {
				if (trim($key)) {
					$clean_keys[]	= trim($key);
				}
			}

			$this->metakey	= implode(', ', $clean_keys);
		}

		return true;
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table. The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param	mixed	An optional array of primary key values to update. If not
	 * 					set the instance property value is used.
	 * @param	int		The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param	int		The user id of the user performing the operation.
	 * @return	bool	True on success.
	 */
	public function publish($pks = null, $state = 1, $userId = 0) {
		// Initialize variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId		= (int) $userId;
		$state		= (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks)) {
			if ($this->$k) {
				$pks	= array($this->$k);
			} else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where	= $k . ' = ' . implode(' OR ' . $k . ' = ', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time')) {
			$checkin	= '';
		} else {
			$checkin	= ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		// UPdate the publishing state for rows with the given primary keys.
		$query = 'UPDATE `' . $this->_tbl . '`'
				. ' SET `state` = ' . (int) $state
				. ' WHERE (' . $where . ')'
				. $checkin
		;
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
			// Checkin the rows.
			foreach ($pks as $pk) {
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks)) {
			$this->state = $state;
		}

		$this->setError('');

		return true;
	}
}