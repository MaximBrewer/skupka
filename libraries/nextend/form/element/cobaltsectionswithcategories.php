<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.element.mixed');
nextendimport('nextend.form.element.joomlamenu');
nextendimport('nextend.form.element.joomlamenuitems');

class NextendElementCobaltSectionsWithCategories extends NextendElementMixed {
    function fetchElement() {
        
        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'element.js');
        $js->addLibraryJsAssetsFile('dojo', 'element/menuwithitems.js');
        
        $html = '';
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $value = explode('|*|', $this->_value);
        
        $db = JFactory::getDBO();
        
        $query = "SELECT 
            id, 
            name,
            name as title,
            0 AS parent,
            0 AS parent_id
            FROM #__js_res_sections ORDER BY name ASC";
        
        $db->setQuery($query);
        $this->sections = $db->loadObjectList('id');
        
        if(!isset($this->sections[$value[0]])){
          $keys = array_keys($this->sections);
          $this->_form->set($this->_name, $keys[0].'|*|0');
        }
        $html.= parent::fetchElement();
        
        $this->groupedList = array();
        
        jimport('joomla.html.html.menu');
        foreach($this->sections AS $section){
            $this->groupedList[$section->id] = array();
            $query = "SELECT DISTINCT 
                id, 
                title,
                title AS name,
                parent_id,
                parent_id AS parent
                FROM #__js_res_categories
                WHERE section_id = ".intval($section->id)." AND published = 1
                ORDER BY name ASC
            ";
            
            $db->setQuery($query);
            $categories = $db->loadObjectList();
    
            $children = array();
            if ($categories) {
                foreach ($categories as $v) {
                    $pt = $v->parent_id;
                    $list = isset($children[$pt]) ? $children[$pt] : array();
                    array_push($list, $v);
                    $children[$pt] = $list;
                }
            }
            $options = JHTML::_('menu.treerecurse', 1, '', array(), $children, 9999, 0, 0);

            foreach ($options AS $option) {
                $this->groupedList[$section->id][] = array($option->id, $option->treename);
            }
        }
        
        $js->addLibraryJs('dojo', '
            new NextendElementMenuWithItems({
              hidden: "' . $this->_id . '",
              options: ' . json_encode($this->groupedList) . ',
              value: "'.$this->_value.'"
            });
        ');
        
        return $html;
    }

    
    function renderCategory($parent, $pre, $appid){
      if(isset($this->cats[$parent])){
          foreach($this->cats[$parent] AS $category){
              $this->groupedList[$appid][] = array($category->id, $pre.$category->name);
              $this->renderCategory($category->id, $pre.' - ', $appid);
          }
      }
    }
}