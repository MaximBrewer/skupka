<?php 
defined('_JEXEC') or die('Restricted access');

/**
 * Overrided VmPlugin class for the OPC ajax and checkout
 * 
 * This class was overrided due to few serious bugs in the orginal release and to be able to add additional functionality to it 
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 *
 ORIGINAL LICENSE AND COPYRIGHT
 * abstract class for payment plugins
 *
 * @package	VirtueMart
 * @subpackage Plugins
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: vmplugin.php 4599 2011-11-02 18:29:04Z alatak $
 */
// Load the helper functions that are needed by all plugins




// Get the plugin library
jimport('joomla.plugin.plugin');

abstract class vmPlugin extends JPlugin {

	// var Must be overriden in every plugin file by adding this code to the constructor:
	// $this->_name = basename(__FILE, '.php');
	// just as note: protected can be accessed only within the class itself and by inherited and parent classes

	//This is normal name of the plugin family, custom, payment
	protected $_psType = 0;
	
	//Id of the joomla table where the plugins are registered
	protected $_jid = 0;

	protected $_vmpItable = 0;
	//the name of the table to store plugin internal data, like payment logs
	protected $_tablename = 0;

	protected $_tableId = 'id';
	//Name of the primary key of this table, for exampel virtuemart_calc_id or virtuemart_order_id
	protected $_tablepkey = 0;

	protected $_vmpCtableAll = array();
	protected $_vmpCtable = 0;
	//the name of the table which holds the configuration like paymentmethods, shipmentmethods, customs
	protected $_configTable = 0;
	protected $_configTableFileName = 0;
	protected $_configTableClassName = 0;
	protected $_xParams = 0;
	protected $_varsToPushParam = array();
	//id field of the config table
	protected $_idName = 0;
	//Name of the field in the configtable, which holds the parameters of the pluginmethod
	protected $_configTableFieldName = 0;

	protected $_debug = false;
	protected $_loggable = false;
	protected $cost_per_transaction  = 0; 
	protected $cost_percent_total   = 0; 
	protected $min_amount = 0; 
	protected $max_amount = 0; 
	protected $_cryptedFields = false;
	
	
	// OPC addons: 
	public static $iCount; 
	public static $detached; 
	static $qCache; 
	public static $ccount; 
	//static $payment_logos = null;
	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function __construct(& $subject, $config) {
		
			
	    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
		
		

		parent::__construct($subject, $config);
		
		
		
		if (!class_exists('ShopFunctions'))
	    require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'shopfunctions.php');
		
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cache.php'); 
		
		$this->_psType = substr($this->_type, 2);

		$lang = JFactory::getLanguage();
		$filename = 'plg_' . $this->_type . '_' . $this->_name;
		
		if (method_exists('VmConfig', 'loadJLang'))
		{
		  //VmConfig::loadJLang($filename);
		  $this->loadJLangThis($filename);
		}
		else
		{
		if(VmConfig::get('enableEnglish', 1)){
		    $lang->load($filename, JPATH_ADMINISTRATOR, 'en-GB', true);
		}
		    $lang->load($filename, JPATH_ADMINISTRATOR, $lang->getDefault(), true);
		$lang->load($filename, JPATH_ADMINISTRATOR, null, true);
		}
/*
		$knownLanguages=$lang->getKnownLanguages();
		foreach($knownLanguages as $key => $knownLanguage) {
			$lang->load ($filename, JPATH_ADMINISTRATOR, $key, TRUE);
		}
		*/
		if (!OPCJ3)
		if (!class_exists ('JParameter')) {
			require(JPATH_VM_LIBRARIES . DIRECTORY_SEPARATOR . 'joomla' . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'parameter.php');
		}

		$this->_tablename = '#__virtuemart_' . $this->_psType . '_plg_' . $this->_name;
		$this->_tableChecked = FALSE;
		
		
		
		$this->_xmlFile	= JPath::clean( JPATH_PLUGINS .'/'. $this->_type .'/'.  $this->_name . '/' . $this->_name . '.xml');
	}
	
	
	public function setConvertDecimal($toConvert) {
		$this->_toConvertDec = $toConvert;
	}
	public function convertDec(&$data){

		if($this->_toConvertDec){
			foreach($this->_toConvertDec as $f){
				if(!empty($data[$f])){
					$data[$f] = str_replace(array(',',' '),array('.',''),$data[$f]);
				}
			}
		}
	}
	
	public function loadJLangThis($fname,$type=0,$name=0){
		if(empty($type)) $type = $this->_type;
		if(empty($name)) $name = $this->_name;
		self::loadJLang($fname,$type,$name);
	}
	
	/*
	public function __destruct()
	{
	   unset($this->_xParams); 
	   unset($this->_varsToPushParam); 
	   unset($this->_varsToPushParam); 
	   unset($this->methods); 
	   unset($this->_cryptedFields); 
	   unset($this->customerData); 
	   unset($this->_vmpCtable); 
	   unset(vmPlugin::$xmlDefaults); 
	   unset($this->_vmpCtableAll); 
	   unset($this->_vmpItable); 
	   unset($this->tableFields); 
	   unset(vmPlugin::$xmlCache); 
	   unset(VmPlugin::$iCount); 
	   unset(vmPlugin::$qCache); 
	   
	   
	}
	*/
	
	public function plgVmOnCheckoutCheckDataShipmentOPC(&$cart, &$msg)
	{
		
		
		
		if ($this->_psType != 'shipment') return null; 
		if (empty($cart->virtuemart_shipmentmethod_id)) return null; 
		
		
	    if (!($method = $this->getVmPluginMethod ($cart->virtuemart_shipmentmethod_id))) {
			return null; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement ($method->shipment_element)) {
			return null;
		}

		
		
		if (method_exists($this, 'plgVmOnCheckoutCheckDataShipment'))
		{
			$ret = $this->plgVmOnCheckoutCheckDataShipment($cart); 
			
			
			
			if ($ret === false) 
			{
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
				
				$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (!empty($debug_plugins)) {
				
				 	$classn = get_class($this); 
					if (!empty($classn))
					{
						
					$nmsg = ' Element: '.$classn; 
					if (!empty($msg)) $nmsg .= ', '.$msg; 
					
					$msg = $nmsg; 
					}
					
				JFactory::getApplication()->enqueueMessage('opc debug: '.$msg); 
			}
				return false; 
			}
			
			if ($ret === true)
			{
				return true; 
				// the classical validation is NOT ENOUGH
				
				
			}
			
		}
		
		// now, if the plgVmOnCheckoutCheckDataShipment function does not exists, call checkConditions
		$ret = $this->plgVmOnSelectCheckShipment($cart); 
		if ($ret === true)
		{
				
				
				
				
				if (!empty($method))
				{
					$ret = $this->checkConditions($cart, $method, $cart->pricesUnformatted); 
					
				

					
					if ($ret === false)
					{
						
						require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
				
				$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (!empty($debug_plugins)) {
				
						
						$classn = get_class($this); 
					if (!empty($classn))
					{
						
					$nmsg = ' Element: '.$classn; 
					if (!empty($msg)) $nmsg .= ', '.$msg; 
					
					$msg = $nmsg; 
					}
						
						JFactory::getApplication()->enqueueMessage('opc debug: '.$msg); 
			}
						return false; 
					}
					// default...
					return true; 
				}
				return null; 
		}
		return null;
		
	}
	
	// by default VM does not check for conditions when using plgVmOnSelectCheckShipment, therefore we added a wrapper in opc: 
	public function plgVmOnSelectCheckShipmentOPC(&$cart, &$msg)
	{
		if ($this->_psType != 'shipment') return null; 
		if (empty($cart->virtuemart_shipmentmethod_id)) return null; 
		if (method_exists($this, 'plgVmOnSelectCheckShipment'))
		{
			$ret = $this->plgVmOnSelectCheckShipment($cart); 
			
		if ($ret === false)
		{
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
				
				$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (!empty($debug_plugins)) {
			
			$classn = get_class($this); 
			if (!empty($classn))
			$msg = ' Element: '.$classn.', '.$msg; 
			}

		}
	
			
			if ($ret === false) return false; 
			if ($ret === true) return true; 
			
		}
		return null; 
	}
	
	
	function renderCreditCardList ($creditCards, $selected_cc_type, $paymentmethod_id, $multiple = FALSE, $attrs = '')
	{
		JFactory::getLanguage()->load('plg_vmpayment_authorizenet', JPATH_ADMINISTRATOR); 
		
		JFactory::getLanguage()->load('plg_vmpayment_alatak_creditcard', JPATH_ADMINISTRATOR); 
		
		$idA = $id = 'cc_type_' . $paymentmethod_id;
		
		if (!is_array($creditCards)) {
			$creditCards = (array)$creditCards;
		}
		foreach ($creditCards as $creditCard) {
			$key = 'VMPAYMENT_AUTHORIZENET_' . strtoupper($creditCard); 
			$text = JText::_($key); 
			if ($text === $key)
			{
				$key = 'VMPAYMENT_ALATAK_CREDITCARD_' . strtoupper($creditCard); 
				$text = JText::_($key); 
				
				if ($key === $text)
				{
					$cc = strtoupper($creditCard); 
					if ($cc == 'AMEX') $cc = 'AMERICANEXPRESS'; 
					else if($cc == 'DINERS_CLUB_CARTE_BLANCHE') $cc='DINERSCLUB_CARTEBLANCHE'; 
					else if ($cc == 'DINERS_CLUB_INTERNATIONAL') $cc = 'DINERSCLUB_INTERNATIONAL'; 
					
					$key = 'VMPAYMENT_ALATAK_CREDITCARD_' . $cc; 
					$text = JText::_($key); 
				}
			}
			$options[] = JHTML::_('select.option', $creditCard, $text );
		}
		if ($multiple) {
			$attrs = 'multiple="multiple"';
			$idA .= '[]';
		}
		return JHTML::_('select.genericlist', $options, $idA, $attrs, 'value', 'text', $selected_cc_type);
	}
	
	static public function loadJLang($fname,$type=0,$name=0){

		$jlang =JFactory::getLanguage();
		$tag = $jlang->getTag();
		
		if (empty($type)) return; 
		if (empty($name)) return; 
		/*
		if(empty($type)) $type = $this->_type;
		if(empty($name)) $name = $this->_name;
		*/
		$path = $basePath = JPATH_ROOT .DIRECTORY_SEPARATOR. 'plugins' .DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$name;

		if(VmConfig::get('enableEnglish', true) and $tag!='en-GB'){
			$testpath = $basePath.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'en-GB'.DIRECTORY_SEPARATOR.'en-GB.'.$fname.'.ini';
			if(!file_exists($testpath)){
				$epath = JPATH_ADMINISTRATOR;
			} else {
				$epath = $path;
			}
			$jlang->load($fname, $epath, 'en-GB');
		}

		$testpath = $basePath.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$tag.DIRECTORY_SEPARATOR.$tag.'.'.$fname.'.ini';
		if(!file_exists($testpath)){
			$path = JPATH_ADMINISTRATOR;
		}

		$jlang->load($fname, $path,$tag,true);
	}
	
	function setCryptedFields($fieldNames){
		$this->_cryptedFields = $fieldNames;
	}
	
	 function setPluginLoggable($set=TRUE){
		$this->_loggable = $set;
	 }
