<?php

N2Loader::import('libraries.form.element.list');

class N2ElementHikaShopBrands extends N2ElementList
{

    function fetchElement() {
        $model = new N2Model('hikashop_brands');

        $query = "SELECT category_id AS id, category_name AS title, category_name AS name,
        category_parent_id AS parent_id, category_parent_id AS parent FROM #__hikashop_category WHERE category_published = 1 AND category_type = 'manufacturer'";

        $menuItems = $model->db->queryAll($query, false, "object");

        $children = array();
        if ($menuItems) {
            foreach ($menuItems as $v) {
                $pt   = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', 1, '', array(), $children, 9999, 0, 0);
        $this->_xml->addChild('option', 'All')
                   ->addAttribute('value', 0);
        if (count($options)) {
            foreach ($options AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->treename))
                           ->addAttribute('value', $option->id);
            }
        }
        return parent::fetchElement();
    }

}
