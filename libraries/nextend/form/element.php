<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.language.language');

class NextendElement {

    var $_form;
    var $_tab;
    var $_xml;
    var $_default;
    var $_name;
    var $_label;
    var $_description;
    var $_id;
    var $_inputname;
    var $_editableName = false;

    function NextendElement(&$form, &$tab, &$xml) {

        $this->_form = $form;
        $this->_tab = $tab;
        $this->_xml = $xml;
    }

    function render($control_name = 'params', $tooltip = true) {
        $this->control_name = $control_name;
        $this->_default = NextendXmlGetAttribute($this->_xml, 'default');
        $this->_name = NextendXmlGetAttribute($this->_xml, 'name');
        $this->_id = $this->generateId($control_name . $this->_name);
        $this->_inputname = (NextendXmlGetAttribute($this->_xml, 'hidename') ? '' : $control_name . '[' . $this->_name . ']');
        $this->_label = NextendXmlGetAttribute($this->_xml, 'label');
        $this->_description = NextendXmlGetAttribute($this->_xml, 'description');
        if ($this->_label == '')
            $this->_label = $this->_name;
        return array(
            $tooltip ? $this->fetchTooltip() : '',
            $this->fetchElement()
        );
    }

    function fetchTooltip() {
        if ($this->_label == '-')
            $this->_label = '';
        $output = '<label id="' . $this->_id . '-lbl" for="' . $this->_id . '">';
        if ($this->_editableName) {
            $element = new SimpleXMLElement('<param type="text" label="' . NextendText::_($this->_label) . '" default="**label**" name="' . $this->_name . 'customlabel" />');
            $customlabel = new NextendElementText($this->_form, $this, $element);
            $h = $customlabel->render($this->control_name);
            $output.= $h[1];
        } else {
            $output.= NextendText::_($this->_label);
        }
        return $output . '</label>';
    }

    function fetchNoTooltip() {

        return "";
    }

    function fetchElement() {
        
    }

    function generateId($name) {

        return str_replace(array(
            '[x]',
            '[',
            ']',
            '-x-',
            ' '
                ), array(
            '-x-',
            '',
            '',
            '[x]',
            ''
                ), $name);
    }

}
