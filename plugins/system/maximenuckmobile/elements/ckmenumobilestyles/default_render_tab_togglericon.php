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
<div class="ckrow">
	<label for="togglericoncontentclosed"><?php echo JText::_('CK_TOGGLE_CLOSED_ICON_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style_edit.png" />
	<input class="radiobutton togglericon" type="radio" value="+" id="togglericoncontentclosedplus" name="togglericoncontentclosed" />
	<label class="radiobutton"  for="togglericoncontentclosedplus" style="width:20px;font-size:24px;">+
	</label><input class="radiobutton togglericon customtextswitcher" type="radio" value="custom" id="togglericoncontentclosedcustom" name="togglericoncontentclosed" />
	<label class="radiobutton"  for="togglericoncontentclosedcustom" style="width:auto;"><?php echo JText::_('CK_CUSTOM_TEXT'); ?></label>
	<input class="togglericon" type="text" value="" id="togglericoncontentclosedcustomtext" name="togglericoncontentclosedcustomtext" />
	<input class="radiobutton togglericon" type="radio" value="" id="togglericoncontentclosednone" name="togglericoncontentclosed" />
	<label class="radiobutton"  for="togglericoncontentclosednone" style="width:auto;"><?php echo JText::_('CK_NONE'); ?></label>
</div>
<div class="ckrow">
	<label for="togglericoncontentopened"><?php echo JText::_('CK_TOGGLE_OPENED_ICON_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style_edit.png" />
	<input class="radiobutton togglericon" type="radio" value="-" id="togglericoncontentopenedminus" name="togglericoncontentopened" />
	<label class="radiobutton"  for="togglericoncontentopenedminus" style="width:20px;font-size:24px;">-
	</label><input class="radiobutton togglericon customtextswitcher" type="radio" value="custom" id="togglericoncontentopenedcustom" name="togglericoncontentopened" />
	<label class="radiobutton"  for="togglericoncontentopenedcustom" style="width:auto;"><?php echo JText::_('CK_CUSTOM_TEXT'); ?></label>
	<input class="togglericon customtextvalue" type="text" value="" id="togglericoncontentopenedcustomtext" name="togglericoncontentopenedcustomtext" />
	<input class="radiobutton togglericon" type="radio" value="" id="togglericoncontentopenednone" name="togglericoncontentopened" />
	<label class="radiobutton"  for="togglericoncontentopenednone" style="width:auto;"><?php echo JText::_('CK_NONE'); ?></label>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="togglericonheight"><?php echo JText::_('CK_HEIGHT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/height.png" />
	<input type="text" id="togglericonheight" name="togglericonheight" class="hasTip togglericon" title="<?php echo JText::_('CK_HEIGHT_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/width.png" />
	<input type="text" id="togglericonwidth" name="togglericonwidth" class="hasTip togglericon" title="<?php echo JText::_('CK_WIDTH_DESC'); ?>"/>
</div>
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="togglericonbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="togglericonbgcolor1" name="togglericonbgcolor1" class="hasTip togglericon <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="togglericonbgcolor2" name="togglericonbgcolor2" class="hasTip togglericon <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'togglericonbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="togglericonbgopacity" name="togglericonbgopacity" class="hasTip togglericon" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="togglericonbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="togglericonbgimage" name="togglericonbgimage" class="hasTip togglericon" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'togglericonbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=togglericonbgimage" rel="{handler: 'iframe', size: {x: 800, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('').trigger('change');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="togglericonbgpositionx" name="togglericonbgpositionx" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="togglericonbgpositiony" name="togglericonbgpositiony" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="togglericonbgimagerepeatrepeat" name="togglericonbgimagerepeat" class="togglericon" />
	<label class="radiobutton first" for="togglericonbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton togglericon" type="radio" value="repeat-x" id="togglericonbgimagerepeatrepeat-x" name="togglericonbgimagerepeat" />
	<label class="radiobutton"  for="togglericonbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton togglericon" type="radio" value="repeat-y" id="togglericonbgimagerepeatrepeat-y" name="togglericonbgimagerepeat" />
	<label class="radiobutton last"  for="togglericonbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton togglericon" type="radio" value="no-repeat" id="togglericonbgimagerepeatno-repeat" name="togglericonbgimagerepeat" />
	<label class="radiobutton last"  for="togglericonbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<div class="ckrow">
	<label for="togglericonmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="togglericonmargintop" name="togglericonmargintop" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="togglericonmarginright" name="togglericonmarginright" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="togglericonmarginbottom" name="togglericonmarginbottom" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="togglericonmarginleft" name="togglericonmarginleft" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="togglericonpaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="togglericonpaddingtop" name="togglericonpaddingtop" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="togglericonpaddingright" name="togglericonpaddingright" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="togglericonpaddingbottom" name="togglericonpaddingbottom" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="togglericonpaddingleft" name="togglericonpaddingleft" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="togglericonbordercolor"><?php echo JText::_('CK_BORDERCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="togglericonbordercolor" name="togglericonbordercolor" class="togglericon <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_borders.png" /></span><span style="width:30px;"><input type="text" id="togglericonbordertopwidth" name="togglericonbordertopwidth" class="togglericon hasTip" style="width:30px;border-top-color:#237CA4;" title="<?php echo JText::_('CK_BORDERTOPWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="togglericonborderrightwidth" name="togglericonborderrightwidth" class="togglericon hasTip" style="width:30px;border-right-color:#237CA4;" title="<?php echo JText::_('CK_BORDERRIGHTWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="togglericonborderbottomwidth" name="togglericonborderbottomwidth" class="togglericon hasTip" style="width:30px;border-bottom-color:#237CA4;" title="<?php echo JText::_('CK_BORDERBOTTOMWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="togglericonborderleftwidth" name="togglericonborderleftwidth" class="togglericon hasTip" style="width:30px;border-left-color:#237CA4;" title="<?php echo JText::_('CK_BORDERLEFTWIDTH_DESC'); ?>" /></span>
	<span>
		<select id="togglericonborderstyle" name="togglericonborderstyle" class="togglericon hasTip" style="width: 70px; border-radius: 0px;">
			<option value="solid">solid</option>
			<option value="dotted">dotted</option>
			<option value="dashed">dashed</option>
		</select>
	</span>
</div>
<div class="ckrow">
	<label for="togglericonroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="togglericonroundedcornerstl" name="togglericonroundedcornerstl" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="togglericonroundedcornerstr" name="togglericonroundedcornerstr" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="togglericonroundedcornersbr" name="togglericonroundedcornersbr" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="togglericonroundedcornersbl" name="togglericonroundedcornersbl" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="togglericonshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="togglericonshadowcolor" name="togglericonshadowcolor" class="togglericon <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="togglericonshadowblur" name="togglericonshadowblur" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="togglericonshadowspread" name="togglericonshadowspread" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="togglericonshadowoffsetx" name="togglericonshadowoffsetx" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="togglericonshadowoffsety" name="togglericonshadowoffsety" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton togglericon" type="radio" value="0" id="togglericonshadowinsetno" name="togglericonshadowinset" />
	<label class="radiobutton last"  for="togglericonshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton togglericon" type="radio" value="1" id="togglericonshadowinsetyes" name="togglericonshadowinset" />
	<label class="radiobutton last"  for="togglericonshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
<div class="ckrow">
	<label for="togglericonfontsize"><?php echo JText::_('CK_TEXT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="togglericonfontsize" name="togglericonfontsize" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="togglericonfontcolor" name="togglericonfontcolor" class="togglericon hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_align_middle.png" />
	<input type="text" id="togglericonlineheight" name="togglericonlineheight" class="togglericon hasTip" style="width:30px;" title="<?php echo JText::_('CK_LINEHEIGHT_DESC'); ?>" />
</div>
<div class="ckrow">
	<label for="togglericonfontfamily"><?php echo JText::_('CK_FONTSTYLE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/font_add.png" />
	<input type="text" id="togglericonfontfamily" name="togglericonfontfamily" class="togglericon hasTip" onchange="clean_gfont_name(this)" title="<?php echo JText::_('CK_GFONT_DESC'); ?>" />
	<input class="radiobutton togglericon" type="radio" value="left" id="togglericontextalignleft" name="togglericontextalign" />
	<label class="radiobutton first" for="togglericontextalignleft"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_left.png" />
	</label><input class="radiobutton togglericon" type="radio" value="center" id="togglericontextaligncenter" name="togglericontextalign" />
	<label class="radiobutton"  for="togglericontextaligncenter"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_center.png" />
	</label><input class="radiobutton togglericon" type="radio" value="right" id="togglericontextalignright" name="togglericontextalign" />
	<label class="radiobutton last"  for="togglericontextalignright"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_right.png" /></label>
	<span class="vertical_separator"></span>
	<input class="radiobutton togglericon" type="radio" value="lowercase" id="togglericontexttransformlowercase" name="togglericontexttransform" />
	<label class="radiobutton first hasTip" title="<?php echo JText::_('CK_LOWERCASE'); ?>" for="togglericontexttransformlowercase"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_lowercase.png" />
	</label><input class="radiobutton togglericon" type="radio" value="uppercase" id="togglericontexttransformuppercase" name="togglericontexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_UPPERCASE'); ?>" for="togglericontexttransformuppercase"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_uppercase.png" />
	</label><input class="radiobutton togglericon" type="radio" value="capitalize" id="togglericontexttransformcapitalize" name="togglericontexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_CAPITALIZE'); ?>" for="togglericontexttransformcapitalize"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_capitalize.png" />
	</label><input class="radiobutton togglericon" type="radio" value="default" id="togglericontexttransformdefault" name="togglericontexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_DEFAULT'); ?>" for="togglericontexttransformdefault"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_default.png" />
	</label>
</div>
<div class="ckrow">
	<label for="togglericonfontweightbold"></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/text_bold.png" />
	<input class="radiobutton togglericon" type="radio" value="bold" id="togglericonfontweightbold" name="togglericonfontweight" />
	<label class="radiobutton first hasTip" title="" for="togglericonfontweightbold" style="width:auto;"><?php echo JText::_('CK_BOLD'); ?>
	</label><input class="radiobutton togglericon" type="radio" value="normal" id="togglericonfontweightnormal" name="togglericonfontweight" />
	<label class="radiobutton hasTip" title="" for="togglericonfontweightnormal" style="width:auto;"><?php echo JText::_('CK_NORMAL'); ?>
	</label>
</div>