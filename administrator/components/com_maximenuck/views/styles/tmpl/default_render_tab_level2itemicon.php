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
	<label for="level2itemnormalstylesiconmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesiconmargintop" name="level2itemnormalstylesiconmargintop" class="level2itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesiconmarginright" name="level2itemnormalstylesiconmarginright" class="level2itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesiconmarginbottom" name="level2itemnormalstylesiconmarginbottom" class="level2itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level2itemnormalstylesiconmarginleft" name="level2itemnormalstylesiconmarginleft" class="level2itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level2itemnormalstylesiconfontsize"><?php echo JText::_('CK_ICON_FORMAT'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level2itemnormalstylesiconfontsize" name="level2itemnormalstylesiconfontsize" class="level2itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_align_middle.png" />
	<input type="text" id="level2itemnormalstylesiconlineheight" name="level2itemnormalstylesiconlineheight" class="level2itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_LINEHEIGHT_DESC'); ?>" />
</div>
<div class="ckrow">
	<label for="level2itemnormalstylesiconfontcolor"><?php echo JText::_('CK_PARENTARROWCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_NORMAL'); ?></span>
	<input type="text" id="level2itemnormalstylesiconfontcolor" name="level2itemnormalstylesiconfontcolor" class="level2itemnormalstylesicon hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_PARENTARROWCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_HOVER'); ?></span>
	<input type="text" id="level2itemhoverstylesiconfontcolor" name="level2itemhoverstylesiconfontcolor" class="level2itemhoverstylesicon hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_PARENTARROWHOVERCOLOR_DESC'); ?>" />
</div>