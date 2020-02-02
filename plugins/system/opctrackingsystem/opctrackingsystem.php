<?php
/**
 * @version		One page checkout for Virtuemart - plugins gallery
 * @copyright	Copyright (C) 2005 - 2011 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');


class plgSystemOpctrackingsystem extends JPlugin
{
   public static $_storedOrder; 
   public static $_product_impressions; 
   public static $_orderstatus; 
   public static $_js; 
   
    public function onAfterInitialise()
	{
		if (!self::_check())  return; 
		
		OPCtrackingHelper::$html = ''; 
		OPCtrackingHelper::$js = ''; 
		OPCtrackingHelper::$html_array = array(); 
		
		
		
	}
	
	public function hasVM() {
		static $c; 
		if (isset($c)) return $c; 
		if (file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php')) {
			$c = true; 
		}
		else $c = false; 
		return $c; 
	}
	
	public function plgVmOnUpdateOrderPayment(&$data,$old_order_status)
	{
	
	
	
	
	  $app = JFactory::getApplication();
		if ($app->getName() != 'administrator') {
			return null;
		}
		
		
		if (is_object($data)) {
		
		$virtuemart_order_id = $data->virtuemart_order_id; 
		$order_status = $data->order_status; 
		$session = JFactory::getSession(); 
		$dataT = $session->get('opctrackingbe', array()); 
		$dataT[$virtuemart_order_id] = $order_status; 
		$session->set('opctrackingbe',$dataT); 
		
		}
	  
	  
	  
	  
	}
	
	
	public function plgVmConfirmedOrderOPCSystem($cart, $order) { 
	   static $done; 
	   if (!empty($done)) return; 
	   
	   $this->plgVmConfirmedOrder2($cart, $order); 
	   
	   $done = true; 
	}
	public function plgVmBeforeProductSearch(&$select, &$joinedTables, &$where, &$groupBy, &$orderBy,&$joinLang) {
		return; 
				

	}
	public function plgVmInterpreteMathOp ($calculationHelper, $rule, $price,$revert){
		
	   $this->plgVmInGatherEffectRulesProduct($calculationHelper, $rule); 
	   
	   return null; 
	}
	
	//this is only triggered in BE !
	public function plgVmCouponUpdateOrderStatus($data,$old_order_status)
	{
	  	$app = JFactory::getApplication();
		$name = $app->getName();
		if ($name == 'administrator') {
			
		
		
		
	  if (!self::_check2()) {
		  return; 
	  }
	  self::getIncludes(); 
	  
	  if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
  
	  
	 
	  plgSystemOpctrackingsystem::orderCreated($data, $old_order_status);  
	  
	  
	  
	  }
	}
	
	static $current_products; 
	public function plgVmInGatherEffectRulesProduct(&$calculationHelper,&$rules) {

	  if (isset($calculationHelper->_product)) {
		 if (empty(plgSystemOpctrackingsystem::$_product_impressions))
			 plgSystemOpctrackingsystem::$_product_impressions = array(); 

	    $p = $calculationHelper->_product->virtuemart_product_id; 
		$this->onTrackingOPCEvent('Product Impression', $p); 
		//plgSystemOpctrackingsystem::$_product_impressions[$p] = $p; 
		
		self::$current_products[] = $calculationHelper->_product; 
	  }
	  
	  
	  
	  
	
	}
	
	//event name to be used in the GTM Lists 
	// ids can be: 
	// --- array of product IDs
	// --- array of TableProducts (or derived object with obj->virtuemart_product_id
	// --- single product_id
	// --- single TableProducts or derived object with obj->virtuemart_product_id
	public function onTrackingOPCEvent($eventName, $ids) {
	   if (empty(plgSystemOpctrackingsystem::$_product_impressions)) plgSystemOpctrackingsystem::$_product_impressions = array(); 
	   if (empty(plgSystemOpctrackingsystem::$_product_impressions[$eventName])) plgSystemOpctrackingsystem::$_product_impressions[$eventName] = array(); 
	   if (is_array($ids)) {
		 foreach ($ids as $k=>$v) {
			 if ((!is_object($v)) && (!is_array($v))) {
				 $v = (int)$v; 
				 if (!empty($v)) {
	               plgSystemOpctrackingsystem::$_product_impressions[$eventName][$v] = $v; 
				 }
			 }
			 else
			 {
				 if ((is_object($v)) && (isset($v->virtuemart_product_id))) {
				    $id = (int)$v->virtuemart_product_id; 
					if (!empty($id)) {
					   plgSystemOpctrackingsystem::$_product_impressions[$eventName][$id] = $id; 
					}
				 }
			 }
		 }
	   }
	   else
	   {
		   if ((!empty($ids)) && (is_numeric($ids))) {
		      plgSystemOpctrackingsystem::$_product_impressions[$eventName][$ids] = (int)$ids; 
		   }
		   else
			   if (is_object($ids) && (isset($ids->virtuemart_product_id))) {
			        $id = (int)$ids->virtuemart_product_id; 
					if (!empty($id)) {
					   plgSystemOpctrackingsystem::$_product_impressions[$eventName][$id] = $id; 
					}
			   }
	   }
	}
	
	
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{

		if (!$this->hasVM()) return; 
		$class = get_class($article); 
		
		if (($class == 'TableProducts') || (($class == 'stdClass') && (isset($article->virtuemart_product_id)))) {
		   //self::toObject($article); 
		   if (isset($article->virtuemart_product_id)) {
			   if (!empty($article->virtuemart_product_id)) {
					$virtuemart_product_id = (int)$article->virtuemart_product_id; 
					$this->onTrackingOPCEvent('Product Details', $virtuemart_product_id);
					//plgSystemOpctrackingsystem::$_product_impressions[$virtuemart_product_id] = $virtuemart_product_id; 
			   }
		   }
		   
		}
		
		if (($class == 'TableCategories')) {
		   //self::toObject($article); 
		   
		   if (isset($article->virtuemart_product_id)) {
			   if (!empty($article->virtuemart_product_id)) {
					$virtuemart_product_id = (int)$article->virtuemart_product_id; 
					$this->onTrackingOPCEvent('Product Details', $virtuemart_product_id);
					//plgSystemOpctrackingsystem::$_product_impressions[$virtuemart_product_id] = $virtuemart_product_id; 
			   }
		   }
		   
		}
		
	}
	
	
	
	
	public function plgVmConfirmedOrder($cart, $order)
	{
	 $this->plgVmConfirmedOrder2($cart, $order); 
	}
	
	public function plgVmConfirmedOrder2($cart, $data)
	{
		

		
	  if (!self::_check()) 
	  {
		  return; 
	  }
	 	
		
	  if (class_exists('plgVmPaymentOpctracking'))
	  if (!empty(plgVmPaymentOpctracking::$_storedOrder)) 
		  self::$_storedOrder = plgVmPaymentOpctracking::$_storedOrder; 
  
  
  
	  if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
	  
	  
	  
	  if (defined('OPCTRACKINGORDERCREATED')) return; 
	  
	  
	  
	  self::_tyPageMod($data, false);  
	  
	  
	  define('OPCTRACKINGORDERCREATED', 1); 
	 
	  
	  
	  if (!is_object($data))
	  {
	  if (isset($data['details']['BT']))
	  {
	   //self::$delay = true; 
	   $order = $data['details']['BT']; 
	   
	   self::orderCreated($order, 'P');  
	  }
	  }
	  else
	  if (isset($data->virtuemart_order_id))
	  {
		
		   if (isset($data->order_status)) $status = $data->order_status; 
	       else $status = 'P'; 
		  
	       
		   if (class_exists('plgSystemOpctrackingsystem'))
	       if (method_exists('plgSystemOpctrackingsystem', 'orderCreated'))
	       plgSystemOpctrackingsystem::orderCreated($data, $status);  
		   
		  
		  
		  
	  }
	}
	
	public static function getChangedStamp($virtuemart_order_id, $order_status) {
		
		$order_status = strtoupper($order_status); 
		
		$db = JFactory::getDBO(); 
		$q = 'select `modified_on` from `#__virtuemart_order_histories` where `virtuemart_order_id` = '.(int)$virtuemart_order_id." and order_status_code = '".$db->escape($order_status)."' order by `modified_on`, `created_on` desc limit 1"; 
		$db->setQuery($q); 
		$date = $db->loadResult(); 
		if (empty($date)) return ''; 
		$date = (string)$date; 
		return $date; 
		
		
		
	}
	
	public static function setCurrentOrderStatus(&$data) {
		if (empty(self::$_orderstatus)) self::$_orderstatus = array(); 
		
		   if (!empty($data)) {
			   if (is_array($data) && (isset($data['order_status'])) && (isset($data['virtuemart_order_id']))) {
					   $virtuemart_order_id = (int)$data['virtuemart_order_id']; 
					   $order_status = $data['order_status']; 
					   $stamp = self::getChangedStamp($virtuemart_order_id, $order_status); 
					   self::$_orderstatus[$virtuemart_order_id][$order_status] = $stamp; 
				  
			   }
			   elseif (is_object($data) && (isset($data->order_status)) && (isset($data->virtuemart_order_id))) {
						   $order_status = $data->order_status;
						   $virtuemart_order_id = (int)$data->virtuemart_order_id;
						   $stamp = self::getChangedStamp($virtuemart_order_id, $order_status); 
						   self::$_orderstatus[$virtuemart_order_id][$order_status] = $stamp; 
				   }
				  elseif ((is_array($data) && (isset($data['details']['BT'])) && (isset($data['details']['BT']->virtuemart_order_id)))) {
					   if (isset($data['details']['BT']->order_status)) {
						   $order_status = $data['details']['BT']->order_status;
						   $virtuemart_order_id = (int)$data['details']['BT']->virtuemart_order_id;
						   $stamp = self::getChangedStamp($virtuemart_order_id, $order_status); 
						   self::$_orderstatus[$virtuemart_order_id][$order_status] = $stamp; 
					   }
			   
		   }
		   }
	}
	
	public static function orderCreated(&$data, $old_order_status)
	{
	  
	 $app = JFactory::getApplication();
		$name = $app->getName();
		if ($name == 'site') {
	  
	  $hash2 = uniqid('opc', true); 
	  if (method_exists('JApplication', 'getHash'))
	  $hashn = JApplication::getHash('opctracking'); 
	  else $hashn = JUtility::getHash('opctracking'); 
	  $hash = JRequest::getVar($hashn, $hash2, 'COOKIE'); 
      if ($hash2 == $hash) 
	  OPCtrackingHelper::setCookie($hash); 
		
		
		self::setCurrentOrderStatus($data); 
		
		}
		else {
			
			$oa = OPCtrackingHelper::getOrderIdandStatus($data); 
			if (empty($oa)) return; 
			$order_id = (int)$oa['order_id']; 
			$order_status = $oa['order_status']; 
			$session = JFactory::getSession(); 
			$data = $session->get('opctrackingbe', array()); 
			$data[$order_id] = $order_status; 
			$session->set('opctrackingbe',$data); 
			
			return; 
			
			
		}
	    
		OPCtrackingHelper::orderCreated($hash, $data, $old_order_status); 
	
		//OPC add-on: if any other plugin updates user data, they should get refreshed: 
			// refresh user data: 
				$user = JFactory::getUser(); 
				$id = $user->id; 
				$user = new JUser($id); 
				$session = JFactory::getSession(); 
				$session->set('user', $user); 
				// end of refresh
			
		 self::_tyPageMod($data, false);  
	}
	
	public $todo = array(); 
    public function onAfterRoute() {
		if (!$this->hasVM()) return; 
		$app = JFactory::getApplication();
		
		if ((!$app->isAdmin()) && (class_exists('OPCtrackingHelper') && (method_exists('OPCtrackingHelper', 'loadAsFirstPageEventAlways'))))
	    {
			
		OPCtrackingHelper::loadAsFirstPageEventAlways(); 
		}
		

		
	     if (!self::_check())  return; 
		 JHTMLOPC::script('opcping.js', 'components/com_onepage/assets/js/', false);
	
		if (empty($this->todo))
		$this->todo = array(); 
	
		//check product view: 
		$option = JRequest::getVar('option', ''); 
		$view = JRequest::getVar('view'); 
		$virtuemart_product_id = JRequest::getVar('virtuemart_product_id', 0); 
		$virtuemart_category_id = JRequest::getVar('virtuemart_category_id', 0); 
		
		$virtuemart_product_id = (int)$virtuemart_product_id; 
		$virtuemart_category_id = (int)$virtuemart_category_id; 
		
		if ($option === 'com_virtuemart')
		{
		if ($view === 'productdetails')
		{
		if (!empty($virtuemart_product_id))
		 {
		    // in case a broken cross version matrix: 
			/*
		    if (method_exists('OPCtrackingHelper', 'productViewEvent'))
		    OPCtrackingHelper::productViewEvent($virtuemart_product_id); 
			*/
			
			if (empty($this->todo['productViewEvent'])) $this->todo['productViewEvent'] = array(); 
			$this->todo['productViewEvent'][$virtuemart_product_id] = $virtuemart_product_id; 
		
		
		 }
		}
		else
		if ($view === 'cart')
		 {
			  /*
			 if (method_exists('OPCtrackingHelper', 'cartViewEvent'))
		     OPCtrackingHelper::cartViewEvent(); 
		     */
			 
		 	 if (empty($this->todo['cartViewEvent'])) $this->todo['cartViewEvent'] = array(); 
			 $this->todo['cartViewEvent'][1] = 1; 

		 
		 }
		else
			if ($view == 'category')
			{
				
				if (empty($this->todo['categoryViewEvent'])) $this->todo['categoryViewEvent'] = array(); 
			    $this->todo['categoryViewEvent'][1] = 1; 
			}
		}
		
		
		 
		 
		
	
	
	}
	
	public function onAfterDispatch() {
	  if (!$this->_check()) return; 
	  
	   if (!empty($this->todo))
	   {
		    foreach ($this->todo as $k=>$v)
			{
				  if ($k === 'productViewEvent')
				  {
					   foreach ($v as $n)
					   {
						    if (method_exists('OPCtrackingHelper', 'productViewEvent'))
							OPCtrackingHelper::productViewEvent($n); 
					   }
					   unset($this->todo[$k]); 
				  }
				  else
					  if ($k === 'cartViewEvent')
					  {
						  if (method_exists('OPCtrackingHelper', 'cartViewEvent'))
						  OPCtrackingHelper::cartViewEvent(); 
					  
						 unset($this->todo[$k]); 
					  }
					  else
						  if ($k === 'categoryViewEvent')
						  {
							  
							  
							  if (method_exists('OPCtrackingHelper', 'categoryViewEvent'))
							  OPCtrackingHelper::categoryViewEvent(); 
						  
						      unset($this->todo[$k]); 
						  }
						   
						 
			}
	   }
	
	}
	
	public function onBeforeRender() {
		if (!$this->hasVM()) return; 
		
		
			$app = JFactory::getApplication();
		$name = $app->getName();
		
		
		
	if (!empty(plgSystemOpctrackingsystem::$_product_impressions)) {
		   $this->todo['imprViewEvent'] = 1; 
	   }
	   
	   
	   
	    if (!empty($this->todo))
	   {
		    foreach ($this->todo as $k=>$v)
			{
				  if ($k === 'productViewEvent')
				  {
					   foreach ($v as $n)
					   {
						    if (method_exists('OPCtrackingHelper', 'productViewEvent'))
							OPCtrackingHelper::productViewEvent($n); 
					   }
					    unset($this->todo[$k]); 
				  }
				  else
					  if ($k === 'cartViewEvent')
					  {
						  if (method_exists('OPCtrackingHelper', 'cartViewEvent'))
						  OPCtrackingHelper::cartViewEvent(); 
					      unset($this->todo[$k]); 
					  }
					  else
						  if ($k === 'categoryViewEvent')
						  {
							  if (method_exists('OPCtrackingHelper', 'categoryViewEvent'))
							  OPCtrackingHelper::categoryViewEvent(); 
							   unset($this->todo[$k]); 
						  }
						  else 
						  if ($k === 'imprViewEvent')
						  {
							  if (method_exists('OPCtrackingHelper', 'imprViewEvent'))
							  OPCtrackingHelper::imprViewEvent(); 
						  
						      unset($this->todo[$k]); 
						  }
			}
	   }
	   
	   if (!empty(self::$_orderstatus)) {
		   
		   
		   
		    if (self::_check()) {
				
				if (!empty(plgSystemOpctrackingsystem::$_js)) {
					$reversed = array_reverse(plgSystemOpctrackingsystem::$_js);
					$doc = JFactory::getDocument(); 
					
					$orderdata = array(); 
					$orderdata['orderdata'] = self::$_orderstatus; 
					$jsdata = ' var opc_current_order = '.json_encode($orderdata).';'; 
					$jsdata .= ' if ((typeof console !== \'undefined\') && (typeof console.log !== \'undefined\')) console.log(\'Detected Order Status\', opc_current_order);  '; 
					$doc->addScriptDeclaration($jsdata); 
					
					foreach ($reversed as $js) {
						$doc->addScriptDeclaration($js); 
					}
					
				}
			}
	   }
	   
	
	}
	
	

	public function onAfterRender()
	{
		
		if (!$this->hasVM()) return; 
		
		$app = JFactory::getApplication();
		$name = $app->getName();
		if ($name == 'administrator') {
		
		
				if (!self::_check2()) {
					return; 
				}
				self::getIncludes(); 
				if (!OPCtrackingHelper::checkStatusBE()) {
					return; 
				}
				else {
					
				if (method_exists('OPCtrackingHelper', 'loadAsLastPageEvent'))
				OPCtrackingHelper::loadAsLastPageEvent(); 	
				
				 $this->_updateHtml(); 
				}
				// trigger admin trackers: 
				
		}
		
		//opc tracking start
		 //if (!empty(self::$delay)) return; 
	   
	   if (!$this->_check()) return; 

	   
	   
	   
	   if (!empty($this->todo))
	   {
		    foreach ($this->todo as $k=>$v)
			{
				  if ($k === 'productViewEvent')
				  {
					   foreach ($v as $n)
					   {
						    if (method_exists('OPCtrackingHelper', 'productViewEvent'))
							OPCtrackingHelper::productViewEvent($n); 
					   }
				  }
				  else
					  if ($k === 'cartViewEvent')
					  {
						  if (method_exists('OPCtrackingHelper', 'cartViewEvent'))
						  OPCtrackingHelper::cartViewEvent(); 
					  }
					  else
						  if ($k === 'categoryViewEvent')
						  {
							  if (method_exists('OPCtrackingHelper', 'categoryViewEvent'))
							  OPCtrackingHelper::categoryViewEvent(); 
						  }
						  else 
						  if ($k === 'imprViewEvent')
						  {
							  if (method_exists('OPCtrackingHelper', 'imprViewEvent'))
							  OPCtrackingHelper::imprViewEvent(); 
						  
						      unset($this->todo[$k]); 
						  }
			}
	   }
	   
	   
	   
	   $this->_opcTrackingCheck(); 
	   
	   if (method_exists('OPCtrackingHelper', 'loadAsLastPageEvent'))
	   OPCtrackingHelper::loadAsLastPageEvent(); 
	   
	   
	   $this->_updateHtml(); 
	  
	}
	// this function is triggered from ajax content to update abandoned cart measurement and cart data
	public static function updateAbaData()
	{
	 
	   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'aba.php'); 
	   OPCAba::update(); 
	}
	// this function pairs user with an order for abandoned cart measurement
	public static function registerOrderAttempt(&$order)
	{
	   if (!self::_check()) return; 
	   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'aba.php'); 
	   OPCAba::orderMade($order); 
	}
	
	public static function registerCart()
	{
	   
	   if (!self::_check()) return; 
	   if (!class_exists('OPCtrackingHelper')) return;
	   $hash2 = uniqid('opc', true); 
	   $hashn = JApplication::getHash('opctracking'); 
	   $hash = JRequest::getVar($hashn, $hash2, 'COOKIE'); 
	   if ($hash2 == $hash)
	   {
	   // create new cookie if not set
	   OPCtrackingHelper::setCookie($hash); 
	   }
	   
	   OPCtrackingHelper::registerCart($hash); 
	}
	
		// this function is triggered on OPC cart display
	public static function registerCartEnter()
	{
	
	   if (!self::_check()) return; 
	   require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'aba.php'); 
	   OPCAba::cartEnter(); 
	}

	
   private function _opcTrackingCheck()
	{
	   if (self::_check()) 
	   {
	   
	   
	   
	   if (!empty(self::$_storedOrder))
	   {
	     self::_tyPageMod(self::$_storedOrder, true); 
	   }
	   
	   
	   
	   require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php'); 
	   
	   //if (!class_exists('OPCtracking')) return; 
	   
	   
	   if (class_exists('OPCtrackingHelper'))
	   {
	   
	   
	   
	   if (!OPCtrackingHelper::checkStatus()) return; 
	 

	 
	  
	 
	   }
	   }	
	}
	
	private function _updateHtml()
	{
		
		
		
		 if (class_exists('OPCtrackingHelper'))
		 {
		  $html = OPCtrackingHelper::$html; //OPCtrackingHelper::getHTML(); 
		  $buffer = JResponse::getBody();
		  $changed = false; 
	      if (!empty($html)) 
	      {
	      
	      //$bodyp = stripos($buffer, '</body'); 
		  
		  $body1 = stripos($buffer, '<body'); 
		  if ($body1 !== false)
		  {
			  $body2 = stripos($buffer, '>', $body1); 
			  if ($body2 !== false)
			  {
				  $changed = true; 
				   $buffer = substr($buffer, 0, $body2+1).$html.substr($buffer, $body2+1); 
				   
				   
			  }
		  }
		  }
		  
		  $js = OPCtrackingHelper::$js; //OPCtrackingHelper::getHTML(); 
		  
		  
	      if (!empty($js))
	      {
			  
	      
	      $bodyp = stripos($buffer, '</head'); 
		  if ($bodyp !== false)
		  {
			  $changed = true; 
			     $buffer = substr($buffer, 0, $bodyp).$js.substr($buffer, $bodyp); 
		  }
		  

		  }
		  
	      //$buffer = substr($buffer, 0, $bodyp).$html.substr($buffer, $bodyp); 
	   
	      if ($changed)
	      JResponse::setBody($buffer);
		 }
	}
	private static function _check2() {
		$debug = false; 
		$format = JRequest::getVar('format', 'html'); 
		if ($format != 'html') 
		{	
		
		return false;
		}
		
		$doc = JFactory::getDocument(); 
		$class = strtoupper(get_class($doc)); 
		
		
		
		$arr = array('JDOCUMENTHTML', 'JDOCUMENTOPCHTML', 'JOOMLA\CMS\DOCUMENT\HTMLDOCUMENT', 'JOOMLA\CMS\DOCUMENT\OPCHTMLDOCUMENT'); 
		if (!in_array($class, $arr)) 
		{
		
		
		return false; 
		}
		
		 if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php')) 
		{
			if ($debug) die('VM'); 
		
		return false;
		}
		 if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php')) 
		{
			if ($debug) die('VM'); 
		
		return false;
		}
		
		// opc component not available
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php')) 
		{
			if ($debug) die('opc PLUGIN'); 
		
		return false;
		}
		
		return true; 
	}
   private static function _check($allowAdmin=false)
	{
		if (php_sapi_name() === 'cli') return false; 
		
		if (class_exists('plgVmPaymentOpctracking'))
	    if (!empty(plgVmPaymentOpctracking::$_storedOrder)) 
		self::$_storedOrder = plgVmPaymentOpctracking::$_storedOrder; 
		
		$debug = false; 
		
	    if (defined('OPC_GENERAL_CHECK')) 
		{
			return OPC_GENERAL_CHECK; 
		}
	
		if (!file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php')) {
			define('OPC_GENERAL_CHECK', false); 
			return false;
		}
	
	
		if (!$allowAdmin)
		{
	  	$app = JFactory::getApplication();
		if ($app->getName() != 'site') {
			if ($debug) die('site'); 
			define('OPC_GENERAL_CHECK', false); 
			return false;
		}
		}
		
		$state2 = self::_check2(); 
		if (!$state2) {
			define('OPC_GENERAL_CHECK', false); 
			return false; 
		}
		
		
		
		self::getIncludes(); 
		
		
		
		
		
		
		
		define('OPC_GENERAL_CHECK', true); 
		return true; 

	}
	
	public static function getIncludes() {
		JLoader::register('OPCplugin', JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plugin.php' );
	    
		
		//load opc compatibility files
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
		
		JLoader::register('OPCconfig', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php' );
		
		JLoader::register('OPCtrackingHelper', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'opctracking.php' );
	}
	
		
	static function _tyPageMod(&$data, $afterrender=false, $html='')
	{
		
		if (empty(self::$_storedOrder)) self::$_storedOrder = $data; 
		if (empty($html))
		$html = JRequest::getVar('html', '', 'default', 'STRING', JREQUEST_ALLOWRAW);
		
		
		
		if (!empty($html))
		{
		
		   if (file_exists(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'thankyou.php')) 
		    {
			  require_once(JPATH_SITE. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_onepage'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'thankyou.php'); 
			  return OPCThankYou::updateHtml($html, $data, $afterrender); 
			}
		}
		
				$user = JFactory::getUser(); 
				$id = $user->id; 
				$user = new JUser($id); 
				$session = JFactory::getSession(); 
				$session->set('user', $user); 

		
		return ''; 
	}
	
	
	
	
}