<?php
/**
 * @package		RuposTel.com
 * @copyright	Copyright (C) 2005 - 2011 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
 
defined('_JEXEC') or die;
JHTMLOPC::stylesheet('bootstrap.min.css', 'components/com_onepage/themes/extra/bootstrap/', array());
JToolBarHelper::Title(JText::_('COM_ONEPAGE_OPC_FILTERS_FORSHIPPING_ANDPAYMENT'), 'generic.png');
JToolBarHelper::apply();
jimport ('joomla.html.html.bootstrap');
jimport('joomla.html.pane');
jimport('joomla.utilities.utility');
JHTMLOPC::script('opcbe.js', 'administrator/components/com_onepage/assets/js/', false);
	
?>
<div id="vmMainPageOPC"> 
<form action="<?php echo JURI::base(); ?>index.php?option=com_onepage&amp;controller=filters" method="post" name="adminForm" id="adminForm">

<input type="hidden" name="option" value="com_onepage" />
<input type="hidden" name="view" value="filters" />
<input type="hidden" name="task" id="task" value="apply" />

<h2><?php echo JText::_('COM_ONEPAGE_OPC_FILTERS_SHIPPINGMETHODS'); ?></h2>

<?php
foreach ($this->sids as $ship) {
	$sid = (int)$ship['virtuemart_shipmentmethod_id']; 
	?>
	<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><?php echo $ship['name']; ?></h3>
  </div>
   <div class="panel-body">
	  <fieldset>
	    <table class="row">
		  
		  <tr><th colspan="3">
		  <legend><?php echo JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER'); ?></legend>
		  </th></tr>
		  
		  <tr><td>
		  <label for="catfilter1"><?php echo JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_1'); ?></label>
		  </td>
		  <td>
		  <input type="text" name="catfilter1[<?php echo $ship['virtuemart_shipmentmethod_id']; ?>]" id="catfilter1" value="<?php echo $this->model->getConfigTxt('catfilter1', $sid); ?>" placeholder="<?php echo $this->escape(JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_1_DESC')); ?>" /><br />
		  </td>
		  <td>
		  <?php echo $this->escape(JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_1_DESC')); ?>
		  </td>
		  </tr>
		
		
		<tr><th colspan="3">
		 <legend><?php echo JText::_('COM_ONEPAGE_OPC_FILTERS_PRODUCT_FILTER'); ?></legend>
		  </th></tr>
		
		
		  <tr><td>
		  
		  <label for="catfilterP1"><?php echo JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_2'); ?></label>
		  </td>
		  <td>
		  <input type="text" name="catfilterP1[<?php echo $ship['virtuemart_shipmentmethod_id']; ?>]" id="catfilterP1" value="<?php echo $this->model->getConfigTxt('catfilterP1', $sid); ?>" placeholder="<?php echo $this->escape(JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_2_DESC')); ?>" /><br />
		  </td>
		  <td>
		  <?php echo $this->escape(JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_2_DESC')); ?>
		  </td>
		  </tr>
		  
		  </table>
		  
		  
		
		
	  
	  </fieldset>
	
	</div>
	</div>
	
	<?php
}

?>

<h2><?php echo JText::_('COM_ONEPAGE_OPC_FILTERS_PAYMENTMETHODS'); ?></h2>
<?php

foreach ($this->pms as $ship) {
	$sid = (int)$ship['virtuemart_paymentmethod_id']; 
	?>
	<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><?php echo $ship['name']; ?></h3>
  </div>
   <div class="panel-body">
	  <fieldset><legend><?php echo JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER'); ?></legend>
	    <table class="row">
		<tr><td>
		  <label for="catfilterp"><?php echo JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_1'); ?>
		   </label>
		   </td>
		   <td>
		  <input type="text" name="catfilterp[<?php echo $ship['virtuemart_paymentmethod_id']; ?>]" id="catfilterp" value="<?php echo $this->model->getConfigTxt('catfilterp', $sid); ?>" placeholder="<?php echo $this->escape(JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_1_DESC')); ?>" />
		  </td>
		  <td>
		  <?php echo $this->escape(JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_1_DESC')); ?>
		  </td>
		  </tr>
		 
		   <tr><td>
		  <label for="catfilterPS1"><?php echo JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_2'); ?></label>
		  </td>
		  <td>
		  <input type="text" name="catfilterPS1[<?php echo $ship['virtuemart_paymentmethod_id']; ?>]" id="catfilterPS1" value="<?php echo $this->model->getConfigTxt('catfilterPS1', $sid); ?>" placeholder="<?php echo $this->escape(JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_2_DESC')); ?>" /><br />
		  </td>
		  <td>
		  <?php echo $this->escape(JText::_('COM_ONEPAGE_OPC_FILTERS_CATEGORY_FILTER_2_DESC')); ?>
		  </td>
		  </tr>
		
		</table>		
	  
	  </fieldset>
	
	</div>
	</div>
	
	<?php
}


?>
</form>
</div>