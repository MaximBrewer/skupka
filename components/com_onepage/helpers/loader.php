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




//extends VirtueMartViewCart
class OPCloader  {
	public static $totals_html; 
	public static $extrahtml; 
	public static $debugMsg; 
	public static $debug_disabled; 
	public static $inform_html; 
	public static $fields_names; 
	static $totalIsZero; 
	static $modelCache; 
	static $methods; 
	function getName()
	{
		return 'OPC'; 
	}

	public function __construct()
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		
		
	}
	
	function getShippingEstimator($ref)
	{
		return;
		$enabled = OPCconfig::get('opc_enable_shipipng_estimator', 0); 
		if (empty($enabled)) return ''; 
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$selected_template = OPCmini::getSelectedTemplate(); 

		
if (substr($selected_template, strlen($selected_template))!=='/') $selected_template.='/';

JHTMLOPC::script('shipping_estimator.js', 'components/com_onepage/themes/extra/default/', false);
/* when copying, copy the CSS to overrides directory as well */
if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.$selected_template.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'shipping_estimator.css'))
{
JHTMLOPC::stylesheet('shipping_estimator.css', 'components/com_onepage/themes/'.$selected_template.'overrides/', array());
}
else
{
JHTMLOPC::stylesheet('shipping_estimator.css', 'components/com_onepage/themes/extra/default/', array());
}
		$estimator_fields = OPCconfig::get('estimator_fields', array()); 
		if (!empty($estimator_fields)) { 
		$userFieldsModel = VmModel::getModel('Userfields');
		$eFields = array(); 
		$eValues = array(); 
		$types = array('BT','ST');
		
		foreach($types as $type){
			
			$preFix = 'estimator_'; 
			
			$userFields = $userFieldsModel->getUserFieldsFor('cart',$type);
			
			foreach ($userFields as $k=>$f)
			{	
			if (!in_array($f->name, $estimator_fields))
			{
				unset($userFields[$k]); 
			}
			}
			
			$eFields = $userFieldsModel->getUserFieldsFilled($userFields,$eValues,$preFix);
		}
		
		}
		
		$html = $this->fetch($this, 'shipping_estimator', array('cart'=>$ref->cart, 'eFields'=>$eFields)); 
		
		return $html; 
	}
	
	function getPluginElement($type, $vmid, $extra=false)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'pluginhelper.php'); 
		return OPCPluginHelper::getPluginElement($type, $vmid, $extra); 


	}
	public static function getPluginData(&$cart)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'pluginhelper.php'); 
		return OPCPluginHelper::getPluginData($cart);
		
	}

	function getMainJs()
	{

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


	public function getCheckBoxProducts(&$ref)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'checkboxproducts.php'); 
		return OPCCheckBoxProducts::getCheckBoxProductsHtml($ref, $this);
	}

	public static function opcDebug($msg, $type='')
	{

		if (empty(OPCloader::$debugMsg)) OPCloader::$debugMsg = array(); 
		if (!empty(OPCloader::$debug_disabled)) return; 

		if (!empty($type))
		{
			if (empty(OPCloader::$debugMsg[$type])) OPCloader::$debugMsg[$type] = array(); 
		}

		if (empty($msg)) return; 
		if (!is_string($msg))
		{
			//only up to 3 levels: 
			$pr = ''; 
			if ((is_array($msg)) || (is_object($msg)))
			{
				foreach ($msg as $k=>$v)
				{
					if (is_array($v) || (is_object($v)))
					{
						foreach ($v as $k2=>$v2)
						{
							
							if (is_array($v2) || (is_object($v2)))
							{
								foreach ($v2 as $k3=>$v3)
								{
									if (is_array($v2) || (is_object($v2)))
									{
										// we do not print vars above 3rd level...
										$pr .= '['.$k.']['.$k2.']['.$k3.'] = -- skipped, too much recursion -- '."\n"; 
									}
									else
									{
										$pr .= '['.$k.']['.$k2.']['.$k3.'] = '.$v3."\n"; 
									}
									
								}
							}
							else
							{
								$pr .= '['.$k.']['.$k2.'] = '.$v2."\n"; 
							}
						}
					}
					else
					{
						$pr .= '['.$k.'] = '.$v."\n"; 
					}
				}
			}
			else
			{
				$pr .= $k."\n"; 
			}
		}
		else
	    if (is_string($msg)) {
			;
			}
		else
		{
			$msg = @var_export($msg, true); 
		}

		if (!empty($type))
		OPCloader::$debugMsg[$type][] = $msg; 
		else
		OPCloader::$debugMsg[] = $msg; 
	

	
	}


	public static function loadJavascriptFiles(&$ref)
	{
		$OPCloader = new OPCloader(); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php'); 
		return OPCJavascript::loadJavascriptFiles($ref, $OPCloader);

	}

	public static function getShippingEnabled($cart=null)
	{

		if (defined('DISABLE_SHIPPING')) return DISABLE_SHIPPING; 

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 


		$op_zero_weight_override = OPCconfig::get('op_zero_weight_override', false);
		$op_disable_shipping = OPCconfig::get('op_disable_shipping', false);



		if (empty($op_zero_weight_override)) 
		{
			define('DISABLE_SHIPPING', $op_disable_shipping);
			return $op_disable_shipping;
		}

		if (!empty($op_disable_shipping))
		{
			define('DISABLE_SHIPPING',1); 
			return true; 
		}

		if (empty($cart)) 
		$cart = VirtueMartCart::getCart();
		
		$weight = 0; 
		foreach( $cart->products as $pkey =>$prow )
		{
			if (isset($prow->product_weight))
			if (!empty($prow->product_weight))
			{
				$w = (float)$prow->product_weight;  
				if ( $w > 0)
				{
					
					
					$weight = 1; 
					continue;
				}
			}
		}
		
		
		if ($weight > 0)
		{
			
			define('DISABLE_SHIPPING',0); 
			return false; 
		}
		
		define('DISABLE_SHIPPING',1); 
		return true; 

		
	}
	public static function getShiptoEnabled($cart=null)
	{

		if (defined('NO_SHIPTO')) return NO_SHIPTO; 




		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 

		$op_disable_shipto = OPCconfig::get('op_disable_shipto', false);
		$disable_ship_to_on_zero_weight = OPCconfig::get('disable_ship_to_on_zero_weight', false);

		if (!empty($op_disable_shipto))
		{
			define('NO_SHIPTO', true); 
			return true; 
		}

		if (empty($disable_ship_to_on_zero_weight)) 
		{
			define('NO_SHIPTO', false); 
			return false;
		}

		// will check the weitht only if ship to is enabled + shop to per weithet is enabled

		if (empty($ref)) 
		$cart = VirtueMartCart::getCart();
		else $cart=$ref->cart; 
		$weight = 0; 
		foreach( $cart->products as $pkey =>$prow )
		{
			if (isset($prow->product_weight))
			if (!empty($prow->product_weight))
			{
				$w = (float)$prow->product_weight;  
				if ( $w > 0)
				{
					
					
					$weight = 1; 
					continue;
				}
			}
		}
		
		if ($weight > 0)
		{
			define('NO_SHIPTO', false); 
			return false; 
		}
		
		
		define('NO_SHIPTO', true); 
		return true; 

		
	}

	/* deprecated */
	public static function getShiptoEnabled2($cart=null)
	{

		$disable_shipping = OPCloader::getShippingEnabled($cart); 

		if (defined('NO_SHIPTO')) return NO_SHIPTO; 

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		$op_disable_shipto = OPCconfig::get('op_disable_shipto', false);
		$disable_ship_to_on_zero_weight = OPCconfig::get('disable_ship_to_on_zero_weight', false);

		// disabled by master config
		if (!empty($op_disable_shipto))
		if (!defined('NO_SHIPTO'))
		{
			define('NO_SHIPTO', 1);
			return true; 
		}
		// shipping is disabled by weight and config says to disable ship to as well
		if (!empty($op_disable_shipto) && (!empty($disable_ship_to_on_zero_weight)))
		{
			define('NO_SHIPTO', 1);
			return true; 
		}
		
		define('NO_SHIPTO', 0);
		return false; 

		
	}


	// returns the domain url ending with slash
	public static function getUrl($rel = false)
	{
		$url = JURI::root(); 
		if ($rel) $url = JURI::root(true);
		if (empty($url)) return '/';    
		if (substr($url, strlen($url)-1)!='/')
		$url .= '/'; 
		return $url; 
	}

	// returns a modified user object, so the emails can be sent to unlogged users as well
	function getUser(&$cart)
	{
		$currentUser = JFactory::getUser();
		return $currentUser; 
		$uid = $currentUser->get('id');
		if (!empty($uid))
		{
			
		}
		
	}

	function getReturnLink(&$ref)
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
		return base64_encode(OPCloader::getUrl().'index.php?option=com_virtuemart&view=cart'.$itemid.$lang);

	}

	function getShowFullTos(&$ref)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'tos.php'); 
		return OPCTos::getShowFullTos($ref, $this); 

	}

	public static function getArticle($id, $repvals=array())
	{
		$article = JTable::getInstance("content");
		
		$article->load($id);
		
		if (!class_exists('CurrencyDisplay'))
		require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
		$currencyDisplay = CurrencyDisplay::getInstance();

		
		
		$parametar = new OPCParameter($article->attribs);
		$x = $parametar->get('show_title', false); 
		$x2 = $parametar->get('title_show', false); 
		
		$intro = $article->get('introtext'); 
		$full = $article->get("fulltext"); 
		JPluginHelper::importPlugin('content'); 
		$dispatcher = JDispatcher::getInstance(); 
		$mainframe = JFactory::getApplication(); 
		$params = $mainframe->getParams('com_content'); 
		
		if ($x || $x2)
		{
			
			

			$title = '<div class="componentheading'.$params->get('pageclass_sfx').'">'.$article->get('title').'</div>';
			
		}
		else $title = ''; 
		if (empty($article->text))
		$article->text = $title.$intro.$full; 
		
		if (!empty($repvals))
		foreach ($repvals as $key=>$val)
		{
			if ((is_array($val)) || (is_object($val)))
			{
				foreach ($val as $k2=>$nval)
				{
					if (!is_string($nval)) continue; 
					
					if ((stripos($k2, 'price')!==false) && (is_numeric($nval)))
					{
						$nval = (float)$nval; 
						$nval2 = $currencyDisplay->priceDisplay ($nval);
						
						$article->text = str_replace('{'.$k2.'_text}', $nval2, $article->text); 
					}
					
					if (is_string($nval))
					$article->text = str_replace('{'.$k2.'}', $nval, $article->text); 
				}
			}
			else
			{
				if (!is_string($val)) continue; 
				$article->text = str_replace('{'.$key.'}', $val, $article->text); 
			}
		}
		
		$results = $dispatcher->trigger('onPrepareContent', array( &$article, &$params, 0)); 
		$results = $dispatcher->trigger('onContentPrepare', array( 'text', &$article, &$params, 0)); 
		
		return $article->get('text');
		
		
	}

	function getTosRequired(&$ref)
	{

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'tos.php'); 
		return OPCTos::getTosRequired($ref, $this);
		
		

	}

	public static function checkOPCSecret()
	{
		$config     = JFactory::getConfig();
		
		if (method_exists($config, 'getValue'))
		$secret       = $config->getValue('secret');
		else 
		$secret       = $config->get('secret');
		
		$secret = md5('opcsecret'.$secret); 
		$opc_secret = JRequest::getVar('opc_secret', null); 
		if ($opc_secret == $secret)
		{
			$preview = JRequest::getVar('preview', false); 
			if (empty($preview)) return false; 
			
			return true; 
		}
		
		return false; 
	}

	function addtocartaslink(&$ref)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'addtocartaslink.php'); 
		OPCAddToCartAsLink::addtocartaslink($ref, $this); 
		

	}
	function getTosLink(&$ref)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'tos.php'); 
		return OPCTos::getTosLink($ref, $this); 

	} 
	public static function getFormVars(&$ref)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
		return OPCCommonHtml::getFormVars($ref); 
		

		
	}

	function getCaptcha(&$ref)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'captcha.php'); 
		return OPCCaptcha::getCaptcha($ref);    
	}
	// input parameters: STaddress or BTaddress fields
	// will change country and state to it's named equivalents
	function setCountryAndState($address)
	{
		// get rid of the references
		$address = $this->copyObj($address); 
		if (!class_exists('ShopFunctions'))
		require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'shopfunctions.php');
	
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		
		if ((isset($address) && (!is_object($address))) || ((!is_object($address)) && (empty($address->virtuemart_country_id))))
		{
			
			if (!empty($address['virtuemart_country_id']) && (!empty($address['virtuemart_country_id']['value'])) && (((is_numeric($address['virtuemart_country_id']['value'])))))
			{
				$country_name = OPCmini::getCountryByID($address['virtuemart_country_id']['value']); 
				$country_3_code = OPCmini::getCountryByID($address['virtuemart_country_id']['value'], 'country_3_code'); 
				$key = 'COM_VIRTUEMART_COUNTRY_'.$country_3_code; 
				$x = JText::_($key); 
				if ($x !== $key) { $country_name = $x; }
				$address['virtuemart_country_id']['value_txt'] = $country_name; 
				
			}
			else 
			{
				$address['virtuemart_country_id']['value'] = ''; 
			}
			
			if (!empty($address['virtuemart_state_id']) && (!empty($address['virtuemart_state_id']['value'])) && ((is_numeric($address['virtuemart_state_id']['value']))))
			{
				$address['virtuemart_state_id']['value_txt'] = shopFunctions::getStateByID($address['virtuemart_state_id']['value']); 
			}
			else $address['virtuemart_state_id']['value'] = ''; 
			
			if (isset($address['virtuemart_state_id']['formcode']))
			{
				if (!empty($address['virtuemart_country_id']['value']))
				$country = $address['virtuemart_country_id']['value']; 
				if (!empty($address['virtuemart_state_id']['value']))
				$state = $address['virtuemart_state_id']['value']; 
				
				if ((!empty($country)) && ((!is_numeric($country)) && (isset($address['virtuemart_country_id']['virtuemart_country_id'])))) {
					$country = $address['virtuemart_country_id']['virtuemart_country_id']; 
				}
				if ((!empty($state)) && ((!is_numeric($state)) && (isset($address['virtuemart_state_id']['virtuemart_state_id'])))) {
					$state = $address['virtuemart_state_id']['virtuemart_state_id']; 
				}
				
				if (!empty($country) && (!empty($state)))
				{
					require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
					if (OPCUserFields::checkCountryState($country, $state))
					{
						require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
						$html = OPCCommonHtml::getStateHtmlSelectByStateAndCountry($state, $country, '', true);
						$address['virtuemart_state_id']['formcode'] = $html; 
					}
				}
			}
			
			
		}
		else
		{
			if (!empty($address->virtuemart_country_id) && (((is_numeric($address->virtuemart_country_id)))))
			{
				$address->virtuemart_country_id = OPCmini::getCountryByID($address->virtuemart_country_id); 
			}
			else $address->virtuemart_country_id = ''; 
			
			if (!empty($address->virtuemart_state_id)  && ((is_numeric($address->virtuemart_state_id))))
			{
				$address->virtuemart_state_id = shopFunctions::getStateByID($address->virtuemart_state_id); 
			}
			else $address->virtuemart_state_id = ''; 
			
		}
		return $address; 
	}
	
	function txtToVal(&$address)
	{
		foreach ($address as $k=>$v)
		if (isset($v['value_txt']))
		$address[$k]['value'] = $v['value_txt']; 
		
		
	}
	
	function getNamedFields(&$BTaddress, $fields, $_u)
	{
		
		$db = JFactory::getDBO(); 
		$sysa = array('virtuemart_state_id', 'virtuemart_country_id'); 
		foreach ($BTaddress as $k=>$val)
		{
			if (!isset($BTaddress[$k]['name'])) continue; 
			if (!in_array($BTaddress[$k]['name'], $sysa))
			
			switch ($BTaddress[$k]['type'])
			{
			case 'multicheckbox':
			case 'multiselect':
			case 'select':
			case 'radio':
			case 'checkbox':
				$vals = explode('|*|', $fields[$val['name']]); 
				
				
				
				
				foreach ($vals as $vv)
				{
					
					
					if (is_object($_u[$BTaddress[$k]['name']]))
					{
						if (!isset($_u[$BTaddress[$k]['name']]->virtuemart_userfield_id)) break;
						
						$_qry = 'SELECT `fieldtitle`, `fieldvalue` '
						. 'FROM `#__virtuemart_userfield_values` '
						. 'WHERE `virtuemart_userfield_id` = ' . (int)$_u[$BTaddress[$k]['name']]->virtuemart_userfield_id
						. " and `fieldvalue` = '".$db->escape($vv)."' " 
						. ' limit 0,1 ';
						$db->setQuery($_qry); 
						
						
						$res = $db->loadAssoc(); 
					}
					else
					{
						
						$value = $_u[$BTaddress[$k]['name']]; 
						if (!is_array($value)) {
						$_qry = 'SELECT v.fieldtitle, v.fieldvalue '
						. 'FROM #__virtuemart_userfield_values as v, #__virtuemart_userfields as f '
						. "WHERE f.virtuemart_userfield_id = v.virtuemart_userfield_id and f.name = '".$db->escape($k)."' and v.fieldvalue = '".$db->escape($value)."' "
						. ' limit 0,1 ';
						}
						else 
						{
						$_qry = 'SELECT v.fieldtitle, v.fieldvalue '
						. 'FROM #__virtuemart_userfield_values as v, #__virtuemart_userfields as f '
						. "WHERE f.virtuemart_userfield_id = v.virtuemart_userfield_id and f.name = '".$db->escape($k)."' and v.fieldvalue = '".$db->escape($vv)."' "
						. ' limit 0,1 ';	
						}
						try
						{
							
							$db->setQuery($_qry); 
							$res = $db->loadAssoc(); 
						}
						catch (Exception $e)
						{
							return;
						}
						
					}
					
					
					
					
					$e = $db->getErrorMsg(); 
					if (!empty($e)) return;
					
					if (isset($res))
					{
						if (!isset($BTaddress[$k]['value_txt'])) $BTaddress[$k]['value_txt'] = ''; 
						
						$BTaddress[$k]['value_txt'] .= OPCLang::_($res['fieldtitle']); 
						if (count($vals)>1) $BTaddress[$k]['value_txt'].='<br />'; 
					}
					else
					{
						
					}
					
					
					
					
				}
				
				
				
				break;
				
				
				
				
				
				
			}
		}
		
		
	}
	
	function getUserInfoBT(&$ref)
	{
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loggedshopper.php'); 
		return OPCLoggedShopper::getUserInfoBT($ref, $this); 
	}
	
	// VM uses too many references and we need to copy the object to change it, otherwise it will change other objects as well
	function copyObj($obj)
	{
		// we don't want references
		if (empty($obj)) return $obj; 
		return unserialize(serialize($obj)); 
		if (is_object($obj))
		$new = new stdClass(); 
		if (is_array($obj))
		$new =  array();
		
		
		
		if (is_array($obj))
		foreach ($obj as $k=>$v)
		{
			if (is_array($v))
			foreach ($v as $n=>$r)
			{
				$new[$k][$n] = $r; 
			}
		}
		
	}
	
	function getUserInfoST($ref)
	{
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loggedshopper.php'); 
		return OPCLoggedShopper::getUserInfoST($ref, $this); 

	}
	// variables outside the form, so it does not slow down the POST			
	function getExtras(&$ref)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
		return OPCCommonHtml::getExtras($ref); 

	}
	// we will not use json or jquery here as it is extremely unstable when having too many scripts on the site
	function getStateHtmlOptions(&$cart, $country, $type='BT', &$retval=array())
	{
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
		return OPCCommonHtml::getStateHtmlOptions($cart, $country, $type, $retval);
	}
	function getStateList(&$ref)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
		return OPCCommonHtml::getStateList($ref);


	}
	
	function getMediaData($id)
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
		return OPCimage::getMediaData($id); 


	}
	function getImageFile($id, $w=0, $h=0)
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
		return OPCimage::getImageFile($id, $w, $h); 



	}

	function registerCurrency(&$cart)
	{
		$mainframe = JFactory::getApplication(); 
		$virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );	 
		
		if (empty($virtuemart_currency_id))
		{
			if (isset($cart->pricesCurrency))
			{
				$virtuemart_currency_id = $cart->pricesCurrency; 
			}
			else
			{
				$virtuemart_currency_id = $cart->paymentCurrency;
			}
		}
		
		if (empty($virtuemart_currency_id)) return; 

		if (!empty($virtuemart_currency_id))
		{
			$db = JFactory::getDBO(); 
			$q = 'select * from #__virtuemart_currencies where virtuemart_currency_id = '.(int)$virtuemart_currency_id.' limit 0,1'; 
			$db->setQuery($q); 
			$res = $db->loadAssoc(); 
			
			if (!empty($res))
			foreach ($res as $key5=>$val5)
			{
				OPCrenderer::registerVar($key5, $val5); 
			}
		}
	}

	function getImageUrl($id, &$tocreate, $w=0, $h=0)
	{

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
		return OPCimage::getImageUrl($id, $tocreate, $w, $h);


	}
	function getActionUrl(&$ref, $onlyindex=false)
	{
		return JRoute::_('index.php'); 
		if ($onlyindex) return JURI::root(true).'/index.php'; 
		return JURI::root(true).'/index.php?option=com_virtuemart&amp;view=opc&amp;controller=opc&amp;task=checkout';


	}

	static function getCouponCode($cart) {
	        if (!class_exists('CouponHelper')) 
			{
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' . DIRECTORY_SEPARATOR . 'coupon.php');
			}
			
			$session = JFactory::getSession(); 
			$coupon_session = $session->get('coupon', '', 'awocoupon');
			if (!empty($coupon_session)) {
				$coupon_session = unserialize($coupon_session);
				if (!empty($coupon_session)) {
				$entered_coupons = array();
		foreach($coupon_session['processed_coupons'] as $coupon) {
			if(!empty($coupon['isauto']) && $coupon['isauto']==1) continue;
			if(!empty($coupon['isbalance']) && $coupon['isbalance']==1) continue;
			$entered_coupons[$coupon['coupon_code']] = 1;
		}
		$ret = implode(',', array_keys($entered_coupons));
		if (!empty($ret)) return $ret; 
				}
			}
				/*
				if (!empty($coupon_session)) {
				  $coupon_code_awo = $coupon_session['coupon_code']; 
				  if (!empty($coupon_session['coupon_code_db'])) {
				    $coupon_code_awo = $coupon_session['coupon_code_db']; 
				  }
				  if (!empty($coupon_code_awo)) {
				    return $coupon_code_awo; 
				  }
				  if ($coupon_code_awo === $cart->couponCode) {
					  if (isset($coupon_session['coupon_code_internal'])) {
					   $coupon = $coupon_session['coupon_code_internal']; 
					   if (!empty($coupon)) return $coupon; 
					  }
					  else
					  {
						  
					  }
				  }
				  
				  
				}
				
			
			*/
		if ((!empty($cart->couponCode) && (strpos($cart->couponCode, '<div')===false))) return $cart->couponCode; 
		$session = JFactory::getSession(); 
		$c = $session->get('opc_last_coupon', ''); 
	    if (!empty($c)) return $c; 
		
		
		
		return ''; 
	}


	static function getCheckoutPrices(&$cart, $auto, &$vm2015, $other=null, $isR=false)
	{
		JFactory::getApplication()->set('rupostel_opc_calculation', true); 		
		//this is original VM3 code: 
		/*
		if(!class_exists('calculationHelper')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
		$calc = calculationHelper::getInstance(); 
		$calc->getCheckoutPrices($cart, false, $other);
		return $cart->cartPrices; 
		*/
		$session = JFactory::getSession(); 

		
		
		//$cart->couponCode = self::getCouponCode($cart); 
		static $coupon;
		
		if ((!empty($c)) && (empty($coupon))) $coupon = $c; 
	
	     if (!class_exists('VmConfig'))
		 require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		 if (!defined('VMPATH_SITE'))
		 VmConfig::loadConfig(); 
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
	    $opc_debug = OPCconfig::get('opc_debug', false); 
		
		if ((empty($isR) && (!empty($cart->couponCode)) && (empty($coupon))))
		{
			$coupon = $cart->couponCode; 
		}
		else
	    if (empty($isR) && (empty($cart->couponCode)) && (empty($coupon)))
		{
			$coupon = ''; 
		}
		
		
		
		$cart->virtuemart_shipmentmethod_id  = (int)$cart->virtuemart_shipmentmethod_id; 
		$cart->virtuemart_paymentmethod_id  = (int)$cart->virtuemart_paymentmethod_id; 

		
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
			$cart->cartPrices = array(); 
		}

		$saved_id =  $cart->virtuemart_shipmentmethod_id;
		$payment_id =  $cart->virtuemart_paymentmethod_id;
		$savedcoupon = $cart->couponCode; 



		if(!class_exists('calculationHelper')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'calculationh.php');
		
		// support for vm2.0.2 where getInstance returned fatal error
		if (isset(calculationHelper::$_instance) && (is_object(calculationHelper::$_instance)))
		$calc = calculationHelper::$_instance; 
		else
		$calc = calculationHelper::getInstance(); 
		
		if (method_exists($calc, 'setCartPrices')) $vm2015 = true; 
		else $vm2015 = false; 
		if ($vm2015)
		{
			
			
			$calc->setCartPrices(array()); 
		}
		
		//$cart->debug = true; 
	
		$awo_fix = OPCconfig::get('awo_fix', false); 

		
		
		
		if ($awo_fix) {
			
		 $awo = OPCmini::extExists('com_awocoupon'); 			
		$session = JFactory::getSession(); 
		if (!empty($awo)) {
		 $cart->couponValue = null; 
		 $cv = $cart->couponCode;
		 $cart->couponCode = ''; 
		 $opc_debug = OPCconfig::get('opc_debug', false);  if (!empty($opc_debug)) { JFactory::getApplication()->enqueueMessage('OPC Debug: Coupon removed at '.__FILE__.':'.__LINE__); }
		 $cart->cartPrices = $cart->cartData = array(); 
		}
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
		 if (empty($calc->_cart)) $calc->_cart =& $cart; 
		}
		
		 $cart->cartPrices = $cart->cartData = array(); 
		 if (isset($calc->_cart))
		 {
			 $calc->_cart->cartData = $calc->_cart->cartPrices = array(); 
		 }
		}
		
		if (empty($opc_debug))	ob_start(); 		
		$prices = $calc->getCheckoutPrices($cart, false, $other);
		if (empty($opc_debug)) { $zz = ob_get_clean(); }
		
		
		
		
		if (is_null($prices)) $prices = $cart->cartPrices; 
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) {
		$prices['couponCode'] = $cart->cartPrices['couponCode'] = $cart->couponCode; 
		}
	
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
			if (!isset($calc->_cart->cartPrices['withTax'])) $calc->_cart->cartPrices['withTax'] = 0; 
			if (!isset($calc->_cart->cartPrices['salesPrice'])) $calc->_cart->cartPrices['salesPrice'] = 0; 

			$prices = $cart->cartPrices = $cart->pricesUnformatted = $calc->_cart->cartPrices; 
			$cart = $calc->_cart; 
		}
			

		if (is_null($prices))
		{
			$prices = $calc->_cart->cartPrices; 
		}
		if (method_exists($calc, 'getCartData'))
		$cart->OPCCartData = $calc->getCartData();
		
		
		
		
		
		$cart->virtuemart_shipmentmethod_id = $saved_id; 
		if (!empty($savedcoupon))
		$cart->couponCode = $savedcoupon; 			
	
		$cart->virtuemart_paymentmethod_id = $payment_id; 

		if (VmConfig::get('rappenrundung', false)==1)
		{
			
			$prices['billTotal'] = round((float)$prices['billTotal'] * 2,1) * 0.5; 
		}
		
		if (!empty($prices['billTotal']))
		{
			$pX = floatval($prices['paymentValue']); 
			$sX = 0; 
			$testP = $pX + $sX; 
			
			// special case for zero value orders, do not charge payment fee: 
			if (($prices['billTotal'] == $pX) || ($prices['billTotal'] == $testP) || ($prices['billTotal'] == $sX))
			{
				if ($vm2015)
				{
					$calc->setCartPrices(array()); 
				}
				
				$cart->virtuemart_paymentmethod_id = "-0"; 
				
				if (empty($opc_debug)) ob_start(); 
				$prices = $calc->getCheckoutPrices($cart, false, $other);
				if (empty($opc_debug))  { $x = ob_get_clean(); }
				
				if (is_null($prices))
				{
					$prices = $calc->_cart->cartPrices; 
				}
				
				$cart->virtuemart_paymentmethod_id = $payment_id; 
				$cart->virtuemart_shipmentmethod_id = $saved_id;
				$zeroSubtotal = true; 
				
			}
			
		}
		
		
		
			if (!empty($coupon))
			$cart->couponCode = $coupon; 
		
		
		if (class_exists('plgSystemOrder_discount_rules')) {

		 require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'pluginhelper.php');
		 OPCPluginHelper::triggerSystemPlugin('plgVmOnCheckoutAdvertise', array( $cart, &$checkoutAdvertise));
		 
		 if ($cart->pricesUnformatted['billTotal'] !== $price['billTotal']) {
			 $prices = $cart->cartPrices = $calc->_cart->cartPrices = $cart->pricesUnformatted; 
		 }
		 
		}

		
		
		
		JFactory::getApplication()->set('rupostel_opc_calculation', false); 		
		return $prices; 
	}

	function getBasket(&$ref, $withwrapper=true, &$op_coupon='', $shipping='', $payment='', $isexpress=false)
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'basket.php'); 
		return OPCBasket::getBasket($ref, $this, $withwrapper, $op_coupon, $shipping, $payment, $isexpress); 
		
	}
	// this is needed for klarna like payment methods
	public static function prepareBT(&$cart)
	{
		if (empty($cart->BT)) $cart->BT = array(); 
		if (!isset($cart->BT['email'])) $cart->BT['email'] = ''; 
		if (!isset($cart->BT['first_name'])) $cart->BT['first_name'] = ''; 
		if (!isset($cart->BT['last_name'])) $cart->BT['last_name'] = ''; 
		if (!isset($cart->BT['virtuemart_country_id'])) $cart->BT['virtuemart_country_id'] = ''; 
		if (!isset($cart->BT['title'])) $cart->BT['title'] = ''; 	
	}
	
	
	
	function getAdminTools(&$ref)
	{
		$admin = false;
		$user = JFactory::getUser();
		if (!method_exists($user, 'authorise')) return ''; 
		if($user->authorise('core.admin','com_virtuemart') or $user->authorise('core.manage','com_virtuemart')){
			$admin  = true;
		}
		
		
		
		if (!$admin) return ''; 
		if (!isset($ref->user))
		$ref->user = JFactory::getUser(); 
		
		if (!isset($ref->cart->user))
		{
			$ref->cart->user = OPCmini::getModel('user');
			$ref->cart->userDetails = $ref->cart->user->getUser();
		}
		$html = ''; 
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'cart'.DIRECTORY_SEPARATOR.'tmpl'.DIRECTORY_SEPARATOR.'default_shopperform.php')) return ''; 
		$adminID = JFactory::getSession()->get('vmAdminID');
		if ((JFactory::getUser()->authorise('core.admin', 'com_virtuemart') || JFactory::getUser($adminID)->authorise('core.admin', 'com_virtuemart')) && (VmConfig::get ('oncheckout_change_shopper', 0))) { 

			$html = $ref->loadTemplate ('shopperform');
			$html .= $this->getNewUserHtml(); 
			
		}

		
		
		
		
		return $html; 
	}

	function showDeliveryDate($ref)
	{
		
		
		require_once (JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		
		

		$renderer = OPCrenderer::getInstance(); 
		
		
		$default = new stdClass(); 
		$default->enabled = false; 
		$default->required = false; 
		$default->offset = 0; 
		$default->format = 'd MM yy';
		$default->storeformat = 'yy-mm-dd'; 
		$default->hollidays = ''; 
		
		$config = OPCconfig::getValue('opc_delivery_date', '', 0, $default, true); 
		
		$required = ''; 
		if (!empty($config->required))
		{
			$required = ' required="required" opcrequired="opcrequired" '; 
		}
		
		$now = $stored = ''; 

		$session = JFactory::getSession(); 
		$data = $session->get('opc_fields', '', 'opc'); 
		
		$opc_conference_mode = OPCconfig::get('opc_conference_mode', false); 
		if (!empty($opc_conference_mode)) {
			$error_redirect = JRequest::getVar('error_redirect', false); 
			if (empty($error_redirect)) {
			  $data = array(); 
			}
		}
		
		
		if (empty($data)) $data = array(); 
		else
		$data = @json_decode($data, true); 
		if (!empty($data['p_id']))
		$payment_default = $data['p_id']; 

		
		
		if (!empty($data['date_picker_store']))
		{
			$now = $data['date_picker_text']; 
			$stored = $data['date_picker_store'];
		}
		if (empty($config->enabled)) return ''; 
		$cgif = $this->getUrl(true).'components/com_onepage/assets/img/calendar.png'; 
		$html = $this->fetch($this, 'delivery_date', array('config'=>$config, 'calendard_gif'=>$cgif, 'now'=>$now, 'required'=>$required)); 
		$html .=  '<input class="opc_date_picker_store" id="opc_date_picker_store" type="hidden" name="opc_date_picker_store" value="'.$stored.'" />';
		return $html; 
		
	}
	
	
	function loadStored(&$cart)
	{
		$session = JFactory::getSession(); 
		$data = $session->get('opc_fields', '', 'opc'); 
		
		$user_id = JFactory::getUser()->get('id'); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		$opc_conference_mode = OPCconfig::get('opc_conference_mode', false); 
		if (!empty($opc_conference_mode)) {
			
			$error_redirect = JRequest::getVar('error_redirect', false); 
			if (empty($error_redirect)) {
			  $cart->BT = array(); 
			  $cart->ST = 0; 
			  $cart->STsameAsBT = true; 
			  $cart->BTaddress = array(); 
			  $cart->STaddress = array(); 
			  $cart->couponCode = ''; 
			  $opc_debug = OPCconfig::get('opc_debug', false);  if (!empty($opc_debug)) { JFactory::getApplication()->enqueueMessage('OPC Debug: Coupon removed at '.__FILE__.':'.__LINE__); }
			  $session = JFactory::getSession(); 
			  $data = $session->clear('opc_fields', 'opc'); 
			  
			  return; 
			}
		}
		
		
		if (empty($data)) {
			// this is the first visit and we will load the user's data: 
			$fields = array(); 
			$user_id = JFactory::getUser()->get('id'); 
			if (!empty($user_id)) {
				
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loggedshopper.php'); 
				$BT = OPCLoggedShopper::getUserInfos($user_id, 'BT'); 
				$cart->BT = $BT; 
				$fields['BT'] = $BT; 
				
				if (empty($BT)) return array(); 
				$ST = OPCLoggedShopper::getUserInfos($user_id, 'ST'); 
				 if (!empty($ST)) {
					$cart->ST = $ST; 
					$cart->STsameAsBT = false; 
					$fields['stopen'] = 1; 
					$fields['ST'] = $ST; 
					$cart->selected_shipto = $ST['virtuemart_userinfo_id']; 
					$cart->RDopen = true; 
					$cart->savedST = $ST; 
				}
				else {
					$cart->ST = $BT; 
				}
				$RD = OPCLoggedShopper::getUserInfos($user_id, 'RD'); 
				if (!empty($RD)) {
				 $cart->RD = $RD; 
				 $cart->RDopen = true; 		
				 $fields['third_address_opened'] = 1; 
				 $fields['RD'] = $RD; 
				}
				else {
				$render_in_third_address = OPCconfig::get('render_in_third_address', array()); 
				$switch_rd = OPCconfig::get('opc_switch_rd', false); 
				$skip = array('virtuemart_userinfo_id', 'virtuemart_order_userinfo_id', 'virtuemart_user_id', 'address_type'); 
				if (!empty($render_in_third_address)) {
	     
				$opc_btrd_def = OPCconfig::get('opc_btrd_def', false); 
	
	
	if (((empty($cart->RD)) && (!empty($cart->ST))) && (empty($opc_btrd_def))) {
		$cart->RD = $cart->ST; 
		$cart->RDopen = false; 	
		
	}
	else
	if ((empty($cart->RD)) && (!empty($cart->BT))) {
		$cart->RD = $cart->BT;
		$cart->RDopen = false; 		
	}
		  
		  
		  
	  }
	  
	  }
				$txt = json_encode($fields); 
			$session = JFactory::getSession(); 
			$session->set('opc_fields', $txt, 'opc'); 
			
			
			
			}
			
			
			
			
			return $fields; 
		}
		
		$fields = @json_decode($data, true); 
		
		if (isset($fields['user_id'])) {
			if ($fields['user_id'] != $user_id) {
				$session->set('opc_fields', '', 'opc'); 
				return $this->loadStored($cart); 
			}
		}
		
		if (empty($cart->BT)) $cart->BT = array(); 
		
		if ((!empty($fields['BT'])) && (is_array($fields['BT'])))
		foreach ($fields['BT'] as $key=>$val)
		{
			$cart->BT[$key] = $val; 
		}
		$noshipto = OPCloader::getShiptoEnabled($cart); 
		if (empty($noshipto)) {
		if (!empty($fields['ST']))
		{
			$cart->ST = array(); 
			if (is_array($fields['ST']))
			foreach ($fields['ST'] as $key=>$val)
			{
				$cart->ST[$key] = $val; 
			}
		}
		
		if (!empty($fields['stopen']))
		$cart->STsameAsBT = 0; 
		else
		$cart->STsameAsBT = 1; 
		}
		else {
			$cart->STsameAsBT = 1; 
			$cart->ST = 0; 
		}
		$cart->RD = array(); 
		if (!empty($fields['RD']))
		{
			$cart->RD = $fields['RD']; 
			
		}
		
		if (isset($fields['third_address_opened']))
			$cart->RDopen = $fields['third_address_opened']; 
		else 
			$cart->RDopen = false; 

		
		
		
		return $fields; 
		
		
	}
	
	function getNewUserHtml()
	{

		
			require_once (JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
			
		
		$renderer = OPCrenderer::getInstance(); 
		$html = $this->fetch($this, 'add_shopper_link', array()); 
		return $html; 
	}
	
	function getPayment(&$ref, &$num, $ajax=false, $isexpress=false)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'pluginhelper.php'); 
		return OPCPluginHelper::getPayment($ref, $this, $num, $ajax, $isexpress); 

	}

	public static function isExpress(&$cart, &$html)
	{
		
		
		//&task=setpayment&expresscheckout=done&pm=9
		$task = JRequest::getVar('task', ''); 
		$express = JRequest::getVar('expresscheckout', ''); 
		$pm = JRequest::getInt('pm', 0); 
		if ((!empty($express)) && (!empty($pm))) {
			
				$cart->virtuemart_paymentmethod_id = (int)$pm; 
				
				return true; 
		}
		
		if (!empty($cart->layoutPath))
		{
			if (stripos($cart->layoutPath, DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR)!==false)
			{
				
				// 5 here means custom layout
				//return 5; 
			}
		}
		
		$dispatcher = JDispatcher::getInstance();
		$ret = array(); 
		$payment_id = 0; 
		$dispatcher->trigger('plgVmIsCustomCheckout', array( &$payment_id, &$cart, &$html));
		if (false)
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		if (!empty($payment_id))
		{
			
			
			$db = JFactory::getDBO(); 
			$q = 'select `payment_element` from #__virtuemart_paymentmethods where virtuemart_paymentmethod_id = '.(int)$payment_id; 
			$db->setQuery($q); 
			$element = $db->loadResult();

			
			if ($element === 'amazon') 
			{
				$cart->virtuemart_paymentmethod_id = $payment_id; 
				$cart->custom_payment_id = $payment_id; 
				
				
				return 5; 
			}
			
			
			
			
		}
		
		// express checkout: 
		$isexpress = false; 
		$session = JFactory::getSession(); 
		$data = $session->get('paypal', '', 'vm');
		if (empty($data)) return; 
		
		try {
		$pplJ = @json_decode($data, false); 
		if (empty($pplJ)) {
		  $ppl = @unserialize($data);
		}
		if (!empty($ppl))
		{
			if (!empty($ppl->token)) 
			{
				$isexpress = true; 
			}
			else return false; 
		}   
		else 
		return false; 

		}
		catch (Exception $e) {
		
		}
		
		{
			$payment_id = 0; 
			
			JPluginHelper::importPlugin('vmpayment');
			$dispatcher = JDispatcher::getInstance();
			$ret = array(); 
			
			$dispatcher->trigger('getPPLExpress', array( &$payment_id, &$cart));
			if (!empty($payment_id))
			{
				$cart->virtuemart_paymentmethod_id = $payment_id; 
				
				return true; 
			}
			return false; 
			
			
			if (!empty($ret))
			{
				foreach ($ret as $plugin)
				{
					if (isset($plugin->paypalproduct))
					{
						if ($plugin->paypalproduct == 'exp')
						{
							return true; 
							
						}
					}
				}
			}
		}
		return false; 
		// expressEND
	}


	// copyright: http://stackoverflow.com/questions/3810230/php-how-to-close-open-html-tag-in-a-string
	function closetags($html) {
		if (class_exists('Tidy'))
		{
			$tidy = new Tidy();
			$clean = $tidy->repairString($html, array(
			'output-xml' => true,
			'input-xml' => true
			));
			
			return $clean;
		}
		preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
		$openedtags = $result[1];
		preg_match_all('#</([a-z]+)>#iU', $html, $result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);
		if (count($closedtags) == $len_opened) {
			return $html;
		}
		$openedtags = array_reverse($openedtags);
		for ($i=0; $i < $len_opened; $i++) {
			if (!in_array($openedtags[$i], $closedtags)) {
				$html .= '</'.$openedtags[$i].'>';
			} else {
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
			}
		}
		return $html;
	} 

	
	public static function getPluginMethods ($type='shipment', $vendorId=1) {
		if (!empty(OPCloader::$methods[$type])) return OPCloader::$methods[$type]; 
		if (!class_exists ('VirtueMartModelUser')) {
			require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'user.php');
		}
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		$usermodel = OPCmini::getModel ('user');
		$user = $usermodel->getUser ();
		$user->shopper_groups = (array)$user->shopper_groups;

		$db = JFactory::getDBO ();

		$select = 'SELECT l.*, v.*, ';

		if (JVM_VERSION === 1) {
			$extPlgTable = '#__plugins';
			$extField1 = 'id';
			$extField2 = 'element';

			$select .= 'j.`' . $extField1 . '`, j.`name`, j.`element`, j.`folder`, j.`client_id`, j.`access`,
				j.`params`,  j.`checked_out`, j.`checked_out_time`,  s.virtuemart_shoppergroup_id ';
		} else {
			$extPlgTable = '#__extensions';
			$extField1 = 'extension_id';
			$extField2 = 'element';

			$select .= 'j.`' . $extField1 . '`,j.`name`, j.`type`, j.`element`, j.`folder`, j.`client_id`, j.`enabled`, j.`access`, j.`protected`, j.`manifest_cache`,
				j.`params`, j.`custom_data`, j.`system_data`, j.`checked_out`, j.`checked_out_time`, j.`state`,  s.`virtuemart_shoppergroup_id` ';
		}
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
		
		if (isset(VmConfig::$vmlang))
		$vmlang = VmConfig::$vmlang; 
		else 
		$vmlang = VMLANG; 
		$q = $select . ' FROM   `#__virtuemart_' . $type . 'methods_' . $vmlang . '` as l ';
		$q .= ' JOIN `#__virtuemart_' . $type . 'methods` AS v   USING (`virtuemart_' . $type . 'method_id`) ';
		$q .= ' LEFT JOIN `' . $extPlgTable . '` as j ON j.`' . $extField1 . '` =  v.`' . $type . '_jplugin_id` ';
		$q .= ' LEFT OUTER JOIN `#__virtuemart_' . $type . 'method_shoppergroups` AS s ON v.`virtuemart_' . $type . 'method_id` = s.`virtuemart_' . $type . 'method_id` ';
		$q .= ' WHERE v.`published` = "1" ';
		
		$q .= ' AND  (v.`virtuemart_vendor_id` = "' . $vendorId . '" OR   v.`virtuemart_vendor_id` = "0")  AND  (';

		foreach ($user->shopper_groups as $groups) {
			$q .= ' (s.`virtuemart_shoppergroup_id`= "' . (int)$groups . '") OR';
		}
		$q .= ' (s.`virtuemart_shoppergroup_id` IS NULL )) GROUP BY v.`virtuemart_' . $type . 'method_id` ORDER BY v.`ordering`';

		$db->setQuery ($q);

		$methods = $db->loadAssocList ();
		$arr = array(); 
		if (!empty($methods))
		foreach ($methods as $m)
		{
			$arr[$m['virtuemart_'.$type.'method_id']] = $m; 
			
		}
		OPCloader::$methods[$type] = $arr; 
		return $arr; 
		$err = $db->getErrorMsg ();
		if (!empty($err)) {
			vmError ('Error reading getPluginMethods ' . $err);
		}
		
		return count ($this->methods);
	}

	function prepareMethods(&$cart, $no_shipping=false)
	{
		
		if (!class_exists('vmPSPlugin')) 
		require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'vmpsplugin.php');
		
		JPluginHelper::importPlugin('vmpayment');
		if (empty($no_shipping))
		{
			JPluginHelper::importPlugin('vmshipment');
			
		}


		$dispatcher = JDispatcher::getInstance(); 
		if (!isset($cart))
		$cart = VirtueMartCart::getCart ();
		$plugins = array(); 
		$html = ''; 
		$results = $dispatcher->trigger('loadPluginJavascriptOPC', array( &$cart, &$plugins, &$html)); 

		
		
		if (!empty($html))
		{
			OPCloader::$extrahtml .= $html; 
		}
		
		
		
	}

	function getShipping(&$ref, &$cartZ, $ajax=false)
	{
		$html = ''; 
		if (empty($cartZ))
		{
			if (!empty($ref->cart))
			{
				$cart =& $ref->cart; 
			}
			else
			$cart = VirtueMartCart::getCart(false, false); 
		}
		else
		{
			$cart =& $cartZ; 
		}

		$cmd = JRequest::getVar('cmd', false); 


		

		
		if (!$ajax)
		{
			
			// so we don't update the address twice   
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'opc.php'); 
			$c = new VirtueMartControllerOpc();  
			
			$c->setAddress($cart, true, false, true); 

		}	


		$op_customer_shipping = OPCconfig::get('op_customer_shipping', false);

		if (($cmd != 'customershipping') && ($cmd != 'runpay'))
		if (!empty($op_customer_shipping))
		{

			$onclick = 'onclick="javascript: return Onepage.op_runSS(null, false, true, \'customershipping\');" ';
			$html = $this->fetch($ref, 'customer_shipping', array('onclick'=>$onclick)); 
			if (empty($html))
			$html = '<a href="#" '.$onclick.'  >'.OPClang::_('COM_ONEPAGE_CLICK_HERE_TO_DISPLAY_SHIPPING').'</a>'; 
			$html .= '<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" /><input type="hidden" name="virtuemart_shipmentmethod_id" checked="checked" id="shipment_id_0" value="choose_shipping" />'; 
			$shipping_choose_html = '<div id="customer_shipping_wrapper">'.$html.'</div>'; 
			$shipping_choose_html_a = array(); 
			$shipping_choose_html_a[] = $shipping_choose_html; 
		}



		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'ajaxhelper.php'); 
		$bhelper = new basketHelper; 



		if (!isset($shipping_choose_html_a))
		{
			$sh = $bhelper->getShippingArrayHtml($ref, $cart, $ajax);
			
		}
		else
		{
			$sh = $shipping_choose_html_a; 
		}

		
		if (empty($cart) || (empty($cart->products)))
		{
			
			$op_disable_shipping = OPCloader::getShippingEnabled($cart); 
			if (empty($op_disable_shipping))
			{
				$html = '<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" /><input type="hidden" name="virtuemart_shipmentmethod_id" checked="checked" id="shipment_id_0" value="choose_shipping" />'; 
			}
			$html .= '<div style="color: red; font-weight: bold;">'.OPCLang::_('COM_VIRTUEMART_EMPTY_CART').'</div>'; 
			$sh = array($html); 	 
		}

		$disable_payment_per_shipping = OPCconfig::get('disable_payment_per_shipping', false);

		$dpps_search = OPCconfig::get('dpps_search', array());
		$dpps_disable = OPCconfig::get('dpps_disable', array());
		$dpps_default = OPCconfig::get('dpps_default', array());

		if (!empty($disable_payment_per_shipping))
		{

			$session = JFactory::getSession(); 
			$dpps =  array();
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'transform.php');
			foreach ($sh as $k=>$cs)
			{
				foreach ($dpps_search as $key=>$val)
				{
					// if we find the need in the shipping, let's associate it with an id
					$val = urldecode($val); 
					if (strpos($cs, $val)!==false)
					{
						//if (!empty($dpps[$key])) continue; 
						$id = OPCTransform::getFT($cs, 'input', 'virtuemart_shipmentmethod_id', 'name', 'virtuemart_shipmentmethod_id', '>', 'value');
						if (is_array($id)) $id = reset($id); 
						if (empty($dpps[$key])) $dpps[$key] = array(); 
						$dpps[$key][] = $id; 
						
						
					}
				}
			}
			$session->set('dpps', $dpps); 
		}

		if (($cart->pricesUnformatted['billTotal']) && empty($cart->pricesUnformatted['billTotal']))
		$ph = array();
		else
		$ph = $bhelper->getPaymentArray(); 
		



		$bhelper->createDefaultAddress($ref, $cart); 
		
		
		
		
		$html = $bhelper->getPaymentArrayHtml($ref->cart, $ph, $sh); 
		self::$totals_html = basketHelper::$totals_html; 
		
		$bhelper->restoreDefaultAddress($ref, $cart); 
		
		
		$ret = '';
		
		$ret .= $html; 

		
		return $ret; 
	}

	function setDefaultShipping($sh, $ret)
	{
	}

	function addListeners($html)
	{
		
		
		$business_selector = OPCconfig::get('business_selector','');
		$opc_ajax_fields = OPCconfig::get('opc_ajax_fields',array());
		
		$brow = array(); 
		if (!empty($business_selector) || (!empty($opc_ajax_fields)))
		{
			$db = JFactory::getDBO(); 
			$q = 'select * from `#__virtuemart_userfields` where 1'; 
			$db->setQuery($q); 
			$browd = $db->loadAssocList(); 
			foreach ($browd as $key=>$val)
			{
				$brow[$val['name']] = $val; 
			}
			
		}


		if (!isset($opc_ajax_fields))
		{
			$opc_ajax_fields = array(); 
			$opc_ajax_fields[] = 'zip'; 
			$opc_ajax_fields[] = 'address_1'; 
			$opc_ajax_fields[] = 'address_2'; 
			$opc_ajax_fields[] = 'virtuemart_state_id'; 
			$opc_ajax_fields[] = 'virtuemart_country_id'; 
			
		}
		$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		
		$later = array('virtuemart_country_id', 'virtuemart_state_id',$opc_vat_key.'_field', 'pluginistraxx_euvatchecker' ); 
		
		
		if (!empty($business_selector) && (in_array($business_selector, $opc_ajax_fields)))
		{
			$ajaxf = $business_selector; 
			if (isset($brow[$ajaxf]))
			{
				$arr = array('select', 'checkbox', 'multicheckbox', 'radio', 'select-one'); 
				$type = $brow[$ajaxf]['type']; 
				if (in_array($type, $arr)) $onblur = 'onchange'; 
				
			}
			
			
			$html = str_replace('name="shipto_'.$ajaxf.'"', 'name="shipto_'.$ajaxf.'"'.' onchange="javascript:Onepage.business2field(this, true);" name="shipto_'.$ajaxf.'"', $html);
			
			$html = str_replace('name="'.$ajaxf.'"', 'name="'.$ajaxf.'"'.' onchange="javascript:Onepage.business2field(this, true);" id="'.$ajaxf.'"', $html);
			
			$ajaxf = $business_selector; 
			$html = str_replace('name="shipto_'.$ajaxf.'[]"', 'name="shipto_'.$ajaxf.'[]"'.' onchange="javascript:Onepage.business2field(this, true);" ', $html);
			
			$html = str_replace('name="'.$ajaxf.'[]"', 'name="'.$ajaxf.'[]"'.' onchange="javascript:Onepage.business2field(this, true);" id="'.$ajaxf.'[]"', $html);
		}
		else
		if (!empty($business_selector))
		{
			$ajaxf = $business_selector; 
			$html = str_replace('name="shipto_'.$ajaxf.'"', ' onchange="javascript:Onepage.business2field(this, false);" name="shipto_'.$ajaxf.'"', $html);
			
			$html = str_replace('name="'.$ajaxf.'"', ' onchange="javascript:Onepage.business2field(this, false);" name="'.$ajaxf.'"', $html);
			
			$ajaxf = $business_selector; 
			$html = str_replace('name="shipto_'.$ajaxf.'[]"', ' onchange="javascript:Onepage.business2field(this, false);" name="shipto_'.$ajaxf.'[]"', $html);
			
			$html = str_replace('name="'.$ajaxf.'[]"', ' onchange="javascript:Onepage.business2field(this, false);" name="'.$ajaxf.'[]"', $html);
			
			
			
			
		}
		
		
		
			
			foreach ($opc_ajax_fields as $ajaxf)
			{
				$onblur = 'onblur'; 
				if (isset($brow[$ajaxf]))
				{
					$arr = array('select', 'checkbox', 'multicheckbox', 'radio', 'select-one'); 
					$type = $brow[$ajaxf]['type']; 
					if (in_array($type, $arr)) $onblur = 'onchange'; 
					
				}
				
				if (!in_array($ajaxf, $later))
				{
				

				
					
					$html = str_replace('id="shipto_'.$ajaxf.'_field"', ' '.$onblur.'="javascript:Onepage.op_runSS(this);" id="shipto_'.$ajaxf.'_field"', $html);
				
				$html = str_replace('id="'.$ajaxf.'_field"', ' '.$onblur.'="javascript:Onepage.op_runSS(this);" id="'.$ajaxf.'_field"', $html);
					
					$html = str_replace('id="'.$ajaxf.'"', ' '.$onblur.'="javascript:Onepage.op_runSS(this);" id="'.$ajaxf.'_field"', $html);
					
					
					$html = str_replace('id="shipto_'.$ajaxf.'"', ' '.$onblur.'="javascript:Onepage.op_runSS(this);" id="shipto_'.$ajaxf.'_field"', $html);
				}
				
			}
			
			$user = JFactory::getUser(); 
			$uid = $user->get('id'); 
			$usersConfig = JComponentHelper::getParams( 'com_users' );
			$usernamechange = $usersConfig->get( 'change_login_name', true );
			if (empty($usernamechange))
			if (!empty($uid))
			{
				// username readonly
				$html = str_replace('name="username"', ' readonly="readonly" name="username"', $html); 
				$html = str_replace('name="opc_username"', ' readonly="readonly" name="opc_username"', $html); 
			}
			
			if (in_array('virtuemart_state_id', $opc_ajax_fields))
			{
				$html = str_replace('id="shipto_virtuemart_state_id"', 'id="shipto_virtuemart_state_id" onchange="javascript:Onepage.op_runSS(this);" ', $html);
			}
			
			$cccount = strpos($html, '"shipto_virtuemart_state_id"'); 

			if ($cccount !== false)
			{
				$par = "'true', ";
				$isThere = true;
			}
			else
			{
				$par = "'false', ";
				$isThere = false;
			}
			if (in_array('virtuemart_country_id', $opc_ajax_fields))
			{
				$html = str_replace('id="shipto_virtuemart_country_id"', 'id="shipto_virtuemart_country_id" onchange="javascript: Onepage.op_validateCountryOp2('.$par.'\'true\', this);" ', $html, $count);
			}
			else
			{
				
				$html = str_replace('id="shipto_virtuemart_country_id"', 'id="shipto_virtuemart_country_id" onchange="javascript: Onepage.changeStateList(this);" ', $html, $count);
			}
		
		
		// state fields
		$cccount = strpos($html, '"virtuemart_state_id"'); 
		if ($cccount !== false)
		{
			$par = "'true', ";
			$isThere = true;
		}
		else
		{
			$par = "'false', ";
			$isThere = false;
		}
		
		$count = 0; 
		
		if (in_array('virtuemart_state_id', $opc_ajax_fields))
		{
			$html = str_replace('id="virtuemart_state_id"', 'id="virtuemart_state_id" onchange="javascript:Onepage.op_runSS(this);" ', $html);
		}
		
		
		
		$opc_euvat = OPCconfig::get('opc_euvat',false);
		$opc_euvat_button = OPCconfig::get('opc_euvat_button',false);
		$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
		//		if (empty($opc_euvat_button))
		if (!empty($opc_euvat))
		{
			$opc_vat_key = OPCconfig::get('opc_vat_field', 'opc_vat'); 
			$html = str_replace('id="'.$opc_vat_key.'_field"', 'id="'.$opc_vat_key.'_field" onchange="javascript: return Onepage.validateOpcEuVat(this);" ', $html);
			
		}
		
		if (in_array('virtuemart_country_id', $opc_ajax_fields))
		{
			$html = str_replace('id="virtuemart_country_id"', 'id="virtuemart_country_id" onchange="javascript: Onepage.op_validateCountryOp2('.$par.'\'false\', this);" ', $html, $count);
		}
		else
		{
			$html = str_replace('id="virtuemart_country_id"', 'id="virtuemart_country_id" onchange="javascript: Onepage.changeStateList(this);" ', $html, $count);
		}
		
		//pluginistraxx_euvatchecker_field
		$html = str_replace('id="pluginistraxx_euvatchecker_field"', ' id="pluginistraxx_euvatchecker_field" onblur="javascript:Onepage.op_runSS(this, false, true);" ', $html); 
		// support for http://www.barg-it.de, plgSystemBit_vm_check_vatid
		
		
		if (version_compare(JVERSION, '1.6.0', '<')) 
		{ 
			$plugin_short_path = 'plugins/system/bitvatidchecker/';
		}
		else {
			$plugin_short_path = 'plugins/system/bit_vm_check_vatid/bitvatidchecker/';
		}
		
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$plugin_short_path))
		{
			include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'bit_vm_check_vatid'.DIRECTORY_SEPARATOR.'include.php'); 
		}
		
		// end support for http://www.barg-it.de, plgSystemBit_vm_check_vatid
		
		
		
		return $html;
	}
	function getJSValidatorScript($obj)
	{
		return $this->fetch($this, 'formvalidator', array()); 
	}

	function isRegistered()
	{
	}

	function isNoLogin()
	{
		
		
		
		
		$no_login_in_template = OPCconfig::get('no_login_in_template', false); 
		
		$currentUser = JFactory::getUser();
		$uid = $currentUser->get('id');
		if (!empty($uid)) 
		{ 

			$no_login_in_template = true; 
		}
		if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION')
		{
			$no_login_in_template = true; 
		}
		return $no_login_in_template; 
	}

	// input param is object
	function hasMissingFieldsST($STaddress)
	{



		$shipping_obligatory_fields = OPCconfig::get('shipping_obligatory_fields', array()); 

		$ignore = array('delimiter', 'captcha', 'hidden'); 

		$types = array(); 
		foreach ($STaddress as $key=>$val)
		{
			if (empty($val))
			if (in_array($key, $shipping_obligatory_fields))
			{
				
				if ($key == 'virtuemart_state_id')
				{
					$c = $val;
					$stateModel = OPCmini::getModel('state'); 
					
					$states = $stateModel->getStates( $c, true, true );
					if (!empty($states)) return true; 
					continue; 
				}
				return true; 
			}
			
		}
		
		return false; 
	}
	function hasMissingFields(&$BTaddress, $cart=null)
	{
		
		if (empty($cart)) {
		  $cart = VirtuemartCart::getCart(); 
		}
		

		$per_order_rendering = OPCconfig::get('per_order_rendering', array()); 
		
		
		$ignore = array('tos', 'agreed', 'customer_note'); 
		
		foreach ($per_order_rendering as $k=>$v)
		{
			if (in_array($v, $ignore))
			{
				unset($per_order_rendering[$k]); 
			}
		}
		
		if (!empty($per_order_rendering)) {
			OPCloader::opcDebug('per_order_rendering is not empty and thus loading unlogged them'); 
			return true; 
		}
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		
		$ret = OPCUserFields::hasMissingFields($BTaddress, $cart); 


		return $ret; 


		
	}

	function getRegistrationHhtml(&$obj, $force=false)
	{

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'unloggedshopper.php'); 
		return OPCUnloggedShopper::getRegistrationHhtml($obj, $this, $force);


	}
	
	function getThirdAddress(&$obj)
	{

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_address.php'); 
		$user_id = JFactory::getUser()->get('id'); 
		return OPCthirdAddress::renderThirdAddress($obj->cart, $user_id);


	}


	public function customizeFieldsPerOPCConfig(&$userFields)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		return OPCUserFields::customizeFieldsPerOPCConfig($userFields, $this); 
		
		
		
		
	}

	function getHtmlInBetween(&$ref)
	{
		$html = '<div class="opc_errors" id="opc_error_msgs" style="display: none; width: 100%; clear:both; border: 1px solid red;">&nbsp;</div>';
		return $html;
	}



	static function setShopperGroup($id, $remove=array())
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shoppergroups.php'); 
		return OPCShopperGroups::setShopperGroups($id, $remove); 
		
		
	}



	// only for unlogged users 
	static function getSetShopperGroup($debug=false)
	{

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shoppergroups.php'); 
		return OPCShopperGroups::getSetShopperGroup($debug); 
	}

	public static function storeError($err)
	{
		JFactory::getSession()->set('opc_last_error', $err); 
	}
	static function getDefaultCountry(&$cart, $searchBT=false )
	{
		
		if ($searchBT)
		{
			if (!empty($cart->BT['virtuemart_country_id']))
			return $cart->BT['virtuemart_country_id'];
		}
		if (defined('OPC_DEFAULT_COUNTRY')) return OPC_DEFAULT_COUNTRY; 
		if (defined('DEFAULT_COUNTRY')) 
		if (is_numeric(DEFAULT_COUNTRY))
		{
			define('OPC_DEFAULT_COUNTRY', DEFAULT_COUNTRY); 
			return DEFAULT_COUNTRY;
		}
		
		$user_id = JFactory::getUser()->get('id'); 
		if (!empty($user_id)) {
		   $db = JFactory::getDBO(); 
		   $q = 'select `virtuemart_country_id` from `#__virtuemart_userinfos` where `virtuemart_user_id` = '.(int)$user_id.' and `address_type` = "BT" limit 0,1'; 
		   $db->setQuery($q); 
		   $rid = $db->loadResult(); 
		   if (!empty($rid)) {
			  $rid = (int)$rid; 
		      define('DEFAULT_COUNTRY', $rid ); 
			  define('OPC_DEFAULT_COUNTRY', $rid ); 
			
			return $rid; 
		   }
		}
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		$op_use_geolocator = OPCconfig::get('op_use_geolocator', false); 
		$default_country_array = OPCconfig::get('default_country_array', array()); 
		
		$default_shipping_country = OPCconfig::get('default_shipping_country', ''); 
		
		if (!empty($op_use_geolocator))
		{
			
			if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php'))
			{
				include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_geolocator'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'helper.php');
				if (class_exists('geoHelper')) 
				$c2 = geoHelper::getCountry2Code("");
				
				if (!empty($c2))
				{
					$db = JFactory::getDBO(); 
					$q = "select virtuemart_country_id from #__virtuemart_countries where country_2_code = '".$db->escape($c2)."' limit 0,1"; 
					$db->setQuery($q); 
					$c = $db->loadResult(); 
					
					if (!empty($c)) 
					{
						define('OPC_DEFAULT_COUNTRY', $c); 
						if (!defined('DEFAULT_COUNTRY'))
						define('DEFAULT_COUNTRY', $c); 
						
						// case IP address
						return $c; 
					}
				}
			}
		}
		$lang = JFactory::getLanguage();
		$tag = $lang->getTag();
		
		
		
		if (!empty($default_country_array[$tag]))
		{
			define('DEFAULT_COUNTRY', $default_country_array[$tag]); 
			define('OPC_DEFAULT_COUNTRY', $default_country_array[$tag]); 
			return $default_country_array[$tag];
		}
		
		if (!empty($default_shipping_country))
		{
			define('DEFAULT_COUNTRY', $default_shipping_country ); 
			define('OPC_DEFAULT_COUNTRY', $default_shipping_country ); 
			
			return $default_shipping_country; 
		}
		
		
		
		$vendor = OPCloader::getVendorInfo($cart); 
		if (!empty($vendor))
		{
			$c = $vendor['virtuemart_country_id']; 
			define('DEFAULT_COUNTRY', $c ); 
			define('OPC_DEFAULT_COUNTRY', $c ); 
			return $c; 
		}
		
		
	}

	public static function setRegType($inreg=false)
	{
	
		
		if (!defined('VM_REGISTRATION_TYPE'))
		{
			
				$user_id = JFactory::getUser()->get('id'); 
				if (!empty($user_id)) {
				 define('VM_REGISTRATION_TYPE', 'NORMAL_REGISTRATION'); 
				 
				 return; 
				}
				
				$usersConfig = JComponentHelper::getParams( 'com_users' );
				$canrun = $usersConfig->get('allowUserRegistration'); 
				$canrun = (int)$canrun; 
				if ($canrun === 0) {
				 define('VM_REGISTRATION_TYPE', 'NO_REGISTRATION'); 
				 return; 
				}
				
			if (VmConfig::get('oncheckout_only_registered', 0))
			{
				if (VmConfig::get('oncheckout_show_register', 0))
				define('VM_REGISTRATION_TYPE', 'NORMAL_REGISTRATION'); 
				else 
				define('VM_REGISTRATION_TYPE', 'SILENT_REGISTRATION'); 
			}
			else
			{
				if (empty($inreg))
				{
					if (VmConfig::get('oncheckout_show_register', 0))
					define('VM_REGISTRATION_TYPE', 'OPTIONAL_REGISTRATION'); 
					else 
					define('VM_REGISTRATION_TYPE', 'NO_REGISTRATION'); 
				}
				else
				{
					if (VmConfig::get('oncheckout_show_register', 0))
					define('VM_REGISTRATION_TYPE', 'NORMAL_REGISTRATION'); 
					else 
					define('VM_REGISTRATION_TYPE', 'NO_REGISTRATION'); 
					
				}
			}
		} 
	}

	function getSTfields(&$obj, $unlg=false, $no_wrapper=false, $dc='')
	{

		static $isUpdated; 





		if (OPCloader::logged($obj->cart) && (empty($unlg)))
		{
 
			return $this->getUserInfoST($obj); 
		}





		if (!class_exists('VirtueMartCart'))
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
		
		if (!empty($obj->cart))
		$cart =& $obj->cart; 
		else
		$cart = VirtueMartCart::getCart();
		
		
		
		
		$default_shipping_country = OPCconfig::get('default_shipping_country', ''); 
		
		if (!empty($dc))
		$default_shipping_country = $dc; 
		else
		$default_shipping_country = OPCloader::getDefaultCountry($cart); 
		
		$noshipto = OPCloader::getShiptoEnabled($cart); 
		if ((empty($noshipto)) && 
		 ((!empty($cart)) && (isset($cart->ST) && ($cart->ST === 0))) &&
		 (isset($cart->savedST)))
		{
			$cart->ST = $cart->savedST;
			if (isset($cart->ST['virtuemart_country_id']))
			$default_shipping_country = $cart->ST['virtuemart_country_id']; 
			

		}
		else {
			if ((!empty($cart->ST)) && (!empty($cart->ST['virtuemart_country_id']))) {
				$default_shipping_country = $cart->ST['virtuemart_country_id']; 
			}
		}
		
		


		$type = 'ST'; 
		$this->address_type = 'ST'; 
		// for unlogged
		// for unlogged
		$virtuemart_userinfo_id = 0;
		
		$new = 1; 
		if (!empty($unlg)) $new = false;
		$fieldtype = $type . 'address';

		$fieldtype = 'STaddress'; 




		if (!empty($cart->ST))
		$savedST = OPCloader::copyAddress($cart->ST); 

		if (!empty($cart->ST) && (count($cart->ST)>2)) $new = false;    






		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php');  
		OPCUserFields::populateCart($cart, 'ST', $new);
		


		if (!empty($savedST))
		OPCloader::restoreDataInCart($cart->ST, $savedST); 



		

		OPCloader::setRegType(); 

		$op_disable_shipto = OPCloader::getShiptoEnabled($cart); 
		if(!class_exists('VirtuemartModelUserfields')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'userfields.php');

		$corefields = array( 'name','username', 'email', 'password', 'password2' , 'agreed','language', 'tos');

		
		{
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');    
			
			
		}

		$custom_rendering_fields = OPCconfig::get('custom_rendering_fields', array());
		$shipping_obligatory_fields = OPCconfig::get('shipping_obligatory_fields', array());

		$only_one_shipping_address_hidden = OPCconfig::get('only_one_shipping_address_hidden', false); 


		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php');  
		
		OPCUserFields::populateCart($cart, 'ST', false);
		$userFields = $cart->STaddress; 

		
		if (!empty($userFields['fields']))
		foreach ($userFields['fields'] as $key=>$uf)   
		{
			
			if (stripos('virtuemart_country_id', $key)!==false)
		  {
			   $userFields['fields'][$key]['formcode'] = str_replace('virtuemart_country_id_field', 'virtuemart_country_id', $userFields['fields'][$key]['formcode']);  
		  }
			
			OPCloader::$fields_names['shipto_'.$key] = $userFields['fields'][$key]['title']; 
			$userFields['fields'][$key]['formcode'] = str_replace('vm-chzn-select', '', $userFields['fields'][$key]['formcode']); 
			if (!empty($corefields))
			foreach($corefields as $k=>$f)
			{

				if ($f == $uf['name'])
				{
					unset($userFields['fields'][$key]);   
					unset($corefields[$k]);
				}
				
			}
			if (empty($custom_rendering_fields)) $custom_rendering_fields = array(); 
			if (in_array($uf['name'], $custom_rendering_fields))
			{
				unset($userFields['fields'][$key]); 
				continue; 
			}
			
			$userFields['fields'][$key]['formcode'] = str_replace('class="virtuemart_country_id required"', 'class="virtuemart_country_id"', $userFields['fields'][$key]['formcode']);
			
			$userFields['fields'][$key]['formcode'] = str_replace('required>', '', $userFields['fields'][$key]['formcode']);
			$userFields['fields'][$key]['formcode'] = str_replace(' required ', '', $userFields['fields'][$key]['formcode']);
			
			$userFields['fields'][$key]['formcode'] = str_replace('required"', '"', $userFields['fields'][$key]['formcode']);
			
			if ($key == 'address_type_name')
			{
				//$userFields['fields'][$key]['formcode'] = str_replace('Shipment', JText::_('COM_VIRTUEMART_SHOPPER_FORM_SHIPTO_LBL'), $userFields['fields'][$key]['formcode']); 
				
			}
			
			
		  
		  			if (stripos('virtuemart_state_id', $key)!==false)
		  {
			   $userFields['fields'][$key]['formcode'] = str_replace('virtuemart_state_id_field', 'virtuemart_state_id', $userFields['fields'][$key]['formcode']);  
		  }
			
			if (!empty($userFields['fields'][$key]['required']))
			{
				$userFields['fields'][$key]['required'] = false; 
			}
			if (!empty($shipping_obligatory_fields))
			{
				if (in_array($key, $shipping_obligatory_fields))
				$userFields['fields'][$key]['required'] = true; 
			}
			// let's add a default address for ST section as well: 
			if ((($key == 'virtuemart_country_id')))  {
			if (((empty($unlg))) || (!empty($default_shipping_country)))
			{
				
				
				
				$userFields['fields'][$key]['formcode'] = str_replace('selected="selected"', '', $userFields['fields'][$key]['formcode']);

				$search = 'value="'.$default_shipping_country.'"';
				$replace = ' value="'.$default_shipping_country.'" selected="selected" ';
				$userFields['fields'][$key]['formcode'] = str_replace($search, $replace, $userFields['fields'][$key]['formcode']);

				$userFields['fields'][$key]['value'] = $default_shipping_country; 
				
				
			}
			  require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'commonhtml.php'); 
			  $clist = array(); 
			  OPCcommonhtml::getCountriesOptionsVals($clist); 
			  $userFields['fields'][$key]['options'] = $clist; 
			   $userFields['fields'][$key]['type'] = 'select'; 
			   
			   
			  
			}
			
			if (($key == 'virtuemart_country_id'))
			{
				
				$userFields['fields'][$key]['formcode'] = str_replace('name=', ' autocomplete="off" name=', $userFields['fields'][$key]['formcode']); 
			}
			
			
			if (isset($userFields['fields'][$key]))
			{
				
				
				if ($key == 'virtuemart_state_id')
				{
					
					if (!empty($cart->ST['virtuemart_country_id']))
					$c = $cart->ST['virtuemart_country_id']; 
					else $c = $default_shipping_country; 
					
					if (empty($c))
					{
						$vendor = OPCloader::getVendorInfo($cart); 
						$c = $vendor['virtuemart_country_id']; 
					}
					
					$retval = array(); 
					$html = $this->getStateHtmlOptions($cart, $c, 'ST', $retval);
					if (!empty($cart->ST['virtuemart_state_id']))
					{
						
						$html = str_replace('value="'.$cart->ST['virtuemart_state_id'].'"', 'value="'.$cart->ST['virtuemart_state_id'].'" selected="selected"', $html); 
						$userFields['fields']['virtuemart_state_id']['value'] = $cart->ST['virtuemart_state_id']; 
					}
					else
					if (!empty($cart->ST['shipto_virtuemart_state_id']))
					{

						$html = str_replace('value="'.$cart->ST['shipto_virtuemart_state_id'].'"', 'value="'.$cart->ST['shipto_virtuemart_state_id'].'" selected="selected"', $html); 
						$userFields['fields']['virtuemart_state_id']['value'] = $cart->ST['shipto_virtuemart_state_id']; 
					}
					$userFields['fields']['virtuemart_state_id']['type'] = 'select'; 
					$userFields['fields']['virtuemart_state_id']['options'] = $retval; 
					
					
					
					if (!empty($userFields['fields'][$key]['required']))
					$userFields['fields']['virtuemart_state_id']['formcode'] = '<select class="inputbox multiple opcrequired" id="shipto_virtuemart_state_id" opcrequired="opcrequired" size="1"  name="shipto_virtuemart_state_id" >'.$html.'</select>'; 
					else
					$userFields['fields']['virtuemart_state_id']['formcode'] = '<select class="inputbox multiple" id="shipto_virtuemart_state_id"  size="1"  name="shipto_virtuemart_state_id" >'.$html.'</select>'; 
					
					
					$userFields['fields']['virtuemart_state_id']['formcode'] = str_replace('id="virtuemart_state_id"', 'id="'.$userFields['fields']['virtuemart_state_id']['name'].'"', $userFields['fields']['virtuemart_state_id']['formcode']); 
				}
			}
		}
		

		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		$this->_model = OPCmini::getModel('user'); 
		$layout = 'default';

		$render_as_hidden = OPCconfig::get('render_as_hidden', array()); 
		
		$hidden = array(); 
		$hidden_html = ''; 
		if (!empty($userFields['fields']))
		foreach ($userFields['fields'] as $key=>$val)
		{
			if (in_array($val['name'], $render_as_hidden)) {
			 $val['hidden'] = true; 
			}
			if (!empty($val['hidden']))
			{
				$hidden[] = $val; 
				$hidden_html .= $val['formcode']; 
				unset($userFields['fields'][$key]); 
			}
		}
		
		OPCUserFields::addSpecialRequired($userFields, 'ST'); 
		OPCUserFields::addDelimiters($userFields); 	 
		OPCUserFields::addListenersToFields($userFields); 
		
		$vars = array(
		'rowFields' => $userFields, 
		'rowFields_st' => $userFields, 
		'cart' => $cart, 
		'opc_logged' => $unlg,
		);
		$html = $this->fetch($this, 'list_user_fields_shipping.tpl', $vars); 
		$hidden_html = str_replace('"required"', '""', $hidden_html); 
		$hidden_html = '<div style="display:none;">'.$hidden_html.'</div>'; 
		$html .= $hidden_html; 

		//$html = $this->addListeners($html);
		
		if (empty($custom_rendering_fields)) $custom_rendering_fields = array(); 
		if (in_array('virtuemart_country_id', $custom_rendering_fields)) $html .= '<input type="hidden" id="shipto_virtuemart_country_id" name="shipto_virtuemart_country_id" value="'.$default_shipping_country.'" />'; 
		if ((in_array('virtuemart_state_id', $custom_rendering_fields)))
		$html .= '<input type="hidden" id="shipto_virtuemart_state_id" name="shipto_virtuemart_state_id" value="0" />';   

		$html = str_replace('class="required"', 'class=" "', $html);

		$vars = array('op_shipto' => $html); 

		if (!empty($only_one_shipping_address_hidden) && (!empty($unlg)))
		{

			$html2 = '<input type="hidden" id="sachone" name="sa" value="adresaina" /><div id="ship_to_wrapper"><div id="idsa">'.$html.'</div></div>'; 
			
			
		}
		else
		{
			
			$html2 = $this->fetch($this, 'single_shipping_address.tpl', $vars); 

			if (empty($html2) && (!empty($unlg)))
			{
				
				$html2 = '<div id="ship_to_wrapper"><input type="checkbox" id="sachone" name="sa" value="adresaina" onkeypress="javascript: Onepage.showSA(this, \'idsa\');" onclick="javascript: Onepage.showSA(this, \'idsa\');" autocomplete="off" />'.OPCLang::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL').'<div id="idsa" style="display: none;">
								'.$html.'</div></div>'; 
			}
		}





		// if theme does not exists, return legacy html
		if (empty($html2) || (!empty($no_wrapper))) 
		return $html; 



		return $html2;

	}

	public static function getShipToOpened()
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		$op_shipto_opened_default = OPCconfig::get('op_shipto_opened_default', false); 
		$only_one_shipping_address_hidden = OPCconfig::get('only_one_shipping_address_hidden', false); 
		if (!empty($only_one_shipping_address_hidden))
		$op_shipto_opened = true; 
		else 
		$op_shipto_opened = $op_shipto_opened_default;  
		
		
		$session = JFactory::getSession(); 
		
		$opc_conference_mode = OPCconfig::get('opc_conference_mode', false); 
		if (!empty($opc_conference_mode)) {
			$error_redirect = JRequest::getVar('error_redirect', false); 
			if (empty($error_redirect)) {
			  return $op_shipto_opened; 
			}
		}
		
		$saved_f = $session->get('opc_fields', array(), 'opc'); 
		if (empty($saved_f)) $saved_fields = array(); 
		else
		$saved_fields = @json_decode($saved_f, true); 
	
	
	if ((!empty($saved_fields)) && isset($saved_fields['stopen']) && ($saved_fields['stopen'] == true)) {
		return true; 
	}
		
		
		return $op_shipto_opened; 
	}

	public static function copyAddress($address)
	{
		$arr = array(); 
		if (empty($address)) return $arr; 
		foreach ($address as $key=>$val) $arr[$key] = $val; 
		return $arr; 
	}
	public static function restoreDataInCart(&$address, $arr)
	{
		if (is_array($address))
		{
			foreach ($arr as $key=>$val)
			{
				if (!empty($val))
				$address[$key] = $val; 
			}
		}
		
		if (is_object($address))
		{
			foreach ($arr as $key=>$val)
			{
				if (!empty($val))
				$address->$key = $val; 
			}
		}
	}

	function reverseId($html, $orig, $new)
	{
		// replaces name and id
		$html = str_replace($orig, $new, $html); 
		
		return $html;
	}
	public static function logged(&$cart)
	{
		static $c; 
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		$umodel = OPCmini::getModel('User'); 
		
		$virtuemart_userinfo_id = 0;
		
		
		
		$user = JFactory::getUser();
		$userId = (int)$user->id; 
		
		
		
		// support for j1.7+
		if (!empty($user->guest) && ($user->guest == '1')) return false; 
		
		if (empty($userId)) return false; 
		
		if ((!empty($c)) && (isset($c[$userId]))) { 
		 $virtuemart_userinfo_id = $uid = $c[$userId]; 
		}
		else
		{
		$db = JFactory::getDBO(); 
		$q = "select virtuemart_userinfo_id from #__virtuemart_userinfos where virtuemart_user_id = '".$userId."' and address_type = 'BT' limit 0,1 "; 
		$db->setQuery($q); 
		$virtuemart_userinfo_id = $uid = $db->loadResult(); 
		
		if (empty($c)) $c = array(); 
		$c[$userId] = (int)$virtuemart_userinfo_id; 
		}
		
		
		
		if (empty($uid)) return false;
		if (method_exists($umodel, 'setId'))
		$umodel->setId($userId); 
		
		
		
		
		
		
		if (empty($virtuemart_userinfo_id)) return false; 
		else return true;


		
	}

	public static $vendorInfo;

	public static function &getVendorInfo(&$cart)
	{

		if (OPCloader::$vendorInfo == null) 
		if (OPCloader::tableExists('virtuemart_vmusers'))
		{
			if (empty($cart->vendorId)) $vendorid = 1; 
			else $vendorid = $cart->vendorId;
			{
				$dbj = $db = JFactory::getDBO(); 

				$q = "SELECT * FROM `#__virtuemart_userinfos` as ui, #__virtuemart_vmusers as uu WHERE ui.virtuemart_user_id = uu.virtuemart_user_id and uu.user_is_vendor = 1 and uu.virtuemart_vendor_id = '".(int)$vendorid."' limit 0,1";
				$dbj->setQuery($q);
				
				$vendorinfo = $dbj->loadAssoc();
				if (empty($vendorinfo)) $vendorinfo = array(); 
				if (!empty($vendorinfo)) {
					
				 $q = 'select * from #__users where id = '.(int)$vendorinfo['virtuemart_user_id']; 
				 $db->setQuery($q); 
				 $res = $db->loadAssoc(); 
				 if (!empty($res)) {
				    $vendorinfo['email'] = $res['email']; 
					$vendorinfo['vendorEmail'] = $res['email']; 
					$vendorinfo['vendor_name'] = JFactory::getConfig()->get('fromname'); ; 
				 }
				}
				if (empty($vendorinfo['vendorEmail']))
				{
				  $vendorinfo['vendorEmail'] = JFactory::getConfig()->get('mailfrom'); 
				  $vendorinfo['vendor_name'] = JFactory::getConfig()->get('fromname'); 
				}
				
				OPCloader::$vendorInfo = $vendorinfo; 
				
				return $vendorinfo; 
			}
		}
		else
		return null; 

		return OPCloader::$vendorInfo; 
	}

	function getCartfields(&$obj)
	{
		if (!defined('VM_VERSION') || (VM_VERSION < 3)) return ''; 
		if (!defined('VM_REGISTRATION_TYPE')) $this->setRegType(); 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 


		if (!empty($obj->cart)) 
		$cart =& $obj->cart; 
		else
		$cart = VirtueMartCart::getCart();
		
		
		
		
		
		OPCUserFields::populateCart($cart, 'ST', false);
		if (empty($cart->cartfieldsaddress)) return; 
		$userFields = $cart->cartfieldsaddress; 
		
		if (empty($userFields) || (empty($userFields['fields']))) return ''; 
		
		$skipreorder = array('email'); 
		OPCUserFields::getUserFields($userFields, $this, $cart, array(), array(), $skipreorder); 
		
		
		$hidden = array(); 
		$hidden_html = ''; 
		$render_as_hidden = OPCconfig::get('render_as_hidden', array()); 
		if (!empty($userFields['fields']))
		foreach ($userFields['fields'] as $key=>$val)
		{
			OPCloader::$fields_names[$key] = $userFields['fields'][$key]['title']; 
			
			if (in_array($val['name'], $render_as_hidden)) {
			 $val['hidden'] = true; 
			}
			
			if (!empty($val['hidden']))
			{
				$hidden[] = $val; 
				$hidden_html .= $val['formcode']; 
				unset($userFields['fields'][$key]); 
			}
			
		}
		
		
		if (empty($userFields))
		{
			$userFields = array(); 
			$userFields['fields'] = array(); 
			
		}
		
		OPCUserFields::addSpecialRequired($userFields, ''); 
		OPCUserFields::addDelimiters($userFields); 
		
		
		
		$vars = array(
		'rowFields' => $userFields, 
		'rowFields_cart' => $userFields, 
		'cart'=> $cart, 
		'is_logged'=> false);
		
		
		$html = $this->fetch($this, 'list_user_fields_cart.tpl', $vars); 
		if (empty($html)) {
			$html = $this->fetch($this, 'list_user_fields.tpl', $vars); 
		}
		
		$hidden_html = str_replace('"required"', '""', $hidden_html); 
		$hidden_html = '<div style="display:none;">'.$hidden_html.'</div>'; 
		$html .= $hidden_html; 


		return $html; 
		
	} 
	function getBTfields(&$obj, $unlg=false, $no_wrapper=false, $is_registration=false)
	{





		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 

		$business_fields = OPCconfig::get('business_fields',array());
		$business_fields2 = OPCconfig::get('business_fields2',array());
		
		
		$default_shipping_country = OPCloader::getDefaultCountry($cart); 
		
		$islogged = OPCloader::logged($obj->cart); 

		if ($islogged && (empty($unlg)))
		{

			return $this->getUserInfoBT($obj); 
		}



		{
			if (!class_exists('VirtueMartCart'))
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
			
			if (!empty($obj->cart)) 
			$cart =& $obj->cart; 
			else
			$cart = VirtueMartCart::getCart();
			
			
			
			
			$type = 'BT'; 
			$this->address_type = 'BT'; 
			// for unlogged
			$virtuemart_userinfo_id = 0;
			$this->virtuemart_userinfo_id = 0;
			$new = 1; 
			
			$user_id = JFactory::getUser()->get('id'); 
			
			 if (!class_exists('VirtuemartModelUser'))
	    require(JPATH_VM_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'models' .DIRECTORY_SEPARATOR. 'user.php');
		$userModel = new VirtuemartModelUser();
			if (method_exists($userModel, 'setId')) {
	   $userModel->setId($user_id);
		}
		    //this resets current state of cart->BTaddress and cart->BT: 
			if (!empty($is_registration)) {
				$user_id = JFactory::getUser()->get('id'); 
				// when this function is called from registration page: 
				if (!empty($user_id)) {
					
					
					
					require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'loggedshopper.php'); 
					$userFields = OPCLoggedShopper::getSetAllBt($cart); 
					
					if (isset($cart->BT['virtuemart_userinfo_id'])) 
					$this->virtuemart_userinfo_id = $virtuemart_userinfo_id = $cart->BT['virtuemart_userinfo_id']; 
					
					$islogged = true; 
					
				}
				$new = false;
			}
			$fieldtype = 'BTaddress';

			if (empty($cart->BT)) $cart->BT = array();    
			$user = JFactory::getUser();
			$uid = $user->get('id');

			// PPL Express address: 
			$moveBT = false; 
			$count = 0; 


			$bt_fields_from = OPCconfig::get('bt_fields_from', 1); 
			

			if (!empty($cart->savedST))
			if (!$islogged)
			{

				foreach ($cart->savedST as $key=>$val)
				{
					if ($key == 'virtuemart_country_id') continue; 
					if ($key == 'virtuemart_state_id') continue;
					if (substr($key, 0, 7)==='shipto_') continue; 
					if (empty($cart->BT[$key]) && (!empty($val)))
					{
						
						
						$count++; 
					}
					else
					if ((!empty($cart->BT[$key])) && ($val != $cart->BT[$key]))
					{
						$count--; 
					}
				}



				if ($count > 0)
				{
					if ($cart->savedST['virtuemart_country_id'] != $cart->BT['virtuemart_country_id'])
					{
						$cart->BT['virtuemart_state_id'] = 0; 
					}
					foreach ($cart->savedST as $key=>$val)
					{
						if (!empty($val))
						$cart->BT[$key] = $val; 
					}
				}
			}



			if (empty($cart->BT['virtuemart_country_id'])) 
			{

				if (!empty($default_shipping_country) && (is_numeric($default_shipping_country)))
				{
					$cart->BT['virtuemart_country_id'] = $default_shipping_country; 
				}
				else
				{
					// let's set a default country
					$vendor = $this->getVendorInfo($cart); 
					$cart->BT['virtuemart_country_id'] = $vendor['virtuemart_country_id']; 
				}
			}




			$type = 'BT'; 




			OPCUserFields::populateCart($cart, $type, true); 










			OPCloader::setRegType(); 



			$op_disable_shipto = OPCloader::getShiptoEnabled($cart); 
			if(!class_exists('VirtuemartModelUserfields')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'userfields.php');

			$corefields = array( 'name','username', 'email', 'password', 'password2' , 'agreed','language', 'tos');



			$userFields =& $cart->$fieldtype;

			$per_order_rendering = OPCconfig::get('per_order_rendering', array()); 


			

			if ($islogged)
			{
				if (!empty($per_order_rendering))
				{
					foreach ($per_order_rendering as $v=>$po)
					{
						if (isset($userFields['fields'][$po]))
						{
							
							
							$fc = $userFields['fields'][$po]['formcode']; 
							$x1 = stripos($fc, 'value="'); 
							if ($x1 !== false)
							{
								$x2 = stripos($fc, '"', $x1+7); 
								
								$nf = substr($fc, 0, $x1+7).substr($fc, $x2); 
								
								$userFields['fields'][$po]['formcode'] = $nf; 
								$userFields['fields'][$po]['value'] = ''; 
								
							}
						}
					}
					
					
				}
			}

			

			
			if ((isset($cart->BTaddress)) && (isset($cart->BTaddress['fields'])) && (isset($cart->BTaddress['fields']['virtuemart_country_id'])) && (!empty($cart->BTaddress['fields']['virtuemart_country_id']['value'])))
			{
				if (is_numeric($cart->BTaddress['fields']['virtuemart_country_id']['value']))
				$cart->BT['virtuemart_country_id'] = $cart->BTaddress['fields']['virtuemart_country_id']['value'];
				
			}


			// unset corefields
			$onlyf = array(); 
			if (empty($bt_fields_from))
			{
				$q = 'select `name` from `#__virtuemart_userfields` where `published`=1 and `registration` = 1'; 
				$db = JFactory::getDBO(); 
				$db->setQuery($q); 
				$onlyf2 = $db->loadAssocList(); 
				foreach ($onlyf2 as $k=>$v)
				{
					$onlyf[] = $v['name']; 
				}
			}
			if (!empty($userFields['fields']))
			foreach ($userFields['fields'] as $key=>$uf)   
			{

				// disable fields that are not marked for registration
				if (!empty($onlyf))
				{
					if (!in_array($uf['name'], $onlyf)) 
					{
						unset($userFields['fields'][$key]); 
						continue; 
					}
				}
				
				if (!isset($userFields['fields'][$key]['formcode'])) continue; 
				
				$userFields['fields'][$key]['formcode'] = str_replace('vm-chzn-select', '', $userFields['fields'][$key]['formcode']); 
				OPCloader::$fields_names[$key] = $userFields['fields'][$key]['title']; 
				if ($userFields['fields'][$key]['type'] == 'delimiter') 
				{
					
					if (!OPCrenderer::hasDel())
					{
						
						
						unset($userFields['fields'][$key]); 
						continue; 		  
						
					}
				}
				
				$no_login_in_template = OPCconfig::get('no_login_in_template', false); 
				$opc_email_in_bt = OPCconfig::get('opc_email_in_bt', false); 
				$custom_rendering_fields = OPCconfig::get('custom_rendering_fields', array()); 
				$op_no_display_name = OPCconfig::get('op_no_display_name', false); 
				$double_email = OPCconfig::get('double_email', false); 
				$opc_check_email = OPCconfig::get('opc_check_email', false); 
				$do_not_display_business = OPCconfig::get('do_not_display_business', false); 
				
				$currentUser = JFactory::getUser();
				$uid = $currentUser->get('id');
				if (!empty($uid)) 
				{ 

					$no_login_in_template = true; 
				}
				
				
				foreach ($corefields as $f)
				{
					if ($f == $uf['name'])
					{
						// will move the email to bt section
						if (empty($no_login_in_template) || ($unlg))
						{
							if ($f == 'email') 
							{
								if (empty($opc_email_in_bt))
								if (!$this->isNoLogin())
								unset($userFields['fields'][$key]);
							}
							else	
							unset($userFields['fields'][$key]);
							continue;
						}
						
						
						
						
					}
				}
				
				if (empty($custom_rendering_fields)) $custom_rendering_fields = array(); 
				if (!empty($custom_rendering_fields))
				if (in_array($uf['name'], $custom_rendering_fields))
				{
					unset($userFields['fields'][$key]); 
					continue; 
				}
				
				if ($key == 'name')	
				if (!empty($op_no_display_name))
				if (!empty($userFields['fields']['name']))
				{
					unset($userFields['fields']['name']);
				}
				 
				if (!empty($do_not_display_business))
				if ($islogged) {
				if ((!empty($business_fields)) || (!empty($business_fields2))) {
					if (!OPCUserFields::isBusiness($cart)) {
						
						
					   if (!empty($business_fields))
					   foreach ($business_fields as $bfn) {
					     unset($userFields['fields'][$bfn]);
						 OPCloader::opcDebug('Unsetting '.$bfn, 'business_fields');	   
					   }
					   if (!empty($business_fields2))
					   foreach ($business_fields2 as $bfn) {
					     unset($userFields['fields'][$bfn]);
						 OPCloader::opcDebug('Unsetting '.$bfn, 'business_fields');	   
					   }
					}
					
					}
				
				}

				
				
			} // end of for each
			
			
			$skipreorder = array('email'); 
			
			
			
			OPCUserFields::getUserFields($userFields, $this, $cart, array(), array(), $skipreorder); 
			
			
			

			
			
			// logic reversed, if email is not in BT, remove it
			if (!((!empty($opc_email_in_bt) || (($this->isNoLogin()))) && (!empty($double_email))))
			{
				unset($userFields['fields']['email2']);
				// email is in BT, let's check for double mail

			}
			
			
			$skipreorder = array(); 
			if ((!empty($opc_email_in_bt) || (($this->isNoLogin()))))
			{
				$skipreorder[] = 'email'; 
				if (!empty($opc_check_email))
				{
					
					
					if ((!OPCloader::logged($cart)) && (empty($uid)))
					if (!empty($userFields['fields']['email']))
					{
						
						$un = $userFields['fields']['email']['formcode']; 
						if (stripos($un, 'id="email_already_exists"')===false)
						{
							
							$un = str_replace('id=', ' onblur="javascript: Onepage.email_check(this);" id=', $un);
							
							
							$un .=  '<span class="email_already_exist" style="display: none; position: relative; color: red; font-size: 10px; background: none; border: none; padding: 0; margin: 0;" id="email_already_exists">';
							$un .= OPCLang::sprintf('COM_ONEPAGE_EMAIL_ALREADY_EXISTS', OPCLang::_('COM_VIRTUEMART_USER_FORM_EMAIL')); 
							$un .= '</span>'; 
							$userFields['fields']['email']['formcode'] = $un; 
						}
					}
				}
			}
			
			
			
			
			
			
			OPCUserFields::reorderFields($userFields, $skipreorder); 

			
			
			


			$this->_model = OPCmini::getModel('user'); 
			$layout = 'default';

			$hidden = array(); 
			$hidden_html = ''; 
			$render_as_hidden = OPCconfig::get('render_as_hidden', array()); 
			
			if (!empty($userFields['fields']))
			foreach ($userFields['fields'] as $key=>$val)
			{
				if (in_array($val['name'], $render_as_hidden)) {
				   $val['hidden'] = true; 
				}
				
				if (!empty($val['hidden']))
				{
					$hidden[] = $val; 
					$hidden_html .= $val['formcode']; 
					unset($userFields['fields'][$key]); 
				}
			} 
			
			OPCUserFields::addSpecialRequired($userFields); 
			OPCUserFields::addDelimiters($userFields); 
			OPCUserFields::addListenersToFields($userFields); 
			
			
			
			$is_logged = OPCloader::logged($cart); 
			
			$vars = array(
			'rowFields' => $userFields, 
			'rowFields_bt' => $userFields, 
			'cart'=> $cart, 
			'is_logged'=> $is_logged);


			$html = $this->fetch($this, 'list_user_fields.tpl', $vars); 




			$hidden_html = str_replace('"required"', '""', $hidden_html); 
			$hidden_html = '<div style="display:none;">'.$hidden_html.'</div>'; 
			$html .= $hidden_html; 

			//$html = $this->addListeners($html);
			
			if (empty($custom_rendering_fields)) $custom_rendering_fields = array(); 
			if (in_array('virtuemart_country_id', $custom_rendering_fields)) $html .= '<input type="hidden" id="virtuemart_country_id" name="virtuemart_country_id" value="'.$default_shipping_country.'" />'; 
			if ((in_array('virtuemart_state_id', $custom_rendering_fields)))
			$html .= '<input type="hidden" id="virtuemart_state_id" name="virtuemart_state_id" value="0" />';   



			return $html;
		}
	}

	function reorderFields(&$userFields)
	{

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
		return OPCUserFields::reorderFields($userFields); 


	}

	function insertAfter(&$arr, $field, $ins, $newkey, $before=false)
	{
		//deprecated 
	}

	function getJavascript(&$ref, $isexpress=false, $action_url='index.php', $option='com_virtuemart', $task='checkout')
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'javascript.php'); 
		return OPCJavascript::getJavascript($ref, $this, $isexpress, $action_url, $option, $task); 
	}   

	public static function getUserFields($address_type='BT', &$cart=null)
	{

		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		$umodel = OPCmini::getModel('user'); 
		
		$virtuemart_userinfo_id = 0; 
		$currentUser = JFactory::getUser();
		$uid = $currentUser->get('id');
		$new = false; 
		
		if ($uid != 0)
		{
			$userDetails = $umodel->getUser();
			$virtuemart_userinfo_id = $umodel->getBTuserinfo_id();
		}
		else $virtuemart_userinfo_id = 0; 
		$layoutName = 'edit'; 
		$task = JRequest::getVar('task'); 
		$userFields = null;
		$view = JRequest::getVar('view', ''); 
		if ((strpos($task, 'cart') || strpos($task, 'checkout') || ($view=='cart')) && empty($virtuemart_userinfo_id)) {

			//New Address is filled here with the data of the cart (we are in the cart)
			if (empty($cart))
			{
				if (!class_exists('VirtueMartCart'))
				require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
				$cart = VirtueMartCart::getCart();
			}
			
			$fieldtype = $address_type . 'address';
			
			{
				
				if (!empty($cart->$address_type))
				if (is_array($cart->$address_type))
				if (count($cart->$address_type)>2) $new = false; 
				
				$saved = OPCloader::copyAddress($cart->$address_type); 
				
				
				require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'userfields.php'); 
				OPCUserFields::populateCart($cart, $address_type, true); 
				
				
				if (!empty($cart->$address_type))
				OPCloader::restoreDataInCart($cart->$address_type, $saved); 
			}
			
			
			if (isset($cart->$fieldtype))
			$userFields = $cart->$fieldtype;
			else 
			$userFields = array(); 

			$task = JRequest::getWord('task', '');
		} else {
			$userFields = $umodel->getUserInfoInUserFields($layoutName, $address_type, $virtuemart_userinfo_id);
			$userFields = $userFields[$virtuemart_userinfo_id];
			$task = 'editaddressST';
		}
		return $userFields;
	}

	public static function getCurrency(&$cart)
	{
		
		if (empty($cart))
		{
			if (!class_exists('VirtueMartCart'))
			require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'cart.php');
			$cart = VirtuemartCart::getCart(); 
		}
		
		static $curr = 0; 
		if (!empty($curr)) return $curr;
		if (!empty($cart))
		$vendorId = $cart->vendorId; 
		else $vendorId = 1; 
		
		if (empty($vendorId)) $vendorId = 1; 
		
		$db = JFactory::getDBO();
		$q  = 'SELECT `vendor_accepted_currencies`, `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id`='.$vendorId;
		$db->setQuery($q);
		$vendor_currency = $db->loadAssoc();
		$mainframe = Jfactory::getApplication();
		$virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id', $cart->pricesCurrency) );
		if (empty($virtuemart_currency_id))
		{
			$virtuemart_currency_id = $vendor_currency['vendor_currency']; 
		}	  

		$curr = $virtuemart_currency_id; 
		return $virtuemart_currency_id; 
	}
	function getContinueLink(&$ref)
	{


		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 

		$no_continue_link = OPCconfig::get('no_continue_link', false); 


		if (!empty($no_continue_link)) return ""; 
		$cl = ''; 
		if (isset($_SERVER['HTTP_REFERER'])) {
		 $reff = $_SERVER['HTTP_REFERER']; 
		}
		else
		{
			$reff = ''; 
		}
		if (!empty($reff))
		{
			
			$reff = OPCloader::slash($reff); 
			if (stripos($reff, 'script')===false)
			{
				
				$cl = $reff; 
			}
		}
		if (empty($cl))
		{
			$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId();
			$categoryLink = '';
			if ($virtuemart_category_id) {
				$categoryLink = '&virtuemart_category_id=' . $virtuemart_category_id;
			}
			
			$cl = JRoute::_('index.php?option=com_virtuemart&view=category' . $categoryLink);
		}
		$session = JFactory::getSession();
		if (!empty($cl)) 
		{
			$cl2 = $session->get('lastcontiuelink', '', 'opc');
			if (!empty($cl2)) return $cl2; 
			
			$session->set('lastcontiuelink', $cl, 'opc');
			return $cl; 
		}
		$cl = $session->get('lastcontiuelink', '', 'opc');
		return $cl; 
	}

	public static function slash($string, $insingle = true)
	{
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		return OPCmini::slash($string, $insingle); 
	}


	function getIntroArticle(&$ref)
	{
		
		
		$add = JRequest::getVar('opc_adc'); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		$op_articleid = OPCconfig::getValue('opc_config', 'op_articleid', 0, 0, true); 
		
		if (!empty($add))
		{
			$adc_op_articleid = OPCconfig::getValue('opc_config', 'adc_op_articleid', 0, 0, true); 
			
			if (!empty($adc_op_articleid)) $op_articleid = $adc_op_articleid; 
			
		}
		
		if (empty($op_articleid))   
		return "";
		if (!is_numeric($op_articleid)) return "";

		if (is_numeric($op_articleid))
		{
			$article = JTable::getInstance("content");
			
			$article->load($op_articleid);
			
			
			$parametar = new OPCParameter($article->attribs);
			
			
			$x = $parametar->get('show_title', false); 
			$x2 = $parametar->get('title_show', false); 
			
			$intro = $article->get('introtext'); 
			$full = $article->get("fulltext"); 
			JPluginHelper::importPlugin('content'); 
			$dispatcher = JDispatcher::getInstance(); 
			$mainframe = JFactory::getApplication(); 
			$params = $mainframe->getParams('com_content'); 
			
			if ($x || $x2)
			{
				
				

				$title = '<div class="componentheading'.$params->get('pageclass_sfx').'">'.$article->get('title').'</div>';
				
			}
			else $title = ''; 
			if (empty($article->text))
			$article->text = $title.$intro.$full; 
			
			
			
			$results = $dispatcher->trigger('onPrepareContent', array( &$article, &$params, 0)); 
			$results = $dispatcher->trigger('onContentPrepare', array( 'text', &$article, &$params, 0)); 
			
			return $article->get('text');
			
			
		}
		return ""; 

	}

	function getSubscriptionCheckbox(&$ref)
	{
		
		
		$opc_acymailing_checkbox = OPCconfig::get('opc_acymailing_checkbox', false); 
		
		if (empty($opc_acymailing_checkbox)) return ''; 
		
		$default_acy_checked = OPCconfig::get('default_acy_checked', false); 
		
		$ita = $acy = ''; 
		
		$session = JFactory::getSession(); 

		$saved_f = $session->get('opc_fields', array(), 'opc'); 
		if (empty($saved_f)) $saved_fields = array(); 
		else
		$saved_fields = @json_decode($saved_f, true); 
		
		if (!empty($opc_acymailing_checkbox))
		$acy = $this->fetch($ref, 'acymailing_checkbox', array(), ''); 
		
		$acysub = false; 
		
		if (isset($ref->cart) && (!empty($ref->cart->BT)))
		{
			if (!empty($ref->cart->BT['email']))
			{
				$email = $ref->cart->BT['email']; 
			}
		}
		if (empty($email))
		{
			$user = JFactory::getUser(); 
			$id = $user->id; 
			$email = $user->get('email'); 
		}
		
		
		$append = ''; 
		if (!empty($email))
		{
			
			$dispatcher = JDispatcher::getInstance(); 
			JPluginHelper::importPlugin('system'); 
			$is_reg = false; 
			$dispatcher->trigger('plgMailchimpCheckRegistered', array( $email, &$is_reg, &$append)); 
			
			$acysub = $is_reg; 
			
		}
		if (empty($is_reg))
		{
			if (!empty($default_acy_checked))
			$acysub = $default_acy_checked; 
			
			
			if ( ((!empty($saved_fields)) && (!empty($saved_fields['acysub']))))
			$acysub = true; 
			
			if ((!empty($saved_fields)) && isset($saved_fields['acysub']) && ($saved_fields['acysub']===false))
			$acysub = false; 
		}
		if ($acysub === true)
		{
			$acy = str_replace('type="checkbox"', ' checked="checked" type="checkbox"', $acy); 
		}
		
		$acy .= $append; 
		
		

		
		
		
		// default
		return $acy; 
	}

	function getItalianCheckbox(&$ref)
	{
		
		
		$opc_italian_checkbox = OPCconfig::get('opc_italian_checkbox', false); 
		if (empty($opc_italian_checkbox)) return ''; 
		
		$default_italian_checked = OPCconfig::get('default_italian_checked'); 
		
		$ita =  ''; 
		
		$session = JFactory::getSession(); 

		$saved_f = $session->get('opc_fields', array(), 'opc'); 
		if (empty($saved_f)) $saved_fields = array(); 
		else
		$saved_fields = @json_decode($saved_f, true); 
		
		
		
		$agree_checked = false; 
		if ((!empty($saved_fields))  && (!empty($saved_fields['italianagreed'])))
		{
			$agree_checked = true; 
		}
		else
		{
			if ((!empty($saved_fields)) && isset($saved_fields['italianagreed']) && ($saved_fields['italianagreed'] === false))
			{
				$agree_checked = false; 
			}
			else
			{
				if (!empty($default_italian_checked)) $agree_checked = true; 
			}
		}
		
		$vars = array('agree_checked' => $agree_checked); 
		
		if (!empty($opc_italian_checkbox))
		$ita = $this->fetch($ref, 'italian_checkbox', $vars, ''); 
		// default
		return $ita; 
	}

	function getTos(&$ref)
	{


		$tos_scrollable = OPCconfig::get('tos_scrollable', true); 


		$link = $this->getTosLink($ref); 

		if (!empty($link))  
		if (!empty($tos_scrollable))
		{
			$start = '<iframe src="'.$link.'" class="tos_iframe" >'; 
			$end = '</iframe>'; 
			return $start.$end; 
		}


		$start = ''; 
		$end = ''; 

		if (empty($ref->cart->vendor->vendor_terms_of_service))
		{
			require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
			$vendorModel = OPCmini::getModel('vendor'); 
			$vendor = $vendorModel->getVendor(); 
			$ref->cart->vendor->vendor_terms_of_service = $vendor->vendor_terms_of_service; 

		}


		$tos_config = OPCconfig::getValue('opc_config', 'tos_config', 0, 0, true); 



		if (empty($tos_config))   
		return $ref->cart->vendor->vendor_terms_of_service;  
		if (!is_numeric($tos_config)) return $start.$ref->cart->vendor->vendor_terms_of_service.$end;  

		if (is_numeric($tos_config))
		{
			$article = JTable::getInstance("content");
			
			$article->load($tos_config);
			
			$intro = $article->get('introtext'); 
			$full = $article->get("fulltext"); // and/or fulltext
			JPluginHelper::importPlugin('content'); 
			$dispatcher = JDispatcher::getInstance(); 
			$mainframe = JFactory::getApplication(); 
			$params = $mainframe->getParams('com_content'); 
			
			$title = '<div class="componentheading'.$params->get('pageclass_sfx').'">'.$article->get('title').'</div>';
			if (empty($article->text))
			$article->text = $title.$intro.$full; 
			
			
			
			$results = $dispatcher->trigger('onPrepareContent', array( &$article, &$params, 0)); 
			$results = $dispatcher->trigger('onContentPrepare', array( 'text', &$article, &$params, 0)); 
			
			return $start.$article->get('text').$end;
			
			
		}
		return ""; 
	}

	function fetch(&$ref, $template, $vars, $new='')
	{

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		$renderer = OPCrenderer::getInstance(); 
		return $renderer->fetch($ref, $template, $vars, $new); 
	}

	function getCoupon(&$obj)
	{
		if (!VmConfig::get('coupons_enable')) 
		{
			return ""; 
		}
		
		
		
		$coupon_text = $obj->cart->couponCode ? OPCLang::_('COM_VIRTUEMART_COUPON_CODE_CHANGE') : OPCLang::_('COM_VIRTUEMART_COUPON_CODE_ENTER');



		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		$renderer = OPCrenderer::getInstance(); 
		$renderer->assignRef('coupon_text', $coupon_text);
		return $this->fetch($obj, 'couponField.tpl', array('cart'=>$obj->cart, 'coupon_text'=>$coupon_text), 'coupon'); 

	}

	public function getJSValidator($ref)
	{
		$html = 'javascript:return Onepage.validateFormOnePage(event, this, true);" autocomplete="off'; 
		
		return $html;
	}
	function renderOPC()
	{
		
	}
	function op_image_info_array($image, $args="", $resize=1, $path_appendix='product', $thumb_width=0, $thumb_height=0)
	{ 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
		return OPCimage::op_image_tag($image, $args, $resize, $path_appendix, $thumb_width, $thumb_height, true );
	}
	function path2url($path)
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
		return OPCimage::path2url($path); 
	}
	function op_image_tag($image, $args="", $resize=1, $path_appendix='product', $thumb_width=0, $thumb_height=0, $retA = false )
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
		return OPCimage::op_image_tag($image, $args, $resize, $path_appendix, $thumb_width, $thumb_height, $retA );
	}
	public function resizeImg($orig, $new,  $new_width, $new_height, $ow, $oh)
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
		return OPCimage::resizeImg($orig, $new,  $new_width, $new_height, $ow, $oh); 
	}
	public function op_show_image(&$image, $extra, $width, $height, $type)
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'image.php'); 
		return OPCimage::op_show_image($image, $extra, $width, $height, $type);
	}
	
	

	
	// for backward compatibility



	static function tableExists($table)
	{
		$db = JFactory::getDBO();
		$prefix = $db->getPrefix();
		$table = str_replace('#__', '', $table); 
		$table = str_replace($prefix, '', $table); 

		$q = "SHOW TABLES LIKE '".$db->getPrefix().$table."'";
		$db->setQuery($q);
		$r = $db->loadResult();
		if (!empty($r)) return true;
		return false;
	}

	public static function checkPurchaseValue($cart) {
		$s = $cart->virtuemart_shipmentmethod_id; 
		$p = $cart->virtuemart_paymentmethod_id; 
		
		$cart->virtuemart_shipmentmethod_id = 0; 
		$cart->virtuemart_paymentmethod_id = 0; 
		
		$ret = ''; 
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 	
		$vendor = OPCmini::getModel('vendor');
		if (empty($vendor)) return; 
		$vendor->setId($cart->vendorId);
		$store = $vendor->getVendor();
		if ($store->vendor_min_pov > 0) {
			$vm2015 = true; 
			$prices = OPCloader::getCheckoutPrices($cart, false, $vm2015, null);
			
			if (!empty($prices['couponValue']) || (!empty($prices['salesPriceCoupon'])))
			$ret = ''; 
			else
			if ($prices['salesPrice'] < $store->vendor_min_pov) {
				if (!class_exists('CurrencyDisplay'))
				require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'currencydisplay.php');
				$currency = CurrencyDisplay::getInstance();
				$ret = JText::sprintf('COM_VIRTUEMART_CART_MIN_PURCHASE', $currency->priceDisplay($store->vendor_min_pov));
			}
		}
		
		$cart->virtuemart_shipmentmethod_id = $s; 
		$cart->virtuemart_paymentmethod_id = $p; 
		
		return $ret;
	}


    //$fileuploadpath = array(filepath=>'', mime=>'')
	public static function fetchUrl($url, $XPost='', $username='', $password='', $compress=false, $fileuploadpath=array())
	{
		$msg = 'OPC Notice: Curl to URL: '.$url; 
		if (!is_array($XPost)) $msg .= ' data length '.strlen($XPost).' bytes';
		error_log( $msg ); 
		$starttime = microtime(true); 
		
		if (!function_exists('curl_init'))
		{
			return file_get_contents($url); 
		}
		
		
		
		$ch = curl_init(); 
		
		//	 curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
		curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		
		 if ((!empty($username)) && (!empty($password)))
	     curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
		
		if (defined('CURLOPT_FOLLOWLOCATION'))
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 8000); // times out after 4s
		
		if (!empty($XPost))
		curl_setopt($ch, CURLOPT_POST, 1); 
		else
		curl_setopt($ch, CURLOPT_POST, 0); 
	
		
		if ((!empty($fileuploadpath)) && (!empty($XPost)) && (is_array($XPost))) {
			if (function_exists('CurlFile')) {
			  $XPost['xml_post_name'] = new CurlFile($fileuploadpath['filepath'], $fileuploadpath['mime'], 'xml_post_name');
			}
			else {
				if (function_exists('curl_file_create')) {
				$XPost['xml_post_name'] = curl_file_create($fileuploadpath['filepath'], $fileuploadpath['mime'], 'xml_post_name');
				}
			}
		}
		
		
		if (!empty($XPost))
		curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost); // add POST fields
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.34 Safari/537.36');
		$cookie = JPATH_SITE.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'curl_cookies_'.uniqid(rand()).'.txt';
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		
	

		if (!empty($compress)) {
			 curl_setopt($ch, CURLOPT_ENCODING , "gzip");
		}
	
		//curl_setopt($ch, CURLOPT_ENCODING , "gzip");
		
		 
		 
		 
		
		$result = curl_exec($ch);   
		
		
		$endtime = microtime(true); 
		$dur = $endtime - $starttime; 
		
		
		error_log('OPC Curl took '.number_format($dur, 4, '.', ' ').' s'); 
		
		if (file_exists($cookie)) {
			
		  @unlink($cookie); 
		}
		
		if ( curl_errno($ch) ) {    

		
			$err = 'ERROR -> ' . curl_errno($ch) . ': ' . curl_error($ch); 
			OPCloader::opcDebug($err, 'CURL');
			
			
			
			@curl_close($ch);
			return false; 
		} else {
			
			
			
			$returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
			OPCloader::opcDebug($url.' -> '.$returnCode, 'CURL');
			switch($returnCode){
			case 404:
			
			
				@curl_close($ch);
				return false; 
				break;
			case 200:
				break;
			default:
			
			
				@curl_close($ch);
				return false; 
				break;
			}
		}
		
		@curl_close($ch);
		

		return $result;   
		
		

	}
	


}
