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
<div class="ckrow">
	<label for="menubarbuttoncontent"><?php echo JText::_('CK_BUTTON_CONTENT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style_edit.png" />
	<input class="radiobutton menubarbutton" type="radio" value="hamburger" id="menubarbuttoncontenthamburger" name="menubarbuttoncontent" />
	<label class="radiobutton"  for="menubarbuttoncontenthamburger" style="width:auto;font-size:24px;padding-left:10px;padding-right:10px;">&#x2261;
	</label><input class="radiobutton menubarbutton customtextswitcher" type="radio" value="custom" id="menubarbuttoncontentcustom" name="menubarbuttoncontent" />
	<label class="radiobutton"  for="menubarbuttoncontentcustom" style="width:auto;"><?php echo JText::_('CK_CUSTOM_TEXT'); ?></label>
	<input class="menubarbutton customtextvalue" type="text" value="" id="menubarbuttoncontentcustomtext" name="menubarbuttoncontentcustomtext" />
	<input class="radiobutton menubarbutton" type="radio" value="none" id="menubarbuttoncontentnone" name="menubarbuttoncontent" />
	<label class="radiobutton"  for="menubarbuttoncontentnone" style="width:auto;"><?php echo JText::_('CK_NONE'); ?></label>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="menubarbuttonheight"><?php echo JText::_('CK_HEIGHT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/height.png" />
	<input type="text" id="menubarbuttonheight" name="menubarbuttonheight" class="hasTip menubarbutton" title="<?php echo JText::_('CK_HEIGHT_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/width.png" />
	<input type="text" id="menubarbuttonwidth" name="menubarbuttonwidth" class="hasTip menubarbutton" title="<?php echo JText::_('CK_WIDTH_DESC'); ?>"/>
</div>
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="menubarbuttonbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="menubarbuttonbgcolor1" name="menubarbuttonbgcolor1" class="hasTip menubarbutton <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="menubarbuttonbgcolor2" name="menubarbuttonbgcolor2" class="hasTip menubarbutton <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'menubarbuttonbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="menubarbuttonbgopacity" name="menubarbuttonbgopacity" class="hasTip menubarbutton" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="menubarbuttonbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="menubarbuttonbgimage" name="menubarbuttonbgimage" class="hasTip menubarbutton" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'menubarbuttonbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=menubarbuttonbgimage" rel="{handler: 'iframe', size: {x: 800, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('').trigger('change');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonbgpositionx" name="menubarbuttonbgpositionx" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonbgpositiony" name="menubarbuttonbgpositiony" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="menubarbuttonbgimagerepeatrepeat" name="menubarbuttonbgimagerepeat" class="menubarbutton" />
	<label class="radiobutton first" for="menubarbuttonbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton menubarbutton" type="radio" value="repeat-x" id="menubarbuttonbgimagerepeatrepeat-x" name="menubarbuttonbgimagerepeat" />
	<label class="radiobutton"  for="menubarbuttonbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton menubarbutton" type="radio" value="repeat-y" id="menubarbuttonbgimagerepeatrepeat-y" name="menubarbuttonbgimagerepeat" />
	<label class="radiobutton last"  for="menubarbuttonbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton menubarbutton" type="radio" value="no-repeat" id="menubarbuttonbgimagerepeatno-repeat" name="menubarbuttonbgimagerepeat" />
	<label class="radiobutton last"  for="menubarbuttonbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<div class="ckrow">
	<label for="menubarbuttonmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonmargintop" name="menubarbuttonmargintop" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonmarginright" name="menubarbuttonmarginright" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonmarginbottom" name="menubarbuttonmarginbottom" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonmarginleft" name="menubarbuttonmarginleft" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="menubarbuttonpaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonpaddingtop" name="menubarbuttonpaddingtop" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonpaddingright" name="menubarbuttonpaddingright" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonpaddingbottom" name="menubarbuttonpaddingbottom" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonpaddingleft" name="menubarbuttonpaddingleft" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="menubarbuttonbordercolor"><?php echo JText::_('CK_BORDERCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="menubarbuttonbordercolor" name="menubarbuttonbordercolor" class="menubarbutton <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_borders.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonbordertopwidth" name="menubarbuttonbordertopwidth" class="menubarbutton hasTip" style="width:30px;border-top-color:#237CA4;" title="<?php echo JText::_('CK_BORDERTOPWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="menubarbuttonborderrightwidth" name="menubarbuttonborderrightwidth" class="menubarbutton hasTip" style="width:30px;border-right-color:#237CA4;" title="<?php echo JText::_('CK_BORDERRIGHTWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="menubarbuttonborderbottomwidth" name="menubarbuttonborderbottomwidth" class="menubarbutton hasTip" style="width:30px;border-bottom-color:#237CA4;" title="<?php echo JText::_('CK_BORDERBOTTOMWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="menubarbuttonborderleftwidth" name="menubarbuttonborderleftwidth" class="menubarbutton hasTip" style="width:30px;border-left-color:#237CA4;" title="<?php echo JText::_('CK_BORDERLEFTWIDTH_DESC'); ?>" /></span>
	<span>
		<select id="menubarbuttonborderstyle" name="menubarbuttonborderstyle" class="menubarbutton hasTip" style="width: 70px; border-radius: 0px;">
			<option value="solid">solid</option>
			<option value="dotted">dotted</option>
			<option value="dashed">dashed</option>
		</select>
	</span>
</div>
<div class="ckrow">
	<label for="menubarbuttonroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonroundedcornerstl" name="menubarbuttonroundedcornerstl" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonroundedcornerstr" name="menubarbuttonroundedcornerstr" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonroundedcornersbr" name="menubarbuttonroundedcornersbr" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonroundedcornersbl" name="menubarbuttonroundedcornersbl" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="menubarbuttonshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="menubarbuttonshadowcolor" name="menubarbuttonshadowcolor" class="menubarbutton <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonshadowblur" name="menubarbuttonshadowblur" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonshadowspread" name="menubarbuttonshadowspread" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonshadowoffsetx" name="menubarbuttonshadowoffsetx" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="menubarbuttonshadowoffsety" name="menubarbuttonshadowoffsety" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton menubarbutton" type="radio" value="0" id="menubarbuttonshadowinsetno" name="menubarbuttonshadowinset" />
	<label class="radiobutton last"  for="menubarbuttonshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton menubarbutton" type="radio" value="1" id="menubarbuttonshadowinsetyes" name="menubarbuttonshadowinset" />
	<label class="radiobutton last"  for="menubarbuttonshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
<div class="ckrow">
	<label for="menubarbuttonfontsize"><?php echo JText::_('CK_TEXT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="menubarbuttonfontsize" name="menubarbuttonfontsize" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="menubarbuttonfontcolor" name="menubarbuttonfontcolor" class="menubarbutton hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_align_middle.png" />
	<input type="text" id="menubarbuttonlineheight" name="menubarbuttonlineheight" class="menubarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_LINEHEIGHT_DESC'); ?>" />
</div>