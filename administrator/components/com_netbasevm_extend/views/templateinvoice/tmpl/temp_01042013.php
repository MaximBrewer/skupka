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

/* @var $this VMInvoiceViewOrder */

JHtmlBehavior::framework();
AdminUIHelper::startAdminArea();

$document = JFactory::getDocument();
// add files js
$files_js = array(
	'administrator/components/com_netbasevm_extend/assets/js/jquery.bpopup-0.9.0.js',
	'administrator/components/com_netbasevm_extend/assets/js/jquery.modcoder.excolor.js'
	);
	
foreach ($files_js as $js){
	$version = ($mtime = filemtime(JPATH_SITE.'/'.$js)) ? $mtime : time();
	$document->addScript(JURI::root().$js.'?v='.$version);
}

?>

<script type="text/javascript">
/* <![CDATA[ */
	jQuery(document).ready(function($) {
		 // show with accordion
		$( "#catalog" ).accordion();
		// create li can drag
		$( "#catalog span" ).draggable({
		  appendTo: "body",
		  helper: "clone"
		});
		// create ol can get li
		$( ".tbl_table .droptrue" ).droppable({
		  activeClass: "ui-state-default",
		  hoverClass: "ui-state-hover",
		  accept: ":not(.ui-sortable-helper)",
		  drop: function( event, ui ) {
			  var data_=ui.draggable.text();
			 var html_=ui.draggable.text();
			 html_+="<input class='del' type='button' value='Delete'  onclick='deleteMe(this);' /><input class='edit' type='button' value='Edit'  onclick='editMe(this);' />";
			$( "<div data='"+data_+"'></div>" ).html(html_).appendTo( this );
			
		  }
		}).sortable({
		  items: "div",
		  sort: function() {
			$( this ).removeClass( "ui-state-default" );
		  }
		});
		
		$('#color_').modcoder_excolor({
			callback_on_ok : function() {
				//alert('Hello World!\nYou chose '+$('#color_').val()+' !');
			}	
		});
		$('#bgcolor_').modcoder_excolor({
			callback_on_ok : function() {
				//alert('Hello World!\nYou chose '+$('#bgcolor_').val()+' !');
			}	
		});
		
		
	});
  
  // create pdf ajax
  function createPDF()
  {
	  var texthtml=jQuery('#alltext').html();
	  var textproduct=jQuery('#textproduct').html();
	  //alert(texthtml);
	  jQuery.ajax({
			url:'index.php?option=com_netbasevm_extend&controller=templateinvoice&task=createPDF',
			type: 'POST',
			data: ({texthtml:texthtml,textproduct:textproduct}),
			//dataType: "text",
			beforeSend: function() {
			},
			ajaxSend: function(){
				//jQuery("div.tab-2").html('Processing...');
			},
			complete: function() {
				//load complete
			},
			ajaxError : function() {
				//jQuery("div#cya-content-source-sub").html('Error: Can not load page');
			},
			success: function(data) {
				//alert(data);
				window.open('<?php echo JURI::root().'administrator/components/com_netbasevm_extend/assets/docs/example_006_jquery.pdf';?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
			}
	   });
  }
  
  // save pdf ajax
  function savePDF()
  {
	   var texthtml=jQuery('#alltext').html();
	  var textproduct=jQuery('#textproduct').html();
	  //alert(texthtml);
	  jQuery.ajax({
			url:'index.php?option=com_netbasevm_extend&controller=templateinvoice&task=savePDF',
			type: 'POST',
			data: ({texthtml:texthtml,textproduct:textproduct}),
			//dataType: "text",
			beforeSend: function() {
			},
			ajaxSend: function(){
				//jQuery("div.tab-2").html('Processing...');
			},
			complete: function() {
				//load complete
			},
			ajaxError : function() {
				//jQuery("div#cya-content-source-sub").html('Error: Can not load page');
			},
			success: function(data) {
				//alert(data);
				alert('File PDF saved !');
			}
	   });
  }
  
  // delete items
  function deleteMe(obj)
  {
	  jQuery(obj).parent('div').remove();
  }
  
  // show align
  function showAlign(number)
  {
	  jQuery('input.bt_'+number).toggle();
  }
  
  // set align
  function setAlign(obj)
  {
	  var val_=jQuery(obj).val();
	  //alert(val_);
	  jQuery(obj).parent('td').attr('align',val_);
	  jQuery(obj).parent('td').find('li').first().css('text-align',val_);
  }
  
  // edit items
  function editMe(obj)
  {
	 // Triggering bPopup when click event is fired
	 jQuery(obj).attr('id','input_current');
	 
	 // get value current
	 var li_=jQuery(obj).parent('div');
	 var temp_label_=li_.find('span#label_text_').first();
	 
	 var val_label_='';
	 if(temp_label_)
	 	var val_label_=temp_label_.text();
	
	var fontsize_=li_.css('font-size');
	var color_=li_.css('color');
	var bgcolor_=li_.css('background-color');
	var fontweight_=li_.css('font-weight');
	var select_align_=li_.css('text-align');
	var select_text_decoration_=li_.css('text-decoration');
		
	jQuery('#label_').val(val_label_);
	jQuery('#fontsize').val(parseInt(fontsize_));
	jQuery('#color_').val(color_);
	jQuery('#bgcolor_').val(bgcolor_);
	
     
	 jQuery('#popup_edit').bPopup(); 
  }
  
  function saveEdit()
  {
	  var li_=jQuery('#input_current').parent('div');
	  
	  var temp_label_=li_.find('span#label_text_').first();
	  if(temp_label_)
	 	temp_label_.remove();
	  
	  // get values
	  var label_=jQuery('#label_').val();
	  var fontsize_=jQuery('#fontsize option:selected').val();
	  var color_=jQuery('#color_').val();
	  var bgcolor_=jQuery('#bgcolor_').val();
	  var select_font_weight_=jQuery('#font_weight option:selected').val();
	  var select_align_=jQuery('#select_align option:selected').val();
	  var select_font_weight_=jQuery('#font_weight option:selected').val();
	  var select_text_decoration_=jQuery('#text_decoration option:selected').val();
	  var only_label_=jQuery('#only_label option:selected').val();
		
		var text_li_=li_.html();
		//var text_label_=li_.find('span#label_text_').first().text();
		//alert(text_label_);
		var data_=li_.attr('data');
		if(only_label_ == 'yes'){
			var text_li_new_=text_li_.replace(data_,'');
			li_.html(text_li_new_);
			
			li_.attr('edit','true');
		}
		else
		{
			var edit_=li_.attr('edit');
			if(edit_ == 'true')
			{
				li_.prepend(data_);
				li_.attr('edit','false');
			}
		}
		
		//if(text_label_ == '')
			li_.prepend('<span id="label_text_">'+label_+'</span>');
		//alert(text_li_);
		li_.css('font-size',fontsize_);
		li_.css('color',color_);
		li_.css('background-color',bgcolor_);
		li_.css('font-weight',select_font_weight_);
		li_.css('text-align',select_align_);
		li_.css('text-decoration',select_text_decoration_);
		
		jQuery('#input_current').removeAttr('id');
		jQuery('#popup_edit').bPopup().close()
  }
  
/* ]]> */
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
		  echo '<td width="30%" class="droptrue" valign="top"></td>';
	  }
	  echo '</tr>';
	}
}

