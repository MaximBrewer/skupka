<?php
/*
*
* @copyright Copyright (C) 2007 - 2014 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

	defined( '_JEXEC' ) or die( 'Restricted access' );
	if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
?>
<fieldset>
<h3><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_LBL') ?></h3>
<div class="container-fluid roundedwrap" style="">


			<?php
				$print_url = juri::root().'index.php?option=com_virtuemart&view=invoice&layout=invoice&tmpl=component&virtuemart_order_id=' . $this->orderbt->virtuemart_order_id . '&order_number=' .$this->orderbt->order_number. '&order_pass=' .$this->orderbt->order_pass;
				$print_link = "<a title=\"".JText::_('COM_VIRTUEMART_PRINT')."\" href=\"javascript:void window.open('$print_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"  >";
				$print_link .=   $this->orderbt->order_number . ' </a>';
			?>
			
			
			<div class="row-fluid ">
			<div class="col-md-6 span6">
			  <strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_NUMBER') ?></strong>
			 </div>
			<div class="col-md-6 span6">
			  <?php echo  $print_link;?>
			 </div>
			</div>
			
			
			<div class="row-fluid">
			<div class="col-md-6 span6">
			 <strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_PASS') ?></strong>
			</div>
			<div class="col-md-6 span6">
				<?php echo  $this->orderbt->order_pass;?>
			</div>
			</div>
			
			
			<div class="row-fluid">
			 <div class="col-md-6 span6">
			<strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_DATE') ?></strong>
			 </div>
			 <div class="col-md-6 span6">
				<?php  echo vmJsApi::date($this->orderbt->created_on,'LC2',true); ?>
			 </div>
			</div>
			
			<div class="row-fluid">
			 <div class="col-md-6 span6">
			<strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_STATUS') ?></strong>
			</div>
			 <div class="col-md-6 span6">
				<?php echo $this->orderstatuslist[$this->orderbt->order_status]; ?>
			</div>
			</div>
			
		<div class="row-fluid">
			  <div class="col-md-6 span6">
				<strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_NAME') ?></strong>
			   </div>
			   <div class="col-md-6 span6">
				<?php
						$username=$this->orderbt->company ? $this->orderbt->company." ":"";
						$username.=$this->orderbt->first_name." ".$this->orderbt->last_name." ";
					if ($this->orderbt->virtuemart_user_id) {
						$userlink = JROUTE::_ ('index.php?option=com_virtuemart&view=user&task=edit&virtuemart_user_id[]=' . $this->orderbt->virtuemart_user_id);
						echo JHTML::_ ('link', JRoute::_ ($userlink), $username, array('title' => JText::_ ('COM_VIRTUEMART_ORDER_EDIT_USER') . ' ' . $username));
					} else {
						echo $this->orderbt->first_name.' '.$this->orderbt->last_name;
					}
					?>
				</div>
		 </div>
		  
			<div class="row-fluid">
			 <div class="col-md-6 span6">
			  <strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_IPADDRESS') ?></strong>
			 </div>
			    <div class="col-md-6 span6">
				<?php echo $this->orderbt->ip_address; ?>
				</div>
			</div>
			<?php
			if ($this->orderbt->coupon_code) { ?>
			
			<div class="row-fluid">
			 <div class="col-md-6 span6">
			
				<strong><?php echo JText::_('COM_VIRTUEMART_COUPON_CODE') ?></strong>
			 </div>
			 <div class="col-md-6 span6">
				<?php echo $this->orderbt->coupon_code; ?>
			  </div>
			</div>
			<?php } ?>
			<?php
			if ($this->orderbt->invoiceNumber and !shopFunctions::InvoiceNumberReserved($this->orderbt->invoiceNumber) ) {
				$invoice_url = juri::root().'index.php?option=com_virtuemart&view=invoice&layout=invoice&format=pdf&tmpl=component&virtuemart_order_id=' . $this->orderbt->virtuemart_order_id . '&order_number=' .$this->orderbt->order_number. '&order_pass=' .$this->orderbt->order_pass;
				$invoice_link = "<a title=\"".JText::_('COM_VIRTUEMART_INVOICE_PRINT')."\"  href=\"javascript:void window.open('$invoice_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"  >";
				$invoice_link .=   $this->orderbt->invoiceNumber . '</a>';?>
			<div class="row-fluid">
			 <div class="col-md-6 span6">
			 
				<strong><?php echo JText::_('COM_VIRTUEMART_INVOICE') ?></strong>
			  </div>
			  <div class="col-md-6 span6">
				<?php echo $invoice_link; ?>
			  </div>
			  
			</div>
			<?php } ?>
</div>
<div class="container-fluid roundedwrap" >			
		
		<div class="row-fluid headerrow">
			<div class="col-md-3 span3">
			<?php echo JText::_('COM_VIRTUEMART_ORDER_HISTORY_DATE_ADDED') ?>
		    </div>
			<div class="col-md-3 span3">
					<?php echo JText::_('COM_VIRTUEMART_ORDER_HISTORY_CUSTOMER_NOTIFIED') ?>
			</div>
			<div class="col-md-3 span3">
			<?php echo JText::_('COM_VIRTUEMART_ORDER_LIST_STATUS') ?>
			</div>
			<div class="col-md-3 span3">
			<?php echo JText::_('COM_VIRTUEMART_COMMENT') ?>
			</div>
		</div>
			<?php
			foreach ($this->orderdetails['history'] as $this->orderbt_event ) {
				?><div class="row-fluid"><?php
				?> <div class="col-md-3 span3"> <?php echo vmJsApi::date($this->orderbt_event->created_on,'LC2',true) ."\n";
				?></div><?php
				if ($this->orderbt_event->customer_notified == 1) {
					echo '<div class="col-md-3 span3">'.JText::_('COM_VIRTUEMART_YES').'</div>';
				}
				else {
					echo '<div class="col-md-3 span3">'.JText::_('COM_VIRTUEMART_NO').'</div>';
				}
				if(!isset($this->orderstatuslist[$this->orderbt_event->order_status_code])){
					if(empty($this->orderbt_event->order_status_code)){
						$this->orderbt_event->order_status_code = 'unknown';
					}
					$_orderStatusList[$this->orderbt_event->order_status_code] = JText::_('COM_VIRTUEMART_UNKNOWN_ORDER_STATUS');
				}

				echo '<div class="col-md-3 span3">'.$this->orderstatuslist[$this->orderbt_event->order_status_code].'</div>';
				echo '<div class="col-md-3 span3">'.$this->orderbt_event->comments."</div>\n";
				?></div><?php
			}
			?>
			<div class="row-fluid">
				<div class="col-md-12 span12">
				<a href="#" class="show_element"><span class="vmicon vmicon-16-editadd"></span><?php echo JText::_('COM_VIRTUEMART_ORDER_UPDATE_STATUS') ?></a>
				<div style="display: none; background: white; z-index: 100;"
					class="element-hidden vm-absolute"
					id="updateOrderStatus"><?php //echo $this->loadTemplate('editstatus'); ?>
				</div>
				</div>
			</div>
			<div class="row-fluid">
			<?php
				// Load additional plugins
				$_dispatcher = JDispatcher::getInstance();
				$_returnValues1 = $_dispatcher->trigger('plgVmOnUpdateOrderBEPayment',array($this->orderID));
				$_returnValues2 = $_dispatcher->trigger('plgVmOnUpdateOrderBEShipment',array(  $this->orderID));
				$_returnValues = array_merge($_returnValues1, $_returnValues2);
				$_plg = '';
				foreach ($_returnValues as $_returnValue) {
					if ($_returnValue !== null) {
						$_plg .= ('	<div class="col-md-12 span12">' . $_returnValue . "</div>\n");
					}
				}
				if ($_plg !== '') {
					echo $_plg; 
				
				}
			?>
			</div>

</div>		
</fieldset>		
	

