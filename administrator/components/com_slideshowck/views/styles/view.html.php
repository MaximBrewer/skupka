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

class SlideshowckViewStyles extends CKView {

	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {

		$user = JFactory::getUser();
		$authorised = ($user->authorise('core.edit', 'com_slideshowck') || (count($user->getAuthorisedCategories('com_slideshowck', 'core.edit'))));

		if ($authorised !== true)
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			return false;
		}

		$this->items = $this->get('Items');

		$this->toolbar = $this->getToolbar();

		$this->input->set('tmpl', 'component');
		$this->input->set('layout', 'modal');

		parent::display();
	}

	private function getToolbar() {
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		if (CKFof::userCan('create')) {
			JToolBarHelper::addNew('style.add', 'JTOOLBAR_NEW');
			JToolBarHelper::custom('style.copy', 'copy', 'copy', 'CK_COPY');
			// Render the popup button
//				$html = '<button class="btn btn-small btn-success" onclick="CKBox.open({handler:\'iframe\', fullscreen: true, url:\'' . JUri::root(true) . '/administrator/index.php?option=com_slideshowck&view=style&layout=modal&tmpl=component&id=0\'})">
//						<span class="icon-new icon-white"></span>
//						' . JText::_('JTOOLBAR_NEW') . '
//						</button>';
//				$bar->appendButton('Custom', $html);
			
		}
		if (CKFof::userCan('edit')) {
			JToolBarHelper::custom('style.edit', 'edit', 'edit', 'CK_EDIT');
			JToolBarHelper::trash('style.delete');
		}

		return $bar;
	}
}
