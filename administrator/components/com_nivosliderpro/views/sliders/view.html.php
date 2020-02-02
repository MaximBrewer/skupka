<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

class NivoSliderProViewSliders extends JMasterViewNivoRev 
{
	protected $items;
	protected $pagination;
	protected $state;
	
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->addToolbar();		
		parent::display($tpl);
	}
	
	
	/**
	 * 
	 * add toolbar
	 */
	protected function addToolbar()
	{

		$title = JText::_('COM_NIVOSLIDERPRO'). " - ". JText::_('COM_NIVOSLIDERPRO_SLIDERS');
		JToolBarHelper::title($title , 'generic.png');
		
		JToolBarHelper::addNew('slider.add','JTOOLBAR_NEW');
		JToolBarHelper::editList('slider.edit','JTOOLBAR_EDIT');
		JToolBarHelper::deleteList('COM_NIVOSLIDERPRO_SLIDER_APPROVE_DELETE', 'sliders.delete','JTOOLBAR_DELETE');
		JToolBarHelper::divider();
		JToolBarHelper::custom('sliders.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
		JToolBarHelper::custom('sliders.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		//JToolBarHelper::divider();
		//JToolBarHelper::preferences('com_nivosliderpro', 300, 600);
	}
}