<?php
/**
* @version $Id: link.php 2008-02-20 Ryan Demmer $
* @package JCE
* @copyright Copyright (C) 2006-2007 Ryan Demmer. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
			<fieldset>
				<legend><?php echo WFText::_('WF_HSEXPAND_LEGEND_POPUP_IMAGE');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="nowrap"><label id="hreflabel" for="href" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_POPUPURL_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_POPUPURL');?></label></td>
						<td><input id="href" type="text" value="" size="150" class="required browser"/></td>
					</tr>
					<tr>
						<td class="nowrap"><label id="titlelabel" for="title" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_POPUPTITLE_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_POPUPTITLE');?></label></td>
						<td colspan="3"><input id="title" name="title" type="text" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="idlabel" for="id" class="hastip" title="<?php echo WFText::_('WF_LABEL_ID_DESC');?>"><?php echo WFText::_('WF_LABEL_ID');?></label></td>
						<td><input id="id" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="stylelabel" for="style" class="hastip" title="<?php echo WFText::_('WF_LABEL_STYLE_DESC');?>"><?php echo WFText::_('WF_LABEL_STYLE');?></label></td>
						<td><input type="text" id="style" value="" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo WFText::_('WF_HSEXPAND_LEGEND_THUMBNAIL_IMAGE');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="nowrap"><label id="srclabel" for="src" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_THUMBURL_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_THUMBURL');?></label></td>
						<td><input id="src" type="text" value="" size="150" class="required browser"/></td>
					</tr>
					<tr>
						<td class="nowrap"><label id="imgtitlelabel" for="imgtitle" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_THUMBTITLE_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_THUMBTITLE');?></label></td>
						<td colspan="3"><input id="imgtitle" name="imgtitle" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="altlabel" for="alt" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_THUMBALT_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_THUMBALT');?></label></td>
						<td><input type="text" id="alt" value="" /></td>
					</tr>
					<tr>
						<td><label id="imgclasslabel" for="imgclass" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_CLASS_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_CLASS');?></label></td>
						<td><input id="imgclass" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="imgidlabel" for="imgid" class="hastip" title="<?php echo WFText::_('WF_LABEL_ID_DESC');?>"><?php echo WFText::_('WF_LABEL_ID');?></label></td>
						<td><input id="imgid" type="text" value="" onchange="return HsExpanderDialog.mirrorValue( this, 'thumbid' );"/></td>
					</tr>
					<tr>
						<td><label id="imgstylelabel" for="imgstyle" class="hastip" title="<?php echo WFText::_('WF_LABEL_STYLE_DESC');?>"><?php echo WFText::_('WF_LABEL_STYLE');?></label></td>
						<td><input type="text" id="imgstyle" value="" /></td>
					</tr>
					<tr>
						<td><label id="widthlabel" for="width" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_WIDTH_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_WIDTH');?></label></td>
						<td><input type="text" id="width" value="" /></td>
					</tr>
					<tr>
						<td><label id="heightlabel" for="height" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_HEIGHT_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_HEIGHT');?></label></td>
						<td><input type="text" id="height" value="" /></td>
					</tr>
				</table>
			</fieldset>
				<label id="unobtrusivelabel" for="unobtrusive" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_UNOBTRUSIVE_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_UNOBTRUSIVE');?></label>
				<input type="checkbox" id="unobtrusive" onclick="return HsExpanderDialog.setTabs();"/>