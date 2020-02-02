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
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/parentitem_illustration_level2.png" />
	<p></p>
</div>
<div class="ckrow">
	<label for=""><?php echo JText::_('CK_PARENTARROWTYPE_LABEL'); ?></label>
	<input class="radiobutton level2itemnormalstyles" type="radio" value="triangle" id="level2itemnormalstylesparentarrowtypetriangle" name="level2itemnormalstylesparentarrowtype" />
	<label class="radiobutton first" for="level2itemnormalstylesparentarrowtypetriangle" style="width:auto;"><?php echo JText::_('CK_TRIANGLE'); ?>
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="image" id="level2itemnormalstylesparentarrowtypeimage" name="level2itemnormalstylesparentarrowtype" />
	<label class="radiobutton"  for="level2itemnormalstylesparentarrowtypeimage" style="width:auto;"><?php echo JText::_('CK_IMAGE'); ?>
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="none" id="level2itemnormalstylesparentarrowtypenone" name="level2itemnormalstylesparentarrowtype" />
	<label class="radiobutton"  for="level2itemnormalstylesparentarrowtypenone" style="width:auto;"><?php echo JText::_('CK_NONE'); ?>
	</label>
</div>
<div class="ckheading"><?php echo JText::_('CK_COMMON_OPTIONS'); ?></div>
<div class="ckrow">
	<label for="level2itemnormalstylesparentarrowmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentarrowmargintop" name="level2itemnormalstylesparentarrowmargintop" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentarrowmarginright" name="level2itemnormalstylesparentarrowmarginright" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentarrowmarginbottom" name="level2itemnormalstylesparentarrowmarginbottom" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentarrowmarginleft" name="level2itemnormalstylesparentarrowmarginleft" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2itemnormalstylesparentarrowpositiontop"><?php echo JText::_('CK_POSITION_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/position_top.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentarrowpositiontop" name="level2itemnormalstylesparentarrowpositiontop" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_POSITIONTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/position_right.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentarrowpositionright" name="level2itemnormalstylesparentarrowpositionright" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_POSITIONRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/position_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentarrowpositionbottom" name="level2itemnormalstylesparentarrowpositionbottom" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_POSITIONBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/position_left.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentarrowpositionleft" name="level2itemnormalstylesparentarrowpositionleft" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_POSITIONLEFT_DESC'); ?>" /></span>
</div>
<div class="ckheading"><?php echo JText::_('CK_TRIANGLE_OPTIONS'); ?></div>
<div class="ckrow">
	<label for="level2itemnormalstylesparentarrowcolor"><?php echo JText::_('CK_PARENTARROWCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_NORMAL'); ?></span>
	<input type="text" id="level2itemnormalstylesparentarrowcolor" name="level2itemnormalstylesparentarrowcolor" class="level2itemnormalstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_PARENTARROWCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_HOVER'); ?></span>
	<input type="text" id="level2itemhoverstylesparentarrowcolor" name="level2itemhoverstylesparentarrowcolor" class="level2itemhoverstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_PARENTARROWHOVERCOLOR_DESC'); ?>" />
</div>
<div class="ckheading"><?php echo JText::_('CK_IMAGE_OPTIONS'); ?></div>
<div class="ckrow">
	<label for="level2itemnormalstylesparentarrowwidth"><?php echo JText::_('CK_DIMENSIONS_REQUIRED_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/width.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentarrowwidth" name="level2itemnormalstylesparentarrowwidth" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_WIDTH_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/height.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentarrowheight" name="level2itemnormalstylesparentarrowheight" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_HEIGHT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2itemnormalstylesparentitemimage"><?php echo JText::_('CK_IMAGE'); ?> - <?php echo JText::_('CK_NORMAL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level2itemnormalstylesparentitemimage" name="level2itemnormalstylesparentitemimage" class="hasTip level2itemnormalstyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" />
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level2itemnormalstylesparentitemimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentitemimagepositionx" name="level2itemnormalstylesparentitemimagepositionx" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesparentitemimagepositiony" name="level2itemnormalstylesparentitemimagepositiony" class="level2itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level2itemnormalstylesparentitemimagerepeatrepeat" name="level2itemnormalstylesparentitemimagerepeat" class="level2itemnormalstyles" />
	<label class="radiobutton first" for="level2itemnormalstylesparentitemimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="repeat-x" id="level2itemnormalstylesparentitemimagerepeatrepeat-x" name="level2itemnormalstylesparentitemimagerepeat" />
	<label class="radiobutton"  for="level2itemnormalstylesparentitemimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="repeat-y" id="level2itemnormalstylesparentitemimagerepeatrepeat-y" name="level2itemnormalstylesparentitemimagerepeat" />
	<label class="radiobutton last"  for="level2itemnormalstylesparentitemimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level2itemnormalstyles" type="radio" value="no-repeat" id="level2itemnormalstylesparentitemimagerepeatno-repeat" name="level2itemnormalstylesparentitemimagerepeat" />
	<label class="radiobutton last"  for="level2itemnormalstylesparentitemimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<div class="ckrow">
	<label for="level2itemhoverstylesparentitemimage"><?php echo JText::_('CK_IMAGE'); ?> - <?php echo JText::_('CK_HOVER'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level2itemhoverstylesparentitemimage" name="level2itemhoverstylesparentitemimage" class="hasTip level2itemhoverstyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" />
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level2itemhoverstylesparentitemimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesparentitemimagepositionx" name="level2itemhoverstylesparentitemimagepositionx" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level2itemhoverstylesparentitemimagepositiony" name="level2itemhoverstylesparentitemimagepositiony" class="level2itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level2itemhoverstylesparentitemimagerepeatrepeat" name="level2itemhoverstylesparentitemimagerepeat" class="level2itemhoverstyles" />
	<label class="radiobutton first" for="level2itemhoverstylesparentitemimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level2itemhoverstyles" type="radio" value="repeat-x" id="level2itemhoverstylesparentitemimagerepeatrepeat-x" name="level2itemhoverstylesparentitemimagerepeat" />
	<label class="radiobutton"  for="level2itemhoverstylesparentitemimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level2itemhoverstyles" type="radio" value="repeat-y" id="level2itemhoverstylesparentitemimagerepeatrepeat-y" name="level2itemhoverstylesparentitemimagerepeat" />
	<label class="radiobutton last"  for="level2itemhoverstylesparentitemimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level2itemhoverstyles" type="radio" value="no-repeat" id="level2itemhoverstylesparentitemimagerepeatno-repeat" name="level2itemhoverstylesparentitemimagerepeat" />
	<label class="radiobutton last"  for="level2itemhoverstylesparentitemimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
