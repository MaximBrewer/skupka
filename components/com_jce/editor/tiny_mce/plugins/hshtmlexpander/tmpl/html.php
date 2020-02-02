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
						<td class="column1"><label id="contentidlabel" for="contentid" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_CONTENTID_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_CONTENTID');?></label></td>
						<td><input id="contentid" type="text" value="" /></td>
					</tr>
					<tr>
						<td><label id="contentstylelabel" for="contentstyle" class="hastip" title="<?php echo WFText::_('WF_LABEL_STYLE_DESC');?>"><?php echo WFText::_('WF_LABEL_STYLE');?></label></td>
						<td><input type="text" id="contentstyle" value="" /></td>
					</tr>
					<tr>
						<td><label id="widthlabel" for="width" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_WIDTH_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_WIDTH');?></label></td>
						<td><input type="text" id="width" value="" /></td>
					</tr>
					<tr>
						<td><label id="heightlabel" for="height" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HEIGHT_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HEIGHT');?></label></td>
						<td><input type="text" id="height" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="allowwidthreductionlabel" for="allowwidthreduction" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_ALLOWWIDTHREDUCTION_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_ALLOWWIDTHREDUCTION');?></label></td>
						<td><select id="allowwidthreduction" name="allowwidthreduction" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="true"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="false"><?php echo WFText::_('WF_OPTION_NO');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="allowheightreductionlabel" for="allowheightreduction" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_ALLOWHEIGHTREDUCTION_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_ALLOWHEIGHTREDUCTION');?></label></td>
						<td><select id="allowheightreduction" name="allowheightreduction" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="true"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="false"><?php echo WFText::_('WF_OPTION_NO');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="preservecontentlabel" for="preservecontent" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_PRESERVECONTENT_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_PRESERVECONTENT');?></label></td>
						<td><select id="preservecontent" name="preservecontent" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="true"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="false"><?php echo WFText::_('WF_OPTION_NO');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="cacheajaxlabel" for="cacheajax" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_CACHEAJAX_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_CACHEAJAX');?></label></td>
						<td><select id="cacheajax" name="cacheajax" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="true"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="false"><?php echo WFText::_('WF_OPTION_NO');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="objectloadtimelabel" for="objectloadtime" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OBJECTLOADTIME_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OBJECTLOADTIME');?></label></td>
						<td><select id="objectloadtime" name="objectloadtime" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="before"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_BEFORE');?></option>
								<option value="after"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_AFTER');?></option>
							</select>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo WFText::_('WF_HSHTMLEXPAND_LEGEND_CONTENTTEXT');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><textarea id="content"  rows="20" cols="80" ></textarea></td>
					</tr>
				</table>
			</fieldset>
