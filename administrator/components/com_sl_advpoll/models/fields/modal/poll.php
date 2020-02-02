<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

/**
 * Supports an HTML modal select list of polls.
 *
 * @package		Joomla.Administrator
 * @subpackage	Skyline.AdvPoll
 */
class JFormFieldModal_Poll extends JFormField {
	/** @var string    The form field type. */
	protected $type = 'Modal_Poll';

	protected function getInput() {
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectPoll_' . $this->id . '(id, title, catid, object) {';
		$script[] = '		document.id("' . $this->id . '_id").value = id;';
		$script[] = '		document.id("' . $this->id . '_name").value = title;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


		// Setup variables for display.
		$html = array();
		$link = 'index.php?option=com_sl_advpoll&amp;view=polls&amp;layout=modal&amp;tmpl=component&amp;function=jSelectPoll_' . $this->id;

		$db = JFactory::getDBO();
		$db->setQuery(
			'SELECT title'
				. ' FROM #__sl_advpoll_polls'
				. ' WHERE id = ' . (int)$this->value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		if (empty($title)) {
			$title = JText::_('COM_SL_ADVPOLL_SELECT_A_POLL');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current user display field.
		$html[]	= '<span class="input-append">';
		$html[]	= '<input type="text" class="input-medium" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';
		$html[] = '<a class="modal btn" title="' . JText::_('COM_SL_ADVPOLL_CHANGE_POLL') . '"  href="' . $link . '&amp;' . JSession::getFormToken() . '=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> ' . JText::_('COM_SL_ADVPOLL_CHANGE_POLL_BUTTON') . '</a>';
		$html[]	= '</span>';

		// The active article id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

		return implode("\n", $html);
	}
}