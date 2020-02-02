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
				<legend><?php echo WFText::_('WF_HSHTMLEXPAND_LEGEND_GENERAL');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="nowrap"><label id="hreflabel" for="href" class="hastip" title="<?php echo WFText::_('WF_LABEL_URL_DESC');?>"><?php echo WFText::_('WF_LABEL_URL');?></label></td>
						<td><input id="href" type="text" value="" size="150" class="required browser"/></td>
 					</tr>
					<tr>
						<td class="nowrap"><label id="titlelabel" for="title" class="hastip" title="<?php echo WFText::_('WF_LABEL_TITLE_DESC');?>"><?php echo WFText::_('WF_LABEL_TITLE');?></label></td>
						<td colspan="3"><input id="title" name="title" type="text" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="idlabel" for="id" class="hastip" title="<?php echo WFText::_('WF_LABEL_ID_DESC');?>"><?php echo WFText::_('WF_LABEL_ID');?></label></td>
						<td><input id="id" type="text" value="" onchange="return HsHtmlExpanderDialog.mirrorValue( this, 'expanderid' );"/></td>
					</tr>
					<tr>
						<td><label id="stylelabel" for="style" class="hastip" title="<?php echo WFText::_('WF_LABEL_STYLE_DESC');?>"><?php echo WFText::_('WF_LABEL_STYLE');?></label></td>
						<td><input type="text" id="style" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="objecttypelabel" for="objecttype" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OBJECTTYPE_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OBJECTTYPE');?></label></td>
						<td><select id="objecttype" name="objecttype" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="ajax"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_AJAX');?></option>
								<option value="iframe"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_IFRAME');?></option>
								<option value="swf"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_FLASH');?></option>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>
				<label id="unobtrusivelabel" for="unobtrusive" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_UNOBTRUSIVE_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_UNOBTRUSIVE');?></label>
				<input type="checkbox" id="unobtrusive" onclick="return HsHtmlExpanderDialog.setTabs();"/>
