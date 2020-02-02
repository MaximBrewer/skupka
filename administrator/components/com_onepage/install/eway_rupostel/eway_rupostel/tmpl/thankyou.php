<?php
/**
 *
 * @author stAn, RuposTel.com
 * @version $Id: eway_rupostel.php 
 * @package eWay Payment Plugin
 * @subpackage payment
 * @copyright Copyright (C) RuposTel.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * eWay Payment is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * Based on Authorize.net plugin by Virtuemart.net team
 *
 * http://rupostel.com
 */
defined('_JEXEC') or die('Restricted access');


//		$html .= $this->renderByLayout('thankyou', array('method' => $this->_currentMethod, 'eway'=>$eway, 'vmid'=>$pm, 'totalInPaymentCurrencyCents'=>$totalInPaymentCurrency, 'order'=>$order));
// the variables are accessible with: 
$viewData['responsemsg'] = str_replace('00, ', '', $viewData['responsemsg']); ?>
<?php echo JText::_('PLG_VMPAYMENT_EWAY_RUPOSTEL_SUCCESS'); ?> <?php $viewData['responsemsg'];  ?>
<br />
<?php echo JText::_('PLG_VMPAYMENT_EWAY_RUPOSTEL_PAYMENT_IS_CONFIRMED_AND_PROCESSED'); ?>

<?php 
$cents = $viewData['totalInPaymentCurrencyCents']; 
$cents = $cents/100; 
$payment_total = number_format($cents, 2, '.', ' '); 
$total = $viewData['order']['details']['BT']->order_total; 


// this page is always shown only on cofirmed payments
$success = true; 
$payment_name = $viewData["payment_name"];
$payment = $viewData["payment"];
$order = $viewData["order"];

?>
<br />
<table>
	<tr>
    	<td><?php echo JText::_('VMPAYMENT_PAYPAL_API_PAYMENT_NAME'); ?></td>
        <td><?php echo $payment_name; ?></td>
    </tr>

	<tr>
    	<td><?php echo JText::_('COM_VIRTUEMART_ORDER_NUMBER'); ?></td>
        <td><?php echo $order['details']['BT']->order_number;; ?></td>
    </tr>
	<?php { ?>
	<tr>
	<?php
	//$html .= $this->getHtmlRowBE('COM_VIRTUEMART_TOTAL', $payment->payment_order_total . " " . shopFunctions::getCurrencyByID($payment->payment_currency, 'currency_code_3'));
	?>
    	<td><?php 
		if ($order['details']['BT']->order_total  != $payment_total)
		echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL_PAYMENT'); 
		else
		echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL');
		?></td>
        <td><?php echo $payment_total; ?>AUD
	
		</td>
    </tr>
		<?php 
		if ($order['details']['BT']->order_total  != $payment_total)
		{
		?>
		<tr><td><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL'); ?></td>
		<td>
		<?php 
		echo $viewData['currencyDisplay']->priceDisplay ($order['details']['BT']->order_total);		
		
		?></td>
		</tr>
	
    <?php 
		}
	}  ?>

</table>
<?php if ($success) { ?>
	<br />
	<a class="vm-button-correct" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=orders&layout=details&order_number='.$viewData["order"]['details']['BT']->order_number.'&order_pass='.$viewData["order"]['details']['BT']->order_pass, false)?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_VIEW_ORDER'); ?></a>
<?php } ?>

