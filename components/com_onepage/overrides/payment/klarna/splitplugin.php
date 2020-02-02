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

// load OPC loader
//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loader.php'); 

defined('_JEXEC') or die('Restricted access');


	  if ($pay->payment_element == 'klarna')
	   {
	      $params = new stdClass(); 
	      $thisklarnaparams = explode('|', $pay->payment_params);
                                foreach ($thisklarnaparams as $item) {
                                                $item = explode('=', $item);
                                                $key = $item[0];
                                                unset($item[0]);
                                                $item = implode('=', $item);
                                                if (!empty($item) ) 
												{
												$pay->$key = json_decode($item);
												$params->$key = $pay->$key; 
												}
                                        }

		 foreach ($params as $key=>$val)
		  {
		    if (stripos($key, 'klarna_payments')!==false)
			 {
			   if (!empty($val))
			   {
			   
			   foreach ($val as $type)
			   {
			    $clone = clone($pay);
				$clone->payment_type = $type;
				$clone->opcref =& $pay->opcref; 
				if ($type=='invoice')
				{
				$clone->payment_id = 'klarna_invoice';
				}
				else
				if ($type=='part')
				{
				
			    $clone->payment_id = 'klarna_partPayment';
				}
				else
				if ($type == 'spec')
				{
				$clone->payment_id = 'klarna_speccamp';
				}
				
			    $add[] =  $clone; 
			   }
			   break; 
			   }
			 }
		  }
		 

	   }