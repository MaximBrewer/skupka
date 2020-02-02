<?php
/**
 * @name		Menu Manager CK
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.application.component.modellist');

/**
 * Menu Item List Model for Menus.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_maximenuck
 * @since       1.6
 */
class MaximenuckModelMigration extends JModelList
{
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
				'id', 'a.id',
				'menutype', 'a.menutype',
				'title', 'a.title',
				'alias', 'a.alias',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'language', 'a.language',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'lft', 'a.lft',
				'rgt', 'a.rgt',
				'level', 'a.level',
				'path', 'a.path',
				'client_id', 'a.client_id',
				'home', 'a.home',
			);

			$app = JFactory::getApplication();
			$assoc = isset($app->item_associations) ? $app->item_associations : 0;
			if ($assoc)
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
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

		$published = $this->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$parentId = $this->getUserStateFromRequest($this->context.'.filter.parent_id', 'filter_parent_id', 0, 'int');
		$this->setState('filter.parent_id',	$parentId);

		$level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', 0, 'int');
		$this->setState('filter.level', $level);

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

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Component parameters.
		$params	= JComponentHelper::getParams('com_maximenuck');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.lft', 'asc');
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

	/**
	 * Builds an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery	A query object.
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();
		$app	= JFactory::getApplication();

		// Select all fields from the table.
		$query->select(
			$this->getState('list.select',
				$db->quoteName(
					array('a.id', 'a.menutype', 'a.title', 'a.alias', 'a.note', 'a.path', 'a.link', 'a.type', 'a.parent_id', 'a.level', 'a.published', 'a.component_id', 'a.checked_out', 'a.checked_out_time', 'a.browserNav', 'a.access', 'a.img', 'a.template_style_id', 'a.params', 'a.lft', 'a.rgt', 'a.home', 'a.language', 'a.client_id'),
					array(null, null, null, null, null, null, null, null, null, null, 'apublished', null, null, null, null, null, null, null, null, null, null, null, null, null)
				)
			)
		);
		$query->select('CASE a.type' .
			' WHEN ' . $db->quote('component') . ' THEN a.published+2*(e.enabled-1) ' .
			' WHEN ' . $db->quote('url') . ' THEN a.published+2 ' .
			' WHEN ' . $db->quote('alias') . ' THEN a.published+4 ' .
			' WHEN ' . $db->quote('separator') . ' THEN a.published+6 ' .
			' WHEN ' . $db->quote('heading') . ' THEN a.published+8 ' .
			' END AS published');
		$query->from($db->quoteName('#__menu').' AS a');

		// Join over the language
		$query->select('l.title AS language_title, l.image as image');
		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');

		// Join over the users.
		$query->select('u.name AS editor');
		$query->join('LEFT', $db->quoteName('#__users').' AS u ON u.id = a.checked_out');

		//Join over components
		$query->select('c.element AS componentname');
		$query->join('LEFT', $db->quoteName('#__extensions').' AS c ON c.extension_id = a.component_id');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the associations.
		$assoc = isset($app->item_associations) ? $app->item_associations : 0;
		if ($assoc)
		{
			$query->select('COUNT(asso2.id)>1 as association');
			$query->join('LEFT', '#__associations AS asso ON asso.id = a.id AND asso.context='.$db->quote('com_maximenuck.item'));
			$query->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key');
			$query->group('a.id');
		}

		// Join over the extensions
		$query->select('e.name AS name');
		$query->join('LEFT', '#__extensions AS e ON e.extension_id = a.component_id');

		// Exclude the root category.
		$query->where('a.id > 1');
		$query->where('a.client_id = 0');

		// Filter on the published state.
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = '.(int) $published);
		} elseif ($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}

		// Filter the items over the menu id if set.
		$menuType = $this->getState('filter.menutype');
		if (!empty($menuType))
		{
			$query->where('a.menutype = '.$db->quote($menuType));
		}


		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}

		// Add the list ordering clause.
		$query->order($db->escape('a.lft').' '.$db->escape('ASC'));

		//echo nl2br(str_replace('#__','jos_',(string)$query)).'<hr/>';
		return $query;
	}
	
	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$query = $this->_getListQuery();

		try
		{
			// $items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
			$items = $this->_getList($query);
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $this->arrangeItems($items);

		return $this->cache[$store];
	}
	
	
	public static function arrangeItems($items)
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu();

		$start   = 1;
		$end     = 0;
		$showAll = 1;

			$lastitem = 0;

			if ($items)
			{
				foreach ($items as $i => $item)
				{

					$item->deeper     = false;
					$item->shallower  = false;
					$item->level_diff = 0;

					if (isset($items[$lastitem]))
					{
						$items[$lastitem]->deeper     = ($item->level > $items[$lastitem]->level);
						$items[$lastitem]->shallower  = ($item->level < $items[$lastitem]->level);
						$items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
					}

					$lastitem     = $i;
					$item->active = false;

				}

				if (isset($items[$lastitem]))
				{
					$items[$lastitem]->deeper     = (($start?$start:1) > $items[$lastitem]->level);
					$items[$lastitem]->shallower  = (($start?$start:1) < $items[$lastitem]->level);
					$items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start?$start:1));
				}
			}
		return $items;
	}
	
	/***for menu types ***/
	
	/**
	 * A reverse lookup of the base link URL to Title
	 *
	 * @var	array
	 */
	protected $rlu = array();

	/**
	 * Method to get the reverse lookup of the base link URL to Title
	 *
	 * @return  array  Array of reverse lookup of the base link URL to Title
	 * @since   1.6
	 */
	public function getReverseLookup()
	{
		if (empty($this->rlu))
		{
			$this->getTypeOptions();
		}
		return $this->rlu;
	}

	/**
	 * Method to get the available menu item type options.
	 *
	 * @return  array  Array of groups with menu item types.
	 * @since   1.6
	 */
	public function getTypeOptions()
	{
		jimport('joomla.filesystem.file');

		$lang = JFactory::getLanguage();
		$list = array();

		// Get the list of components.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('name, element AS ' . $db->qn('option'));
		$query->from('#__extensions');
		$query->where('type = ' . $db->q('component'));
		$query->where('enabled = 1');
		$query->order('name ASC');
		$db->setQuery($query);
		$components = $db->loadObjectList();

		foreach ($components as $component)
		{
			if ($options = $this->getTypeOptionsByComponent($component->option))
			{
				$list[$component->name] = $options;

				// Create the reverse lookup for link-to-name.
				foreach ($options as $option)
				{
					if (isset($option->request))
					{
						$this->rlu[MaximenuckHelper::getLinkKey($option->request)] = $option->get('title');

						if (isset($option->request['option']))
						{
								$lang->load($option->request['option'].'.sys', JPATH_ADMINISTRATOR, null, false, false)
							||	$lang->load($option->request['option'].'.sys', JPATH_ADMINISTRATOR.'/components/'.$option->request['option'], null, false, false)
							||	$lang->load($option->request['option'].'.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
							||	$lang->load($option->request['option'].'.sys', JPATH_ADMINISTRATOR.'/components/'.$option->request['option'], $lang->getDefault(), false, false);
						}
					}
				}
			}
		}

		return $list;
	}

	protected function getTypeOptionsByComponent($component)
	{
		$options = array();

		$mainXML = JPATH_SITE.'/components/'.$component.'/metadata.xml';

		if (is_file($mainXML))
		{
			$options = $this->getTypeOptionsFromXML($mainXML, $component);
		}

		if (empty($options))
		{
			$options = $this->getTypeOptionsFromMVC($component);
		}

		return $options;
	}

	protected function getTypeOptionsFromXML($file, $component)
	{
		$options = array();

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($file))
		{
			return false;
		}

		// Look for the first menu node off of the root node.
		if (!$menu = $xml->xpath('menu[1]'))
		{
			return false;
		}
		else
		{
			$menu = $menu[0];
		}

		// If we have no options to parse, just add the base component to the list of options.
		if (!empty($menu['options']) && $menu['options'] == 'none')
		{
			// Create the menu option for the component.
			$o = new JObject;
			$o->title		= (string) $menu['name'];
			$o->description	= (string) $menu['msg'];
			$o->request		= array('option' => $component);

			$options[] = $o;

			return $options;
		}

		// Look for the first options node off of the menu node.
		if (!$optionsNode = $menu->xpath('options[1]'))
		{
			return false;
		}
		else
		{
			$optionsNode = $optionsNode[0];
		}

		// Make sure the options node has children.
		if (!$children = $optionsNode->children())
		{
			return false;
		}
		else
		{
			// Process each child as an option.
			foreach ($children as $child)
			{
				if ($child->getName() == 'option')
				{
					// Create the menu option for the component.
					$o = new JObject;
					$o->title		= (string) $child['name'];
					$o->description	= (string) $child['msg'];
					$o->request		= array('option' => $component, (string) $optionsNode['var'] => (string) $child['value']);

					$options[] = $o;
				}
				elseif ($child->getName() == 'default')
				{
					// Create the menu option for the component.
					$o = new JObject;
					$o->title		= (string) $child['name'];
					$o->description	= (string) $child['msg'];
					$o->request		= array('option' => $component);

					$options[] = $o;
				}
			}
		}

		return $options;
	}

	protected function getTypeOptionsFromMVC($component)
	{
		$options = array();

		// Get the views for this component.
		$path = JPATH_SITE . '/components/' . $component . '/views';

		if (is_dir($path))
		{
			$views = JFolder::folders($path);
		}
		else
		{
			return false;
		}

		foreach ($views as $view)
		{
			// Ignore private views.
			if (strpos($view, '_') !== 0)
			{
				// Determine if a metadata file exists for the view.
				$file = $path.'/'.$view.'/metadata.xml';

				if (is_file($file))
				{
					// Attempt to load the xml file.
					if ($xml = simplexml_load_file($file))
					{
						// Look for the first view node off of the root node.
						if ($menu = $xml->xpath('view[1]'))
						{
							$menu = $menu[0];

							// If the view is hidden from the menu, discard it and move on to the next view.
							if (!empty($menu['hidden']) && $menu['hidden'] == 'true')
							{
								unset($xml);
								continue;
							}

							// Do we have an options node or should we process layouts?
							// Look for the first options node off of the menu node.
							if ($optionsNode = $menu->xpath('options[1]'))
							{
								$optionsNode = $optionsNode[0];

								// Make sure the options node has children.
								if ($children = $optionsNode->children())
								{
									// Process each child as an option.
									foreach ($children as $child)
									{
										if ($child->getName() == 'option')
										{
											// Create the menu option for the component.
											$o = new JObject;
											$o->title		= (string) $child['name'];
											$o->description	= (string) $child['msg'];
											$o->request		= array('option' => $component, 'view' => $view, (string) $optionsNode['var'] => (string) $child['value']);

											$options[] = $o;
										}
										elseif ($child->getName() == 'default')
										{
											// Create the menu option for the component.
											$o = new JObject;
											$o->title		= (string) $child['name'];
											$o->description	= (string) $child['msg'];
											$o->request		= array('option' => $component, 'view' => $view);

											$options[] = $o;
										}
									}
								}
							}
							else {
								$options = array_merge($options, (array) $this->getTypeOptionsFromLayouts($component, $view));
							}
						}
						unset($xml);
					}

				}
				else {
					$options = array_merge($options, (array) $this->getTypeOptionsFromLayouts($component, $view));
				}
			}
		}

		return $options;
	}

	protected function getTypeOptionsFromLayouts($component, $view)
	{
		$options = array();
		$layouts = array();
		$layoutNames = array();
		$templateLayouts = array();
		$lang = JFactory::getLanguage();

		// Get the layouts from the view folder.
		$path = JPATH_SITE . '/components/' . $component . '/views/' . $view . '/tmpl';
		if (is_dir($path))
		{
			$layouts = array_merge($layouts, JFolder::files($path, '.xml$', false, true));
		}
		else
		{
			return $options;
		}

		// build list of standard layout names
		foreach ($layouts as $layout)
		{
			// Ignore private layouts.
			if (strpos(basename($layout), '_') === false)
			{
				$file = $layout;
				// Get the layout name.
				$layoutNames[] = basename($layout, '.xml');
			}
		}

		// get the template layouts
		// TODO: This should only search one template -- the current template for this item (default of specified)
		$folders = JFolder::folders(JPATH_SITE . '/templates', '', false, true);
		// Array to hold association between template file names and templates
		$templateName = array();
		foreach ($folders as $folder)
		{
			if (is_dir($folder . '/html/' . $component . '/' . $view))
			{
				$template = basename($folder);
				$lang->load('tpl_'.$template.'.sys', JPATH_SITE, null, false, false)
				||	$lang->load('tpl_'.$template.'.sys', JPATH_SITE.'/templates/'.$template, null, false, false)
				||	$lang->load('tpl_'.$template.'.sys', JPATH_SITE, $lang->getDefault(), false, false)
				||	$lang->load('tpl_'.$template.'.sys', JPATH_SITE.'/templates/'.$template, $lang->getDefault(), false, false);

				$templateLayouts = JFolder::files($folder . '/html/' . $component . '/' . $view, '.xml$', false, true);

				foreach ($templateLayouts as $layout)
				{
					$file = $layout;
					// Get the layout name.
					$templateLayoutName = basename($layout, '.xml');

					// add to the list only if it is not a standard layout
					if (array_search($templateLayoutName, $layoutNames) === false)
					{
						$layouts[] = $layout;
						// Set template name array so we can get the right template for the layout
						$templateName[$layout] = basename($folder);
					}
				}
			}
		}

		// Process the found layouts.
		foreach ($layouts as $layout)
		{
			// Ignore private layouts.
			if (strpos(basename($layout), '_') === false)
			{
				$file = $layout;
				// Get the layout name.
				$layout = basename($layout, '.xml');

				// Create the menu option for the layout.
				$o = new JObject;
				$o->title		= ucfirst($layout);
				$o->description	= '';
				$o->request		= array('option' => $component, 'view' => $view);

				// Only add the layout request argument if not the default layout.
				if ($layout != 'default')
				{
					// If the template is set, add in format template:layout so we save the template name
					$o->request['layout'] = (isset($templateName[$file])) ? $templateName[$file] . ':' . $layout : $layout;
				}

				// Load layout metadata if it exists.
				if (is_file($file))
				{
					// Attempt to load the xml file.
					if ($xml = simplexml_load_file($file))
					{
						// Look for the first view node off of the root node.
						if ($menu = $xml->xpath('layout[1]'))
						{
							$menu = $menu[0];

							// If the view is hidden from the menu, discard it and move on to the next view.
							if (!empty($menu['hidden']) && $menu['hidden'] == 'true')
							{
								unset($xml);
								unset($o);
								continue;
							}

							// Populate the title and description if they exist.
							if (!empty($menu['title']))
							{
								$o->title = trim((string) $menu['title']);
							}

							if (!empty($menu->message[0]))
							{
								$o->description = trim((string) $menu->message[0]);
							}
						}
					}
				}

				// Add the layout to the options array.
				$options[] = $o;
			}
		}

		return $options;
	}
	
	public function getMenus()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true)
			->select('title, menutype')
			->from('#__menu_types')
			->order('title');
		$db->setQuery($query);
		$menus = $db->loadObjectList();

		return $menus;
	}
}
