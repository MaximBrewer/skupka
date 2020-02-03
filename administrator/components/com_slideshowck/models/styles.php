<?php
/**
 * @name		Slideshow CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

defined('_JEXEC') or die;

use \Slideshowck\CKModel;
use \Slideshowck\CKFof;

class SlideshowckModelStyles extends CKModel {

	protected $context = 'slideshowck.styles';

	public function __construct($config = array()) {

		parent::__construct($config);
	}

	public function getItems() {
		// Create a new query object.
		$db = CKFof::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.*');
		$query->from('`#__slideshowck_styles` AS a');

		// Filter by search in title
		$search = $this->getState('filter_search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = ' . (int) substr($search, 3));
			} else {
				$search = $db->Quote('%' .$search . '%');
				$query->where('(' . 'a.name LIKE ' . $search . ' )');
			}
		}

		// Do not list the trashed items
		$query->where('a.state > -1');

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if ($orderCol && $orderDirn) {
			$query->order($orderCol . ' ' . $orderDirn);
		}

		$limitstart = $this->state->get('limitstart');
		$limit = $this->state->get('limit');
		$db->setQuery($query, $limitstart, $limit);

		$items = $db->loadObjectList();

		// automatically get the total number of items from the query
		$total = $this->getTotal($query);
		$this->state->set('limit_total', (empty($total) ? 0 : (int)$total));

		return $items;
	}
}
