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
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/parentitem_illustration.png" />
	<p></p>
</div>
<div class="ckrow">
	<label for=""><?php echo JText::_('CK_PARENTARROWTYPE_LABEL'); ?></label>
	<input class="radiobutton level1itemnormalstyles" type="radio" value="triangle" id="level1itemnormalstylesparentarrowtypetriangle" name="level1itemnormalstylesparentarrowtype" />
	<label class="radiobutton first" for="level1itemnormalstylesparentarrowtypetriangle" style="width:auto;"><?php echo JText::_('CK_TRIANGLE'); ?>
	</label><input class="radiobutton level1itemnormalstyles" type="radio" value="image" id="level1itemnormalstylesparentarrowtypeimage" name="level1itemnormalstylesparentarrowtype" />
	<label class="radiobutton"  for="level1itemnormalstylesparentarrowtypeimage" style="width:auto;"><?php echo JText::_('CK_IMAGE'); ?>
	</label><input class="radiobutton level1itemnormalstyles" type="radio" value="none" id="level1itemnormalstylesparentarrowtypenone" name="level1itemnormalstylesparentarrowtype" />
	<label class="radiobutton"  for="level1itemnormalstylesparentarrowtypenone" style="width:auto;"><?php echo JText::_('CK_NONE'); ?>
	</label>
</div>
<div class="ckheading"><?php echo JText::_('CK_COMMON_OPTIONS'); ?></div>
<div class="ckrow">
	<label for="level1itemnormalstylesparentarrowmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentarrowmargintop" name="level1itemnormalstylesparentarrowmargintop" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentarrowmarginright" name="level1itemnormalstylesparentarrowmarginright" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentarrowmarginbottom" name="level1itemnormalstylesparentarrowmarginbottom" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentarrowmarginleft" name="level1itemnormalstylesparentarrowmarginleft" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level1itemnormalstylesparentarrowpositiontop"><?php echo JText::_('CK_POSITION_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/position_top.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentarrowpositiontop" name="level1itemnormalstylesparentarrowpositiontop" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_POSITIONTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/position_right.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentarrowpositionright" name="level1itemnormalstylesparentarrowpositionright" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_POSITIONRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/position_bottom.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentarrowpositionbottom" name="level1itemnormalstylesparentarrowpositionbottom" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_POSITIONBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/position_left.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentarrowpositionleft" name="level1itemnormalstylesparentarrowpositionleft" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_POSITIONLEFT_DESC'); ?>" /></span>
</div>
<div class="ckheading"><?php echo JText::_('CK_TRIANGLE_OPTIONS'); ?></div>
<div class="ckrow">
	<label for="level1itemnormalstylesparentarrowcolor"><?php echo JText::_('CK_PARENTARROWCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_NORMAL'); ?></span>
	<input type="text" id="level1itemnormalstylesparentarrowcolor" name="level1itemnormalstylesparentarrowcolor" class="level1itemnormalstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_PARENTARROWCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_HOVER'); ?></span>
	<input type="text" id="level1itemhoverstylesparentarrowcolor" name="level1itemhoverstylesparentarrowcolor" class="level1itemhoverstyles hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_PARENTARROWHOVERCOLOR_DESC'); ?>" />
</div>
<div class="ckheading"><?php echo JText::_('CK_IMAGE_OPTIONS'); ?></div>
<div class="ckrow">
	<label for="level1itemnormalstylesparentarrowwidth"><?php echo JText::_('CK_DIMENSIONS_REQUIRED_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/width.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentarrowwidth" name="level1itemnormalstylesparentarrowwidth" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_WIDTH_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/height.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentarrowheight" name="level1itemnormalstylesparentarrowheight" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_HEIGHT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level1itemnormalstylesparentitemimage"><?php echo JText::_('CK_IMAGE'); ?> - <?php echo JText::_('CK_NORMAL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level1itemnormalstylesparentitemimage" name="level1itemnormalstylesparentitemimage" class="hasTip level1itemnormalstyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" />
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level1itemnormalstylesparentitemimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentitemimagepositionx" name="level1itemnormalstylesparentitemimagepositionx" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesparentitemimagepositiony" name="level1itemnormalstylesparentitemimagepositiony" class="level1itemnormalstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level1itemnormalstylesparentitemimagerepeatrepeat" name="level1itemnormalstylesparentitemimagerepeat" class="level1itemnormalstyles" />
	<label class="radiobutton first" for="level1itemnormalstylesparentitemimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level1itemnormalstyles" type="radio" value="repeat-x" id="level1itemnormalstylesparentitemimagerepeatrepeat-x" name="level1itemnormalstylesparentitemimagerepeat" />
	<label class="radiobutton"  for="level1itemnormalstylesparentitemimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level1itemnormalstyles" type="radio" value="repeat-y" id="level1itemnormalstylesparentitemimagerepeatrepeat-y" name="level1itemnormalstylesparentitemimagerepeat" />
	<label class="radiobutton last"  for="level1itemnormalstylesparentitemimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level1itemnormalstyles" type="radio" value="no-repeat" id="level1itemnormalstylesparentitemimagerepeatno-repeat" name="level1itemnormalstylesparentitemimagerepeat" />
	<label class="radiobutton last"  for="level1itemnormalstylesparentitemimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
<div class="ckrow">
	<label for="level1itemhoverstylesparentitemimage"><?php echo JText::_('CK_IMAGE'); ?> - <?php echo JText::_('CK_HOVER'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level1itemhoverstylesparentitemimage" name="level1itemhoverstylesparentitemimage" class="hasTip level1itemhoverstyles" title="<?php echo JText::_('CK_BACKGROUNDIMAGE_DESC'); ?>" />
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level1itemhoverstylesparentitemimage" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsetx.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesparentitemimagepositionx" name="level1itemhoverstylesparentitemimagepositionx" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONX_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/offsety.png" /></span><span style="width:30px;"><input type="text" id="level1itemhoverstylesparentitemimagepositiony" name="level1itemhoverstylesparentitemimagepositiony" class="level1itemhoverstyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_BACKGROUNDPOSITIONY_DESC'); ?>" /></span>
	<input class="radiobutton" type="radio" value="repeat" id="level1itemhoverstylesparentitemimagerepeatrepeat" name="level1itemhoverstylesparentitemimagerepeat" class="level1itemhoverstyles" />
	<label class="radiobutton first" for="level1itemhoverstylesparentitemimagerepeatrepeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat.png" />
	</label><input class="radiobutton level1itemhoverstyles" type="radio" value="repeat-x" id="level1itemhoverstylesparentitemimagerepeatrepeat-x" name="level1itemhoverstylesparentitemimagerepeat" />
	<label class="radiobutton"  for="level1itemhoverstylesparentitemimagerepeatrepeat-x"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-x.png" />
	</label><input class="radiobutton level1itemhoverstyles" type="radio" value="repeat-y" id="level1itemhoverstylesparentitemimagerepeatrepeat-y" name="level1itemhoverstylesparentitemimagerepeat" />
	<label class="radiobutton last"  for="level1itemhoverstylesparentitemimagerepeatrepeat-y"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_repeat-y.png" />
	</label><input class="radiobutton level1itemhoverstyles" type="radio" value="no-repeat" id="level1itemhoverstylesparentitemimagerepeatno-repeat" name="level1itemhoverstylesparentitemimagerepeat" />
	<label class="radiobutton last"  for="level1itemhoverstylesparentitemimagerepeatno-repeat"><img class="iconck" src="<?php echo $this->imagespath ?>/images/bg_no-repeat.png" /></label>
</div>
