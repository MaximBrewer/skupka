<?php
/*
*
* @copyright Copyright (C) 2007 - 2013 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* stAn note: Always use default headers for your php files, so they cannot be executed outside joomla security 
*
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

$loadapi = (int)$this->params->loadapi; 
if ($loadapi===1) {
	
	
include(__DIR__.DIRECTORY_SEPARATOR.'zbozi'.DIRECTORY_SEPARATOR."ZboziKonverze.php");

try {

    $zbozi = new ZboziKonverze($this->params->zbozi_cislo, $this->params->zbozi_kod);
    
    // testovací režim
	if (!empty($this->params->sandbox)) {
     $zbozi->useSandbox(true);
	}
foreach ($this->order['items'] as $key=>$order_item) { 
 

    $zbozi->addCartItem(array(
        "productName" => "'".$this->escapeSingle($order_item->order_item_name)."'",
        "itemId" => "'".$this->escapeSingle($order_item->pid)."'",
        "unitPrice" => number_format($order_item->product_final_price, 2, '.', ''),
        "quantity" => number_format($order_item->product_quantity , 0, '.', ''),
    ));
}



$delivery_date = ''; 
if (!empty($this->order['details']['BT']->delivery_date)) {
	$delivery_date = $this->order['details']['BT']->delivery_date;
}
else {
	$delivery_date = $this->order['details']['BT']->order_created;
	if (method_exists('DateTime', 'createFromFormat')) {
	 $format = 'Y-m-d';
	 $delivery_date = DateTime::createFromFormat($format, $delivery_date);
	}
}

    $zbozi->setOrder(array(
        "email" => "'".$this->escapeSingle($this->order['details']['BT']->email)."'",
        "deliveryType" => "'".$this->escapeSingle($this->order['details']['BT']->shipment_name_txt)."'",
        "deliveryPrice" => number_format($this->order['details']['BT']->order_shipment, 2, '.', ''),
        "deliveryDate" => $delivery_date,
        "orderId" => "'".$this->escapeSingle($this->idformat)."'",
        "otherCosts" => 0,
        "paymentType" => "'".$this->escapeSingle($this->order['details']['BT']->payment_name_txt)."'",
        "totalPrice" => number_format($order_total, 2, '.', ''),
    ));

    $zbozi->send();

} catch (ZboziKonverzeException $e) {
    // handle errors
    print "Error: " . $e->getMessage();
}
?>
<script>
	if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: zbozi CURL spusteno'); 
	  }
</script>
<?php

}
elseif ($loadapi === 2) {
	?>
	<script>
  (function(w,d,s,u,n,k,c,t){w.ZboziConversionObject=n;w[n]=w[n]||function(){
    (w[n].q=w[n].q||[]).push(arguments)};w[n].key=k;c=d.createElement(s);
    t=d.getElementsByTagName(s)[0];c.async=1;c.src=u;t.parentNode.insertBefore(c,t)
  })(window,document,"script","https://www.zbozi.cz/conversion/js/conv.js","zbozi","<?php echo $this->params->zbozi_cislo; ?>");

  
  <?php if (!empty($this->params->sandbox)) { ?>
  // zapnutí testovacího režimu
  zbozi("useSandbox");
  <?php } ?>
  // nastavení informací o objednávce
  zbozi("setOrder",{
      "orderId": '<?php echo $this->escapeSingle($this->idformat); ?>',
      "totalPrice": "<?php echo number_format($order_total, 2, '.', ''); ?>"
  });

  // odeslání
  zbozi("send");
  
  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: zbozi JS spusteno'); 
	  }
  
</script>
	
	<?php
	
	
	}
	else {
	

?>



<iframe src="//www.zbozi.cz/action/<?php echo $this->params->zbozi_cislo; ?>/conversion?chsum=<?php echo $this->params->zbozi_kod; ?>&price=<?php echo number_format($this->order['details']['BT']->order_total, 2, ',', ''); ?>&uniqueId=<?php echo $this->idformat;?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" style="position:absolute; top:-3000px; left:-3000px; width:1px; height:1px; overflow:hidden;"></iframe> 


<script>
	if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: zbozi iFrame spusteno'); 
	  }
</script>


<?php 
}