/**
	 * @return array
	 */
	function getTableSQLFields() {

		return false;
	}
	
	
	function plgVmDetachVmPlugins(&$p=null) {
		if (empty($p)) {
		 if (empty(vmPlugin::$detached)) vmPlugin::$detached = array(); 
	     vmPlugin::$detached[] =& $this; 
		 
		 if (empty($p)) $p = array(); 
	     $p[] =& $this; 
		 
		}
		else {
			if (empty($p)) $p = array(); 
	        $p[] =& $this; 
		}
	}
	
	function plgVmConfirmedOrderOPC($type, $cart, $order)
	{
	  $ret = null; 
		
	  if ($this->_psType != $type) return null; 
	   try {
	    $ret = $this->plgVmConfirmedOrder($cart, $order); 
	   }
	   catch (Exception $e) {
		$msg = (string)$e->getMessage(); 
	   JFactory::getApplication()->enqueueMessage($msg, 'error'); 
	  }
	   if (empty(vmPlugin::$detached)) vmPlugin::$detached = array(); 
	   vmPlugin::$detached[] =& $this; 
	  /*
	  
	  
	  // we cannot detach whole class, because it cannot print shipping name then: 
	  
	 
	  $mmethods = array_diff(get_class_methods($this), get_class_methods('JPlugin'));
	  
	  foreach ($mmethods as $key)
	  {
		  if ($key === 'plgVmConfirmedOrder') unset($mmethods[$key]); 
		  else 
		  if (substr($key, 0, 2) === '__') unset($mmethods[$key]); 
		  else
		  {
		  $event = $key; 
		  $m = $this->$key; 
		  $method = array('event' => $event, 'handler' => $m);
		  $dispatcher->register($key, $this); 
		  }
	  }
	  */
	 
	  
	  
	  
	  return $ret; 
	  
	}
	
	
	function plgVmConfirmedOrderOPCExcept($types, &$cart, &$order)
	{
	  if (in_array($this->_psType, $types)) return null; 
	  
	   if (empty(vmPlugin::$detached)) vmPlugin::$detached = array(); 
	   vmPlugin::$detached[] =& $this; 
	  
	  
	  
	  
	  try {
	    $ret = $this->plgVmConfirmedOrder($cart, $order); 
	  }
	  catch (Exception $e) {
	   $msg = (string)$e->getMessage(); 
	   JFactory::getApplication()->enqueueMessage($msg, 'error'); 
	  }
	  return $ret; 
	}
	

