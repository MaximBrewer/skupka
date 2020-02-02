<?php
defined('_JEXEC') or die('Restricted access');

class OPChikapayment {
	public static function getPaymentHTML($withWrap=false, &$checkoutView=null) {
		if (is_null($checkoutview)) {
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'hika'.DIRECTORY_SEPARATOR.'checkout.controller.php');
			$checkoutControllerOpc = new checkoutControllerOpc; 
			$checkoutView = $checkoutControllerOpc->getViewOPC(); 
		}
		$payment_html = $checkoutView->getPaymentHtml(); 
		if (!empty($withWrap)) {
			$payment_html = '<div id="payment_html">'.$payment_html.'</div>'; 
		}
		
		return $payment_html; 
	}
}