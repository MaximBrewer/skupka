<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.form.tab');

class NextendTabFontmatrix extends NextendTab {
    
    var $biggestindex = 0;

    function NextendTabFontmatrix(&$form, &$xml) {
        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('tabs/fontmatrix.css');
        $this->_matrixname = NextendXmlGetAttribute($xml, 'name');
        parent::NextendTab($form, $xml);
    }

    function initElements() {
        parent::initElements();
        
        foreach($this->_form->_data AS $k => $v){
            preg_match('/sliderfontcustom([0-9]*)$/', $k, $matches);
            if(count($matches)){
                nextendimport('nextend.form.element.fontmanager');
                $element = new SimpleXMLElement('<param type="fontmanager" base64="1" label="" name="'.$matches[0].'" tabs="Text|Link|Link:Hover" />');
                $font = new NextendElementFontmanager($this->_form, $this, $element);
                $font->_editableName = true;
                $this->_elements[$matches[0]] = $font;
                if($this->biggestindex < $matches[1]){
                    $this->biggestindex = intval($matches[1]);
                }
            }
        }
    }

    function render($control_name) {
        parent::render($control_name);

        $js = NextendJavascript::getInstance();
        $js->addLibraryJsAssetsFile('dojo', 'fontmatrix.js');

        $element = new SimpleXMLElement('<param type="fontmanager" base64="1" label="**label**" default="**value**" name="**name**" tabs="Text|Link|Link:Hover" />');
        $extrafont = new NextendElementFontmanager($this->_form, $this, $element);
        $extrafont->_includeJS = false;
        $extrafont->_editableName = true;
        $extrahtml = $extrafont->render($control_name);

        $js->addLibraryJs('dojo', '
            new NextendFontmatrix({
              table: "nextend-' . $this->_matrixname . '",
              name: "' . $this->_matrixname . '",
              html: ' . json_encode($extrahtml) . ',
              js: ' . json_encode($extrafont->printjs) . ',
              biggestindex: '.$this->biggestindex.'
            });
        ');
    }

    function decorateGroupStart() {

        echo "<table id='nextend-" . $this->_matrixname . "'>";
    }

    function decorateElement(&$el, $out, $i) {
        $class = 'odd';
        if ($i % 2)
            $class = 'even';
        echo "<tr class='" . $class . "'>";
        $title = NextendXmlGetAttribute($el->_xml, 'description');
        $class = '';
        if ($title != '') {
            $class = ' nextend-hastip';
            $title = ' title="' . $title . '"';
        }
        echo "<td class='nextend-label" . $class . "' " . $title . ">" . $out[0] . "</td>";
        echo "<td class='nextend-element'>" . $out[1] . "<div class='nextend-duplicate nextend-fontmatrix nextend-element-hastip' title='Duplicate'></div>".($el->_editableName ? "<div class='nextend-trash nextend-fontmatrix nextend-element-hastip' title='Delete'></div>":"")."</td>";
        echo "</tr>";
    }

}