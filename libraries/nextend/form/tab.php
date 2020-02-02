<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.element');

class NextendTab {

    var $_form;

    var $_xml;

    var $_name;

    var $_attributes;

    var $_elements;

    function NextendTab(&$form, &$xml) {

        $this->_form = $form;
        $this->_xml = $xml;
        $this->_name = NextendXmlGetAttribute($xml, 'name');
        $this->_hidetitle = NextendXmlGetAttribute($xml, 'hidetitle');
        $this->initElements();
    }

    function initElements() {

        $this->_elements = array();
        foreach ($this->_xml->param AS $element) {
            $test = NextendXmlGetAttribute($element, 'test');
            if(!empty($test) && (!isset($this->_form->{$test}) || !$this->_form->{$test})) continue;
            $type = NextendXmlGetAttribute($element, 'type');
            $name = NextendXmlGetAttribute($element, 'name');
            $class = 'NextendElement' . $type;
            if (!class_exists($class, false)) {
                if (!nextendimportpath($this->_form->_xmlfolder . 'element/' . $type)) {
                    nextendimport('nextend.form.element.' . $type);
                }
            }
            $this->_elements[$name] = new $class($this->_form, $this, $element);
        }
    }

    function render($control_name) {

        $this->decorateTitle();
        $this->decorateGroupStart();
        $keys = array_keys($this->_elements);
        for ($i = 0; $i < count($keys); $i++) {
            $this->decorateElement($this->_elements[$keys[$i]], $this->_elements[$keys[$i]]->render($control_name), $i);
        }
        $this->decorateGroupEnd();
    }

    function decorateTitle() {
        echo "<div class='nextend-tab'>";
        if ($this->_hidetitle != 1) echo "<h3>" . NextendText::_(NextendXmlGetAttribute($this->_xml, 'label')) . "</h3>";
    }

    function decorateGroupStart() {

        echo "<table>";
    }

    function decorateGroupEnd() {

        echo "</table>";
        echo "</div>";
    }

    function decorateElement(&$el, $out, $i) {
        $class = 'odd';
        if ($i % 2) $class = 'even';
        echo "<tr class='" . $class . "'>";
        $title = NextendXmlGetAttribute($el->_xml, 'description');
        $class = '';
        if ($title != '') {
            $class = ' nextend-hastip';
            $title = ' title="' . NextendText::_($title) . '"';
        }
        echo "<td class='nextend-label" . $class . "' " . $title . ">" . $out[0] . "</td>";
        echo "<td class='nextend-element'>" . $out[1] . "</td>";
        echo "</tr>";
    }
}

?>