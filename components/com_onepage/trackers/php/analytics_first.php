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
if (!empty($this->params->universalga)) $uga = 'true'; 
else $uga = 'false'; 

$tracker_name = 'OPCTracker'; //.$idformat; 

?>
<script type="text/javascript">
//<![CDATA[
   
   // if google universal analytics enabled + it is not initialized + old GA is not created
   if ((<?php echo $uga; ?>) && ((typeof ga == 'undefined') && ((typeof _gat == 'undefined'))))
   {
    <?php 
	include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ga_init.php'); 
    
	?>
	
	
   }
   
   
   // if universal analytics is not initialized, check if OLD GA is initialized
  if (((typeof gaJsHost == 'undefined') || (typeof _gat == 'undefined')) && (typeof ga == 'undefined'))
   {
   
      <?php include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'_gat_init.php'); ?>
	  
	  
	  
   }
   


  if (typeof ga != 'undefined')
   {
      <?php 
	  include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ga_create.php'); 
	  ?>
	 
	
      <?php 
	  // if normal ecommerce tracking enabled: 
	  if (!empty($this->params->ec_type)) 
	  { 
	  // if normal ec
	   include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ecommerce_init.php'); 	
	  //if normal ec
	  } 
	  else 
	  {   //if enhanced
		include(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'ec_init.php'); 
	  }
	 ?>
  
   }
   else
   {
	   if (typeof _gat != 'undefined')
	   {
	     var pageTracker = _gat._getTracker("<?php echo $this->params->google_analytics_id; ?>");
  pageTracker._trackPageview();
  
  										
if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: _gat pageTracker _trackPageview'); 
	  }
	   }

   }
  
//]]>
</script>
