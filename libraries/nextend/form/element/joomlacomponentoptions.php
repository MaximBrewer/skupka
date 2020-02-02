<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.form.element.text');

class NextendElementJoomlaComponentOptions extends NextendElementText {

    function fetchElement() {
        JHTML::_('behavior.modal');
        $html = '<a class="nextend-configurator-button modal" rel="{handler: \'iframe\', size: {x: 875, y: 550}}" href="index.php?option=com_config&view=component&component='.NextendXmlGetAttribute($this->_xml, 'component').'&tmpl=component">Configure</a>';
        return $html;
    }
}
