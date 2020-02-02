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
	<label for="menustylesimageplus"><?php echo JText::_('CK_IMAGE_PLUS'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="menustylesimageplus" name="menustylesimageplus" class="hasTip menustyles" title="<?php echo JText::_('CK_PLUS_IMAGE_DESC'); ?>" />
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=menustylesimageplus" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label for="menustylesimageminus"><?php echo JText::_('CK_IMAGE_MINUS'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="menustylesimageminus" name="menustylesimageminus" class="hasTip menustyles" title="<?php echo JText::_('CK_MINUS_IMAGE_DESC'); ?>" />
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=menustylesimageminus" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label for="menustylesparentarrowwidth"><?php echo JText::_('CK_PARENT_IMAGE_WIDTH_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/width.png" /></span><span style="width:30px;"><input type="text" id="menustylesparentarrowwidth" name="menustylesparentarrowwidth" class="menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_WIDTH_DESC'); ?>" /></span>
</div>
<div class="ckrow">
	<label for="menustylesparentarrowposition"><?php echo JText::_('CK_PARENT_IMAGE_POSITION_LABEL'); ?></label>
	<input class="radiobutton menustyles" type="radio" value="left" id="menustylesparentarrowpositionleft" name="menustylesparentarrowposition" />
	<label class="radiobutton first hasTip" title="" for="menustylesparentarrowpositionleft" style="width:auto;"><?php echo JText::_('CK_LEFT'); ?>
	</label><input class="radiobutton menustyles" type="radio" value="right" id="menustylesparentarrowpositionright" name="menustylesparentarrowposition" />
	<label class="radiobutton hasTip" title="" for="menustylesparentarrowpositionright" style="width:auto;"><?php echo JText::_('CK_RIGHT'); ?>
	</label>
</div>