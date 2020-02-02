<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('jquery.framework');
JHTML::stylesheet('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/css/uikit.min.css'); 
JHTML::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
JHTML::script('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.8/js/uikit.min.js'); 


?>
<h1>Fix Errors</h1>
<form action="index.php?option=com_onepage&view=order_details&task=ajax&ajax=yes&tid=<?php echo (int)$tidd['tid']; ?>&cmd=sendXmlMulti" method="post"><table class="uk-table">
    <caption>Please fix missing manufacturer errors first</caption>
    <thead>
        <tr>
			<th>Manufacturer</th>
            <th>Product Name</th>
			<th>Product SKU</th>
			
        </tr>
    </thead>
    <tfoot>
	
        <tr colspan="3">
            <td>Products</td>
        </tr>
		
    </tfoot>
    <tbody>
	<?php 
	$n = 0; 
	
	
	
	foreach ($errors as $product_id=>$item) { 
	
	
	
	?>
        <tr>
            <td><select name="manufacturer[<?php echo $item->virtuemart_product_id; ?>]" /><option value=""><?php echo JText::_('COM_ONEPAGE_NOT_CONFIGURED'); ?></option>
			<?php foreach ($mfs as $mf) { ?>
			<option value="<?php echo $mf['virtuemart_manufacturer_id']; ?>"><?php echo $mf['mf_name']; ?></option>
			<?php } ?>
			</select>
			  
			</td>
        
            <td><?php echo $item->product_name; ?></td>
			<td><?php echo $item->product_sku; ?></td>
        
            
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
<input type="hidden" name="doaction" value="2" />
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
?>
	
	
	<?php
	$n = 0; 
	foreach ($localid as $order_id) { 
	
	?><input type="hidden" name="selectedorder_<?php echo $n; ?>" value="<?php echo $order_id; ?>"/>
	<?php $n++; } ?>


</form>