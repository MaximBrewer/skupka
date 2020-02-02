<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJomSocialVideotype extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model('community_videos');

        $query = "SELECT type FROM #__community_videos GROUP BY type";
        $types = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', '');

        $this->_xml->addChild('option', htmlspecialchars(n2_('Youtube')))
                   ->addAttribute('value', 'youtube');

        if (count($types)) {
            foreach ($types AS $type) {
                if ($type->type != 'youtube') {
                    $this->_xml->addChild('option', ucfirst($type->type))
                               ->addAttribute('value', $type->type);
                }
            }
        }
        return parent::fetchElement();
    }
}