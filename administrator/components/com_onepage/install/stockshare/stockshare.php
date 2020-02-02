<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* OPC ADS plugin is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 


// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemStockshare extends JPlugin
{
	function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}
	
	function cmdcreatetrigstatus() {
		$db = JFactory::getDBO(); 
		$q = "SHOW TRIGGERS "; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		$found = false; 
		if (!empty($res)) {
			foreach ($res as $k=>$v) {
				if ($v['Trigger'] === 'SHAREDSTOCK') {
					$found = true; 
					/*
					$check = $this->getTSQL(); 
					$current = $v['Statement']; 
					
					if ($check === $current) {
						
					}
					*/
					
				}
			}
		}
		
		if ($found) { echo '<div style="color:green;">Triggerer SHAREDSTOCK Installed</div>'; }
		else {
			echo '<div style="color:red;">Triggerer SHAREDSTOCK NOT Installed</div>'; 
		}
		$prefix = $db->getPrefix();
	   $q = "SHOW TABLES LIKE '".$prefix."virtuemart_sharedstock'";
	   $db->setQuery($q);
	   $r = $db->loadResult();
	if (!empty($r)) {
		echo '<div style="color:green;">Table #__virtuemart_sharedstock OK</div>'; 
	}
	else {
		echo '<div style="color:red;">Table #__virtuemart_sharedstock does not exist</div>'; 
	}
		
		
	}
	
	function cmdcreatetrig() {
		$this->installSQL(); 
	}
	
	private function getTSQL() {
		$config = JFactory::getConfig(); 
	$username = $config->get('user'); 
	$host = $config->get('host'); 
	$dbprefix = $config->get('dbprefix'); 
	$q = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.'trig.sql'); 
	$q = str_replace('{{username}}', $username, $q); 
	$q = str_replace('{{host}}', $host, $q); 
	$q = str_replace('{{prefix}}', $dbprefix, $q); 
	
	return $q; 
	}
	
	private function installSQL() {
		
		$q = 'CREATE TABLE IF NOT EXISTS `#__virtuemart_sharedstock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_id` int(11) NOT NULL,
  `virtuemart_product_id` int(1) NOT NULL,
  `mpn` varchar(160) NOT NULL,
  `product_in_stock` int(11) DEFAULT NULL,
  `product_ordered` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `virtuemart_product_id_2` (`virtuemart_product_id`,`mpn`),
  KEY `virtuemart_product_id` (`virtuemart_product_id`),
  KEY `ref_id` (`ref_id`),
  KEY `mpm` (`mpn`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;'; 
	$db = JFactory::getDBO(); 
	$db->setQuery($q); 
	$db->query(); 
	
	
	
	$q = 'DROP TRIGGER IF EXISTS `SHAREDSTOCK`'; 
	$db->setQuery($q); 
	$db->query(); 
	
	$q = $this->getTSQL(); 
	
	
	
	
	try { 
	 $db->setQuery($q); 
	 $db->query(); 
	}
	catch(Exception $e) {
		echo 'Error creating triggerer '.(string)$e.':<br />'; 
		echo '<textarea style="width:100%;" rows="10">'.$q.'</textarea>'; 
		
	}
	
	echo 'OK'; 
	
	
	
	

		
	}
	
	function onAjaxStockshare() {
		
		if (!$this->_checkPerm()) {
			echo 'This feature is only available to Super Administrators'; 
			JFactory::getApplication()->close(); 
		}
		
		
		
		ob_start(); 
		$post = JRequest::get('post'); 
		$cmd = JRequest::getWord('cmd'); 
		
		$checkstatus = JRequest::getVar('checkstatus', null); 
		if (!empty($checkstatus)) $cmd .= 'status'; 
		
		if (method_exists($this, 'cmd'.$cmd)) {
			$funct = 'cmd'.$cmd; 
			//$this->$cmd($post); 
		    call_user_func(array($this, $funct), $post); 
		}
		else {
			$this->_die('Command not found'); 
		}
		$html = ob_get_clean(); 
		
		@header('Content-Type: text/html; charset=utf-8');
		@header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		@header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		echo $html; 
		JFactory::getApplication()->close(); 
		
		
		
	}
	private function cmdloadstock($post) {
		return $this->cmdloadmpns($post, true);
	}
	private function cmdloadmpns($post, $incstock=false) {
		
		$url = $post['jform']['params']['google_csv_url']; 
		
		$data = $this->_curlget($url);
		if (strpos($data, "\r\n")!== false) {
			$del = "\r\n";
		}
		else {
			$del = "\n"; 
		}
		$DataCsv = str_getcsv($data, $del); //parse the rows 
	    foreach($DataCsv as $k=>$Row) $DataCsv[$k] = str_getcsv($Row, ","); //parse the items in rows 
		$last_mpn = ''; 
		$in_stock = 0; 
		
		$db = JFactory::getDBO(); 
		
		$q = 'START TRANSACTION'; 
		$db->setQuery($q); 
		$db->query(); 
		
		 $q = 'SET @STOCK_TRIG_DISABLED = true'; 
		 $db->setQuery($q); 
	     $db->query(); 
		
		for ($i=1; $i<count($DataCsv); $i++) {
			$row = $DataCsv[$i]; 
			
			if (!empty($row[0])) {
				$last_mpn = trim($row[0]); 
				$in_stock = 0; 
			}
			else {
				$row[0] = $last_mpn; 
				
			}
			
			if (!empty($row[2])) {
				$in_stock = $row[2]; 
			}
			else {
				$row[2] = $in_stock;  
			}
			
			$mpn = trim($row[0]); 
			$sku = trim($row[1]); 
			
			$remove = array("\r", "\n"); 
			$removeto = array("", ""); 
			$mpn = str_replace($remove, $removeto, $mpn);
			$sku = str_replace($remove, $removeto, $sku);
			
			echo 'MPN: '.$mpn.' SKU:'.$sku; 
			if ($incstock) {
			echo ' In Stock:'.$in_stock; 
			}
			echo "<br />\n"; 
				
			if (empty($sku) || (empty($mpn))) continue; 
			
			$mpn = trim($mpn); 
			
			$q = "update #__virtuemart_products set product_mpn = '".$db->escape($mpn)."'"; 
			
			if ($incstock) {
				$q .= ', product_in_stock = '.(int)$in_stock; 
			}
			$q .= " where product_sku = '".$db->escape($sku)."'"; 
			$db->setQuery($q); 
			$db->query(); 
			
		}
		
		 $q = 'COMMIT'; 
			   $db->setQuery($q); 
			   $db->query(); 
		$q = 'START TRANSACTION'; 
		$db->setQuery($q); 
		$db->query(); 
		
		for ($i=1; $i<count($DataCsv); $i++) 
		{
		
		   $row = $DataCsv[$i]; 
			
			if (!empty($row[0])) {
				$last_mpn = $row[0]; 
				$in_stock = 0; 
			}
			else {
				$row[0] = trim($last_mpn); 
				
			}
			
			if (!empty($row[2])) {
				$in_stock = $row[2]; 
			}
			else {
				$row[2] = $in_stock;  
			}
			
			$mpn = trim($row[0]); 
			$sku = trim($row[1]); 
			
			$remove = array("\r", "\n"); 
			$removeto = array("", ""); 
			$mpn = str_replace($remove, $removeto, $mpn);
			$sku = str_replace($remove, $removeto, $sku);
			
			if (empty($sku) || (empty($mpn))) continue; 
			   
			   $q = "select * from #__virtuemart_sharedstock where mpn = '".$db->escape($mpn)."' and ref_id = 0 and virtuemart_product_id = 0 limit 1"; 
			   $db->setQuery($q); 
			   $mainmpn = $db->loadAssoc(); 
			   
			   if ($incstock) {
			   $store_stock = $in_stock; 
			   $store_ordered = 0; 
			   }
			   else {
				   $q = "select product_in_stock from #__virtuemart_products where product_mpn = '".$db->escape($mpn)."' order by product_in_stock asc limit 1"; 
				   $db->setQuery($q); 
				   $min = $db->loadResult(); 
				   $store_stock = (int)$min; 
				   
				   $q = "select product_ordered from #__virtuemart_products where product_mpn = '".$db->escape($mpn)."' order by product_ordered desc limit 1"; 
				   $db->setQuery($q); 
				   $max = $db->loadResult(); 
				   $store_ordered = (int)$max; 
			   }
			   
			
			   
			   if (empty($mainmpn)) {
				   $q = "insert into #__virtuemart_sharedstock (id, ref_id, virtuemart_product_id, mpn, product_in_stock, product_ordered) values ('NULL', 0, 0, '".$db->escape($mpn)."', ".(int)$store_stock.", 0)"; 
				   $db->setQuery($q); 
				   $db->query(); 
				   $ref_id = (int)$db->insertid();
				   
				   echo 'Inserting new main MPN stock info<br />'; 
			   }
			   else {
				   $ref_id = (int)$mainmpn['id']; 
				   echo 'Found master MPN: '.$mpn.'<br />'; 
			   }
			   
			   
			   if (!empty($sku)) {
			   $q = "select virtuemart_product_id, product_ordered from #__virtuemart_products where product_sku = '".$db->escape($sku)."'"; 
			   $db->setQuery($q); 
			   $ressku = $db->loadAssocList(); 
			   
			   if (!empty($ressku)) {
				   echo 'Updating stock for '.$sku.'<br />'; 
				   foreach ($ressku as $rrow) {
					   $virtuemart_product_id = (int)$rrow['virtuemart_product_id']; 
					   $store_ordered = $rrow['product_ordered']; 
					   $q = "select * from #__virtuemart_sharedstock where virtuemart_product_id = ".(int)$virtuemart_product_id." limit 1"; 
					   $db->setQuery($q); 
					   $resline = $db->loadAssoc(); 
					   if (empty($resline)) {
						   $q = "insert into #__virtuemart_sharedstock  (id, ref_id, virtuemart_product_id, mpn, product_in_stock, product_ordered) values ('NULL', ".(int)$ref_id.", ".(int)$virtuemart_product_id.", '".$db->escape($mpn)."', ".(int)$store_stock.", ".(int)$store_ordered.")"; 
						   $db->setQuery($q); 
						   $db->query(); 
					   }
					   else {
						   $id = (int)$resline['id']; 
						   $resline['ref_id'] = (int)$resline['ref_id']; 
						   if (($resline['mpn'] !== $mpn) || ($ref_id !== $resline['ref_id'])) {
							   $q = "update #__virtuemart_sharedstock set mpn = '".$db->escape($mpn)."', ref_id = ".(int)$ref_id.' where `id` = '.(int)$id; 
							   $db->setQuery($q); 
							   $db->query(); 
						   }
					   }
					   
				   }
			   }
			   }
			   
			   
			   }
			   
			    $q = 'SET @STOCK_TRIG_DISABLED = NULL'; 
			   $db->setQuery($q); 
			   $db->query(); 
			   
			   $q = 'COMMIT'; 
			   $db->setQuery($q); 
			   $db->query(); 
		
		
		echo ' Finished '."<br />\n"; 
	}
	
	private function cmddownloadstock() {
		
		@ini_set("memory_limit","512M");
		
		if (!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel.php')) $this->_die('Cannot find PHPExcel in '.JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel');
		
		require_once ( JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel.php');
require_once ( JPATH_ROOT.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'Classes'.DIRECTORY_SEPARATOR.'PHPExcel'.DIRECTORY_SEPARATOR.'IOFactory.php');
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("RuposTel Systems")
							 ->setLastModifiedBy("RuposTel Systems")
							 ->setTitle("OPC Stock Management")
							 ->setSubject("OPC Stock Management")
							 ->setDescription("Stock Management for VirtueMart")
							 ->setKeywords("orders, virtuemart, eshop")
							 ->setCategory("Stock");
		
		
		$objPHPExcel->setActiveSheetIndex(0)
             ->setCellValueByColumnAndRow(0, 1, 'Code');
			$objPHPExcel->setActiveSheetIndex(0)
             ->setCellValueByColumnAndRow(1, 1, 'Art.nr.');
			 $objPHPExcel->setActiveSheetIndex(0)
             ->setCellValueByColumnAndRow(2, 1, 'InStock');
		$objPHPExcel->getActiveSheet()->getStyle("A1:C1")->getFont()->setBold(true);
		
		$db = JFactory::getDBO(); 
		$q = "select product_sku, product_mpn, product_in_stock from #__virtuemart_products where product_mpn <> '' and product_sku <> '' order by product_mpn"; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		
		$last_mpn = ''; 
		if (!empty($res))
		foreach ($res as $ind=>$row) {
			
			
			
			$product_mpn = trim($row['product_mpn']); 
			if ($last_mpn === $product_mpn) {
				$product_mpn_print = ''; 
			}
			else {
				$product_mpn_print = $product_mpn; 
			}
			
			$product_sku = $row['product_sku']; 
			$product_in_stock = $row['product_in_stock']; 
			$rown_n = $ind+2; 
			
			$objPHPExcel->setActiveSheetIndex(0)
             ->setCellValueByColumnAndRow(0, $rown_n, $product_mpn_print);
			$objPHPExcel->setActiveSheetIndex(0)
             ->setCellValueByColumnAndRow(1, $rown_n, $product_sku);
			 $objPHPExcel->setActiveSheetIndex(0)
             ->setCellValueByColumnAndRow(2, $rown_n, $product_in_stock);
			
			$last_mpn = $product_mpn; 
			
		}
		
		

	$objPHPExcel->getActiveSheet()->setTitle('Products');
	$objPHPExcel->setActiveSheetIndex(0);
	

//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$tmp = JPATH_SITE.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'temp'.uniqid().'.tmp'; 
$objWriter->save('php://output'); 
//$objWriter->save($tmp); 

unset($objWriter); 
$objWriter = null; 

@header('Content-Type: application/vnd.ms-excel');
@header('Content-Disposition: attachment;filename="products.xlsx"');
@header('Cache-Control: max-age=0');
	flush(); 
	JFactory::getApplication()->close(); 
	}
	
	private function _curlget($url) {
	
	if (!function_exists('curl_init')) return file_get_contents($url); 
	
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	$err = curl_error($ch); 
	if (!empty($err)) $this->_die($url.'=>'.$err); 
	curl_close($ch);
	return $data;
	}
	
	private function _die($msg) {
		echo $msg; 
		$html = ob_get_clean(); 
		echo $html;
		//@header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Error', true, 500);
		JFactory::getApplication()->close(); 
	}
	
	private function _checkPerm() {
	   $user = JFactory::getUser(); 
	   
      $isroot = $user->authorise('core.admin');	
	  
	  if (!$isroot) 
	  {
		
		return false; 
	  }
	  
	  $iss = JFactory::getApplication()->isSite(); 
	  if (!empty($iss)) return false; 
	  
	  return true; 
   }
	
	function onAfterInitialise() {
		
		if (JFactory::getApplication()->isAdmin()) {
			$db = JFactory::getDBO(); 
			$q = 'SET @JOOMLA_IS_ADMIN = true;'; 
			$db->setQuery($q); 
			$db->query(); 
		}
		else {
			$db = JFactory::getDBO(); 
			$q = 'SET @JOOMLA_IS_ADMIN = false;'; 
			$db->setQuery($q); 
			$db->query(); 
		}
	}
	function plgVmBeforeProductSearch(&$select, &$join, &$where, &$group, &$order, &$joinLang) {
		//var_dump($select); 
	}
	
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		
		if (!JFactory::getApplication()->isSite()) return; 
		//recursive protection: 
		if (!empty(self::$inloop)) return; 
		
		$class = get_class($article); 
		
		
		if (($class == 'TableProducts') || (($class == 'stdClass') && (isset($article->virtuemart_product_id)))) {
		   //self::toObject($article); 
		   if (isset($article->virtuemart_product_id)) {
		    $virtuemart_product_id = (int)$article->virtuemart_product_id; 
			
			if (empty($article->product_mpn)) return; 
			
			$article->product_mpn = trim($article->product_mpn); 
			
			
			$db = JFactory::getDBO(); 
			$q = 'select ref.product_in_stock, ref.product_ordered from #__virtuemart_sharedstock as ref,#__virtuemart_sharedstock as p where (p.virtuemart_product_id = '.(int)$virtuemart_product_id.') and ((p.ref_id = ref.id) and (ref.ref_id = 0)) limit 1'; 
			$db->setQuery($q); 
			$res = $db->loadAssoc(); 
			
			
			
			if (!empty($res)) {
			 $article->product_in_stock = (int)$article->product_in_stock; 
			 $article->product_ordered = (int)$article->product_ordered; 
			 
			 
			 
			 $product_in_stock = (int)$res['product_in_stock'];
			 $product_ordered = (int)$res['product_ordered']; 
			 if (($product_in_stock !== $article->product_in_stock) || ($product_ordered !== $article->product_ordered)) {
			   
			   
			  
			   
			   $q = 'START TRANSACTION'; 
			   $db->setQuery($q); 
			   $db->query(); 
			   
			   $q = 'SET @STOCK_TRIG_DISABLED = true'; 
			   $db->setQuery($q); 
			   $db->query(); 
			   
			   $q = 'update #__virtuemart_products set product_in_stock = '.(int)$product_in_stock.', product_ordered = '.(int)$product_ordered.' where virtuemart_product_id = '.(int)$virtuemart_product_id.' '; 
			   $db->setQuery($q); 
			   $db->query(); 
			   
			   
			   
			   $q = 'SET @STOCK_TRIG_DISABLED = NULL'; 
			   $db->setQuery($q); 
			   $db->query(); 
			   
			   $q = 'COMMIT'; 
			   $db->setQuery($q); 
			   $db->query(); 
			   
			   $article->product_in_stock = $product_in_stock; 
			   $article->product_ordered = $product_ordered;
			   
			   
			 }
			 
			 
			}
		   }
		}
	}
	
	private function enableTriggerer() {
			$db = JFactory::getDBO(); 
			$q = 'SET @JOOMLA_IS_ADMIN = false;'; 
			$db->setQuery($q); 
			$db->query(); 
			
			
	}
	
	private function disableTriggerer() {
			$db = JFactory::getDBO(); 
			$q = 'SET @JOOMLA_IS_ADMIN = true;'; 
			$db->setQuery($q); 
			$db->query(); 
			
			
	}
	
	function plgVmOnUpdateOrderShipment(&$data, $old_status) {
		
		$this->enableTriggerer(); 
	}
	function plgVmOnUpdateOrderPayment(&$data, $old_status) {
		$this->enableTriggerer(); 
	}
	function plgVmOnCancelPayment(&$data, $old_status) {
		$this->enableTriggerer(); 
	}
	function plgVmOnUserOrder(&$data) {
		$this->enableTriggerer(); 
	}
	function plgVmGetProductStockToUpdateByCustom(&$data, $param, $custom) {
		$this->enableTriggerer(); 
	}
	
	function plgVmOnUpdateOrderLineShipment($data) {
		$this->enableTriggerer(); 
	}
	function plgVmOnUpdateOrderLinePayment($data) {
		$this->enableTriggerer(); 
	}
	function plgVmConfirmedOrder($cart, $order) {
		$this->enableTriggerer(); 
	}
	
	function plgVmBeforeStoreProduct(&$data, &$product_data) {
		$this->disableTriggerer(); 
	}
	function plgVmCloneProduct(&$product) {
		$this->disableTriggerer(); 
	}
	
	
	
}