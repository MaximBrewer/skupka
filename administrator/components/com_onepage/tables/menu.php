<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Import JTableMenu
JLoader::register('JTableMenu', JPATH_PLATFORM . '/joomla/database/table/menu.php');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 */
class MenusTableMenu extends JTableMenu
{
	/**
	 * Method to delete a node and, optionally, its child nodes from the table.
	 *
	 * @param   integer  $pk        The primary key of the node to delete.
	 * @param   boolean  $children  True to delete child nodes, false to move them up a level.
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     http://docs.joomla.org/JTableNested/delete
	 * @since   2.5
	 */
	public function delete($pk = null, $children = false)
	{
		return parent::delete($pk, $children);
	}
	static $folders; 
	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True on success
	 *
	 * @see     JTable::check
	 * @since   11.1
	 */
	public function check()
	{
		// If the alias field is empty, set it to the title.
		$this->alias = trim($this->alias);

		// Cast the home property to an int for checking.
		$this->home = (int) $this->home;
		// Verify that a first level menu item alias is not the name of a folder.
		if (empty(self::$folders)) 
		{
		jimport('joomla.filesystem.folders');
		self::$folders = JFolder::folders(JPATH_ROOT); 
		}
		
		if ($this->parent_id == 1 && in_array($this->alias, self::$folders))
		{
			
			$this->alias = $this->alias.'-1';
		}

		// Verify that the home item a component.
		if ($this->home && $this->type != 'component')
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_HOME_NOT_COMPONENT'));
			return false;
		}

		return true;
	}

	
	public function checkAlias(&$table, $i=0)
	{
	
	  if ($table->load(array('alias' => $this->alias, 'parent_id' => $this->parent_id, 'client_id' => $this->client_id, 'language' => $this->language))
			&& ($table->id != $this->id || $this->id == 0))
		{
		  $i++; 
		  $this->alias .= '-'.$i; 
		  if (!$this->checkAlias($table, $i)) return false;
		  if ($i > 10) 
		  {

		  return false; 
		  }
		}
	 return true; 
	}
	
	/**
	 * Overloaded store function
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  mixed  False on failure, positive integer on success.
	 *
	 * @see     JTable::store
	 * @since   11.1
	 */
	public function store($updateNulls = false)
	{
		$db = JFactory::getDBO();
		// Verify that the alias is unique
		$table = JTable::getInstance('Menu', 'JTable');
		// re-do alias: 
		
		if (!$this->checkAlias($table))
		{
		    //stAn, this should never be reached
			if ($this->menutype == $table->menutype)
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_UNIQUE_ALIAS'));
			}
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_UNIQUE_ALIAS_ROOT'));
			}
			return false;
		}
		// Verify that the home page for this language is unique
		if ($this->home == '1')
		{
			$table = JTable::getInstance('Menu', 'JTable');
			if ($table->load(array('home' => '1', 'language' => $this->language)))
			{
				if ($table->checked_out && $table->checked_out != $this->checked_out)
				{
					$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_DEFAULT_CHECKIN_USER_MISMATCH'));
					return false;
				}
				$table->home = 0;
				$table->checked_out = 0;
				$table->checked_out_time = $db->getNullDate();
				$table->store();
			}
			// Verify that the home page for this menu is unique.
			if ($table->load(array('home' => '1', 'menutype' => $this->menutype)) && ($table->id != $this->id || $this->id == 0))
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_MENU_HOME_NOT_UNIQUE_IN_MENU'));
				return false;
			}
		}
		
		if (empty($this->parent_id))
		{


		}
		
		if (!parent::store($updateNulls))
		{
			return false;
		}
		// Get the new path in case the node was moved
		/*
		$pathNodes = $this->getPath();
		$segments = array();
		foreach ($pathNodes as $node)
		{
			// Don't include root in path
			if ($node->alias != 'root')
			{
				$segments[] = $node->alias;
			}
		}
		$newPath = trim(implode('/', $segments), ' /\\');
		*/
		$newPath = $this->path; 
		// Use new path for partial rebuild of table
		// Rebuild will return positive integer on success, false on failure


		
		//$parent = $this->{$this->_tbl_key}; 
		$parent = $this->parent_id; 
		return ($this->rebuild($parent, $this->lft, $this->level, $newPath) > 0);
	}
	
}