function getOwnUrl(){

		if(JVM_VERSION!=1){
			$url = '/plugins/'.$this->_type.'/'.$this->_name;
		} else{
			$url = '/plugins/'.$this->_type;
		}
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
			$url = '/plugins/'.$this->_type.'/'.$this->_name;
		}
		
		return $url;
	}

	public function getPaymentMethodsOPC(&$cart, &$payments)
	{
	
	
	  if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
 	  if ($this->_psType != 'payment') return; 
	 
		
		if ($this->_name == 'klarna')
		{
		  $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		  if (isset($address['virtuemart_country_id']))
		  $country = $address['virtuemart_country_id']; 
		  if (empty($country) && (!empty($cart->BT['virtuemart_country_id']))) $country = $cart->BT['virtuemart_country_id']; 
		   if (!class_exists('ShopFunctions'))
		  require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'shopfunctions.php');
		  
		  if (empty($country)) return; 
		  
		  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		  
		  
		  $countryCode = OPCmini::getCountryByID ($country, 'country_2_code');
		  $avai = array('SE', 'DE', 'NL', 'NO', 'DK', 'FI'); 
		  $countryCode = strtoupper($countryCode); 
		  if (!in_array($countryCode, $avai)) 
		  {
		   return; 
		  }
		  
		}
		
		 $nmethods = $this->getPluginMethodsOPC ($cart->vendorId);
		if (empty($this->methods))
	    {
		   
			return;
		}
		
		if (!empty($this->methods))
		foreach ($this->methods as &$method)
		{
		  $method->opcref =& $this; 
		  $payments[] =& $method;   
		}
		
			
	}
	
	
	
	public function getShipmentMethodsOPC($vendor_id=1, &$payments)
	{
	 
 	  if ($this->_psType != 'shipment') return; 
	 
		
		
		 $nmethods = $this->getPluginMethodsOPC ($vendor_id);
		if (empty($this->methods))
	    {
			return;
		}
		
		if (!empty($this->methods))
		foreach ($this->methods as &$method)
		{
		  $method->opcref =& $this; 
		  $payments[] =& $method;   
		}
		
			
	}
	
	public function checkAdvancedConditionsOPC($type='category', $config=array()) {
		if (empty($config)) return true; 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'filters.php');
		return OPCfilters::checkAdvancedConditionsOPC($type, $config); 
	}
	
	public function plgVmDisplayListFEShipmentOPCNocache(&$cart, $selected = 0, &$htmlIn)
	{
	  if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
	   if ($this->_psType != 'shipment') return; 
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
	  
	  if ($this->getPluginMethodsOPC($cart->vendorId) === 0) {
	  
                return FALSE;            
        }  
	   
	   $return = array(); 
	   $filter = array(); 
	   
	   if (isset($this->methods))
	   {
	   foreach ($this->methods as $key => $method)
	   {
	   if (isset($method->virtuemart_shipmentmethod_id))
	   {
	     $vm_id = (int)$method->virtuemart_shipmentmethod_id; 
		 if (empty($vm_id)) continue; 
		 $allc = count($this->methods); 
		 $default = array(); 
		 $config = OPCconfig::getValue('opcfilters', 'catfilter1', $vm_id, $default, false, false);
		 if (!is_array($config)) $config = (array)$config; 
		 if (!empty($config)) {
			 
			 if (!$this->checkAdvancedConditionsOPC('category', $config)) {
			//	 $return[] = '&nbsp;'; 
			
				 $filter[$vm_id] = $vm_id; 
				
				 unset($this->methods[$key]); 
				 if ($allc === 1) return; 
				 continue; 
				
			 }
		 }
		 
		 		 $default = array(); 
		 $config = OPCconfig::getValue('opcfilters', 'catfilterP1', $vm_id, $default, false, false);
		 if (!is_array($config)) $config = (array)$config; 
		 if (!empty($config)) {
			 
			 if (!$this->checkAdvancedConditionsOPC('product', $config)) {
			//	 $return[] = '&nbsp;'; 
			
				 $filter[$vm_id] = $vm_id; 
				
				 unset($this->methods[$key]); 
				 if ($allc === 1) return; 
				 continue; 
				
			 }
		 }
		 
	     $html = ''; 
		 
		 // return html=' '; if you do not want to display the shipping method
	      require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'transform.php');
		 
		 if (!isset($filter[$vm_id]))	
		 OPCtransform::overrideShippingHtml($html, $cart, $vm_id); 
	     
		 if ($html != '') 
	     {
			$kk = $method->shipment_element.'____'.$method->virtuemart_shipmentmethod_id; 
			 
			 
			 $json = json_encode($html); $e = false; $e2 = false; 
			if (function_exists('json_last_error_msg'))
	        $e = json_last_error_msg(); 
		    if (function_exists('json_last_error'))
		    $e2 = json_last_error(); 
		
			if ((empty($json)) || (!empty($e2)))
			{
				if (class_exists('OPCloader'))
				{
					OPCloader::opcDebug('Shipping method did not pass UTF8 test: '.$kk); 
				}
				continue; 
			}
			
			$return[] = $html; 
		    
	     }
		 
		 
	   
		 //break; 
		}
	   }
	   
	   if (!empty($return))
	    {
		  foreach ($return as $k=>$v)
		  {
			 $kk = $this->_name; 
		     $htmlIn[$kk.'____'.$k] = $v; 
		  }
		  return true; 
		}
	   
	   }
	   else return null; 
	  
	  
	  /*
	  $htmlstart = $htmlIn; 
	  $newhtml = array(); 
	  */
	  
	  $htmlS = array(); 
	  
	  try {
	    $ret = $this->plgVmDisplayListFEShipment($cart, $selected, $htmlS);
	  }
	  catch (Exception $e) {
	   $msg = (string)$e->getMessage(); 
	   JFactory::getApplication()->enqueueMessage($msg, 'error'); 
	  }
	  
	  foreach ($htmlS as $k=>$v)
	  {
		  $kk = $this->_name; 
		    $json = json_encode($v); $e = false; $e2 = false; 
			if (function_exists('json_last_error_msg'))
	        $e = json_last_error_msg(); 
		    if (function_exists('json_last_error'))
		    $e2 = json_last_error(); 
		
			if ((empty($json)) || (!empty($e2)))
			{
				if (class_exists('OPCloader'))
				{
					OPCloader::opcDebug('Shipping method did not pass UTF8 test: '.$kk); 
				}
				continue; 
			}
		  
		  $htmlIn[$kk.'____'.$k] = $v; 
	  }
	  
	 
	 
	  if (!empty($filter)) {
		  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'transform.php');
	  foreach ($htmlIn as $kx => $ar1) {
		 
		  if (is_array($ar1)) {
			  foreach ($ar1 as $k1=>$v1) {
			    $vid = OPCtransform::getIdInHtml($v1, 'shipment');
				 if (isset($filter[$vid])) {
				   unset($htmlIn[$kx][$k1]); 
				 }
			  }
		  }
		  else {
			  $vid = OPCtransform::getIdInHtml($v1, 'shipment');
			 
			  if (isset($filter[$vid])) {
				  unset($htmlIn[$kx]); 
			  }
			  
		  }
	  }
	  }
	  return $ret; 
	  
	  /*
	  if (!empty($newhtml))
	  {
	  if (!empty($this->methods))
	  foreach ($newhtml as &$html)
	  {
	  foreach ($this->methods as $key => $method)
	   {
	     OPCTransform::getOverride('opc_transform', $this->_name, $this->_psType, $this, $method, $html); 
	   }
	   
	   
	    
	   $htmlIn[] = $html; 
	   return true; 
		
		}
	   }
	  //$htmlIn .= $newhtml; 
	  */
	  
	}
	
	
	
	
	
	private function _setMissingOPC(&$method)
	{
	  if (!isset($method->payment_logos)) $method->payment_logos = ''; 
	  if (!isset($method->cost_per_transaction)) $method->cost_per_transaction = 0; 
	  if (!isset($method->cost_percent_total)) $method->cost_percent_total = 0; 
		 
		if (!isset($method->tax_id)) $method->tax_id = -1; 
		if (!isset($method->weight_unit)) $method->weight_unit = 'KG'; 
	}
	
		/**
	 * Fill the array with all plugins found with this plugin for the current vendor
	 *
	 * @return True when plugins(s) was (were) found for this vendor, false otherwise
	 * @author Oscar van Eijk
	 * @author max Milbers
	 * @author valerie Isaksen
	 */
	protected function getPluginMethodsOPC ($vendorId) {
		$at = array('shipment', 'payment'); 
		if (!in_array($this->_psType, $at)) {
		  if (empty($this->methods))
		  $this->methods = array(); 
		  return; 
		}
	


	
		if (!class_exists ('VirtueMartModelUser')) {
			require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'user.php');
		}
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		OPCmini::setVMLANG(); 
		

		
		//vm3 CODE: 
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		if ((isset(VmConfig::$defaultLang)) && (isset(VmConfig::$vmlang))) {
		
				
		
		$usermodel = VmModel::getModel('user');
		$user = $usermodel->getUser ();
		$user->shopper_groups = (array)$user->shopper_groups;

		$db = JFactory::getDBO ();
		if(empty($vendorId)) $vendorId = 1;
		$select = 'SELECT i.*, ';

		$extPlgTable = '#__extensions';
		$extField1 = 'extension_id';
		$extField2 = 'element';

		$select .= 'j.`' . $extField1 . '`,j.`name`, j.`type`, j.`element`, j.`folder`, j.`client_id`, j.`enabled`, j.`access`, j.`protected`, j.`manifest_cache`,
			j.`params`, j.`custom_data`, j.`system_data`, j.`checked_out`, j.`checked_out_time`, j.`state`,  s.virtuemart_shoppergroup_id ';

		if(!VmConfig::$vmlang){
			if (method_exists('VmConfig', 'setdbLanguageTag'))
			VmConfig::setdbLanguageTag();
		}

		
		
		$joins = array();
		if(VmConfig::$defaultLang!=VmConfig::$vmlang and Vmconfig::$langCount>1){
			$langFields = array($this->_psType.'_name',$this->_psType.'_desc');

			$useJLback = false;
			
			if (!isset(VmConfig::$jDefLang)) {
				

			$jDefLangTag = VmConfig::get('vmDefLang',false);
			if(!$jDefLangTag) {
				if (class_exists('JComponentHelper') && (method_exists('JComponentHelper', 'getParams'))) {
					$params = JComponentHelper::getParams('com_languages');
					$jDefLangTag = $params->get('site', 'en-GB');
				} else {
					$jDefLangTag = 'en-GB';//use default joomla
					
				}
			}

			$jDefLang = strtolower(strtr($jDefLangTag,'-','_'));
		
			}
			else {
				$jDefLang = VmConfig::$jDefLang; 
			}
			
			
			if(VmConfig::$defaultLang != $jDefLang) {
				//OPC323$joins[] = ' LEFT JOIN `#__virtuemart_'.$this->_psType.'_'.VmConfig::$jDefLang.'` as ljd';
				$joins[] = ' LEFT JOIN `#__virtuemart_'.$this->_psType.'methods_'.$jDefLang.'` as ljd ON ljd.`virtuemart_'.$this->_psType.'method_id` = i.`virtuemart_'.$this->_psType.'method_id`';
				$useJLback = true;
			}

			foreach($langFields as $langField){
				$expr2 = 'ld.'.$langField;
				if($useJLback){
					$expr2 = 'IFNULL(ld.'.$langField.',ljd.'.$langField.')';
				}
				$select .= ', IFNULL(l.'.$langField.','.$expr2.') as '.$langField.'';
			}
			
			
			//OPC323$joins[] = ' LEFT JOIN `#__virtuemart_'.$this->_psType.'methods_'.VmConfig::$defaultLang.'` as ld using (`virtuemart_'.$this->_psType.'method_id`)';
			  $joins[] = ' LEFT JOIN `#__virtuemart_'.$this->_psType.'methods_'.VmConfig::$defaultLang.'` as ld ON ld.`virtuemart_'.$this->_psType.'method_id` = i.`virtuemart_'.$this->_psType.'method_id`';
			//OPC323$joins[] = ' LEFT JOIN `#__virtuemart_'.$this->_psType.'methods_'.VmConfig::$vmlang.'` as l using (`virtuemart_'.$this->_psType.'method_id`)';
			
			$joins[] = ' LEFT JOIN `#__virtuemart_'.$this->_psType.'methods_'.VmConfig::$vmlang.'` as l ON l.`virtuemart_'.$this->_psType.'method_id` = i.`virtuemart_'.$this->_psType.'method_id`';

		} else {
			$select .= ', l.* ';
			//OPC323$joins[] = ' LEFT JOIN `#__virtuemart_'.$this->_psType.'methods_'.VmConfig::$vmlang.'` as l using (`virtuemart_'.$this->_psType.'method_id`)';
			$joins[] = ' LEFT JOIN `#__virtuemart_'.$this->_psType.'methods_'.VmConfig::$vmlang.'` as l ON l.`virtuemart_'.$this->_psType.'method_id` = i.`virtuemart_'.$this->_psType.'method_id`';
		}

		$q = $select . ' FROM   `#__virtuemart_' . $this->_psType . 'methods' . '` as i ';

		
		$joins[]= ' LEFT JOIN `' . $extPlgTable . '` as j ON j.`' . $extField1 . '` =  i.`' . $this->_psType . '_jplugin_id` ';
		$joins[]= ' LEFT OUTER JOIN `#__virtuemart_' . $this->_psType . 'method_shoppergroups` AS s ON i.`virtuemart_' . $this->_psType . 'method_id` = s.`virtuemart_' . $this->_psType . 'method_id` ';

		$q .= implode(' '."\n",$joins);
		$q .= ' WHERE i.`published` = "1" AND j.`' . $extField2 . '` = "' . $this->_name . '"
	    						AND  (i.`virtuemart_vendor_id` = "' . $vendorId . '" OR i.`virtuemart_vendor_id` = "0" OR i.`shared` = "1")
	    						AND  (';

		foreach ($user->shopper_groups as $groups) {
			$q .= ' s.`virtuemart_shoppergroup_id`= "' . (int)$groups . '" OR';
		}
		$q .= ' (s.`virtuemart_shoppergroup_id`) IS NULL ) GROUP BY i.`virtuemart_' . $this->_psType . 'method_id` ORDER BY i.`ordering`';


		$db->setQuery ($q);

		$this->methods = $db->loadObjectList ();

		
		if ($this->methods) {
			foreach ($this->methods as $method) {
				
				$key = $this->_psType.'_name'; 
				
			    if (empty($method->$key)) {
				
			       require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php');
				   $table = '#__virtuemart_'.$this->_psType.'methods_en_gb'; 
				   if (OPCmini::tableExists('#__virtuemart_'.$this->_psType.'methods_en_gb')) {
					   $k2 = 'virtuemart_'.$this->_psType.'method_id'; 
					   if (!empty($method->$k2)) {
					   $q = 'select `'.$key.'` from `'.$table.'` where `'.$k2.'` = '.(int)$method->$k2; 
					  
					   $db->setQuery($q); 
					   
					   $r = $db->loadResult(); 
					   if (!empty($r)) {
					       $method->$key = $r; 
					   }
					   }
				   }
				   
				}
				
				
				VmTable::bindParameterable ($method, $this->_xParams, $this->_varsToPushParam);
			}
		} else if($this->methods===false){


			
		}
		return count ($this->methods);
		
		}
		
		
		
		$usermodel = VmModel::getModel ('user');
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
				j.`params`, j.`custom_data`, j.`system_data`, j.`checked_out`, j.`checked_out_time`, j.`state`,  s.virtuemart_shoppergroup_id ';
		}

		
		
		 { 
		$q = $select . ' FROM   `#__virtuemart_' . $this->_psType . 'methods_' . VMLANG . '` as l ';
		$q .= ' JOIN `#__virtuemart_' . $this->_psType . 'methods` AS v   USING (`virtuemart_' . $this->_psType . 'method_id`) ';
		
		$q .= ' LEFT JOIN `' . $extPlgTable . '` as j ON j.`' . $extField1 . '` =  v.`' . $this->_psType . '_jplugin_id` ';
		$q .= ' LEFT OUTER JOIN `#__virtuemart_' . $this->_psType . 'method_shoppergroups` AS s ON v.`virtuemart_' . $this->_psType . 'method_id` = s.`virtuemart_' . $this->_psType . 'method_id` ';
		$q .= ' WHERE v.`published` = "1" AND j.`' . $extField2 . '` = "' . $this->_name . '" '; 
	    if (!empty($vendorId))
		$q .= ' AND  (v.`virtuemart_vendor_id` = "' . $vendorId . '" OR   v.`virtuemart_vendor_id` = "0") '; 
		if (!empty($user->shopper_groups))
		{
        $q .= ' AND  (';
		foreach ($user->shopper_groups as $groups) {
			$q .= ' s.`virtuemart_shoppergroup_id`= "' . (int)$groups . '" OR';
		}
		$q .= ' (s.`virtuemart_shoppergroup_id`) IS NULL ) '; 
		}
		$q .= ' GROUP BY v.`virtuemart_' . $this->_psType . 'method_id` ORDER BY v.`ordering`';
		}
		
		$db->setQuery ($q);


		$this->methods = $db->loadObjectList ();

		$err = $db->getErrorMsg ();
		if (!empty($err)) {
			vmError ('Error reading getPluginMethods ' . $err);
		}
		if ($this->methods) {
			foreach ($this->methods as $method) {
			    if (!empty($this->_xParams))
				VmTable::bindParameterable ($method, $this->_xParams, $this->_varsToPushParam);
			}
		}

		return count ($this->methods);
	}
	
	
	public function plgVmDisplayListFEPaymentOPCNocache(&$cart, $selected = 0, &$htmlIn)
	{
	   
	  if ($this->_psType != 'payment') return; 
	  
	  if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
	   
	    if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'currencydisplay.php');
		 }
		
		 $currency = CurrencyDisplay::getInstance ();
	   
	   
	   
	   
		
		if ($this->_name == 'klarna')
		{
		  $address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
		  if (isset($address['virtuemart_country_id']))
		  $country = $address['virtuemart_country_id']; 
		  if (empty($country) && (!empty($cart->BT['virtuemart_country_id']))) $country = $cart->BT['virtuemart_country_id']; 
		   if (!class_exists('ShopFunctions'))
		  require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'shopfunctions.php');
		  if (empty($country)) return; 
		  
		  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		  
		  $countryCode = OPCmini::getCountryByID ($country, 'country_2_code');
		  $avai = array('SE', 'DE', 'NL', 'NO', 'DK', 'FI'); 
		  $countryCode = strtoupper($countryCode); 
		  if (!in_array($countryCode, $avai)) 
		  {
		   return; 
		  }
		  
		}
		
	    if ($this->getPluginMethodsOPC($cart->vendorId) === 0) {
	  
                return FALSE;            
        }
	   $return = array(); 
	   $filter = array(); 
	   
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
	   $zero_p = OPCConfig::get('default_payment_zero_total', 0);   
	   $zero_p = (int)$zero_p; 
		 
	   if (isset($this->methods))
	   {
	   $ref =& $this; 
	   foreach ($this->methods as $key => &$method)
	   {
	   if (!$this->checkConditions ($cart, $method, $cart->cartPrices)) continue; 
	   if (isset($method->virtuemart_paymentmethod_id ))
	   {
		   
		 
		 $vm_id = (int)$method->virtuemart_paymentmethod_id; 
		 
		 $default = array(); 
		 $config = OPCconfig::getValue('opcfilters', 'catfilterp', $vm_id, $default, false, false);
		 if (!is_array($config)) $config = (array)$config; 
		 if (!empty($config)) {
			 if (!$this->checkAdvancedConditionsOPC('category', $config)) {
				 $filter[$vm_id] = $vm_id; 
				 continue; 
			 }
		 }
		 
		  $config = OPCconfig::getValue('opcfilters', 'catfilterPS1', $vm_id, $default, false, false);
		 if (!is_array($config)) $config = (array)$config; 
		 if (!empty($config)) {
			 if (!$this->checkAdvancedConditionsOPC('product', $config)) {
				 $filter[$vm_id] = $vm_id; 
				 continue; 
			 }
		 }
		 
		
		 
		 if ($zero_p === $vm_id) {
			
			 continue; 
		 }
		   
	     $this->_setMissingOPC($method); 
		 
		
		 
	     
	     $html = ''; 
		 //$filename = 'plg_' . $this->_type . '_' . $this->_name;
		 $name = $this->_name; 
		 jimport('joomla.filesystem.file');
   $name = JFile::makeSafe($name); 
   $type = $this->_psType; 
   if ($type == 'vmpayment') $type = 'payment'; 
   if ($type == 'vmshipment') $type = 'shipment'; 
   $type = JFile::makeSafe($type); 
   
  
   
   		static $theme; 
		if (empty($theme) && (class_exists('op_languageHelper')))
		{
		
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		$selected_template = OPCmini::getSelectedTemplate();  
		$theme = $selected_template; 
		
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		
		}

   $layout_name = 'after_render'; 
   $layout = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$layout_name.'.php';
   
   if (file_exists($layout))
		 {
		  $method_name = $this->_psType . '_name';
		  
		  $plugin =& $method; 
		  $pluginmethod_id = $this->_idName;
		  $plugin_name = $this->_psType . '_name';
		  $plugin_desc = $this->_psType . '_desc';
		  $logosFieldName = $this->_psType . '_logos';
		  $logo_list = $plugin->$logosFieldName;
		 
		 	$pricesUnformatted= $cart->pricesUnformatted;
			$arr = array($this, 'setCartPrices'); 
			if (is_callable($arr))
			$pluginSalesPrice = $this->setCartPrices ($cart, $pricesUnformatted,$method);
			else $pluginSalesPrice = 0; 
		
		 
		  $url = JURI::root () . 'images/stories/virtuemart/' . $this->_psType . '/';
		  if (!is_array ($logo_list)) {
				$logo_list = (array)$logo_list;
			}
		  
		 
		  
		  
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   
		   ob_start(); 
		   if (!empty($opc_debug_theme) && class_exists('OPCrenderer')) OPCrenderer::debugTheme($layout); 
		   include($layout); 
		   if (!empty($opc_debug_theme) && class_exists('OPCrenderer')) OPCrenderer::debugTheme($layout); 
		   if (!empty($html)) $null = ob_get_clean(); 
		   else
		   $html = ob_get_clean(); 
		   
		    
			//$html = '<div class="opc_payment_wrap_'.$vm_id.'">'.$html.'</div>'; 
		   $isset = true; 
		   
		   
		 }
		 else
		 {
			  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'transform.php');
			 
		 if (!isset($filter[$vm_id]))	 
	     OPCtransform::overridePaymentHtml($html, $cart, $vm_id, $this->_name, $this->_type, $method); 
		 }
	     if ($html != '') 
	     {
			 
			
			 
			//$return[] = '<div class="opc_payment_wrap_'.$vm_id.'">'.$html.'</div>'; 
		    
	     }
	   
		 //break; 
		}
	   }
	   
	   if (!empty($return))
	    {
			
			$json = json_encode($html); $e = false; $e2 = false; 
			if (function_exists('json_last_error_msg'))
	        $e = json_last_error_msg(); 
		    if (function_exists('json_last_error'))
		    $e2 = json_last_error(); 
		
			if ((empty($json)) || (!empty($e2)))
			{
				if (class_exists('OPCloader'))
				{
					$kk = $this->_name; 
					OPCloader::opcDebug('Payment method did not pass UTF8 test: '.$kk); 
				}
				
			}
			else
			{
			
				$htmlIn[] = $return; 
				return true; 
			}
		}
	   
	   }
	   else return null; 
	  
	  
	  if (empty($htmlIn)) $htmlIn = array(); 
	  require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'transform.php');
	  $new = array(); 
	  try {
	    $return = $this->plgVmDisplayListFEPayment($cart, $selected, $new);
	  }
	   catch (Exception $e) {
	   $msg = (string)$e->getMessage(); 
	   JFactory::getApplication()->enqueueMessage($msg, 'error'); 
	  }
	  
	  
	  // loads render_after
	  foreach ($new as $ki=>&$html)
	    {
			if (is_array($html))
			{
			foreach ($html as $kj => &$paymentplugin_payment)
			{
			
			 $id = OPCtransform::getIdInHtml($paymentplugin_payment, 'payment');			 
			 if (empty($id)) continue; 
			 if ($zero_p === $id) {
			  unset($html[$kj]);
			  continue; 
			 }
			 if (isset($filter[$id])) {
				 
				 unset($html[$kj]); 
				 continue; 
			 }
		     OPCtransform::overridePaymentHtml($paymentplugin_payment, $cart, $id, $this->_name, $this->_type); 
			
			}
			}
			else {
				 $id = OPCtransform::getIdInHtml($html, 'payment');			 
			 if (empty($id)) continue; 
			 if ($zero_p === $id) {
			  unset($new[$ki]);
			  continue; 
			 }
			 if (isset($filter[$id])) {
				 
				 unset($new[$ki]);
				 continue; 
			 }
		     OPCtransform::overridePaymentHtml($html, $cart, $id, $this->_name, $this->_type); 
			
			}
		    if (!empty($html))
			{
				//$html = '<div class="opc_payment_wrap_'.$vm_id.'">'.$html.'</div>'; 
			}
		     $json = json_encode($html); $e = false; $e2 = false; 
			if (function_exists('json_last_error_msg'))
	        $e = json_last_error_msg(); 
		    if (function_exists('json_last_error'))
		    $e2 = json_last_error(); 
		
			if ((empty($json)) || (!empty($e2)))
			{
				
				if (class_exists('OPCloader'))
				{
					$kk = $this->_name; 
					OPCloader::opcDebug('Payment method did not pass UTF8 test: '.$kk); 
				}
			  continue; 	
			}
			
		  
		  
		  $htmlIn[] = $html; 
		}
	  return $return; 
	} 
	
	function getPPLExpress(&$payment_id, &$cart)
	{
	  
	  $methods = $this->getPluginMethodsOPC($cart->vendorId); 
	  if ($methods === 0) {
			return FALSE;
		}
		
		foreach ($this->methods as $m)
		 {
		 
		   if (isset($m->paypalproduct))
			  {
			  
			    if ($m->paypalproduct == 'exp')
				  {
				  
				    if (isset($m->virtuemart_paymentmethod_id))
				    $payment_id = $m->virtuemart_paymentmethod_id;
					
					
				    return; 
					
				  }
			  }
			  else
			  return;
		 }
	  
	  
	}
	
	
	function getPluginOPC($vid, &$cart, &$ret)
	{
		
		
		if (isset($this->customerData))
		if (method_exists($this->customerData, 'getVar'))
		{
		  $token = $this->customerData->getVar('token'); 
		  if (!empty($token))
		   {
		     
		   }
		}
		
		$m = $this->getVmPluginMethod($vid); 
		if (empty($m)) return;
		$ret[] = $m; 
		return;

	}
	
	
	function getPluginHtmlOPC(&$result, &$methodOPC, $type='shipment', $virtuemart_id=0, $cart)
	{
	  if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
	   
	   $allowed = array('shipment', 'payment'); 
	   if (!in_array($this->_psType, $allowed)) return; 
	   
	   if ($this->getPluginMethodsOPC($cart->vendorId) === 0) {
                return FALSE;            
        }  
	   
	   if ($this->_psType == $type)
	   if (isset($this->methods))
	   foreach ($this->methods as $key => $method)
	   {
	   //if (!$this->checkConditions ($cart, $method, $cart->cartPrices)) continue; 
	   if (isset($method->virtuemart_shipmentmethod_id))
	   if ($virtuemart_id == $method->virtuemart_shipmentmethod_id) 
	   {
	     $methodSalesPrice = $this->calculateSalesPrice($cart, $method, $cart->pricesUnformatted);  
		 $method_name = $this->_psType . '_name';
		 $method->$method_name = $this->renderPluginName ($method);
		 $html = $this->getPluginHtml($method, 0, $methodSalesPrice);
		 $method->OPCname = $this->renderPluginName($method);
		 $method->OPCsalesprice = $methodSalesPrice; 
		 $methodOPC = $method; 
	     $result = $html; 
		 
		
		 break; 
		}
	   }
	}
	
	function getPluginNameOPC(&$result, &$methodOPC, $type='shipment', $virtuemart_id=0, $cart)
	{
	
	$allowed = array('shipment', 'payment'); 
	   if (!in_array($this->_psType, $allowed)) return; 
	
	  if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
	   
	   if ($this->getPluginMethodsOPC($cart->vendorId) === 0) {
                return FALSE;            
        }  
	   
	   if ($this->_psType == $type)
	   if (isset($this->methods))
	   foreach ($this->methods as $key => $method)
	   {
	   if (isset($method->virtuemart_shipmentmethod_id))
	   if ($virtuemart_id == $method->virtuemart_shipmentmethod_id) 
	   {
	     $result = $this->renderPluginName($method);
		 break; 
		}
	   }
	}
	
	function plgGetPluginObject(&$result, $type='shipment', $virtuemart_id=0)
	{
	   //if (empty($virtuemart_id) || ($virtuemart_id == 
	   if (isset($this->virtuemart_shipmentmethod_id))
	   if (!empty($virtuemart_id))
	   if ($virtuemart_id != $this->virtuemart_shipmentmethod_id) return null;
	   
	   if (empty($type) || ($this->_psType == $type))
	   $result[] =& $this; 
	   
	   return $this;
	}

	function display3rdInfo($intro,$developer,$contactlink,$manlink){
		$logolink = $this->getOwnUrl() ;
		return shopfunctions::display3rdInfo($this->_name,$intro,$developer,$logolink,$contactlink,$manlink);
	}
	
	private static $xmlCache; 
	private static $xmlDefaults; 
	
	static public function getVarsToPushByXML ($xmlFile,$name){
		$data = array();
		$defaults = array(); 
		
		
		if (isset(vmPlugin::$xmlCache[$xmlFile][$name])) return vmPlugin::$xmlCache[$xmlFile][$name]; 
		
		if (is_file ( $xmlFile )) {

			//$xml = JFactory::getXML ('simple');
			//$result = $xml->loadFile ($xmlFile);
			
			
			if ((!defined('VM_VERSION')) || (VM_VERSION < 3))
			{
			$xml =  JFactory::getXML($xmlFile);
			
			if ($xml) {
				if (isset( $xml->document->params) ){
					$params = $xml->document->params;
					foreach ($params as $param) {
						if ($param->_name = "params") {
							if ($children = $param->_children) {
								foreach ($children as $child) {
									if (isset($child->_attributes['name'])) {
										$data[$child->_attributes['name']] = array('', 'char');
										$result = TRUE;
									}
								}
							}
						}
					}
				} else {
					$form = JForm::getInstance($name, $xmlFile, array(),false, '//config');
					
					$fieldSets = $form->getFieldsets();
					foreach ($fieldSets as $name => $fieldSet) {
						foreach ($form->getFieldset($name) as $field) {
							// todo : type?
							$type='char';
							$data[(string)$field->fieldname] = array('',  $type);
						}
					}
			}
			}
			}
			else
			{
			
			
			    // should be only for 3.0.10+ 
				$form = JForm::getInstance($name, $xmlFile, array(),false, '//vmconfig | //config[not(//vmconfig)]');
				$data = vmPlugin::getVarsToPushFromForm($form);
			if (!empty($data))
			{
			vmPlugin::$xmlCache[$xmlFile][$name] = $data; 
			return $data;
			
			}
			
			//update: vm3.0.2 !!! start
			$data = array();

		if (is_file ( $xmlFile )) {

			//$xml = JFactory::getXML ('simple');
			//$result = $xml->loadFile ($xmlFile);
			$xml =  JFactory::getXML($xmlFile);
			if ($xml) {
				if (isset( $xml->document->params) ){

					$params = $xml->document->params;
					foreach ($params as $param) {
						if ($param->_name = "params") {
							if ($children = $param->_children) {
								foreach ($children as $child) {
									if (!empty($child->_attributes['name'])) {
										$fieldname = (string)$child->_attributes['name'];
										$private = false;
										if(strlen($fieldname)>1){
											if(substr($fieldname,0,2)=='__'){
												$private = true;
											}
										}

										if(!$private){
											$data[$fieldname] = array('', 'char');
										}
									}
								}
							}
						}
					}
				} else {

					$form = JForm::getInstance($name, $xmlFile, array(),false, '//vmconfig | //config[not(//vmconfig)]');
					$fieldSets = $form->getFieldsets();
					foreach ($fieldSets as $name => $fieldSet) {
						foreach ($form->getFieldset($name) as $field) {

							$fieldname = (string)$field->fieldname;
							$private = false;

							if(strlen($fieldname)>1){
								if(substr($fieldname,0,2)=='__'){
									$private = true;
								}
							}

							if(!$private){
								$type='char';
								$data[$fieldname] = array('',  $type);
							}

						}
					}
				}
			}
		}
		
		
		if (!empty($data))
		{
		vmPlugin::$xmlCache[$xmlFile][$name] = $data; 
		return $data;
		}
		
			//update: vm3.0.2 !!! end
			
		
			        $xml = simplexml_load_file($xmlFile); 
					if (!empty($xml))
					if (isset( $xml->vmconfig) )
					{
					
					foreach ($xml->vmconfig->fields->children() as $i=>$child)
					 {
					    
					    $tagname = (string)$i; 
						if ($tagname == 'fieldset')
						 { 
						   foreach ($child->children() as $u=>$param)
						    {
							   $tname = (string)$u; 
							   
							   if ($tname == 'field')
							    {
								// repeat 1
								  $attr = current($param->attributes()); 
								 
								  if (isset($attr['name']))
								  {
								  
								    $data[(string)$attr['name']] = array('',  'char');
									
									if (isset($attr['default']))
									 {
									   $defaults[(string)$attr['name']] = (string)$attr['default']; 
									 }
								  
								  }
								// repeat 1	 end
								  
								   
								}
							}
						 }
						 else
						 if ($tagname == 'field')
						  {
						     // repeat 1
						      $attr = current($param->attributes()); 
								  if (isset($attr['name']))
								  {
								  
								    $data[(string)$attr['name']] = array('',  'char');
									
									if (isset($attr['default']))
									 {
									   $defaults[(string)$attr['name']] = (string)$attr['default']; 
									 }
								  }
						    // repeat 1 end
						  }
						
					 }
					
				}
					}
				}
			
		
		vmPlugin::$xmlCache[$xmlFile][$name] = $data; 
		vmPlugin::$xmlDefaults[$name] = $defaults; 
		
		
		return $data;
		}
		
	
	
	/**
	 * Checks if this plugin should be active by the trigger
	 *
	 * @author Max Milbers
	 * @param string $psType shipment,payment,custom
	 * @param        string the name of the plugin for example textinput, paypal
	 * @param        int/array $jid the registered plugin id(s) of the joomla table
	 *
	 * @param int/array $id the registered plugin id(s) of the joomla table
	 */
	protected function selectedThis ($psType, $name = 0, $jid = 0) {

		if ($psType !== 0) {
			if($psType!=$this->_psType){
				vmdebug('selectedThis $psType does not fit');
				
				return false;
			}
		}

		if($name!==0){
			if($name!=$this->_name){
 				vmdebug('selectedThis $name '.$name.' does not fit pluginname '.$this->_name);
				
				return false;
			}
		}

		if($jid===0){
		     
			return false;
		} else {
			if($this->_jid===0){
				$this->getJoomlaPluginId();
			}
			if(is_array($jid)){
				if(!in_array($this->_jid,$jid)){
					//vmdebug('selectedThis id '.$jid.' not in array does not fit '.$this->_jid);
			
			return false;
				}
			} else {
				if($jid!=$this->_jid){
					//vmdebug('selectedThis $jid '.$jid.' does not fit '.$this->_jid);
					//echo $jid; 
				//	echo '<br />'.$this->_jid.'<br />'; 
					return false;
				}
			}
		}

		return true;
	}
	
	public function plgVmDisplayListFEShipmentOPC(&$cart, $selected = 0, &$htmlIn)
	{
	    if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
	   
		if ($this->_type != 'vmshipment') return; 

		$pluginmethod_id = $this->_idName; //virtuemart_shipmentmethod_id
		$pluginName = $this->_psType . '_name'; // shipment_name
			
		$pluginName = $this->_name; 
		$data = array($selected, $pluginmethod_id, $pluginName ); 
			

			
		$hash = OPCcache::getGeneralCacheHash('plgVmDisplayListFEShipment', $cart, $data); 
		$val = OPCcache::getValue($hash); 
			
		VmPlugin::$iCount++; 
		
		
		
		
			if (!empty($val))
			{
				
				if (empty($val[0])) 
				{
				//break;
				return; 
				//return $val[0]; 
				}
				else
				{
				foreach ($val[1] as $vala)
				{
				 $htmlIn[] = $vala; 
				}
				
				return true; 
				}
			}
			
		
		
		
			
		
		
		
		$htmlIn2 = array(); 
		$val = $this->plgVmDisplayListFEShipmentOPCNocache($cart, $selected, $htmlIn2);
		$html = ''; 
		if (is_array($htmlIn2))
		{
		foreach ($htmlIn2 as $h)
		{
			if (is_array($h))
			{
				foreach ($h as $h2)
				  $html .= $h2; 
			}
			  else
			$html .= $h; 
		}
		}
		else $html  = $htmlIn2; 
		if (!empty($html) || (stripos($html, 'virtuemart_shipmentmethod_id')!==false))
		if (stripos($html, 'error')===false)
		$val2 = OPCcache::setValue($hash, array($val, $htmlIn2 )); 
		
	
	
		if (!empty($htmlIn2))
		{
			$htmlIn = array_merge($htmlIn, $htmlIn2); 
		}
		
		return $val; 
	}
	
	public function plgVmGetSpecificCache($cart, $id=0)
	{
		
		if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }
		
		if ($this->_type == 'vmshipment')
		{
		
		if (empty($cart->virtuemart_shipmentmethod_id)) 
		{
		
		return ""; 
		}
		else
		$virtuemart_shipmentmethod_id = $cart->virtuemart_shipmentmethod_id; 
	     
	    
		if (!($this->selectedThisByMethodId($virtuemart_shipmentmethod_id))) {
			return "";
		}
		
		
		
		
		$to_address = (($cart->ST == 0) ? $cart->BT : $cart->ST);
	    
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'third_party'.DIRECTORY_SEPARATOR.'third_party_shipping_permanent_cache.php'); 

		}
	}
	function &setCache(&$value)
	{
	  $arg_list = func_get_args();
	  $hash = ''; 
	  for ($i = 1; $i < count($arg_list); $i++) {
        $hash .= json_encode($arg_list[$i]); 
	  }
	  $hash = md5($hash); 
	  vmPlugin::$qCache[$hash] = $arg_list[0]; 
	  return $value; 
	  /*
	  $cache =  JFactory::getCache();
	  $orig = $cache->getCaching(); 
	  $cache->setCaching( 1 );
	  $cache->store($value, $hash, 'opccache'); 
	  $cache->setCaching( $orig );
	  */
	  return $value; 
	}
	
	function &getCache()
	{
	  $f = false; 
	  return $f;
	  $arg_list = func_get_args();
	  $hash = ''; 
	  for ($i = 0; $i < count($arg_list); $i++) {
        $hash .= json_encode($arg_list[$i]); 
	  }
	  $hash = md5($hash); 
	  if (isset(vmPlugin::$qCache[$hash])) 
	  {
	   // we have a match, let's measure it
	  $counta = vmPlugin::$ccount; 
	  if (empty($counta)) vmPlugin::$ccount = 1; 
	  else vmPlugin::$ccount++;
	  
	  return vmPlugin::$qCache[$hash]; 
	  }
	  $res = false; 
	  /*
	  $cache =  JFactory::getCache();
	  $orig = $cache->getCaching(); 
	  $cache->setCaching( 1 );
	  $res = $cache->get($hash, 'opccache'); 
	  $cache->setCaching( $orig );
	  */
	  return $res; 
	  
	  
	}
	/**
	* Checks if this plugin should be active by the trigger
	* @author Max Milbers
	* @author Valérie Isaksen
	* @param string $psType shipment,payment,custom
	* @param string the name of the plugin for exampel textinput, paypal
	* @param int/array $id the registered plugin id(s) of the joomla table
	*/
	
	
	function selectedThisByMethodId(  $id='type') {
		//echo '<br />selectedThisByMethodId:'.$id.' type:'.$this->_psType.' idName:'.$this->_idName.' name:'.$this->_name.'<br />'; 
		//selectedThisByMethodId:2 type:shipment idName:virtuemart_shipmentmethod_id
		//if($psType!=$this->_psType) return false;
		
		
		$db = JFactory::getDBO();
		
		if($id==='type'){
			return true;
		} else {
			$db = JFactory::getDBO();
			
			if (version_compare(JVERSION, '1.6.0', '<')) 
			{
				$q = 'SELECT vm.* FROM `'.$this->_configTable.'` AS vm,
							#__plugins AS j WHERE vm.`'.$this->_idName.'` = "'.$id.'"
							AND vm.'.$this->_psType.'_jplugin_id = j.id
							AND j.element = "'.$this->_name.'"';
			} else {
				$q = 'SELECT vm.* FROM `'.$this->_configTable.'` AS vm,
							#__extensions AS j WHERE vm.`'.$this->_idName.'` = "'.$id.'"
							AND vm.'.$this->_psType.'_jplugin_id = j.extension_id
							AND j.element = "'.$this->_name.'"';
			}
			
			
			$x = vmPlugin::getCache('selectedThisByMethodId', $q); 
			
			
			if (!empty($x)) return $x; 
			//echo 'selectedThisByMethod'.$this->_psType;
			$db->setQuery($q);
			$res = $db->loadObject(); 
			
			if(empty($res))
			{
// 				//vmError('selectedThisByMethodId '.$db->getQuery());
				$res = false; 
				$x = vmPlugin::setCache($res, 'selectedThisByMethodId', $q); 
				return false; 
			} else {
				$x = vmPlugin::setCache($res, 'selectedThisByMethodId', $q); 
				return $res;
			}
		}
	}
