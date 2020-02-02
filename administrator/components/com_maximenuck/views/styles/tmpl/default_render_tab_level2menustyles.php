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
	<div id="level2menustyles_iilustration">
		<img src="<?php echo $this->imagespath ?>/images/menu_illustration.png" />
	</div>
</div>
<div class="ckrow">
	<label for="menustylessubmenuheight"><?php echo JText::_('CK_SUBMENU_SETTINGS'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/height.png" /></span><span style="width:30px;"><input type="text" id="menustylessubmenuheight" name="menustylessubmenuheight" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SUBMENUHEIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/width.png" /></span><span style="width:30px;"><input type="text" id="menustylessubmenuwidth" name="menustylessubmenuwidth" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SUBMENUWIDTH_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="menustylessubmenu1marginleft" name="menustylessubmenu1marginleft" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SUBMENUMARGINLEFT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="menustylessubmenu1margintop" name="menustylessubmenu1margintop" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SUBMENUMARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="menustylessubmenu2marginleft" name="menustylessubmenu2marginleft" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SUBSUBMENUMARGINLEFT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="menustylessubmenu2margintop" name="menustylessubmenu2margintop" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SUBSUBMENUMARGINTOP_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2menustylespaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="level2menustylespaddingtop" name="level2menustylespaddingtop" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="level2menustylespaddingright" name="level2menustylespaddingright" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2menustylespaddingbottom" name="level2menustylespaddingbottom" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="level2menustylespaddingleft" name="level2menustylespaddingleft" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
<div class="ckrow">
	<label for="level2itemnormalstylestextgfont"><?php echo JText::_('CK_FONTSTYLE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/font_add.png" />
	<input type="text" id="level2itemnormalstylestextgfont" name="level2itemnormalstylestextgfont" class="level2itemnormalstyles hasTip" onchange="clean_gfont_name(this);" title="<?php echo JText::_('CK_GFONT_DESC'); ?>" />
	<input type="hidden" id="level2itemnormalstylestextisgfont" name="level2itemnormalstylestextisgfont" class="isgfont level2itemnormalstyles" />
	<input class="radiobutton" type="radio" id="level2menustylesalignmentleft" name="level2menustylesalignment" class="level2menustyles" />
	<label class="radiobutton first" for="level2menustylesalignmentleft"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_left.png" />
	</label><input class="radiobutton" type="radio" id="level2menustylesalignmentcenter" name="level2menustylesalignment" class="level2menustyles" />
	<label class="radiobutton"  for="level2menustylesalignmentcenter"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_center.png" />
	</label><input class="radiobutton" type="radio" id="level2menustylesalignmentright" name="level2menustylesalignment" class="level2menustyles" />
	<label class="radiobutton last"  for="level2menustylesalignmentright"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_right.png" /></label>
	<span class="vertical_separator"></span>
	<input class="radiobutton level2itemnormalstyles" type="radio" value="lowercase" id="level2itemnormalstylestexttransformlowercase" name="level2itemnormalstylestexttransform" />
	<label class="radiobutton first hasTip" for="level2itemnormalstylestexttransformlowercase" title="<?php echo JText::_('CK_LOWERCASE'); ?>"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_lowercase.png" />
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="uppercase" id="level2itemnormalstylestexttransformuppercase" name="level2itemnormalstylestexttransform" />
	<label class="radiobutton hasTip" for="level2itemnormalstylestexttransformuppercase" title="<?php echo JText::_('CK_UPPERCASE'); ?>"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_uppercase.png" />
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="capitalize" id="level2itemnormalstylestexttransformcapitalize" name="level2itemnormalstylestexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_CAPITALIZE'); ?>" for="level2itemnormalstylestexttransformcapitalize"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_capitalize.png" />
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="default" id="level2itemnormalstylestexttransformdefault" name="level2itemnormalstylestexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_DEFAULT'); ?>" for="level2itemnormalstylestexttransformdefault"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_default.png" />
	</label>
</div>
<div class="ckrow">
	<label for="level2itemnormalstylesfontweightbold"></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/text_bold.png" />
	<input class="radiobutton level2itemnormalstyles" type="radio" value="bold" id="level2itemnormalstylesfontweightbold" name="level2itemnormalstylesfontweight" />
	<label class="radiobutton first hasTip" title="" for="level2itemnormalstylesfontweightbold" style="width:auto;"><?php echo JText::_('CK_BOLD'); ?>
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="normal" id="level2itemnormalstylesfontweightnormal" name="level2itemnormalstylesfontweight" />
	<label class="radiobutton hasTip" title="" for="level2itemnormalstylesfontweightnormal" style="width:auto;"><?php echo JText::_('CK_NORMAL'); ?>
	</label>
</div>
<div class="ckrow">
	<label for="level2itemnormalstylesfontsize"><?php echo JText::_('CK_TITLEFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level2itemnormalstylesfontsize" name="level2itemnormalstylesfontsize" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_NORMAL'); ?></span>
	<input type="text" id="level2itemnormalstylesfontcolor" name="level2itemnormalstylesfontcolor" class="level2itemnormalstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_HOVER'); ?></span>
	<input type="text" id="level2itemhoverstylesfontcolor" name="level2itemhoverstylesfontcolor" class="level2itemhoverstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTHOVERCOLOR_DESC'); ?>" />
</div>
<div class="ckrow">
	<label for="level2itemnormalstylesdescfontsize"><?php echo JText::_('CK_DESCFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level2itemnormalstylesdescfontsize" name="level2itemnormalstylesdescfontsize" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_NORMAL'); ?></span>
	<input type="text" id="level2itemnormalstylesdescfontcolor" name="level2itemnormalstylesdescfontcolor" class="level2itemnormalstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_HOVER'); ?></span>
	<input type="text" id="level2itemhoverstylesdescfontcolor" name="level2itemhoverstylesdescfontcolor" class="level2itemhoverstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTHOVERCOLOR_DESC'); ?>" />
</div>
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="level2menustylesbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2menustylesbgcolor1" name="level2menustylesbgcolor1" class="hasTip level2menustyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2menustylesbgcolor2" name="level2menustylesbgcolor2" class="hasTip level2menustyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level2menustylesbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="level2menustylesbgopacity" name="level2menustylesbgopacity" class="hasTip level2menustyles" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="level2menustylesbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level2menustylesbgimage" name="level2menustylesbgimage" class="hasTip level2menustyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level2menustylesbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level2menustylesbgimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2menustylesbgpositionx" name="level2menustylesbgpositionx" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2menustylesbgpositiony" name="level2menustylesbgpositiony" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level2menustylesbgimagerepeatrepeat" name="level2menustylesbgimagerepeat" class="level2menustyles" />
	<label class="radiobutton first" for="level2menustylesbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level2menustyles" type="radio" value="repeat-x" id="level2menustylesbgimagerepeatrepeat-x" name="level2menustylesbgimagerepeat" />
	<label class="radiobutton"  for="level2menustylesbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level2menustyles" type="radio" value="repeat-y" id="level2menustylesbgimagerepeatrepeat-y" name="level2menustylesbgimagerepeat" />
	<label class="radiobutton last"  for="level2menustylesbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level2menustyles" type="radio" value="no-repeat" id="level2menustylesbgimagerepeatno-repeat" name="level2menustylesbgimagerepeat" />
	<label class="radiobutton last"  for="level2menustylesbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<div class="ckrow">
	<label for="level2menustylesbordercolor"><?php echo JText::_('CK_BORDERCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level2menustylesbordercolor" name="level2menustylesbordercolor" class="level2menustyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_borders.png" /></span><span style="width:30px;"><input type="text" id="level2menustylesbordertopwidth" name="level2menustylesbordertopwidth" class="level2menustyles hasTip" style="width:30px;border-top-color:#237CA4;" title="<?php echo JText::_('CK_BORDERTOPWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level2menustylesborderrightwidth" name="level2menustylesborderrightwidth" class="level2menustyles hasTip" style="width:30px;border-right-color:#237CA4;" title="<?php echo JText::_('CK_BORDERRIGHTWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level2menustylesborderbottomwidth" name="level2menustylesborderbottomwidth" class="level2menustyles hasTip" style="width:30px;border-bottom-color:#237CA4;" title="<?php echo JText::_('CK_BORDERBOTTOMWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level2menustylesborderleftwidth" name="level2menustylesborderleftwidth" class="level2menustyles hasTip" style="width:30px;border-left-color:#237CA4;" title="<?php echo JText::_('CK_BORDERLEFTWIDTH_DESC'); ?>" /></span>
	<span>
		<select id="level2menustylesborderstyle" name="level2menustylesborderstyle" class="level2menustyles hasTip" style="width: 70px; border-radius: 0px;">
			<option value="solid">solid</option>
			<option value="dotted">dotted</option>
			<option value="dashed">dashed</option>
		</select>
	</span>
</div>
<div class="ckrow">
	<label for="level2menustylesroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="level2menustylesroundedcornerstl" name="level2menustylesroundedcornerstl" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="level2menustylesroundedcornerstr" name="level2menustylesroundedcornerstr" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="level2menustylesroundedcornersbr" name="level2menustylesroundedcornersbr" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="level2menustylesroundedcornersbl" name="level2menustylesroundedcornersbl" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2menustylesshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level2menustylesshadowcolor" name="level2menustylesshadowcolor" class="level2menustyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level2menustylesshadowblur" name="level2menustylesshadowblur" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="level2menustylesshadowspread" name="level2menustylesshadowspread" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2menustylesshadowoffsetx" name="level2menustylesshadowoffsetx" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2menustylesshadowoffsety" name="level2menustylesshadowoffsety" class="level2menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton level2menustyles" type="radio" value="0" id="level2menustylesshadowinsetno" name="level2menustylesshadowinset" />
	<label class="radiobutton last"  for="level2menustylesshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton level2menustyles" type="radio" value="1" id="level2menustylesshadowinsetyes" name="level2menustylesshadowinset" />
	<label class="radiobutton last"  for="level2menustylesshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>