<?php
/**
 * @copyright	Copyright (c) 2013 Skyline (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Plugin Select category for sl_advpoll.
 *
 * @package		Joomla.Module
 * @subpakage	Skyline.Advpoll
 */
class JFormFieldCatepoll extends JFormField {
	/** @var string Field type */
	protected $type	= 'catepoll';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput() {

		$js	= <<<JS
jQuery(document).ready(function(){
	var $ = jQuery;
	var random_yes = $('#jform_params_show_random0');
	var random_no = $('#jform_params_show_random1');
	var cate_poll = $('#jformparamscate_poll');
	var poll_id_name = $('#jform_params_poll_id_name');

	if(random_yes.is(":checked")) {
		cate_poll.closest('.control-group').css('display', 'block');
		poll_id_name.closest('.control-group').css('display', 'none');
	}

	if(random_no.is(":checked")) {
		cate_poll.closest('.control-group').css('display', 'none');
		poll_id_name.closest('.control-group').css('display', 'block');
	}

	random_yes.click(function(){
		cate_poll.closest('.control-group').css('display', 'block');
		poll_id_name.closest('.control-group').css('display', 'none');
	});

	random_no.click(function(){
		cate_poll.closest('.control-group').css('display', 'none');
		poll_id_name.closest('.control-group').css('display', 'block');
	});

});
JS;
		JFactory::getDocument()->addScriptDeclaration($js);

		$db = JFactory::getDbo();
		$query = "SELECT DISTINCT p.catid, c.title FROM #__sl_advpoll_polls AS p
				INNER JOIN #__categories AS c ON p.catid = c.id ";
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return JHTML::_('select.genericlist', $rows, $this->name, '', 'catid', 'title', $this->value );
	}

}

