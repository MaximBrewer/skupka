	jQuery( function($) {

			$('.orderedit').hide();
			$('.ordereditI').show();
			$('.orderedit').css('backgroundColor', 'lightgray');

			jQuery('.updateOrderItemStatus').click(function() {
				document.orderItemForm.task.value = 'updateOrderItemStatus';
				document.orderItemForm.submit();
				return false
			});

			jQuery('select#virtuemart_paymentmethod_id').change(function(){
				jQuery('span#delete_old_payment').show();
				jQuery('input#delete_old_payment').attr('checked','checked');
			});

		});

		function enableEdit(e)
		{
			jQuery('.orderedit').each( function()
			{
				var d = jQuery(this).css('visibility')=='visible';
				jQuery(this).toggle();
				jQuery('.orderedit').css('backgroundColor', d ? 'white' : 'lightgray');
				jQuery('.orderedit').css('color', d ? 'blue' : 'black');
			});
			jQuery('.ordereditI').each( function()
			{
				jQuery(this).toggle();
			});
			e.preventDefault();
		};

		function addNewLine(e,i) {

			var row = jQuery('#itemTable').find('tbody tr:first').html();
			var needle = 'item_id['+i+']';
			//var needle = new RegExp('item_id['+i+']','igm');
			while (row.contains(needle)){
				row = row.replace(needle,'item_id[0]');
			}

			//alert(needle);
			jQuery('#itemTable').find('tbody').prepend('<tr>'+row+'</tr>');
			e.preventDefault();
		};

		function cancelEdit(e) {
			jQuery('#orderItemForm').each(function(){
				this.reset();
			});
			jQuery('.selectItemStatusCode')
				.find('option:selected').prop('selected', true)
				.end().trigger('liszt:updated');
			jQuery('.orderedit').hide();
			jQuery('.ordereditI').show();
			e.preventDefault();
		}

		function resetOrderHead(e) {
			jQuery('#orderForm').each(function(){
				this.reset();
			});
			jQuery('select#virtuemart_paymentmethod_id')
				.find('option:selected').prop('selected', true)
				.end().trigger('liszt:updated');
			jQuery('select#virtuemart_shipmentmethod_id')
				.find('option:selected').prop('selected', true)
				.end().trigger('liszt:updated');
			e.preventDefault();
		}

		
		
		
		
jQuery('.show_element').click(function() {
  jQuery('.element-hidden').toggle();
  return false;
});
// jQuery('select#order_items_status').change(function() {
	////selectItemStatusCode
	// var statusCode = this.value;
	// jQuery('.selectItemStatusCode').val(statusCode);
	// return false
// });
jQuery('.updateOrderItemStatus').click(function() {
	document.orderItemForm.task.value = 'updateOrderItemStatus';
	document.orderItemForm.submit();
	return false;
});
jQuery('.updateOrder').click(function() {
	document.orderForm.submit();
	return false;
});
jQuery('.createOrder').click(function() {
	document.orderForm.task.value = 'CreateOrderHead';
	document.orderForm.submit();
	return false;
});
jQuery('.newOrderItem').click(function() {
	document.orderItemForm.task.value = 'newOrderItem';
	document.orderItemForm.submit();
	return false;
});
function confirmation(destnUrl) {
	var answer = confirm(COM_VIRTUEMART_ORDER_DELETE_ITEM_JS);
	if (answer) {
		window.location = destnUrl;
	}
}
/* JS for editstatus */

jQuery('.orderStatFormSubmit').click(function() {
	//document.orderStatForm.task.value = 'updateOrderItemStatus';
	document.orderStatForm.submit();

	return false;
});