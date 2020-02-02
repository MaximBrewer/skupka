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
//require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 

defined('_JEXEC') or die('Restricted access');


	if ($payment->payment_element == 'piraeus')
	if (isset($payment->monthinstallmentsP))
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

		$paymentCurrency = CurrencyDisplay::getInstance($payment->payment_currency);
        $totalInPaymentCurrency = round($paymentCurrency->convertCurrencyTo($payment->payment_currency, $cart->pricesUnformatted['billTotal'], false), 2);

		
		JRequest::setVar('monthinstallments', $payment->monthinstallmentsP); 
		$t = JRequest::getVar('monthinstallmentsamount', 0);
		if (empty($t)) { 
		 JRequest::setVar('monthinstallmentsamount', $totalInPaymentCurrency);
		}
		ob_start(); 
		$payment->opcref->plgVmOnSelectCheckPayment($cart, $msg); 
		
		$ign = ob_get_clean(); 
		$payment_id_override = $payment->payment_id_override;
		
		
	 }
