<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * @copyright   Copyright (C) 2013 InteraMind Ltd. All rights reserved.
 * @license     GNU/GPL, see LICENSE.txt
 * @package		This file is part of InteraMind VM Email Manager Component
 **/

$before = '';
$between = ', ';
$after = '';

if (!empty($groups)){
	echo $before;
	echo implode($between, $groups);
	echo $after;	
}
