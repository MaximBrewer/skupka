<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJoomShoppingManufacturers extends N2ElementList
{

    function fetchElement() {

        $db = JFactory::getDBO();

        require_once(JPATH_SITE . "/components/com_jshopping/lib/factory.php");
        $lang = JSFactory::getLang();

        $query = "SELECT manufacturer_id AS id, `" . $lang->get('name') . "` AS title
              FROM #__jshopping_manufacturers
              WHERE manufacturer_publish = 1
              ORDER BY ordering";

        $db->setQuery($query);
        $menuItems = $db->loadObjectList();

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);

        if (count($menuItems)) {
            foreach ($menuItems AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->title))
                           ->addAttribute('value', $option->id);
            }
        }
        return parent::fetchElement();
    }

}
