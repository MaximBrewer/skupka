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
		if (form.share_date.value == "") {
			alert( "<?php echo JText::_( 'Date field cannot be empty', true ); ?>" );
		} 
		
		else if (form.share_title.value == "") {
			alert( "<?php echo JText::_( 'You must specify a Title for your list', true ); ?>" );
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
		<label for="user_id">
			<?php echo JText::_( 'USER_ID' ); ?>:
		</label>
	</td>
	<td>
		<?php
		$UserList = FavoritesViewFavoritessh::getUserList();
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
		<label for="share_date">
			<?php echo JText::_( 'SHARE_DATE' ); ?>:
		</label>
	</td>
	<td>
		<?php echo JHTML::calendar($this->data->share_date, 'share_date', 'share_date'); ?>
	</td>
</tr>
<tr>
	<td width="100" align="right" class="key">
		<label for="share_title">
			<?php echo JText::_( 'SHARE_TITLE' ); ?>:
		</label>
	</td>
	<td>
		<input class="text_area" type="text" name="share_title" id="share_title" size="32" maxlength="32" value="<?php echo htmlspecialchars($this->data->share_title, ENT_COMPAT, 'UTF-8');?>" />
	</td>
</tr>
<tr>
	<td width="100" align="right" class="key">
		<label for="share_desc">
			<?php echo JText::_( 'SHARE_DESC' ); ?>:
		</label>
	</td>
	<td>
		<input class="text_area" type="text" name="share_desc" id="share_desc" size="32" maxlength="100" value="<?php echo htmlspecialchars($this->data->share_desc, ENT_COMPAT, 'UTF-8');?>" />
	</td>
</tr>
<tr>
	<td width="100" align="right" class="key">
		<label for="isWishList">
			<?php echo JText::_( 'ISWISHLIST' ); ?>:
		</label>
	</td>
	<td>
		<?php echo JHTML::_('select.booleanlist', 'isWishList', null, $this->data->isWishList, JText::_( 'JYES' ), JText::_( 'JNO' ), false); ?>
	</td>
</tr>
<tr>
    <td width="100" align="right" class="key">
		<label for="AdminNote">
			<?php echo JText::_( 'NOTE' ); ?>:
		</label>
	</td>
	<td>
		<?php echo JText::_( 'ADMIN_NOTE' ); ?>
	</td>
</tr>
<!-- jcb code -->

		</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_wishlist" />
<input type="hidden" name="id" value="<?php echo $this->data->shared_id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="favoritessh" />
</form>
