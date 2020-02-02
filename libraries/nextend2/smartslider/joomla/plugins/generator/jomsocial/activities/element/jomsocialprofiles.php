<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJomSocialProfiles extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model('community_profiles');

        $query    = "SELECT id, name FROM #__community_profiles ORDER BY id";
        $profiles = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);
        if (count($profiles)) {
            foreach ($profiles AS $profile) {
                $this->_xml->addChild('option', htmlspecialchars($profile->name))
                           ->addAttribute('value', $profile->id);
            }
        }
        return parent::fetchElement();
    }
}