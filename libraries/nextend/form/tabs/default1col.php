<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.tab');

class NextendTabDefault1col extends NextendTab {

    function decorateElement(&$el, $out, $i) {
        $class = 'odd';
        if ($i % 2) $class = 'even';
        echo "<tr class='" . $class . "'>";
        echo "<td class='nextend-element'>" . $out[1] . "</td>";
        echo "</tr>";
    }
    
}