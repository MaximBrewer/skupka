<?php

N2Loader::import('libraries.form.element.list');
N2Loader::import('libraries.parse.parse');

class N2ElementCobaltCategories extends N2ElementList
{

    function fetchElement() {

        $db = new N2Model("js_res_sections");

        $section    = $this->_form->get('info');
        $section_id = $section->section_id;


        $query = "SELECT DISTINCT 
            id, 
            title,
            title AS name,
            parent_id,
            parent_id AS parent,
            section_id
            FROM #__js_res_categories
            WHERE section_id = '" . $section_id . "'
            ORDER BY lft ASC
        ";

        $categories = $db->db->queryAll($query, false, "object");

        $children = array();
        if ($categories) {
            foreach ($categories as $v) {
                $pt   = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', 1, '', array(), $children, 9999, 0, 0);

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
