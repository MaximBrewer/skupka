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
ga('create', '<?php echo $this->params->google_analytics_id; ?>', 'auto', {'name': '<?php echo $tracker_name."'"; 

$user_id = JFactory::getUser()->get('id'); 
if (!empty($user_id)) echo ", 'userId': '".$user_id."' "; 

 ?>});
 
 
 	  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: Creating new GA tracker'); 
	  }
