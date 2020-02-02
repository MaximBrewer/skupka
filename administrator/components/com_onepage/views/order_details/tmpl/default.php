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

	

echo $this->loadTemplate('includes');
$document = JFactory::getDocument();
?>
<div id="vmMainPageOPC" class="span12 row-fluid">

<form name='adminForm' id="adminForm">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="order_details" />
		<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
		
		<input type="hidden" id="order_number" name="order_number" value="<?php echo $this->order['details']['BT']->order_number; ?>" />
	  <input type="hidden" id="general_param" name="general_param" value="0" />
	  <input type="hidden" id="task" name="task" value="save" />
	  <input type="hidden" name="view" value="order_details" />
	  <input type="hidden" name="contoller" value="order_details" />
	  <input type="hidden" name="option" value="com_onepage" />
	  <input type="hidden" id="general_param1" name="general_param1" value="0" />
	  <input type="hidden" id="cmd" name="cmd" value="" />
	  <input type="hidden" id="localid" name="localid" value="<?php echo $this->order['details']['BT']->virtuemart_order_id; ?>" />
	  <input type="hidden" id="orderid" name="orderid" value="<?php echo $this->order['details']['BT']->virtuemart_order_id; ?>" />
	  <input type="hidden" id="fieldid" name="fieldid" value="<?php echo $this->order['details']['BT']->virtuemart_order_id; ?>" />
		
		<?php echo JHTML::_( 'form.token' ); 
    $order_id = 	$this->orderID;
	echo '<input type="hidden" id="scrolly" name="scrolly" value="'.JRequest::getVar('scrolly',0).'" />';
	echo '<input type="hidden" id="op_curtab" name="op_curtab" value="'.JRequest::getVar('op_curtab', '').'" />';

		
		?>
</form>
<div >
<div id="opc_order_details">

<?php
echo $this->loadTemplate('header');
$pane = OPCPane::getInstance('tabs', array('active'=>'panel01id', 'startOffset'=>0));
        echo $pane->startPane('order_general');
        
		echo $pane->startPanel(JText::_('COM_VIRTUEMART_DETAILS'), 'panel01id');
		echo $this->loadTemplate('details');
?>


<?php
echo $pane->endPanel();

echo $pane->startPanel(JText::_('COM_ONEPAGE_EXPORT_ORDEREXPORTTAB'), 'order_e2');
echo $this->loadTemplate('export');
echo $pane->endPanel();

echo $pane->startPanel(JText::_('COM_ONEPAGE_LOG'), 'panel1');
?><div id="opc_response">&nbsp;</div><?php
echo $pane->endPanel();
 
echo $pane->endPane(); 

?>

