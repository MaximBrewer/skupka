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

defined('_JEXEC') or die('Restrict Access');

JHTML::_('behavior.tooltip');
JHtml::_('behavior.calendar');
AdminUIHelper::startAdminArea($this);
global $mainframe;

$delivery_note = $this->delivery_note;

//build string with neccesay statuses for invice cration (from invoice config)
$orderStatuses = (array)NbordersHelper::getParams()->get('order_status');

foreach ($orderStatuses as &$orderStatus)
	$orderStatus = isset($this->statuses[$orderStatus]) ? $this->statuses[$orderStatus]->name : $orderStatus;
        
if (count($orderStatuses)==1)
	$sendStatuses = $orderStatuses[0];
elseif (count($orderStatuses)>1)  {
	$sendStatuses = ' '.JText::_('COM_NETBASEVM_EXTEND_OR').' '.array_pop($orderStatuses);
	$sendStatuses = implode(', ',$orderStatuses).$sendStatuses;
}
           
?>

<script language="javascript" defer="defer">

function show_change(div_id)
{
	var div = document.getElementById(div_id);

	if (div.style.display=='none')
		div.style.display='block';
	else 
		div.style.display='none';
}

function show_change_date(order_id)
{
	var div = document.getElementById('change_order_date_'+order_id);

	if (div.style.display=='none'){
		div.style.display='block';
		div.getElement('img.calendar').fireEvent('click');}
	else {
		div.style.display='none'; 
		calendar.hide();}
}

function reset_search()
{
	$('filter_orders').getElements('input[type=text]').set('value','');
	$('filter_orders').getElements('input[type=checkbox]').set('checked',false);
	$('filter_orders').getElements('option').set('selected',false);
}


