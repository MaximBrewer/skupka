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

$aVariables = array(
      'si' => $this->params->iProgramId,
      'ti' => $this->order['details']['BT']->virtuemart_order_id,
      'oa' => urlencode(mb_substr($this->order['details']['BT']->first_name, 0, 40)),
      'om' => urlencode(mb_substr($this->order['details']['BT']->last_name, 0, 40)), 
      'bd' => number_format($this->order['details']['BT']->order_total, 2, '.', ''),
      'ln' => (int)$this->order['details']['BT']->virtuemart_country_id,
      'pc' => urlencode(trim($this->order['details']['BT']->zip)),
      'rv' => number_format($this->order['details']['BT']->order_total, 2, '.', ''),
      'e1' => urlencode(mb_substr($this->order['details']['BT']->payment_name, 0, 40)),
      'e2' => 'sku',
      'e3' => 1
   );
   $sUrl = "//ds1.nl/t/";
   $sGlue = "?";

   foreach ($aVariables as $sKey => $mValue)
   {
      $sUrl .= $sGlue.$sKey."=".urlencode($mValue);
      $sGlue = "&";
   } 
?>
<img src='<?php echo $sUrl; ?>' style='border: 0px; height: 1px; width: 1px;' alt='Adverteren via Daisycon' />