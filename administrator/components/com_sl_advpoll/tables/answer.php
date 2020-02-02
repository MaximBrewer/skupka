<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

/**
 * Skyline  Advanced Poll Component - Answer Table Class.
 *
 */
class SL_AdvPollTableAnswer extends JTable {

	/**
	 * Constructor.
	 *
	 * @param	JDatabase	A database connector object.
	 */
	public function __construct(&$db) {
		parent::__construct('#__sl_advpoll_answers', 'id', $db);
	}

	/**
	 * Overloaded check method to ensure data integrity.
	 *
	 * @return	boolean		True on success.
	 */
	public function check() {
		// check for valid name
		if (trim($this->title) == '') {
			$this->setError(JText::_('COM_SL_ADVPOLL_ANSWER_ERROR_TABLES_TITLE'));
			return false;
		}

		return true;
	}
}