<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendElementTextarea extends NextendElement {
    
    function fetchElement() {

        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('element/textarea.css');
        $html = "";
        $html.= "<div class='nextend-textarea' style='".NextendXmlGetAttribute($this->_xml, 'style')."'>";
        $html.= "<textarea id='" . $this->_id . "' style='".NextendXmlGetAttribute($this->_xml, 'style2')."' name='" . $this->_inputname . "' autocomplete='off'>" . $this->_form->get($this->_name, $this->_default) . "</textarea>";
        $html.= "</div>";
        return $html;
    }
}
