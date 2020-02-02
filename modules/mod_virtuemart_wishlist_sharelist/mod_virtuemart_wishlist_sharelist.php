<?php
/**
* description: Virtuemart Add to Sharelist Module
* Serjoka serjoka@gmail.com
* @package VirtueMart
* @subpackage classes
* @copyright Copyright (C) 2012 2Kweb Solutions. All rights reserved.
* This program is distributed under the terms of the GNU General Public License
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

// change the number of items to display
$num_lists = $params->get ('num_lists', 5);

$cache	= &JFactory::getCache('mod_virtuemart_wishlist_sharelist', 'output');
$key = 'sharelist'.$user->id.'.'.$num_lists;

	ob_start();
	// Try to load the data from cache.
	/* Load  VM function */
	if (!class_exists( 'mod_virtuemart_wishlist_sharelist' )) require('helper.php');

//Get current user object
$user =& JFactory::getUser();
	/* load the template */
	require(JModuleHelper::getLayoutPath('mod_virtuemart_wishlist_sharelist'));
	$output = ob_get_clean();
	$cache->store($output, $key);

echo $output;
?>
<!--Shared End-->
