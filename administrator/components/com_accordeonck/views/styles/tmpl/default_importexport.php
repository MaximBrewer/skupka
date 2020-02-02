<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
defined('_JEXEC') or die;
?>
<div class="" id="accordeonckimportpopup" style="padding:10px;display:none;">
	<div class=""><h1><?php echo JText::_('CK_IMPORT'); ?></h1></div>
		<br />
		<form id="importPage" name="importPage" method="post" enctype="multipart/form-data">
			<div>
				<fieldset>
					<table class="table table-striped">
						<tr>
							<td width="110" class="key">
								<label for="title">
									<?php echo JText::_('CK_CHOOSE_FILE_MMCK'); ?>:
								</label>
							</td>
							<td>
								<input class="inputbox" type="file" name="file" id="file" size="60" />
								<input type="submit" name="submitfile" class="inputbox btn" value="<?php echo JText::_('CK_INSTALL'); ?>" />
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="task" value="uploadParamsFile" />
		</form>
</div>
<div id="accordeonckexportpopup" class="" style="position:relative;display:none;">
	<div style="padding: 10px;">
		<div class="ckExportModalContent">
			<div class="" id="">
				<div class=""><h1><?php echo JText::_('CK_EXPORT'); ?></h1></div>
					<div id="ckExportPageDownload" style="border-top: 1px solid #ddd;padding: 0.75em;">

					</div>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function() {
		jQuery("form#importPage").on('submit', function(e) { 
			e.preventDefault();
			var form = document.forms.namedItem('importPage');
			var formData = new FormData(form);
			uploadParamsFile(formData);
			return false;
		});
	});
</script>