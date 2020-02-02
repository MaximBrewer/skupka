<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
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
					<div>
						<div class="ckpopuptitle"><?php echo JText::_('CK_MENU_EDITION') . ' : ' . $input->get('menutype', 'string', '') ?></div>
					</div>
					<div class="ck_buttons_right">
						<div class="ck_button big">
							<?php if ($input->get('layout','') == 'modal') { ?>
							<a href="javascript:void(0);" class="ckcancel" onclick="window.parent.CKBox.close()">
							<?php } else { ?>
							<a href="<?php echo JUri::root(true) ?>/administrator/index.php?option=com_maximenuck&view=menus" class="ckcancel" onclick="window.parent.jModalClose()">
							<?php } ?>
								<?php echo JText::_('CK_CLOSE') ?>
							</a>
						</div>
					</div>
				</td>
			</tr>
        </table>
    </div>
</div>
<script type="text/javascript">
	$ck('#mainmenuck').find('.infotip').hover(
			function() {
				if ($ck(this).attr('rel')) {
					$ck('#checkmodules').hide();
					$ck('#infotipdesc').append($ck("<span class=\"infotipdesc\">" + $ck(this).attr('rel') + "</span>")).hide().show();
				}
			},
			function() {
				$ck('#infotipdesc').find("span.infotipdesc").remove();
				$ck('#checkmodules').show();
			}
	);
</script>