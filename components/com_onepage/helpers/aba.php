<?php
/**
 * @version		$Id: aba.php 
 * @package		abandoned cart helper for opc
 * @subpackage	com_onepage
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class OPCAba {
  public static function cartEnter()
   {
   
      require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php'); 
       $hash2 = uniqid('opc', true); 
	   $hashn = JApplication::getHash('opctracking'); 
	   $hash = JRequest::getVar($hashn, $hash2, 'COOKIE'); 
       if ($hash2 == $hash) 
	   OPCtrackingHelper::setCookie($hash); 
	   
	   
   }
  public static function update()
   {
   }
  public static function orderMade(&$order)
    {
	}
	
   private static function getObject()
    {
	   $ret = array(); 
	   //$ret = 
	}
	
   private static function updateData()
    {
	  
	   $q = "insert into #__virtuemart_plg_opctracking (virtuemart_order_id, hash, shown, created, created_by, modified, modified_by) values ('0', '".$db->escape($hash)."', '', '".$dd."', '".(int)$user_id."', '".$dd."', '".(int)$user_id."' )"; 
	}
}