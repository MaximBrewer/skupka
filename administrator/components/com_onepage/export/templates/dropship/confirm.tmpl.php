<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('jquery.framework');
JHTML::stylesheet('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/css/uikit.min.css'); 
JHTML::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
JHTML::script('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/js/uikit.min.js'); 

?><form action="index.php?option=com_onepage&view=order_details&task=ajax&ajax=yes&tid=<?php echo (int)$tidd['tid']; ?>&cmd=sendXmlMulti" method="post">
  <?php foreach ($groups as $mf_id => $group) { ?>
  <table class="uk-table">
    
	<caption>Supplier <?php echo $mfs[$mf_id]['mf_name']; ?></caption>
    <thead>
        <tr>
			
            <th>Order ID</th>
			<th>Order Number</th>
			<th>Item data</th>
			<th>Imported as</th>
        </tr>
    </thead>
    <tfoot>
	
        <tr colspan="4">
            <td>Items</td>
        </tr>
		
    </tfoot>
    <tbody>
	<?php 
	$n = 0; 
	foreach ($group as $k=>$item) { 
	
	
	$order_id = $item->virtuemart_order_item_id;
	?>
        <tr>
            
        
            <td><input <?php if (!isset($toskip[$virtuemart_order_item_id])) echo ' checked="checked" '; ?> type="hidden" name="selecteditem_<?php echo $n; ?>" title="<?php echo $item->virtuemart_order_item_id; ?>" value="<?php echo $item->virtuemart_order_item_id; ?>"/><?php echo $item->order['details']['BT']->virtuemart_order_id; ?></td>
        
            <td><?php echo $item->order['details']['BT']->order_number; ?></td>
			<td><?php echo $item->quantity; ?> pcs | <?php echo $item->product_name; ?> | <?php echo $item->product_sku; ?></td>
			<td><?php if (isset($toskip[$virtuemart_order_item_id])) echo $toskip[$virtuemart_order_item_id]; else echo '&nbsp;'; ?></td>
        </tr>
	<?php 
	$n++; 
	} ?>
    </tbody>
</table>
  <?php } ?>

  <fieldset data-uk-margin>
   <?php foreach ($groups as $mf_id => $group) {
	   $email = $mfs[$mf_id]['mf_email']; 
	   ?><label for="mfmailid_<?php echo $mf_id; ?>"><input type="checkbox" checked="checked" value="<?php echo $mf_id; ?>" id="mfmailid_<?php echo $mf_id; ?>" name="mf_sendmail[<?php echo $mf_id; ?>]" /><?php echo $email; ?></label>
	   <?php
   }
	   ?>
	   
	   <label for="mfmailid_joomla"><input type="checkbox" checked="checked" value="joomla" id="mfmailid_joomla" name="mf_sendmail[joomla]" /><?php echo JFactory::getConfig()->get('mailfrom'); ?></label>
	   
	   <?php if (!empty($tidd['config']->sendcc)) { ?>
	   <label for="mfcc"><input type="checkbox" checked="checked" disabled="disabled" value="joomla" id="mfcc" name="mfcc[joomla]" /><?php echo $tidd['config']->sendcc; ?></label>
	   <?php } ?>
	   
	   
  </fieldset>
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