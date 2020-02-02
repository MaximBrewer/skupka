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

?>
	  if (typeof ga != 'undefined')
	  ga('<?php echo $tracker_name.'.'; ?>send', 'pageview'<?php 
			  if (!empty($this->params->page_url) && (!empty($this->isPurchaseEvent)))
			  {
			  echo ', { '."\n\r"; 
			  echo "'page': '".$this->params->page_url."',\r\n"; 
			  echo "'title': '".$this->escapeSingle($this->params->page_title)."'\r\n"; 
			  echo "}\r\n";
			  }
			  else
			  if (!empty($this->isCartEvent))
			  {
				  
			  echo ', { '."\n\r"; 
			  echo "'page': '/cart',\r\n"; 
			  echo "'title': 'OPC Cart Page'\r\n"; 
			  echo "}\r\n";
				  
			  }				  ?>);
			  
			  
	if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
  
  <?php if (!empty($this->params->page_url) && (!empty($this->isPurchaseEvent))) {  ?>
	    	  console.log('OPC Tracking: GA triggering page view - sending data to GA: Page URL: <?php echo $this->escapeSingle($this->params->page_url).' Page title: '.$this->escapeSingle($this->params->page_title); ?>'); 
  <?php } else { ?>
      	  console.log('OPC Tracking: GA triggering page view - sending data to GA'); 
  <?php } ?>
	  }
			  
		