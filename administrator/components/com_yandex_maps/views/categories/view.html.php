<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewCategories extends JViewLegacy{
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl=null) {
		$tpl = JRequest::getCmd('layout', null);
		JToolBarHelper::Title('Список категорий ('.$this->get('Total').')');
		JToolBarHelper::addNew('categories.add');
		JToolBarHelper::deleteList("Вы уверены?",'categories.delete');
		JToolBarHelper::custom('categories.gototrash', 'trash', '',  'Корзина', false);
		
		$this->items = $this->get('Items');

		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		parent::display($tpl);
	}
}