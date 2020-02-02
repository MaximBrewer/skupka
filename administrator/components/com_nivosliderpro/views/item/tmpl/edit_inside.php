<?php
	// No direct access.
	defined('_JEXEC') or die;
	
	 $fieldSetOptional = $this->form->getFieldset('optional');
	 $styleImagePreview = "";
?>

			<fieldset class="adminform">
				<legend><?php echo empty($this->item->id) ? JText::_('COM_NIVOSLIDERPRO_NEW') : JText::sprintf('COM_NIVOSLIDERPRO_EDIT', $this->item->id); ?></legend>
				<div class="slide_wrapper_inside">
					<ul class="adminformlist" id="slide_list">
						<li>
							<?php $this->putField("id") ?>
						</li>
						<li>
							<div>
								<?php $this->putField("title") ?>
							</div>
						</li>
						<li>
							<?php $this->putField("sliderid") ?>
						</li>				
						<li>
							<?php $this->putField("published") ?>
						</li>
						<li>
							<div class="clr"></div>
							<hr>
							<div class="clr"></div>
						</li>
						<li>
							<?php $this->putOptionalField("image");?>								
						</li>
						<li>
							<div class="clear"></div>
							<br>
							<div id="image_preview_wrapper" class="image_preview_wrapper" <?php echo $this->imagePreviewStyle?>></div>
							
						</li>						
						<li>
							<div class="sap_vert"></div>
						</li>
						<li>
							<?php echo $fieldSetOptional["jform_params_description"]->label;?>
							<div class="clr"></div> 
							<?php echo $fieldSetOptional["jform_params_description"]->input;?>
						</li>
						<li>
							<div class="sap_vert"></div>
						</li>						
						<li>
							<?php $this->putOptionalField("image_alt");?>
						</li>
						
						<li>
							<div class="sap_vert"></div>
						</li>						
																		
						<li>
							<?php $this->putOptionalField("activate_link");?>
						</li>				
						<li>
							<?php $this->putOptionalField("link");?>
						</li>
						<li>
							<?php $this->putOptionalField("link_open_in");?>
						</li>
					</ul>
				
				</div>
			</fieldset>				
