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
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="level1itemhoverstylesbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level1itemhoverstylesbgcolor1" name="level1itemhoverstylesbgcolor1" class="hasTip level1itemhoverstyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level1itemhoverstylesbgcolor2" name="level1itemhoverstylesbgcolor2" class="hasTip level1itemhoverstyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level1itemhoverstylesbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="level1itemhoverstylesbgopacity" name="level1itemhoverstylesbgopacity" class="hasTip level1itemhoverstyles" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="level1itemhoverstylesbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level1itemhoverstylesbgimage" name="level1itemhoverstylesbgimage" class="hasTip level1itemhoverstyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level1itemhoverstylesbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level1itemhoverstylesbgimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesbgpositionx" name="level1itemhoverstylesbgpositionx" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesbgpositiony" name="level1itemhoverstylesbgpositiony" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level1itemhoverstylesbgimagerepeatrepeat" name="level1itemhoverstylesbgimagerepeat" class="level1itemhoverstyles" />
	<label class="radiobutton first" for="level1itemhoverstylesbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level1itemhoverstyles" type="radio" value="repeat-x" id="level1itemhoverstylesbgimagerepeatrepeat-x" name="level1itemhoverstylesbgimagerepeat" />
	<label class="radiobutton"  for="level1itemhoverstylesbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level1itemhoverstyles" type="radio" value="repeat-y" id="level1itemhoverstylesbgimagerepeatrepeat-y" name="level1itemhoverstylesbgimagerepeat" />
	<label class="radiobutton last"  for="level1itemhoverstylesbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level1itemhoverstyles" type="radio" value="no-repeat" id="level1itemhoverstylesbgimagerepeatno-repeat" name="level1itemhoverstylesbgimagerepeat" />
	<label class="radiobutton last"  for="level1itemhoverstylesbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<div class="ckrow">
	<label for="level1itemhoverstylesbordercolor"><?php echo JText::_('CK_BORDERCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level1itemhoverstylesbordercolor" name="level1itemhoverstylesbordercolor" class="level1itemhoverstyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_borders.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesbordertopwidth" name="level1itemhoverstylesbordertopwidth" class="level1itemhoverstyles hasTip" style="width:30px;border-top-color:#237CA4;" title="<?php echo JText::_('CK_BORDERTOPWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level1itemhoverstylesborderrightwidth" name="level1itemhoverstylesborderrightwidth" class="level1itemhoverstyles hasTip" style="width:30px;border-right-color:#237CA4;" title="<?php echo JText::_('CK_BORDERRIGHTWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level1itemhoverstylesborderbottomwidth" name="level1itemhoverstylesborderbottomwidth" class="level1itemhoverstyles hasTip" style="width:30px;border-bottom-color:#237CA4;" title="<?php echo JText::_('CK_BORDERBOTTOMWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level1itemhoverstylesborderleftwidth" name="level1itemhoverstylesborderleftwidth" class="level1itemhoverstyles hasTip" style="width:30px;border-left-color:#237CA4;" title="<?php echo JText::_('CK_BORDERLEFTWIDTH_DESC'); ?>" /></span>
	<span>
		<select id="level1itemhoverstylesborderstyle" name="level1itemhoverstylesborderstyle" class="level1itemhoverstyles hasTip" style="width: 70px; border-radius: 0px;">
			<option value="solid">solid</option>
			<option value="dotted">dotted</option>
			<option value="dashed">dashed</option>
		</select>
	</span>
</div>
<div class="ckrow">
	<label for="level1itemhoverstylesroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesroundedcornerstl" name="level1itemhoverstylesroundedcornerstl" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesroundedcornerstr" name="level1itemhoverstylesroundedcornerstr" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesroundedcornersbr" name="level1itemhoverstylesroundedcornersbr" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesroundedcornersbl" name="level1itemhoverstylesroundedcornersbl" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level1itemhoverstylesshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level1itemhoverstylesshadowcolor" name="level1itemhoverstylesshadowcolor" class="level1itemhoverstyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesshadowblur" name="level1itemhoverstylesshadowblur" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesshadowspread" name="level1itemhoverstylesshadowspread" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesshadowoffsetx" name="level1itemhoverstylesshadowoffsetx" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesshadowoffsety" name="level1itemhoverstylesshadowoffsety" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton level1itemhoverstyles" type="radio" value="0" id="level1itemhoverstylesshadowinsetno" name="level1itemhoverstylesshadowinset" />
	<label class="radiobutton last"  for="level1itemhoverstylesshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton level1itemhoverstyles" type="radio" value="1" id="level1itemhoverstylesshadowinsetyes" name="level1itemhoverstylesshadowinset" />
	<label class="radiobutton last"  for="level1itemhoverstylesshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckrow">
	<label for="level1itemhoverstylestextshadowcolor"><?php echo JText::_('CK_TEXTSHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level1itemhoverstylestextshadowcolor" name="level1itemhoverstylestextshadowcolor" class="level1itemhoverstyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylestextshadowblur" name="level1itemhoverstylestextshadowblur" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylestextshadowoffsetx" name="level1itemhoverstylestextshadowoffsetx" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylestextshadowoffsety" name="level1itemhoverstylestextshadowoffsety" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="level1itemhoverstylesmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesmargintop" name="level1itemhoverstylesmargintop" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesmarginright" name="level1itemhoverstylesmarginright" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesmarginbottom" name="level1itemhoverstylesmarginbottom" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesmarginleft" name="level1itemhoverstylesmarginleft" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level1itemhoverstylespaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylespaddingtop" name="level1itemhoverstylespaddingtop" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylespaddingright" name="level1itemhoverstylespaddingright" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylespaddingbottom" name="level1itemhoverstylespaddingbottom" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylespaddingleft" name="level1itemhoverstylespaddingleft" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
