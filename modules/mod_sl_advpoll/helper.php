<?php
/**
 * @version		$Id$
 * @author		Pham Minh Tuan (admin@extstore.com)
 * @package		Joomla.Site
 * @subpakage	Skyline.AdvPoll
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

/**
 * Skyline Software - Advanced Poll Helper Class.
 *
 * @package		Joomla.Site
 * @subpakage	Skyline.AdvPoll
 */
class modSL_AdvPollHelper {

	/**
	 * Get poll data.
	 *
	 * @static
	 * @param $params
	 * @return mixed
	 */
	public static function getItem($params) {
		// Initialize variables
		JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_sl_advpoll/models');
		$id		= (int) $params->get('poll_id');
		$show_random = (int) $params->get('show_random', 0);
		$cate_poll	= (int) $params->get('cate_poll', 0);

		$model	= JModelLegacy::getInstance('Poll', 'SL_AdvPollModel', array('ignore_request' => true));
		$model->setState('poll.id', $id);
		$model->setState('filter.published', 1);
		$model->setState('filter.archived', 2);
		$model->setState('params', $params);

		$model->setState('show_random', $show_random);
		$model->setState('cate_poll', $cate_poll);

		$item = $model->getItem();
        $item->canVote = $model->checkPermission();

        return $item;
	}

}