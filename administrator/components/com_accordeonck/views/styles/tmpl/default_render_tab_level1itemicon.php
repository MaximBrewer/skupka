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
<div class="ckrow">
	<label for="level1itemnormalstylesiconmargintop"><?php echo JText::_('CK_MARGIN_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_top.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesiconmargintop" name="level1itemnormalstylesiconmargintop" class="level1itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINTOP_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_right.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesiconmarginright" name="level1itemnormalstylesiconmarginright" class="level1itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINRIGHT_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_bottom.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesiconmarginbottom" name="level1itemnormalstylesiconmarginbottom" class="level1itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINBOTTOM_DESC'); ?>" /></span>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/margin_left.png" /></span><span style="width:30px;"><input type="text" id="level1itemnormalstylesiconmarginleft" name="level1itemnormalstylesiconmarginleft" class="level1itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_MARGINLEFT_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="level1itemnormalstylesiconfontsize"><?php echo JText::_('CK_ICON_FORMAT'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="level1itemnormalstylesiconfontsize" name="level1itemnormalstylesiconfontsize" class="level1itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_align_middle.png" />
	<input type="text" id="level1itemnormalstylesiconlineheight" name="level1itemnormalstylesiconlineheight" class="level1itemnormalstylesicon hasTip" style="width:30px;" title="<?php echo JText::_('CK_LINEHEIGHT_DESC'); ?>" />
</div>
<div class="ckrow">
	<label for="level1itemnormalstylesiconfontcolor"><?php echo JText::_('CK_PARENTARROWCOLOR_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_NORMAL'); ?></span>
	<input type="text" id="level1itemnormalstylesiconfontcolor" name="level1itemnormalstylesiconfontcolor" class="level1itemnormalstylesicon hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_PARENTARROWCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<span><?php echo JText::_('CK_HOVER'); ?></span>
	<input type="text" id="level1itemhoverstylesiconfontcolor" name="level1itemhoverstylesiconfontcolor" class="level1itemhoverstylesicon hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_PARENTARROWHOVERCOLOR_DESC'); ?>" />
</div>