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
class Improved_ajax_loginViewForms extends JViewLegacy
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
    $this->lang = $this->getLanguages();

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
        
		Improved_ajax_loginHelper::addSubmenu('forms');
        
		$this->addToolbar();
        
    $this->sidebar = JHtmlSidebar::render();
		parent::display($GLOBALS['j25']? '25' : $tpl);
	}

  protected function getLanguages() {
    $db = JFactory::getDbo();
    $db->setQuery(
        'SELECT lang_code AS code, title' .
        ' FROM #__languages' .
        ' ORDER BY sef ASC'
    );
    return $db->loadObjectList();
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

		JToolBarHelper::title(JText::_('COM_IMPROVED_AJAX_LOGIN_TITLE_FORMS'), 'forms.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR.'/views/form';
        if (file_exists($formPath)) {
/*
        if ($canDo->get('core.create')) {
			    JToolBarHelper::addNew('form.add','JTOOLBAR_NEW');
		    }
*/
		    if ($canDo->get('core.edit') && isset($this->items[0])) {
			    JToolBarHelper::editList('form.edit','JTOOLBAR_EDIT');
		    }

        }
/*
		if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::custom('forms.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			    JToolBarHelper::custom('forms.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'forms.delete','JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
			    JToolBarHelper::divider();
			    JToolBarHelper::archiveList('forms.archive','JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
            	JToolBarHelper::custom('forms.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
		}

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
		    if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			    JToolBarHelper::deleteList('', 'forms.delete','JTOOLBAR_EMPTY_TRASH');
			    JToolBarHelper::divider();
		    } else if ($canDo->get('core.edit.state')) {
			    JToolBarHelper::trash('forms.trash','JTOOLBAR_TRASH');
			    JToolBarHelper::divider();
		    }
        }
*/
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_improved_ajax_login');
		}
        
        //Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_improved_ajax_login&view=forms');
        
        $this->extra_sidebar = '';
/*
		JHtmlSidebar::addFilter(

			JText::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

		);

		//Filter for the field type
		$select_label = JText::sprintf('COM_IMPROVED_AJAX_LOGIN_FILTER_SELECT_LABEL', 'Type');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "login";
		$options[0]->text = "Login";
		$options[1] = new stdClass();
		$options[1]->value = "registration";
		$options[1]->text = "Registration";
		$options[2] = new stdClass();
		$options[2]->value = "profile";
		$options[2]->text = "Edit Profile";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_type',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.type'), true)
		);

		//Filter for the field theme
		$select_label = JText::sprintf('COM_IMPROVED_AJAX_LOGIN_FILTER_SELECT_LABEL', 'Theme');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "elegant";
		$options[0]->text = "Elegant";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_theme',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.theme'), true)
		);
*/
        
	}
    
	protected function getSortFields()
	{
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
		'a.state' => JText::_('JSTATUS'),
		'a.checked_out' => JText::_('COM_IMPROVED_AJAX_LOGIN_FORMS_CHECKED_OUT'),
		'a.checked_out_time' => JText::_('COM_IMPROVED_AJAX_LOGIN_FORMS_CHECKED_OUT_TIME'),
		'a.title' => JText::_('COM_IMPROVED_AJAX_LOGIN_FORMS_TITLE'),
		'a.type' => JText::_('COM_IMPROVED_AJAX_LOGIN_FORMS_TYPE'),
		'a.theme' => JText::_('COM_IMPROVED_AJAX_LOGIN_FORMS_THEME'),
		);
	}

    
}
