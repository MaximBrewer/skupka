function addMore(el)
{
	var cl = el.id.split('add_more_').join(''); 
	var d = document.getElementById('range_table'); 
		if (d != null)
	if (typeof jQuery != 'undefined')
	{
		cl = parseInt(cl); 
		cl++;
		var new_row = rowIns.split('{n}').join(cl); 
		el.className += ' more_class_hidden'; 
		jQuery(d).append(new_row); 
	}
	else
	{
		alert('jQuery is required for this feature. If not available, you must click save to get new rows'); 
	}
	return false; 
}