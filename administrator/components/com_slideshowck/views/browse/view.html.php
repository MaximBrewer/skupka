<?php
/**
 * @name		Slideshow CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */
 
 
// No direct access
defined('_JEXEC') or die;

use \Slideshowck\CKView;
use \Slideshowck\CKFof;

class SlideshowckViewBrowse extends CKView {

	function display($tpl = 'default') {
		$input = JFactory::getApplication()->input;

		$user = JFactory::getUser();
		$authorised = ($user->authorise('core.edit', 'com_slideshowck') || (count($user->getAuthorisedCategories('com_slideshowck', 'core.edit'))));

		if ($authorised !== true)
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			return false;
		}

		// load the items
		require_once JPATH_ADMINISTRATOR . '/components/com_slideshowck/helpers/ckbrowse.php';
		$this->items = CKBrowse::getItemsList();

		parent::display($tpl);
	}
}
