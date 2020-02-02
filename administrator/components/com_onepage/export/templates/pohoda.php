<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

$eu = array('AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK', 'HR', 'GR', 'UK'); 
$cdate = $data['cdate_0_named']; //2013-01-01 06:56:48
$time = strtotime($cdate); 
$mth = date('n', $time); 

$ip =  $data['ip_address_0']; 
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR); 

$rates_file = dirname(__FILE__).DS.'data'.DS.'RP-2016_3q.csv'; 
$ppl_file = dirname(__FILE__).DS.'data'.DS.'Downloadpaypal2016_3q.csv'; 


try {

// read reates
$arr = array(); 
if (file_exists($rates_file)) {
	$file = fopen($rates_file,"r");
	if ($file !== false)
while(! feof($file))
  {
    $datae = fgetcsv($file);
	$arr[$datae[0]] = $datae[1]; 
  }

  
fclose($file);
}
// end read rates

//read ppl
$ppla = array(); 
if (file_exists($ppl_file)) {
	
$file = fopen($ppl_file,"r");

if ($file !== false)
while(! feof($file))
  {
    $datap = fgetcsv($file);
    $ppla[] = $datap; 
  }

  
fclose($file);
}
$paypal_data = array(); 
// find order_id: 
$ppl_id = false; 
$order_id = $data['order_id_0']; 

foreach ($ppla as $pr)
{
   if (isset($pr[15]))
   if (!empty($pr[15]))
    {
	   $otext = $pr[15]; 
	   if (stripos($otext, 'Order Number')===0)
	   if (stripos($otext, $order_id)>0)
	     {
		 
		 
		    $txt = str_replace('Order Number: ', '', $otext); 
			if ($txt == $order_id)
			  {
			     $ppl_id = $pr[12]; 
				 $paypal_data = $pr; 
				 break; 
			  }
		 }
	}
}



//end read ppl



$start = strtotime('2014-01-01'); 
$lastrate = 1.3194; 
$notfound = 0; 
for ($i = $start; $i<($start+(366*24*60*60*6)); $i = $i+(60*24*60))
{
 
 $date = date('j.n.Y', $i); //."<br />"; 
 if (isset($arr[$date]))
 {
 $arr[$date] = (float)$arr[$date]; 
 $lastrate = (float)$arr[$date]; 
 }
 else 
 {
	 $arr[$date] = $lastrate; 
	 
 }
 
}





$has_items = false; 
$order_stamp_date = date('j.n.Y', $time); 
if (!isset($arr[$order_stamp_date])) {
	die('Exchange rate not found: '.$time.' '.$order_stamp_date); 
}

$order_rate = $arr[$order_stamp_date]; 
 
 $total = $order_total = (float)$data['order_total_0']; 
$t = (float)$data['order_tax_0']; 
$order_tax = $t; 

if ($mth != 1)
if (stripos($data['order_item_name_0_0'], 'One Page Checkout')!==false)
 {
	 
	$t = (float)$data['order_tax_0']; 
	if (empty($t))
    $total = (float)$data['order_total_0']; 
    else
	{
		$total = (float)$data['order_total_0'];
		$total = $total - $t; 
		
		
		$order_tax = $t; 
		
	}
	$total_a = ((12 - $mth + 1) / 12) * $total; 
	
	// casove rozlisenie predplatneho na 12 mesiacov: 
	$total_a = round($total_a, 2); 
	$total_b = $total - $total_a; 
	
	$a_ratio = $total_a / $total; 
	$b_ratio = $total_b / $total; 
	
	$has_items = true; 
 }

 $currency = $data['order_currency_0']; 

 if (strlen($data['order_item_name_0_0'].' '.$data['product_attribute_0_0'])<=40)
 {
 
 }


if ($data['order_status_0'] == 'C') 
{
	
	
	
	
	
foreach ($data as $k=>$v)
 {
   $data[$k] = iconv('UTF-8', 'cp1250', $v);
   //$data[$k] = preg_replace("/([\xC2\xC4])([\x80-\xBF])/e",  "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)", $data[$k]);
 }
echo '<?xml version="1.0" encoding="Windows-1250"?>'; 
$data['bt_opc_vat_0'] = trim($data['bt_opc_vat_0']); 
$data['bt_opc_vat_0'] = str_replace(' ', '', $data['bt_opc_vat_0']); 
$data['bt_opc_vat_0'] = str_replace('.', '', $data['bt_opc_vat_0']); 
if (!empty($data['bt_opc_vat_0']))
{
$lt = substr($data['bt_opc_vat_0'], 0,2); 
$lt = strtoupper($lt); 
$data['bt_opc_vat_0'] = $lt.substr($data['bt_opc_vat_0'], 2); 
}

$order_id = $data['order_id_0']; 
 
 
  
  $country = array('country_2_code' => $data['country_2_code_0']); 

require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_geolocator'.DS.'assets'.DS.'helper.php');

$ip_country = geoHelper::getCountry2Code($ip);

if (empty($ip_country)) {
 var_dump($ip); 
 die('empty ip country'); 
 $ip_country = $data['country_2_code']; 
}
$is_moss = false; 

				$t = (float)$data['order_tax_0']; 
				
				
				
				if (!empty($t)) { 
				
				$is_moss = true; 
				}
				else
				{
if (in_array($ip_country, $eu))
{
   if ($ip_country != 'SK')
   {
	   
	   if (empty($data['bt_opc_vat_0']))
	   {
		   
		$db = JFactory::getDBO(); 
		$q = 'select * from #__onepage_moss where ip = "'.$db->escape($ip).'" and vat_response_id NOT LIKE "" order by id asc'; 
		$db->setQuery($q); 
		$res = $db->loadAssocList(); 
		$e = $db->getErrorMsg(); if (!empty($e)) die($e); 
		if (!empty($res))
		{
			die('vat removed additionally'); 
		}
	   $is_moss = true; 
	  
	   if (empty($t))
	   {
		    require_once(dirname(__FILE__).DS.'moss.php'); 
			
			$rate = 0; 
			
			$rate = getMossRate($ip_country); 
			
			$data['order_tax_0'] = $order_tax = round($total - ($total / (1+$rate)), 2); 
			
			$data['order_tax_details_0'] = serialize(array($rate => $order_tax)); 
			
	   }
	   }
	   
   }	   
}
				}




  //$return = iconv('UTF-8', 'cp1250', $text);
  //return preg_replace("/([\xC2\xC4])([\x80-\xBF])/e",  "chr(ord('\\1')<<6&0xC0|ord('\\2')&0x3F)", $return);
//echo var_export($data, true); 


$pk = array('opc_eub' => 373,
			'opc_eub2' => 434,
			'opc' => 282, 
			'opc_euf' => 374, 
			'opc_usf' => 376, 
			'opc_usb' => 375, 
			'opc_sk' => 372, 
			'opcsluzby_eub' => 377, 
			'opcsluzby_usb' => 378, ); 

		
 if ($country['country_2_code']=='SK')
  {
    //echo 'ok sk <br />'; 
    //$this->updateOne($db_mbd, $row['ID'], $pk['opc_sk']); 
	$predk = 'opc_sk'; 
  }
  else
  if (in_array($country['country_2_code'], $eu))
   {
     
     if (!empty($res['company']))
	  {
	    //$this->updateOne($db_mbd, $row['ID'], $pk['opc_eub2']); 
		 //echo 'ok eub2 <br />'; 
		 $predk = 'opc_eub2'; 
	  }
	  else
	  {
	  //$this->updateOne($db_mbd, $row['ID'], $pk['opc_euf']); 
	  //echo 'ok euf <br />'; 
	  $predk = 'opc_euf'; 
	  }
   }
   else
   if (!empty($res['company']))
	  {
	    //$this->updateOne($db_mbd, $row['ID'], $pk['opc_usb']); 
		// echo 'ok usb <br />'; 
		$predk = 'opc_usb'; 
	  }
	  else
	  {
	  //$this->updateOne($db_mbd, $row['ID'], $pk['opc_usf']); 
	  //echo 'ok usf	  <br />'; 
	  $predk = 'opc_usf'; 
	  }			

	  
	  ?>

<dat:dataPack id="2223" ico="36644251" application="StwTest" version = "2.0" note="Import FA"        
xmlns:dat="http://www.stormware.cz/schema/version_2/data.xsd"        
xmlns:inv="http://www.stormware.cz/schema/version_2/invoice.xsd"        
xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd" >

<dat:dataPackItem id="IT<?php echo $data['special_value_ai_0']; ?>" version="2.0">

	<!-- faktura bez polozky s adresou, ale bez vazby na adresar -->
		<inv:invoice version="2.0">
			<inv:invoiceHeader>
				<inv:invoiceType>issuedInvoice</inv:invoiceType>
				<inv:symPar><?php echo $data['special_value_ai_0']?></inv:symPar>
				<inv:symVar><?php 
				
				// if we have ppl id, let's put it here: 
				//if (!empty($ppl_id)) echo $ppl_id; 
				//else
				echo '1300'.$order_id; 
				
				?></inv:symVar>
				<inv:date><?php echo date('Y-m-d', $data['cdate_0']); ?></inv:date>
				<inv:dateTax><?php echo date('Y-m-d', $data['cdate_0']); ?></inv:dateTax>
				<inv:number>
					<typ:numberRequested checkDuplicity="true"><?php 
					//echo $num;
					//if (false)
					{
					$num = $data['special_value_ai_0']; 
					echo $num;
					//<typ:ids>2012</typ:ids>
					if (false)
					if ((substr($num, 0,4)=='2012') && (strlen($num)>4))
					{
					$x = substr($num, 4); 
					$x = (int)$x; 
					echo $x; 
					}
					else
					echo $data['special_value_ai_0']; } ?></typ:numberRequested>
				</inv:number>
			
				
				
				<inv:dateAccounting><?php echo date('Y-m-d', $data['cdate_0']); ?></inv:dateAccounting>
				<inv:dateDue><?php echo date('Y-m-d', $data['cdate_0']); ?></inv:dateDue>
				<inv:accounting>
					<typ:ids><?php echo $predk; ?></typ:ids>
				</inv:accounting>
				<inv:classificationKVDPH>
				  <typ:ids>KN</typ:ids>
				</inv:classificationKVDPH>
				<inv:classificationVAT>
					<typ:ids><?php 
					if ($is_moss) echo 'RDzahrSl'; 
					else
					if ((strlen($data['bt_opc_vat_0'])>0) && ($data['bt_country_3_code_0']!= 'SVK'))
					echo 'UDzahrSl'; 
					else echo 'UD'; 
					?></typ:ids>
					<typ:classificationVATType><?php
					echo 'nonSubsume'; 
					if (false)
					 if ($data['bt_country_3_code_0']== 'SVK') echo 'nonSubsume'; 
					else echo 'nonSubsume'; 
					?></typ:classificationVATType>

				</inv:classificationVAT>
				<inv:text><?php echo htmlspecialchars($data['order_item_name_0_0']); ?></inv:text>
				<inv:partnerIdentity>
					<typ:address>
						<typ:company><?php $x = $data['bt_company_0']; 
						echo htmlspecialchars(substr($x, 0, 95)); 
						?></typ:company>
						<typ:division></typ:division>
						<typ:name><?php $name = $data['bt_first_name_0'].' '.$data['bt_last_name_0']; 
						$name = substr($name, 0, 31);
						echo htmlspecialchars($name);						
						?></typ:name>
						<typ:city><?php echo htmlspecialchars(substr($data['bt_city_0'], 0, 45));  ?></typ:city>
						<typ:street><?php echo htmlspecialchars(substr($data['bt_address_1_0'].' '.$data['bt_country_3_code_0'], 0, 64));; ?></typ:street>
						<typ:zip><?php echo htmlspecialchars(substr($data['bt_zip_0'], 0, 15)); ?></typ:zip>
						<typ:ico></typ:ico>
						<typ:dic><?php echo htmlspecialchars(substr($data['bt_opc_vat_0'], 0,18)); ?></typ:dic>
						<typ:icDph><?php echo htmlspecialchars(substr($data['bt_opc_vat_0'], 0, 18)); ?></typ:icDph>
						<typ:country><typ:ids><?php echo htmlspecialchars($country['country_2_code']); ?></typ:ids></typ:country>
						<typ:email><?php echo htmlspecialchars($data['bt_email_0']); ?></typ:email>
					</typ:address>

					<typ:shipToAddress>
					<?php
					  if (!empty($paypal_data))
					   {
					     ?>
						 <typ:email><?php echo htmlspecialchars($paypal_data[10]); ?></typ:email>
						 <typ:company><?php $x = $paypal_data[3]; 
						echo htmlspecialchars(mb_substr($x, 0, 95)); 
						?></typ:company>
						 <typ:city><?php echo htmlspecialchars(mb_substr($paypal_data[35], 0, 45));  ?></typ:city>
						 <typ:country><typ:ids><?php 
			
			$pcountry = $paypal_data[40]; 
			$cx = strlen($paypal_data[40]); 
			
			
			if ($cx == 2)
  {
     echo $paypal_data[40]; 
  }
  else
  if ($cx == 3)
  {
  $q = 'select country_2_code from #__virtuemart_countries where country_3_code = "'.$pcountry.'" limit 1'; 
  $dbj->setQuery($q);
  $country2code = $dbj->loadResult();  
  if (!empty($country2code))
  echo $country2code; 
  }
  else
  {
  $q = 'select country_2_code from #__virtuemart_countries where country_name LIKE "'.$pcountry.'" limit 1'; 
  $dbj->setQuery($q);
  $country2code = $dbj->loadResult();  
	if (!empty($country2code)) echo $country2code; 
  }  			
					
						 ?></typ:ids></typ:country>
						 <typ:street><?php echo htmlspecialchars(substr($paypal_data[35].' '.$paypal_data[40], 0, 64));; ?></typ:street>
						 <typ:zip><?php echo htmlspecialchars(substr($paypal_data[39], 0, 15)); ?></typ:zip>
						 
						 <?php
					   }
					?>
					
					</typ:shipToAddress>
				</inv:partnerIdentity>
				
					<?php 
				if ($is_moss) { ?>
<inv:MOSS>
    <typ:ids><?php
				if ($ip_country == 'GR') $ip_country = 'EL'; 
				if ($ip_country == 'UK') $ip_country = 'GB'; 
				echo htmlspecialchars($ip_country);
				?></typ:ids>
  </inv:MOSS>
				<?php } ?>
				
				<inv:numberOrder><?php 
				if (!empty($ppl_id))
				echo $ppl_id; 
				else
				echo $data['order_id_0']; ?></inv:numberOrder>

				<inv:dateOrder><?php echo date('Y-m-d', $data['cdate_0']); ?></inv:dateOrder>
				<inv:paymentType>
					<typ:ids>draft</typ:ids>
				</inv:paymentType>
				<inv:account>
				<typ:ids><?php if ($data['bt_virtuemart_paymentmethod_id_0']==4) echo 'PPL'; 
				  else
				  if ($data['bt_virtuemart_paymentmethod_id_0']==20) echo 'TABA'; 
				  else
				  if ($data['bt_virtuemart_paymentmethod_id_0']==2) echo 'SLSP'; 
				  
				  ?></typ:ids>
				</inv:account>
				<inv:note><![CDATA[<?php 
				$start = 'XML OPC'; 
				if (!empty($data['customer_note_0']))
				 {
				   echo $data['customer_note_0']; 
				 }
			 
				 else echo $start; 
				 
				 
				 if (!empty($data['product_attribute_0_0'])) echo $data['product_attribute_0_0']."\n\r"; 
				 
				 if (!empty($data['product_attribute_0_1'])) echo $data['product_attribute_0_1']."\n\r"; 
				 
				 if (!empty($data['product_attribute_0_2'])) echo $data['product_attribute_0_2']."\n\r"; 
				 
				 if (!empty($data['product_attribute_0_3'])) echo $data['product_attribute_0_3']."\n\r"; 
				 
				  
				?>]]></inv:note>
				<inv:intNote><![CDATA[<?php 
				$inote = ''; 
				if (!empty($ppl_id))
				echo $ppl_id."\n\r"; 
				
				print_r($data); 
				if (!empty($paypal_data))
				{
				echo "\n\r"; 
				print_r($paypal_data); 
				}
				echo htmlspecialchars($inote); 
				
				
				//print_r($data); 
				?>]]></inv:intNote>
			
				
			</inv:invoiceHeader>	
			<?php if ($has_items)
			 {
			 ?>
			<inv:invoiceDetail>
			<inv:invoiceItem>
				<inv:text><?php echo htmlspecialchars($data['order_item_name_0_0'].' Bezne Obdobie'); ?></inv:text>
				<inv:quantity>1</inv:quantity>
				<?php if ($is_moss) { ?>
				 <inv:payVAT>false</inv:payVAT>
				 <inv:rateVAT>historyHigh</inv:rateVAT>
				 <inv:percentVAT><?php 
				 
				 $data_t = unserialize($data['order_tax_details_0']); 
				 
				 foreach ($data_t as $rate=>$tax_value)
				 {
					 break; 
				 }
				 $rate_t = (int)($rate*100); 
				 echo $rate_t; 
				 ?></inv:percentVAT>
				<?php } ?>
				<inv:classificationKVDPH>
				  <typ:ids>KN</typ:ids>
				</inv:classificationKVDPH>
				
				<inv:classificationVAT>
					<typ:ids><?php 
					if ($is_moss) echo 'RDzahrSl'; 
					else
					if ((strlen($data['bt_opc_vat_0'])>0) && ($data['bt_country_3_code_0']!= 'SVK'))
					echo 'UDzahrSl'; 
					else 
					echo 'UD'; 
					?></typ:ids>
					<typ:classificationVATType><?php
					echo 'nonSubsume'; 
					if (false)
					 if ($data['bt_country_3_code_0']== 'SVK') echo 'nonSubsume'; 
					else echo 'nonSubsume'; 
					?></typ:classificationVATType>

				</inv:classificationVAT>

				
				<inv:accounting>
				  <typ:ids><?php echo $predk; ?></typ:ids>
				</inv:accounting><?php
				
			 if ($currency == 'EUR')
			 {
				?>

<inv:homeCurrency>

<typ:unitPrice><?php echo $total_a; ?></typ:unitPrice>
		<?php if ($is_moss) { ?>				 

<typ:price><?php echo $total_a; ?></typ:price>

<typ:priceVAT><?php echo $a_ratio * $order_tax; ?></typ:priceVAT>
		<?php } ?>
						
					</inv:homeCurrency>
			  <?php 
			  }
			  else
			  {
			  ?>
				
				<inv:foreignCurrency>
					<typ:unitPrice><?php echo $total_a; ?></typ:unitPrice>
				</inv:foreignCurrency>
			 <?php } ?>	
			
			</inv:invoiceItem>
			
			
						<inv:invoiceItem>
						<inv:classificationKVDPH>
				  <typ:ids>KN</typ:ids>
				</inv:classificationKVDPH>
				<inv:text><?php echo htmlspecialchars($data['order_item_name_0_0'].' Bezne Obdobie'); ?></inv:text>
				<inv:quantity>1</inv:quantity>
				<?php if ($is_moss) { ?>
				 <inv:payVAT>false</inv:payVAT>
				 <inv:rateVAT>historyHigh</inv:rateVAT>
				 <inv:percentVAT><?php 
				 $data_t = unserialize($data['order_tax_details_0']); 
				 foreach ($data_t as $rate=>$tax_value)
				 {
					 break; 
				 }
				 $rate_t = (int)($rate*100); 
				 echo $rate_t; 
				 ?></inv:percentVAT>
				<?php } ?>
				<inv:classificationVAT>
					<typ:ids><?php 
					if ($is_moss) echo 'RDzahrSl'; 
					else
					if ((strlen($data['bt_opc_vat_0'])>0) && ($data['bt_country_3_code_0']!= 'SVK'))
					echo 'UDzahrSl'; 
					else echo 'UD'; 
					?></typ:ids>
					<typ:classificationVATType><?php
					echo 'nonSubsume'; 
					if (false)
					 if ($data['bt_country_3_code_0']== 'SVK') echo 'nonSubsume'; 
					else echo 'nonSubsume'; 
					?></typ:classificationVATType>

				</inv:classificationVAT>

				<inv:accounting><typ:ids><?php
				 echo $predk.'_'.$mth; ?></typ:ids></inv:accounting><?php
				
			 if ($currency == 'EUR')
			 {
				?>
				<inv:homeCurrency>
						<typ:unitPrice><?php echo $total_b; ?></typ:unitPrice>
						
						<?php if ($is_moss) { ?>				 
		<typ:price><?php echo $total_b; ?></typ:price>
    <typ:priceVAT><?php echo $b_ratio * $order_tax; ?></typ:priceVAT>
		<?php } ?>
					
						
					</inv:homeCurrency>
			  <?php 
			  }
			  else
			  {
			  ?>
				
				<inv:foreignCurrency>
					<typ:unitPrice><?php echo $total_b; ?></typ:unitPrice>
				</inv:foreignCurrency>
			 <?php } ?>	
			
			</inv:invoiceItem>

			
			
		   </inv:invoiceDetail>	
		   <?php 
		     
		   }
		   ?>

		   
		   <?php if ($is_moss && (!$has_items)) { 
			
			/// moss single imte: 
			?>
			
			<inv:invoiceDetail>
			<inv:invoiceItem>
				<inv:text><?php echo htmlspecialchars($data['order_item_name_0_0'].' Bezne Obdobie'); ?></inv:text>
				<inv:quantity>1</inv:quantity>
				<?php if ($is_moss) { ?>
				 <inv:payVAT>false</inv:payVAT>
				 <inv:rateVAT>historyHigh</inv:rateVAT>
				 <inv:percentVAT><?php 
				 
				 $data_t = unserialize($data['order_tax_details_0']); 
				 
				 foreach ($data_t as $rate=>$tax_value)
				 {
					 break; 
				 }
				 $rate_t = (int)($rate*100); 
				 echo $rate_t; 
				 ?></inv:percentVAT>
				<?php } ?>
				<inv:classificationKVDPH>
				  <typ:ids>KN</typ:ids>
				</inv:classificationKVDPH>
				
				<inv:classificationVAT>
					<typ:ids><?php 
					if ($is_moss) echo 'RDzahrSl'; 
					else
					if ((strlen($data['bt_opc_vat_0'])>0) && ($data['bt_country_3_code_0']!= 'SVK'))
					echo 'UDzahrSl'; 
					else 
					echo 'UD'; 
					?></typ:ids>
					<typ:classificationVATType><?php
					echo 'nonSubsume'; 
					if (false)
					 if ($data['bt_country_3_code_0']== 'SVK') echo 'nonSubsume'; 
					else echo 'nonSubsume'; 
					?></typ:classificationVATType>

				</inv:classificationVAT>

				
				<inv:accounting>
				  <typ:ids><?php echo $predk; ?></typ:ids>
				</inv:accounting><?php
				
			 if ($currency == 'EUR')
			 {
				 $total = $total - $order_tax; 
				?>
				<inv:homeCurrency>
						<typ:unitPrice><?php echo $total; ?></typ:unitPrice>
		<?php if ($is_moss) { 
		
		?>				 
		<typ:price><?php echo $total; ?></typ:price>
    <typ:priceVAT><?php echo $order_tax; ?></typ:priceVAT>
		<?php } ?>
						
					</inv:homeCurrency>
			  <?php 
			  }
			  else
			  {
			  ?>
				
				<inv:foreignCurrency>
					<typ:unitPrice><?php echo $total*$order_rate; ?></typ:unitPrice>
				</inv:foreignCurrency>
			 <?php } ?>	
			
			</inv:invoiceItem>
			 </inv:invoiceDetail>	
			
			<?php } 
			// end moss single item
			?>
		   

			<?php 
			if ($is_moss && (!$has_items)) {
			?>
			
			
			<inv:invoiceSummary>
		    <inv:roundingVAT>noneEveryRate</inv:roundingVAT>	
			</inv:invoiceSummary>
			
			<?php } else { ?>
			<inv:invoiceSummary>
		    <inv:roundingVAT>noneEveryRate</inv:roundingVAT>			
			<?php
			if ($currency == 'EUR')
			 {
				?>

				<inv:homeCurrency>
				    <?php if (!$is_moss) { ?>
					<typ:priceNone><?php echo $data['order_total_0']; ?></typ:priceNone>
				    <?php } else {  ?>
				    <typ:priceHighSum><?php echo $order_total; ?></typ:priceHighSum>
					<typ:priceHighVAT><?php echo $order_tax; ?></typ:priceHighVAT>
<?php
					}
					//$unit = $data['order_total_0'] - $order_tax; 
					
					

					if (false) { ?> 
					<typ:unitPrice><?php echo $total; ?></typ:unitPrice> 
				    <typ:price><?php echo $total; ?></typ:price>
					<typ:priceVAT><?php echo $order_tax; ?></typ:priceVAT>
					
					<?php } ?>
				</inv:homeCurrency>
				<?php }
				else 
				{
				
					
				?>
				 <inv:foreignCurrency>
				    <typ:currency>
					  <typ:ids>USD</typ:ids>
					</typ:currency>
					
					
					<typ:rate><?php echo $order_rate; ?></typ:rate>
					<typ:amount>1</typ:amount>
					<typ:priceSum><?php echo $data['order_total_0']; ?></typ:priceSum>
				 </inv:foreignCurrency>
				<?php
				}
				?>
			</inv:invoiceSummary>
			<?php } ?>
		</inv:invoice>

	</dat:dataPackItem>


</dat:dataPack>
<?php
}
}
catch (Exception $e) {
	echo $e; 
	die(); 
}