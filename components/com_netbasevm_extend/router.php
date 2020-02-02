<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/
if (!defined('_VALID_MOS') && !defined('_JEXEC'))
die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

/**
 * @param	array	A named array
 * @return	array
 */
function NetBaseVM_ExtendBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view'])) {
		$segments[] = $query['view'];
		unset($query['view']);
	}
	if (isset($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (isset($query['download_code'])) {
		$segments[] = $query['download_code'];
		unset($query['download_code']);
	}

	return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/digitolldownloads/view/task/download_code/Itemid
 *
 * index.php?/digitolldownloads/view/Itemid
 */
function NetBaseVM_ExtendParseRoute($segments)
{
	$vars = array();

	if (!empty($segments))
		$vars['view'] = array_shift($segments);
	
	if (!empty($segments))
		$vars['task'] = array_shift($segments);
	
	if (!empty($segments))
		$vars['download_code'] = array_shift($segments);

	return $vars;
}
