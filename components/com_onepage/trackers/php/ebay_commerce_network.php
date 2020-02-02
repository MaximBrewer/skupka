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
$oc =  (int)(((float)$this->order['details']['BT']->order_shipment )*100); 
$os = (int)($order_total * 100); 

$order_total_txt = number_format($order_total, 2, '.', ''); 

$order_costs = (float)$this->order['details']['BT']->order_shipment + (float)$this->order['details']['BT']->order_payment; 
$order_costs_txt = number_format($order_costs, 2, '.', ''); 


$idformat = $this->idformat; 
 


?>
<script type="text/javascript">
var _roi = _roi || [];

// Schritt 1: Hinzufügen der grundlegenden Bestelldetails

_roi.push(['_setMerchantId', '<?php echo $this->escapeSingle($this->params->merchantID); ?>']); // erforderlich
_roi.push(['_setOrderId', '<?php echo $this->escapeSingle($idformat); ?>']); // eindeutige Bestellnr.
_roi.push(['_setOrderAmount', '<?php echo $order_total_txt; ?>']); // Warenkorbwert inklusive Steuern und Versandkosten
_roi.push(['_setOrderNotes', '']); // Hinweise zur Bestellung, maximal 50 Zeichen

// Schritt 2: Hinzufügen aller Artikel in der Bestellung,
// bei denen Ihre E-Commerce-Engine alle Artikel im Warenkorb durchläuft und für jeden Artikel _addItem ausgibt
// Beachten Sie, dass die Reihenfolge der Werte beibehalten werden muss, um das Funktionieren des ROI Trackers zu gewährleisten.
<?php
foreach ($this->order['items'] as $key=>$order_item) {
if (empty($order_item->order_item_sku)) $id = 'id_'.$order_item->virtuemart_product_id; 
else $id = $order_item->order_item_sku; 
?>
_roi.push(['_addItem',
'<?php echo $this->escapeSingle($id); ?>', // Händler-Artikelnr.
'<?php echo $this->escapeSingle($order_item->order_item_name); ?>', // Produktname
'<?php echo $this->escapeSingle($order_item->virtuemart_category_id); ?>', // Kategorie-ID
'<?php echo $this->escapeSingle($order_item->category_name); ?>', // Kategorie-Name
'<?php echo number_format($order_item->product_final_price, 2, '.', ''); ?>', // Einzelpreis (inkl. MwSt.)
'<?php echo $order_item->product_quantity; ?>' // Artikelmenge
]);
<?php 
}
?>
// Schritt 3: Übermitteln der Transaktion an den ROI-Tracker vom eBay Commerce Network

_roi.push(['_trackTrans']);
</script>
<script type="text/javascript" src="<?php echo $this->params->scripturl; ?>"></script>
<?php
