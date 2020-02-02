
if(typeof Tips == "undefined")
	function Tips(){};

var SqueezeBox = new function(){
	
	this.onClose = null;
	
	this.initialize = function(){};
	this.assign = function(){};
	
	
	/**
	 * open fancybox, set onclose function
	 */
	this.open = function(onCloseFunction){
				
		this.onClose = onCloseFunction;
		var url = "index.php?option=com_unitegallery&view=mediaselect";
		
		//var url = "index.php?option=com_media&view=images&tmpl=component";
		
		var options = {};
		options.handler = "iframe";
		options.size = {x:900,y:550};
		
		var objFancybox = jQuery("#fancybox_trigger");
		objFancybox.attr("href",url);
				
		jQuery("#fancybox_trigger").trigger("click");
	};
		
	/**
	 * close fancybox
	 */
	this.close = function(){
		
		jQuery("#fancybox-close").trigger("click");
	};	
};


/**
 * 
 * on inset image, taken from iframe
 */
function jInsertFieldValue(urlImage){
	
	if(typeof SqueezeBox.onClose == "function")
		SqueezeBox.onClose(urlImage);
	
	SqueezeBox.close();
}


/**
 * provider admin class
 */
function UniteProviderAdminUG(){
	
	
	/**
	 * open "add image" dialog
	 */
	this.openAddImageDialog = function(title, onInsert, isMultiple){
		
		SqueezeBox.open(function(urlImage){
			onInsert(urlImage);
		});
		
	};
	
	/**
	 * get shortcode
	 */
	this.getShortcode = function(alias, catid){
		
		var addhtml = "";
		if(catid)
			addhtml = " catid="+catid;
		
		var shortcode = "{unitegallery "+alias + addhtml + "}";
		
		if(alias == "")
			shortcode = "";
		
		return(shortcode);
	};
	
}

