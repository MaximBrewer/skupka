<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * View to edit a Poll.
 *
 */
class SL_AdvPollViewPoll extends JViewLegacy {

	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view.
	 */
	public function display($tpl = null) {
		$app			= JFactory::getApplication();
		$key			= 'error_msg' . $app->input->get('id', 0, 'post');
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->config	= JComponentHelper::getParams('com_sl_advpoll');
		$this->message	= JFactory::getApplication()->getUserState($key) ? JFactory::getApplication()->getUserState($key) :
							JHtml::_('date', 'now', $this->config->get('date_format', 'l, F d, Y g:i:s A'));

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
	}
}