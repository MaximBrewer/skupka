<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php jimport( 'joomla.html.editor' ); $editor =& JFactory::getEditor(); ?>
<?php jimport( 'joomla.html.html' ); ?>
<?php $data =& $this->data; ?>
<script type="text/javascript">

	Joomla.submitbutton = function(pressbutton) {
		var form = document.adminForm;
	
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
	
		// remove this code
		alert ('<?php echo 'Remember to add js check in ' . __FILE__ . ' after line n. ' . __LINE__; ?>');
		submitform( pressbutton );
		return;
		// end remove this code
	
		// do field validation
		if (form.My_Field_Name.value == "") {
			alert( "<?php echo JText::_( 'Field must have a name', true ); ?>" );
		} else if (form.My_Field_Name.value.match(/[a-zA-Z0-9]*/) != form.My_Field_Name.value) {
			alert( "<?php echo JText::_( 'Field name contains bad caracters', true ); ?>" );
		} else if (form.My_Field_Name_typefield.options[form.My_Field_Name_typefield.selectedIndex].value == "0") {
			alert( "<?php echo JText::_( 'You must select a field type', true ); ?>" );		
		} else {
			submitform( pressbutton );
		}
	}

</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'DETAILS' ); ?></legend>
		<table class="admintable">
<!-- jcb code -->
<!-- jcb code -->

		</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_wishlist" />
<input type="hidden" name="id" value="<?php echo $this->data->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="josusers" />
</form>
