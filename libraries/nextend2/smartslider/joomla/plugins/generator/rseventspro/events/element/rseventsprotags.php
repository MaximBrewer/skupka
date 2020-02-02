<?php

N2Loader::import('libraries.form.element.list');

class N2ElementRSEventsProTags extends N2ElementList
{

    function fetchElement() {
        $model  = new N2Model('rseventspro_tags');
        $query  = "SELECT id, name FROM #__rseventspro_tags WHERE published = 1";
        $groups = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);
        if (count($groups)) {
            foreach ($groups AS $group) {
                $this->_xml->addChild('option', htmlspecialchars($group->name))
                           ->addAttribute('value', $group->id);
            }
        }
        return parent::fetchElement();
    }

}
