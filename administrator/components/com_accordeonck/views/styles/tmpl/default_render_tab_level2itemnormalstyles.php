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
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="level2itemnormalstylesbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2itemnormalstylesbgcolor1" name="level2itemnormalstylesbgcolor1" class="hasTip level2itemnormalstyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2itemnormalstylesbgcolor2" name="level2itemnormalstylesbgcolor2" class="hasTip level2itemnormalstyles <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level2itemnormalstylesbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="level2itemnormalstylesbgopacity" name="level2itemnormalstylesbgopacity" class="hasTip level2itemnormalstyles" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="level2itemnormalstylesbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level2itemnormalstylesbgimage" name="level2itemnormalstylesbgimage" class="hasTip level2itemnormalstyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level2itemnormalstylesbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level2itemnormalstylesbgimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesbgpositionx" name="level2itemnormalstylesbgpositionx" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesbgpositiony" name="level2itemnormalstylesbgpositiony" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level2itemnormalstylesbgimagerepeatrepeat" name="level2itemnormalstylesbgimagerepeat" class="level2itemnormalstyles" />
	<label class="radiobutton first" for="level2itemnormalstylesbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="repeat-x" id="level2itemnormalstylesbgimagerepeatrepeat-x" name="level2itemnormalstylesbgimagerepeat" />
	<label class="radiobutton"  for="level2itemnormalstylesbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="repeat-y" id="level2itemnormalstylesbgimagerepeatrepeat-y" name="level2itemnormalstylesbgimagerepeat" />
	<label class="radiobutton last"  for="level2itemnormalstylesbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="no-repeat" id="level2itemnormalstylesbgimagerepeatno-repeat" name="level2itemnormalstylesbgimagerepeat" />
	<label class="radiobutton last"  for="level2itemnormalstylesbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<?php $this->interface->createBorders('level2itemnormalstyles') ?>
<div class="ckrow">
	<label for="level2itemnormalstylesroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesroundedcornerstl" name="level2itemnormalstylesroundedcornerstl" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesroundedcornerstr" name="level2itemnormalstylesroundedcornerstr" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesroundedcornersbr" name="level2itemnormalstylesroundedcornersbr" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesroundedcornersbl" name="level2itemnormalstylesroundedcornersbl" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2itemnormalstylesshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level2itemnormalstylesshadowcolor" name="level2itemnormalstylesshadowcolor" class="level2itemnormalstyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesshadowblur" name="level2itemnormalstylesshadowblur" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesshadowspread" name="level2itemnormalstylesshadowspread" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesshadowoffsetx" name="level2itemnormalstylesshadowoffsetx" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesshadowoffsety" name="level2itemnormalstylesshadowoffsety" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton level2itemnormalstyles" type="radio" value="0" id="level2itemnormalstylesshadowinsetno" name="level2itemnormalstylesshadowinset" />
	<label class="radiobutton last"  for="level2itemnormalstylesshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="1" id="level2itemnormalstylesshadowinsetyes" name="level2itemnormalstylesshadowinset" />
	<label class="radiobutton last"  for="level2itemnormalstylesshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckrow">
	<label for="level2itemnormalstylestextshadowcolor"><?php echo JText::_('CK_TEXTSHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level2itemnormalstylestextshadowcolor" name="level2itemnormalstylestextshadowcolor" class="level2itemnormalstyles <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylestextshadowblur" name="level2itemnormalstylestextshadowblur" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylestextshadowoffsetx" name="level2itemnormalstylestextshadowoffsetx" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylestextshadowoffsety" name="level2itemnormalstylestextshadowoffsety" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="level2itemnormalstylesmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesmargintop" name="level2itemnormalstylesmargintop" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesmarginright" name="level2itemnormalstylesmarginright" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesmarginbottom" name="level2itemnormalstylesmarginbottom" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesmarginleft" name="level2itemnormalstylesmarginleft" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2itemnormalstylespaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylespaddingtop" name="level2itemnormalstylespaddingtop" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylespaddingright" name="level2itemnormalstylespaddingright" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylespaddingbottom" name="level2itemnormalstylespaddingbottom" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylespaddingleft" name="level2itemnormalstylespaddingleft" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>