<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.application.component.controlleradmin');

/**
 * Poll List Controller Class.
 *
 */
class SL_AdvPollControllerPolls extends JControllerAdmin {
	/** @var string		The prefix to use with controller messages. */
	protected $text_prefix	= 'COM_SL_ADVPOLL_POLLS';
	
	/**
	 * Proxy for getModel.
	 */
	public function getModel($name = 'Poll', $prefix = 'SL_AdvPollModel', $config = array('ignore_request' => true)) {
		$model	= parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax(){
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
}