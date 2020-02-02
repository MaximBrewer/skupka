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


	if ($payment->payment_element == 'klarna')
	if (isset($payment->payment_id))
	 {
	   /*
	    $session = JFactory::getSession ();
		$sessionKlarna = $session->get ('Klarna', 0, 'vm');
		if (!empty($sessionKlarna)) {
			$sessionKlarnaData = unserialize ($sessionKlarna);
		}
		else 
		$sessionKlarnaData = new stdClass(); 
		
		$sessionKlarnaData->klarna_option = $payment->payment_type; 
		$session->set ('Klarna', serialize($sessionKlarnaData), 'vm');
		*/
		$msg = ''; 

		JRequest::setVar('klarna_paymentmethod', $payment->payment_id); 
		ob_start(); 
		$payment->opcref->plgVmOnSelectCheckPayment($cart, $msg); 
		
		$ign = ob_get_clean(); 
		$payment_id_override = $payment->payment_id; 
	 }
