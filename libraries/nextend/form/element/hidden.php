<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendElementHidden extends NextendElement {
    
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

        return "<input id='" . $this->_id . "' name='" . $this->_inputname . "' value='" . htmlspecialchars($this->_form->get($this->_name, $this->_default), ENT_QUOTES) . "' type='".$this->_mode."' autocomplete='off' />";
    }
}
