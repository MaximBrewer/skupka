<?php
defined('_JEXEC') or die;
JLoader::register('MediaHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/media.php');

abstract class JHtmlMedia{
	static public function _($name,$id,$group="images",$folder='',$classes = '', $value=""){
		JHTML::_( 'behavior.modal' );
		?><div class="input-prepend input-append">
		<div class="media-preview add-on">
			<span class="hasTipPreview" title=""><i class="icon-eye"></i></span>
		</div>
		<input type="text" name="jform[<?php echo $group?>][<?php echo $name?>]" id="<?php echo $id?>" value="<?php echo $value?>" readonly="readonly" title="" class="input-small hasTipImgpath <?php echo $classes;?>">
		<a class="modal btn" title="Выбрать" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=61&amp;author=<?php echo JFactory::getUser()->id?>&amp;fieldid=<?php echo $id?>&amp;folder=<?php echo $folder?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
		Выбрать</a><a class="btn hasTooltip" title="" href="#" onclick="
		jInsertFieldValue('', '<?php echo $id; ?>');
		return false;
		" data-original-title="Очистить">
		<i class="icon-remove"></i></a>
	</div>
	<script>
	jQuery(function($) {
		if (!SqueezeBox.doc) {
			SqueezeBox.initialize({});
			SqueezeBox.assign($('a.modal').get(), {
				parse: 'rel'
			});
		}
	});
	function jModalClose() {
		SqueezeBox.close();
	}
	function jInsertFieldValue(value, id) {
		var $ = jQuery.noConflict();
		var old_value = $("#" + id).val();
		if (old_value != value) {
			var $elem = $("#" + id);
			$elem.val(value);
			$elem.trigger("change");
			if (typeof($elem.get(0).onchange) === "function") {
				$elem.get(0).onchange();
			}
			jMediaRefreshPreview(id);
		}
	}
	function jMediaRefreshPreview(id) {
		var $ = jQuery.noConflict();
		var value = $("#" + id).val();
		var $img = $("#" + id + "_preview");
		if ($img.length) {
			if (value) {
				$img.attr("src", "<?php echo JURI::root()?>" + value);
				$("#" + id + "_preview_empty").hide();
				$("#" + id + "_preview_img").show()
			} else { 
				$img.attr("src", "")
				$("#" + id + "_preview_empty").show();
				$("#" + id + "_preview_img").hide();
			} 
		} 
	}
	function jMediaRefreshPreviewTip(tip){
		var $ = jQuery.noConflict();
		var $tip = $(tip);
		var $img = $tip.find("img.media-preview");
		$tip.find("div.tip").css("max-width", "none");
		var id = $img.attr("id");
		id = id.substring(0, id.length - "_preview".length);
		jMediaRefreshPreview(id);
		$tip.show();
	}
	function jMediaRefreshImgpathTip(tip){
		var $ = jQuery.noConflict();
		var $tip = $(tip);
		$tip.css("max-width", "none");
		var $imgpath = $("#" + "<?php echo $id?>").val();
		$("#TipImgpath").html($imgpath);
		if ($imgpath.length) {
		 $tip.show();
		} else {
		 $tip.hide();
		}
	}
	</script>
	<?php }
}