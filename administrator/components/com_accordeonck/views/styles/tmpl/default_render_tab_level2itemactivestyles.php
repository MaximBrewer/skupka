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
	<input class="radiobutton level2itemactivestyles undisabled" type="radio" value="1" id="level2itemactivestylesidemhoveryes" name="level2itemactivestylesidemhover" checked="checked"/>
	<label class="radiobutton first" for="level2itemactivestylesidemhoveryes" onclick="disable_active_styles('#tab_level2itemactivestyles')" style="width:auto;"><?php echo JText::_('CK_ACTIVE_SYLES_IDEM_HOVER'); ?>
	</label><input class="radiobutton level2itemactivestyles undisabled" type="radio" value="0" id="level2itemactivestylesidemhoverno" name="level2itemactivestylesidemhover" />
	<label class="radiobutton" for="level2itemactivestylesidemhoverno" onclick="enable_active_styles('#tab_level2itemactivestyles')" style="width:auto;"><?php echo JText::_('CK_ACTIVE_SYLES_CUSTOM'); ?></label>
</div>
<div class="ckrow">
	<label for="level2itemactivestylesfontsize"><?php echo JText::_('CK_TITLEFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level2itemactivestylesfontsize" name="level2itemactivestylesfontsize" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2itemactivestylesfontcolor" name="level2itemactivestylesfontcolor" class="level2itemactivestyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
</div>
<div class="ckrow">
	<label for="level2itemactivestylesdescfontsize"><?php echo JText::_('CK_DESCFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level2itemactivestylesdescfontsize" name="level2itemactivestylesdescfontsize" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2itemactivestylesdescfontcolor" name="level2itemactivestylesdescfontcolor" class="level2itemactivestyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
</div>
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="level2itemactivestylesbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2itemactivestylesbgcolor1" name="level2itemactivestylesbgcolor1" class="hasTip level2itemactivestyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2itemactivestylesbgcolor2" name="level2itemactivestylesbgcolor2" class="hasTip level2itemactivestyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level2itemactivestylesbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="level2itemactivestylesbgopacity" name="level2itemactivestylesbgopacity" class="hasTip level2itemactivestyles" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="level2itemactivestylesbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level2itemactivestylesbgimage" name="level2itemactivestylesbgimage" class="hasTip level2itemactivestyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level2itemactivestylesbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level2itemactivestylesbgimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesbgpositionx" name="level2itemactivestylesbgpositionx" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesbgpositiony" name="level2itemactivestylesbgpositiony" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level2itemactivestylesbgimagerepeatrepeat" name="level2itemactivestylesbgimagerepeat" class="level2itemactivestyles" />
	<label class="radiobutton first" for="level2itemactivestylesbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level2itemactivestyles" type="radio" value="repeat-x" id="level2itemactivestylesbgimagerepeatrepeat-x" name="level2itemactivestylesbgimagerepeat" />
	<label class="radiobutton"  for="level2itemactivestylesbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level2itemactivestyles" type="radio" value="repeat-y" id="level2itemactivestylesbgimagerepeatrepeat-y" name="level2itemactivestylesbgimagerepeat" />
	<label class="radiobutton last"  for="level2itemactivestylesbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level2itemactivestyles" type="radio" value="no-repeat" id="level2itemactivestylesbgimagerepeatno-repeat" name="level2itemactivestylesbgimagerepeat" />
	<label class="radiobutton last"  for="level2itemactivestylesbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<?php $this->interface->createBorders('level2itemactivestyles') ?>
<div class="ckrow">
	<label for="level2itemactivestylesroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesroundedcornerstl" name="level2itemactivestylesroundedcornerstl" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesroundedcornerstr" name="level2itemactivestylesroundedcornerstr" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesroundedcornersbr" name="level2itemactivestylesroundedcornersbr" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesroundedcornersbl" name="level2itemactivestylesroundedcornersbl" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2itemactivestylesshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level2itemactivestylesshadowcolor" name="level2itemactivestylesshadowcolor" class="level2itemactivestyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesshadowblur" name="level2itemactivestylesshadowblur" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesshadowspread" name="level2itemactivestylesshadowspread" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesshadowoffsetx" name="level2itemactivestylesshadowoffsetx" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesshadowoffsety" name="level2itemactivestylesshadowoffsety" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton level2itemactivestyles" type="radio" value="0" id="level2itemactivestylesshadowinsetno" name="level2itemactivestylesshadowinset" />
	<label class="radiobutton last"  for="level2itemactivestylesshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton level2itemactivestyles" type="radio" value="1" id="level2itemactivestylesshadowinsetyes" name="level2itemactivestylesshadowinset" />
	<label class="radiobutton last"  for="level2itemactivestylesshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckrow">
	<label for="level2itemactivestylestextshadowcolor"><?php echo JText::_('CK_TEXTSHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level2itemactivestylestextshadowcolor" name="level2itemactivestylestextshadowcolor" class="level2itemactivestyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylestextshadowblur" name="level2itemactivestylestextshadowblur" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylestextshadowoffsetx" name="level2itemactivestylestextshadowoffsetx" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylestextshadowoffsety" name="level2itemactivestylestextshadowoffsety" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="level2itemactivestylesmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesmargintop" name="level2itemactivestylesmargintop" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesmarginright" name="level2itemactivestylesmarginright" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesmarginbottom" name="level2itemactivestylesmarginbottom" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylesmarginleft" name="level2itemactivestylesmarginleft" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2itemactivestylespaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylespaddingtop" name="level2itemactivestylespaddingtop" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylespaddingright" name="level2itemactivestylespaddingright" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylespaddingbottom" name="level2itemactivestylespaddingbottom" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="level2itemactivestylespaddingleft" name="level2itemactivestylespaddingleft" class="level2itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
