<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.tab');
nextendimport('nextend.form.tabs.defaultvertical');

class NextendTabDefaultVerticalGroup extends NextendTabDefaultVertical {
    
    var $_group = '';
    
    var $_i = 0;
    
    function decorateElement(&$el, $out, $i) {
        $trclass = 'odd';
        if ($this->_i % 2) $trclass = 'even';
        $title = NextendXmlGetAttribute($el->_xml, 'description');
        $class = '';
        if($title != ''){
            $class = ' nextend-hastip';
            $title= ' title="'.$title.'"';
        }
        if(NextendXmlGetAttribute($el->_xml, 'groupstart') == 1){
            echo "<tr class='" . $trclass . " nextend-label'>";
            $this->_group = "<tr class='" . $trclass . " nextend-element'>";
        }
        echo "<td class='".$class."' ".$title.">" . $out[0] . "</td>";
        $this->_group.= "<td>" . $out[1] . "</td>";
        
        if(NextendXmlGetAttribute($el->_xml, 'groupend') == 1){
            echo "</tr>";
            echo $this->_group;
            echo "</tr>";
            $this->_group = '';
            $this->_i++;
        }
    }
}