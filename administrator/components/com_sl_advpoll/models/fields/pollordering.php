<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of Polls.
 *
 */
class JFormFieldPollOrdering extends JFormField {

	/** @var string		The form field type. */
	protected $type	= 'PollOrdering';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput() {
		// Initialize variables.
		$html	= array();
		$attr	= '';

		$js	= <<<JS
jQuery(document).ready(function(){
		var $ = jQuery;
		var custom_style = $('#jform_params_custom_style');
		var header_footer_bg = $('#jform_params_header_footer_bg');
		var header_footer_text = $('#jform_params_header_footer_text');
		var body_bg = $('#jform_params_body_bg');
		var body_text = $('#jform_params_body_text');
		var custom_css = $('#jform_params_custom_css');

		if(custom_style.val() == 0) {
			header_footer_bg.closest('.control-group').css('display', 'none');
			header_footer_text.closest('.control-group').css('display', 'none');
			body_bg.closest('.control-group').css('display', 'none');
			body_text.closest('.control-group').css('display', 'none');
			custom_css.closest('.control-group').css('display', 'none');
		} else {
			header_footer_bg.closest('.control-group').css('display', 'block');
			header_footer_text.closest('.control-group').css('display', 'block');
			body_bg.closest('.control-group').css('display', 'block');
			body_text.closest('.control-group').css('display', 'block');
			custom_css.closest('.control-group').css('display', 'block');
		}

		custom_style.change(function(){
			if($(this).val() == 0) {
				header_footer_bg.closest('.control-group').css('display', 'none');
				header_footer_text.closest('.control-group').css('display', 'none');
				body_bg.closest('.control-group').css('display', 'none');
				body_text.closest('.control-group').css('display', 'none');
				custom_css.closest('.control-group').css('display', 'none');
			} else {
				header_footer_bg.closest('.control-group').css('display', 'block');
				header_footer_text.closest('.control-group').css('display', 'block');
				body_bg.closest('.control-group').css('display', 'block');
				body_text.closest('.control-group').css('display', 'block');
				custom_css.closest('.control-group').css('display', 'block');
			}
		});
});
JS;

		JFactory::getDocument()->addScriptDeclaration($js);

		// Initialize some field attributes.
		$attr	.= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr	.= ((string) $this->element['disabled']) == 'true' ? ' disabled="disabled"' : '';
		$attr	.= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize Javascript field attributes.
		$attr	.= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Get some field values from the form.
		$pollId	= (int) $this->form->getValue('id');
		$categoryId		= (int) $this->form->getValue('catid');
		
		// Build the query for the ordering list.
		$query = 'SELECT ordering AS value, title AS text'
				. ' FROM #__sl_advpoll_polls'
				. ' WHERE catid = ' . (int) $categoryId
				. ' ORDER BY ordering'
		;

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[]	= JHtml::_('list.ordering', '', $query, trim($attr), $this->value, $pollId ? 0 : 1);
			$html[]	= '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '" />';
		} else {
			// Create a regular list.
			$html[]	= JHtml::_('list.ordering', $this->name, $query, trim($attr), $this->value, $pollId ? 0 : 1);
		}

		return implode($html);
	}
}