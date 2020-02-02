<?php
/**
 * @version		opc_currency.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemOpc_currency extends JPlugin
{
    function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}
	
	function onAfterInitialise() {
	   if ((!empty($_GET)) && (!empty($_GET['carttmpl'])) && (!empty($_GET['virtuemart_currency_id']))) {
	      
	   }		   
	}
	
    function onAfterRoute()
	{
		
	 $app = JFactory::getApplication(); 
	 if ($app->isAdmin()) return; 
	 
	 if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 
	 
	 // opc is requried: 
	 if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'))
		 return; 
	 
	 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	 
	 $currency_per_lang = OPCconfig::get('currency_per_lang', array()); 
	 $currency_switch = OPCconfig::get('currency_switch', 1); 
	 $currency_switch = (int)$currency_switch; 
	 
	 
	 if ($currency_switch === 1)
	 {
	   if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php')) 
	   {
		   return;
	   }		   
	   
	   include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php');
	   
	 }
	 

	 
	 
	 
	 $can_change = OPCconfig::getValueNoCache('currency_config', 'can_change', 0, true, false); 
	 
	 
	 
	  $session = JFactory::getSession(); 
	 if (empty($can_change))
	 {
	
	 
	 $ci = JRequest::getVar('virtuemart_currency_id'); 
	 
	
	
	 if (!empty($ci))
	  {
	    // user can change currency, so let's change it: 
	  
	    //currency was set elsewhere
		
		$session->set('opc_currency', $ci); 
		// set global request variable
		$c_int = (int)$ci; 
		JRequest::setVar('virtuemart_currency_id', $c_int); 
	 
		$app->setUserState('virtuemart_currency_id', $c_int); 
		$app->setUserState('com_virtuemart.virtuemart_currency_id', $c_int); 
		$this->setCurrency($c_int); 
		
		$debug = $this->params->get('debug'); 
		if (!empty($debug)) {
		$app->enqueueMessage('OPC currency debug: currency was set from GET/POST, currency ID: '.$c_int); 
		}
		
		return; 
	  }
	  
	  // user is not currently changing the currency
	  
	  //$ci2 = $session->get('opc_currency');
      //if (!empty($ci2)) return; 	  
	 }
	 
	 
	 //debug: 
	 $c_int = $session->get('opc_currency', null);
	 
	 $tag = JFactory::getLanguage()->getTag(); 
	 $tag_old = $session->get('opc_last_language', $tag); 
	 $redo = false; 
	 if (($tag_old != $tag) && ($currency_switch === 2)) {
	    $redo = true; 
		
	 }
	 
	 if ((empty($c_int)) || ($redo))
	 {
	 if ($currency_switch === 1)
	 {
	 
	
	 {
	 if ($_SERVER['REMOTE_ADDR'] == '192.168.122.122')
	 $_SERVER['REMOTE_ADDR'] = '92.240.237.203'; 
	 
	 if (class_exists('geoHelper')) 
	 $c2c = geoHelper::getCountry2Code(); 
	 }
	 
	 
		 
	 
	 
	 
	 if (empty($c2c)) {
		 
		 $debug = $this->params->get('debug'); 
		if (!empty($debug)) {
				$app->enqueueMessage('OPC currency debug: geolocator is not installed  ! '); 
		}
		 
		 return; 
	 }
	 
	 
	 
	 
	 
	 $default = 0; 
	 $c_int = OPCconfig::getValueNoCache('currency_config', $c2c, 0, $default); 
	 
	 $debug = $this->params->get('debug'); 
		if (!empty($debug)) {
				$app->enqueueMessage('OPC currency debug: geolocator country:'.$c2c.' and currency returned: '.$c_int.' '); 
		}
	 
	 
	 }
	 else
	 if ($currency_switch === 2)
		 {
			 
			 
			 
			 if (isset($currency_per_lang[$tag]))
			 {
				  $c_int = (int)$currency_per_lang[$tag]; 
				  
				  $debug = $this->params->get('debug'); 
				  if (!empty($debug)) {
				    $app->enqueueMessage('OPC currency debug: currency per language: '.$tag.': '.$c_int); 
				  }
				  if ($redo) {
				    $session->set('opc_currency', $c_int); 
				  }
				  
				  $session->set('opc_last_language', $tag); 
				  
			 }
			 else
			 {
				  $debug = $this->params->get('debug'); 
				  if (!empty($debug)) {
				  $app->enqueueMessage('OPC currency debug: currency per language, language tag not found: '.$tag.': '.$c_int); 
				  }
			 }
			 
			 
			
		 }
	 }
	 else {
		 $debug = $this->params->get('debug'); 
		if (!empty($debug)) {
		  $app->enqueueMessage('OPC currency debug: currency was already decided, clear your cookies to see the page as the first time visitor'); 
		}
	 }
	//$arr = array ('lang'=>JRequest::getVar('lang'), 'language'=>JRequest::getVar('language'), 'switchlang'=>JRequest::getVar('switchlang'), 'post'=>$_POST); 
	//echo json_encode($arr); 
	
	 
	 
	 
	 $c_int = (int)$c_int; 
	 if (empty($c_int)) return; 
	 
	 $debug = $this->params->get('debug'); 
		if (!empty($debug)) {
	 $app->enqueueMessage('OPC currency debug: current currency is '.$c_int); 
		}
	 $this->setCurrency($c_int); 
	 
	 
	// $virtuemart_currency_id = $app->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getVar('virtuemart_currency_id',$currencyDisplay->_vendorCurrency) );
	 
	}
	
	public function setCurrency($c_int)
	{
		
		if (!class_exists('VmConfig'))
		  require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		  

		
		 // set global request variable
	 JRequest::setVar('virtuemart_currency_id', $c_int); 
	 $app = JFactory::getApplication(); 
	 $app->setUserState('virtuemart_currency_id', $c_int); 
	 $app->setUserState('com_virtuemart.virtuemart_currency_id', $c_int); 
	 
	 VmConfig::loadConfig(); 
	 
	 if (!class_exists('VirtuemartCart'))
	 require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php'); 
	 $cart = VirtuemartCart::getCart(); 
	 $cart->pricesCurrency = $c_int; 
	 
	 if (!class_exists('CurrencyDisplay')) 
		 require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .  DIRECTORY_SEPARATOR. 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');
	 $cd = CurrencyDisplay::getInstance($c_int); 
	}
	
	
	public function onAfterRender()
	{
		 $buffer = JResponse::getBody();
		 $x = stripos($buffer, '</body'); 
		 
		 if ($x !== false)
		 {
			 $code = $this->_getLangCode(); 
			 if (empty($code)) return; 
			 $ins = '<script>
//<![CDATA[  
var vmLang = \'&lang='.$code.'\'; 
//]]>
</script>'; 

			 $buffer = substr($buffer, 0, $x).$ins.substr($buffer, $x); 
			 JResponse::setBody($buffer);
		 }
		
	}
	
	private function _getLangCode()
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