/**
	* Checks if this plugin should be active by the trigger
	* @author Max Milbers
	* @author Valérie Isaksen
	* @param string the name of the plugin for exampel textinput, paypal
	* @param int/array $id the registered plugin id(s) of the joomla table
	*/
	protected function selectedThisByJPluginId(  $jplugin_id='type') {

		$db = JFactory::getDBO();

		if($jplugin_id==='type'){
			return true;
		} else {
			$db = JFactory::getDBO();

			if (version_compare(JVERSION, '1.6.0', '<')) 
			{
				$q = 'SELECT vm.* FROM `'.$this->_configTable.'` AS vm,
							#__plugins AS j WHERE vm.`'.$this->_psType.'_jplugin_id`  = "'.$jplugin_id.'"
							AND vm.'.$this->_psType.'_jplugin_id = j.id
							AND j.`element` = "'.$this->_name.'"';
			} else {
				$q = 'SELECT vm.* FROM `'.$this->_configTable.'` AS vm,
							#__extensions AS j WHERE vm.`'.$this->_psType.'_jplugin_id`  = "'.$jplugin_id.'"
							AND vm.`'.$this->_psType.'_jplugin_id` = j.extension_id
							AND j.`element` = "'.$this->_name.'"';
			}
			
			$x = vmPlugin::getCache('selectedThisByJPluginId', $q);  if (!empty($x)) return $x; 
			
			$db->setQuery($q);
			if(!$res = $db->loadObject() ){
// 				vmError('selectedThisByMethodId '.$db->getQuery());
				$res = false; 
				return vmPlugin::setCache($res, 'selectedThisByJPluginId', $q);  
			} else {
				return vmPlugin::setCache($res, 'selectedThisByJPluginId', $q);  
				
			}
		}
	}

	/**
	 * Gets the id of the joomla table where the plugin is registered
	 * @author Max Milbers
	 */
	final protected function getJoomlaPluginId(){

		if(!empty($this->_jid)) return $this->_jid;
		$db = JFactory::getDBO();

		if (version_compare(JVERSION, '1.6.0', '<')) 
			{
			$q = 'SELECT j.`id` AS c FROM #__plugins AS j
					WHERE j.element = "'.$this->_name.'" AND j.folder = "'.$this->_type.'"';
		} else {
			$q = 'SELECT j.`extension_id` AS c FROM #__extensions AS j
					WHERE j.element = "'.$this->_name.'" AND j.`folder` = "'.$this->_type.'"';
		}
		$x = vmPlugin::getCache('getJoomlaPluginId', $q);  if (!empty($x)) return $x; 
		
		$db->setQuery($q);
		$this->_jid = $db->loadResult();
		if(!$this->_jid){
			vmError('getJoomlaPluginId '.$db->getErrorMsg());
			$res = false; 
			return vmPlugin::setCache($res, 'getJoomlaPluginId', $q);
			return false;
		} else {
		    return vmPlugin::setCache($this->_jid, 'getJoomlaPluginId', $q);
			return $this->_jid;
		}
	}
