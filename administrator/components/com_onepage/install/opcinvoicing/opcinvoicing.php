<?php
/**
 * @version		opcinvoicing.php 
 * @copyright	Copyright (C) 2005 - 2015 RuposTel.com
 * @license		COMMERCIAL !
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemOpcinvoicing extends JPlugin
{
    function __construct(& $subject, $config)
	{
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php')) return; 
		
		parent::__construct($subject, $config);
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 
	}
	
	public function onAfterDispatch() {
		$app = JFactory::getApplication(); 
		$isSite = $app->isSite(); 
		if (empty($isSite)) { 
		   $view = JRequest::getVar('view', ''); 
		   $option = JRequest::getVar('option', ''); 
		   $layout = JRequest::getVar('layout', ''); 
		   $format = JRequest::getVar('format', 'html'); 
		   $tmpl = JRequest::getVar('tmpl', ''); 
		   if (($view === 'orders') && ($option === 'com_virtuemart') && ($layout === '') && ($format === 'html') && ($tmpl === '')) {
			
			if (!class_exists('VmConfig'))
		    require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
		 
		    VmConfig::loadConfig(); 
			
			if (class_exists('vmAccess')) {
			if (method_exists('vmAccess', 'manager'))
			if (vmAccess::manager('orders.delete')) {
			
			require_once ( JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
	$ehelper = new OnepageTemplateHelper();
	$templates = $ehelper->getExportTemplates('ALL', true);
			
			if (!empty($templates)) {

			// Toolbar object
$toolbar = JToolBar::getInstance('toolbar');
$layout = new JLayoutFile('joomla.toolbar.popup');

// Render the popup button
$dhtml = $layout->render(array('name' => 'test', 'text' => 'OPC', 'class' => 'icon-archive'));
$toolbar->appendButton('Custom', $dhtml);
			
			JFactory::getLanguage()->load('com_onepage'); 
			
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'compatibility.php'); 

			JHTMLOPC::script('opcbe.js', 'administrator/components/com_onepage/assets/js/', false);
			JHTMLOPC::script('onepage_ajax.js', 'administrator/components/com_onepage/assets/js/', false);
			
		  ob_start(); 
		  ?><div class="modal hide fade" id="modal-test">
  <div class="modal-header">
    <button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
    <h3><?php echo JText::_('COM_ONEPAGE_ORDER_MANAGEMENT'); ?></h3>
  </div>
  <div class="modal-body">
  <h2><?php echo JText::_('COM_ONEPAGE_UTILS_ACTION'); ?></h2>
  <?php foreach ($templates as $tid) {
    ?><p><a class="btn btn-primary" style="float: left;" href="#" onclick="javascript:return vmProcessOrders(this);" data-tid="<?php echo htmlentities(json_encode($tid)); ?>"><?php echo $tid['tid_name']; ?></a><br style="clear: both;"/></p>
  <?php } ?>
  <form name="opcactions" id="opcactions" method="post" action="index.php">
    <input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="view" value="order_details" />
<input type="hidden" name="task" value="ajax" />
<input type="hidden" name="ajax" value="yes" />
<input type="hidden" name="doaction" value="" />
<input type="hidden" name="order_number" value="0" />
<input type="hidden" name="tid" id="opctid" value="" />
<input type="hidden" name="cmd" value="sendxmlmulti" /><?php
  $orderModel = VmModel::getModel('Orders'); 
  $orderModel->search = str_replace('%', ' ', $orderModel->search); 
  $orderModel->search = str_replace('"', '', $orderModel->search); 
?>
<input type="hidden" name="vmlimit" value="<?php echo (int)$orderModel->_limit; ?>" />
<input type="hidden" name="vmlimitstart" value="<?php echo (int)$orderModel->_limitStart; ?>" />
<input type="hidden" name="vmsearch" value="<?php echo htmlentities(trim($orderModel->search)); ?>" />

<div style="display:none;" id="opc_inject_here">&nbsp;</div>
  </form>
  </div>
  <div class="modal-footer">
    <button class="btn" type="button" data-dismiss="modal">
      <?php echo JText::_('JCANCEL'); ?>
    </button>
  </div>
</div>
<?php 
$html = ob_get_clean(); 
self::$appendHTML = $html;
			}
			
			}
		   }
		}
		}
	}
	
	private static $appendHTML; 
	public function onAfterRender() {
	   if (!empty(self::$appendHTML)) {
		   $html = self::$appendHTML;
		   $buffer = JResponse::getBody(); 
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
		   JResponse::setBody($buffer); 
		   
		   
	   }
	}
	
	private function canRun()
	{
		if (!file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'))
			{
				
				return false;
			}
		
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php'); 
				require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'numbering.php'); 
	
	
	   //OPCNumbering::$debug = false; 
	   return true; 
	}
	
	public function plgVmOnUserInvoice($orderDetails,&$data)
	{
		if (!$this->canRun()) return; 
	
		
		$order_id = null; 
		
		if (is_object($orderDetails))
		{
		if (!empty($orderDetails->virtuemart_order_id )) {
		 $order_id = $orderDetails->virtuemart_order_id; 
		}
	    if (isset($orderDetails->order_status)) {
		 $order_status = $orderDetails->order_status; 
		}
		}
		else
		{
			if (!empty($orderDetails['details']['BT']))
			if (is_object($orderDetails['details']['BT'])) {
			$order_id = $orderDetails['details']['BT']->virtuemart_order_id; 
			$order_status = $orderDetails['details']['BT']->order_status; 
			}
			
			if (empty($order_id))
			{
				if (is_array($orderDetails))
				{
					if (!empty($orderDetails['virtuemart_order_id']))
					{
						$order_id = $orderDetails['virtuemart_order_id']; 
						$order_status = $orderDetails['order_status']; 
					}
					
					$number = $orderDetails['order_number']; 
				}
			}
			
			//virtuemart_order_id
		}
		
		
		$order_id = (int)$order_id; 
		if ((!empty($order_id)) && (!empty($order_status))) {
		  $this->plgGetInvoiceGeneratedOPC($order_id, $order_status); 
		}
		
		
		

		
		
		
		
		
	}
	
	public function plgGetInvoiceGeneratedOPC($order_id, $order_status) {
		
		if (empty($order_id)) return; 
		
		
		
		ob_start(); 
		
	    require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'export_helper.php');
			if (class_exists('OnepageTemplateHelper'))
	{
	$ehelper = new OnepageTemplateHelper($order_id);
    
    $templates = $ehelper->getExportTemplates('ALL');
	
	
	
	
	if (!empty($templates))
	foreach ($templates as $t)
    {
      if (empty($t['tid_enabled'] )) continue;
      if (empty($t['tid_autocreate'])) continue;
	  
      
	  
	  if (!in_array($order_status, $t['tid_autocreatestatus'])) continue; 
	  
      if ((!empty($t['tid_foreign'])) && (!empty($t['tid_foreigntemplate']))) 
	  $special = $ehelper->getNextAi($t['tid_foreigntemplate'], $order_id);
      else
      if ((!empty($t['tid_special'])) && (!empty($t['tid_ai']))) $special = $ehelper->getNextAi($t['tid'], $order_id);
	  if (!empty($t['tid_special']) && (empty($special))) continue;		      
	 	$tid = $t['tid'];
	 
	 $specials = array();
	 if (!empty($special))
	 $specials[0] = $special;
 
	
 
	
	 $ehelper->processTemplate($tid, $order_id, $specials, 'AUTOPROCESSING');
	 
	 
	
	}
	
	
	 }
	 
	 
	 $z = ob_get_clean(); 
	}
	
	
	public function plgVmConfirmedOrder($cart, $order)
	{
		
		if (!$this->canRun()) return; 
		
		$order_number = $order['details']['BT']->order_status; 
		$order_id = $order['details']['BT']->virtuemart_order_id; 
		
		$order_id = (int)$order_id; 
		if ((!empty($order_id)) && (!empty($order_status))) {
		  $this->plgGetInvoiceGeneratedOPC($order_id, $order_status); 
		}
		
		
		
	}
	public function plgVmOnUserOrder(&$orderDetails)
	{
		
		
		if (!$this->canRun()) 
		{
			
			return; 
		}
		
		$order_id = 0; 
		
		if (is_object($orderDetails))
		{
		if (!empty($orderDetails->virtuemart_order_id ))  {
		$order_id = $orderDetails->virtuemart_order_id; 
		$order_status = $orderDetails->order_status; 
		}
	    
		}
		else
		if (is_array($orderDetails))
		{
			if (!empty($orderDetails['details']['BT'])) {
			$order_id = $orderDetails['details']['BT']->virtuemart_order_id; 
			$order_status = $orderDetails['details']['BT']->order_status; 
			}
			
		}
		
		$order_id = (int)$order_id; 
		if ((!empty($order_id)) && (!empty($order_status))) {
		  $this->plgGetInvoiceGeneratedOPC($order_id, $order_status); 
		}
		
	
		
	}
	
	public function plgVmOnUpdateOrderPayment(&$orderDetails,$old_order_status)
	{
	   
	  	if (!$this->canRun()) 
		{
			
			return; 
		}

	    $order_id = 0; 
		
		if (is_object($orderDetails))
		{
		if (!empty($orderDetails->virtuemart_order_id ))  {
		$order_id = $orderDetails->virtuemart_order_id; 
		$order_status = $orderDetails->order_status; 
		}
	    
		}
		else
		if (is_array($orderDetails))
		{
			if (!empty($orderDetails['details']['BT'])) {
			 $order_id = $orderDetails['details']['BT']->virtuemart_order_id; 
			 $order_status = $orderDetails['details']['BT']->order_status; 
			}
			if (!empty($orderDetails['virtuemart_order_id'])) {
			  $order_id = $orderDetails['virtuemart_order_id'];
			}
			if (!empty($orderDetails['order_status'])) {
			  $order_status = $orderDetails['order_status'];
			}
			
		}
		
		$order_id = (int)$order_id; 
		if ((!empty($order_id)) && (!empty($order_status))) {
		  $this->plgGetInvoiceGeneratedOPC($order_id, $order_status); 
		}
	  
	  
	}
	
	
	public function plgVmCouponUpdateOrderStatus($orderDetails,$old_order_status)
	{
	   
	  	if (!$this->canRun()) 
		{
			
			return; 
		}

	    $order_id = 0; 
		
		if (is_object($orderDetails))
		{
		if (!empty($orderDetails->virtuemart_order_id ))  {
		$order_id = $orderDetails->virtuemart_order_id; 
		$order_status = $orderDetails->order_status; 
		}
	    
		}
		else
		if (is_array($orderDetails))
		{
			if (!empty($orderDetails['details']['BT'])) {
			 $order_id = $orderDetails['details']['BT']->virtuemart_order_id; 
			 $order_status = $orderDetails['details']['BT']->order_status; 
			}
			if (!empty($orderDetails['virtuemart_order_id'])) {
			  $order_id = $orderDetails['virtuemart_order_id'];
			}
			if (!empty($orderDetails['order_status'])) {
			  $order_status = $orderDetails['order_status'];
			}
			
		}
		
		$order_id = (int)$order_id; 
		if ((!empty($order_id)) && (!empty($order_status))) {
		  $this->plgGetInvoiceGeneratedOPC($order_id, $order_status); 
		}
	  
	  
	}
	
	
	public function plgVmOnPaymentResponseReceived(&$html)
	{

	    if (empty($html)) $html = '&nbsp;'; 
	   
	   {
	    if (!class_exists('VirtueMartModelOrders')) {
			require(JPATH_VM_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'orders.php');
		}
		if (!class_exists('shopFunctionsF')) {
			require(JPATH_VM_SITE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'shopfunctionsf.php');
		}
		// PPL, iDeal, heidelpay: 
	    $order_number = JRequest::getString('on', 0);
		// eway:
		if (empty($order_number))
		 {
		   $order_number = JRequest::getString('orderid', 0);
		   if (empty($order_number))
		    {
			   //systempay
			  $order_number = JRequest::getString('order_id', 0);
			}
			if (empty($order_number))
			{
				$order_number = JRequest::getString('ordernumber', 0); 
			}
			if (empty($order_number))
			{
				$order_number = JRequest::getString('ono', 0); 
			}
			
		 }
		 if (empty($order_number)) return; 
		$orderModel = VmModel::getModel('orders');
	    $virtuemart_order_id = (int)VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
		if (empty($virtuemart_order_id)) return;
	    $order = $orderDetails = $orderModel->getOrder($virtuemart_order_id);
		if (!empty($orderDetails))
		if (is_object($orderDetails['details']['BT'])) {
			$order_id = $orderDetails['details']['BT']->virtuemart_order_id; 
			$order_status = $orderDetails['details']['BT']->order_status; 
			}
		
	   }
	   
	    $order_id = (int)$order_id; 
		if ((!empty($order_id)) && (!empty($order_status))) {
		  $this->plgGetInvoiceGeneratedOPC($order_id, $order_status); 
		}
	   
	   
	   

	}
	
	
	
}
