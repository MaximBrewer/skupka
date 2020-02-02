<?php
/**
 * @package	HikaShop for Joomla!
 * @version	3.1.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2017 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
$hikashop_config =& hikashop_config();

if (!class_exists('CheckoutViewCheckout')) {
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_hikashop'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'checkout'.DIRECTORY_SEPARATOR.'view.html.php'); 
}


class checkoutViewOpccheckout extends CheckoutViewCheckout {
	public $ctrl = 'checkout';
	public $nameListing = 'CHECKOUT';
	public $nameForm = 'CHECKOUT';
	public $icon = 'checkout';
	public $extraFields = array();
	public $requiredFields = array();
	public $validMessages = array();
	public $triggerView = array('hikashop','hikashoppayment','hikashopshipping');

	public $config = null;
	public $fieldClass = null;

	protected $legacy = false;

	public function __construct() {
		
		if(!class_exists('hikashopCheckoutHelper'))
			hikashop_get('helper.checkout');
		
		$this->assignAll($this); 
		$this->params = new HikaParameter('');
		parent::__construct();
	}
	
	public function assignAll(&$refX) {
		$ref = OPChikaRef::getInstance(); 
		foreach ($ref as $kx=>$vv) {
			$refX->{$kx} = $vv; 
		}
		$refX->config = hikashop_config();
		$refX->legacy = 0; 
		
		$ref->cartView =& $this; 
		
	}

	public function display($tpl = null, $params = array()) {
		
		
		
        $ref = OPChikaRef::getInstance(); 
		$ref->cart = $ref->checkoutHelper->getCart();		
		
		
		
		
		OPChikaAddress::setDefaultAddress(); 
		OPChikamini::minPovReached(); 
		
		$this->view_params =& $params;

		$this->paramBase = HIKASHOP_COMPONENT.'.'.$this->getName();
		
		$op_basket = OPChikabasket::getBasket(); 
		
		
		
		$selected_template = OPChikarenderer::getSelectedTemplate(); 
		$vars = array('selected_template'=>$selected_template); 
		echo OPChikarenderer::fetch('include', $vars); 
		
		$no_jscheck = true; 
		$ajaxify_cart = true; 
		$no_login_in_template = false; 
		$force_hide_payment = false; 
		$min_reached = ''; 
		$advertises = ''; 
		$return_url = OPChikamini::getReturnLink(); 
		$captcha = ''; 
		$delivery_date = ''; 
		
		
		     $delivery_date = ''; 
			 $no_shipping  = OPChikaShipping::getShippingEnabled(); 
			 $no_shipto = OPChikashipping::getShipToEnabled(); 
			 $op_userfields_cart = ''; 
			 $action_url = ''; 
			 $tos_required = true; 
             $op_coupon = OPChikabasket::getCouponHTML(); 
             $html_in_between = ''; 
             $continue_link = ''; 
             $op_login_t = ''; 
             $shipping_method_html = ''; 
             $op_userfields = OPChikaaddress::getBTAddressFieldsHTML(); 
             $shippingtxt = ''; 
             $chkship = ''; 
             $op_shipto = OPChikashipto::getShipToHtml(); 
			 
			 $third_address = ''; 
             $op_tos = ''; 
             $op_payment = OPChikapayment::getPaymentHTML(true, $this); 
             $tos_con = ''; 
             $agreement_txt = ''; 
             $show_full_tos = ''; 
             $google_checkout_button = ''; 
             $paypal_express_button = ''; 
             $related_products = ''; 
             $registration_html = OPChikaregistration::getRegistrationFieldsHTML(); 
			 
			 $onsubmit = ''; 
			 $tos_link = '';
			 $checkbox_products = ''; 
			 
			 $italian_checkbox = ''; 
			 $subscription_checkbox = ''; 
			 $subscription_checkbox = ''; 
		
		
		$tpla = Array(
		    "ajaxify_cart"=>$ajaxify_cart,
			"no_jscheck"=>$no_jscheck,
			"shipping_estimator" => '',
			"force_hide_payment" => $force_hide_payment, 
			"hide_payment" => $force_hide_payment,
			"no_login_in_template"=>$no_login_in_template,
			"min_reached_text" => $min_reached,
			"checkoutAdvertises" => $advertises, 
			"intro_article" => $intro_article, 
			"return_url" => $return_url, 
			"captcha" 	=> $captcha, 
			"delivery_date" => $delivery_date, 
			"no_shipping" => $no_shipping,
			"op_onclick" => ' onclick="'.$onsubmit.'" ', 
			"no_shipto" => NO_SHIPTO, 
			'op_userfields_cart'=>$op_userfields_cart,
			"action_url" => $action_url,
			"tos_required" => $tos_required,
			"op_userinfo_st" => "",
            "op_basket" => $op_basket,
            "op_coupon" => $op_coupon, 
            "html_in_between" => $html_in_between, 
            "continue_link" => $continue_link, 
            "op_login_t" => $op_login_t,
            "shipping_method_html" => $shipping_method_html.JHtml::_('form.token'),
            "op_userfields" => $op_userfields,
            "shippingtxt" => $shippingtxt,
            "chkship" => $chkship,
            "op_shipto" => $op_shipto,
			"third_address" => $third_address,
            "op_tos" => $op_tos,
             "op_payment" => $op_payment.JHtml::_('form.token'),
             "tos_con" => $tos_con, 
             "agreement_txt" => $agreement_txt,
             "show_full_tos" => $show_full_tos,
             "google_checkout_button" => $google_checkout_button,
             "paypal_express_button" => $paypal_express_button,
             "related_products" => $related_products, 
             "registration_html" => $registration_html,
			 "onsubmit" => $onsubmit,
			 "tos_link" => $tos_link,
			 "checkbox_products" => '',
			 'italian_checkbox' => $subscription_checkbox.$italian_checkbox, 
			 'privacy_checkbox' => $italian_checkbox,
			 'subscription_checkbox' => $subscription_checkbox, 
			 'acymailing_checkbox' => $subscription_checkbox, 
          
           ) ;
		
		
		
		$is_logged = OPChikauser::logged(); 
		if ($is_logged) {
		$html = OPChikarenderer::fetch('onepage.logged.tpl', $tpla); 	
		$logged_wrap = '<div class="opc_logged_wrapper" id="opc_logged_wrapper" >'; 
		}
		else {
		$html = OPChikarenderer::fetch('onepage.unlogged.tpl', $tpla); 	
		$logged_wrap = '<div class="opc_unlogged_wrapper" id="opc_unlogged_wrapper" >'; 
		}
		$html =  '<div id="vmMainPageOPC">'.$logged_wrap.$html.'</div></div>'; 
		
		
		$rep = array('Onepage.', 'name="adminForm"', '"showSA', 'javascript: showSA', 'javascript: return op_login', 'return submitenter', 'return op_openlink', 'return changeST', 'return op_unhide(', 'onchange="showFields('); 
		
		$repwith = array('Hikaonepage.', ' id="adminForm" name="adminForm" ', '"Hikaonepage.showSA', 'javascript: Hikaonepage.showSA', 'javascript: return Hikaonepage.op_login', 'return Hikaonepage.submitenter', 'return Hikaonepage.op_openlink', 'return Hikaonepage.changeST', 'return Hikaonepage.op_unhide(', 'onclick="return Hikaonepage.showFields('); 
		if (OPChikaconfig::get('useSSL', false))
			 {
			    $rep[] = 'src="http:'; 
				$repwith[] = 'src="https:';
			 }
		
		$html = str_replace($rep, $repwith, $html); 
		
		echo $html; 
		
		
		echo OPChikajs::getJavascript();
		OPChikajs::getCSS();
		
		$this->html = ''; 
		parent::display($tpl);
		return;
		
	}

	public function termsandconditions() {
		$terms_article = $this->config->get('checkout_terms', 0);
		$article = '';
		$this->assignRef('article', $article);

		if (empty($terms_article))
			return;

		$db = JFactory::getDBO();
		$sql = 'SELECT * FROM #__content WHERE id = ' . intval($terms_article);
		$db->setQuery($sql);
		$data = $db->loadObject();

		if (is_object($data))
			$article = $data->introtext . $data->fulltext;
	}
	
	public function getShippingHtml() {
		
		$ref = OPChikaRef::getInstance(); 
		$view = $this->getOriginalView('show_block_shipping'); 
		
		$view->continueShopping = $this->config->get('continue_shopping');
		$view->display_checkout_bar = $this->config->get('display_checkout_bar');
		

		$cartIdParam = ($cart_id > 0) ? '&cart_id=' . $cart_id : '';
		$view->cartIdParam = $cartIdParam;

		$view->initItemId();

		$view->workflow_step = hikashop_getCID();
		if($view->workflow_step > 0)
			$view->workflow_step--;
		if($view->workflow_step < 0)
			$view->workflow_step = 0;
		$view->step = 0;


	    $view->ajax = true;

		$view->workflow = $this->checkoutHelper->checkout_workflow;

		JPluginHelper::importPlugin('hikashop');
		JPluginHelper::importPlugin('hikashoppayment');
		JPluginHelper::importPlugin('hikashopshipping');
		$dispatcher = JDispatcher::getInstance();

		$view->checkout_data = array();
		   
		   $content = array(); 
		   $content['type'] = 'shipping'; 
		   $content['params'] = null; 
		   $ret = ''; 
		   $pos = 0; 
		   $task = 'shipping';
		   $layout = 'shipping'; 
		   $view->block_position = 0;

			$ctrl = hikashop_get('helper.checkout-' . $task);
			if(!empty($ctrl)) {
				$view->checkout_data[0] = $ctrl->display($view, $content['params']);
			} else {
				$dispatcher->trigger('onInitCheckoutStep', array($task, &$view));
			}
		
		$options =& $content['params']; 
		
		$ctrl = hikashop_get('helper.checkout-' . $task);
		if(!empty($ctrl)) {
			$previous_options = null;
			if(!empty($view->options))
				$previous_options = $view->options;

			$view->options = $options;
			$view->module_position = (int)0;

			$view->setLayout('show_block_' . $layout);
			$ret = $view->loadTemplate();

			$view->options = $previous_options;

			return $ret;
		}

		$ret = '';
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onCheckoutStepDisplay', array('shipping', &$ret, &$view, $pos, $options));
		
		
		return $ret;
		
	}
	
	public function getPaymentHtml() {
		
		$type = 'payment';
		
		$ref = OPChikaRef::getInstance(); 
		$view = $this->getOriginalView('show_block_'.$type); 
		
		$view->continueShopping = $this->config->get('continue_shopping');
		$view->display_checkout_bar = $this->config->get('display_checkout_bar');
		

		$cartIdParam = ($cart_id > 0) ? '&cart_id=' . $cart_id : '';
		$view->cartIdParam = $cartIdParam;

		$view->initItemId();

		$view->workflow_step = hikashop_getCID();
		if($view->workflow_step > 0)
			$view->workflow_step--;
		if($view->workflow_step < 0)
			$view->workflow_step = 0;
		$view->step = 0;


	    $view->ajax = true;

		$view->workflow = $this->checkoutHelper->checkout_workflow;

		JPluginHelper::importPlugin('hikashop');
		JPluginHelper::importPlugin('hikashoppayment');
		JPluginHelper::importPlugin('hikashopshipping');
		$dispatcher = JDispatcher::getInstance();

		$view->checkout_data = array();
		   
		   $content = array(); 
		   $content['type'] = $type; 
		   $content['params'] = null; 
		   $ret = ''; 
		   $pos = 0; 
		   $task = $type;
		   $layout = $type;
		   $view->block_position = 0;

			$ctrl = hikashop_get('helper.checkout-' . $task);
			if(!empty($ctrl)) {
				$view->checkout_data[0] = $ctrl->display($view, $content['params']);
			} else {
				$dispatcher->trigger('onInitCheckoutStep', array($task, &$view));
			}
		
		$options =& $content['params']; 
		
		$ctrl = hikashop_get('helper.checkout-' . $task);
		if(!empty($ctrl)) {
			$previous_options = null;
			if(!empty($view->options))
				$previous_options = $view->options;

			$view->options = $options;
			$view->module_position = (int)0;

			$view->setLayout('show_block_' . $layout);
			$ret = $view->loadTemplate();

			$view->options = $previous_options;

			return $ret;
		}

		$ret = '';
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onCheckoutStepDisplay', array($type, &$ret, &$view, $pos, $options));
		
		
		return $ret;
		
	}
	
	
	public function show() {
		
		$checkoutHelper = hikashopCheckoutHelper::get();
		$this->checkoutHelper = $checkoutHelper;

		$imageHelper = hikashop_get('helper.image');
		$this->imageHelper = $imageHelper;

		$this->continueShopping = $this->config->get('continue_shopping');
		$this->display_checkout_bar = $this->config->get('display_checkout_bar');
		$cartHelper = hikashop_get('helper.cart');
		$this->assignRef('cart', $cartHelper);

		$cart_id = $checkoutHelper->getCartId();
		$this->assignRef('cart_id', $cart_id);
		$cartIdParam = ($cart_id > 0) ? '&cart_id=' . $cart_id : '';
		$this->assignRef('cartIdParam', $cartIdParam);

		$this->initItemId();

		$this->workflow_step = hikashop_getCID();
		if($this->workflow_step > 0)
			$this->workflow_step--;
		if($this->workflow_step < 0)
			$this->workflow_step = 0;
		$this->step = ($this->workflow_step + 1);

		$tmpl = JRequest::getCmd('tmpl', '');
		if($tmpl == 'ajax')
			$this->ajax = true;

		$this->workflow = $checkoutHelper->checkout_workflow;

		JPluginHelper::importPlugin('hikashop');
		JPluginHelper::importPlugin('hikashoppayment');
		JPluginHelper::importPlugin('hikashopshipping');
		$dispatcher = JDispatcher::getInstance();

		$this->checkout_data = array();

		foreach($this->workflow['steps'][$this->workflow_step]['content'] as $k => &$content) {
			$task = $content['task'];
			$this->block_position = $k;

			$ctrl = hikashop_get('helper.checkout-' . $task);
			if(!empty($ctrl)) {
				$this->checkout_data[$k] = $ctrl->display($this, $content['params']);
			} else {
				$dispatcher->trigger('onInitCheckoutStep', array($task, &$this));
			}
		}
		unset($content);

		hikashop_setPageTitle('CHECKOUT');
	}

	public function showblock() {
		
		$checkoutHelper = hikashopCheckoutHelper::get();
		$this->checkoutHelper = $checkoutHelper;

		$this->workflow_step = hikashop_getCID();
		if($this->workflow_step > 0)
			$this->workflow_step--;
		if($this->workflow_step < 0)
			$this->workflow_step = 0;
		$this->step = ($this->workflow_step + 1);

		$block_pos = JRequest::getInt('blockpos', 0);
		$block_task = JRequest::getString('blocktask', null);

		$this->block_position = $block_pos;

		$cart_id = $checkoutHelper->getCartId();
		$this->assignRef('cart_id', $cart_id);
		$cartIdParam = ($cart_id > 0) ? '&cart_id=' . $cart_id : '';
		$this->assignRef('cartIdParam', $cartIdParam);

		$this->initItemid();

		$tmpl = JRequest::getCmd('tmpl', '');
		if($tmpl == 'ajax')
			$this->ajax = true;

		$this->workflow = $checkoutHelper->checkout_workflow;

		$this->checkout_data = array();

		if(empty($this->workflow['steps'][$this->workflow_step]['content']))
			return false;
		if(empty($this->workflow['steps'][$this->workflow_step]['content'][$block_pos]))
			return false;
		if($this->workflow['steps'][$this->workflow_step]['content'][$block_pos]['task'] != $block_task)
			return false;

		$content = $this->workflow['steps'][$this->workflow_step]['content'][$block_pos];
		if(empty($content['params']))
			$content['params'] = array();
		JPluginHelper::importPlugin('hikashop');
		JPluginHelper::importPlugin('hikashoppayment');
		JPluginHelper::importPlugin('hikashopshipping');
		$dispatcher = JDispatcher::getInstance();

		$ctrl = hikashop_get('helper.checkout-' . $block_task);
		if(!empty($ctrl)) {
			$this->checkout_data[$block_pos] = $ctrl->display($this, $content['params']);
		} else {
			$dispatcher->trigger('onInitCheckoutStep', array($block_task, &$this));
		}

		$dispatcher->trigger('onHikashopBeforeDisplayView', array(&$this));

		echo $this->displayBlock($block_task, $block_pos, $content['params']);

		$dispatcher->trigger('onHikashopAfterDisplayView', array(&$this));

		$events = $checkoutHelper->getEvents();
		if(!empty($events)) {
			echo "\r\n".'<script type="text/javascript">'."\r\n";
			foreach($events as $k => $v) {
				echo 'window.Oby.fireAjax("'.$k.'", '.json_encode($v).');' . "\r\n";
			}
			echo "\r\n".'</script>';
		}
		$config = hikashop_config();
		if($config->get('bootstrap_forcechosen')) {
			echo "\r\n".'<script type="text/javascript">'."\r\n";
			echo '
			if(typeof(hkjQuery) != "undefined" && hkjQuery().chosen)
				hkjQuery(\'.hikashop_checkout_page select\').not(\'.chzen-done\').chosen();
			';
			echo "\r\n".'</script>';
		}
		$this->displayView = false;
		return true;
	}

	public function displayBlock($layout, $pos, $options) {
		$ctrl = hikashop_get('helper.checkout-' . $layout);
		if(!empty($ctrl)) {
			$previous_options = null;
			if(!empty($this->options))
				$previous_options = $this->options;

			$this->options = $options;
			$this->module_position = (int)$pos;

			$this->setLayout('show_block_' . $layout);
			$ret = $this->loadTemplate();

			$this->options = $previous_options;

			return $ret;
		}

		$ret = '';
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onCheckoutStepDisplay', array($layout, &$ret, &$this, $pos, $options));
		return $ret;
	}
	public function getOriginalView($layout) {
		$controller = JControllerLegacy::getInstance('Checkout');
		$view = $controller->getView('checkout', 'html', '');
		$view->setLayout($layout); 
		
		$view->addTemplatePath(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_hikashop'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'checkout'.DIRECTORY_SEPARATOR.'tmpl'); 
		$view->addTemplatePath(JPATH_SITE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.JFactory::getApplication()->getTemplate().'html'.DIRECTORY_SEPARATOR.'com_hikashop'.DIRECTORY_SEPARATOR.'checkout'); 
		
		$view->params =& $this->params; 
		
		
		$this->assignAll($view); 
		
		return $view; 

		
	}
	public function getDisplayProductPrice(&$product, $unit = false) {
		
		
		$view = $this->getOriginalView('listing_price'); 
		
		$previous_price_with_tax = $view->params->get('price_with_tax', false);
		$view->row =& $product;
		$view->unit = $unit;
		$view->params->set('price_with_tax', $view->options['price_with_tax']);

		$view->setLayout('listing_price');
		//parent::setLayout('tmpl:listing_price');
		$ret = $view->loadTemplate();
		
		unset($view->row);
		unset($view->unit);
		$view->params->set('price_with_tax', $previous_price_with_tax);

		return $ret;
	}

	public function addOptionPriceToProduct(&$productPrice, &$optionPrice) {
		foreach(get_object_vars($productPrice) as $key => $value) {
			if($key == 'unit_price')
				$this->addOptionPriceToProduct($productPrice->$key, $optionPrice->$key);
			if(strpos($key, 'price_value') === false)
				continue;
			$productPrice->$key += (float)hikashop_toFloat(@$optionPrice->$key);
		}
	}

	public function loadFields() {
		if(!hikashop_level(2) || !empty($this->extraFields['item']))
			return;
		if(empty($this->fieldClass))
			$this->fieldClass = hikashop_get('class.field');
		$products = null;
		if(!empty($this->checkoutHelper)) {
			$cart = $this->checkoutHelper->getCart();
			$products =& $cart->products;
		}
		$this->extraFields['item'] = $this->fieldClass->getFields('frontcomp', $products, 'item');
	}

	public function state() {
		$namekey = JRequest::getCmd('namekey','');
		if(!headers_sent()) {
			header('Content-Type:text/html; charset=utf-8');
		}

		if(empty($namekey)) {
			echo '<span class="state_no_country">'.JText::_('PLEASE_SELECT_COUNTRY_FIRST').'</span>';
			exit;
		}

		$field_namekey = JRequest::getString('field_namekey', '');
		if(empty($field_namekey))
			$field_namekey = 'address_state';

		$field_id = JRequest::getString('field_id', '');
		if(empty($field_id))
			$field_id = 'address_state';

		$field_type = JRequest::getString('field_type', '');
		if(empty($field_type))
			$field_type = 'address';

		$db = JFactory::getDBO();
		$query = 'SELECT * FROM '.hikashop_table('field').' WHERE field_namekey = '.$db->Quote($field_namekey);
		$db->setQuery($query, 0, 1);
		$field = $db->loadObject();

		$countryType = hikashop_get('type.country');
		echo $countryType->displayStateDropDown($namekey, $field_id, $field_namekey, $field_type, '', $field->field_options);
		exit;
	}

	public function end() {
		$html = JRequest::getVar('hikashop_plugins_html', '', 'default', 'string', JREQUEST_ALLOWRAW);
		$this->assignRef('html', $html);

		$noform = JRequest::getVar('noform', 1, 'default', 'int');
		$this->assignRef('noform', $noform);

		$order_id = JRequest::getInt('order_id');
		if(empty($order_id)) {
			$app = JFactory::getApplication();
			$order_id = $app->getUserState('com_hikashop.order_id');
		}
		$order = null;
		if(!empty($order_id)) {
			$orderClass = hikashop_get('class.order');
			$order = $orderClass->loadFullOrder($order_id,false,false);
		}

		$this->assignRef('order',$order);
	}

	public function after_end() {
		$order_id = JRequest::getInt('order_id');
		if(empty($order_id)) {
			$app = JFactory::getApplication();
			$order_id = $app->getUserState('com_hikashop.order_id');
		}

		$order = null;
		if(!empty($order_id)) {
			$orderClass = hikashop_get('class.order');
			$order = $orderClass->loadFullOrder($order_id, false, false);
		}

		JPluginHelper::importPlugin('hikashoppayment');
		JPluginHelper::importPlugin('hikashopshipping');
		$this->assignRef('order', $order);
	}

	 public function shop_closed() {
		$checkoutHelper = hikashopCheckoutHelper::get();
		$messages = $checkoutHelper->displayMessages('shop_closed', false);
		$this->assignRef('messages',$messages);
	 }

	protected function initItemid() {
		global $Itemid;
		$checkout_itemid = (int)$Itemid;
		$itemid_for_checkout = (int)$this->config->get('checkout_itemid', 0);
		if(!empty($itemid_for_checkout) && $checkout_itemid != $itemid_for_checkout)
			$checkout_itemid = $itemid_for_checkout;
		$url_itemid = (!empty($checkout_itemid)) ? '&Itemid='.$checkout_itemid : '';

		$this->assignRef('itemid', $checkout_itemid);
		$this->assignRef('url_itemid', $url_itemid);
	}

	public function notice() {
		global $Itemid;
		$url_itemid = '';
		if(!empty($Itemid))
			$url_itemid = '&Itemid=' . $Itemid;
		jimport('joomla.html.parameter');
		$cartHelper = hikashop_get('helper.cart');
		$this->assignRef('url_itemid', $url_itemid);
		$this->assignRef('cartClass', $cartHelper);
		$config = hikashop_config();
		$this->assignRef('config', $config);
	}

	public function &initCart() {
		if($this->legacy)
			return parent::initCart();
		$checkoutHelper = hikashopCheckoutHelper::get();
		$cart = $checkoutHelper->getCart();
		return $cart;
	}
}
