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
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
// path to images directory
$path		= JPATH_ROOT.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR."content".DIRECTORY_SEPARATOR."highslide".DIRECTORY_SEPARATOR."graphics".DIRECTORY_SEPARATOR."outlines";
$filter		= ".png";
$files		= JFolder::files($path, $filter);

$outlineoptions = "";

if ( is_array($files) )
{
	foreach ($files as $file)
	{
		$file = JFile::stripExt( $file );
		$outlineoptions .= '<option value="'.$file.'">'.$file."</option>\n";
	}
}
?>
			<fieldset>
				<legend><?php echo WFText::_('WF_HSHTMLEXPAND_LEGEND_INLINEOPTS');?></legend>
				<table border="0" cellpadding="0" cellspacing="4">
					<tr>
						<td><label id="wrapperclasslabel" for="wrapperclass" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_WRAPPERCLASS_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_WRAPPERCLASS');?></label></td>
						<td><input type="text" id="wrapperclass" value="" /></td>
					</tr>
					<tr>
						<td><label id="slideshowgrouplabel" for="slideshowgroup" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SLIDESHOWGROUP_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SLIDESHOWGROUP');?></label></td>
						<td><input type="text" id="slideshowgroup" value="" /></td>
					</tr>
					<tr>
						<td><label id="thumbnailidlabel" for="thumbnailid" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_THUMBNAILID_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_THUMBNAILID');?></label></td>
						<td><input type="text" id="thumbnailid" value="" /></td>
					</tr>
					<tr>
						<td><label id="targetxlabel" for="targetx" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_TARGETX_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_TARGETX');?></label></td>
						<td><input type="text" id="targetx" value="" />
						<label id="targetylabel" for="targety" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_TARGETY_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_TARGETY');?></label>
						<input type="text" id="targety" value="" /></td>
					</tr>
					<tr>
						<td><label id="minwidthlabel" for="minwidth" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_MINWIDTH_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_MINWIDTH');?></label></td>
						<td><input type="text" id="minwidth" value="" />
						<label id="minheightlabel" for="minheight" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_MINHEIGHT_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_MINHEIGHT');?></label>
						<input type="text" id="minheight" value=""/></td>
					</tr>
					<tr>
						<td class="column1"><label id="outlinetypelabel" for="outlinetype" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OUTLINETYPE_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OUTLINETYPE');?></label></td>
						<td><select id="outlinetype" name="outlinetype" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<?php echo $outlineoptions; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="outlinewhileanimatinglabel" for="outlinewhileanimating" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OUTLINEWHILEANIMATING_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OUTLINEWHILEANIMATING');?></label></td>
						<td><select id="outlinewhileanimating" name="outlinewhileanimating" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="true"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="false"><?php echo WFText::_('WF_OPTION_NO');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="allowsizereductionlabel" for="allowsizereduction" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_ALLOWSIZEREDUCTION_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_ALLOWSIZEREDUCTION');?></label></td>
						<td><select id="allowsizereduction" name="allowsizereduction" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="true"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="false"><?php echo WFText::_('WF_OPTION_NO');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="alignlabel" for="align" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_ALIGN_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_ALIGN');?></label></td>
						<td><select id="align" name="align" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="auto"><?php echo WFText::_('WF_OPTION_AUTO');?></option>
								<option value="center"><?php echo WFText::_('WF_OPTION_CENTER');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="anchorlabel" for="anchor" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_ANCHOR_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_ANCHOR');?></label></td>
						<td><select id="anchor" name="anchor" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="auto"><?php echo WFText::_('WF_OPTION_AUTO');?></option>
								<option value="top"><?php echo WFText::_('WF_OPTION_TOP');?></option>
								<option value="top right"><?php echo WFText::_('WF_OPTION_TOP_RIGHT');?></option>
								<option value="top left"><?php echo WFText::_('WF_OPTION_TOP_LEFT');?></option>
								<option value="bottom"><?php echo WFText::_('WF_OPTION_BOTTOM');?></option>
								<option value="bottom right"><?php echo WFText::_('WF_OPTION_BOTTOM_RIGHT');?></option>
								<option value="bottom left"><?php echo WFText::_('WF_OPTION_BOTTOM_LEFT');?></option>
								<option value="right"><?php echo WFText::_('WF_OPTION_RIGHT');?></option>
								<option value="left"><?php echo WFText::_('WF_OPTION_LEFT');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="easinglabel" for="easing" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_EASING_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_EASING');?></label></td>
						<td><select id="easing" name="easing" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="easeInQuad">EaseInQuad</option>
								<option value="linearTween">LinearTween</option>
								<option value="easeOutQuad">EaseOutQuad</option>
								<option value="easeInOutQuad">EaseInOutQuad</option>
								<option value="easeInCubic">EaseInCubic</option>
								<option value="easeOutCubic">EaseOutCubic</option>
								<option value="easeInOutCubic">EaseInOutCubic</option>
								<option value="easeInQuart">EaseInQuart</option>
								<option value="easeOutQuart">EaseOutQuart</option>
								<option value="easeInOutQuart">EaseInOutQuart</option>
								<option value="easeInQuint">EaseInQuint</option>
								<option value="easeOutQuint">EaseOutQuint</option>
								<option value="easeInOutQuint">EaseInOutQuint</option>
								<option value="easeInSine">EaseInSine</option>
								<option value="easeOutSine">EaseOutSine</option>
								<option value="easeInOutSine">EaseInOutSine</option>
								<option value="easeInExpo">EaseInExpo</option>
								<option value="easeOutExpo">EaseOutExpo</option>
								<option value="easeInOutExpo">EaseInOutExpo</option>
								<option value="easeInCirc">EaseInCirc</option>
								<option value="easeOutCirc">EaseOutCirc</option>
								<option value="easeInOutCirc">EaseInOutCirc</option>
								<option value="easeInElastic">EaseInElastic</option>
								<option value="easeOutElastic">EaseOutElastic</option>
								<option value="easeInOutElastic">EaseInOutElastic</option>
								<option value="easeInBack">EaseInBack</option>
								<option value="easeOutBack">EaseOutBack</option>
								<option value="easeInOutBack">EaseInOutBack</option>
								<option value="easeInBounce">EaseInBounce</option>
								<option value="easeOutBounce">EaseOutBounce</option>
								<option value="easeInOutBounce">EaseInOutBounce</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="easingcloselabel" for="easingclose" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_EASINGCLOSE_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_EASINGCLOSE');?></label></td>
						<td><select id="easingclose" name="easingclose" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="easeInQuad">EaseInQuad</option>
								<option value="linearTween">LinearTween</option>
								<option value="easeOutQuad">EaseOutQuad</option>
								<option value="easeInOutQuad">EaseInOutQuad</option>
								<option value="easeInCubic">EaseInCubic</option>
								<option value="easeOutCubic">EaseOutCubic</option>
								<option value="easeInOutCubic">EaseInOutCubic</option>
								<option value="easeInQuart">EaseInQuart</option>
								<option value="easeOutQuart">EaseOutQuart</option>
								<option value="easeInOutQuart">EaseInOutQuart</option>
								<option value="easeInQuint">EaseInQuint</option>
								<option value="easeOutQuint">EaseOutQuint</option>
								<option value="easeInOutQuint">EaseInOutQuint</option>
								<option value="easeInSine">EaseInSine</option>
								<option value="easeOutSine">EaseOutSine</option>
								<option value="easeInOutSine">EaseInOutSine</option>
								<option value="easeInExpo">EaseInExpo</option>
								<option value="easeOutExpo">EaseOutExpo</option>
								<option value="easeInOutExpo">EaseInOutExpo</option>
								<option value="easeInCirc">EaseInCirc</option>
								<option value="easeOutCirc">EaseOutCirc</option>
								<option value="easeInOutCirc">EaseInOutCirc</option>
								<option value="easeInElastic">EaseInElastic</option>
								<option value="easeOutElastic">EaseOutElastic</option>
								<option value="easeInOutElastic">EaseInOutElastic</option>
								<option value="easeInBack">EaseInBack</option>
								<option value="easeOutBack">EaseOutBack</option>
								<option value="easeInOutBack">EaseInOutBack</option>
								<option value="easeInBounce">EaseInBounce</option>
								<option value="easeOutBounce">EaseOutBounce</option>
								<option value="easeInOutBounce">EaseInOutBounce</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="fadeinoutlabel" for="fadeinout" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_FADEINOUT_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_FADEINOUT');?></label></td>
						<td><select id="fadeinout" name="fadeinout" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="true"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="false"><?php echo WFText::_('WF_OPTION_NO');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="column1"><label id="openonhoverlabel" for="openonhover" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OPENONHOVER_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_OPENONHOVER');?></label></td>
						<td><input class="checkbox" type="checkbox" id="openonhover" name="openonhover"/></td>
					</tr>
					<tr>
						<td class="column1"><label id="dragbyheadinglabel" for="dragbyheading" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_DRAGBYHEADING_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_DRAGBYHEADING');?></label></td>
						<td><select id="dragbyheading" name="dragbyheading">
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="true"><?php echo WFText::_('WF_OPTION_YES');?></option>
								<option value="false"><?php echo WFText::_('WF_OPTION_NO');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="dimmingopacitylabel" for="dimmingopacity" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_DIMMINGOPACITY_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_DIMMINGOPACITY');?></label></td>
						<td><input type="text" id="dimmingopacity" value="" /></td>
					</tr>
					<tr>
						<td class="column1"><label id="numberpositionlabel" for="numberposition" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_NUMBERPOSITION_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_NUMBERPOSITION');?></label></td>
						<td><select id="numberposition" name="numberposition" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
								<option value="caption"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_CAPTION');?></option>
								<option value="heading"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_HEADING');?></option>
								<option value="null"><?php echo WFText::_('WF_OPTION_NONE');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="nowrap"><label id="psrclabel" for="psrc" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SRC_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_SRC');?></label></td>
						<td><input id="psrc" type="text" value="" size="150" class="required browser"/></td>
 					</tr>
					<tr>
					<td class="column1"><label id="crvpositionlabel" for="crvposition" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_CRVPOSITION_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_CRVPOSITION');?></label></td>
						<td><select id="crvposition" name="crvposition" >
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
					<td class="column1"><label id="crhpositionlabel" for="crhposition" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_CRHPOSITION_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_CRHPOSITION');?></label></td>
						<td><select id="crhposition" name="crhposition" >
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
					<td class="column1"><label id="transitionslabel" for="transitions" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_TRANSITIONS_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_TRANSITIONS');?></label></td>
						<td><select id="transitions" name="transitions" >
								<option value=""><?php echo WFText::_('WF_OPTION_NOT_SET');?></option>
            					<option value="'fade'"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_FADE');?></option>
								<option value="'fade', 'crossfade'"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_FADE_CROSSFADE');?></option>
            					<option value="'fade', 'expand'"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_FADE_EXPAND');?></option>
<!--            				<option value="'crossfade'">Crossfade</option> -->
<!--            				<option value="'crossfade', 'fade'">Crossfade, Fade</option> -->
<!--            				<option value="'crossfade', 'expand'">Crossfade, Expand</option> -->
            					<option value="'expand'"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_EXPAND');?></option>
            					<option value="'expand', 'fade'"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_EXPAND_FADE');?></option>
            					<option value="'expand', 'crossfade'"><?php echo WFText::_('WF_HSHTMLEXPAND_OPTION_EXPAND_CROSSFADE');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label id="captiontextlabel" for="captiontext" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_CAPTIONTEXT_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_CAPTIONTEXT');?></label></td>
						<td><input type="text" id="captiontext" value="" /></td>
					</tr>
					<tr>
						<td><label id="headingtextlabel" for="headingtext" class="hastip" title="<?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HEADINGTEXT_DESC');?>"><?php echo WFText::_('WF_HSHTMLEXPAND_LABEL_HEADINGTEXT');?></label></td>
						<td><input type="text" id="headingtext" value="" /></td>
					</tr>
				</table>
			</fieldset>
