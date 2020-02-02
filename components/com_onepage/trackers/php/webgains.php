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

/*    <variables to be assigned valueat run time>    */
$wgOrderValue = number_format($order_total, 2, '.', ''); # total order value in the currency your program runs in (please do not include currency symbol)
$wgOrderReference = rawurlencode($this->order['details']['BT']->order_number);
$wgEventID=$this->params->commision_type; # this identify's the commission type (in account under Program Setup (commission types))
$wgComment= rawurlencode(''); #optional field
$wgMultiple=1;

$items = ''; 
$type = $this->params->price_type;
foreach ($this->order['items'] as $key=>$order_item) {
  if (!empty($order_item->$type)) $uprice = $order_item->$type; 
  else $uprice = $order_item->product_final_price; 
  if (!empty($items)) $items .= '|'; 
  $items .= $wgEventID.'::'.number_format($uprice, 2, '.', '').'::'.rawurlencode($order_item->order_item_name).'::'.rawurlencode($order_item->order_item_sku).'::'.rawurlencode($this->order['details']['BT']->order_number);
}
$wgItems= $items;
/*    (optional) should contain pipe separated list of shopping basket items. Fields for each item are seperated by double colon.
                         First field is commission type ,  second field is price of item, third field (optional) is name of item, fourth field (optional) is product code/id, fifth field (optional) is voucher code. Example for two items; items=1::54.99::Harry%20Potter%20dvd::hpdvd93876|5::2.99::toothbrush::tb287::voucher1    */
$wgCustomerID= '';# please do not use without contacting us first
$wgProductID= '';# please do not use without contacting us first


/*    </variables to be assigned values at run time>    */

/*    <variables to be assigned values on install>    */
$wgSLang = 'php';# string, used to identify the programming language of your online systems. Needed because url_encoding differs between platforms.
$lang = $this->order['details']['BT']->order_language; 
if (empty($lang))
 {
   $l = JFactory::getLanguage(); 
   $lang = $l->getTag(); 
 }
if (empty($lang)) $lang = 'en-GB'; 
$lang = str_replace('-', '_', $lang); 

$wgLang = $lang; # string, used to identify the human language of the transaction
$wgPin = (string)$this->params->pin;# pin number provided by webgains (in account under Program Setup (program settings -> technical setup))
$wgProgramID = (int)$this->params->ProgramID; # int, used to identify you to webgains systems
$wgVoucherCode = rawurlencode(''); #string, used to store the voucher code used for transaction
$wgCurrency = $this->params->currency; #only the following are valid: AUD,CAD,CHF,CZK,DKK,EUR,GBP,HKD,HUF,JPY,NOK,NZD,PLN,SEK,SGD,SKK,USD
/*    </variables to be assigned values on install>    */

/*    <not user configurable>    */
$wgVersion = '1.2';
$wgSubDomain="track";
$wgCheckString ="wgver=$wgVersion&wgsubdomain=$wgSubDomain&wglang=$wgLang&wgslang=$wgSLang&wgprogramid=$wgProgramID&wgeventid=$wgEventID&wgvalue=$wgOrderValue&wgorderreference=$wgOrderReference&wgcomment=$wgComment&wgmultiple=$wgMultiple&wgitems=$wgItems&wgcustomerid=$wgCustomerID&wgproductid=$wgProductID&wgvouchercode=$wgVoucherCode";
$wgCheckSum=md5($wgPin.$wgCheckString); # make checksum
$wgQueryString = $wgCheckString."&wgchecksum=".$wgCheckSum."&wgCurrency=".$wgCurrency;
$wgUri = '://'.$wgSubDomain.".webgains.com/transaction.html?".$wgQueryString;
/*    </not user configurable>    */
?>


<script language="javascript" type="text/javascript">
if(location.protocol.toLowerCase() == "https:") wgProtocol="https";
else wgProtocol="http";
wgUri = wgProtocol + "<?php echo($wgUri);?>" + "&wgprotocol=" + wgProtocol + "&wglocation=" + location.href;
document.write('<sc'+'ript language="JavaScript"  type="text/javascript" src="'+wgUri+'"></sc'+'ript>');
</script>

<noscript>
<img src="http://<?php echo($wgSubDomain);?>.webgains.com/transaction.html?wgrs=1&<?php echo($wgQueryString);?>&wgprotocol=https" alt="" width="1" height="1"/>
</noscript>