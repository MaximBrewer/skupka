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
<tr>
	<td width="100" align="right" class="key">
		<label for="dt_stamp">
			<?php echo JText::_( 'DT_STAMP' ); ?>:
		</label>
	</td>
	<td>
		<?php echo JHTML::calendar($this->data->dt_stamp, 'dt_stamp', 'dt_stamp'); ?>
	</td>
</tr>
<tr>
	<td width="100" align="right" class="key">
		<label for="log_type">
			<?php echo JText::_( 'LOG_TYPE' ); ?>:
		</label>
	</td>
	<td>
		<input class="text_area" type="text" name="log_type" id="log_type" size="32" maxlength="1" value="<?php echo htmlspecialchars($this->data->log_type, ENT_COMPAT, 'UTF-8');?>" />
	</td>
</tr>
<tr>
	<td width="100" align="right" class="key">
		<label for="user_id">
			<?php echo JText::_( 'USER_ID' ); ?>:
		</label>
	</td>
	<td>
		<input class="text_area" type="text" name="user_id" id="user_id" size="32" maxlength="11" value="<?php echo htmlspecialchars($this->data->user_id, ENT_COMPAT, 'UTF-8');?>" />
	</td>
</tr>
<tr>
	<td width="100" align="right" class="key">
		<label for="cust_id">
			<?php echo JText::_( 'CUST_ID' ); ?>:
		</label>
	</td>
	<td>
		<input class="text_area" type="text" name="cust_id" id="cust_id" size="32" maxlength="11" value="<?php echo htmlspecialchars($this->data->cust_id, ENT_COMPAT, 'UTF-8');?>" />
	</td>
</tr>
<tr>
	<td width="100" align="right" class="key">
		<label for="product_id">
			<?php echo JText::_( 'PRODUCT_ID' ); ?>:
		</label>
	</td>
	<td>
		<input class="text_area" type="text" name="product_id" id="product_id" size="32" maxlength="11" value="<?php echo htmlspecialchars($this->data->product_id, ENT_COMPAT, 'UTF-8');?>" />
	</td>
</tr>
<tr>
	<td width="100" align="right" class="key">
		<label for="product_qty">
			<?php echo JText::_( 'PRODUCT_QTY' ); ?>:
		</label>
	</td>
	<td>
		<input class="text_area" type="text" name="product_qty" id="product_qty" size="32" maxlength="3" value="<?php echo htmlspecialchars($this->data->product_qty, ENT_COMPAT, 'UTF-8');?>" />
	</td>
</tr>
<!-- jcb code -->

		</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_wishlist" />
<input type="hidden" name="id" value="<?php echo $this->data->log_id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="favoriteslog" />
</form>
