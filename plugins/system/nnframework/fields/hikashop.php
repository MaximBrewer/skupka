<?php
/**
 * Element: HikaShop
 *
 * @package         NoNumber Framework
 * @version         15.9.5525
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/groupfield.php';

class JFormFieldNN_HikaShop extends NNFormGroupField
{
	public $type = 'HikaShop';

	protected function getInput()
	{
		if ($error = $this->missingFilesOrTables(array('categories' => 'category', 'products' => 'product')))
		{
			return $error;
		}

		return $this->getSelectList();
	}

	function getCategories()
	{
		$query = $this->db->getQuery(true)
			->select('COUNT(*)')
			->from('#__hikashop_category AS c')
			->where('c.category_published > -1');
		$this->db->setQuery($query);
		$total = $this->db->loadResult();

		if ($total > $this->max_list_count)
		{
			return -1;
		}

		$query->clear()
			->select('c.category_id')
			->from('#__hikashop_category AS c')
			->where('c.category_type = ' . $this->db->quote('root'));
		$this->db->setQuery($query);
		$root = (int) $this->db->loadResult();

		$query->clear()
			->select('c.category_id as id, c.category_parent_id AS parent_id, c.category_name AS title, c.category_published as published')
			->from('#__hikashop_category AS c')
			->where('c.category_type = ' . $this->db->quote('product'))
			->where('c.category_published > -1')
			->order('c.category_ordering, c.category_name');
		$this->db->setQuery($query);
		$items = $this->db->loadObjectList();

		return $this->getOptionsTreeByList($items, $root);
	}

	function getProducts()
	{
		$query = $this->db->getQuery(true)
			->select('p.product_id as id, p.product_name AS name, c.category_name AS cat, p.product_published AS published')
			->from('#__hikashop_product AS p')
			->join('LEFT', '#__hikashop_product_category AS x ON x.product_id = p.product_id')
			->join('LEFT', '#__hikashop_category AS c ON c.category_id = x.category_id')
			->where('p.product_published > -1')
			->order('p.product_name, p.product_id');
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();

		return $this->getOptionsByList($list, array('cat', 'id'));
	}
}