//before batch sending
function clicked_batch()
{
	if ($('batch_select_selected_list').checked && document.adminForm.boxchecked.value==0){
		alert('<?php echo JText::_('COM_NETBASEVM_EXTEND_CHECK_AT_LEAST_ONE_ORDER')?>');
		document.adminForm.task.value='';
		return false;}

	document.adminForm.target = '';
	document.adminForm.task.value='batch';
	
	//download pdfs - open form target in new window
	if ($('batch_download').checked){ //generator_order_by.value
		newwindow = window.open('index.php?option=com_netbasevm_extend&controller=nborders','win2', 'status=yes,toolbar=yes,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
		document.adminForm.target = 'win2';
		if (window.focus) newwindow.focus()
		return true;
	}
	
	return true;
}

//before batch sending
function clicked_filter()
{
	document.adminForm.task.value='';
	document.adminForm.target = '';

	return true;
}

</script>

<?php 
$total =  $this->get('Total'); 
JHTML::_('behavior.calendar');

$params = NbordersHelper::getParams();

$options = array();
$options[]=JHTML::_('select.option','invoice', JText::_('COM_NETBASEVM_EXTEND_INVOICES'));
$options[]=JHTML::_('select.option','dn', JText::_('COM_NETBASEVM_EXTEND_DELIVERY_NOTES'));

$starting_order = $params->get('starting_order',0);

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<table class="adminheading" width="100%">
	<tr>
		<td valign="top">
        	<?php echo  JHtml::_('tabs.start', 'list-orders'); ?>
            <?php echo JHtml::_('tabs.panel', JText::_('COM_NETBASEVM_EXTEND_FILTER_ORDERS'), 'filter-order'); ?>
			<fieldset id="filter_orders" style="width:99.8% !important;">

				<table style="width:100%" cellpadding="0" cellspacing="0" class="admintable">
				<tr><td style="width:120px">
					<label class= "start_date" for="start_date">
		           	<?php echo JText::_('COM_NETBASEVM_EXTEND_DATE_FROM'); ?>: </label>
		       		</td><td>
					<?php echo JHTML::calendar(JRequest::getVar('filter_start_date'), 'filter_start_date', 'start_date', '%Y-%m-%d', 'style="float: left !important;margin: 2px 0 0;"'); ?>
		           	<label for="filter_end_date" style="margin-left:20px; padding-top: 8px; float: left;"><?php echo JText::_('COM_NETBASEVM_EXTEND_DATE_TO'); ?>: </label>
					<?php echo JHTML::calendar(JRequest::getVar('filter_end_date'), 'filter_end_date', 'end_date', '%Y-%m-%d','style="float: left !important;margin: 2px 0 0;"'); ?>
                    <br/><br/>
				</td></tr><tr><td>
					<label for="order_status"> <?php echo JText::_('COM_NETBASEVM_EXTEND_STATUS')?></label>
					</td><td>					
					<?php echo JHTML::_('select.genericlist', $this->statuses, 'filter_order_status[]', 'multiple="multiple" size="5"', 'id', 'name', JRequest::getVar('filter_order_status',array()));  ?>
				<?php //echo JHTML::_ ('select.genericlist', $this->statuses, "filter_order_status[]",  $order->order_status, TRUE); ?>
					
				</td></tr><tr><td>
					<label class="filter_id" for="filter_id"> <?php echo JText::_('COM_NETBASEVM_EXTEND_ORDER_ID')?></label>
					</td><td>
					<input style="float: left !important;" type="text" name="filter_id" id="filter_id" value="<?php echo JRequest::getVar('filter_id')?>"/>
                    <label for="filter_email" style="margin: 7px 0 0 18px;"> <?php echo JText::_('COM_NETBASEVM_EXTEND_MAIL'); ?>  </label>
                    <input type="text" name="filter_email" id="filter_email" value="<?php echo JRequest::getVar('filter_email')?>" />
                    <br/><br/>
				</td></tr><tr><td>
				<?php if ($this->order_numbering=='own') { ?>
					<label for="filter_inv_prefix"> <?php echo JText::_('COM_NETBASEVM_EXTEND_INVOICE_PREFIX'); ?>: </label>
					</td><td>
					<input type="text" name="filter_inv_prefix" id="filter_inv_prefix" value="<?php echo JRequest::getVar('filter_inv_prefix')?>" />
                    
					</td></tr><tr><td>
				<?php } ?>
                                        <label class="filter_name" for="filter_name" style=" line-height:18px;" >First name <span style="opacity:0.5">or</span><br>Last name </label>
                   
					</td><td>
					 <input type="text" name="filter_name" id="filter_name" value="<?php echo JRequest::getVar('filter_name')?>"/>
                    <input type="submit" class="input_bg" value="<?php echo JText::_('COM_NETBASEVM_EXTEND_FILTER_ORDERS'); ?>" style="margin-left:50px;cursor:pointer;" onclick="clicked_filter();">
                    <input type="button" value="<?php echo JText::_('COM_NETBASEVM_EXTEND_CLEAR'); ?>" class="input_bg" style="margin-left:15px;cursor:pointer;" onclick="reset_search();this.form.submit();">
					
	           	</td></tr>
                
				</table>
			</fieldset>
			<?php echo JHtml::_('tabs.panel', JText::_('COM_NETBASEVM_EXTEND_BATCH_ACTION'), 'filter-batch-order'); ?>
            <table id="batch_action" width="100%" cellpadding="0" cellspacing="0" class="admintable">
            <tr>
            	<th><?php echo JText::_('COM_NETBASEVM_EXTEND_PROCESS_ORDERS')?></th>
                <th><?php echo JText::_('COM_NETBASEVM_EXTEND_BATCH_ACTION')?></th>
            </tr>
            <tr>
			<td valign="top" width="40%" align="left">
				<table width="100%" cellpadding="0" cellspacing="0" class="admintable">
				<tr>
					<td>
                    <br/>
					<label><input type="radio" id="batch_select_selected_list" name="batch_select" value="selected_list"<?php if (JRequest::getVar('batch_select','selected_list')=='selected_list') echo ' checked';?>> <?php echo JText::_('COM_NETBASEVM_EXTEND_ORDERS_CHECKED_IN_LIST')?></label>
					</td>
				</tr>
				<tr>
					<td>
					<label><input type="radio" name="batch_select" value="all_filtered"<?php if (JRequest::getVar('batch_select')=='all_filtered') echo ' checked';?>> <?php echo JText::_('COM_NETBASEVM_EXTEND_ORDERS_MATCHING_FILTER')?></label>	
					</td>
				</tr>
			</table>
			</td>
			
			<td align="left">
			
			<table width="100%" cellpadding="0" cellspacing="0" class="admintable">
				<tr>
					<td>
                    <br/>
					<label><input type="radio" id="batch_download" name="batch" value="download"<?php if (JRequest::getVar('batch','download')=='download') echo ' checked';?>> <?php echo JText::_('COM_NETBASEVM_EXTEND_DOWNLOAD')?></label>
					<?php if ($params->get('delivery_note')==1) echo JHTML::_('select.genericlist', $options, 'batch_download_option', null, 'value', 'text', JRequest::getVar('batch_download_option'));  ?>
					</td>
				</tr>
				<tr>
					<td>
				<label><input type="radio" name="batch" value="mail"<?php if (JRequest::getVar('batch')=='mail') echo ' checked';?>> <?php echo JText::_('COM_NETBASEVM_EXTEND_SEND_EMAIL')?></label>
				<?php 
				if ($params->get('delivery_note') && !$params->get('send_both',1))
					echo JHTML::_('select.genericlist', $options, 'batch_mail_option', null, 'value', 'text', JRequest::getVar('batch_mail_option'));
				elseif ($params->get('delivery_note') && $params->get('send_both',1))
					echo '<label>&nbsp; '.JString::strtolower(JText::_('COM_NETBASEVM_EXTEND_INVOICES')).' & '.JString::strtolower(JText::_('COM_NETBASEVM_EXTEND_DELIVERY_NOTES')).'</label>';
				else
					echo '<label>'.JString::strtolower(JText::_('COM_NETBASEVM_EXTEND_INVOICES')).'</label>';
					?>
				<label><input type="checkbox" name="batch_mail_force" value="1"<?php if (JRequest::getVar('batch_mail_force','0')==1) echo ' checked';?>>
					<span class="batch_mail_force"><?php echo JText::_('COM_NETBASEVM_EXTEND_ALSO_ALREADY_SENT')?></span>
				</label>
					</td>
				</tr>
				<?php if ($params->get('invoice_number')=='own') { ?>
				<tr>
					<td>
					<label><input type="radio" name="batch" value="create_invoice"<?php if (JRequest::getVar('batch')=='create_invoice') echo ' checked';?>> <?php echo JText::_('COM_NETBASEVM_EXTEND_CREATE_INVOICE_NUMBERS')?></label>
					</td>
				</tr>
				<?php } ?>
				<?php if ($params->get('cache_pdf')) { ?>
				<tr>
					<td>
				<label><input type="radio" name="batch" value="generate"<?php if (JRequest::getVar('batch')=='generate') echo ' checked';?>> <?php echo JText::_('COM_NETBASEVM_EXTEND_PRE-GENERATE_PDFS')?></label>
				<label><input type="checkbox" name="batch_generate_force" value="1"<?php if (JRequest::getVar('batch_generate_force','0')==1) echo ' checked';?>>
					<span class="batch_generate_force"><?php echo JText::_('COM_NETBASEVM_EXTEND_ALSO_ALREADY_GENERATED')?><span>
				</label>
					</td>
				</tr>
				<?php } ?>
				
				<tr>
					<td>
				<label><input type="radio" name="batch" value="change_status"<?php if (JRequest::getVar('batch')=='change_status') echo ' checked';?>> <?php echo JText::_('COM_NETBASEVM_EXTEND_CHANGE_STATUS')?></label>
				<?php echo JHTML::_('select.genericlist', $this->statuses, 'batch_status', null, 'id', 'name', JRequest::getVar('batch_status')); ?>
				<span style="display: block; float: right; margin-right: 445px;"><label><input type="checkbox" name="batch_notify_customer" value="Y"<?php if (JRequest::getVar('batch_notify_customer')==1) echo ' checked';?>>
					<span class="batch_notify_customer"><?php echo JText::_('COM_NETBASEVM_EXTEND_NOTIFY_CUSTOMER')?></span>
				</label></span>
					</td>
				</tr>
				
				<tr>
					<td>
				 <br/>
                <br/>
                <input type="submit" class="input_bg batch_process_button" value="<?php echo JText::_('COM_NETBASEVM_EXTEND_PROCESS')?>" style="float:right!important; margin-right:75px;cursor:pointer;" onclick="return clicked_batch();">
					</td>
				</tr>
			</table>
			</td>
            </tr>
            </table>
			<!--  
				<fieldset style="text-align:right">
					<input type="button" value="<?php echo JText::_('COM_NETBASEVM_EXTEND_GENERATE_ALL'); ?>" class="hasTip" title="<?php echo JText::_('COM_NETBASEVM_EXTEND_GENERATE_ALL_DESC'); ?>"
					onclick="javascript:void window.open('../index.php?option=com_netbasevm_extend&view=vminvoice&task=cronGenerate', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');" >
				</fieldset>
			-->
            <?php echo JHtml::_('tabs.end'); ?>
		</td>
	</tr>
</table>
<br/>
<div id="editcell">
<table class="adminlist listorder" cellpadding="0" cellspacing="0" style="border:1px solid #ebebeb;" width="100%">
	<thead>
		<tr>
		<?php 
		//build header array to pass it to plugin
		$header = array();
		$header['id'] = '<th width="5">'.JText::_('ID').'</th>';	
		$header['check'] = '<th width="20"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>';
		$header['order_id'] = '<th width="30">'.JText::_('COM_NETBASEVM_EXTEND_ORDER_ID').'</th>';
		$header['order_no'] = '<th width="60">'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_NO').'</th>';
		$header['edit'] = '<th width="1%">'.JText::_('COM_NETBASEVM_EXTEND_EDIT_ORDER').'</th>';
		$header['name'] = '<th>'.JText::_('COM_NETBASEVM_EXTEND_CLIENT_NAME').'</th>';
		//$header['company'] = '<th>'.JText::_('COM_NETBASEVM_EXTEND_COMPANY').'</th>';
		$header['email'] = '<th>'.JText::_('COM_NETBASEVM_EXTEND_MAIL').'</th>';
		$header['status'] = '<th width="210">'.JText::_('COM_NETBASEVM_EXTEND_STATUS').'</th>';
		
		$header['created_date'] = '<th width="80">'.JText::_('COM_NETBASEVM_EXTEND_CREATED_DATE').'</th>';
		$header['modified_date'] = '<th width="80">'.JText::_('COM_NETBASEVM_EXTEND_LAST_MODIFIED').'</th>';
		
		$header['total'] = '<th width="80">'.JText::_('COM_NETBASEVM_EXTEND_TOTAL').'</th>';
		
		$header['order_date'] = '<th width="80">'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_DATE').'</th>';
		
		$header['order_sent'] = '<th width="45">'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_SENT').'</th>';
		if ($delivery_note)
			$header['dn_sent'] = '<th width="45" title="'.JText::_('COM_NETBASEVM_EXTEND_DELIVERY_NOTE_SENT').'">'.JText::_('COM_NETBASEVM_EXTEND_DN_SENT').'</th>';
		$header['order_mail'] = '<th width="45" title="'.JText::_('COM_NETBASEVM_EXTEND_MAIL_INVOICE').'">'.JText::_('COM_NETBASEVM_EXTEND_MAIL').'</th>';
		if ($delivery_note)
			$header['dn_mail'] = '<th width="45" title="'.JText::_('COM_NETBASEVM_EXTEND_MAIL_DELIVERY_NOTE_ONLY').'">'.JText::_('COM_NETBASEVM_EXTEND_MAIL_DN').'</th>';
		$header['generate_invoice'] = '<th width="45" title="'.JText::_('COM_NETBASEVM_EXTEND_GENERATE_INVOICE_PDF').'">'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_PDF').'</th>';
		if ($delivery_note)
			$header['generate_dn'] = '<th width="45" title="'.JText::_('COM_NETBASEVM_EXTEND_GENERATE_DELIVERY_NOTE_PDF').'">'.JText::_('COM_NETBASEVM_EXTEND_DN_PDF').'</th>';
		
		//support for custom plugins
		$this->dispatcher->trigger('onInvoicesListHeader', array(&$header, $this));
		echo implode(PHP_EOL, $header);
		?>
		</tr>
	</thead>
	<?php
	$k = 0;
	for ($i = 0, $n = count($this->invoices); $i < $n; $i++) {
	    //$tm1 = microtime(true);
	    //echo '<tr><td colspan="14">' . ($tm1 - $tm2) . '</td></tr>';
	    //$tm2 = $tm1; 
		$row = $this->invoices[$i];
		$checked 	= JHTML::_('grid.id', $i, $row->order_id);
		
		$editOrder_url = "index.php?option=com_netbasevm_extend&controller=nborders&task=editOrder&cid=". $row->order_id;
		
		
		$pdf_url = "index.php?option=com_netbasevm_extend&controller=nborders&task=pdf&cid=". $row->order_id;
		$pdf_dn_url = "index.php?option=com_netbasevm_extend&controller=nborders&task=pdf_dn&cid=". $row->order_id;
		$pdf_link = "&nbsp;<a class='cms-pdf' href=\"javascript:void window.open('$pdf_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\">";
		$pdf_dn_link = "&nbsp;<a href=\"javascript:void window.open('$pdf_dn_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\">";
        $mail_url = "index.php?option=com_netbasevm_extend&controller=nborders&task=send_mail&cid=". $row->order_id;
		$mail_dn_url = "index.php?option=com_netbasevm_extend&controller=nborders&task=send_delivery_note&cid=". $row->order_id;

		$onclick="onclick=\"return confirm('".JText::_('COM_NETBASEVM_EXTEND_RESEND_INVOICE_PROMPT')."');\"";
		$mail_link = "&nbsp;<a href='$mail_url' ".($row->order_mailed ? $onclick : '').">";
		$onclick="onclick=\"return confirm('".JText::_('COM_NETBASEVM_EXTEND_RESEND_DN_PROMPT')."');\"";
        $mail_dn_link = "&nbsp;<a href='$mail_dn_url' ".($row->dn_mailed ? $onclick : '').">";       
		
        $item = array();
        $item['id'] = '<td>'.($i+1).'</td>';
        $item['check'] = '<td>'.$checked.'</td>';
        $item['order_id'] = '<td>'.$row->order_id.'</td>';
        
        if ($this->order_numbering!='own')
        	$item['order_no'] = '<td>'.$row->invoiceNoFull.'</td>';
        else{

			if ($row->invoiceNoFull){
        		$item['order_no'] = '
				<td><a href="javascript:void(0)" onclick="show_change(\'change_order_no_'.$row->order_id.'\');" title="'.JText::_('COM_NETBASEVM_EXTEND_EDIT').'">
				'.$row->invoiceNoFull.'
				</a>
				<div style="display:none" id="change_order_no_'.$row->order_id.'">'
				.($this->prefix_editing==1 ? '
					<input style="width:100%" type="text" size="8" name="order_prefix['.$row->order_id.']" value="'.$row->invoiceNoPrefix.'" title="'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_PREFIX').'">
					<input style="width:100%" type="text" size="8" name="order_no['.$row->order_id.']" value="'.$row->invoiceNoDb.'" title="'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_NO').'">
				' : '
					<input style="width:100%" type="text" size="8" name="order_no['.$row->order_id.']" value="'.$row->invoiceNoDb.'" title="'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_NO').'">
				').'
				<input style="width:100%" type="submit" name="update_inv_no['.$row->order_id.']" value="'.JText::_('COM_NETBASEVM_EXTEND_EDIT').'">
				</div></td>';
        	}
        	else {
        		$item['order_no'] = '<td><a href="javascript:void(0)" onclick="show_change(\'change_order_no_'.$row->order_id.'\');">
				<span class="hasTip" title="'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_NUMBER_NOT_GENERATED_YET').'">'.JText::_('COM_NETBASEVM_EXTEND_CREATE').'</span>
				</a>
				<div style="display:none" id="change_order_no_'.$row->order_id.'">'
				.($this->prefix_editing==1 ? '
					<input style="width:100%" type="text" size="8" name="order_prefix['.$row->order_id.']" value="'.$this->default_prefix.'" title="'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_PREFIX').'">
					<input style="width:100%" type="text" size="8" name="order_no['.$row->order_id.']" value="'.$this->newInoviceNo.'" title="'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_NO').'">			
				' : '
					<input style="width:100%" type="text" size="8" name="order_no['.$row->order_id.']" value="'.$this->newInoviceNo.'" title="'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_NO').'">
				').'
				<input style="width:100%;" type="submit" name="update_inv_no['.$row->order_id.']" value="'.JText::_('COM_NETBASEVM_EXTEND_CREATE').'">
				</div></td>';
        	}
       } 
        
        $item['edit'] = '<td align="center"><a class="editOrder" href="'.$editOrder_url.'" title="'.JText::_("Edit order").'"><span class="unseen">'.JText::_("Edit order").'</span></a></td>';
        
        
        $item['name'] = '<td>'.stripslashes($row->last_name) . ' ' . stripslashes($row->first_name).'</td>';
        
        
        
        //$item['company'] = '<td>'.stripslashes($row->company).'</td>';
        $item['email'] = '<td>'.$row->email.'</td>';
        
        
        $item['status'] = '<td>'.JHTML::_('select.genericlist', $this->statuses, 'status['.$row->order_id.']', null, 'id', 'name', $row->order_status).'
	    <input type="submit"  class="input_bg"  style="float:right;padding:1px 2px !important;cursor:pointer;" name="update['.$row->order_id.']" value="'.JText::_('COM_NETBASEVM_EXTEND_UPDATE').'">
		<span style="white-space: nowrap; margin-left:-4px;margin-top:2px;"><input type="checkbox" name="notify['.$row->order_id.']" value="YF">'.JText::_('COM_NETBASEVM_EXTEND_NOTIFY_CUSTOMER').'</span></td>';
        
        
        
      	
        $item['created_date'] = '<td>'.JHTML::_('date',  $row->cdate, JText::_('DATE_FORMAT_LC3')).'</td>';
        $item['modified_date'] = '<td>'.JHTML::_('date',  $row->mdate, JText::_('DATE_FORMAT_LC3')).'</td>';
        
        
        $item['total'] = '<td>'.InvoiceCurrencyDisplay::getFullValue($row->order_total, $row->order_currency).'</td>';
        
        
        
        if ($row->invoiceNoFull==false){

	        if ($row->order_id<$starting_order)
	        	$reason=JText::_('COM_NETBASEVM_EXTEND_INVOICES AREN\'T AUTOMATICALLY CREATED BEFORE ORDER').' '.$starting_order;
	        else
	        	$reason=JText::_('COM_NETBASEVM_EXTEND_NEEDS_TO_GET_IN_STATE').' '.$sendStatuses.'.';
								
       		$item['order_date'] = '<td align="center" colspan="'.($delivery_note ? 7 : 4).'">
				<span class="invoice_yes" title="'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_NUMBER_NOT_GENERATED_YET').'::'.$reason.'">
				'.JText::_('COM_NETBASEVM_EXTEND_INVOICE_NUMBER_NOT_GENERATED_YET').'.</span>
			</td>';
       		
       		$item['order_sent'] = '';
       		if ($delivery_note)
       			$item['dn_sent'] = '';
       		$item['order_mail'] = '';
       		if ($delivery_note)
       			$item['dn_mail'] = '';
       		$item['generate_invoice'] = '';
       		if ($delivery_note)
       			$item['generate_dn'] = '';
	        
	    } else {
	        
	    	$defDate = date('d-m-Y',$row->invoiceDate>0 ? $row->invoiceDate : time());
	        $item['order_date'] = '<td>
				<a href="javascript:void(0)" onclick="show_change_date(\''.$row->order_id.'\');">
				'.($row->invoiceDate>0 ? JHTML::_('date',  $row->invoiceDate, JText::_('DATE_FORMAT_LC3')): JText::_('COM_NETBASEVM_EXTEND_CREATE')).'</a>
				<div style="display:none" id="change_order_date_'.$row->order_id.'">
				'.JHTML::calendar($defDate, 'order_date['.$row->order_id.']', 'order_date_'.$row->order_id, '%d-%m-%Y','style="width:70%"').
				'<input  class="input_bg"  style="float:left;padding:2px 5px !important;cursor:pointer;" type="submit" name="update_inv_date['.$row->order_id.']" value="'.JText::_('COM_NETBASEVM_EXTEND_CHANGE').'">
				</div>
			</td>';
		
	        $item['order_sent'] = '<td align="center">
					<img src="'.NbordersHelper::imgSrc('mail_' . ($row->order_mailed ? 'y' : 'n') . '.png').'" />
				</td>';
			if ($delivery_note) 
	        	$item['dn_sent'] = '<td align="center">
						<img src="'.NbordersHelper::imgSrc('mail_' . ($row->dn_mailed ? 'y' : 'n') . '.png').'" />
					</td>';
	        	
	        $item['order_mail'] = '<td align="center">
				'.$mail_link.'<img class="img-send-mail" src="'.NbordersHelper::imgSrc('mail-hover.png').'" /></a>
			</td>';
	        
			if ($delivery_note)
	        	$item['dn_mail'] = '<td align="center">
					'.$mail_dn_link.'<img src="'.NbordersHelper::imgSrc('mail-hover.png').'" /></a>
				</td>';
			        	
	        $item['generate_invoice'] = '<td align="center"'.($row->generated!=false ? ' class="generated"' : '').'>
	            '.$pdf_link.'vf</a>
			</td>';
	        
			if ($delivery_note)
	        	$item['generate_dn'] = '<td align="center"'.($row->generatedDN!=false ? ' class="generated"' : '').'>
	            '.$pdf_dn_link.'<img src="'.NbordersHelper::imgSrc('pdf.png').'" title="'.($row->generatedDN!=false ? JText::_('COM_NETBASEVM_EXTEND_ALREADY_GENERATED') : '').'"/></a>
			</td>';
			
       	}
        
        $results = $this->dispatcher->trigger('onInvoicesListItem', array(&$item, $i, $row, $this));
        foreach ($results as $result)
        	if ($result===false) //false = not display row
        		continue;
        
        //display row
        ?>
        <tr class="<?php echo "row$k"; ?>">
        <?php  echo implode(PHP_EOL, $item); ?>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>

</table>
<div class="pagination_order"><?php echo $this->pagination->getListFooter(); ?></div>
</div>

    <input type="hidden" name="total" id="total" value="<?php echo $n ;?>" />
    <input type="hidden" name="option" value="com_netbasevm_extend" />
    <input type="hidden" name="task" value="" autocomplete="off"/>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="nborders" />
    

</form>

<?php AdminUIHelper::endAdminArea(); ?>