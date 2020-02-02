<?php
/**
 
 
 * @copyright (C) 2014 Interamind LTD, http://www.interamind.com
**/

defined('_JEXEC') or die( 'Restricted access' );

	
jimport('joomla.plugin.plugin');

class plgvmeeDynamicCouponVMEEAWO extends JPlugin {

	const ORIENTATION_ORDER = 1;
	const ORIENTATION_EXISTING_CUSTOMER = 2;
	const ORIENTATION_NEW_CUSTOMER = 4;
	const ORIENTATION_CART = 8;
	
	function plgvmeeDynamicCouponVMEEAWO(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}
	
	function replaceTags(&$str, &$errors, &$resources){
		
		return $this->replaceCouponTags($str, $errors, $resources);
	}
	
	function replaceCouponTags(&$str, &$errors, &$resources){
		
		preg_match_all('/\[COUPON_AWO:[^\]]*\]/s', $str, $arr, PREG_PATTERN_ORDER);
		if(is_array($arr[0])){
			foreach ($arr[0] as $custom_label){
				preg_match('/\[COUPON_AWO:([^\]]*)\]/', $custom_label, $inner_arr);
				
				if(!$this->isInStatus($resources)){
					$str = str_replace( $custom_label, '', $str);
				}else{
					$coupon_tag = trim($inner_arr[1]);
					$couponArr = explode('|', $coupon_tag);
					
					if(sizeof($couponArr) != 3){
						JError::raiseWarning( 669, 'The AWO coupon tag: '.$custom_label.' is not valid. Wrong number of parameters.');
						$str = str_replace( $custom_label, '', $str);
						continue;
					}
					
					$coupon_id = $this->createDynamicCouponAWO($resources, $couponArr, $errors);
					
					if($coupon_id === false){
						if(is_array($errors) && sizeof($errors) > 0){
							for($i = 0; $i < sizeof($errors); $i++) {
								JError::raiseWarning( 669, $errors[$i]);
							}
						}
						$str = str_replace( $custom_label, '', $str);
						continue;
					}
					
					$coupon_prefix = $this->params->def('coupon_prefix');
					$coupon_suffix = $this->params->def('coupon_suffix');
						
					$replace = 	$coupon_prefix.$coupon_id.$coupon_suffix;			
					$str = str_replace( $custom_label, $replace, $str);
				}
			}
		}	
		
		return $str;
	}
	
	private function createDynamicCouponAWO(&$resources, &$couponArr, &$errors){
		//example [COUPON_AWO:5|14|current]
		if($this->isAwoInstalled()){
			$coupon_code = strtoupper(uniqid());
			if(empty($coupon_code)){
				$errors[] = "Dynamic Coupon ID was not created.";
				return false;
			}
			
			if(empty($couponArr) || !is_array($couponArr)){
				$errors[] = "Coupon tag is not valid.";
				return false;
			}
			
			$coupon_id = $couponArr[0];
			if(!is_numeric($coupon_id)){
				$errors[] = "Coupon ID format is not valid.";
				return false;
			}
			
			$expiration = $couponArr[1];
			if(!is_numeric($expiration) && $expiration !='default'){
				$errors[] = "Expiration date format is not valid: ".$expiration;
				return false;
			}else if($expiration == 'default'){
				$expiration = null;
			}
			
			$user = $couponArr[2];
			if($user == 'current'){
				 $user = $this->getUserId($resources);
			}else if($user == 'default'){
				$user = null;
			}else{
				$errors[] = "Users format is not valid. Must be 'current' or 'default': ".$user;
				return false;
			}
			
			$list = awoAutoGenerate::getCouponTemplates();
			$isIdExists = false;
			if(!is_array($list)){
				$errors[] = "AwoCoupon coupon ID was not found: ".$coupon_id;
				return false;
			}else{
				foreach ($list as $arr) {
					if($arr->id == $coupon_id){
						$isIdExists = true;
						break;
					}
					
				}
				if(!$isIdExists){
					$errors[] = "AwoCoupon coupon ID was not found: ".$coupon_id;
					return false;
				}
			}
			
			$obj = awoAutoGenerate::generateCoupon($coupon_id, $coupon_code, $expiration, $user);
			if($obj === false){
				$errors[] = "AwoCoupon was not able to generate coupon from this coupon ID: ".$coupon_id;
				return false;
			}

			return $coupon_code;
		}
		$errors[] = "AwoCoupons couponent is not installed.";
		return false;
	}
	
