<?php

N2Loader::import('libraries.form.element.list');

class N2ElementZooTags extends N2ElementList
{

    function fetchElement() {
        $model = new N2Model('zoo_tag');
        $query = 'SELECT name FROM #__zoo_tag GROUP BY name';
        $tags  = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);

        if (count($tags)) {
            foreach ($tags AS $tag) {
                $tagName = htmlspecialchars($tag->name);
                $this->_xml->addChild('option', " - ".$tagName)
                           ->addAttribute('value', "'" . $tagName . "'");
            }
        }
        return parent::fetchElement();
    }

}
