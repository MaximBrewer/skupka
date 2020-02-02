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

$this->params->dual_mode = (int)$this->params->dual_mode; 

if ((empty($this->params->dual_mode)) || (($this->params->dual_mode === 1) && ($this->order['details']['BT']->country_3_code === 'SVK')) || (($this->params->dual_mode === 2) && ($this->order['details']['BT']->user_currency_code_3 === 'EUR') ))
{


?>

<script type="text/javascript">
/* <![CDATA[ */
if (typeof _hrq == 'undefined') {
 var _hrq = _hrq || [];
 _hrq.push(['setKey','<?php echo $this->escapeSingle($this->params->heureka_key); ?>' ]); 
 _hrq.push(['setOrderId', '<?php echo $this->idformat; ?>' ]);
<?php foreach ($this->order['items'] as $order_item) { ?>
 _hrq.push(['addProduct','<?php echo $this->escapeSingle($order_item->order_item_name); ?>','<?php echo number_format($order_item->product_final_price, 2, '.', ''); ?>','<?php echo number_format($order_item->product_quantity , 0, '.', ''); ?>' ]);
<?php } ?>
 _hrq.push(['trackOrder']);
}

(function() {
var ho = document.createElement('script');
ho.type = 'text/javascript'; ho.async = true;
ho.src = ('https:' == document.location.protocol
? 'https://ssl' : 'http://www') +
'.heureka.sk/direct/js/ext/2-roi-async.js';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(ho, s);
})();


if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: Heureka.sk, meranie konverzii.'); 
	  }
/* ]]> */
</script>
<?php
if (!empty($this->params->allow_visitor_data))
 {


 include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'API'.DIRECTORY_SEPARATOR.'HeurekaOvereno.php'); 
 try {
    $overeno = new OPCHeurekaOvereno($this->params->heureka_secret_key, OPCHeurekaOvereno::LANGUAGE_SK);
    // SK shops should use $overeno = new HeurekaOvereno('9b011a7086cfc0210cccfbdb7e51aac8', HeurekaOvereno::LANGUAGE_SK);
    $email = $this->order['details']['BT']->email; 
    // set customer email - MANDATORY
    $overeno->setEmail($email);
?><script>
if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: Heureka.sk, overene zakaznikmi.'); 
	  }
</script><?php 

    /**
     * Products names should be provided in UTF-8 encoding. The service can handle
     * WINDOWS-1250 and ISO-8859-2 if necessary             
     */
	foreach ($this->order['items'] as $key=>$order_item) { 
    $overeno->addProduct($order_item->order_item_name);
    /**
     * And/or add products using item ID
     */
    $overeno->addProductItemId($order_item->pid);
	}
    // add order ID - BIGINT (0 - 18446744073709551615)
    $overeno->addOrderId($this->idformat);
    // send request
    $overeno->send();
} catch (HeurekaOverenoException $e) {
    // handle errors
   // print $e->getMessage();
}

 
 
 }
}