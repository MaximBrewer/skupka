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
$order_total = $this->order['details']['BT']->order_total;


if (!empty($this->params->universalga)) $uga = 'true'; 
else $uga = 'false'; 

// generic fix: 
if (empty($this->order['details']['BT']->currency_code_3))
$this->order['details']['BT']->currency_code_3 = 'USD'; 


 $idformat = $this->idformat; 
 $tracker_name = 'OPCTracker'; //.$idformat; 
 
 
 if (!empty($this->params->server2server))
 {
	 //php_ga_addtransaction.php
	 try 
	 {
	 include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'php-ga'.DIRECTORY_SEPARATOR.'autoload.php'); 
	 include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'php_ga_addtransaction.php'); 
	 
	 ?><script>
	  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: php ga server2server order tracking sent'); 
	  }
	  </script><?php
	 
	 }
	 catch (Exception $e)
	 {
		 ?><script>
	  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: php ga server 2 server order tracking ERROR'); 
	  }
	  </script><?php
	 }
	 
	 
 }
 else
 {
 
 
?>
<script type="text/javascript">
//<![CDATA[
  
  
  // if universtal analytics is initialized
  if (typeof ga != 'undefined')
   {
	   
	  
	   
      <?php 
	  $app = JFactory::getApplication();
	  
			if ($app->getName() === 'administrator') {
				
				
	  ?>
		ga('OPCTracker.set', 'nonInteraction', true);
	  <?php
		}
			//include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ga_create.php'); 
	  ?>
	
      <?php 
	  // if normal ecommerce tracking enabled: 
	  if (!empty($this->params->ec_type)) 
	  { 
	  // if normal ec
	   //added to _first: include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ecommerce_init.php'); 	
 	   include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ecommerce_addtransaction.php'); 	
		
	foreach ($this->order['items'] as $key=>$order_item) 
	{ 
   // add item might be called for every item in the shopping cart
   // where your ecommerce engine loops through each item in the cart and
   // prints out _addItem for each 
   if (empty($order_item->category_name)) $order_item->category_name = ''; 
   if (!empty($order_item->virtuemart_category_name)) $order_item->category_name = $order_item->virtuemart_category_name;  
   
    include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ecommerce_additem.php'); 	
    
	} 
		
		// maybe we are missing action here ??
	   //added to _last:  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ecommerce_send.php'); 
		////added later: include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'pageview.php'); 
	
	  }  //if normal ec
	  else 
	  {   //if enhanced
	
		// enhanced ecommerce GA
		//added to _first: include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ec_init.php'); 
		
		
		if ($order_total < 0)
		{
		include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ec_refund.php'); 
		
		
		
		}
		else
		{		
			foreach ($this->order['items'] as $key=>$order_item) 
			{ 
			// add item might be called for every item in the shopping cart
			// where your ecommerce engine loops through each item in the cart and
			// prints out _addItem for each 
			if (empty($order_item->category_name)) $order_item->category_name = ''; 
			if (!empty($order_item->virtuemart_category_name)) $order_item->category_name = $order_item->virtuemart_category_name;  
   
			include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ec_addproduct.php'); 
			
			
			
			} 
			//end of foreach
			$action = 'purchase'; 
			include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ec_action.php'); 
	    

		
		}
	  //added later: include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'pageview.php'); 

	  
	}  // end if enhanced ec
	
	?>
	
	  

	
   
   }
   else
   {

	


  
  <?php 
  
  
  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'_gat_addtrans.php');  

  
  foreach ($this->order['items'] as $key=>$order_item) { 
   // add item might be called for every item in the shopping cart
   // where your ecommerce engine loops through each item in the cart and
   // prints out _addItem for each 
    if (empty($order_item->category_name)) $order_item->category_name = ''; 
   if (!empty($order_item->virtuemart_category_name)) $order_item->category_name = $order_item->virtuemart_category_name;  
   include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'_gat_additem.php');  
   
   
   
   
   } 
   ?>
   
   
   if (typeof pageTracker != 'undefined')
   {
   pageTracker._trackTrans(); //submits transaction to the Analytics servers
   

  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: _gat pageTracker _trackTrans'); 
	  }
   }

   
   
   }

  
//]]>
</script>

<?php 
 }
 // server2server or javascript tracking...