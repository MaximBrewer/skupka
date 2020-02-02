<?php  defined ('_JEXEC') or die('Restricted access');

/**
 * pickup or delivery plugin
 * license - commercial
 * @author RuposTel.com
 *
 */
 
$method = $viewData['method']; 
$isselected = $viewData['isselected']; 
$button_checkbox_ed = $viewData['button_checkbox_ed']; 
$cal = $viewData['cal']; 
$htmlselect = $viewData['htmlselect']; 
$pfdisabled = $viewData['pfdisabled']; 
$button_checkbox_uned = $viewData['button_checkbox_uned']; 
$cond = $viewData['cond']; 
$inactive = $viewData['inactive']; 
$cal2 = $viewData['cal2']; 
$selectroutes = $viewData['selectroutes']; 
$htmldeliveryslots = $viewData['htmldeliveryslots']; 
$notcondhtml = $viewData['notcondhtml']; 
$pickup_inlinejs = $viewData['pickup_inlinejs']; 
$freeinlinejs = $viewData['freeinlinejs']; 

?>
			  <div class="pf_shipping customtemplate">
			  <div class="pf_pickup">
			  <div class="opc_heading"><button class="pickup_checkbox <?php echo $button_checkbox_ed; ?>" name="pickup_checkbox" <?php echo $pickup_inlinejs; ?> id="pickup_checkbox"><div>&nbsp;</div><span class="opc_title"><?php echo JText::_($method->pickup_label); ?></span></button></div>
				<div class="opc_inside">
				<div>
				  <div class="field_wrapper">
				    <div class="formLabel">
					
				  
					
					
					
					  <label for="pickup_date" class="<?php echo $isselected; ?>" id="p_item1"><?php echo JText::_('COM_VIRTUEMART_DATE'); ?>:</label>
					</div>
				    <div class="formFieldShipping">
					<?php echo $cal; ?>
					</div>
					</div>
					 
					 <?php
					 if (empty($method->disable_pickup_time))
					{
						?>
					
					<div class="field_wrapper">
					<div class="formLabel">
					  <label for="pickup_date" class="<?php echo $isselected; ?>" id="p_item2"><?php echo JText::_('COM_VIRTUEMART_TIME'); ?>:</label>
					</div>
				    <div class="formFieldShipping">
					<?php echo $htmlselect; ?>
					</div>
					</div>
					
					
					
					
					
					<?php } ?>
					
					
					</div>
				</div>
			  
			  </div>
			  
			 
			  
			  <div class="pf_free">
			  <div class="opc_heading"><button <?php echo $pfdisabled; ?> class="pickup_checkbox <?php echo $button_checkbox_uned; ?>" name="free_checkbox" <?php echo $freeinlinejs; ?> id="free_checkbox"><div>&nbsp;</div><span class="opc_title"><?php echo JText::_($method->free_label); ?></span></button></div>
				<div class="opc_inside">
				<div>
				 <?php if ($cond) { ?>
				 
				 <?php if (!empty($method->routes)) { ?>
				<div class="field_wrapper pf_field_wrapper">					
				  <div class="formLabel">					  
				  <label for="free_date" id="r_item" class="<?php echo $inactive; ?>"><?php echo JText::_($method->route_label); ?>:</label>	
				  </div>
				  <div class="formFieldShipping pf_formField"> 
						<?php echo $selectroutes; ?>
				
				  </div>
				</div>
				<?php } ?>
				 
				 
				<div class="field_wrapper">
				    <div class="formLabel">
					  <label for="free_date" id="d_item1" class="<?php echo $inactive; ?>" ><?php echo JText::_('COM_VIRTUEMART_DATE'); ?>:</label>
					</div>
				    <div class="formFieldShipping">
				<?php echo $cal2; ?>
					</div>
				</div>
				
				
				<div class="field_wrapper pf_field_wrapper">
					<div class="formLabel pf_formLabel">
					  <label for="free_time" id="d_item2" class="<?php echo $inactive; ?>" ><?php echo JText::_('COM_VIRTUEMART_TIME'); ?>:</label>
					</div>
				    <div class="formFieldShipping pf_formField">
					  <?php echo $htmldeliveryslots; ?>
					</div>
				</div>
				
				
				
				<?php } 
				else {  
				 echo $notcondhtml; 
				}
				?>
				
				<?php if (empty($method->pf_mode)) {

				?>
					<div class="details <?php echo $inactive; ?>"><a target="_blank" class="pfdmod" rel="{handler: \'iframe\', size: {x: 800, y: 400}}" href="<?php echo JRoute::_('index.php?option=com_delivery&view=timetable&tmpl=component&nosef=1'); ?>" onclick="javascript: return Onepage.op_openlink(this); ">View timetable</a></div>
				<?php } ?>
				</div>
				</div>
			   </div>
		</div>
				
				<div class="clear" style="width: 100%; float: none; clear: both;">&nbsp;</div>
				
					
					  
					  