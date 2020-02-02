<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewMaps extends JViewLegacy{
	protected $items;

	protected $pagination;

	protected $state;

	function display($tpl=null) {
		$tpl = JRequest::getCmd('layout', null);
		JToolBarHelper::Title('Список карт ('.$this->get('Total').')');
		JToolBarHelper::addNew('maps.add');
		JToolBarHelper::deleteList("Вы уверены?",'maps.delete');
		JToolBarHelper::custom('maps.gototrash', 'trash', '',  'Корзина', false);
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		parent::display($tpl);
	}
	protected function getSortFields() {
		return array(
			'a.ordering'     => JText::_('JGRID_HEADING_ORDERING'),
			'a.title'        => JText::_('JGLOBAL_TITLE'),
			'a.modified_time'=> JText::_('JDATE'),
			'a.id'           => JText::_('JGRID_HEADING_ID'),
		);
	}
}