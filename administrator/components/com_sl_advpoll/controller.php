<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

/**
 * Skyline  Advanced Poll Controller.
 *
 * @package		Joomla.Administrator
 * @subpackage	Skyline.AdvPoll
 */
class SL_AdvPollController extends JControllerLegacy {

	/**
	 * Method to display a view.
	 *
	 * @param	bool 			$cachable	If true, the view output will be cached.
	 * @param	bool 			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFileterInput::clean()}.
	 * @return	JController		This object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false) {
		require_once(JPATH_COMPONENT. '/helpers/sl_advpoll.php');
		$view		= $this->input->get('view', 'dashboard');
		$this->input->set('view', $view);
		$layout		= $this->input->get('layout', 'default');
		$id			= $this->input->get('id');
		
		// Check for edit form.
		if ($view == 'poll' && $layout == 'edit' && !$this->checkEditId('com_sl_advpoll.edit.' . $view, $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_sl_advpoll&view=polls' , false));

			return false;
		}

		return parent::display();
	}
}