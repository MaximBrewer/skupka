<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php
nextendimport('nextend.form.tab');

class NextendTabSlide extends NextendTab {

    function decorateTitle() {
        echo "<div class='nextend-tab'>";
        if ($this->_hidetitle != 1)
            echo "<h3>" . NextendText::_(NextendXmlGetAttribute($this->_xml, 'label')) . "</h3>";
        ?>
        <div style="position: absolute; right: 130px; top: 0;">
            <div class="smartslider-advanced-layers smartslider-greybar">
                <div class="smartslider-toolbar-simple smartslider-toolbar-options smartslider-button-grey first">
                    <div><?php echo NextendText::_('Simple_mode'); ?></div>
                </div><div class="smartslider-toolbar-advanced smartslider-toolbar-options smartslider-button-grey last">
                    <div><?php echo NextendText::_('Advanced_mode'); ?></div>
                </div>
            </div>
            <div class="smartslider-device-switch smartslider-greybar smartslider-toolbar-options-4">
                <div class="smartslider-toolbar-device-all smartslider-toolbar-options smartslider-button-grey first">
                    <div><?php echo NextendText::_('All'); ?></div>
                </div><div class="smartslider-toolbar-desktop smartslider-toolbar-options smartslider-button-grey">
                    <div><?php echo NextendText::_('Desktop'); ?></div>
                </div><div class="smartslider-toolbar-tablet smartslider-toolbar-options smartslider-button-grey">
                    <div><?php echo NextendText::_('Tablet'); ?></div>
                </div><div class="smartslider-toolbar-phone smartslider-toolbar-options smartslider-button-grey last">
                    <div><?php echo NextendText::_('Mobile'); ?></div>
                </div>
            </div>
        </div>
        <div class="smartslider-toolbar-play"><?php echo NextendText::_('PLAY'); ?></div>
        <div class="smartslider-slide-console"></div>
    <?php
    }

    function decorateGroupStart() {

    }

    function decorateGroupEnd() {

        echo "</div>";
    }

    function decorateElement(&$el, $out, $i) {

        echo $out[1];
    }
}