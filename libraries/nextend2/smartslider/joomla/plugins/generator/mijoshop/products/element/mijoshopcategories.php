<?php

N2Loader::import('libraries.form.element.list');

class N2ElementMijoShopCategories extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model("mijoshop_category");

        $lang   = '';
        $config = MijoShop::get('opencart')
                          ->get('config');
        if (is_object($config)) {
            $lang = ' AND cd.language_id = ' . $config->get('config_language_id');
        }

        $query = 'SELECT 
                    m.category_id AS id, 
                    cd.name AS name, 
                    cd.name AS title, 
                    m.parent_id AS parent, 
                    m.parent_id as parent_id
                FROM #__mijoshop_category m
                LEFT JOIN #__mijoshop_category_description AS cd ON cd.category_id = m.category_id
                WHERE m.status = 1 ' . $lang . '
                ORDER BY m.sort_order';

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
