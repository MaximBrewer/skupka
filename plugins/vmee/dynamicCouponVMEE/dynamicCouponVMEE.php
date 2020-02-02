<?php
/**
* @copyright (C) 2014 Interamind LTD, http://www.interamind.com
**/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgvmeeDynamicCouponVMEE extends JPlugin {
	const ORIENTATION_ORDER = 1;
	const ORIENTATION_EXISTING_CUSTOMER = 2;
	const ORIENTATION_NEW_CUSTOMER = 4;
	const ORIENTATION_CART = 8;
	
	static $isInitialzedVM = false;
	static $vmModels = array();

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
	function replaceTags(&$str, &$errors, &$resources){
		
		return $this->replaceCouponTags($str, $errors, $resources);
	}
	
	function replaceCouponTags(&$str, &$errors, &$resources){
		
		preg_match_all('/\[COUPON:[^\]]*\]/s', $str, $arr, PREG_PATTERN_ORDER);
		if(is_array($arr[0])){
			foreach ($arr[0] as $custom_label){
				preg_match('/\[COUPON:([^\]]*)\]/', $custom_label, $inner_arr);
				
/* 				if(!$this->isInStatus($resources)){
					$str = str_replace( $custom_label, '', $str);
				}else{ */
					$coupon_tag = trim($inner_arr[1]);
					$couponArr = explode('|', $coupon_tag);
					
					$percent_or_total = $couponArr[0];
					$gift_or_permanent = $couponArr[1];
					$coupon_value = $this->getCouponValue($couponArr[2], $errors, $resources);
					$valid_from = isset($couponArr[3]) ? $couponArr[3] : '0';
					$start_date = isset($couponArr[4]) ? $couponArr[4] : '';
					$expire_date = isset($couponArr[5]) ? $couponArr[5] : '';
					if(is_numeric($expire_date)){
						$time = strtotime($start_date . ' +' . $expire_date . ' days');
						$expire_date = date('Y-m-d',$time);
					}
					
					$coupon_id = $this->createDynamicCoupon($percent_or_total, $gift_or_permanent, $coupon_value, $valid_from, $start_date, $expire_date);
					$coupon_prefix = $this->params->def('coupon_prefix');
					$coupon_suffix = $this->params->def('coupon_suffix');
					
					$replace =  $coupon_id ? $coupon_id : '[COUPON:'.$coupon_tag.']';
					$replace = 	$coupon_prefix.$replace.$coupon_suffix;			
					
					$str = str_replace( $custom_label, $replace, $str);
				/* } */
			}
		}	
		
		return $str;
	}
	
	private function getCouponValue($value_param, &$errors, &$resources){
		//{0.15*ORDER_TOTAL}
		preg_match('/\{([^\*]*)(\*)([^\}]*)\}/', $value_param, $inner_arr);
		if(!empty($inner_arr)){
			$precent = $inner_arr[1];
			$order_id = $resources['order_id'];
			$order_total = $this->getOrderTotal($order_id);
			$coupon_value = $precent*$order_total;
			return ceil($coupon_value);
		}
		
		return $value_param;
	}
	
	private function getOrderTotal($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT order_total FROM #__virtuemart_orders WHERE virtuemart_order_id=".$order_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}
	
	/* private function isInStatus(&$resources){
			$statusArray = $this->params->get('status');
			if(empty($statusArray)){
				return true;
			}
			
			if(!key_exists('order_id', $resources))
				return true;

			$order_status = $this->getOrderStatus($resources['order_id']);
			if(empty($order_status))
				return true;

			if(is_array($statusArray)){
				foreach ($statusArray as $status){
					if($status == $order_status)
						return true;
				}
			}
			else if($statusArray ==  $order_status){
				return true;
			}
			
			return false;
	} */
	
	function getOrderStatus($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT order_status FROM #__virtuemart_orders WHERE virtuemart_order_id=".$order_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
	}
	
	private function createDynamicCoupon($percent_or_total, $gift_or_permanent, $coupon_value, $valid_from, $start_date, $expire_date){
		//[COUPON:percent_or_total|gift_or_permanent|coupon_value]
		
		$coupon_id = strtoupper(uniqid());
		$coupon = array(
		        'coupon_code' =>  $coupon_id,
		        'percent_or_total' => strtolower($percent_or_total), 
		        'coupon_type' => strtolower($gift_or_permanent),
		        'coupon_value' => (float)$coupon_value,
				'valid_from' => isset($valid_from) && !is_null($valid_from) ? (float)$valid_from : 0,
				'start_date' => isset($start_date) && !is_null($start_date) ? $start_date : '',
				'expire_date' => isset($expire_date) && !is_null($expire_date) ? $expire_date : ''
		);
		
//		if($vm_ps_coupon->add_coupon_code($coupon))
		
		if($this->add_coupon_code($coupon))
			return $coupon_id;
		
		return false;
	}
	
	function add_coupon_code( &$d ) {
	    $bRes = false;
		$coupon_code = $d['coupon_code'];
		$percent_or_total = strtolower($d['percent_or_total']) == 'percent' ? 'percent' : 'total';
		$coupon_type = strtolower($d['coupon_type']) == 'gift' ? 'gift' : 'permanent';
		$coupon_value = (float)$d['coupon_value'];
		$valid_from = (float)$d['valid_from'];
		$start_date = $d['start_date'];
		$expire_date = $d['expire_date'];
	
		$fields = array(
						'coupon_code' => $coupon_code,
						'percent_or_total' => $percent_or_total,
						'coupon_type' => $coupon_type,
						'coupon_value' => $coupon_value,
						'coupon_start_date' => $start_date,
						'coupon_expiry_date' => $expire_date,
						'coupon_value_valid' => $valid_from
					);

		$model = self::getVmModels('coupon');
		$couponId = $model->store($fields);
		if(!empty($couponId)){
			$bRes = true;
		}
		
		return $bRes;
    }
	
	function getAvailableTagsDesc(){
		if(file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_vmemails".DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR."vmemails.php") )
			require_once ( JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_vmemails".DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR."vmemails.php");
	
		$tplVars = $this->getTemplateVariables();
		return array(
			vmemailsModelVmemails::$TYPE_REGISTRATION => $tplVars,
			vmemailsModelVmemails::$TYPE_ORDER_CONFIRM => $tplVars,
			vmemailsModelVmemails::$TYPE_ADMIN_ORDER_CONFIRM => $tplVars,
			vmemailsModelVmemails::$TYPE_ORDER_SATAUS_CHANGED => $tplVars
		);
	}
	
	private function getTemplateVariables(){
		$availableTags =  $this->getAvailableTags(self::ORIENTATION_CART | self::ORIENTATION_EXISTING_CUSTOMER | self::ORIENTATION_NEW_CUSTOMER | self::ORIENTATION_ORDER);
		return $availableTags[0];
		
	}
	
	public function getAvailableTags($orientation){
		$availableTags = array();
		$description = "<p>[COUPON:percent_or_total|gift_or_permanent|coupon_value|valid_value|start_date|expiry_date]</p>
				<ul>
					<li>percent_or_total - Coupon discount type. Can be 'percent' or 'total'.</li>
					<li>gift_or_permanent - Coupon type. Can be 'gift' or 'permanent'.</li>
					<li>coupon value - Value of the coupon.</li>
					<li>valid_value - [Optional] The minimum order value this coupon can be applied to.</li>
					<li>start date - [Optional] Start date for the coupon in format 'yyyy-mm-dd', or 'now'.</li>
					<li>expiry_date - [Optional] Expiry date for the coupon in format 'yyyy-mm-dd', or number of days from start date.</li>
				</ul>
				<p>Dynamically creates new coupon code and adds it both to <a target=\"_blank\" href=\"index.php?option=com_virtuemart&view=coupon\">VirtueMart's coupon list</a> and to the email.</p>
				<p>The newly created coupon will have the following format: <strong>4C504BCF71082</strong></p>";
		$description .= $this->checkOrientation("<p>Coupon value can be also a percentage of the order's total value by replacing the value with the following format:</p>
												<p>{0.15*ORDER_TOTAL}</p>
												<p>For example, this will create a coupon with a value of %15 of the order total:<br>[COUPON:total|gift|{0.15*ORDER_TOTAL}]</p>",
												self::ORIENTATION_ORDER,
												$orientation);
		//the vmee coupon tags are relevant for all types of templated orientations
		$availableTags[] =  array(
				"title" => "Dynamic coupons template variables",
				"name" => "Dynamic coupons tags",
				"example" => "<p>[COUPON:percent|gift|25]</p>
								<p>[COUPON:percent|gift|25|150]</p>
								<p>[COUPON:percent|gift|25||now|3]</p>
								<p>[COUPON:percent|gift|25|||2013-05-28]</p>",
				"description" => $description
		);
		
		return $availableTags;
	}
	
	private function checkOrientation($data, $allowedOrientatiom, $testedOrientation){
		$res = '';
		if($allowedOrientatiom & $testedOrientation){
			$res = $data;
		}
	
		return $res;
	}
	
	static function getVmModels($vmModelName){
		if(!key_exists($vmModelName, self::$vmModels)){
			self::loadVmModel($vmModelName);
		}
		return self::$vmModels[$vmModelName];
	}
	
	static private function loadVmModel($vmModelName){
		self::loadVirtueMartFiles();
		self::$vmModels[$vmModelName] = JModelLegacy::getInstance($vmModelName, 'VirtuemartModel');
	}
	
	static function loadVirtueMartFiles(){
		if(!self::$isInitialzedVM){
			if (!class_exists('VmConfig')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
			if (!class_exists('ShopFunctions')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctions.php');
			if (!class_exists('VirtueMartModelCustomfields')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'customfields.php');
			if (!class_exists('CurrencyDisplay')) require(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'currencydisplay.php');
	
			VmConfig::loadConfig();
	
			$lang = JFactory::getLanguage();
			$extension = 'com_virtuemart_orders';
			$base_dir = JPATH_SITE . '/components/com_virtuemart';
			$language_tag = null;
			$reload = true;
			$lang->load($extension, $base_dir, $language_tag, $reload);
				
			$extension = 'com_virtuemart_shoppers';
			$lang->load($extension, $base_dir, $language_tag, $reload);
				
			$extension = 'com_virtuemart';
			$lang->load($extension, $base_dir, $language_tag, $reload);
				
			$base_dir = JPATH_ADMINISTRATOR . '/components/com_virtuemart';
			$lang->load($extension, $base_dir, $language_tag, $reload);
			
			$lang->load('com_vmeeplus', JPATH_SITE, null, true);
			$lang->load('com_vmeeplus', JPATH_ADMINISTRATOR, null, true);
			// 			$lang->load('com_virtuemart', JPATH_BASE, null, false, false)
			// 			||	$lang->load('com_virtuemart', JPATH_COMPONENT, null, false, false)
			// 			||	$lang->load('com_virtuemart', JPATH_BASE, $lang->getDefault(), false, false)
			// 			||	$lang->load('com_virtuemart', JPATH_COMPONENT, $lang->getDefault(), false, false);
	
			self::$isInitialzedVM = true;
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart' . DIRECTORY_SEPARATOR . 'models');
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_virtuemart/tables');
		}
	}
	
}
