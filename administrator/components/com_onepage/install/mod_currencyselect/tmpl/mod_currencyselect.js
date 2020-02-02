function changeCurrency(el)
{
	var d = document.getElementById('cur_virtuemart_currency_id'); 
	var id = 0; 
	if (typeof el.rel != 'undefined')
		id = el.rel; 
	else
	if (typeof el.getAttribute != 'undefined')
		 id = el.getAttribute('rel'); 
	else
	{
		if (typeof jQuery != 'undefined')
		id = jQuery(el).getAttr('rel'); 
	}
	if (id != 0)
	if (d != null)
	{
		d.value = id; 
		
	}
	return true; 
}