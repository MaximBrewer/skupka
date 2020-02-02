function addMore(el, rowIns)
{
	var cla = el.id.split('_'); 
	var cl = cla[cla.length - 1]; 
	
	var tt = el.getAttribute('rel', ''); 
	var current_id = el.getAttribute('current_id', ''); 
	
	if ((tt != null) &&  (tt != '')) 
	var d = document.getElementById(tt); 
    else
	var d = document.getElementById('append_to_table'); 
	if (d != null)
	if (typeof jQuery != 'undefined')
	{
		cl = parseInt(cl); 
		current_id = parseInt(current_id); 
		cl++;
		
		if (typeof rowIns[current_id] == 'undefined') return false;  
		
		var new_row = rowIns[current_id].split('{n}').join(cl); 
		el.className += ' more_class_hidden'; 
		jQuery(d).append(new_row); 
	}
	else
	{
		alert('jQuery is required for this feature. If not available, you must click save to get new rows'); 
	}
	return false; 
}