<?php
/*------------------------------------
* -Netbase- Advanced Virtuemart Invoices for Virtuemart
* Author    CMSMart Team
* Copyright (C) 2012 http://cmsmart.net. All Rights Reserved.
* @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* Websites: http://cmsmart.net
* Email: team@cmsmart.net
* Technical Support:  Forum - http://bloorum.com/forums
-----------------------------------------------------*/

defined('_JEXEC') or ('Restrict Access');

JHtmlBehavior::framework();

AdminUIHelper::startAdminArea($this);


$document = JFactory::getDocument();


// add files js

$files_js = array(

	'administrator/components/com_netbasevm_extend/assets/js/jquery.bpopup-0.9.0.js',
	'administrator/components/com_netbasevm_extend/assets/js/jquery.modcoder.excolor.js',

	);

	

foreach ($files_js as $js){

	$version = ($mtime = filemtime(JPATH_SITE.'/'.$js)) ? $mtime : time();

	$document->addScript(JURI::root().$js.'?v='.$version);

}
?>

<script type="text/javascript">
	var number_table_=1;

	function createTable($,html,idtable)
	{
		jQuery('#config_table_').bPopup();

		jQuery('#bt_save_table_').click(function(){

			var number_row_=parseInt($('#number_row_').val());

			var number_col_=parseInt($('#number_col_').val());

			var table_=$(idtable);

			//alert(idtable);

			for(var i=0;i< number_row_;i++)
			{
				var newTR_ = new Element('tr');
				
				for(var j=0;j < number_col_;j++)
				{
					var newTD_ = new Element('td');

					newTD_.set('html','<div class="droptrue"></div>');

					$(newTD_).appendTo(newTR_);
				}

				jQuery(newTR_).appendTo(table_);
			}

			jQuery( ".tbl_table_inner .droptrue" ).droppable({

			  activeClass: "ui-state-default",

			  hoverClass: "ui-state-hover",

			  accept: ":not(.ui-sortable-helper)",

			  drop: function( event, ui ) {

				 var data_=ui.draggable.text();

				 var html_=ui.draggable.text();

				 var clear_='style="display:inline-block;"';

				 if(html_ == '{break}'){

					 html_+="<input class='del' type='button' value='Delete'  onclick='deleteMe(this);' />";

					clear_='style="display:block;"';

				 }

				 else if(html_ == '{space}'){

					html_+="<input class='del' type='button' value='Delete'  onclick='deleteMe(this);' /><input class='edit' type='button' value='Edit'  onclick='editSpace(this);' />";	 

				 }

				 else if(html_ == '{table}'){

					 html_="<table class='tbl_table_inner' cellspacing='0' cellpadding='0' width='100%' id='table_"+number_table_+"'></table><input class='del' type='button' value='Delete'  onclick='deleteMe(this);' /><span style='display:block;'></span>";

					 createTable($,html_,'#table_'+number_table_);

					 number_table_++;

				} 

				else if(html_ == '{custom_text}'){

					html_+="<input class='del' type='button' value='Delete'  onclick='deleteMe(this);' /><input class='edit' type='button' value='Edit'  onclick='editMe(this,1);' />";

				}

				 else

				{

						html_+="<input class='del' type='button' value='Delete'  onclick='deleteMe(this);' /><input class='edit' type='button' value='Edit'  onclick='editMe(this,1);' />";

				}

				

				jQuery( "<span class='dragme' data='"+data_+"'"+clear_+" setWidth='50' ></span>" ).html(html_).appendTo( this );

			  }

			}).sortable({

			  items: "span",

			  sort: function() {

				jQuery( this ).removeClass( "ui-state-default" );

			  }

			});

		});

	}
	jQuery(document).ready(function($) {

		 // show with accordion

		jQuery( "#catalog" ).accordion();

		// create li can drag

		jQuery( "#catalog span" ).draggable({

		  appendTo: "body",

		  helper: "clone"

		});

		// create ol can get li

		jQuery( ".tbl_table .droptrue" ).droppable({

		  activeClass: "ui-state-default",

		  hoverClass: "ui-state-hover",

		  accept: ":not(.ui-sortable-helper)",

		  drop: function( event, ui ) {

			  var data_=ui.draggable.text();

			 var html_=ui.draggable.text();

			 var clear_='style="display:inline-block;"';

			 if(html_ == '{break}'){

				 html_+="<input class='del' type='button' value=''  onclick='deleteMe(this);' />";

			 	clear_='style="display:block;"';

			 }

			 else if(html_ == '{space}'){

				html_+="<input class='del' type='button' value=''  onclick='deleteMe(this);' /><input class='edit' type='button' value=''  onclick='editSpace(this);' />";	 

			 }

			 else if(html_ == '{table}'){

				 html_="<table class='tbl_table_inner' cellspacing='0' cellpadding='0' width='100%' id='table_"+number_table_+"'></table><input class='del' type='button' value=''  onclick='deleteMe(this);' /><span style='display:block;'></span>";

				 createTable($,html_,'#table_'+number_table_);

				 number_table_++;

			}

		   else if(html_ == '{custom_text}')

		   {

			   html_="<div class='custom_text'>Pls input custom text at here.</div><input class='del' type='button' value=''  onclick='deleteMe(this);' /><input class='edit' type='button' value=''  onclick='editMe(this,1);' />";

		   } 

		   else

			  {

				  html_+="<input class='del' type='button' value=''  onclick='deleteMe(this);' /><input class='edit' type='button' value=''  onclick='editMe(this,1);' />";

			  }

			

			// append html to tag div

			jQuery( "<span class='dragme' data='"+data_+"'"+clear_+" setWidth='50' ></span>" ).html(html_).appendTo( this );

		  }

		}).sortable({

		  items: "span",

		  sort: function() {

			jQuery( this ).removeClass( "ui-state-default" );

		  }

		});

		jQuery('#color_').modcoder_excolor({

			input_text_color : '#f00e3b',

			callback_on_ok : function() {

			}	

		});

		jQuery('#bgcolor_').modcoder_excolor({

			input_text_color : '#f00e3b',

			callback_on_ok : function() {

			}	

		});

		jQuery('#template_general').click(function(){

			jQuery('#alltext').toggle();

		});

		jQuery('#template_products').click(function(){

			jQuery('#textproduct').toggle();

		});

	});


  function rgb2hex(rgb){
	 //alert(rgb);	 
	 rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	 //alert(rgb);
	 return "#" +

	  ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +

	  ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +

	  ("0" + parseInt(rgb[3],10).toString(16)).slice(-2);

  }

  function createJSON(id)

  {

	  var template_=new Array();

	  var all_=jQuery(id).find('span.dragme');

	  if(all_.length > 0)

	  {

		  for(var i=0;i < all_.length;i++){

			  var txt_=jQuery(all_[i]).attr('data');

			  var label_=jQuery(all_[i]).attr('setLabel');

			  var width_=parseInt(jQuery(all_[i]).attr('setWidth'));

			  var height_=parseInt(jQuery(all_[i]).attr('setHeight'));

			  var fontsize_=parseInt(jQuery(all_[i]).css('font-size'));

			  var color_=jQuery(all_[i]).css('color');

			  var bgcolor_=jQuery(all_[i]).css('background-color');

			  var fontweight_=jQuery(all_[i]).css('font-weight');

			  var select_align_=jQuery(all_[i]).css('text-align');

			  var select_text_decoration_=jQuery(all_[i]).css('text-decoration');

			  var only_label_=jQuery(all_[i]).attr('onlyLabel');

			  

			  label_=label_?label_:'';

			  width_=width_?width_:1;

			  height_=height_?height_:1;

			  if(color_ == null)
			  	color_='#000000';
			  else	
			  	color_=rgb2hex(color_);

			  if((bgcolor_ == 'transparent')||(bgcolor_ == null))
			  	bgcolor_='#ffffff';
			  else
			  	bgcolor_=rgb2hex(bgcolor_);

			  template_[i]={name:txt_,label_:label_,show_label:only_label_,width:width_,height:height_,fontsize_:fontsize_,color:color_,bgcolor:bgcolor_,fontweight:fontweight_,align:select_align_,textdecoration:select_text_decoration_};
		  }
	  }

	  return template_;

  }

  function createPDF()

  {

	  var texthtml=jQuery('#alltext').html();

	  var textproduct=jQuery('#textproduct').html();

	  var template_=createJSON('#tbl_');

	  var items_=createJSON('#tbl_products');

	  jQuery.ajax({

			url:'index.php?option=com_netbasevm_extend&controller=templateinvoice&task=createPDF',

			type: 'POST',

			data: ({template:template_,items:items_}),

			beforeSend: function() {
				jQuery('.adminheading').fadeTo("slow", 0.3);
			},

			ajaxSend: function(){

			},

			complete: function() {

			},

			ajaxError : function() {
			},

			success: function(data) {

				//alert(data);
				jQuery('.adminheading').fadeTo("fast", 1);
				window.open('<?php echo JURI::root().'administrator/components/com_netbasevm_extend/assets/docs/example_006_jquery.pdf';?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
				
			}

	   });

  }

  // save pdf ajax

  function savePDF()

  {

	   var texthtml=jQuery('#alltext').html();
	  //echo texthml;
	  var textproduct=jQuery('#textproduct').html();
	  // get array json

	  var template_=createJSON('#tbl_');

	  var items_=createJSON('#tbl_products');

	  //alert(texthtml);

	  jQuery.ajax({

			url:'index.php?option=com_netbasevm_extend&controller=templateinvoice&task=savePDF',

			type: 'POST',

			data: ({texthtml:texthtml,textproduct:textproduct,template:template_,items:items_}),

			//dataType: "text",

			beforeSend: function() {
				jQuery('.adminheading').fadeTo("slow", 0.3);
			},

			ajaxSend: function(){

			},

			complete: function() {

			},

			ajaxError : function() {

			},

			success: function(data) {

				//alert(data);

				alert('File PDF saved !');
				jQuery('.adminheading').fadeTo("fast", 1);

			}

	   });

  }

  // delete items

  function deleteMe(obj)

  {

	  jQuery(obj).parent('span').remove();

  }

  // edit items

  function editMe(obj,showCustomText)

  {

	 // Triggering bPopup when click event is fired

	 jQuery(obj).attr('id','input_current');
	jQuery('#color_').parents('p').hide();
	jQuery('#fontsize').parent('p').hide();
	
	if(jQuery(obj).parent('span').attr('hascustomtext') == 0){
		//jQuery('#color_').parents('p').hide();
		//jQuery('#fontsize').parent('p').hide();
		jQuery('#bgcolor_').parents('p').hide();
		jQuery('.popup_edit_span_button').attr('value','Save');
		
	}
	 // get value current

	 var li_=jQuery(obj).parent('span');

	 li_.attr('hasCustomText',0);

	 // show textarea when custom text

	 var val_label_='';

	 if(showCustomText == 1){

	 	jQuery('#custom_text').css('display','block');

		li_.attr('hasCustomText',1);

		

		var span_custom=li_.find('.custom_text').first();

		val_label_=span_custom.html();

	 }

	 else
	
	 	jQuery('#custom_text').css('display','none');

	var width_=li_.attr('setWidth');

	var height_=li_.attr('setHeight');

	var fontsize_=li_.css('font-size');

	var color_=li_.css('color');

	var bgcolor_=li_.css('background-color');

	var fontweight_=li_.css('font-weight');

	var select_align_=li_.css('text-align');

	var select_text_decoration_=li_.css('text-decoration');

	jQuery('#label_').val(val_label_);

	jQuery('#width_').val(width_?parseInt(width_):'');

	jQuery('#height_').val(height_?parseInt(height_):'');

	//jQuery('#fontsize').val(parseInt(fontsize_));

	jQuery('#color_').val(color_);

	jQuery('#bgcolor_').val(bgcolor_);

	 jQuery('#popup_edit').bPopup(); 

  }

  function saveEdit()

  {

	  var li_=jQuery('#input_current').parent('span');

	  // get values

	  var label_=jQuery('#label_').val();

	  var width_=jQuery('#width_').val();

	  var height_=jQuery('#height_').val();

	  var fontsize_=jQuery('#fontsize').val();

	  var color_=jQuery('#color_').val();

	  var bgcolor_=jQuery('#bgcolor_').val();

	  //var only_label_=jQuery('#only_label option:selected').val();

	  var select_font_weight_=jQuery('#font_weight option:selected').val();

	  var select_align_=jQuery('#select_align option:selected').val();

	  var select_font_weight_=jQuery('#font_weight option:selected').val();

	  var select_text_decoration_=jQuery('#text_decoration option:selected').val();

	  var only_label_=jQuery('#only_label option:selected').val();

		var hasCustomText=li_.attr('hasCustomText');

		if(hasCustomText == 1)

		{

			var span_custom=li_.find('.custom_text').first();

			span_custom.html(label_);

		}

		li_.attr('setLabel',label_);

		li_.attr('onlyLabel',only_label_);

		li_.attr('setWidth',width_);

		li_.attr('setHeight',height_);

		li_.css('font-size',fontsize_+'px');

		
		li_.css('color',color_);

		li_.css('background-color',bgcolor_);

		

		li_.css('font-weight',select_font_weight_);

		li_.css('text-align',select_align_);

		li_.css('text-decoration',select_text_decoration_);

  }

 
  function resetEdit()

  {

	jQuery('#label_').val('');

	jQuery('#width_').val('');

	jQuery('#height_').val('');

	jQuery('#fontsize').val('');

	jQuery('#color_').val('#000000');

	jQuery('#bgcolor_').val('#ffffff');

	

	//li_.removeAttr('setLabel');

	li_.removeAttr('onlyLabel');

	li_.removeAttr('setWidth');

	li_.removeAttr('setHeight');

	saveEdit(); 

  }
  function closeEdit()

  {

	  jQuery('#input_current').removeAttr('id');

	  jQuery('#popup_edit').bPopup().close();
	 // jQuery('#color_').parents('p').show();
		//jQuery('#fontsize').parent('p').show();
		jQuery('#bgcolor_').parents('p').show();
		jQuery('.popup_edit_span_button').attr('value','Save & Preview');
		

  }

  // Functions for popup edit space

  function editSpace(obj)

  {

	  // Triggering bPopup when click event is fired

	 jQuery(obj).attr('id','input_current');

	 // get value current

	var li_=jQuery(obj).parent('span');

	var space_=li_.attr('setWidth');

	jQuery('#space_').val(space_?parseInt(space_):0);

	 jQuery('#popup_edit_space').bPopup(); 

  }
	function editRemin(obj)

	  {

		  // Triggering bPopup when click event is fired

		 jQuery(obj).attr('id','input_current');

		 // get value current

		var li_=jQuery(obj).parent('span');

		var space_=li_.attr('setWidth');

		jQuery('#space_').val(space_?parseInt(space_):0);

		 jQuery('#popup_edit_remin').bPopup(); 

	  }
  function resetSpace()

  {

	  var li_=jQuery('#input_current').parent('span');

	  // remove all span old

	  jQuery('#space_').val(0);

  }

  function saveSpace()

  {

	  var li_=jQuery('#input_current').parent('span');

	  var space_=parseInt(jQuery('#space_').val());

	  li_.attr('setWidth',space_);

	  li_.css('width',space_+'px');

  }

 
  function closeSpace()

  {
                                                                                                  
	  jQuery('#input_current').removeAttr('id');

	  jQuery('#popup_edit_space').bPopup().close()

  }

</script>

<?php 



// create tr,td

function createTable($numberTR,$numberTD)

{

	for($j=0;$j<$numberTR;$j++)

	{

	   echo '<tr>';

	  for($i=0;$i < $numberTD;$i++)

	  {

		  echo '<td class="droptrue" width="10%"></td>';

	  }

	  echo '</tr>';

	}

}



// create divs

function createDiv($numberDIV)

{

	for($j=0;$j<$numberDIV;$j++)

	{

		 echo '<div class="droptrue"></div>';

	}

}



// create span

function createSpan($numberSpan)

{

	for($j=0;$j<$numberSpan;$j++)

	{

		 echo '<span class="droptrue"></span>';

	}

}



?>



<div id="allcontent">

  <table class="adminheading" width="100%">

      <tr>

          <td valign="top" width="20%">

              <div id="list_fields">

                  <legend><?php echo JText::_('COM_NETBASEVM_EXTEND_LIST_FIELDS')?></legend>

                  <div id="catalog">

                      <h2><span class="info-vendor-text"><a href="#"><?php echo JText::_('COM_NETBASEVM_EXTEND_FIELD_HEADER')?></a></span></h2>

                      <div>

                        <ul>

                          <li><label>Logo:</label><span>{logo}</span></li>

                          <li><label>Contact:</label><span>{contact}</span></li>

                        </ul>

                      </div>

                      <h2><span class="info-text"><a href="#"><?php echo JText::_('COM_NETBASEVM_EXTEND_INFO_PRODUCTS')?></a></span></h2>

                      <div>

                        <ul>

                          <li><label title="Show products in order. Pls drag this.">Area Show Products:</label><span>{products}</span></li>	

                          <li><label title="show product name. drag this">Product Name:</label><span>{product_name}</span></li>

                          <li><label title="show quantity. drag this">Quantity:</label><span>{quantity}</span></li>

                          <li><label title="show product SKU. drag this">Product SKU:</label><span>{sku}</span></li>

                          <li><label title="show product price. drag this">Product Price:</label><span>{price}</span></li>

                          <li><label title="show product desc. drag this">Product Desc:</label><span>{product_desc}</span></li>

                          <li><label title="show product short desc. drag this">Product Short Desc:</label><span>{product_s_desc}</span></li>

                          <li><label title="show product weight. drag this">Product Weight:</label><span>{product_weight}</span></li>

                          <li><label title="show product weight unit. drag this">Product Weight Unit:</label><span>{product_weight_unit}</span></li>

                          <li><label title="show price not tax. drag this">Price Not Tax:</label><span>{price_notax}</span></li>

                          <li><label title="show price width tax. drag this">Price Width Tax:</label><span>{price_widthtax}</span></li>

                           <li><label title="show discount product. drag this">Discount:</label><span>{discount}</span></li>

                           <li><label title="show discount item. drag this">Discount Item:</label><span>{discount_item}</span></li>

                           

                           <li><label title="show image product. drag this">Product Image:</label><span>{product_image}</span></li>

                          <li><label title="show tax price. drag this">Tax Price:</label><span>{tax_price}</span></li>

                          <li><label title="show tax rate. drag this">Tax Rate:</label><span>{tax_rate}</span></li>

                          <li><label title="show tax item price. drag this">Tax Item Price:</label><span>{tax_item_price}</span></li>

                           <li><label title="show Subtotal Tax. drag this">Subtotal Tax:</label><span>{subtotal_tax}</span></li>

                           <li><label title="show Subtotal Price. drag this">Subtotal Price:</label><span>{subtotal_price}</span></li>

                           <li><label title="show Subtotal. drag this">Subtotal:</label><span>{subtotal}</span></li>

                        </ul>

                      </div>

                      <h2><span class="info-order-text"><a href="#"><?php echo JText::_('COM_NETBASEVM_EXTEND_INFO_MORES')?></a></span></h2>

                      <div>

                        <ul>

                        	<li><label title="show payment type. drag this">Payment Name:</label><span>{payment_name}</span></li>

                            <li><label title="show payment desc. drag this">Payment Desc:</label><span>{payment_desc}</span></li>
							<li><label title="show payment type. drag this">Payment Price:</label><span>{payment_price}</span></li>

                            <li><label title="show payment desc. drag this">Payment Tax:</label><span>{payment_tax}</span></li>

                          <li><label title="show shipping type. drag this">Shipping Type:</label><span>{shipping_type}</span></li>

                          <li><label title="show shipping address. drag this">Shipping Address:</label><span>{shipping_address}</span></li>

                          <li><label title="show date shipping. drag this">Shipping Date:</label><span>{shipping_date}</span></li>

                            <li><label title="show shipping name. drag this">Shipping Name:</label><span>{shipping_name}</span></li>

                             <li><label title="show shipping desc. drag this">Shipping Desc:</label><span>{shipping_desc}</span></li>
                          <li><label title="show shipping desc. drag this">Shipping Tax:</label><span>{shipping_tax}</span></li>
 							<li><label title="show shipping desc. drag this">Shipping Price:</label><span>{shipping_price}</span></li>
                          <li><label title="show invoice number. drag this">Invoice Number:</label><span>{invoice_number}</span></li>

                           <li><label title="show order number. drag this">Order Number:</label><span>{order_number}</span></li>
                          <li><label title="show shipping desc. drag this">Order Tax:</label><span>{order_tax}</span></li>
 						<li><label title="show shipping desc. drag this">Order Subtotal:</label><span>{order_subtotal}</span></li>
						<li><label title="show shipping desc. drag this">Order Total:</label><span>{order_total}</span></li>
                          <li><label title="show invoice date. drag this">Invoice Date:</label><span>{invoice_date}</span></li>

                          <li><label title="show billing address. drag this">Billing Address:</label><span>{billing_address}</span></li>

                          <li><label title="show customer number. drag this">Customer Number:</label><span>{customer_number}</span></li>

                          <li><label title="show shopper group. drag this">Shopper Group:</label><span>{shopper_group}</span></li>

                           <li><label title="show customer note. drag this">Customer Note:</label><span>{customer_note}</span></li>

                           <li><label title="show coupon code. drag this">Coupon Code:</label><span>{coupon_code}</span></li>

                           <li><label title="show coupon discount. drag this">Coupon Discount:</label><span>{coupon_discount}</span></li>

                           <li><label title="show total price product. drag this">Total Price:</label><span>{total_price}</span></li>

                           <li><label title="show order discount. drag this">Order Discount:</label><span>{order_discount}</span></li>

                       <li><label title="show order status. drag this">Order Status:</label><span>{order_status}</span></li>

                        </ul>

                      </div>

                      <h2><span class="break-line-text"><a href="#"><?php echo JText::_('COM_NETBASEVM_EXTEND_BREAK_LINES')?></a></span></h2>

                      <div>

                        <ul>

                          <li><label title="create new line.">Break Line:</label><span>{break}</span></li>

                           <li><label title="create space between elements .">Space:</label><span>{space}</span></li>

                        </ul>

                      </div>

                       <h2><span class="custom-text-text"><a href="#"><?php echo JText::_('COM_NETBASEVM_EXTEND_CUSTOM_TEXT')?></a></span></h2>

                      <div>

                        <ul>

                          <li><label title="add custom text you want show.drag this">Custom Text:</label><span>{custom_text}</span></li>

                        </ul>

                      </div>

                   </div>

              </div>

          </td>

          <td valign="top" width="80%">

              <div id="template_invoices">

                  <legend><?php echo JText::_('COM_NETBASEVM_EXTEND_TEMPLATE_INVOICES')?></legend>
                      <span class="span_button" onclick="createPDF();">Create template PDF to view</span>

                      <span class="span_button" onclick="savePDF();">Save Template PDF</span>
						<br/><br/>
                      <div style="clear:both;"></div>
					 <div class="template_general_boder">	
                      <h1 id="template_general">Template General</h1>

                      <?php 

					  	jimport('joomla.filesystem.file');

						

						$path = JPATH_ADMINISTRATOR.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'tmp'.DS.'template_general.html';

						$html='';

						if(JFile::exists($path)){

							$html = file_get_contents($path);

						}

					  ?>

                      <div id="alltext">

                      	  <?php if($html !=''):?>

						  	<?php echo html_entity_decode($html);?>

                          <?php else:?>

                          <div id='tbl_' class='tbl_table' style='width:100%;'>

								  <?php 

                                    createDiv(1);

								  ?>

                          </div>

                          <?php endif;?>

                      </div>
					</div>
                     
					<div class="template_products-border">
                      <h1 id="template_products">Template Products</h1>

                      <div id="textproduct">

                      	<?php

							$path_product = JPATH_ADMINISTRATOR.DS.'components' . DS . 'com_netbasevm_extend' . DS.'assets'.DS.'tmp'.DS.'template_product.html';

							$html_product='';

							if(JFile::exists($path_product)){

								$html_product = file_get_contents($path_product);

							}

					  	?>

                      	<?php if($html_product !=''):?>

						  <?php echo $html_product;?>

                        <?php else:?>

                        <div id="tbl_products" class="tbl_table"  style="width:100%;">

							<?php 

                              createDiv(1);

                            ?>

                        </div>

                        <?php endif;?>

                      </div>
					</div>
              </div>

          </td>

      </tr>

  </table>

  <!-- Popup edit -->

  <div id="popup_edit">

  	<p id="custom_text"><label>Custom text: </label><textarea id="label_" cols="4" rows="4" ></textarea></p>

    <p><label>Width: </label>

    	<input id="width_" type="text" size="10" />

    </p>

    <p style="display:none;"><label>Height: </label>

    	<input id="height_" type="text" size="10" />

    </p>

    <p style="display:none;"><label>Show only on a line: </label>

    	<select id="only_label" name="only_label">

        	<option value="no">No</option>

            <option value="yes">Yes</option>

        </select>

    </p>

    <p><label>Font Size: </label>

    	<input id="fontsize" type="text" size="10" value=""/>

    </p>

    <p><label>Align: </label>

    	<select id="select_align" name="select_align">

        	<option value="center">Center</option>

            <option value="left">Left</option>

            <option value="right">Right</option>

        </select>

    </p>

    <p><label>Color: </label><input id="color_" type="text" value="#000000" readonly="readonly" size="15" /></p>

    <p><label>Background Color: </label><input id="bgcolor_" type="text" value="#ffffff" readonly="readonly" size="15" /></p>

    <p style="display:none;"><label>Font-weight: </label>

    	<select id="font_weight" name="font_weight">

            <option value="normal">Normal</option>

            <option value="bold">Bold</option>

        </select>

    </p>

    <p style="display:none;"><label>Text decoration: </label>

    	<select id="text_decoration" name="text_decoration">

            <option value="none">None</option>

            <option value="underline">Underline</option>

            <option value="overline">Overline</option>

            <option value="line-through">Line-through</option>

        </select>

    </p>

    <p class="btn_invoice">
        <input class="popup_edit_span_button" type="button" value="Save & Preview" onclick="saveEdit();" />
        <input class="span_button" type="button" value="Reset" onclick="resetEdit();" />
        <input class="span_button" type="button" value="Close" onclick="closeEdit();" />
    </p>

  </div>

  <!-- End -->

  

  <!-- Popup edit space -->

  <div id="popup_edit_space">

  	<p><label>Width: </label><input id="space_" type="text" size="50" /></p>

    <p><input class="popup_edit_space_span_button" type="button" value="Save & Preview" onclick="saveSpace();" /></p>

    <p><input class="span_button" type="button" value="Reset" onclick="resetSpace();" /></p>

    <p><input class="span_button" type="button" value="Close" onclick="closeSpace();" /></p>

  </div>

  <!-- End -->

   

  <!-- Popup config table -->

  <div id="config_table_">

  	<p><label>Number Row: </label><input id="number_row_" type="text" size="50" /></p>

    <p><label>Number Column: </label><input id="number_col_" type="text" size="50" /></p>

    <!--<p><label>Width Column: </label><input id="width_col_" type="text" size="50" />%</p>-->

    <p><input class="config_table_span_button" type="button" value="Save & Preview" id="bt_save_table_" /></p>

    <p><input class="span_button" type="button" value="Reset" id="bt_reset_table_" /></p>

    <p><input class="span_button" type="button" value="Close" id="bt_close_table_" /></p>

  </div>

  <!-- End -->

  

</div>

<?php AdminUIHelper::endAdminArea(); ?>