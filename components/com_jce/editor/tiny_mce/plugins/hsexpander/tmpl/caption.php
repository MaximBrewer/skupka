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
				<legend><?php echo WFText::_('WF_HSEXPAND_LEGEND_GENERAL');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="column1"><label id="captionidlabel" for="captionid" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_CAPTIONID_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_CAPTIONID');?></label></td>
						<td><input id="captionid" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="captionstylelabel" for="captionstyle" class="hastip" title="<?php echo WFText::_('WF_LABEL_STYLE_DESC');?>"><?php echo WFText::_('WF_LABEL_STYLE');?></label></td>
						<td><input type="text" id="captionstyle" value="" /></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo WFText::_('WF_HSEXPAND_LEGEND_CAPTIONHTMLTEXT');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><textarea id="caption" rows="15" cols="80" ></textarea></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo WFText::_('WF_HSEXPAND_LEGEND_CAPTIONOVERLAY');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td class="column1"><label id="coenableoverlaylabel" for="coenableoverlay" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_COENABLEOVERLAY_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_COENABLEOVERLAY');?></label></td>
						<td><input class="checkbox" type="checkbox" id="coenableoverlay" name="coenableoverlay"/></td>
					</tr>
					<tr>
						<td class="column1"><label id="cofadelabel" for="cofade" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_COFADE_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_COFADE');?></label></td>
						<td><select id="cofade" name="cofade" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="0"><?php echo WFText::_('WF_OPTION_NO');?></option>
								<option value="1"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="2"><?php echo WFText::_('WF_HSEXPAND_OPTION_NOT_IN_IE');?></option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="covpositionlabel" for="covposition" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_COVPOSITION_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_COVPOSITION');?></label></td>
						<td><select id="covposition" name="covposition" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="above"><?php echo WFText::_('WF_HSEXPAND_OPTION_ABOVE');?></option>
								<option value="top"><?php echo WFText::_('WF_OPTION_TOP');?></option>
								<option value="middle"><?php echo WFText::_('WF_HSEXPAND_OPTION_MIDDLE');?></option>
								<option value="bottom"><?php echo WFText::_('WF_OPTION_BOTTOM');?></option>
								<option value="below"><?php echo WFText::_('WF_HSEXPAND_OPTION_BELOW');?></option>
							</select>
						</td>
					</tr>
					<tr>
					<td class="column1"><label id="cohpositionlabel" for="cohposition" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_COHPOSITION_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_COHPOSITION');?></label></td>
						<td><select id="cohposition" name="cohposition" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="leftpanel"><?php echo WFText::_('WF_HSEXPAND_OPTION_LEFT_PANEL');?></option>
								<option value="left"><?php echo WFText::_('WF_OPTION_LEFT');?></option>
								<option value="center"><?php echo WFText::_('WF_OPTION_CENTER');?></option>
								<option value="right"><?php echo WFText::_('WF_OPTION_RIGHT');?></option>
								<option value="rightpanel"><?php echo WFText::_('WF_HSEXPAND_OPTION_RIGHT_PANEL');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="cooffsetxlabel" for="cooffsetx" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_COOFFSETX_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_COOFFSETX');?></label></td>
						<td><input type="text" id="cooffsetx" value="" /></td>
					</tr>
					<tr>
						<td><label id="cooffsetylabel" for="cooffsety" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_COOFFSETY_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_COOFFSETY');?></label></td>
						<td><input type="text" id="cooffsety" value="" /></td>
					</tr>
					<tr>
					<td class="column1"><label id="corelativetolabel" for="corelativeto" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_CORELATIVETO_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_CORELATIVETO');?></label></td>
						<td><select id="corelativeto" name="corelativeto" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="viewport"><?php echo WFText::_('WF_HSEXPAND_OPTION_VIEWPORT');?></option>
								<option value="expander"><?php echo WFText::_('WF_HSEXPAND_OPTION_EXPANDER');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="cohideonmouseoutlabel" for="cohideonmouseout" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_COHIDEONMOUSEOUT_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_COHIDEONMOUSEOUT');?></label></td>
						<td><select id="cohideonmouseout" name="cohideonmouseout" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="true"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="false"><?php echo WFText::_('WF_OPTION_NO');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="coopacitylabel" for="coopacity" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_COOPACITY_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_COOPACITY');?></label></td>
						<td><input type="text" id="coopacity" value="" /></td>
					</tr>
					<tr>
						<td><label id="cowidthlabel" for="cowidth" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_COWIDTH_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_COWIDTH');?></label></td>
						<td><input type="text" id="cowidth" value="" /></td>
					</tr>
					<tr>
						<td><label id="coclassnamelabel" for="coclassname" class="hastip" title="<?php echo WFText::_('WF_HSEXPAND_LABEL_COCLASSNAME_DESC');?>"><?php echo WFText::_('WF_HSEXPAND_LABEL_COCLASSNAME');?></label></td>
						<td><input type="text" id="coclassname" value="" /></td>
					</tr>
				</table>
			</fieldset>