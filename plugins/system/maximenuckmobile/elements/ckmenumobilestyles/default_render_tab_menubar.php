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
	<label for="menubarheight"><?php echo JText::_('CK_HEIGHT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/height.png" />
	<input type="text" id="menubarheight" name="menubarheight" class="hasTip menubar" title="<?php echo JText::_('CK_HEIGHT_DESC'); ?>"/>
</div>
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="menubarbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="menubarbgcolor1" name="menubarbgcolor1" class="hasTip menubar <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="menubarbgcolor2" name="menubarbgcolor2" class="hasTip menubar <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'menubarbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="menubarbgopacity" name="menubarbgopacity" class="hasTip menubar" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="menubarbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="menubarbgimage" name="menubarbgimage" class="hasTip menubar" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'menubarbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=menubarbgimage" rel="{handler: 'iframe', size: {x: 800, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('').trigger('change');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="menubarbgpositionx" name="menubarbgpositionx" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="menubarbgpositiony" name="menubarbgpositiony" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="menubarbgimagerepeatrepeat" name="menubarbgimagerepeat" class="menubar" />
	<label class="radiobutton first" for="menubarbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton menubar" type="radio" value="repeat-x" id="menubarbgimagerepeatrepeat-x" name="menubarbgimagerepeat" />
	<label class="radiobutton"  for="menubarbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton menubar" type="radio" value="repeat-y" id="menubarbgimagerepeatrepeat-y" name="menubarbgimagerepeat" />
	<label class="radiobutton last"  for="menubarbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton menubar" type="radio" value="no-repeat" id="menubarbgimagerepeatno-repeat" name="menubarbgimagerepeat" />
	<label class="radiobutton last"  for="menubarbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<div class="ckrow">
	<label for="menubarmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="menubarmargintop" name="menubarmargintop" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="menubarmarginright" name="menubarmarginright" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="menubarmarginbottom" name="menubarmarginbottom" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="menubarmarginleft" name="menubarmarginleft" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="menubarpaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="menubarpaddingtop" name="menubarpaddingtop" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="menubarpaddingright" name="menubarpaddingright" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="menubarpaddingbottom" name="menubarpaddingbottom" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="menubarpaddingleft" name="menubarpaddingleft" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="menubarbordercolor"><?php echo JText::_('CK_BORDERCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="menubarbordercolor" name="menubarbordercolor" class="menubar <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_borders.png" /></span><span style="width:30px;"><input type="text" id="menubarbordertopwidth" name="menubarbordertopwidth" class="menubar hasTip" style="width:30px;border-top-color:#237CA4;" title="<?php echo JText::_('CK_BORDERTOPWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="menubarborderrightwidth" name="menubarborderrightwidth" class="menubar hasTip" style="width:30px;border-right-color:#237CA4;" title="<?php echo JText::_('CK_BORDERRIGHTWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="menubarborderbottomwidth" name="menubarborderbottomwidth" class="menubar hasTip" style="width:30px;border-bottom-color:#237CA4;" title="<?php echo JText::_('CK_BORDERBOTTOMWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="menubarborderleftwidth" name="menubarborderleftwidth" class="menubar hasTip" style="width:30px;border-left-color:#237CA4;" title="<?php echo JText::_('CK_BORDERLEFTWIDTH_DESC'); ?>" /></span>
	<span>
		<select id="menubarborderstyle" name="menubarborderstyle" class="menubar hasTip" style="width: 70px; border-radius: 0px;">
			<option value="solid">solid</option>
			<option value="dotted">dotted</option>
			<option value="dashed">dashed</option>
		</select>
	</span>
</div>
<div class="ckrow">
	<label for="menubarroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="menubarroundedcornerstl" name="menubarroundedcornerstl" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="menubarroundedcornerstr" name="menubarroundedcornerstr" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="menubarroundedcornersbr" name="menubarroundedcornersbr" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="menubarroundedcornersbl" name="menubarroundedcornersbl" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="menubarshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="menubarshadowcolor" name="menubarshadowcolor" class="menubar <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="menubarshadowblur" name="menubarshadowblur" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="menubarshadowspread" name="menubarshadowspread" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="menubarshadowoffsetx" name="menubarshadowoffsetx" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="menubarshadowoffsety" name="menubarshadowoffsety" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton menubar" type="radio" value="0" id="menubarshadowinsetno" name="menubarshadowinset" />
	<label class="radiobutton last"  for="menubarshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton menubar" type="radio" value="1" id="menubarshadowinsetyes" name="menubarshadowinset" />
	<label class="radiobutton last"  for="menubarshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
<div class="ckrow">
	<label for="menubarfontsize"><?php echo JText::_('CK_TEXT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="menubarfontsize" name="menubarfontsize" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="menubarfontcolor" name="menubarfontcolor" class="menubar hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_align_middle.png" />
	<input type="text" id="menubarlineheight" name="menubarlineheight" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_LINEHEIGHT_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/text_padding_left.png" />
	<input type="text" id="menubartextindent" name="menubartextindent" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_TEXTINDENT_DESC'); ?>" />
</div>
<div class="ckrow">
	<label for="menubarfontfamily"><?php echo JText::_('CK_FONTSTYLE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/font_add.png" />
	<input type="text" id="menubarfontfamily" name="menubarfontfamily" class="menubar hasTip" onchange="clean_gfont_name(this)" title="<?php echo JText::_('CK_GFONT_DESC'); ?>" />
	<input class="radiobutton menubar" type="radio" value="left" id="menubartextalignleft" name="menubartextalign" />
	<label class="radiobutton first" for="menubartextalignleft"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_left.png" />
	</label><input class="radiobutton menubar" type="radio" value="center" id="menubartextaligncenter" name="menubartextalign" />
	<label class="radiobutton"  for="menubartextaligncenter"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_center.png" />
	</label><input class="radiobutton menubar" type="radio" value="right" id="menubartextalignright" name="menubartextalign" />
	<label class="radiobutton last"  for="menubartextalignright"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_right.png" /></label>
	<span class="vertical_separator"></span>
	<input class="radiobutton menubar" type="radio" value="lowercase" id="menubartexttransformlowercase" name="menubartexttransform" />
	<label class="radiobutton first hasTip" title="<?php echo JText::_('CK_LOWERCASE'); ?>" for="menubartexttransformlowercase"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_lowercase.png" />
	</label><input class="radiobutton menubar" type="radio" value="uppercase" id="menubartexttransformuppercase" name="menubartexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_UPPERCASE'); ?>" for="menubartexttransformuppercase"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_uppercase.png" />
	</label><input class="radiobutton menubar" type="radio" value="capitalize" id="menubartexttransformcapitalize" name="menubartexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_CAPITALIZE'); ?>" for="menubartexttransformcapitalize"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_capitalize.png" />
	</label><input class="radiobutton menubar" type="radio" value="default" id="menubartexttransformdefault" name="menubartexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_DEFAULT'); ?>" for="menubartexttransformdefault"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_default.png" />
	</label>
</div>
<div class="ckrow">
	<label for="menubarfontweightbold"></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/text_bold.png" />
	<input class="radiobutton menubar" type="radio" value="bold" id="menubarfontweightbold" name="menubarfontweight" />
	<label class="radiobutton first hasTip" title="" for="menubarfontweightbold" style="width:auto;"><?php echo JText::_('CK_BOLD'); ?>
	</label><input class="radiobutton menubar" type="radio" value="normal" id="menubarfontweightnormal" name="menubarfontweight" />
	<label class="radiobutton hasTip" title="" for="menubarfontweightnormal" style="width:auto;"><?php echo JText::_('CK_NORMAL'); ?>
	</label>
</div>
