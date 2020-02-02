<?php
/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
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
$order_total = number_format($order_total, 2, '.', ''); 


if (!defined('ADWORDS_CONVERSIONS')) {
	define('ADWORDS_CONVERSIONS', 'PRODUCT'); 
}

//stAn, we include generic datalayer.php now with purchase event: 

$this->params->tag_event = 'purchase'; 
$this->params->adwords_remarketing = false; 

?><script type="text/javascript">
/* <![CDATA[ */

if (typeof dataLayer == 'undefined')
	dataLayer = []; 

<?php 
include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'datalayer.php'); 
?>

/* ]]> */
</script>





<script type="text/javascript">
/* <![CDATA[ */

if (typeof window.google_tag_params === 'undefined') window.google_tag_params = { }; 

var google_conversion_id =  <?php 
$cid = preg_replace("/[^0-9]/", "", $this->params->google_conversion_id); 
if (empty($cid)) echo '0'; else echo $cid; 

?>;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = false;

<?php 


// generic fix: 
if (empty($this->order['details']['BT']->currency_code_3))
$this->order['details']['BT']->currency_code_3 = 'USD'; 


$this->isPureJavascript = false;
?>
var google_conversion_currency = "<?php echo $this->order['details']['BT']->currency_code_3; ?>";
var google_conversion_order_id = "<?php echo $this->order['details']['BT']->virtuemart_order_id; ?>";
var google_conversion_label = "<?php echo $this->escapeDouble($this->params->google_conversion_label); ?>";

/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/<?php echo $this->params->google_conversion_id; ?>/?value=<?php echo $order_total; ?>&oid=<?php echo (int)$this->order['details']['BT']->virtuemart_order_id; ?>&guid=ON&script=0&currency=<?php echo $this->order['details']['BT']->currency_code_3; ?>" />
</div>
</noscript>

<script>
	  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: Adword GTM conversion tracking initialized'); 
	  }
</script>