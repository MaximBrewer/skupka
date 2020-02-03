<?php
/**
 * @name		Slideshow CK
 * @package		com_slideshowck
 * @copyright	Copyright (C) 2019. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */
 
defined('_JEXEC') or die;
?>
<div class="" id="ckimportpopup" style="padding:10px;display:none;">
	<div class=""><h1><?php echo JText::_('CK_IMPORT'); ?></h1></div>
		<br />
		<?php echo SlideshowckHelper::getProMessage() ?>
</div>
<div id="ckexportpopup" class="" style="position:relative;display:none;">
	<div style="padding: 10px;">
		<div class="ckexportmodalcontent">
			<div class="" id="">
				<div class=""><h1><?php echo JText::_('CK_EXPORT'); ?></h1></div>
					<?php echo SlideshowckHelper::getProMessage() ?>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function() {
		jQuery("form#importpage").on('submit', function(e) { 
			e.preventDefault();
			var form = document.forms.namedItem('importpage');
			var formData = new FormData(form);
			ckUploadParamsFile(formData);
			return false;
		});
	});
</script>