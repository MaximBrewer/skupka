<?php

/**
 *
 * @author stAn, RuposTel.com
 * @version $Id: eway_rupostel.php 
 * @package eWay Payment Plugin
 * @subpackage payment
 * @copyright Copyright (C) RuposTel.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * eWay Payment is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * Based on Authorize.net plugin by Virtuemart.net team
 *
 * http://rupostel.com
 */
defined('_JEXEC') or die('Restricted access');



class RupEwayPaymentLive {
    var $myGatewayURL;
    var $myCustomerID;
    var $myTransactionData = array();
    var $myCurlPreferences = array();
	var $error; 
	var $xmlRequest; 
    //Class Constructor
	function RupEwayPaymentLive($customerID = EWAY_DEFAULT_CUSTOMER_ID, $method = EWAY_DEFAULT_PAYMENT_METHOD ,$liveGateway  = EWAY_DEFAULT_LIVE_GATEWAY) {
	
		$this->myCustomerID = $customerID;
	    switch($method){

		    case 'REAL_TIME';

		    		if($liveGateway)
		    			$this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME;
		    		else
	    				$this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME_TESTING_MODE;
	    		break;
	    	 case 'REAL_TIME_CVN';
		    		if($liveGateway)
		    			$this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME_CVN;
		    		else
	    				$this->myGatewayURL = EWAY_PAYMENT_LIVE_REAL_TIME_CVN_TESTING_MODE;
	    		break;
	    	case 'GEO_IP_ANTI_FRAUD';
		    		if($liveGateway)
		    			$this->myGatewayURL = EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD;
		    		else
		    			//in testing mode process with REAL-TIME
	    				$this->myGatewayURL = EWAY_PAYMENT_LIVE_GEO_IP_ANTI_FRAUD_TESTING_MODE;
	    		break;
    	}
	}
	
	
	//Payment Function
	function doPayment(&$error, &$xml) {
	    $this->error = ''; 
		$this->xmlRequest = ''; 
		$xmlRequest = "<ewaygateway><ewayCustomerID>" . $this->myCustomerID . "</ewayCustomerID>";
		foreach($this->myTransactionData as $key=>$value)
			$xmlRequest .= "<$key>$value</$key>";
        $xmlRequest .= "</ewaygateway>";
		$xml = $xmlRequest; 
		/*
		header("Content-Type: text/xml");
		echo $xmlRequest;		
		die;
		*/		
		 
		$xmlResponse = $this->sendTransactionToEway($xmlRequest, $error);


		if(!empty($xmlResponse)){
			$responseFields = $this->parseResponse($xmlResponse);
			return $responseFields;
		}
		 $resp = 'Response: '.var_export($xmlResponse, true)."<br />"; 
		 $this->error .= "Error in XML response from eWAY <br />";
		 $error .= "Error in XML response from eWAY <br />".$resp;
		 
         return false;
	}

	//Send XML Transaction Data and receive XML response
	function sendTransactionToEway($xmlRequest, &$error) {
	
	if (!function_exists('curl_init')) 
	 {
	   $this->error .= 'Error, CURL library not found! Transaction was not processed! <br />'; 
	   $error = $this->error; 
	   return false; 
	 }
		$ch = curl_init($this->myGatewayURL);
				curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
				curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 4); // times out after 4s
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_ENCODING , "gzip");
				//curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml; charset=utf-8\n\n", "Content-Length: "));
        foreach($this->myCurlPreferences as $key=>$value)
        	curl_setopt($ch, $key, $value);

        $xmlResponse = curl_exec($ch);

		if ( curl_errno($ch) ) {
	    @curl_close($ch);
	    $this->error .= 'ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch)."<br />\n";
		$error = $this->error; 
		return false; 
		} else {
		  $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		  switch($returnCode){
            case 404:
			    @curl_close($ch);
				$this->error .= 'Error connecting to '.$this->myGatewayURL.' HTTP Status code: '.$returnCode." Transaction was not processed! <br /><br />\n";				
				$error = $this->error; 
                return false; 
                break;
            case 200:
			return $xmlResponse;
        	break;
            default:
				@curl_close($ch);
				$this->error .= 'Error connecting to '.$this->myGatewayURL.' HTTP Status code: '.$returnCode." Transaction was not processed! <br /><br />\n";
				$error = $this->error; 
            	return false; 
                break;
        }
		}
		$error = 'Unknown Error'; 
		return false; 
        
        	
	}
	
	
	//Parse XML response from eway and place them into an array
	function parseResponse($xmlResponse){
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser,  $xmlResponse, $xmlData, $index);
        $responseFields = array();
        foreach($xmlData as $data)
	    	if($data["level"] == 2)
			{
			   if (isset($data["value"]))
        		$responseFields[$data["tag"]] = $data["value"];
			}
        return $responseFields;
	}
	
	
	//Set Transaction Data
	//Possible fields: "TotalAmount", "CustomerFirstName", "CustomerLastName", "CustomerEmail", "CustomerAddress", "CustomerPostcode", "CustomerInvoiceDescription", "CustomerInvoiceRef",
	//"CardHoldersName", "CardNumber", "CardExpiryMonth", "CardExpiryYear", "TrxnNumber", "Option1", "Option2", "Option3", "CVN", "CustomerIPAddress", "CustomerBillingCountry"
	function setTransactionData($field, $value) {
		//if($field=="TotalAmount")
		//	$value = round($value*100);
		$this->myTransactionData["eway" . $field] = htmlentities(trim($value));
	}
	
	
	//receive special preferences for Curl
	function setCurlPreferences($field, $value) {
		$this->myCurlPreferences[$field] = $value;
	}
		
	
	//obtain visitor IP even if is under a proxy
	function getVisitorIP(){
		$ip = $_SERVER["REMOTE_ADDR"];
		if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
		$proxy = $_SERVER["HTTP_X_FORWARDED_FOR"];
		if(ereg("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$",$proxy))
		        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		return $ip;
	}
	
	
}
