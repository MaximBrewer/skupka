<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendElementButton extends NextendElement {
    
    var $_mode = 'hidden';
    
    var $_tooltip = false;
    
    function fetchTooltip() {
        if($this->_tooltip){
            return parent::fetchTooltip();
        }else{
            return $this->fetchNoTooltip();
        }
    }
    
    function fetchElement() {
        $href = "href='#' onclick='return false;'";
        $url = NextendXmlGetAttribute($this->_xml, 'url');
        if($url){
            $href = "href='".$url."' target='".NextendXmlGetAttribute($this->_xml, 'target')."'";
        }
        return "<a ".$href." id='" . $this->_id . "' class='button' >".$this->_label."</a>";
    }
}
