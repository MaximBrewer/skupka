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
	<label for="level3itemnormalstylesfontfamily"><?php echo JText::_('CK_FONTSTYLE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/font_add.png" />
	<input type="text" id="level3itemnormalstylesfontfamily" name="level3itemnormalstylesfontfamily" class="level3itemnormalstyles hasTip" onchange="ckCleanGfontName(this);" title="<?php echo JText::_('CK_GFONT_DESC'); ?>" />
	<input type="hidden" id="level3itemnormalstylestextisgfont" name="level3itemnormalstylestextisgfont" class="isgfont level3itemnormalstyles" />
	<input class="radiobutton level3itemnormalstyles" type="radio" value="left" id="level3itemnormalstylestextalignleft" name="level3itemnormalstylestextalign" />
	<label class="radiobutton first" for="level3itemnormalstylestextalignleft"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_left.png" />
	</label><input class="radiobutton level3itemnormalstyles" type="radio" value="center" id="level3itemnormalstylestextaligncenter" name="level3itemnormalstylestextalign" />
	<label class="radiobutton"  for="level3itemnormalstylestextaligncenter"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_center.png" />
	</label><input class="radiobutton level3itemnormalstyles" type="radio" value="right" id="level3itemnormalstylestextalignright" name="level3itemnormalstylestextalign" />
	<label class="radiobutton last"  for="level3itemnormalstylestextalignright"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_right.png" /></label>
	<span class="vertical_separator"></span>
	<input class="radiobutton level3itemnormalstyles" type="radio" value="lowercase" id="level3itemnormalstylestexttransformlowercase" name="level3itemnormalstylestexttransform" />
	<label class="radiobutton first hasTip" for="level3itemnormalstylestexttransformlowercase" title="<?php echo JText::_('CK_LOWERCASE'); ?>"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_lowercase.png" />
	</label><input class="radiobutton level3itemnormalstyles" type="radio" value="uppercase" id="level3itemnormalstylestexttransformuppercase" name="level3itemnormalstylestexttransform" />
	<label class="radiobutton hasTip" for="level3itemnormalstylestexttransformuppercase" title="<?php echo JText::_('CK_UPPERCASE'); ?>"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_uppercase.png" />
	</label><input class="radiobutton level3itemnormalstyles" type="radio" value="capitalize" id="level3itemnormalstylestexttransformcapitalize" name="level3itemnormalstylestexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_CAPITALIZE'); ?>" for="level3itemnormalstylestexttransformcapitalize"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_capitalize.png" />
	</label><input class="radiobutton level3itemnormalstyles" type="radio" value="default" id="level3itemnormalstylestexttransformdefault" name="level3itemnormalstylestexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_DEFAULT'); ?>" for="level3itemnormalstylestexttransformdefault"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_default.png" />
	</label>
</div>
<div class="ckrow">
	<label for="level3itemnormalstylesfontweightbold"></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/text_bold.png" />
	<input class="radiobutton level3itemnormalstyles" type="radio" value="bold" id="level3itemnormalstylesfontweightbold" name="level3itemnormalstylesfontweight" />
	<label class="radiobutton first hasTip" title="" for="level3itemnormalstylesfontweightbold" style="width:auto;"><?php echo JText::_('CK_BOLD'); ?>
	</label><input class="radiobutton level3itemnormalstyles" type="radio" value="normal" id="level3itemnormalstylesfontweightnormal" name="level3itemnormalstylesfontweight" />
	<label class="radiobutton hasTip" title="" for="level3itemnormalstylesfontweightnormal" style="width:auto;"><?php echo JText::_('CK_NORMAL'); ?>
	</label>
</div>
<div class="ckrow">
	<label for="level3itemnormalstylesfontsize"><?php echo JText::_('CK_TITLEFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level3itemnormalstylesfontsize" name="level3itemnormalstylesfontsize" class="level3itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_NORMAL'); ?></span>
	<input type="text" id="level3itemnormalstylesfontcolor" name="level3itemnormalstylesfontcolor" class="level3itemnormalstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_HOVER'); ?></span>
	<input type="text" id="level3itemhoverstylesfontcolor" name="level3itemhoverstylesfontcolor" class="level3itemhoverstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTHOVERCOLOR_DESC'); ?>" />
</div>
<div class="ckrow">
	<label for="level3itemnormalstylesdescfontsize"><?php echo JText::_('CK_DESCFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level3itemnormalstylesdescfontsize" name="level3itemnormalstylesdescfontsize" class="level3itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_NORMAL'); ?></span>
	<input type="text" id="level3itemnormalstylesdescfontcolor" name="level3itemnormalstylesdescfontcolor" class="level3itemnormalstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_HOVER'); ?></span>
	<input type="text" id="level3itemhoverstylesdescfontcolor" name="level3itemhoverstylesdescfontcolor" class="level3itemhoverstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTHOVERCOLOR_DESC'); ?>" />
</div>
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="level3menustylesbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level3menustylesbgcolor1" name="level3menustylesbgcolor1" class="hasTip level3menustyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level3menustylesbgcolor2" name="level3menustylesbgcolor2" class="hasTip level3menustyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level3menustylesbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="level3menustylesbgopacity" name="level3menustylesbgopacity" class="hasTip level3menustyles" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="level3menustylesbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level3menustylesbgimage" name="level3menustylesbgimage" class="hasTip level3menustyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level3menustylesbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level3menustylesbgimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesbgpositionx" name="level3menustylesbgpositionx" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesbgpositiony" name="level3menustylesbgpositiony" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level3menustylesbgimagerepeatrepeat" name="level3menustylesbgimagerepeat" class="level3menustyles" />
	<label class="radiobutton first" for="level3menustylesbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level3menustyles" type="radio" value="repeat-x" id="level3menustylesbgimagerepeatrepeat-x" name="level3menustylesbgimagerepeat" />
	<label class="radiobutton"  for="level3menustylesbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level3menustyles" type="radio" value="repeat-y" id="level3menustylesbgimagerepeatrepeat-y" name="level3menustylesbgimagerepeat" />
	<label class="radiobutton last"  for="level3menustylesbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level3menustyles" type="radio" value="no-repeat" id="level3menustylesbgimagerepeatno-repeat" name="level3menustylesbgimagerepeat" />
	<label class="radiobutton last"  for="level3menustylesbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<?php $this->interface->createBorders('level3menustyles') ?>
<div class="ckrow">
	<label for="level3menustylesroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesroundedcornerstl" name="level3menustylesroundedcornerstl" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesroundedcornerstr" name="level3menustylesroundedcornerstr" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesroundedcornersbr" name="level3menustylesroundedcornersbr" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesroundedcornersbl" name="level3menustylesroundedcornersbl" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level3menustylesshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level3menustylesshadowcolor" name="level3menustylesshadowcolor" class="level3menustyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesshadowblur" name="level3menustylesshadowblur" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesshadowspread" name="level3menustylesshadowspread" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesshadowoffsetx" name="level3menustylesshadowoffsetx" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesshadowoffsety" name="level3menustylesshadowoffsety" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton level3menustyles" type="radio" value="0" id="level3menustylesshadowinsetno" name="level3menustylesshadowinset" />
	<label class="radiobutton last"  for="level3menustylesshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton level3menustyles" type="radio" value="1" id="level3menustylesshadowinsetyes" name="level3menustylesshadowinset" />
	<label class="radiobutton last"  for="level3menustylesshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="level3menustylesmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesmargintop" name="level3menustylesmargintop" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesmarginright" name="level3menustylesmarginright" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesmarginbottom" name="level3menustylesmarginbottom" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesmarginleft" name="level3menustylesmarginleft" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level3menustylespaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="level3menustylespaddingtop" name="level3menustylespaddingtop" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="level3menustylespaddingright" name="level3menustylespaddingright" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="level3menustylespaddingbottom" name="level3menustylespaddingbottom" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="level3menustylespaddingleft" name="level3menustylespaddingleft" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>