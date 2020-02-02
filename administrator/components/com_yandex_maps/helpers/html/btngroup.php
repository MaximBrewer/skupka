<?php
defined('_JEXEC') or die;
JLoader::register('BtnGroupHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/btngroup.php');

abstract class JHtmlBtnGroup{
	static public function _($name, $title, $value, $group = 'controls', $class='', $id_input = null, $class_input = '') {
		$id = $id_input ? $id_input : 'jform_'.$group.'_'.$name;
		$fullname = $id_input ? $id_input : "jform[$group][$name]";
		?>
			<div class="control-group <?php echo $class?>">
				<label class="control-label" for="<?php echo $id?>" ><?php echo $title?></label>
				<div class="controls">
					<input type="hidden" class="<?php echo 'jform_'.$group;?> <?php echo $class_input?>" id="<?php echo $id?>" name="<?php echo $fullname?>" data-name="<?php echo $name?>" value="<?php echo $value?>">
					<div class="btn-group" data-toggle="buttons-radio">
						<button id="<?php echo $id?>0" onclick="jQuery('#<?php echo $id?>').val(0).trigger('change');" type="button" class="btn <?php echo !$value ? 'active btn-danger' : ''?>"><?php echo JText::_("JNO")?></button>
						<button id="<?php echo $id?>1" onclick="jQuery('#<?php echo $id?>').val(1).trigger('change');" type="button" class="btn <?php echo $value ? 'active btn-success' : ''?>"><?php echo JText::_("JYES")?></button>
					</div>
					<script>
						(function($){
							var id = '#<?php echo $id?>',
								input = $(id);
							input.on('change update', function () {
								if (!this.value) {
									this.value = 0;
								}
								if (parseInt(this.value)) {
									$(id+'0').removeClass('btn-danger').removeClass('active')
									$(id+'1').addClass('btn-success').addClass('active')
								} else {
									$(id+'0').addClass('btn-danger').addClass('active')
									$(id+'1').removeClass('btn-success').removeClass('active')
								}
							})
						}(jQuery));
					</script>
				</div>
			</div>
	<?php }
}