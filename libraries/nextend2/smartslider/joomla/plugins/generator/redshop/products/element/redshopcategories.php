<?php

N2Loader::import('libraries.form.element.list');

class N2ElementRedShopCategories extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model('redshop_category');

        $query = 'SELECT
                    m.category_id AS id, 
                    m.category_name AS name, 
                    m.category_name AS title, 
                    f.category_parent_id AS parent, 
                    f.category_parent_id as parent_id
                FROM #__redshop_category m
                LEFT JOIN #__redshop_category_xref AS f ON m.category_id = f.category_child_id
                WHERE m.published = 1
                ORDER BY m.ordering';

        $menuItems = $model->db->queryAll($query, false, "object");
        $children  = array();
        if ($menuItems) {
            foreach ($menuItems as $v) {
                $pt   = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
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
