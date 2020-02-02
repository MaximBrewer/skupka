<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEasyDiscussTags extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model('discuss_tags');

        $query = 'SELECT * FROM #__discuss_tags WHERE published = 1 ORDER BY id';

        $menuItems = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', '0');

        if (count($menuItems)) {
            foreach ($menuItems AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->title))
                           ->addAttribute('value', $option->id);
            }
        }
        return parent::fetchElement();
    }
}