/**
** Parts of this code is written by joomlaprofessionals.com Copyright (c) 2012, 2015 All Right Reserved.
** Many part of this code is from VirtueMart Team Copyright (c) 2004 - 2015. All rights reserved.
** Some parts might even be Joomla and is Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved. 
** http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
** This source is free software. This version may have been modified pursuant
** to the GNU General Public License, and as distributed it includes or
** is derivative of works licensed under the GNU General Public License or
** other free or open source software licenses.
**
** THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY 
** KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
** IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
** PARTICULAR PURPOSE.

** <author>Joomlaproffs / Virtuemart team</author>
** <email>info@joomlaprofessionals.com</email>
** <date>2015</date>
*/

var selectedpaymentid = 0;
var action = "";
var countrychange = "";
var popupopen = 0;
var button_primary = "opg-button-danger";
var button_danger = "opg-button-danger";
var form_danger =  "opg-form-danger";

jQuery(document).ready(function(){
    button_primary = vmonepage.button_primary;
    button_danger = vmonepage.button_danger;
	form_danger = vmonepage.form_danger;
								
	jQuery(".opg-alert").hide();
	//jQuery("#system-message-container").hide();
	
	
	
	if (window._klarnaCheckout) {
            window._klarnaCheckout(function (api) {
                    api.on({
                        'shipping_address_change': function (datas) {
						  if(window.KLARNALOCALE == 'en-us'  || window.KLARNALOCALE == 'en-gb')
						   {		
							 	 if(!popupopen)
								{
								 	jQuery("#loadingbutton").click();											  
									popupopen = true;
								}											  
								jQuery.ajax({
						        	type: "POST",
							        cache: false,
							        dataType: "json",
									url: window.vmSiteurl + "index.php?&option=com_virtuemart&view=cart&vmtask=klarnaupdate",
									data : jQuery.param(datas),
						        }).done(
							    function (data, textStatus){
									  action = "updateaddress"; 
									  update_shipment();
                			    });
						    }
                        }
                     });
					api.on({
					    'change': function(data) {
							jQuery.ajax({
						        	type: "POST",
							        cache: false,
							        dataType: "json",
									url: window.vmSiteurl + "index.php?&option=com_virtuemart&view=cart&vmtask=klarnacartupdate",
									data :  jQuery("#klarna_fields :input").serialize(),
						        }).done(
							    function (data, textStatus){
                			    });
					    }
			  		});
            });
	}
	if(vmonepage.popupaddress > 1)
	{
	     jQuery('#billtopopup').on({
	     'show.uk.modal': function()
	 	  {
			 jQuery('#BTsameAsST').prop('checked', false);
      	  },
	      'hide.uk.modal': function(){
		   value = validatebillto("yes");
		   if(value == true)
		   {
			   jQuery("#billtobutton").removeClass(button_danger);    
			   jQuery("#billtobutton").addClass(button_primary);  
		   }
		   else
		   {	
		     jQuery("#billtoicon").hide();
	    	 jQuery("#billtobutton").removeClass(button_primary);
			 jQuery('#BTsameAsST').prop('checked', true);
		   }
		  }
		});
	}
	else
	{
	  jQuery('#shiptopopup').on({
		'show.uk.modal': function()
		{
			 jQuery('#STsameAsBT').prop('checked', false);
		},
		  'hide.uk.modal': function(){
		   value = validateshipto("yes");
		   
		   if(value == true)
		   {
			  
		   }
		   else
		   {
			 jQuery("#shiptoicon").hide();
			 jQuery("#shiptobutton").removeClass(button_primary);
			 jQuery('#STsameAsBT').prop('checked', true);
		   }
		}
	  });
	}
	jQuery(".refreshbutton").each(function(){
	   jQuery(this).click(function(){
			update_product(jQuery(this).attr("data-itemid"));	
	 	});
	});


	jQuery("#shipmentset").click(function(){
		setshipment();
	});

	jQuery("#paymentset").click(function(){
		setpayment();
	});
	jQuery(".removeproduct").each(function(){
	   jQuery(this).click(function(){
			removeproduct(jQuery(this).attr("data-itemid"));
	   });
	});
});

jQuery(window).load(function() {

if(jQuery('#checkoutForm').length  > 0)
	{
		firsttime_updateprice  = "yes";	
		Virtuemart.product(jQuery("div.product"));
		update_prices();
	}
});





function validatecomment()
{
		 
  if(jQuery("#commentpopup #customer_note_field").hasClass("required"))
  {
  
     comval = jQuery("#commentpopup #customer_note_field").val();
	 if(comval == "")
	 {
	    jQuery("#commentpopup #customer_note_field").addClass(form_danger);
		jQuery("#commenticon").hide();
		jQuery("#commentbutton").removeClass(button_primary);
	 }
	 else
	 {
		 	 
	 	 jQuery("#commenticon").show();
		 jQuery("#commentbutton").addClass(button_primary);
	     jQuery("#commentpopup #customer_note_field").removeClass(form_danger);
	     updatecart();   
    	 jQuery("#commentclose").click();
	 }

  }
  else
  {
  	comval = jQuery("#commentpopup #customer_note_field").val();
	 if(comval == "")
	 { 
		 jQuery("#commenticon").hide();
		 jQuery("#commentbutton").removeClass(button_primary);
		 updatecart();   
	     jQuery("#commentclose").click();
	 }
	 else
	 {	
		 jQuery("#commenticon").show();
		 jQuery("#commentbutton").addClass(button_primary);
	     jQuery("#commentpopup #customer_note_field").removeClass(form_danger);
    	 updatecart();   
	     jQuery("#commentclose").click();
	 }
  }
}

function removeshipto()
{
	 jQuery('#shipto_fields_div input').each(function() 
     {
		  elementid = jQuery(this).attr("id");
		  jQuery("#"+elementid).val("");
	 });
     jQuery('#STsameAsBT').prop('checked', true);
	 updatecart();   
     jQuery("#shiptoclose").click();
}

function validatebillto(returnval)
{
	var validator=new JFormValidator();  
	var billtoaddress_valid = true;
	jQuery('#billto_fields_div input').each(function(){
												 
		var validatefield = validator.validate(this);
		elementid = jQuery(this).attr("id");
		if(validatefield == false)
		{
		  billtoaddress_valid = false;	 
		  jQuery("#"+elementid).addClass(form_danger);
		}
		else
		{
		  jQuery("#"+elementid).removeClass(form_danger);
		}
	 });

	 
   	 country_ele =  document.getElementById('virtuemart_country_id');
	 if(jQuery("#virtuemart_country_id").length > 0)
	 {
	     var validatefield = validator.validate(country_ele);
		 if(validatefield == false)
		 {
			  billtoaddress_valid = false;
			  jQuery("#virtuemart_country_id").addClass(form_danger);
 		 }
		 else
		 {
			  jQuery("#virtuemart_country_id").removeClass(form_danger);
	  	 }
	 }
	 
	 state_ele =  document.getElementById('virtuemart_state_id');
	 if(jQuery("#virtuemart_state_id").length > 0)
	 {
	     var validatefield = validator.validate(state_ele);
		 if(validatefield == false)
		 {
			  billtoaddress_valid = false;
			  jQuery("#virtuemart_state_id").addClass(form_danger);
	 	 }
		 else
		 {
			  jQuery("#virtuemart_state_id").removeClass(form_danger);
	  	 }
	}
	if(returnval == "yes")
	{
	   return billtoaddress_valid;
	}
	if(!billtoaddress_valid) 
	{
	     jQuery("#billtoicon").hide();
	     jQuery("#billtobutton").removeClass(button_primary);
		 return false;
	}
	else
	{
	   jQuery("#billtoicon").show();
	   jQuery("#billtobutton").addClass(button_primary);
	   updateaddress(4);
	}
	
}

function validateshipto(returnval)
{
	var shipaddress_valid = true;
	if(jQuery('#STsameAsBT').prop('checked') ==true)
	{
	   jQuery("#shiptoicon").hide();
       jQuery("#shiptobutton").removeClass(button_primary);
	  
	}
	else
	{
		var validator=new JFormValidator();
		jQuery('#shipto_fields_div input').each(function() {
			var validatefield = validator.validate(this);
			elementid = jQuery(this).attr("id");
			if(validatefield == false)
			{
			  shipaddress_valid = false;	 
			  jQuery("#"+elementid).addClass(form_danger);
			}
			else
			{
			  jQuery("#"+elementid).removeClass(form_danger);
			}
		});
		
		 country_ele2 = jQuery('#shipto_virtuemart_country_id');
		 if(jQuery('#shipto_virtuemart_country_id').length > 0)
		 {
	    	 var validatefield = validator.validate(country_ele2);
			 if(validatefield == false)
			 {
				  shipaddress_valid = false;
				  jQuery("#shipto_virtuemart_country_id").addClass(form_danger);
		 	 }
			 else
			 {
			  jQuery("#shipto_virtuemart_country_id").removeClass(form_danger);
		  	 }
		 }
		 state_ele2 = jQuery('#shipto_virtuemart_state_id');
		 if(jQuery('#shipto_virtuemart_state_id').length > 0)
		 {
	    	 var validatefield=validator.validate(state_ele2);
			 if(validatefield == false)	
			 {
				  shipaddress_valid = false;
				  jQuery("#shipto_virtuemart_state_id").addClass(form_danger);
		 	 }	
			 else
			 {
				  jQuery("#shipto_virtuemart_state_id").removeClass(form_danger);
		  	 }
		 }
	}
	if(returnval == "yes")
	{
	   return shipaddress_valid;
	}
	if(!shipaddress_valid) 
	{
	     jQuery("#shiptoicon").hide();
	     jQuery("#shiptobutton").removeClass(button_primary);
		 return false;
	}
	else
	{
	   jQuery("#shiptoicon").show();
	   jQuery("#shiptobutton").addClass(button_primary);
	   updateaddress(4);
	   jQuery("#shiptoclose").click();
	}
}


