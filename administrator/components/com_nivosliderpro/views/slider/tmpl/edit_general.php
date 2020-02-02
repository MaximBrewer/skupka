<?php defined('_JEXEC') or die; ?>

<?php

	if($this->isNew)
		$boxTitle = JText::_('COM_NIVOSLIDERPRO_NEW');
	else
		$boxTitle = JText::_('COM_NIVOSLIDERPRO_SLIDER_SETTINGS');
		
?>

	<div class="width-60 fltlft">	
		<?php UniteFunctionJoomla::putHtmlFieldsetBox($this->form, "general", $boxTitle); ?>
		<?php if($this->isNew == false):?>
			<a href="<?php echo $this->linkEditSlides?>" id="button_edit_slides_general" class="button-primary">Edit Slides</a>
		<?php endif;?>
				
	</div> 

	<div class="width-40 fltrt">
		<?php UniteFunctionJoomla::putHtmlFieldsetBoxes($this->form, "params"); ?>
	</div>
	
	<div class="clr"></div>
	
	