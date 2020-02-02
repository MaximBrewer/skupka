  function cartStackOnChange(setSiteID) {
	  if (typeof Onepage == 'undefined') return; 
	  var extraquery = Onepage.buildExtra(op_userfields, true); 
	  
	  
	  try {
	  var pp = parseQuerySring(extraquery); 
	 
	 
	  
	  
	  var _cartstack_update = [];
        _cartstack_update.push(['setSiteID', setSiteID]);
         if (typeof pp != 'undefined') {
		_cartstack_update.push(['setDataItem', pp]);
		 
		 if (typeof pp['email'] != 'undefined') {
		    _cartstack_update.push(['setEmail', pp.email]);
		 }
		 
		 }
		
		if (typeof cartstack_updatecart != 'undefined')
        cartstack_updatecart(_cartstack_update);
		
		
		 }
	   catch (e) {
          console.log(e); 
	  }
	} 
	
	function updateEmailCartStack(setSiteID, email) {
	
	if (typeof email == 'undefined') return;
	if (email.split('@').length !== 2) return;
	
	if (typeof Onepage == 'undefined') return; 
	 
	  
	  
	  try {
	
	  
	  var _cartstack_update = [];
        _cartstack_update.push(['setSiteID', setSiteID]);
        
		 
		
		    _cartstack_update.push(['setEmail', email]);
		 
		 
		 
		
		if (typeof cartstack_updatecart != 'undefined')
        cartstack_updatecart(_cartstack_update);
		
		
		 }
	   catch (e) {
          console.log(e); 
	  }
	}
	
	function parseQuerySring(query)
	{
		
		var match,
        pl     = /\+/g,  // Regex for replacing addition symbol with a space
        search = /([^&=]+)=?([^&]*)/g,
        decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); };
        

    urlParams = {};
    while (match = search.exec(query))
       urlParams[decode(match[1])] = decode(match[2]);
   
	return urlParams; 
	}
	