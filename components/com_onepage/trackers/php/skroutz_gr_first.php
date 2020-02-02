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

?><!-- Add the Tracking Script and Connect to your Account -->
<script>
  (function(a,b,c,d,e,f,g){a[e]= a[e] || function(){
    (a[e].q = a[e].q || []).push(arguments);};f=b.createElement(c);f.async=true;
    f.src=d;g=b.getElementsByTagName(c)[0];g.parentNode.insertBefore(f,g);
  })(window,document,'script','https://analytics.skroutz.gr/analytics.min.js','sa');

  sa('session', 'connect', '<?php echo $this->params->advertiser_id; ?>');  // Connect your Account.
  
  
  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: skroutz.gr tracking initialized'); 
	  }
  
</script>