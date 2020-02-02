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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restrict Access');

// Panes
$params1 = array('allowAllClose' => true);
$pane = JPane::getInstance('sliders', $params1);

$info = InvoiceHelper::getComponentInfo();
			
?>

<script type="text/javascript">
/* <![CDATA[ */
function showUpgrade()
{
    window.location="index.php?option=com_vminvoice&controller=upgrade";
}
/* ]]> */
</script>

<div class="col width-60">
	<div class="icons" id="cpanel">
	    
		<div class="config">
			<h2><?php echo JText::_('COM_VMINVOICE_CONFIGURATION'); ?></h2>
	    	<!-- Global Configuration -->
	    	<div class="icon">
	    		<a href="index.php?option=com_vminvoice&controller=config&type=general" title="<?php echo JText::_('COM_VMINVOICE_CONFIGURE_GLOBAL_FUNCTIONALITY'); ?>">
	       		<img src="<?php echo InvoiceHelper::imgSrc('icon-48-config.png')?>" alt="" width="48" height="48" border="0"/>
	       		<span><?php echo JText::_('COM_VMINVOICE_GLOBAL_CONFIGURATION'); ?></span>
	       	</a>
	       </div>
	    	 <!-- Invoice Configuration -->
	    	 <div class="icon">
	    		<a href="index.php?option=com_vminvoice&controller=config&type=invoice" title="<?php echo JText::_('COM_VMINVOICE_CONFIGURE_INVOICE_FUNCTIONALITY'); ?>">
	       		<img src="<?php echo InvoiceHelper::imgSrc('icon-48-config-invoices.png')?>" alt="" width="48" height="48" border="0"/>
	       		<span><?php echo JText::_('COM_VMINVOICE_INVOICE_CONFIGURATION'); ?></span>
	       	</a>
	       </div>
	       
	    	 <!-- Delivery Note Configuration -->
	    	 <div class="icon">
	    		<a href="index.php?option=com_vminvoice&controller=config&type=dn" title="<?php echo JText::_('COM_VMINVOICE_CONFIGURE_DELIVERY_NOTE_FUNCTIONALITY'); ?>">
	       		<img src="<?php echo InvoiceHelper::imgSrc('icon-48-config-dn.png')?>" alt="" width="48" height="48" border="0"/>
	       		<span><?php echo JText::_('COM_VMINVOICE_DELIVERY_NOTE_CONFIGURATION'); ?></span>
	       	</a>
	       </div>
	       
	       
	       <!--  Extensions Management -->
	       <div class="icon"> <?php // controller: vminvoice_additional_field ?>
	    		<a href="index.php?option=com_vminvoice&controller=fields" title="<?php echo JText::_('COM_VMINVOICE_FIELDS'); ?>">
	       		<img src="<?php echo InvoiceHelper::imgSrc('icon-48-fields.png')?>" alt="" width="48" height="48" border="0"/>
	       		<span><?php echo JText::_('COM_VMINVOICE_FIELDS'); ?></span>
	       	</a>
	       </div> 
	       <!--  Upgrade page -->
	       <div class="icon"> <?php // controller: vminvoice_additional_field ?>
	    		<a href="index.php?option=com_vminvoice&controller=upgrade" title="<?php echo JText::_('COM_VMINVOICE_UPGRADE'); ?>">
	       		<img src="<?php echo InvoiceHelper::imgSrc('icon-48-vmupdate.png')?>" alt="" width="48" height="48" border="0"/>
	       		<span><?php echo JText::_('COM_VMINVOICE_UPGRADE'); ?></span>
	       	</a>
	       </div>
	
	      	<div style="clear: both;"></div>
	    </div>
	                  
		<div class="invoices">
	   		<h2><?php echo JText::_('COM_VMINVOICE_INVOICE_ORDER_MANAGEMENT'); ?></h2>	       
	        <!--  Invoice list -->
	      	<div class="icon"> <?php // controller: vminvoice_list ?>
	       	<a href="index.php?option=com_vminvoice&controller=invoices" title="<?php echo JText::_('COM_VMINVOICE_INVOICES_/_ORDERS'); ?>">
	      			<img src="<?php echo InvoiceHelper::imgSrc('icon-48-invoices.png')?>" alt="" width="48" height="48" border="0"/>
	      			<span><?php echo JText::_('COM_VMINVOICE_INVOICE_ORDER_MANAGEMENT'); ?></span>
	      		</a>
	      	</div>	       
	      	
	        <!--  Create new order -->
	      	<div class="icon"> <?php // controller: vminvoice_list ?>
	       	<a href="index.php?option=com_vminvoice&controller=invoices&task=addOrder" title="<?php echo JText::_('COM_VMINVOICE_NEW_ORDER'); ?>">
	      			<img src="<?php echo InvoiceHelper::imgSrc('icon-48-addorder.png')?>" alt="" width="48" height="48" border="0"/>
	      			<span><?php echo JText::_('COM_VMINVOICE_CREATE_NEW_ORDER'); ?></span>
	      		</a>
	      	</div>
	      	

	      	<div style="clear: both;"></div>
	   	</div>
	   	
	   	<div class="help">
	   		<h2><?php echo JText::_('COM_VMINVOICE_HELP_AND_SUPPORT'); ?></h2>
	   		<!--  Documentation -->
	   		<div class="icon">
				<a href="http://www.artio.net/vm-invoice/documentation" title="<?php echo JText::_('COM_VMINVOICE_VIEW_VM_INVOICE_DOCUMENTATION'); ?>" target="_blank">
	        		<img src="<?php echo InvoiceHelper::imgSrc('icon-48-docs.png')?>" alt="" width="48" height="48" align="middle" border="0"/>
	      			<span><?php echo JText::_('COM_VMINVOICE_DOCUMENTATION'); ?></span>
	      		</a>
	      	</div>
	      	<!--  Changelog -->
	      	<div class="icon">
	   			<a href="http://www.artio.net/vm-invoice/vm-invoices-changelog" title="<?php echo JText::_('COM_VMINVOICE_VIEW_VM_INVOICE_CHANGELOG'); ?>" target="_blank">
	        		<img src="<?php echo InvoiceHelper::imgSrc('icon-48-info.png')?>" alt="" width="48" height="48" align="middle" border="0"/>
	      			<span><?php echo JText::_('COM_VMINVOICE_CHANGELOG'); ?></span>
	      		</a>
	      	</div>
	      	<!--  Support -->
	      	<div class="icon">
	   			<a href="index.php?option=com_vminvoice&amp;controller=info&amp;task=help" title="<?php echo JText::_('COM_VMINVOICE_NEED_HELP_WITH_VM_INVOICE'); ?>">
	         		<img src="<?php echo InvoiceHelper::imgSrc('icon-48-help.png')?>" alt="" width="48" height="48" align="middle" border="0"/>
	      			<span><?php echo JText::_('COM_VMINVOICE_SUPPORT'); ?></span>
	      		</a>
	      	</div>	
	      	
	      	<div style="clear: both;"></div>
	   	</div>
	
    </div>
