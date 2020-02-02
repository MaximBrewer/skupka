<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.element.list');

class NextendElementCobaltSections extends NextendElementList{
    
    function fetchElement() {
        foreach($this->parent->sections AS $section){
            $this->_xml->addChild('option', htmlspecialchars(ucfirst($section->name)))->addAttribute('value', $section->id);
        }
        
        $this->_value = $this->_form->get($this->_name, $this->_default);
        
        $html = parent::fetchElement();
        
        return $html;
    }
    
}