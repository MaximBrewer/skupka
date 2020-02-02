<?php
defined('_JEXEC') or die('Restricted access');


class OPChikamini {
	public static function minPovReached() {
		if (!defined('_MIN_POV_REACHED')) {
			define('_MIN_POV_REACHED', 1); 
			
		}
	}
	public static function getUrl($rel = false)
	{
		$url = JURI::root(); 
		if ($rel) $url = JURI::root(true);
		if (empty($url)) return '/';    
		if (substr($url, strlen($url)-1)!='/')
		$url .= '/'; 
		return $url; 
	}
	function getReturnLink()
	{
		
		$lang = self::getLangCode(); 
		
		if (!empty($lang))
		{
			$lang = '&lang='.$lang; 
		}
		
		$itemid = JRequest::getVar('Itemid', ''); 
		if (!empty($itemid))
		$itemid = '&Itemid='.$itemid; 
		else $itemid = ''; 
		return base64_encode(self::getUrl().'index.php?option=com_hikashop&ctrl=checkout'.$itemid.$lang);

	}
	
	
	public static function getVendorCurrencyStyle() {
		
		$currency_id = hikashop_getCurrency();
		$db = JFactory::getDBO(); 
	$q = 'select * from #__hikashop_currency where currency_id = '.(int)$currency_id.' limit 0,1'; 
	$db->setQuery($q); 
	
	$c2 = $db->loadObject(); 
	if (empty($c2))
	{ 
	   $c2 = new stdClass(); 
	   $c2->currency_symbol = '$'; 
	   $c2->currency_decimal_place = 2; 
	   $c2->currency_decimal_symbol = '.'; 
	   $c2->currency_thousands = ' '; 
	   $c2->currency_positive_style = '{number} {symbol}';
	   $c2->currency_negative_style = '{sign}{number} {symbol}'; 
	}
	else {
		$arr = array(); 
	$arr[0] = '1'; 
	$arr[1] = $c2->currency_symbol; 
	if ($c2->currency_format === '%n') $c2->currency_decimal_place = 0; 
	else {
		//($c2->currency_format === '%i')
		$c2->currency_decimal_place = 2; 
	}
	$locale = json_decode($c2->currency_locale); 
	$arr[2] = $c2->currency_decimal_place; 
	$arr[3] = $c2->currency_decimal_symbol; 
	$arr[4] = $c2->currency_thousands; 
	// for now
	$arr[5] = '3';
	$arr[6] = '8';
	$arr[7] = '8';
	$arr[8] = $c2->currency_positive_style; 
	$arr[9] = $c2->currency_negative_style; 
	$vendor_currency_display_style = implode('|', $arr);
	}
	}
	 public static function getPwdError()
 {
   $jlang = JFactory::getLanguage(); 
   	 if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) {

   $jlang->load('com_users', JPATH_SITE, 'en-GB', true); 
   $jlang->load('com_users', JPATH_SITE, $jlang->getDefault(), true); 
   $jlang->load('com_users', JPATH_SITE, null, true); 
   
   return OPCLang::_('COM_USERS_FIELD_RESET_PASSWORD1_MESSAGE'); 
   
   }
   else
   {
    $jlang->load('com_user', JPATH_SITE, 'en-GB', true); 
    $jlang->load('com_user', JPATH_SITE, $jlang->getDefault(), true); 
    $jlang->load('com_user', JPATH_SITE, null, true); 

    return OPCLang::_('PASSWORDS_DO_NOT_MATCH'); 
   }
 }
	
	public static function getLangCode()
	{
		$langO = JFactory::getLanguage();
		$lang = JRequest::getVar('lang', ''); 
		$locales = $langO->getLocale();
		$tag = $langO->getTag(); 
		$app = JFactory::getApplication(); 		
		
		
		if (class_exists('JLanguageHelper') && (method_exists('JLanguageHelper', 'getLanguages')))
		{
			$sefs 		= JLanguageHelper::getLanguages('sef');
			foreach ($sefs as $k=>$v)
			{
				if ($v->lang_code == $tag)
				if (isset($v->sef)) 
				{
					$ret = $v->sef; 

					return $ret; 
				}
			}
		}
		
		
		
		if ( version_compare( JVERSION, '3.0', '<' ) == 1) {       
			if (isset($locales[6]) && (strlen($locales[6])==2))
			{
				$action_url .= '&amp;lang='.$locales[6]; 
				$lang = $locales[6]; 
				return $lang; 
			}
			else
			if (!empty($locales[4]))
			{
				$lang = $locales[4]; 
				
				if (stripos($lang, '_')!==false)
				{
					$la = explode('_', $lang); 
					$lang = $la[1]; 
					if (stripos($lang, '.')!==false)
					{
						$la2 = explode('.', $lang); 
						$lang = strtolower($la2[0]); 
					}
					
					
				}
				return $lang; 
			}
			else
			{
				return $lang; 
				
			}
		}
		return $lang; 
	}

	
	
}