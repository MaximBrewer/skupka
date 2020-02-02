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
						<td class="column1"><label id="headingidlabel" for="headingid" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HEADINGID_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HEADINGID');?></label></td>
						<td><input id="headingid" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="headingstylelabel" for="headingstyle" class="hastip" title="<?php echo WFText::_('WF_LABEL_STYLE_DESC');?>"><?php echo WFText::_('WF_LABEL_STYLE');?></label></td>
						<td><input type="text" id="headingstyle" value="" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo WFText::_('WF_HSHTMLEXPAND_LEGEND_HEADINGHTMLTEXT');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><textarea id="heading" rows="15"  cols="80" ></textarea></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo WFText::_('WF_HSHTMLEXPAND_LEGEND_HEADINGOVERLAY');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="column1"><label id="hoenableoverlaylabel" for="hoenableoverlay" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOENABLEOVERLAY_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOENABLEOVERLAY');?></label></td>
						<td><input class="checkbox" type="checkbox" id="hoenableoverlay" name="hoenableoverlay"/></td>
					</tr>
					<tr>
						<td class="column1"><label id="hofadelabel" for="hofade" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOFADE_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOFADE');?></label></td>
						<td><select id="hofade" name="hofade" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="0"><?php echo WFText::_('WF_OPTION_NO');?></option>
								<option value="1"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="2"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_NOT_IN_IE');?></option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="hovpositionlabel" for="hovposition" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOVPOSITION_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOVPOSITION');?></label></td>
						<td><select id="hovposition" name="hovposition" >
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
					<td class="column1"><label id="hohpositionlabel" for="hohposition" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOHPOSITION_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOHPOSITION');?></label></td>
						<td><select id="hohposition" name="hohposition" >
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
						<td><label id="hooffsetxlabel" for="hooffsetx" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOOFFSETX_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOOFFSETX');?></label></td>
						<td><input type="text" id="hooffsetx" value="" /></td>
					</tr>
					<tr>
						<td><label id="hooffsetylabel" for="hooffsety" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOOFFSETY_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOOFFSETY');?></label></td>
						<td><input type="text" id="hooffsety" value="" /></td>
					</tr>
					<tr>
					<td class="column1"><label id="horelativetolabel" for="horelativeto" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HORELATIVETO_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HORELATIVETO');?></label></td>
						<td><select id="horelativeto" name="horelativeto" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="viewport"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_VIEWPORT');?></option>
								<option value="expander"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_EXPANDER');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="hohideonmouseoutlabel" for="hohideonmouseout" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOHIDEONMOUSEOUT_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOHIDEONMOUSEOUT');?></label></td>
						<td><select id="hohideonmouseout" name="hohideonmouseout" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="true"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="false"><?php echo WFText::_('WF_OPTION_NO');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="hoopacitylabel" for="hoopacity" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOOPACITY_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOOPACITY');?></label></td>
						<td><input type="text" id="hoopacity" value="" /></td>
					</tr>
					<tr>
						<td><label id="howidthlabel" for="howidth" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOWIDTH_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOWIDTH');?></label></td>
						<td><input type="text" id="howidth" value="" /></td>
					</tr>
					<tr>
						<td><label id="hoclassnamelabel" for="hoclassname" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOCLASSNAME_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HOCLASSNAME');?></label></td>
						<td><input type="text" id="hoclassname" value="" /></td>
					</tr>
				</table>
			</fieldset>
