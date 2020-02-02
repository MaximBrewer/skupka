<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.form.element.list');

class NextendElementCobaltCategories extends NextendElementList {

    var $_menutype = 'mainmenu';

    function fetchElement() {
        $menu = explode('|*|', $this->parent->_value);
				
        $currentsection = $this->parent->sections[$menu[0]];
        
        
        $query = "SELECT DISTINCT 
            id, 
            title,
            title AS name,
            parent_id,
            parent_id AS parent
            FROM #__js_res_categories
            WHERE section_id = ".intval($currentsection->id)." AND published = 1
            ORDER BY name ASC
        ";
        
        $db = JFactory::getDBO();

        $db->setQuery($query);
        $this->_categories = $db->loadObjectList();

        $children = array();
        if ($this->_categories) {
            foreach ($this->_categories as $v) {
                $pt = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', 1, '', array(), $children, 9999, 0, 0);
        $this->_xml->addChild('option', 'Root')->addAttribute('value', 0);
        if (count($options)) {
            foreach ($options AS $option) {
                $this->_xml->addChild('option', htmlspecialchars($option->treename))->addAttribute('value', $option->id);
            }
        }
        
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $html = parent::fetchElement();
        return $html;
    }
}
