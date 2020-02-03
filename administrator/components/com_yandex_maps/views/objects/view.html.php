<?php
defined("_JEXEC") or die("Access deny");
class Yandex_MapsViewObjects extends JViewLegacy{
	protected $items;
	protected $pagination;
	protected $state;

	function display($tpl=null) {
		JToolBarHelper::Title('Список объектов ('.$this->get('Total').')');
		JToolBarHelper::addNew('objects.add');

		$params = JComponentHelper::getParams('com_yandex_maps');
		if ($params->get('registration_organization_active',1)) {
			JToolBarHelper::addNew('objects.addorg', 'Добавить организацию');
		}

		JToolbarHelper::publish('objects.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('objects.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::deleteList("Вы уверены?",'objects.delete');
		JToolBarHelper::custom('objects.gototrash', 'trash', '',  'Корзина', false);
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		parent::display($tpl);
	}
}