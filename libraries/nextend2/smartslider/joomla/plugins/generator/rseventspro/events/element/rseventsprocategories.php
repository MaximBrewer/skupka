<?php

N2Loader::import('libraries.form.element.list');
jimport('joomla.access.access');

class N2ElementRSEventsProCategories extends N2ElementList
{

    function fetchElement() {
        $model     = new N2Model('rseventspro_categories');
        $query     = "SELECT id, name, parent_id, title FROM #__assets WHERE name LIKE '%com_rseventspro.category%' ORDER BY parent_id";
        $menuItems = $model->db->queryAll($query, false, "object");
        for ($i = 0; $i < count($menuItems); $i++) {
            $name = explode('.', $menuItems[$i]->name);
            @$menuItems[$i]->rsEventCatId = end($name);
        }

        $parentModel = new N2Model('rseventspro_categories_parent');
        $query       = "SELECT id FROM #__assets WHERE name = 'com_rseventspro' LIMIT 1";
        $mainParent  = $parentModel->db->queryAll($query, false, "object");

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
        $options = JHTML::_('menu.treerecurse', $mainParent[0]->id, '', array(), $children, 9999, 0, 0);

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);
        if (count($options)) {
            foreach ($options AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->treename))
                           ->addAttribute('value', $option->rsEventCatId);
            }
        }
        return parent::fetchElement();
    }
}
