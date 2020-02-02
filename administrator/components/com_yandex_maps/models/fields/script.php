<?php
defined('JPATH_BASE') or die;
class JFormFieldScript extends JFormField{
	protected function getlabel(){
		return '';
	}
	protected function getInput(){
		$id = $this->getAttribute('forid') ?: 'jform_object_iconImageHref';
		$size = $this->getAttribute('forsize') ?: 'jform_object_iconImageSize';
		$offset = $this->getAttribute('foroffset') ?: 'jform_object_iconImageOffset';
		return '<script>
		window.JURL_ROOT = "'.JURI::root().'";
				loadImage = function (fimage, callback) {
					var loaded = false
					function loadHandler() {
						if (loaded) {
							return
						}
						loaded = true;
						callback&&callback.call(img)
					}
					var img = new Image()
					img.onload = loadHandler
					img.src = fimage;
					if ( img.complete || img.clientHeight>0 ) {
						loadHandler()
					}
				}
			jQuery(\'#'.$id.'\').on("change", function(){
				loadImage(JURL_ROOT + this.value, function () {
					jQuery(\'#'.$size.'\').val(this.width+\',\'+this.height).trigger("update");
					jQuery(\'#'.$offset.'\').val(-Math.round(this.width/2)+\',-\'+Math.round(this.height/2)).trigger("update");
				})
			})
		</script>';
	}
	public function getAttribute($attr_name, $default = null){
		if (!empty($this->element[$attr_name])) {
			return $this->element[$attr_name];
		} else {
			return $default;
		}
	}
}
