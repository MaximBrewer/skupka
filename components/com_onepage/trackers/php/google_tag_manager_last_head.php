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





if (!empty($this->params->adwords_remarketing)) {
 ?>
<script>
if (typeof dataLayer == 'undefined')
	dataLayer = []; 


if (typeof dataLayerImpr !== 'undefined')
dataLayer.push(dataLayerImpr); 

if ((typeof console != 'undefined') && (typeof console.log == 'function')) {
 console.log('OPC Tracking GTM Datalayer (last_head)', dataLayer); 
}

if (dataLayer.length == 0)
{
	if (typeof window.google_tag_params === 'undefined') window.google_tag_params = { }; 
	
	dataLayer.push({
    'event': '<?php echo $this->escapeSingle($this->params->tag_event); ?>',
    'google_tag_params': window.google_tag_params
   });
   
   if ((typeof console != 'undefined') && (typeof console.log == 'function')) {
		console.log('OPC Tracking GTM: Adding empty remarketing tag', dataLayer); 
	}
}
</script>
 
 <?php
$this->isPureJavascript = true; 
}

