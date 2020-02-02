<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEasySocialGroups extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model('social_clusters_categories');

        $query      = "SELECT id, title FROM #__social_clusters WHERE state = 1 AND cluster_type='group' ORDER BY id";
        $categories = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', '0');

        if (count($categories)) {
            foreach ($categories AS $category) {
                $this->_xml->addChild('option', htmlspecialchars($category->title))
                           ->addAttribute('value', $category->id);
            }
        }
        return parent::fetchElement();
    }
}