function changecheckout(val)
{

 if(val == 1)
  {
    jQuery("#regtitle").slideUp();
	jQuery("#guesttitle").slideDown();

	jQuery("#guestchekcout").addClass(button_primary);
	jQuery("#regcheckout").removeClass(button_primary);
	jQuery("#regicon").removeClass("opg-icon-check");
	jQuery("#guesticon").addClass("opg-icon-check");

	   jQuery('#register').attr('checked', false);
	   jQuery('#user_fields_div').hide();
	   window.lastvalue = 1;

    
  }
  if(val == 2)
  {
     jQuery("#regtitle").slideDown();
	 jQuery("#guesttitle").slideUp();
	 
	 jQuery("#guestchekcout").removeClass(button_primary);
	 jQuery("#regcheckout").addClass(button_primary);
	 jQuery("#regicon").addClass("opg-icon-check");
	 jQuery("#guesticon").removeClass("opg-icon-check");
	
	   jQuery('#register').attr('checked', true);
	   jQuery('#user_fields_div').show();
	   window.lastvalue = 2;
	
  }
}

function changemode(val)
{
  if(val == 1)
  {
    jQuery("#logindiv").slideDown();
	jQuery("#loginbtn").addClass(button_primary);
	jQuery("#regbtn").removeClass(button_primary);
	jQuery("#old_payments").slideUp();
	jQuery(".all_shopper_fields").slideUp();
	jQuery("#other-things").slideUp();
  }
  if(val == 2)
  {
     jQuery("#logindiv").slideUp();
	 jQuery("#loginbtn").removeClass(button_primary);
	 jQuery("#regbtn").addClass(button_primary);
	 jQuery("#old_payments").slideDown();
	 jQuery(".all_shopper_fields").slideDown();
	 jQuery("#other-things").slideDown();
  }
}