?>

<div id="allcontent">
  <table class="adminheading" width="100%">
      <tr>
          <td valign="top" width="25%">
              <fieldset id="list_fields">
                  <legend><?php echo JText::_('COM_NETBASEVM_EXTEND_LIST_FIELDS')?></legend>
                  <div id="catalog">
                      <h2><a href="#"><?php echo JText::_('COM_NETBASEVM_EXTEND_FIELD_HEADER')?></a></h2>
                      <div>
                        <ul>
                          <li><label>Logo:</label><span>{logo}</span></li>
                          <li><label>Contact:</label><span>{contact}</span></li>
                        </ul>
                      </div>
                      <h2><a href="#"><?php echo JText::_('COM_NETBASEVM_EXTEND_INFO_PRODUCTS')?></a></h2>
                      <div>
                        <ul>
                          <li><label>Area Show Products:</label><span>{products}</span></li>	
                          <li><label>Quantity:</label><span>{quantity}</span></li>
                          <li><label>Payment Type:</label><span>{payment_type}</span></li>
                          <li><label>Shipping Type:</label><span>{shipping_type}</span></li>
                          <li><label>Invoice Number:</label><span>{invoice_number}</span></li>
                          <li><label>Invoice Date:</label><span>{invoice_date}</span></li>
                        </ul>
                      </div>
                      <h2><a href="#"><?php echo JText::_('COM_NETBASEVM_EXTEND_INFO_ADDRESS')?></a></h2>
                      <div>
                        <ul>
                          <li><label>Billing Address:</label><span>{billing_address}</span></li>
                          <li><label>Sipping Address:</label><span>{sipping_address}</span></li>
                        </ul>
                      </div>
                      <h2><a href="#"><?php echo JText::_('COM_NETBASEVM_EXTEND_BREAK_LINES')?></a></h2>
                      <div>
                        <ul>
                          <li><label>Break Line:</label><span>{break_line}</span></li>
                        </ul>
                      </div>
                   </div>
              </fieldset>
          </td>
          <td valign="top" width="75%">
              <fieldset id="template_invoices">
                  <legend><?php echo JText::_('COM_NETBASEVM_EXTEND_TEMPLATE_INVOICES')?></legend>
                      <span class="span_button" onclick="createPDF();">Create template PDF to view</span>
                      <span class="span_button" onclick="savePDF();">Save Template PDF</span>
                      <div style="clear:both;"></div>
                      <h1>Template General</h1>
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
						  	<?php echo $html;?>
                          <?php else:?>
                          <table id="tbl_" class="tbl_table" width="100%" cellpadding="0" cellspacing="0" border="1">
                              <tbody>
								  <?php 
                                    createTable(5,3);
                                  ?>
                                  <tr>
                                      <td colspan="25"  class="droptrue"><div data="{products}">{products}<input class="del" value="Delete" onclick="deleteMe(this);" type="button"><input class='edit' type='button' value='Edit'  onclick='editMe(this);' /></div>
                                      </td>
                                  </tr>
                                  <?php 
									createTable(5,3);
								  ?>
                              </tbody>
                          </table>
                          <?php endif;?>
                      </div>
                      <div style="clear:both;"></div>
                      <h1>Template Products</h1>
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
                        <table id="tbl_products" class="tbl_table" border="0" width="100%" cellpadding="0" cellspacing="0">
                            <tbody>
                                <?php 
                                  createTable(5,3);
                                ?>
                            </tbody>
                        </table>
                        <?php endif;?>
                      </div>
              </fieldset>
          </td>
      </tr>
  </table>
  <!-- Popup edit -->
  <div id="popup_edit">
  	<p><label>Label: </label><input id="label_" type="text" size="50" /></p>
    <p><label>Only show Label: </label>
    	<select id="only_label" name="only_label">
        	<option value="no">No</option>
            <option value="yes">Yes</option>
        </select>
    </p>
    <p><label>Font Size: </label>
    	<select id="fontsize" name="fontsize">
        	<option value="xx-small">xx-small</option>
            <option value="x-small">x-small</option>
            <option value="small">small</option>
            <option value="medium">medium</option>
            <option value="large">large</option>
            <option value="x-large">x-large</option>
            <option value="xx-large">xx-large</option>
        </select>
    </p>
    <p><label>Align: </label>
    	<select id="select_align" name="select_align">
        	<option value="center">Center</option>
            <option value="left">Left</option>
            <option value="right">Right</option>
        </select>
    </p>
    <p><label>Color: </label><input id="color_" type="text" value="#000000" size="15" /></p>
    <p><label>Background Color: </label><input id="bgcolor_" type="text" value="#ffffff" size="15" /></p>
    <p><label>Font-weight: </label>
    	<select id="font_weight" name="font_weight">
            <option value="normal">Normal</option>
            <option value="bold">Bold</option>
        </select>
    </p>
    <p><label>Text decoration: </label>
    	<select id="text_decoration" name="text_decoration">
            <option value="none">None</option>
            <option value="underline">Underline</option>
            <option value="overline">Overline</option>
            <option value="line-through">Line-through</option>
        </select>
    </p>
    <p><input class="span_button" type="button" value="Save" onclick="saveEdit();" /></p>
  </div>
  <!-- End -->
</div>
<?php AdminUIHelper::endAdminArea(); ?>