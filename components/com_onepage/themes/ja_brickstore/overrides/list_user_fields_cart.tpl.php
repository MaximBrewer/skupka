<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
* RuposTel.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/


{
	$hiddenFields = '';
	// Output: Userfields
	foreach($rowFields_cart['fields'] as $field) {
	?>
	<fieldset class="vm-fieldset-<?php echo str_replace('_','-',$field['name']) ?>">
		<div  class="cart <?php echo str_replace('_','-',$field['name']) ?>" title="<?php echo strip_tags($field['description']) ?>">
		<span class="cart <?php echo str_replace('_','-',$field['name']) ?>" ><?php echo $field['title'] ?></span>

		<?php
		if ($field['hidden'] == true) {
			// We collect all hidden fields
			// and output them at the end
			$hiddenFields .= $field['formcode'] . "\n";
		} else { ?>
				<?php echo $field['formcode'] ?>
			</div>
	<?php } ?>

	</fieldset>

	<?php
	}
	// Output: Hidden Fields
	echo $hiddenFields;
}