</div>

<div class="col width-40">

<fieldset class="adminform">
<legend>ARTIO VM Invoice</legend>
	
	<table class="admintable">
	   <tr>
			<td class="key"></td>
			<td>
	      		<a href="http://www.artio.net/virtuemart-tools/vm-invoice-generator" target="_blank">
	          		<img src="<?php echo InvoiceHelper::imgSrc('vminvoice.png')?>" align="middle" alt="VM Invoice logo" style="border: none; margin: 8px;" />
	        	</a>
			</td>
		</tr>
	   <tr>
	      <td class="key" width="120"></td>
	      <td><a href="<?php echo 'http://'.$info['authorUrl']; ?>" target="_blank">ARTIO</a> VM Invoices</td>
	   </tr>	
	   <tr>
	      <td class="key"><?php echo JText::_('COM_VMINVOICE_VERSION'); ?>:</td>
	      <td><?php echo $info['version']; ?></td>
	   </tr>
	   <tr>
	      <td class="key"><?php echo JText::_('COM_VMINVOICE_NEWEST_VERSION'); ?>:</td>
	      <td><?php echo $this->newestVersion; ?></td>
	   </tr>
	   <tr>
	      <td class="key"><?php echo JText::_('COM_VMINVOICE_DATE'); ?>:</td>
	      <td><?php echo $info['creationDate']; ?></td>
	   </tr>
	   <tr>
	      <td class="key" valign="top"><?php echo JText::_('COM_VMINVOICE_COPYRIGHT'); ?>:</td>
	      <td><?php echo $info['copyright']; ?></td>
	   </tr>
	   <tr>
	      <td class="key"><?php echo JText::_('COM_VMINVOICE_AUTHOR'); ?>:</td>
	      <td><a href="<?php echo 'http://'.$info['authorUrl']; ?>" target="_blank"><?php echo $info['author']; ?></a>,
	      <a href="mailto:<?php echo $info['authorEmail']; ?>"><?php echo $info['authorEmail']; ?></a></td>
	   </tr>
	   <tr>
	      <td class="key" valign="top"><?php echo JText::_('COM_VMINVOICE_DESCRIPTION'); ?>:</td>
	      <td><?php echo $info['description']; ?></td>
	   </tr>
	   <tr>
	      <td class="key"><?php echo JText::_('COM_VMINVOICE_LICENSE'); ?>:</td>
	      <td><?php echo $info['license']; ?></td>
	   </tr>
	</table>
</fieldset>
	<?php
	//echo $pane->endPanel();
	//echo $pane->endPane();
    ?>

</div>	      	
