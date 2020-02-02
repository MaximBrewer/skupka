<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJoomShoppingLabels extends N2ElementList
{

    function fetchElement() {

        $db = JFactory::getDBO();

        require_once(JPATH_SITE . "/components/com_jshopping/lib/factory.php");
        $lang = JSFactory::getLang();

        $query = "SELECT id, `" . $lang->get('name') . "` AS name
              FROM #__jshopping_product_labels
              ORDER BY name";

        $db->setQuery($query);
        $labels = $db->loadObjectList();

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', -1);

        $this->_xml->addChild('option', htmlspecialchars(n2_('None')))
                   ->addAttribute('value', 0);
        if (count($labels)) {
            foreach ($labels AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->name))
                           ->addAttribute('value', $option->id);
            }
        }
        return parent::fetchElement();
    }

}
