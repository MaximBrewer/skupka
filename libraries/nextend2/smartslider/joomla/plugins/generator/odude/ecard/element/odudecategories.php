<?php

N2Loader::import('libraries.form.element.list');

class N2ElementODudeCategories extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model('ecard_cate');

        $query     = 'SELECT cat AS id, subcat AS parent_id, name AS title FROM #__ecard_cate ORDER BY parent_id';
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
