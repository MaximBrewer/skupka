<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<h2>Update Order Status</h2>
<form action="index.php?option=com_onepage&view=order_details&task=ajax&ajax=yes&tid=<?php echo (int)$tidd['tid']; ?>&cmd=sendXmlMulti" method="post">
  <?php foreach ($orders as $k1=>$order) { ?>
  <table class="uk-table">
    
	<caption>Order <?php echo $order['details']['BT']->order_number.' ('.$order['details']['BT']->virtuemart_order_id.')'; ?> </caption>
    <thead>
        <tr>
            <th>Order Number (Order ID)</th>
			<th>Item</th>
			<th>Current order status</th>
			<th>New order status</th>
			<th><?php $txt = dropshipHelper::getAvaiTitle(); echo JText::_($txt); ?></th>
        </tr>
    </thead>
    <tfoot>
	
        <tr colspan="5">
            <td>Items</td>
        </tr>
		
    </tfoot>
    <tbody>
	<?php 
	$n = 0; 
	foreach ($order['items'] as $k=>$item) { 
	
	
	$order_id = $item->virtuemart_order_item_id;
	
	
	//var_dump( $orders[$k1]['items'][$k]->calculated); die(); 
	?>
        <tr>
            
        
            <td><?php  echo $order['details']['BT']->order_number.' ('.$order['details']['BT']->virtuemart_order_id.')'; ?></td>
			<td><?php echo $item->quantity; ?> pcs | <?php echo $item->product_name; ?> | <?php echo $item->product_sku; ?></td>
			<td><?php echo $_orderStatusList[$item->order_status]; ?> (<?php echo $item->order_status; ?>)</td>
			<td style="color:green"><?php echo $_orderStatusList[$calculatedStatuses[$item->virtuemart_order_item_id]]; ?> (<?php echo $calculatedStatuses[$item->virtuemart_order_item_id]; ?>)</td>
			<td><?php $txt = dropshipHelper::getAvaiValue($item->virtuemart_product_id); echo JText::_($txt);  ?></td>
			
        </tr>
	<?php 
	$n++; 
	} ?>
    </tbody>
</table>
  <?php } ?>


<p>
<button class="uk-button uk-button-primary"><?php echo JText::_('COM_VIRTUEMART_UPDATE_STATUS'); ?></button>
</p>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="view" value="order_details" />
<input type="hidden" name="task" value="ajax" />
<input type="hidden" name="ajax" value="yes" />
<input type="hidden" name="doaction" value="3" />
<input type="hidden" name="order_number" value="0" />
<input type="hidden" name="tid" value="<?php echo (int)$tidd['tid']; ?>" />
<input type="hidden" name="cmd" value="sendxmlmulti" />

<?php 
$vmlimit = JRequest::getVar('vmlimit', 0); 
if (!empty($vmlimit)) { 
?>
<input type="hidden" name="vmlimit" value="<?php echo (int)JRequest::getVar('vmlimit', 0); ?>" />
<input type="hidden" name="vmlimitstart" value="<?php echo (int)JRequest::getVar('vmlimitstart', 0); ?>" />
<input type="hidden" name="vmsearch" value="<?php echo htmlentities(JRequest::getVar('vmsearch', '')); ?>" />

<?php
}



	$n = 0; 
	foreach ($localid as $order_id) { 
	
	?><input type="hidden" name="selectedorder_<?php echo $n; ?>" value="<?php echo $order_id; ?>"/>
	<?php $n++; } ?>

</form>
