<?php
defined('_JEXEC') or die;
JLoader::register('XYHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/xy.php');

abstract class JHtmlXY{
	static public  function _($id, $name, $fields = array(), $group ='', $classes = '', $value=''){?>
		<div class="form-inline">
			<input type="hidden" value="<?php echo $value?>" id="<?php echo $id?>" name="jfield[<?php echo $group?>][<?php echo $name?>]" class="input-small <?php echo $classes;?>">
		<?php foreach ($fields as $title) {?>
			<input type="text"  class="input-small xy xy<?php echo $id?>" placeholder="<?php echo $title?>">
		<?php } ?>
		</div>
		<script>
			jQuery('#<?php echo $id?>').on('update' ,function() {
				var values = this.value.split(','), i = 0, elms = jQuery('.xy<?php echo $id?>');
				for (;i<values.length;i = i + 1) {
					if (!elms.eq(i).is(':focus')) {
						elms.eq(i).val(values[i]);
					}
				}
			}).trigger('update');
			jQuery('.xy<?php echo $id?>').on('change keydown keyup' ,function() {
				var values = [], i;
				jQuery('.xy<?php echo $id?>').each(function() {
					values.push(this.value);
				});
				jQuery('#<?php echo $id?>').val(values.join(',')).trigger('change');
			});
		</script>
	<?php
	}
}