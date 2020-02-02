<?php
/*-------------------------------------------------------------------------
# com_improved_ajax_login - com_improved_ajax_login
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2013 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Improved_ajax_login.
 */
class Improved_ajax_loginViewOauths extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}

	  $this->addToolbar();
    Improved_ajax_loginHelper::addSubmenu('oauths');
    $this->sidebar = JHtmlSidebar::render();

		parent::display($GLOBALS['j25']? '25' : $tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/improved_ajax_login.php';

		$state	= $this->get('State');
		$canDo	= Improved_ajax_loginHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_IMPROVED_AJAX_LOGIN_TITLE_OAUTHS'), 'oauths.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/oauth';
        if (file_exists($formPath)) {
		    if ($canDo->get('core.edit') && isset($this->items[0])) {
			    JToolBarHelper::editList('oauth.edit','JTOOLBAR_EDIT');
		    }

        }

		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->published)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('oauths.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('oauths.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'oauths.delete','JTOOLBAR_DELETE');
            }
		}


		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_improved_ajax_login');
		}
        
      //Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_improved_ajax_login&view=oauths');
    $this->extra_sidebar = '';
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)
		);

        
	}
    
	protected function getSortFields()
	{
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.published' => JText::_('JSTATUS'),
		'a.name' => JText::_('COM_IMPROVED_AJAX_LOGIN_OAUTHS_NAME'),
		'a.app_id' => JText::_('COM_IMPROVED_AJAX_LOGIN_OAUTHS_APP_ID'),
		'a.app_secret' => JText::_('COM_IMPROVED_AJAX_LOGIN_OAUTHS_APP_SECRET'),
		);
	}

    
}
