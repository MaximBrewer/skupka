<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendAclAbstract {

    function authorise($array) {
        return true;
    }
}
if (nextendIsJoomla()) {
    nextendimport('nextend.acl.joomla');
} else {
    nextendimport('nextend.acl.default');
}