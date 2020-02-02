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
	<label for="level3menustylesimageplus"><?php echo JText::_('CK_IMAGE_PLUS'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level3menustylesimageplus" name="level3menustylesimageplus" class="hasTip level3menustyles" title="<?php echo JText::_('CK_PLUS_IMAGE_DESC'); ?>" />
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level3menustylesimageplus" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label for="level3imageminus"><?php echo JText::_('CK_IMAGE_MINUS'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/image.png" />
	<span class="btn-group">
		<input type="text" id="level3menustylesimageminus" name="level3menustylesimageminus" class="hasTip level3menustyles" title="<?php echo JText::_('CK_MINUS_IMAGE_DESC'); ?>" />
		<a class="modal btn" href="<?php echo JUri::base(true) ?>/index.php?option=com_media&view=images&tmpl=component&fieldid=level3menustylesimageminus" rel="{handler: 'iframe', size: {x: 700, y: 600}}" ><?php echo JText::_('CK_SELECT'); ?></a>
		<a class="btn" href="javascript:void(0)" onclick="$ck(this).parent().find('input').val('');"><?php echo JText::_('CK_CLEAR'); ?></a>
	</span>
</div>
<div class="ckrow">
	<label for="level3menustylesparentarrowwidth"><?php echo JText::_('CK_PARENT_IMAGE_WIDTH_LABEL'); ?></label>
	<span><img class="iconck" src="<?php echo $this->imagespath ?>/images/width.png" /></span><span style="width:30px;"><input type="text" id="level3menustylesparentarrowwidth" name="level3menustylesparentarrowwidth" class="level3menustyles hasTip" style="width:30px;" title="<?php echo JText::_('CK_PADDINGRIGHT_DESC'); ?>" /></span>
</div>