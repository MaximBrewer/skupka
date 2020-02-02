<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;

class MaximenuckViewMigration extends JViewLegacy
{

	protected $items;

	protected $pagination;

	protected $state;
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$lang 		= JFactory::getLanguage();
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->ordering = array();

		$this->menuhtml = $this->addMenuSelection();

		// $this->addToolbar();
		parent::display($tpl);
		
		die;
	}

	protected function addMenuSelection() {
		require_once JPATH_COMPONENT.'/helpers/maximenuckhelper.php';

		$canDo	= MaximenuckHelper::getActions($this->state->get('filter.parent_id'));
		$menushtml = '';
		// Add a batch button
		if ($canDo->get('core.edit'))
		{
			$menushtml .= '<div id="toolbar-menu" class="btn-wrapper">';
			foreach ($this->get('Menus') as $menu) {
				$active = ($menu->menutype == JFactory::getApplication()->input->get('menutype')) ? ' active' : '';
				$menushtml .= '<a href="index.php?option=com_maximenuck&view=migration&menutype='.$menu->menutype.'"><button class="btn btn-small btn-primary'.$active.'">
						<i class="icon-list-view"></i>
						' . $menu->title . '</button></a>';
			}
			
		} else {
			$menushtml = Jtext::_('COM_MENUMANAGERCK_NOT_HAVE_RIGHT_TO_EDIT');
		}

		return $menushtml;
	}
}