<form action="index.php" method="post" name="orderForm" id="orderForm"><!-- Update order head form -->
<div class="linewrapper">
<div class="row-fluid">
	<?php if ($this->orderbt->customer_note || true) { ?>
	<div class="col-md-6 span6 ">
		
		<div class="row-fluid">
			<div class="col-md-6 span6 headerrow">
			<?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_CUSTOMER_NOTE') ?>
			</div>
			<div class="col-md-6 span6">
					<textarea rows="4" cols="50" name="customer_note"><?php echo $this->orderbt->customer_note; ?></textarea>
				
				
			</div>
		</div>
		</div>
		<div class="col-md-6 span6">
		
					<div class="row-fluid">

					<div class="col-md-12 span12 headerrow">
				<?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_SHIPMENT') ?>
					
					</div>
					
					</div>
					<div class="row-fluid">
					<div class="col-md-6 span6">
					<?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL') ?>
					</div>
					
						<?php
						$model = VmModel::getModel('paymentmethod');
						$payments = $model->getPayments();
						$model = VmModel::getModel('shipmentmethod');
						$shipments = $model->getShipments();
						?>
						<div class="col-md-6 span6">
							<input  type="hidden" size="10" name="virtuemart_paymentmethod_id" value="<?php echo $this->orderbt->virtuemart_paymentmethod_id; ?>"/>
							<!--
							<? echo VmHTML::select("virtuemart_paymentmethod_id", $payments, $this->orderbt->virtuemart_paymentmethod_id, '', "virtuemart_paymentmethod_id", "payment_name"); ?>
							<span id="delete_old_payment" style="display: none;"><br />
								<input id="delete_old_payment" type="checkbox" name="delete_old_payment" value="1" /> <label class='' for="" title="<?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_DELETE_DESC'); ?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_DELETE'); ?></label>
							</span>
							-->
							<?php
							foreach($payments as $payment) {
								if($payment->virtuemart_paymentmethod_id == $this->orderbt->virtuemart_paymentmethod_id) echo $payment->payment_name;
							}
							?>
						</div>
				</div>
					<div class="row-fluid">
					
						<div class="col-md-6 span6"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPMENT_LBL') ?>
						</div>
						<div class="col-md-6 span6">
							<input type="hidden" size="10" name="virtuemart_shipmentmethod_id" value="<?php echo $this->orderbt->virtuemart_shipmentmethod_id; ?>"/>
							<!--
							<? echo VmHTML::select("virtuemart_shipmentmethod_id", $shipments, $this->orderbt->virtuemart_shipmentmethod_id, '', "virtuemart_shipmentmethod_id", "shipment_name"); ?>
							<span id="delete_old_shipment" style="display: none;"><br />
								<input id="delete_old_shipment" type="checkbox" name="delete_old_shipment" value="1" /> <label class='' for=""><?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE'); ?></label>
							</span>
							-->
							<?php
							foreach($shipments as $shipment) {
								if($shipment->virtuemart_shipmentmethod_id == $this->orderbt->virtuemart_shipmentmethod_id) echo $shipment->shipment_name;
							}
							?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="col-md-6 span6">
						<?php echo JText::_('COM_VIRTUEMART_DELIVERY_DATE') ?>
						</div>
						<div class="col-md-6 span6">
						<input type="text" maxlength="190" class="required" value="<?php echo $this->orderbt->delivery_date; ?>" size="30" name="delivery_date" id="delivery_date_field">
						</div>
					</div>
					
		  </div>
	<?php } ?>
 </div>
</div>
&nbsp;
<table width="100%">
	<tr>
		<td width="50%" valign="top">
		<table class="adminlist" width="100%">
			<thead>
				<tr>
					<th  style="text-align: center;" colspan="2"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_BILL_TO_LBL') ?></th>
				</tr>
			</thead>

			<?php
			foreach ($this->userfields['fields'] as $_field ) {

				echo '		<tr>'."\n";
				echo '			<td class="key">'."\n";
				echo '				<label for="'.$_field['name'].'_field">'."\n";
				echo '					'.$_field['title'] . ($_field['required']?' *': '')."\n";
				echo '				</label>'."\n";
				echo '			</td>'."\n";
				echo '			<td>'."\n";
				echo '				'.$_field['formcode']."\n";
				echo '			</td>'."\n";
				echo '		</tr>'."\n"; //*/
			/*	$fn = $_field['name'];
				$fv = $_field['value'];
				$ft = $_field['title'];
				echo '		<tr>'."\n";
				echo '			<td class="key">'."\n";
				echo '				'.$ft."\n";
				echo '			</td>'."\n";
				echo '			<td>'."\n";
				echo "				<input name='BT_$fn' id='$fn' value='$fv' size='50'>\n";
				echo '			</td>'."\n";
				echo '		</tr>'."\n";*/
			}
			?>

		</table>
		</td>
		<td width="50%" valign="top">
		<table class="adminlist" width="100%">
			<thead>
				<tr>
					<th   style="text-align: center;" colspan="2"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIP_TO_LBL') ?></th>
				</tr>
			</thead>

			<?php
			foreach ($this->shipmentfields['fields'] as $_field ) {
				echo '		<tr>'."\n";
				echo '			<td class="key">'."\n";
				echo '				<label for="'.$_field['name'].'_field">'."\n";
				echo '					'.$_field['title'] . ($_field['required']?' *': '')."\n";
				echo '				</label>'."\n";
				echo '			</td>'."\n";
				echo '			<td>'."\n";
				echo '				'.$_field['formcode']."\n";
				echo '			</td>'."\n";
				echo '		</tr>'."\n";
			}
			?>

		</table>
		</td>
	</tr>
