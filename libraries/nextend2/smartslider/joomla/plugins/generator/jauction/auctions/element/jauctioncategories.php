<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJAuctionCategories extends N2ElementList
{

    function fetchElement() {

        $db = JFactory::getDBO();

        $query = "SELECT catid AS id, alias AS title, parent AS parent_id FROM #__productcat_node WHERE publish = 1 ORDER BY ordering";

        $db->setQuery($query);
        $menuItems = $db->loadObjectList();

        $children = array();
        if ($menuItems) {
            foreach ($menuItems as $v) {
                $pt   = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', 1, '', array(), $children, 9999, 0, 0);
        $this->_xml->addChild('option', htmlspecialchars(n2_('All')))
                   ->addAttribute('value', 0);

        if (count($options)) {
            foreach ($options AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->treename))
                           ->addAttribute('value', $option->id);
            }
        }
        return parent::fetchElement();
    }

}
