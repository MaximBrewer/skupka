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



if (!empty($this->params->universalga)) $uga = 'true'; 
else $uga = 'false'; 

// generic fix: 
if (empty($this->order['details']['BT']->currency_code_3))
$this->order['details']['BT']->currency_code_3 = 'USD'; 


  $idformat = $this->idformat; 
 


?>


<script>
  if (typeof dataLayer == 'undefined')
  dataLayer = [];
</script>

<script type="text/javascript">
//<![CDATA[
<?php

 
   include_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_onepage'.DIRECTORY_SEPARATOR.'trackers'.DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'GA'.DIRECTORY_SEPARATOR.'datalayer.php'); 
 
?>
//]]>
</script>

<?php 
  $this->isPureJavascript = true; 
 
