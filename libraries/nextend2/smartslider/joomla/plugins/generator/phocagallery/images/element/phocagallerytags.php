<?php

N2Loader::import('libraries.form.element.list');

class N2ElementPhocaGalleryTags extends N2ElementList
{

    function fetchElement() {

        $query = 'SELECT id, title FROM #__phocagallery_tags WHERE published = 1 ORDER BY ordering';

        $model = new N2Model('phocagallery_tags');
        $tags  = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);
        if (count($tags)) {
            foreach ($tags AS $tag) {
                $this->_xml->addChild('option', htmlspecialchars($tag->title))
                           ->addAttribute('value', $tag->id);
            }
        }
        return parent::fetchElement();
    }

}
