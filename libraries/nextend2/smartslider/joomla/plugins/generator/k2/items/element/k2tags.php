<?php

N2Loader::import('libraries.form.element.list');

class N2ElementK2tags extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model('k2_tags');

        $query = 'SELECT id, name FROM #__k2_tags WHERE published = 1 ORDER BY id';

        $tags = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);

        if (count($tags)) {
            foreach ($tags AS $tag) {
                $this->_xml->addChild('option', htmlspecialchars($tag->name))
                           ->addAttribute('value', $tag->id);
            }
        }
        return parent::fetchElement();
    }

}
