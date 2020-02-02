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

if(typeof Virtuemart === "undefined")
		var Virtuemart = {};
		
			Virtuemart.setproducttype =  function (form, id) {
				form.view = null;
				var $ = jQuery, datas = form.serialize();
				var prices = form.parent(".productdetails").find(".product-price");
				if (0 == prices.length) {
					prices = $("#productPrice" + id);
				}
				datas = datas.replace("&view=cart", "");
				prices.fadeTo("fast", 0.75);
				
		 jQuery.ajax({
        	type: "POST",
	        cache: false,
	        dataType: "json",
    	    url: window.vmSiteurl + "index.php?&option=com_virtuemart&view=productdetails&task=recalculate&format=json&nosef=1" + window.vmLang,
        	data: datas
	       }).done(
					function (data, textStatus) 
					{
						prices.fadeTo("fast", 1);
						// refresh price
						for (var key in data) {
							var value = data[key];
							
							 if ( key=='messages' )
							 {
				                    var newmessages = jQuery( data[key] ).find("div.alert").addClass("vmprices-message");
               					    if (!jQuery( "#system-message-container #system-message").length && newmessages.length) 
									{
			                            jQuery( "#system-message-container" ).append( "<div id='system-message'></div>" );
           				      		}
                   		  	        newmessages.appendTo( "#system-message-container #system-message");
			                } else { // prices
            				        if (value!=0) prices.find("span.Price"+key).show().html(value);
				                    else prices.find(".Price"+key).html(0).hide();
                			}
						}
					});
				return false; // prevent reload
			}
			Virtuemart.removeproduct=function(productid){
				var $ = jQuery ;
				$.ajaxSetup({ cache: false })
				$.getJSON("index.php?type=deleteprod&nosef=1&view=cart&task=viewJS&productid="+productid+"&format=json"+window.vmLang,
					function(datas, textStatus) {
						  if ($(".vmCartModule")[0]) {
                       		 Virtuemart.productUpdate($(".vmCartModule"));
                         }
					}
				);
			}
			Virtuemart.productUpdate =function(mod, curview) {
				

				jQuery('body').trigger('updateVirtueMartCartModule');
			}
			Virtuemart.sendtocart =  function (form){

				if (Virtuemart.addtocart_popup ==1) {
					Virtuemart.cartEffect(form) ;
				} else {
					form.append('<input type="hidden" name="task" value="add" />');
					form.submit();
				}
			}
			Virtuemart.eventsetproducttype = function (event){
 			    Virtuemart.setproducttype(event.data.cart,event.data.virtuemart_product_id);
			}
			Virtuemart.incrQuantity = (function(event) {
			    var rParent = jQuery(this).parent().parent();
	    		quantity = rParent.find('.quantity-input');
			    virtuemart_product_id = rParent.find('input[name="virtuemart_product_id[]"]').val();
			    Ste = parseInt(quantity.attr("data-step"));
			    if (isNaN(Ste)) Ste = 1;
			    Qtt = parseInt(quantity.val());
			    if (!isNaN(Qtt)) {
			        quantity.val(Qtt + Ste);
			        maxQtt = parseInt(quantity.attr("max"));
		        if(!isNaN(maxQtt) && quantity.val()>maxQtt){
        	    quantity.val(maxQtt);
		        }
		        Virtuemart.setproducttype(event.data.cart,virtuemart_product_id);
		    }
			});
			Virtuemart.decrQuantity = (function(event) {
			    var rParent = jQuery(this).parent().parent();
				
	    		quantity = rParent.find('.quantity-input');
			    var virtuemart_product_id = rParent.find('input[name="virtuemart_product_id[]"]').val();
			    var Ste = parseInt(quantity.attr("data-step"));
			    if (isNaN(Ste)) Ste = 1;
			    var minQtt = parseInt(quantity.attr("data-init"));
			    if (isNaN(minQtt)) minQtt = 1;
			    var Qtt = parseInt(quantity.val());
			    if (!isNaN(Qtt) && Qtt>Ste) {
			        quantity.val(Qtt - Ste);
		        if(!isNaN(minQtt) && quantity.val()<minQtt){
		            quantity.val(minQtt);
		        }
			    } else quantity.val(minQtt);
			    Virtuemart.setproducttype(event.data.cart,virtuemart_product_id);
			});
			
			Virtuemart.cartEffect = function(form) {

                var $ = jQuery ;
                $.ajaxSetup({ cache: false });
                var datas = form.serialize();

                if(usefancy){
                    $.fancybox.showActivity(); 
                }
				if(document.addtocartalert == 1)
				{
				  datas= datas+"&releated=no";		 
				}
				else
				{
				  datas= datas+"&releated=yes";		 
				}
				
				

				//var modal = new $.UIkit.modal.Modal("#add_to_cart_popup");
				//var modal = new $.UIkit.modal("#add_to_cart_popup");
                $.getJSON(vmSiteurl+'index.php?option=com_virtuemart&nosef=1&view=cart&task=addJS&format=json'+vmLang,datas,
                function(datas, textStatus) {
                    if(datas.stat ==1){
                        var txt = datas.msg;
                    } else if(datas.stat ==2){
                        var txt = datas.msg +"<H4>"+form.find(".pname").val()+"</H4>";
                    } else {
                        var txt = "<H4>"+vmCartError+"</H4>"+datas.msg;
                    }
					plaintext = "";
					if(document.addtocartalert == 1) //check addcart function from popup
					{
						 plaintext = jQuery(txt).text();
						 plaintext = plaintext.replace("/", "");
						 plaintext = plaintext.replace("/", "");
						 
						 carthtmltxt = '<div class="opg-alert" data-opg-alert><a href="/" class="opg-alert-close opg-close"></a><p>'+plaintext+'</p></div>';
						 jQuery("#cartalert").html(carthtmltxt);
						 document.addtocartalert = 0;
					} 
					else
					{
	                    if(usefancy){
							
							if(vmonepage.CARTPAGE == "yes")
							 {
								 window.location.reload(); 
							 }
							 else
							 {
	    	                    $.fancybox({
        	                        "titlePosition" : 	"inside",
            	                    "transitionIn"	:	"fade",
                	                "transitionOut"	:	"fade",
                    	            "changeFade"    :   "fast",
                            	    "type"			:	"html",
                        	        "autoCenter"    :   true,
                                	"closeBtn"      :   false,
	                                "closeClick"    :   false,
	                                "content"       :   txt
    	                         }
        	                   );
							 }
	                    } else {
							jQuery('#add_to_cart_popup .inner-content').html(txt);
							if(vmonepage.CARTPAGE == "yes")
							 {
								 window.location.reload(); 
							 }
							 else
							 {
								jQuery( "#addtocart_popup_button" ).click();
							 }
						
	                    }
					}

                    if ($(".vmCartModule")[0]) {
                        Virtuemart.productUpdate($(".vmCartModule"));
                    }
                });

                $.ajaxSetup({ cache: true });
			}

			Virtuemart.product =  function(carts) {

				
				carts.each(function(){
					var cart = jQuery(this),
					step=cart.find('input[name="quantity"]'),
					addtocart = cart.find('.addtocart-button'),
					plus   = cart.find('.quantity-plus'),
					minus  = cart.find('.quantity-minus'),
					select = cart.find('select:not(.no-vm-bind)'),
					radio = cart.find('input:radio:not(.no-vm-bind)'),
					virtuemart_product_id = cart.find('input[name="virtuemart_product_id[]"]').val(),
					quantity = cart.find('.quantity-input');
					

                    var Ste = parseInt(step.val());
                    //Fallback for layouts lower than 2.0.18b
                    if(isNaN(Ste)){
                        Ste = 1;
                    }
					addtocart.unbind( "click" );
					addtocart.click(function(e) { 
						Virtuemart.sendtocart(cart);
						return false;
					});
					plus.unbind( "click" );
					plus
            			.off('click', Virtuemart.incrQuantity)
			            .on('click', {cart:cart}, Virtuemart.incrQuantity);

				    minus
			            .off('click', Virtuemart.decrQuantity)
			            .on('click', {cart:cart},Virtuemart.decrQuantity);
					
					select.change(function() {
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
					radio.change(function() {
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
					quantity.keyup(function() {
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
				});

			}

		jQuery.noConflict();
		jQuery(document).ready(function($) {

			Virtuemart.product($("form.product"));

			$("form.js-recalculate").each(function(){
				if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
					var id= $(this).find('input[name="virtuemart_product_id[]"]').val();
					Virtuemart.setproducttype($(this),id);

				}
			});
		});

