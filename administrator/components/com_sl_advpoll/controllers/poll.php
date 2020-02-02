<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.controllerform');

/**
 * Poll Controller Class.
 *
 */
class SL_AdvPollControllerPoll extends JControllerForm {
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	$data	An array of input data.
	 * @return	bool
	 */
	protected function allowAdd($data = array()) {
		// Initialize variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId) {
			// If the category has been passed in URL check it.
			$allow	= $user->authorise('core.create', $this->option . '.category.' . $categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd($data);
		} else {
			return $allow;
		}
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	THe name of the key for the primary key.
	 * @return	bool
	 */
	protected function allowEdit($data = array(), $key = 'id') {
		// Initialize variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$categoryId	= 0;

		if ($recordId) {
			$categoryId	= (int) $this->getModel()->getItem($recordId)->catid;
		}

		if ($categoryId) {
			// The Category has been set. Check the Category permissions.
			return JFactory::getUser()->authorise('core.edit', $this->option . '.category.' . $categoryId);
		} else {
			// Since there is no asset tracking, revert to the component permissions.
			return parent::allowEdit($data, $key);
		}
	}
	
	/**
	 * Method to run batch operations.
	 *
	 * @return	void
	 */
	public function batch() {
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model	= $this->getModel('Poll', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_sl_advpoll&view=polls' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 * @param	JModel	$model		The data model object.
	 * @param	array	$validData	The validated data.
	 * @return	void
	 */
	protected function postSaveHook(JModelLegacy &$model, $validData = array()) {
		$data			= JRequest::get('post');
		$id				= $model->getState('poll.id');
		$answer_model	= JModelLegacy::getInstance('Answer', 'SL_AdvPollModel', array('ignore_request' => true));

		//get data from answers table
		$answers_model	= JModelLegacy::getInstance('Answers', 'SL_AdvPollModel', array('ignore_request' => true));

		$answers_model->setState('filter.poll_id', $id);
		$answersItem	= $answers_model->getItems();

		// process answers
		$answers		= $data['answers'];

		//delete some items not in submit form
		foreach ($answersItem as $item) {
			if (!in_array($item->id , $answers['id'])) {
				$answer_model->delete($item->id);
			}
		}

		// prevent 2 answers have same title
		$added_answers	= array();

		if (isset($answers['title'])) {
			for ($i = 0, $n = count($answers['title']); $i < $n; $i++) {
				if ($answers['title'][$i] && !in_array($answers['title'][$i], $added_answers)) {
					$added_answers[]	= $answers['title'][$i];

					$poll_answers	= array(
						'pollid'	=> $id,
						'ordering'	=> $i,
					);

					foreach ($answers as $key => $value) {
						$poll_answers[$key]	= $value[$i];
					}

					$answer_model->save($poll_answers);
				}
			}
		}
	}
}