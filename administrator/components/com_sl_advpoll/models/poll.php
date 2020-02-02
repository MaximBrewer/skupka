<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');

/**
 * Poll Model.
 *
 */
class SL_AdvPollModelPoll extends JModelAdmin {
	/** @var string		The prefix to use with controller messages. */
	protected $text_prefix	= 'COM_SL_ADVPOLL_POLLS';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 * @return	bool	True if allowed to delete the record. Defaults to the permission set in the component.
	 */
	protected function canDelete($record) {
		if (!empty($record->id)) {
			if ($record->state != -2) {
				return;
			}
			$user = JFactory::getUser();

			if ($record->catid) {
				return $user->authorise('core.delete', 'com_sl_advpoll.category.' . (int) $record->catid);
			} else {
				return parent::canDelete($record);
			}
		}
	}

	/**
	 * Method to test whether a record can have its state changed.
	 *
	 * @param	object	A record object.
	 * @return	bool	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 */
	protected function canEditState($record) {
		$user	= JFactory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_sl_advpoll.category.' . (int) $record->catid);
		} else {
			return parent::canEditState($record);
		}
	}
	
	/**
	 * Returns a reference to a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @pararm	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'Poll', $prefix = 'SL_AdvPollTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm				A JForm object on success, false on failure.
	 */
	public function getForm($data = array(), $loadData = true) {
		// Initialize variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form	= $this->loadForm('com_sl_advpoll.poll', 'poll', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		// Determine correct permissions to check.
		if ($this->getState('poll.id')) {
			// Existing record. Can only edit in selected Categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
		} else {
			// New record. Can only create in selected Categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}
		
		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable field while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return mixed	The data for the form.
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data	= JFactory::getApplication()->getUserState('com_sl_advpoll.edit.poll.data', array());

		if (empty($data)) {
			$data	= $this->getItem();

			// Prime some default values.
			if ($this->getState('poll.id') == 0) {
				$app	= JFactory::getApplication();
				$data->set('catid', JRequest::getInt('catid', $app->getUserState('com_sl_advpoll.polls.filter.category_id')));
			}
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	int		The id of the primary key.
	 * @return	mixed	Object on success, false, on failure.
	 */
	public function getItem($pk = null) {
		if ($item	= parent::getItem($pk)) {
			if ($item->id) {
				// get answers
				$answers_model	= JModelLegacy::getInstance('Answers', 'SL_AdvPollModel', array('ignore_request' => true));

				$answers_model->setState('list.limit', 9999);
				$answers_model->setState('list.start', 0);
				$answers_model->setState('filter.poll_id', $item->id);

				$item->answers	= $answers_model->getItems();
			} else {
				$item->answers	= array();
			}
		}

		return $item;
	}

	/**
	 * Prepare and sanitize the table prior to saving.
	 */
	protected function prepareTable(&$table) {
		jimport('joomla.filter.output');
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();

		$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias		= JApplication::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias	= JApplication::stringURLSafe($table->title);
		}

		if (empty($table->id)) {
			// Set the values

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db		= JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__sl_advpoll_polls');
				$max	= $db->loadResult();

				$table->ordering	= $max + 1;
			}
		} else {
			// Set the values
		}
	}
	
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 * @return	array	An array of conditions to add to ordering queries.
	 */
	protected function getReorderConditions($table) {
		$condition		= array();
		$condition[]	= 'catid = ' . (int) $table->catid;

		return $condition;
	}
}