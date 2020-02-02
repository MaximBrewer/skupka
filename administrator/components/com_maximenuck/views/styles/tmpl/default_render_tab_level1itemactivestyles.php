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
<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
<div class="ckrow">
	<input class="radiobutton level1itemactivestyles undisabled" type="radio" value="1" id="level1itemactivestylesidemhoveryes" name="level1itemactivestylesidemhover" checked="checked"/>
	<label class="radiobutton first" for="level1itemactivestylesidemhoveryes" onclick="disable_active_styles('#tab_level1itemactivestyles')" style="width:auto;"><?php echo JText::_('CK_ACTIVE_SYLES_IDEM_HOVER'); ?>
	</label><input class="radiobutton level1itemactivestyles undisabled" type="radio" value="0" id="level1itemactivestylesidemhoverno" name="level1itemactivestylesidemhover" />
	<label class="radiobutton" for="level1itemactivestylesidemhoverno" onclick="enable_active_styles('#tab_level1itemactivestyles')" style="width:auto;"><?php echo JText::_('CK_ACTIVE_SYLES_CUSTOM'); ?></label>
</div>
<div class="ckrow">
	<label for="level1itemactivestylesfontsize"><?php echo JText::_('CK_TITLEFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level1itemactivestylesfontsize" name="level1itemactivestylesfontsize" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level1itemactivestylesfontcolor" name="level1itemactivestylesfontcolor" class="level1itemactivestyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
</div>
<div class="ckrow">
	<label for="level1itemactivestylesdescfontsize"><?php echo JText::_('CK_DESCFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level1itemactivestylesdescfontsize" name="level1itemactivestylesdescfontsize" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level1itemactivestylesdescfontcolor" name="level1itemactivestylesdescfontcolor" class="level1itemactivestyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
</div>
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="level1itemactivestylesbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level1itemactivestylesbgcolor1" name="level1itemactivestylesbgcolor1" class="hasTip level1itemactivestyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level1itemactivestylesbgcolor2" name="level1itemactivestylesbgcolor2" class="hasTip level1itemactivestyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level1itemactivestylesbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="level1itemactivestylesbgopacity" name="level1itemactivestylesbgopacity" class="hasTip level1itemactivestyles" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="level1itemactivestylesbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level1itemactivestylesbgimage" name="level1itemactivestylesbgimage" class="hasTip level1itemactivestyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level1itemactivestylesbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level1itemactivestylesbgimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesbgpositionx" name="level1itemactivestylesbgpositionx" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesbgpositiony" name="level1itemactivestylesbgpositiony" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level1itemactivestylesbgimagerepeatrepeat" name="level1itemactivestylesbgimagerepeat" class="level1itemactivestyles" />
	<label class="radiobutton first" for="level1itemactivestylesbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level1itemactivestyles" type="radio" value="repeat-x" id="level1itemactivestylesbgimagerepeatrepeat-x" name="level1itemactivestylesbgimagerepeat" />
	<label class="radiobutton"  for="level1itemactivestylesbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level1itemactivestyles" type="radio" value="repeat-y" id="level1itemactivestylesbgimagerepeatrepeat-y" name="level1itemactivestylesbgimagerepeat" />
	<label class="radiobutton last"  for="level1itemactivestylesbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level1itemactivestyles" type="radio" value="no-repeat" id="level1itemactivestylesbgimagerepeatno-repeat" name="level1itemactivestylesbgimagerepeat" />
	<label class="radiobutton last"  for="level1itemactivestylesbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<div class="ckrow">
	<label for="level1itemactivestylesbordercolor"><?php echo JText::_('CK_BORDERCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level1itemactivestylesbordercolor" name="level1itemactivestylesbordercolor" class="level1itemactivestyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_borders.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesbordertopwidth" name="level1itemactivestylesbordertopwidth" class="level1itemactivestyles hasTip" style="width:30px;border-top-color:#237CA4;" title="<?php echo JText::_('CK_BORDERTOPWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level1itemactivestylesborderrightwidth" name="level1itemactivestylesborderrightwidth" class="level1itemactivestyles hasTip" style="width:30px;border-right-color:#237CA4;" title="<?php echo JText::_('CK_BORDERRIGHTWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level1itemactivestylesborderbottomwidth" name="level1itemactivestylesborderbottomwidth" class="level1itemactivestyles hasTip" style="width:30px;border-bottom-color:#237CA4;" title="<?php echo JText::_('CK_BORDERBOTTOMWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level1itemactivestylesborderleftwidth" name="level1itemactivestylesborderleftwidth" class="level1itemactivestyles hasTip" style="width:30px;border-left-color:#237CA4;" title="<?php echo JText::_('CK_BORDERLEFTWIDTH_DESC'); ?>" /></span>
	<span>
		<select id="level1itemactivestylesborderstyle" name="level1itemactivestylesborderstyle" class="level1itemactivestyles hasTip" style="width: 70px; border-radius: 0px;">
			<option value="solid">solid</option>
			<option value="dotted">dotted</option>
			<option value="dashed">dashed</option>
		</select>
	</span>
</div>
<div class="ckrow">
	<label for="level1itemactivestylesroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesroundedcornerstl" name="level1itemactivestylesroundedcornerstl" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesroundedcornerstr" name="level1itemactivestylesroundedcornerstr" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesroundedcornersbr" name="level1itemactivestylesroundedcornersbr" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesroundedcornersbl" name="level1itemactivestylesroundedcornersbl" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level1itemactivestylesshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level1itemactivestylesshadowcolor" name="level1itemactivestylesshadowcolor" class="level1itemactivestyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesshadowblur" name="level1itemactivestylesshadowblur" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesshadowspread" name="level1itemactivestylesshadowspread" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesshadowoffsetx" name="level1itemactivestylesshadowoffsetx" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesshadowoffsety" name="level1itemactivestylesshadowoffsety" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton level1itemactivestyles" type="radio" value="0" id="level1itemactivestylesshadowinsetno" name="level1itemactivestylesshadowinset" />
	<label class="radiobutton last"  for="level1itemactivestylesshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton level1itemactivestyles" type="radio" value="1" id="level1itemactivestylesshadowinsetyes" name="level1itemactivestylesshadowinset" />
	<label class="radiobutton last"  for="level1itemactivestylesshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckrow">
	<label for="level1itemactivestylestextshadowcolor"><?php echo JText::_('CK_TEXTSHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level1itemactivestylestextshadowcolor" name="level1itemactivestylestextshadowcolor" class="level1itemactivestyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylestextshadowblur" name="level1itemactivestylestextshadowblur" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylestextshadowoffsetx" name="level1itemactivestylestextshadowoffsetx" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylestextshadowoffsety" name="level1itemactivestylestextshadowoffsety" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="level1itemactivestylesmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesmargintop" name="level1itemactivestylesmargintop" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesmarginright" name="level1itemactivestylesmarginright" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesmarginbottom" name="level1itemactivestylesmarginbottom" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylesmarginleft" name="level1itemactivestylesmarginleft" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level1itemactivestylespaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylespaddingtop" name="level1itemactivestylespaddingtop" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylespaddingright" name="level1itemactivestylespaddingright" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylespaddingbottom" name="level1itemactivestylespaddingbottom" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="level1itemactivestylespaddingleft" name="level1itemactivestylespaddingleft" class="level1itemactivestyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
