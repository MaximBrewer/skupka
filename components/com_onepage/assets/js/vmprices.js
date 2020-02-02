if(typeof Virtuemart === "undefined")
	{
		var Virtuemart = {
			setproducttype : function (form, id) {
				form.view = null;
				var $ = jQuery; 
				//orignal: datas = form.serialize();
				
				var datas = form.serializeArray(); 
				
				var query = ''; 
				for (var i=0; i<datas.length; i++)
				{
				  if (datas[i].name != 'undefined')
				  {
				    //stAn - no other characters then & have to be encoded here, all are handled by apache and other systems
				    query += '&'+datas[i].name+'='+datas[i].value.split("&").join("%26");
				  }
				}
				
				var prices = form.parents(".productdetails").find(".product-price");
				if (0 == prices.length) {
					prices = $("#productPrice" + id);
				}
				query = query.split("&view=cart").join("&view=productdetails");
				Virtuemart.log(query); 
				

				prices.fadeTo("fast", 0.75);
				$.ajax({
				type: "POST",
				url: window.vmSiteurl + 'index.php?option=com_virtuemart&nosef=1&view=productdetails&task=recalculate&tmpl=component&virtuemart_product_id='+id+'&format=json' + window.vmLang,
				data: query,
				complete: 
					function (datas, textStatus) {
					if (!(datas.readyState==4 && datas.status==200)) return;
					Virtuemart.log(datas); 
					datasJson = jQuery.parseJSON(datas); 
					if (!(datasJson != null)) 
					{ 
					   //the json deserialization failed 
					   part = datas.responseText.indexOf('{"'); 
					   resp = datas.responseText.substr(part); 
					   datasJson = jQuery.parseJSON(resp); 
					}
					
					
					datas = datasJson; 
					Virtuemart.log(datas); 
						prices.fadeTo("fast", 1);
						// refresh price
						for (var key in datas) {
							var value = datas[key];
							if (value!=0) prices.find("span.Price"+key).show().html(value);
							else prices.find(".Price"+key).html(0).hide();
						}
					}
					});
				return false; // prevent reload
			},
			productUpdate : function(mod) {

				var $ = jQuery ;
				$.ajaxSetup({ cache: false })
				$.getJSON(window.vmSiteurl+"index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json"+window.vmLang,
					function(datas, textStatus) {
						if (datas.totalProduct >0) {
							mod.find(".vm_cart_products").html("");
							$.each(datas.products, function(key, val) {
								$("#hiddencontainer .container").clone().appendTo(".vmCartModule .vm_cart_products");
								$.each(val, function(key, val) {
									if ($("#hiddencontainer .container ."+key)) mod.find(".vm_cart_products ."+key+":last").html(val) ;
								});
							});
							mod.find(".total").html(datas.billTotal);
							mod.find(".show_cart").html(datas.cart_show);
						}
						mod.find(".total_products").html(datas.totalProductTxt);
					}
				);
			},
			sendtocart : function (form){

				if (Virtuemart.addtocart_popup ==1) {
					Virtuemart.cartEffect(form) ;
				} else {
					form.append('<input type="hidden" name="task" value="add" />');
					form.submit();
				}
			},
			cartEffect : function(form) {

                var $ = jQuery ;
                $.ajaxSetup({ cache: false });
                //var datas = form.serialize();
				var datas = form.serializeArray(); 
				
				
				query = ''; 
				for (var i=0; i<datas.length; i++)
				{
				  if (datas[i].name != 'undefined')
				  {
				    //stAn - no other characters then & have to be encoded here, all are handled by apache and other systems
				    query += '&'+datas[i].name+'='+datas[i].value.split("&").join("%26");
				  }
				}
				query += '&view=cart'; 
                if(usefancy){
                    $.fancybox.showActivity();
                }
				Virtuemart.log(query); 
				
                $.ajax({
				type: "POST",
				url: window.vmSiteurl + 'index.php?option=com_virtuemart&nosef=1&view=cart&task=addJS&format=json&tmpl=component' + window.vmLang,
				data: query,
				error: 
				function (datas, textStatus, errorThrown)
				{
				   Virtuemart.log(datas, textStatus, errorThrown); 
				},
				cache: false,
				complete: 
                function(datas, textStatus) {
				
				// error 500 could have a retry or at least a log information into js console
				if (!(datas.readyState==4 && datas.status==200)) return;
				    Virtuemart.log(datas);
				    datasJson = jQuery.parseJSON(datas); 
					if (!(datasJson != null)) 
					{ //the json deserialization failed 
					   Virtuemart.log(datas.responseText);
					   part = datas.responseText.indexOf('{"'); 
					   resp = datas.responseText.substr(part); 
					   datasJson = jQuery.parseJSON(resp); 
					}
					datas = datasJson; 
					
					
					
                    if(datas.stat ==1){

                        var txt = datas.msg;
                    } else if(datas.stat ==2){
                        var txt = datas.msg +"<H4>"+form.find(".pname").val()+"</H4>";
                    } else {
					    if (typeof vmCartError != 'undefined')
                        var txt = "<H4>"+vmCartError+"</H4>"+datas.msg;
						else 
						var txt = "<H4>Cart Error</H4>"+datas.msg;
                    }
					if ((typeof usefancy != 'undefined') && (usefancy))
                    {
                        $.fancybox({
                                "titlePosition" : 	"inside",
                                "transitionIn"	:	"elastic",
                                "transitionOut"	:	"elastic",
                                "type"			:	"html",
                                "autoCenter"    :   true,
                                "closeBtn"      :   false,
                                "closeClick"    :   false,
                                "content"       :   txt
                            }
                        );
                    } else {
					    if (typeof $.facebox != 'undefined')
						{
                         $.facebox.settings.closeImage = closeImage;
                         $.facebox.settings.loadingImage = loadingImage;
                         //$.facebox.settings.faceboxHtml = faceboxHtml;
                         $.facebox({ text: txt }, 'my-groovy-style');
						}
                    }

                    if ($(".vmCartModule")[0]) {
                        Virtuemart.productUpdate($(".vmCartModule"));
                    }
                }
				});

                $.ajaxSetup({ cache: true });
			},
			product : function(carts) {
				carts.each(function(){
					var cart = jQuery(this),
					step=cart.find('input[name="quantity"]'),
					addtocart = cart.find('input.addtocart-button'),
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
					addtocart.click(function(e) { 
						Virtuemart.sendtocart(cart);
						return false;
					});
					plus.click(function() {
						var Qtt = parseInt(quantity.val());
						if (!isNaN(Qtt)) {
							quantity.val(Qtt + Ste);
						Virtuemart.setproducttype(cart,virtuemart_product_id);
						}
						
					});
					minus.click(function() {
						var Qtt = parseInt(quantity.val());
						if (!isNaN(Qtt) && Qtt>Ste) {
							quantity.val(Qtt - Ste);
						} else quantity.val(Ste);
						Virtuemart.setproducttype(cart,virtuemart_product_id);
					});
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

			}, 
			log: function()
			{
			   //stAn,maybe a global logging variable should be present
			   if ((typeof virtuemart_debug != 'undefined') && (virtuemart_debug == true))
			   if (typeof console != 'undefined')
			   if (typeof console.log != 'undefined')
			   if (console.log != null)
			   for (var i = 0; i < arguments.length; i++) {
				console.log(arguments[i]);
				}
			   
			}
		};
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
	}
