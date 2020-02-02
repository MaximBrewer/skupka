<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$input = new JInput();
?>
<div id="mainmenuck" class="clearfix">
    <div class="ckheader clearfix">
        <table>
			<tr>
				<td style="vertical-align:top;">
					<div class="ckheaderlogo"></div>
				</td>
				<td style="width:100%;">
					<div id="ckmessage">
						<div></div>
					</div>
					<div class="ck_buttons_right">
						<div class="ck_button big">
							<a href="javascript:void(0);" id="ckpopupstyleswizard_import" class="ckimport black" onclick="importparams('<?php echo $input->get('id',0,'int'); ?>')">
								<?php echo JText::_('CK_IMPORT') ?>
							</a>
						</div>
						<div class="ck_button big">
							<a href="javascript:void(0);" id="ckpopupstyleswizard_export" class="ckexport black" onclick="exportparams('<?php echo $input->get('id',0,'int'); ?>')">
								<?php echo JText::_('CK_EXPORT') ?>
							</a>
						</div>
						<div class="ck_button big">
							<a href="javascript:void(0);" id="ckpopupstyleswizard_clearfields" class="ckcancel" onclick="clear_fields()">
								<?php echo JText::_('CK_CLEAR_FIELDS') ?>
							</a>
						</div>
						<div class="ck_button big">
							<a href="javascript:void(0);" id="ckpopupstyleswizard_makepreview" class="ckpreview black" onclick="preview_stylesparams(this)">
								<?php echo JText::_('CK_PREVIEW') ?>
							</a>
						</div>
						<div class="ck_button big">
							<a href="javascript:void(0);" id="ckpopupstyleswizard_save" class="cksave black" onclick="save_stylesparams(this, '<?php echo $input->get('id',0,'int'); ?>', '<?php echo $input->get('layout','','string'); ?>')">
								<?php echo JText::_('CK_SAVE') ?>
							</a>
						</div>
						<?php /*
						<div class="ck_button big">
							<a href="javascript:void(0)" class="ckfullscreen" onclick="toggle_fullscreen(window.parent)">
								<?php echo JText::_('CK_FULLSCREEN') ?>
							</a>
						</div>
						 */ ?>
						<?php if ($input->get('layout', '', 'string') === 'modal') { ?>
						<div class="ck_button big">
							<a href="javascript:void(0)" class="ckcancel" onclick="window.parent.CKBox.close()">
								<?php echo JText::_('CK_CLOSE') ?>
							</a>
						</div>
						<?php } else { ?>
						<div class="ck_button big">
							<a href="<?php echo JRoute::_('index.php?option=com_accordeonck'); ?>" class="ckcancel" >
								<?php echo JText::_('CK_EXIT') ?>
							</a>
						</div>
						<?php } ?>
					</div>
				</td>
			</tr>
        </table>
    </div>
</div>