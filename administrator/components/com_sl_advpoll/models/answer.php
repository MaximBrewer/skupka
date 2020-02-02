<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.modeladmin');

/**
 * Answer Model.
 *
 */
class SL_AdvPollModelAnswer extends JModelAdmin {
	/** @var string		The prefix to use with controller messages. */
	protected $text_prefix	= 'COM_SL_ADVPOLL_ANSWERS';

	
	/**
	 * Returns a reference to a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @pararm	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'Answer', $prefix = 'SL_AdvPollTable', $config = array()) {
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
		$form	= $this->loadForm('com_sl_advpoll.answer', 'answer', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
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
		$data	= JFactory::getApplication()->getUserState('com_sl_advpoll.edit.answer.data', array());

		if (empty($data)) {
			$data	= $this->getItem();

			// Prime some default values.
			if ($this->getState('answer.id') == 0) {
				$app	= JFactory::getApplication();
				$data->set('pollid', JRequest::getInt('pollid', $app->getUserState('com_sl_advpoll.answers.filter.poll_id')));
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

		if (empty($table->id)) {
			// Set the values

			// Set ordering to the last item if not set
			if (empty($table->ordering)) {
				$db		= JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__sl_advpoll_answers');
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
		$condition[]	= 'pollid = ' . (int) $table->pollid;

		return $condition;
	}
}