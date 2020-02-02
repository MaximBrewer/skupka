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
				<legend><?php echo WFText::_('WF_HSHTMLEXPAND_LEGEND_OVERLAY');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="column1"><label id="expanderidlabel" for="expanderid" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_EXPANDERID_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_EXPANDERID');?></label></td>
						<td><input id="expanderid" type="text" value="" onchange="return HsHtmlExpanderDialog.mirrorValue( this, 'id' );"/></td>
					</tr>
					<tr>
						<td class="column1"><label id="overlayidlabel" for="overlayid" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVERLAYID_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVERLAYID');?></label></td>
						<td><input id="overlayid" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="overlaystylelabel" for="overlaystyle" class="hastip" title="<?php echo WFText::_('WF_LABEL_STYLE_DESC');?>"><?php echo WFText::_('WF_LABEL_STYLE');?></label></td>
						<td><input type="text" id="overlaystyle" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="ovfadelabel" for="ovfade" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVFADE_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVFADE');?></label></td>
						<td><select id="ovfade" name="ovfade" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="0"><?php echo WFText::_('WF_OPTION_NO');?></option>
								<option value="1"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="2"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_NOT_IN_IE');?></option>
						</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="ovvpositionlabel" for="ovvposition" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVVPOSITION_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVVPOSITION');?></label></td>
						<td><select id="ovvposition" name="ovvposition" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="above"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_ABOVE');?></option>
								<option value="top"><?php echo WFText::_('WF_OPTION_TOP');?></option>
								<option value="middle"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_MIDDLE');?></option>
								<option value="bottom"><?php echo WFText::_('WF_OPTION_BOTTOM');?></option>
								<option value="below"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_BELOW');?></option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="ovhpositionlabel" for="ovhposition" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVHPOSITION_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVHPOSITION');?></label></td>
						<td><select id="ovhposition" name="ovhposition" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="leftpanel"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_LEFT_PANEL');?></option>
								<option value="left"><?php echo WFText::_('WF_OPTION_LEFT');?></option>
								<option value="center"><?php echo WFText::_('WF_OPTION_CENTER');?></option>
								<option value="right"><?php echo WFText::_('WF_OPTION_RIGHT');?></option>
								<option value="rightpanel"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_RIGHT_PANEL');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="ovoffsetxlabel" for="ovoffsetx" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVOFFSETX_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVOFFSETX');?></label></td>
						<td><input type="text" id="ovoffsetx" value="" /></td>
					</tr>
					<tr>
						<td><label id="ovooffsetylabel" for="ovoffsety" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVOFFSETY_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVOFFSETY');?></label></td>
						<td><input type="text" id="ovoffsety" value="" /></td>
					</tr>
					<tr>
					<td class="column1"><label id="ovrelativetolabel" for="ovrelativeto" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVRELATIVETO_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVRELATIVETO');?></label></td>
						<td><select id="ovrelativeto" name="ovrelativeto" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="viewport"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_VIEWPORT');?></option>
								<option value="expander"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_EXPANDER');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="ovhideonmouseoutlabel" for="ovhideonmouseout" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVHIDEONMOUSEOUT_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVHIDEONMOUSEOUT');?></label></td>
						<td><select id="ovhideonmouseout" name="ovhideonmouseout" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="true"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="false"><?php echo WFText::_('WF_OPTION_NO');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="ovopacitylabel" for="ovopacity" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVOPACITY_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVOPACITY');?></label></td>
						<td><input type="text" id="ovopacity" value="" /></td>
					</tr>
					<tr>
						<td><label id="ovwidthlabel" for="ovwidth" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVWIDTH_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVWIDTH');?></label></td>
						<td><input type="text" id="ovwidth" value="" /></td>
					</tr>
					<tr>
						<td><label id="ovclassnamelabel" for="ovclassname" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVCLASSNAME_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OVCLASSNAME');?></label></td>
						<td><input type="text" id="ovclassname" value="" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo WFText::_('WF_HSHTMLEXPAND_LEGEND_OVERLAYHTMLTEXT');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><textarea id="overlay" rows="16"  cols="80" ></textarea></td>
					</tr>
				</table>
			</fieldset>
