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

$lang = JFactory::getLanguage();
$language_tag = $lang->getTag();

if (!empty($this->order['details']['BT']->order_language))
{
	$language_tag = $this->order['details']['BT']->order_language; 
}
//echo $language_tag;

/** Begin TradeTracker Virtuemart code DE**/
if (($language_tag == "de-DE") && (!empty($this->params->tt_campaignID))) { 
	/** Begin TradeTracker Virtuemart code DE**/
	$tt_campaignID = $this->params->tt_campaignID; 
	$tt_productID = $this->params->tt_productID; 
	$tt_trackingGroupID = $this->params->tt_trackingGroupID; 
	$tt_scriptTag = include_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'tradetracker'.DIRECTORY_SEPARATOR.'tradetracker.php');
	echo $tt_scriptTag;
	/** End TradeTracker Virtuemart code DE **/

	/** Conversion Pixel - Car Bags DE - Remarketing **/
	if (!empty($this->params->AppNexusID)) { ?>
	<script src="https://secure.adnxs.com/px?id=<?php echo $this->params->AppNexusID; ?>&t=1" type="text/javascript"></script> 
	
	<script>
	if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: Trade Tracker DE'); 
	  }
	
	</script>
	<?php  }
	/** End of Conversion Pixel **/					

} elseif (($language_tag == "nl-NL") && (!empty($this->params->tt_campaignIDNL ))) { 				
	/** Begin TradeTracker Virtuemart code NL**/
	$tt_campaignID = $this->params->tt_campaignIDNL; 
	$tt_productID = $this->params->tt_productIDNL; 
	$tt_trackingGroupID = $this->params->tt_trackingGroupIDNL; 
	$tt_scriptTag = include_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'tradetracker'.DIRECTORY_SEPARATOR.'tradetracker.php');
	echo $tt_scriptTag;
	/** End TradeTracker Virtuemart code NL**/

	/** Conversion Pixel - Car Bags NL - Remarketing **/
	if (!empty($this->params->AppNexusIDNL)) { ?>
	<script src="https://secure.adnxs.com/px?id=<?php echo $this->params->AppNexusIDNL; ?>&t=1" type="text/javascript"></script> 
	<?php  }
	/** End of Conversion Pixel **/	
	if (!empty($this->params->journy_code)) { ?>		
	<img src="//drs2.veinteractive.com/DataReceiverService.asmx/Pixel?journeycode=<?php echo $this->params->journy_code; ?>" width="1" height="1"/>	
	<?php  }
	
	?>
	<script>
	if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: Trade Tracker NL'); 
	  }
	
	</script>
	
	<?php
	
	
}
