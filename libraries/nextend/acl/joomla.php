<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendAcl extends NextendAclAbstract {

    var $_user = null;

    function __construct($userid = null) {
        $this->_user = JFactory::getUser($userid);
    }

    function authorise($array) {
        if(version_compare(JVERSION, '1.6.0', 'ge'))
          return $this->_user->authorise($array[0], $array[1]);
        return true;
    }
}