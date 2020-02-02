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
	<label for="level2itemgroupbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2itemgroupbgcolor1" name="level2itemgroupbgcolor1" class="hasTip level2itemgroup <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="level2itemgroupbgcolor2" name="level2itemgroupbgcolor2" class="hasTip level2itemgroup <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level2itemgroupbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="level2itemgroupbgopacity" name="level2itemgroupbgopacity" class="hasTip level2itemgroup" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="level2itemgroupbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level2itemgroupbgimage" name="level2itemgroupbgimage" class="hasTip level2itemgroup" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'level2itemgroupbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level2itemgroupbgimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemgroupbgpositionx" name="level2itemgroupbgpositionx" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemgroupbgpositiony" name="level2itemgroupbgpositiony" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level2itemgroupbgimagerepeatrepeat" name="level2itemgroupbgimagerepeat" class="level2itemgroup" />
	<label class="radiobutton first" for="level2itemgroupbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level2itemgroup" type="radio" value="repeat-x" id="level2itemgroupbgimagerepeatrepeat-x" name="level2itemgroupbgimagerepeat" />
	<label class="radiobutton"  for="level2itemgroupbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level2itemgroup" type="radio" value="repeat-y" id="level2itemgroupbgimagerepeatrepeat-y" name="level2itemgroupbgimagerepeat" />
	<label class="radiobutton last"  for="level2itemgroupbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level2itemgroup" type="radio" value="no-repeat" id="level2itemgroupbgimagerepeatno-repeat" name="level2itemgroupbgimagerepeat" />
	<label class="radiobutton last"  for="level2itemgroupbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<?php $this->interface->createBorders('level2itemgroup') ?>
<div class="ckrow">
	<label for="level2itemgrouproundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="level2itemgrouproundedcornerstl" name="level2itemgrouproundedcornerstl" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="level2itemgrouproundedcornerstr" name="level2itemgrouproundedcornerstr" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="level2itemgrouproundedcornersbr" name="level2itemgrouproundedcornersbr" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="level2itemgrouproundedcornersbl" name="level2itemgrouproundedcornersbl" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2itemgroupshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="level2itemgroupshadowcolor" name="level2itemgroupshadowcolor" class="level2itemgroup <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="level2itemgroupshadowblur" name="level2itemgroupshadowblur" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="level2itemgroupshadowspread" name="level2itemgroupshadowspread" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemgroupshadowoffsetx" name="level2itemgroupshadowoffsetx" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemgroupshadowoffsety" name="level2itemgroupshadowoffsety" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton level2itemgroup" type="radio" value="0" id="level2itemgroupshadowinsetno" name="level2itemgroupshadowinset" />
	<label class="radiobutton last"  for="level2itemgroupshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton level2itemgroup" type="radio" value="1" id="level2itemgroupshadowinsetyes" name="level2itemgroupshadowinset" />
	<label class="radiobutton last"  for="level2itemgroupshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="level2itemgroupmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level2itemgroupmargintop" name="level2itemgroupmargintop" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level2itemgroupmarginright" name="level2itemgroupmarginright" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2itemgroupmarginbottom" name="level2itemgroupmarginbottom" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level2itemgroupmarginleft" name="level2itemgroupmarginleft" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2itemgrouppaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="level2itemgrouppaddingtop" name="level2itemgrouppaddingtop" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="level2itemgrouppaddingright" name="level2itemgrouppaddingright" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2itemgrouppaddingbottom" name="level2itemgrouppaddingbottom" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="level2itemgrouppaddingleft" name="level2itemgrouppaddingleft" class="level2itemgroup hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>