	private function isInStatus(&$resources){
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
	}
	
	function getOrderStatus($order_id){
		$db = JFactory::getDBO();
		$q = "SELECT order_status FROM #__virtuemart_orders WHERE virtuemart_order_id=".$order_id;
		$db->setQuery($q);
		$result = $db->loadResult();
		return $result;
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
		$availableTags = $this->getAvailableTags(self::ORIENTATION_CART | self::ORIENTATION_EXISTING_CUSTOMER | self::ORIENTATION_NEW_CUSTOMER | self::ORIENTATION_ORDER);
		return $availableTags[0];
	}
	
	private function isAwoInstalled()
	{
		$success = false;
		
	    jimport('joomla.filesystem.file');
        if (JFile::exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plgautogenerate.php')) 
        {
			include_once JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_awocoupon'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'plgautogenerate.php' ;
            $success = true;
        }
		return $success;
	}
	
	private function getUserId(&$resources){
		if(key_exists('user_id', $resources)){
			$user_id = $resources['user_id'];
			if(!empty($user_id)){
				return $user_id;
			}
			else{
				return null;
			}
		}else if(key_exists('order_id', $resources)){
			$order_id = $resources['order_id'];
			if(!empty($order_id)){
				$db = JFactory::getDBO();
				$q = "SELECT virtuemart_user_id FROM #__virtuemart_orders WHERE virtuemart_order_id=".$order_id;
				$db->setQuery($q);
				$result = $db->loadResult();
				return $result;
			}else{
				return null;
			}
		}
		
		return null;
	}
	
	public function getAvailableTags($orientation){
		
		$availableTags = array();
		//the AWO coupon tags are relevant for all types of templated orientations
		$db = JFactory::getDBO();
		$q = "SELECT extension_id FROM #__extensions WHERE element='dynamicCouponVMEEAWO'";
		$db->setQuery($q);
		$result = $db->loadResult();
		$link = "index.php?option=com_plugins&view=plugin&layout=edit&extension_id=".$result;
		$notInstalledError = "";
		if(!$this->isAwoInstalled()){
			$notInstalledError = "<p style='color:red;'>ERROR. AwoCoupons component is not installed. You will not be able to use this plugin.</p>";
		}
		
		$availableTags[] = array(
			"title" => "Dynamic coupons (AwoCoupon compatible) template variables",
			"name" => "Dynamic coupons (AwoCoupon compatible)",
			"example" => "",
			"description" => $notInstalledError."<p>Dynamically creates new AwoCoupon code and adds it both to <a target=\"_blank\" href=\"index.php?option=com_awocoupon&view=coupons\">AwoCoupons list</a> and to the email.</p>
				<p>The newly created coupon will have the following format: <strong>4C504BCF71082</strong></p>
				<p><ul><li><b>AwoCoupon template ID:</b> Coupon ID to duplicate (Will create a new coupon code with the same parameters).</li><li><b>Expiration date:</b> number of days from the time the email is sent. Or use 'default' to use the parameters from the coupon.</li><li><b>User:</b> Use 'current' for this user or 'default' to take the parameters from the coupon.</li></ul></p>
				<p><b>Usage example:</b></p>
				<p>[COUPON_AWO:5|14|current]</p>
				<p>[COUPON_AWO:5|default|default]</p>
				<p>[COUPON_AWO:5|14|current]</p>
				<p>For more details how to set this coupon <a target=\"_blank\" href=\"".$link."\">see the plugin page.</a></p>"
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
}
?>