/**
	 * Create the table for this plugin if it does not yet exist.
	 * Or updates the table, if it exists. Please be aware that this function is slowing and is only called
	 * storing a method or installing/udpating a plugin.
	 *
	 * @param string $psType shipment,payment,custom
	 * @author Valérie Isaksen
	 * @author Max Milbers
	 */
	protected function onStoreInstallPluginTableVM3 ($psType,$name=FALSE) {

		//vmdebug('Executing onStoreInstallPluginTable ');

		if(!empty($name) and $name!=$this->_name){
			return false;
		}

		//Todo the psType should be name of the plugin.
		if ($psType == $this->_psType) {

			$SQLfields = $this->getTableSQLFields();
			if(empty($SQLfields)) return false;

			$loggablefields = $this->getTableSQLLoggablefields();
			$tablesFields = array_merge($SQLfields, $loggablefields);

			$db = JFactory::getDBO();
			$query = 'SHOW TABLES LIKE "%' . str_replace('#__', '', $this->_tablename) . '"';
			$db->setQuery($query);
			$result = $db->loadResult();

			if ($result) {
				$update[$this->_tablename] = array($tablesFields, array(), array());
				$app = JFactory::getApplication();
				//$app->enqueueMessage(get_class($this) . ':: VirtueMart2 update ' . $this->_tablename);
				if (!class_exists('GenericTableUpdater'))
					require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'tableupdater.php');
				$updater = new GenericTableUpdater();
				$updater->updateMyVmTables($update);
			} else {
				$query = $this->createTableSQL($name,$tablesFields);
				if(empty($query)){
					return false;
				} else {
					$db->setQuery ($query);
					if (!$db->execute ()) {
						//vmWarn($this->_name . '::onStoreInstallPluginTable: ' . JText::_ ('COM_VIRTUEMART_SQL_ERROR') . ' ' . $db->stderr (TRUE));
						//echo $this->_name . '::onStoreInstallPluginTable: ' . JText::_ ('COM_VIRTUEMART_SQL_ERROR') . ' ' . $db->stderr (TRUE);
					} else {
						return true;
					}
				}
			}

		}
		return false;
	}
	/**
	* Create the table for this plugin if it does not yet exist.
	* @author Valérie Isaksen
	* @author Max Milbers
	*/
	protected function onStoreInstallPluginTable($psType, $name=FALSE) {
// stAn merge sept 2012
if(!empty($name) and $name!=$this->_name){
			return false;
		}
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3)) 
		return $this->onStoreInstallPluginTableVM3($psType, $name); 
		
		if($psType==$this->_psType){
			$query = $this->getVmPluginCreateTableSQL();
			
			if(empty($query)){
				return false;
			} else {
				$db = JFactory::getDBO();
				$db->setQuery($query);
				if (!$db->query()) {
					//JError::raiseWarning(1, $this->_name.'::onStoreInstallPluginTable: ' . JText::_('COM_VIRTUEMART_SQL_ERROR') . ' ' . $db->stderr(true));
					//echo $this->_name.'::onStoreInstallPluginTable: ' . JText::_('COM_VIRTUEMART_SQL_ERROR') . ' ' . $db->stderr(true);
				}
else {
return true; 
}

			}
		}

