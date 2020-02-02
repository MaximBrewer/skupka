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
	<label for="topbarbuttoncontent"><?php echo JText::_('CK_BUTTON_CONTENT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style_edit.png" />
	<input class="radiobutton topbarbutton" type="radio" value="close" id="topbarbuttoncontenthamburger" name="topbarbuttoncontent" />
	<label class="radiobutton"  for="topbarbuttoncontenthamburger" style="width:auto;font-size:24px;padding-left:10px;padding-right:10px;">×
	</label><input class="radiobutton topbarbutton customtextswitcher" type="radio" value="custom" id="topbarbuttoncontentcustom" name="topbarbuttoncontent" />
	<label class="radiobutton"  for="topbarbuttoncontentcustom" style="width:auto;"><?php echo JText::_('CK_CUSTOM_TEXT'); ?></label>
	<input class="topbarbutton customtextvalue" type="text" value="" id="topbarbuttoncontentcustomtext" name="topbarbuttoncontentcustomtext" />
	<input class="radiobutton topbarbutton" type="radio" value="none" id="topbarbuttoncontentnone" name="topbarbuttoncontent" />
	<label class="radiobutton"  for="topbarbuttoncontentnone" style="width:auto;"><?php echo JText::_('CK_NONE'); ?></label>
</div>
<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
<div class="ckrow">
	<label for="topbarbuttonheight"><?php echo JText::_('CK_HEIGHT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/height.png" />
	<input type="text" id="topbarbuttonheight" name="topbarbuttonheight" class="hasTip topbarbutton" title="<?php echo JText::_('CK_HEIGHT_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/width.png" />
	<input type="text" id="topbarbuttonwidth" name="topbarbuttonwidth" class="hasTip topbarbutton" title="<?php echo JText::_('CK_WIDTH_DESC'); ?>"/>
</div>
<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
<div class="ckrow">
	<label for="topbarbuttonbgcolor1"><?php echo JText::_('CK_BGCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="topbarbuttonbgcolor1" name="topbarbuttonbgcolor1" class="hasTip topbarbutton <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR_DESC'); ?>"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="topbarbuttonbgcolor2" name="topbarbuttonbgcolor2" class="hasTip topbarbutton <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BGCOLOR2_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'topbarbuttonbgimage')"/>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/layers.png" />
	<input type="text" id="topbarbuttonbgopacity" name="topbarbuttonbgopacity" class="hasTip topbarbutton" style="width:30px;" title="<?php echo JText::_('CK_BGOPACITY_DESC'); ?>"/>
</div>
<div class="ckrow">
	<label for="topbarbuttonbgimage"><?php echo JText::_('CK_BACKGROUNDIMAGE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="topbarbuttonbgimage" name="topbarbuttonbgimage" class="hasTip topbarbutton" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" onchange="check_gradient_image_conflict(this, 'topbarbuttonbgcolor2')"/>
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=topbarbuttonbgimage" rel="{handler: 'iframe', size: {x: 800, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('').trigger('change');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonbgpositionx" name="topbarbuttonbgpositionx" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonbgpositiony" name="topbarbuttonbgpositiony" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="topbarbuttonbgimagerepeatrepeat" name="topbarbuttonbgimagerepeat" class="topbarbutton" />
	<label class="radiobutton first" for="topbarbuttonbgimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton topbarbutton" type="radio" value="repeat-x" id="topbarbuttonbgimagerepeatrepeat-x" name="topbarbuttonbgimagerepeat" />
	<label class="radiobutton"  for="topbarbuttonbgimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton topbarbutton" type="radio" value="repeat-y" id="topbarbuttonbgimagerepeatrepeat-y" name="topbarbuttonbgimagerepeat" />
	<label class="radiobutton last"  for="topbarbuttonbgimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton topbarbutton" type="radio" value="no-repeat" id="topbarbuttonbgimagerepeatno-repeat" name="topbarbuttonbgimagerepeat" />
	<label class="radiobutton last"  for="topbarbuttonbgimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<div class="ckrow">
	<label for="topbarbuttonmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonmargintop" name="topbarbuttonmargintop" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonmarginright" name="topbarbuttonmarginright" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonmarginbottom" name="topbarbuttonmarginbottom" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonmarginleft" name="topbarbuttonmarginleft" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="topbarbuttonpaddingtop"><?php echo JText::_('CK_PADDING_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_top.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonpaddingtop" name="topbarbuttonpaddingtop" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_right.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonpaddingright" name="topbarbuttonpaddingright" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_bottom.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonpaddingbottom" name="topbarbuttonpaddingbottom" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/padding_left.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonpaddingleft" name="topbarbuttonpaddingleft" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="topbarbuttonbordercolor"><?php echo JText::_('CK_BORDERCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="topbarbuttonbordercolor" name="topbarbuttonbordercolor" class="topbarbutton <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_BORDERCOLOR_DESC'); ?>"/></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_borders.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonbordertopwidth" name="topbarbuttonbordertopwidth" class="topbarbutton hasTip" style="width:30px;border-top-color:#237CA4;" title="<?php echo JText::_('CK_BORDERTOPWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="topbarbuttonborderrightwidth" name="topbarbuttonborderrightwidth" class="topbarbutton hasTip" style="width:30px;border-right-color:#237CA4;" title="<?php echo JText::_('CK_BORDERRIGHTWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="topbarbuttonborderbottomwidth" name="topbarbuttonborderbottomwidth" class="topbarbutton hasTip" style="width:30px;border-bottom-color:#237CA4;" title="<?php echo JText::_('CK_BORDERBOTTOMWIDTH_DESC'); ?>" /></span>
	<span style="width:30px;"><input type="text" id="topbarbuttonborderleftwidth" name="topbarbuttonborderleftwidth" class="topbarbutton hasTip" style="width:30px;border-left-color:#237CA4;" title="<?php echo JText::_('CK_BORDERLEFTWIDTH_DESC'); ?>" /></span>
	<span>
		<select id="topbarbuttonborderstyle" name="topbarbuttonborderstyle" class="topbarbutton hasTip" style="width: 70px; border-radius: 0px;">
			<option value="solid">solid</option>
			<option value="dotted">dotted</option>
			<option value="dashed">dashed</option>
		</select>
	</span>
</div>
<div class="ckrow">
	<label for="topbarbuttonroundedcornerstl"><?php echo JText::_('CK_ROUNDEDCORNERS_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tl.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonroundedcornerstl" name="topbarbuttonroundedcornerstl" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTL_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_tr.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonroundedcornerstr" name="topbarbuttonroundedcornerstr" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSTR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_br.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonroundedcornersbr" name="topbarbuttonroundedcornersbr" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/border_radius_bl.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonroundedcornersbl" name="topbarbuttonroundedcornersbl" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_ROUNDEDCORNERSBL_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="topbarbuttonshadowcolor"><?php echo JText::_('CK_SHADOW_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><input type="text" id="topbarbuttonshadowcolor" name="topbarbuttonshadowcolor" class="topbarbutton <?php echo $this->colorpicker_class; ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_blur.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonshadowblur" name="topbarbuttonshadowblur" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWBLUR_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/shadow_spread.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonshadowspread" name="topbarbuttonshadowspread" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_SHADOWSPREAD_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonshadowoffsetx" name="topbarbuttonshadowoffsetx" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="topbarbuttonshadowoffsety" name="topbarbuttonshadowoffsety" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_OFFSETY_DESC'); ?>" /></span>
	<label></label><input class="radiobutton topbarbutton" type="radio" value="0" id="topbarbuttonshadowinsetno" name="topbarbuttonshadowinset" />
	<label class="radiobutton last"  for="topbarbuttonshadowinsetno" style="width:auto;"><?php echo JText::_('CK_OUT'); ?>
	</label><input class="radiobutton topbarbutton" type="radio" value="1" id="topbarbuttonshadowinsetyes" name="topbarbuttonshadowinset" />
	<label class="radiobutton last"  for="topbarbuttonshadowinsetyes" style="width:auto;"><?php echo JText::_('CK_IN'); ?></label>
</div>
<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
<div class="ckrow">
	<label for="topbarbuttonfontsize"><?php echo JText::_('CK_TEXT_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="topbarbuttonfontsize" name="topbarbuttonfontsize" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_NORMAL'); ?></span>
	<input type="text" id="topbarbuttonfontcolor" name="topbarbuttonfontcolor" class="topbarbutton hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_align_middle.png" />
	<input type="text" id="topbarbuttonlineheight" name="topbarbuttonlineheight" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_LINEHEIGHT_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/text_padding_left.png" />
	<input type="text" id="topbarbuttontextindent" name="topbarbuttontextindent" class="topbarbutton hasTip" style="width:30px;" title="<?php echo JText::_('CK_TEXTINDENT_DESC'); ?>" />
</div>