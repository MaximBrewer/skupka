function listbox_selectall(listID, isSelect) 
{  
	var listbox = document.getElementById(listID);  
    for(var count=0; count < listbox.options.length; count++)   
    	listbox.options[count].selected = isSelect;  
} 
