<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::stylesheet('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/css/uikit.min.css'); 
JHTML::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
JHTML::script('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/js/uikit.min.js'); 

?><form action="index.php?option=com_onepage&view=order_details&task=ajax&ajax=yes&tid=<?php echo (int)$tidd['tid']; ?>&cmd=sendXmlMulti" method="get"><table class="uk-table">
    <caption>Confirm Orders</caption>
    <thead>
        <tr>
			<th>To import</th>
            <th>Order ID</th>
			<th>Order Number</th>
			<th>Imported as</th>
        </tr>
    </thead>
    <tfoot>
	
        <tr colspan="3">
            <td>Orders</td>
        </tr>
		
    </tfoot>
    <tbody>
	<?php 
	$n = 0; 
	foreach ($orders as $k=>$order) { 
	
	
	$order_id = $order['details']['BT']->virtuemart_order_id;
	?>
        <tr>
            <td><input <?php if (!isset($toskip[$order_id])) echo ' checked="checked" '; ?> type="checkbox" name="selectedorder_<?php echo $n; ?>" title="<?php echo $order['details']['BT']->virtuemart_order_id; ?>" value="<?php echo $order['details']['BT']->virtuemart_order_id; ?>"/></td>
        
            <td><?php echo $order['details']['BT']->virtuemart_order_id; ?></td>
        
            <td><?php echo $order['details']['BT']->order_number; ?></td>
			<td><?php if (isset($toskip[$order_id])) echo $toskip[$order_id]; else echo '&nbsp;'; ?></td>
        </tr>
	<?php 
	$n++; 
	} ?>
    </tbody>
</table>
<p>
<button class="uk-button uk-button-primary">Proceed...</button>
</p>
<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="view" value="order_details" />
<input type="hidden" name="task" value="ajax" />
<input type="hidden" name="ajax" value="yes" />
<input type="hidden" name="doaction" value="1" />
<input type="hidden" name="order_number" value="0" />
<input type="hidden" name="tid" value="<?php echo (int)$tidd['tid']; ?>" />
<input type="hidden" name="cmd" value="sendxmlmulti" />
</form>