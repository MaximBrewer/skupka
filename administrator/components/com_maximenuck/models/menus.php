<?php

/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Maximenuck records.
 */
class MaximenuckModelMenus extends JModelList {

	protected $_context		= 'com_maximenuck.menus';

	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'menutype', 'a.menutype',
				'title', 'a.title'
			);
		}

		parent::__construct($config);
	}

	public function getListQuery()
	{

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true)
			->select('title, menutype')
			->from('#__menu_types as a');
			// ->order('title');

		// Filter by search in title.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.title LIKE ' . $search . ' OR a.menutype LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.title');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		$search = $this->getUserStateFromRequest($this->context.'.search', 'filter_search');
		$this->setState('filter.search', $search);

		$menuType = $app->input->get('menutype', null);
		if ($menuType)
		{
			if ($menuType != $app->getUserState($this->context.'.filter.menutype'))
			{
				$app->setUserState($this->context.'.filter.menutype', $menuType);
				$app->input->set('limitstart', 0);
			}
		}
		else
		{
			$menuType = $app->getUserState($this->context.'.filter.menutype');

			if (!$menuType)
			{
				$menuType = $this->getDefaultMenuType();
			}
		}

		$this->setState('filter.menutype', $menuType);

		// Component parameters.
		$params	= JComponentHelper::getParams('com_maximenuck');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.title', 'asc');
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id	A prefix for the store id.
	 *
	 * @return  string  A store id.
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.language');
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.parent_id');
		$id	.= ':'.$this->getState('filter.menutype');

		return parent::getStoreId($id);
	}

	/**
	 * Finds the default menu type.
	 *
	 * In the absence of better information, this is the first menu ordered by title.
	 *
	 * @return  string	The default menu type
	 * @since   1.6
	 */
	protected function getDefaultMenuType()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true)
			->select('menutype')
			->from('#__menu_types')
			->order('title');
		$db->setQuery($query, 0, 1);
		$menuType = $db->loadResult();

		return $menuType;
	}


}