return false;
	}


	function getTableSQLLoggablefields() {
		
		$isM55 = true; 
	if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php')) {
       require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'mini.php'); 
	   $isM55 = OPCmini::isMysql('5.6.5'); 
	}
	if ($isM55) {
		return array(
		    'created_on' => ' datetime NOT NULL default CURRENT_TIMESTAMP',
		    'created_by' => "int(11) NOT NULL DEFAULT '0'",
		    'modified_on' => ' datetime NOT NULL DEFAULT CURRENT_TIMESTAMP',
		    'modified_by' => "int(11) NOT NULL DEFAULT '0'",
		    'locked_on' => ' datetime NOT NULL DEFAULT CURRENT_TIMESTAMP',
		    'locked_by' => 'int(11) NOT NULL DEFAULT \'0\''
		);
	}
	else
	{
		return array(
		    'created_on' => ' datetime NOT NULL default \'0000-00-00 00:00:00\'',
		    'created_by' => "int(11) NOT NULL DEFAULT '0'",
		    'modified_on' => ' datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
		    'modified_by' => "int(11) NOT NULL DEFAULT '0'",
		    'locked_on' => ' datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
		    'locked_by' => 'int(11) NOT NULL DEFAULT \'0\''
		);
	}
	    }

   /**
	 * @param $tableComment
	 * @return string
	 */
	protected function createTableSQL ($tableComment,$tablesFields=0) {

		$query = "CREATE TABLE IF NOT EXISTS `" . $this->_tablename . "` (";
		if(!empty($tablesFields)){
			foreach ($tablesFields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . " , ";
			}
		} else {
			$SQLfields = $this->getTableSQLFields ();
			$loggablefields = $this->getTableSQLLoggablefields ();
			foreach ($SQLfields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . " , ";
			}
			foreach ($loggablefields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . ", ";
			}
		}

		$query .= "	      PRIMARY KEY (`id`)
	    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='" . $tableComment . "' AUTO_INCREMENT=1 ;";
		return $query;
	}
	
	/**
	 *
	 * @param $psType
	 * @param $name
	 * @param $id
	 * @param $xParams
	 * @param $varsToPush
	 * @return bool
	 */
	protected function getTablePluginParams ($psType,$name, $id, &$xParams,&$varsToPush) {
		//vmdebug('getTablePluginParams $this->_psType '.$this->_psType.' sets $psType '.$psType.' $name',$name);
		if (!empty($this->_psType) and !$this->selectedThis ($psType, $name, $id)) {
			return FALSE;
		}
		//$x = $this->myClass(); 
		$varsToPush = $this->_varsToPushParam;
		$xParams = $this->_xParams;
		
		//vmdebug('getTablePluginParams '.$name.' sets xParams '.$xParams.' vars',$varsToPush);
	}

	function myClass()
	 {
	   $y = get_class($this); 
	   return $y; 
	 }
	/**
	 * Set with this function the provided plugin parameters
	 *
	 * @param string $paramsFieldName
	 * @param array $varsToPushParam
	 */
	function setConfigParameterable($paramsFieldName,$varsToPushParam){
	    //$x = $this->myClass(); 
		$this->_varsToPushParam = $varsToPushParam;
		
		{
		$arr = array('shipment', 'payment'); 
		if (in_array($this->_psType, $arr))
		{
		if (!isset($this->_varsToPushParam[$this->_psType.'_logos']))
		$this->_varsToPushParam[$this->_psType.'_logos'] = array('', 'char'); 
		if (!isset($this->_varsToPushParam['weight_unit']))
		$this->_varsToPushParam['weight_unit'] = array('KG', 'char'); 
		
		if (!isset($this->_varsToPushParam['tax_id']))
		$this->_varsToPushParam['tax_id'] = array(-1, 'int'); 
		}
		}
		$this->_xParams = $paramsFieldName;
	}

	protected function setOnTablePluginParams($name,$id,&$table){

		//Todo I think a test on this is wrong here
		//Adjusted it like already done in declarePluginParams
		if (!empty($this->_psType) and !$this->selectedThis ($this->_psType, $name, $id)) {
			return FALSE;
		}
		else {
		    //$x = $this->myClass(); 
			$table->setParameterable ($this->_xParams, $this->_varsToPushParam);
			return TRUE;
		}

	}

	/**
	 * @param $psType
	 * @param $name
	 * @param $id
	 * @param $data
	 * @return bool
	 */
	/**
	 * @param $psType
	 * @param $name
	 * @param $id
	 * @param $data
	 * @return bool
	 */
	protected function declarePluginParams ($psType, &$data) {

		//vmdebug('declarePluginParams ',$this->_psType,$data);
		if(!empty($this->_psType)){
			$element = $this->_psType.'_element';
			$jplugin_id = $this->_psType.'_jplugin_id';
			if(!isset($data->$element) or !isset($data->$jplugin_id)) return FALSE;
			if(!$this->selectedThis($psType,$data->$element,$data->$jplugin_id)){
				return FALSE;
			}
		}
		if (!class_exists ('VmTable')) {
			require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'vmtable.php');
		}
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		 {
		     //Is only used for the config tables!
		//VmTable::bindParameterable ($data, $data->_xParams, $this->_varsToPushParam);
		if(isset($this->_varsToPushParam)){
			if(isset($data->_varsToPushParam)){
				$data->_varsToPushParam = array_merge((array)$data->_varsToPushParam, (array)$this->_varsToPushParam);
			} else {
				$data->_varsToPushParam = (array)$this->_varsToPushParam;
			}
			//vmdebug(' vars to push',$data->_varsToPushParam);
			//$data->_varsToPushParam = $this->_varsToPushParam;
		} else{
			vmdebug('no vars to push?',$this);
		}
		 }
		 else
		 {
		if (!empty($this->_xParams))
		VmTable::bindParameterable ($data, $this->_xParams, $this->_varsToPushParam);
		 }
		 
		 if($this->_cryptedFields){
			$data->setCryptedFields($this->_cryptedFields);
		}
		return TRUE;

	}

	
	protected function getVmPluginMethodVM3 ($int, $cache = true) {

		if ($this->_vmpCtable === 0 || !$cache) {
			$db = JFactory::getDBO ();

			if (!class_exists ($this->_configTableClassName)) {
				if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . $this->_configTableFileName . '.php')) 
					return new stdClass(); 
				
				require(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . $this->_configTableFileName . '.php');
			}
			$this->_vmpCtable = new $this->_configTableClassName($db);
			if ($this->_xParams !== 0) {
				$this->_vmpCtable->setParameterable ($this->_configTableFieldName, $this->_varsToPushParam);
			}

			if($this->_cryptedFields){
				$this->_vmpCtable->setCryptedFields($this->_cryptedFields);
			}
		}

		return $this->_vmpCtable->load ($int);
	}
	
	
	public function getVmPluginMethod($int, $cache=true){

	$class = $this->myClass(); 	  
	

	  if (defined('VM_VERSION') && (VM_VERSION >= 3)) 
	   {
	   $admin = JFactory::getApplication()->isAdmin();    
	   if (($this->_name === 'amazon') || ($admin))
	   {
		   
	      return $this->getVmPluginMethodVM3($int, false); 
	   }
	   
	   //if ($this->_vmpCtable === 0 || !$cache) 
	   
	   {
			$db = JFactory::getDBO ();

			if (!class_exists ($this->_configTableClassName)) {
				require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'tables' . DIRECTORY_SEPARATOR . $this->_configTableFileName . '.php');
			}
			$this->_vmpCtable = new $this->_configTableClassName($db);
			if ($this->_xParams !== 0) {
				$this->_vmpCtable->setParameterable ($this->_configTableFieldName, $this->_varsToPushParam);
			}
			if($this->_cryptedFields){
				$this->_vmpCtable->setCryptedFields($this->_cryptedFields);
			}
		}
		
		$ret = $this->_vmpCtable->load ($int);
		
		 
		 
		  if (isset(vmPlugin::$xmlDefaults[$this->_name.'Form']))
		   {
		     $defaults = vmPlugin::$xmlDefaults[$this->_name.'Form']; 
			 foreach ($defaults as $key=>$name)
			  {
			   if (!isset($this->$key))
			   $this->$key = $name; 
			  }
		
		   }
		
		VmTable::bindParameterable ($ret, $this->_xParams, $this->_varsToPushParam);
		
		
		if (!empty($ret->payment_logos))
		{
			
			if (is_array($ret->payment_logos))
			{
			$a = reset($ret->payment_logos); 
			if (empty($a))
			{
				$ret->payment_logos = $this->payment_logos = array(); 
			}
			}
			
		}
		
		if (!empty($ret->shipment_logos))
		{
			if (is_array($ret->shipment_logos))
			{
			$a = reset($ret->shipment_logos); 
			if (empty($a))
			{
				$ret->shipment_logos = $this->shipment_logos = array(); 
			}
			}
			
		}
		if (!empty($ret->shipment_logos))
		{
			if (is_string($ret->shipment_logos))
				if ($ret->shipment_logos === 'index.html')
				{
					$ret->shipment_logos = array(); 
				}
		
		}
		if (!empty($ret->payment_logos))
		{
			if (is_string($ret->payment_logos))
				if ($ret->payment_logos === 'index.html')
				{
					$ret->payment_logos = array(); 
				}
		
		}
		if (!class_exists('VirtuemartCart'))
		require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'cart.php');
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		$default = false; 
		$config = OPCconfig::get('override_payment_currency', $default); 
		
		if (!empty($config))
		{
			
			
			
		$cart = VirtuemartCart::getCart(); 
		if (isset($cart->pricesCurrency))
		{
		 $ret->payment_currency = $ret->email_currency = $cart->pricesCurrency; 
		 $cart->paymentCurrency = $cart->pricesCurrency; 
		 
		 
		 
		 $cart->setCartIntoSession(); 
		}
		}
		return $ret; 
	   
	   
	   }
	
	  
	   
	   $x = vmPlugin::getCache('getVmPluginMethod', $class.$int); 
	   {
	   $this->_vmpCtable = $x;
	   
	   // vm 2.0.20
	   if (!empty($x))
	   if (!isset($x->tax_id)) $x->tax_id = -1; 
	   
	   if (!empty($x)) return $x; 
	   }
	   $x = $this->selectedThisByMethodId($int); 
	   if (empty($x)) 
	   {
	   $res = false; 
	   return vmPlugin::setCache($res, 'getVmPluginMethod', $class.$int); 
	   }

	   
	   
	   
	    /*
		static $lastInt; 
		if (!empty($lastInt))
		{
		  if ($int != $lastInt) 
		   $refresh = true; 
		  else $refresh = false;
		}
		else 
		{
		  $refresh = true; 
		  $lastInt = $int; 
		}
		*/
		
		if (!class_exists ('VmTable')) {
			require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'vmtable.php');
		}

		
		if (empty($this->_vmpCtableAll)) $this->_vmpCtableAll = array(); 
		$type = $this->_psType; 
		
	    
		
		if((empty($this->_vmpCtableAll[$type.$int])))
		{
			$db = JFactory::getDBO();

			if(!class_exists($this->_configTableClassName))require(JPATH_VM_ADMINISTRATOR.DIRECTORY_SEPARATOR.'tables'.DIRECTORY_SEPARATOR.$this->_configTableFileName.'.php');
			$this->_vmpCtableAll[$type.$int] = new $this->_configTableClassName($db);
			
			
			if ($this->_xParams !== 0) {
			    if (method_exists($this->_vmpCtableAll[$type.$int], 'setParameterable'))
				$this->_vmpCtableAll[$type.$int]->setParameterable($this->_xParams, $this->_varsToPushParam);
			}

			if($this->_cryptedFields){
			    if (method_exists($this->_vmpCtableAll[$type.$int], 'setCryptedFields'))
				$this->_vmpCtableAll[$type.$int]->setCryptedFields($this->_cryptedFields);
			}
			
			
			
		}
		
			
			
			
			
		// some plugins are missing some of the params:
		$this->_vmpCtableAll[$type.$int]->setParameterable($this->_xParams,$this->_varsToPushParam);
		
			
			
		
		
		$x = $this->_vmpCtableAll[$type.$int]->load($int);
		
		
		
		$this->_vmpCtable = $this->_vmpCtableAll[$type.$int];
		if (!empty($x))
		{
		if (!isset($x->payment_logos)) $x->payment_logos = ''; 
		if (!isset($x->cost_per_transaction)) $x->cost_per_transaction = 0; 
		if (!isset($x->cost_percent_total)) $x->cost_percent_total = 0; 
		 
		if (!isset($x->tax_id)) $x->tax_id = -1; 
		if (!isset($x->weight_unit)) $x->weight_unit = 'KG'; 
		
		
		}
		return vmPlugin::setCache($x, 'getVmPluginMethod', $class.$int);
	}

	protected function storeVmPluginMethod () {

	}
	
	/**
	 * This stores the data of the plugin, attention NOT the configuration of the pluginmethod,
	 * this function should never be triggered only called from triggered functions.
	 *
	 * @author Max Milbers
	 * @param array  $values array or object with the data to store
	 * @param string $tableName When different then the default of the plugin, provid it here
	 * @param string $tableKey an additionally unique key
	 */
	protected function storePluginInternalData (&$values, $primaryKey = 0, $id = 0, $preload = FALSE) {
		
		if ($primaryKey === 0) {
			$primaryKey = $this->_tablepkey;
		}
		if ($this->_vmpItable === 0) {
			$this->_vmpItable = $this->createPluginTableObject ($this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable);
		}
		
		//vmdebug('storePluginInternalData',$value);
		$this->_vmpItable->bindChecknStore ($values, $preload);
		
		$errors = $this->_vmpItable->getErrors ();
		
		
		if (!empty($errors)) {
			foreach ($errors as $error) {
				vmError ($error);


			}
		}
		
		return $values;

	}
	
	
	/**
	 * This loads the data stored by the plugin before, NOT the configuration of the method,
	 * this function should never be triggered only called from triggered functions.
	 *
	 * @param int    $id
	 * @param string $primaryKey
	 */
	protected function getPluginInternalData ($id, $primaryKey = 0) {
		$x = vmPlugin::getCache('getPluginInternalData', $this->_vmpItable, $id, $this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable); 
		
		
		
		if (!empty($x)) 
		{
		 return $x; 
		}
		
		if (isset($this->_vmpItable))
		$vmpItableStored = $this->_vmpItable; 
		else $vmpItableStored = null; 
		
		if ($primaryKey === 0) {
			$primaryKey = $this->_tablepkey;
		}
		//if ($this->_vmpItable === 0) 
		{
		
			$this->_vmpItable = $this->createPluginTableObject ($this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable);
		}
		
		$ret = $this->_vmpItable->load ($id);
		vmPlugin::setCache($ret, 'getPluginInternalData',$vmpItableStored, $id, $this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable); 
		
		return $ret; 
		
	}


	

	/**
	 * @param      $tableName
	 * @param      $tableFields
	 * @param      $primaryKey
	 * @param      $tableId
	 * @param bool $loggable
	 * @return VmTableData
	 */
	protected function createPluginTableObject ($tableName, $tableFields, $primaryKey, $tableId, $loggable = FALSE) {

		if (!class_exists ('VmTableData')) {
			require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'vmtabledata.php');
		}
		$db = JFactory::getDBO ();
		$table = new VmTableData($tableName, $tableId, $db);
		foreach ($tableFields as $field) {
			$table->$field = 0;
		}
		/*
		if (substr($tableName, 0, 6) == 'TableS')
		if (!isset($table->shipment_logos)) $table->shipment_logos = ''; 
		if (substr($tableName, 0, 6) == 'TableP')
		if (!isset($table->payment_logos)) $table->payment_logos = ''; 
		
		if (!isset($table->cost_per_transaction)) $table->cost_per_transaction = 0; 
		if (!isset($table->cost_percent_total)) $table->cost_percent_total = 0; 
		*/
		if ($primaryKey !== 0) {
			$table->setPrimaryKey ($primaryKey);
		}
		if ($loggable) {
			$table->setLoggable ();
		}
		
		if($this->_cryptedFields){
			$this->_vmpCtable->setCryptedFields($this->_cryptedFields);
		}
		
		if (!defined('VM_VERSION') || (VM_VERSION < 3)) 
		if (!$this->_tableChecked) {
			$this->onStoreInstallPluginTable ($this->_psType);
			$this->_tableChecked = TRUE;
		}

		return $table;
	}

	/**
	 * @param     $id
	 * @param int $primaryKey
	 * @return mixed
	 */
	protected function removePluginInternalData ($id, $primaryKey = 0) {
		if ($primaryKey === 0) {
			$primaryKey = $this->_tablepkey;
		}
		if ($this->_vmpItable === 0) {
			$this->_vmpItable = $this->createPluginTableObject ($this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable);
		}
		vmdebug ('removePluginInternalData $id ' . $id . ' and $primaryKey ' . $primaryKey);
		return $this->_vmpItable->delete ($id);
	}
	
	public function loadPluginJavascriptOPC(&$cart, &$plugins, &$html)
	{
	  
	   if (!isset($cart->vendorId))
	   {
	     $cart->vendorId = 1; 
	   }

	  
	  $arr = array('payment', 'shipment'); 
	  if (!in_array($this->_psType, $arr)) return; 
	  

	  
	  $nmethods = $this->getPluginMethodsOPC ($cart->vendorId);
	  
	  
	  if (empty($this->methods))
	    {
			
			return;
		}
	 
	 if (!empty($this->methods))
		foreach ($this->methods as &$method)
		{
		  $m->opcref =& $this; 
		  $plugins[] =& $method;   
		
		if (method_exists($this, 'getOpcJavascript')) {
		  $this->getOpcJavascript($method); 
		}
		else
		{
		 if ($this->_psType === 'payment') {
			 $opc_payment_refresh = OPCconfig::get('opc_payment_refresh', false); 
			 if (!empty($opc_payment_refresh)) {
				 return; 
			 }
		 }
			
		 $viewData = $this->pluginViewData; 
		  
		 $htmlIn = ''; 
		 $extra = array(); 
	     OPCTransform::getOverride('opc_javascript', $this->_name, $this->_psType, $this, $method, $htmlIn, $extra, $viewData); 
		}
		
		/*
		$name = $this->_name;
		$psType  = $this->_psType; 
		
		static $theme; 
		if (empty($theme))
		{
		//include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		$selected_template = OPCrenderer::getSelectedTemplate();  
		
		$theme = $selected_template; 
		}
		
		if ($psType === NULL) {
			$psType = $this->_psType;
		}
		
		$layout_name = 'opc_javascript'; 
		
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$psType.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$layout_name.'.php'))
		 {
		  
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$psType.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$layout_name.'.php';
		   $isset = true; 
		 }
		 else
		if (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$psType.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$layout_name.'.php'))
		 {
		  		   $isset = true; 
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$psType.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$layout_name.'.php';
		 }
		 
		 
		 
		 if (!empty($layout)) 
		 {
		 ob_start(); 
		 include($layout); 
		 $html .= ob_get_clean(); 
		 }
		 */
		}
		
	}
	
	/**
	 * Get the path to a layout for a type
	 *
	 * @param   string  $type  The name of the type
	 * @param   string  $layout  The name of the type layout. If alternative
	 *                           layout, in the form template:filename.
	 * @param   array   $viewData  The data you want to use in the layout
	 *                           can be an object/array/string... to reuse in the template
	 * @return  string  The path to the type layout
	 * original from libraries\joomla\application\module\helper.php
	 * @since   11.1
	 * @author Patrick Kohl, Valérie Isaksen
	 */
	 
	var $pluginViewData = array(); 
	public function renderByLayout ($layout_name = 'default', $viewData = NULL, $name = NULL, $psType = NULL) {
		if ($name === NULL) {
			$name = $this->_name;
		}
		
		if (!empty($viewData)) {
			foreach ($viewData as $k=>$v) {
				$this->pluginViewData[$k] = $v; 
			}
		}
		 
    $session = JFactory::getSession(); 
		 $isdisabled = $session->get('disableopc', false); 
		  
		
		
		static $theme; 
		
		// only load OPC if we use it !
		if ((empty($theme) && (class_exists('op_languageHelper'))) && (empty($isdisabled)) )
		{
		//include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'renderer.php'); 
		$selected_template = OPCrenderer::getSelectedTemplate();  
		
		include(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'onepage.cfg.php'); 
		
		
		$theme = $selected_template; 
		}
		else $theme = false; 
		
        
		
		if (isset($this->methods))
		if (count($this->methods)===1)
		{
			$method = $this->methods[0]; 
			
		}
		
		if ($psType === NULL) {
			$psType = $this->_psType;
		}
		$layout = vmPlugin::_getLayoutPath ($name, 'vm' . $psType, $layout_name);
		
		$layout2 = vmPlugin::_getLayoutPath ($name, $psType, $layout_name);
		
		
		
		jimport('joomla.filesystem.file');
		$psType = strtolower($psType); 
		$psType = JFile::makeSafe($psType); 
		$isset = false; 
		
		if (empty($isdisabled)) {
		if ((!empty($theme)) && (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$psType.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$layout_name.'.php')))
		 {
		  
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR.$theme.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$psType.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$layout_name.'.php';
		   $isset = true; 
		 }
		 else
		if ( ((!empty($theme)) || (defined('OPC_VIEW_LOADED'))) && (file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$psType.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$layout_name.'.php')))
		 {
		  		   $isset = true; 
		   $name = JFile::makeSafe($name); 
		   $layout_name = JFile::makeSafe($layout_name); 
		   $layout = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$psType.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$layout_name.'.php';
		

		}
		
		if (!$isset)
		if (strpos($layout, 'payment_form')!==false)
		if (strpos($layout, 'klarna')!==false)
		 {
		   $layout = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$psType.DIRECTORY_SEPARATOR.'klarna'.DIRECTORY_SEPARATOR.'payment_form.php'; 
		 }
		 if (is_array($viewData))
		 if (!empty($viewData['paymnentForm']) && ($viewData['paymentForm']=='#paymentForm'))
		 {
		   $viewData['paymnentForm'] = '#adminForm'; 
		 }
		 if (!$isset)
		 if ((strpos($layout, 'javascript')!==false) && ($name=='stripe'))
		 {
		   $layout = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$psType.DIRECTORY_SEPARATOR.'stripe'.DIRECTORY_SEPARATOR.'javascript.php'; 
		 }
		
		 if (strpos($layout, 'display_payment')!==false)
		 if (strpos($layout, 'ddmandate')!==false)
		 {
		   $layout = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$psType.DIRECTORY_SEPARATOR.'ddmandate'.DIRECTORY_SEPARATOR.'payment_form.php'; 
		 }
		 
		}
		 
		 if ((!file_exists($layout)) && (file_exists($layout2))) $layout = $layout2; 
	     if (file_exists($layout))
		 {
			
			 
		  ob_start ();
		  if (!empty($opc_debug_theme) && class_exists('OPCrenderer')) OPCrenderer::debugTheme($layout); 
		  include ($layout);
		  if (!empty($opc_debug_theme) && class_exists('OPCrenderer')) OPCrenderer::debugTheme($layout); 
		  $html = ob_get_clean ();
		  
		  		

		  
		  
		  return $html; 
		}
		
		return ''; 

	}
	
	
	/**
	 * @param        $pluginName
	 * @param        $group
	 * @param string $layout
	 * @return mixed
	 * @author Valérie Isaksen
	 */
	public function getTemplatePath($pluginName, $group, $layout = 'default') {
		$layoutPath = vmPlugin::_getLayoutPath ($pluginName, 'vm' . $group, $layout);
		return str_replace(DIRECTORY_SEPARATOR . $layout . '.php','',$layoutPath );
	}
	
	
	// not used
	public function plgVmOnSelectCheckDataPaymentOPC($cart, $msg)
	{
		if ($ret === true)
		{
			$method = $this->getVmPluginMethod ($cart->virtuemart_paymentmethod_id); 
				if (!empty($method))
				{
					if (method_exists($this, 'checkConditions'))
					$ret = $this->checkConditions($cart, $method, $cart->cartPrices); 
					if ($ret === false)
					{
						
						require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
				
				$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (!empty($debug_plugins)) {
						
						$classn = get_class($this); 
					if (!empty($classn))
					$msg .= ' Element: '.$classn.', '.$msg; 
			}
						return false; 
					}
				}
		}
	}
	
	// unbelievable, but we need a vm fix here: 
	public function plgVmOnSelectCheckPaymentOPC(&$cart, &$msg)
	 {
		if (empty($cart->virtuemart_paymentmethod_id)) return null; 
	    $ret = $this->plgVmOnSelectCheckPayment($cart, $msg); 
		if ($ret === false) { 
		  if ($this->_name == 'paymill')
		   {
		     if (empty($this->methods)) return null; 
		      
		   }
		  if ($this->_name == 'klikandpay')
		  {
			  if (empty($this->methods)) return null; 
		  }
		   
		   
		   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
				
				$debug_plugins = OPCConfig::get('opc_debug_plugins', false); 
			if (!empty($debug_plugins))  {
		   $classn = get_class($this); 
					if (!empty($classn))
					{
						
					$nmsg = ' Element: '.$classn; 
					if (!empty($msg)) $nmsg .= ', '.$msg; 
					
					$msg = $nmsg; 
					}
			}
		   
		}

		return $ret; 
	 }
	 
	public function __call($method, $arguments)
    {
		if (method_exists($this, $method))
		{
			$r = call_user_func_array(array($this, $method), $arguments); 
			return $r; 
		}
	    else return null; 
		
    }
	
	
	/**
	 *  Note: We have 2 subfolders for versions > J15 for 3rd parties developers, to avoid 2 installers
	 *
	 * @author Patrick Kohl, Valérie Isaksen
	 */
	private function _getLayoutPath ($pluginName, $group, $layout = 'default') {
		$app = JFactory::getApplication ();
		$layoutPath=$templatePathWithGroup=$defaultPathWithGroup='';
		// get the template and default paths for the layout
		if (JVM_VERSION >= 2) {
			$templatePath = JPATH_SITE . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $app->getTemplate () . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . $layout . '.php';
			$defaultPath = JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR . $layout . '.php';
		}
		else {
			$templatePath = JPATH_SITE . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $app->getTemplate () . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . $layout . '.php';
			$defaultPath = JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $group . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR . $layout . '.php';
		}
		
		if (defined('VM_VERSION') && (VM_VERSION >= 3))
		{
			if(!class_exists('VmTemplate')) 
				require(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmtemplate.php');
			if (method_exists('VmTemplate', 'loadVmTemplateStyle'))
			{
			$vmStyle = VmTemplate::loadVmTemplateStyle();
		    $template = $vmStyle['template'];
			}
			else
			{
				$template = JFactory::getApplication ()->getTemplate (); 
			}
		
		   $layoutPath=$templatePathWithGroup=$defaultPathWithGroup='';
		jimport ('joomla.filesystem.file');
		// First search in the new system
		
		$template = JFile::makeSafe($template); 
		$group = JFile::makeSafe($group); 
		$layout = JFile::makeSafe($layout); 
		$pluginName = JFile::makeSafe($pluginName); 
		$type = $this->_psType; 
		$type = JFile::makeSafe($type); 
		
		$session = JFactory::getSession(); 
		 $isdisabled = $session->get('disableopc', false); 
		 if (empty($isdisabled)) {
		$OPCpath = JPATH_SITE.DIRECTORY_SEPARATOR. 'components' .DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'overrides'.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR. $pluginName .DIRECTORY_SEPARATOR. $layout . '.php';
		if (file_exists($OPCpath)) {
			$layoutPath = $OPCpath; 
			return $layoutPath; 
		}
		 }
			
		$templatePath1         = JPATH_SITE.DIRECTORY_SEPARATOR. 'templates' .DIRECTORY_SEPARATOR. $template .DIRECTORY_SEPARATOR. 'html' .DIRECTORY_SEPARATOR. $group .DIRECTORY_SEPARATOR. $pluginName .DIRECTORY_SEPARATOR. $layout . '.php';
		$defaultPath2          = JPATH_SITE.DIRECTORY_SEPARATOR. 'plugins' .DIRECTORY_SEPARATOR. $group .DIRECTORY_SEPARATOR. $pluginName .DIRECTORY_SEPARATOR. 'tmpl' .DIRECTORY_SEPARATOR. $layout . '.php';
		$defaultPathWithGroup = JPATH_SITE.DIRECTORY_SEPARATOR. 'plugins' .DIRECTORY_SEPARATOR. $group .DIRECTORY_SEPARATOR. $pluginName .DIRECTORY_SEPARATOR. $pluginName .DIRECTORY_SEPARATOR. 'tmpl' .DIRECTORY_SEPARATOR. $layout . '.php';
		
		if (file_exists ($templatePath1)) {
			$layoutPath= $templatePath1;
		} 
		elseif (file_exists($templatePath)) {
			$layoutPath= $templatePath;
		}
		elseif (file_exists($defaultPath2)) {
			$layoutPath= $defaultPath2;
		}elseif (file_exists($defaultPathWithGroup)) {
			$layoutPath = $defaultPathWithGroup;
		}
		
		if (!empty($layoutPath)) {
		
			return $layoutPath;
			
		}
		    
		  
		}


		// if the site template has a layout override, use it
		jimport ('joomla.filesystem.file');
		if (file_exists($templatePath)) {
			return $templatePath;
		}
		else {
			return $defaultPath;
		}
	}
	public static function pBS($die=true)
	{
		$x = debug_backtrace(); 
		echo 'Bakctrace'."<br />\n"; 
		foreach ($x as $l) echo $l['file'].' '.$l['line']."<br />\n"; 
		if ($die)
		{
		JFactory::getApplication()->close(); 
	    die(); 
		}
	}
	/*
	function plgVmOnSelectedCalculatePriceShipmentOPC (VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) 
	{
		// if ($cart->virtuemart_shipmentmethod_id==10)
			{
				$cart_prices['shipmentTax'] = 1;
		$cart_prices['shipmentValue'] = 10; 
		
		return true; 
			}
		if (!($method = $this->getVmPluginMethod ($cart->virtuemart_shipmentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		 
		
		if (!$this->selectedThisElement ($method->shipment_element)) {
			return FALSE;
		}
		

		$cart_prices['shipmentTax'] = 1;
		$cart_prices['shipmentValue'] = 10; 
		
		return true; 

		
		
	}
    */
	
	function plgVmgetEmailCurrency($virtuemart_paymentmethod_id, $virtuemart_order_id, &$emailCurrencyId) {

		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		
		$default = false; 
		$config = OPCconfig::get('override_payment_currency', $default); 
		$virtuemart_order_id = (int)$virtuemart_order_id; 
		
		if (!empty($virtuemart_order_id))
		if (!empty($config))
		{
			$db = JFactory::getDBO(); 
			$q = 'select `user_currency_id` from #__virtuemart_orders where virtuemart_order_id = '.(int)$virtuemart_order_id; 
		    $db->setQuery($q); 
			$c = $db->loadResult(); 
			if (!empty($c))
			{
				$emailCurrencyId = $c; 
				return $c; 
			}
			
		}
		

	}
	
	function plgVmOnCheckoutAdvertiseOPC($cart, &$payment_advertise) {
	  if ((!empty($this->_psType)) && (!empty($this->_name)))
	  {
		  $layout_name = 'advertise'; 
		  $type = $this->_psType; 
		  $nmethods = $this->getPluginMethodsOPC ($cart->vendorId);
		  if (!empty($this->methods))
	      {
			
			$method = reset($this->methods); 
			
		    require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'transform.php');
			$html = ''; 
			$extra = array(); 
			$extra['cart'] =& $cart; 
			OPCTransform::getOverride('advertise', $this->_name, $this->_psType, $this, $method, $html, $extra); 
			
			if (!empty($html)) {
			  if (is_array($html))
			  {
			   foreach ($html as $h) { 
			   $hash = md5($h); 
			   $payment_advertise[$hash] = $h; }
			  }
			  else
			  {
				  $hash = md5($html); 
				  $payment_advertise[$hash] = $html; 
			  }
			  return; 
			}
			
			
		  }
	  }
	  
	  if (method_exists($this, 'plgVmOnCheckoutAdvertise'))
	  {
		  $saved = $payment_advertise; 
		  $this->plgVmOnCheckoutAdvertise($cart, $payment_advertise); 
		  if (!is_array($payment_advertise))
		  {
			  
			  $payment_advertise = $saved; 
		  }
		  
	  }
	  
	}
	
	
	/**
	 * This function gets the parameters of a plugin from the given JForm $form.
	 * This is used for the configuration GUI in the BE.
	 * Attention: the xml Params must be always a subset of the varsToPushParams declared in the constructor
	 * @param $form
	 * @return array
	 */
	static public function getVarsToPushFromForm ($form){
		$data = array();

		$fieldSets = $form->getFieldsets();
		foreach ($fieldSets as $name => $fieldSet) {
			foreach ($form->getFieldset($name) as $field) {

				$fieldname = (string)$field->fieldname;
				$private = false;

				if(strlen($fieldname)>1){
					if(substr($fieldname,0,2)=='__'){
						$private = true;
					}
				}

				if(!$private){
					$type='char';
					$data[$fieldname] = array('',  $type);
				}

			}
		}

		return $data;
	}
	function plgVmDeclarePluginParamsPaymentVM3( &$data) {
		if (is_callable($this, 'declarePluginParams'))
		return $this->declarePluginParams('payment', $data);
	}
	function plgVmDeclarePluginParamsShipmentVM3( &$data) {
		if (is_callable($this, 'declarePluginParams'))
		return $this->declarePluginParams('shipment', $data);
	}
	
	
}


class OPCPluginLoaded {
}