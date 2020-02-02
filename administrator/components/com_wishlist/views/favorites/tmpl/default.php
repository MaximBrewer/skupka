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
	
		// do field validation
		if (form.fav_date.value == "") {
			alert( "<?php echo JText::_( 'Date field cannot be empty', true ); ?>" );	
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
		<label for="product_id">
			<?php echo JText::_( 'PRODUCT_NAME' ); ?>:
		</label>
	</td>
	<td>
    	<?php
		$ProductList = FavoritesViewFavorites::getProductList();
		?>
        <select name="product_id" id="product_id">
        <?php
		foreach ($ProductList as $prod) {
			$selected = '';
			if ($prod->virtuemart_product_id == $this->data->product_id) $selected ='selected';
		?>
        <option value="<?php echo htmlspecialchars($prod->virtuemart_product_id, ENT_COMPAT, 'UTF-8');?>" <?php echo $selected ?>><?php echo htmlspecialchars($prod->product_name, ENT_COMPAT, 'UTF-8');?></option>
        <?php } ?>
        </select>
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
<tr>
    <td width="100" align="right" class="key">
		<label for="AdminNote">
			<?php echo JText::_( 'NOTE' ); ?>:
		</label>
	</td>
	<td>
		<?php echo JText::_( 'PRODUCT_NOTE' ); ?>
	</td>
</tr>
<tr>
	<td width="100" align="right" class="key">
		<label for="user_id">
			<?php echo JText::_( 'NAME' ); ?>:
		</label>
	</td>
	<td>
		<?php
		$UserList = FavoritesViewFavorites::getUserList();
		?>
        <select name="user_id" id="user_id">
        <?php
		foreach ($UserList as $user) {
			$selected = '';
			if ($user->id == $this->data->user_id) $selected ='selected';
		?>
        <option value="<?php echo htmlspecialchars($user->id, ENT_COMPAT, 'UTF-8');?>" <?php echo $selected ?>><?php echo htmlspecialchars($user->name, ENT_COMPAT, 'UTF-8');?></option>
        <?php } ?>
        </select>
	</td>
</tr>
<tr>
	<td width="100" align="right" class="key">
		<label for="fav_date">
			<?php echo JText::_( 'FAV_DATE' ); ?>:
		</label>
	</td>
	<td>
		<?php echo JHTML::calendar($this->data->fav_date, 'fav_date', 'fav_date'); ?>
	</td>
</tr>
<!-- jcb code -->

		</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_wishlist" />
<input type="hidden" name="id" value="<?php echo $this->data->fav_id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="favorites" />
</form>
