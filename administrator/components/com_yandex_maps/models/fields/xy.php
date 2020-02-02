<?php
defined('JPATH_BASE') or die;
class JFormFieldXY extends JFormField{
	protected function getInput(){
		jimport('joomla.version');
		$version = new JVersion();
		$html = '';
		if (version_compare($version->RELEASE, '3.2')<0) {
			JHtml::script(JURI::root().'administrator/components/com_yandex_maps/helpers/joomla25/jui/js/jquery.min.js');
		}
		$html.= '<div class="form-inline">
			<input type="hidden" id="'.$this->id.'" name="'.$this->name.'" class="input-small" value="'.$this->value.'">
			<input type="text"  class="input-small xy xy'.$this->id.'" placeholder="">
			<input type="text"  class="input-small xy xy'.$this->id.'" placeholder="">
		</div>
		<script>
			jQuery("#'.$this->id.'").on("update" ,function() {
				var values = this.value.split(","), i = 0, elms = jQuery(".xy'.$this->id.'");
				for (;i<values.length;i = i + 1) {
					if (!elms.eq(i).is(":focus")) {
						elms.eq(i).val(values[i]);
					}
				}
			}).trigger("update");
			jQuery(".xy'.$this->id.'").on("change keydown keyup" ,function() {
				var values = [], i;
				jQuery(".xy'.$this->id.'").each(function() {
					values.push(this.value);
				});
				jQuery("#'.$this->id.'").val(values.join(",")).trigger("change");
			});
		</script>';
		return $html;
	}
}
