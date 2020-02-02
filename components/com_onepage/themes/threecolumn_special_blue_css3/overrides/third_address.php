<?php
/**
 * @version		opctracking.php 
 * @copyright	Copyright (C) 2005 - 2013 RuposTel.com
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<input type="checkbox" <?php if (!empty($checked)) echo $checked; ?> value="1" name="third_address_opened" id="third_address_opened" onclick="return Onepage.toggleFields(['third_address_wrap'], '', !this.checked);" /><label for="third_address_opened"><?php echo JText::_('COM_ONEPAGE_THIRD_ADDRESS_ADD'); ?></label>
<div id="third_address_wrap" <?php if (empty($checked)) echo ' style="display: none;" ';  ?>>
<?php
echo $fields_html; 
?>
</div>