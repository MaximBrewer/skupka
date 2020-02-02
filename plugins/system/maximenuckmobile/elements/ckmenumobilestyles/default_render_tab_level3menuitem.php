<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;
?>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="level3menuitemheight"><?php echo JText::_('CK_HEIGHT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/height.png" />
	<input type="text" id="level3menuitemheight" name="level3menuitemheight" class="hasTip level3menuitem" title="<?php echo JText::_('CK_HEIGHT_DESC'); ?>"/>
</div>
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="level3menuitembgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level3menuitembgcolor1" name="level3menuitembgcolor1" class="hasTip level3menuitem <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level3menuitembgcolor2" name="level3menuitembgcolor2" class="hasTip level3menuitem <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level3menuitembgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="level3menuitembgopacity" name="level3menuitembgopacity" class="hasTip level3menuitem" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="level3menuitembgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level3menuitembgimage" name="level3menuitembgimage" class="hasTip level3menuitem" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level3menuitembgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level3menuitembgimage" rel="{handler: 'iframe', size: {x: 800, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('').trigger('change');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level3menuitembgpositionx" name="level3menuitembgpositionx" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level3menuitembgpositiony" name="level3menuitembgpositiony" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level3menuitembgimagerepeatrepeat" name="level3menuitembgimagerepeat" class="level3menuitem" />
	<label class="radiobutton first" for="level3menuitembgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level3menuitem" type="radio" value="repeat-x" id="level3menuitembgimagerepeatrepeat-x" name="level3menuitembgimagerepeat" />
	<label class="radiobutton"  for="level3menuitembgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level3menuitem" type="radio" value="repeat-y" id="level3menuitembgimagerepeatrepeat-y" name="level3menuitembgimagerepeat" />
	<label class="radiobutton last"  for="level3menuitembgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level3menuitem" type="radio" value="no-repeat" id="level3menuitembgimagerepeatno-repeat" name="level3menuitembgimagerepeat" />
	<label class="radiobutton last"  for="level3menuitembgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<div class="ckrow">
	<label for="level3menuitemmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemmargintop" name="level3menuitemmargintop" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemmarginright" name="level3menuitemmarginright" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemmarginbottom" name="level3menuitemmarginbottom" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemmarginleft" name="level3menuitemmarginleft" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level3menuitempaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="level3menuitempaddingtop" name="level3menuitempaddingtop" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="level3menuitempaddingright" name="level3menuitempaddingright" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="level3menuitempaddingbottom" name="level3menuitempaddingbottom" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="level3menuitempaddingleft" name="level3menuitempaddingleft" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level3menuitembordercolor"><?php echo JText::_('CK_BORDERCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level3menuitembordercolor" name="level3menuitembordercolor" class="level3menuitem <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_borders.png" /></span><span style="width:30px;"><input type="text" id="level3menuitembordertopwidth" name="level3menuitembordertopwidth" class="level3menuitem hasTip" style="width:30px;border-top-color:#237CA4;" title="<?php echo JText::_('CK_BORDERTOPWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level3menuitemborderrightwidth" name="level3menuitemborderrightwidth" class="level3menuitem hasTip" style="width:30px;border-right-color:#237CA4;" title="<?php echo JText::_('CK_BORDERRIGHTWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level3menuitemborderbottomwidth" name="level3menuitemborderbottomwidth" class="level3menuitem hasTip" style="width:30px;border-bottom-color:#237CA4;" title="<?php echo JText::_('CK_BORDERBOTTOMWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level3menuitemborderleftwidth" name="level3menuitemborderleftwidth" class="level3menuitem hasTip" style="width:30px;border-left-color:#237CA4;" title="<?php echo JText::_('CK_BORDERLEFTWIDTH_DESC'); ?>" /></span>
	<span>
		<select id="level3menuitemborderstyle" name="level3menuitemborderstyle" class="level3menuitem hasTip" style="width: 70px; border-radius: 0px;">
			<option value="solid">solid</option>
			<option value="dotted">dotted</option>
			<option value="dashed">dashed</option>
		</select>
	</span>
</div>
<div class="ckrow">
	<label for="level3menuitemroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemroundedcornerstl" name="level3menuitemroundedcornerstl" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemroundedcornerstr" name="level3menuitemroundedcornerstr" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemroundedcornersbr" name="level3menuitemroundedcornersbr" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemroundedcornersbl" name="level3menuitemroundedcornersbl" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level3menuitemshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level3menuitemshadowcolor" name="level3menuitemshadowcolor" class="level3menuitem <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemshadowblur" name="level3menuitemshadowblur" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemshadowspread" name="level3menuitemshadowspread" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemshadowoffsetx" name="level3menuitemshadowoffsetx" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level3menuitemshadowoffsety" name="level3menuitemshadowoffsety" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton level3menuitem" type="radio" value="0" id="level3menuitemshadowinsetno" name="level3menuitemshadowinset" />
	<label class="radiobutton last"  for="level3menuitemshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton level3menuitem" type="radio" value="1" id="level3menuitemshadowinsetyes" name="level3menuitemshadowinset" />
	<label class="radiobutton last"  for="level3menuitemshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
<div class="ckrow">
	<label for="level3menuitemfontsize"><?php echo JText::_('CK_TEXT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level3menuitemfontsize" name="level3menuitemfontsize" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level3menuitemfontcolor" name="level3menuitemfontcolor" class="level3menuitem hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_align_middle.png" />
	<input type="text" id="level3menuitemlineheight" name="level3menuitemlineheight" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_LINEHEIGHT_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/text_padding_left.png" />
	<input type="text" id="level3menuitemtextindent" name="level3menuitemtextindent" class="level3menuitem hasTip" style="width:30px;" title="<?php echo JText::_('CK_TEXTINDENT_DESC'); ?>" />
</div>
<div class="ckrow">
	<label for="level3menuitemfontfamily"><?php echo JText::_('CK_FONTSTYLE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/font_add.png" />
	<input type="text" id="level3menuitemfontfamily" name="level3menuitemfontfamily" class="level3menuitem hasTip" onchange="clean_gfont_name(this)" title="<?php echo JText::_('CK_GFONT_DESC'); ?>" />
	<input class="radiobutton level3menuitem" type="radio" value="left" id="level3menuitemtextalignleft" name="level3menuitemtextalign" />
	<label class="radiobutton first" for="level3menuitemtextalignleft"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_left.png" />
	</label><input class="radiobutton level3menuitem" type="radio" value="center" id="level3menuitemtextaligncenter" name="level3menuitemtextalign" />
	<label class="radiobutton"  for="level3menuitemtextaligncenter"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_center.png" />
	</label><input class="radiobutton level3menuitem" type="radio" value="right" id="level3menuitemtextalignright" name="level3menuitemtextalign" />
	<label class="radiobutton last"  for="level3menuitemtextalignright"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_right.png" /></label>
	<span class="vertical_separator"></span>
	<input class="radiobutton level3menuitem" type="radio" value="lowercase" id="level3menuitemtexttransformlowercase" name="level3menuitemtexttransform" />
	<label class="radiobutton first hasTip" title="<?php echo JText::_('CK_LOWERCASE'); ?>" for="level3menuitemtexttransformlowercase"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_lowercase.png" />
	</label><input class="radiobutton level3menuitem" type="radio" value="uppercase" id="level3menuitemtexttransformuppercase" name="level3menuitemtexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_UPPERCASE'); ?>" for="level3menuitemtexttransformuppercase"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_uppercase.png" />
	</label><input class="radiobutton level3menuitem" type="radio" value="capitalize" id="level3menuitemtexttransformcapitalize" name="level3menuitemtexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_CAPITALIZE'); ?>" for="level3menuitemtexttransformcapitalize"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_capitalize.png" />
	</label><input class="radiobutton level3menuitem" type="radio" value="default" id="level3menuitemtexttransformdefault" name="level3menuitemtexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_DEFAULT'); ?>" for="level3menuitemtexttransformdefault"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_default.png" />
	</label>
</div>
<div class="ckrow">
	<label for="level3menuitemfontweightbold"></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/text_bold.png" />
	<input class="radiobutton level3menuitem" type="radio" value="bold" id="level3menuitemfontweightbold" name="level3menuitemfontweight" />
	<label class="radiobutton first hasTip" title="" for="level3menuitemfontweightbold" style="width:auto;"><?php echo JText::_('CK_BOLD'); ?>
	</label><input class="radiobutton level3menuitem" type="radio" value="normal" id="level3menuitemfontweightnormal" name="level3menuitemfontweight" />
	<label class="radiobutton hasTip" title="" for="level3menuitemfontweightnormal" style="width:auto;"><?php echo JText::_('CK_NORMAL'); ?>
	</label>
</div>
