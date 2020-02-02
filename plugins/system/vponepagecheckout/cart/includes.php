<?php
/**
 *---------------------------------------------------------------------------------------
 * @package      VP One Page Checkout - Joomla! System Plugin
 * @subpackage   For VirtueMart 3+
 *---------------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2012-2017 VirtuePlanet Services LLP. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @authors      Abhishek Das
 * @email        info@virtueplanet.com
 * @link         https://www.virtueplanet.com
 *---------------------------------------------------------------------------------------
 * $Revision: 3 $
 * $LastChangedDate: 2017-09-20 20:00:08 +0530 (Wed, 20 Sep 2017) $
 * $Id: includes.php 3 2017-09-20 14:30:08Z Abhshek Das $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

if (!class_exists('VmConfig'))
{
	require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
}

if(!class_exists('VmView'))
{
	require(JPATH_SITE . '/components/com_virtuemart/helpers/vmview.php');
}

if(JPluginHelper::isEnabled('system', 'bonus')) 
{
	JLoader::discover('VmbonusHelperFront', JPATH_SITE . '/components/com_vm_bonus/helpers');
}

if(!class_exists('vmPSPlugin'))
{
	require(JPATH_VM_PLUGINS . '/vmpsplugin.php');
}

if (!class_exists ('CurrencyDisplay'))
{
	require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/currencydisplay.php');
}

if (!class_exists('CouponHelper'))
{
	require(VMPATH_SITE . '/helpers/coupon.php');
}

if (!class_exists ('VirtueMartModelUserfields')) 
{
	require(VMPATH_ADMIN . '/models/userfields.php');
}

if(!class_exists('VmHtml'))
{
	require(VMPATH_ADMIN . '/helpers/html.php');
}

if (!class_exists('VirtueMartCart'))
{
	require(VMPATH_SITE . '/helpers/cart.php');
}

if(!function_exists('vpdump'))
{
	function vpdump($data)
	{
		ob_start();
		print_r($data);
		$str = ob_get_contents();
		ob_end_clean();
		
		echo '<pre class="vpdump" dir="ltr">';
		echo '<small>' . gettype($data) . '</small> ';
		echo '<font color="#cc0000">' . $str . '</font>';
		echo '<i>{Length: ' . strlen($str) . '}</i>';
		echo '<ul style="margin:15px 0; padding:15px; list-style-position:inside; background: #FAFAFA; border: 1px solid #DDD;">';
		if(function_exists('xdebug_call_file'))
			echo '<li>Calling File: ' . xdebug_call_file() . '</li>';
		if(function_exists('xdebug_call_class'))
			echo '<li>Calling Class: ' . xdebug_call_class() . '</li>';
		if(function_exists('xdebug_call_function'))
			echo '<li>Calling Function: ' . xdebug_call_function() . '</li>';	
		if(function_exists('xdebug_call_line'))
			echo '<li>Calling Line: ' . xdebug_call_line() . '</li>';
		echo '<li>Current Memory Usage: ' . vpCalculateSize(memory_get_usage()) . '</li>';
		echo '<li>Peak Memory Usage: ' . vpCalculateSize(memory_get_peak_usage()) . '</li>';
		echo '</ul>';
		echo '</pre>';
	}
}

if(!function_exists('vpCalculateSize'))
{
	function vpCalculateSize($mem_usage)
	{
		if ($mem_usage < 1024) 
			return $mem_usage." bytes"; 
		elseif ($mem_usage < 1048576) 
			return round($mem_usage/1024, 2)." KB"; 
		else 
			return round($mem_usage/1048576, 2)." MB";
	}
}
