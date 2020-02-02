<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
if (!defined('DS'))
define('DS', DIRECTORY_SEPARATOR); 

class cliHelper {
	public static $returnjson; 
	public static function flushBuffer() {
		if (empty(self::$returnjson)) {
		  $outb = ''; 
		   $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); 
		   $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); 
		   self::writeLog( $outb); 
		   flush(); 
		}
		else {
			@ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean(); @ob_get_clean();
			
			ob_start(); ob_start(); ob_start();
		}
	}
	public static function showUsage($er='', $code=1) {
		
		if (empty(self::$returnjson)) {
		echo 'OPC CLI Class, Copyright RuposTel.com 2016'."\n\n"; 
		
		if (!empty($er)) {
			echo "\033[31m"; 
			self::writeLog( "OPC CLI Error: ".$er."\n\n"); 
			echo "\033[0m"; 
		}
		
		echo 'Usage: /usr/sbin/php /administrator/components/com_onepage/cli.php --task={task} --override_jroot={your joomla root path} --myurl={your live site\s url} TASK OPTIONS'."\n"; 
		echo 'List of available tasks: to_parent_cats, xmlexport, order, neworder'."\n";
		echo '- to_parent_cats: Associates all products to their parent categories among whole category path up to the top category'."\n"; 
		echo '- xmlexport: Triggers XML export for Google, Heureka and other feeds'."\n"; 
		echo '- order: Creates an order from an already existing virtuemart_order_item_id\'s'."\n"; 
		echo '   required parameters are --order_item_id OR --order_item_json'."\n"; 
		echo '   optional parameters are --user_id, --myurl, --override_jroot, --order_status'."\n"; 
		
		echo '- price_import: Imports prices from CSV as product_sku,price '."\n"; 
		echo '   required parameters are: --csvfile '."\n"; 
		echo '   optional parameters are: --myurl, --override_jroot, --virtuemart_shoppergroup_id '."\n"; 
		
		echo '- neworder: Creates an order from virtuemart_product_id\'s and their customData in json format '."\n"; 
		echo '   required parameters are: --products_json, --user_id'."\n"; 
		echo '   optional parameters are: --myurl, --override_jroot, --order_status, --productsdata_json'."\n"; 
		echo "\n\n"; 
		echo '--task=order'."\n"; 
		echo 'Example to create a new order from already existing order line: '."\n"; 
		echo '#php cli.php --task=order --order_item_json=\'{"847":3,"846":4,"845":5}\' --order_status=U --override_jroot=/srv/www/rupostel.com/web/'."\n"; 
		echo 'Above statement will create a new order containing: '."\n"; 
		echo ' - a referenced virtuemart_product_id from virtuemart_order_item_id 847 with quantity 3 and it\'s original attributes - custom values'."\n"; 
		echo ' - a referenced virtuemart_product_id from virtuemart_order_item_id 846 with quantity 4 and it\'s original attributes - custom values'."\n"; 
		echo ' - a referenced virtuemart_product_id from virtuemart_order_item_id 845 with quantity 5 and it\'s original attributes - custom values'."\n"; 
		echo ' - all virtuemart_order_item_id\'s must be part of a single order otherwise applicattion return an error'."\n"; 
		echo ' - user_id is taken from this order and must not be a zero value user_id'."\n"; 
		echo ' - override_jroot points to the root of joomla'."\n"; 
		
		$arr = array(); 
		$arr[847] = 3;
		$arr[846] = 4;
		$arr[845] = 5;
		
		echo ' - Example for json parameter arr[order_item_id] = quantity, arr[order_item_id2] = quantity --order_item_json=\''.str_replace("'", "\'", json_encode($arr)).'\''."\n\n"; 
		echo '--task=neworder'."\n"; 
		echo 'Example usage:  '."\n"; 
		echo '#php cli.php --task=neworder --products_json=\'{"1109":3}\' --user_id=42 --order_status=U --myurl=https://vm2.rupostel.com/purity/'."\n"; 
		echo '- this will create a new order with product_id 1109 and quantity 3 for user_id 42 with order status U and setting myurl override for greater compatiblity with 3rd parties.'."\n\n";
		echo "\n"; 
		echo 'Other input parameters: '."\n"; 
		echo '--debug=1 Will display errors, notices and debug information'."\n"; 
		echo '--return_status_json=1 Will always return json output'."\n"; 
		echo '--virtuemart_shipmentmethod_id=18 Will set shipment method to ID18 for orderp processing functions'."\n"; 
		echo '--virtuemart_paymentmethod_id=18 Will set shipment method to ID18 for orderp processing functions'."\n"; 
		echo '--productsdata_json Custom field values of the products as $arr[$product_id] '."\n"; 
		
		self::flushBuffer(); 
		}
		else {
			self::returnJson(0, $er, $code); 
		}
		
		
		if (class_exists('JFactory')) {
		 $app = JFactory::getApplication(); 
		 $app->close(1); 
		}
		die(1); 
	}
	
	public static function returnJson($order_id, $errorMsg, $code) {
		$arr = array(); 
		$arr['order_id'] = (int)$order_id; 
		$arr['returnMsg'] = $errorMsg; 
		$arr['returnCode'] = (int)$code; 
		if (!empty(self::$debugMsgs)) {
			$arr['debugMsgs'] = self::$debugMsgs; 
		}
		self::writeLog(json_encode($arr)); 
	}
	public static $logfile; 
	public static function writeLog($msg) {
		if (empty($msg)) return; 
		
		if (!empty(self::$logfile)) {
			if (php_sapi_name() !== 'cli') die('Access denied - use CRON to access php directly!'); 
			file_put_contents(self::$logfile, $msg, FILE_APPEND | LOCK_EX); 
		}
		echo $msg; 
	}
	
	public static $debugMsgs; 
	
	public static function setErrorReporting($debug) {
		if (!$debug) {
	
  
  if (empty(cliHelper::$returnjson)) {
   error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);
   @ini_set('display_startup_errors', 1); 
   @ini_set('display_errors', 1);
  }
  else {
   error_reporting(0);
   @ini_set('display_startup_errors', 0); 
   @ini_set('display_errors', 0);
  }
}
else {
	error_reporting(0); 
	@ini_set('display_errors', 0);
}
	}
	
	public static function debug($msg, $nl=true, $nlstart=false) {
		  if (!empty(self::$returnjson)) {
			if (empty(self::$debugMsgs)) self::$debugMsgs = array(); 
		    self::$debugMsgs[$msg] = $msg; 
		  }
		  else {
		   error_reporting(E_ALL); 
		   @ini_set('display_errors', 1); 
		   @ini_set('error_reporting', E_ALL); 
		   if ($nlstart) self::writeLog( "\n" ); 
		   if ($nl) { self::writeLog( 'DEBUG: '); }
		   self::writeLog( $msg ); 
		   if ($nl) self::writeLog( "\n" ); 
		   $outb = ''; 
		   $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); 
		   $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); $outb .= @ob_get_clean(); 
		   self::writeLog( $outb); 
		  }
		  

	}
	
	//http://php.net/manual/en/function.set-error-handler.php
	public static function mapErrorCode($code) {
    $error = $log = null;
    switch ($code) {
        case E_PARSE:
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
            $error = 'Fatal Error';
            $log = LOG_ERR;
            break;
        case E_WARNING:
        case E_USER_WARNING:
        case E_COMPILE_WARNING:
        case E_RECOVERABLE_ERROR:
            $error = 'Warning';
            $log = LOG_WARNING;
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $error = 'Notice';
            $log = LOG_NOTICE;
            break;
        case E_STRICT:
            $error = 'Strict';
            $log = LOG_NOTICE;
            break;
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $error = 'Deprecated';
            $log = LOG_NOTICE;
            break;
        default :
		    $error = $code; 
            break;
    }
       //return array('error'=>$error, 'log'=>$log);
	   return $error;
	}
}





