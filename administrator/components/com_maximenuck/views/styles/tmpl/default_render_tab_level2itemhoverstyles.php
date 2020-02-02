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
	<label for="level2itemhoverstylesbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2itemhoverstylesbgcolor1" name="level2itemhoverstylesbgcolor1" class="hasTip level2itemhoverstyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2itemhoverstylesbgcolor2" name="level2itemhoverstylesbgcolor2" class="hasTip level2itemhoverstyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level2itemhoverstylesbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="level2itemhoverstylesbgopacity" name="level2itemhoverstylesbgopacity" class="hasTip level2itemhoverstyles" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="level2itemhoverstylesbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level2itemhoverstylesbgimage" name="level2itemhoverstylesbgimage" class="hasTip level2itemhoverstyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level2itemhoverstylesbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level2itemhoverstylesbgimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label for="level2itemhoverstylesbordercolor"><?php echo JText::_('CK_BORDERCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level2itemhoverstylesbordercolor" name="level2itemhoverstylesbordercolor" class="level2itemhoverstyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_borders.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesbordertopwidth" name="level2itemhoverstylesbordertopwidth" class="level2itemhoverstyles hasTip" style="width:30px;border-top-color:#237CA4;" title="<?php echo JText::_('CK_BORDERTOPWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level2itemhoverstylesborderrightwidth" name="level2itemhoverstylesborderrightwidth" class="level2itemhoverstyles hasTip" style="width:30px;border-right-color:#237CA4;" title="<?php echo JText::_('CK_BORDERRIGHTWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level2itemhoverstylesborderbottomwidth" name="level2itemhoverstylesborderbottomwidth" class="level2itemhoverstyles hasTip" style="width:30px;border-bottom-color:#237CA4;" title="<?php echo JText::_('CK_BORDERBOTTOMWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="level2itemhoverstylesborderleftwidth" name="level2itemhoverstylesborderleftwidth" class="level2itemhoverstyles hasTip" style="width:30px;border-left-color:#237CA4;" title="<?php echo JText::_('CK_BORDERLEFTWIDTH_DESC'); ?>" /></span>
	<span>
		<select id="level2itemhoverstylesborderstyle" name="level2itemhoverstylesborderstyle" class="level2itemhoverstyles hasTip" style="width: 70px; border-radius: 0px;">
			<option value="solid">solid</option>
			<option value="dotted">dotted</option>
			<option value="dashed">dashed</option>
		</select>
	</span>
</div>
<div class="ckrow">
	<label for="level2itemhoverstylesroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesroundedcornerstl" name="level2itemhoverstylesroundedcornerstl" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesroundedcornerstr" name="level2itemhoverstylesroundedcornerstr" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesroundedcornersbr" name="level2itemhoverstylesroundedcornersbr" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesroundedcornersbl" name="level2itemhoverstylesroundedcornersbl" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2itemhoverstylesshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level2itemhoverstylesshadowcolor" name="level2itemhoverstylesshadowcolor" class="level2itemhoverstyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesshadowblur" name="level2itemhoverstylesshadowblur" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesshadowspread" name="level2itemhoverstylesshadowspread" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesshadowoffsetx" name="level2itemhoverstylesshadowoffsetx" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesshadowoffsety" name="level2itemhoverstylesshadowoffsety" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton level2itemhoverstyles" type="radio" value="0" id="level2itemhoverstylesshadowinsetno" name="level2itemhoverstylesshadowinset" />
	<label class="radiobutton last"  for="level2itemhoverstylesshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton level2itemhoverstyles" type="radio" value="1" id="level2itemhoverstylesshadowinsetyes" name="level2itemhoverstylesshadowinset" />
	<label class="radiobutton last"  for="level2itemhoverstylesshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckrow">
	<label for="level2itemhoverstylestextshadowcolor"><?php echo JText::_('CK_TEXTSHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level2itemhoverstylestextshadowcolor" name="level2itemhoverstylestextshadowcolor" class="level2itemhoverstyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylestextshadowblur" name="level2itemhoverstylestextshadowblur" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylestextshadowoffsetx" name="level2itemhoverstylestextshadowoffsetx" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylestextshadowoffsety" name="level2itemhoverstylestextshadowoffsety" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="level2itemhoverstylesmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesmargintop" name="level2itemhoverstylesmargintop" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesmarginright" name="level2itemhoverstylesmarginright" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesmarginbottom" name="level2itemhoverstylesmarginbottom" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesmarginleft" name="level2itemhoverstylesmarginleft" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2itemhoverstylespaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylespaddingtop" name="level2itemhoverstylespaddingtop" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylespaddingright" name="level2itemhoverstylespaddingright" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylespaddingbottom" name="level2itemhoverstylespaddingbottom" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylespaddingleft" name="level2itemhoverstylespaddingleft" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