</table>
		<input type="hidden" name="task" value="updateOrderHead" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
		<input type="hidden" name="old_virtuemart_paymentmethod_id" value="<?php echo $this->orderbt->virtuemart_paymentmethod_id; ?>" />
		<input type="hidden" name="old_virtuemart_shipmentmethod_id" value="<?php echo $this->orderbt->virtuemart_shipmentmethod_id; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
echo $this->loadTemplate('cart'); 

echo $this->loadTemplate('plugins'); 
?>
   <div id="debug_window" ondblclick="javascript:this.style.display='none';" style="position: fixed; bottom: 0px; right: 0px; width: 20%; overflow:scroll; overflow-x: none; height: 30%; display: none; color: black; font-size: 10px; text-align: right; background-color: grey; filter: alpha(opacity=40); opacity: 0.4; ">Hello, close me with double click<br />
   </div>


<?php
//AdminUIHelper::imitateTabs('end');
//AdminUIHelper::endAdminArea(); 
?>


<?php
$js = '
//<![CDATA[

var COM_VIRTUEMART_ORDER_DELETE_ITEM_JS = "'.addslashes( JText::_('COM_VIRTUEMART_ORDER_DELETE_ITEM_JS') ).'";
var editingItem = 0;


		          		function gotocontact( id ) {
						var form = document.adminForm;
						form.target = "_parent";
						form.contact_id.value = id;
						form.option.value = "com_users";
						submitform( "contact" );
						}
						var sendXml = "sendXml";
		          		var opTimer = null;
		          		var opStop = false;
		          		var opTemplates = [];
						var focusedE = null;
						var timeOut = null;
						var tmpElement = null;
						var scrollY = 0;
						var lasttab = 0;
						var deb = document.getElementById("debug_window");	
						function submitbutton(task, formId)
						{
						 if (formId == null) formId = "adminForm";
	 					 var d = document.getElementById("task");
	 					 d.value = task;
	 					 formm = document.getElementById(formId);
	 					 if (formm != null)
	 					 {
	 					  formm.submit();
	 					 }
	 					 
	 				     return true;
						}

		          		function changeStateList2() { 
						  var selected_country = null;
						  var country = document.getElementById("bt_country");
							  for (var i=0; i<country.length; i++)
				 				if (country[i].selected)
					selected_country = country[i].value;
			  		changeDynaList("bt_state",states, selected_country, originalPos, originalOrder);
			  
							} 
							/*var xmlhttp2 = null;*/ 
							var op_url = "'.$this->ehelper->getUrl().'/administrator/index.php";
							var op_params = "&option=com_onepage&nosef=1&view=order_details&task=ajax&orderid='.$order_id.'&localid='.$order_id.'&ajax=yes&order_number='.$this->order['details']['BT']->order_number.'"; '."\n".'
							var op_localid = "'.$order_id.'"; 
							var multiOrders = false; ';
							if (!empty($runTimer)) $js .= ' 
							 opStop = true;
 							 opTimer=setTimeout("op_timer()", 2000);
							';
						$scrollY = JRequest::getVar('scrolly', 0);
						
						
							
	
    $js .= 'if(window.addEventListener){ // Mozilla, Netscape, Firefox' . "\n";
    $js .= '    window.addEventListener("load", function(){ op_init(); }, false);' . "\n";
    $js .= '} else { // IE' . "\n";
    $js .= '    window.attachEvent("onload", function(){ op_init(); });' . "\n";
    $js .= '}';
 	$js .= '
						
							//]]>
							';
	$doc = JFactory::getDocument();
	$doc->addScriptDeclaration( $js);
?>
</div>
</div>
</div>
<?php
  /*VM2 end */