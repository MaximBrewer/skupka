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

var gaJsHost = (("https:" == document.location.protocol ) ? "https://ssl." : "http://www.");
  document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
  
  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: Initializing _gat object'); 
	  }
	
  if (typeof _gat == 'undefined')
  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
  {
	  console.log('OPC Tracking: _gat object was not initialized, please update to Universal Analytics, google dropped support for _gat tracking'); 
  }	  
	  

	  