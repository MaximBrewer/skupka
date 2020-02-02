<?php
/**
 * 
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */


// no direct access
defined('_JEXEC') or die;
class xmlCategory {
  var $children;
  var $id; 
  var $txt; 
  
  public static $allcats; 
  
  function __construct($id=0, $txt=0)
   {
      
	  $this->id = $id; 
	  $this->txt = $txt; 
	  $this->children = array(); 
   }

   
   
  
  public function addItem(&$item)
  {
   
    $this->children[$item->id] =& $item; 
	//self::$allcats[$item->id] =& $this->children[$item->id]; 
  }
  
}

