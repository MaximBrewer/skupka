<?php
/**
 * @name		Accordeon Menu CK params
 * @package		com_accordeonck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;
?>
<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
<div class="ckrow">
	<label for="menustylesfontfamily"><?php echo JText::_('CK_FONTSTYLE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/font_add.png" />
	<input type="text" id="menustylesfontfamily" name="menustylesfontfamily" class="menustyles hasTip" onchange="ckCleanGfontName(this);" title="<?php echo JText::_('CK_GFONT_DESC'); ?>" />
	<input type="hidden" id="menustylestextisgfont" name="menustylestextisgfont" class="isgfont menustyles" />
	<input class="radiobutton menustyles" type="radio" value="left" id="menustylestextalignleft" name="menustylestextalign" />
	<label class="radiobutton first" for="menustylestextalignleft"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_left.png" />
	</label><input class="radiobutton menustyles" type="radio" value="center" id="menustylestextaligncenter" name="menustylestextalign" />
	<label class="radiobutton"  for="menustylestextaligncenter"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_center.png" />
	</label><input class="radiobutton menustyles" type="radio" value="right" id="menustylestextalignright" name="menustylestextalign" />
	<label class="radiobutton last"  for="menustylestextalignright"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_right.png" /></label>
	<span class="vertical_separator"></span>
	<input class="radiobutton level1itemnormalstyles" type="radio" value="lowercase" id="level1itemnormalstylestexttransformlowercase" name="level1itemnormalstylestexttransform" />
	<label class="radiobutton first hasTip" title="<?php echo JText::_('CK_LOWERCASE'); ?>" for="level1itemnormalstylestexttransformlowercase"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_lowercase.png" />
	</label><input class="radiobutton level1itemnormalstyles" type="radio" value="uppercase" id="level1itemnormalstylestexttransformuppercase" name="level1itemnormalstylestexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_UPPERCASE'); ?>" for="level1itemnormalstylestexttransformuppercase"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_uppercase.png" />
	</label><input class="radiobutton level1itemnormalstyles" type="radio" value="capitalize" id="level1itemnormalstylestexttransformcapitalize" name="level1itemnormalstylestexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_CAPITALIZE'); ?>" for="level1itemnormalstylestexttransformcapitalize"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_capitalize.png" />
	</label><input class="radiobutton level1itemnormalstyles" type="radio" value="default" id="level1itemnormalstylestexttransformdefault" name="level1itemnormalstylestexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_DEFAULT'); ?>" for="level1itemnormalstylestexttransformdefault"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_default.png" />
	</label>
</div>
<div class="ckrow">
	<label for="level1itemnormalstylesfontweightbold"></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/text_bold.png" />
	<input class="radiobutton level1itemnormalstyles" type="radio" value="bold" id="level1itemnormalstylesfontweightbold" name="level1itemnormalstylesfontweight" />
	<label class="radiobutton first hasTip" title="" for="level1itemnormalstylesfontweightbold" style="width:auto;"><?php echo JText::_('CK_BOLD'); ?>
	</label><input class="radiobutton level1itemnormalstyles" type="radio" value="normal" id="level1itemnormalstylesfontweightnormal" name="level1itemnormalstylesfontweight" />
	<label class="radiobutton hasTip" title="" for="level1itemnormalstylesfontweightnormal" style="width:auto;"><?php echo JText::_('CK_NORMAL'); ?>
	</label>
</div>
<div class="ckrow">
	<label for="level1itemnormalstylesfontsize"><?php echo JText::_('CK_TITLEFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level1itemnormalstylesfontsize" name="level1itemnormalstylesfontsize" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_NORMAL'); ?></span>
	<input type="text" id="level1itemnormalstylesfontcolor" name="level1itemnormalstylesfontcolor" class="level1itemnormalstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />

	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_HOVER'); ?></span>
	<input type="text" id="level1itemhoverstylesfontcolor" name="level1itemhoverstylesfontcolor" class="level1itemhoverstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTHOVERCOLOR_DESC'); ?>" />
</div>
<div class="ckrow">
	<label for="level1itemnormalstylesdescfontsize"><?php echo JText::_('CK_DESCFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level1itemnormalstylesdescfontsize" name="level1itemnormalstylesdescfontsize" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_NORMAL'); ?></span>
	<input type="text" id="level1itemnormalstylesdescfontcolor" name="level1itemnormalstylesdescfontcolor" class="level1itemnormalstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_HOVER'); ?></span>
	<input type="text" id="level1itemhoverstylesdescfontcolor" name="level1itemhoverstylesdescfontcolor" class="level1itemhoverstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTHOVERCOLOR_DESC'); ?>" />
</div>
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="menustylesbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="menustylesbgcolor1" name="menustylesbgcolor1" class="hasTip menustyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="menustylesbgcolor2" name="menustylesbgcolor2" class="hasTip menustyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'menustylesbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="menustylesbgopacity" name="menustylesbgopacity" class="hasTip menustyles" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="menustylesbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="menustylesbgimage" name="menustylesbgimage" class="hasTip menustyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'menustylesbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=menustylesbgimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('').trigger('change');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="menustylesbgpositionx" name="menustylesbgpositionx" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="menustylesbgpositiony" name="menustylesbgpositiony" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="menustylesbgimagerepeatrepeat" name="menustylesbgimagerepeat" class="menustyles" />
	<label class="radiobutton first" for="menustylesbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton menustyles" type="radio" value="repeat-x" id="menustylesbgimagerepeatrepeat-x" name="menustylesbgimagerepeat" />
	<label class="radiobutton"  for="menustylesbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton menustyles" type="radio" value="repeat-y" id="menustylesbgimagerepeatrepeat-y" name="menustylesbgimagerepeat" />
	<label class="radiobutton last"  for="menustylesbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton menustyles" type="radio" value="no-repeat" id="menustylesbgimagerepeatno-repeat" name="menustylesbgimagerepeat" />
	<label class="radiobutton last"  for="menustylesbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<?php $this->interface->createBorders('menustyles') ?>
<div class="ckrow">
	<label for="menustylesroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="menustylesroundedcornerstl" name="menustylesroundedcornerstl" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="menustylesroundedcornerstr" name="menustylesroundedcornerstr" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="menustylesroundedcornersbr" name="menustylesroundedcornersbr" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="menustylesroundedcornersbl" name="menustylesroundedcornersbl" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="menustylesshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="menustylesshadowcolor" name="menustylesshadowcolor" class="menustyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="menustylesshadowblur" name="menustylesshadowblur" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="menustylesshadowspread" name="menustylesshadowspread" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="menustylesshadowoffsetx" name="menustylesshadowoffsetx" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="menustylesshadowoffsety" name="menustylesshadowoffsety" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton menustyles" type="radio" value="0" id="menustylesshadowinsetno" name="menustylesshadowinset" />
	<label class="radiobutton last"  for="menustylesshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton menustyles" type="radio" value="1" id="menustylesshadowinsetyes" name="menustylesshadowinset" />
	<label class="radiobutton last"  for="menustylesshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="menustylesmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="menustylesmargintop" name="menustylesmargintop" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="menustylesmarginright" name="menustylesmarginright" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="menustylesmarginbottom" name="menustylesmarginbottom" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="menustylesmarginleft" name="menustylesmarginleft" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="menustylespaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="menustylespaddingtop" name="menustylespaddingtop" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="menustylespaddingright" name="menustylespaddingright" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="menustylespaddingbottom" name="menustylespaddingbottom" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="menustylespaddingleft" name="menustylespaddingleft" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
