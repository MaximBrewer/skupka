var UniteAdmin = new function(){
	
	var t = this;
	
	var errorMessageID = null;
	var successMessageID = null;
	var ajaxLoaderID = null;
	var ajaxHideButtonID = null;
	var colorMoveEventFunc = null;
	
	
	/**
	 * debug html on the top of the page (from the master view)
	 */
	t.debug = function(html){
		jQuery("#div_debug").show().html(html);
	}
	
	/**
	 * output data to console
	 */
	t.trace = function(data,clear){
		if(clear && clear == true)
			console.clear();	
		console.log(data);
	}
	
	/**
	 * show error message
	 */
	t.showErrorMessage = function(message){
		var html = "<div class='error_message_box' id='system-message-error'>"+message+"</div>";
		jQuery("#system-message-container").html(html);		
	}
	
	/**
	 * hide error message
	 */
	var hideErrorMessage = function(){
		jQuery("#system-message-container").html("");
	}
	
	/**
	 * how success message
	 */
	var showSuccessMessage = function(message){
		var html = "<div class='success_message_box' id='system-message-success'>"+message+"</div>";
		jQuery("#system-message-container").html(html);
 		
		//hide the message delay
		if(jQuery('#system-message-success').length)
			setTimeout("jQuery('#system-message-success').hide('slow')",3000);
		
	}
	
	/**
	 * init color pickers input
	 */
	var initColorPickers = function(){
		//appent div to the body
		var fields = jQuery("input.color-picker");
		if(fields.length == 0)	
			return(false);
		
		jQuery("body").append("<div id='farbtastic_wrapper' class='farbtastic_wrapper' style='display:none;'><div id='farb_picker'></div></div>");
		
		var picker = jQuery.farbtastic('#farb_picker');
		var wrapper = jQuery("#farbtastic_wrapper");
		 
		fields.each(function(){
			picker.linkTo(this);
		});
		
		fields.focus(function(){
			wrapper.show();
			picker.linkTo(this);
			var input = jQuery(this);
			var offset = input.offset();
			
			//set picker position
			wrapper.css({
				"left":offset.left + input.width()+20,
				"top":offset.top - wrapper.height() + 150
			});
			
		}).click(function(){			
			return(false);	//prevent body click
		});
		
		wrapper.click(function(){
			return(false);	//prevent body click
		});
		
		jQuery("body").click(function(){
			wrapper.hide();
		});
	}
	
	/**
	 * init checkbox form field
	 */
	var initCheckboxes = function(){
		jQuery(".mycheckbox_check").click(function(){
			var strChecked = this.checked?"true":"false";
			jQuery(this).siblings(".mycheckbox_input").val(strChecked);
		});
	}

	
	/**
	 * set color picker move event function.
	 */
	t.onColorPickerMove = function(func){
		colorMoveEventFunc = func;
	}
	
	/**
	 * on color picker move event. pass event to stored functions.
	 */
	t.onColorPickerMoveEvent = function(){
		if(colorMoveEventFunc) colorMoveEventFunc();
	}
	
	/**
	 * init every page in the project
	 */
	t.initGlobal = function(){
		initColorPickers();
		initCheckboxes();
	}
	
	/**
	 * hide system message with delay
	 */
	t.hideSystemMessageDelay = function(){
		
		if(jQuery('#system-message').length)		
			setTimeout("jQuery('#system-message').hide('slow')",1000);
	}
	
	
	/**
	 * set ajax loader id that will be shown, and hidden on ajax request
	 * this loader will be shown only once, and then need to be sent again.
	 */
	this.setAjaxLoaderID = function(id){
		ajaxLoaderID = id;
	}
	
	/**
	 * show loader on ajax actions
	 */
	var showAjaxLoader = function(){
		if(ajaxLoaderID)
			jQuery("#"+ajaxLoaderID).show();
	}
	
	/**
	 * hide and remove ajax loader. next time has to be set again before "ajaxRequest" function.
	 */
	var hideAjaxLoader = function(){
		if(ajaxLoaderID){
			jQuery("#"+ajaxLoaderID).hide();
			ajaxLoaderID = null;
		}
	}
	
	/**
	 * set button to hide / show on ajax operations.
	 */
	this.setAjaxHideButtonID = function(buttonID){
		ajaxHideButtonID = buttonID;
	}
	
	/**
	 * if exist ajax button to hide, hide it.
	 */
	var hideAjaxButton = function(){
		if(ajaxHideButtonID)
			jQuery("#"+ajaxHideButtonID).hide();
	}
	
	/**
	 * if exist ajax button, show it, and remove the button id.
	 */
	var showAjaxButton = function(){
		if(ajaxHideButtonID){
			jQuery("#"+ajaxHideButtonID).show();
			ajaxHideButtonID = null;
		}		
	}
	
	
	/**
	 * Ajax request function. call wp ajax, if error - print error message.
	 * if success, call "success function" 
	 */
	this.ajaxRequest = function(action,data,successFunction){
		
		var objData = {
			action:action,
			client_action:action,
			data:data
		}
		
		hideErrorMessage();
		//showAjaxLoader();
		//hideAjaxButton();
		
		jQuery.ajax({
			type:"post",
			url:g_urlAjax,
			dataType: 'json',
			data:objData,
			success:function(response){
				//hideAjaxLoader();
				
				if(!response){
					t.showErrorMessage("Empty ajax response!");
					return(false);					
				}

				if(response == -1){
					t.showErrorMessage("ajax error!!!");
					return(false);
				}
				
				if(response == 0){
					t.showErrorMessage("ajax error, action: <b>"+action+"</b> not found");
					return(false);
				}
				
				if(response.success == undefined){
					t.showErrorMessage("The 'success' param is a must!");
					return(false);
				}
				
				if(response.success == false){
					t.showErrorMessage(response.message);
					return(false);
				}
				
				//success actions:

				//run a success event function
				if(typeof successFunction == "function")
					successFunction(response);
				else{
					if(response.message)
						showSuccessMessage(response.message);
				}
				
				if(response.is_redirect)
					location.href=response.redirect_url;
			
			},		 	
			error:function(jqXHR, textStatus, errorThrown){
				hideAjaxLoader();
				
				if(textStatus == "parsererror")
					t.debug(jqXHR.responseText);
				
				t.showErrorMessage("Ajax Error!!! " + textStatus);
			}
		});
		
	}//ajaxrequest
	
	/**
	 * upen "add image" dialog
	 */
	this.openAddImageDialog = function(title,onInsert){
		
		if(!title)
			title = 'Select Image';
		var params = "type=image&post_id=0&TB_iframe=true";
		
		params = encodeURI(params);
		
		tb_show(title,'media-upload.php?'+params);
		
		window.send_to_editor = function(html) {
			 tb_remove();
			 var urlImage = jQuery('img',html).attr('src');
			 onInsert(urlImage);
		}
	}
	
	/**
	 * load css file on the fly
	 * replace current item if exists
	 */
	this.loadCssFile = function(urlCssFile,replaceID){
		
		//jQuery("#paradigmslider-captions-css").remove();
		
		jQuery("head").append("<link>");
		var css = jQuery("head").children(":last");
		css.attr({
		      rel:  "stylesheet",
		      type: "text/css",
		      href: urlCssFile
		});
		
		//replace current element
		if(replaceID){
			jQuery("#"+replaceID).remove();
			css.attr({id:replaceID});
		}
	}
	
	/**
	 * hide form field
	 */
	this.hideFormField = function(field){
		jQuery("#"+field).hide();
		jQuery("#"+field+"-lbl").hide();
		jQuery("#"+field+"-btn").hide();
	}
	
	this.showFormField = function(field){
		jQuery("#"+field).show().removeClass("hidden");
		jQuery("#"+field+"-lbl").show().removeClass("hidden");
	}
	
}

//script for global init
jQuery(document).ready(function(){
	UniteAdmin.initGlobal();
})


//user functions:
function trace(data,clear){
	UniteAdmin.trace(data,clear);
}

function debug(data){
	UniteAdmin.debug(data);
}

