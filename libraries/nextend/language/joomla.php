<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendText extends NextendTextAbstract{

}

$lang = JFactory::getLanguage();
NextendText::$lng = str_replace('-', '_', $lang->getTag());