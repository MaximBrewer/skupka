function bindFancyBox(el)
{
	// jquery must be loaded: 
	if (typeof el.data == 'undefined') return faceboxBinder(el); 
	
	if (!el.data("fancybox")) {
	el.data("fancybox", true);
	
	// fancybox must be loaded: 
	if (typeof el.fancybox == 'undefined') return faceboxBinder(el); 
	
	var params = {
	 href: el.attr('href'),
	 type: 'iframe',
	 autoDimensions: true,
	 overlayShow: true
	};
	el.data('rel', el.rel); 
	var rr = {}; 
	
	var relData = el.attr('rel'); 
	if (relData != null)
	{
	
	  try {
		var str = 'var rr = '+relData+';';
		eval(str); 
		
		if (rr != null)
		{
			if (typeof rr.size != 'undefined')
			if (typeof rr.size.x != 'undefined')
			{
			  params.width = rr.size.x; 	
			}
			if (typeof rr.size != 'undefined')
			if (typeof rr.size.y != 'undefined')
			{
			  params.height = rr.size.y; 	
			}
		}
		
	  }
	  catch (e) { 
	 
	  }
	
	}	
	
	
	
	el.attr('rel', ''); 
	el.fancybox(params); 
	}
	return false; 
}


function faceboxBinder(el)
{
	// jquery must be loaded: 
	if (typeof el.data == 'undefined') return; 
	
	if (!el.data("facebox")) {
	el.data("facebox", true);
	
	// facebox must be loaded: 
	if (typeof el.facebox == 'undefined') return; 
	
	var params = {
	 href: el.attr('href'),
	 type: 'iframe',
	 autoDimensions: true,
	 overlayShow: true
	};
	el.data('rel', el.rel); 
	var rr = {}; 
	
	var relData = el.attr('rel'); 
	if (relData != null)
	{
	
	  try {
		var str = 'var rr = '+relData+';';
		eval(str); 
		
		if (rr != null)
		{
			if (typeof rr.size != 'undefined')
			if (typeof rr.size.x != 'undefined')
			{
			  params.width = rr.size.x; 	
			}
			if (typeof rr.size != 'undefined')
			if (typeof rr.size.y != 'undefined')
			{
			  params.height = rr.size.y; 	
			}
		}
		
	  }
	  catch (e) { 
	 
	  }
	
	}	
	
	
	
	el.attr('rel', ''); 
	//el.fancybox(params); 
	el.facebox( params, 'my-groovy-style' ); 
	}
	return false; 
}

var clickSemafor = false;
function op_openlink(el)
{
  if (typeof jQuery == 'undefined')
  {
	  window.open(el.href,'','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
	  return false; 
  }
	// should be binded with JHTMLOPC::_('behaviour.modal'); 
	if (el.className.indexOf('modal')>=0) return false; 
	// check for legacy: 
	if (typeof SqueezeBox != 'undefined')
	if (typeof $ != 'undefined')
	if (typeof document.id != 'undefined')
	{
		 // if we got mootools, do nothing... 
		 if (el.className.indexOf('modal')>=0) return false; 
		 var options = {}; 
		 var rel = jQuery(el).attr('rel'); 
		 if ((rel != null) && (typeof rel != 'undefined'))
		 {
			 try {
				var str = ' options = '+rel+';'; 
				eval(str); 
			 }
			 catch (e) {
			 }
			 
			 options.parse = 'rel'; 
		 }
		 else
		 {
			 el.setAttribute('rel', ''); 
			 options.parse  = ''; 
		 }
		 if (typeof options.size == 'undefined')
		 {
			 options.size = {}; 
			 options.size.x = 500; 
			 options.size.y = 400; 
		 }
		 if (typeof options.handler == 'undefined') options.handler = 'iframe'; 
		 console.log(rel); 
		 console.log(options); 
		 var me = document.id(el); 
		 SqueezeBox.fromElement(me, options); 
		 return false; 
	}
	
	if (typeof el == 'undefined')
	if (!el) return false; 
	if (window.clickSemafor) 
	{
		window.clickSemafor = true; 
		return false; 
	}
	
 
  if ((typeof jQuery != 'undefined') && ((typeof jQuery.fancybox != 'undefined')) && (typeof jQuery.facebox == 'undefined'))
  {
	   var e = jQuery(el); 
	   if (typeof e.fancybox == 'undefined') return false; 
	   
	   if (typeof e.data != 'undefined')
	   if (e.data('fancybox')===true) return false; 
	   
	   if (typeof bindFancyBox != 'undefined')
	   {
		   bindFancyBox(e); 
	   }
	   else
	   {
		  e.fancybox({
        type: 'iframe',
        href: e.attr('href')
		});  
	   }
	   e.attr('rel', ''); 
	  
	  {
		  
		window.clickSemafor = true; 
		e.trigger('click'); 
		
		  
	  }
  }
  
  if (el.className.indexOf('modal')>=0) return false; 
  
  
  
  return false;
}
