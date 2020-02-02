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
	<input class="radiobutton level3itemactivestyles undisabled" type="radio" value="1" id="level3itemactivestylesidemhoveryes" name="level3itemactivestylesidemhover" checked="checked"/>
	<label class="radiobutton first" for="level3itemactivestylesidemhoveryes" onclick="disable_active_styles('#tab_level3itemactivestyles')" style="width:auto;"><?php echo JText::_('CK_ACTIVE_SYLES_IDEM_HOVER'); ?>
	</label><input class="radiobutton level3itemactivestyles undisabled" type="radio" value="0" id="level3itemactivestylesidemhoverno" name="level3itemactivestylesidemhover" />
	<label class="radiobutton" for="level3itemactivestylesidemhoverno" onclick="enable_active_styles('#tab_level3itemactivestyles')" style="width:auto;"><?php echo JText::_('CK_ACTIVE_SYLES_CUSTOM'); ?></label>
</div>
<div class="ckrow">
	<label for="level3itemactivestylesfontsize"><?php echo JText::_('CK_TITLEFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level3itemactivestylesfontsize" name="level3itemactivestylesfontsize" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level3itemactivestylesfontcolor" name="level3itemactivestylesfontcolor" class="level3itemactivestyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
</div>
<div class="ckrow">
	<label for="level3itemactivestylesdescfontsize"><?php echo JText::_('CK_DESCFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level3itemactivestylesdescfontsize" name="level3itemactivestylesdescfontsize" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level3itemactivestylesdescfontcolor" name="level3itemactivestylesdescfontcolor" class="level3itemactivestyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
</div>
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="level3itemactivestylesbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level3itemactivestylesbgcolor1" name="level3itemactivestylesbgcolor1" class="hasTip level3itemactivestyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level3itemactivestylesbgcolor2" name="level3itemactivestylesbgcolor2" class="hasTip level3itemactivestyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level3itemactivestylesbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="level3itemactivestylesbgopacity" name="level3itemactivestylesbgopacity" class="hasTip level3itemactivestyles" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="level3itemactivestylesbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level3itemactivestylesbgimage" name="level3itemactivestylesbgimage" class="hasTip level3itemactivestyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level3itemactivestylesbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level3itemactivestylesbgimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesbgpositionx" name="level3itemactivestylesbgpositionx" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesbgpositiony" name="level3itemactivestylesbgpositiony" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level3itemactivestylesbgimagerepeatrepeat" name="level3itemactivestylesbgimagerepeat" class="level3itemactivestyles" />
	<label class="radiobutton first" for="level3itemactivestylesbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level3itemactivestyles" type="radio" value="repeat-x" id="level3itemactivestylesbgimagerepeatrepeat-x" name="level3itemactivestylesbgimagerepeat" />
	<label class="radiobutton"  for="level3itemactivestylesbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level3itemactivestyles" type="radio" value="repeat-y" id="level3itemactivestylesbgimagerepeatrepeat-y" name="level3itemactivestylesbgimagerepeat" />
	<label class="radiobutton last"  for="level3itemactivestylesbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level3itemactivestyles" type="radio" value="no-repeat" id="level3itemactivestylesbgimagerepeatno-repeat" name="level3itemactivestylesbgimagerepeat" />
	<label class="radiobutton last"  for="level3itemactivestylesbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<?php $this->interface->createBorders('level3itemactivestyles') ?>
<div class="ckrow">
	<label for="level3itemactivestylesroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesroundedcornerstl" name="level3itemactivestylesroundedcornerstl" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesroundedcornerstr" name="level3itemactivestylesroundedcornerstr" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesroundedcornersbr" name="level3itemactivestylesroundedcornersbr" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesroundedcornersbl" name="level3itemactivestylesroundedcornersbl" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level3itemactivestylesshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level3itemactivestylesshadowcolor" name="level3itemactivestylesshadowcolor" class="level3itemactivestyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesshadowblur" name="level3itemactivestylesshadowblur" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesshadowspread" name="level3itemactivestylesshadowspread" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesshadowoffsetx" name="level3itemactivestylesshadowoffsetx" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesshadowoffsety" name="level3itemactivestylesshadowoffsety" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton level3itemactivestyles" type="radio" value="0" id="level3itemactivestylesshadowinsetno" name="level3itemactivestylesshadowinset" />
	<label class="radiobutton last"  for="level3itemactivestylesshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton level3itemactivestyles" type="radio" value="1" id="level3itemactivestylesshadowinsetyes" name="level3itemactivestylesshadowinset" />
	<label class="radiobutton last"  for="level3itemactivestylesshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckrow">
	<label for="level3itemactivestylestextshadowcolor"><?php echo JText::_('CK_TEXTSHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level3itemactivestylestextshadowcolor" name="level3itemactivestylestextshadowcolor" class="level3itemactivestyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylestextshadowblur" name="level3itemactivestylestextshadowblur" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylestextshadowoffsetx" name="level3itemactivestylestextshadowoffsetx" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylestextshadowoffsety" name="level3itemactivestylestextshadowoffsety" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="level3itemactivestylesmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesmargintop" name="level3itemactivestylesmargintop" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesmarginright" name="level3itemactivestylesmarginright" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesmarginbottom" name="level3itemactivestylesmarginbottom" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylesmarginleft" name="level3itemactivestylesmarginleft" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level3itemactivestylespaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylespaddingtop" name="level3itemactivestylespaddingtop" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylespaddingright" name="level3itemactivestylespaddingright" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylespaddingbottom" name="level3itemactivestylespaddingbottom" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="level3itemactivestylespaddingleft" name="level3itemactivestylespaddingleft" class="level3itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
