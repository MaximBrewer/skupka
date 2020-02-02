<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Answer records.
 *
 */
class SL_AdvPollModelAnswers extends JModelList {
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields']	= array(
				'id', 'a.id',
				'title', 'a.title',
				'type_answer', 'a.type_answer',
				'pollid', 'a.pollid', 'poll_title',
				'state', 'a.state',
				'ordering', 'a.ordering',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null) {
		// Initialize variables.
		$app		= JFactory::getApplication('administrator');

		// Load the filter state.
		$published	= $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
		
		$pollId	= $this->getUserStateFromRequest($this->context . '.filter.poll_id', 'filter_poll_id', '');
		$this->setState('filter.poll_id', $pollId);

		// Load the parameters.
		$params		= JComponentHelper::getParams('com_sl_advpoll');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.ordering', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and different modules
	 * that might need different sets of data or different ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '') {
		// compile the store id.
		$id	.= ':' . $this->getState('filter.access');
		$id	.= ':' . $this->getState('filter.state');
		$id	.= ':' . $this->getState('filter.poll_id');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 */
	protected function getListQuery() {
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.type_answer, a.pollid,
				 a.state, a.ordering, a.votes'
			)
		);

		$query->from('#__sl_advpoll_answers AS a');

		// Join over the Polls.
		$query->select('poll.title AS poll_title');
		$query->join('LEFT', '#__sl_advpoll_polls AS poll ON poll.id = a.pollid');

		// Filter by published state.
		$published	= $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by poll.
		$pollId	= $this->getState('filter.poll_id');
		if (is_numeric($pollId)) {
			$query->where('a.pollid = ' . (int) $pollId);
		}

		// Add the list ordering clause.
		$orderCol	= $this->getState('list.ordering', 'a.ordering');
		$orderDirn	= $this->getState('list.direction');

		if ($orderCol == 'a.ordering' || $orderCol == 'poll_title') {
			$orderCol = 'poll_title ' . $orderDirn . ', a.ordering';
		}
		
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}