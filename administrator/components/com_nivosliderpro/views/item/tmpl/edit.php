<?php

// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'item.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>


<form action="<?php echo JRoute::_('index.php?option=com_nivosliderpro&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="slide_wrapper">
		<div class="width-100 fltlft">
			<?php
				echo $this->loadTemplate("inside"); 
			?>
		</div>	<!-- width_100 -->
	</div> <!-- slide wrapper -->
	
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	
</form>

	<script type="text/javascript">
		jQuery(document).ready(function(){
			UniteNivoPro.initItemView();
		});
	</script>


<div class="clr"></div>

<?php 
	HelperNivoPro::includeView("sliders/tmpl/footer.php");
?>	 

