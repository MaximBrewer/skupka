<?php

N2Loader::import('libraries.form.element.list');

class N2ElementFlexiTags extends N2ElementList
{

    function fetchElement() {

        $db = JFactory::getDBO();

        $query = 'SELECT id, name FROM #__flexicontent_tags WHERE published = 1 ORDER BY id';

        $db->setQuery($query);
        $menuItems = $db->loadObjectList();

        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', '0');

        if (count($menuItems)) {
            foreach ($menuItems AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->name))
                           ->addAttribute('value', $option->id);
            }
        }
        return parent::fetchElement();
    }
}
