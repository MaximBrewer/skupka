<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.tab');

class NextendTabDefaultVertical extends NextendTab {

    function NextendTabDefaultVertical(&$form, &$xml) {
        $css = NextendCss::getInstance();
        $css->addCssLibraryFile('tabs/defaultvertical.css');
        $this->_matrixname = NextendXmlGetAttribute($xml, 'name');
        parent::NextendTab($form, $xml);
    }
    
    function decorateGroupStart() {

        echo "<table class='nextend-tab-defaultvertical'>";
    }
    
    function decorateElement(&$el, $out, $i) {
        $trclass = 'odd';
        if ($i % 2) $trclass = 'even';
        $title = NextendXmlGetAttribute($el->_xml, 'description');
        $class = '';
        if($title != ''){
            $class = ' nextend-hastip';
            $title= ' title="'.$title.'"';
        }
        echo "<tr class='" . $trclass . " nextend-label'>";
        echo "<td class='".$class."' ".$title.">" . $out[0] . "</td>";
        echo "</tr>";
        echo "<tr class='" . $trclass . " nextend-element'>";
        echo "<td>" . $out[1] . "</td>";
        echo "</tr>";
    }
}