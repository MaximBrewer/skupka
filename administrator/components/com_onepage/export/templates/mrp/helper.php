<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
class mrpHelper { 
  public static $tidd; 
  public static $dir; 
  public static function checkSkus($skus) {
  $session = JFactory::getSession(); 
foreach ($skus as $x) {
	$checked = $session->get('mrp_sku_'.$x, false); 
	if ($checked) continue; 
	
	if (empty($x)) { die('empty SKU !'); } 
	//$x = '123'; 
	$tx = mrpHelper::getStock($x, self::$tidd, self::$dir); 
	
	$xml = simplexml_load_string($tx); 
	$stavy = $xml->xpath('//mrpEnvelope/body/mrpResponse/data/datasets/stavy');
    if (empty($stavy)) {
		
		die('polozka nenajdena v ziadnom sklade '.$x); 
	}
	else {
		$session->set('mrp_sku_'.$x, true); 
		
	}
	
}
  }
  
  public static function render($template, $orders=array(), $skus=array(), $toskip=array(), $ehelper=null) {
	  $tidd = self::$tidd; 
	  $dir = self::$dir; 
	  $html = ''; 
	  if (file_exists(__DIR__.DIRECTORY_SEPARATOR.$template.'.tmpl.php')) {
	  ob_start(); 
	  include(__DIR__.DIRECTORY_SEPARATOR.$template.'.tmpl.php'); 
	  $html = ob_get_clean(); 
	  }
	  return $html; 
  }
  
  public static function generateAndSendXML($orders, $skus, $ehelper, &$msgs) {
	  $order_ids = array();
	  $tidd = self::$tidd; 
	  $dir = self::$dir; 
	  
	  foreach ($orders as $k=>$order) {
		  $order_ids[(int)$order['details']['BT']->virtuemart_order_id] = (int)$order['details']['BT']->virtuemart_order_id;
	  }
ob_start(); 
include(__DIR__.DIRECTORY_SEPARATOR.'order.xml.php'); 
$xml = ob_get_clean(); 



$fn = implode('_', $order_ids); 
$time = time(); 
$xf = $dir.DIRECTORY_SEPARATOR.$fn.'_'.$time.'.xml'; 
file_put_contents($xf, $xml); 



$tx = mrpHelper::send($xml, self::$tidd); 
$xf2 = $dir.DIRECTORY_SEPARATOR.$fn.'_reply_'.$time.'.xml';
file_put_contents($xf2, $tx); 

$xml = simplexml_load_string($tx); 
$err = $xml->xpath('//mrpEnvelope/body/mrpResponse/status/error/errorMessage');
if (!empty($err)) {
$msgs[] = (string)$err[0]; 

}

$data['special_value_ai_0'] = ''; 

$f = $xml->xpath('//mrpEnvelope/body/mrpResponse/data/datasets/objednavka/rows/row/fields');
$msg = $error; 
foreach ($f as $row) {
	$puvodniCislo = (string)$row->puvodniCislo; 
	$puvodniCislo = (int)$puvodniCislo; 
	$cislo = (string)$row->cislo; 

	foreach ($order_ids as $order_id) {
		$order_id = (int)$order_id; 
		if ($puvodniCislo !== $order_id) continue; 
		$msgs[] = 'Objednávka '.$order_id.' importovaná ako '.$cislo; 
		$ehelper->setCustomSpecial($tid, $order_id, $cislo, 'CREATED', $cislo); 
		
		$data['special_value_ai_0'] .= $cislo.'_'; 
	}
	
}


return $msgs; 
  }
  

  public static function getStock($mrp_plu) {
	    $time = time(); 
		
		
		
	    $mrp_plu = str_replace(',', '.', $mrp_plu); 
		
		ob_start(); 
		include(__DIR__.DIRECTORY_SEPARATOR.'stock.xml.php'); 
		$xmlsend = ob_get_clean(); 
		
		if (!empty($dir)) {
		
		 $xf = $dir.DIRECTORY_SEPARATOR.'send_'.$mrp_plu.'_'.$time.'.xml'; 
		 file_put_contents($xf, $xmlsend); 
		}
		
		$xml = self::doCurl($xmlsend); 
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'.$xml; 
		$simpleXml = simplexml_load_string($xml); 
		if ($simpleXml === false) {
			echo 'error: '; var_dump($xml); die(); 
		}
		$dom = dom_import_simplexml($simpleXml)->ownerDocument;
	    $dom->formatOutput = true;
		$fxml = $dom->saveXML();
		
		if (!empty($dir)) {
		
		 $xf = $dir.DIRECTORY_SEPARATOR.$mrp_plu.'_'.$time.'.xml'; 
		 file_put_contents($xf, $fxml); 
		}
		
		return $xml; 
	  
  }

  public static  function send($xml) {
	  
	  return self::doCurl($xml); 
	  


	  
  }
  
  public static  function doCurl($xml) {
	  
$mrpurl = self::$tidd['config']->url;
if (empty($mrpurl)) return 'no MRP URL configured'; 
$ch = curl_init();



curl_setopt($ch, CURLOPT_URL,$mrpurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

$headers = [
    'X-Apple-Tz: 0',
    'X-Apple-Store-Front: 143444,12',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Encoding: gzip, deflate',
    'Accept-Language: en-US,en;q=0.5',
    'Cache-Control: no-cache',
    'Content-Type: application/xhtml+xml; charset=utf-8',
    'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,30);
curl_setopt($ch,CURLOPT_TIMEOUT, 60);
// in real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
// receive server response ...

//echo $httpcode; 

$server_output = curl_exec ($ch);
$httpcode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpcode !== 200) {
	
	die('error sending data, returned http code: '.$httpcode.' to URL '.$mrpurl); 
}

curl_close ($ch);

return $server_output; 
  }
  
}