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

<script type="text/javascript" src="<?php echo $this->params->zanox_url; ?>&mode=<?php echo $this->params->mode; ?>&CustomerID=<?php echo $this->order['details']['bt']->virtuemart_user_id; ?>&OrderID=<?php echo $this->idformat; ?>&CurrencySymbol=<?php echo $this->order['details']['BT']->currency_code_3; ?>&TotalPrice=<?php echo number_format($this->order['details']['BT']->order_total, 2, '.', ''); ?>&PartnerID=<?php echo $this->params->partner_id; ?>"></script>
<noscript>
<img src="<?php echo $this->params->zanox_url; ?>&mode=2&CustomerID=<?php echo $this->order['details']['bt']->virtuemart_user_id; ?>&OrderID=<?php echo $this->idformat; ?>&CurrencySymbol=<?php echo $this->order['details']['BT']->currency_code_3; ?>&TotalPrice=<?php echo number_format($this->order['details']['BT']->order_total, 2, '.', ''); ?>&PartnerID=<?php echo $this->params->partner_id; ?>" width="1" height="1" />
</noscript>