<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

// check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restrict Access');

class InvoiceCurrencyDisplay
{
   
    static $vm2Currency;
    static $vm1Vendor;
    static $vm1Style;
    
	static $currencyReplacements;

	
	/**
	 * Get number of decimals for currency
	 * @param unknown_type $currency
	 */
	static function getDecimals($currency=null)
	{
		if (COM_VMINVOICE_ISVM2){
			self::loadVM2Currency($currency);
			return isset(self::$vm2Currency[$currency]->currency_decimal_place) ? self::$vm2Currency[$currency]->currency_decimal_place : false;
		}
		else{
			self::loadVM1Currency();
			return isset(self::$vm1Style[2]) ? self::$vm1Style[2] : false;
		}
	}
	
	static function getSymbol($currency=null)
	{
		if (COM_VMINVOICE_ISVM2){
			self::loadVM2Currency($currency);
			return isset(self::$vm2Currency[$currency]->currency_symbol) ? self::$vm2Currency[$currency]->currency_symbol : false;
		}
		else{
			self::loadVM1Currency();
			
			if (!$currency || ($currency == self::$vm1Vendor->vendor_currency)) //we are displaying vendor currency
	       		$symbol = self::$vm1Style[1]; //symbol take from format settings
	       	else //else symbol will be passed curency code
	       		$symbol = $currency;
	       	   
			return $symbol;
		}
	}
	
	static function loadVM2Currency($currency=null)
	{
	    if (!isset(self::$vm2Currency[$currency]))	{
    		$db = JFactory::getDBO();
    		$db->setQuery('SELECT * FROM #__virtuemart_currencies WHERE virtuemart_currency_id = '.(int)$currency);
			self::$vm2Currency[$currency] = $db->loadObject();
    	}
	}
	 
	static function loadVM1Currency()
	{
	     if (!self::$vm1Vendor){
	       $db = JFactory::getDBO();
	       $db->setQuery('SELECT `vendor_currency`, `vendor_currency_display_style`, `vendor_currency` FROM `#__vm_vendor` WHERE `vendor_id` = 1');
	       self::$vm1Vendor =  $db->loadObject();
	       self::$vm1Style = explode('|',self::$vm1Vendor->vendor_currency_display_style);
	     }
	}
	
    /**
     * Returns formatted currency
     * 
     * @param float $nb	number
     * @param string/int $currency currency code (vm1) currency id (vm2)
     * @param bool	if show symbol
     */
    static function getFullValue($nb,$currency=null,$showSymbol=true)
    {
    	$nb = (float)$nb;
    	
    	$rounding = 1;
    	
    	if (!$currency)
    		return $nb;
    		self::loadVM2Currency($currency);
    		
    		if (!self::$vm2Currency[$currency])
    			return (float)$nb;
    		
    		$symbol = self::$vm2Currency[$currency]->currency_symbol;
    		
    		// check for defined currency replacements
	        $symbol = self::replaceSymbol($symbol);

		    if ($rounding==0)
		        $nb = self::roundBetterDown($nb,self::$vm2Currency[$currency]->currency_decimal_place);
		    elseif ($rounding==1)  
	    		$nb = self::roundBetter($nb,self::$vm2Currency[$currency]->currency_decimal_place);
	    	else
	    		$nb = self::roundBetterUp($nb,self::$vm2Currency[$currency]->currency_decimal_place);
	        
    		$number = number_format(abs($nb), (int)self::$vm2Currency[$currency]->currency_decimal_place, self::$vm2Currency[$currency]->currency_decimal_symbol, self::$vm2Currency[$currency]->currency_thousands);
    		$return = self::$vm2Currency[$currency]->{$nb>=0?'currency_positive_style':'currency_negative_style'};
    		$return = str_replace('{sign}','-',$return);
    		$return = str_replace('{number}',$number,$return);
			$return = str_replace('{symbol}',$showSymbol ? $symbol : '',$return);
			//$return = str_replace(' ','&nbsp;', $return); //replace by nbsp to not break lines in invoices
			
			return $return;
    }
    
    static function replaceSymbol($symbol)
    {
    	if (strlen(self::$currencyReplacements) > 0) {
	        $curr_c = explode(",", self::$currencyReplacements);
	        foreach ($curr_c as $ccf) {
	            $cc_fields = explode("|", $ccf);
	            if ($symbol == $cc_fields[0] && isset($cc_fields[1]))
	                $symbol = $cc_fields[1];
	    	}
	    }
	    
	    return $symbol;
    }

	static function roundBetter($number, $precision = 0, $mode = 1, $direction = NULL) {    
	    if (!isset($direction) || is_null($direction)) { 
	        return round($number, (int)$precision/*, $mode*/); 
	    } 
	    
	    else { 
	        $factor = pow(10, -1 * $precision); 
	
	        return strtolower(substr($direction, 0, 1)) == 'd' 
	            ? floor($number / $factor) * $factor 
	            : ceil($number / $factor) * $factor; 
	    } 
	} 
	
	// roundBetterUp(1999, -3) => 2000 
	// roundBetterUp(1001, -3) => 2000 
	static function roundBetterUp($number, $precision = 0, $mode = 1) { 
	    return self::roundBetter($number, $precision, $mode, 'up'); 
	} 
	
	// roundBetterDown(1999, -3) => 1000 
	// roundBetterDown(1001, -3) => 1000 
	static function roundBetterDown($number, $precision = 0, $mode = 1) { 
	    return self::roundBetter($number, $precision, $mode, 'down'); 
	} 
	static function getsymbol2 ($id){
		$db = JFactory::getDBO();
		$query =$db->getQuery(true);
	
		$query->select($db->quoteName(array('currency_symbol')));
		$query->from($db->quoteName('#__virtuemart_currencies'));
		$query->where($db->quoteName('virtuemart_currency_id') . ' = '. $db->quote($id));
	
		$db->setQuery ($query);
		$row = $db->loadAssoc();
		$cs = $row[currency_symbol];
	
		return array($cs);
	}
}
?>