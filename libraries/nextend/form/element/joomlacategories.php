<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.form.element.list');

class NextendElementJoomlacategories extends NextendElementList {

    function fetchElement() {

        $db = JFactory::getDBO();
        
        $query = 'SELECT 
                    m.id, 
                    m.title AS name, 
                    m.title, 
                    m.parent_id AS parent, 
                    m.parent_id
                FROM #__categories m
                WHERE m.published = 1 AND (m.extension = "com_content" OR m.extension = "system")
                ORDER BY m.lft';


        $db->setQuery($query);
        $menuItems = $db->loadObjectList();
        $children = array();
        if ($menuItems) {
            foreach ($menuItems as $v) {
                $pt = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        if (count($options)) {
            foreach ($options AS $option) {
                $this->_xml->addChild('option', $option->treename)->addAttribute('value', $option->id);
            }
        }
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $html = parent::fetchElement();
        return $html;
    }

}
