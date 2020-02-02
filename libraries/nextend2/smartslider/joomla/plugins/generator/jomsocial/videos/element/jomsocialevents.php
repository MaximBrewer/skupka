<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJomSocialEvents extends N2ElementList
{

    function fetchElement() {

        $model = new N2Model('community_events');

        $query  = "SELECT id, title FROM #__community_events ORDER BY id";
        $events = $model->db->queryAll($query, false, "object");

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);
        if (count($events)) {
            foreach ($events AS $event) {
                $this->_xml->addChild('option', htmlspecialchars($event->title))
                           ->addAttribute('value', $event->id);
            }
        }
        return parent::fetchElement();
    }
}