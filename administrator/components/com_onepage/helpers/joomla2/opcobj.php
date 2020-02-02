<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/ 
// no direct access
defined('_JEXEC') or die('Restricted access');




class OPCObj {
  private $data; 
  function __construct($data)
   {
      $this->data =& $data; 
   }
  function get($key, $val=null)
  {
    if (method_exists($this->data, 'get'))
	$this->data->get($key, $val); 
	else
	$this->data->getValue($key, $val); 
  }
  function getValue($key, $val=null)
  {
    if (method_exists($this->data, 'get'))
	$this->data->get($key, $val); 
	else
	$this->data->getValue($key, $val); 
  }
  
}
