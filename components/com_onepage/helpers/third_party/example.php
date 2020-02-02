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

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

// add this function to your plugin to generate: 
// id of an element which will be hidden when data empty or shown when data not empty
// "where" will insert the data html on the checkokut
// executed from opc controller and sent over javascript during the ajax calls 

/*
example from custom list_userfields.tpl.php

			if ($field['name']=='city')
			{
			  echo '<div id="custom_pf_msg">
			  <div class="ziparrow"><div class="ziparrow2">&nbsp;</div></div>
			  <div id="msg_custom_pf_msg">&nbsp;</div></div>'; 
			}


*/


public function plgGetOpcData(&$data, &$cart, $object)
	{
	   if ($this->getPluginMethods($cart->vendorId) === 0) {
		  return false;
		}
		foreach ($this->methods as $method)
		{
		$address = $cart->BT; 
		$what = ''; 
		$nbproducts_cond = $this->_nbproductsCond ($cart, $method, $address['zip'], $what);
		$object->where = 'msg_custom_pf_msg'; 
		$object->id = 'custom_pf_msg'; 
		$object->data = ''; 
		if (file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'))
		{
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		if ($op_default_zip == $address['zip'])
		  {
		    $data[] = $object; 
			return true; 
		  }
		}
		if (empty($address['zip'])) 
		$object->data = ''; 
		else
		if ($nbproducts_cond)
		 {
		   
		   $object->data = ''; 
		 }
		 else
		 {
		    
			if ($what == 'zip_list')
			 {
			  $object->data = $method->zip_list_error_here; 
			 }
			else
			if ($what == 'zip_range')
			{
			  $object->data = $method->zip_range_error_here; 
			}
			else
			{
			  $object->data = $method->zip_general_error;
			}
		    
		 }
		break; 
		}
		$data[] = $object; 
	}
	