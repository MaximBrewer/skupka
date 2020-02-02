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
	<label for="menubartextgfont"><?php echo JText::_('CK_FONTSTYLE_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/font_add.png" />
	<input type="text" id="menubartextgfont" name="menubartextgfont" class="menubar hasTip" onchange="clean_gfont_name(this)" title="<?php echo JText::_('CK_GFONT_DESC'); ?>" />
	<input class="radiobutton menubar" type="radio" value="left" id="menubartextalignleft" name="menubartextalign" />
	<label class="radiobutton first" for="menubartextalignleft"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_left.png" />
	</label><input class="radiobutton menubar" type="radio" value="center" id="menubartextaligncenter" name="menubartextalign" />
	<label class="radiobutton"  for="menubartextaligncenter"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_center.png" />
	</label><input class="radiobutton menubar" type="radio" value="right" id="menubartextalignright" name="menubartextalign" />
	<label class="radiobutton last"  for="menubartextalignright"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_align_right.png" /></label>
	<span class="vertical_separator"></span>
	<input class="radiobutton menubar" type="radio" value="lowercase" id="menubartexttransformlowercase" name="menubartexttransform" />
	<label class="radiobutton first hasTip" title="<?php echo JText::_('CK_LOWERCASE'); ?>" for="menubartexttransformlowercase"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_lowercase.png" />
	</label><input class="radiobutton menubar" type="radio" value="uppercase" id="menubartexttransformuppercase" name="menubartexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_UPPERCASE'); ?>" for="menubartexttransformuppercase"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_uppercase.png" />
	</label><input class="radiobutton menubar" type="radio" value="capitalize" id="menubartexttransformcapitalize" name="menubartexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_CAPITALIZE'); ?>" for="menubartexttransformcapitalize"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_capitalize.png" />
	</label><input class="radiobutton menubar" type="radio" value="default" id="menubartexttransformdefault" name="menubartexttransform" />
	<label class="radiobutton hasTip" title="<?php echo JText::_('CK_DEFAULT'); ?>" for="menubartexttransformdefault"><img class="iconck" src="<?php echo $this->imagespath ?>/images/text_default.png" />
	</label>
</div>
<div class="ckrow">
	<label for="menubarfontweightbold"></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/text_bold.png" />
	<input class="radiobutton menubar" type="radio" value="bold" id="menubarfontweightbold" name="menubarfontweight" />
	<label class="radiobutton first hasTip" title="" for="menubarfontweightbold" style="width:auto;"><?php echo JText::_('CK_BOLD'); ?>
	</label><input class="radiobutton menubar" type="radio" value="normal" id="menubarfontweightnormal" name="menubarfontweight" />
	<label class="radiobutton hasTip" title="" for="menubarfontweightnormal" style="width:auto;"><?php echo JText::_('CK_NORMAL'); ?>
	</label>
</div>
<div class="ckrow">
	<label for="menubarfontsize"><?php echo JText::_('CK_TITLEFONTSTYLES_LABEL'); ?></label>
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/style.png" />
	<input type="text" id="menubarfontsize" name="menubarfontsize" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/color.png" />
	<input type="text" id="menubarfontcolor" name="menubarfontcolor" class="menubar hasTip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/shape_align_middle.png" />
	<input type="text" id="menubarlineheight" name="menubarlineheight" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_LINEHEIGHT_DESC'); ?>" />
	<img class="iconck" src="<?php echo $this->imagespath ?>/images/text_padding_left.png" />
	<input type="text" id="menubartextindent" name="menubartextindent" class="menubar hasTip" style="width:30px;" title="<?php echo JText::_('CK_TEXTINDENT_DESC'); ?>" />
</div>