function strip_tags(str, allow) {
  // making sure the allow arg is a string containing only tags in lowercase (<a><b><c>)
  allow = (((allow || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
  var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
  return str.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
    return allow.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
  });
}

function applycoupon() {
	
	if(!popupopen)
	{
	 	jQuery("#loadingbutton").click();											  
	    popupopen = true;
    }
	couponcode = jQuery("#coupon_code").val();
	jQuery.ajax({
        	type: "POST",
	        cache: false,
	        dataType: "json",
				url: window.vmSiteurl + "index.php?&option=com_virtuemart&view=cart&vmtask=applycoupon&couponcode=" + couponcode
	        }).done(
		    function (data, textStatus){
			   if(data.wrongcoupon)
			   {
				   var r = '<div class="opg-margin-small-top opg-alert opg-alert-warning" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p>' + data.couponMessage + "</p></div>";
				   jQuery("#customerror").html("");
				   jQuery("#customerror").show();
				   jQuery("#customerror").html(r);
				   jQuery('html,body').animate({
	    			    scrollTop: jQuery("#customerror").offset().top},
		    	   'slow');
				   if(popupopen == true)
				   {
				   	jQuery("#loadingbtnclose").click();
					popupopen = false;
				   }
			   }
			   else
			   {
				   var r = '<div class="opg-margin-small-top opg-alert opg-alert-success" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p>' + data.couponMessage + "</p></div>";
				   jQuery("#customerror").html("");
				   jQuery("#customerror").show();
				   jQuery("#customerror").html(r);
				   jQuery('html,body').animate({
	    			    scrollTop: jQuery("#customerror").offset().top},
		    	   'slow');
				   update_prices();
			   }
	        });
}


function ajaxlogin()
{
 	if(!popupopen)
	{
	 	jQuery("#loadingbutton").click();											  
	    popupopen = true;
    }	 
 jQuery("#userlogin_username").removeClass(form_danger);
 jQuery("#userlogin_password").removeClass(form_danger);
 usernameval = document.getElementById("userlogin_username").value;
 passwordval = document.getElementById("userlogin_password").value;
 
 loginempty = document.getElementById("loginempty").value; 
 loginerror = document.getElementById("loginerrors").value; 
 if(usernameval == "" || passwordval == "")
 {
   if(usernameval == "")
   {
     jQuery("#userlogin_username").addClass(form_danger);
   }
   if(passwordval == "")
   {
     jQuery("#userlogin_password").addClass(form_danger);
   }
    var r = '<div class="opg-alert opg-alert-danger" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p>' + loginempty + "</p></div>";
	jQuery("#loginerror").show();
	jQuery("#loginerror").html(r);
	if(popupopen == true)
    {
	   	jQuery("#loadingbtnclose").click();
		popupopen = false;
	}
 }
  else
  {
     jQuery("#loginerror").hide();
     var url= vmSiteurl+"index.php?option=com_virtuemart&view=cart";
	 url += "&vmtask=userlogin&username=" + encodeURIComponent(usernameval) + "&passwd=" + encodeURIComponent(passwordval); 
	  jQuery.ajax({
        	type: "POST",
	        cache: false,
    	    url:  url,
	       }).done(
			function (data, textStatus) 
			{
			  if(data == "error")
			  {
			     jQuery("#userlogin_username").addClass(form_danger);
				 jQuery("#userlogin_password").addClass(form_danger);
				 var r = '<div class="opg-alert opg-alert-danger" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p>' + loginerror + "</p></div>";
				 jQuery("#loginerror").show();
				 jQuery("#loginerror").html(r);
				 if(popupopen == true)
				   {
				   	jQuery("#loadingbtnclose").click();
					popupopen = false;
				   }
			  }
			  else
			  {
				if(popupopen == true)
				   {
				   	jQuery("#loadingbtnclose").click();
					popupopen = false;
				   }   
			    window.location.reload();
			  }
		    });
  }
}



function submit_order() {	
  
   jQuery("#customerror").html("");
   errormsg = "";
    if(vmonepage.captchaenabled > 0)
    {
	   captcha_response  = grecaptcha.getResponse();
	   if(captcha_response == "")
	   {
		    errormsg += "<p>"+vmonepage.captchainvalid+"</p>";
	   }
    }
   if(vmonepage.agree_to_tos_onorder == 1)
   {
	  if(jQuery("#squaredTwo").prop("checked") == false) 
	  { 
	      jQuery("div.squaredTwo").addClass(form_danger);
	      jQuery("div.squaredTwo").addClass("errorcheck");
		  errormsg += "<p>"+vmonepage.acceptmeessage+"</p>";
	  }
	  else
	  {
	      jQuery("div.squaredTwo").removeClass(form_danger);
		  jQuery("div.squaredTwo").removeClass("errorcheck");
	  }
   }
   if(vmonepage.showextraterms > 0)
   {
	  if(jQuery("#privacy_checkbox").prop("checked") == false ) 
	  { 
	        errormsg += "<p>"+vmonepage.privacymeessage+"</p>";
	  }
   }
   minpurchase =  parseFloat(document.getElementById("minmumpurchase").value);
   carttotalunformat  = parseFloat(document.getElementById("carttotalunformat").value);
   if(minpurchase > 0 )
	{ 
	  if(minpurchase > carttotalunformat)
	  { 
		    errormsg += '<p>' + vmonepage.minpurchaseerror + '</p>';
	  }
	}
	
	if(selected_shipment==false)  errormsg += '<p>' + vmonepage.selectshipment + '</p>';
	if(selected_payment==false)  errormsg += '<p>' + vmonepage.selectpayment + '</p>';


	var validator = new JFormValidator();
	
    inputvalidation = true;
	if(vmonepage.onlyregistered > 0)
	{
		jQuery('#user_fields_div input').each(function(){
			var validatefield = validator.validate(this);
			elementid = jQuery(this).attr("id");
			if(validatefield == false)
			{
			  inputvalidation = false;
			  jQuery("#"+elementid).addClass(form_danger);
			}
			else
			{
			  jQuery("#"+elementid).removeClass(form_danger);
			}
			
		});
     }
	 if(vmonepage.popupaddress > 1)
	 {
   	     var validator=new JFormValidator();
	     jQuery('#shipto_fields_div input').each(function(){
			var validatefield=validator.validate(this);
			elementid = jQuery(this).attr("id");
			if(validatefield == false)
			{
			  inputvalidation = false;	  
			  jQuery("#"+elementid).addClass(form_danger);
			}
			else
			{
			  jQuery("#"+elementid).removeClass(form_danger);
			}
		});
		 country_ele2 =  document.getElementById('shipto_virtuemart_country_id');
		 if(jQuery('#shipto_virtuemart_country_id').length > 0)
		 {
		     var validatefield =validator.validate(country_ele2);
			 if(validatefield == false)
			 {
				  inputvalidation = false;	  
				  jQuery("#shipto_virtuemart_country_id").addClass(form_danger);
	 		 }
			 else
			 {
			  jQuery("#shipto_virtuemart_country_id").removeClass(form_danger);
		  	 }
		 }
		 state_ele2 =  document.getElementById('shipto_virtuemart_state_id');
		 if(jQuery('#shipto_virtuemart_state_id').length > 0)
		 {
	    	 var validatefield=validator.validate(state_ele2);
			 if(validatefield == false)
			 {
				  inputvalidation = false;	  
				  jQuery("#shipto_virtuemart_state_id").addClass(form_danger);
		 	 }	
			 else
			 {
				  jQuery("#shipto_virtuemart_state_id").removeClass(form_danger);
		  	 }
		 }
	 }
	 else
	 {
	    jQuery('#billto_fields_div input').each(function(){
		var validatefield = validator.validate(this);
		elementid = jQuery(this).attr("id");
			if(validatefield == false)
			{
			  inputvalidation = false;	 
			  jQuery("#"+elementid).addClass(form_danger);
			}
			else
			{
			  jQuery("#"+elementid).removeClass(form_danger);
			}
		 });
	
   		 country_ele = document.getElementById("virtuemart_country_id");
		 if(jQuery("#virtuemart_country_id").length > 0)
		 {
			 var validator=new JFormValidator();
		     var validatefield = validator.validate(country_ele);
			 if(validatefield == false)
			 {
				  inputvalidation = false;
				  jQuery("#virtuemart_country_id").addClass(form_danger);
 			 }
			 else
			 {
				  jQuery("#virtuemart_country_id").removeClass(form_danger);
		  	 }
		 }
		 state_ele = document.getElementById("virtuemart_state_id");
		 if(jQuery("#virtuemart_state_id").length > 0)
		 {
		     var validatefield = validator.validate(state_ele);
			 if(validatefield == false)
			 {
				  inputvalidation = false;
				  jQuery("#virtuemart_state_id").addClass(form_danger);
		 	 }
			 else
			 {
				  jQuery("#virtuemart_state_id").removeClass(form_danger);
		  	 }
		}	
	 }
    if(vmonepage.shipmentfileds > 0 && vmonepage.popupaddress == 1)
	{
		if(jQuery('#STsameAsBT').prop("checked") == true ) 
		{
			jQuery('#shipto_fields_div input').each(function() 
	   	    { 
			    inputid = jQuery(this).attr('id');
				if(typeof inputid != 'undefined') 
				{
					var name= inputid.replace('shipto_','');
					if(jQuery("#billto_fields_div #"+name).length > 0)
					{
						jQuery(this).val(jQuery("#billto_fields_div #"+name).val());
					}
				}
			});
		  	 if(jQuery("#virtuemart_country_id").length > 0 && jQuery("#shipto_virtuemart_country_id").length > 0)
		     {
				 jQuery("#shipto_virtuemart_country_id").val(jQuery("#virtuemart_country_id").val());
	   		 }
		} 
		else
		{
			var validator=new JFormValidator();
			jQuery('#shipto_fields_div input').each(function(){
				var validatefield=validator.validate(this);
				elementid = jQuery(this).attr("id");
				if(validatefield == false)
				{
				  inputvalidation = false;	  
				  jQuery("#"+elementid).addClass(form_danger);
				}
				else
				{
				  jQuery("#"+elementid).removeClass(form_danger);
				}
			});
		 country_ele2 =  document.getElementById('shipto_virtuemart_country_id');
		 if(jQuery('#shipto_virtuemart_country_id').length > 0)
		 {
		     var validatefield =validator.validate(country_ele2);
			 if(validatefield == false)
			 {
				  inputvalidation = false;	  
				  jQuery("#shipto_virtuemart_country_id").addClass(form_danger);
	 		 }
			 else
			 {
			  jQuery("#shipto_virtuemart_country_id").removeClass(form_danger);
		  	 }
		 }
		 state_ele2 =  document.getElementById('shipto_virtuemart_state_id');
		 if(jQuery('#shipto_virtuemart_state_id').length > 0)
		 {
	   		 var validatefield=validator.validate(state_ele2);
			 if(validatefield == false)
			 {
				  inputvalidation = false;	  
				  jQuery("#shipto_virtuemart_state_id").addClass(form_danger);
	 		 }	
			 else
			 {
				  jQuery("#shipto_virtuemart_state_id").removeClass(form_danger);
	  		 }
		 }
	  }
	}
	else
	{
		if(jQuery('#BTsameAsST').prop("checked") == true ) 
		{
			jQuery('#billto_fields_div input').each(function() 
	   	    { 
			    inputid = jQuery(this).attr('id');
				var name= "shipto_"+inputid;
				
				if(jQuery("#shipto_fields_div #"+name).length > 0)
				{
					jQuery(this).val(jQuery("#shipto_fields_div #"+name).val());
				}
			});
		  	 if(jQuery("#shipto_virtuemart_country_id").length > 0 && jQuery("#virtuemart_country_id").length > 0)
		     {
				 jQuery("#virtuemart_country_id").val(jQuery("#shipto_virtuemart_country_id").val());
    		 }
			 if(jQuery("#shipto_virtuemart_state_id").length > 0 && jQuery("#virtuemart_state_id").length > 0)
		     {
				 var stateoptions = jQuery("#shipto_virtuemart_state_id > option").clone();
				 jQuery('#virtuemart_state_id').append(stateoptions);
				 jQuery("#virtuemart_state_id").val(jQuery("#shipto_virtuemart_state_id").val());
    		 }
		} 
		billtovalidate = true;
	    jQuery('#billto_fields_div input').each(function(){
			var validatefield = validator.validate(this);
			elementid = jQuery(this).attr("id");
			if(validatefield == false)
			{
			  inputvalidation = false;	 
			  billtovalidate = false;
			  jQuery("#"+elementid).addClass(form_danger);
			}
			else
			{
			  jQuery("#"+elementid).removeClass(form_danger);
			}
		 });
	   	 country_ele =  document.getElementById('virtuemart_country_id');
		 if(jQuery("#virtuemart_country_id").length > 0)
		 {
		     var validatefield = validator.validate(country_ele);
			 if(validatefield == false)
			 {
				  inputvalidation = false;
				  billtovalidate = false;
				  jQuery("#virtuemart_country_id").addClass(form_danger);
	 		 }
			 else
			 {
				  jQuery("#virtuemart_country_id").removeClass(form_danger);
		  	 }
		 }
		 state_ele =  document.getElementById('virtuemart_state_id');
		 if(jQuery("#virtuemart_state_id").length > 0)
		 {
		     var validatefield = validator.validate(state_ele);
			 if(validatefield == false)
			 {
				  inputvalidation = false;
				  billtovalidate = false;
				  jQuery("#virtuemart_state_id").addClass(form_danger);
		 	 }	
			 else
			 {
				  jQuery("#virtuemart_state_id").removeClass(form_danger);
		  	 }
		  }
		  
		  if(billtovalidate == false)
		  {
	 	     jQuery("#billtobutton").removeClass(button_primary);  
			 jQuery("#billtobutton").addClass(button_danger);  
		  }
		  else
		  {
			 jQuery("#billtobutton").removeClass(button_danger);    
			 jQuery("#billtobutton").addClass(button_primary);  
			 
		  }
	}
	
	jQuery("#user_error").hide();
    jQuery("#email_error").hide();
	
	if(!inputvalidation ||  errormsg != "") 
	{
		  if(!inputvalidation)
		  {
		 	  errormsg += "<p>"+vmonepage.invaliddata+"</p>";
		  }
		   var r = '<div class="opg-margin-small-top opg-alert opg-alert-warning" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p>' + errormsg + "</p></div>";
		   jQuery("#customerror").html("");
		   jQuery("#customerror").show();
		   jQuery("#customerror").html(r);
		   
		    jQuery('html,body').animate({
	    	    scrollTop: jQuery("#customerror").offset().top},
    	    'slow');

			return;
	 }
		
    if(!popupopen)
	{
	 	jQuery("#loadingbutton").click();											  
	    popupopen = true;
    }
	var register_state=true;

	if(jQuery('#register').prop("checked") == true ) {
		
	  register_state=false;
	  registerurl = "index.php?option=com_virtuemart&view=cart&vmtask=registeruser&"+vmonepage.token+"=1";	
	  jQuery.ajax({
        	type: "POST",
	        cache: false,
    	    url: window.vmSiteurl + registerurl,
			dataType: "json",
			data : jQuery("#billto_inputdiv :input").serialize()
       }).done(
	   function (data, textStatus) 
	   {
		   if(data.error && data.error==1) 
		   {
			      	erromsg = '<div data-opg-alert="" class="opg-alert opg-alert-warning"><a href="#" class="opg-alert-close opg-close"></a><p>'+data.message+'</p></div>';
					if(data.message != "")
					{
			 
					   jQuery("#customerror").html("");
					   jQuery("#customerror").show();
					   jQuery("#customerror").html(erromsg);
		   			   jQuery('html,body').animate({
				    	    scrollTop: jQuery("#customerror").offset().top},
    	    		   'slow');
					}
					
					if(popupopen == true)
				    {
				   	 jQuery("#loadingbtnclose").click();
				 	 popupopen = false;
				    }
				    
			  	    
					return false;
		   }
		   else
		   {
			     	 jQuery("#userlogin_username").removeClass(form_danger);
				 	 jQuery("#userlogin_password").removeClass(form_danger);
					 usernameval = document.getElementById("username_field").value;
					 passwordval = document.getElementById("password_field").value;
					 returnurlval = document.getElementById("returnurl").value;
					 
					 
				     var url= vmSiteurl+"index.php?option=com_virtuemart&view=cart";
					 url += "&vmtask=userlogin&username=" + encodeURIComponent(usernameval) + "&passwd=" + encodeURIComponent(passwordval) + "&return=" + encodeURIComponent(returnurlval); 
					  	datas = jQuery("#checkoutForm").serialize();
	 					datas = datas.replace("&task=confirm" , "");
	 					datas = datas.replace("&task=update" , "");
	 					datas = datas.replace("&task=user.login" , "");
					  jQuery.ajax({
					        	type: "POST",
						        cache: false,
					    	    url:  url,
						       }).done(
									function (data, textStatus) 
									{
										  if(data == "error")
										  {
											    if(popupopen == true)
											    {
											   	 jQuery("#loadingbtnclose").click();
												 popupopen = false;
											    }
											    
										  	    
										  }
										  else
										  {
											     jQuery.ajax({
					        							type: "POST",
												        cache: false,
														data : datas,
														dataType : "json",
											    	    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=completecheckout',
														
												 }).done(
													 function (data, textStatus) 
													 {
														 if(data.success == 1) 
														 {
															if(popupopen == true)
															{
															   	jQuery("#loadingbtnclose").click();
																popupopen = false;
														   }
															jQuery("#checkoutForm").submit();
														 }
														 else
														 {
														   errordata = data.message;	  
														   var r = '<div class="opg-margin-small-top opg-alert opg-alert-warning" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p>' + vmonepage.invaliddata + "</p><p>"+errordata+"</p></div>";
														   jQuery("#customerror").html("");
														   jQuery("#customerror").show();
														   jQuery("#customerror").html(r);
														   
														   if(popupopen == true)
				   											{
															   	jQuery("#loadingbtnclose").click();
																popupopen = false;
															 }
														   
													  	   
														   jQuery('html,body').animate({
													    	    scrollTop: jQuery("#customerror").offset().top},
												    	    'slow');
															return;
														 }
														 
														 
												     });
										  }
									});
			   
		   }
		   
	   });
	
   } // if register button checked else
   else
   {
	   datas = jQuery("#checkoutForm").serialize();
	 	datas = datas.replace("&task=confirm" , "");
	 	datas = datas.replace("&task=update" , "");
	 	datas = datas.replace("&task=user.login" , "");
        jQuery.ajax({
				type: "POST",
		        cache: false,
				data : datas,
				dataType : "json",
	    	    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=completecheckout',
		 }).done(
			 function (data, textStatus) 
				 {
					 if(data.success == 1) 
					 {
						 if(popupopen == true)
						   {
						   	jQuery("#loadingbtnclose").click();
							popupopen = false;
					      }
						 jQuery("#checkoutForm").submit();
					 }
					 else
					 {
						   errordata = data.message;	  
						   var r = '<div class="opg-margin-small-top opg-alert opg-alert-warning" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p>' + vmonepage.invaliddata + "</p><p>"+errordata+"</p></div>";
						   jQuery("#customerror").html("");
						   jQuery("#customerror").show();
						   jQuery("#customerror").html(r);
						   
						   if(popupopen == true)
						   {
						   	 jQuery("#loadingbtnclose").click();
						 	 popupopen = false;
						   }
						   
					  	   
						   
						   jQuery('html,body').animate({
					    	    scrollTop: jQuery("#customerror").offset().top},
				    	    'slow');
						  return;
						 
					 }
				
						
		 });
   }

}
function update_product(vmid) 
{
	if(!popupopen)
	{
	 	jQuery("#loadingbutton").click();											  
	    popupopen = true;
    }
	qtyvalue = jQuery("#quantity_"+vmid).val();
	if(qtyvalue > 0)
	{
		jQuery.ajax({
				type: "POST",
		        cache: false,
	    	    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=ajaxupdate',
				dataType: "json",
				data : jQuery("#allproducts :input").serialize()
		 }).done(
			 function (data, textStatus){
				 
				 if(data.error) 
				 {
				   qtytext = "#quantity_"+data.vmid;
				   jQuery(qtytext).val(data.defaultqty);
				   var r = '<div class="opg-margin-small-top opg-alert opg-alert-warning" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p>' + data.msg + "</p></div>";
				   jQuery("#customerror").html("");
				   jQuery("#customerror").show();
				   jQuery("#customerror").html(r);
				   jQuery('html,body').animate({
				    	    scrollTop: jQuery("#customerror").offset().top},
    	    	   'slow');
				   
				   if(popupopen == true)
				   {
				   	jQuery("#loadingbtnclose").click();
					popupopen = false;
				   }
				   
			  	   
				 }
				 else
				 {
					if (jQuery(".vmCartModule")[0]) 
					{
						 currentview = "";
						 jQuery('body').trigger('updateVirtueMartCartModule');
                    }  
					else
					{
						 var $ = jQuery ;
						 $.ajaxSetup({ cache: false })
					  	 $.getJSON(window.vmSiteurl + "index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json" + window.vmLang,
						function(datas, textStatus) 
						{
						  if (datas.totalProduct > 0) 
						  {
							 
						  }
						  else
						  {
							    window.location.reload();
						  }
						});
				    }
					var r = '<div class="opg-margin-small-top opg-alert opg-alert-success" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p>' + vmonepage.productupdate + "</p></div>";
				   jQuery("#customerror").html("");
				   jQuery("#customerror").show();
				   jQuery("#customerror").html(r);
				   jQuery('html,body').animate({
				    	    scrollTop: jQuery("#customerror").offset().top},
    	    	   'slow')
				   action = "updateaddress";
				   updatepayment();
				 }
	 	 });
	}
	else
	{
		 removeproduct(vmid);	
	}
}
function update_prices()
{
	jQuery.ajax({
				type: "POST",
		        cache: false,
	    	    url: window.vmSiteurl +  'index.php?option=com_virtuemart&view=cart&vmtask=ajaxprice',
				dataType: "json"
		 }).done(
			 function (data, textStatus){
				 if(data.error) 
				 {
				 }
				 else
				 {

					 jQuery.each(data.products, function(id, product) {
						 if(vmonepage.show_tax > 0)
						 {
							 if(data.products[id].subtotal_tax_amount != "")
							 {
								 jQuery('#subtotal_tax_amount_div_'+id).show(); 
		    	                 if (jQuery('#subtotal_tax_amount_'+id).length > 0) 
								 {
									jQuery('#subtotal_tax_amount_'+id).html(data.products[id].subtotal_tax_amount); 
		    	         	     }
							 }
							 else
							 {
								 jQuery('#subtotal_tax_amount_div_'+id).hide(); 
							 }
					     }
						 if(jQuery("#subtotal_salesPrice_"+id))
						 {
							jQuery('#subtotal_salesPrice_'+id).html(data.products[id].subtotal_salesPrice);
						 }
	                     if(jQuery('#subtotal_discount_'+id)) 
						 {
							 if(data.products[id].subtotal_discount != "")
							 {
								 jQuery('#subtotal_discount_div_'+id).show(); 
								 jQuery('#subtotal_discount_'+id).html(data.products[id].subtotal_discount);
							 }
							 else
							 {
								jQuery('#subtotal_discount_div_'+id).hide();  
							 }
		                 }
			             if (jQuery('#subtotal_with_tax_'+id) ) 
						 {
	                        jQuery('#subtotal_with_tax_'+id).html(data.products[id].subtotal_with_tax);
		                 }		
						 if (jQuery('#subtotal_amount_'+id) ) 
						 {
	                        jQuery('#subtotal_amount_'+id).html(data.products[id].subtotal_with_tax);
		                 }	

					 });
					 
					 // TAX RULE TYPE ONE START
					 
					 jQuery("#DATaxRulesBill").hide();
					 jQuery("#DATaxRulesBill .opg-grid").each(function(){
					   jQuery(this).hide();											    
 			  	     });
					 if(typeof(data.DATaxRulesBill)!= 'undefined')
					 {
						  jQuery.each(data.DATaxRulesBill, function(id, taxdata) {
			 	 			 if(jQuery("#dataxdiv_"+id).length > 0)
							 {
								 jQuery("#dataxdiv_"+id).show();    
								 jQuery("#DATaxRulesBill").show();
								 jQuery("#datax_amount_"+id).html(taxdata.price);
							 }
							 else
							 {
							      jQuery("#DATaxRulesBill").show();
							 	  htmldiv = '<div id="dataxdiv_'+id+'"  class=" opg-grid opg-text-right"><div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2">'+taxdata.name+'</div><div id="datax_amount_'+id+'"   class="price-amount opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2">'+taxdata.price+'</div><div class="clear"></div></div>';
								  jQuery("#DATaxRulesBill").append(htmldiv);
							 }
						  });
					 }
					 // TAX RULE TYPE ONE END
					 
					 // TAX RULE TYPE TWO START
					 jQuery("#taxRulesBill").hide();
					 jQuery("#taxRulesBill .opg-grid").each(function(){
					   jQuery(this).hide();											    
 			  	     });
					 if(typeof(data.taxRulesBill)!= 'undefined')
					 {
						  jQuery.each(data.taxRulesBill, function(id, taxdata) {
			 	 			 if(jQuery("#taxdiv_"+id).length > 0)
							 {

								 jQuery("#taxdiv_"+id).show();   
								 jQuery("#taxRulesBill").show();
								 jQuery("#tax_amount_"+id).html(taxdata.price);
							 }
							 else
							 {
							      
							 	  htmldiv = '<div id="taxdiv_'+id+'"  class=" opg-grid opg-text-right"><div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2">'+taxdata.name+'</div><div id="tax_amount_'+id+'"   class="price-amount opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2">'+taxdata.price+'</div><div class="clear"></div></div>';
								  jQuery("#taxRulesBill").append(htmldiv);
								  jQuery("#taxRulesBill").show();
							 }
						  });
					 }
					 // TAX RULE TYPE TWO END	
					 
					 
					  // TAX RULE TYPE THREE START
					 jQuery("#DBTaxRulesBill").hide();
					 jQuery("#DBTaxRulesBill .opg-grid").each(function(){
					   jQuery(this).hide();											    
 			  	     });
					 if(typeof(data.DBTaxRulesBill)!= 'undefined')
					 {
						  jQuery.each(data.DBTaxRulesBill, function(id, taxdata) {
			 	 			 if(jQuery("#db_tax_amount_"+id).length > 0)
							 {
								 jQuery("#db_taxdiv_"+id).show();    
								 jQuery("#DBTaxRulesBill").show();
								 jQuery("#db_tax_amount_"+id).html(taxdata.price);
							 }
							 else
							 {
							      jQuery("#DBTaxRulesBill").show();
							 	  htmldiv = '<div id="db_taxdiv_'+id+'"  class=" opg-grid opg-text-right"><div class="price-type opg-width-large-3-4 opg-width-small-1-2 opg-width-1-2">'+taxdata.name+'</div><div id="db_tax_amount_'+id+'"   class="price-amount opg-width-large-1-4 opg-width-small-1-2 opg-width-1-2">'+taxdata.price+'</div><div class="clear"></div></div>';
								  jQuery("#DBTaxRulesBill").append(htmldiv);
							 }
						  });
					 }
					 // TAX RULE TYPE THREE END
				   
					if(data.salesPrice != "")
					{
					    jQuery("sales_pricefulldiv").show();
						jQuery('#sales_price').html(data.salesPrice);
			    	}
					else
					{
					   jQuery("sales_pricefulldiv").hide();
					}
				    if(vmonepage.show_tax > 0)
				    {
			 		  jQuery('#shipment_tax').html(data.shipmentTax);
			 	    }
				    if(data.salesPriceShipment != "")
 				    { 
				      jQuery("#shipmentfulldiv").show();
					  jQuery('#shipment').html(data.salesPriceShipment);
			        }
			   	   else
				   {
				     jQuery("#shipmentfulldiv").hide();
				   }
				   
				   if(data.salesPricePayment != "")
 				   { 
				      jQuery("#paymentfulldiv").show();
					  jQuery('#paymentprice').html(data.salesPricePayment);
			       }
			   	   else
				   {
				      jQuery("#paymentfulldiv").hide();
				   }
				   
				   
				   if(vmonepage.show_tax > 0)
				   {
				 	 jQuery('#payment_tax').html(data.paymentTax);
					 if(data.billTaxAmount != "")
				 	 {
				      	 jQuery("#total_taxfulldiv").show();
						 jQuery('#total_tax').html(data.billTaxAmount);
				     }
				     else
				     {
				        jQuery('#total_tax').html(data.billTaxAmount);
				        jQuery("#total_taxfulldiv").hide();
				     }
				 }
				jQuery('#payment').html(data.salesPricePayment);
				if(data.billDiscountAmount != "")
				{
				    jQuery("#total_amountfulldiv").show();
					jQuery('#total_amount').html(data.billDiscountAmount);
			    }
				else
				{
				   jQuery("#total_amountfulldiv").hide();
				}
				if(data.billTotal != "")
				{
				    jQuery("#bill_totalamountfulldiv").show();
					jQuery("#bottom_total").show();
					jQuery('#bill_total').html(data.billTotal);
					jQuery('#carttotal').html(data.billTotal);
					jQuery('#carttotalunformat').val(data.billTotalunformat);
				}
				else
				{
				    jQuery("#bill_totalamountfulldiv").hide();
					jQuery("#bottom_total").hide();
				}
				jQuery("#couponpricediv").hide();
				
				if(vmonepage.couponenable > 0)
				{
					var coupontext = data.couponCode;
					if (data.couponDescr != '') 
					{
						coupontext += ' (' + data.couponDescr + ')';
					}
					if (data.salesPriceCoupon) 
					{
						 jQuery("#coupon_code_txt").html(coupontext);
						 jQuery("#couponpricediv").show();
					} 
					else 
					{
                        jQuery("#coupon_code_txt").html("");
						jQuery("#couponpricediv").hide();
					}
					if(vmonepage.show_tax > 0)
					{
						if(data.couponTax) 
						{
							
							jQuery("#coupon_tax").html(data.couponTax);
							jQuery("#coupon_taxfulldiv").show();
						} 
						else 
						{
							jQuery("#coupon_tax").html('');
							jQuery("#coupon_taxfulldiv").hide();
						}
					 }
					 if(data.salesPriceCoupon) 
					 {
						jQuery("#coupon_price").html(data.salesPriceCoupon);
					 }
					 else 
					 {
						jQuery("#coupon_price").html('');
					 }
			     } 
				 selectedpayid = jQuery("#paymentsdiv input[name='virtuemart_paymentmethod_id']:checked").val();
		         if(jQuery("#paydiv_"+selectedpayid).length > 0)
  	 	     	 {
					  jQuery("#paydiv_"+selectedpayid+" div").find("*").removeClass("opg-hidden");

			     }
				 
				(function($){
				var klarna_id = $('#klarna_checkout_onepage').val();
				if (klarna_id != null) 
				{
					if ($("#paymentsdiv input[name='virtuemart_paymentmethod_id']:checked").val() == klarna_id) 
					{
					     if(vmonepage.customernote > 0)
					     {
					        document.getElementById("extracommentss").style.display = "block";
				    	 }
						  if(document.getElementById("klarna-checkout-iframe") == null)
						 {
						    if(jQuery("#payments .error_div").length == 0)
						    {
						  	  document.location.reload(); 
						    }
						 }
					     $("#klarna-checkout-container").slideDown();
						 $("#klarna_fields").slideDown();
					     $('#otherpay_buttons').slideUp();
				  	     $('div.all_shopper_fields').slideUp();
					     $('div#other-things').slideUp();
					}
					else 
					{
						if(vmonepage.customernote > 0)
					    {
					        document.getElementById("extracommentss").style.display = "none";
				    	} 
					    $("#klarna-checkout-container").slideUp();
						$("#klarna_fields").slideUp();
					    $('#otherpay_buttons').slideDown();
				        $('div.all_shopper_fields').slideDown();
					    $('div#other-things').slideDown();
					};
			  }
			  else
			  {
				   //jQuery("#klarnadiv").hide();
   	          }
			})(jQuery);
			if (typeof window._klarnaCheckout != 'undefined') 
			{
				window._klarnaCheckout(function (api) {
	        	api.resume();
			  	});
			};
				 
			
				 if(firsttime_updateprice != "yes")
				 {
					  if(popupopen == true)
				      {
				    	jQuery("#loadingbtnclose").click();
				    	popupopen = false;
				      }
				 }
				 firsttime_updateprice = "no";
				 
			  	   
		}
			
		 });
}
function removeproduct(vmproductid)
{
	     	if(!popupopen)
			{
	 			jQuery("#loadingbutton").click();											  
			    popupopen = true;
    		}
		 jQuery.ajax({
				type: "POST",
		        cache: false,
	    	    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=deleteproduct&vmproductid='+vmproductid,
				dataType: "json"
		 }).done(
			 function (data, textStatus){
				 if(data.error) 
				 {
				 }
				 else
				 {
				   deletemsg = vmonepage.removeprouct;
				   
				   var r = '<div class="opg-alert opg-alert-warning" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p>' + deletemsg + "</p></div>";
				   jQuery("#customerror").html("");
				   jQuery("#customerror").show();
				   jQuery("#customerror").html(r);
					jQuery('#product_row_'+vmproductid).remove();
					if (jQuery(".vmCartModule")[0]) 
					{
						 currentview = "";
						 jQuery('body').trigger('updateVirtueMartCartModule');
                    }  
					jQuery.getJSON(vmSiteurl+"index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json"+vmLang,
					   function(datas, textStatus) 
					   {
						  if (datas.totalProduct >0) 
						   {
								updatepayment();
						   } 
						   else 
						   {
							    window.location.reload();
						   }
						}
					);
					 
				 }
		 });
			
}
function update_shipment()
{
	if(!popupopen)
	{
	 	jQuery("#loadingbutton").click();											  
	    popupopen = true;
    }
	jQuery.ajax({
				type: "POST",
		        cache: false,
	    	    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=ajaxshipment',
				dataType: "json"
		 }).done(
			 function (data, textStatus){
				 if(data.error) 
				 {
				 }
				 else
				 {
				    jQuery('#shipment_selection').empty();
					var shipments="";
					if(data.length == 0)
					{
						if(vmonepage.listshipments > 0)
						{ 
						  divname = "shipment_nill";
						}
						else
						{
						  divname = "shipment_fulldiv";	
						}
					     jQuery("#"+divname).html("");
						 newhtml = '<p id="shipmentnill" class="opg-text-warning"></p>';
						 jQuery("#"+divname).html(newhtml);
					     country_ele = jQuery('#virtuemart_country_id');
					     if(country_ele != null)
						 {
						     var validator = new JFormValidator();
						     var cval2 =validator.validate(country_ele);
							 if(cval2 == false)
 							 {
								  shipmentnil  = vmonepage.chosecountry;
								  jQuery("#shipmentnill").html("");
								  jQuery("#shipmentnill").html(shipmentnil); 
 			 				 } 
							 else
							 {
								  shipmentnil  = vmonepage.noshipmethod;
								  jQuery("#shipmentnill").html("");
								  jQuery("#shipmentnill").html(shipmentnil);
						  	 }
						 }
						 else
						 {
							  shipmentnil  = vmonepage.noshipmethod;
							  jQuery("#shipmentnill").html("");
							  jQuery("#shipmentnill").html(shipmentnil);
						 }
					}
					else
					{
						 jQuery("#shipment_fulldiv").html("");
						 newhtml = '<table class="opg-table opg-table-striped" id="shipmenttable"><tr id="shipmentrow"><td id="shipmentdetails"></td></tr></table>';
					 jQuery("#shipment_fulldiv").html(newhtml);
					}
					if(data)
					{
					    shipments+= '<ul class="opg-list" id="shipment_ul">';
					    for(var i=0;i<data.length;i++) {
						   inputstr = data[i].toString();
						   var n = inputstr.search("checked"); 
						   if(n > 0)
						   {
						     var activeclasss = "liselected";
						   }
						   else
						   {
						     var activeclasss = "";
						   }
						   if(activeclasss != "" && vmonepage.listshipments == 0)
						   {
							  texxt = data[i];
							  tmptxt = strip_tags(texxt, '<span><img>');
							  tmptxt = tmptxt.replace('</span><span', '</span><br /><span');
							  tmptxt = tmptxt.replace('vmshipment_description', 'vmshipment_description opg-text-small');
							  tmptxt = tmptxt.replace('vmshipment_cost', 'vmshipment_cost opg-text-small');
							  
							  jQuery("#shipmentdetails").html(tmptxt);
							  if(data.length > 1)
							  {
							    if(document.getElementById("shipchange") == null)
								{
								     jQuery("#shipchangediv").remove();
								     temptext = "";
								  	 temptext =  '<td id="shipchangediv" class="opg-width-1-4">';
								     target = "{target:'#shipmentdiv'}";
							         temptext += '<a class="opg-button '+button_primary+'" href="#" data-opg-modal="'+target+'">';
									 temptext += vmonepage.changetext;
									 temptext += '</a></td>';
									 jQuery("#shipmentrow").append(temptext);
							    }
							  }
							  else
							  {
							    jQuery("#shipchangediv").remove();
							  }
						    } 
						    texxts = "";
							texxts = data[i];
							texxts = strip_tags(texxts, '<span><img><input>');
							texxts = texxts.replace('</span><span', '</span><br /><span');
							texxts = texxts.replace('vmshipment_description', 'vmpayment_description opg-text-small');
							texxts = texxts.replace('vmshipment_cost', 'vmpayment_cost opg-text-small');
							if(vmonepage.listshipments > 0)
							{
								texxts = texxts.replace('<input', '<input onclick="setshipment()"');
							}
	                        shipments+='<li class="'+activeclasss+'">';
							shipments+='<label class="opg-width-1-1">'+texxts+'</label>';
							shipments+='<hr class="opg-margin-small-bottom opg-margin-small-top" /></li>';
					    }
						shipments+='</ul>';
						oneshipmenthide = document.getElementById("oneshipmenthide").value;
						if(oneshipmenthide == "yes")
						{
						  if(data.length == 1)
						  {
						    jQuery("#shipment_select").addClass("opg-hidden");
						  }
						  else if(data.length > 1 ||  data.length == 0)
						  {
						    jQuery("#shipment_select").removeClass("opg-hidden");
						  }
						}
						
						jQuery('#shipment_selection').html("");
						jQuery('#shipment_selection').html(shipments);
						jQuery("#shipmentclose").click();
					}
					var shipmentchecked=false;
					if(jQuery('#shipment_selection').length > 0) 
					{
						jQuery("#shipment_selection input").each(function(){
							if(jQuery(this).prop("checked") == true )  
							{
								shipmentchecked=true;
								return false;
			    		    }	
					    });
					}
					if(shipmentchecked == false)
					{
						 if(jQuery("#shipment_selection input").length > 1)
						  {
						     autoshipid = document.getElementById("auto_shipmentid").value;
							 if(autoshipid > 0)
							 {
							    jQuery("#shipments #shipment_id_"+autoshipid).attr('checked', true);
								setshipment();
							 }
							 else
							 {
							   jQuery("#shipment_selection input").each(function(){
									jQuery(this).prop('checked', true);
									return false;
					    	   });	 
							   setshipment();
							 }
						  }
						  else  if(jQuery("#shipment_selection input").length > 0)
						  {
						      jQuery("#shipment_selection input").each(function(){
									jQuery(this).prop('checked', true);
									return false;
					    	   });	 
							  setshipment();
						  }
					}
					
					if (action != "updateaddress")
					{
						update_prices();
					}
					else if(countrychange == "yes" && vmonepage.countryreload == 1)
					{
					 	  document.location.reload(); 
					}
					else
					{
					  update_prices();	
					}
			   }
		});
}
function updatepayment()
{
	
	
	
	if(!popupopen)
	{
	 	jQuery("#loadingbutton").click();											  
	    popupopen = true;
    } 
	 jQuery.ajax({
				type: "POST",
		        cache: false,
	    	    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=ajaxpayandship',
				dataType: "json"
		 }).done(
			 function (data, textStatus){
				 
				jQuery("#paymentsdiv").html("");
				if(data.payments.length == 0)
				{
					 if(vmonepage.listpayments > 0)
				 	 { 
					   paydivname = "payment_nill";
					 }
				 	 else
					 {
					   paydivname = "payment_fulldiv";	
					 }
					 
				     jQuery("#"+paydivname).html("");
					 newhtml = '<p id="paymentnill" class="opg-text-warning"></p>';
					 jQuery("#"+paydivname).html("");
					 
				     country_ele = jQuery('#virtuemart_country_id');
				     if(country_ele != null)
					 { 
					     var validator=new JFormValidator();
					     var cval2 =validator.validate(country_ele);
						 if(cval2 == false)
						 {
							  paymentnil  = vmonepage.chosecountry;
							  jQuery("#paymentnill").html("");
							  jQuery("#paymentnill").html(paymentnil); 
 		 				 } 
						 else
						 {
							  paymentnil  = vmonepage.nopaymethod;
							  jQuery("#paymentnill").html("");
							  jQuery("#paymentnill").html(paymentnil);
					  	 }
					 }
					 else
					 {
					 
					     paymentnil  = vmonepage.nopaymethod;
						 jQuery("#paymentnill").html("");
						 jQuery("#paymentnill").html(paymentnil);
					 }
				}
				else
				{
					 jQuery("#payment_fulldiv").html("");
					 newhtml = '<table class="opg-table opg-table-striped" id="paymentable"><tr id="paymentrow"><td id="paymentdetails"></td></tr></table>';
					 jQuery("#payment_fulldiv").html(newhtml);
				}
				var payments="";
				if(data.payments) 
				{
				    payments+= '<ul class="opg-list" id="payment_ul">';
				    for(var i=0;i<data.payments.length;i++) 
					{
						   inputstr = data.payments[i].toString();
						   var s = inputstr.search("klarna-checkout-container"); 
						   if(s > 0)
						   {
						      //jQuery("#klarna-checkout-container").appendTo("#klarnadiv");
						   }
						   var n = inputstr.search("checked"); 
						   if(n > 0)
						   {
						      var activeclasss = "liselected";
					   	   }
					   	   else
					       {
					   		  var activeclasss = "";
					       }
						   if(activeclasss != ""  && vmonepage.listpayments == 0)
						   {
						      texxt = data.payments[i];
							  
							  pos = texxt.indexOf("</span></span>"); 
							  if(pos > 0)
							  {
								  texxt =  texxt.substring(0, pos); 
							  }
							  tmptxt = strip_tags(texxt, '<span><img><div>');
							  tmptxt = tmptxt.replace('klarna-checkout-container', 'klarna-checkout-containers_div');
							  tmptxt = tmptxt.replace('</span><span', '</span><br /><span');
							  tmptxt = tmptxt.replace('vmpayment_description', 'vmpayment_description opg-text-small');
							  tmptxt = tmptxt.replace('vmpayment_cost', 'vmpayment_cost opg-text-small');
							  jQuery("#paymentdetails").html(tmptxt);
						 	  if(data.payments.length > 1 )
							  {	
							     if(document.getElementById("paychangediv") == null)
							 	 {
								     jQuery("#paychangediv").remove();
							 	     temptext = "";
								  	 temptext =  '<td id="paychangediv" class="opg-width-1-4">';
									 target = "{target:'#paymentdiv'}";
							         temptext += '<a class="opg-button '+button_primary+'" href="#" data-opg-modal="'+target+'">';
									 temptext += vmonepage.changetext;
									 temptext += '</a></td>';
									 jQuery("#paymentrow").append(temptext);
							     }
						   	 }
						  	 else
						   	 {
						   		 jQuery("#paychangediv").remove();
						  	 }
						   } 
						   texxts = "";
						   texxts = data.payments[i];
						   pos = texxts.indexOf("</span></span>"); 
						   if(pos > 0)
						   {
							   texxts =  texxts.substring(0, pos); 
						   }

						   tmptxts = strip_tags(texxts, '<span><img><input><div>');
						   tmptxts = tmptxts.replace('klarna-checkout-container', 'klarna-checkout-containers_div');
						   tmptxts = tmptxts.replace('</span><span', '</span><br /><span');
						   tmptxts = tmptxts.replace('vmpayment_description', 'vmpayment_description opg-text-small');
						   tmptxts = tmptxts.replace('vmpayment_cost', 'vmpayment_cost opg-text-small');
						   if(vmonepage.listpayments > 0)
						   {
								tmptxts = tmptxts.replace('type="radio"', 'type="radio" onclick="setpayment()" ');
						   }
						   payments+='<li class="'+activeclasss+'">';
						   payments+='<label class="opg-width-1-1">'+tmptxts+'</label>';
						   payments+="<hr class='opg-margin-small-bottom opg-margin-small-top' /></li>";
						 
			         }
					payments += "</ul>";
					
					onepayementhide = document.getElementById("onepaymenthide").value;
					if(onepayementhide == "yes")
					{
					  if(data.payments.length == 1)
					  {
					    jQuery("#payment_select").addClass("opg-hidden");
					  }
					  else if(data.payments.length > 1 ||  data.payments.length == 0)
					  {
					    jQuery("#payment_select").removeClass("opg-hidden");
					  }
					}
					jQuery("#paymentclose").click();
					jQuery("#paymentsdiv").html(payments);

			   }	
			    
	                       //FOR AUTHORIZED .NET 		   
  						   if(jQuery("#payment_ul .vmpayment_cardinfo").length > 0)
						   {
						     jQuery("#payment_ul .vmpayment_cardinfo").remove();
						   }
						   if(jQuery("#paymentrow .vmpayment_cardinfo").length > 0)
						   {
						     jQuery("#paymentrow .vmpayment_cardinfo").remove();
						   }
						   

			   
			   
			 paymentchecked  = false;
			if(jQuery('#paymentsdiv').length > 0) 
			{
				jQuery("#paymentsdiv input").each(function(){
					if(jQuery(this).prop("checked") == true )  
					{
						paymentchecked=true;
						return false;
	    		    }	
			    });
			}
			
			
			if(paymentchecked == false)
			{
			   if(jQuery("#paymentsdiv input").length > 1)	
			  {
			     autopayid = document.getElementById("auto_paymentid").value;
				 if(autopayid > 0)
				 {
				   jQuery("#payments #payment_id_"+autopayid).attr('checked', true);
				   jQuery("#paymentsdiv #payment_id_"+autopayid).attr('checked', true);
				   setpayment();
				 }
				 else
				 {
				   jQuery("#paymentsdiv input").each(function(){
						jQuery(this).prop('checked', true);
							return false;
		    	   });	 
				   setpayment();
				 }
			  }
			  else if(jQuery("#paymentsdiv input").length > 0)
			  {
			       jQuery("#paymentsdiv input").each(function(){
						jQuery(this).prop('checked', true);
							return false;
		    	   });	 
				   setpayment();
			  }
			}
			
		    jQuery(".paydiv").each(function(){
			     jQuery(this).hide();									
			});
			selectedpayid = 0;
			selectedpayid = jQuery("#paymentsdiv input[name='virtuemart_paymentmethod_id']:checked").val();
		    if(jQuery("#paydiv_"+selectedpayid).length > 0)
			{
				jQuery("#paydiv_"+selectedpayid).show();
			}
			if(document.getElementById('klarna_checkout_onepage') != null)
			{
	            klarnapaymentid = document.getElementById('klarna_checkout_onepage').value;
				if(klarnapaymentid == selectedpaymentid)
				{ 
				  if(vmonepage.customernote > 0)
				  {
				     document.getElementById("extracommentss").style.display = "block";
				  }
				  if(document.getElementById("klarna-checkout-iframe") == null)
				  {
					  document.location.reload(); 
				  }
			    }
				else
				 {
					  if(vmonepage.customernote > 0)
					  {
					      document.getElementById("extracommentss").style.display = "none";
					  }
	   	         }
			}
			else
			{
				  if(vmonepage.customernote > 0)
				  {
					  document.getElementById("extracommentss").style.display = "none";
				  }
			}
			
			if (action != "updateaddress")
			{
				update_prices();
			} 
			else 
			{
				   jQuery('#shipment_selection').empty();
					var shipments="";
					if(data.shipments.length == 0)
					{
						if(vmonepage.listshipments > 0)
						{ 
						  divname = "shipment_nill";
						}
						else
						{
						  divname = "shipment_fulldiv";	
						}
					     jQuery("#"+divname).html("");
						 newhtml = '<p id="shipmentnill" class="opg-text-warning"></p>';
						 jQuery("#"+divname).html(newhtml);
					     country_ele = jQuery('#virtuemart_country_id');
					     if(country_ele != null)
						 {
						     var validator = new JFormValidator();
						     var cval2 =validator.validate(country_ele);
							 if(cval2 == false)
 							 {
								  shipmentnil  = vmonepage.chosecountry;
								  jQuery("#shipmentnill").html("");
								  jQuery("#shipmentnill").html(shipmentnil); 
 			 				 } 
							 else
							 {
								  shipmentnil  = vmonepage.noshipmethod;
								  jQuery("#shipmentnill").html("");
								  jQuery("#shipmentnill").html(shipmentnil);
						  	 }
						 }
						 else
						 {
							  shipmentnil  = vmonepage.noshipmethod;
							  jQuery("#shipmentnill").html("");
							  jQuery("#shipmentnill").html(shipmentnil);
						 }
					}
					else
					{
						 jQuery("#shipment_fulldiv").html("");
						 newhtml = '<table class="opg-table opg-table-striped" id="shipmenttable"><tr id="shipmentrow"><td id="shipmentdetails"></td></tr></table>';
					 jQuery("#shipment_fulldiv").html(newhtml);
					}
					if(data.shipments)
					{
					    shipments+= '<ul class="opg-list" id="shipment_ul">';
					    for(var i=0;i<data.shipments.length;i++) {
						   inputstr = data.shipments[i].toString();
						   var n = inputstr.search("checked"); 
						   if(n > 0)
						   {
						     var activeclasss = "liselected";
						   }
						   else
						   {
						     var activeclasss = "";
						   }
						   if(activeclasss != "" && vmonepage.listshipments == 0)
						   {
							  texxt = data.shipments[i];
							  tmptxt = strip_tags(texxt, '<span><img>');
							  tmptxt = tmptxt.replace('</span><span', '</span><br /><span');
							  tmptxt = tmptxt.replace('vmshipment_description', 'vmshipment_description opg-text-small');
							  tmptxt = tmptxt.replace('vmshipment_cost', 'vmshipment_cost opg-text-small');
							  jQuery("#shipmentdetails").html(tmptxt);
							  if(data.shipments.length > 1)
							  {
							    if(document.getElementById("shipchange") == null)
								{
								     jQuery("#shipchangediv").remove();
								     temptext = "";
								  	 temptext =  '<td id="shipchangediv" class="opg-width-1-4">';
								     target = "{target:'#shipmentdiv'}";
							         temptext += '<a class="opg-button '+button_primary+'" href="#" data-opg-modal="'+target+'">';
									 temptext += vmonepage.changetext;
									 temptext += '</a></td>';
									 jQuery("#shipmentrow").append(temptext);
							    }
							  }
							  else
							  {
							    jQuery("#shipchangediv").remove();
							  }
						    } 
						    texxts = "";
							texxts = data.shipments[i];
							texxts = strip_tags(texxts, '<span><img><input>');
							texxts = texxts.replace('</span><span', '</span><br /><span');
							texxts = texxts.replace('vmshipment_description', 'vmpayment_description opg-text-small');
							texxts = texxts.replace('vmshipment_cost', 'vmpayment_cost opg-text-small');
							if(vmonepage.listshipments > 0)
							{
								texxts = texxts.replace('<input', '<input onclick="setshipment()"');
							}
	                        shipments+='<li class="'+activeclasss+'">';
							shipments+='<label class="opg-width-1-1">'+texxts+'</label>';
							shipments+='<hr class="opg-margin-small-bottom opg-margin-small-top" /></li>';
					    }
						shipments+='</ul>';
						oneshipmenthide = document.getElementById("oneshipmenthide").value;
						if(oneshipmenthide == "yes")
						{
						  if(data.shipments.length == 1)
						  {
						    jQuery("#shipment_select").addClass("opg-hidden");
						  }
						  else if(data.shipments.length > 1 ||  data.shipments.length == 0)
						  {
						    jQuery("#shipment_select").removeClass("opg-hidden");
						  }
						}
						jQuery("#shipment_selection").html("");
						jQuery("#shipmentclose").click();
						jQuery("#shipment_selection").html(shipments);

					}
					var shipmentchecked=false;
					if(jQuery('#shipment_selection').length > 0) 
					{
						jQuery("#shipment_selection input").each(function(){
							if(jQuery(this).prop("checked") == true )  
							{
								shipmentchecked=true;
								return false;
			    		    }	
					    });
					}
					if(shipmentchecked == false)
					{
						 if(jQuery("#shipment_selection input").length > 1)
						  {
						     autoshipid = document.getElementById("auto_shipmentid").value;
							 if(autoshipid > 0)
							 {
							    jQuery("#shipments #shipment_id_"+autoshipid).attr('checked', true);
								setshipment();
							 }
							 else
							 {
							   jQuery("#shipment_selection input").each(function(){
									jQuery(this).prop('checked', true);
									return false;
					    	   });	 
							   setshipment();
							 }
						  }
						  else  if(jQuery("#shipment_selection input").length > 0)
						  {
						      jQuery("#shipment_selection input").each(function(){
									jQuery(this).prop('checked', true);
									return false;
					    	   });	 
							  setshipment();
						  }
					}
				
					  update_prices();	
			}
				
			
			
		});
}

function setshipment()
{
	 jQuery("#shipmentclose").click();
	 if(!popupopen)
	 {
	  	jQuery("#loadingbutton").click();											  
	    popupopen = true;
     }
	 selectedshipid = jQuery("#shipment_selection input[name='virtuemart_shipmentmethod_id']:checked").val();
	 datas = jQuery("#checkoutForm").serialize();
	 datas = datas.replace("&task=confirm" , "");
	 datas = datas.replace("&task=update" , "");
	 datas = datas.replace("&task=user.login" , "");
	 
	 jQuery.ajax({
				type: "POST",
		        cache: false,
	    	    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=setshipment&shipid='+selectedshipid,
				data : datas,
				 }).done(
					 function (data, textStatus){
						 update_shipment();	
				 });
}
function setpayment()
{
	 jQuery("#paymentclose").click();
 	 if(!popupopen)
	 {
	 	jQuery("#loadingbutton").click();											  
	    popupopen = true;
     }
	 selectedpayid = jQuery("#paymentsdiv input[name='virtuemart_paymentmethod_id']:checked").val();
	 datas = jQuery("#checkoutForm").serialize();
	 datas = datas.replace("&task=confirm" , "");
	 datas = datas.replace("&task=update" , "");
	 datas = datas.replace("&task=user.login" , "");
	 jQuery.ajax({
				type: "POST",
		        cache: false,
	    	    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=setpayment&payid='+selectedpayid,
				data : datas,
				dataType: "json"

	 }).done(
				 function (data, textStatus){
					  if(data.response == 'redirect')
					   {
						   redirecturl = window.vmSiteurl + 'index.php?option=com_virtuemart&view=plugin&type=vmpayment&name=paypal&action=SetExpressCheckout&pm='+selectedpayid;
					   window.location.href = redirecturl;
					   }
					   else
					   {
						   updatepayment();	
					   }
	 });

}
function updatecustomernote(element)
{
	jQuery("#extracommentss #customer_note_field").val(jQuery(element).val());
	jQuery("#commentpopup #customer_note_field").val(jQuery(element).val());
	if(!popupopen)
	{
		jQuery("#loadingbutton").click();											  
		popupopen = true;
	}
	
	 datas = jQuery("#checkoutForm").serialize();
	 datas = datas.replace("&task=confirm" , "");
	 datas = datas.replace("&task=update" , "");
	 datas = datas.replace("&task=user.login" , "");
	 
	 jQuery.ajax({
				type: "POST",
		        cache: false,
	    	    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=updatecartaddress',
				data : datas,
				dataType: "json"
		 }).done(
			 function (data, textStatus){
				 
				 if(popupopen == true)
				  {
				   	jQuery("#loadingbtnclose").click();
					popupopen = false;
				  }
		 });
}
function updatecart()
{
	if(!popupopen)
	{
	 	jQuery("#loadingbutton").click();											  
	    popupopen = true;
    }
	 
	 datas = jQuery("#checkoutForm").serialize();
	 datas = datas.replace("&task=confirm" , "");
	 datas = datas.replace("&task=update" , "");
	 datas = datas.replace("&task=user.login" , "");
	 jQuery.ajax({
				type: "POST",
		        cache: false,
	    	    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&task=updatecartJS',
				data : datas,
				dataType: "json"
		 }).done(
			 function (data, textStatus){
				update_prices();
		 }).fail(
		     function (data, textStatus){
				update_prices();
         });
}
function updateaddress(fieldtype)
{
	 action = "updateaddress";
	 countrychange = "no";
	 if(fieldtype == 1)
	 {
		 countrychange = "yes";
	 }
	 if(!popupopen)
	 {
	 	jQuery("#loadingbutton").click();											  
	    popupopen = true;
     }
	
	 datas = jQuery("#checkoutForm").serialize();
	 datas = datas.replace("&task=confirm" , "");
	 datas = datas.replace("&task=update" , "");
	 datas = datas.replace("&task=user.login" , "");
	 
	 jQuery.ajax({
				type: "POST",
		        cache: false,
	    	    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=updatecartaddress',
				data : datas,
				dataType: "json"
		 }).done(
			 function (data, textStatus){
				updatepayment(); 
		 });
  	
}
function checkemail()
{
   emailval  = jQuery("#email_field").val();	  
   if(jQuery('#register').prop("checked") == true && emailval != "") 
   {
	     jQuery("#email_error").hide(); 
		 jQuery("#email_field").removeClass("opg-form-danger");
		if (validatesEmail(emailval)) 
		{
			 jQuery.ajax({
					type: "POST",
			        cache: false,
	    		    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=checkemail',
					data : { "emailval" : emailval },
					dataType: "json"
			 }).done(
				 function (data, textStatus){
					 if(data.exists == "yes")
					 {
						 jQuery("#email_field").addClass("opg-form-danger");
					     var errormsg = '<div class="opg-margin-small-top opg-alert opg-alert-warning" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p >' + data.msg + "</p></div>";
						 jQuery("#email_error").show();
						 jQuery("#email_error").html(errormsg);
					 }
			 });
		}
		else
		{
			jQuery("#email_field").addClass("opg-form-danger");
		}
   }
}
function checkuser()
{
	userval  = jQuery("#username_field").val();	  
   if(jQuery('#register').prop("checked") == true && userval != "") 
   {
	     jQuery("#user_error").hide(); 
		 jQuery("#username_field").removeClass("opg-form-danger");
		 jQuery.ajax({
				type: "POST",
		        cache: false,
    		    url: window.vmSiteurl + 'index.php?option=com_virtuemart&view=cart&vmtask=checkuser',
				data : { "userval" : userval },
				dataType: "json"
		 }).done(
			 function (data, textStatus){
				 if(data.exists == "yes")
				 {
					 jQuery("#username_field").addClass("opg-form-danger");
				     var errormsg = '<div class="opg-margin-small-top opg-alert opg-alert-warning" data-opg-alert><a href="" class="opg-alert-close opg-close"></a><p >' + data.msg + "</p></div>";
					 jQuery("#user_error").show();
					 jQuery("#user_error").html(errormsg);
				 }
		 });
		
   }
	
}

function validatesEmail(sEmail) {
var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
if (filter.test(sEmail)) {
return true;
}
else {
return false;
}
}

