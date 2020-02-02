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


if (!defined('ADWORDS_CONVERSIONS')) {
	
$this->isPureJavascript = false;

?><script type="text/javascript">
/* <![CDATA[ */

if (typeof window.google_tag_params === 'undefined') window.google_tag_params = { }; 

var google_conversion_id =  <?php 
$cid = preg_replace("/[^0-9]/", "", $this->params->google_conversion_id); 
if (empty($cid)) echo '0'; else echo $cid; 

?>;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;


/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" 
  src="https://googleads.g.doubleclick.net/pagead/viewthroughconversion/<?php echo $this->params->google_conversion_id; ?>/?guid=ON&script=0" />
</div>
</noscript>

<script>
	  if ((typeof console != 'undefined')  && (typeof console.log != 'undefined')  &&  (console.log != null))
	  {
	     console.log('OPC Tracking: Adword GTM Remarketing'); 
	  }
</script>

<?php 
}