<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJMarketLanguages extends N2ElementList
{

    function fetchElement() {

        $db = JFactory::getDBO();

        $query = "SELECT lgid, name FROM #__language_node WHERE lgid IN (SELECT lgid FROM #__product_trans)";

        $db->setQuery($query);
        $menuItems = $db->loadObjectList();

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);

        if (count($menuItems)) {
            foreach ($menuItems AS $item) {
                $this->_xml->addChild('option', htmlspecialchars($item->name))
                           ->addAttribute('value', $item->lgid);
            }
        }
        return parent::fetchElement();
    }

}
